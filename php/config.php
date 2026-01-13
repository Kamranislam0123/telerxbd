<?php
/**
 * Database Configuration File
 * Update these values according to your database settings
 */

// Database configuration live
define('DB_HOST', 'localhost');
define('DB_USER', 'telerxb2_telerx');
define('DB_PASS', '&+;*LkaHNYztJ+{E');
define('DB_NAME', 'telerxb2_telerx_db');


// Database configuration local
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '123');
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
?>

