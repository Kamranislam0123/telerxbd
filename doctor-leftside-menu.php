<!DOCTYPE html>
<html lang="en">
<body>
    <div class="col-lg-4 col-xl-3 theiaStickySidebar">
        <!-- Profile Sidebar -->
        <div class="profile-sidebar doctor-sidebar profile-sidebar-new">
            <div class="widget-profile pro-widget-content">
                <div class="profile-info-widget">
                    <a href="doctor-profile?doctor_id=<?php echo $doctor_id; ?>" class="booking-doc-img">
                        <img src="<?php echo htmlspecialchars($doctor['profile_image']); ?>" alt="User Image">
                    </a>
                <div class="profile-det-info">
                    <h3><a href="doctor-profile?doctor_id=<?php echo $doctor_id; ?>"><?php echo htmlspecialchars($doctor['name']); ?></a></h3>
            <div class="patient-details">
                <h5 class="mb-0"><?php echo htmlspecialchars($doctor['degrees'] ?: 'Doctor'); ?></h5>
    </div>
<?php
// Display multiple specialities as badges
$speciality_display = !empty($doctor['speciality']) ? $doctor['speciality'] : (!empty($doctor['specialty']) ? $doctor['specialty'] : '');
if (!empty($speciality_display)) {
    $specialities_array = array_map('trim', explode(',', $speciality_display));
    foreach ($specialities_array as $spec) {
        if (!empty($spec)) {
            echo '<span class="badge doctor-role-badge me-1 mb-1"><i class="fa-solid fa-circle"></i>' . htmlspecialchars($spec) . '</span>';
        }
    }
} else {
    echo '<span class="badge doctor-role-badge"><i class="fa-solid fa-circle"></i>Doctor</span>';
}
?>
</div>
</div>
</div>
<div class="doctor-available-head">
    <div class="input-block input-block-new">
        <label class="form-label">Availability</label>
        <select class="select form-control">
            <option>I am Available Now</option>
            <option>Not Available</option>
        </select>
    </div>
</div>
<div class="dashboard-widget">
    <nav class="dashboard-menu">
        <ul>
            <li>
                <a href="doctor-dashboard">
                    <i class="isax isax-category-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- <li>
                <a href="doctor-request.html">
                    <i class="isax isax-clipboard-tick"></i>
                    <span>Requests</span>
                    <small class="unread-msg">2</small>
                </a>
            </li> -->
            <li>
                <a href="appointments">
                    <i class="isax isax-calendar-1"></i>
                    <span>Appointments</span>
                </a>
            </li>
            <!-- <li>
                <a href="available-timings.html">
                    <i class="isax isax-calendar-tick"></i>
                    <span>Available Timings</span>
                </a>
            </li> -->
            <!-- <li>
                <a href="my-patients.html">
                    <i class="fa-solid fa-user-injured"></i>
                    <span>My Patients</span>
                </a>
            </li> -->
            <!-- <li>
                <a href="doctor-specialities.html">
                    <i class="isax isax-clock"></i>
                    <span>Specialties & Services</span>
                </a>
            </li> -->
            <!-- <li>
                <a href="reviews.html">
                    <i class="isax isax-star-1"></i>
                    <span>Reviews</span>
                </a>
            </li> -->
            <!-- <li>
                <a href="accounts.html">
                    <i class="isax isax-profile-tick"></i>
                    <span>Accounts</span>
                </a>
            </li> -->
            <!-- <li>
                <a href="invoices.html">
                    <i class="isax isax-document-text"></i>
                    <span>Invoices</span>
                </a>
            </li> -->
            <!-- <li>
                <a href="doctor-payment.html">
                    <i class="fa-solid fa-money-bill-1"></i>
                    <span>Payout Settings</span>
                </a>
            </li> -->
            <!-- <li>
                <a href="chat-doctor.html">
                    <i class="isax isax-messages-1"></i>
                    <span>Message</span>
                    <small class="unread-msg">7</small>
                </a>
            </li> -->
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'doctor-profile-settings.php') ? 'active' : ''; ?>">
                <a href="<?php echo (defined('APP_BASE') && APP_BASE) ? APP_BASE . '/doctor-profile-settings' : 'doctor-profile-settings'; ?>">
                    <i class="isax isax-setting-2"></i>
                    <span>Profile Settings</span>
                </a>
            </li>
            <!-- <li>
                <a href="social-media.html">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Social Media</span>
                </a>
            </li> -->
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'change-password.php') ? 'active' : ''; ?>">
                <a href="<?php echo (defined('APP_BASE') && APP_BASE) ? APP_BASE . '/change-password' : 'change-password'; ?>">
                    <i class="isax isax-key"></i>
                    <span>Change Password</span>
                </a>
            </li>
            <li>
                <a href="php/logout.php">  <!-- এই লাইনটি সঠিক? -->
                    <i class="isax isax-logout"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
</div>
</body>