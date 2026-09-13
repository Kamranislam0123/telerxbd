<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/subscription-helper.php';

$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$mobile_clean = preg_replace('/[^0-9]/', '', $mobile);

if (strlen($mobile_clean) < 10 || strlen($mobile_clean) > 15) {
    echo json_encode(['success' => false, 'message' => 'Mobile number must contain 10-15 digits.']);
    exit;
}
$mobile = $mobile_clean;

try {
    // ---------------------------------------------------------------
    // Determine caller type: patient or special_tid
    // ---------------------------------------------------------------
    $user_type = $_SESSION['user_type'] ?? '';
    $is_patient     = ($user_type === 'patient'     && isset($_SESSION['patient_id'])     && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true);
    $is_special_tid = ($user_type === 'special_tid' && isset($_SESSION['special_tid_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true);

    if (!$is_patient && !$is_special_tid) {
        echo json_encode(['success' => false, 'message' => 'Please log in to your patient or Special TID account first.']);
        exit;
    }

    // ---------------------------------------------------------------
    // Resolve identity fields for the appointment row
    // ---------------------------------------------------------------
    if ($is_special_tid) {
        // Special TID: emergency is always free, no subscription needed
        $patient_id           = null;             // no patients row
        $special_tid_id       = (int)$_SESSION['special_tid_id'];
        $patient_name         = $_SESSION['special_tid_name'] ?? 'Special TID User';
        $is_covered_by_sub    = true;             // treated as "free / no payment"
        $pay_method           = 'free_special_tid';
        $sub_id_to_store      = null;
        $status               = 'confirmed';
    } else {
        // Regular patient
        $patient_id           = (int)$_SESSION['patient_id'];
        $special_tid_id       = null;
        $patient_name         = $_SESSION['patient_name'] ?? '';
        // Check subscription quota
        $conn_tmp             = getDBConnection();
        $active_sub           = getActiveSubscription($patient_id, $conn_tmp);
        $conn_tmp->close();
        $is_covered_by_sub    = ($active_sub && $active_sub['remaining_calls'] > 0);
        $pay_method           = $is_covered_by_sub ? 'subscription' : 'bkash';
        $sub_id_to_store      = $is_covered_by_sub ? (int)$active_sub['id'] : null;
        $status               = $is_covered_by_sub ? 'confirmed' : 'pending';
    }

    $conn = getDBConnection();
    $conn->begin_transaction();

    // ---------------------------------------------------------------
    // Find Emergency Doctor
    // ---------------------------------------------------------------
    $doctor_email = EMERGENCY_DOCTOR_EMAIL;  // defined in config.php
    $doc_check = $conn->prepare("SELECT id FROM doctors WHERE email = ? LIMIT 1");
    $doc_check->bind_param("s", $doctor_email);
    $doc_check->execute();
    $doc_res = $doc_check->get_result();
    if ($doc_res->num_rows === 0) {
        throw new Exception("Emergency doctor not found in the system.");
    }
    $doctor_id = (int) $doc_res->fetch_assoc()['id'];
    $doc_check->close();

    // ---------------------------------------------------------------
    // Create appointment
    // ---------------------------------------------------------------
    $appointment_date   = date('Y-m-d');
    $slot_time          = date('H:i');
    $appointment_time   = $slot_time;
    $appointment_number = 'EMG00000';
    $is_emergency       = 1;

    $ins = $conn->prepare("
        INSERT INTO appointments (
            patient_id, doctor_id, appointment_date, slot_time, appointment_time,
            status, appointment_number, patient_name, mobile, patient_phone,
            is_emergency, payment_method, subscription_id, created_by_special_tid_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $ins->bind_param(
        "iisssssssssisi",
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
        $is_emergency,
        $pay_method,
        $sub_id_to_store,
        $special_tid_id
    );

    if (!$ins->execute()) {
        throw new Exception("Execute failed: " . $ins->error);
    }
    $appointment_id = $conn->insert_id;
    $ins->close();

    // Update booking number to EMGxxxxx
    $booking_number = 'EMG' . str_pad($appointment_id, 5, '0', STR_PAD_LEFT);
    $upd = $conn->prepare("UPDATE appointments SET appointment_number = ? WHERE id = ?");
    $upd->bind_param("si", $booking_number, $appointment_id);
    $upd->execute();
    $upd->close();

    // Deduct subscription quota only for patients using subscription
    if ($is_patient && $is_covered_by_sub && $patient_id) {
        useEmergencyCallQuota($patient_id, $appointment_id, $conn);
    }

    $conn->commit();
    $conn->close();

    echo json_encode([
        'success'                => true,
        'message'                => 'Emergency booking successful.',
        'appointment_id'         => $appointment_id,
        'covered_by_subscription' => $is_covered_by_sub,
        'is_special_tid'         => $is_special_tid,
    ]);

} catch (Exception $e) {
    if (isset($conn)) @$conn->rollback();
    error_log('book-emergency: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
