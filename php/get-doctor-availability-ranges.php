<?php
/**
 * Get doctor availability for a weekday: returns 15-min slot_times (expanded from ranges).
 * GET: day_of_week (0-6). If omitted, returns slots for all 7 days (keys 0-6).
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
$day_of_week = isset($_GET['day_of_week']) ? (int) $_GET['day_of_week'] : null;

try {
    $conn = getDBConnection();

    $check = $conn->query("SHOW TABLES LIKE 'doctor_availability_ranges'");
    if (!$check || $check->num_rows === 0) {
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Table not found. Run php/run-migration-availability-ranges.php', 'slots' => []]);
        exit;
    }

    if ($day_of_week !== null && ($day_of_week < 0 || $day_of_week > 6)) {
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Invalid day_of_week', 'slots' => []]);
        exit;
    }

    $days = $day_of_week !== null ? [$day_of_week] : [0, 1, 2, 3, 4, 5, 6];
    $result = [];

    $stmt = $conn->prepare("SELECT start_time, end_time FROM doctor_availability_ranges WHERE doctor_id = ? AND day_of_week = ? ORDER BY start_time");
    foreach ($days as $d) {
        $stmt->bind_param("ii", $doctor_id, $d);
        $stmt->execute();
        $res = $stmt->get_result();
        $ranges = [];
        while ($row = $res->fetch_assoc()) {
            $ranges[] = $row;
        }
        $result[(string)$d] = rangesToSlotTimes($ranges);
    }
    $stmt->close();
    $conn->close();

    if ($day_of_week !== null) {
        echo json_encode(['success' => true, 'slots' => $result[(string)$day_of_week]]);
    } else {
        echo json_encode(['success' => true, 'slots' => $result]);
    }
} catch (Exception $e) {
    error_log('get-doctor-availability-ranges: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load slots', 'slots' => $day_of_week !== null ? [] : array_fill_keys(['0','1','2','3','4','5','6'], [])]);
}
