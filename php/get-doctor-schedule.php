<?php
/**
 * Get doctor's schedule for a single date (doctor_schedule table).
 * GET: slot_date (Y-m-d). Uses session doctor_id.
 */
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['success' => false, 'message' => 'Configuration not found']);
    exit;
}
require_once $config_path;

if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$doctor_id = (int) $_SESSION['doctor_id'];
$slot_date = isset($_GET['slot_date']) ? trim($_GET['slot_date']) : '';

$d = DateTime::createFromFormat('Y-m-d', $slot_date);
if (!$d || $d->format('Y-m-d') !== $slot_date) {
    echo json_encode(['success' => false, 'message' => 'Invalid date', 'slots' => []]);
    exit;
}

try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT slot_time FROM doctor_schedule WHERE doctor_id = ? AND slot_date = ? ORDER BY slot_time");
    $stmt->bind_param("is", $doctor_id, $slot_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $slots = [];
    while ($row = $result->fetch_assoc()) {
        $slots[] = $row['slot_time'];
    }
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => true, 'slots' => $slots]);
} catch (Exception $e) {
    error_log('get-doctor-schedule: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load slots', 'slots' => []]);
}
