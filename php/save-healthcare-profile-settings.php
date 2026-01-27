<?php
/**
 * Save Health-Worker Profile Settings Handler
 * Handles saving health-worker profile settings
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

// Check if health-worker is logged in
if (!isset($_SESSION['healthcare_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$healthcare_id = $_SESSION['healthcare_id'];
$section = isset($_POST['section']) ? $_POST['section'] : 'all';

try {
    $conn = getDBConnection();

    switch ($section) {
        case 'all':
            // Handle complete profile form submission
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $degrees = isset($_POST['degrees']) ? trim($_POST['degrees']) : '';
            $currently_working = isset($_POST['currently_working']) ? trim($_POST['currently_working']) : '';
            $present_address = isset($_POST['present_address']) ? trim($_POST['present_address']) : '';

            // Handle profile image upload
            $profile_image = '';
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $file = $_FILES['profile_image'];
                $max_size = 4 * 1024 * 1024; // 4MB
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                
                if ($file['size'] <= $max_size && in_array($file['type'], $allowed_types)) {
                    $upload_dir = '../assets/img/healthcare/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $file_name = 'healthcare_' . $healthcare_id . '_' . time() . '.' . $extension;
                    $upload_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $profile_image = 'assets/img/healthcare/' . $file_name;
                    }
                }
            }

            // Handle NID file upload
            $nid_file = '';
            if (isset($_FILES['nid_file']) && $_FILES['nid_file']['error'] == 0) {
                $file = $_FILES['nid_file'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                
                if (in_array($file['type'], $allowed_types)) {
                    $upload_dir = '../assets/uploads/documents/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $file_name = 'nid_healthcare_' . $healthcare_id . '_' . time() . '.' . $extension;
                    $upload_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $nid_file = 'assets/uploads/documents/' . $file_name;
                    }
                }
            }

            // Build family members JSON from POST arrays
            $family_members = [];
            if (!empty($_POST['family_relation']) && is_array($_POST['family_relation'])) {
                foreach ($_POST['family_relation'] as $i => $rel) {
                    $name = isset($_POST['family_name'][$i]) ? trim((string) $_POST['family_name'][$i]) : '';
                    $nid  = isset($_POST['family_nid'][$i])  ? trim((string) $_POST['family_nid'][$i])  : '';
                    if ($rel !== '' || $name !== '' || $nid !== '') {
                        $family_members[] = ['relation' => $rel, 'name' => $name, 'nid' => $nid];
                    }
                }
            }
            $family_members_json = json_encode($family_members);

            // Handle degrees certificate upload
            $degrees_certificate = '';
            if (isset($_FILES['degrees_certificate']) && $_FILES['degrees_certificate']['error'] == 0) {
                $file = $_FILES['degrees_certificate'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                
                if (in_array($file['type'], $allowed_types)) {
                    $upload_dir = '../assets/uploads/certificates/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $file_name = 'degrees_healthcare_' . $healthcare_id . '_' . time() . '.' . $extension;
                    $upload_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $degrees_certificate = 'assets/uploads/certificates/' . $file_name;
                    }
                }
            }

            // Update healthcare_providers table
            if (!empty($name) || !empty($email) || !empty($phone)) {
                $update_fields = [];
                $update_values = [];
                $types = '';
                
                if (!empty($name)) {
                    $update_fields[] = "name = ?";
                    $update_values[] = $name;
                    $types .= 's';
                }
                if (!empty($email)) {
                    $update_fields[] = "email = ?";
                    $update_values[] = $email;
                    $types .= 's';
                }
                if (!empty($phone)) {
                    $update_fields[] = "phone = ?";
                    $update_values[] = $phone;
                    $types .= 's';
                }
                
                $update_values[] = $healthcare_id;
                $types .= 'i';
                
                $sql = "UPDATE healthcare_providers SET " . implode(', ', $update_fields) . " WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$update_values);
                $stmt->execute();
                $stmt->close();
            }

            // Check if family_members column exists
            $has_family_col = false;
            $col_check = $conn->query("SHOW COLUMNS FROM healthcare_providers_profiles LIKE 'family_members'");
            if ($col_check && $col_check->num_rows > 0) $has_family_col = true;

            // Update or insert healthcare profile
            if ($has_family_col) {
                $stmt = $conn->prepare("
                    INSERT INTO healthcare_providers_profiles (healthcare_provider_id, profile_image, gender, degrees, currently_working, present_address, nid_file, degrees_certificate, family_members)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    profile_image = IF(VALUES(profile_image) != '', VALUES(profile_image), profile_image),
                    gender = VALUES(gender),
                    degrees = VALUES(degrees),
                    currently_working = VALUES(currently_working),
                    present_address = VALUES(present_address),
                    nid_file = IF(VALUES(nid_file) != '', VALUES(nid_file), nid_file),
                    degrees_certificate = IF(VALUES(degrees_certificate) != '', VALUES(degrees_certificate), degrees_certificate),
                    family_members = VALUES(family_members)
                ");
                $stmt->bind_param("issssssss", $healthcare_id, $profile_image, $gender, $degrees, $currently_working, $present_address, $nid_file, $degrees_certificate, $family_members_json);
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO healthcare_providers_profiles (healthcare_provider_id, profile_image, gender, degrees, currently_working, present_address, nid_file, degrees_certificate)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    profile_image = IF(VALUES(profile_image) != '', VALUES(profile_image), profile_image),
                    gender = VALUES(gender),
                    degrees = VALUES(degrees),
                    currently_working = VALUES(currently_working),
                    present_address = VALUES(present_address),
                    nid_file = IF(VALUES(nid_file) != '', VALUES(nid_file), nid_file),
                    degrees_certificate = IF(VALUES(degrees_certificate) != '', VALUES(degrees_certificate), degrees_certificate)
                ");
                $stmt->bind_param("isssssss", $healthcare_id, $profile_image, $gender, $degrees, $currently_working, $present_address, $nid_file, $degrees_certificate);
            }
            $stmt->execute();
            $stmt->close();

            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Profile settings updated successfully!',
                'profile_image' => $profile_image
            ]);
            break;

        default:
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid section']);
            break;
    }

    $conn->close();
    
} catch (Exception $e) {
    ob_clean();
    error_log("Health-worker profile settings error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save profile settings. Please try again.']);
}
?>
