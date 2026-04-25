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
    $doctor['profile_image'] = !empty($doctor['profile_image']) ? $doctor['profile_image'] : 'assets/img/doctors-dashboard/doctor-profile-img.jpg';

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

    // Helper: get date for a day of week (today or next occurrence)
    function getDayDate($dayName) {
        $dayName = strtolower($dayName);
        $daysMap = [
            'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
            'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 0
        ];
        if (!isset($daysMap[$dayName])) return '';
        $targetDay = $daysMap[$dayName];
        $today = new DateTime();
        $currentDay = (int)$today->format('w');
        $daysToAdd = ($targetDay - $currentDay + 7) % 7;
        $date = clone $today;
        if ($daysToAdd > 0) $date->modify("+$daysToAdd days");
        return $date->format('M d, Y');
    }

    // Same as getDayDate but returns Y-m-d for API (doctor_schedule slot_date)
    function getDayDateYmd($dayName) {
        $dayName = strtolower($dayName);
        $daysMap = [
            'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
            'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 0
        ];
        if (!isset($daysMap[$dayName])) return '';
        $targetDay = $daysMap[$dayName];
        $today = new DateTime();
        $currentDay = (int)$today->format('w');
        $daysToAdd = ($targetDay - $currentDay + 7) % 7;
        $date = clone $today;
        if ($daysToAdd > 0) $date->modify("+$daysToAdd days");
        return $date->format('Y-m-d');
    }

    // Days ordered starting from today (current date first)
    $daysList = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    $todayDayName = strtolower(date('l'));
    $todayIdx = array_search($todayDayName, $daysList);
    $daysOrder = [];
    for ($i = 0; $i < 7; $i++) {
        $daysOrder[] = $daysList[($todayIdx + $i) % 7];
    }

    $dayLabels = [];
    foreach ($daysList as $day) {
        $dayLabels[$day] = [
            'display' => ucfirst($day) . ($day === $todayDayName ? ' (Today)' : ''),
            'date' => getDayDate($day),
            'is_today' => ($day === $todayDayName)
        ];
    }

    // day_of_week for availability API: 0=Sunday, 1=Monday, ... 6=Saturday
    $dayOfWeekMap = ['sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6];

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
                                    <img src="<?php echo htmlspecialchars($doctor['profile_image']); ?>" alt="Profile Image" id="profile_image_preview" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="upload-img">
                                    <h5>Profile Image</h5>
                                    <div class="imgs-load d-flex align-items-center">
                                        <div class="change-photo">
                                            Upload New
                                            <input type="file" class="upload" name="profile_image" accept="image/*" id="profile_image_input">
                                        </div>
                                        <a href="#" class="upload-remove" id="remove_profile_image">Remove</a>
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

								<!-- General Availability -->
								<div class="tab-pane fade show active" id="general-availability">
									<div class="card custom-card">
										<div class="card-body">
											<div class="card-header">
												<h3>Select Available Slots</h3>
											</div>

											<div class="available-tab">
												<label class="form-label">Select Available days (starting from today)</label>
												<ul class="nav">
													<?php foreach ($daysOrder as $idx => $day): $lab = $dayLabels[$day]; $active = ($idx === 0); $dayYmd = getDayDateYmd($day); $dow = $dayOfWeekMap[$day] ?? 0; ?>
													<li>
														<a href="#" class="<?php echo $active ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#<?php echo $day; ?>" data-day="<?php echo $day; ?>" data-date="<?php echo htmlspecialchars($dayYmd); ?>" data-day-of-week="<?php echo $dow; ?>"><strong><?php echo htmlspecialchars($lab['display']); ?></strong><br><small style="font-size: 11px; color: #999;"><?php echo htmlspecialchars($lab['date']); ?></small></a>
													</li>
													<?php endforeach; ?>
												</ul>
											</div>
											<div class="tab-content pt-0">
												<?php foreach ($daysOrder as $idx => $day): $lab = $dayLabels[$day]; $active = ($idx === 0); $dayYmd = getDayDateYmd($day); $dow = $dayOfWeekMap[$day] ?? 0; ?>
												<div class="tab-pane fade <?php echo $active ? 'active show' : ''; ?>" id="<?php echo $day; ?>" data-day="<?php echo $day; ?>" data-date="<?php echo htmlspecialchars($dayYmd); ?>" data-day-of-week="<?php echo $dow; ?>">
													<div class="slot-box" data-day="<?php echo $day; ?>" data-date="<?php echo htmlspecialchars($dayYmd); ?>" data-day-of-week="<?php echo $dow; ?>">
														<div class="slot-header">
															<h5><?php echo htmlspecialchars($lab['display'] . ' - ' . $lab['date']); ?></h5>
															<ul>
																<li>
																	<a href="#" class="add-slot" data-bs-toggle="modal" data-bs-target="#add_slot" data-day="<?php echo $day; ?>" data-date="<?php echo htmlspecialchars($dayYmd); ?>" data-day-of-week="<?php echo $dow; ?>">Add Slots</a>
																</li>
																<li>
																	<a href="#" class="del-slot" data-bs-toggle="modal" data-bs-target="#delete_slot" data-day="<?php echo $day; ?>" data-date="<?php echo htmlspecialchars($dayYmd); ?>" data-day-of-week="<?php echo $dow; ?>">Delete All</a>
																</li>
															</ul>
														</div>
														<div class="slot-body" data-day="<?php echo $day; ?>" data-date="<?php echo htmlspecialchars($dayYmd); ?>" data-day-of-week="<?php echo $dow; ?>">
															<p>No Slots Available</p>
														</div>
													</div>
												</div>
												<?php endforeach; ?>

												
											</div>

										</div>
									</div>
								</div>
								<!-- /General Availability -->

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

                                            		<!-- Add Slots -->
		<div class="modal fade custom-modals" id="add_slot">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add New Slot</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-xmark"></i>
						</button>
					</div>
					<form id="add_slot_form" method="post" action="">
						<input type="hidden" id="slot_day" name="slot_day" value="">
						<input type="hidden" id="slot_date" name="slot_date" value="">
						<input type="hidden" id="slot_day_of_week" name="slot_day_of_week" value="">
						<div class="modal-body">
							<div class="timing-modal">
								<p class="text-muted small mb-3">Appointment duration: <strong>15 minutes</strong>. Select a slot tab, then choose which timings are available.</p>
								<div class="row">
									<div class="col-md-12 mb-3">
										<div class="form-wrap">
											<input type="hidden" id="slot_period" name="slot_period" value="">
											<div class="available-tab">
												<label class="form-label">Select Slot <span class="text-danger">*</span></label>
												<ul class="nav slot-period-tabs d-flex flex-wrap" id="slot_period_tabs" role="tablist">
													<li>
														<a href="#slot-pane-morning" class="active" data-bs-toggle="tab" data-bs-target="#slot-pane-morning" data-period="morning" role="tab">Morning (6 AM – 12 PM)</a>
													</li>
													<li>
														<a href="#slot-pane-afternoon" data-bs-toggle="tab" data-bs-target="#slot-pane-afternoon" data-period="afternoon" role="tab">Afternoon (12 PM – 6 PM)</a>
													</li>
													<li>
														<a href="#slot-pane-evening" data-bs-toggle="tab" data-bs-target="#slot-pane-evening" data-period="evening" role="tab">Evening (6 PM – 12 AM)</a>
													</li>
													<li>
														<a href="#slot-pane-night" data-bs-toggle="tab" data-bs-target="#slot-pane-night" data-period="night" role="tab">Night (12 AM – 6 AM)</a>
													</li>
												</ul>
											</div>
											<div class="tab-content pt-3" id="slot_period_content">
												<div class="tab-pane fade show active" id="slot-pane-morning" data-period="morning" role="tabpanel">
													<div class="form-wrap">
														<label class="col-form-label d-block">Select available timings (24 slots, 15 min each)</label>
														<div id="slot_timings_morning" class="token-slot mt-2 mb-2 slot-timings-pane"></div>
														<div class="mt-2">
															<button type="button" class="btn btn-outline-secondary btn-sm slot-select-all" data-pane="slot_timings_morning">Select All</button>
															<button type="button" class="btn btn-outline-secondary btn-sm ms-1 slot-clear-all" data-pane="slot_timings_morning">Clear All</button>
														</div>
													</div>
												</div>
												<div class="tab-pane fade" id="slot-pane-afternoon" data-period="afternoon" role="tabpanel">
													<div class="form-wrap">
														<label class="col-form-label d-block">Select available timings (24 slots, 15 min each)</label>
														<div id="slot_timings_afternoon" class="token-slot mt-2 mb-2 slot-timings-pane"></div>
														<div class="mt-2">
															<button type="button" class="btn btn-outline-secondary btn-sm slot-select-all" data-pane="slot_timings_afternoon">Select All</button>
															<button type="button" class="btn btn-outline-secondary btn-sm ms-1 slot-clear-all" data-pane="slot_timings_afternoon">Clear All</button>
														</div>
													</div>
												</div>
												<div class="tab-pane fade" id="slot-pane-evening" data-period="evening" role="tabpanel">
													<div class="form-wrap">
														<label class="col-form-label d-block">Select available timings (24 slots, 15 min each)</label>
														<div id="slot_timings_evening" class="token-slot mt-2 mb-2 slot-timings-pane"></div>
														<div class="mt-2">
															<button type="button" class="btn btn-outline-secondary btn-sm slot-select-all" data-pane="slot_timings_evening">Select All</button>
															<button type="button" class="btn btn-outline-secondary btn-sm ms-1 slot-clear-all" data-pane="slot_timings_evening">Clear All</button>
														</div>
													</div>
												</div>
												<div class="tab-pane fade" id="slot-pane-night" data-period="night" role="tabpanel">
													<div class="form-wrap">
														<label class="col-form-label d-block">Select available timings (24 slots, 15 min each)</label>
														<div id="slot_timings_night" class="token-slot mt-2 mb-2 slot-timings-pane"></div>
														<div class="mt-2">
															<button type="button" class="btn btn-outline-secondary btn-sm slot-select-all" data-pane="slot_timings_night">Select All</button>
															<button type="button" class="btn btn-outline-secondary btn-sm ms-1 slot-clear-all" data-pane="slot_timings_night">Clear All</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<!-- <div class="col-md-12">
										<div class="form-wrap mb-0">
											<label class="col-form-label d-block">Assign Appointment Spaces</label>
											<div class="custom-control form-check custom-control-inline">
												<input type="radio" id="space1" name="rating_option" class="form-check-input" value="1" checked="">
												<label class="form-check-label" for="space1">Space 1</label>
											</div>
											<div class="custom-control form-check custom-control-inline">
												<input type="radio" id="space2" name="rating_option" class="form-check-input" value="2">
												<label class="form-check-label" for="space2">Space 2</label>
											</div>
											<div class="custom-control form-check custom-control-inline">
												<input type="radio" id="space3" name="rating_option" class="form-check-input" value="3">
												<label class="form-check-label" for="space3">Space 3</label>
											</div>
											<div class="custom-control form-check custom-control-inline">
												<input type="radio" id="space4" name="rating_option" class="form-check-input" value="4">
												<label class="form-check-label" for="space4">Space 4</label>
											</div>
										</div>
									</div> -->
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-gray" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary prime-btn" id="add_slot_submit">Save Changes</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Add Slots -->

		<!-- Remove Slots -->
		<div class="modal fade info-modal" id="delete_slot">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="success-wrap">
							<div class="success-info">
								<div class="text-center">
									<span class="icon-success bg-red"><i class="fa-solid fa-xmark"></i></span>
									<h3>Remove Slots</h3>
									<p>Are you sure you want to remove all slots for this day?</p>
								</div>
							</div>
						</div>

						<div class="modal-btn text-center">
							<a href="#" class="btn btn-gray" data-bs-dismiss="modal">Cancel</a>
							<button type="button" class="btn btn-primary prime-btn" id="delete_slot_confirm">Yes, Remove</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Remove Slots -->
                    </div>



<?php include 'footer.php'; ?>
		<script>
		$(document).ready(function() {
            // Profile image preview
            $('#profile_image_input').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profile_image_preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#remove_profile_image').on('click', function(e) {
                e.preventDefault();
                $('#profile_image_input').val('');
                $('#profile_image_preview').attr('src', '<?php echo htmlspecialchars($doctor['profile_image']); ?>');
            });

			var slotPeriodLabels = { morning: 'Morning (6:00 AM – 12:00 PM)', afternoon: 'Afternoon (12:00 PM – 6:00 PM)', evening: 'Evening (6:00 PM – 12:00 AM)', night: 'Night (12:00 AM – 6:00 AM)' };
			var deleteSlotDate = null;
			var deleteSlotDay = null;
			var deleteSlotDayOfWeek = null;

			// Slot definitions: 4 periods x 24 x 15-min timings each
			var slotTimings = { morning: [], afternoon: [], evening: [], night: [] };
			function pad(n) { return n < 10 ? '0' + n : n; }
			function genTimes(startHour, startMin, count) {
				var list = [];
				for (var i = 0; i < count; i++) {
					var m = startMin + i * 15, h = startHour + Math.floor(m / 60);
					m = m % 60; h = h % 24;
					var ampm = h >= 12 ? 'PM' : 'AM';
					var h12 = h % 12 || 12;
					list.push({ h: h, m: m, label: h12 + ':' + pad(m) + ' ' + ampm, value: pad(h) + ':' + pad(m) });
				}
				return list;
			}
			slotTimings.morning   = genTimes(6, 0, 24);
			slotTimings.afternoon = genTimes(12, 0, 24);
			slotTimings.evening   = genTimes(18, 0, 24);
			slotTimings.night     = genTimes(0, 0, 24);

			function formatTime24to12(val) {
				var p = (val || '').split(':');
				var h = parseInt(p[0], 10), m = parseInt(p[1] || 0, 10);
				var ampm = h >= 12 ? 'PM' : 'AM';
				var h12 = h % 12 || 12;
				return h12 + ':' + pad(m) + ' ' + ampm;
			}

			function slotToPeriod(time) {
				var parts = (time || '').split(':');
				var h = parseInt(parts[0], 10);
				if (h >= 6 && h < 12) return 'morning';
				if (h >= 12 && h < 18) return 'afternoon';
				if (h >= 18 && h < 24) return 'evening';
				return 'night';
			}

			function renderSlotBody($body, slots) {
				if (!slots || slots.length === 0) {
					$body.html('<p>No Slots Available</p>');
					return;
				}
				slots = slots.slice().sort();
				var byPeriod = { morning: [], afternoon: [], evening: [], night: [] };
				slots.forEach(function(t) {
					var p = slotToPeriod(t);
					if (byPeriod[p]) byPeriod[p].push(t);
				});
				var html = '';
				['morning','afternoon','evening','night'].forEach(function(period) {
					if (byPeriod[period].length === 0) return;
					html += '<div class="slot-group mb-3"><h6 class="fs-14 mb-2">' + (slotPeriodLabels[period] || period) + '</h6><ul class="time-slots">';
					byPeriod[period].forEach(function(t) {
						html += '<li><i class="isax isax-clock"></i>' + formatTime24to12(t) + '</li>';
					});
					html += '</ul></div>';
				});
				$body.html(html);
			}

			function loadSlotsForDay(dayOfWeek, day) {
				var $body = $('.slot-body[data-day-of-week="' + dayOfWeek + '"]');
				if (!$body.length) $body = $('.slot-body[data-day="' + day + '"]');
				$body.html('<p class="text-muted">Loading...</p>');
				$.get('php/get-doctor-availability-ranges.php', { day_of_week: dayOfWeek }, function(r) {
					if (r && r.success && r.slots && r.slots.length) {
						renderSlotBody($body, r.slots);
					} else {
						$body.html('<p>No Slots Available</p>');
					}
				}, 'json').fail(function() {
					$body.html('<p>Failed to load slots.</p>');
				});
			}

			// Load slots when a day tab is shown
			$('.available-tab .nav a[data-date]').on('shown.bs.tab', function() {
				var dayOfWeek = $(this).data('day-of-week');
				var day = $(this).data('day');
				if (dayOfWeek !== undefined) loadSlotsForDay(dayOfWeek, day);
			});
			// Load slots for the initially active day
			var $activeTab = $('.tab-content.pt-0 .tab-pane.active.show');
			if ($activeTab.length) {
				loadSlotsForDay($activeTab.data('day-of-week'), $activeTab.data('day'));
			}

			$('.add-slot').on('click', function() {
				var day = $(this).data('day');
				var date = $(this).data('date');
				var dayOfWeek = $(this).data('day-of-week');
				if (day) $('#slot_day').val(day);
				if (date) $('#slot_date').val(date);
				if (dayOfWeek !== undefined) $('#slot_day_of_week').val(dayOfWeek);
			});

			$('.del-slot').on('click', function() {
				deleteSlotDate = $(this).data('date') || null;
				deleteSlotDay = $(this).data('day') || null;
				deleteSlotDayOfWeek = $(this).data('day-of-week');
			});

			$('#delete_slot_confirm').on('click', function() {
				if (deleteSlotDayOfWeek === undefined || deleteSlotDayOfWeek === null) {
					$('#delete_slot').modal('hide');
					return;
				}
				$.post('php/save-doctor-availability-ranges.php', { day_of_week: deleteSlotDayOfWeek, slot_times: [] }, function(r) {
					if (r && r.success) {
						showAlert('success', 'Slots removed.');
						loadSlotsForDay(deleteSlotDayOfWeek, deleteSlotDay);
					} else {
						showAlert('danger', (r && r.message) ? r.message : 'Failed to remove slots.');
					}
				}, 'json').fail(function() {
					showAlert('danger', 'Failed to remove slots.');
				});
				deleteSlotDate = null;
				deleteSlotDay = null;
				deleteSlotDayOfWeek = null;
				$('#delete_slot').modal('hide');
			});

			function fillSlotPane(period, paneId) {
				var list = $('#' + paneId);
				list.empty();
				if (!slotTimings[period] || !slotTimings[period].length) return;
				slotTimings[period].forEach(function(t) {
					var id = 'st_' + period + '_' + t.value.replace(':', '_');
					list.append(
						'<div class="form-check-inline visits me-0">' +
						'<label class="visit-btns">' +
						'<input type="checkbox" class="form-check-input slot-time-cb" name="slot_times[]" value="' + t.value + '" id="' + id + '">' +
						'<span class="visit-rsn">' + t.label + '</span>' +
						'</label></div>'
					);
				});
			}

			$('#slot_period_tabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
				var period = $(this).data('period');
				$('#slot_period').val(period || '');
				$('#slot_period_tabs a').removeClass('active');
				$(this).addClass('active');
			});

			$(document).on('click', '.slot-select-all', function() {
				var paneId = $(this).data('pane');
				$('#' + paneId).find('.slot-time-cb').prop('checked', true);
			});
			$(document).on('click', '.slot-clear-all', function() {
				var paneId = $(this).data('pane');
				$('#' + paneId).find('.slot-time-cb').prop('checked', false);
			});

			// Normalize time to HH:MM so it matches checkbox values (06:00 not 6:00)
			function normalizeSlotTime(t) {
				if (!t || typeof t !== 'string') return '';
				var parts = t.trim().split(':');
				var h = parseInt(parts[0], 10);
				var m = parseInt(parts[1] || 0, 10);
				return pad(isNaN(h) ? 0 : h) + ':' + pad(isNaN(m) ? 0 : m);
			}

			$('#add_slot').on('show.bs.modal', function() {
				$('#slot_period').val('morning');
				var dayOfWeek = $('#slot_day_of_week').val();
				var $content = $('#slot_period_content').closest('.form-wrap');
				var $loader = $content.find('.slot-modal-loader');
				if (!$loader.length) {
					$content.find('.available-tab').after('<p class="slot-modal-loader text-muted small mb-0 mt-2">Loading existing slots…</p>');
					$loader = $content.find('.slot-modal-loader');
				}
				$('#slot_period_content').hide();
				$loader.show();
				function showFormAndPreCheck(existingSlots) {
					fillSlotPane('morning', 'slot_timings_morning');
					fillSlotPane('afternoon', 'slot_timings_afternoon');
					fillSlotPane('evening', 'slot_timings_evening');
					fillSlotPane('night', 'slot_timings_night');
					$('#slot_period_tabs a').removeClass('active').first().addClass('active');
					$('#slot_period_content .tab-pane').removeClass('show active').first().addClass('show active');
					(existingSlots || []).forEach(function(t) {
						var v = normalizeSlotTime(t);
						if (v) $('#add_slot_form').find('.slot-time-cb[value="' + v + '"]').prop('checked', true);
					});
					$loader.hide();
					$('#slot_period_content').show();
				}
				if (dayOfWeek !== '' && dayOfWeek !== undefined) {
					$.get('php/get-doctor-availability-ranges.php', { day_of_week: dayOfWeek }, function(r) {
						var slots = (r && r.success && r.slots) ? r.slots : [];
						showFormAndPreCheck(slots);
					}, 'json').fail(function() {
						showFormAndPreCheck([]);
					});
				} else {
					showFormAndPreCheck([]);
				}
			});

			$('#add_slot_form').on('submit', function(e) {
				e.preventDefault();
				var dayOfWeek = $('#slot_day_of_week').val();
				var slotDay = $('#slot_day').val();
				var checkedTimes = [];
				$('#add_slot_form .slot-time-cb:checked').each(function() {
					checkedTimes.push($(this).val());
				});
				if (dayOfWeek === '' || dayOfWeek === undefined) {
					alert('Please add slots from a day tab (e.g. Monday).');
					return false;
				}
				if (checkedTimes.length === 0) {
					alert('Please select at least one timing.');
					return false;
				}
				$.post('php/save-doctor-availability-ranges.php', { day_of_week: dayOfWeek, slot_times: checkedTimes }, function(r) {
					if (r && r.success) {
						showAlert('success', 'Slots saved successfully.');
						loadSlotsForDay(dayOfWeek, slotDay);
						$('#add_slot').modal('hide');
						$('#slot_day_of_week').val('');
						$('#slot_day').val('');
						$('#slot_date').val('');
					} else {
						alert((r && r.message) ? r.message : 'Failed to save slots.');
					}
				}, 'json').fail(function() {
					alert('Failed to save slots.');
				});
				return false;
			});

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