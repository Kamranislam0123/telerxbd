<?php
/**
 * Save Doctor Availability Slots
 * Expects POST: slots_json = JSON object { monday: [ { period, times: [] }, ... ], ... }
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

if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Availability slots are not available.']);
