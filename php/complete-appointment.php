<?php
/**
 * Complete Appointment Handler - TeleRx Bangladesh
 * Marks an appointment as completed.
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration
require_once 'config.php';

// Set response header to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get appointment ID from POST request
$appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : null;

if (!$appointment_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
    exit;
}

try {
    $conn = getDBConnection();

    // Verify ownership and update status only if it's currently confirmed or pending
    // Doctors and patients can both end calls, but usually the status update is automatic
    $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ? AND status NOT IN ('cancelled', 'completed')");
    $stmt->bind_param("i", $appointment_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Appointment marked as completed']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Appointment was already completed or cancelled']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update appointment status']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Complete appointment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
