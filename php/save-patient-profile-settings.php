<?php
/**
 * Save Patient Profile Settings Handler
 * Handles saving patient profile settings
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
$section = isset($_POST['section']) ? $_POST['section'] : '';

try {
    $conn = getDBConnection();

    switch ($section) {
        case 'all':
            // Handle complete profile form submission

            // Basic Information
            $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
            $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
            $name = trim($first_name . ' ' . $last_name);
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $date_of_birth = isset($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : '';
            $blood_group = isset($_POST['blood_group']) ? trim($_POST['blood_group']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $city = isset($_POST['city']) ? trim($_POST['city']) : '';
            $state = isset($_POST['state']) ? trim($_POST['state']) : '';
            $country = isset($_POST['country']) ? trim($_POST['country']) : '';
            $pincode = isset($_POST['pincode']) ? trim($_POST['pincode']) : '';

            // Handle profile image upload
            $profile_image = '';
            $upload_errors = [];

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['profile_image'];

                // Check for upload errors
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $upload_errors[] = "Upload error: " . $file['error'];
                } else {
                    // Check file size (4MB limit as mentioned in UI)
                    $max_size = 4 * 1024 * 1024; // 4MB in bytes
                    if ($file['size'] > $max_size) {
                        $upload_errors[] = "File too large. Maximum size is 4MB.";
                    }

                    // Check file type
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                    if (!in_array($file['type'], $allowed_types)) {
                        $upload_errors[] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
                    }

                    // If no errors, proceed with upload
                    if (empty($upload_errors)) {
                        $upload_dir = '../assets/img/patients/';

                        // Ensure directory exists and is writable
                        if (!is_dir($upload_dir)) {
                            if (!mkdir($upload_dir, 0755, true)) {
                                $upload_errors[] = "Failed to create upload directory.";
                            }
                        }

                        if (empty($upload_errors) && is_writable($upload_dir)) {
                            // Generate unique filename
                            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $new_filename = 'patient_' . $patient_id . '_' . time() . '.' . $file_extension;
                            $upload_path = $upload_dir . $new_filename;

                            // Move uploaded file
                            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                                $profile_image = 'assets/img/patients/' . $new_filename;
                            } else {
                                $upload_errors[] = "Failed to move uploaded file.";
                            }
                        } else {
                            $upload_errors[] = "Upload directory is not writable.";
                        }
                    }
                }
            }

            // If there are upload errors, return them
            if (!empty($upload_errors)) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => implode(' ', $upload_errors)]);
                $conn->close();
                exit;
            }

            // Validate required fields
            if (empty($name)) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Name is required']);
                $conn->close();
                exit;
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Valid email is required']);
                $conn->close();
                exit;
            }

            // Check if email is already taken by another patient
            $check_stmt = $conn->prepare("SELECT id FROM patients WHERE email = ? AND id != ?");
            $check_stmt->bind_param("si", $email, $patient_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            if ($check_result->num_rows > 0) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Email already exists']);
                $check_stmt->close();
                $conn->close();
                exit;
            }
            $check_stmt->close();

            // Build update query dynamically based on available columns
            $update_fields = [];
            $update_values = [];
            $update_types = '';

            // Always update these fields
            $update_fields[] = "name = ?";
            $update_values[] = $name;
            $update_types .= 's';

            $update_fields[] = "email = ?";
            $update_values[] = $email;
            $update_types .= 's';

            // Check if column exists before adding to update
            $columns_to_check = [
                'phone' => $phone,
                'gender' => $gender,
                'date_of_birth' => $date_of_birth,
                'blood_group' => $blood_group,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'pincode' => $pincode,
                'profile_image' => $profile_image
            ];

            // Check which columns exist in the table
            $result = $conn->query("SHOW COLUMNS FROM patients");
            $existing_columns = [];
            while ($row = $result->fetch_assoc()) {
                $existing_columns[] = $row['Field'];
            }

            foreach ($columns_to_check as $column => $value) {
                if (in_array($column, $existing_columns)) {
                    if ($column === 'profile_image' && !empty($value)) {
                        $update_fields[] = "$column = ?";
                        $update_values[] = $value;
                        $update_types .= 's';
                    } elseif ($column !== 'profile_image') {
                        $update_fields[] = "$column = ?";
                        $update_values[] = $value;
                        $update_types .= 's';
                    }
                }
            }

            // Add patient_id to values array
            $update_values[] = $patient_id;
            $update_types .= 'i';

            // Build and execute update query
            $update_query = "UPDATE patients SET " . implode(', ', $update_fields) . " WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            
            if ($stmt) {
                $stmt->bind_param($update_types, ...$update_values);
                $stmt->execute();

                if ($stmt->affected_rows >= 0) {
                    // Update session variables
                    $_SESSION['patient_name'] = $name;
                    $_SESSION['patient_email'] = $email;

                    ob_clean();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Profile updated successfully!',
                        'profile_image' => $profile_image ?: ''
                    ]);
                } else {
                    ob_clean();
                    echo json_encode(['success' => false, 'message' => 'No changes were made']);
                }
                $stmt->close();
            } else {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }

            break;

        default:
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid section']);
            break;
    }

    $conn->close();

} catch (Exception $e) {
    ob_clean();
    error_log("Patient profile settings save error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
