<?php
$current_page = isset($current_page) ? $current_page : basename($_SERVER['PHP_SELF']);
$healthcare = $healthcare ?? [];
?>
<div class="col-lg-4 col-xl-3 theiaStickySidebar">
    <!-- Profile Sidebar -->
    <div class="profile-sidebar doctor-sidebar profile-sidebar-new">
        <div class="widget-profile pro-widget-content">
            <div class="profile-info-widget">
                <a href="health-worker-profile-settings.php" class="booking-doc-img">
                    <img src="<?php echo htmlspecialchars($healthcare['profile_image'] ?? 'assets/img/doctors-dashboard/doctor-profile-img.jpg'); ?>" alt="User Image">
                </a>
                <div class="profile-det-info">
                    <h3><?php echo htmlspecialchars($healthcare['name'] ?? 'Health Worker'); ?></h3>
                    <div class="patient-details">
                        <h5 class="mb-0"><?php echo htmlspecialchars($healthcare['degrees'] ?? 'Health-Worker'); ?></h5>
                    </div>
                    <?php if (!empty($healthcare['tid'])): ?>
                    <span class="badge doctor-role-badge mt-1"><i class="fa-solid fa-circle"></i> TID: <?php echo htmlspecialchars($healthcare['tid']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="dashboard-widget">
            <nav class="dashboard-menu">
                <ul>
                    <li>
                        <a href="health-worker-dashboard.php">
                            <i class="isax isax-element-35"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="<?php echo ($current_page === 'health-worker-dashboard.php') ? 'active' : ''; ?>">
                        <a href="health-worker-dashboard.php">
                            <i class="fa-solid fa-user-injured"></i>
                            <span>Patient</span>
                        </a>
                    </li>
                    <li class="<?php echo ($current_page === 'health-worker-profile-settings.php') ? 'active' : ''; ?>">
                        <a href="health-worker-profile-settings.php">
                            <i class="isax isax-setting-2"></i>
                            <span>Profile Settings</span>
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
<!-- col-lg-4 left open - parent page closes it with </div> (same as doctor-leftside-menu) -->
