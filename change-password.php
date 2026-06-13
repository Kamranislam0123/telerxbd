<?php
require_once __DIR__ . '/php/config.php';

// Redirect to login if not logged in
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Build AJAX URL
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && (strtolower($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '') === 'on' || $_SERVER['HTTP_X_FORWARDED_SSL'] === '1'));
$rp_host  = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
$rp_base  = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$rp_path  = ($rp_base === '' || $rp_base === '/') ? '' : $rp_base;
$ajax_url = ($is_https ? 'https://' : 'http://') . $rp_host . $rp_path . '/php/change-password.php';

// Determine where to redirect after success (back to dashboard)
$user_type = $_SESSION['user_type'] ?? '';
$redirect_after = match($user_type) {
    'doctor'      => 'doctor-profile-settings.php',
    'healthcare'  => 'health-worker-profile-settings.php',
    'special_tid' => 'health-worker-dashboard.php',
    'patient'     => 'patient-profile-settings.php',
    default       => 'login.php',
};

include 'header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <h2 class="breadcrumb-title">Change Password</h2>
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

                <div class="account-content">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-12 col-lg-6 login-right">

                            <div class="login-header">
                                <h3>Change <span>Password</span></h3>
                                <p class="text-muted small mt-2">Enter your current password, then set a new one.</p>
                            </div>

                            <!-- Alert message -->
                            <div id="rp-message" class="alert" style="display:none;"></div>

                            <form id="reset-password-form" novalidate autocomplete="off">

                                <!-- Current Password -->
                                <div class="mb-3">
                                    <label class="form-label" for="rp-current">Current Password</label>
                                    <div class="pass-group">
                                        <input
                                            type="password"
                                            class="form-control pass-input"
                                            id="rp-current"
                                            name="current_password"
                                            placeholder="Enter your current password"
                                            required
                                            autocomplete="current-password"
                                        >
                                        <span class="custom-toggle-password feather-eye-off" style="cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></span>
                                    </div>
                                </div>

                                <!-- New Password -->
                                <div class="mb-3">
                                    <label class="form-label" for="rp-new">New Password</label>
                                    <div class="pass-group" id="passwordInput">
                                        <input
                                            type="password"
                                            class="form-control pass-input"
                                            id="rp-new"
                                            name="new_password"
                                            placeholder="Enter new password (min. 6 chars)"
                                            required
                                            autocomplete="new-password"
                                        >
                                        <span class="custom-toggle-password feather-eye-off" style="cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></span>
                                        <span class="pass-checked"><i class="feather-check"></i></span>
                                    </div>
                                    <div class="password-strength" id="passwordStrength">
                                        <span id="poor"></span>
                                        <span id="weak"></span>
                                        <span id="strong"></span>
                                        <span id="heavy"></span>
                                    </div>
                                    <div id="passwordInfo"></div>
                                </div>

                                <!-- Confirm New Password -->
                                <div class="mb-3">
                                    <label class="form-label" for="rp-confirm">Confirm New Password</label>
                                    <div class="pass-group">
                                        <input
                                            type="password"
                                            class="form-control pass-input-sub"
                                            id="rp-confirm"
                                            name="confirm_password"
                                            placeholder="Re-enter new password"
                                            required
                                            autocomplete="new-password"
                                        >
                                        <span class="custom-toggle-password-sub feather-eye-off" style="cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></span>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="mb-3">
                                    <button class="btn btn-primary-gradient w-100" type="submit" id="rp-btn">
                                        <i class="feather-lock me-1"></i> Change Password
                                    </button>
                                </div>

                                <div class="account-signup text-center">
                                    <p><a href="<?php echo htmlspecialchars($redirect_after); ?>">
                                        <i class="feather-arrow-left me-1"></i> Back to Dashboard
                                    </a></p>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

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
<!-- Feather Icon JS -->
<script src="assets/js/feather.min.js"></script>
<!-- Validation JS (password strength meter) -->
<script src="assets/js/validation.js"></script>
<!-- Custom JS -->
<script src="assets/js/script.js"></script>

<script>
$(document).ready(function () {
    var redirectAfter = <?php echo json_encode($redirect_after); ?>;

    $('#reset-password-form').on('submit', function (e) {
        e.preventDefault();

        var current = $('#rp-current').val();
        var newPass = $('#rp-new').val();
        var confirm = $('#rp-confirm').val();
        var btn     = $('#rp-btn');

        // Client-side validation
        if (!current) {
            showMsg('danger', 'Please enter your current password.');
            return;
        }
        if (newPass.length < 6) {
            showMsg('danger', 'New password must be at least 6 characters.');
            return;
        }
        if (newPass !== confirm) {
            showMsg('danger', 'Passwords do not match.');
            return;
        }

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');
        $('#rp-message').hide().removeClass('alert-success alert-danger');

        $.ajax({
            url: <?php echo json_encode($ajax_url); ?>,
            type: 'POST',
            data: {
                current_password: current,
                new_password:     newPass,
                confirm_password: confirm
            },
            dataType: 'json',
            success: function (res) {
                if (res && res.success) {
                    showMsg('success', '<i class="feather-check-circle me-1"></i>' + (res.message || 'Password changed!'));
                    $('#reset-password-form')[0].reset();
                    setTimeout(function () {
                        window.location.href = redirectAfter;
                    }, 2000);
                } else {
                    showMsg('danger', '<i class="feather-alert-circle me-1"></i>' + (res.message || 'An error occurred.'));
                    btn.prop('disabled', false).html('<i class="feather-lock me-1"></i> Change Password');
                }
            },
            error: function () {
                showMsg('danger', 'Could not connect. Please try again.');
                btn.prop('disabled', false).html('<i class="feather-lock me-1"></i> Change Password');
            }
        });
    });

    // Toggle Password (overriding global script.js to ensure it targets the correct field and bypasses cache)
    $(document).off('click', '.custom-toggle-password').on('click', '.custom-toggle-password', function () {
        $(this).toggleClass("feather-eye feather-eye-off");
        var input = $(this).closest('.pass-group').find('input');
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $(document).off('click', '.custom-toggle-password-sub').on('click', '.custom-toggle-password-sub', function () {
        $(this).toggleClass("feather-eye feather-eye-off");
        var input = $(this).closest('.pass-group').find('input');
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    function showMsg(type, html) {
        $('#rp-message')
            .removeClass('alert-success alert-danger')
            .addClass('alert alert-' + type)
            .html(html)
            .fadeIn(200);
    }
});
</script>

</body>
</html>
