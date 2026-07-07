<?php
/**
 * Check Follow-up Eligibility
 * Checks if a patient is eligible for a free or discounted follow-up appointment.
 */
ini_set('display_errors', 0);
ob_start();
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

$doctor_id = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;
$mobile = isset($_GET['mobile']) ? trim($_GET['mobile']) : '';
$target_date_str = isset($_GET['appointment_date']) ? trim($_GET['appointment_date']) : '';

if ($doctor_id <= 0 || $mobile === '') {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Missing doctor_id or mobile number',
        'eligible_with_report' => false,
        'eligible_without_report' => false
    ]);
    exit;
}

// Clean mobile number (digits only)
$mobile_clean = preg_replace('/[^0-9]/', '', $mobile);
if (strlen($mobile_clean) < 10) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Invalid mobile number',
        'eligible_with_report' => false,
        'eligible_without_report' => false
    ]);
    exit;
}

try {
    $conn = getDBConnection();
    
    // Resolve patient ID or use mobile directly
    $patient_id = 0;
    $patient_lookup = $conn->prepare("SELECT id FROM patients WHERE phone = ? LIMIT 1");
    if ($patient_lookup) {
        $patient_lookup->bind_param("s", $mobile_clean);
        $patient_lookup->execute();
        $res = $patient_lookup->get_result();
        if ($res && $res->num_rows > 0) {
            $patient_id = (int)$res->fetch_assoc()['id'];
        }
        $patient_lookup->close();
    }
    
    // Find the latest completed appointment for this patient/mobile with the selected doctor
    if ($patient_id > 0) {
        $apt_stmt = $conn->prepare("
            SELECT id, appointment_date, follow_up_type 
            FROM appointments 
            WHERE (patient_id = ? OR mobile = ?) 
              AND doctor_id = ? 
              AND status = 'completed' 
            ORDER BY appointment_date DESC 
            LIMIT 1
        ");
        $apt_stmt->bind_param("isi", $patient_id, $mobile_clean, $doctor_id);
    } else {
        $apt_stmt = $conn->prepare("
            SELECT id, appointment_date, follow_up_type 
            FROM appointments 
            WHERE mobile = ? 
              AND doctor_id = ? 
              AND status = 'completed' 
            ORDER BY appointment_date DESC 
            LIMIT 1
        ");
        $apt_stmt->bind_param("si", $mobile_clean, $doctor_id);
    }
    
    if (!$apt_stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    
    $apt_stmt->execute();
    $result = $apt_stmt->get_result();
    $prev_apt = $result->fetch_assoc();
    $apt_stmt->close();
    $conn->close();
    
    if (!$prev_apt) {
        ob_clean();
        echo json_encode([
            'success' => true,
            'eligible_with_report' => false,
            'eligible_without_report' => false,
            'message' => 'No previous completed appointment found with this doctor.'
        ]);
        exit;
    }
    
    $prev_date_str = $prev_apt['appointment_date'];
    $follow_up_type = $prev_apt['follow_up_type'] ?? '';
    
    // Determine the comparison date (target appointment date or today)
    $target_date = new DateTime();
    if ($target_date_str !== '') {
        $parsed_date = DateTime::createFromFormat('Y-m-d', $target_date_str);
        if ($parsed_date) {
            $target_date = $parsed_date;
        }
    }
    $target_date->setTime(0, 0, 0);
    
    $prev_date = new DateTime($prev_date_str);
    $prev_date->setTime(0, 0, 0);
    
    $interval = $prev_date->diff($target_date);
    $days_diff = (int)$interval->format('%r%a'); // Signed days difference
    
    $eligible_with_report = false;
    $eligible_without_report = false;
    
    if ($days_diff >= 0 && $days_diff <= 14) {
        // Within 14 days
        if ($follow_up_type === 'with_report') {
            $eligible_with_report = true;
            $eligible_without_report = true;
        } else {
            // Either 'without_report' or not explicitly selected, but within 14 days:
            // "If the patient books an appointment within 14 days without a report, they should be able to book the appointment by paying 50% of the fee."
            $eligible_without_report = true;
        }
    }
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'eligible_with_report' => $eligible_with_report,
        'eligible_without_report' => $eligible_without_report,
        'previous_appointment_date' => $prev_date_str,
        'days_since_previous' => $days_diff,
        'prescribed_follow_up_type' => $follow_up_type,
        'message' => 'Eligibility checked successfully'
    ]);
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'eligible_with_report' => false,
        'eligible_without_report' => false
    ]);
}
?>
