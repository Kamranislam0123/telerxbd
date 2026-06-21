<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

try {
    $conn = getDBConnection();
    
    // Fetch emergency appointments for today
    // Let's assume emergency cases are completed when call_status is 'completed'
    // but for now, we just fetch those with is_emergency = 1 and appointment_date = CURDATE()
    // that are NOT cancelled or completed (if we track that).
    // The current table has `status` which is 'confirmed' usually for emergencies.
    
    $sql = "SELECT id, patient_name, patient_phone, mobile, age, weight, body_temperature, blood_pressure, pulse, spo2, status, appointment_time 
            FROM appointments 
            WHERE doctor_id = ? AND is_emergency = 1 AND appointment_date = CURDATE() 
            ORDER BY created_at DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $cases = [];
    while ($row = $res->fetch_assoc()) {
        $cases[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'data' => $cases
    ]);

} catch (Exception $e) {
    error_log('get-emergency-cases: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
