<?php
// Use config so session cookie path is '/' and works for dashboard after login
require_once __DIR__ . '/php/config.php';
// Full same-origin URL for login AJAX (avoids status 0 / CORS / mixed content issues on live)
$login_ajax_url = (defined('APP_BASE') ? APP_BASE : '') . '/php/login.php';
include 'header.php';?>

<div class="login-page-wrapper">
    <main class="login-card">

        <section class="brand-panel">
            <div class="brand-logo">TeleRx Bangladesh</div>
        </section>

        <section class="form-panel">

            <h1 class="form-title">Login</h1>
            <p class="form-subtitle">Access to your account.</p>

            <div id="login-message" class="alert" style="display: none;"></div>
            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger">Invalid email or password. Please try again.</div>
            <?php endif; ?>
            <form id="doctor-login-form" method="POST" action="<?php echo htmlspecialchars($login_ajax_url); ?>">
                <input type="hidden" id="login-redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? ''); ?>">
            <div class="mb-3">
                <label class="form-label">E-mail or Mobile</label>
                <input type="text" class="form-control" name="email" id="login-email" placeholder="Enter email or mobile number" required>
            </div>
                <div class="mb-3">
                    <div class="form-group-flex">
                    <label class="form-label">Password</label>

                </div>
                <div class="pass-group">
                    <input type="password" class="form-control pass-input" name="password" id="login-password" required>
                    <span class="custom-toggle-password feather-eye-off" style="cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></span>
                </div>
            </div>
            <div class="mb-3 form-check-box">
                <div class="form-group-flex">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember_me" value="1" checked>
                        <label class="form-check-label" style="font-size:12px;" for="remember">
                            Remember Me
                        </label>
                        <a href="#" class="right-text forgot forgot">Forgot Password?</a>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <button class="btn btn-primary-gradient w-100" type="submit" id="login-btn">Sign in</button>
            </div>
            <div class="account-signup">
                <p>Don't have account?</p>
                <p><a href="registration">Register Now</a></p>
            </div>
        </section>
    </main>
</div>

<?php include 'footer.php'; ?>

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
                remember_me: $('#remember').is(':checked') ? 1 : 0,
                redirect: $('#login-redirect').val()
            };
            
            // Submit via AJAX (full same-origin URL; server redirects if not AJAX)
            $.ajax({
                url: <?php echo json_encode($login_ajax_url); ?>,
                type: 'POST',
                data: formData,
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response && response.success) {
                        var userType = (response.user_type || 'doctor');
                        var redirectUrl = (response.redirect && String(response.redirect).trim()) ? String(response.redirect).trim() : '';
                        if (!redirectUrl) {
                            switch(userType) {
                                case 'healthcare':
                                    redirectUrl = 'health-worker-profile-settings.php';
                                    break;
                                case 'special_tid':
                                    redirectUrl = 'health-worker-dashboard.php';
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
                    var errorMsg;
                    if (xhr.status === 0) {
                        errorMsg = 'Connection failed (request blocked or network error). ';
                        errorMsg += 'Try: (1) Use the same protocol as the page (e.g. https:// if the site is secure). ';
                        errorMsg += '(2) Open in a private/incognito window. (3) Disable browser extensions that block requests. ';
                        errorMsg += 'Or <button type="button" class="btn btn-link p-0 align-baseline btn-login-fallback">log in without AJAX</button>.';
                    } else {
                        errorMsg = 'An error occurred. Please try again later.';
                        var resp = xhr.responseText;
                        try {
                            if (typeof resp === 'string' && resp.length > 0) {
                                var response = JSON.parse(resp);
                                if (response && response.message) errorMsg = response.message;
                                if (response && response.error) errorMsg += '<br><small>' + response.error + '</small>';
                            }
                        } catch (e) {
                            if (typeof resp === 'string' && resp.length > 0)
                                errorMsg += '<br><small>Response: ' + resp.substring(0, 200) + '</small>';
                        }
                        errorMsg += '<br><small>Status: ' + (xhr.status || status) + (error ? ' | ' + error : '') + '</small>';
                    }
                    console.error('Login Error:', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText, error: error });
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
        $(document).on('click', '.btn-login-fallback', function() {
            document.getElementById('doctor-login-form').submit();
        });

        // Fix for eye button password show/hide
        $(document).off('click', '.custom-toggle-password').on('click', '.custom-toggle-password', function () {
            $(this).toggleClass("feather-eye feather-eye-off");
            var input = $(this).closest('.pass-group').find('input');
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    });
</script>