<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$mobile_clean = preg_replace('/[^0-9]/', '', $mobile);

if (strlen($mobile_clean) < 10 || strlen($mobile_clean) > 15) {
    echo json_encode(['success' => false, 'message' => 'Mobile number must contain 10-15 digits.']);
    exit;
}
$mobile = $mobile_clean;

try {
    $conn = getDBConnection();
    $conn->begin_transaction();

    // 1. Find or create patient
    $patient_id = 0;
    $patient_name = 'Emergency Patient';
    
    $patient_lookup = $conn->prepare("SELECT id, name FROM patients WHERE phone = ? LIMIT 1");
    $patient_lookup->bind_param("s", $mobile);
    $patient_lookup->execute();
    $patient_res = $patient_lookup->get_result();

    if ($patient_res && $patient_res->num_rows > 0) {
        $existing_patient = $patient_res->fetch_assoc();
        $patient_id = (int) $existing_patient['id'];
        $patient_name = $existing_patient['name'];
    } else {
        // Create new patient
        $base_email = 'emergency' . $mobile . '@auto.telerx.local';
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
            $auto_email = 'emergency' . $mobile . '+' . $counter . '@auto.telerx.local';
        }

        $default_password_hash = password_hash('123456', PASSWORD_DEFAULT);
        $patient_create = $conn->prepare("INSERT INTO patients (name, email, phone, password, requires_password_change) VALUES (?, ?, ?, ?, 1)");
        $patient_create->bind_param("ssss", $patient_name, $auto_email, $mobile, $default_password_hash);
        if (!$patient_create->execute()) {
            throw new Exception("Failed to auto-register patient.");
        }
        $patient_id = (int) $conn->insert_id;
        $patient_create->close();
    }
    $patient_lookup->close();

    // 2. Auto log in the patient
    $_SESSION['logged_in'] = true;
    $_SESSION['user_type'] = 'patient';
    $_SESSION['patient_id'] = $patient_id;
    $_SESSION['patient_name'] = $patient_name;
    $_SESSION['patient_email'] = $auto_email ?? ''; // if new
    $_SESSION['patient_phone'] = $mobile;

    // 3. Find Emergency Doctor ID
    $doctor_email = 'emergency@telerx.com';
    $doc_check = $conn->prepare("SELECT id FROM doctors WHERE email = ? LIMIT 1");
    $doc_check->bind_param("s", $doctor_email);
    $doc_check->execute();
    $doc_res = $doc_check->get_result();
    if ($doc_res->num_rows === 0) {
        throw new Exception("Emergency doctor not found in the system.");
    }
    $doctor_id = (int) $doc_res->fetch_assoc()['id'];
    $doc_check->close();

    // 4. Create appointment
    $appointment_date = date('Y-m-d');
    $slot_time = date('H:i'); // Adjusted format to avoid overflow
    $appointment_time = $slot_time;
    $status = 'confirmed'; // As requested by user, confirm immediately
    $appointment_number = 'EMG00000';
    $is_emergency = 1;

    // Ensure we insert into existing columns
    // the generic book-appointment.php does some conditional column checks, we will just use basic columns
    $ins = $conn->prepare("
        INSERT INTO appointments (
            patient_id, doctor_id, appointment_date, slot_time, appointment_time,
            status, appointment_number, patient_name, mobile, patient_phone, is_emergency
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $ins->bind_param(
        "iissssssssi",
        $patient_id,
        $doctor_id,
        $appointment_date,
        $slot_time,
        $appointment_time,
        $status,
        $appointment_number,
        $patient_name,
        $mobile,
        $mobile,
        $is_emergency
    );

    if (!$ins->execute()) {
        throw new Exception("Execute failed: " . $ins->error);
    }
    $appointment_id = $conn->insert_id;
    $ins->close();

    $booking_number = 'EMG' . str_pad($appointment_id, 5, '0', STR_PAD_LEFT);
    $upd = $conn->prepare("UPDATE appointments SET appointment_number = ? WHERE id = ?");
    $upd->bind_param("si", $booking_number, $appointment_id);
    $upd->execute();
    $upd->close();

    $conn->commit();
    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => 'Emergency booking successful.',
        'appointment_id' => $appointment_id
    ]);

} catch (Exception $e) {
    if (isset($conn)) @$conn->rollback();
    error_log('book-emergency: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
