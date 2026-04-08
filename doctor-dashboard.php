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

    $appointments = [];
    $upcoming_appointment = null;
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
									<?php if ($upcoming_appointment): ?>
									<div class="upcoming-patient-info">
										<div class="info-details">
											<span class="img-avatar"><img src="<?php echo htmlspecialchars($upcoming_appointment['profile_image']); ?>" alt=""></span>
											<div class="name-info">
												<span>#APT<?php echo str_pad($upcoming_appointment['id'], 5, '0', STR_PAD_LEFT); ?></span>
												<h6><?php echo htmlspecialchars($upcoming_appointment['display_name']); ?></h6>
											</div>
										</div>
										<div class="date-details">
											<span><?php echo htmlspecialchars($upcoming_appointment['status'] ?: 'Booked'); ?></span>
											<h6><?php echo date('M j, Y', strtotime($upcoming_appointment['appointment_date'])); ?>, <?php echo date('g:i A', strtotime($upcoming_appointment['slot_time'])); ?></h6>
										</div>
										<div class="circle-bg">
											<img src="assets/img/bg/dashboard-circle-bg.png" alt="Img">
										</div>
									</div>
									<div class="appointment-card-footer">
										<h5><i class="fa-solid fa-calendar-check"></i>Appointment</h5>
										<div class="btn-appointments">
											<a href="appointments.php" class="btn">View Details</a>
										</div>
									</div>
									<?php else: ?>
									<div class="upcoming-patient-info">
										<p class="text-muted mb-0 p-3">No upcoming appointments</p>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<!-- Appointments Today, Patients Today, Total Patient cards commented out
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
						-->
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
												<?php if (empty($appointments)): ?>
												<tr>
													<td colspan="3" class="text-center py-4 text-muted">No appointments yet</td>
												</tr>
												<?php else: foreach ($appointments as $apt): ?>
												<tr>
													<td>
														<div class="patient-info-profile">
															<a href="appointments.php" class="table-avatar">
																<img src="<?php echo htmlspecialchars($apt['profile_image']); ?>" alt="">
															</a>
															<div class="patient-name-info">
																<span>#APT<?php echo str_pad($apt['id'], 5, '0', STR_PAD_LEFT); ?></span>
																<h5><a href="appointments.php"><?php echo htmlspecialchars($apt['display_name']); ?></a></h5>
															</div>
														</div>
													</td>
													<td>
														<div class="appointment-date-created">
															<h6><?php echo date('j M Y', strtotime($apt['appointment_date'])); ?> <?php echo date('g:i A', strtotime($apt['slot_time'])); ?></h6>
															<span class="badge table-badge"><?php echo htmlspecialchars($apt['status'] ?: 'Booked'); ?></span>
														</div>
													</td>
													<td>
														<div class="apponiment-actions d-flex align-items-center">
															<a href="video-call.php?appointment_id=<?php echo (int)$apt['id']; ?>" class="text-info-icon me-2" title="Video Call"><i class="fa-solid fa-video"></i></a>
															<a href="appointments.php" class="text-success-icon me-2" title="View"><i class="fa-solid fa-eye"></i></a>
														</div>
													</td>
												</tr>
												<?php endforeach; endif; ?>
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
   
</body>
</html>

<?php include 'footer.php'; ?>
<?php include 'footer.php'; ?>
<?php include 'footer.php'; ?>
<?php include 'footer.php'; ?>
<?php include 'footer.php'; ?>
<?php include 'footer.php'; ?>