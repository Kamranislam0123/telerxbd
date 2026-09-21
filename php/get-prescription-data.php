<?php
/**
 * Get Prescription Data
 * Fetches clinical record and doctor sticky note for an appointment.
 */

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

$doctor_id = (int)$_SESSION['doctor_id'];
$appointment_id = (int)($_GET['appointment_id'] ?? $_POST['appointment_id'] ?? 0);

if ($appointment_id <= 0) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid Appointment ID']);
    exit;
}

try {
    $conn = getDBConnection();

    // Ensure sticky_note column exists
    $col_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'sticky_note'");
    if ($col_check->num_rows === 0) {
        $conn->query("ALTER TABLE appointments ADD COLUMN sticky_note TEXT NULL");
    }

    $stmt = $conn->prepare("SELECT id, patient_name, chief_complaints, on_examination, diagnosis, medications, advice, note_reference, prescription_footer, follow_up_type, follow_up_date, sticky_note FROM appointments WHERE id = ? AND doctor_id = ?");
    $stmt->bind_param("ii", $appointment_id, $doctor_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Appointment not found or access denied']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $data = $res->fetch_assoc();
    $stmt->close();
    $conn->close();

    $medications = json_decode($data['medications'] ?? '', true) ?: [];

    ob_clean();
    echo json_encode([
        'success' => true,
        'data' => [
            'appointment_id' => (int)$data['id'],
            'patient_name' => $data['patient_name'] ?? '',
            'chief_complaints' => $data['chief_complaints'] ?? '',
            'on_examination' => $data['on_examination'] ?? '',
            'diagnosis' => $data['diagnosis'] ?? '',
            'medications' => $medications,
            'advice' => $data['advice'] ?? '',
            'note_reference' => $data['note_reference'] ?? '',
            'prescription_footer' => $data['prescription_footer'] ?? '',
            'follow_up_type' => $data['follow_up_type'] ?? '',
            'follow_up_date' => $data['follow_up_date'] ?? '',
            'sticky_note' => $data['sticky_note'] ?? ''
        ]
    ]);
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
