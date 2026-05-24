<?php
/**
 * Diagnostic Script - Video Call Notifications
 * Load this script at: https://yourdomain.com/php/debug-call.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== TeleRx Debug Diagnostics ===\n\n";

// 1. Session Information
echo "1. Active Session Details:\n";
echo "Session Logged In: " . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'Yes' : 'No') . "\n";
echo "User Type: " . ($_SESSION['user_type'] ?? 'N/A') . "\n";
if (isset($_SESSION['patient_id'])) {
    echo "Patient ID: " . $_SESSION['patient_id'] . "\n";
}
if (isset($_SESSION['healthcare_id'])) {
    echo "Healthcare ID: " . $_SESSION['healthcare_id'] . "\n";
    echo "Healthcare TID: " . ($_SESSION['healthcare_id'] ?? 'N/A') . "\n";
}
if (isset($_SESSION['doctor_id'])) {
    echo "Doctor ID: " . $_SESSION['doctor_id'] . "\n";
}
echo "\n";

// 2. Server Times Comparison
try {
    $conn = getDBConnection();
    
    $php_time = date('Y-m-d H:i:s');
    
    $res = $conn->query("SELECT NOW() as db_time, @@system_time_zone as sys_tz, @@time_zone as conn_tz");
    $db_row = $res->fetch_assoc();
    $db_time = $db_row['db_time'];
    
    echo "2. Server Time Information:\n";
    echo "PHP Time (Dhaka): $php_time\n";
    echo "MySQL Time (NOW()): $db_time\n";
    echo "MySQL System Timezone: " . $db_row['sys_tz'] . "\n";
    echo "MySQL Connection Timezone: " . $db_row['conn_tz'] . "\n";
    
    $time_diff = strtotime($php_time) - strtotime($db_time);
    echo "Time difference (PHP - MySQL): " . $time_diff . " seconds\n";
    echo "\n";
    
    // 3. Active Calls in Database
    echo "3. Active Calling Appointments in Database:\n";
    $res_calls = $conn->query("
        SELECT id, patient_id, doctor_id, status, call_status, call_started_at 
        FROM appointments 
        WHERE call_status IS NOT NULL
    ");
    
    if ($res_calls && $res_calls->num_rows > 0) {
        while ($row = $res_calls->fetch_assoc()) {
            echo " - Appointment ID: " . $row['id'] . "\n";
            echo "   Patient ID: " . $row['patient_id'] . "\n";
            echo "   Doctor ID: " . $row['doctor_id'] . "\n";
            echo "   Appointment Status: " . $row['status'] . "\n";
            echo "   Call Status: " . $row['call_status'] . "\n";
            echo "   Call Started At: " . $row['call_started_at'] . "\n";
            
            $start_diff = strtotime($db_time) - strtotime($row['call_started_at']);
            echo "   Seconds since call started (according to MySQL NOW()): " . $start_diff . " seconds\n";
        }
    } else {
        echo "No appointments found with active call_status (either call was not started or was cleared).\n";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
