    <div class="col-lg-4 col-xl-3 theiaStickySidebar">
        <!-- Profile Sidebar -->
        <div class="profile-sidebar patient-sidebar profile-sidebar-new">
            <div class="widget-profile pro-widget-content">
                <div class="profile-info-widget">
                    <a href="patient-profile-settings.php" class="booking-doc-img">
                        <img src="<?php echo htmlspecialchars($patient['profile_image'] ?? 'assets/img/doctors-dashboard/profile-06.jpg'); ?>" alt="User Image">
                    </a>
                    <div class="profile-det-info">
                        <h3><a href="<?php echo (defined('APP_BASE') && APP_BASE) ? APP_BASE . '/patient-profile-settings.php' : 'patient-profile-settings.php'; ?>"><?php echo htmlspecialchars($patient['name']); ?></a></h3>
                        <div class="patient-details">
                            <h5 class="mb-0">Patient ID : PT<?php echo str_pad($patient['id'], 6, '0', STR_PAD_LEFT); ?></h5>
                        </div>
<?php
// Display patient info if available
if (!empty($patient['gender']) || !empty($patient['date_of_birth'])) {
    $info_parts = [];
    if (!empty($patient['gender'])) {
        $info_parts[] = $patient['gender'];
    }
    if (!empty($patient['date_of_birth'])) {
        $birth_date = new DateTime($patient['date_of_birth']);
        $today = new DateTime();
        $age = $today->diff($birth_date);
        $info_parts[] = $age->y . ' years ' . $age->m . ' Months';
    }
    if (!empty($info_parts)) {
        echo '<span>' . htmlspecialchars(implode(' <i class="fa-solid fa-circle"></i> ', $info_parts)) . '</span>';
    }
}
?>
                    </div>
                </div>
            </div>
            <div class="dashboard-widget">
                <nav class="dashboard-menu">
                    <ul>
                        <li <?php echo (basename($_SERVER['PHP_SELF']) == 'patient-dashboard.php') ? 'class="active"' : ''; ?>>
                            <a href="<?php echo (defined('APP_BASE') && APP_BASE) ? APP_BASE . '/patient-dashboard.php' : 'patient-dashboard.php'; ?>">
                                <i class="isax isax-category-2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li <?php echo (basename($_SERVER['PHP_SELF']) == 'patient-appointments.php') ? 'class="active"' : ''; ?>>
                            <a href="<?php echo (defined('APP_BASE') && APP_BASE) ? APP_BASE . '/patient-appointments.php' : 'patient-appointments.php'; ?>">
                                <i class="isax isax-calendar-1"></i>
                                <span>My Appointments</span>
                            </a>
                        </li>
                        <li <?php echo (basename($_SERVER['PHP_SELF']) == 'patient-profile-settings.php') ? 'class="active"' : ''; ?>>
                            <a href="<?php echo (defined('APP_BASE') && APP_BASE) ? APP_BASE . '/patient-profile-settings.php' : 'patient-profile-settings.php'; ?>">
                                <i class="isax isax-setting-2"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="php/logout.php">
                                <i class="isax isax-logout"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
