<?php
/**
 * Multi-User Registration Handler
 * Handles patient, doctor, and healthcare registration form submissions
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

// Clear output buffer and set content type to JSON
ob_clean();
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get user type and input data
$user_type = isset($_POST['user_type']) ? trim($_POST['user_type']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$bmdc_no = isset($_POST['bmdc_no']) ? trim($_POST['bmdc_no']) : '';
$nid_number = isset($_POST['nid_number']) ? trim($_POST['nid_number']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate user type
$valid_user_types = ['patient', 'doctor', 'healthcare'];
if (!in_array($user_type, $valid_user_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid user type']);
    exit;
}

// Validation based on user type
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
} elseif (strlen($name) < 2) {
    $errors[] = 'Name must be at least 2 characters';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters';
}

// User type specific validations
switch ($user_type) {
    case 'doctor':
        if (empty($phone)) {
            $errors[] = 'Phone number is required';
        } elseif (strlen($phone) < 10) {
            $errors[] = 'Phone number must be at least 10 digits';
        }

        if (empty($bmdc_no)) {
            $errors[] = 'BMDC number is required';
        } elseif (strlen($bmdc_no) < 5) {
            $errors[] = 'BMDC number must be at least 5 characters';
        }
        break;

    case 'healthcare':
        if (empty($nid_number)) {
            $errors[] = 'NID number is required';
        } elseif (strlen($nid_number) < 10) {
            $errors[] = 'NID number must be at least 10 characters';
        }
        break;

    case 'patient':
        // No additional validations for patients
        break;
}

// If there are validation errors, return them
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
    exit;
}

try {
    $conn = getDBConnection();

    // Check if email already exists in any user table
    $email_check_tables = ['patients', 'doctors', 'healthcare_providers'];
    foreach ($email_check_tables as $table) {
        $stmt = $conn->prepare("SELECT id FROM {$table} WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();
    }

    // User type specific duplicate checks
    switch ($user_type) {
        case 'doctor':
            // Check if BMDC number already exists
            $stmt = $conn->prepare("SELECT id FROM doctors WHERE bmdc_no = ?");
            $stmt->bind_param("s", $bmdc_no);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'BMDC number already registered']);
                $stmt->close();
                $conn->close();
                exit;
            }
            $stmt->close();

            // Check if phone already exists
            $stmt = $conn->prepare("SELECT id FROM doctors WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Phone number already registered']);
                $stmt->close();
                $conn->close();
                exit;
            }
            $stmt->close();
            break;

        case 'healthcare':
            // Check if NID number already exists
            $stmt = $conn->prepare("SELECT id FROM healthcare_providers WHERE nid_number = ?");
            $stmt->bind_param("s", $nid_number);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'NID number already registered']);
                $stmt->close();
                $conn->close();
                exit;
            }
            $stmt->close();
            break;

        case 'patient':
            // No additional duplicate checks for patients
            break;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert based on user type
    $user_id = null;
    $redirect_url = 'login.html';

    switch ($user_type) {
        case 'patient':
            $stmt = $conn->prepare("INSERT INTO patients (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            $redirect_url = 'login.html';
            break;

        case 'doctor':
            $stmt = $conn->prepare("INSERT INTO doctors (name, email, phone, bmdc_no, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $bmdc_no, $hashed_password);
            $redirect_url = 'doctor-profile-settings.php';
            break;

        case 'healthcare':
            $stmt = $conn->prepare("INSERT INTO healthcare_providers (name, email, nid_number, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $nid_number, $hashed_password);
            $redirect_url = 'login.html';
            break;
    }

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;

        // Set session variables based on user type
        switch ($user_type) {
            case 'patient':
                $_SESSION['patient_id'] = $user_id;
                $_SESSION['patient_name'] = $name;
                $_SESSION['patient_email'] = $email;
                $_SESSION['user_type'] = 'patient';
                break;

            case 'doctor':
                $_SESSION['doctor_id'] = $user_id;
                $_SESSION['doctor_name'] = $name;
                $_SESSION['doctor_email'] = $email;
                $_SESSION['user_type'] = 'doctor';
                break;

            case 'healthcare':
                $_SESSION['healthcare_id'] = $user_id;
                $_SESSION['healthcare_name'] = $name;
                $_SESSION['healthcare_email'] = $email;
                $_SESSION['user_type'] = 'healthcare';
                break;
        }

        $_SESSION['logged_in'] = true;

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful!',
            'user_id' => $user_id,
            'user_type' => $user_type,
            'redirect' => $redirect_url
        ]);
    } else {
        throw new Exception("Registration failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again later.', 'error' => $e->getMessage()]);
}
?>

