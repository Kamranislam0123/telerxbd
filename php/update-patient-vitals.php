<?php
/**
 * Update Patient Vitals and Details
 * Updates clinical vitals in appointments table and profile info in patients table.
 */

// Disable error display to prevent breaking JSON response
ini_set('display_errors', 0);
ob_start();
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = (int)($_POST['appointment_id'] ?? 0);
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    
    // Vitals for appointments table
    $age = $_POST['age'] ?? null;
    $weight = $_POST['weight'] ?? null;
    $temp = $_POST['body_temperature'] ?? null;
    $bp = $_POST['blood_pressure'] ?? null;
    $pulse = $_POST['pulse'] ?? null;
    $spo2 = $_POST['spo2'] ?? null;
    $rbs_fbs = $_POST['rbs_fbs'] ?? null;
    
    // Profile info for patients table
    $gender = $_POST['gender'] ?? null;
    $blood_group = $_POST['blood_group'] ?? null;


    if ($appointment_id <= 0) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid Appointment ID']);
        exit;
    }

    try {
        $conn = getDBConnection();
        
        // Verify ownership
        $stmt = $conn->prepare("SELECT id, patient_id FROM appointments WHERE id = ? AND doctor_id = ?");
        $stmt->bind_param("ii", $appointment_id, $doctor_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Access denied or invalid appointment']);
            $stmt->close();
            $conn->close();
            exit;
        }
        $appointment_data = $res->fetch_assoc();
        $real_patient_id = $appointment_data['patient_id'];
        $stmt->close();

        // Start transaction
        $conn->begin_transaction();

        // Update appointment vitals
        $update_appt_sql = "UPDATE appointments SET 
                            age = ?, 
                            weight = ?, 
                            body_temperature = ?, 
                            blood_pressure = ?, 
                            pulse = ?,
                            spo2 = ?,
                            rbs_fbs = ?
                           WHERE id = ?";
        
        $stmt_appt = $conn->prepare($update_appt_sql);
        $stmt_appt->bind_param("sssssssi", $age, $weight, $temp, $bp, $pulse, $spo2, $rbs_fbs, $appointment_id);
        $stmt_appt->execute();
        $stmt_appt->close();

        // Update patient profile if patient_id is valid
        if ($real_patient_id > 0) {
            $update_patient_sql = "UPDATE patients SET 
                                    gender = ?, 
                                    blood_group = ?
                                   WHERE id = ?";
            $stmt_patient = $conn->prepare($update_patient_sql);
            $stmt_patient->bind_param("ssi", $gender, $blood_group, $real_patient_id);
            $stmt_patient->execute();
            $stmt_patient->close();
        }

        $conn->commit();
        
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Patient details updated successfully'
        ]);
        
        $conn->close();
    } catch (Exception $e) {
        if (isset($conn)) $conn->rollback();
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
