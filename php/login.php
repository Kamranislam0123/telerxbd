<?php
/**
 * Doctor Login Handler
 * Handles doctor login form submission
 */

// Start output buffering to catch any accidental output
ob_start();

// Turn off error display to prevent warnings from breaking JSON
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// Get the directory of this file
$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    ob_clean(); // Clear any output
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Configuration file not found']);
    exit;
}
require_once $config_path;

// Clear output buffer and set content type to JSON (must be before any output)
ob_clean();
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and sanitize input data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$remember_me = isset($_POST['remember_me']) ? true : false;

// Validation
$errors = [];

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($password)) {
    $errors[] = 'Password is required';
}

// If there are validation errors, return them
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
    exit;
}

try {
    $conn = getDBConnection();
    
    // Find doctor by email
    $stmt = $conn->prepare("SELECT id, name, email, phone, bmdc_no, password FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        $stmt->close();
        $conn->close();
        exit;
    }
    
    $doctor = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $doctor['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        $stmt->close();
        $conn->close();
        exit;
    }
    
    // Set session variables
    $_SESSION['doctor_id'] = $doctor['id'];
    $_SESSION['doctor_name'] = $doctor['name'];
    $_SESSION['doctor_email'] = $doctor['email'];
    $_SESSION['doctor_phone'] = $doctor['phone'];
    $_SESSION['doctor_bmdc_no'] = $doctor['bmdc_no'];
    $_SESSION['logged_in'] = true;
    
    // Set session cookie expiration (30 days if remember me, 1 day otherwise)
    // Note: We can't use ini_set after session_start, so we use setcookie
    $cookie_lifetime = $remember_me ? (60 * 60 * 24 * 30) : (60 * 60 * 24);
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        session_id(),
        time() + $cookie_lifetime,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
    
    // Optional: Store session in database for better security (only if table exists)
    try {
        $session_token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', time() + ($remember_me ? 60 * 60 * 24 * 30 : 60 * 60 * 24));
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $session_stmt = $conn->prepare("INSERT INTO doctor_sessions (doctor_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)");
        if ($session_stmt) {
            $session_stmt->bind_param("issss", $doctor['id'], $session_token, $ip_address, $user_agent, $expires_at);
            $session_stmt->execute();
            $session_stmt->close();
            $_SESSION['session_token'] = $session_token;
        }
    } catch (Exception $session_error) {
        // Session table might not exist, continue without it
        error_log("Session storage error: " . $session_error->getMessage());
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful!',
        'doctor' => [
            'id' => $doctor['id'],
            'name' => $doctor['name'],
            'email' => $doctor['email']
        ],
        'redirect' => 'doctor-profile-settings.php'
    ]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    // In development, show the actual error. In production, hide it.
    $error_message = 'Login failed. Please try again later.';
    if (ini_get('display_errors')) {
        $error_message .= ' Error: ' . $e->getMessage();
    }
    error_log("Login error: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());
    echo json_encode(['success' => false, 'message' => $error_message, 'error' => $e->getMessage()]);
}
?>

