<?php
/**
 * Save doctor availability as time RANGES for a weekday (optimized storage).
 * POST: day_of_week (0-6), slot_times[] (e.g. 09:00, 09:15). Converts to ranges and stores.
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

if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$doctor_id = (int) $_SESSION['doctor_id'];
$day_of_week = isset($_POST['day_of_week']) ? (int) $_POST['day_of_week'] : -1;
$raw_times = isset($_POST['slot_times']) && is_array($_POST['slot_times']) ? $_POST['slot_times'] : [];

if ($day_of_week < 0 || $day_of_week > 6) {
    echo json_encode(['success' => false, 'message' => 'Invalid day of week (0-6)']);
    exit;
}

$slot_times = [];
foreach ($raw_times as $t) {
    $t = preg_replace('/[^0-9:]/', '', trim($t));
    if (strlen($t) >= 4) $slot_times[] = $t;
}
$slot_times = array_unique($slot_times);
if (count($slot_times) > 96) {
    echo json_encode(['success' => false, 'message' => 'Maximum 96 slots per day']);
    exit;
}

try {
    $conn = getDBConnection();
    $conn->begin_transaction();

    $del = $conn->prepare("DELETE FROM doctor_availability_ranges WHERE doctor_id = ? AND day_of_week = ?");
    $del->bind_param("ii", $doctor_id, $day_of_week);
    $del->execute();
    $del->close();

    if (count($slot_times) > 0) {
        $ranges = slotTimesToRanges($slot_times);
        $ins = $conn->prepare("INSERT INTO doctor_availability_ranges (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        foreach ($ranges as $r) {
            $ins->bind_param("iiss", $doctor_id, $day_of_week, $r['start_time'], $r['end_time']);
            $ins->execute();
        }
        $ins->close();
    }

    $conn->commit();
    $conn->close();
    echo json_encode(['success' => true, 'message' => 'Slots saved successfully']);
} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    error_log('save-doctor-availability-ranges: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save slots']);
}
