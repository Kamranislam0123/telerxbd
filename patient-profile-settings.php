<?php
/**
 * Patient Profile Settings - TeleRx Bangladesh
 * Patient profile settings page
 */

// Include configuration
$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.php');
    exit;
}
require_once $config_path;

// Check if patient is logged in
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit;
}

$patient_id = $_SESSION['patient_id'];

try {
    $conn = getDBConnection();

    // Fetch patient's basic information
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: login.php');
        exit;
    }

    $patient = $result->fetch_assoc();

    // Set default values if profile data is missing
    $patient['profile_image'] = $patient['profile_image'] ?? 'assets/img/doctors-dashboard/profile-06.jpg';
    $patient['phone'] = $patient['phone'] ?? '';
    $patient['gender'] = $patient['gender'] ?? '';
    $patient['date_of_birth'] = $patient['date_of_birth'] ?? '';
    $patient['blood_group'] = $patient['blood_group'] ?? '';
    $patient['address'] = $patient['address'] ?? '';
    $patient['city'] = $patient['city'] ?? '';
    $patient['state'] = $patient['state'] ?? '';
    $patient['country'] = $patient['country'] ?? '';
    $patient['pincode'] = $patient['pincode'] ?? '';

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Patient profile settings error: " . $e->getMessage());
    header('Location: login.php');
    exit;
}

