<?php
require_once 'php/config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
	header("Location: login.php");
	exit;
}

// Get appointment ID from URL
$appointment_id = $_GET['appointment_id'] ?? null;
$channel_name = $appointment_id ? "appointment_" . $appointment_id : "general_call";

// Assign unique UID for Agora (simple mapping for now)
if ($_SESSION['user_type'] === 'doctor' && isset($_SESSION['doctor_id'])) {
	$uid = 1000 + (int) $_SESSION['doctor_id'];
	$user_name = $_SESSION['doctor_name'] ?? 'Doctor';
} else if ($_SESSION['user_type'] === 'patient' && isset($_SESSION['patient_id'])) {
	$uid = 2000 + (int) $_SESSION['patient_id'];
	$user_name = $_SESSION['patient_name'] ?? 'Patient';
} else if ($_SESSION['user_type'] === 'healthcare' && isset($_SESSION['healthcare_id'])) {
	$uid = 3000 + (int) $_SESSION['healthcare_id'];
	$user_name = $_SESSION['healthcare_name'] ?? 'Health Worker';
} else {
	$uid = rand(4000, 5000);
	$user_name = 'User';
}

$other_party_name = 'User';
$other_party_image = 'assets/img/patients/patient1.jpg';
$details_html = '';

if ($appointment_id) {
	try {
		$conn = getDBConnection();
		$stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
		$stmt->bind_param("i", $appointment_id);
		$stmt->execute();
		$res = $stmt->get_result();
		if ($res->num_rows > 0) {
			$appointment = $res->fetch_assoc();

			$stmt_d = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
			$stmt_d->bind_param("i", $appointment['doctor_id']);
			$stmt_d->execute();
			$doctor = $stmt_d->get_result()->fetch_assoc();

			if ($_SESSION['user_type'] === 'doctor' || $_SESSION['user_type'] === 'healthcare') {
				$other_party_name = $appointment['patient_name'] ?? 'Patient';
				$other_party_image = 'assets/img/patients/patient.jpg';

				// Fetch full patient details
				$patient_info = null;
				if (isset($appointment['patient_id'])) {
					$stmt_p = $conn->prepare("SELECT * FROM patients WHERE id = ?");
					$stmt_p->bind_param("i", $appointment['patient_id']);
					$stmt_p->execute();
					$patient_info = $stmt_p->get_result()->fetch_assoc();
					if ($patient_info && !empty($patient_info['profile_image'])) {
						$other_party_image = $patient_info['profile_image'];
					}
				}
			}
		}
		$conn->close();
	} catch (Exception $e) {
		error_log("Error fetching details: " . $e->getMessage());
	}
}

