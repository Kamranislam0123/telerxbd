<?php
/**
 * Authentication Check Helper
 * Include this file in pages that require doctor login
 */

require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if doctor is logged in
 * @return bool
 */
function isDoctorLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['doctor_id']);
}

/**
 * Require login - redirect to login page if not logged in
 */
function requireDoctorLogin() {
    if (!isDoctorLoggedIn()) {
        header('Location: login.html');
        exit;
    }
}

/**
 * Get current doctor ID
 * @return int|null
 */
function getCurrentDoctorId() {
    return isset($_SESSION['doctor_id']) ? $_SESSION['doctor_id'] : null;
}

/**
 * Get current doctor data
 * @return array|null
 */
function getCurrentDoctor() {
    if (!isDoctorLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['doctor_id'] ?? null,
        'name' => $_SESSION['doctor_name'] ?? null,
        'email' => $_SESSION['doctor_email'] ?? null,
        'phone' => $_SESSION['doctor_phone'] ?? null,
        'bmdc_no' => $_SESSION['doctor_bmdc_no'] ?? null
    ];
}
?>

