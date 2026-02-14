<?php
/**
 * Doctor Profile Settings - TeleRx Bangladesh
 * Dynamic profile settings page with all forms in tabs
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
											<button class="nav-link active" id="pills-upcoming-tab" data-bs-toggle="pill" data-bs-target="#pills-upcoming" type="button" role="tab" aria-controls="pills-upcoming" aria-selected="false">Upcoming<span>21</span></button>
										</li>	
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="pills-cancel-tab" data-bs-toggle="pill" data-bs-target="#pills-cancel" type="button" role="tab" aria-controls="pills-cancel" aria-selected="true">Cancelled<span>16</span></button>
										</li>
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="pills-complete-tab" data-bs-toggle="pill" data-bs-target="#pills-complete" type="button" role="tab" aria-controls="pills-complete" aria-selected="true">Completed<span>214</span></button>
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
									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-upcoming-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-01.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0001</p>
														<h6><a href="doctor-upcoming-appointment.html">Adrian</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>11 Nov 2024 10.45 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Video Call</li>
												</ul>
												
											</li>
											<li class="mail-info-patient">
												<ul>
													<li><i class="isax isax-sms5"></i>adran@example.com</li>
													<li><i class="isax isax-call5"></i>+1 504 368 6874</li>
												</ul>
											</li>
											<li class="appointment-action">
												<ul>
													<li>
														<a href="doctor-upcoming-appointment.html"><i class="isax isax-eye4"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-messages-25"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-close-circle5"></i></a>
													</li>
												</ul>
											</li>
											<li class="appointment-start">
												<a href="doctor-appointment-start.html" class="start-link">Start Now</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-upcoming-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-02.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0002</p>
														<h6><a href="doctor-upcoming-appointment.html">Kelly</a><span class="badge new-tag">New</span></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>05 Nov 2024 11.50 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Audio Call</li>
												</ul>
												
											</li>
											<li class="mail-info-patient">
												<ul>
													<li><i class="isax isax-sms5"></i>kelly@example.com</li>
													<li><i class="isax isax-call5"></i> +1 832 891 8403</li>
												</ul>
											</li>
											<li class="appointment-action">
												<ul>
													<li>
														<a href="doctor-upcoming-appointment.html"><i class="isax isax-eye4"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-messages-25"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-close-circle5"></i></a>
													</li>
												</ul>
											</li>
											<li class="appointment-start">
												<a href="doctor-appointment-start.html" class="start-link">Start Now</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-upcoming-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-03.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0003</p>
														<h6><a href="doctor-upcoming-appointment.html">Samuel</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>27 Oct 2024 09.30 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Video Call</li>
												</ul>
												
											</li>
											<li class="mail-info-patient">
												<ul>
													<li><i class="isax isax-sms5"></i>samuel@example.com</li>
													<li><i class="isax isax-call5"></i>  +1 749 104 6291</li>
												</ul>
											</li>
											<li class="appointment-action">
												<ul>
													<li>
														<a href="doctor-upcoming-appointment.html"><i class="isax isax-eye4"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-messages-25"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-close-circle5"></i></a>
													</li>
												</ul>
											</li>
											<li class="appointment-start">
												<a href="doctor-appointment-start.html" class="start-link">Start Now</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-upcoming-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-04.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0004</p>
														<h6><a href="doctor-upcoming-appointment.html">Catherine</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>18 Oct 2024 12.20 PM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Direct Visit</li>
												</ul>
												
											</li>
											<li class="mail-info-patient">
												<ul>
													<li><i class="isax isax-sms5"></i>catherine@example.com</li>
													<li><i class="isax isax-call5"></i>+1 584 920 7183</li>
												</ul>
											</li>
											<li class="appointment-action">
												<ul>
													<li>
														<a href="doctor-upcoming-appointment.html"><i class="isax isax-eye4"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-messages-25"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-close-circle5"></i></a>
													</li>
												</ul>
											</li>
											<li class="appointment-start">
												<a href="doctor-appointment-start.html" class="start-link">Start Now</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-upcoming-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-05.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0005</p>
														<h6><a href="doctor-upcoming-appointment.html">Robert</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>10 Oct 2024 11.30 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Chat</li>
												</ul>
												
											</li>
											<li class="mail-info-patient">
												<ul>
													<li><i class="isax isax-sms5"></i>robert@example.com</li>
													<li><i class="isax isax-call5"></i> +1 059 327 6729</li>
												</ul>
											</li>
											<li class="appointment-action">
												<ul>
													<li>
														<a href="doctor-upcoming-appointment.html"><i class="isax isax-eye4"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-messages-25"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-close-circle5"></i></a>
													</li>
												</ul>
											</li>
											<li class="appointment-start">
												<a href="doctor-appointment-start.html" class="start-link">Start Now</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-upcoming-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-06.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0006</p>
														<h6><a href="doctor-upcoming-appointment.html">Anderea</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>26 Sep 2024 10.20 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Chat</li>
												</ul>
												
											</li>
											<li class="mail-info-patient">
												<ul>
													<li><i class="isax isax-sms5"></i>anderea@example.com</li>
													<li><i class="isax isax-call5"></i>  +1 278 402 7103</li>
												</ul>
											</li>
											<li class="appointment-action">
												<ul>
													<li>
														<a href="doctor-upcoming-appointment.html"><i class="isax isax-eye4"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-messages-25"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-close-circle5"></i></a>
													</li>
												</ul>
											</li>
											<li class="appointment-start">
												<a href="doctor-appointment-start.html" class="start-link">Start Now</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-upcoming-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-07.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0007</p>
														<h6><a href="doctor-upcoming-appointment.html">Peter</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>14 Sep 2024 08.10 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Chat</li>
												</ul>
												
											</li>
											<li class="mail-info-patient">
												<ul>
													<li><i class="isax isax-sms5"></i>peter@example.com</li>
													<li><i class="isax isax-call5"></i> +1 638 278 0249</li>
												</ul>
											</li>
											<li class="appointment-action">
												<ul>
													<li>
														<a href="doctor-upcoming-appointment.html"><i class="isax isax-eye4"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-messages-25"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-close-circle5"></i></a>
													</li>
												</ul>
											</li>
											<li class="appointment-start">
												<a href="doctor-appointment-start.html" class="start-link">Start Now</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-upcoming-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-08.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0008</p>
														<h6><a href="doctor-upcoming-appointment.html">Emily</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>03 Sep 2024 06.00 PM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Video Call</li>
												</ul>
												
											</li>
											<li class="mail-info-patient">
												<ul>
													<li><i class="isax isax-sms5"></i>emily@example.com</li>
													<li><i class="isax isax-call5"></i>  +1 261 039 1873</li>
												</ul>
											</li>
											<li class="appointment-action">
												<ul>
													<li>
														<a href="doctor-upcoming-appointment.html"><i class="isax isax-eye4"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-messages-25"></i></a>
													</li>
													<li>
														<a href="#"><i class="isax isax-close-circle5"></i></a>
													</li>
												</ul>
											</li>
											<li class="appointment-start">
												<a href="doctor-appointment-start.html" class="start-link">Start Now</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Pagination -->
									<div class="pagination dashboard-pagination">
										<ul>
											<li>
												<a href="#" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
											</li>
											<li>
												<a href="#" class="page-link">1</a>
											</li>
											<li>
												<a href="#" class="page-link active">2</a>
											</li>
											<li>
												<a href="#" class="page-link">3</a>
											</li>
											<li>
												<a href="#" class="page-link">4</a>
											</li>
											<li>
												<a href="#" class="page-link">...</a>
											</li>
											<li>
												<a href="#" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
											</li>
										</ul>
									</div>
									<!-- /Pagination -->

								</div>
								<div class="tab-pane fade" id="pills-cancel" role="tabpanel" aria-labelledby="pills-cancel-tab">
									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-cancelled-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-01.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0001</p>
														<h6><a href="doctor-cancelled-appointment.html">Adrian</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>11 Nov 2024 10.45 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Video Call</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-cancelled-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-cancelled-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-02.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0002</p>
														<h6><a href="doctor-cancelled-appointment.html">Kelly</a><span class="badge new-tag">New</span></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>05 Nov 2024 11.50 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Audio Call</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-cancelled-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-cancelled-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-03.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0003</p>
														<h6><a href="doctor-cancelled-appointment.html">Samuel</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>27 Oct 2024 09.30 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Video Call</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-cancelled-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-cancelled-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-04.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0004</p>
														<h6><a href="doctor-cancelled-appointment.html">Catherine</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>18 Oct 2024 12.20 PM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Direct Visit</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-cancelled-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-cancelled-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-05.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0005</p>
														<h6><a href="doctor-cancelled-appointment.html">Robert</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>10 Oct 2024 11.30 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Chat</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-cancelled-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-cancelled-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-06.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0006</p>
														<h6><a href="doctor-cancelled-appointment.html">Anderea</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>26 Sep 2024 10.20 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Chat</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-cancelled-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-cancelled-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-07.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0007</p>
														<h6><a href="doctor-cancelled-appointment.html">Peter</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>14 Sep 2024 08.10 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Chat</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-cancelled-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-cancelled-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-08.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0008</p>
														<h6><a href="doctor-cancelled-appointment.html">Emily</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>03 Sep 2024 06.00 PM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Video Call</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-cancelled-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Pagination -->
									<div class="pagination dashboard-pagination">
										<ul>
											<li>
												<a href="#" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
											</li>
											<li>
												<a href="#" class="page-link">1</a>
											</li>
											<li>
												<a href="#" class="page-link active">2</a>
											</li>
											<li>
												<a href="#" class="page-link">3</a>
											</li>
											<li>
												<a href="#" class="page-link">4</a>
											</li>
											<li>
												<a href="#" class="page-link">...</a>
											</li>
											<li>
												<a href="#" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
											</li>
										</ul>
									</div>
									<!-- /Pagination -->
								</div>
								<div class="tab-pane fade" id="pills-complete" role="tabpanel" aria-labelledby="pills-complete-tab">
									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-completed-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-01.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0001</p>
														<h6><a href="doctor-completed-appointment.html">Adrian</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>11 Nov 2024 10.45 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Video Call</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-completed-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-completed-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-02.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0002</p>
														<h6><a href="doctor-completed-appointment.html">Kelly</a><span class="badge new-tag">New</span></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>05 Nov 2024 11.50 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Audio Call</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-completed-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-completed-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-03.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0003</p>
														<h6><a href="doctor-completed-appointment.html">Samuel</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>27 Oct 2024 09.30 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Video Call</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-completed-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-completed-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-04.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0004</p>
														<h6><a href="doctor-completed-appointment.html">Catherine</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>18 Oct 2024 12.20 PM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Direct Visit</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-completed-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-completed-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-05.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0005</p>
														<h6><a href="doctor-completed-appointment.html">Robert</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>10 Oct 2024 11.30 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Chat</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-completed-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-completed-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-06.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0006</p>
														<h6><a href="doctor-completed-appointment.html">Anderea</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>26 Sep 2024 10.20 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Chat</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-completed-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-completed-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-07.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0007</p>
														<h6><a href="doctor-completed-appointment.html">Peter</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>14 Sep 2024 08.10 AM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Chat</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-completed-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Appointment List -->
									<div class="appointment-wrap">
										<ul>
											<li>
												<div class="patinet-information">
													<a href="doctor-completed-appointment.html">
														<img src="assets/img/doctors-dashboard/profile-08.jpg" alt="User Image">
													</a>
													<div class="patient-info">
														<p>#Apt0008</p>
														<h6><a href="doctor-completed-appointment.html">Emily</a></h6>
													</div>
												</div>
											</li>
											<li class="appointment-info">
												<p><i class="isax isax-clock5"></i>03 Sep 2024 06.00 PM</p>
												<ul class="d-flex apponitment-types">
													<li>General Visit</li>
													<li>Video Call</li>
												</ul>
												
											</li>
											<li class="appointment-detail-btn">
												<a href="doctor-completed-appointment.html" class="start-link">View Details</a>
											</li>
										</ul>
									</div>
									<!-- /Appointment List -->

									<!-- Pagination -->
									<div class="pagination dashboard-pagination">
										<ul>
											<li>
												<a href="#" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
											</li>
											<li>
												<a href="#" class="page-link">1</a>
											</li>
											<li>
												<a href="#" class="page-link active">2</a>
											</li>
											<li>
												<a href="#" class="page-link">3</a>
											</li>
											<li>
												<a href="#" class="page-link">4</a>
											</li>
											<li>
												<a href="#" class="page-link">...</a>
											</li>
											<li>
												<a href="#" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
											</li>
										</ul>
									</div>
									<!-- /Pagination -->
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
		
	</body>
</html>