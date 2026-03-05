<?php
/**
 * Get available booking slots for a doctor on a date.
 * Uses doctor_availability_ranges (optimized): get ranges for that weekday, expand to 15-min slots.
 * GET: doctor_id, slot_date (Y-m-d).
 */
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['success' => false, 'message' => 'Configuration not found', 'slots' => []]);
    exit;
}
require_once $config_path;
require_once __DIR__ . '/slot-helpers.php';

$doctor_id = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;
$slot_date = isset($_GET['slot_date']) ? trim($_GET['slot_date']) : '';

if ($doctor_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid doctor', 'slots' => []]);
    exit;
}

$d = DateTime::createFromFormat('Y-m-d', $slot_date);
if (!$d || $d->format('Y-m-d') !== $slot_date) {
    echo json_encode(['success' => false, 'message' => 'Invalid date', 'slots' => []]);
    exit;
}

// 0=Sunday, 1=Monday, ... 6=Saturday (PHP date('w'))
$day_of_week = (int) $d->format('w');

try {
    $conn = getDBConnection();

    $check = $conn->query("SHOW TABLES LIKE 'doctor_availability_ranges'");
    if (!$check || $check->num_rows === 0) {
        $conn->close();
        echo json_encode(['success' => true, 'slots' => []]);
        exit;
    }

    $stmt = $conn->prepare("SELECT start_time, end_time FROM doctor_availability_ranges WHERE doctor_id = ? AND day_of_week = ? ORDER BY start_time");
    $stmt->bind_param("ii", $doctor_id, $day_of_week);
    $stmt->execute();
    $result = $stmt->get_result();
    $ranges = [];
    while ($row = $result->fetch_assoc()) {
        $ranges[] = $row;
    }
    $stmt->close();

    $slots = rangesToSlotTimes($ranges);

    // Exclude already booked slots (appointments table)
    $checkApp = $conn->query("SHOW TABLES LIKE 'appointments'");
    if ($checkApp && $checkApp->num_rows > 0) {
        $stmt2 = $conn->prepare("SELECT slot_time FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND (status IS NULL OR status != 'cancelled')");
        $stmt2->bind_param("is", $doctor_id, $slot_date);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $booked = [];
        while ($row = $res2->fetch_assoc()) {
            $booked[] = $row['slot_time'];
        }
        $stmt2->close();
        $slots = array_values(array_diff($slots, $booked));
        sort($slots);
    }

    $conn->close();
    echo json_encode(['success' => true, 'slots' => $slots]);
} catch (Exception $e) {
    error_log('get-available-slots: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'slots' => []]);
}
