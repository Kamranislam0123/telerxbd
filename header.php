<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" >
    <meta name="description" content="TeleRx Bangladesh offers many features, like scheduling appointments with  top doctors via voice, video call & chat.">
    <meta name="keywords" content="TeleRx Bangladesh, online doctor appointment, doctor booking Bangladesh, telemedicine Bangladesh, online medical consultation, book doctor Dhaka, specialist doctors BD, hospital appointment Bangladesh, clinic appointment booking, virtual doctor visit BD, health care services Bangladesh, online healthcare platform, doctor video call Bangladesh, medical services BD, lab test booking Bangladesh">
    <meta name="author" content="TeleRx Bangladesh">
    <meta property="og:url" content="https://telerxbd.com">
    <meta property="og:type" content="website">
    <meta property="og:title" content="TeleRx Bangladesh">
    <meta property="og:description" content="TeleRx Bangladesh offers many features, like scheduling appointments with  top doctors via voice, video call & chat.">
    <meta property="og:image" content="assets/img/preview-banner.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="https://telerxbd.com">
    <meta property="twitter:url" content="https://telerxbd.com">
    <meta name="twitter:title" content="TeleRx Bangladesh - Care Beyond Distance">
    <meta name="twitter:description" content="TeleRx Bangladesh offers many features, like scheduling appointments with  top doctors via voice, video call & chat.">
    <meta name="twitter:image" content="assets/img/preview-banner.jpg">
    <title>TeleRx Bangladesh</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">

    <!-- Theme Settings Js -->
    <script src="assets/js/theme-script.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <!-- Iconsax CSS-->
    <link rel="stylesheet" href="assets/css/iconsax.css">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

    <!-- select CSS -->
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="assets/css/feather.css">

    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">

    <!-- Animation CSS -->
    <link rel="stylesheet" href="assets/css/aos.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper home-one">

        <!-- Header -->
        <header class="header header-custom header-fixed header-one home-head-one">
            <div class="container">
                <nav class="navbar navbar-expand-lg header-nav">
                    <div class="navbar-header">
                        <a id="mobile_btn" href="javascript:void(0);">
                            <span class="bar-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </a>
                        <a href="/" class="navbar-brand logo">
                            <img src="assets/img/logo.svg" class="img-fluid" alt="Logo">
                        </a>
                    </div>
                    <div class="main-menu-wrapper">
                        <div class="menu-header">
                            <a href="/" class="menu-logo">
                                <img src="assets/img/logo.svg" class="img-fluid" alt="Logo">
                            </a>
                            <a id="menu_close" class="menu-close" href="javascript:void(0);">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                        <ul class="main-nav">
                            <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                                <a href="/">Home</a>
                            </li>
                            <li class="<?php echo ($current_page == 'doctors.php') ? 'active' : ''; ?>">
                                <a href="doctors.php">Our Doctors</a>
                            </li>
                            <li class="<?php echo ($current_page == 'globalcare.php') ? 'active' : ''; ?>">
                                <a href="global-care.php">Global Care</a>
                            </li>
                            <li class="<?php echo ($current_page == 'welfare.php') ? 'active' : ''; ?>">
                                <a href="welfare.php">Welfare</a>
                            </li>
                            <li class="<?php echo ($current_page == 'about-us.php') ? 'active' : ''; ?>">
                                <a href="about-us.php">About Us</a>
                            </li>
                            <li class="<?php echo ($current_page == 'contact-us.php') ? 'active' : ''; ?>">
                                <a href="contact-us.php">Contact</a>
                            </li>
                            <!-- Mobile view login/signup link -->
                            <li class="login-link">
                                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                                    <a href="logout.php">Logout</a>
                                <?php else: ?>
                                    <a href="login.php">Login / Signup</a>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>

                    <!-- Right side navigation -->
                    <ul class="nav header-navbar-rht">
                        <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                            <!-- Logged in user menu -->
                            <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'doctor'): ?>
                                <!-- Doctor specific menu -->
                                <li class="nav-item dropdown noti-nav me-3 pe-0">
                                    <a href="#" class="dropdown-toggle active-dot active-dot-danger nav-link p-0" data-bs-toggle="dropdown">
                                        <i class="isax isax-notification-bing"></i>
                                    </a>
                                    <!-- Notification dropdown -->
                                    <div class="dropdown-menu notifications dropdown-menu-end">
                                        <!-- ... notification content ... -->
                                    </div>
                                </li>
                                <li class="nav-item noti-nav me-3 pe-0">
                                    <a href="chat-doctor.html" class="nav-link active-dot active-dot-success p-0">
                                        <i class="isax isax-message-2"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <!-- User dropdown menu -->
                            <li class="nav-item dropdown has-arrow logged-item">
                                <a href="#" class="nav-link ps-0" data-bs-toggle="dropdown">
                                    <span class="user-img">
                                        <?php
                                        $profile_image = 'assets/img/doctors-dashboard/doctor-profile-img.jpg';
                                        $user_name = 'User';

                                        if($_SESSION['user_type'] == 'doctor') {
                                            $profile_image = $_SESSION['profile_image'] ?? $profile_image;
                                            $user_name = $_SESSION['doctor_name'] ?? 'Doctor';
                                        } elseif($_SESSION['user_type'] == 'healthcare') {
                                            $profile_image = $_SESSION['profile_image'] ?? $profile_image;
                                            $user_name = $_SESSION['healthcare_name'] ?? 'Healthcare';
                                        } elseif($_SESSION['user_type'] == 'patient') {
                                            $profile_image = $_SESSION['profile_image'] ?? $profile_image;
                                            $user_name = $_SESSION['patient_name'] ?? 'Patient';
                                        }
                                        ?>
                                        <img class="rounded-circle" src="<?php echo htmlspecialchars($profile_image); ?>" width="31" alt="<?php echo htmlspecialchars($user_name); ?>">
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="user-header">
                                        <div class="avatar avatar-sm">
                                            <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="User Image" class="avatar-img rounded-circle">
                                        </div>
                                        <div class="user-text">
                                            <h6><?php echo htmlspecialchars($user_name); ?></h6>
                                            <p class="text-muted mb-0">
                                                <?php
                                                echo $_SESSION['user_type'] == 'doctor' ? 'Doctor' :
                                                        ($_SESSION['user_type'] == 'healthcare' ? 'Healthcare Provider' : 'Patient');
                                                ?>
                                            </p>
                                        </div>
                                    </div>

                                    <?php if($_SESSION['user_type'] == 'doctor'): ?>
                                        <a class="dropdown-item" href="doctor-dashboard.php">Dashboard</a>
                                        <a class="dropdown-item" href="doctor-profile-settings.php">Profile Settings</a>
                                    <?php elseif($_SESSION['user_type'] == 'healthcare'): ?>
                                        <a class="dropdown-item" href="health-worker-dashboard.php">Dashboard</a>
                                        <a class="dropdown-item" href="health-worker-profile-settings.php">Profile Settings</a>
                                    <?php elseif($_SESSION['user_type'] == 'patient'): ?>
                                        <a class="dropdown-item" href="patient-dashboard.php">Dashboard</a>
                                        <a class="dropdown-item" href="patient-profile-settings.php">Profile Settings</a>
                                    <?php endif; ?>

                                    <a class="dropdown-item" href="php/logout.php">Logout</a>
                                </div>
                            </li>
                        <?php else: ?>
                            <!-- Guest/Non-logged in menu -->
                            <li class="register-btn">
                                <a href="registration.php" class="btn btn-dark reg-btn">
                                    <i class="isax isax-user"></i>Register
                                </a>
                            </li>
                            <li class="register-btn">
                                <a href="login.php" class="btn btn-primary log-btn">
                                    <i class="isax isax-lock"></i>Login
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </header>
        <div class="header-margin"></div>
        <!-- /Header -->