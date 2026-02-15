<?php
/**
 * Doctor Profile Settings - TeleRx Bangladesh
 * Dynamic profile settings page with all forms in tabs
 */

// Include configuration
$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.php');
    exit;
}
require_once $config_path;

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

try {
    $conn = getDBConnection();

    // Fetch doctor's basic information and profile
    $stmt = $conn->prepare("
        SELECT d.*, dp.*
        FROM doctors d
        LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id
        WHERE d.id = ?
    ");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: login.php');
        exit;
    }

    $doctor = $result->fetch_assoc();

    // Set default values if profile data is missing
    $doctor['bio'] = $doctor['bio'] ?? '';
    $doctor['specialty'] = $doctor['specialty'] ?? '';
    $doctor['languages_spoken'] = $doctor['languages_spoken'] ?? '';
    $doctor['consultation_fee'] = $doctor['consultation_fee'] ?? '';
    $doctor['experience_years'] = $doctor['experience_years'] ?? '';
    $doctor['profile_image'] = $doctor['profile_image'] ?? 'assets/img/doctors-dashboard/doctor-profile-img.jpg';

    // Set default values for new doctor table fields
    $doctor['gender'] = $doctor['gender'] ?? '';
    $doctor['account_number'] = $doctor['account_number'] ?? '';
    $doctor['degrees'] = $doctor['degrees'] ?? '';
    $doctor['currently_working'] = $doctor['currently_working'] ?? '';
    // Map specialty from database to speciality for form (database uses 'specialty', form uses 'speciality')
    $doctor['speciality'] = $doctor['specialty'] ?? ($doctor['speciality'] ?? '');
    $doctor['present_address'] = $doctor['present_address'] ?? '';
    $doctor['district'] = $doctor['district'] ?? '';
    $doctor['bmdc_certificate'] = $doctor['bmdc_certificate'] ?? '';
    $doctor['nid_card'] = $doctor['nid_card'] ?? '';
    $doctor['degrees_certificate'] = $doctor['degrees_certificate'] ?? '';

    // Fetch doctor's experiences
    $stmt = $conn->prepare("SELECT * FROM doctor_experiences WHERE doctor_id = ? ORDER BY start_date DESC");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $experiences = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch doctor's education
    $stmt = $conn->prepare("SELECT * FROM doctor_education WHERE doctor_id = ? ORDER BY year_of_completion DESC");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $education = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch doctor's awards
    $stmt = $conn->prepare("SELECT * FROM doctor_awards WHERE doctor_id = ? ORDER BY award_year DESC");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $awards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch doctor's insurances
    $stmt = $conn->prepare("SELECT * FROM doctor_insurances WHERE doctor_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $insurances = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch doctor's clinics
    $stmt = $conn->prepare("SELECT * FROM doctor_clinics WHERE doctor_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $clinics = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Doctor profile settings error: " . $e->getMessage());
    header('Location: login.php');
    exit;
}

// Split name for form fields
$name_parts = explode(' ', $doctor['name']);
$first_name = $name_parts[0] ?? '';
$last_name = $name_parts[1] ?? '';

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<body>
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center inner-banner">
                <div class="col-md-12 col-12 text-center">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <h3><a href="doctor-profile.php?doctor_id=<?php echo $doctor_id; ?>"><?php echo htmlspecialchars($doctor['name']); ?></a></h3>
                        </ol>
                        <h2 class="breadcrumb-title">Profile Settings</h2>
                    </nav>
                </div>
            </div>
        </div>
        <div class="breadcrumb-bg">
            <img src="assets/img/bg/breadcrumb-bg-01.png" alt="img" class="breadcrumb-bg-01">
            <img src="assets/img/bg/breadcrumb-bg-02.png" alt="img" class="breadcrumb-bg-02">
            <img src="assets/img/bg/breadcrumb-icon.png" alt="img" class="breadcrumb-bg-03">
            <img src="assets/img/bg/breadcrumb-icon.png" alt="img" class="breadcrumb-bg-04">
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Page Content -->
    <div class="content doctor-content">
        <div class="container">
            <div class="row">

                <?php
                include 'doctor-leftside-menu.php';
                ?>

                </div>
                <div class="col-lg-8 col-xl-9">

                    <!-- Single Profile Form -->
                    <form action="php/save-profile-settings.php" method="POST" enctype="multipart/form-data" id="profileForm">
                        <input type="hidden" name="section" value="all">

                        <!-- Profile Image Upload -->
                        <div class="setting-card">
                            <div class="change-avatar img-upload">
                                <div class="profile-img">
                                    <i class="fa-solid fa-file-image"></i>
                                </div>
                                <div class="upload-img">
                                    <h5>Profile Image</h5>
                                    <div class="imgs-load d-flex align-items-center">
                                        <div class="change-photo">
                                            Upload New
                                            <input type="file" class="upload" name="profile_image" accept="image/*">
                                        </div>
                                        <a href="#" class="upload-remove">Remove</a>
                                    </div>
                                    <p class="form-text">Photo size upto 4MB (jpg, jpeg or png format)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="setting-card">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Gender</label>
                                        <select class="form-control" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?php echo (isset($doctor['gender']) && $doctor['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo (isset($doctor['gender']) && $doctor['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                            <option value="Other" <?php echo (isset($doctor['gender']) && $doctor['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Fees (৳)</label>
                                        <input type="number" class="form-control" name="consultation_fee" value="<?php echo htmlspecialchars($doctor['consultation_fee']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Account number: bKash / Rocket Etc.</label>
                                        <input type="text" class="form-control" name="account_number" value="<?php echo htmlspecialchars($doctor['account_number'] ?? ''); ?>" placeholder="Enter your mobile banking account">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Degrees</label>
                                        <input type="text" class="form-control" name="degrees" value="<?php echo htmlspecialchars($doctor['degrees'] ?? ''); ?>" placeholder="e.g., MBBS, MD">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Currently Working / Experience</label>
                                        <input type="text" class="form-control" name="currently_working" value="<?php echo htmlspecialchars($doctor['currently_working'] ?? ''); ?>" placeholder="Current workplace and experience">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">BMDC Number</label>
                                        <input type="text" class="form-control" name="bmdc_no" value="<?php echo htmlspecialchars($doctor['bmdc_no']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Speciality <small class="text-muted">(Select multiple)</small></label>
                                        <?php
                                        // Get the current selected specialities (check both specialty and speciality fields)
                                        $current_speciality_str = !empty($doctor['speciality']) ? $doctor['speciality'] : (!empty($doctor['specialty']) ? $doctor['specialty'] : '');
                                        // Parse comma-separated specialities into an array
                                        $selected_specialities = [];
                                        if (!empty($current_speciality_str)) {
                                            $selected_specialities = array_map('trim', explode(',', $current_speciality_str));
                                        }

                                        // Define all available specialities
                                        $all_specialities = [
                                            'General Physician', 'Pediatrician', 'Gynecologist', 'Dermatologist', 'ENT Specialist',
                                            'Psychiatrist', 'Diabetologist', 'Cardiologist', 'Neurologist', 'Orthopedic Specialist',
                                            'Urologist', 'Gastroenterologist', 'Physiotherapist', 'Pulmonologist', 'Nephrologist',
                                            'Oncologist', 'Sexologist', 'Rheumatologist', 'Allergist/Immunologist', 'Ophthalmologist',
                                            'Psychologist', 'Internal Medicine', 'Family Medicine', 'Physical Medicine'
                                        ];
                                        ?>
                                        <div class="speciality-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px; background-color: #f9f9f9;">
                                            <?php foreach ($all_specialities as $spec): ?>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input speciality-checkbox" type="checkbox" name="speciality[]" value="<?php echo htmlspecialchars($spec); ?>" id="speciality-<?php echo str_replace([' ', '/'], ['-', '-'], strtolower($spec)); ?>" <?php echo in_array($spec, $selected_specialities) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="speciality-<?php echo str_replace([' ', '/'], ['-', '-'], strtolower($spec)); ?>">
                                                        <?php echo htmlspecialchars($spec); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Present Address</label>
                                        <input type="text" class="form-control" name="present_address" value="<?php echo htmlspecialchars($doctor['address'] ?? $doctor['present_address'] ?? ''); ?>" placeholder="Your current address">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">District</label>
                                        <select class="form-control" name="district">
                                            <option value="">Select District</option>
                                            <option value="Bagerhat" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Bagerhat') ? 'selected' : ''; ?>>Bagerhat</option>
                                            <option value="Bandarban" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Bandarban') ? 'selected' : ''; ?>>Bandarban</option>
                                            <option value="Barguna" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Barguna') ? 'selected' : ''; ?>>Barguna</option>
                                            <option value="Barisal" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Barisal') ? 'selected' : ''; ?>>Barisal</option>
                                            <option value="Bhola" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Bhola') ? 'selected' : ''; ?>>Bhola</option>
                                            <option value="Bogra" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Bogra') ? 'selected' : ''; ?>>Bogra</option>
                                            <option value="Brahmanbaria" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Brahmanbaria') ? 'selected' : ''; ?>>Brahmanbaria</option>
                                            <option value="Chandpur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Chandpur') ? 'selected' : ''; ?>>Chandpur</option>
                                            <option value="Chapai Nawabganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Chapai Nawabganj') ? 'selected' : ''; ?>>Chapai Nawabganj</option>
                                            <option value="Chattogram" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Chattogram') ? 'selected' : ''; ?>>Chattogram</option>
                                            <option value="Chuadanga" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Chuadanga') ? 'selected' : ''; ?>>Chuadanga</option>
                                            <option value="Comilla" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Comilla') ? 'selected' : ''; ?>>Comilla</option>
                                            <option value="Cox's Bazar" <?php echo (isset($doctor['district']) && $doctor['district'] == "Cox's Bazar") ? 'selected' : ''; ?>>Cox's Bazar</option>
                                            <option value="Dhaka" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Dhaka') ? 'selected' : ''; ?>>Dhaka</option>
                                            <option value="Dinajpur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Dinajpur') ? 'selected' : ''; ?>>Dinajpur</option>
                                            <option value="Faridpur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Faridpur') ? 'selected' : ''; ?>>Faridpur</option>
                                            <option value="Feni" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Feni') ? 'selected' : ''; ?>>Feni</option>
                                            <option value="Gaibandha" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Gaibandha') ? 'selected' : ''; ?>>Gaibandha</option>
                                            <option value="Gazipur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Gazipur') ? 'selected' : ''; ?>>Gazipur</option>
                                            <option value="Gopalganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Gopalganj') ? 'selected' : ''; ?>>Gopalganj</option>
                                            <option value="Habiganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Habiganj') ? 'selected' : ''; ?>>Habiganj</option>
                                            <option value="Jamalpur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Jamalpur') ? 'selected' : ''; ?>>Jamalpur</option>
                                            <option value="Jessore" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Jessore') ? 'selected' : ''; ?>>Jessore</option>
                                            <option value="Jhalokati" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Jhalokati') ? 'selected' : ''; ?>>Jhalokati</option>
                                            <option value="Jhenaidah" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Jhenaidah') ? 'selected' : ''; ?>>Jhenaidah</option>
                                            <option value="Joypurhat" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Joypurhat') ? 'selected' : ''; ?>>Joypurhat</option>
                                            <option value="Khagrachari" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Khagrachari') ? 'selected' : ''; ?>>Khagrachari</option>
                                            <option value="Khulna" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Khulna') ? 'selected' : ''; ?>>Khulna</option>
                                            <option value="Kishoreganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Kishoreganj') ? 'selected' : ''; ?>>Kishoreganj</option>
                                            <option value="Kurigram" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Kurigram') ? 'selected' : ''; ?>>Kurigram</option>
                                            <option value="Kushtia" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Kushtia') ? 'selected' : ''; ?>>Kushtia</option>
                                            <option value="Lakshmipur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Lakshmipur') ? 'selected' : ''; ?>>Lakshmipur</option>
                                            <option value="Lalmonirhat" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Lalmonirhat') ? 'selected' : ''; ?>>Lalmonirhat</option>
                                            <option value="Madaripur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Madaripur') ? 'selected' : ''; ?>>Madaripur</option>
                                            <option value="Magura" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Magura') ? 'selected' : ''; ?>>Magura</option>
                                            <option value="Manikganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Manikganj') ? 'selected' : ''; ?>>Manikganj</option>
                                            <option value="Meherpur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Meherpur') ? 'selected' : ''; ?>>Meherpur</option>
                                            <option value="Moulvibazar" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Moulvibazar') ? 'selected' : ''; ?>>Moulvibazar</option>
                                            <option value="Munshiganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Munshiganj') ? 'selected' : ''; ?>>Munshiganj</option>
                                            <option value="Mymensingh" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Mymensingh') ? 'selected' : ''; ?>>Mymensingh</option>
                                            <option value="Naogaon" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Naogaon') ? 'selected' : ''; ?>>Naogaon</option>
                                            <option value="Narail" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Narail') ? 'selected' : ''; ?>>Narail</option>
                                            <option value="Narayanganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Narayanganj') ? 'selected' : ''; ?>>Narayanganj</option>
                                            <option value="Narsingdi" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Narsingdi') ? 'selected' : ''; ?>>Narsingdi</option>
                                            <option value="Natore" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Natore') ? 'selected' : ''; ?>>Natore</option>
                                            <option value="Nawabganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Nawabganj') ? 'selected' : ''; ?>>Nawabganj</option>
                                            <option value="Netrakona" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Netrakona') ? 'selected' : ''; ?>>Netrakona</option>
                                            <option value="Nilphamari" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Nilphamari') ? 'selected' : ''; ?>>Nilphamari</option>
                                            <option value="Noakhali" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Noakhali') ? 'selected' : ''; ?>>Noakhali</option>
                                            <option value="Pabna" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Pabna') ? 'selected' : ''; ?>>Pabna</option>
                                            <option value="Panchagarh" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Panchagarh') ? 'selected' : ''; ?>>Panchagarh</option>
                                            <option value="Patuakhali" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Patuakhali') ? 'selected' : ''; ?>>Patuakhali</option>
                                            <option value="Pirojpur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Pirojpur') ? 'selected' : ''; ?>>Pirojpur</option>
                                            <option value="Rajbari" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Rajbari') ? 'selected' : ''; ?>>Rajbari</option>
                                            <option value="Rajshahi" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Rajshahi') ? 'selected' : ''; ?>>Rajshahi</option>
                                            <option value="Rangamati" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Rangamati') ? 'selected' : ''; ?>>Rangamati</option>
                                            <option value="Rangpur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Rangpur') ? 'selected' : ''; ?>>Rangpur</option>
                                            <option value="Satkhira" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Satkhira') ? 'selected' : ''; ?>>Satkhira</option>
                                            <option value="Shariatpur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Shariatpur') ? 'selected' : ''; ?>>Shariatpur</option>
                                            <option value="Sherpur" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Sherpur') ? 'selected' : ''; ?>>Sherpur</option>
                                            <option value="Sirajganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Sirajganj') ? 'selected' : ''; ?>>Sirajganj</option>
                                            <option value="Sunamganj" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Sunamganj') ? 'selected' : ''; ?>>Sunamganj</option>
                                            <option value="Sylhet" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Sylhet') ? 'selected' : ''; ?>>Sylhet</option>
                                            <option value="Tangail" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Tangail') ? 'selected' : ''; ?>>Tangail</option>
                                            <option value="Thakurgaon" <?php echo (isset($doctor['district']) && $doctor['district'] == 'Thakurgaon') ? 'selected' : ''; ?>>Thakurgaon</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Years of Experience</label>
                                        <input type="number" class="form-control" name="experience_years" value="<?php echo htmlspecialchars($doctor['experience_years']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-wrap">
                                        <label class="form-label">Biography / Experience Detail</label>
                                        <textarea class="form-control" rows="4" name="bio" placeholder="Write about your experience and biography"><?php echo htmlspecialchars($doctor['bio']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Uploads -->
                        <div class="setting-title">
                            <h5>Document Uploads</h5>
                        </div>
                        <div class="setting-card">
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">BMDC Certificate</label>
                                        <div class="change-avatar img-upload">
                                            <div class="profile-img">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </div>
                                            <div class="upload-img">
                                                <h6>BMDC Certificate</h6>
                                                <div class="imgs-load d-flex align-items-center">
                                                    <div class="change-photo">
                                                        Upload
                                                        <input type="file" class="upload" name="bmdc_certificate" accept=".pdf,.jpg,.png,.jpeg">
                                                    </div>
                                                </div>
                                                <p class="form-text">PDF, JPG, PNG up to 5MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">NID Card</label>
                                        <div class="change-avatar img-upload">
                                            <div class="profile-img">
                                                <i class="fa-solid fa-id-card"></i>
                                            </div>
                                            <div class="upload-img">
                                                <h6>NID Card</h6>
                                                <div class="imgs-load d-flex align-items-center">
                                                    <div class="change-photo">
                                                        Upload
                                                        <input type="file" class="upload" name="nid_card" accept=".pdf,.jpg,.png,.jpeg">
                                                    </div>
                                                </div>
                                                <p class="form-text">PDF, JPG, PNG up to 5MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Degrees Certificate</label>
                                        <div class="change-avatar img-upload">
                                            <div class="profile-img">
                                                <i class="fa-solid fa-graduation-cap"></i>
                                            </div>
                                            <div class="upload-img">
                                                <h6>Degrees Certificate</h6>
                                                <div class="imgs-load d-flex align-items-center">
                                                    <div class="change-photo">
                                                        Upload
                                                        <input type="file" class="upload" name="degrees_certificate" accept=".pdf,.jpg,.png,.jpeg">
                                                    </div>
                                                </div>
                                                <p class="form-text">PDF, JPG, PNG up to 5MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- /Profile Settings -->
                                        <div class="business-hour-table">
                                            <div class="business-hour-row">
                                                <div class="business-hour-label">
                                                    <h6>Monday</h6>
                                                </div>
                                                <div class="business-hour-input">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="monday_start" value="09:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            <span class="business-hour-to">to</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="monday_end" value="17:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="monday_available" value="1" checked>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="business-hour-row">
                                                <div class="business-hour-label">
                                                    <h6>Tuesday</h6>
                                                </div>
                                                <div class="business-hour-input">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="tuesday_start" value="09:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            <span class="business-hour-to">to</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="tuesday_end" value="17:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="tuesday_available" value="1" checked>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="business-hour-row">
                                                <div class="business-hour-label">
                                                    <h6>Wednesday</h6>
                                                </div>
                                                <div class="business-hour-input">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="wednesday_start" value="09:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            <span class="business-hour-to">to</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="wednesday_end" value="17:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="wednesday_available" value="1" checked>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="business-hour-row">
                                                <div class="business-hour-label">
                                                    <h6>Thursday</h6>
                                                </div>
                                                <div class="business-hour-input">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="thursday_start" value="09:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            <span class="business-hour-to">to</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="thursday_end" value="17:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="thursday_available" value="1" checked>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="business-hour-row">
                                                <div class="business-hour-label">
                                                    <h6>Friday</h6>
                                                </div>
                                                <div class="business-hour-input">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="friday_start" value="09:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            <span class="business-hour-to">to</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="friday_end" value="17:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="friday_available" value="1" checked>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="business-hour-row">
                                                <div class="business-hour-label">
                                                    <h6>Saturday</h6>
                                                </div>
                                                <div class="business-hour-input">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="saturday_start" value="10:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            <span class="business-hour-to">to</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="saturday_end" value="16:00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="saturday_available" value="1" checked>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="business-hour-row">
                                                <div class="business-hour-label">
                                                    <h6>Sunday</h6>
                                                </div>
                                                <div class="business-hour-input">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="sunday_start" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            <span class="business-hour-to">to</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="form-wrap">
                                                                <input type="time" class="form-control" name="sunday_end" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="sunday_available" value="1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        </div>

                    </div>
                    <!-- /Profile Settings -->

                </div>
            </div>
        </div>
    </div>
    <!-- /Page Content -->

                        <!-- Submit Button -->
                        <div class="modal-btn text-end mb-4" style="padding-right: 5rem;">
                    <button type="submit" class="btn btn-primary prime-btn" id="saveBtn">
                                <span class="btn-text">Save Changes</span>
                                <div class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>

                        </div>
                    </form>
                        </div>

                    </div>

<?php include 'footer.php'; ?>
		<script>
		$(document).ready(function() {
			// Handle all profile settings form submissions
			$('form[action="php/save-profile-settings.php"]').on('submit', function(e) {
				e.preventDefault();

				var form = $(this);
				var submitBtn = form.find('button[type="submit"]');
				var originalText = submitBtn.html();

				// Disable button and show loading
				submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Saving...');

				// Prepare form data
				var formData = new FormData(this);

				// Submit via AJAX
				$.ajax({
					url: form.attr('action'),
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							// Show success message
							showAlert('success', response.message || 'Profile settings updated successfully!');

							// Update profile images and name dynamically
							if (response.profile_image && response.profile_image.trim() !== '') {
								// Update sidebar profile image
								$('.booking-doc-img img').attr('src', response.profile_image);

								// Update navigation profile image
								$('.user-img img').attr('src', response.profile_image);

								// Update dropdown avatar image
								$('.avatar-img').attr('src', response.profile_image);

								// Clear the file input
								form.find('input[name="profile_image"]').val('');
							}

							// Update doctor name if it was changed
							var newName = form.find('input[name="name"]').val();
							if (newName && newName.trim() !== '') {
								// Update sidebar doctor name
								$('.profile-det-info h3 a').text(newName);

								// Update navigation dropdown name
								$('.user-text h6').text(newName);
							}

							// Reset form button
							submitBtn.prop('disabled', false).html(originalText);
						} else {
							showAlert('danger', response.message || 'Failed to save profile settings.');
							submitBtn.prop('disabled', false).html(originalText);
						}
					},
					error: function(xhr, status, error) {
						console.error('AJAX Error:', xhr.responseText);
						var errorMsg = 'An error occurred while saving. Please try again.';
						try {
							var response = JSON.parse(xhr.responseText);
							errorMsg = response.message || errorMsg;
						} catch(e) {}

						showAlert('danger', errorMsg);
						submitBtn.prop('disabled', false).html(originalText);
					}
				});
			});

			// Function to show alerts
			function showAlert(type, message) {
				// Remove any existing alerts
				$('.alert').remove();

				// Create new alert
				var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
				var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
					'<strong>' + (type === 'success' ? 'Success!' : 'Error!') + '</strong> ' + message +
					'<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
					'</div>';

				// Add alert to page
				$('body').prepend(alertHtml);

				// Auto-hide success alerts after 5 seconds
				if (type === 'success') {
					setTimeout(function() {
						$('.alert-success').fadeOut();
					}, 5000);
				}

				// Scroll to top to show alert
				$('html, body').animate({ scrollTop: 0 }, 500);
			}
		});
		</script>

	</body>
</html>