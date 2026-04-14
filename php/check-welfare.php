<?php
/**
 * Check welfare usage limits.
 * POST: tid
 * Limit: A patient can receive welfare a maximum of two times per month using one TID.
 */
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['user_type'] ?? '') !== 'patient') {
    echo json_encode(['success' => false, 'message' => 'Please log in as a patient.']);
    exit;
}

$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['success' => false, 'message' => 'Configuration not found']);
    exit;
}
require_once $config_path;

$tid = isset($_POST['tid']) ? trim($_POST['tid']) : '';
$patient_id = (int) $_SESSION['patient_id'];

if ($tid === '') {
    echo json_encode(['success' => false, 'message' => 'Please enter a TeleRx ID (TID).']);
    exit;
}

try {
    $conn = getDBConnection();

    // 1. Validate the TID exists
    $tid_check = $conn->prepare("SELECT id, tid FROM healthcare_providers WHERE UPPER(TRIM(tid)) = UPPER(TRIM(?)) LIMIT 1");
    $tid_check->bind_param("s", $tid);
    $tid_check->execute();
    $tid_res = $tid_check->get_result();

    if ($tid_res->num_rows === 0) {
        $tid_check->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Invalid TeleRx ID (TID). Please check and try again.']);
        exit;
    }
    
    $tid_row = $tid_res->fetch_assoc();
    $canonical_tid = $tid_row['tid'];
    $tid_check->close();

    // 2. Count usage in the current month by this patient for this TID using Welfare payment method
    // We check for payment_method = 'welfare'. Prior to this column, we only have referrer_tid, but since 
    // the rule is simple, we enforce it strictly based on payment_method if exists, or just the referrer_tid.
    
    // Check if payment_method column exists, just to be safe
    $has_payment_method = false;
    $col_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'payment_method'");
    if ($col_check && $col_check->num_rows > 0) {
        $has_payment_method = true;
    }

    if ($has_payment_method) {
        $usage = $conn->prepare("SELECT COUNT(id) AS usage_count FROM appointments WHERE patient_id = ? AND UPPER(TRIM(referrer_tid)) = UPPER(TRIM(?)) AND payment_method = 'welfare' AND MONTH(appointment_date) = MONTH(CURRENT_DATE()) AND YEAR(appointment_date) = YEAR(CURRENT_DATE()) AND (status IS NULL OR status != 'cancelled')");
    } else {
        $usage = $conn->prepare("SELECT COUNT(id) AS usage_count FROM appointments WHERE patient_id = ? AND UPPER(TRIM(referrer_tid)) = UPPER(TRIM(?)) AND MONTH(appointment_date) = MONTH(CURRENT_DATE()) AND YEAR(appointment_date) = YEAR(CURRENT_DATE()) AND (status IS NULL OR status != 'cancelled')");
    }

    $usage->bind_param("is", $patient_id, $canonical_tid);
    $usage->execute();
    $usage_res = $usage->get_result();
    $usage_count = 0;
    
    if ($usage_res && $usage_res->num_rows > 0) {
        $row = $usage_res->fetch_assoc();
        $usage_count = (int)$row['usage_count'];
    }
    $usage->close();
    $conn->close();

    $max_limit = 2;
    $remaining = $max_limit - $usage_count;

    if ($remaining <= 0) {
        echo json_encode(['success' => false, 'message' => 'Welfare limit exceeded. You have already used this TID ' . $usage_count . ' times this month.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Valid TID. You have ' . $remaining . ' welfare bookings remaining this month with this TID.']);
    }

} catch (Exception $e) {
    error_log('check-welfare.php error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
