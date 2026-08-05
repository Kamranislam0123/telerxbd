<?php
include 'header.php';

/**
 * Doctor Profile - TeleRx Bangladesh
 * Dynamic doctor profile page showing detailed information
 */

// Include configuration
$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.php');
    exit;
}
require_once $config_path;

// Get doctor ID from URL parameter or default to logged-in doctor
$profile_doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_SESSION['doctor_id']) ? $_SESSION['doctor_id'] : null);

if (!$profile_doctor_id) {
    header('Location: login.php');
    exit;
}

try {
    $conn = getDBConnection();

    // Fetch doctor + profile (explicit columns — matches profile settings / doctors list)
    $stmt = $conn->prepare("
        SELECT
            d.id,
            d.name,
            d.email,
            d.phone,
            d.bmdc_no,
            d.created_at,
            dp.bio,
            dp.specialty,
            dp.languages_spoken,
            dp.consultation_fee,
            dp.experience_years,
            dp.total_appointments,
            dp.total_reviews,
            dp.average_rating,
            dp.is_available,
            dp.address,
            dp.city,
            dp.state,
            dp.zip_code,
            dp.district,
            dp.present_address,
            dp.profile_image,
            dp.gender
        FROM doctors d
        LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id
        WHERE d.id = ?
    ");
    $stmt->bind_param("i", $profile_doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: index.html');
        exit;
    }

    $doctor = $result->fetch_assoc();

    // Set default values if profile data is missing
    $doctor['bio'] = $doctor['bio'] ?? 'Experienced healthcare professional dedicated to providing quality medical care.';
    $doctor['specialty'] = $doctor['specialty'] ?? 'General Medicine';
    $doctor['languages_spoken'] = $doctor['languages_spoken'] ?? 'English, Bengali';
    $doctor['consultation_fee'] = $doctor['consultation_fee'] ?? 100.00;
    $doctor['experience_years'] = $doctor['experience_years'] ?? 0;
    $doctor['total_appointments'] = $doctor['total_appointments'] ?? 0;
    $doctor['total_reviews'] = $doctor['total_reviews'] ?? 0;
    $doctor['average_rating'] = $doctor['average_rating'] ?? 0.00;
    $doctor['is_available'] = $doctor['is_available'] ?? true;
    $doctor['profile_image'] = $doctor['profile_image'] ?? 'assets/img/doctors/doc-profile-02.jpg';

    // Location from DB only (present_address is what doctor-profile-settings saves)
    $present = trim((string) ($doctor['present_address'] ?? ''));
    $addr = trim((string) ($doctor['address'] ?? ''));
    $district = trim((string) ($doctor['district'] ?? ''));
    $city = trim((string) ($doctor['city'] ?? ''));
    $state = trim((string) ($doctor['state'] ?? ''));
    $zip = trim((string) ($doctor['zip_code'] ?? ''));
    if ($present !== '') {
        $doctor_location_display = $present;
        foreach ([$district, $city, $state, $zip] as $seg) {
            if ($seg !== '' && stripos($doctor_location_display, $seg) === false) {
                $doctor_location_display .= ', ' . $seg;
            }
        }
    } else {
        $segs = array_unique(array_filter([$addr, $district, $city, $state, $zip], function ($s) {
            return $s !== '';
        }));
        $doctor_location_display = implode(', ', $segs);
    }
    $doctor_location_maps_url = $doctor_location_display !== ''
        ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($doctor_location_display)
        : '';

    // Fetch doctor's experiences
    $stmt = $conn->prepare("
        SELECT * FROM doctor_experiences
        WHERE doctor_id = ?
        ORDER BY start_date DESC
    ");
    $stmt->bind_param("i", $profile_doctor_id);
    $stmt->execute();
    $experiences = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch doctor's education
    $stmt = $conn->prepare("
        SELECT * FROM doctor_education
        WHERE doctor_id = ?
        ORDER BY year_of_completion DESC
    ");
    $stmt->bind_param("i", $profile_doctor_id);
    $stmt->execute();
    $education = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch doctor's awards
    $stmt = $conn->prepare("
        SELECT * FROM doctor_awards
        WHERE doctor_id = ?
        ORDER BY award_year DESC
    ");
    $stmt->bind_param("i", $profile_doctor_id);
    $stmt->execute();
    $awards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch doctor's clinics
    $stmt = $conn->prepare("
        SELECT * FROM doctor_clinics
        WHERE doctor_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $profile_doctor_id);
    $stmt->execute();
    $clinics = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Doctor profile error: " . $e->getMessage());
    header('Location: index.html');
    exit;
}

?>
	<!-- Page Content -->
	<div class="content" style="background-color: #f8f9fa; padding-top: 20px;">
		<div class="container">
			
			<!-- Breadcrumb -->
			<nav aria-label="breadcrumb" class="page-breadcrumb" style="margin-bottom: 10px; display: flex; justify-content: flex-start;">
				<ol class="breadcrumb mb-0" style="background: transparent; padding: 0; font-size: 14px; margin: 0;">
					<li class="breadcrumb-item"><a href="index.php" style="color: #6b7280; text-decoration: none;">Home</a></li>
					<li class="breadcrumb-item"><a href="search.php" style="color: #6b7280; text-decoration: none;">Doctors</a></li>
					<li class="breadcrumb-item active" aria-current="page" style="color: #111827; font-weight: 500;">Dr. <?php echo htmlspecialchars(str_replace('Dr. ', '', $doctor['name'])); ?></li>
				</ol>
			</nav>
			<!-- /Breadcrumb -->

					<!-- Doctor Widget -->
					<div class="card doc-profile-card" style="border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); margin-bottom: 30px; margin-top: 20px;">
						<div class="card-body" style="padding: 30px;">
							<div class="row align-items-center">
								<!-- Left Image -->
								<div class="col-md-3 col-sm-12 text-center text-md-start mb-4 mb-md-0 position-relative">
									<div class="doctor-img" style="width: 180px; height: 180px; margin: 0 auto; border-radius: 12px; overflow: hidden; position: relative;">
										<img src="<?php echo htmlspecialchars($doctor['profile_image']); ?>" class="img-fluid" alt="Doctor Image" style="width: 100%; height: 100%; object-fit: cover;">
									</div>
									<div style="position: absolute; bottom: -12px; left: 50%; transform: translateX(-50%);">
										<span class="badge" style="background-color: <?php echo $doctor['is_available'] ? '#10b981' : '#9ca3af'; ?>; color: white; padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 2px solid white;">
											<?php echo $doctor['is_available'] ? 'Online' : 'Offline'; ?>
										</span>
									</div>
								</div>
								
								<!-- Middle Info -->
								<div class="col-md-6 col-sm-12">
									<h3 style="font-weight: 700; color: #111827; margin-bottom: 8px;">Dr. <?php echo htmlspecialchars(str_replace('Dr. ', '', $doctor['name'])); ?></h3>
									<p style="color: #4b5563; font-size: 15px; margin-bottom: 8px;">MBBS</p>
									<p style="color: #3b82f6; font-size: 15px; font-weight: 500; margin-bottom: 20px;"><?php echo htmlspecialchars($doctor['specialty']); ?></p>
									
									<div class="row" style="margin-bottom: 15px;">
										<div class="col-4" style="border-right: 1px solid #e5e7eb;">
											<p style="color: #6b7280; font-size: 13px; margin-bottom: 4px;">Total Experience</p>
											<p style="color: #111827; font-weight: 600; font-size: 14px; margin-bottom: 0;"><?php echo $doctor['experience_years']; ?>+ Years</p>
										</div>
										<div class="col-4" style="border-right: 1px solid #e5e7eb;">
											<p style="color: #6b7280; font-size: 13px; margin-bottom: 4px;">BMDC Number</p>
											<p style="color: #111827; font-weight: 600; font-size: 14px; margin-bottom: 0;"><?php echo htmlspecialchars($doctor['bmdc_no'] ?: 'N/A'); ?></p>
										</div>
										<div class="col-4">
											<p style="color: #6b7280; font-size: 13px; margin-bottom: 4px;">Total Rating</p>
											<p style="color: #111827; font-weight: 600; font-size: 14px; margin-bottom: 0;">
												<i class="fas fa-star" style="color: #f59e0b;"></i> <?php echo number_format($doctor['average_rating'], 1); ?> <span style="color: #6b7280; font-weight: 400;">(<?php echo $doctor['total_reviews']; ?>)</span>
											</p>
										</div>
									</div>
									
									<p style="color: #4b5563; font-size: 14px; margin-bottom: 0;">Working in <span style="font-weight: 500; color: #111827;"><?php echo !empty($experiences) ? htmlspecialchars($experiences[0]['hospital_name']) : 'N/A'; ?></span></p>
								</div>
								
								<!-- Right Info -->
								<div class="col-md-3 col-sm-12 text-md-end mt-4 mt-md-0 position-relative" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
									<div style="margin-top: auto; margin-bottom: 15px;">
										<h5 style="font-weight: 700; color: #111827; margin-bottom: 10px; font-size: 18px;">Consultation Fee</h5>
										<div class="d-flex align-items-baseline justify-content-md-end mb-3">
											<span style="color: #3b82f6; font-size: 36px; font-weight: 700;">৳<?php echo number_format($doctor['consultation_fee'], 0); ?></span>
											<span style="color: #6b7280; font-size: 14px; margin-left: 8px;">(Inc. VAT)</span>
										</div>
									</div>
									<a href="booking.php?doctor_id=<?php echo $profile_doctor_id; ?>" class="btn btn-primary w-100" style="background-color: #3b82f6; border-color: #3b82f6; color: #fff; font-weight: 500; padding: 12px; border-radius: 8px;">
										Book Doctor Now
									</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /Doctor Widget -->
					
					<div class="doctors-detailed-info custom-tabs-section" style="margin-top: 40px; margin-bottom: 60px;">
						<ul class="nav nav-tabs border-bottom-0" id="doctorProfileTab" role="tablist" style="border-bottom: 1px solid #e5e7eb !important; gap: 30px;">
							<li class="nav-item" role="presentation">
								<button class="nav-link active custom-tab-btn" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane" type="button" role="tab" aria-controls="info-tab-pane" aria-selected="true" style="border: none; background: transparent; padding: 10px 0; color: #3b82f6; font-weight: 600; border-bottom: 2px solid #3b82f6; border-radius: 0;">
									<i class="fas fa-info-circle me-2"></i>Info
								</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link custom-tab-btn" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience-tab-pane" type="button" role="tab" aria-controls="experience-tab-pane" aria-selected="false" style="border: none; background: transparent; padding: 10px 0; color: #6b7280; font-weight: 500; border-bottom: 2px solid transparent; border-radius: 0;">
									<i class="fas fa-briefcase me-2"></i>Experience
								</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link custom-tab-btn" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-tab-pane" type="button" role="tab" aria-controls="reviews-tab-pane" aria-selected="false" style="border: none; background: transparent; padding: 10px 0; color: #6b7280; font-weight: 500; border-bottom: 2px solid transparent; border-radius: 0;">
									<i class="fas fa-comments me-2"></i>Reviews
								</button>
							</li>
						</ul>

						<div class="tab-content mt-5" id="doctorProfileTabContent">
							<!-- Info Tab -->
							<div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
								<div class="row">
									<div class="col-lg-7 mb-4 mb-lg-0">
										<h4 style="color: #3b82f6; font-weight: 700; margin-bottom: 20px;">About Doctor</h4>
										<div style="color: #4b5563; font-size: 15px; line-height: 1.7;">
											<?php echo nl2br(htmlspecialchars($doctor['bio'])); ?>
										</div>
									</div>
									<div class="col-lg-5">
										<h4 style="color: #3b82f6; font-weight: 700; margin-bottom: 20px;">At a Glance</h4>
										
										<!-- Consultation Time Box -->
										<div class="p-3 mb-3" style="background-color: #f8f9fa; border-radius: 10px;">
											<div class="d-flex align-items-center mb-2">
												<i class="fas fa-video text-secondary me-2"></i>
												<span style="font-weight: 500; color: #4b5563;">Instant Consultation Time</span>
											</div>
											<div class="d-flex align-items-center">
												<span style="width: 8px; height: 8px; background-color: #9ca3af; border-radius: 50%; display: inline-block; margin-right: 10px;"></span>
												<span style="font-weight: 600; color: #111827;">Sat - Fri</span>
												<span style="color: #6b7280; margin-left: 8px;">(5:00 PM - 10:00 PM)</span>
											</div>
										</div>
										
										<!-- Stats Grid Box -->
										<div class="p-4" style="background-color: #f8f9fa; border-radius: 10px;">
											<div class="row g-4">
												<div class="col-6">
													<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Consultation Fee</p>
													<p style="color: #111827; font-weight: 700; font-size: 16px; margin-bottom: 0;">৳<?php echo number_format($doctor['consultation_fee'], 0); ?> <span style="font-weight: 400; font-size: 13px; color: #9ca3af;">(inc. VAT)</span></p>
												</div>
												<div class="col-6">
													<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Follow-Up Fee</p>
													<p style="color: #111827; font-weight: 700; font-size: 16px; margin-bottom: 0;">৳<?php echo number_format($doctor['consultation_fee'] * 0.7, 0); ?> <span style="font-weight: 400; font-size: 13px; color: #9ca3af;">(inc. VAT)</span></p>
													<p style="font-size: 12px; color: #6b7280; margin-top: 2px; margin-bottom: 0;">(Within 30 days)</p>
												</div>
												<div class="col-6">
													<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Patient Attended</p>
													<p style="color: #111827; font-weight: 700; font-size: 16px; margin-bottom: 0;"><?php echo number_format($doctor['total_appointments']); ?></p>
												</div>
												<div class="col-6">
													<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Joined DocTime</p>
													<p style="color: #111827; font-weight: 700; font-size: 16px; margin-bottom: 0;"><?php echo date('F d, Y', strtotime($doctor['created_at'] ?? 'now')); ?></p>
												</div>
												<div class="col-6">
													<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Doctor Code</p>
													<p style="color: #111827; font-weight: 700; font-size: 16px; margin-bottom: 0;">DT<?php echo str_pad($doctor['id'], 4, '0', STR_PAD_LEFT); ?></p>
												</div>
												<div class="col-6">
													<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Avg. Consultation Time</p>
													<p style="color: #111827; font-weight: 700; font-size: 16px; margin-bottom: 0;">15 minutes</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Experience Tab -->
							<div class="tab-pane fade" id="experience-tab-pane" role="tabpanel" aria-labelledby="experience-tab" tabindex="0">
								<div class="row g-4">
									<?php if (!empty($experiences)): ?>
										<?php foreach ($experiences as $index => $exp): ?>
											<div class="col-md-6">
												<div class="p-4 h-100" style="border: 1px solid #e5e7eb; border-radius: 10px; background-color: #fff;">
													<h5 style="font-weight: 700; color: #111827; margin-bottom: 20px;"><?php echo htmlspecialchars($exp['hospital_name']); ?></h5>
													
													<div class="row mb-3">
														<div class="col-6">
															<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Designation</p>
															<p style="color: #111827; font-weight: 500; font-size: 15px; margin-bottom: 0;"><?php echo htmlspecialchars($exp['title']); ?></p>
														</div>
														<div class="col-6">
															<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Department</p>
															<p style="color: #111827; font-weight: 500; font-size: 15px; margin-bottom: 0;"><?php echo htmlspecialchars($doctor['specialty']); ?></p>
														</div>
													</div>
													
													<div class="row">
														<div class="col-6">
															<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Employment Status</p>
															<p style="color: #111827; font-weight: 500; font-size: 15px; margin-bottom: 0;">
																<?php 
																$start_date = date('M j, Y', strtotime($exp['start_date']));
																$end_date = $exp['currently_working'] ? 'current' : ($exp['end_date'] ? date('M j, Y', strtotime($exp['end_date'])) : 'current');
																echo $start_date . ' - ' . $end_date;
																?>
															</p>
														</div>
														<div class="col-6">
															<p style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Period</p>
															<p style="color: #111827; font-weight: 500; font-size: 15px; margin-bottom: 0;">
																<?php echo htmlspecialchars($exp['years_of_experience']); ?>
															</p>
														</div>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									<?php else: ?>
										<div class="col-12 text-center text-muted py-5">
											<p>No experience information available.</p>
										</div>
									<?php endif; ?>
								</div>
							</div>

							<!-- Reviews Tab -->
							<div class="tab-pane fade" id="reviews-tab-pane" role="tabpanel" aria-labelledby="reviews-tab" tabindex="0">
								<div class="text-center text-muted py-5">
									<p>Reviews will be available soon.</p>
								</div>
							</div>
						</div>
							<!-- <div class="doc-information-details" id="insurence">
								<div class="detail-title slider-nav d-flex justify-content-between align-items-center">
									<h4>Insurance  Accepted (6)</h4>
									<div class="nav nav-container slide-1"></div>
								</div>
								<div class="insurence-logo-slider owl-carousel">
									<div class="insurence-logo">
										<span><img src="assets/img/icons/insurence-logo-01.svg" alt="Img"></span>
									</div>
									<div class="insurence-logo">
										<span><img src="assets/img/icons/insurence-logo-02.svg" alt="Img"></span>
									</div>
									<div class="insurence-logo">
										<span><img src="assets/img/icons/insurence-logo-03.svg" alt="Img"></span>
									</div>
									<div class="insurence-logo">
										<span><img src="assets/img/icons/insurence-logo-04.svg" alt="Img"></span>
									</div>
									<div class="insurence-logo">
										<span><img src="assets/img/icons/insurence-logo-05.svg" alt="Img"></span>
									</div>
									<div class="insurence-logo">
										<span><img src="assets/img/icons/insurence-logo-06.svg" alt="Img"></span>
									</div>
									<div class="insurence-logo">
										<span><img src="assets/img/icons/insurence-logo-03.svg" alt="Img"></span>
									</div>
									<div class="insurence-logo">
										<span><img src="assets/img/icons/insurence-logo-04.svg" alt="Img"></span>
									</div>
								</div>
							</div> -->
							<!-- <div class="doc-information-details" id="speciality">
								<div class="detail-title">
									<h4>Speciality</h4>
								</div>
								<ul class="special-links">
									<li><a href="#">Orthopedic Consultation</a></li>
									<li><a href="#">Delivery Blocks</a></li>
									<li><a href="#">Ultrasound Injection</a></li>
									<li><a href="#">Tooth Bleaching</a></li>
									<li><a href="#">Tooth Bleaching</a></li>
									<li><a href="#">Cosmetic</a></li>
								</ul>
							</div> -->
							<!-- <div class="doc-information-details" id="services">
								<div class="detail-title">
									<h4>Services & Pricing</h4>
								</div>
								<ul class="special-links">
									<li><a href="#">Orthopedic Consultation <span>$52</span></a></li>
									<li><a href="#">Delivery Blocks <span>$24</span></a></li>
									<li><a href="#">Ultrasound Injection <span>$31</span></a></li>
									<li><a href="#">Tooth Bleaching <span>$20</span></a></li>
									<li><a href="#">Tooth Bleaching <span>$15</span></a></li>
									<li><a href="#">Cosmetic <span>$10</span></a></li>
								</ul>
							</div> -->
							<!-- <div class="doc-information-details" id="availability">
								<div class="detail-title slider-nav d-flex justify-content-between align-items-center">
									<h4>Availability</h4>
									<div class="nav nav-container slide-2"></div>
								</div>
								<div class="availability-slots-slider owl-carousel">
									<div class="availability-date">
										<div class="book-date">
											<h6>Wed Feb 2024</h6>
											<span>01:00 - 02:00 PM</span>
										</div>
									</div>
									<div class="availability-date">
										<div class="book-date">
											<h6>Wed Feb 2024</h6>
											<span>01:00 - 02:00 PM</span>
										</div>
									</div>
									<div class="availability-date">
										<div class="book-date">
											<h6>Wed Feb 2024</h6>
											<span>01:00 - 02:00 PM</span>
										</div>
									</div>
									<div class="availability-date">
										<div class="book-date">
											<h6>Wed Feb 2024</h6>
											<span>01:00 - 02:00 PM</span>
										</div>
									</div>
									<div class="availability-date">
										<div class="book-date">
											<h6>Wed Feb 2024</h6>
											<span>01:00 - 02:00 PM</span>
										</div>
									</div>
									<div class="availability-date">
										<div class="book-date">
											<h6>Wed Feb 2024</h6>
											<span>01:00 - 02:00 PM</span>
										</div>
									</div>
									<div class="availability-date">
										<div class="book-date">
											<h6>Wed Feb 2024</h6>
											<span>01:00 - 02:00 PM</span>
										</div>
									</div>
									<div class="availability-date">
										<div class="book-date">
											<h6>Wed Feb 2024</h6>
											<span>01:00 - 02:00 PM</span>
										</div>
									</div>
								</div>
							</div> -->
							<!-- <div class="doc-information-details" id="clinic">
								<div class="detail-title">
									<h4>Clinics & Locations</h4>
								</div>
								<div class="clinic-loc">
									<div class="row align-items-center">
										<div class="col-lg-7">
											<div class="clinic-info">
												<div class="clinic-img"><img src="assets/img/clinic/clinic-11.jpg" alt="Img"></div>
												<div class="detail-clinic">
													<h5>Sofi's Clinic - </h5>
													<span>$350 / Apponitment</span>
													<p>2286 Sundown Lane, Old Trafford 24541, UK</p>
												</div>
											</div>
											<div class="d-flex align-items-center avail-time-slot">
												<div class="availability-date">
													<div class="book-date">
														<h6>Monday</h6>
														<span>07:00 AM - 09:00 PM</span>
													</div>
												</div>
												<div class="availability-date">
													<div class="book-date">
														<h6>Tuesday</h6>
														<span>07:00 AM - 09:00 PM</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-5">
											<div class="contact-map d-flex">
												<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3193.7301009561315!2d-76.13077892422932!3d36.82498697224007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89bae976cfe9f8af%3A0xa61eac05156fbdb9!2sBeachStreet%20USA!5e0!3m2!1sen!2sin!4v1669777904208!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
											</div>
										</div>
									</div>
								</div>
								<div class="clinic-loc mb-0">
									<div class="row align-items-center">
										<div class="col-lg-7">
											<div class="clinic-info">
												<div class="clinic-img"><img src="assets/img/clinic/clinic-12.jpg" alt="Img"></div>
												<div class="detail-clinic">
													<h5>The Family Dentistry Clinic </h5>
													<span>$550 / Apponitment</span>
													<p>MDS - Periodontology and Oral Implantology, BDS</p>
												</div>
											</div>
											<div class="d-flex align-items-center avail-time-slot">
												<div class="availability-date">
													<div class="book-date">
														<h6>Friday</h6>
														<span>07:00 AM - 09:00 PM</span>
													</div>
												</div>
												<div class="availability-date">
													<div class="book-date">
														<h6>Saturday</h6>
														<span>07:00 AM - 09:00 PM</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-5">
											<div class="contact-map d-flex">
												<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3193.7301009561315!2d-76.13077892422932!3d36.82498697224007!2m3!1f0!2f0!3m2!1sen!2sin!4v1669777904208!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
											</div>
										</div>
									</div>
								</div>
							</div> -->
							<!-- <div class="doc-information-details" id="membership">
								<div class="detail-title">
									<h4>Membership</h4>
								</div>
								<div class="member-ship-info">
									<span class="mem-check"><i class="fa-solid fa-check"></i></span>
									<p>Affiliate members include related allied health professionals- evidence based
										(Dietitians, Physiotherapist, Occupational therapist and Clinical Psychologist) who will
										team up with allopathic physicians to
										support the Lifestyle Medicine movement in India through ISLM.
									</p>
								</div>
								<div class="member-ship-info mb-0">
									<span class="mem-check"><i class="fa-solid fa-check"></i></span>
									<p>Physician members include the allopathic doctors only (MBBS, MD, MS, DM, MCH, DNB or equivalent degree)
										who hold a valid medical license as recognized by the Medical Council of India.
									</p>
								</div>
							</div> -->
							<!-- <div class="doc-information-details" id="awards">
								<div class="detail-title slider-nav d-flex justify-content-between align-items-center">
									<h4>Awards</h4>
									<div class="nav nav-container slide-3"></div>
								</div>
								<div class="awards-slider owl-carousel">
									<div class="award-card">
										<div class="award-card-info">
											<span><img src="assets/img/icons/bookmark-star.svg" alt="Img"></span>
											<h5>Award Name (2021)</h5>
											<p>evidence based (Dietitians, Physiotherapist, Occupational therapist and Clinical)</p>
										</div>
									</div>
									<div class="award-card">
										<div class="award-card-info">
											<span><img src="assets/img/icons/bookmark-star.svg" alt="Img"></span>
											<h5>Award Name (2022)</h5>
											<p>evidence based (Dietitians, Physiotherapist, Occupational therapist and Clinical)</p>
										</div>
									</div>
									<div class="award-card">
										<div class="award-card-info">
											<span><img src="assets/img/icons/bookmark-star.svg" alt="Img"></span>
											<h5>Award Name (2023)</h5>
											<p>evidence based (Dietitians, Physiotherapist, Occupational therapist and Clinical)</p>
										</div>
									</div>
									<div class="award-card">
										<div class="award-card-info">
											<span><img src="assets/img/icons/bookmark-star.svg" alt="Img"></span>
											<h5>Award Name (2024)</h5>
											<p>evidence based (Dietitians, Physiotherapist, Occupational therapist and Clinical)</p>
										</div>
									</div>
									<div class="award-card">
										<div class="award-card-info">
											<span><img src="assets/img/icons/bookmark-star.svg" alt="Img"></span>
											<h5>Award Name (2020)</h5>
											<p>evidence based (Dietitians, Physiotherapist, Occupational therapist and Clinical)</p>
										</div>
									</div>
								</div>
							</div> -->
							<!-- <div class="doc-information-details" id="bussiness_hour">
								<div class="detail-title">
									<h4>Business Hours</h4>
								</div>
								<div class="hours-business">
									<ul>
										<li>
											<div class="today-hours">
												<h6>Today</h6>
												<span>5 Feb 2024</span>
											</div>
											<div class="availed">
												<span class="badge doc-avail-badge"><i class="fa-solid fa-circle"></i>Available </span>
												<p>07:00 AM - 09:00 PM</p>
											</div>
										</li>
										<li>
											<h6>Monday</h6>
											<p>07:00 AM - 09:00 PM</p>
										</li>
										<li>
											<h6>Tuesday</h6>
											<p>07:00 AM - 09:00 PM</p>
										</li>
										<li>
											<h6>Wednesday</h6>
											<p>07:00 AM - 09:00 PM</p>
										</li>
										<li>
											<h6>Thursday</h6>
											<p>07:00 AM - 09:00 PM</p>
										</li>
										<li>
											<h6>Friday</h6>
											<p>07:00 AM - 09:00 PM</p>
										</li>
										<li>
											<h6>Saturday</h6>
											<p>07:00 AM - 09:00 PM</p>
										</li>
										<li>
											<h6>Sunday</h6>
											<p>07:00 AM - 09:00 PM</p>
										</li>
									</ul>
								</div>
							</div> -->
							<!-- <div class="doc-information-details" id="review">
								<div class="detail-title">
									<h4>Reviews (200)</h4>
								</div>
								<div class="doc-review-card">
									<div class="user-info-review">
										<div class="reviewer-img">
											<a href="#" class="avatar-img"><img src="assets/img/clients/client-13.jpg" alt="Img"></a>
											<div class="review-star">
												<a href="#">kadajsalamander</a>
												<div class="rating">
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<span>5.0 | 2 days ago</span>
												</div>
											</div>
										</div>
										<span class="thumb-icon"><i class="fa-regular fa-thumbs-up"></i>Yes,Recommend for Appointment</span>
									</div>
									<p>Thank you for this informative article! I've had a couple of hit-and-miss experiences with
										freelancers in the past, and I realize now that I wasn't vetting them properly. Your checklist
										for choosing the right freelancer is going to be my go-to from now on
									</p>
									<a href="#" class="reply d-flex align-items-center"><i class="fa-solid fa-reply me-2"></i>Reply</a>
								</div>
								<div class="doc-review-card">
									<div class="user-info-review">
										<div class="reviewer-img">
											<a href="#" class="avatar-img"><img src="assets/img/clients/client-14.jpg" alt="Img"></a>
											<div class="review-star">
												<a href="#">Dane jose</a>
												<div class="rating">
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<span>5.0 | 1 Months ago</span>
												</div>
											</div>
										</div>
										<span class="thumb-icon"><i class="fa-regular fa-thumbs-up"></i>Yes,Recommend for Appointment</span>
									</div>
									<p>As a freelancer myself, I find this article spot on! It's important for clients to
										understand what to look for in a freelancer and how to foster a good working relationship.
										The point about mutual respect
										and clear communication is key in my experience. Well done
									</p>
									<a href="#" class="reply d-flex align-items-center"><i class="fa-solid fa-reply me-2"></i>Reply</a>
								</div>
								<div class="doc-review-card mb-0">
									<div class="user-info-review">
										<div class="reviewer-img">
											<a href="#" class="avatar-img"><img src="assets/img/clients/client-15.jpg" alt="Img"></a>
											<div class="review-star">
												<a href="#">Dane jose</a>
												<div class="rating">
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<span>5.0 | 15 days ago</span>
												</div>
											</div>
										</div>
										<span class="thumb-icon"><i class="fa-regular fa-thumbs-up"></i>Yes,Recommend for Appointment</span>
									</div>
									<p>Great article! I've bookmarked it for future reference. I'd love to read more about managing long-term relationships with freelancers, if you have any tips on that.
									</p>
									<a href="#" class="reply d-flex align-items-center"><i class="fa-solid fa-reply me-2"></i>Reply</a>
									<div class="replied-info">
										<div class="user-info-review">
											<div class="reviewer-img">
												<a href="#" class="avatar-img"><img src="assets/img/clients/client-16.jpg" alt="Img"></a>
												<div class="review-star">
													<a href="#">Robert Hollenbeck</a>
												</div>
											</div>
										</div>
										<p>Thank you for your comment and I will try to make a another post on that topic.
										</p>
										<a href="#" class="reply d-flex align-items-center"><i class="fa-solid fa-reply me-2"></i>Reply</a>
									</div>
									<!-- Pagination 
									<div class="pagination dashboard-pagination">
										<ul>
											<li>
												<a href="#" class="page-link prev-link"><i class="fa-solid fa-chevron-left me-2"></i>Prev</a>
											</li>
											<li>
												<a href="#" class="page-link active">1</a>
											</li>
											<li>
												<a href="#" class="page-link">2</a>
											</li>
											<li>
												<a href="#" class="page-link">3</a>
											</li>
											<li>
												<a href="#" class="page-link">4</a>
											</li>
											<li>
												<a href="#" class="page-link">5</a>
											</li>
											<li>
												<a href="#" class="page-link">6</a>
											</li>
											<li>
												<a href="#" class="page-link next-link">Next<i class="fa-solid fa-chevron-right ms-2"></i></a>
											</li>
										</ul>
									</div>
									/Pagination -->
								</div>
							</div> -->
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
		
		<!-- Voice Call Modal -->
		<div class="modal fade call-modal" id="voice_call">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<!-- Outgoing Call -->
						<div class="call-box incoming-box">
							<div class="call-wrapper">
								<div class="call-inner">
									<div class="call-user">
										<img alt="User Image" src="assets/img/doctors/doctor-thumb-02.jpg" class="call-avatar">
										<h4>Dr. Darren Elder</h4>
										<span>Connecting...</span>
									</div>							
									<div class="call-items">
										<a href="javascript:void(0);" class="btn call-item call-end" data-bs-dismiss="modal" aria-label="Close"><i class="material-icons">call_end</i></a>
										<a href="voice-call.html" class="btn call-item call-start"><i class="material-icons">call</i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- Outgoing Call -->

					</div>
				</div>
			</div>
		</div>
		<!-- /Voice Call Modal -->
		
		<!-- Video Call Modal -->
		<div class="modal fade call-modal" id="video_call">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">
					
						<!-- Incoming Call -->
						<div class="call-box incoming-box">
							<div class="call-wrapper">
								<div class="call-inner">
									<div class="call-user">
										<img class="call-avatar" src="assets/img/doctors/doctor-thumb-02.jpg" alt="User Image">
										<h4>Dr. Darren Elder</h4>
										<span>Calling ...</span>
									</div>							
									<div class="call-items">
										<a href="javascript:void(0);" class="btn call-item call-end" data-bs-dismiss="modal" aria-label="Close"><i class="material-icons">call_end</i></a>
										<a href="video-call.html" class="btn call-item call-start"><i class="material-icons">videocam</i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Incoming Call -->
						
					</div>
				</div>
			</div>
		</div>
		<!-- Video Call Modal -->
		
		<!-- jQuery -->
		<script src="assets/js/jquery-3.7.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="assets/js/bootstrap.bundle.min.js"></script>

		<!-- Owl Carousel JS -->
		<script src="assets/js/owl.carousel.min.js"></script>
		
		<!-- Fancybox JS -->
		<script src="assets/plugins/fancybox/jquery.fancybox.min.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/script.js"></script>
		
	</body>
</html>