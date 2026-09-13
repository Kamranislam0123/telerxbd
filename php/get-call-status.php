<?php
/**
 * Get Call Status - TeleRx Bangladesh
 * Returns the current call_status for a given appointment.
 * Used by the patient's emergency-live-dashboard.php to poll whether
 * the emergency doctor has accepted and started the call.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

header('Content-Type: application/json');

// Must be logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;
if ($appointment_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
    exit;
}

try {
    $conn = getDBConnection();

    // Only allow the patient who owns this appointment, or doctors/healthcare workers
    $user_type   = $_SESSION['user_type'] ?? '';
    $patient_id  = $_SESSION['patient_id'] ?? null;
    $doctor_id   = $_SESSION['doctor_id'] ?? null;

    if ($user_type === 'patient' && $patient_id) {
        $stmt = $conn->prepare(
            "SELECT call_status FROM appointments WHERE id = ? AND patient_id = ? LIMIT 1"
        );
        $stmt->bind_param("ii", $appointment_id, $patient_id);
    } elseif (in_array($user_type, ['doctor', 'healthcare', 'special_tid'])) {
        $stmt = $conn->prepare(
            "SELECT call_status FROM appointments WHERE id = ? LIMIT 1"
        );
        $stmt->bind_param("i", $appointment_id);
    } else {
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        $conn->close();
        exit;
    }

    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
    $conn->close();

    if ($res->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found']);
        exit;
    }

    $row = $res->fetch_assoc();
    echo json_encode([
        'success'     => true,
        'call_status' => $row['call_status'] ?? null
    ]);

} catch (Exception $e) {
    error_log("get-call-status.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
