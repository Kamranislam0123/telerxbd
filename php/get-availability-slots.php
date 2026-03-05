<?php
/**
 * Get Doctor Availability Slots
 * GET doctor_id (required) - returns JSON { monday: [ { period, periodLabel, times: [] }, ... ], ... }
 */

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['success' => false, 'message' => 'Configuration not found']);
    exit;
}
require_once $config_path;

$doctor_id = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;
if ($doctor_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid doctor']);
    exit;
}

$slots = [
    'monday' => [], 'tuesday' => [], 'wednesday' => [], 'thursday' => [],
    'friday' => [], 'saturday' => [], 'sunday' => []
];
echo json_encode(['success' => true, 'slots' => $slots]);
