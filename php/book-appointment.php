<?php
/**
 * Save booking form data to appointments table.
 * POST: doctor_id, appointment_date, slot_time, patient_name, mobile, notes, age, weight,
 *       body_temperature, blood_pressure, pulse, spo2, rbs_fbs.
 * Optional: patient_id from session if patient is logged in.
 */
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['success' => false, 'message' => 'Configuration not found']);
    exit;
}
require_once $config_path;
require_once __DIR__ . '/slot-helpers.php';

$doctor_id = isset($_POST['doctor_id']) ? (int) $_POST['doctor_id'] : 0;
$appointment_date = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';
$slot_time = isset($_POST['slot_time']) ? preg_replace('/[^0-9:]/', '', trim($_POST['slot_time'])) : '';

if ($doctor_id <= 0 || $appointment_date === '' || strlen($slot_time) < 4) {
    echo json_encode(['success' => false, 'message' => 'Invalid request. Select date and time.']);
    exit;
}

$d = DateTime::createFromFormat('Y-m-d', $appointment_date);
if (!$d || $d->format('Y-m-d') !== $appointment_date) {
    echo json_encode(['success' => false, 'message' => 'Invalid date']);
    exit;
}

$today = (new DateTime())->setTime(0, 0, 0);
if ($d < $today) {
    echo json_encode(['success' => false, 'message' => 'Cannot book past dates']);
    exit;
}

$patient_name = isset($_POST['patient_name']) ? trim($_POST['patient_name']) : '';
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
if ($patient_name === '' || $mobile === '') {
    echo json_encode(['success' => false, 'message' => 'Full Name and Mobile Number are required']);
    exit;
}

if (!preg_match('/^[\p{L}\s\.\-]{2,100}$/u', $patient_name)) {
    echo json_encode(['success' => false, 'message' => 'Patient name must be 2-100 characters and contain only letters, spaces, dot, or hyphen.']);
    exit;
}

$mobile_clean = preg_replace('/[^0-9]/', '', $mobile);
if (strlen($mobile_clean) < 10 || strlen($mobile_clean) > 15) {
    echo json_encode(['success' => false, 'message' => 'Mobile number must contain 10-15 digits.']);
    exit;
}
$mobile = $mobile_clean;

$patient_id = isset($_SESSION['patient_id']) ? (int) $_SESSION['patient_id'] : 0;
$booked_by_special_tid_id = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'special_tid' && isset($_SESSION['special_tid_id']))
    ? (int) $_SESSION['special_tid_id']
    : null;
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
$age = isset($_POST['age']) ? trim($_POST['age']) : '';
$weight = isset($_POST['weight']) ? trim($_POST['weight']) : '';
$body_temperature = isset($_POST['body_temperature']) ? trim($_POST['body_temperature']) : '';
$blood_pressure = isset($_POST['blood_pressure']) ? trim($_POST['blood_pressure']) : '';
$pulse = isset($_POST['pulse']) ? trim($_POST['pulse']) : '';
$spo2 = isset($_POST['spo2']) ? trim($_POST['spo2']) : '';
$rbs_fbs = isset($_POST['rbs_fbs']) ? trim($_POST['rbs_fbs']) : '';

// Handle Attachment Upload
$attachment_path = '';
if (isset($_FILES['booking_attachment']) && $_FILES['booking_attachment']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['booking_attachment']['tmp_name'];
    $fileName = $_FILES['booking_attachment']['name'];
    $fileSize = $_FILES['booking_attachment']['size'];
    $fileType = $_FILES['booking_attachment']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'pdf');
    if (in_array($fileExtension, $allowedfileExtensions)) {
        $uploadFileDir = __DIR__ . '/../assets/img/attachments/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $attachment_path = 'assets/img/attachments/' . $newFileName;
        }
    }
}

// Accept TID from either field name (booking form uses booking_telerx_id, JS sends telerx_id)
$referrer_tid = isset($_POST['telerx_id']) ? trim($_POST['telerx_id']) : (isset($_POST['booking_telerx_id']) ? trim($_POST['booking_telerx_id']) : '');
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'bkash';

