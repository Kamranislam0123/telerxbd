<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$patient_id = $_SESSION['patient_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = (int)($_POST['appointment_id'] ?? 0);
    
    // Vitals for appointments table
    $temp = $_POST['body_temperature'] ?? null;
    $bp = $_POST['blood_pressure'] ?? null;
    $pulse = $_POST['pulse'] ?? null;
    $spo2 = $_POST['spo2'] ?? null;

    if ($appointment_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Appointment ID']);
        exit;
    }

    try {
        $conn = getDBConnection();
        
        // Verify ownership
        $stmt = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND patient_id = ?");
        $stmt->bind_param("ii", $appointment_id, $patient_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Access denied or invalid appointment']);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();

        // Update appointment vitals
        $update_appt_sql = "UPDATE appointments SET 
                            body_temperature = ?, 
                            blood_pressure = ?, 
                            pulse = ?,
                            spo2 = ?
                           WHERE id = ?";
        
        $stmt_appt = $conn->prepare($update_appt_sql);
        $stmt_appt->bind_param("ssssi", $temp, $bp, $pulse, $spo2, $appointment_id);
        $stmt_appt->execute();
        $stmt_appt->close();

        echo json_encode([
            'success' => true,
            'message' => 'Vitals updated successfully'
        ]);
        
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
