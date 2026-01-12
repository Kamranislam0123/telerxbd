<?php
/**
 * Doctor Profile Settings - TeleRx Bangladesh
 * Dynamic profile settings page with all forms in tabs
 */

// Include configuration
$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.html');
    exit;
}
require_once $config_path;

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
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
        header('Location: login.html');
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
    $doctor['department'] = $doctor['department'] ?? '';
    $doctor['present_address'] = $doctor['present_address'] ?? '';
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
    header('Location: login.html');
    exit;
}

// Split name for form fields
$name_parts = explode(' ', $doctor['name']);
$first_name = $name_parts[0] ?? '';
$last_name = $name_parts[1] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="utf-8">
		<title>TeleRx Bangladesh - Doctor Profile Settings</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="keywords" content="practo clone, doccure, doctor appointment, Practo clone html template, doctor booking template">
		<meta name="author" content="Practo Clone HTML Template - Doctor Booking Template">
		<meta property="og:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="og:type" content="website">
		<meta property="og:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta property="og:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta property="og:image" content="assets/img/preview-banner.jpg">
		<meta name="twitter:card" content="summary_large_image">
		<meta property="twitter:domain" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="twitter:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta name="twitter:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta name="twitter:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="twitter:image" content="assets/img/preview-banner.jpg">	
		
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
		
        <!-- select CSS -->
		<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

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
		<div class="main-wrapper home-one" data-magic-cursor="hide">
					
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
							<a href="index.html" class="navbar-brand logo">
								<img src="assets/img/logo.svg" class="img-fluid" alt="Logo">
							</a>
						</div>
						<div class="main-menu-wrapper">
							<div class="menu-header">
								<a href="index.html" class="menu-logo">
									<img src="assets/img/logo.svg" class="img-fluid" alt="Logo">
								</a>
								<a id="menu_close" class="menu-close" href="javascript:void(0);">
									<i class="fas fa-times"></i>
								</a>
							</div>
							<ul class="main-nav">
								<li class="has-submenu megamenu active">
									<a href="index.html">Home </a></li>
								<li><a href="search.php">Doctor List</a></li>
								<li><a href="search-2.php">Doctor List</a></li>
								<li><a href="doctor-profile.php">Doctor Profile</a></li>
								<li><a href="about-us.html">About Us</a></li>
								<li><a href="contact-us.html">Contact</a></li>
								<li><a href="blog-grid.html">Blog</a></li>
								<li class="login-link"><a href="login.html">Login / Signup</a></li>
							</ul>
						</div>
						<ul class="nav header-navbar-rht">
							<!-- Notifications -->
							<li class="nav-item dropdown noti-nav me-3 pe-0">
								<a href="#" class="dropdown-toggle active-dot active-dot-danger nav-link p-0" data-bs-toggle="dropdown">
									<i class="isax isax-notification-bing"></i>
								</a>
								<div class="dropdown-menu notifications dropdown-menu-end ">
									<div class="topnav-dropdown-header">
										<span class="notification-title">Notifications</span>
									</div>
									<div class="noti-content">
										<ul class="notification-list">
											<li class="notification-message">
												<a href="#">
													<div class="notify-block d-flex">
														<span class="avatar">
															<img class="avatar-img" alt="Ruby perin" src="assets/img/clients/client-01.jpg">
														</span>
														<div class="media-body">
															<h6>Travis Tremble <span class="notification-time">18.30 PM</span></h6>
															<p class="noti-details">Sent a amount of $210 for his Appointment  <span class="noti-title">Dr.Ruby perin </span></p>
														</div>
													</div>
												</a>
											</li>
											<li class="notification-message">
												<a href="#">
													<div class="notify-block d-flex">
														<span class="avatar">
															<img class="avatar-img" alt="Hendry Watt" src="assets/img/clients/client-02.jpg">
														</span>
														<div class="media-body">
															<h6>Travis Tremble <span class="notification-time">12 Min Ago</span></h6>
															<p class="noti-details"> has booked her appointment to  <span class="noti-title">Dr. Hendry Watt</span></p>
														</div>
													</div>
												</a>
											</li>
											<li class="notification-message">
												<a href="#">
													<div class="notify-block d-flex">
														<div class="avatar">
															<img class="avatar-img" alt="Maria Dyen" src="assets/img/clients/client-03.jpg">
														</div>
														<div class="media-body">
															<h6>Travis Tremble <span class="notification-time">6 Min Ago</span></h6>
															<p class="noti-details"> Sent a amount  $210 for his Appointment   <span class="noti-title">Dr.Maria Dyen</span></p>
														</div>
													</div>
												</a>
											</li>
											<li class="notification-message">
												<a href="#">
													<div class="notify-block d-flex">
														<div class="avatar avatar-sm">
															<img class="avatar-img" alt="client-image" src="assets/img/clients/client-04.jpg">
														</div>
														<div class="media-body">
															<h6>Travis Tremble <span class="notification-time">8.30 AM</span></h6>
															<p class="noti-details"> Send a message to his doctor</p>
														</div>
													</div>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</li>
							<!-- /Notifications -->

							<!-- Messages -->
							<li class="nav-item noti-nav me-3 pe-0">
								<a href="chat-doctor.html" class="dropdown-toggle nav-link active-dot active-dot-success p-0">
									<i class="isax isax-message-2"></i>
								</a>
							</li>
							<!-- /Messages -->

							<!-- User Menu -->
							<li class="nav-item dropdown has-arrow logged-item">
								<a href="#" class="nav-link ps-0" data-bs-toggle="dropdown">
									<span class="user-img">
										<img class="rounded-circle" src="assets/img/doctors-dashboard/doctor-profile-img.jpg" width="31" alt="Darren Elder">
									</span>
								</a>
								<div class="dropdown-menu dropdown-menu-end">
									<div class="user-header">
										<div class="avatar avatar-sm">
											<img src="assets/img/doctors-dashboard/doctor-profile-img.jpg" alt="User Image" class="avatar-img rounded-circle">
										</div>
										<div class="user-text">
											<h6>Dr Edalin Hendry</h6>
											<p class="text-muted mb-0">Doctor</p>
										</div>
									</div>
									<a class="dropdown-item" href="doctor-dashboard.html">Dashboard</a>
									<a class="dropdown-item" href="doctor-profile-settings.html">Profile Settings</a>
									<a class="dropdown-item" href="login.html">Logout</a>
								</div>
							</li>
							<!-- /User Menu -->
						</ul>
					</nav>
				</div>
			</header>
			<!-- /Header -->		

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
						<div class="col-lg-4 col-xl-3 theiaStickySidebar">
							
							<!-- Profile Sidebar -->
							<div class="profile-sidebar doctor-sidebar profile-sidebar-new">
								<div class="widget-profile pro-widget-content">
									<div class="profile-info-widget">
										<a href="doctor-profile.php?doctor_id=<?php echo $doctor_id; ?>" class="booking-doc-img">
											<img src="<?php echo htmlspecialchars($doctor['profile_image']); ?>" alt="User Image">
										</a>
										<div class="profile-det-info">
											<h3><a href="doctor-profile.php?doctor_id=<?php echo $doctor_id; ?>"><?php echo htmlspecialchars($doctor['name']); ?></a></h3>
											<div class="patient-details">
												<h5 class="mb-0"><?php echo htmlspecialchars($doctor['degrees'] ?: 'Doctor'); ?></h5>
											</div>
											<span class="badge doctor-role-badge"><i class="fa-solid fa-circle"></i><?php echo htmlspecialchars($doctor['department'] ?: 'Doctor'); ?></span>
										</div>
									</div>
								</div>
								<div class="doctor-available-head">
									<div class="input-block input-block-new">
										<label class="form-label">Availability <span class="text-danger">*</span></label>
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
												<a href="doctor-dashboard.php">
													<i class="isax isax-category-2"></i>
													<span>Dashboard</span>
												</a>
											</li>
											<li>
												<a href="doctor-request.html">
													<i class="isax isax-clipboard-tick"></i>
													<span>Requests</span>
													<small class="unread-msg">2</small>
												</a>
											</li>
											<li>
												<a href="appointments.html">
													<i class="isax isax-calendar-1"></i>
													<span>Appointments</span>
												</a>
											</li>
											<li>
												<a href="available-timings.html">
													<i class="isax isax-calendar-tick"></i>
													<span>Available Timings</span>
												</a>
											</li>
											<li>
												<a href="my-patients.html">
													<i class="fa-solid fa-user-injured"></i>
													<span>My Patients</span>
												</a>
											</li>
											<li>
												<a href="doctor-specialities.html">
													<i class="isax isax-clock"></i>
													<span>Specialties & Services</span>
												</a>
											</li>
											<li>
												<a href="reviews.html">
													<i class="isax isax-star-1"></i>
													<span>Reviews</span>
												</a>
											</li>
											<li>
												<a href="accounts.html">
													<i class="isax isax-profile-tick"></i>
													<span>Accounts</span>
												</a>
											</li>
											<li>
												<a href="invoices.html">
													<i class="isax isax-document-text"></i>
													<span>Invoices</span>
												</a>
											</li>
											<li>
												<a href="doctor-payment.html">
													<i class="fa-solid fa-money-bill-1"></i>
													<span>Payout Settings</span>
												</a>
											</li>																																				
											<li>
												<a href="chat-doctor.html">
													<i class="isax isax-messages-1"></i>
													<span>Message</span>
													<small class="unread-msg">7</small>
												</a>
											</li>
											<li class="active">
												<a href="doctor-profile-settings.html">
													<i class="isax isax-setting-2"></i>
													<span>Profile Settings</span>
												</a>
											</li>
											<li>
												<a href="social-media.html">
													<i class="fa-solid fa-shield-halved"></i>
													<span>Social Media</span>
												</a>
											</li>
											<li>
												<a href="doctor-change-password.html">
													<i class="isax isax-key"></i>
													<span>Change Password</span>
												</a>
											</li>
											<li>
												<a href="login.html">
													<i class="isax isax-logout"></i>
													<span>Logout</span>
												</a>
											</li>
										</ul>
									</nav>
								</div>
							</div>
							<!-- /Profile Sidebar -->							
							
						</div>
						<div class="col-lg-8 col-xl-9">
						
							<!-- Profile Settings -->
							<div class="dashboard-header">
								<h3><a href="doctor-profile.php?doctor_id=<?php echo $doctor_id; ?>"><?php echo htmlspecialchars($doctor['name']); ?></a> Profile Settings</h3>
							</div>

							<!-- Profile Form -->
							<div class="setting-title">
								<h5>Profile Information</h5>
							</div>

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
											<p class="form-text">Your Image should Below 4 MB, Accepted format jpg,png,svg</p>
										</div>
									</div>
								</div>

								<!-- Basic Information -->
								<div class="setting-card">
									<div class="row">
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Name <span class="text-danger">*</span></label>
												<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Email <span class="text-danger">*</span></label>
												<input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Phone Number <span class="text-danger">*</span></label>
												<input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Gender <span class="text-danger">*</span></label>
												<select class="form-control" name="gender" required>
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
												<input type="number" class="form-control" name="consultation_fee" value="<?php echo htmlspecialchars($doctor['consultation_fee']); ?>" step="0.01" min="0">
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
												<label class="form-label">BMDC Number <span class="text-danger">*</span></label>
												<input type="text" class="form-control" name="bmdc_no" value="<?php echo htmlspecialchars($doctor['bmdc_no']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Department</label>
												<input type="text" class="form-control" name="department" value="<?php echo htmlspecialchars($doctor['specialty']); ?>" placeholder="e.g., Cardiology, Pediatrics">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Present Address</label>
												<input type="text" class="form-control" name="present_address" value="<?php echo htmlspecialchars($doctor['address'] ?? ''); ?>" placeholder="Your current address">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Years of Experience</label>
												<input type="number" class="form-control" name="experience_years" value="<?php echo htmlspecialchars($doctor['experience_years']); ?>" min="0" max="50">
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
								<div class="modal-btn text-end">
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
							
   
			<!-- Footer Section -->
			<footer class="footer inner-footer">
				<div class="footer-top">
					<div class="container">
						<div class="row">
							<div class="col-lg-8">
								<div class="row">
									<div class="col-lg-3 col-md-3">
										<div class="footer-widget footer-menu">
											<h6 class="footer-title">Company</h6>
											<ul>
												<li><a href="about-us.html">About</a></li>
												<li><a href="search.php">Features</a></li>
												<li><a href="javascript:void(0);">Works</a></li>
												<li><a href="javascript:void(0);">Careers</a></li>
												<li><a href="javascript:void(0);">Locations</a></li>
											</ul>
										</div>
									</div>
									<div class="col-lg-3 col-md-3">
										<div class="footer-widget footer-menu">
											<h6 class="footer-title">Treatments</h6>
											<ul>
												<li><a href="search.php">Dental</a></li>
												<li><a href="search.php">Cardiac</a></li>
												<li><a href="search.php">Spinal Cord</a></li>
												<li><a href="search.php">Hair Growth</a></li>
												<li><a href="search.php">Anemia & Disorder</a></li>
											</ul>
										</div>
									</div>
									<div class="col-lg-3 col-md-3">
										<div class="footer-widget footer-menu">
											<h6 class="footer-title">Specialities</h6>
											<ul>
												<li><a href="search.php">Transplant</a></li>
												<li><a href="search.php">Cardiologist</a></li>
												<li><a href="search.php">Oncology</a></li>
												<li><a href="search.php">Pediatrics</a></li>
												<li><a href="search.php">Gynacology</a></li>
											</ul>
										</div>
									</div>
									<div class="col-lg-3 col-md-3">
										<div class="footer-widget footer-menu">
											<h6 class="footer-title">Utilites</h6>
											<ul>
												<li><a href="pricing.html">Pricing</a></li>
												<li><a href="contact-us.html">Contact</a></li>
												<li><a href="contact-us.html">Request A Quote</a></li>
												<li><a href="javascript:void(0);">Premium Membership</a></li>
												<li><a href="javascript:void(0);">Integrations</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-7">
								<div class="footer-widget">
									<h6 class="footer-title">Newsletter</h6>
									<p class="mb-2">Subscribe & Stay Updated from the Doccure</p>
									<div class="subscribe-input">
										<form action="#">
											<input type="email" class="form-control" placeholder="Enter Email Address">
											<button type="submit" class="btn btn-md btn-primary-gradient d-inline-flex align-items-center"><i class="isax isax-send-25 me-1"></i>Send</button>
										</form>
									</div>
									<div class="social-icon">
										<h6 class="mb-3">Connect With Us</h6>
										<ul>
											<li>
												<a href="javascript:void(0);"><i class="fa-brands fa-facebook"></i></a>
											</li>
											<li>
												<a href="javascript:void(0);"><i class="fa-brands fa-x-twitter"></i></a>
											</li>
											<li>
												<a href="javascript:void(0);"><i class="fa-brands fa-instagram"></i></a>
											</li>
											<li>
												<a href="javascript:void(0);"><i class="fa-brands fa-linkedin"></i></a>
											</li>
											<li>
												<a href="javascript:void(0);"><i class="fa-brands fa-pinterest"></i></a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="footer-bg">
						<img src="assets/img/bg/footer-bg-01.png" alt="img" class="footer-bg-01">
						<img src="assets/img/bg/footer-bg-02.png" alt="img" class="footer-bg-02">
						<img src="assets/img/bg/footer-bg-03.png" alt="img" class="footer-bg-03">
						<img src="assets/img/bg/footer-bg-04.png" alt="img" class="footer-bg-04">
						<img src="assets/img/bg/footer-bg-05.png" alt="img" class="footer-bg-05">
					</div>
				</div>
				<div class="footer-bottom">
					<div class="container">
						<!-- Copyright -->
						<div class="copyright">
							<div class="copyright-text">
								<p class="mb-0">Copyright © 2026 TeleRx Bangladesh. All Rights Reserved</p>
							</div>
							<!-- Copyright Menu -->
							<div class="copyright-menu">
								<ul class="policy-menu">
									<li><a href="javascript:void(0);">Legal Notice</a></li>
									<li><a href="privacy-policy.html">Privacy Policy</a></li>
									<li><a href="javascript:void(0);">Refund Policy</a></li>
								</ul>
							</div>
							<!-- /Copyright Menu -->
							<ul class="payment-method">
								<li><a href="javascript:void(0);"><img src="assets/img/icons/card-01.svg" alt="Img"></a></li>
								<li><a href="javascript:void(0);"><img src="assets/img/icons/card-02.svg" alt="Img"></a></li>
								<li><a href="javascript:void(0);"><img src="assets/img/icons/card-03.svg" alt="Img"></a></li>
								<li><a href="javascript:void(0);"><img src="assets/img/icons/card-04.svg" alt="Img"></a></li>
								<li><a href="javascript:void(0);"><img src="assets/img/icons/card-05.svg" alt="Img"></a></li>
								<li><a href="javascript:void(0);"><img src="assets/img/icons/card-06.svg" alt="Img"></a></li>
							</ul>
						</div>
						<!-- /Copyright -->					
					</div>
				</div>
			</footer>
			<!-- /Footer Section -->
		   
		</div>
		<!-- /Main Wrapper -->
	  
		<!-- jQuery -->
		<script src="assets/js/jquery-3.7.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="assets/js/bootstrap.bundle.min.js"></script>
		
		<!-- Sticky Sidebar JS -->
        <script src="assets/plugins/theia-sticky-sidebar/ResizeSensor.js"></script>
        <script src="assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js"></script>
		
		<!-- Select2 JS -->
		<script src="assets/plugins/select2/js/select2.min.js"></script>
		
		<!-- Bootstrap Tagsinput JS -->
		<script src="assets/plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.js"></script>
		
		<!-- Profile Settings JS -->
		<script src="assets/js/profile-settings.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/script.js"></script>

		<!-- Profile Settings Form Handler -->
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