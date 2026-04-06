<?php
/**
 * Upload Prescription Handler
 * Handles prescription file uploads from doctors
 */

// Disable error display to prevent breaking JSON response
ini_set('display_errors', 0);

// Start output buffering
ob_start();

// Set content type to JSON
header('Content-Type: application/json');

// Include configuration
require_once __DIR__ . '/config.php';

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['appointment_id']) || empty($_POST['appointment_id'])) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Appointment ID is required']);
        exit;
    }

    $appointment_id = (int)$_POST['appointment_id'];

    try {
        $conn = getDBConnection();
        
        // Verify this appointment belongs to the logged-in doctor
        $stmt = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND doctor_id = ?");
        $stmt->bind_param("ii", $appointment_id, $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid appointment or access denied']);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();

        // Handle file upload
        if (isset($_FILES['prescription_file'])) {
            $file = $_FILES['prescription_file'];
            $upload_errors = [];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $upload_errors[] = "Upload error: " . $file['error'];
            } else {
                // Check file size (5MB limit)
                $max_size = 5 * 1024 * 1024; // 5MB in bytes
                if ($file['size'] > $max_size) {
                    $upload_errors[] = "File too large. Maximum size is 5MB.";
                }

                // Check file type
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                if (!in_array($file['type'], $allowed_types)) {
                    $upload_errors[] = "Invalid file type. Only JPG, PNG, GIF, and PDF are allowed.";
                }

                if (empty($upload_errors)) {
                    $upload_dir = '../assets/prescriptions/';
                    
                    if (!is_dir($upload_dir)) {
                        if (!mkdir($upload_dir, 0755, true)) {
                            ob_clean();
                            echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
                            exit;
                        }
                    }

                    if (is_writable($upload_dir)) {
                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        // Generate unique filename
                        $file_name = 'prescription_' . $appointment_id . '_' . time() . '.' . $extension;
                        $upload_path = $upload_dir . $file_name;

                        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                            // Update database
                            $db_path = 'assets/prescriptions/' . $file_name;
                            $update_stmt = $conn->prepare("UPDATE appointments SET prescription_path = ? WHERE id = ? AND doctor_id = ?");
                            $update_stmt->bind_param("sii", $db_path, $appointment_id, $doctor_id);
                            
                            if ($update_stmt->execute()) {
                                ob_clean();
                                echo json_encode([
                                    'success' => true,
                                    'message' => 'Prescription uploaded successfully',
                                    'prescription_path' => $db_path
                                ]);
                            } else {
                                ob_clean();
                                echo json_encode(['success' => false, 'message' => 'Database update failed']);
                            }
                            $update_stmt->close();
                        } else {
                            ob_clean();
                            echo json_encode(['success' => false, 'message' => 'Failed to save file']);
                        }
                    } else {
                        ob_clean();
                        echo json_encode(['success' => false, 'message' => 'Upload directory is not writable']);
                    }
                } else {
                    ob_clean();
                    echo json_encode(['success' => false, 'message' => implode(", ", $upload_errors)]);
                }
            }
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        }
        
        $conn->close();
    } catch (Exception $e) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
