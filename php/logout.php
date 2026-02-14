<?php
/**
 * Doctor Logout Handler
 * Handles doctor logout
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration with correct path (both files in same php folder)
$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    // Even if config not found, destroy session and redirect
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
    header('Location: ../login.php'); // এক লেভেল উপরে যাবে কারণ logout.php php ফোল্ডারের ভিতরে
    exit;
}

require_once $config_path;

try {
    // If session token exists, remove it from database
    if (isset($_SESSION['doctor_id']) && isset($_SESSION['session_token'])) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM doctor_sessions WHERE session_token = ?");
        $stmt->bind_param("s", $_SESSION['session_token']);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
} catch (Exception $e) {
    // Continue with logout even if database cleanup fails
    error_log("Logout error: " . $e->getMessage());
}

// Destroy all session data
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page (এক লেভেল উপরে)
header('Location: ../login.php');
exit;
?>