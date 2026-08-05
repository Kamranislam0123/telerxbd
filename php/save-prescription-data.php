<?php
/**
 * Save Prescription Data
 * Saves clinical record to appointments table for PDF generation.
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
    $chief_complaints = $_POST['chief_complaints'] ?? '';
    $on_examination = $_POST['on_examination'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $advice = $_POST['advice'] ?? '';
    $note_reference = $_POST['note_reference'] ?? '';
    $prescription_footer = $_POST['prescription_footer'] ?? '';
    $has_follow_up = $_POST['has_follow_up'] ?? 'no';
    $follow_up_type = $_POST['follow_up_type'] ?? '';
    $follow_up_date = $_POST['follow_up_date'] ?? '';

    if ($has_follow_up === 'yes') {
        $allowed_follow_up = ['with_report', 'without_report'];
        if (!in_array($follow_up_type, $allowed_follow_up, true)) {
            $follow_up_type = null;
        }
        if (empty($follow_up_date)) {
            $follow_up_date = null;
        }
    } else {
        $follow_up_type = null;
        $follow_up_date = null;
    }
    
    // Process medications into JSON
    $medications = [];
    if (isset($_POST['medicine_name']) && is_array($_POST['medicine_name'])) {
        foreach ($_POST['medicine_name'] as $index => $name) {
            if (!empty($name)) {
                $medications[] = [
                    'name' => $name,
                    'dose' => $_POST['medicine_dose'][$index] ?? '',
                    'duration' => $_POST['medicine_duration'][$index] ?? ''
                ];
            }
        }
    }
    $meds_json = json_encode($medications);
 
    if ($appointment_id <= 0) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid Appointment ID']);
        exit;
    }
 
    try {
        $conn = getDBConnection();
        
        // Verify ownership and status
        $stmt = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND doctor_id = ?");
        $stmt->bind_param("ii", $appointment_id, $doctor_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Access denied or invalid appointment']);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();
 
        // Ensure prescription_footer column exists
        $col_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'prescription_footer'");
        if ($col_check->num_rows === 0) {
            $conn->query("ALTER TABLE appointments ADD COLUMN prescription_footer TEXT NULL");
        }
 
        // Ensure note_reference column exists
        $note_col_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'note_reference'");
        if ($note_col_check->num_rows === 0) {
            $conn->query("ALTER TABLE appointments ADD COLUMN note_reference TEXT NULL");
        }
 
        // Ensure follow_up_type column exists
        $follow_up_col_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'follow_up_type'");
        if ($follow_up_col_check->num_rows === 0) {
            $conn->query("ALTER TABLE appointments ADD COLUMN follow_up_type VARCHAR(32) NULL");
        }

        // Ensure follow_up_date column exists and can hold strings
        $follow_up_date_col_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'follow_up_date'");
        if ($follow_up_date_col_check->num_rows === 0) {
            $conn->query("ALTER TABLE appointments ADD COLUMN follow_up_date VARCHAR(50) NULL");
        } else {
            $col_info = $follow_up_date_col_check->fetch_assoc();
            if (stripos($col_info['Type'], 'date') !== false) {
                $conn->query("ALTER TABLE appointments MODIFY COLUMN follow_up_date VARCHAR(50) NULL");
            }
        }
 
        // Update appointment with prescription details
        $update_sql = "UPDATE appointments SET 
                        chief_complaints = ?, 
                        on_examination = ?, 
                        diagnosis = ?, 
                        medications = ?, 
                        advice = ?,
                        note_reference = ?,
                        prescription_footer = ?,
                        follow_up_type = ?,
                        follow_up_date = ?
                       WHERE id = ?";
        
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssssssi", $chief_complaints, $on_examination, $diagnosis, $meds_json, $advice, $note_reference, $prescription_footer, $follow_up_type, $follow_up_date, $appointment_id);
        
        if ($update_stmt->execute()) {
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Prescription data saved successfully',
                'appointment_id' => $appointment_id
            ]);
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $conn->error]);
        }
        
        $update_stmt->close();
        $conn->close();
    } catch (Exception $e) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
