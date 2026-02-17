<?php
/**
 * Change Patient Password Handler
 * Handles patient password change requests
 */

// Disable error display to prevent breaking JSON response
ini_set('display_errors', 0);

// Start output buffering
ob_start();

// Set content type to JSON
header('Content-Type: application/json');

// Include configuration
$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Configuration file not found']);
    exit;
}
require_once $config_path;

// Check if patient is logged in
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$patient_id = $_SESSION['patient_id'];
$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Validation
if (empty($current_password)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Current password is required']);
    exit;
}

if (empty($new_password) || strlen($new_password) < 6) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
    exit;
}

if ($new_password !== $confirm_password) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'New password and confirm password do not match']);
    exit;
}

try {
    $conn = getDBConnection();

    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $patient = $result->fetch_assoc();
    $stmt->close();

    // Verify current password
    if (!password_verify($current_password, $patient['password'])) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        $conn->close();
        exit;
    }

    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password
    $stmt = $conn->prepare("UPDATE patients SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $patient_id);

    if ($stmt->execute()) {
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    ob_clean();
    error_log("Change password error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
?>