// Split name for form fields
$name_parts = explode(' ', $patient['name']);
$first_name = $name_parts[0] ?? '';
$last_name = isset($name_parts[1]) ? implode(' ', array_slice($name_parts, 1)) : '';

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
                            <h3><a href="patient-dashboard.php"><?php echo htmlspecialchars($patient['name']); ?></a></h3>
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
                include 'patient-leftside-menu.php';
                ?>
                <div class="col-lg-8 col-xl-9">
                    
                    <!-- Settings List -->
                    <div class="setting-tab">
                        <div class="appointment-tabs">
                            <ul class="nav nav-tabs" id="patientSettingsTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Profile</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Change Password</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- /Settings List -->

                    <!-- Tab Content -->
                    <div class="tab-content mt-4" id="patientSettingsTabContent">

                        <!-- Profile Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <form action="php/save-patient-profile-settings.php" method="POST" enctype="multipart/form-data" id="profileForm">
                                <input type="hidden" name="section" value="all">

                                <!-- Profile Image Upload -->
                                <div class="setting-card">
                            <div class="change-avatar img-upload">
                                <div class="profile-img">
                                    <img src="<?php echo htmlspecialchars($patient['profile_image']); ?>" alt="Profile Image" id="profile_image_preview" style="width: 100%; height: 100%; object-fit: cover;">
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
                        <div class="setting-title">
                            <h6>Information</h6>
                        </div>
                        <div class="setting-card">
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Date of Birth</label>
                                        <div class="form-icon">
                                            <input type="date" class="form-control" name="date_of_birth" value="<?php echo htmlspecialchars($patient['date_of_birth']); ?>">
                                            <span class="icon"><i class="isax isax-calendar-1"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($patient['email']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Gender</label>
                                        <select class="form-control" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?php echo ($patient['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo ($patient['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                            <option value="Other" <?php echo ($patient['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Blood Group</label>
                                        <select class="form-control" name="blood_group">
                                            <option value="">Select</option>
                                            <option value="A+ve" <?php echo ($patient['blood_group'] == 'A+ve') ? 'selected' : ''; ?>>A+ve</option>
                                            <option value="A-ve" <?php echo ($patient['blood_group'] == 'A-ve') ? 'selected' : ''; ?>>A-ve</option>
                                            <option value="B+ve" <?php echo ($patient['blood_group'] == 'B+ve') ? 'selected' : ''; ?>>B+ve</option>
                                            <option value="B-ve" <?php echo ($patient['blood_group'] == 'B-ve') ? 'selected' : ''; ?>>B-ve</option>
                                            <option value="AB+ve" <?php echo ($patient['blood_group'] == 'AB+ve') ? 'selected' : ''; ?>>AB+ve</option>
                                            <option value="AB-ve" <?php echo ($patient['blood_group'] == 'AB-ve') ? 'selected' : ''; ?>>AB-ve</option>
                                            <option value="O+ve" <?php echo ($patient['blood_group'] == 'O+ve') ? 'selected' : ''; ?>>O+ve</option>
                                            <option value="O-ve" <?php echo ($patient['blood_group'] == 'O-ve') ? 'selected' : ''; ?>>O-ve</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="setting-title">
                            <h6>Address</h6>
                        </div>
                        <div class="setting-card">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-wrap">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($patient['address']); ?>" placeholder="Enter your address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($patient['city']); ?>" placeholder="Enter your city">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control" name="state" value="<?php echo htmlspecialchars($patient['state']); ?>" placeholder="Enter your state">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Country</label>
                                        <input type="text" class="form-control" name="country" value="<?php echo htmlspecialchars($patient['country'] ?: 'Bangladesh'); ?>" placeholder="Enter your country">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label">Pincode</label>
                                        <input type="text" class="form-control" name="pincode" value="<?php echo htmlspecialchars($patient['pincode']); ?>" placeholder="Enter your pincode">
                                    </div>
                                </div>
                            </div>
                        </div>

                                <!-- Submit Button -->
                                <div class="modal-btn text-end mb-4">
                                    <button type="submit" class="btn btn-primary prime-btn" id="saveBtn">
                                        <span class="btn-text">Save Changes</span>
                                        <div class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!-- /Profile Tab -->

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="border-bottom pb-3 mb-3">
                                        <h5>Change Password</h5>
                                    </div>
                                    <form action="php/change-patient-password.php" method="POST" id="changePasswordForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                                    <div class="pass-group">
                                                        <input type="password" class="form-control pass-input-sub" name="current_password" id="current-password" required>
                                                        <span class="feather-eye-off toggle-password-sub"></span>
                                                    </div>									
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">New Password <span class="text-danger">*</span></label>
                                                    <div class="pass-group">
                                                        <input type="password" class="form-control pass-input" name="new_password" id="new-password" required>
                                                        <span class="feather-eye-off toggle-password"></span>
                                                    </div>									
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                    <div class="pass-group">
                                                        <input type="password" class="form-control pass-input-sub" name="confirm_password" id="confirm-password" required>
                                                        <span class="feather-eye-off toggle-password-sub"></span>
                                                    </div>									
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-btn border-top pt-3 text-end">
                                            <button type="button" class="btn btn-md btn-light rounded-pill" onclick="location.reload()">Cancel</button>
                                            <button type="submit" class="btn btn-md btn-primary-gradient rounded-pill" id="changePasswordBtn">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /Change Password Tab -->

                    </div>
                    <!-- /Tab Content -->

                </div>
            </div>
        </div>
    </div>
    <!-- /Page Content -->

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
        $('#profile_image_preview').attr('src', '<?php echo htmlspecialchars($patient['profile_image']); ?>');
    });

    // Handle patient profile settings form submissions
    $('#profileForm').on('submit', function(e) {
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

                    // Update profile images dynamically
                    if (response.profile_image && response.profile_image.trim() !== '') {
                        // Update sidebar profile image
                        $('.booking-doc-img img').attr('src', response.profile_image);
                        $('#profile_image_preview').attr('src', response.profile_image);
                        // Clear the file input
                        form.find('input[name="profile_image"]').val('');
                    }

                    // Reset form button
                    submitBtn.prop('disabled', false).html(originalText);
                } else {
                    showAlert('danger', response.message || 'Failed to save profile settings.');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
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

    // Handle change password form
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitBtn = $('#changePasswordBtn');
        var originalText = submitBtn.html();
        var newPassword = $('#new-password').val();
        var confirmPassword = $('#confirm-password').val();

        // Validate passwords match
        if (newPassword !== confirmPassword) {
            showAlert('danger', 'New password and confirm password do not match.');
            return;
        }

        // Validate password length
        if (newPassword.length < 6) {
            showAlert('danger', 'Password must be at least 6 characters long.');
            return;
        }

        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Saving...');

        // Submit via AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Password changed successfully!');
                    form[0].reset();
                    submitBtn.prop('disabled', false).html(originalText);
                } else {
                    showAlert('danger', response.message || 'Failed to change password.');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = 'An error occurred while changing password. Please try again.';
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
