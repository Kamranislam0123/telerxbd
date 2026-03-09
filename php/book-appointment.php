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

$patient_id = isset($_SESSION['patient_id']) ? (int) $_SESSION['patient_id'] : 0;
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
$age = isset($_POST['age']) ? trim($_POST['age']) : '';
$weight = isset($_POST['weight']) ? trim($_POST['weight']) : '';
$body_temperature = isset($_POST['body_temperature']) ? trim($_POST['body_temperature']) : '';
$blood_pressure = isset($_POST['blood_pressure']) ? trim($_POST['blood_pressure']) : '';
$pulse = isset($_POST['pulse']) ? trim($_POST['pulse']) : '';
$spo2 = isset($_POST['spo2']) ? trim($_POST['spo2']) : '';
$rbs_fbs = isset($_POST['rbs_fbs']) ? trim($_POST['rbs_fbs']) : '';
// Accept TID from either field name (booking form uses booking_telerx_id, JS sends telerx_id)
$referrer_tid = isset($_POST['telerx_id']) ? trim($_POST['telerx_id']) : (isset($_POST['booking_telerx_id']) ? trim($_POST['booking_telerx_id']) : '');

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

    if ($has_referrer_tid) {
        $ins = $conn->prepare("
            INSERT INTO appointments (
                patient_id, doctor_id, appointment_date, slot_time, appointment_time,
                status, appointment_number, notes, patient_name, mobile, patient_phone,
                age, weight, body_temperature, blood_pressure, pulse, spo2, rbs_fbs, referrer_tid
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
            $referrer_tid
        );
    } else {
        $ins = $conn->prepare("
            INSERT INTO appointments (
                patient_id, doctor_id, appointment_date, slot_time, appointment_time,
                status, appointment_number, notes, patient_name, mobile, patient_phone,
                age, weight, body_temperature, blood_pressure, pulse, spo2, rbs_fbs
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param(
            "iissssssssssssssss",
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
            $rbs_fbs
        );
    }
    if (!$ins->execute()) {
        $conn->rollback();
        $ins->close();
        $conn->close();
        error_log('book-appointment insert: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to save booking.']);
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
            'attachment' => '—',
            'symptoms' => $notes ? (strlen($notes) > 80 ? substr($notes, 0, 80) . '...' : $notes) : '—'
        ]
    ]);
} catch (Exception $e) {
    if (isset($conn)) @$conn->rollback();
    error_log('book-appointment: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Booking failed. Please try again.']);
}
