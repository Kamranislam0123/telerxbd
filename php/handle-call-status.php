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
        // Doctors, healthcare workers, and special TID users can always start a call.
        // Patients can start a call ONLY for emergency appointments (is_emergency = 1),
        // so the emergency doctor dashboard shows them as active.
        $allowed = in_array($user_type, ['doctor', 'healthcare', 'special_tid']);

        if (!$allowed && $user_type === 'patient') {
            // Check if this is an emergency appointment for this patient
            $chk = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND is_emergency = 1 AND patient_id = ? LIMIT 1");
            $patient_id_chk = $_SESSION['patient_id'] ?? 0;
            $chk->bind_param("ii", $appointment_id, $patient_id_chk);
            $chk->execute();
            $chk_res = $chk->get_result();
            $allowed = ($chk_res->num_rows > 0);
            $chk->close();
        }

        if (!$allowed) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden: Only providers or emergency patients can start a call']);
            $conn->close();
            exit;
        }

        // Update appointment call status to 'in_progress' and record starting time
        $stmt = $conn->prepare("UPDATE appointments SET call_status = 'in_progress', call_started_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $appointment_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Call started successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to start call']);
        }
        $stmt->close();

    } elseif ($action === 'end_call' || $action === 'decline_call') {
        // Both provider and patient can end/decline
        $stmt = $conn->prepare("UPDATE appointments SET call_status = 'ended', call_started_at = NULL WHERE id = ?");
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
