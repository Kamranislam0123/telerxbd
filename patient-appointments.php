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

	// Fetch appointments for this patient (if appointments table exists)
	$appointments = [];

	$apt_stmt = $conn->prepare(
		"SELECT a.*, d.name AS doctor_name, d.email AS doctor_email, d.phone AS doctor_phone, dp.profile_image AS doctor_image, dp.specialty AS doctor_specialty
		 FROM appointments a
		 LEFT JOIN doctors d ON d.id = a.doctor_id
		 LEFT JOIN doctor_profiles dp ON dp.doctor_id = d.id
		 WHERE a.patient_id = ?
		 ORDER BY a.appointment_date DESC, a.slot_time DESC"
	);
	if ($apt_stmt) {
		$apt_stmt->bind_param("i", $patient_id);
		if (!$apt_stmt->execute()) {
			throw new Exception("Execute failed: (" . $apt_stmt->errno . ") " . $apt_stmt->error);
		}
		$apt_result = $apt_stmt->get_result();
		while ($row = $apt_result->fetch_assoc()) {
			// Normalise / fallback values
			$row['appointment_number'] = $row['appointment_number'] ?? ('APT' . str_pad($row['id'], 5, '0', STR_PAD_LEFT));
			$row['doctor_image'] = $row['doctor_image'] ?: 'assets/img/doctors/doctor-01.jpg';
			// appointment_type may not exist on older schemas
			$row['appointment_type'] = $row['appointment_type'] ?? ($row['visit_type'] ?? 'Consultation');
			$appointments[] = $row;
		}
		$apt_stmt->close();
	}

	$conn->close();

} catch (Exception $e) {
    error_log("Patient appointments error: " . $e->getMessage());
    // If we're already logged in, don't redirect to login.php, just show the error.
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        die("An error occurred while loading your appointments. Please contact support. Error: " . htmlspecialchars($e->getMessage()));
    } else {
        header('Location: login.php');
        exit;
    }
}

include 'header.php';

?>
<!DOCTYPE html>
<html lang="en">
<body>
	<style>
	.appointment-action ul li .btn-sm {
		padding: 4px 10px;
		font-size: 12px;
		line-height: 1.5;
		border-radius: 4px;
		min-width: unset;
		height: auto;
		white-space: nowrap;
	}
	</style>

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
									<ul class="nav nav-pills inner-tab">
										<li class="nav-item">
											<button class="nav-link active">All Appointments <span><?php echo count($appointments); ?></span></button>
										</li>	
									</ul>
								</div>
							</div>
							<!-- /Appointment Tabs -->
							
							<!-- Tab Content -->
							<div class="tab-content" id="pills-tabContent">
								
								<!-- All Appointments Tab -->
								<div class="tab-pane fade show active">
									<div class="row">
										<div class="col-xl-12 d-flex">
											<div class="dashboard-card w-100">
												<div class="dashboard-card-head">
													<div class="header-title">
														<h5>My Appointments History</h5>
													</div>
												</div>
												<div class="dashboard-card-body">
													<?php if (empty($appointments)): ?>
														<div class="text-center py-5">
															<i class="isax isax-calendar-1" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
															<h5 class="text-muted">No Appointments Found</h5>
															<a href="search.php" class="btn btn-primary-gradient mt-3">Book Appointment</a>
														</div>
													<?php else: ?>
														<?php foreach ($appointments as $appointment): 
															$status = strtolower(trim($appointment['status'] ?? 'pending'));
															$badge_class = 'bg-primary-light';
															$display_status = ucfirst($status);
															if ($status === 'completed') {
																$badge_class = 'bg-success-light';
															} elseif ($status === 'cancelled') {
																$badge_class = 'bg-danger-light';
															} elseif (in_array($status, ['confirmed', 'booked', 'pending', 'upcoming'])) {
																$badge_class = 'bg-info-light';
																$display_status = 'Upcoming';
															}
														?>
															<!-- Appointment List -->
															<div class="appointment-wrap">
																<ul>
																	<li>
																		<div class="patinet-information">
																			<a href="appointment-detail.php?id=<?php echo (int)$appointment['id']; ?>">
																				<img src="<?php echo htmlspecialchars($appointment['doctor_image'] ?? 'assets/img/doctors/doctor-01.jpg'); ?>" alt="Doctor Image">
																			</a>
																			<div class="patient-info">
																				<p>#<?php echo htmlspecialchars($appointment['appointment_number'] ?? ('APT' . str_pad($appointment['id'] ?? 0, 5, '0', STR_PAD_LEFT))); ?></p>
																				<h6><a href="appointment-detail.php?id=<?php echo (int)$appointment['id']; ?>"><?php echo htmlspecialchars($appointment['doctor_name'] ?? 'Doctor'); ?></a></h6>
																			</div>
																		</div>
																	</li>
																	<li class="appointment-info">
																		<p><i class="isax isax-clock5"></i><?php echo isset($appointment['appointment_date']) ? date('d M Y h:i A', strtotime($appointment['appointment_date'])) : 'N/A'; ?></p>
																		<ul class="d-flex apponitment-types">
																			<li>Video Call</li>
																			<li><span class="badge <?php echo $badge_class; ?>"><?php echo $display_status; ?></span></li>
																		</ul>												
																	</li>
																	<li class="mail-info-patient">
																		<ul>
																			<li><i class="isax isax-sms5"></i><?php echo htmlspecialchars($appointment['doctor_email'] ?? 'N/A'); ?></li>
																			<li><i class="isax isax-call5"></i><?php echo htmlspecialchars($appointment['doctor_phone'] ?? 'N/A'); ?></li>
																		</ul>
																	</li>
																	<li class="appointment-action">
																		<ul>
																			<li>
																				<a href="appointment-detail.php?id=<?php echo (int)$appointment['id']; ?>" title="View"><i class="isax isax-eye4"></i></a>
																			</li>
																			<li>
																				<a href="chat.php?doctor_id=<?php echo (int)$appointment['doctor_id']; ?>&appointment_id=<?php echo (int)$appointment['id']; ?>" title="Chat"><i class="isax isax-messages-25"></i></a>
																			</li>
																			<?php if ($status !== 'cancelled' && $status !== 'completed'): ?>
																			<li>
																				<a href="javascript:void(0);" class="cancel-appointment" data-id="<?php echo (int)$appointment['id']; ?>" title="Cancel"><i class="isax isax-close-circle5"></i></a>
																			</li>
																			<?php endif; ?>
																		</ul>
																		<?php if ($status === 'completed' && !empty($appointment['prescription_path'])): ?>
																		<div class="mt-2 text-center">
																			<a href="<?php echo htmlspecialchars($appointment['prescription_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success btn-prescription btn-block w-100"><i class="isax isax-document-text me-1"></i>View Prescription</a>
																		</div>
																		<?php endif; ?>
																	</li>
																	<li class="appointment-detail-btn">
																		<?php if ($status !== 'cancelled' && $status !== 'completed'): ?>
																			<a href="video-call.php?appointment_id=<?php echo (int)$appointment['id']; ?>" class="btn btn-md btn-primary-gradient"><i class="isax isax-calendar-tick5 me-1"></i>Attend</a>
																		<?php else: ?>
																			<a href="appointment-detail.php?id=<?php echo (int)$appointment['id']; ?>" class="btn btn-md btn-outline-primary">View Details</a>
																		<?php endif; ?>
																	</li>
																</ul>
															</div>
															<!-- /Appointment List -->
														<?php endforeach; ?>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /All Appointments Tab -->
								
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
