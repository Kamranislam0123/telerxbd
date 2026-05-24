<?php
/**
 * Check Incoming Calls - TeleRx Bangladesh
 * Checks if there is an active incoming call for the logged-in patient or healthcare worker.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_type = $_SESSION['user_type'] ?? '';
$patient_id = $_SESSION['patient_id'] ?? null;
$healthcare_tid = $_SESSION['healthcare_tid'] ?? $_SESSION['special_tid_code'] ?? null;

// Only patients and healthcare providers can receive calls
if ($user_type !== 'patient' && $user_type !== 'healthcare' && $user_type !== 'special_tid') {
    echo json_encode(['success' => false, 'message' => 'Not a receiver account type']);
    exit;
}

try {
    $conn = getDBConnection();
    $active_call = null;

    if ($user_type === 'patient' && $patient_id) {
        $stmt = $conn->prepare("
            SELECT a.id as appointment_id, d.name as doctor_name, dp.profile_image as doctor_image 
            FROM appointments a 
            JOIN doctors d ON a.doctor_id = d.id 
            LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id 
            WHERE a.patient_id = ? 
              AND a.call_status = 'in_progress' 
              AND a.call_started_at >= NOW() - INTERVAL 5 MINUTE 
            ORDER BY a.call_started_at DESC 
            LIMIT 1
        ");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $active_call = $res->fetch_assoc();
        }
        $stmt->close();
    } elseif (($user_type === 'healthcare' || $user_type === 'special_tid') && $healthcare_tid) {
        $stmt = $conn->prepare("
            SELECT a.id as appointment_id, d.name as doctor_name, dp.profile_image as doctor_image, a.patient_name 
            FROM appointments a 
            JOIN doctors d ON a.doctor_id = d.id 
            LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id 
            WHERE a.referrer_tid = ? 
              AND a.call_status = 'in_progress' 
              AND a.call_started_at >= NOW() - INTERVAL 5 MINUTE 
            ORDER BY a.call_started_at DESC 
            LIMIT 1
        ");
        $stmt->bind_param("s", $healthcare_tid);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $active_call = $res->fetch_assoc();
        }
        $stmt->close();
    }

    if ($active_call) {
        // Fallback profile image if not set
        if (empty($active_call['doctor_image'])) {
            $active_call['doctor_image'] = 'assets/img/doctors-dashboard/doctor-profile-img.jpg';
        }
        
        echo json_encode([
            'success' => true,
            'call' => [
                'appointment_id' => (int)$active_call['appointment_id'],
                'doctor_name' => $active_call['doctor_name'],
                'doctor_image' => $active_call['doctor_image'],
                'patient_name' => $active_call['patient_name'] ?? null
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'call' => null]);
    }

    $conn->close();
} catch (Exception $e) {
    error_log("check-incoming-calls.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
