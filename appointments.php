<?php
/**
 * Doctor Appointments - TeleRx Bangladesh
 * Dynamic appointments list (upcoming, cancelled, completed) from appointments table.
 */

// Include configuration
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
    // Map specialty from database to speciality for form (database uses 'specialty', form uses 'speciality')
    $doctor['speciality'] = $doctor['specialty'] ?? ($doctor['speciality'] ?? '');
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

    // Fetch doctor's appointments (all), then split by status/date
    $upcoming_appointments = [];
    $cancelled_appointments = [];
    $completed_appointments = [];
    $apt_stmt = $conn->prepare("SELECT id, appointment_number, patient_name, mobile, appointment_date, slot_time, status, notes, created_at, prescription_path FROM appointments WHERE doctor_id = ? ORDER BY appointment_date DESC, slot_time DESC");
    if ($apt_stmt) {
        $apt_stmt->bind_param("i", $doctor_id);
        $apt_stmt->execute();
        $apt_result = $apt_stmt->get_result();
        $today = date('Y-m-d');
        while ($row = $apt_result->fetch_assoc()) {
            $row['appointment_number'] = $row['appointment_number'] ?? ('APT' . str_pad($row['id'], 5, '0', STR_PAD_LEFT));
            if (isset($row['status']) && strtolower($row['status']) === 'cancelled') {
                $cancelled_appointments[] = $row;
            } elseif ($row['appointment_date'] >= $today) {
                $upcoming_appointments[] = $row;
            } else {
                $completed_appointments[] = $row;
            }
        }
        $apt_stmt->close();
    }

    $conn->close();

} catch (Exception $e) {
    error_log("Doctor profile settings error: " . $e->getMessage());
    header('Location: login.php');
    exit;
}

