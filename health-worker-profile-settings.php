<?php
/**
 * Health-Worker Profile Settings - TeleRx Bangladesh
 * Profile settings page for health-workers
 */

// Include configuration
$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.html');
    exit;
}
require_once $config_path;

// Check if health-worker is logged in
if (!isset($_SESSION['healthcare_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

$healthcare_id = $_SESSION['healthcare_id'];

try {
    $conn = getDBConnection();

    // Fetch health-worker's basic information and profile
    $stmt = $conn->prepare("
        SELECT h.*, hp.*
        FROM healthcare_providers h
        LEFT JOIN healthcare_providers_profiles hp ON h.id = hp.healthcare_provider_id
        WHERE h.id = ?
    ");
    $stmt->bind_param("i", $healthcare_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: login.html');
        exit;
    }

    $healthcare = $result->fetch_assoc();

    // Generate TID if it doesn't exist
    if (empty($healthcare['tid'])) {
        $date_prefix = date('Ymd');
        $tid_counter = 1;
        
        // Find the highest TID for today
        $tid_check = $conn->query("SELECT tid FROM healthcare_providers WHERE tid LIKE 'TEL-{$date_prefix}-%' ORDER BY tid DESC LIMIT 1");
        if ($tid_check && $tid_check->num_rows > 0) {
            $last_tid = $tid_check->fetch_assoc()['tid'];
            $last_counter = (int)substr($last_tid, -4);
            $tid_counter = $last_counter + 1;
        }
        $tid = 'TEL-' . $date_prefix . '-' . str_pad($tid_counter, 4, '0', STR_PAD_LEFT);
        
        // Update the healthcare provider with TID
        $update_stmt = $conn->prepare("UPDATE healthcare_providers SET tid = ? WHERE id = ?");
        $update_stmt->bind_param("si", $tid, $healthcare_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        $healthcare['tid'] = $tid;
    }

    // Set default values if profile data is missing
    $healthcare['profile_image'] = $healthcare['profile_image'] ?? 'assets/img/doctors-dashboard/doctor-profile-img.jpg';
    $healthcare['gender'] = $healthcare['gender'] ?? '';
    $healthcare['degrees'] = $healthcare['degrees'] ?? '';
    $healthcare['currently_working'] = $healthcare['currently_working'] ?? '';
    $healthcare['present_address'] = $healthcare['present_address'] ?? '';
    $healthcare['phone'] = $healthcare['phone'] ?? '';

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Health-worker profile settings error: " . $e->getMessage());
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>TeleRx Bangladesh - Health-Worker Profile Settings</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="keywords" content="practo clone, doccure, doctor appointment, Practo clone html template, doctor booking template">
		<meta name="author" content="Practo Clone HTML Template - Doctor Booking Template">
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
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
		
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
		
		<!-- Feathericon CSS -->
    	<link rel="stylesheet" href="assets/css/feather.css">
		
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
									<a href="index.html">Home</a>
								</li>
								<li><a href="search-2.php">Doctor List</a></li>
								<li><a href="about-us.html">About Us</a></li>
								<li><a href="contact-us.html">Contact</a></li>
								<li class="login-link"><a href="php/logout.php">Logout</a></li>
							</ul>
						</div>
						<ul class="nav header-navbar-rht">
							<!-- User Menu -->
							<li class="nav-item dropdown has-arrow logged-item">
								<a href="#" class="nav-link ps-0" data-bs-toggle="dropdown">
									<span class="user-img">
										<img class="rounded-circle" src="<?php echo htmlspecialchars($healthcare['profile_image']); ?>" width="31" alt="<?php echo htmlspecialchars($healthcare['name']); ?>">
									</span>
								</a>
								<div class="dropdown-menu dropdown-menu-end">
									<div class="user-header">
										<div class="avatar avatar-sm">
											<img src="<?php echo htmlspecialchars($healthcare['profile_image']); ?>" alt="User Image" class="avatar-img rounded-circle">
										</div>
										<div class="user-text">
											<h6><?php echo htmlspecialchars($healthcare['name']); ?></h6>
											<p class="text-muted mb-0">Health-Worker</p>
										</div>
									</div>
									<a class="dropdown-item" href="health-worker-profile-settings.php">Profile Settings</a>
									<a class="dropdown-item" href="php/logout.php">Logout</a>
								</div>
							</li>
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
									<h3><?php echo htmlspecialchars($healthcare['name']); ?></h3>
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
										<a href="#" class="booking-doc-img">
											<img src="<?php echo htmlspecialchars($healthcare['profile_image']); ?>" alt="User Image">
										</a>
										<div class="profile-det-info">
											<h3><?php echo htmlspecialchars($healthcare['name']); ?></h3>
											<div class="patient-details">
												<h5 class="mb-0"><?php echo htmlspecialchars($healthcare['degrees'] ?: 'Health-Worker'); ?></h5>
											</div>
											<?php if (!empty($healthcare['tid'])): ?>
											<span class="badge doctor-role-badge"><i class="fa-solid fa-circle"></i>TID: <?php echo htmlspecialchars($healthcare['tid']); ?></span>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
							<!-- /Profile Sidebar -->							
						</div>
						<div class="col-lg-8 col-xl-9">
							<!-- Profile Settings -->
							<div class="dashboard-header">
								<h3><?php echo htmlspecialchars($healthcare['name']); ?> Profile Settings</h3>
							</div>

							<!-- Profile Form -->
							<div class="setting-title">
								<h5>Profile Information</h5>
							</div>

							<!-- Single Profile Form -->
							<form action="php/save-healthcare-profile-settings.php" method="POST" enctype="multipart/form-data" id="profileForm">
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
												<label class="form-label">Name</label>
												<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($healthcare['name']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Email</label>
												<input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($healthcare['email']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Phone Number</label>
												<input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($healthcare['phone']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Gender</label>
												<select class="form-control" name="gender">
													<option value="">Select Gender</option>
													<option value="Male" <?php echo (isset($healthcare['gender']) && $healthcare['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
													<option value="Female" <?php echo (isset($healthcare['gender']) && $healthcare['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
													<option value="Other" <?php echo (isset($healthcare['gender']) && $healthcare['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
												</select>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">TID (TeleRx ID)</label>
												<input type="text" class="form-control" value="<?php echo htmlspecialchars($healthcare['tid'] ?: 'Not Generated'); ?>" readonly>
												<small class="form-text text-muted">This is your unique TeleRx ID (automatically generated)</small>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Degrees</label>
												<input type="text" class="form-control" name="degrees" value="<?php echo htmlspecialchars($healthcare['degrees'] ?? ''); ?>" placeholder="e.g., B.Sc Nursing, Diploma">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Currently Working / Experience</label>
												<input type="text" class="form-control" name="currently_working" value="<?php echo htmlspecialchars($healthcare['currently_working'] ?? ''); ?>" placeholder="Current workplace and experience">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Present Address</label>
												<input type="text" class="form-control" name="present_address" value="<?php echo htmlspecialchars($healthcare['present_address'] ?? ''); ?>" placeholder="Your current address">
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
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">NID Card (File Upload)</label>
												<div class="change-avatar img-upload">
													<div class="profile-img">
														<i class="fa-solid fa-id-card"></i>
													</div>
													<div class="upload-img">
														<h6>NID Card</h6>
														<div class="imgs-load d-flex align-items-center">
															<div class="change-photo">
																Upload
																<input type="file" class="upload" name="nid_file" accept=".pdf,.jpg,.png,.jpeg">
															</div>
														</div>
														<p class="form-text">PDF, JPG, PNG up to 5MB</p>
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
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
												<li><a href="about-us.html">About</a></li>
												<li><a href="search.php">Features</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="footer-bottom">
					<div class="container">
						<div class="copyright">
							<div class="copyright-text">
								<p class="mb-0">Copyright © 2026 TeleRx Bangladesh. All Rights Reserved</p>
							</div>
						</div>
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
		
		<!-- Custom JS -->
		<script src="assets/js/script.js"></script>

		<!-- Profile Settings Form Handler -->
		<script>
		$(document).ready(function() {
			// Handle profile settings form submissions
			$('form[action="php/save-healthcare-profile-settings.php"]').on('submit', function(e) {
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
								$('.booking-doc-img img').attr('src', response.profile_image);
								$('.user-img img').attr('src', response.profile_image);
								$('.avatar-img').attr('src', response.profile_image);
								form.find('input[name="profile_image"]').val('');
							}

							// Update name if it was changed
							var newName = form.find('input[name="name"]').val();
							if (newName && newName.trim() !== '') {
								$('.profile-det-info h3').text(newName);
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
				$('.alert').remove();
				var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
				var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
					'<strong>' + (type === 'success' ? 'Success!' : 'Error!') + '</strong> ' + message +
					'<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
					'</div>';
				$('body').prepend(alertHtml);
				if (type === 'success') {
					setTimeout(function() {
						$('.alert-success').fadeOut();
					}, 5000);
				}
				$('html, body').animate({ scrollTop: 0 }, 500);
			}
		});
		</script>
	</body>
</html>
