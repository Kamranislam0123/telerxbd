<?php
/**
 * Save Doctor Profile Settings Handler
 * Handles saving different sections of doctor profile settings
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

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$doctor_id = $_SESSION['doctor_id'];
$section = isset($_POST['section']) ? $_POST['section'] : '';

try {
    $conn = getDBConnection();

    switch ($section) {
        case 'all':
            // Handle complete profile form submission

            // Basic Information
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $consultation_fee = isset($_POST['consultation_fee']) ? (float)$_POST['consultation_fee'] : 0;
            $account_number = isset($_POST['account_number']) ? trim($_POST['account_number']) : '';
            $degrees = isset($_POST['degrees']) ? trim($_POST['degrees']) : '';
            $currently_working = isset($_POST['currently_working']) ? trim($_POST['currently_working']) : '';
            $bmdc_no = isset($_POST['bmdc_no']) ? trim($_POST['bmdc_no']) : '';
            $department = isset($_POST['department']) ? trim($_POST['department']) : '';
            $present_address = isset($_POST['present_address']) ? trim($_POST['present_address']) : '';
            $experience_years = isset($_POST['experience_years']) ? (int)$_POST['experience_years'] : 0;
            $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';

            // Handle profile image upload
            $profile_image = '';
            $upload_errors = [];

            if (isset($_FILES['profile_image'])) {
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
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                    if (!in_array($file['type'], $allowed_types)) {
                        $upload_errors[] = "Invalid file type. Only JPG, PNG, GIF, and SVG are allowed.";
                    }

                    // If no errors, proceed with upload
                    if (empty($upload_errors)) {
                        $upload_dir = '../assets/img/doctors/';

                        // Ensure directory exists and is writable
                        if (!is_dir($upload_dir)) {
                            if (!mkdir($upload_dir, 0755, true)) {
                                $upload_errors[] = "Failed to create upload directory.";
                            }
                        }

                        if (is_writable($upload_dir)) {
                            // Generate unique filename
                            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $file_name = 'doctor_' . $doctor_id . '_' . time() . '.' . $extension;
                            $upload_path = $upload_dir . $file_name;

                            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                                $profile_image = 'assets/img/doctors/' . $file_name;
                            } else {
                                $upload_errors[] = "Failed to save uploaded file.";
                            }
                        } else {
                            $upload_errors[] = "Upload directory is not writable.";
                        }
                    }
                }

                // Log any errors for debugging
                if (!empty($upload_errors)) {
                    error_log("Profile image upload errors for doctor $doctor_id: " . implode(', ', $upload_errors));
                }
            }

            // Handle document uploads
            $bmdc_certificate = '';
            if (isset($_FILES['bmdc_certificate']) && $_FILES['bmdc_certificate']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                if (in_array($_FILES['bmdc_certificate']['type'], $allowed_types)) {
                    $upload_dir = '../assets/uploads/certificates/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $file_name = 'bmdc_' . $doctor_id . '_' . time() . '.' . pathinfo($_FILES['bmdc_certificate']['name'], PATHINFO_EXTENSION);
                    $upload_path = $upload_dir . $file_name;

                    if (move_uploaded_file($_FILES['bmdc_certificate']['tmp_name'], $upload_path)) {
                        $bmdc_certificate = 'assets/uploads/certificates/' . $file_name;
                    }
                }
            }

            $nid_card = '';
            if (isset($_FILES['nid_card']) && $_FILES['nid_card']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                if (in_array($_FILES['nid_card']['type'], $allowed_types)) {
                    $upload_dir = '../assets/uploads/documents/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $file_name = 'nid_' . $doctor_id . '_' . time() . '.' . pathinfo($_FILES['nid_card']['name'], PATHINFO_EXTENSION);
                    $upload_path = $upload_dir . $file_name;

                    if (move_uploaded_file($_FILES['nid_card']['tmp_name'], $upload_path)) {
                        $nid_card = 'assets/uploads/documents/' . $file_name;
                    }
                }
            }

            $degrees_certificate = '';
            if (isset($_FILES['degrees_certificate']) && $_FILES['degrees_certificate']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                if (in_array($_FILES['degrees_certificate']['type'], $allowed_types)) {
                    $upload_dir = '../assets/uploads/certificates/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $file_name = 'degrees_' . $doctor_id . '_' . time() . '.' . pathinfo($_FILES['degrees_certificate']['name'], PATHINFO_EXTENSION);
                    $upload_path = $upload_dir . $file_name;

                    if (move_uploaded_file($_FILES['degrees_certificate']['tmp_name'], $upload_path)) {
                        $degrees_certificate = 'assets/uploads/certificates/' . $file_name;
                    }
                }
            }

            // Update doctor basic information
            if (!empty($name) || !empty($email) || !empty($phone) || !empty($bmdc_no)) {
                $stmt = $conn->prepare("UPDATE doctors SET name = ?, email = ?, phone = ?, bmdc_no = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $name, $email, $phone, $bmdc_no, $doctor_id);
                $stmt->execute();
                $stmt->close();
            }

            // Update or insert doctor profile
            $stmt = $conn->prepare("
                INSERT INTO doctor_profiles (doctor_id, bio, specialty, consultation_fee, experience_years, profile_image, gender, account_number, degrees, currently_working, department, present_address, bmdc_certificate, nid_card, degrees_certificate)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                bio = VALUES(bio),
                specialty = VALUES(specialty),
                consultation_fee = VALUES(consultation_fee),
                experience_years = VALUES(experience_years),
                profile_image = IF(VALUES(profile_image) != '', VALUES(profile_image), profile_image),
                gender = VALUES(gender),
                account_number = VALUES(account_number),
                degrees = VALUES(degrees),
                currently_working = VALUES(currently_working),
                department = VALUES(department),
                present_address = VALUES(present_address),
                bmdc_certificate = IF(VALUES(bmdc_certificate) != '', VALUES(bmdc_certificate), bmdc_certificate),
                nid_card = IF(VALUES(nid_card) != '', VALUES(nid_card), nid_card),
                degrees_certificate = IF(VALUES(degrees_certificate) != '', VALUES(degrees_certificate), degrees_certificate)
            ");
            $stmt->bind_param("issdissdsssssss", $doctor_id, $bio, $department, $consultation_fee, $experience_years, $profile_image, $gender, $account_number, $degrees, $currently_working, $department, $present_address, $bmdc_certificate, $nid_card, $degrees_certificate);
            $stmt->execute();
            $stmt->close();

            // Handle business hours
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            foreach ($days as $day) {
                $start_time = isset($_POST[$day . '_start']) ? $_POST[$day . '_start'] : null;
                $end_time = isset($_POST[$day . '_end']) ? $_POST[$day . '_end'] : null;
                $is_available = isset($_POST[$day . '_available']) ? 1 : 0;

                // Check if business hour already exists for this day and doctor
                $stmt = $conn->prepare("
                    SELECT id FROM doctor_business_hours
                    WHERE doctor_id = ? AND day_of_week = ?
                ");
                $day_capitalized = ucfirst($day);
                $stmt->bind_param("is", $doctor_id, $day_capitalized);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();

                if ($result->num_rows > 0) {
                    // Update existing
                    $stmt = $conn->prepare("
                        UPDATE doctor_business_hours
                        SET start_time = ?, end_time = ?, is_available = ?
                        WHERE doctor_id = ? AND day_of_week = ?
                    ");
                    $stmt->bind_param("ssiis", $start_time, $end_time, $is_available, $doctor_id, $day_capitalized);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    // Insert new
                    $stmt = $conn->prepare("
                        INSERT INTO doctor_business_hours (doctor_id, day_of_week, start_time, end_time, is_available)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param("isssi", $doctor_id, $day_capitalized, $start_time, $end_time, $is_available);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            break;

        case 'basic':
            // Handle basic profile information
            $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
            $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
            $display_name = isset($_POST['display_name']) ? trim($_POST['display_name']) : '';
            $designation = isset($_POST['designation']) ? trim($_POST['designation']) : '';
            $speciality = isset($_POST['speciality']) ? trim($_POST['speciality']) : '';
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $experience = isset($_POST['experience']) ? (int)$_POST['experience'] : 0;
            $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
            $languages = isset($_POST['languages']) ? trim($_POST['languages']) : '';
            $consultation_fee = isset($_POST['consultation_fee']) ? (float)$_POST['consultation_fee'] : 0;

            // Handle profile image upload
            $profile_image = '';
            $upload_errors = [];

            if (isset($_FILES['profile_image'])) {
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
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                    if (!in_array($file['type'], $allowed_types)) {
                        $upload_errors[] = "Invalid file type. Only JPG, PNG, GIF, and SVG are allowed.";
                    }

                    // If no errors, proceed with upload
                    if (empty($upload_errors)) {
                        $upload_dir = '../assets/img/doctors/';

                        // Ensure directory exists and is writable
                        if (!is_dir($upload_dir)) {
                            if (!mkdir($upload_dir, 0755, true)) {
                                $upload_errors[] = "Failed to create upload directory.";
                            }
                        }

                        if (is_writable($upload_dir)) {
                            // Generate unique filename
                            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $file_name = 'doctor_' . $doctor_id . '_' . time() . '.' . $extension;
                            $upload_path = $upload_dir . $file_name;

                            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                                $profile_image = 'assets/img/doctors/' . $file_name;
                            } else {
                                $upload_errors[] = "Failed to save uploaded file.";
                            }
                        } else {
                            $upload_errors[] = "Upload directory is not writable.";
                        }
                    }
                }

                // Log any errors for debugging
                if (!empty($upload_errors)) {
                    error_log("Profile image upload errors for doctor $doctor_id: " . implode(', ', $upload_errors));
                }
            }

            // Update or insert doctor profile
            $stmt = $conn->prepare("
                INSERT INTO doctor_profiles (doctor_id, bio, specialty, languages_spoken, consultation_fee, experience_years, profile_image)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                bio = VALUES(bio),
                specialty = VALUES(specialty),
                languages_spoken = VALUES(languages_spoken),
                consultation_fee = VALUES(consultation_fee),
                experience_years = VALUES(experience_years),
                profile_image = IF(VALUES(profile_image) != '', VALUES(profile_image), profile_image)
            ");
            $stmt->bind_param("isssdis", $doctor_id, $bio, $speciality, $languages, $consultation_fee, $experience, $profile_image);
            $stmt->execute();
            $stmt->close();

            // Update doctor name if provided
            if (!empty($display_name)) {
                $stmt = $conn->prepare("UPDATE doctors SET name = ? WHERE id = ?");
                $stmt->bind_param("si", $display_name, $doctor_id);
                $stmt->execute();
                $stmt->close();
                $_SESSION['doctor_name'] = $display_name;
            }

            break;

        case 'experience':
            // Handle experience information
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $hospital_name = isset($_POST['hospital_name']) ? trim($_POST['hospital_name']) : '';
            $years_of_experience = isset($_POST['years_of_experience']) ? trim($_POST['years_of_experience']) : '';
            $location = isset($_POST['location']) ? trim($_POST['location']) : '';
            $employment_type = isset($_POST['employment_type']) ? $_POST['employment_type'] : 'Full Time';
            $job_description = isset($_POST['job_description']) ? trim($_POST['job_description']) : '';
            $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
            $end_date = isset($_POST['end_date']) && !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $currently_working = isset($_POST['currently_working']) ? 1 : 0;

            // Handle hospital logo upload
            $hospital_logo = '';
            if (isset($_FILES['hospital_logo']) && $_FILES['hospital_logo']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($_FILES['hospital_logo']['type'], $allowed_types)) {
                    $upload_dir = '../assets/img/hospitals/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $file_name = 'hospital_' . $doctor_id . '_' . time() . '.' . pathinfo($_FILES['hospital_logo']['name'], PATHINFO_EXTENSION);
                    $upload_path = $upload_dir . $file_name;

                    if (move_uploaded_file($_FILES['hospital_logo']['tmp_name'], $upload_path)) {
                        $hospital_logo = 'assets/img/hospitals/' . $file_name;
                    }
                }
            }

            $stmt = $conn->prepare("
                INSERT INTO doctor_experiences (doctor_id, title, hospital_name, years_of_experience, location, employment_type, job_description, start_date, end_date, currently_working, hospital_logo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("issssssssiss", $doctor_id, $title, $hospital_name, $years_of_experience, $location, $employment_type, $job_description, $start_date, $end_date, $currently_working, $hospital_logo);
            $stmt->execute();
            $stmt->close();

            break;

        case 'education':
            // Handle education information
            $degree = isset($_POST['degree']) ? trim($_POST['degree']) : '';
            $institution = isset($_POST['institution']) ? trim($_POST['institution']) : '';
            $year_of_completion = isset($_POST['year_of_completion']) ? (int)$_POST['year_of_completion'] : null;
            $grade = isset($_POST['grade']) ? trim($_POST['grade']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';

            $stmt = $conn->prepare("
                INSERT INTO doctor_education (doctor_id, degree, institution, year_of_completion, grade, description)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ississ", $doctor_id, $degree, $institution, $year_of_completion, $grade, $description);
            $stmt->execute();
            $stmt->close();

            break;

        case 'awards':
            // Handle awards information
            $award_name = isset($_POST['award_name']) ? trim($_POST['award_name']) : '';
            $award_year = isset($_POST['award_year']) ? (int)$_POST['award_year'] : null;
            $awarded_by = isset($_POST['awarded_by']) ? trim($_POST['awarded_by']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';

            // Handle award certificate upload
            $award_certificate = '';
            if (isset($_FILES['award_certificate']) && $_FILES['award_certificate']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                if (in_array($_FILES['award_certificate']['type'], $allowed_types)) {
                    $upload_dir = '../assets/img/awards/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $file_name = 'award_' . $doctor_id . '_' . time() . '.' . pathinfo($_FILES['award_certificate']['name'], PATHINFO_EXTENSION);
                    $upload_path = $upload_dir . $file_name;

                    if (move_uploaded_file($_FILES['award_certificate']['tmp_name'], $upload_path)) {
                        $award_certificate = 'assets/img/awards/' . $file_name;
                    }
                }
            }

            $stmt = $conn->prepare("
                INSERT INTO doctor_awards (doctor_id, award_name, award_year, awarded_by, description, award_certificate)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("isisss", $doctor_id, $award_name, $award_year, $awarded_by, $description, $award_certificate);
            $stmt->execute();
            $stmt->close();

            break;

        case 'insurance':
            // Handle insurance information
            $insurance_name = isset($_POST['insurance_name']) ? trim($_POST['insurance_name']) : '';
            $insurance_provider = isset($_POST['insurance_provider']) ? trim($_POST['insurance_provider']) : '';
            $policy_number = isset($_POST['policy_number']) ? trim($_POST['policy_number']) : '';
            $coverage_amount = isset($_POST['coverage_amount']) ? (float)$_POST['coverage_amount'] : 0;
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';

            $stmt = $conn->prepare("
                INSERT INTO doctor_insurances (doctor_id, insurance_name, insurance_provider, policy_number, coverage_amount, description)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("isssds", $doctor_id, $insurance_name, $insurance_provider, $policy_number, $coverage_amount, $description);
            $stmt->execute();
            $stmt->close();

            break;

        case 'clinics':
            // Handle clinic information
            $clinic_name = isset($_POST['clinic_name']) ? trim($_POST['clinic_name']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $city = isset($_POST['city']) ? trim($_POST['city']) : '';
            $state = isset($_POST['state']) ? trim($_POST['state']) : '';
            $zip_code = isset($_POST['zip_code']) ? trim($_POST['zip_code']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $website = isset($_POST['website']) ? trim($_POST['website']) : '';
            $consultation_fee = isset($_POST['consultation_fee']) ? (float)$_POST['consultation_fee'] : 0;

            // Handle clinic logo upload
            $clinic_logo = '';
            if (isset($_FILES['clinic_logo']) && $_FILES['clinic_logo']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($_FILES['clinic_logo']['type'], $allowed_types)) {
                    $upload_dir = '../assets/img/clinics/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $file_name = 'clinic_' . $doctor_id . '_' . time() . '.' . pathinfo($_FILES['clinic_logo']['name'], PATHINFO_EXTENSION);
                    $upload_path = $upload_dir . $file_name;

                    if (move_uploaded_file($_FILES['clinic_logo']['tmp_name'], $upload_path)) {
                        $clinic_logo = 'assets/img/clinics/' . $file_name;
                    }
                }
            }

            $stmt = $conn->prepare("
                INSERT INTO doctor_clinics (doctor_id, clinic_name, address, city, state, zip_code, phone, email, website, consultation_fee, clinic_logo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("issssssssss", $doctor_id, $clinic_name, $address, $city, $state, $zip_code, $phone, $email, $website, $consultation_fee, $clinic_logo);
            $stmt->execute();
            $stmt->close();

            break;

        case 'business_hours':
            // Handle business hours
            $clinic_id = isset($_POST['clinic_id']) ? (int)$_POST['clinic_id'] : null;

            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            foreach ($days as $day) {
                $start_time = isset($_POST[$day . '_start']) ? $_POST[$day . '_start'] : null;
                $end_time = isset($_POST[$day . '_end']) ? $_POST[$day . '_end'] : null;
                $is_available = isset($_POST[$day . '_available']) ? 1 : 0;

                // Skip if no clinic selected or times are empty
                if (!$clinic_id || (!$is_available && empty($start_time))) {
                    continue;
                }

                // Check if business hour already exists for this day and clinic
                $stmt = $conn->prepare("
                    SELECT id FROM doctor_business_hours
                    WHERE doctor_id = ? AND clinic_id = ? AND day_of_week = ?
                ");
                $day_capitalized = ucfirst($day);
                $stmt->bind_param("iis", $doctor_id, $clinic_id, $day_capitalized);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();

                if ($result->num_rows > 0) {
                    // Update existing
                    $stmt = $conn->prepare("
                        UPDATE doctor_business_hours
                        SET start_time = ?, end_time = ?, is_available = ?
                        WHERE doctor_id = ? AND clinic_id = ? AND day_of_week = ?
                    ");
                    $stmt->bind_param("ssiiis", $start_time, $end_time, $is_available, $doctor_id, $clinic_id, $day_capitalized);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    // Insert new
                    $stmt = $conn->prepare("
                        INSERT INTO doctor_business_hours (doctor_id, clinic_id, day_of_week, start_time, end_time, is_available)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param("iisssi", $doctor_id, $clinic_id, $day_capitalized, $start_time, $end_time, $is_available);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            break;

        default:
            throw new Exception("Invalid section specified");
    }

    // Close connection
    $conn->close();

    // Return JSON success response
    echo json_encode([
        'success' => true,
        'message' => 'Profile settings updated successfully!',
        'section' => $section,
        'profile_image' => $profile_image
    ]);
    exit;

} catch (Exception $e) {
    error_log("Profile settings save error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save profile settings. Please try again.',
        'error' => $e->getMessage()
    ]);
    exit;
}
?>
