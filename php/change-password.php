<?php
/**
 * Simple Password Reset Handler
 * Uses session to identify user — no email field needed.
 */

ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Must be logged in
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'You must be logged in to change your password.']);
    exit;
}

$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new_password     = isset($_POST['new_password'])     ? $_POST['new_password']     : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Validation
if (empty($current_password)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Current password is required.']);
    exit;
}
if (strlen($new_password) < 6) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
    exit;
}
if ($new_password !== $confirm_password) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'New password and confirm password do not match.']);
    exit;
}
if ($current_password === $new_password) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'New password must be different from the current password.']);
    exit;
}

// Determine user table and ID from session
$user_type = $_SESSION['user_type'] ?? '';

$table_map = [
    'doctor'     => ['table' => 'doctors',              'id_key' => 'doctor_id',     'email_key' => 'doctor_email'],
    'healthcare' => ['table' => 'healthcare_providers',  'id_key' => 'healthcare_id', 'email_key' => 'healthcare_email'],
    'special_tid'=> ['table' => 'special_tid_users',     'id_key' => 'special_tid_id','email_key' => 'special_tid_email'],
    'patient'    => ['table' => 'patients',              'id_key' => 'patient_id',    'email_key' => 'patient_email'],
];

if (!isset($table_map[$user_type])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid session. Please log in again.']);
    exit;
}

$map      = $table_map[$user_type];
$table    = $map['table'];
$user_id  = $_SESSION[$map['id_key']] ?? 0;

if (!$user_id) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Session error. Please log in again.']);
    exit;
}

try {
    $conn = getDBConnection();

    // Fetch stored password hash
    $stmt = $conn->prepare("SELECT password FROM `$table` WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'User account not found.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $row = $result->fetch_assoc();
    $stmt->close();

    // Verify current password
    if (!password_verify($current_password, $row['password'])) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        $conn->close();
        exit;
    }

    // Hash and update new password
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $upd    = $conn->prepare("UPDATE `$table` SET password = ? WHERE id = ?");
    $upd->bind_param('si', $hashed, $user_id);

    if ($upd->execute() && $upd->affected_rows > 0) {
        // Clear requires_password_change flag for patients
        if ($user_type === 'patient') {
            try {
                $clear = $conn->prepare("UPDATE patients SET requires_password_change = 0 WHERE id = ?");
                $clear->bind_param('i', $user_id);
                $clear->execute();
                $clear->close();
            } catch (Exception $e) {
                // Column may not exist, ignore
            }
        }
        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Password changed successfully!']);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Could not update password. Please try again.']);
    }

    $upd->close();
    $conn->close();

} catch (Exception $e) {
    ob_clean();
    error_log('Reset password error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
?>