// Include required scripts for Agora Token generation if needed on frontend via AJAX
// or we can generate it here if we want but AJAX is cleaner for keeping credentials hidden.
?>
<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<title>Doccure</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description"
		content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
	<meta name="keywords"
		content="practo clone, doccure, doctor appointment, Practo clone html template, doctor booking template">
	<meta name="author" content="Practo Clone HTML Template - Doctor Booking Template">
	<meta property="og:url" content="https://doccure.dreamstechnologies.com/html/">
	<meta property="og:type" content="website">
	<meta property="og:title" content="Doctors Appointment HTML Website Templates | Doccure">
	<meta property="og:description"
		content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
	<meta property="og:image" content="assets/img/preview-banner.jpg">
	<meta name="twitter:card" content="summary_large_image">
	<meta property="twitter:domain" content="https://doccure.dreamstechnologies.com/html/">
	<meta property="twitter:url" content="https://doccure.dreamstechnologies.com/html/">
	<meta name="twitter:title" content="Doctors Appointment HTML Website Templates | Doccure">
	<meta name="twitter:description"
		content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
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

	<style>
		#local-video {
			width: 100%;
			height: 100%;
			background: #000;
			border-radius: 10px;
		}

		#remote-video {
			width: 100%;
			height: 100%;
			background: #2e2e2e;
			border-radius: 10px;
		}

		.call-content-wrap {
			position: relative;
		}

		.call-window {
			position: relative;
		}

		.user-video {
			width: 100%;
			height: 500px;
			overflow: hidden;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 10px;
		}

		.my-video {
			position: absolute;
			bottom: 80px;
			right: 20px;
			width: 150px;
			height: 120px;
			z-index: 10;
			border: 2px solid #fff;
			border-radius: 10px;
			overflow: hidden;
		}

		.call-footer {
			position: absolute;
			bottom: 10px;
			width: 100%;
			z-index: 100;
			background: transparent !important;
			border: none !important;
		}

		#video-toggle,
		#audio-toggle,
		#leave-btn,
		#view-details-btn {
			display: none;
		}

		#join-btn-container {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			z-index: 100;
		}

		#join-btn {
			padding: 15px 40px;
			font-size: 18px;
		}
		.medicine-row .btn-remove-medicine {
			padding: 5px 10px;
			color: #ff0000;
		}
		.medicine-row {
			border-bottom: 1px solid #eee;
			padding-bottom: 10px;
			margin-bottom: 10px;
		}
		.medicine-row:last-child {
			border-bottom: none;
		}
		.patient-details-card {
			background: #fff;
			border-radius: 15px;
			padding: 25px;
			margin-top: 30px;
			box-shadow: 0 4px 20px rgba(0,0,0,0.08);
			border: 1px solid #eef2f6;
		}
		.patient-details-card h4 {
			margin-bottom: 25px;
			color: #15558d;
			font-weight: 700;
			font-size: 1.25rem;
			display: flex;
			align-items: center;
			border-bottom: 2px solid #f0f4f8;
			padding-bottom: 15px;
		}
		.patient-details-card h4 i {
			color: #15558d;
			background: #eef2f6;
			padding: 10px;
			border-radius: 10px;
			margin-right: 12px;
			font-size: 1.1rem;
		}
		.detail-group-title {
			font-size: 0.9rem;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			color: #888;
			margin-bottom: 15px;
			font-weight: 600;
		}
		.detail-row {
			margin-bottom: 12px;
			display: flex;
			align-items: flex-start;
		}
		.detail-label {
			font-weight: 600;
			color: #6c757d;
			width: 140px;
			flex-shrink: 0;
			font-size: 0.9rem;
		}
		.detail-value {
			color: #272b41;
			font-weight: 500;
			font-size: 0.95rem;
			word-break: break-word;
		}
		.vital-badge {
			background: #f8f9fa;
			border: 1px solid #e9ecef;
			border-radius: 8px;
			padding: 10px 15px;
			height: 100%;
			transition: all 0.3s ease;
		}
		.vital-badge:hover {
			border-color: #15558d;
			background: #fff;
			box-shadow: 0 2px 10px rgba(21, 85, 141, 0.05);
		}
		.vital-label {
			display: block;
			font-size: 0.75rem;
			color: #777;
			margin-bottom: 4px;
			font-weight: 600;
			text-transform: uppercase;
		}
		.vital-value {
			display: block;
			font-size: 1rem;
			color: #15558d;
			font-weight: 700;
		}
		.symptoms-box {
			background: #fff9f0;
			border: 1px solid #ffe8cc;
			border-radius: 10px;
			padding: 15px;
			margin-top: 10px;
			color: #664d03;
			font-size: 0.95rem;
			line-height: 1.5;
		}
	</style>

	<!-- Feathericon CSS -->
	<link rel="stylesheet" href="assets/css/feather.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/custom.css">

</head>

