<?php
/**
 * Download Medical History File Handler - TeleRx Bangladesh
 */

session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/subscription-helper.php';

// Ensure patient is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

$patient_id = isset($_SESSION['patient_id']) ? (int)$_SESSION['patient_id'] : 0;

// Check if patient has an active PREMIUM subscription
$active_sub = getActiveSubscription($patient_id);
$is_premium_subscriber = false;

if ($active_sub) {
    $plan_code = strtolower($active_sub['plan_code'] ?? '');
    $plan_name = strtolower($active_sub['plan_name'] ?? '');
    if (strpos($plan_code, 'premium') !== false || strpos($plan_name, 'premium') !== false) {
        $is_premium_subscriber = true;
    }
}

if (!$is_premium_subscriber) {
    // Premium Subscription required to download medical history files
    header('Location: ../patient-medical-history.php');
    exit;
}

$file_name = isset($_GET['file']) ? trim($_GET['file']) : '';
$file_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Sanitize filename to avoid path traversal
$base_name = basename($file_name);
$medical_dir = realpath(__DIR__ . '/../uploads/medical_history');

if (!$medical_dir) {
    // If directory doesn't exist, try creating it
    $target_dir = __DIR__ . '/../uploads/medical_history';
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    $medical_dir = realpath($target_dir);
}

// Database check if file_id is provided
if ($file_id > 0) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT file_path, title FROM patient_medical_history WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();

        if ($res && !empty($res['file_path'])) {
            $base_name = basename($res['file_path']);
        }
    } catch (Exception $e) {
        // Fallback to filename parameter if DB fails
    }
}

if (empty($base_name)) {
    die("Invalid request: No file specified.");
}

$file_path = $medical_dir . DIRECTORY_SEPARATOR . $base_name;

// Verify file exists and is within medical_history folder
if (!file_exists($file_path) || !is_file($file_path)) {
    die("File not found: " . htmlspecialchars($base_name));
}

// Set headers to force download
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $base_name . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// Clear output buffer before serving file
if (ob_get_level()) {
    ob_end_clean();
}

readfile($file_path);
exit;
