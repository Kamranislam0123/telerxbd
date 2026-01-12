<?php
/**
 * Doctor Logout Handler
 * Handles doctor logout
 */

require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // If session token exists, remove it from database
    if (isset($_SESSION['session_token'])) {
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

// Redirect to login page
header('Location: login.html');
exit;
?>

