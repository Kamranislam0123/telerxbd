<?php
session_start();
include 'header.php';?>

<head>
<body>
<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <h2 class="breadcrumb-title">Register Now</h2>
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
                <!-- Registration Tab Content -->
                <div class="account-content">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-12 col-lg-6 login-right">
                            <div class="login-header">
                                <h3>Join TeleRx Bangladesh</h3>
                                <p class="text-muted">Please make sure your registration type</p>
                            </div>
                            <!-- Registration Tabs -->
                            <ul class="nav nav-tabs nav-justified mb-4" id="registrationTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="patient-tab" data-bs-toggle="tab" data-bs-target="#patient" type="button" role="tab" aria-controls="patient" aria-selected="true">
                                        <i class="isax isax-user me-1"></i>Patient
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="doctor-tab" data-bs-toggle="tab" data-bs-target="#doctor" type="button" role="tab" aria-controls="doctor" aria-selected="false">
                                        <i class="isax isax-stethoscope me-1"></i>Doctor
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="health-worker-tab" data-bs-toggle="tab" data-bs-target="#health-worker" type="button" role="tab" aria-controls="health-worker" aria-selected="false">
                                        <i class="isax isax-hospital me-1"></i>Health-worker
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="registrationTabContent">

                                <!-- Patient Registration Tab -->
                                <div class="tab-pane fade show active" id="patient" role="tabpanel" aria-labelledby="patient-tab">
                                    <div id="patient-message" class="alert" style="display: none;"></div>
                                    <form id="patient-register-form">
                                        <div class="mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" name="name" id="patient-name" placeholder="Enter your full name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" name="email" id="patient-email" placeholder="Enter your email address" required>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-group-flex">
                                                <label class="form-label">Create Password</label>
                                            </div>
                                            <div class="pass-group">
                                                <input type="password" class="form-control pass-input" name="password" id="patient-password" placeholder="Create a strong password" required>
                                                <span class="feather-eye-off toggle-password"></span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-primary-gradient w-100" type="submit" id="patient-register-btn">Register as Patient</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Doctor Registration Tab -->
                                <div class="tab-pane fade" id="doctor" role="tabpanel" aria-labelledby="doctor-tab">
                                    <div id="doctor-message" class="alert" style="display: none;"></div>
                                    <form id="doctor-register-form">
                                        <div class="mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" name="name" id="doctor-name" placeholder="Enter your full name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" name="email" id="doctor-email" placeholder="Enter your email address" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input class="form-control form-control-lg group_formcontrol form-control-phone" id="doctor-phone" name="phone" type="text" placeholder="Enter your phone number" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">BMDC Registration Number</label>
                                            <input type="text" class="form-control" name="bmdc_no" id="doctor-bmdc_no" placeholder="Enter your BMDC registration number" required>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-group-flex">
                                                <label class="form-label">Create Password</label>
                                            </div>
                                            <div class="pass-group">
                                                <input type="password" class="form-control pass-input" name="password" id="doctor-password" placeholder="Create a strong password" required>
                                                <span class="feather-eye-off toggle-password"></span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-primary-gradient w-100" type="submit" id="doctor-register-btn">Register as Doctor</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Health-worker Registration Tab -->
                                <div class="tab-pane fade" id="health-worker" role="tabpanel" aria-labelledby="health-worker-tab">
                                    <div id="health-worker-message" class="alert" style="display: none;"></div>
                                    <form id="health-worker-register-form">
                                        <div class="mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" name="name" id="health-worker-name" placeholder="Enter your full name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" name="email" id="health-worker-email" placeholder="Enter your email address" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Mobile Number</label>
                                            <input type="text" class="form-control" name="phone" id="health-worker-phone" placeholder="Enter your mobile number" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">NID Number</label>
                                            <input type="text" class="form-control" name="nid_number" id="health-worker-nid" placeholder="Enter your National ID number" required>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-group-flex">
                                                <label class="form-label">Create Password</label>
                                            </div>
                                            <div class="pass-group">
                                                <input type="password" class="form-control pass-input" name="password" id="health-worker-password" placeholder="Create a strong password" required>
                                                <span class="feather-eye-off toggle-password"></span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-primary-gradient w-100" type="submit" id="health-worker-register-btn">Register as Health-worker</button>
                                        </div>
                                    </form>
                                </div>

                            </div>

                            <div class="account-signup">
                                <p>Already have account? <a href="login.php">Sign In</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Registration Tab Content -->

            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</div>

