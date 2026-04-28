<?php
session_start();

// Allow booking by patient or Special TID user
$is_patient = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && (($_SESSION['user_type'] ?? '') === 'patient');
$is_special_tid = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && (($_SESSION['user_type'] ?? '') === 'special_tid');
if (!$is_patient && !$is_special_tid) {
	$booking_doctor_id = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : (isset($_GET['id']) ? (int) $_GET['id'] : 0);
	$redirectUrl = 'registration.php';
	if ($booking_doctor_id > 0) {
		$redirectUrl .= '?redirect=' . urlencode('booking.php?doctor_id=' . $booking_doctor_id);
	}
	header('Location: ' . $redirectUrl);
	exit;
}

include 'header.php';

$booking_doctor_id = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : (isset($_GET['id']) ? (int) $_GET['id'] : 0);
$doctor = null;
$period_labels = ['morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening', 'night' => 'Night'];

if ($booking_doctor_id > 0) {
	try {
		require_once __DIR__ . '/php/config.php';
		$conn = getDBConnection();
		$stmt = $conn->prepare("SELECT d.*, dp.specialty, dp.consultation_fee, dp.profile_image, dp.district, dp.city, dp.state FROM doctors d LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id WHERE d.id = ?");
		$stmt->bind_param("i", $booking_doctor_id);
		$stmt->execute();
		$res = $stmt->get_result();
		if ($res->num_rows > 0) {
			$doctor = $res->fetch_assoc();
			$doctor['profile_image'] = $doctor['profile_image'] ?? 'assets/img/clients/client-15.jpg';
			$doctor['specialty'] = $doctor['specialty'] ?? 'General Physician';
			$doctor['consultation_fee'] = (float) ($doctor['consultation_fee'] ?? 0);
			$location_parts = array_filter([$doctor['district'] ?? '', $doctor['city'] ?? '', $doctor['state'] ?? '']);
			$doctor['location'] = !empty($location_parts) ? implode(', ', $location_parts) : '';
		}
		$res->free();
		$stmt->close();
		$conn->close();
	} catch (Exception $e) {
		error_log('booking.php: ' . $e->getMessage());
	}
}

function format_slot_time_12($t)
{
	$p = explode(':', $t);
	$h = (int) ($p[0] ?? 0);
	$m = (int) ($p[1] ?? 0);
	$ampm = $h >= 12 ? 'PM' : 'AM';
	$h12 = $h % 12 ?: 12;
	return $h12 . ':' . str_pad((string) $m, 2, '0', STR_PAD_LEFT) . ' ' . $ampm;
}
?>

