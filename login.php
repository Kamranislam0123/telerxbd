<?php
// Use config so session cookie path is '/' and works for dashboard after login
require_once __DIR__ . '/php/config.php';
// Base path for AJAX (works at root and in subdirectory on live)
$login_base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$login_ajax_url = ($login_base === '' ? '' : $login_base) . '/php/login.php';
include 'header.php';?>

<head>
<body>
<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <h2 class="breadcrumb-title">Login to Your TeleRx Account</h2>
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
				<div class="container-fluid">
					
					<div class="row">
						<div class="col-md-8 offset-md-2">
							
							<!-- Login Tab Content -->
							<div class="account-content">
								<div class="row align-items-center justify-content-center">
									<div class="col-md-12 col-lg-6 login-right">
										<div class="login-header">
											<h3>Login <span>TeleRx Bangladesh</span></h3>
										</div>
										<div id="login-message" class="alert" style="display: none;"></div>
										<form id="doctor-login-form" method="POST">
											<div class="mb-3">
												<label class="form-label">E-mail or Mobile</label>
												<input type="text" class="form-control" name="email" id="login-email" placeholder="Enter email or mobile number" required>
											</div>
											<div class="mb-3">
												<div class="form-group-flex">
													<label class="form-label">Password</label>
													<a href="forgot-password.html" class="forgot-link">Forgot password?</a>
												</div>
												<div class="pass-group">
													<input type="password" class="form-control pass-input" name="password" id="login-password" required>
													<span class="feather-eye-off toggle-password"></span>
												</div>
											</div>
											<div class="mb-3 form-check-box">
												<div class="form-group-flex">
													<div class="form-check mb-0">
														<input class="form-check-input" type="checkbox" id="remember" name="remember_me" value="1" checked>
														<label class="form-check-label" for="remember">
															Remember Me  
														</label>
													</div>												
												</div>
											</div>
											<div class="mb-3">
												<button class="btn btn-primary-gradient w-100" type="submit" id="login-btn">Sign in</button>
											</div>
											<div class="account-signup">
												<p>Don't have an account ? <a href="registration.php">Sign up</a></p>
											</div>
										</form>
									</div>
								</div>
							</div>
							<!-- /Login Tab Content -->
								
						</div>
					</div>

				</div>

                <?php include 'footer.php'; ?>

			</div>		
			<!-- /Page Content -->

		</div>
		<!-- /Main Wrapper -->
	  
		<!-- jQuery -->
		<script src="assets/js/jquery-3.7.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="assets/js/bootstrap.bundle.min.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/script.js"></script>
		
		<!-- Doctor Login Form Handler -->
		<script>
		$(document).ready(function() {
			$('#doctor-login-form').on('submit', function(e) {
				e.preventDefault();
				
				var form = $(this);
				var submitBtn = $('#login-btn');
				var messageDiv = $('#login-message');
				
				// Disable submit button
				submitBtn.prop('disabled', true).text('Signing in...');
				messageDiv.hide().removeClass('alert-success alert-danger');
				
				// Get form data
				var formData = {
					email: $('#login-email').val(),
					password: $('#login-password').val(),
					remember_me: $('#remember').is(':checked') ? 1 : 0
				};
				
				// Submit via AJAX (URL works at root and in subdirectory on live)
				$.ajax({
					url: <?php echo json_encode($login_ajax_url); ?>,
					type: 'POST',
					data: formData,
					dataType: 'json',
					success: function(response) {
						if (response && response.success) {
							var userType = (response.user_type || 'doctor');
							var redirectUrl = (response.redirect && String(response.redirect).trim()) ? String(response.redirect).trim() : '';
							if (!redirectUrl) {
								switch(userType) {
									case 'healthcare':
										redirectUrl = 'health-worker-profile-settings.php';
										break;
									case 'patient':
										redirectUrl = 'patient-dashboard.php';
										break;
									default:
										redirectUrl = 'doctor-profile-settings.php';
								}
							}
							messageDiv.addClass('alert-success').html('<strong>Success!</strong> ' + (response.message || 'Redirecting...')).fadeIn();
							setTimeout(function() {
								window.location.replace(redirectUrl);
							}, 800);
						} else {
							var errorMsg = response.message || 'Login failed. Please try again.';
							if (response.errors && response.errors.length > 0) {
								errorMsg += '<ul class="mb-0 mt-2">';
								response.errors.forEach(function(error) {
									errorMsg += '<li>' + error + '</li>';
								});
								errorMsg += '</ul>';
							}
							messageDiv.addClass('alert-danger').html('<strong>Error!</strong> ' + errorMsg).fadeIn();
							submitBtn.prop('disabled', false).text('Sign in');
						}
					},
					error: function(xhr, status, error) {
						var errorMsg = 'An error occurred. Please try again later.';
						
						// Try to parse JSON response
						try {
							var response = JSON.parse(xhr.responseText);
							if (response.message) {
								errorMsg = response.message;
							}
							if (response.error && response.error.length > 0) {
								errorMsg += '<br><small>Details: ' + response.error + '</small>';
							}
						} catch (e) {
							// If not JSON, show raw response or status
							if (xhr.responseText) {
								errorMsg += '<br><small>Response: ' + xhr.responseText.substring(0, 200) + '</small>';
							}
							errorMsg += '<br><small>Status: ' + status + ' | Error: ' + error + '</small>';
						}
						
						console.error('Login Error:', {
							status: xhr.status,
							statusText: xhr.statusText,
							responseText: xhr.responseText,
							error: error
						});
						
						messageDiv.addClass('alert-danger').html('<strong>Error!</strong> ' + errorMsg).fadeIn();
						submitBtn.prop('disabled', false).text('Sign in');
					},
					complete: function(xhr) {
						// If we got 200 but success callback didn't run (e.g. JSON parse issue), try to redirect anyway
						if (xhr.status === 200 && xhr.responseText) {
							try {
								var data = JSON.parse(xhr.responseText.replace(/^\s+|\s+$/g, ''));
								if (data && data.success && data.redirect) {
									var url = String(data.redirect).trim();
									if (url) {
										messageDiv.addClass('alert-success').html('<strong>Success!</strong> Redirecting...').fadeIn();
										setTimeout(function() { window.location.replace(url); }, 300);
									}
								}
							} catch (e) { /* ignore */ }
						}
					}
				});
			});
		});
		</script>
		
	</body>
</html>