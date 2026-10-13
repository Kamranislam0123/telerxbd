<?php
require_once __DIR__ . '/php/config.php';

$current_page = basename($_SERVER['PHP_SELF']);

// ---- helpers for the new header (highlight the page the visitor is on) ----
$hf_v = '1';   // change this number after editing site-chrome.css / site-chrome.js if the browser shows the old design
$hf_page = strtolower(pathinfo($current_page, PATHINFO_FILENAME));                 // index, doctors, products ...
$hf_path = strtolower(str_replace('\\', '/', $_SERVER['PHP_SELF']));               // /HomeService/home-service.php
$hf_in_homecare = (strpos($hf_path, '/homeservice/') !== false);
$hf_in_shop     = (strpos($hf_path, '/products/') !== false);
$hf_on = function ($yes) { return $yes ? 'is-active' : ''; };
$hf_logged_in = (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true);
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
    <link rel="shortcut icon" type="image/x-icon" sizes="48x48" href="assets/img/fav.png">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/appleicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Iconsax CSS-->
    <link rel="stylesheet" href="assets/css/iconsax.css">

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

    <!-- Subscription CSS -->
    <link rel="stylesheet" href="assets/css/subscription.css">

    <!-- Medical Camp CSS -->
    <link rel="stylesheet" href="assets/css/medical-camp.css">

    <!-- Training CSS -->
    <link rel="stylesheet" href="assets/css/training.css">

    <!-- Index Home Page CSS -->
    <link rel="stylesheet" href="assets/css/index-program-sections.css">
    
    <!-- Login CSS -->
    <link rel="stylesheet" href="assets/css/login.css">

    <!-- Registration CSS -->
    <link rel="stylesheet" href="assets/css/registration.css">

    <!-- Modern header & footer -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/site-chrome.css?v=<?php echo $hf_v; ?>">

    <!-- Rangeslider CSS -->
    <link rel="stylesheet" href="assets/plugins/ion-rangeslider/css/ion.rangeSlider.css">
    <link rel="stylesheet" href="assets/plugins/ion-rangeslider/css/ion.rangeSlider.min.css">

    <!-- Theme Settings Js -->
    <script src="assets/js/theme-script.js"></script>

