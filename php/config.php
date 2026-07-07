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
// Cookie path '/' so session works for all app pages (php/ and root) - fixes health-worker-dashboard redirect
// is_https: support live proxies (Cloudflare, nginx, etc.) that set X-Forwarded-Proto / X-Forwarded-SSL
if (session_status() === PHP_SESSION_NONE) {
    $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && (strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on' || $_SERVER['HTTP_X_FORWARDED_SSL'] === '1'))
        || (!empty($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $is_secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    } else {
        session_set_cookie_params(
            0,
            '/',
            '',
            $is_secure,
            true
        );
    }
    session_start();
}

// Set timezone (adjust according to your location)
date_default_timezone_set('Asia/Dhaka');

// Application base path (web path to app root). Derived from config location so it is
// correct whether config is loaded from login.php, php/login.php, or any other file.
// Example: http://localhost/Telerx → APP_BASE = '/Telerx'; at domain root → ''.
if (!defined('APP_BASE')) {
    $doc_root = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ''), '/');
    $app_root = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: '');
    $app_base = '';
    if ($doc_root !== '' && $app_root !== '' && strpos($app_root, $doc_root) === 0) {
        $app_base = substr($app_root, strlen($doc_root));
        if ($app_base === '/') {
            $app_base = '';
        }
    } else {
        $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        $app_base = (basename($script_dir) === 'php') ? dirname($script_dir) : $script_dir;
        if ($app_base === '/' || $app_base === '\\' || $app_base === '.') {
            $app_base = '';
        }
    }
    define('APP_BASE', $app_base);
}

// Shared emergency doctor account used for emergency video calls
if (!defined('EMERGENCY_DOCTOR_EMAIL')) {
    define('EMERGENCY_DOCTOR_EMAIL', 'emergency@telerx.com');
}

/**
 * Whether the given (or logged-in) doctor is the shared emergency doctor account.
 */
function isEmergencyDoctor($doctor_id = null) {
    if ($doctor_id === null) {
        if (!isset($_SESSION['doctor_id'])) {
            return false;
        }
        $doctor_id = (int) $_SESSION['doctor_id'];
        if (!empty($_SESSION['doctor_email'])) {
            return strcasecmp(trim($_SESSION['doctor_email']), EMERGENCY_DOCTOR_EMAIL) === 0;
        }
    } else {
        $doctor_id = (int) $doctor_id;
    }

    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT email FROM doctors WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        if (!$row || empty($row['email'])) {
            return false;
        }
        return strcasecmp(trim($row['email']), EMERGENCY_DOCTOR_EMAIL) === 0;
    } catch (Exception $e) {
        return false;
    }
}

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
