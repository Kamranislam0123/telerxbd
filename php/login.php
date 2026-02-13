<?php
/**
 * Multi-User Login Handler
 * Handles login for doctors, health-workers, and patients
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
    
    $user_found = false;
    $user_data = null;
    $user_type = null;
    $redirect_url = 'login.php';
    
    // Check doctors table
    $stmt = $conn->prepare("SELECT id, name, email, phone, bmdc_no, password FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        if (password_verify($password, $user_data['password'])) {
            $user_type = 'doctor';
            $user_found = true;
        }
    }
    $stmt->close();
    
    // If not found in doctors, check healthcare_providers
    if (!$user_found) {
        $stmt = $conn->prepare("SELECT id, name, email, phone, nid_number, tid, password FROM healthcare_providers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            if (password_verify($password, $user_data['password'])) {
                $user_type = 'healthcare';
                $user_found = true;
            }
        }
        $stmt->close();
    }
    
    // If not found, check patients table (if it exists)
    if (!$user_found) {
        try {
            $stmt = $conn->prepare("SELECT id, name, email, password FROM patients WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                if (password_verify($password, $user_data['password'])) {
                    $user_type = 'patient';
                    $user_found = true;
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            // Patients table doesn't exist, skip
        }
    }
    
    // If user not found or password incorrect
    if (!$user_found) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        $conn->close();
        exit;
    }
    
    // Set session variables based on user type
    $_SESSION['logged_in'] = true;
    $_SESSION['user_type'] = $user_type;
    
    switch ($user_type) {
        case 'doctor':
            $_SESSION['doctor_id'] = $user_data['id'];
            $_SESSION['doctor_name'] = $user_data['name'];
            $_SESSION['doctor_email'] = $user_data['email'];
            $_SESSION['doctor_phone'] = $user_data['phone'] ?? '';
            $_SESSION['doctor_bmdc_no'] = $user_data['bmdc_no'] ?? '';
            $redirect_url = 'doctor-profile-settings.php';
            
            // Store session in database for doctors (if table exists)
            try {
                $session_token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', time() + ($remember_me ? 60 * 60 * 24 * 30 : 60 * 60 * 24));
                $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
                $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                
                $session_stmt = $conn->prepare("INSERT INTO doctor_sessions (doctor_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)");
                if ($session_stmt) {
                    $session_stmt->bind_param("issss", $user_data['id'], $session_token, $ip_address, $user_agent, $expires_at);
                    $session_stmt->execute();
                    $session_stmt->close();
                    $_SESSION['session_token'] = $session_token;
                }
            } catch (Exception $session_error) {
                // Session table might not exist, continue without it
                error_log("Session storage error: " . $session_error->getMessage());
            }
            break;
            
        case 'healthcare':
            $_SESSION['healthcare_id'] = $user_data['id'];
            $_SESSION['healthcare_name'] = $user_data['name'];
            $_SESSION['healthcare_email'] = $user_data['email'];
            $_SESSION['healthcare_phone'] = $user_data['phone'] ?? '';
            $_SESSION['healthcare_nid'] = $user_data['nid_number'] ?? '';
            $_SESSION['healthcare_tid'] = $user_data['tid'] ?? '';
            $redirect_url = 'health-worker-profile-settings.php';
            break;
            
        case 'patient':
            $_SESSION['patient_id'] = $user_data['id'];
            $_SESSION['patient_name'] = $user_data['name'];
            $_SESSION['patient_email'] = $user_data['email'];
            $redirect_url = 'index.html';
            break;
    }
    
    // Set session cookie expiration (30 days if remember me, 1 day otherwise)
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
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful!',
        'user_type' => $user_type,
        'user' => [
            'id' => $user_data['id'],
            'name' => $user_data['name'],
            'email' => $user_data['email']
        ],
        'redirect' => $redirect_url
    ]);
    
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