try {
    $conn = getDBConnection();
    $conn->begin_transaction();

    // If TeleRx ID (TID) provided, validate it belongs to a health worker and use canonical TID from DB
    if ($referrer_tid !== '') {
        $tid_check = $conn->prepare("SELECT id, tid FROM healthcare_providers WHERE UPPER(TRIM(tid)) = UPPER(TRIM(?)) LIMIT 1");
        if ($tid_check) {
            $tid_check->bind_param("s", $referrer_tid);
            $tid_check->execute();
            $tid_res = $tid_check->get_result();
            if ($tid_res && $tid_res->num_rows > 0) {
                $tid_row = $tid_res->fetch_assoc();
                $referrer_tid = $tid_row['tid']; // store canonical TID (e.g. T1001) for dashboard
            } else {
                $referrer_tid = '';
            }
            $tid_check->close();
        } else {
            $referrer_tid = '';
        }
    }

    // Special TID booking flow:
    // - if health worker is booking, resolve patient by mobile
    // - create patient automatically when mobile does not exist
    if ($booked_by_special_tid_id > 0) {
        $phone_col_check = $conn->query("SHOW COLUMNS FROM patients LIKE 'phone'");
        $has_patient_phone = $phone_col_check && $phone_col_check->num_rows > 0;

        if (!$has_patient_phone) {
            $conn->rollback();
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'Patient phone support is not available in database.']);
            exit;
        }

        $patient_lookup = $conn->prepare("SELECT id, name FROM patients WHERE phone = ? LIMIT 1");
        if (!$patient_lookup) {
            $conn->rollback();
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'Unable to verify patient by mobile number.']);
            exit;
        }
        $patient_lookup->bind_param("s", $mobile);
        $patient_lookup->execute();
        $patient_res = $patient_lookup->get_result();

        if ($patient_res && $patient_res->num_rows > 0) {
            $existing_patient = $patient_res->fetch_assoc();
            $patient_id = (int) $existing_patient['id'];
            // Keep appointment name aligned with existing patient profile name.
            $patient_name = trim($existing_patient['name'] ?? '') !== '' ? $existing_patient['name'] : $patient_name;
        } else {
            // patients.email is required+unique in this project, generate deterministic synthetic email from mobile.
            $base_email = 'patient' . $mobile . '@auto.telerx.local';
            $auto_email = $base_email;
            $counter = 1;
            while (true) {
                $email_check = $conn->prepare("SELECT id FROM patients WHERE email = ? LIMIT 1");
                $email_check->bind_param("s", $auto_email);
                $email_check->execute();
                $email_exists = $email_check->get_result()->num_rows > 0;
                $email_check->close();
                if (!$email_exists) {
                    break;
                }
                $counter++;
                $auto_email = 'patient' . $mobile . '+' . $counter . '@auto.telerx.local';
            }

            $default_password_hash = password_hash('12345', PASSWORD_DEFAULT);
            $patient_create = $conn->prepare("INSERT INTO patients (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $patient_create->bind_param("ssss", $patient_name, $auto_email, $mobile, $default_password_hash);
            if (!$patient_create->execute()) {
                $conn->rollback();
                $patient_create->close();
                $conn->close();
                echo json_encode(['success' => false, 'message' => 'Failed to auto-register patient.']);
                exit;
            }
            $patient_id = (int) $conn->insert_id;
            $patient_create->close();
        }
        $patient_lookup->close();
    }

    // 1) Check slot is in doctor's availability (doctor_availability_ranges for this weekday)
    $day_of_week = (int) $d->format('w'); // 0=Sun, 6=Sat
    $stmt = $conn->prepare("SELECT start_time, end_time FROM doctor_availability_ranges WHERE doctor_id = ? AND day_of_week = ? ORDER BY start_time");
    $stmt->bind_param("ii", $doctor_id, $day_of_week);
    $stmt->execute();
    $res = $stmt->get_result();
    $ranges = [];
    while ($row = $res->fetch_assoc()) {
        $ranges[] = $row;
    }
    $stmt->close();
    $available_slots = rangesToSlotTimes($ranges);
    if (!in_array($slot_time, $available_slots)) {
        $conn->rollback();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'This slot is not available.']);
        exit;
    }

    // 2) Check slot not already booked
    $taken = $conn->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND slot_time = ? AND (status IS NULL OR status != 'cancelled')");
    $taken->bind_param("iss", $doctor_id, $appointment_date, $slot_time);
    $taken->execute();
    if ($taken->get_result()->num_rows > 0) {
        $taken->close();
        $conn->rollback();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'This slot is already booked.']);
        exit;
    }
    $taken->close();

    // Ensure payment_method column exists, it may cause implicit commit, so do it cautiously
    $has_payment_method = false;
    $pm_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'payment_method'");
    if ($pm_check && $pm_check->num_rows > 0) {
        $has_payment_method = true;
    }
    if (!$has_payment_method) {
        @$conn->query("ALTER TABLE appointments ADD COLUMN payment_method VARCHAR(50) DEFAULT 'bkash' COMMENT 'bkash or welfare'");
        $pm_check2 = $conn->query("SHOW COLUMNS FROM appointments LIKE 'payment_method'");
        if ($pm_check2 && $pm_check2->num_rows > 0) {
            $has_payment_method = true;
        }
    }

    // If welfare payment, enforce the limits server-side
    if ($payment_method === 'welfare') {
        if ($referrer_tid === '') {
            $conn->rollback();
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'TID is required for Welfare bookings.']);
            exit;
        }

        if ($has_payment_method) {
            $usage = $conn->prepare("SELECT COUNT(id) AS usage_count FROM appointments WHERE patient_id = ? AND UPPER(TRIM(referrer_tid)) = UPPER(TRIM(?)) AND payment_method = 'welfare' AND MONTH(appointment_date) = MONTH(CURRENT_DATE()) AND YEAR(appointment_date) = YEAR(CURRENT_DATE()) AND (status IS NULL OR status != 'cancelled')");
        } else {
            $usage = $conn->prepare("SELECT COUNT(id) AS usage_count FROM appointments WHERE patient_id = ? AND UPPER(TRIM(referrer_tid)) = UPPER(TRIM(?)) AND MONTH(appointment_date) = MONTH(CURRENT_DATE()) AND YEAR(appointment_date) = YEAR(CURRENT_DATE()) AND (status IS NULL OR status != 'cancelled')");
        }
        $usage->bind_param("is", $patient_id, $referrer_tid);
        $usage->execute();
        $usage_res = $usage->get_result();
        $usage_count = $usage_res && $usage_res->num_rows > 0 ? (int)$usage_res->fetch_assoc()['usage_count'] : 0;
        $usage->close();

        if ($usage_count >= 2) {
            $conn->rollback();
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'Welfare limit exceeded for this month with this TID.']);
            exit;
        }
    }

    // 3) Insert appointment (patient_id 0 for guest booking). Include referrer_tid if column exists.
    $appointment_number = 'APT00000';
    $status = 'confirmed';
    $has_referrer_tid = false;
    $col_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'referrer_tid'");
    if ($col_check && $col_check->num_rows > 0) {
        $has_referrer_tid = true;
    }
    // If we have a valid TID but column missing, add it so health worker dashboard can show referred patients
    if (!$has_referrer_tid && $referrer_tid !== '') {
        @$conn->query("ALTER TABLE appointments ADD COLUMN referrer_tid VARCHAR(20) DEFAULT NULL COMMENT 'Health worker TID (e.g. T1001)'");
        $col_check2 = $conn->query("SHOW COLUMNS FROM appointments LIKE 'referrer_tid'");
        if ($col_check2 && $col_check2->num_rows > 0) {
            $has_referrer_tid = true;
        }
    }
    $mobile_phone = $mobile;

    // Ensure created_by_special_tid_id column exists for "bookings created under this Special TID account"
    $has_created_by_special_tid = false;
    $created_by_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'created_by_special_tid_id'");
    if ($created_by_check && $created_by_check->num_rows > 0) {
        $has_created_by_special_tid = true;
    }
    if (!$has_created_by_special_tid) {
        @$conn->query("ALTER TABLE appointments ADD COLUMN created_by_special_tid_id INT NULL DEFAULT NULL COMMENT 'Special TID account that created this booking'");
        $created_by_check2 = $conn->query("SHOW COLUMNS FROM appointments LIKE 'created_by_special_tid_id'");
        if ($created_by_check2 && $created_by_check2->num_rows > 0) {
            $has_created_by_special_tid = true;
        }
    }

    // Ensure attachment_path column exists
    $has_attachment_path = false;
    $col_check_att = $conn->query("SHOW COLUMNS FROM appointments LIKE 'attachment_path'");
    if ($col_check_att && $col_check_att->num_rows > 0) {
        $has_attachment_path = true;
    }
    if (!$has_attachment_path) {
        @$conn->query("ALTER TABLE appointments ADD COLUMN attachment_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to uploaded attachment'");
    }

    if ($has_referrer_tid && $has_payment_method && $has_created_by_special_tid) {
        $ins = $conn->prepare("
            INSERT INTO appointments (
                patient_id, doctor_id, appointment_date, slot_time, appointment_time,
                status, appointment_number, notes, patient_name, mobile, patient_phone,
                age, weight, body_temperature, blood_pressure, pulse, spo2, rbs_fbs, referrer_tid, payment_method, attachment_path, created_by_special_tid_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param(
            "iisssssssssssssssssssi",
            $patient_id,
            $doctor_id,
            $appointment_date,
            $slot_time,
            $slot_time,
            $status,
            $appointment_number,
            $notes,
            $patient_name,
            $mobile,
            $mobile_phone,
            $age,
            $weight,
            $body_temperature,
            $blood_pressure,
            $pulse,
            $spo2,
            $rbs_fbs,
            $referrer_tid,
            $payment_method,
            $attachment_path,
            $booked_by_special_tid_id
        );
    } else if ($has_referrer_tid && $has_payment_method) {
        $ins = $conn->prepare("
            INSERT INTO appointments (
                patient_id, doctor_id, appointment_date, slot_time, appointment_time,
                status, appointment_number, notes, patient_name, mobile, patient_phone,
                age, weight, body_temperature, blood_pressure, pulse, spo2, rbs_fbs, referrer_tid, payment_method, attachment_path
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param(
            "iisssssssssssssssssss",
            $patient_id,
            $doctor_id,
            $appointment_date,
            $slot_time,
            $slot_time,
            $status,
            $appointment_number,
            $notes,
            $patient_name,
            $mobile,
            $mobile_phone,
            $age,
            $weight,
            $body_temperature,
            $blood_pressure,
            $pulse,
            $spo2,
            $rbs_fbs,
            $referrer_tid,
            $payment_method,
            $attachment_path
        );
    } else if ($has_referrer_tid && $has_created_by_special_tid) {
        $ins = $conn->prepare("
            INSERT INTO appointments (
                patient_id, doctor_id, appointment_date, slot_time, appointment_time,
                status, appointment_number, notes, patient_name, mobile, patient_phone,
                age, weight, body_temperature, blood_pressure, pulse, spo2, rbs_fbs, referrer_tid, attachment_path, created_by_special_tid_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param(
            "iissssssssssssssssssi",
            $patient_id,
            $doctor_id,
            $appointment_date,
            $slot_time,
            $slot_time,
            $status,
            $appointment_number,
            $notes,
            $patient_name,
            $mobile,
            $mobile_phone,
            $age,
            $weight,
            $body_temperature,
            $blood_pressure,
            $pulse,
            $spo2,
            $rbs_fbs,
            $referrer_tid,
            $attachment_path,
            $booked_by_special_tid_id
        );
    } else if ($has_referrer_tid) {
        $ins = $conn->prepare("
            INSERT INTO appointments (
                patient_id, doctor_id, appointment_date, slot_time, appointment_time,
                status, appointment_number, notes, patient_name, mobile, patient_phone,
                age, weight, body_temperature, blood_pressure, pulse, spo2, rbs_fbs, referrer_tid, attachment_path
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param(
            "iissssssssssssssssss",
            $patient_id,
            $doctor_id,
            $appointment_date,
            $slot_time,
            $slot_time,
            $status,
            $appointment_number,
            $notes,
            $patient_name,
            $mobile,
            $mobile_phone,
            $age,
            $weight,
            $body_temperature,
            $blood_pressure,
            $pulse,
            $spo2,
            $rbs_fbs,
            $referrer_tid,
            $attachment_path
        );
    } else if ($has_created_by_special_tid) {
        $ins = $conn->prepare("
            INSERT INTO appointments (
                patient_id, doctor_id, appointment_date, slot_time, appointment_time,
                status, appointment_number, notes, patient_name, mobile, patient_phone,
                age, weight, body_temperature, blood_pressure, pulse, spo2, rbs_fbs, attachment_path, created_by_special_tid_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param(
            "iisssssssssssssssssi",
            $patient_id,
            $doctor_id,
            $appointment_date,
            $slot_time,
            $slot_time,
            $status,
            $appointment_number,
            $notes,
            $patient_name,
            $mobile,
            $mobile_phone,
            $age,
            $weight,
            $body_temperature,
            $blood_pressure,
            $pulse,
            $spo2,
            $rbs_fbs,
            $attachment_path,
            $booked_by_special_tid_id
        );
    } else {
        $ins = $conn->prepare("
            INSERT INTO appointments (
                patient_id, doctor_id, appointment_date, slot_time, appointment_time,
                status, appointment_number, notes, patient_name, mobile, patient_phone,
                age, weight, body_temperature, blood_pressure, pulse, spo2, rbs_fbs, attachment_path
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param(
            "iisssssssssssssssss",
            $patient_id,
            $doctor_id,
            $appointment_date,
            $slot_time,
            $slot_time,
            $status,
            $appointment_number,
            $notes,
            $patient_name,
            $mobile,
            $mobile_phone,
            $age,
            $weight,
            $body_temperature,
            $blood_pressure,
            $pulse,
            $spo2,
            $rbs_fbs,
            $attachment_path
        );
    }
    if (!$ins) {
        $conn->rollback();
        $msg = "Prepare failed: " . $conn->error;
        error_log($msg);
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    }

    if (!$ins->execute()) {
        $msg = "Execute failed: " . $ins->error . " (Conn Error: " . $conn->error . ")";
        $conn->rollback();
        $ins->close();
        $conn->close();
        error_log('book-appointment insert error: ' . $msg);
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    }
    $appointment_id = $conn->insert_id;
    $ins->close();

    $booking_number = 'APT' . str_pad($appointment_id, 5, '0', STR_PAD_LEFT);
    $upd = $conn->prepare("UPDATE appointments SET appointment_number = ? WHERE id = ?");
    $upd->bind_param("si", $booking_number, $appointment_id);
    $upd->execute();
    $upd->close();

    $conn->commit();
    $conn->close();

    $dateFormatted = $d->format('M d, Y');
    $time12 = date('g:i A', strtotime($slot_time));
    echo json_encode([
        'success' => true,
        'message' => 'Booking saved.',
        'appointment_id' => $appointment_id,
        'booking_number' => $booking_number,
        'summary' => [
            'date_time' => $dateFormatted . ', ' . $time12,
            'full_name' => $patient_name,
            'mobile' => $mobile,
            'age' => $age ?: '—',
            'weight' => $weight ?: '—',
            'body_temperature' => $body_temperature ?: '—',
            'blood_pressure' => $blood_pressure ?: '—',
            'pulse' => $pulse ?: '—',
            'spo2' => $spo2 ?: '—',
            'rbs_fbs' => $rbs_fbs ?: '—',
            'attachment' => $attachment_path ? basename($attachment_path) : '—',
            'symptoms' => $notes ? (strlen($notes) > 80 ? substr($notes, 0, 80) . '...' : $notes) : '—'
        ]
    ]);
} catch (Exception $e) {
    if (isset($conn)) @$conn->rollback();
    error_log('book-appointment: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Booking failed. Please try again.']);
}
