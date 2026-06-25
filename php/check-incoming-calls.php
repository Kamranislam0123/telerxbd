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
$special_tid_id = $_SESSION['special_tid_id'] ?? null;

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
    } elseif ($user_type === 'healthcare' && $healthcare_tid) {
        $stmt = $conn->prepare("
            SELECT a.id as appointment_id, d.name as doctor_name, dp.profile_image as doctor_image, a.patient_name 
            FROM appointments a 
            JOIN doctors d ON a.doctor_id = d.id 
            LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id 
            WHERE UPPER(TRIM(a.referrer_tid)) = UPPER(TRIM(?)) 
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
    } elseif ($user_type === 'special_tid') {
        // Special TID users: check by referrer_tid OR by created_by_special_tid_id
        // This covers both cases: appointments referred with their TID code,
        // and appointments they created directly (stored via created_by_special_tid_id)
        $conditions = [];
        $bind_types = '';
        $bind_values = [];

        if (!empty($healthcare_tid)) {
            $conditions[] = "UPPER(TRIM(a.referrer_tid)) = UPPER(TRIM(?))";
            $bind_types .= 's';
            $bind_values[] = $healthcare_tid;
        }
        if (!empty($special_tid_id)) {
            $conditions[] = "a.created_by_special_tid_id = ?";
            $bind_types .= 'i';
            $bind_values[] = $special_tid_id;
        }

        if (!empty($conditions)) {
            $query = "
            SELECT a.id as appointment_id, d.name as doctor_name, dp.profile_image as doctor_image, a.patient_name 
            FROM appointments a 
            JOIN doctors d ON a.doctor_id = d.id 
            LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id 
            WHERE (" . implode(' OR ', $conditions) . ") 
              AND a.call_status = 'in_progress' 
              AND a.call_started_at >= NOW() - INTERVAL 5 MINUTE 
            ORDER BY a.call_started_at DESC 
            LIMIT 1
        ";

            $stmt = $conn->prepare($query);
            $stmt->bind_param($bind_types, ...$bind_values);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows > 0) {
                $active_call = $res->fetch_assoc();
            }
            $stmt->close();
        }
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
