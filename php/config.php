<?php
/**
 * Database Configuration File
 * Update these values according to your database settings
 */

// Database configuration live database
// define('DB_HOST', 'localhost');
// define('DB_USER', 'telerxb2_telerx');
// define('DB_PASS', '&+;*LkaHNYztJ+{E');
// define('DB_NAME', 'telerxb2_telerx_db');

// Database configuration local kamran

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '123');
define('DB_NAME', 'telerx_db');


// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'telerx_db');

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error . ". Please check your database credentials in config.php");
    }
    
    // Set charset to utf8mb4 for proper character encoding
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone (adjust according to your location)
date_default_timezone_set('Asia/Dhaka');

// Error reporting (disable in production)
// Note: display_errors is set per-file for API endpoints to prevent breaking JSON
error_reporting(E_ALL);
// ini_set('display_errors', 1); // Commented out - set per file as needed


// Auto-login from remember me cookie
function checkRememberMe() {
    if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_token'])) {
        try {
            $conn = getDBConnection();
            $token = $_COOKIE['remember_token'];

            // Check doctors table
            $stmt = $conn->prepare("SELECT * FROM doctors WHERE remember_token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $_SESSION['logged_in'] = true;
                $_SESSION['user_type'] = 'doctor';
                $_SESSION['doctor_id'] = $user['id'];
                $_SESSION['doctor_name'] = $user['name'];
                $_SESSION['doctor_email'] = $user['email'];
                $_SESSION['profile_image'] = $user['profile_image'] ?? 'assets/img/doctors-dashboard/doctor-profile-img.jpg';
                return true;
            }

            // Check healthcare_providers table
            $stmt = $conn->prepare("SELECT * FROM healthcare_providers WHERE remember_token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $_SESSION['logged_in'] = true;
                $_SESSION['user_type'] = 'healthcare';
                $_SESSION['healthcare_id'] = $user['id'];
                $_SESSION['healthcare_name'] = $user['name'];
                $_SESSION['healthcare_email'] = $user['email'];
                $_SESSION['profile_image'] = $user['profile_image'] ?? 'assets/img/avatar/default-avatar.jpg';
                return true;
            }

            $conn->close();
        } catch (Exception $e) {
            error_log("Remember me error: " . $e->getMessage());
        }
    }
    return false;
}

// Call this at the start of every page
checkRememberMe();

?>
