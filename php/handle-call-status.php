<?php
/**
 * Handle Call Status Updates - TeleRx Bangladesh
 * Sets calling status for an appointment call session.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : null;
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

if (!$appointment_id || empty($action)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$user_type = $_SESSION['user_type'] ?? '';

try {
    $conn = getDBConnection();

    if ($action === 'start_call') {
        // Only allow doctor, healthcare, or special_tid to start the call
        if (!in_array($user_type, ['doctor', 'healthcare', 'special_tid'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden: Only providers can start a call']);
            $conn->close();
            exit;
        }

        // Update appointment call status to 'calling' and record starting time
        $stmt = $conn->prepare("UPDATE appointments SET call_status = 'calling', call_started_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $appointment_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Call started successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to start call']);
        }
        $stmt->close();

    } elseif ($action === 'end_call' || $action === 'decline_call') {
        // Both provider and patient can end/decline
        $stmt = $conn->prepare("UPDATE appointments SET call_status = NULL, call_started_at = NULL WHERE id = ?");
        $stmt->bind_param("i", $appointment_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Call status cleared successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to clear call status']);
        }
        $stmt->close();

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

    $conn->close();
} catch (Exception $e) {
    error_log("handle-call-status.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