// Split name for form fields
$name_parts = explode(' ', $doctor['name']);
$first_name = $name_parts[0] ?? '';
$last_name = $name_parts[1] ?? '';

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<body>
		<style>
		.appointment-action ul li .btn-sm,
		.appointment-wrap .btn-prescription {
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
                        <?php
                        $current_page = 'appointments.php';
                        include 'doctor-leftside-menu.php';
                        ?>

						</div>
						
						<div class="col-lg-8 col-xl-9">
							<div class="dashboard-header">
								<h3>Appointments</h3>
								<ul class="header-list-btns">
									<li>
										<div class="input-block dash-search-input">
											<input type="text" class="form-control" placeholder="Search">
											<span class="search-icon"><i class="isax isax-search-normal"></i></span>
										</div>
									</li>
									<li>
										<div class="view-icons">
											<a href="appointments.html" class="active"><i class="isax isax-grid-7"></i></a>
										</div>
									</li>
									<li>
										<div class="view-icons">
											<a href="doctor-appointments-grid.html"><i class="fa-solid fa-th"></i></a>
										</div>
									</li>
									<li>
										<div class="view-icons">
											<a href="#"><i class="isax isax-calendar-tick"></i></a>
										</div>
									</li>
								</ul>
							</div>
							<div class="appointment-tab-head">
								<div class="appointment-tabs">
									<ul class="nav nav-pills inner-tab " id="pills-tab" role="tablist">
										<li class="nav-item" role="presentation">
											<button class="nav-link active" id="pills-upcoming-tab" data-bs-toggle="pill" data-bs-target="#pills-upcoming" type="button" role="tab" aria-controls="pills-upcoming" aria-selected="false">Upcoming<span><?php echo count($upcoming_appointments); ?></span></button>
										</li>	
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="pills-cancel-tab" data-bs-toggle="pill" data-bs-target="#pills-cancel" type="button" role="tab" aria-controls="pills-cancel" aria-selected="true">Cancelled<span><?php echo count($cancelled_appointments); ?></span></button>
										</li>
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="pills-complete-tab" data-bs-toggle="pill" data-bs-target="#pills-complete" type="button" role="tab" aria-controls="pills-complete" aria-selected="true">Completed<span><?php echo count($completed_appointments); ?></span></button>
										</li>
									</ul>
								</div>
								<div class="filter-head">
									<div class="position-relative daterange-wraper me-2">
										<div class="input-groupicon calender-input">
											<input type="text" class="form-control  date-range bookingrange" placeholder="From Date - To Date ">
										</div>
										<i class="isax isax-calendar-1"></i>
									</div>
									<div class="form-sorts dropdown">
										<a href="javascript:void(0);" class="dropdown-toggle" id="table-filter"><i class="isax isax-filter me-2"></i>Filter By</a>
										<div class="filter-dropdown-menu">
											<div class="filter-set-view">
												<div class="accordion" id="accordionExample">
													<div class="filter-set-content">
														<div class="filter-set-content-head">
															<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Name<i class="fa-solid fa-chevron-right"></i></a>
														</div>
														<div class="filter-set-contents accordion-collapse collapse show" id="collapseTwo" data-bs-parent="#accordionExample">
															<ul>
																<li>
																	<div class="input-block dash-search-input w-100">
																		<input type="text" class="form-control" placeholder="Search">
																		<span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
																	</div>
																</li>
															</ul>
														</div>
													</div>
													<div class="filter-set-content">
														<div class="filter-set-content-head">
															<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Appointment Type<i class="fa-solid fa-chevron-right"></i></a>
														</div>
														<div class="filter-set-contents accordion-collapse collapse show" id="collapseOne" data-bs-parent="#accordionExample">
															<ul>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox" checked>
																			<span class="checkmarks"></span>
																			<span class="check-title">All Type</span>
																		</label>
																	</div>																
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			<span class="check-title">Video Call</span>
																		</label>
																	</div>																
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			<span class="check-title">Audio Call</span>
																		</label>
																	</div>																
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			<span class="check-title">Chat</span>
																		</label>
																	</div>																
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			<span class="check-title">Direct Visit</span>
																		</label>
																	</div>																
																</li>
															</ul>
														</div>
													</div>												
													<div class="filter-set-content">
														<div class="filter-set-content-head">
															<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Visit Type<i class="fa-solid fa-chevron-right"></i></a>
														</div>
														<div class="filter-set-contents accordion-collapse collapse show" id="collapseThree" data-bs-parent="#accordionExample">
															<ul>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox" checked>
																			<span class="checkmarks"></span>
																			<span class="check-title">All Visit</span>
																		</label>
																	</div>
																	
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			<span class="check-title">General</span>
																		</label>
																	</div>
																	
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			<span class="check-title">Consultation</span>
																		</label>
																	</div>
																	
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			<span class="check-title">Follow-up</span>
																		</label>
																	</div>
																	
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			<span class="check-title">Direct Visit</span>
																		</label>
																	</div>
																	
																</li>
															</ul>
														</div>
													</div>
												</div>
												
												<div class="filter-reset-btns">
													<a href="appointments.html" class="btn btn-light">Reset</a>
													<a href="appointments.html" class="btn btn-primary">Filter Now</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="tab-content appointment-tab-content">
								<div class="tab-pane fade show active" id="pills-upcoming" role="tabpanel" aria-labelledby="pills-upcoming-tab">
									<?php
									$profile_imgs = ['profile-01.jpg', 'profile-02.jpg', 'profile-03.jpg', 'profile-04.jpg', 'profile-05.jpg', 'profile-06.jpg', 'profile-07.jpg', 'profile-08.jpg'];
									foreach ($upcoming_appointments as $idx => $a):
										$apt_date = $a['appointment_date'];
										$slot = $a['slot_time'] ?? '';
										$time_display = $slot ? date('g.i A', strtotime($slot)) : '';
										$date_display = $apt_date ? date('d M Y', strtotime($apt_date)) . ($time_display ? ' ' . $time_display : '') : '';
										$img = 'assets/img/doctors-dashboard/' . ($profile_imgs[$idx % count($profile_imgs)]);
									?>
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="appointment-detail.php?id=<?php echo (int)$a['id']; ?>">
														<img src="<?php echo htmlspecialchars($img); ?>" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#<?php echo htmlspecialchars($a['appointment_number']); ?></p>
														<h6><a href="appointment-detail.php?id=<?php echo (int)$a['id']; ?>"><?php echo htmlspecialchars($a['patient_name'] ?? 'Patient'); ?></a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i><?php echo htmlspecialchars($date_display); ?></p>
												<ul class="d-flex apponitment-types">
													<li>Consultation</li>
													<li>Video Call</li>
												</ul>
											</li>
											<li class="mail-info-patient">
												<ul>
													<li><i class="isax isax-call5"></i><?php echo htmlspecialchars($a['mobile'] ?? '—'); ?></li>
												</ul>
											</li>
											<li class="appointment-action">
												<ul>
													<li>
														<a href="appointment-detail.php?id=<?php echo (int)$a['id']; ?>" title="View"><i class="isax isax-eye4"></i></a>
													</li>
													<li><a href="#" title="Chat"><i class="isax isax-messages-25"></i></a></li>
													<li><a href="#" class="text-danger-icon" title="Cancel"><i class="isax isax-close-circle5"></i></a></li>
												</ul>
												<div class="mt-2 text-center">
													<?php if (!empty($a['prescription_path'])): ?>
														<a href="<?php echo htmlspecialchars($a['prescription_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success btn-prescription w-100"><i class="isax isax-document-text me-1"></i>View Prescription</a>
													<?php else: ?>
														<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary upload-prescription btn-prescription w-100" data-id="<?php echo (int)$a['id']; ?>"><i class="isax isax-export me-1"></i>Upload Prescription</a>
													<?php endif; ?>
												</div>
											</li>
											<li class="appointment-start">
												<a href="video-call.php?appointment_id=<?php echo (int)$a['id']; ?>" class="start-link">Start Now</a>
											</li>
										</ul>
									</div>
									<?php endforeach; ?>
									<?php if (empty($upcoming_appointments)): ?>
									<div class="appointment-wrap"><p class="text-muted mb-0 p-3">No upcoming appointments.</p></div>
									<?php endif; ?>
								</div>
								<div class="tab-pane fade" id="pills-cancel" role="tabpanel" aria-labelledby="pills-cancel-tab">
									<?php
									foreach ($cancelled_appointments as $idx => $a):
										$apt_date = $a['appointment_date'];
										$slot = $a['slot_time'] ?? '';
										$time_display = $slot ? date('g.i A', strtotime($slot)) : '';
										$date_display = $apt_date ? date('d M Y', strtotime($apt_date)) . ($time_display ? ' ' . $time_display : '') : '';
										$img = 'assets/img/doctors-dashboard/' . ($profile_imgs[$idx % count($profile_imgs)]);
									?>
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="appointment-detail.php?id=<?php echo (int)$a['id']; ?>">
														<img src="<?php echo htmlspecialchars($img); ?>" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#<?php echo htmlspecialchars($a['appointment_number']); ?></p>
														<h6><a href="appointment-detail.php?id=<?php echo (int)$a['id']; ?>"><?php echo htmlspecialchars($a['patient_name'] ?? 'Patient'); ?></a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i><?php echo htmlspecialchars($date_display); ?></p>
												<ul class="d-flex apponitment-types">
													<li>Consultation</li>
													<li>Video Call</li>
												</ul>
											</li>
											<li class="appointment-detail-btn">
												<a href="appointment-detail.php?id=<?php echo (int)$a['id']; ?>" class="start-link">View Details</a>
												<?php if (!empty($a['prescription_path'])): ?>
													<a href="<?php echo htmlspecialchars($a['prescription_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success btn-prescription ms-2"><i class="isax isax-document-text me-1"></i>View Prescription</a>
												<?php else: ?>
													<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary upload-prescription btn-prescription ms-2" data-id="<?php echo (int)$a['id']; ?>"><i class="isax isax-export me-1"></i>Upload Prescription</a>
												<?php endif; ?>
											</li>
										</ul>
									</div>
									<?php endforeach; ?>
									<?php if (empty($cancelled_appointments)): ?>
									<div class="appointment-wrap"><p class="text-muted mb-0 p-3">No cancelled appointments.</p></div>
									<?php endif; ?>
								</div>
								<div class="tab-pane fade" id="pills-complete" role="tabpanel" aria-labelledby="pills-complete-tab">
									<?php
									foreach ($completed_appointments as $idx => $a):
										$apt_date = $a['appointment_date'];
										$slot = $a['slot_time'] ?? '';
										$time_display = $slot ? date('g.i A', strtotime($slot)) : '';
										$date_display = $apt_date ? date('d M Y', strtotime($apt_date)) . ($time_display ? ' ' . $time_display : '') : '';
										$img = 'assets/img/doctors-dashboard/' . ($profile_imgs[$idx % count($profile_imgs)]);
									?>
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="appointment-detail.php?id=<?php echo (int)$a['id']; ?>">
														<img src="<?php echo htmlspecialchars($img); ?>" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#<?php echo htmlspecialchars($a['appointment_number']); ?></p>
														<h6><a href="appointment-detail.php?id=<?php echo (int)$a['id']; ?>"><?php echo htmlspecialchars($a['patient_name'] ?? 'Patient'); ?></a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i><?php echo htmlspecialchars($date_display); ?></p>
												<ul class="d-flex apponitment-types">
													<li>Consultation</li>
													<li>Video Call</li>
												</ul>
											</li>
											<li class="appointment-detail-btn">
												<a href="appointment-detail.php?id=<?php echo (int)$a['id']; ?>" class="start-link">View Details</a>
												<?php if (!empty($a['prescription_path'])): ?>
													<a href="<?php echo htmlspecialchars($a['prescription_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success btn-prescription ms-2"><i class="isax isax-document-text me-1"></i>View Prescription</a>
												<?php else: ?>
													<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary upload-prescription btn-prescription ms-2" data-id="<?php echo (int)$a['id']; ?>"><i class="isax isax-export me-1"></i>Upload Prescription</a>
												<?php endif; ?>
											</li>
										</ul>
									</div>
									<?php endforeach; ?>
									<?php if (empty($completed_appointments)): ?>
									<div class="appointment-wrap"><p class="text-muted mb-0 p-3">No completed appointments.</p></div>
									<?php endif; ?>

								</div>
							</div>
						</div>
					</div>

				</div>

			</div>		
			<!-- /Page Content -->

		</div>
		<!-- /Main Wrapper -->
		
		<!-- Appointment Details Modal -->
		<div class="modal fade custom-modal" id="appt_details">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Appointment Details</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
						</button>
					</div>
					<div class="modal-body">
						<ul class="info-details">
							<li>
								<div class="details-header">
									<div class="row">
										<div class="col-md-6">
											<span class="title">#APT0001</span>
											<span class="text">21 Oct 2023 10:00 AM</span>
										</div>
										<div class="col-md-6">
											<div class="text-end">
												<button type="button" class="btn bg-success-light btn-sm" id="topup_status">Completed</button>
											</div>
										</div>
									</div>
								</div>
							</li>
							<li>
								<span class="title">Status:</span>
								<span class="text">Completed</span>
							</li>
							<li>
								<span class="title">Confirm Date:</span>
								<span class="text">29 Jun 2023</span>
							</li>
							<li>
								<span class="title">Paid Amount</span>
								<span class="text">$450</span>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- /Appointment Details Modal -->
	  
		<!-- jQuery -->
		<script src="assets/js/jquery-3.7.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="assets/js/bootstrap.bundle.min.js"></script>

		<!-- select JS -->
		<script src="assets/plugins/select2/js/select2.min.js"></script>

		<!-- Daterangepikcer JS -->
		<script src="assets/js/moment.min.js"></script>
		<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
		
		<!-- Sticky Sidebar JS -->
        <script src="assets/plugins/theia-sticky-sidebar/ResizeSensor.js"></script>
        <script src="assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/script.js"></script>

		<!-- Prescription Upload Handler -->
		<script>
		$(document).ready(function() {
			// Create a hidden file input
			var $fileInput = $('<input type="file" id="prescription_input" style="display:none;" accept=".pdf,.jpg,.jpeg,.png">');
			$('body').append($fileInput);
			var currentAppointmentId = null;

			$('.upload-prescription').on('click', function() {
				currentAppointmentId = $(this).data('id');
				$fileInput.trigger('click');
			});

			$fileInput.on('change', function() {
				var file = this.files[0];
				if (!file || !currentAppointmentId) return;

				var formData = new FormData();
				formData.append('prescription_file', file);
				formData.append('appointment_id', currentAppointmentId);

				// Show loading or disable button
				var $btn = $('.upload-prescription[data-id="' + currentAppointmentId + '"]');
				$btn.html('<i class="fa-solid fa-spinner fa-spin"></i>');

				$.ajax({
					url: 'php/upload-prescription.php',
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							// Check if it's in the action list (Upcoming) or standard list (Cancelled/Completed)
							if ($btn.closest('.appointment-action').length) {
								$btn.replaceWith('<a href="' + response.prescription_path + '" target="_blank" class="btn btn-sm btn-outline-success btn-prescription"><i class="isax isax-document-text me-1"></i>View Prescription</a>');
							} else {
								$btn.replaceWith('<a href="' + response.prescription_path + '" target="_blank" class="btn btn-sm btn-outline-success btn-prescription ms-2"><i class="isax isax-document-text me-1"></i>View Prescription</a>');
							}
							alert('Prescription uploaded successfully!');
						} else {
							alert('Upload failed: ' + response.message);
							$btn.html('<i class="isax isax-export me-1"></i>Upload Prescription');
						}
					},
					error: function() {
						alert('An error occurred during upload.');
						$btn.html('<i class="isax isax-export me-1"></i>Upload Prescription');
					},
					complete: function() {
						$fileInput.val('');
					}
				});
			});
		});
		</script>
		
	</body>
</html>

<?php include 'footer.php'; ?>
