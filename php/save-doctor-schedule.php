<?php
/**
 * Save doctor's schedule for a single date (doctor_schedule table).
 * POST: slot_date (Y-m-d), slot_times[] (e.g. 09:00, 09:15). Replaces all slots for that date. Max 96/day. No past dates.
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
$slot_date = isset($_POST['slot_date']) ? trim($_POST['slot_date']) : '';
$raw_times = isset($_POST['slot_times']) && is_array($_POST['slot_times']) ? $_POST['slot_times'] : [];

$d = DateTime::createFromFormat('Y-m-d', $slot_date);
if (!$d || $d->format('Y-m-d') !== $slot_date) {
    echo json_encode(['success' => false, 'message' => 'Invalid date']);
    exit;
}

$today = (new DateTime())->setTime(0, 0, 0);
if ($d < $today) {
    echo json_encode(['success' => false, 'message' => 'Cannot add slots for past dates']);
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
    $del = $conn->prepare("DELETE FROM doctor_schedule WHERE doctor_id = ? AND slot_date = ?");
    $del->bind_param("is", $doctor_id, $slot_date);
    $del->execute();
    $del->close();

    if (count($slot_times) > 0) {
        $ins = $conn->prepare("INSERT INTO doctor_schedule (doctor_id, slot_date, slot_time) VALUES (?, ?, ?)");
        foreach ($slot_times as $slot_time) {
            $ins->bind_param("iss", $doctor_id, $slot_date, $slot_time);
            $ins->execute();
        }
        $ins->close();
    }
    $conn->commit();
    $conn->close();
    echo json_encode(['success' => true, 'message' => 'Slots saved successfully']);
} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    error_log('save-doctor-schedule: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save slots']);
}
