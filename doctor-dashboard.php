<?php
/**
 * Doctor Dashboard - TeleRx Bangladesh
 * Dynamic dashboard showing logged-in doctor's information
 */

// Include configuration and start session
$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.php');
    exit;
}
require_once $config_path;

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get doctor information from session
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
        header('Location: login.php');
        exit;
    }

    $doctor = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    // Set default values if profile data is missing
    $doctor['profile_image'] = $doctor['profile_image'] ?? 'assets/img/doctors-dashboard/doctor-profile-img.jpg';
    $doctor['specialty'] = $doctor['specialty'] ?? 'General Medicine';

    // Extract variables for template use
    $doctor_name = $doctor['name'];
    $doctor_email = $doctor['email'];
    $doctor_phone = $doctor['phone'];
    $doctor_bmdc_no = $doctor['bmdc_no'];
    $doctor_specialty = $doctor['specialty'];
    $doctor_profile_image = $doctor['profile_image'];

} catch (Exception $e) {
    error_log("Doctor dashboard error: " . $e->getMessage());
    header('Location: login.php');
    exit;
}

include 'header.php';

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<style>
		.dashboard-welcome-card {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border-radius: 15px;
			padding: 30px;
			color: white;
			box-shadow: 0 10px 30px rgba(0,0,0,0.1);
		}
		.welcome-content h3 {
			font-size: 2rem;
			font-weight: 600;
			margin-bottom: 10px;
		}
		.welcome-content p {
			font-size: 1.1rem;
			opacity: 0.9;
			margin-bottom: 0;
		}
		</style>

	</head>		
	<body>

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center inner-banner">
                <div class="col-md-12 col-12 text-center">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <h3><a href="doctor-profile.php?doctor_id=<?php echo $doctor_id; ?>"><?php echo htmlspecialchars($doctor['name']); ?></a></h3>
                        </ol>
                        <h2 class="breadcrumb-title">Dashboard</h2>
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

                        <?php
                        include 'doctor-leftside-menu.php';
                        ?>
							
						</div>
						
						<div class="col-lg-8 col-xl-9">
							<div class="row">
                                <div class="col-xl-7 d-flex">
                                    <div class="dashboard-main-col w-100">
                                        <div class="upcoming-appointment-card">
                                            <div class="title-card">
                                                <h5>Upcoming Appointment</h5>
                                            </div>
                                            <div class="upcoming-patient-info">
                                                <div class="info-details">
                                                    <span class="img-avatar"><img src="assets/img/doctors-dashboard/profile-01.jpg" alt="Img"></span>
                                                    <div class="name-info">
                                                        <span>#Apt0001</span>
                                                        <h6>Adrian Marshall</h6>
                                                    </div>

                                                </div>
                                                <div class="date-details">
                                                    <span>General visit</span>
                                                    <h6>Today, 10:45 AM</h6>
                                                </div>
                                                <div class="circle-bg">
                                                    <img src="assets/img/bg/dashboard-circle-bg.png" alt="Img">
                                                </div>
                                            </div>
                                            <div class="appointment-card-footer">
                                                <h5><i class="fa-solid fa-video"></i>Video Appointment</h5>
                                                <div class="btn-appointments">
                                                    <a href="chat-doctor.html" class="btn">Chat Now</a>
                                                    <a href="doctor-appointment-start.html" class="btn">Start Appointment</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 d-flex">
									<div class="dashboard-box-col w-100">
										<div class="dashboard-widget-box">
											<div class="dashboard-content-info">
												<h6>Total Patient</h6>
												<h4>978</h4>
												<span class="text-success"><i class="fa-solid fa-arrow-up"></i>15% From Last Week</span>
											</div>
											<div class="dashboard-widget-icon">
												<span class="dash-icon-box"><i class="fa-solid fa-user-injured"></i></span>
											</div>
										</div>
										<div class="dashboard-widget-box">
											<div class="dashboard-content-info">
												<h6>Patients Today</h6>
												<h4>80</h4>
												<span class="text-danger"><i class="fa-solid fa-arrow-up"></i>15% From Yesterday</span>
											</div>
											<div class="dashboard-widget-icon">
												<span class="dash-icon-box"><i class="fa-solid fa-user-clock"></i></span>
											</div>
										</div>
										<div class="dashboard-widget-box">
											<div class="dashboard-content-info">
												<h6>Appointments Today</h6>
												<h4>50</h4>
												<span class="text-success"><i class="fa-solid fa-arrow-up"></i>20% From Yesterday</span>
											</div>
											<div class="dashboard-widget-icon">
												<span class="dash-icon-box"><i class="fa-solid fa-calendar-days"></i></span>
											</div>
										</div>
									</div>							
								</div>
								<div class="col-xl-8 d-flex">
									<div class="dashboard-card w-100">
										<div class="dashboard-card-head">
											<div class="header-title">
												<h5>Appointment</h5>
											</div>
											<div class="dropdown header-dropdown">
												<a class="dropdown-toggle nav-tog" data-bs-toggle="dropdown" href="javascript:void(0);">
													Last 7 Days
												</a>
												<div class="dropdown-menu dropdown-menu-end">
													<a href="javascript:void(0);" class="dropdown-item">
														Today
													</a>
													<a href="javascript:void(0);" class="dropdown-item">
														This Month
													</a>
													<a href="javascript:void(0);" class="dropdown-item">
														Last 7 Days
													</a>
												</div>
											</div>
											
										</div>
										<div class="dashboard-card-body">
											<div class="table-responsive">
												<table class="table dashboard-table appoint-table">
													<tbody>
														<tr>
															<td>
																<div class="patient-info-profile">
																	<a href="appointments.php" class="table-avatar">
																		<img src="assets/img/doctors-dashboard/profile-01.jpg" alt="Img">
																	</a>
																	<div class="patient-name-info">
																		<span>#Apt0001</span>
																		<h5><a href="appointments.php">Adrian Marshall</a></h5>
																	</div>
																</div>
																
															</td>
															<td>
																<div class="appointment-date-created">
																	<h6>11 Nov 2024 10.45 AM</h6>
																	<span class="badge table-badge">General</span>
																</div>
															</td>
															<td>
																<div class="apponiment-actions d-flex align-items-center">
																	<a href="#" class="text-success-icon me-2"><i class="fa-solid fa-check"></i></a>
																	<a href="#" class="text-danger-icon"><i class="fa-solid fa-xmark"></i></a>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="patient-info-profile">
																	<a href="appointments.php" class="table-avatar">
																		<img src="assets/img/doctors-dashboard/profile-02.jpg" alt="Img">
																	</a>
																	<div class="patient-name-info">
																		<span>#Apt0002</span>
																		<h5><a href="appointments.php">Kelly Stevens</a></h5>
																	</div>
																</div>
																
															</td>
															<td>
																<div class="appointment-date-created">
																	<h6>10 Nov 2024 11.00 AM</h6>
																	<span class="badge table-badge">Clinic Consulting</span>
																</div>
															</td>
															<td>
																<div class="apponiment-actions d-flex align-items-center">
																	<a href="#" class="text-success-icon me-2"><i class="fa-solid fa-check"></i></a>
																	<a href="#" class="text-danger-icon"><i class="fa-solid fa-xmark"></i></a>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="patient-info-profile">
																	<a href="appointments.php" class="table-avatar">
																		<img src="assets/img/doctors-dashboard/profile-03.jpg" alt="Img">
																	</a>
																	<div class="patient-name-info">
																		<span>#Apt0003</span>
																		<h5><a href="appointments.php">Samuel Anderson</a></h5>
																	</div>
																</div>
																
															</td>
															<td>
																<div class="appointment-date-created">
																	<h6>03 Nov 2024 02.00 PM</h6>
																	<span class="badge table-badge">General</span>
																</div>
															</td>
															<td>
																<div class="apponiment-actions d-flex align-items-center">
																	<a href="#" class="text-success-icon me-2"><i class="fa-solid fa-check"></i></a>
																	<a href="#" class="text-danger-icon"><i class="fa-solid fa-xmark"></i></a>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="patient-info-profile">
																	<a href="appointments.php" class="table-avatar">
																		<img src="assets/img/doctors-dashboard/profile-04.jpg" alt="Img">
																	</a>
																	<div class="patient-name-info">
																		<span>#Apt0004</span>
																		<h5><a href="appointments.php">Catherine Griffin</a></h5>
																	</div>
																</div>
																
															</td>
															<td>
																<div class="appointment-date-created">
																	<h6>01 Nov 2024 04.00 PM</h6>
																	<span class="badge table-badge">Clinic Consulting</span>
																</div>
															</td>
															<td>
																<div class="apponiment-actions d-flex align-items-center">
																	<a href="#" class="text-success-icon me-2"><i class="fa-solid fa-check"></i></a>
																	<a href="#" class="text-danger-icon"><i class="fa-solid fa-xmark"></i></a>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="patient-info-profile">
																	<a href="appointments.php" class="table-avatar">
																		<img src="assets/img/doctors-dashboard/profile-05.jpg" alt="Img">
																	</a>
																	<div class="patient-name-info">
																		<span>#Apt0005</span>
																		<h5><a href="appointments.php">Robert Hutchinson</a></h5>
																	</div>
																</div>
																
															</td>
															<td>
																<div class="appointment-date-created">
																	<h6>28 Oct 2024 05.30 PM</h6>
																	<span class="badge table-badge">General</span>
																</div>
															</td>
															<td>
																<div class="apponiment-actions d-flex align-items-center">
																	<a href="#" class="text-success-icon me-2"><i class="fa-solid fa-check"></i></a>
																	<a href="#" class="text-danger-icon"><i class="fa-solid fa-xmark"></i></a>
																</div>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
										
									</div>
								</div>
							</div>
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
		
		<!-- Bootstrap Core JS -->
		<script src="assets/js/bootstrap.bundle.min.js"></script>
		
		<!-- Sticky Sidebar JS -->
        <script src="assets/plugins/theia-sticky-sidebar/ResizeSensor.js"></script>
        <script src="assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js"></script>

		<!-- select JS -->
		<script src="assets/plugins/select2/js/select2.min.js"></script>

		<!-- Apexchart JS -->
		<script src="assets/plugins/apex/apexcharts.min.js"></script>
		<script src="assets/plugins/apex/chart-data.js"></script>
		
		<!-- Circle Progress JS -->
		<script src="assets/js/circle-progress.min.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/script.js"></script>
		
	</body>
</html>