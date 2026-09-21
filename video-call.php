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
} else if ($_SESSION['user_type'] === 'special_tid' && isset($_SESSION['special_tid_id'])) {
	$uid = 3500 + (int) $_SESSION['special_tid_id'];
	$user_name = $_SESSION['special_tid_name'] ?? 'Special TID User';
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

			if ($_SESSION['user_type'] === 'doctor' || $_SESSION['user_type'] === 'healthcare' || $_SESSION['user_type'] === 'special_tid') {
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
		.sticky-floating-notes-btn {
			position: fixed !important;
			bottom: 30px !important;
			right: 30px !important;
			z-index: 999999 !important;
			height: 50px !important;
			padding: 0 22px !important;
			border-radius: 25px !important;
			background: linear-gradient(135deg, #fef08a 0%, #f59e0b 100%) !important;
			border: 2px solid #d97706 !important;
			color: #451a03 !important;
			font-weight: 800 !important;
			font-size: 15px !important;
			box-shadow: 0 8px 25px rgba(217, 119, 6, 0.5) !important;
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			gap: 8px !important;
			cursor: pointer !important;
			transition: transform 0.2s ease, box-shadow 0.2s ease !important;
		}
		.sticky-floating-notes-btn:hover {
			transform: translateY(-3px) scale(1.06) !important;
			box-shadow: 0 12px 30px rgba(217, 119, 6, 0.7) !important;
			color: #000 !important;
		}
		.sticky-floating-notes-btn .note-badge-dot {
			position: absolute !important;
			top: -3px !important;
			right: -3px !important;
			width: 14px !important;
			height: 14px !important;
			background-color: #ef4444 !important;
			border: 2px solid #ffffff !important;
			border-radius: 50% !important;
		}
		/* Facebook Web Chat-style Right Side Portrait Sticky Note Modal */
		#sticky_note_modal {
			z-index: 1070 !important;
			overflow: hidden !important;
			pointer-events: none !important;
		}
		#sticky_note_modal .modal-dialog {
			position: fixed !important;
			bottom: 20px !important;
			right: 20px !important;
			margin: 0 !important;
			width: 340px !important;
			max-width: calc(100vw - 25px) !important;
			height: 280px !important;
			max-height: calc(100vh - 40px) !important;
			pointer-events: auto !important;
			transform: none !important;
		}
		#sticky_note_modal .modal-content {
			height: 100% !important;
			display: flex !important;
			flex-direction: column !important;
			border-radius: 14px !important;
			border: 2px solid #f59e0b !important;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
			overflow: hidden !important;
			background: #ffffff !important;
		}
		#sticky_note_modal .modal-header {
			background: linear-gradient(135deg, #fef08a 0%, #f59e0b 100%) !important;
			color: #451a03 !important;
			padding: 10px 14px !important;
			border-bottom: 1px solid #d97706 !important;
		}
		#sticky_note_modal .modal-header .modal-title {
			font-size: 14px !important;
			font-weight: 700 !important;
			color: #451a03 !important;
		}
		#sticky_note_modal .modal-body {
			flex: 1 1 auto !important;
			padding: 10px 12px !important;
			display: flex !important;
			flex-direction: column !important;
			overflow-y: auto !important;
			background-color: #fffdf0 !important;
		}
		#sticky_note_modal .sticky-note-input-wrapper {
			display: flex !important;
			flex-direction: column !important;
			flex: 1 1 auto !important;
			height: 100% !important;
			margin-bottom: 0 !important;
		}
		#sticky_note_modal #sticky_modal_text {
			flex: 1 1 auto !important;
			height: 100% !important;
			min-height: 120px !important;
			background-color: #fefce8 !important;
			border: 1.5px solid #fde047 !important;
			border-radius: 8px !important;
			padding: 10px !important;
			font-size: 13.5px !important;
			line-height: 1.4 !important;
			color: #451a03 !important;
			resize: none !important;
			margin-bottom: 0 !important;
		}
		#sticky_note_modal #sticky_modal_text:focus {
			outline: none !important;
			box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25) !important;
			border-color: #f59e0b !important;
		}
		#sticky_note_modal .modal-footer {
			background: #ffffff !important;
			padding: 8px 12px !important;
			border-top: 1px solid #e5e7eb !important;
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
					<?php if (($_SESSION['user_type'] === 'doctor' || $_SESSION['user_type'] === 'healthcare' || $_SESSION['user_type'] === 'special_tid') && isset($patient_info)): ?>
					<?php $is_doctor = ($_SESSION['user_type'] === 'doctor'); ?>
					<div class="patient-details-card">
						<h4><i class="isax isax-user me-2"></i>Patient Details</h4>
						
						<form id="patient_details_form">
							<input type="hidden" name="appointment_id" value="<?php echo (int)$appointment_id; ?>">
							<input type="hidden" name="patient_id" value="<?php echo (int)($patient_info['id'] ?? 0); ?>">
							
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
										<?php if ($is_doctor): ?>
											<select class="form-select form-select-sm" name="gender" style="width: auto; display: inline-block;">
												<option value="">Select</option>
												<option value="Male" <?php echo (($patient_info['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
												<option value="Female" <?php echo (($patient_info['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
												<option value="Other" <?php echo (($patient_info['gender'] ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
											</select>
										<?php else: ?>
											<span class="detail-value"><?php echo htmlspecialchars(ucfirst($patient_info['gender'] ?? 'N/A')); ?></span>
										<?php endif; ?>
									</div>

									<div class="detail-row">
										<span class="detail-label">Blood Group:</span>
										<?php if ($is_doctor): ?>
											<select class="form-select form-select-sm" name="blood_group" style="width: auto; display: inline-block;">
												<option value="">Select</option>
												<?php 
												$bg_options = ['A+ve', 'A-ve', 'B+ve', 'B-ve', 'AB+ve', 'AB-ve', 'O+ve', 'O-ve'];
												foreach($bg_options as $bg) {
													$selected = (($patient_info['blood_group'] ?? '') == $bg) ? 'selected' : '';
													echo "<option value=\"$bg\" $selected>$bg</option>";
												}
												?>
											</select>
										<?php else: ?>
											<span class="detail-value text-danger fw-bold"><?php echo htmlspecialchars($patient_info['blood_group'] ?? 'N/A'); ?></span>
										<?php endif; ?>
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
												<?php if ($is_doctor): ?>
													<input type="text" class="form-control form-control-sm text-center" name="age" value="<?php echo htmlspecialchars($appointment['age'] ?? ''); ?>">
												<?php else: ?>
													<span class="vital-value"><?php echo htmlspecialchars($appointment['age'] ?? 'N/A'); ?></span>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-md-3 col-sm-4 col-6">
											<div class="vital-badge text-center">
												<span class="vital-label">Weight (kg)</span>
												<?php if ($is_doctor): ?>
													<input type="text" class="form-control form-control-sm text-center" name="weight" value="<?php echo htmlspecialchars($appointment['weight'] ?? ''); ?>">
												<?php else: ?>
													<span class="vital-value"><?php echo htmlspecialchars($appointment['weight'] ?? 'N/A'); ?> kg</span>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-md-3 col-sm-4 col-6">
											<div class="vital-badge text-center">
												<span class="vital-label">Temp (°F)</span>
												<?php if ($is_doctor): ?>
													<input type="text" class="form-control form-control-sm text-center" name="body_temperature" value="<?php echo htmlspecialchars($appointment['body_temperature'] ?? ''); ?>">
												<?php else: ?>
													<span class="vital-value"><?php echo htmlspecialchars($appointment['body_temperature'] ?? 'N/A'); ?> °F</span>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-md-3 col-sm-4 col-6">
											<div class="vital-badge text-center">
												<span class="vital-label">BP</span>
												<?php if ($is_doctor): ?>
													<input type="text" class="form-control form-control-sm text-center" name="blood_pressure" value="<?php echo htmlspecialchars($appointment['blood_pressure'] ?? ''); ?>">
												<?php else: ?>
													<span class="vital-value"><?php echo htmlspecialchars($appointment['blood_pressure'] ?? 'N/A'); ?></span>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-md-3 col-sm-4 col-6">
											<div class="vital-badge text-center">
												<span class="vital-label">Pulse (bpm)</span>
												<?php if ($is_doctor): ?>
													<input type="text" class="form-control form-control-sm text-center" name="pulse" value="<?php echo htmlspecialchars($appointment['pulse'] ?? ''); ?>">
												<?php else: ?>
													<span class="vital-value"><?php echo htmlspecialchars($appointment['pulse'] ?? 'N/A'); ?> bpm</span>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-md-3 col-sm-4 col-6">
											<div class="vital-badge text-center">
												<span class="vital-label">SpO2 (%)</span>
												<?php if ($is_doctor): ?>
													<input type="text" class="form-control form-control-sm text-center" name="spo2" value="<?php echo htmlspecialchars($appointment['spo2'] ?? ''); ?>">
												<?php else: ?>
													<span class="vital-value"><?php echo htmlspecialchars($appointment['spo2'] ?? 'N/A'); ?> %</span>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-md-3 col-sm-4 col-6">
											<div class="vital-badge text-center">
												<span class="vital-label">RBS/FBS</span>
												<?php if ($is_doctor): ?>
													<input type="text" class="form-control form-control-sm text-center" name="rbs_fbs" value="<?php echo htmlspecialchars($appointment['rbs_fbs'] ?? ''); ?>">
												<?php else: ?>
													<span class="vital-value"><?php echo htmlspecialchars($appointment['rbs_fbs'] ?? 'N/A'); ?></span>
												<?php endif; ?>
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

									<?php if ($is_doctor): ?>
									<div class="text-end mt-3">
										<button type="submit" class="btn btn-info btn-sm" id="btn_update_vitals">
											<i class="isax isax-save-2 me-1"></i> Update Patient Details
										</button>
									</div>
									<div id="update_message_area" class="mt-2"></div>
									<?php endif; ?>
								</div>
							</div>
						</form>
					</div>
					<?php endif; ?>


					<!-- Prescription Form Section -->
					<?php if ($_SESSION['user_type'] === 'doctor' && $appointment_id): ?>
					<div class="patient-details-card mt-4 mb-4">
						<div class="d-flex align-items-center justify-content-between mb-3">
							<h4 class="mb-0"><i class="isax isax-edit-2 me-2"></i>Generate Prescription</h4>
						</div>

						<form id="prescription_form">
							<input type="hidden" name="appointment_id" value="<?php echo (int)$appointment_id; ?>">

							<div class="row">
								<div class="col-md-6">
									<div class="form-group mb-3">
										<label class="form-label">Chief Complaints</label>
										<textarea class="form-control" name="chief_complaints" rows="3" placeholder="Symptoms, duration..."><?php echo htmlspecialchars($appointment['chief_complaints'] ?? $appointment['notes'] ?? ''); ?></textarea>
									</div>
									<div id="prescription_message_area" class="mt-2"></div>
								</div>

								<div class="col-md-6">
									<div class="form-group mb-3">
										<label class="form-label">On Examination</label>
										<textarea class="form-control" name="on_examination" rows="3" placeholder="Vitals, physical findings..."><?php echo htmlspecialchars($appointment['on_examination'] ?? ''); ?></textarea>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group mb-4">
										<label class="form-label">Diagnosis</label>
										<input type="text" class="form-control" name="diagnosis" placeholder="Primary diagnosis" value="<?php echo htmlspecialchars($appointment['diagnosis'] ?? ''); ?>">
									</div>
								</div>
							</div>

							<hr>
							<h6 class="mb-3">Medications (Rx)</h6>
							<div id="medicine_list">
								<?php 
								$medications = [];
								if (!empty($appointment['medications'])) {
									$medications = json_decode($appointment['medications'], true) ?: [];
								}
								if (empty($medications)) {
									$medications = [['name' => '', 'dose' => '', 'duration' => '']];
								}
								foreach ($medications as $index => $med): 
								?>
								<div class="medicine-row mt-2">
									<div class="row g-2">
										<div class="col-md-5">
											<input type="text" class="form-control" name="medicine_name[]" placeholder="Medicine name" value="<?php echo htmlspecialchars($med['name'] ?? ''); ?>" required>
										</div>
										<div class="col-md-3">
											<input type="text" class="form-control" name="medicine_dose[]" placeholder="Dose (e.g. 1+0+1)" value="<?php echo htmlspecialchars($med['dose'] ?? ''); ?>">
										</div>
										<div class="col-md-3">
											<input type="text" class="form-control" name="medicine_duration[]" placeholder="Duration (e.g. 7 days)" value="<?php echo htmlspecialchars($med['duration'] ?? ''); ?>">
										</div>
										<div class="col-md-1">
											<button type="button" class="btn btn-link btn-remove-medicine" style="<?php echo count($medications) === 1 ? 'display:none;' : ''; ?>"><i class="fa-solid fa-trash"></i></button>
										</div>
									</div>
								</div>
								<?php endforeach; ?>
							</div>
							<button type="button" class="btn btn-sm btn-outline-info mt-2" id="btn_add_medicine"><i class="fa-solid fa-plus me-1"></i>Add Medicine</button>

							<hr class="mt-4">
							<div class="form-group mb-3">
								<label class="form-label">Advice / Instructions</label>
								<textarea class="form-control" name="advice" rows="3" placeholder="Diet, rest, follow-up..."><?php echo htmlspecialchars($appointment['advice'] ?? ''); ?></textarea>
							</div>

							<div class="form-group mb-3">
								<label class="form-label d-block">Follow-up?</label>
								<?php 
								$saved_follow_up = $appointment['follow_up_type'] ?? ''; 
								$saved_follow_up_date = $appointment['follow_up_date'] ?? ''; 
								$has_follow_up = !empty($saved_follow_up) ? 'yes' : 'no';
								?>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="has_follow_up" id="follow_up_yes" value="yes" <?php echo $has_follow_up === 'yes' ? 'checked' : ''; ?>>
									<label class="form-check-label" for="follow_up_yes">Yes</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="has_follow_up" id="follow_up_no" value="no" <?php echo $has_follow_up === 'no' ? 'checked' : ''; ?>>
									<label class="form-check-label" for="follow_up_no">No</label>
								</div>
							</div>

							<div id="follow_up_details_container" style="<?php echo $has_follow_up === 'yes' ? '' : 'display: none;'; ?>">
								<div class="form-group mb-3">
									<label class="form-label d-block">Follow-up Type</label>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="follow_up_type" id="follow_up_with_report" value="with_report" <?php echo $saved_follow_up === 'with_report' ? 'checked' : ''; ?>>
										<label class="form-check-label" for="follow_up_with_report">Follow-up with Report</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="follow_up_type" id="follow_up_without_report" value="without_report" <?php echo $saved_follow_up === 'without_report' ? 'checked' : ''; ?>>
										<label class="form-check-label" for="follow_up_without_report">Follow-up without Report</label>
									</div>
								</div>
								
								<div class="form-group mb-3">
									<label class="form-label">Follow-up After (Days)</label>
									<input type="number" class="form-control" name="follow_up_date" id="follow_up_date" placeholder="e.g. 20" value="<?php echo htmlspecialchars($saved_follow_up_date); ?>">
								</div>
							</div>

							<div class="form-group mb-3">
								<label class="form-label">Note / Reference</label>
								<textarea class="form-control" name="note_reference" rows="2" placeholder="Additional note or reference..."><?php echo htmlspecialchars($appointment['note_reference'] ?? ''); ?></textarea>
							</div>

							<div class="form-group mb-4">
								<label class="form-label">Prescription Footer (Optional)</label>
								<textarea class="form-control" name="prescription_footer" rows="2" placeholder="e.g. Free Medical Camp address..."><?php echo htmlspecialchars($appointment['prescription_footer'] ?? ''); ?></textarea>
							</div>

							<div class="text-end">
								<button type="submit" class="btn btn-primary btn-lg px-5" id="btn_submit_prescription">Generate & Save Prescription PDF</button>
							</div>
						</form>
					</div>
					<?php endif; ?>

					<!-- Sticky Floating Treatment Template + My Notes Widget -->
					<?php if ($_SESSION['user_type'] === 'doctor' && $appointment_id): ?>
					<div id="trx_floating_sticky_widget" style="position: fixed; right: 24px; bottom: 24px; z-index: 1050; width: 330px; max-width: calc(100vw - 32px); display: flex; flex-direction: column;">
						<!-- Treatment Template Panel (Opens UP above Treatment Template button) -->
						<div id="tt_panel" class="trx-tool-panel" style="display: none; border: 2px solid #0d6efd; border-radius: 12px; background: #ffffff; padding: 12px; margin-bottom: 8px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);">
							<div class="input-group input-group-sm mb-2">
								<span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #0d6efd; border-radius: 6px 0 0 6px;"><i class="fa-solid fa-magnifying-glass"></i></span>
								<input type="text" id="tt_search_input" class="form-control border-start-0" placeholder="Search treatment template" style="border-color: #0d6efd; border-radius: 0 6px 6px 0; font-size: 13px;">
							</div>
							<div id="tt_toast_msg"></div>
							<div id="tt_list_container" class="pe-1" style="max-height: 200px; overflow-y: auto;">
								<!-- Template Items populated by JS -->
							</div>
							<button type="button" id="tt_panel_toggle_btn" class="btn btn-primary w-100 btn-sm text-center fw-semibold mt-2" style="background-color: #0d6efd; border: none; border-radius: 6px; padding: 8px 12px;">
								Treatment Template
							</button>
						</div>

						<!-- Standalone Buttons Container (Middle Anchor) -->
						<div id="standalone_buttons_container" class="d-flex flex-column gap-2">
							<button type="button" id="btn_standalone_tt" class="btn btn-primary w-100 py-2.5 fw-bold d-flex align-items-center justify-content-between px-3" style="background-color: #0d6efd; border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);">
								<span>Treatment Template</span>
								<i class="fa-solid fa-chevron-up fs-6"></i>
							</button>
							<button type="button" id="btn_standalone_notes" class="btn btn-primary w-100 py-2.5 fw-bold d-flex align-items-center justify-content-between px-3" style="background-color: #0d6efd; border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);">
								<span>My Notes</span>
								<i class="fa-solid fa-chevron-down fs-6"></i>
							</button>
						</div>

						<!-- My Notes Panel (Opens DOWN below My Notes button) -->
						<div id="notes_panel" class="trx-tool-panel" style="display: none; border: 2px solid #0d6efd; border-radius: 12px; background: #ffffff; padding: 12px; margin-top: 8px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);">
							<div class="mb-2">
								<textarea id="notes_textarea" class="form-control" rows="5" placeholder="Write doctor private notes here..." style="border: 2px solid #0d6efd; border-radius: 8px; background-color: #ffffff; padding: 12px; font-size: 14px; width: 100%; resize: vertical; min-height: 140px;"><?php echo htmlspecialchars($appointment['sticky_note'] ?? ''); ?></textarea>
							</div>
							<div id="notes_alert_msg"></div>
							<!-- Visually attached bottom buttons -->
							<div class="d-flex align-items-center gap-2">
								<button type="button" id="notes_panel_toggle_btn" class="btn btn-primary btn-sm flex-fill fw-semibold" style="background-color: #0d6efd; border: none; border-radius: 6px; padding: 7px 10px;">
									My Notes
								</button>
								<button type="button" id="btn_save_notes" class="btn btn-success btn-sm flex-fill fw-semibold" style="border-radius: 6px; padding: 7px 10px;">
									Save
								</button>
								<button type="button" id="btn_clear_notes" class="btn btn-outline-danger btn-sm flex-fill fw-semibold" style="border-radius: 6px; padding: 7px 10px;">
									Clear
								</button>
							</div>
						</div>
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
			if ($('#vc_floating_sticky_btn').length > 0) {
				$('body').append($('#vc_floating_sticky_btn'));
			}

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
					}
				} catch (err) {
					console.error("Error getting token:", err);
				}

				try {
					await client.join(options.appId, options.channel, options.token, options.uid);
					localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack();
					localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();

					localTracks.videoTrack.play("local-video");
					await client.publish(Object.values(localTracks));

					$("#join-btn-container").hide();
					$("#video-toggle, #audio-toggle, #leave-btn, #view-details-btn").show();

					// If the user is a doctor or health provider, notify the database that the call has started
					<?php if (in_array($_SESSION['user_type'], ['doctor', 'healthcare', 'special_tid'])): ?>
					try {
						await $.post('php/handle-call-status.php', {
							appointment_id: "<?php echo (int) $appointment_id; ?>",
							action: 'start_call'
						});
					} catch (err) {
						console.error("Failed to notify call start to server:", err);
					}
					<?php endif; ?>
					<?php if ($_SESSION['user_type'] === 'patient' && !empty($appointment['is_emergency']) && (int)$appointment['is_emergency'] === 1): ?>
					// Patient joining an emergency call — notify server so emergency doctor sees active case
					try {
						await $.post('php/handle-call-status.php', {
							appointment_id: "<?php echo (int) $appointment_id; ?>",
							action: 'start_call'
						});
					} catch (err) {
						console.error("Failed to notify emergency call start to server:", err);
					}
					<?php endif; ?>
				} catch (err) {
					console.error("Join call failed:", err);
					alert("Failed to join call. Please check your camera/microphone permissions.");
				}
			};

			const leaveCall = async () => {
				// Clear the calling status on the backend
				<?php if (in_array($_SESSION['user_type'], ['doctor', 'healthcare', 'special_tid'])): ?>
				try {
					await $.post('php/handle-call-status.php', {
						appointment_id: "<?php echo (int) $appointment_id; ?>",
						action: 'end_call'
					});
				} catch (err) {
					console.error("Failed to notify call end to server:", err);
				}
				<?php endif; ?>

				for (let trackName in localTracks) {
					let track = localTracks[trackName];
					if (track) {
						track.stop();
						track.close();
						localTracks[trackName] = null;
					}
				}
				await client.leave();
				$("#join-btn-container").show();
				$("#video-toggle, #audio-toggle, #leave-btn, #view-details-btn").hide();
				$("#remote-video").empty();
				$("#local-video").empty();

				// Optional: Mark as completed on backend for non-doctors
				<?php if ($_SESSION['user_type'] !== 'doctor'): ?>
					try {
						await $.post('php/complete-appointment.php', { appointment_id: "<?php echo (int) $appointment_id; ?>" });
						window.location.href = "<?php echo ($_SESSION['user_type'] === 'patient') ? 'patient-dashboard.php' : 'index.php'; ?>";
					} catch (err) {
						console.error("Failed to mark appointment as completed:", err);
						window.location.href = "index.php";
					}
				<?php endif; ?>
			};

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

			client.on("user-unpublished", (user) => {
				delete remoteUsers[user.uid];
				$("#remote-video").empty();
			});

			$("#join-btn").click(joinCall);
			$("#leave-btn").click(leaveCall);

            <?php if (in_array($_SESSION['user_type'], ['patient', 'healthcare', 'special_tid']) && !empty($appointment['call_status']) && $appointment['call_status'] === 'in_progress'): ?>
            // Automatically join the video when a receiver answers an incoming call.
            setTimeout(function() {
                if ($('#join-btn').is(':visible')) {
                    joinCall();
                }
            }, 500);
            <?php endif; ?>

            let isVideoMuted = false;
            $("#video-toggle").click(function () {
                if (!isVideoMuted) {
                    localTracks.videoTrack.setEnabled(false);
                    isVideoMuted = true;
                    $(this).find('i').removeClass('isax-video').addClass('isax-video-slash');
                    $(this).attr('title', 'Enable Video');
                } else {
                    localTracks.videoTrack.setEnabled(true);
                    isVideoMuted = false;
                    $(this).find('i').removeClass('isax-video-slash').addClass('isax-video');
                    $(this).attr('title', 'Disable Video');
                }
            });

            let isAudioMuted = false;
			$("#audio-toggle").click(function () {
				if (!isAudioMuted) {
					localTracks.audioTrack.setEnabled(false);
					isAudioMuted = true;
					$(this).find('i').removeClass('isax-microphone-2').addClass('isax-microphone-slash');
					$(this).attr('title', 'Unmute');
				} else {
					localTracks.audioTrack.setEnabled(true);
					isAudioMuted = false;
					$(this).find('i').removeClass('isax-microphone-slash').addClass('isax-microphone-2');
					$(this).attr('title', 'Mute');
				}
			});

			// Handle Patient Details Update
			$('#patient_details_form').on('submit', function (e) {
				e.preventDefault();
				const btn = $('#btn_update_vitals');
				const originalText = btn.html();
				btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Updating...');

				$.ajax({
					url: 'php/update-patient-vitals.php',
					type: 'POST',
					data: $(this).serialize(),
					dataType: 'json',
					success: function (res) {
						if (res.success) {
							$('#update_message_area').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' + res.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
							// Auto-hide message after 5 seconds
							setTimeout(() => { $('#update_message_area').empty(); }, 5000);
						} else {
							$('#update_message_area').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + res.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
						}
						btn.prop('disabled', false).html(originalText);
					},
					error: function () {
						$('#update_message_area').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">An error occurred while updating patient details.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
						btn.prop('disabled', false).html(originalText);
					}
				});
			});

			// Handle Prescription Submission
			function savePrescriptionAndOpen(previewOnly) {
				const btn = $('#btn_submit_prescription');
				const previewBtn = $('#btn_preview_prescription');
				const originalText = btn.html();
				const originalPreviewText = previewBtn.text();

				btn.prop('disabled', true);
				previewBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');

				$.ajax({
					url: 'php/save-prescription-data.php',
					type: 'POST',
					data: $('#prescription_form').serialize(),
					dataType: 'json',
					success: function (res) {
						if (res.success) {
							const url = previewOnly
								? 'php/generate-prescription.php?appointment_id=' + res.appointment_id + '&preview=html'
								: 'php/generate-prescription.php?appointment_id=' + res.appointment_id;

							window.open(url, '_blank');

							if (!previewOnly) {
								$('#prescription_message_area').html('<div class="alert alert-success alert-dismissible fade show" role="alert">Prescription generated! Redirecting to dashboard...<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
								setTimeout(() => {
									window.location.href = 'doctor-dashboard.php';
								}, 3000);
							}
						} else {
							$('#prescription_message_area').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + res.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
						}
					},
					error: function () {
						$('#prescription_message_area').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">An error occurred while saving prescription data.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
					},
					complete: function () {
						btn.prop('disabled', false).html(originalText);
						previewBtn.prop('disabled', false).text(originalPreviewText);
					}
				});
			}

			$('#prescription_form').on('submit', function (e) {
				e.preventDefault();
				savePrescriptionAndOpen(false);
			});

			$('#btn_preview_prescription').on('click', function () {
				savePrescriptionAndOpen(true);
			});

			// Add Medicine Row
			$('#btn_add_medicine').click(function () {
				const row = `
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
                    </div>`;
				$('#medicine_list').append(row);
			});

			// Remove Medicine Row
			$(document).on('click', '.btn-remove-medicine', function () {
				$(this).closest('.medicine-row').remove();
			});

			// Toggle follow-up details visibility
			$('input[name="has_follow_up"]').change(function() {
				if ($(this).val() === 'yes') {
					$('#follow_up_details_container').slideDown();
				} else {
					$('#follow_up_details_container').slideUp();
				}
			});

			// --- Right-Side Treatment Template + My Notes Logic ---
			function escapeHtml(text) {
				if (!text) return '';
				return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
			}

			const treatmentTemplates = [
				{
					name: "Fever",
					complaints: "High grade fever, body ache, chills (3 days)",
					diagnosis: "Acute Febrile Illness / Viral Fever",
					meds: [
						{ name: "Tab. Paracetamol 500mg", dose: "1+1+1 (After food)", duration: "5 days" },
						{ name: "Tab. Omeprazole 20mg", dose: "1+0+1 (Before food)", duration: "5 days" }
					],
					advice: "Rest, drink plenty of fluids (water, ORS, soups). Sponge with lukewarm water if temp > 101°F."
				},
				{
					name: "Cold",
					complaints: "Runny nose, sneezing, mild sore throat (2 days)",
					diagnosis: "Upper Respiratory Tract Infection (URTI) / Common Cold",
					meds: [
						{ name: "Tab. Fexofenadine 120mg", dose: "0+0+1 (At night)", duration: "5 days" },
						{ name: "Tab. Paracetamol 500mg", dose: "1+0+1 (As needed)", duration: "3 days" },
						{ name: "Nasal Spray Normal Saline", dose: "2 puffs each nostril", duration: "5 days" }
					],
					advice: "Steam inhalation twice daily. Avoid cold drinks and ice water."
				},
				{
					name: "Cough",
					complaints: "Dry cough, throat irritation, chest congestion (4 days)",
					diagnosis: "Acute Bronchitis / Cough",
					meds: [
						{ name: "Syr. Levosalbutamol + Ambroxol", dose: "2 tsp (10ml) 1+1+1", duration: "5 days" },
						{ name: "Tab. Montelukast 10mg", dose: "0+0+1 (At night)", duration: "7 days" }
					],
					advice: "Gargle with warm salt water 3 times a day. Avoid dust and smoke exposure."
				},
				{
					name: "Gastritis / Acidity",
					complaints: "Upper abdominal burning, bloating, nausea (1 week)",
					diagnosis: "Acute Gastritis / GERD",
					meds: [
						{ name: "Cap. Esomeprazole 40mg", dose: "1+0+1 (30 min before food)", duration: "14 days" },
						{ name: "Syr. Antacid Gel", dose: "2 tsp (10ml) after meals", duration: "7 days" }
					],
					advice: "Avoid spicy, oily, and fried food. Eat small frequent meals."
				},
				{
					name: "Hypertension",
					complaints: "Occasional headache, dizziness, mild fatigue",
					diagnosis: "Essential Hypertension",
					meds: [
						{ name: "Tab. Amlodipine 5mg", dose: "1+0+0 (Morning)", duration: "30 days" }
					],
					advice: "Low salt diet. Regular blood pressure monitoring. Walk 30 mins daily."
				},
				{
					name: "Diabetes Type 2",
					complaints: "Increased thirst, frequent urination, fatigue",
					diagnosis: "Type 2 Diabetes Mellitus",
					meds: [
						{ name: "Tab. Metformin 500mg", dose: "1+0+1 (With food)", duration: "30 days" }
					],
					advice: "Strict diabetic diet. Avoid sugar and refined carbs. Regular physical activity."
				},
				{
					name: "Headache / Migraine",
					complaints: "Throbbing headache on one side, light sensitivity (1 day)",
					diagnosis: "Migraine / Tension Headache",
					meds: [
						{ name: "Tab. Naproxen 500mg + Domperidone 10mg", dose: "1+0+0 (SOS after food)", duration: "3 days" }
					],
					advice: "Rest in a quiet, dark room. Maintain regular sleep pattern."
				},
				{
					name: "Diarrhea",
					complaints: "Loose watery stools 4-5 times, abdominal cramps (1 day)",
					diagnosis: "Acute Gastroenteritis",
					meds: [
						{ name: "ORS Sachet", dose: "1 sachet in 1L water, drink frequently", duration: "3 days" },
						{ name: "Tab. Ciprofloxacin 500mg", dose: "1+0+1 (After food)", duration: "5 days" }
					],
					advice: "Drink plenty of ORS and coconut water. Eat light food like rice porridge and bananas."
				},
				{
					name: "Skin Allergy",
					complaints: "Itchy red spots on skin, allergic reaction (2 days)",
					diagnosis: "Allergic Dermatitis / Urticaria",
					meds: [
						{ name: "Tab. Cetirizine 10mg", dose: "0+0+1 (At night)", duration: "7 days" }
					],
					advice: "Avoid harsh soaps. Do not scratch the affected area."
				}
			];

			let isTtOpen = false;
			let isNotesOpen = false;

			function updateRightPanelStates() {
				if (isTtOpen && isNotesOpen) {
					// State 4: Both open
					$('#tt_panel').slideDown(200);
					$('#notes_panel').slideDown(200);
					$('#btn_standalone_tt').hide();
					$('#btn_standalone_notes').hide();
				} else if (isTtOpen && !isNotesOpen) {
					// State 3: Only Treatment Template open
					$('#tt_panel').slideDown(200);
					$('#notes_panel').slideUp(200);
					$('#btn_standalone_tt').hide();
					$('#btn_standalone_notes').show();
				} else if (!isTtOpen && isNotesOpen) {
					// State 2: Only My Notes open
					$('#tt_panel').slideUp(200);
					$('#notes_panel').slideDown(200);
					$('#btn_standalone_tt').show();
					$('#btn_standalone_notes').hide();
				} else {
					// State 1: Both closed
					$('#tt_panel').slideUp(200);
					$('#notes_panel').slideUp(200);
					$('#btn_standalone_tt').show();
					$('#btn_standalone_notes').show();
				}
			}

			// Render Treatment Template List
			function renderTemplatesList(filterText = '') {
				let html = '';
				const query = filterText.toLowerCase().trim();
				let count = 0;
				treatmentTemplates.forEach(function(tpl, idx) {
					if (!query || tpl.name.toLowerCase().includes(query)) {
						count++;
						html += `
							<div class="tt-item p-2 mb-1 rounded bg-light border text-dark fw-medium" data-idx="${idx}" style="cursor: pointer; font-size: 13.5px; transition: background 0.15s;">
								<i class="fa-solid fa-file-medical text-primary me-2"></i>${escapeHtml(tpl.name)}
							</div>
						`;
					}
				});
				if (count === 0) {
					html = `<div class="text-muted p-2 text-center" style="font-size: 12.5px;">No templates found</div>`;
				}
				$('#tt_list_container').html(html);
			}

			renderTemplatesList();

			// Real-time Template Search Filter
			$('#tt_search_input').on('keyup input', function() {
				renderTemplatesList($(this).val());
			});

			// Template Hover & Click
			$(document).on('mouseenter', '.tt-item', function() {
				$(this).css({'background-color': '#e7f1ff', 'color': '#0d6efd', 'border-color': '#b6d4fe'});
			}).on('mouseleave', '.tt-item', function() {
				$(this).css({'background-color': '#f8f9fa', 'color': '#212529', 'border-color': '#dee2e6'});
			});

			// Select / Apply Template to Prescription Form
			$(document).on('click', '.tt-item', function() {
				const idx = $(this).data('idx');
				const tpl = treatmentTemplates[idx];
				if (!tpl) return;

				$('textarea[name="chief_complaints"]').val(tpl.complaints);
				$('input[name="diagnosis"]').val(tpl.diagnosis);
				$('textarea[name="advice"]').val(tpl.advice);

				if (tpl.meds && tpl.meds.length > 0) {
					let medsHtml = '';
					tpl.meds.forEach(function(m) {
						medsHtml += `
							<div class="medicine-row mt-2">
								<div class="row g-2">
									<div class="col-md-5">
										<input type="text" class="form-control" name="medicine_name[]" placeholder="Medicine name" value="${escapeHtml(m.name)}" required>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" name="medicine_dose[]" placeholder="Dose (e.g. 1+0+1)" value="${escapeHtml(m.dose)}">
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" name="medicine_duration[]" placeholder="Duration (e.g. 7 days)" value="${escapeHtml(m.duration)}">
									</div>
									<div class="col-md-1">
										<button type="button" class="btn btn-link btn-remove-medicine" style="${tpl.meds.length === 1 ? 'display:none;' : ''}"><i class="fa-solid fa-trash"></i></button>
									</div>
								</div>
							</div>
						`;
					});
					$('#medicine_list').html(medsHtml);
				}

				$('#tt_toast_msg').html(`<div class="alert alert-success py-1 px-2 mb-2" style="font-size:12px;"><i class="fa-solid fa-circle-check me-1"></i>Loaded "${escapeHtml(tpl.name)}"</div>`);
				setTimeout(() => { $('#tt_toast_msg').empty(); }, 3000);
			});

			// Standalone & Panel Toggle Event Handlers
			$('#btn_standalone_tt, #tt_panel_toggle_btn').click(function() {
				isTtOpen = !isTtOpen;
				updateRightPanelStates();
			});

			$('#btn_standalone_notes, #notes_panel_toggle_btn').click(function() {
				isNotesOpen = !isNotesOpen;
				updateRightPanelStates();
			});

			// Save Doctor Note AJAX
			$('#btn_save_notes').click(function() {
				const aptId = $('input[name="appointment_id"]').val();
				const noteText = $('#notes_textarea').val();
				const $btn = $(this);

				if (!aptId || aptId <= 0) return;

				$btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

				$.ajax({
					url: 'php/manage-sticky-note.php',
					type: 'POST',
					data: { action: 'save', appointment_id: aptId, sticky_note: noteText },
					dataType: 'json',
					success: function(res) {
						if (res.success) {
							$('#notes_alert_msg').html('<div class="alert alert-success py-1 px-2 mb-2" style="font-size:12px;"><i class="fa-solid fa-check me-1"></i>Note saved!</div>');
							setTimeout(() => { $('#notes_alert_msg').empty(); }, 3000);
						} else {
							$('#notes_alert_msg').html('<div class="alert alert-danger py-1 px-2 mb-2" style="font-size:12px;">' + escapeHtml(res.message) + '</div>');
						}
					},
					error: function() {
						$('#notes_alert_msg').html('<div class="alert alert-danger py-1 px-2 mb-2" style="font-size:12px;">Error saving note.</div>');
					},
					complete: function() {
						$btn.prop('disabled', false).text('Save');
					}
				});
			});

			// Clear Doctor Note AJAX
			$('#btn_clear_notes').click(function() {
				const aptId = $('input[name="appointment_id"]').val();
				$('#notes_textarea').val('');
				if (!aptId || aptId <= 0) return;

				$.ajax({
					url: 'php/manage-sticky-note.php',
					type: 'POST',
					data: { action: 'delete', appointment_id: aptId },
					dataType: 'json',
					success: function(res) {
						$('#notes_alert_msg').html('<div class="alert alert-info py-1 px-2 mb-2" style="font-size:12px;">Notes cleared.</div>');
						setTimeout(() => { $('#notes_alert_msg').empty(); }, 3000);
					}
				});
			});

			// If the provider closes the window/tab or navigates away, clear the call status
			<?php if (in_array($_SESSION['user_type'], ['doctor', 'healthcare', 'special_tid'])): ?>
			$(window).on('beforeunload', function() {
				const formData = new FormData();
				formData.append('appointment_id', '<?php echo (int)$appointment_id; ?>');
				formData.append('action', 'end_call');
				navigator.sendBeacon('php/handle-call-status.php', formData);
			});
			<?php endif; ?>
			<?php if ($_SESSION['user_type'] === 'patient' && !empty($appointment['is_emergency']) && (int)$appointment['is_emergency'] === 1): ?>
			// Patient navigating away from emergency call clears the call status
			$(window).on('beforeunload', function() {
				const formData = new FormData();
				formData.append('appointment_id', '<?php echo (int)$appointment_id; ?>');
				formData.append('action', 'end_call');
				navigator.sendBeacon('php/handle-call-status.php', formData);
			});
			<?php endif; ?>
		});
	</script>

	<script src="assets/js/script.js"></script>

</body>

</html>