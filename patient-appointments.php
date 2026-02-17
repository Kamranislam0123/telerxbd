<?php
/**
 * Patient Appointments - TeleRx Bangladesh
 * Patient appointments page showing all appointments
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

    // Set default values if profile data is missing
    $patient['profile_image'] = $patient['profile_image'] ?? 'assets/img/doctors-dashboard/profile-06.jpg';

    // TODO: Fetch appointments from appointments table when it exists
    // For now, we'll show an empty state
    $appointments = [];

    $conn->close();

} catch (Exception $e) {
    error_log("Patient appointments error: " . $e->getMessage());
    header('Location: login.php');
    exit;
}

include 'header.php';

?>
<!DOCTYPE html>
<html lang="en">
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
                        <h2 class="breadcrumb-title">My Appointments</h2>
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
								<h3>My Appointments</h3>
							</div>
							
							<!-- Appointment Tabs -->
							<div class="appointment-tab-head">
								<div class="appointment-tabs">
									<ul class="nav nav-pills inner-tab" id="pills-tab" role="tablist">
										<li class="nav-item" role="presentation">
											<button class="nav-link active" id="pills-upcoming-tab" data-bs-toggle="tab" data-bs-target="#pills-upcoming" type="button" role="tab" aria-controls="pills-upcoming" aria-selected="true">Upcoming<span>0</span></button>
										</li>	
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="pills-cancel-tab" data-bs-toggle="tab" data-bs-target="#pills-cancel" type="button" role="tab" aria-controls="pills-cancel" aria-selected="false">Cancelled<span>0</span></button>
										</li>
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="pills-complete-tab" data-bs-toggle="tab" data-bs-target="#pills-complete" type="button" role="tab" aria-controls="pills-complete" aria-selected="false">Completed<span>0</span></button>
										</li>
									</ul>
								</div>
							</div>
							<!-- /Appointment Tabs -->
							
							<!-- Tab Content -->
							<div class="tab-content" id="pills-tabContent">
								
								<!-- Upcoming Appointments Tab -->
								<div class="tab-pane fade show active" id="pills-upcoming" role="tabpanel" aria-labelledby="pills-upcoming-tab">
									<div class="row">
										<div class="col-xl-12 d-flex">
											<div class="dashboard-card w-100">
												<div class="dashboard-card-head">
													<div class="header-title">
														<h5>Upcoming Appointments</h5>
													</div>
												</div>
												<div class="dashboard-card-body">
													<?php 
													$upcoming_appointments = array_filter($appointments, function($apt) {
														return isset($apt['status']) && in_array(strtolower($apt['status']), ['upcoming', 'pending', 'confirmed', 'scheduled']);
													});
													?>
													<?php if (empty($upcoming_appointments)): ?>
														<div class="text-center py-5">
															<i class="isax isax-calendar-1" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
															<h5 class="text-muted">No Upcoming Appointments</h5>
															<p class="text-muted">You don't have any upcoming appointments. Book your first appointment now!</p>
															<a href="search.php" class="btn btn-primary-gradient mt-3">Book Appointment</a>
														</div>
													<?php else: ?>
														<div class="table-responsive">
															<table class="table dashboard-table appoint-table">
																<thead>
																	<tr>
																		<th>Doctor</th>
																		<th>Date & Time</th>
																		<th>Type</th>
																		<th>Status</th>
																		<th>Action</th>
																	</tr>
																</thead>
																<tbody>
																	<?php foreach ($upcoming_appointments as $appointment): ?>
																		<tr>
																			<td>
																				<div class="patient-info-profile">
																					<a href="#" class="table-avatar">
																						<img src="<?php echo htmlspecialchars($appointment['doctor_image'] ?? 'assets/img/doctors/doctor-01.jpg'); ?>" alt="Doctor">
																					</a>
																					<div class="patient-name-info">
																						<span>#<?php echo htmlspecialchars($appointment['appointment_id'] ?? 'N/A'); ?></span>
																						<h5><a href="#"><?php echo htmlspecialchars($appointment['doctor_name'] ?? 'Doctor'); ?></a></h5>
																					</div>
																				</div>
																			</td>
																			<td>
																				<div class="appointment-date-created">
																					<h6><?php echo isset($appointment['appointment_date']) ? date('d M Y h:i A', strtotime($appointment['appointment_date'])) : 'N/A'; ?></h6>
																					<span class="badge table-badge"><?php echo htmlspecialchars($appointment['appointment_type'] ?? 'N/A'); ?></span>
																				</div>
																			</td>
																			<td><?php echo htmlspecialchars($appointment['appointment_type'] ?? 'N/A'); ?></td>
																			<td>
																				<span class="badge badge-success">
																					<?php echo ucfirst($appointment['status'] ?? 'Upcoming'); ?>
																				</span>
																			</td>
																			<td>
																				<div class="apponiment-actions d-flex align-items-center">
																					<a href="#" class="text-success-icon me-2" title="View"><i class="fa-solid fa-eye"></i></a>
																					<a href="#" class="text-danger-icon" title="Cancel"><i class="fa-solid fa-xmark"></i></a>
																				</div>
																			</td>
																		</tr>
																	<?php endforeach; ?>
																</tbody>
															</table>
														</div>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Upcoming Appointments Tab -->
								
								<!-- Cancelled Appointments Tab -->
								<div class="tab-pane fade" id="pills-cancel" role="tabpanel" aria-labelledby="pills-cancel-tab">
									<div class="row">
										<div class="col-xl-12 d-flex">
											<div class="dashboard-card w-100">
												<div class="dashboard-card-head">
													<div class="header-title">
														<h5>Cancelled Appointments</h5>
													</div>
												</div>
												<div class="dashboard-card-body">
													<?php 
													$cancelled_appointments = array_filter($appointments, function($apt) {
														return isset($apt['status']) && strtolower($apt['status']) == 'cancelled';
													});
													?>
													<?php if (empty($cancelled_appointments)): ?>
														<div class="text-center py-5">
															<i class="isax isax-calendar-1" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
															<h5 class="text-muted">No Cancelled Appointments</h5>
															<p class="text-muted">You don't have any cancelled appointments.</p>
														</div>
													<?php else: ?>
														<div class="table-responsive">
															<table class="table dashboard-table appoint-table">
																<thead>
																	<tr>
																		<th>Doctor</th>
																		<th>Date & Time</th>
																		<th>Type</th>
																		<th>Status</th>
																		<th>Action</th>
																	</tr>
																</thead>
																<tbody>
																	<?php foreach ($cancelled_appointments as $appointment): ?>
																		<tr>
																			<td>
																				<div class="patient-info-profile">
																					<a href="#" class="table-avatar">
																						<img src="<?php echo htmlspecialchars($appointment['doctor_image'] ?? 'assets/img/doctors/doctor-01.jpg'); ?>" alt="Doctor">
																					</a>
																					<div class="patient-name-info">
																						<span>#<?php echo htmlspecialchars($appointment['appointment_id'] ?? 'N/A'); ?></span>
																						<h5><a href="#"><?php echo htmlspecialchars($appointment['doctor_name'] ?? 'Doctor'); ?></a></h5>
																					</div>
																				</div>
																			</td>
																			<td>
																				<div class="appointment-date-created">
																					<h6><?php echo isset($appointment['appointment_date']) ? date('d M Y h:i A', strtotime($appointment['appointment_date'])) : 'N/A'; ?></h6>
																					<span class="badge table-badge"><?php echo htmlspecialchars($appointment['appointment_type'] ?? 'N/A'); ?></span>
																				</div>
																			</td>
																			<td><?php echo htmlspecialchars($appointment['appointment_type'] ?? 'N/A'); ?></td>
																			<td>
																				<span class="badge badge-danger">
																					<?php echo ucfirst($appointment['status'] ?? 'Cancelled'); ?>
																				</span>
																			</td>
																			<td>
																				<div class="apponiment-actions d-flex align-items-center">
																					<a href="#" class="text-success-icon me-2" title="View"><i class="fa-solid fa-eye"></i></a>
																				</div>
																			</td>
																		</tr>
																	<?php endforeach; ?>
																</tbody>
															</table>
														</div>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Cancelled Appointments Tab -->
								
								<!-- Completed Appointments Tab -->
								<div class="tab-pane fade" id="pills-complete" role="tabpanel" aria-labelledby="pills-complete-tab">
									<div class="row">
										<div class="col-xl-12 d-flex">
											<div class="dashboard-card w-100">
												<div class="dashboard-card-head">
													<div class="header-title">
														<h5>Completed Appointments</h5>
													</div>
												</div>
												<div class="dashboard-card-body">
													<?php 
													$completed_appointments = array_filter($appointments, function($apt) {
														return isset($apt['status']) && strtolower($apt['status']) == 'completed';
													});
													?>
													<?php if (empty($completed_appointments)): ?>
														<div class="text-center py-5">
															<i class="isax isax-calendar-1" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
															<h5 class="text-muted">No Completed Appointments</h5>
															<p class="text-muted">You don't have any completed appointments yet.</p>
														</div>
													<?php else: ?>
														<div class="table-responsive">
															<table class="table dashboard-table appoint-table">
																<thead>
																	<tr>
																		<th>Doctor</th>
																		<th>Date & Time</th>
																		<th>Type</th>
																		<th>Status</th>
																		<th>Action</th>
																	</tr>
																</thead>
																<tbody>
																	<?php foreach ($completed_appointments as $appointment): ?>
																		<tr>
																			<td>
																				<div class="patient-info-profile">
																					<a href="#" class="table-avatar">
																						<img src="<?php echo htmlspecialchars($appointment['doctor_image'] ?? 'assets/img/doctors/doctor-01.jpg'); ?>" alt="Doctor">
																					</a>
																					<div class="patient-name-info">
																						<span>#<?php echo htmlspecialchars($appointment['appointment_id'] ?? 'N/A'); ?></span>
																						<h5><a href="#"><?php echo htmlspecialchars($appointment['doctor_name'] ?? 'Doctor'); ?></a></h5>
																					</div>
																				</div>
																			</td>
																			<td>
																				<div class="appointment-date-created">
																					<h6><?php echo isset($appointment['appointment_date']) ? date('d M Y h:i A', strtotime($appointment['appointment_date'])) : 'N/A'; ?></h6>
																					<span class="badge table-badge"><?php echo htmlspecialchars($appointment['appointment_type'] ?? 'N/A'); ?></span>
																				</div>
																			</td>
																			<td><?php echo htmlspecialchars($appointment['appointment_type'] ?? 'N/A'); ?></td>
																			<td>
																				<span class="badge badge-success">
																					<?php echo ucfirst($appointment['status'] ?? 'Completed'); ?>
																				</span>
																			</td>
																			<td>
																				<div class="apponiment-actions d-flex align-items-center">
																					<a href="#" class="text-success-icon me-2" title="View"><i class="fa-solid fa-eye"></i></a>
																					<a href="#" class="text-info-icon" title="View Prescription"><i class="fa-solid fa-prescription"></i></a>
																				</div>
																			</td>
																		</tr>
																	<?php endforeach; ?>
																</tbody>
															</table>
														</div>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Completed Appointments Tab -->
								
							</div>
							<!-- /Tab Content -->
							
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
		
		<!-- Custom JS -->
		<script src="assets/js/script.js"></script>
		
	</body>
</html>
