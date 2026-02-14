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
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Debug logging (remove in production)
error_log("Registration attempt - User type: $user_type, Name: $name, Email: $email, Phone: " . (empty($phone) ? 'empty' : 'provided') . ", NID: " . (empty($nid_number) ? 'empty' : 'provided'));

// Validate user type
$valid_user_types = ['patient', 'doctor', 'healthcare'];
if (!in_array($user_type, $valid_user_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid user type']);
    exit;
}

// Validation based on user type
$errors = [];

if (empty($name) || strlen(trim($name)) < 2) {
    $errors[] = 'Name is required and must be at least 2 characters';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email address is required';
}

if (empty($password) || strlen($password) < 6) {
    $errors[] = 'Password is required and must be at least 6 characters';
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
        if (empty($phone) || strlen(trim($phone)) == 0) {
            $errors[] = 'Mobile number is required';
        } else {
            // Remove spaces, dashes, and other non-digit characters for validation
            $phone_clean = preg_replace('/[^0-9]/', '', $phone);
            if (strlen($phone_clean) < 10) {
                $errors[] = 'Mobile number must contain at least 10 digits';
            }
        }
        
        if (empty($nid_number) || strlen(trim($nid_number)) == 0) {
            $errors[] = 'NID number is required';
        } elseif (!preg_match('/^[0-9]{10}$|^[0-9]{13}$|^[0-9]{17}$/', trim($nid_number))) {
            $errors[] = 'NID number must be exactly 10, 13, or 17 digits';
        }
        break;

    case 'patient':
        // No additional validations for patients
        break;
}

// If there are validation errors, return them
if (!empty($errors)) {
    http_response_code(400);
    error_log("Validation errors: " . implode(', ', $errors));
    echo json_encode(['success' => false, 'message' => 'Validation failed. Please check the following:', 'errors' => $errors]);
    exit;
}

try {
    $conn = getDBConnection();

    // Check if email already exists in any user table (only check tables that exist)
    $email_check_tables = [];
    
    // Check which tables exist
    $tables_result = $conn->query("SHOW TABLES");
    $existing_tables = [];
    if ($tables_result) {
        while ($row = $tables_result->fetch_array()) {
            $existing_tables[] = $row[0];
        }
    }
    
    // Only check tables that exist
    $tables_to_check = ['patients', 'doctors', 'healthcare_providers'];
    foreach ($tables_to_check as $table) {
        if (in_array($table, $existing_tables)) {
            $email_check_tables[] = $table;
        }
    }
    
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
            // Check if phone already exists
            $stmt = $conn->prepare("SELECT id FROM healthcare_providers WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Mobile number already registered']);
                $stmt->close();
                $conn->close();
                exit;
            }
            $stmt->close();
            
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
    $redirect_url = 'login.php';

    switch ($user_type) {
        case 'patient':
            $stmt = $conn->prepare("INSERT INTO patients (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            $redirect_url = 'login.php';
            break;

        case 'doctor':
            $stmt = $conn->prepare("INSERT INTO doctors (name, email, phone, bmdc_no, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $bmdc_no, $hashed_password);
            $redirect_url = 'doctor-profile-settings.php';
            break;

        case 'healthcare':
            // Check if phone and tid columns exist
            $columns_check = $conn->query("SHOW COLUMNS FROM healthcare_providers LIKE 'phone'");
            $has_phone = $columns_check && $columns_check->num_rows > 0;
            $tid_check_col = $conn->query("SHOW COLUMNS FROM healthcare_providers LIKE 'tid'");
            $has_tid = $tid_check_col && $tid_check_col->num_rows > 0;

            // Generate TID (TeleRx ID) - Format: T1001, T1002, T1003... (only when tid column exists)
            $tid = 'T1001';
            if ($has_tid) {
                $tid_res = $conn->query("SELECT COALESCE(MAX(CAST(SUBSTRING(tid, 2) AS UNSIGNED)), 1000) + 1 AS next_num FROM healthcare_providers WHERE tid REGEXP '^T[0-9]+\$'");
                if ($tid_res && $row = $tid_res->fetch_assoc()) {
                    $tid = 'T' . (int) $row['next_num'];
                }
            }
            
            if ($has_phone && $has_tid) {
                $stmt = $conn->prepare("INSERT INTO healthcare_providers (name, email, phone, nid_number, password, tid) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $name, $email, $phone, $nid_number, $hashed_password, $tid);
            } elseif ($has_phone) {
                $stmt = $conn->prepare("INSERT INTO healthcare_providers (name, email, phone, nid_number, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $email, $phone, $nid_number, $hashed_password);
            } elseif ($has_tid) {
                $stmt = $conn->prepare("INSERT INTO healthcare_providers (name, email, nid_number, password, tid) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $email, $nid_number, $hashed_password, $tid);
            } else {
                $stmt = $conn->prepare("INSERT INTO healthcare_providers (name, email, nid_number, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $nid_number, $hashed_password);
            }
            $redirect_url = 'health-worker-profile-settings.php';
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
                $_SESSION['healthcare_phone'] = $phone;
                $_SESSION['logged_in'] = true;
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

