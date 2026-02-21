<?php
session_start();
include 'header.php';
?>

<body>
	<!-- Terms -->
	<div class="doctor-content">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 mx-auto">
					<div class="booking-wizard">
						<ul class="form-wizard-steps d-sm-flex align-items-center justify-content-center" id="progressbar2">
							<li class="progress-active">
								<div class="profile-step">
									<span class="multi-steps">1</span>
									<div class="step-section">
										<h6>Details</h6>
									</div>
								</div>
							</li>
							<li>
								<div class="profile-step">
									<span class="multi-steps">5</span>
									<div class="step-section">
										<h6>Payment</h6>
									</div>
								</div>
							</li>
							<li>
								<div class="profile-step">
									<span class="multi-steps">6</span>
									<div class="step-section">
										<h6>Confirmation</h6>
									</div>
								</div>
							</li>
						</ul>
					</div>
					<div class="booking-widget multistep-form mb-5">
						<fieldset id="first">
							<div class="card booking-card mb-0">
								<div class="card-header">
									<div class="booking-header pb-0">
										<div class="card mb-0">
											<div class="card-body">
												<div class="d-flex align-items-center flex-wrap rpw-gap-2 flex-wrap row-gap-2">
													<span class="avatar avatar-xxxl avatar-rounded me-2 flex-shrink-0"><img src="assets/img/clients/client-15.jpg" alt=""></span>
													<div>
														<h4 class="mb-1">Dr. Michael Brown <span class="badge bg-orange fs-12"><i class="fa-solid fa-star me-1"></i>5.0</span></h4>
														<p class="text-indigo mb-3 fw-medium">Psychologist</p>
														<p class="mb-0"><i class="isax isax-location me-2"></i>5th Street - 1011 W 5th St, Suite 120, Austin, TX 78703</p>
													</div>
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
															<div class="book-title">	
																<h6 class="fs-14 mb-2">Morning</h6>
															</div>
															<div class="token-slot mt-2 mb-2">
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment" checked>
																		<span class="visit-rsn">09:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">-</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">-</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">09:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">-</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
															</div>	
																<div class="book-title">	
																<h6 class="fs-14 mb-2">Evening</h6>
															</div>
															<div class="token-slot mt-2 mb-2">
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment" checked>
																		<span class="visit-rsn">09:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">-</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">-</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">09:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">-</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">10:45</span>
																	</label>
																</div>
																<div class="form-check-inline visits me-0">
																	<label class="visit-btns">
																		<input type="checkbox" class="form-check-input" name="appintment">
																		<span class="visit-rsn">-</span>
																	</label>
																</div>
															</div>	
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
														<input type="text" class="form-control">
													</div>
												</div>
												<div class="col-lg-6 col-md-6">
													<div class="mb-3">
														<label class="form-label">Mobile Number</label>
														<input type="text" class="form-control">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Age</label>
														<input type="text" class="form-control">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Weight</label>
														<input type="text" class="form-control" placeholder="Kg">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Body Temperature</label>
														<input type="text" class="form-control">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Blood Pressure (BP)</label>
														<input type="text" class="form-control">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">Pulse</label>
														<input type="text" class="form-control">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">SpO<sub>2</sub></label>
														<input type="text" class="form-control">
													</div>
												</div>
												<div class="col-lg-3 col-md-6">
													<div class="mb-3">
														<label class="form-label">RBS/FBS</label>
														<input type="text" class="form-control">
													</div>
												</div>
												<div class="col-lg-12">
													<div class="mb-3">
														<label class="form-label">Symptoms in Details</label>
														<textarea class="form-control" rows="3"></textarea>
													</div>
												</div>
												<div class="col-lg-6">
													<div class="mb-3">
														<label class="form-label">Attachment</label>
														<input type="file" class="form-control">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
										<a href="javascript:void(0);" class="btn btn-md btn-dark inline-flex align-items-center rounded-pill">
											<i class="isax isax-arrow-left-2 me-1"></i>
											Back
										</a>
										<a href="javascript:void(0);" class="btn btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill">
											Add Bacic Info
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
													<div class="d-flex align-items-center flex-wrap rpw-gap-2 mb-4 flex-wrap row-gap-2">
														<span class="avatar avatar-xxxl avatar-rounded me-2 flex-shrink-0"><img src="assets/img/clients/client-15.jpg" alt=""></span>
														<div>
															<h4 class="mb-1">Dr. Michael Brown <span class="badge bg-orange fs-12"><i class="fa-solid fa-star me-1"></i>5.0</span></h4>
															<p class="text-indigo mb-3 fw-medium">Psychologist</p>
															<p class="mb-0"><i class="isax isax-location me-2"></i>5th Street - 1011 W 5th St, Suite 120, Austin, TX 78703</p>
														</div>
													</div>
													<h6 class="mb-2" style="text-align: center;">Check Your Booking Info</h6>
													<div class="row gx-2 gy-3">
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Date & Time</h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Full Name</h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Mobile Number</h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Age</h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Weight</h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Body Temperature</h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Blood Pressure (BP)</h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Pulse</h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">SpO<sub>2</sub></h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">RBS/FBS</h6>
																<p class="mb-0">Pick from previous section</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Attachment</h6>
																<p class="mb-0">Count the number of Attach..</p>
															</div>
														</div>
														<div class="col-lg-3 col-sm-6">
															<div>
																<h6 class="fs-14 fw-medium mb-1">Details Summury</h6>
																<p class="mb-0">প্রথম ২০ ক্যারেকটার দেখাবে, তারপর ৩ টা (ডট) ...</p>
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
																<ul class="nav nav-pills mb-3 row" id="pills-tab" role="tablist">
																	<li class="nav-item col-sm-6" role="presentation">
																	  	<button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab">
																		<img src="assets/img/icons/payment-icon-01.svg" class="me-2" alt="">
																		</button>
																	</li>
																	<li class="nav-item col-sm-6" role="presentation">
																	  <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab">
																		<img src="assets/img/icons/payment-icon-02.svg" class="me-2" alt="">
																	  </button>
																	</li>																
																  </ul>
																  <div class="tab-content" id="pills-tabContent">
																	<div class="tab-pane fade show active" id="pills-home" role="tabpanel">
																		<div class="mb-3">
																			<label class="form-label">Automatically Redirect to bKash Payment</label>
																		</div>
																	</div>
																	<div class="tab-pane fade" id="pills-profile" role="tabpanel">
																		<div>
																			<label class="form-label">Tearms & Conditions</label>
																			<p>1. Monthly two (2) Times Applicable</p>
																			<p>2. Fake NID strickly not allowed</p>
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
																				<label class="form-label">Upload Your NID</label>
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
													<div>
														<h6 class="mb-3">Payment Info</h6>
														<div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between mb-2">
															<p class="mb-0">Doctor Fee</p>
															<span class="fw-medium d-block">200/-</span>
														</div>
														<div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between mb-2">
															<p class="mb-0">Tax</p>
															<span class="fw-medium d-block">0/-</span>
														</div>
														<div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between mb-2">
															<p class="mb-0">Discount</p>
															<span class="fw-medium text-danger d-block">-15/-</span>
														</div>
													</div>
													<div class="bg-primary d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between p-3 rounded">
														<h6 class="text-white">Total</h6>
														<h6 class="text-white">200/-</h6>
													</div>
													<div>
													<h6 class="mb-3">TeleRx ID (TID)</h6>
														<input type="text" class="form-control">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
										<a href="javascript:void(0);" class="btn btn-md btn-dark prev_btns inline-flex align-items-center rounded-pill">
											<i class="isax isax-arrow-left-2 me-1"></i>
											Back
										</a>
										<a href="javascript:void(0);" class="btn btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill">
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
													<div class="card-header d-flex align-items-center flex-wrap rpw-gap-2">
														<span class="avatar avatar-lg avatar-rounded me-2 flex-shrink-0"><img src="assets/img/clients/client-16.jpg" alt=""></span>
														<p class="mb-0">Your Booking has been Confirmed with <span class="text-dark">Dr. Michael Brown </span>  be on time before <span class="text-dark">15 Mins </span> From the appointment Time</p>
													</div>
													<div class="card-body pb-1">
														<div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between mb-3">
															<h6>Booking Info</h6>
															<a href="javascript:void(0);" class="btn btn-light rounded-pill"><i class="isax isax-calendar me-1"></i>Reschedule</a>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="mb-3">
																	<label class="form-label">Service</label>
																	<div class="form-plain-text">Cardiology (30 Mins)</div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="mb-3">
																	<label class="form-label">Additional Service</label>
																	<div class="form-plain-text">Echocardiograms</div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="mb-3">
																	<label class="form-label">Date & Time</label>
																	<div class="form-plain-text">10:00 - 11:00 AM, 15, Oct 2025 </div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="mb-3">
																	<label class="form-label">Appointment type</label>
																	<div class="form-plain-text">Clinic </div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="mb-3">
																	<label class="form-label">Clinic Name & Location</label>
																	<div class="form-plain-text">Wellness Path <a href="javascript:void(0);" class="text-primary">View Location</a></div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="card">
													<div class="card-body d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
														<div>
															<h6 class="mb-1">Need Our Assistance</h6>
															<p class="mb-0">Call us in case you face any Issue on Booking / Cancellation</p>
														</div>
														<a href="javascript:void(0);" class="btn btn-light rounded-pill"><i class="isax isax-call5 me-1"></i>Call Us</a>
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-4 d-flex">
											<div class="card flex-fill">
												<div class="card-body d-flex flex-column justify-content-between">
													<div class="text-center">
														<h6 class="fs-14 mb-2">Booking Number</h6>
														<span class="booking-id-badge mb-3">DCRA12565</span>
														<span class="d-block mb-3"><img src="assets/img/icons/payment-qr.svg" alt=""></span>
														<p>Scan this QR Code to Download the details of Appointment</p>
													</div>
													<div>
														<a href="javascript:void(0);" class="btn w-100 mb-3 btn-md btn-dark prev_btns inline-flex align-items-center rounded-pill">
															Add To Calendar
														</a>
														<a href="doctor-grid.html" class="btn w-100 btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill">
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
</body>

<?php include 'footer.php'; ?>