<body class="call-page">

	<!-- Main Wrapper -->
	<div class="main-wrapper">

		<?php include 'header.php'; ?>


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
							<li class="breadcrumb-item"><a href="index.html"><i class="isax isax-home-15"></i></a></li>
							<li class="breadcrumb-item active">Video Call</li>
						</ol>
						<h2 class="breadcrumb-title">Video Call</h2>
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
	<div class="content">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 mx-auto">
					<!-- Call Wrapper -->
					<div class="call-wrapper">
						<div class="call-main-row">
							<div class="call-main-wrapper">
								<div class="call-view">
									<div class="call-window">

										<!-- Call Header -->
										<div class="fixed-header">
											<div class="navbar">
												<div class="user-details">
													<div class="float-start user-img">
														<a class="avatar avatar-sm me-2" href="javascript:void(0);"
															title="<?php echo htmlspecialchars($other_party_name); ?>">
															<img src="<?php echo htmlspecialchars($other_party_image); ?>"
																alt="User Image" class="rounded-circle">
															<span class="status online"></span>
														</a>
													</div>
													<div class="user-info float-start">
														<a
															href="javascript:void(0);"><span><?php echo htmlspecialchars($other_party_name); ?></span></a>
														<span class="last-seen">UID: <?php echo $uid; ?></span>
													</div>
												</div>
												<ul class="nav float-end custom-menu">
													<li class="nav-item">
														<span class="badge bg-success">Channel:
															<?php echo htmlspecialchars($channel_name); ?></span>
													</li>
												</ul>
											</div>
										</div>
										<!-- /Call Header -->

										<!-- Call Contents -->
										<div class="call-contents">
											<div class="call-content-wrap">
												<div class="user-video">
													<div id="join-btn-container">
														<button id="join-btn"
															class="btn btn-success rounded-pill border-0 px-4 py-2">Start
															Consultation</button>
													</div>
													<div id="remote-video"></div>
												</div>
												<div class="my-video">
													<div id="local-video"></div>
												</div>
											</div>
										</div>
										<!-- Call Contents -->

										<!-- Call Footer -->
										<div class="call-footer">
											<div class="call-icons">
												<ul class="call-items">
													<li class="call-item">
														<a href="javascript:void(0)" class="mute-video"
															id="video-toggle" title="Disable Video" data-placement="top"
															data-bs-toggle="tooltip">
															<i class="isax isax-video"></i>
														</a>
													</li>
													<li class="call-item">
														<a href="javascript:void(0)" class="call-end" id="leave-btn">
															<i class="isax isax-call"></i>
														</a>
													</li>
													<li class="call-item">
														<a href="javascript:void(0)" class="mute-bt" id="audio-toggle"
															title="Mute" data-placement="top" data-bs-toggle="tooltip">
															<i class="isax isax-microphone-2"></i>
														</a>
													</li>
												</ul>
											</div>
										</div>
										<!-- /Call Footer -->

									</div>
								</div>

							</div>
						</div>
					</div>
					<!-- /Call Wrapper -->

					<!-- Patient Details Section -->
					<?php if (($_SESSION['user_type'] === 'doctor' || $_SESSION['user_type'] === 'healthcare') && isset($patient_info)): ?>
					<div class="patient-details-card">
						<h4><i class="isax isax-user me-2"></i>Patient Details</h4>
						
						<div class="row">
							<!-- Basic Information -->
							<div class="col-lg-4 col-md-6">
								<h6 class="detail-group-title">Profile Information</h6>
								<div class="detail-row">
									<span class="detail-label">Name:</span>
									<span class="detail-value"><?php echo htmlspecialchars($patient_info['name'] ?? 'N/A'); ?></span>
								</div>
								<div class="detail-row">
									<span class="detail-label">Gender:</span>
									<span class="detail-value"><?php echo htmlspecialchars(ucfirst($patient_info['gender'] ?? 'N/A')); ?></span>
								</div>
								<div class="detail-row">
									<span class="detail-label">DOB:</span>
									<span class="detail-value"><?php echo htmlspecialchars($patient_info['date_of_birth'] ?? 'N/A'); ?></span>
								</div>
								<div class="detail-row">
									<span class="detail-label">Blood Group:</span>
									<span class="detail-value text-danger fw-bold"><?php echo htmlspecialchars($patient_info['blood_group'] ?? 'N/A'); ?></span>
								</div>
								<div class="detail-row">
									<span class="detail-label">Phone:</span>
									<span class="detail-value"><?php echo htmlspecialchars($patient_info['phone'] ?? 'N/A'); ?></span>
								</div>
								<div class="detail-row">
									<span class="detail-label">Email:</span>
									<span class="detail-value"><?php echo htmlspecialchars($patient_info['email'] ?? 'N/A'); ?></span>
								</div>
								<div class="detail-row">
									<span class="detail-label">Address:</span>
									<span class="detail-value"><?php 
										$address = [];
										if (!empty($patient_info['address'])) $address[] = $patient_info['address'];
										if (!empty($patient_info['city'])) $address[] = $patient_info['city'];
										if (!empty($patient_info['state'])) $address[] = $patient_info['state'];
										if (!empty($patient_info['country'])) $address[] = $patient_info['country'];
										if (!empty($patient_info['pincode'])) $address[] = $patient_info['pincode'];
										echo htmlspecialchars(implode(', ', $address) ?: 'N/A');
									?></span>
								</div>
							</div>

							<!-- Vitals & Booking -->
							<div class="col-lg-8 col-md-6">
								<h6 class="detail-group-title">Current Vitals & Booking Info</h6>
								<div class="row g-2 mb-4">
									<div class="col-md-3 col-sm-4 col-6">
										<div class="vital-badge text-center">
											<span class="vital-label">Age</span>
											<span class="vital-value"><?php echo htmlspecialchars($appointment['age'] ?? 'N/A'); ?></span>
										</div>
									</div>
									<div class="col-md-3 col-sm-4 col-6">
										<div class="vital-badge text-center">
											<span class="vital-label">Weight</span>
											<span class="vital-value"><?php echo htmlspecialchars($appointment['weight'] ?? 'N/A'); ?> kg</span>
										</div>
									</div>
									<div class="col-md-3 col-sm-4 col-6">
										<div class="vital-badge text-center">
											<span class="vital-label">Temp</span>
											<span class="vital-value"><?php echo htmlspecialchars($appointment['body_temperature'] ?? 'N/A'); ?> °F</span>
										</div>
									</div>
									<div class="col-md-3 col-sm-4 col-6">
										<div class="vital-badge text-center">
											<span class="vital-label">BP</span>
											<span class="vital-value"><?php echo htmlspecialchars($appointment['blood_pressure'] ?? 'N/A'); ?></span>
										</div>
									</div>
									<div class="col-md-3 col-sm-4 col-6">
										<div class="vital-badge text-center">
											<span class="vital-label">Pulse</span>
											<span class="vital-value"><?php echo htmlspecialchars($appointment['pulse'] ?? 'N/A'); ?> bpm</span>
										</div>
									</div>
									<div class="col-md-3 col-sm-4 col-6">
										<div class="vital-badge text-center">
											<span class="vital-label">SpO2</span>
											<span class="vital-value"><?php echo htmlspecialchars($appointment['spo2'] ?? 'N/A'); ?> %</span>
										</div>
									</div>
									<div class="col-md-3 col-sm-4 col-6">
										<div class="vital-badge text-center">
											<span class="vital-label">RBS/FBS</span>
											<span class="vital-value"><?php echo htmlspecialchars($appointment['rbs_fbs'] ?? 'N/A'); ?></span>
										</div>
									</div>
								</div>

								<div class="mb-3">
									<span class="detail-label d-block mb-1">Chief Complaints / Symptoms:</span>
									<div class="symptoms-box">
										<?php echo nl2br(htmlspecialchars($appointment['notes'] ?? 'None provided')); ?>
									</div>
								</div>

								<?php if (!empty($appointment['referrer_tid'])): ?>
								<div class="detail-row">
									<span class="detail-label">Referred By:</span>
									<span class="detail-value fw-bold text-primary"><?php echo htmlspecialchars($appointment['referrer_tid']); ?></span>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<!-- Prescription Form Section -->
					<?php if ($_SESSION['user_type'] === 'doctor' && $appointment_id): ?>
					<div class="patient-details-card mt-4 mb-4">
						<h4><i class="isax isax-edit-2 me-2"></i>Generate Prescription</h4>
						<form id="prescription_form">
							<input type="hidden" name="appointment_id" value="<?php echo (int)$appointment_id; ?>">
							
							<div class="row">
								<div class="col-md-6">
									<div class="form-group mb-3">
										<label class="form-label">Chief Complaints</label>
										<textarea class="form-control" name="chief_complaints" rows="3" placeholder="Symptoms, duration..."></textarea>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group mb-3">
										<label class="form-label">On Examination</label>
										<textarea class="form-control" name="on_examination" rows="3" placeholder="Vitals, physical findings..."></textarea>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group mb-4">
										<label class="form-label">Diagnosis</label>
										<input type="text" class="form-control" name="diagnosis" placeholder="Primary diagnosis">
									</div>
								</div>
							</div>

							<hr>
							<h6 class="mb-3">Medications (Rx)</h6>
							<div id="medicine_list">
								<!-- Medicine Row -->
								<div class="medicine-row mt-2">
									<div class="row g-2">
										<div class="col-md-5">
											<input type="text" class="form-control" name="medicine_name[]" placeholder="Medicine name" required>
										</div>
										<div class="col-md-3">
											<input type="text" class="form-control" name="medicine_dose[]" placeholder="Dose (e.g. 1+0+1)">
										</div>
										<div class="col-md-3">
											<input type="text" class="form-control" name="medicine_duration[]" placeholder="Duration (e.g. 7 days)">
										</div>
										<div class="col-md-1">
											<button type="button" class="btn btn-link btn-remove-medicine" style="display:none;"><i class="fa-solid fa-trash"></i></button>
										</div>
									</div>
								</div>
							</div>
							<button type="button" class="btn btn-sm btn-outline-info mt-2" id="btn_add_medicine"><i class="fa-solid fa-plus me-1"></i>Add Medicine</button>

							<hr class="mt-4">
							<div class="form-group mb-3">
								<label class="form-label">Advice / Instructions</label>
								<textarea class="form-control" name="advice" rows="3" placeholder="Diet, rest, follow-up..."></textarea>
							</div>

							<div class="form-group mb-4">
								<label class="form-label">Prescription Footer (Optional)</label>
								<textarea class="form-control" name="prescription_footer" rows="2" placeholder="e.g. Free Medical Camp address..."></textarea>
							</div>
							
							<div class="text-end">
								<button type="submit" class="btn btn-primary btn-lg px-5" id="btn_submit_prescription">Generate & Save Prescription PDF</button>
							</div>
						</form>
					</div>
					<?php endif; ?>

				</div>
			</div>

		</div>

	</div>
	<!-- /Page Content -->

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
										<li><a href="about-us.php">About</a></li>
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
										<li><a href="contact-us.php">Contact</a></li>
										<li><a href="contact-us.php">Request A Quote</a></li>
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
									<button type="submit"
										class="btn btn-md btn-primary-gradient d-inline-flex align-items-center"><i
											class="isax isax-send-25 me-1"></i>Send</button>
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
						<p class="mb-0">Copyright © 2025 Doccure. All Rights Reserved</p>
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

	<!-- Agora Web SDK -->
	<script src="https://download.agora.io/sdk/release/AgoraRTC_N.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>

	<!-- Custom JS -->
	<script>
		$(document).ready(function () {
			const options = {
				appId: "d4ab628137c74b519e71dec351b83c34",
				channel: "<?php echo $channel_name; ?>",
				uid: <?php echo $uid; ?>,
				token: null // Will be generated via server-side logic if needed
			};

			const client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
			let localTracks = {
				videoTrack: null,
				audioTrack: null
			};
			let remoteUsers = {};

			const joinCall = async () => {
				// We call the server to get a valid token
				try {
					const response = await $.ajax({
						url: 'php/generate-agora-token.php',
						type: 'POST',
						data: { channel: options.channel, uid: options.uid },
						dataType: 'json'
					}).fail(function (jqXHR, textStatus, errorThrown) {
						console.error("AJAX Error Details:", {
							status: jqXHR.status,
							responseText: jqXHR.responseText,
							textStatus: textStatus,
							errorThrown: errorThrown
						});
					});

					if (response.success) {
						options.token = response.token;

						console.log("Token received:", options.token);

						// Subscribe to events
						client.on("user-published", async (user, mediaType) => {
							await client.subscribe(user, mediaType);
							if (mediaType === "video") {
								remoteUsers[user.uid] = user;
								$("#remote-video").html("");
								user.videoTrack.play("remote-video");
							}
							if (mediaType === "audio") {
								user.audioTrack.play();
							}
						});

						client.on("user-unpublished", user => {
							delete remoteUsers[user.uid];
							$("#remote-video").html("");
						});

						// Join and Publish local media
						await client.join(options.appId, options.channel, options.token, options.uid);

						localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack();
						localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();

						localTracks.videoTrack.play("local-video");
						await client.publish([localTracks.audioTrack, localTracks.videoTrack]);

						$("#join-btn-container").hide();
						$("#video-toggle, #audio-toggle, #leave-btn, #view-details-btn").show();
					} else {
						alert("Could not start call: " + response.message);
					}
				} catch (err) {
					console.error(err);
				}
			};

			const leaveCall = async () => {
				for (let trackName in localTracks) {
					let track = localTracks[trackName];
					if (track) {
						track.stop();
						track.close();
						localTracks[trackName] = null;
					}
				}
				await client.leave();
				$("#local-video").html("");
				$("#remote-video").html("");
				$("#join-btn-container").show();

				// Mark as completed on backend
				try {
					await $.post('php/complete-appointment.php', { appointment_id: "<?php echo (int) $appointment_id; ?>" });
				} catch (err) {
					console.error("Failed to mark appointment as completed:", err);
				}

				// Handle redirect after ending the call
				<?php if ($_SESSION['user_type'] === 'doctor'): ?>
					// Doctor stays on page to generate prescription if needed
				<?php else: ?>
					<?php
					$redirect_url = 'index.php';
					if (isset($_SESSION['user_type'])) {
						if ($_SESSION['user_type'] === 'patient') {
							$redirect_url = 'patient-dashboard.php';
						}
					}
					?>
					window.location.href = "<?php echo $redirect_url; ?>";
				<?php endif; ?>
			};

			$("#join-btn").click(function () {
				joinCall();
			});

			$("#leave-btn").click(function () {
				leaveCall();
			});

			let isVideoMuted = false;
			$("#video-toggle").click(function () {
				if (!isVideoMuted) {
					localTracks.videoTrack.setEnabled(false);
					isVideoMuted = true;
					$(this).find('i').removeClass('isax-video').addClass('isax-video-slash');
				} else {
					localTracks.videoTrack.setEnabled(true);
					isVideoMuted = false;
					$(this).find('i').removeClass('isax-video-slash').addClass('isax-video');
				}
			});

			let isAudioMuted = false;
			$("#audio-toggle").click(function () {
				if (!isAudioMuted) {
					localTracks.audioTrack.setEnabled(false);
					isAudioMuted = true;
					$(this).find('i').removeClass('isax-microphone-2').addClass('isax-microphone-slash');
				} else {
					localTracks.audioTrack.setEnabled(true);
					isAudioMuted = false;
					$(this).find('i').removeClass('isax-microphone-slash').addClass('isax-microphone-2');
				}
			});

			// --- Prescription Logic ---
			$('#btn_add_medicine').click(function() {
				const newRow = `
					<div class="medicine-row mt-2">
						<div class="row g-2">
							<div class="col-md-5">
								<input type="text" class="form-control" name="medicine_name[]" placeholder="Medicine name" required>
							</div>
							<div class="col-md-3">
								<input type="text" class="form-control" name="medicine_dose[]" placeholder="Dose (e.g. 1+0+1)">
							</div>
							<div class="col-md-3">
								<input type="text" class="form-control" name="medicine_duration[]" placeholder="Duration (e.g. 7 days)">
							</div>
							<div class="col-md-1">
								<button type="button" class="btn btn-link btn-remove-medicine"><i class="fa-solid fa-trash"></i></button>
							</div>
						</div>
					</div>
				`;
				$('#medicine_list').append(newRow);
			});

			$(document).on('click', '.btn-remove-medicine', function() {
				$(this).closest('.medicine-row').remove();
			});

			$('#prescription_form').submit(function(e) {
				e.preventDefault();
				
				const $submitBtn = $('#btn_submit_prescription');
				const originalText = $submitBtn.text();
				$submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Generating...');

				$.ajax({
					url: 'php/save-prescription-data.php',
					type: 'POST',
					data: $(this).serialize(),
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							// Trigger PDF generation
							window.open('php/generate-prescription.php?appointment_id=' + response.appointment_id, '_blank');
							
							// Optional: redirect back to dashboard after generation
							setTimeout(() => {
								window.location.href = 'doctor-dashboard.php';
							}, 2000);
						} else {
							alert('Error: ' + response.message);
							$submitBtn.prop('disabled', false).text(originalText);
						}
					},
					error: function() {
						alert('An error occurred while saving prescription data.');
						$submitBtn.prop('disabled', false).text(originalText);
					}
				});
			});

		});
	</script>
	<script src="assets/js/script.js"></script>

</body>

</html>