<!-- /Page Content -->

    <script>
    $(document).ready(function() {
        // Initialize phone input for doctor registration
        var doctorPhoneInput = document.querySelector("#doctor-phone");
        if (doctorPhoneInput) {
            window.intlTelInput(doctorPhoneInput, {
                initialCountry: "bd",
                separateDialCode: true,
                utilsScript: "assets/plugins/intltelinput/js/utils.js"
            });
        }

        // Patient Registration Form Handler
        $('#patient-register-form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var submitBtn = $('#patient-register-btn');
            var messageDiv = $('#patient-message');

            // Disable submit button
            submitBtn.prop('disabled', true).text('Processing...');
            messageDiv.hide().removeClass('alert-success alert-danger');

            // Get form data
            var formData = {
                user_type: 'patient',
                name: $('#patient-name').val(),
                email: $('#patient-email').val(),
                password: $('#patient-password').val()
            };

            // Submit via AJAX
            $.ajax({
                url: 'php/register.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        messageDiv.addClass('alert-success').html('<strong>Success!</strong> ' + response.message).fadeIn();
                        setTimeout(function() {
                            window.location.href = response.redirect || 'login.html';
                        }, 1500);
                    } else {
                        var errorMsg = response.message || 'Registration failed. Please try again.';
                        if (response.errors && response.errors.length > 0) {
                            errorMsg += '<ul class="mb-0 mt-2">';
                            response.errors.forEach(function(error) {
                                errorMsg += '<li>' + error + '</li>';
                            });
                            errorMsg += '</ul>';
                        }
                        messageDiv.addClass('alert-danger').html('<strong>Error!</strong> ' + errorMsg).fadeIn();
                        submitBtn.prop('disabled', false).text('Register as Patient');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = 'An error occurred. Please try again later.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    messageDiv.addClass('alert-danger').html('<strong>Error!</strong> ' + errorMsg).fadeIn();
                    submitBtn.prop('disabled', false).text('Register as Patient');
                }
            });
        });

        // Doctor Registration Form Handler
        $('#doctor-register-form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var submitBtn = $('#doctor-register-btn');
            var messageDiv = $('#doctor-message');

            // Disable submit button
            submitBtn.prop('disabled', true).text('Processing...');
            messageDiv.hide().removeClass('alert-success alert-danger');

            // Get form data
            var formData = {
                user_type: 'doctor',
                name: $('#doctor-name').val(),
                email: $('#doctor-email').val(),
                phone: $('#doctor-phone').val(),
                bmdc_no: $('#doctor-bmdc_no').val(),
                password: $('#doctor-password').val()
            };

            // Submit via AJAX
            $.ajax({
                url: 'php/register.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        messageDiv.addClass('alert-success').html('<strong>Success!</strong> ' + response.message).fadeIn();
                        setTimeout(function() {
                            window.location.href = response.redirect || 'doctor-profile-settings.php';
                        }, 1500);
                    } else {
                        var errorMsg = response.message || 'Registration failed. Please try again.';
                        if (response.errors && response.errors.length > 0) {
                            errorMsg += '<ul class="mb-0 mt-2">';
                            response.errors.forEach(function(error) {
                                errorMsg += '<li>' + error + '</li>';
                            });
                            errorMsg += '</ul>';
                        }
                        messageDiv.addClass('alert-danger').html('<strong>Error!</strong> ' + errorMsg).fadeIn();
                        submitBtn.prop('disabled', false).text('Register as Doctor');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = 'An error occurred. Please try again later.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    messageDiv.addClass('alert-danger').html('<strong>Error!</strong> ' + errorMsg).fadeIn();
                    submitBtn.prop('disabled', false).text('Register as Doctor');
                }
            });
        });

        // Health-worker Registration Form Handler
        $('#health-worker-register-form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var submitBtn = $('#health-worker-register-btn');
            var messageDiv = $('#health-worker-message');

            // Disable submit button
            submitBtn.prop('disabled', true).text('Processing...');
            messageDiv.hide().removeClass('alert-success alert-danger');

            // Get form data
            var name = $('#health-worker-name').val().trim();
            var email = $('#health-worker-email').val().trim();
            var phone = $('#health-worker-phone').val().trim();
            var nid_number = $('#health-worker-nid').val().trim();
            var password = $('#health-worker-password').val();

            // Client-side validation
            var clientErrors = [];
            if (!name || name.length < 2) {
                clientErrors.push('Name must be at least 2 characters');
            }
            if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                clientErrors.push('Please enter a valid email address');
            }
            if (!phone || phone.replace(/\D/g, '').length < 10) {
                clientErrors.push('Mobile number must contain at least 10 digits');
            }
            if (!nid_number || nid_number.length < 10) {
                clientErrors.push('NID number must be at least 10 characters');
            }
            if (!password || password.length < 6) {
                clientErrors.push('Password must be at least 6 characters');
            }

            if (clientErrors.length > 0) {
                var errorMsg = 'Please fix the following errors:<ul class="mb-0 mt-2">';
                clientErrors.forEach(function(err) {
                    errorMsg += '<li>' + err + '</li>';
                });
                errorMsg += '</ul>';
                messageDiv.addClass('alert-danger').html('<strong>Validation Error!</strong> ' + errorMsg).fadeIn();
                submitBtn.prop('disabled', false).text('Register as Health-worker');
                return;
            }

            var formData = {
                user_type: 'healthcare',
                name: name,
                email: email,
                phone: phone,
                nid_number: nid_number,
                password: password
            };

            // Debug: Log form data (remove in production)
            console.log('Form Data:', formData);

            // Submit via AJAX
            $.ajax({
                url: 'php/register.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        messageDiv.addClass('alert-success').html('<strong>Success!</strong> ' + response.message).fadeIn();
                        setTimeout(function() {
                            window.location.href = response.redirect || 'login.html';
                        }, 1500);
                    } else {
                        var errorMsg = response.message || 'Registration failed. Please try again.';
                        if (response.errors && response.errors.length > 0) {
                            errorMsg += '<ul class="mb-0 mt-2">';
                            response.errors.forEach(function(error) {
                                errorMsg += '<li>' + error + '</li>';
                            });
                            errorMsg += '</ul>';
                        }
                        messageDiv.addClass('alert-danger').html('<strong>Error!</strong> ' + errorMsg).fadeIn();
                        submitBtn.prop('disabled', false).text('Register as Health-worker');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = 'An error occurred. Please try again later.';
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                        if (response.errors && response.errors.length > 0) {
                            errorMsg += '<ul class="mb-0 mt-2">';
                            response.errors.forEach(function(err) {
                                errorMsg += '<li>' + err + '</li>';
                            });
                            errorMsg += '</ul>';
                        }
                    } catch(e) {
                        console.error('Error parsing response:', xhr.responseText);
                        if (xhr.responseText) {
                            errorMsg += '<br><small>' + xhr.responseText.substring(0, 200) + '</small>';
                        }
                    }
                    messageDiv.addClass('alert-danger').html('<strong>Error!</strong> ' + errorMsg).fadeIn();
                    submitBtn.prop('disabled', false).text('Register as Health-worker');
                }
            });
        });

        // Tab switching functionality
        $('#registrationTabs .nav-link').on('shown.bs.tab', function (e) {
            // Clear any existing messages when switching tabs
            $('.alert').hide();
            // Reset all form buttons
            $('.btn[type="submit"]').prop('disabled', false).each(function() {
                var btnId = $(this).attr('id');
                if (btnId.includes('patient')) {
                    $(this).text('Register as Patient');
                } else if (btnId.includes('doctor')) {
                    $(this).text('Register as Doctor');
                } else if (btnId.includes('health-worker')) {
                    $(this).text('Register as Health-worker');
                }
            });
        });
    });
    </script>

</body>
</html>