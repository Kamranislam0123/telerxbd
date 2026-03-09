<?php
/**
 * Health-Worker Profile Settings - TeleRx Bangladesh
 * Profile settings page for health-workers
 */

// Include configuration
$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.php');
    exit;
}
require_once $config_path;

// Check if health-worker is logged in
if (!isset($_SESSION['healthcare_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$healthcare_id = $_SESSION['healthcare_id'];

try {
    $conn = getDBConnection();

    // Fetch health-worker's basic information and profile
    $stmt = $conn->prepare("
        SELECT h.*, hp.*
        FROM healthcare_providers h
        LEFT JOIN healthcare_providers_profiles hp ON h.id = hp.healthcare_provider_id
        WHERE h.id = ?
    ");
    $stmt->bind_param("i", $healthcare_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: login.php');
        exit;
    }

    $healthcare = $result->fetch_assoc();

    // Generate TID if it doesn't exist (Format: T1001, T1002, T1003...)
    if (empty($healthcare['tid'])) {
        $tid_res = $conn->query("SELECT COALESCE(MAX(CAST(SUBSTRING(tid, 2) AS UNSIGNED)), 1000) + 1 AS next_num FROM healthcare_providers WHERE tid REGEXP '^T[0-9]+\$'");
        $next_num = 1001;
        if ($tid_res && $row = $tid_res->fetch_assoc()) {
            $next_num = (int) $row['next_num'];
        }
        $tid = 'T' . $next_num;

        // Update the healthcare provider with TID
        $update_stmt = $conn->prepare("UPDATE healthcare_providers SET tid = ? WHERE id = ?");
        $update_stmt->bind_param("si", $tid, $healthcare_id);
        $update_stmt->execute();
        $update_stmt->close();

        $healthcare['tid'] = $tid;
    }

    // Set default values if profile data is missing
    $healthcare['profile_image'] = $healthcare['profile_image'] ?? 'assets/img/doctors-dashboard/doctor-profile-img.jpg';
    $healthcare['gender'] = $healthcare['gender'] ?? '';
    $healthcare['degrees'] = $healthcare['degrees'] ?? '';
    $healthcare['currently_working'] = $healthcare['currently_working'] ?? '';
    $healthcare['present_address'] = $healthcare['present_address'] ?? '';
    $healthcare['phone'] = $healthcare['phone'] ?? '';

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Health-worker profile settings error: " . $e->getMessage());
    header('Location: login.php');
    exit;
}

$current_page = 'health-worker-profile-settings.php';
include 'header.php';
?>
    <!-- Page Content (same structure as doctor-dashboard) -->
    <div class="content">
        <div class="container">
            <div class="row">
                <?php include 'health-worker-leftside-menu.php'; ?>
                </div>
                <div class="col-lg-8 col-xl-9">
							<!-- Profile Settings -->
							<div class="dashboard-header">
								<h3><?php echo htmlspecialchars($healthcare['name']); ?> Profile Settings</h3>
							</div>

							<!-- Profile Form -->
							<div class="setting-title">
								<h5>Profile Information</h5>
							</div>

							<!-- Single Profile Form -->
							<form action="php/save-healthcare-profile-settings.php" method="POST" enctype="multipart/form-data" id="profileForm">
								<input type="hidden" name="section" value="all">

								<!-- Profile Image Upload -->
								<div class="setting-card">
									<div class="change-avatar img-upload">
										<div class="profile-img">
											<i class="fa-solid fa-file-image"></i>
										</div>
										<div class="upload-img">
											<h5>Profile Image</h5>
											<div class="imgs-load d-flex align-items-center">
												<div class="change-photo">
													Upload New
													<input type="file" class="upload" name="profile_image" accept="image/*">
												</div>
												<a href="#" class="upload-remove">Remove</a>
											</div>
											<p class="form-text">Your Image should Below 4 MB, Accepted format jpg,png,svg</p>
										</div>
									</div>
								</div>

								<!-- Basic Information -->
								<div class="setting-card">
									<div class="row">
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Name</label>
												<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($healthcare['name']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Email</label>
												<input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($healthcare['email']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Phone Number</label>
												<input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($healthcare['phone']); ?>" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Gender</label>
												<select class="form-control" name="gender">
													<option value="">Select Gender</option>
													<option value="Male" <?php echo (isset($healthcare['gender']) && $healthcare['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
													<option value="Female" <?php echo (isset($healthcare['gender']) && $healthcare['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
													<option value="Other" <?php echo (isset($healthcare['gender']) && $healthcare['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
												</select>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">TID (TeleRx ID)</label>
												<input type="text" class="form-control" value="<?php echo htmlspecialchars($healthcare['tid'] ?: 'Not Generated'); ?>" readonly>
												<small class="form-text text-muted">This is your unique TeleRx ID (automatically generated)</small>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Degrees</label>
												<input type="text" class="form-control" name="degrees" value="<?php echo htmlspecialchars($healthcare['degrees'] ?? ''); ?>" placeholder="e.g., B.Sc Nursing, Diploma">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Currently Working / Experience</label>
												<input type="text" class="form-control" name="currently_working" value="<?php echo htmlspecialchars($healthcare['currently_working'] ?? ''); ?>" placeholder="Current workplace and experience">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Present Address</label>
												<input type="text" class="form-control" name="present_address" value="<?php echo htmlspecialchars($healthcare['present_address'] ?? ''); ?>" placeholder="Your current address">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">District</label>
												<select class="form-control" name="district">
													<option value="">Select District</option>
													<option value="Bagerhat" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Bagerhat') ? 'selected' : ''; ?>>Bagerhat</option>
													<option value="Bandarban" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Bandarban') ? 'selected' : ''; ?>>Bandarban</option>
													<option value="Barguna" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Barguna') ? 'selected' : ''; ?>>Barguna</option>
													<option value="Barisal" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Barisal') ? 'selected' : ''; ?>>Barisal</option>
													<option value="Bhola" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Bhola') ? 'selected' : ''; ?>>Bhola</option>
													<option value="Bogra" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Bogra') ? 'selected' : ''; ?>>Bogra</option>
													<option value="Brahmanbaria" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Brahmanbaria') ? 'selected' : ''; ?>>Brahmanbaria</option>
													<option value="Chandpur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Chandpur') ? 'selected' : ''; ?>>Chandpur</option>
													<option value="Chapai Nawabganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Chapai Nawabganj') ? 'selected' : ''; ?>>Chapai Nawabganj</option>
													<option value="Chattogram" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Chattogram') ? 'selected' : ''; ?>>Chattogram</option>
													<option value="Chuadanga" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Chuadanga') ? 'selected' : ''; ?>>Chuadanga</option>
													<option value="Comilla" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Comilla') ? 'selected' : ''; ?>>Comilla</option>
													<option value="Cox's Bazar" <?php echo (isset($healthcare['district']) && $healthcare['district'] == "Cox's Bazar") ? 'selected' : ''; ?>>Cox's Bazar</option>
													<option value="Dhaka" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Dhaka') ? 'selected' : ''; ?>>Dhaka</option>
													<option value="Dinajpur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Dinajpur') ? 'selected' : ''; ?>>Dinajpur</option>
													<option value="Faridpur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Faridpur') ? 'selected' : ''; ?>>Faridpur</option>
													<option value="Feni" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Feni') ? 'selected' : ''; ?>>Feni</option>
													<option value="Gaibandha" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Gaibandha') ? 'selected' : ''; ?>>Gaibandha</option>
													<option value="Gazipur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Gazipur') ? 'selected' : ''; ?>>Gazipur</option>
													<option value="Gopalganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Gopalganj') ? 'selected' : ''; ?>>Gopalganj</option>
													<option value="Habiganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Habiganj') ? 'selected' : ''; ?>>Habiganj</option>
													<option value="Jamalpur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Jamalpur') ? 'selected' : ''; ?>>Jamalpur</option>
													<option value="Jessore" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Jessore') ? 'selected' : ''; ?>>Jessore</option>
													<option value="Jhalokati" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Jhalokati') ? 'selected' : ''; ?>>Jhalokati</option>
													<option value="Jhenaidah" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Jhenaidah') ? 'selected' : ''; ?>>Jhenaidah</option>
													<option value="Joypurhat" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Joypurhat') ? 'selected' : ''; ?>>Joypurhat</option>
													<option value="Khagrachari" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Khagrachari') ? 'selected' : ''; ?>>Khagrachari</option>
													<option value="Khulna" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Khulna') ? 'selected' : ''; ?>>Khulna</option>
													<option value="Kishoreganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Kishoreganj') ? 'selected' : ''; ?>>Kishoreganj</option>
													<option value="Kurigram" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Kurigram') ? 'selected' : ''; ?>>Kurigram</option>
													<option value="Kushtia" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Kushtia') ? 'selected' : ''; ?>>Kushtia</option>
													<option value="Lakshmipur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Lakshmipur') ? 'selected' : ''; ?>>Lakshmipur</option>
													<option value="Lalmonirhat" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Lalmonirhat') ? 'selected' : ''; ?>>Lalmonirhat</option>
													<option value="Madaripur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Madaripur') ? 'selected' : ''; ?>>Madaripur</option>
													<option value="Magura" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Magura') ? 'selected' : ''; ?>>Magura</option>
													<option value="Manikganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Manikganj') ? 'selected' : ''; ?>>Manikganj</option>
													<option value="Meherpur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Meherpur') ? 'selected' : ''; ?>>Meherpur</option>
													<option value="Moulvibazar" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Moulvibazar') ? 'selected' : ''; ?>>Moulvibazar</option>
													<option value="Munshiganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Munshiganj') ? 'selected' : ''; ?>>Munshiganj</option>
													<option value="Mymensingh" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Mymensingh') ? 'selected' : ''; ?>>Mymensingh</option>
													<option value="Naogaon" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Naogaon') ? 'selected' : ''; ?>>Naogaon</option>
													<option value="Narail" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Narail') ? 'selected' : ''; ?>>Narail</option>
													<option value="Narayanganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Narayanganj') ? 'selected' : ''; ?>>Narayanganj</option>
													<option value="Narsingdi" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Narsingdi') ? 'selected' : ''; ?>>Narsingdi</option>
													<option value="Natore" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Natore') ? 'selected' : ''; ?>>Natore</option>
													<option value="Nawabganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Nawabganj') ? 'selected' : ''; ?>>Nawabganj</option>
													<option value="Netrakona" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Netrakona') ? 'selected' : ''; ?>>Netrakona</option>
													<option value="Nilphamari" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Nilphamari') ? 'selected' : ''; ?>>Nilphamari</option>
													<option value="Noakhali" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Noakhali') ? 'selected' : ''; ?>>Noakhali</option>
													<option value="Pabna" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Pabna') ? 'selected' : ''; ?>>Pabna</option>
													<option value="Panchagarh" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Panchagarh') ? 'selected' : ''; ?>>Panchagarh</option>
													<option value="Patuakhali" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Patuakhali') ? 'selected' : ''; ?>>Patuakhali</option>
													<option value="Pirojpur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Pirojpur') ? 'selected' : ''; ?>>Pirojpur</option>
													<option value="Rajbari" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Rajbari') ? 'selected' : ''; ?>>Rajbari</option>
													<option value="Rajshahi" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Rajshahi') ? 'selected' : ''; ?>>Rajshahi</option>
													<option value="Rangamati" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Rangamati') ? 'selected' : ''; ?>>Rangamati</option>
													<option value="Rangpur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Rangpur') ? 'selected' : ''; ?>>Rangpur</option>
													<option value="Satkhira" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Satkhira') ? 'selected' : ''; ?>>Satkhira</option>
													<option value="Shariatpur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Shariatpur') ? 'selected' : ''; ?>>Shariatpur</option>
													<option value="Sherpur" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Sherpur') ? 'selected' : ''; ?>>Sherpur</option>
													<option value="Sirajganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Sirajganj') ? 'selected' : ''; ?>>Sirajganj</option>
													<option value="Sunamganj" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Sunamganj') ? 'selected' : ''; ?>>Sunamganj</option>
													<option value="Sylhet" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Sylhet') ? 'selected' : ''; ?>>Sylhet</option>
													<option value="Tangail" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Tangail') ? 'selected' : ''; ?>>Tangail</option>
													<option value="Thakurgaon" <?php echo (isset($healthcare['district']) && $healthcare['district'] == 'Thakurgaon') ? 'selected' : ''; ?>>Thakurgaon</option>
												</select>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">NID Number</label>
												<input type="text" class="form-control" name="nid_number" id="nid_number" value="<?php echo htmlspecialchars($healthcare['nid_number'] ?? ''); ?>" placeholder="Enter your 10, 13, or 17 digit NID number" pattern="[0-9]{10}|[0-9]{13}|[0-9]{17}" title="NID must be 10, 13, or 17 digits" required>
												<small class="form-text text-muted">Must be 10, 13, or 17 digits</small>
											</div>
										</div>
									</div>
								</div>

								<!-- Family Members -->
								<?php
								$family_list = [];
								if (!empty($healthcare['family_members'])) {
									$dec = json_decode($healthcare['family_members'], true);
									if (is_array($dec)) $family_list = $dec;
								}
								if (empty($family_list)) $family_list = [['relation' => '', 'name' => '', 'nid' => '']];
								?>
								<div class="setting-title">
									<h5>Family Members</h5>
								</div>
								<div class="setting-card">
									<div id="family-members-container">
										<?php foreach ($family_list as $idx => $fm): ?>
										<div class="family-member-row row align-items-end mb-2">
											<div class="col-md-3 col-6">
												<label class="form-label">Relation</label>
												<select class="form-control" name="family_relation[]">
													<option value="">Select</option>
													<option value="Father" <?php echo (($fm['relation'] ?? '') === 'Father') ? 'selected' : ''; ?>>Father</option>
													<option value="Mother" <?php echo (($fm['relation'] ?? '') === 'Mother') ? 'selected' : ''; ?>>Mother</option>
													<option value="Sister" <?php echo (($fm['relation'] ?? '') === 'Sister') ? 'selected' : ''; ?>>Sister</option>
													<option value="Wife" <?php echo (($fm['relation'] ?? '') === 'Wife') ? 'selected' : ''; ?>>Wife</option>
												</select>
											</div>
											<div class="col-md-3 col-6">
												<label class="form-label">Name</label>
												<input type="text" class="form-control" name="family_name[]" value="<?php echo htmlspecialchars($fm['name'] ?? ''); ?>" placeholder="Name">
											</div>
											<div class="col-md-3 col-6">
												<label class="form-label">NID</label>
												<input type="text" class="form-control" name="family_nid[]" value="<?php echo htmlspecialchars($fm['nid'] ?? ''); ?>" placeholder="NID">
											</div>
											<div class="col-md-3 col-6 d-flex align-items-end gap-1 pb-1">
												<button type="button" class="btn btn-outline-primary btn-sm btn-add-family" title="Add more"><i class="fa-solid fa-plus"></i></button>
												<button type="button" class="btn btn-outline-danger btn-sm btn-remove-family" title="Remove"><i class="fa-solid fa-minus"></i></button>
											</div>
										</div>
										<?php endforeach; ?>
									</div>
									<p class="form-text text-muted mb-0">Add your family members. Use the <i class="fa-solid fa-plus"></i> icon to add more.</p>
								</div>

								<!-- File Uploads -->
								<div class="setting-title">
									<h5>Document Uploads</h5>
								</div>
								<div class="setting-card">
									<div class="row">
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">NID Card (File Upload)</label>
												<div class="change-avatar img-upload">
													<div class="profile-img">
														<i class="fa-solid fa-id-card"></i>
													</div>
													<div class="upload-img">
														<h6>NID Card</h6>
														<div class="imgs-load d-flex align-items-center">
															<div class="change-photo">
																Upload
																<input type="file" class="upload" name="nid_file" accept=".pdf,.jpg,.png,.jpeg">
															</div>
														</div>
														<p class="form-text">PDF, JPG, PNG up to 5MB</p>
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="form-wrap">
												<label class="form-label">Degrees Certificate</label>
												<div class="change-avatar img-upload">
													<div class="profile-img">
														<i class="fa-solid fa-graduation-cap"></i>
													</div>
													<div class="upload-img">
														<h6>Degrees Certificate</h6>
														<div class="imgs-load d-flex align-items-center">
															<div class="change-photo">
																Upload
																<input type="file" class="upload" name="degrees_certificate" accept=".pdf,.jpg,.png,.jpeg">
															</div>
														</div>
														<p class="form-text">PDF, JPG, PNG up to 5MB</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<!-- Submit Button -->
								<div class="modal-btn text-end">
									<button type="submit" class="btn btn-primary prime-btn" id="saveBtn">
										<span class="btn-text">Save Changes</span>
										<div class="spinner-border spinner-border-sm ms-2 d-none" role="status">
											<span class="visually-hidden">Loading...</span>
										</div>
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
    <!-- /Page Content -->
<?php include 'footer.php'; ?>
		<script>
		$(document).ready(function() {
			// Family members: add row
			$(document).on('click', '.btn-add-family', function() {
				var $first = $('.family-member-row').first();
				var $clone = $first.clone();
				$clone.find('select').val('');
				$clone.find('input[type="text"]').val('');
				$('#family-members-container').append($clone);
				$('.btn-remove-family').show();
			});

			// Family members: remove row (keep at least one)
			$(document).on('click', '.btn-remove-family', function() {
				if ($('.family-member-row').length > 1) {
					$(this).closest('.family-member-row').remove();
					if ($('.family-member-row').length === 1) $('.btn-remove-family').hide();
				}
			});
			if ($('.family-member-row').length === 1) $('.btn-remove-family').hide();

			// NID number real-time validation
			$('input[name="nid_number"]').on('input', function() {
				var nid = $(this).val().trim();
				var isValid = nid === '' || /^[0-9]{10}$|^[0-9]{13}$|^[0-9]{17}$/.test(nid);
				
				if (nid !== '' && !isValid) {
					$(this).addClass('is-invalid');
					if ($(this).next('.invalid-feedback').length === 0) {
						$(this).after('<div class="invalid-feedback">NID must be 10, 13, or 17 digits</div>');
					}
				} else {
					$(this).removeClass('is-invalid');
					$(this).next('.invalid-feedback').remove();
				}
			});

			// Handle profile settings form submissions
			$('form[action="php/save-healthcare-profile-settings.php"]').on('submit', function(e) {
				e.preventDefault();

				var form = $(this);
				var submitBtn = form.find('button[type="submit"]');
				var originalText = submitBtn.html();

				// Validate NID number
				var nidNumber = form.find('input[name="nid_number"]').val().trim();
				if (nidNumber && !/^[0-9]{10}$|^[0-9]{13}$|^[0-9]{17}$/.test(nidNumber)) {
					showAlert('danger', 'NID number must be exactly 10, 13, or 17 digits.');
					submitBtn.prop('disabled', false).html(originalText);
					form.find('input[name="nid_number"]').focus();
					return false;
				}

				// Disable button and show loading
				submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Saving...');

				// Prepare form data
				var formData = new FormData(this);

				// Submit via AJAX
				$.ajax({
					url: form.attr('action'),
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							// Show success message
							showAlert('success', response.message || 'Profile settings updated successfully!');

							// Update profile images and name dynamically
							if (response.profile_image && response.profile_image.trim() !== '') {
								$('.booking-doc-img img').attr('src', response.profile_image);
								$('.user-img img').attr('src', response.profile_image);
								$('.avatar-img').attr('src', response.profile_image);
								form.find('input[name="profile_image"]').val('');
							}

							// Update name if it was changed
							var newName = form.find('input[name="name"]').val();
							if (newName && newName.trim() !== '') {
								$('.profile-det-info h3').text(newName);
								$('.user-text h6').text(newName);
							}

							// Reset form button
							submitBtn.prop('disabled', false).html(originalText);
						} else {
							showAlert('danger', response.message || 'Failed to save profile settings.');
							submitBtn.prop('disabled', false).html(originalText);
						}
					},
					error: function(xhr, status, error) {
						console.error('AJAX Error:', xhr.responseText);
						var errorMsg = 'An error occurred while saving. Please try again.';
						try {
							var response = JSON.parse(xhr.responseText);
							errorMsg = response.message || errorMsg;
						} catch(e) {}

						showAlert('danger', errorMsg);
						submitBtn.prop('disabled', false).html(originalText);
					}
				});
			});

			// Function to show alerts
			function showAlert(type, message) {
				$('.alert').remove();
				var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
				var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
					'<strong>' + (type === 'success' ? 'Success!' : 'Error!') + '</strong> ' + message +
					'<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
					'</div>';
				$('body').prepend(alertHtml);
				if (type === 'success') {
					setTimeout(function() {
						$('.alert-success').fadeOut();
					}, 5000);
				}
				$('html, body').animate({ scrollTop: 0 }, 500);
			}
		});
		</script>
	</body>
</html>
