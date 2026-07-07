<?php
require_once __DIR__ . '/config.php';

try {
    $conn = getDBConnection();
    
    // Set a completed appointment to today's date for patient 8 (Namirah Chowdhury Aanaya) or the latest patient
    // to test follow-up widget.
    $today = date('Y-m-d');
    
    $stmt = $conn->prepare("
        UPDATE appointments 
        SET appointment_date = ?, 
            status = 'completed', 
            follow_up_type = 'with_report' 
        WHERE status = 'completed' 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $stmt->bind_param("s", $today);
    
    if ($stmt->execute()) {
        $stmt_select = $conn->query("SELECT id, patient_id, doctor_id, patient_name, appointment_date FROM appointments WHERE status = 'completed' ORDER BY id DESC LIMIT 1");
        $appointment = $stmt_select->fetch_assoc();
        
        echo "<h3>Success!</h3>";
        echo "We have updated the latest completed appointment to today's date.<br><br>";
        echo "<b>Details:</b><br>";
        echo "- Appointment ID: #" . $appointment['id'] . "<br>";
        echo "- Patient ID: " . $appointment['patient_id'] . " (" . $appointment['patient_name'] . ")<br>";
        echo "- Doctor ID: " . $appointment['doctor_id'] . "<br>";
        echo "- Date Set To: " . $appointment['appointment_date'] . "<br>";
        echo "- Follow-up Type: Follow-up with Report (Free)<br><br>";
        echo "<b>How to Test:</b><br>";
        echo "1. Log in to the patient account associated with this appointment.<br>";
        echo "2. Go to the dashboard. You will now see the <b>Active Follow-up Eligibility</b> widget!<br>";
    } else {
        echo "Error updating appointment: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