<body>
	<!-- Terms -->
	<div class="doctor-content">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 mx-auto">
					<?php
					$booking_steps = [
						['num' => 1, 'label' => 'Details'],
						['num' => 2, 'label' => 'Payment'],
						['num' => 3, 'label' => 'Confirmation'],
					];
					?>
					<div class="booking-wizard">
						<ul class="form-wizard-steps d-sm-flex align-items-center justify-content-center"
							id="progressbar2">
							<?php foreach ($booking_steps as $i => $step): ?>
								<li class="<?php echo $i === 0 ? 'progress-active' : ''; ?>">
									<div class="profile-step">
										<span class="multi-steps"><?php echo (int) $step['num']; ?></span>
										<div class="step-section">
											<h6><?php echo htmlspecialchars($step['label']); ?></h6>
										</div>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="booking-widget multistep-form mb-5">
						<fieldset id="first">
							<div class="card booking-card mb-0">
								<div class="card-header">
									<div class="booking-header pb-0">
										<div class="card mb-0">
											<div class="card-body">
												<div
													class="d-flex align-items-center flex-wrap rpw-gap-2 flex-wrap row-gap-2">
													<?php if ($doctor): ?>
														<span
															class="avatar avatar-xxxl avatar-rounded me-2 flex-shrink-0"><img
																src="<?php echo htmlspecialchars($doctor['profile_image']); ?>"
																alt=""></span>
														<div>
															<h4 class="mb-1">
																<?php echo htmlspecialchars($doctor['name']); ?> <span
																	class="badge bg-orange fs-12"><i
																		class="fa-solid fa-star me-1"></i>5.0</span></h4>
															<p class="text-indigo mb-3 fw-medium">
																<?php echo htmlspecialchars($doctor['specialty']); ?></p>
															<p class="mb-0"><i
																	class="isax isax-location me-2"></i><?php echo htmlspecialchars($doctor['location'] ?: '—'); ?>
															</p>
														</div>
													<?php else: ?>
														<div>
															<h4 class="mb-1">Select a doctor</h4>
															<p class="mb-0 text-muted">Please choose a doctor from <a
																	href="doctors.php">Doctor</a> to book an appointment.
															</p>
														</div>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body booking-body">
									<div class="card mb-0">
										<div class="card-body pb-0">
											<div class="row">
												<div class="col-lg-5">
													<div class="card">
														<div class="card-body p-2 pt-3">
															<div id="datetimepickershow"></div>
														</div>
													</div>
												</div>
												<div class="col-lg-7">
													<div class="card booking-wizard-slots">
														<div class="card-body">
															<?php if (!$doctor): ?>
																<p class="text-muted mb-0">Select a doctor to see available
																	slots.</p>
															<?php else: ?>
																<input type="hidden" id="booking_doctor_id"
																	value="<?php echo (int) $booking_doctor_id; ?>">
																<input type="hidden" id="booking_appointment_date" value="">
																<input type="hidden" id="booking_slot_time" value="">
																<p class="text-muted small mb-2">Select a date on the left,
																	then choose a time slot below.</p>
																<div id="booking-slots-container">
																	<p class="text-muted mb-0">Select a date to see
																		available slots.</p>
																</div>
															<?php endif; ?>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body booking-body">
									<div class="card mb-0">
										<div class="card-body pb-1">
											<div class="row">
												<div class="col-lg-6 col-md-6">
													<div class="mb-3">
														<label class="form-label">Full Name</label>
														<input type="text" class="form-control" name="booking_full_name"
															id="booking_full_name" required>
													</div>
												</div>
												<div class="col-lg-6 col-md-6">
													<div class="mb-3">
														<label class="form-label">Mobile Number</label>
														<input type="text" class="form-control" name="booking_mobile"
															id="booking_mobile" required>
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Age</label>
														<input type="text" class="form-control" name="booking_age"
															id="booking_age">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Weight</label>
														<input type="text" class="form-control" name="booking_weight"
															id="booking_weight" placeholder="Kg">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Body Temperature</label>
														<input type="text" class="form-control"
															name="booking_body_temperature"
															id="booking_body_temperature">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Blood Pressure (BP)</label>
														<input type="text" class="form-control"
															name="booking_blood_pressure" id="booking_blood_pressure">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Pulse</label>
														<input type="text" class="form-control" name="booking_pulse"
															id="booking_pulse">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">SpO<sub>2</sub></label>
														<input type="text" class="form-control" name="booking_spo2"
															id="booking_spo2">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">RBS/FBS</label>
														<input type="text" class="form-control" name="booking_rbs_fbs"
															id="booking_rbs_fbs">
													</div>
												</div>
												<div class="col-lg-12">
													<div class="mb-3">
														<label class="form-label">Symptoms in Details</label>
														<textarea class="form-control" rows="3" name="booking_symptoms"
															id="booking_symptoms"></textarea>
													</div>
												</div>
												<div class="col-lg-6">
													<div class="mb-3">
														<label class="form-label">Attachment</label>
														<input type="file" class="form-control"
															name="booking_attachment" id="booking_attachment"
															accept=".pdf,.jpg,.jpeg,.png">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
										<a href="javascript:void(0);"
											class="btn btn-md btn-dark inline-flex align-items-center rounded-pill">
											<i class="isax isax-arrow-left-2 me-1"></i>
											Back
										</a>
										<a href="javascript:void(0);"
											class="btn btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill">
											Add Basic Info
											<i class="isax isax-arrow-right-3 ms-1"></i>
										</a>
									</div>
								</div>
							</div>
						</fieldset>
						<fieldset>
							<div class="card booking-card mb-0">
								<div class="card-header">
									<div class="booking-header pb-0">
										<div class="card mb-0">
											<div class="card-body">
												<div
													class="d-flex align-items-center flex-wrap rpw-gap-2 mb-4 flex-wrap row-gap-2">
													<?php if ($doctor): ?>
														<span
															class="avatar avatar-xxxl avatar-rounded me-2 flex-shrink-0"><img
																src="<?php echo htmlspecialchars($doctor['profile_image']); ?>"
																alt=""></span>
														<div>
															<h4 class="mb-1">
																<?php echo htmlspecialchars($doctor['name']); ?> <span
																	class="badge bg-orange fs-12"><i
																		class="fa-solid fa-star me-1"></i>5.0</span></h4>
															<p class="text-indigo mb-3 fw-medium">
																<?php echo htmlspecialchars($doctor['specialty']); ?></p>
															<p class="mb-0"><i
																	class="isax isax-location me-2"></i><?php echo htmlspecialchars($doctor['location'] ?: '—'); ?>
															</p>
														</div>
													<?php else: ?>
														<span
															class="avatar avatar-xxxl avatar-rounded me-2 flex-shrink-0"><img
																src="assets/img/clients/client-15.jpg" alt=""></span>
														<div>
															<h4 class="mb-1">Select a doctor</h4>
															<p class="text-muted mb-0">Complete booking from the first step
																with a selected doctor.</p>
														</div>
													<?php endif; ?>
												</div>
												<h6 id="booking-info-heading" class="mb-2" style="text-align: center;">
													Check Your Booking Info</h6>
												<div class="row gx-2 gy-3" id="booking-summary">
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Date & Time</h6>
															<p class="mb-0" id="summary_date_time">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Full Name</h6>
															<p class="mb-0" id="summary_full_name">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Mobile Number</h6>
															<p class="mb-0" id="summary_mobile">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Age</h6>
															<p class="mb-0" id="summary_age">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Weight</h6>
															<p class="mb-0" id="summary_weight">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Body Temperature</h6>
															<p class="mb-0" id="summary_body_temperature">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Blood Pressure (BP)</h6>
															<p class="mb-0" id="summary_blood_pressure">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Pulse</h6>
															<p class="mb-0" id="summary_pulse">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">SpO<sub>2</sub></h6>
															<p class="mb-0" id="summary_spo2">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">RBS/FBS</h6>
															<p class="mb-0" id="summary_rbs_fbs">—</p>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Attachment</h6>
															<p class="mb-0" id="summary_attachment">—</p>
														</div>
													</div>
													<div class="col-lg-6 col-sm-6">
														<div>
															<h6 class="fs-14 fw-medium mb-1">Symptoms / Details</h6>
															<p class="mb-0" id="summary_symptoms">—</p>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body booking-body">
									<div class="row">
										<div class="col-lg-6 d-flex">
											<div class="card flex-fill mb-3 mb-lg-0">
												<div class="card-body">
													<h6 class="mb-3">Payment Method</h6>
													<div class="payment-tabs">
														<ul class="nav nav-pills mb-3 row" id="pills-tab"
															role="tablist">
															<li class="nav-item col-sm-6" role="presentation">
																<button class="nav-link active" id="pills-home-tab"
																	data-bs-toggle="pill" data-bs-target="#pills-home"
																	type="button" role="tab">
																	<img src="assets/img/icons/payment-icon-01.svg"
																		class="me-2" alt="">
																</button>
															</li>
															<li class="nav-item col-sm-6" role="presentation">
																<button class="nav-link" id="pills-profile-tab"
																	data-bs-toggle="pill"
																	data-bs-target="#pills-profile" type="button"
																	role="tab">
																	<img src="assets/img/icons/payment-icon-02.svg"
																		class="me-2" alt="">
																</button>
															</li>
														</ul>
														<div class="tab-content" id="pills-tabContent">
															<div class="tab-pane fade show active" id="pills-home"
																role="tabpanel">
																<div class="mb-3">
																	<label class="form-label">Automatically Redirect to
																		bKash Payment</label>
																</div>
															</div>
															<div class="tab-pane fade" id="pills-profile"
																role="tabpanel">
																<div>
																	<label class="form-label">Tearms &
																		Conditions</label>
																	<p>1. Monthly two (2) Times Applicable using one TID
																	</p>
																	<p>2. Fake NID strickly not allowed</p>
																</div>
																<div class="mb-3">
																	<label class="form-label">TeleRx ID (TID) <span
																			class="text-danger">*</span></label>
																	<div class="input-group">
																		<input type="text" class="form-control"
																			name="welfare_tid" id="welfare_tid"
																			placeholder="Enter TID">
																		<button class="btn btn-primary" type="button"
																			id="btn_check_welfare">Check</button>
																	</div>
																	<div id="welfare_status" class="mt-2"
																		style="display:none; font-size: 13px; font-weight: 500;">
																	</div>
																</div>
																<div class="mb-3">
																	<label class="form-label">National ID Number</label>
																	<div class="position-relative input-icon">
																		<input type="text" class="form-control">
																		<span><i class="isax isax-user"></i></span>
																	</div>
																</div>
																<div>
																	<div class="mb-3">
																		<label class="form-label">Upload Your
																			NID</label>
																		<input type="file" class="form-control">
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="col-lg-6 d-flex">
											<div class="card flex-fill mb-0">
												<div class="card-body">
													<?php
													$consultation_fee = $doctor ? (float) ($doctor['consultation_fee'] ?? 0) : 0;
													$tax = 0;
													$discount = 0;
													$total_fee = $consultation_fee + $tax - $discount;
													?>
													<div>
														<h6 class="mb-3">Payment Info</h6>
														<div
															class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between mb-2">
															<p class="mb-0">Doctor Fee</p>
															<span
																class="fw-medium d-block"><?php echo number_format($consultation_fee, 0); ?>/-</span>
														</div>
														<div
															class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between mb-2">
															<p class="mb-0">Tax</p>
															<span
																class="fw-medium d-block"><?php echo number_format($tax, 0); ?>/-</span>
														</div>
														<div
															class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between mb-2">
															<p class="mb-0">Discount</p>
															<span
																class="fw-medium text-danger d-block"><?php echo $discount > 0 ? '-' : ''; ?><?php echo number_format($discount, 0); ?>/-</span>
														</div>
													</div>
													<div
														class="bg-primary d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between p-3 rounded">
														<h6 class="text-white">Total</h6>
														<h6 class="text-white">
															<?php echo number_format($total_fee, 0); ?>/-</h6>
													</div>
													<div id="optional_tid_wrapper">
														<h6 class="mb-3 mt-3">TeleRx ID (TID)</h6>
														<input type="text" class="form-control" name="booking_telerx_id"
															id="booking_telerx_id"
															placeholder="Enter health worker's TeleRx ID (optional)">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
										<a href="javascript:void(0);"
											class="btn btn-md btn-dark prev_btns inline-flex align-items-center rounded-pill">
											<i class="isax isax-arrow-left-2 me-1"></i>
											Back
										</a>
										<a href="javascript:void(0);" id="booking_confirm_pay_btn"
											class="btn btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill">
											Confirm & Pay
											<i class="isax isax-arrow-right-3 ms-1"></i>
										</a>
									</div>
								</div>
							</div>
						</fieldset>
						<fieldset>
							<div class="card booking-card">
								<div class="card-body booking-body pb-1">
									<div class="row">
										<div class="col-lg-8 d-flex">
											<div class="flex-fill">
												<div class="card ">
													<div class="card-header">
														<h5 class="d-flex align-items-center flex-wrap rpw-gap-2">
															<i class="isax isax-tick-circle5 text-success me-2"></i>
															Booking Confirmed
														</h5>
													</div>
													<div
														class="card-header d-flex align-items-center flex-wrap rpw-gap-2">
														<span
															class="avatar avatar-lg avatar-rounded me-2 flex-shrink-0"><img
																src="<?php echo $doctor ? htmlspecialchars($doctor['profile_image']) : 'assets/img/clients/client-16.jpg'; ?>"
																alt=""></span>
														<p class="mb-0">Your Booking has been Confirmed with <span
																class="text-dark"><?php echo $doctor ? htmlspecialchars($doctor['name']) : 'the doctor'; ?></span>
															— please be on time, at least <span class="text-dark">15
																mins</span> before the appointment time.</p>
													</div>
													<div class="card-body pb-1">
														<div
															class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between mb-3">
															<h6>Booking Info</h6>
															<a href="booking.php?doctor_id=<?php echo $doctor ? (int) $doctor['id'] : ''; ?>"
																class="btn btn-light rounded-pill"><i
																	class="isax isax-calendar me-1"></i>Reschedule</a>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="mb-3">
																	<label class="form-label">Service</label>
																	<div class="form-plain-text" id="confirm_service">
																		<?php echo $doctor ? htmlspecialchars($doctor['specialty'] ?? 'Consultation') : '—'; ?>
																	</div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="mb-3">
																	<label class="form-label">Date & Time</label>
																	<div class="form-plain-text" id="confirm_date_time">
																		—</div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="mb-3">
																	<label class="form-label">Appointment type</label>
																	<div class="form-plain-text">Video consultation
																	</div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="mb-3">
																	<label class="form-label">Location</label>
																	<div class="form-plain-text">
																		<?php echo $doctor ? htmlspecialchars($doctor['location'] ?: 'TeleRx Online') : '—'; ?>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="card">
													<div
														class="card-body d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
														<div>
															<h6 class="mb-1">Need Our Assistance</h6>
															<p class="mb-0">Call us in case you face any Issue on
																Booking / Cancellation</p>
														</div>
														<a href="javascript:void(0);"
															class="btn btn-light rounded-pill"><i
																class="isax isax-call5 me-1"></i>Call Us</a>
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-4 d-flex">
											<div class="card flex-fill">
												<div class="card-body d-flex flex-column justify-content-between">
													<div class="text-center">
														<h6 class="fs-14 mb-2">Booking Number</h6>
														<span class="booking-id-badge mb-3">—</span>
														<span class="d-block mb-3"><img
																src="assets/img/icons/payment-qr.svg" alt=""></span>
														<p>Scan this QR Code to Download the details of Appointment</p>
													</div>
													<div>
														<a href="javascript:void(0);"
															class="btn w-100 mb-3 btn-md btn-dark prev_btns inline-flex align-items-center rounded-pill">
															Add To Calendar
														</a>
														<a href="booking.php"
															class="btn w-100 btn-md btn-primary-gradient inline-flex align-items-center rounded-pill">
															Start New Booking
														</a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div>
								<a href="booking.php" class="">
									<i class="isax isax-arrow-left-2 me-1"></i>
									Back to Bookings
								</a>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Terms -->

	<!-- Toast container for booking messages -->
	<div id="booking-toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
	</div>

</body>

<?php include 'footer.php'; ?>