</head>
<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper home-one">

    <!-- Header -->
    <header class="hf-header" id="hf-header">
        <!-- top strip (desktop) -->
        <div class="hf-top">
            <div class="hf-wrap hf-top-inner">
                <div class="hf-follow">
                    <span>Follow us :</span>
                    <a href="https://www.facebook.com/telerxbd" aria-label="Facebook" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                    <a href="https://www.youtube.com/@TeleRxBD" aria-label="YouTube" target="_blank" rel="noopener"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
                    <a href="https://www.instagram.com/telerxbd" aria-label="Instagram" target="_blank" rel="noopener"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                    <a href="https://bd.linkedin.com/company/telerxbd" aria-label="LinkedIn" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
                </div>
                <a href="https://wa.me/8801836838888" target="_blank" rel="noopener noreferrer" class="hf-helpline">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                    <span>24/7 Helpline: <strong>01836-838888</strong></span>
                </a>
            </div>
        </div>

        <!-- main bar -->
        <div class="hf-bar">
            <div class="hf-wrap hf-bar-inner">
                <button type="button" class="hf-burger" id="hf_burger" aria-label="Open menu" aria-expanded="false" aria-controls="hf_menu">
                    <span></span><span></span><span></span>
                </button>

                <a href="/" class="hf-logo" aria-label="TeleRx Bangladesh - Home">
                    <img src="assets/img/logo.svg" alt="TeleRx Bangladesh">
                </a>

                <div class="hf-menu" id="hf_menu">
                    <div class="hf-menu-head">
                        <a href="/" class="hf-logo" aria-label="TeleRx Bangladesh - Home">
                            <img src="assets/img/logo.svg" alt="TeleRx Bangladesh">
                        </a>
                        <button type="button" class="hf-close" id="hf_close" aria-label="Close menu"><i class="fas fa-times" aria-hidden="true"></i></button>
                    </div>

                    <ul class="hf-nav">
                        <li class="<?php echo $hf_on($hf_page === 'index' && !$hf_in_homecare && !$hf_in_shop); ?>">
                            <a class="hf-link" href="/">Home</a>
                        </li>
                        <li class="<?php echo $hf_on(in_array($hf_page, ['doctors', 'doctor-profile'], true)); ?>">
                            <a class="hf-link" href="doctors">Our Doctors</a>
                        </li>
                        <li class="<?php echo $hf_on($hf_page === 'welfare'); ?>">
                            <a class="hf-link" href="welfare">Welfare</a>
                        </li>
                        <li class="<?php echo $hf_on($hf_in_homecare); ?>">
                            <a class="hf-link" href="HomeService/home-service">Home Care</a>
                        </li>
                        <li class="<?php echo $hf_on($hf_in_shop); ?>">
                            <a class="hf-link" href="Products/products">Products</a>
                        </li>
                        <li class="<?php echo $hf_on(in_array($hf_page, ['subscription', 'subscription-checkout'], true)); ?>">
                            <a class="hf-link" href="subscription">Subscription</a>
                        </li>
                        <li class="hf-dd <?php echo $hf_on(in_array($hf_page, ['medical-camp', 'corporate', 'training', 'about-us', 'contact', 'global-care'], true)); ?>">
                            <button type="button" class="hf-link" aria-haspopup="true" aria-expanded="false">
                                More <i class="fas fa-chevron-down hf-caret" aria-hidden="true"></i>
                            </button>
                            <ul class="hf-dd-menu">
                                <li class="<?php echo $hf_on($hf_page === 'medical-camp'); ?>">
                                    <a href="medical-camp"><i class="fa-solid fa-house-medical" aria-hidden="true"></i>Medical Camp</a>
                                </li>
                                <li class="<?php echo $hf_on($hf_page === 'corporate'); ?>">
                                    <a href="corporate"><i class="fa-solid fa-building" aria-hidden="true"></i>Corporate/NGO</a>
                                </li>
                                <li class="<?php echo $hf_on($hf_page === 'training'); ?>">
                                    <a href="training"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>Training</a>
                                </li>
                                <li class="<?php echo $hf_on($hf_page === 'about-us'); ?>">
                                    <a href="about-us"><i class="fa-solid fa-circle-info" aria-hidden="true"></i>About Us</a>
                                </li>
                                <li class="<?php echo $hf_on($hf_page === 'contact'); ?>">
                                    <a href="contact"><i class="fa-solid fa-headset" aria-hidden="true"></i>Contact Us</a>
                                </li>
                                <li class="<?php echo $hf_on($hf_page === 'global-care'); ?>">
                                    <a href="global-care"><i class="fa-solid fa-earth-asia" aria-hidden="true"></i>Global Care</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <!-- Phone menu: buttons at the bottom -->
                    <div class="hf-menu-foot">
                        <a href="emergency-booking" class="hf-btn is-emergency is-block"><i class="fa-solid fa-truck-medical" aria-hidden="true"></i>Emergency</a>
                        <?php if ($hf_logged_in): ?>
                            <a href="php/logout.php" class="hf-btn is-ghost is-block"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>Logout</a>
                        <?php else: ?>
                            <a href="registration" class="hf-btn is-ghost is-block"><i class="isax isax-user" aria-hidden="true"></i>Register</a>
                            <a href="login" class="hf-btn is-primary is-block"><i class="isax isax-lock" aria-hidden="true"></i>Login / Signup</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right side -->
                <div class="hf-actions">
                    <a href="emergency-booking" class="hf-btn is-emergency hf-hide-sm">
                        <i class="fa-solid fa-truck-medical" aria-hidden="true"></i>Emergency
                    </a>
                    <?php if ($hf_logged_in): ?>
                        <!-- Logged in user menu -->
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'doctor'): ?>
                            <!-- Doctor specific menu -->
                            <div class="dropdown hf-hide-sm">
                                <a href="#" class="hf-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                                    <i class="isax isax-notification-bing" aria-hidden="true"></i><span class="hf-dot"></span>
                                </a>
                                <!-- Notification dropdown -->
                                <div class="dropdown-menu notifications dropdown-menu-end hf-notify">
                                    <!-- ... notification content ... -->
                                    <p>No new notifications</p>
                                </div>
                            </div>
                            <a href="chat-doctor.html" class="hf-icon-btn hf-hide-sm" aria-label="Messages">
                                <i class="isax isax-message-2" aria-hidden="true"></i><span class="hf-dot is-green"></span>
                            </a>
                        <?php endif; ?>

                        <!-- User dropdown menu -->
                        <div class="dropdown hf-user">
                            <?php
                            $profile_image = 'assets/img/doctors-dashboard/doctor-profile-img.jpg';
                            $user_name = 'User';

                            if($_SESSION['user_type'] == 'doctor') {
                                $profile_image = $_SESSION['profile_image'] ?? $profile_image;
                                $user_name = $_SESSION['doctor_name'] ?? 'Doctor';
                            } elseif($_SESSION['user_type'] == 'healthcare') {
                                $profile_image = $_SESSION['profile_image'] ?? $profile_image;
                                $user_name = $_SESSION['healthcare_name'] ?? 'Healthcare';
                            } elseif($_SESSION['user_type'] == 'special_tid') {
                                $profile_image = $_SESSION['profile_image'] ?? $profile_image;
                                $user_name = $_SESSION['special_tid_name'] ?? 'Special TID User';
                            } elseif($_SESSION['user_type'] == 'patient') {
                                $profile_image = $_SESSION['profile_image'] ?? $profile_image;
                                $user_name = $_SESSION['patient_name'] ?? 'Patient';
                            }
                            ?>
                            <a href="#" class="hf-user-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu">
                                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="<?php echo htmlspecialchars($user_name); ?>">
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="hf-user-head">
                                    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="User Image">
                                    <div>
                                        <h6><?php echo htmlspecialchars($user_name); ?></h6>
                                        <p>
                                            <?php
                                            echo $_SESSION['user_type'] == 'doctor' ? 'Doctor' :
                                                    ($_SESSION['user_type'] == 'healthcare' ? 'Healthcare Provider' :
                                                    (($_SESSION['user_type'] == 'special_tid') ? 'Special TID User' : 'Patient'));
                                            ?>
                                        </p>
                                    </div>
                                </div>

                                <?php if($_SESSION['user_type'] == 'doctor'): ?>
                                    <a class="dropdown-item" href="doctor-dashboard"><i class="fa-solid fa-gauge" aria-hidden="true"></i>Dashboard</a>
                                    <a class="dropdown-item" href="doctor-profile-settings"><i class="fa-solid fa-user-gear" aria-hidden="true"></i>Profile Settings</a>
                                <?php elseif($_SESSION['user_type'] == 'healthcare'): ?>
                                    <a class="dropdown-item" href="health-worker-dashboard"><i class="fa-solid fa-gauge" aria-hidden="true"></i>Dashboard</a>
                                    <a class="dropdown-item" href="health-worker-profile-settings"><i class="fa-solid fa-user-gear" aria-hidden="true"></i>Profile Settings</a>
                                <?php elseif($_SESSION['user_type'] == 'special_tid'): ?>
                                    <a class="dropdown-item" href="health-worker-dashboard"><i class="fa-solid fa-gauge" aria-hidden="true"></i>Dashboard</a>
                                <?php elseif($_SESSION['user_type'] == 'patient'): ?>
                                    <a class="dropdown-item" href="patient-dashboard"><i class="fa-solid fa-gauge" aria-hidden="true"></i>Dashboard</a>
                                    <a class="dropdown-item" href="patient-medical-history"><i class="fa-solid fa-notes-medical" aria-hidden="true"></i>Medical History</a>
                                    <a class="dropdown-item" href="patient-profile-settings"><i class="fa-solid fa-user-gear" aria-hidden="true"></i>Profile Settings</a>
                                <?php endif; ?>

                                <a class="dropdown-item is-logout" href="php/logout.php"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Guest/Non-logged in menu -->
                        <a href="registration" class="hf-btn is-ghost hf-hide-sm">
                            <i class="isax isax-user" aria-hidden="true"></i>Register
                        </a>
                        <a href="login" class="hf-btn is-primary">
                            <i class="isax isax-lock" aria-hidden="true"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- dark layer behind the phone menu -->
        <div class="hf-backdrop" id="hf_backdrop"></div>
    </header>
    <div class="hf-spacer"></div>

    <!-- /Header -->