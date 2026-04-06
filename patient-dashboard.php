<?php
/**
 * Patient Dashboard - TeleRx Bangladesh
 * Dynamic dashboard showing logged-in patient's information
 */

// Include configuration and start session
$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.php');
    exit;
}
require_once $config_path;

// Check if patient is logged in
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit;
}

// Get patient information from session
$patient_id = $_SESSION['patient_id'];

try {
    $conn = getDBConnection();

    // Fetch patient's basic information
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: login.php');
        exit;
    }

    $patient = $result->fetch_assoc();
    $stmt->close();

    $appointments = [];
    $upcoming = [];
    $conn->close();

    // Set default values if profile data is missing
    $patient['profile_image'] = $patient['profile_image'] ?? 'assets/img/doctors-dashboard/profile-06.jpg';

    // Extract variables for template use
    $patient_name = $patient['name'];
    $patient_email = $patient['email'];
    $patient_profile_image = $patient['profile_image'];

} catch (Exception $e) {
    error_log("Patient dashboard error: " . $e->getMessage());
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
                            <h3><a href="patient-dashboard.php"><?php echo htmlspecialchars($patient['name']); ?></a></h3>
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
                        include 'patient-leftside-menu.php';
                        ?>
						<div class="col-lg-8 col-xl-9">
							<div class="dashboard-header">
								<h3>Dashboard</h3>
							</div>
							
							<!-- Health Records Section (Top) -->
							<div class="row">
								<div class="col-xl-8 d-flex">
									<div class="dashboard-card w-100">
										<div class="dashboard-card-head">
											<div class="header-title">
												<h5>Health Records</h5>
											</div>											
										</div>
										<div class="dashboard-card-body">
											<div class="row">
												<div class="col-sm-7">
													<div class="row">
														<div class="col-lg-6">
															<div class="health-records icon-orange">
																<span><i class="fa-solid fa-heart"></i>Heart Rate</span>
																<h3>140 Bpm <sup> 2%</sup></h3>
															</div>
														</div>
														<div class="col-lg-6">
															<div class="health-records icon-amber">
																<span><i class="fa-solid fa-temperature-high"></i>Body Temperature </span>
																<h3>37.5 C</h3>
															</div>
														</div>
														<div class="col-lg-6">
															<div class="health-records icon-dark-blue">
																<span><i class="fa-solid fa-notes-medical"></i>Glucose Level</span>
																<h3>70 - 90<sup> 7%</sup></h3>
															</div>
														</div>
														<div class="col-lg-6">
															<div class="health-records icon-blue">
																<span><i class="fa-solid fa-highlighter"></i>SPo2</span>
																<h3>96%</h3>
															</div>
														</div>
														<div class="col-lg-6">
															<div class="health-records icon-red">
																<span><i class="fa-solid fa-syringe"></i>Blood Pressure</span>
																<h3>100 mg/dl<sup> 2%</sup></h3>
															</div>
														</div>
														<div class="col-lg-6">
															<div class="health-records icon-purple">
																<span><i class="fa-solid fa-user-pen"></i>BMI </span>
																<h3>20.1 kg/m2</h3>
															</div>
														</div>
														<div class="col-md-12">
															<div class="report-gen-date">
																<p>Report generated on last visit : <?php echo date('d M Y'); ?> <span><i class="fa-solid fa-copy"></i></span></p>
															</div>
														</div>
													</div>
												</div>
												<div class="col-sm-5">
													<div class="chart-over-all-report">
														<h6>Overall Report</h6>
														<div class="circle-bar circle-bar3 report-chart">
															<div class="circle-graph3" data-percent="66">
																<p>Last visit
																	<?php echo date('d M Y'); ?></p>
															</div>
														</div>
														<span class="health-percentage">Your health is 95% Normal</span>
														<a href="medical-details.html" class="btn btn-dark w-100 rounded-pill">View Details<i class="fa-solid fa-chevron-right ms-2"></i></a>
													</div>													
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-xl-4 d-flex">
									<div class="favourites-dashboard w-100">
										<div class="book-appointment-head">
											<h3><span>Book a new</span>Appointment</h3>
											<span class="add-icon"><a href="search.php"><i class="fa-solid fa-circle-plus"></i></a></span>
										</div>
										<div class="dashboard-card w-100">
											<div class="dashboard-card-head">
												<div class="header-title">
													<h5>Upcoming Appointments</h5>
												</div>
													<div class="card-view-link">
													<a href="<?php echo (defined('APP_BASE') && APP_BASE) ? APP_BASE . '/patient-appointments.php' : 'patient-appointments.php'; ?>">View All</a>
												</div>
											</div>
											<div class="dashboard-card-body">
												<?php if (empty($upcoming)): ?>
												<p class="text-muted text-center mb-0">No upcoming appointments</p>
												<?php else: ?>
												<ul class="list-unstyled mb-0">
													<?php foreach (array_slice($upcoming, 0, 5) as $apt): ?>
													<li class="d-flex justify-content-between align-items-center py-2 border-bottom">
														<span><strong><?php echo htmlspecialchars($apt['doctor_name'] ?? 'Doctor'); ?></strong><br>
														<small class="text-muted"><?php echo date('M j, Y', strtotime($apt['appointment_date'])); ?> <?php echo date('g:i A', strtotime($apt['slot_time'])); ?></small></span>
														<span class="badge bg-success"><?php echo htmlspecialchars($apt['status'] ?? 'Booked'); ?></span>
													</li>
													<?php endforeach; ?>
												</ul>
												<?php endif; ?>
											</div>
										</div>
									</div>								
								</div>
							</div>
							
							<!-- Reports Section with Tabs -->
							<div class="row">
								<div class="col-xl-12 d-flex">
									<div class="dashboard-card w-100">
										<div class="dashboard-card-head">
											<div class="header-title">
												<h5>Reports</h5>
											</div>											
										</div>
										<div class="dashboard-card-body">
											<div class="account-detail-table">
												<!-- Tab Menu -->
												<nav class="patient-dash-tab border-0 pb-0">
												   <ul class="nav nav-tabs-bottom">
													    <li class="nav-item">
														   <a class="nav-link active" href="#appoint-tab" data-bs-toggle="tab">Appointments</a>
													    </li>
													    <li class="nav-item">
														   <a class="nav-link" href="#medical-tab" data-bs-toggle="tab">Medical Records</a>
													    </li>
													    <li class="nav-item">
															<a class="nav-link" href="#prsc-tab" data-bs-toggle="tab">Prescriptions</a>
													    </li>
														<li class="nav-item">
															<a class="nav-link" href="#invoice-tab" data-bs-toggle="tab">Invoices</a>
														</li>
												   </ul>
											   </nav>
											   <!-- /Tab Menu -->
											   
											   <!-- Tab Content -->
											   <div class="tab-content pt-0">
												   
												   <!-- Appointments Tab -->
												   <div id="appoint-tab" class="tab-pane fade show active">
														<div class="custom-new-table">
															<div class="table-responsive">
																<table class="table table-hover table-center mb-0">
																	<thead>
																		<tr>
																			<th>ID</th>
																			<th>Doctor</th>
																			<th>Date & Time</th>
																			<th>Status</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php if (empty($appointments)): ?>
																		<tr>
																			<td colspan="4" class="text-center py-5">
																				<p class="text-muted">No appointments found</p>
																			</td>
																		</tr>
																		<?php else: foreach ($appointments as $apt): ?>
																		<tr>
																			<td>#APT<?php echo str_pad($apt['id'], 5, '0', STR_PAD_LEFT); ?></td>
																			<td><?php echo htmlspecialchars($apt['doctor_name'] ?? '—'); ?><br><small class="text-muted"><?php echo htmlspecialchars($apt['specialty'] ?? ''); ?></small></td>
																			<td><?php echo date('M j, Y', strtotime($apt['appointment_date'])); ?> <?php echo date('g:i A', strtotime($apt['slot_time'])); ?></td>
																			<td><span class="badge bg-success"><?php echo htmlspecialchars($apt['status'] ?? 'Booked'); ?></span></td>
																		</tr>
																		<?php endforeach; endif; ?>
																	</tbody>
																</table>
															</div>
														</div>
												   </div>
												   <!-- /Appointments Tab -->
												   
												   <!-- Medical Records Tab -->
												   <div class="tab-pane fade" id="medical-tab">
														<div class="custom-table">
															<div class="table-responsive">
																<table class="table table-center mb-0">
																	<thead>
																		<tr>
																			<th>ID</th>
																			<th>Name</th>
																			<th>Date</th>
																			<th>Record For</th>
																			<th>Comments</th>
																			<th>Action</th>
																		</tr>
																	</thead>
																	<tbody>
																		<tr>
																			<td colspan="6" class="text-center py-5">
																				<p class="text-muted">No medical records found</p>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</div>
														</div>
												   </div>
												   <!-- /Medical Records Tab -->
												   
												   <!-- Prescriptions Tab -->
												   <div class="tab-pane fade" id="prsc-tab">
														<div class="custom-table">
															<div class="table-responsive">
																<table class="table table-center mb-0">
																	<thead>
																		<tr>
																			<th>ID</th>
																			<th>Doctor</th>
																			<th>Date</th>
																			<th>Prescription</th>
																			<th>Action</th>
																		</tr>
																	</thead>
																	<tbody>
																		<tr>
																			<td colspan="5" class="text-center py-5">
																				<p class="text-muted">No prescriptions found</p>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</div>
														</div>
												   </div>
												   <!-- /Prescriptions Tab -->
												   
												   <!-- Invoices Tab -->
												   <div class="tab-pane fade" id="invoice-tab">
														<div class="custom-table">
															<div class="table-responsive">
																<table class="table table-center mb-0">
																	<thead>
																		<tr>
																			<th>Invoice ID</th>
																			<th>Doctor</th>
																			<th>Date</th>
																			<th>Amount</th>
																			<th>Status</th>
																			<th>Action</th>
																		</tr>
																	</thead>
																	<tbody>
																		<tr>
																			<td colspan="6" class="text-center py-5">
																				<p class="text-muted">No invoices found</p>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</div>
														</div>
												   </div>
												   <!-- /Invoices Tab -->
												   
											   </div>
											   <!-- /Tab Content -->
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Reports Section -->
							
						</div>
					</div>
				</div>
			</div>		
			<!-- /Page Content -->
   
			<!-- Footer Section -->
			<?php include 'footer.php'; ?>
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
