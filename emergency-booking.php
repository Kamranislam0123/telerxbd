<?php
session_start();

// Allow both patients and Special TID users
$is_patient     = isset($_SESSION['patient_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['user_type'] === 'patient';
$is_special_tid = isset($_SESSION['special_tid_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['user_type'] === 'special_tid';

if (!$is_patient && !$is_special_tid) {
    header('Location: login.php?redirect=emergency-booking.php');
    exit;
}

require_once __DIR__ . '/php/config.php';

// Prefill mobile number from the correct session key
$prefill_mobile = '';
if ($is_special_tid) {
    $prefill_mobile = $_SESSION['special_tid_mobile'] ?? '';
} else {
    $prefill_mobile = $_SESSION['patient_phone'] ?? '';
    if (empty($prefill_mobile)) {
        try {
            $conn = getDBConnection();
            $stmt = $conn->prepare("SELECT phone FROM patients WHERE id = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param("i", $_SESSION['patient_id']);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res && $row = $res->fetch_assoc()) {
                    $prefill_mobile = $row['phone'] ?? '';
                }
                $stmt->close();
            }
            $conn->close();
        } catch (Exception $e) {
            error_log('emergency-booking.php prefill: ' . $e->getMessage());
        }
    }
}

// For patients: check subscription quota. For Special TID: always free.
$active_sub = null;
if ($is_patient) {
    require_once __DIR__ . '/php/subscription-helper.php';
    $active_sub = getActiveSubscription($_SESSION['patient_id']);
}

include 'header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <h2 class="breadcrumb-title">Emergency Booking</h2>
                </nav>
            </div>
        </div>
    </div>
    <div class="breadcrumb-bg">
        <img src="assets/img/bg/breadcrumb-bg-01.png" alt="img" class="breadcrumb-bg-01">
        <img src="assets/img/bg/breadcrumb-bg-02.png" alt="img" class="breadcrumb-bg-02">
    </div>
</div>
<!-- /Breadcrumb -->

<!-- Page Content -->
<div class="content align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 login-right">
                <div class="login-header text-center">
                    <h3>24/7 Emergency Care</h3>
                    <p class="text-muted">Enter your mobile number to get immediate access to an available emergency doctor.</p>
                </div>

                <div id="emergency-message" class="alert" style="display: none;"></div>

                <?php if ($is_special_tid): ?>
                    <!-- Special TID users always get a free emergency call -->
                    <div class="alert alert-success border-0 mb-4 shadow-sm" style="border-radius: 10px; background: #ecfdf5; border-left: 5px solid #10b981 !important;">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-shield-heart fs-3 text-success me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-success">Free Emergency Access &mdash; Special TID</h6>
                                <p class="mb-0 small text-secondary">
                                    As a Special TID user, emergency calls are <strong>always free</strong>. No payment is required.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php elseif ($active_sub && $active_sub['remaining_calls'] > 0): ?>
                    <div class="alert alert-success border-0 mb-4 shadow-sm" style="border-radius: 10px; background: #ecfdf5; border-left: 5px solid #10b981 !important;">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-shield-heart fs-3 text-success me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-success">Covered by <?php echo htmlspecialchars($active_sub['plan_name']); ?> Subscription</h6>
                                <p class="mb-0 small text-secondary">
                                    You have <strong><?php echo $active_sub['remaining_calls']; ?></strong> free emergency calls remaining. No payment is required for this consultation.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="emergency-booking-form">
                    <div class="mb-4">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="mobile" id="emergency-mobile" placeholder="e.g. 01XXXXXXXXX" value="<?php echo htmlspecialchars($prefill_mobile); ?>" required>
                    </div>

                    <?php if (!$is_special_tid): ?>
                    <!-- Payment method only shown to regular patients -->
                    <div class="mb-4">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" id="emergency-payment-method">
                            <?php if ($active_sub && $active_sub['remaining_calls'] > 0): ?>
                                <option value="subscription" selected>TeleRx Subscription (Free Quota)</option>
                            <?php else: ?>
                                <option value="bkash" selected>bKash (Send Money)</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <!-- Hidden free indicator for Special TID -->
                    <input type="hidden" name="payment_method" value="free_special_tid">
                    <?php endif; ?>

                    <div class="mb-4">
                        <button class="btn btn-danger w-100 btn-lg" type="submit" id="emergency-btn">
                            <i class="fa-solid fa-truck-medical me-2"></i> Confirm Emergency Booking
                        </button>
                    </div>

                    <div class="text-center text-muted small">
                        <p>By proceeding, you agree to our Terms and Conditions.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<?php include 'footer.php'; ?>

<!-- jQuery -->
<script src="assets/js/jquery-3.7.1.min.js"></script>

<!-- Bootstrap Core JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="assets/js/script.js"></script>

<script>
var IS_SPECIAL_TID = <?php echo $is_special_tid ? 'true' : 'false'; ?>;

$(document).ready(function() {
    $('#emergency-booking-form').on('submit', function(e) {
        e.preventDefault();

        var submitBtn = $('#emergency-btn');
        var messageDiv = $('#emergency-message');
        var mobile = $('#emergency-mobile').val().trim();

        if (!mobile || mobile.length < 10) {
            messageDiv.removeClass('alert-success').addClass('alert-danger').html('Please enter a valid mobile number.').show();
            return;
        }

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...');
        messageDiv.hide();

        $.ajax({
            url: 'php/book-emergency.php',
            type: 'POST',
            data: { mobile: mobile },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (IS_SPECIAL_TID || response.covered_by_subscription) {
                        // Free: go directly to emergency live dashboard
                        var label = IS_SPECIAL_TID ? 'Booking Confirmed!' : 'Covered by Subscription!';
                        messageDiv.removeClass('alert-danger').addClass('alert-success').html('<strong>' + label + '</strong> Connecting you to the emergency doctor...').fadeIn();
                        setTimeout(function() {
                            window.location.href = 'emergency-live-dashboard.php?appointment_id=' + response.appointment_id;
                        }, 1000);
                    } else {
                        // Patient needs to pay
                        messageDiv.removeClass('alert-danger').addClass('alert-success').html('<strong>Booking Saved!</strong> Redirecting to payment page...').fadeIn();
                        setTimeout(function() {
                            window.location.href = 'payment.php?appointment_id=' + response.appointment_id;
                        }, 1000);
                    }
                } else {
                    messageDiv.removeClass('alert-success').addClass('alert-danger').html('<strong>Error!</strong> ' + response.message).fadeIn();
                    submitBtn.prop('disabled', false).html('<i class="fa-solid fa-truck-medical me-2"></i> Confirm Emergency Booking');
                }
            },
            error: function() {
                messageDiv.removeClass('alert-success').addClass('alert-danger').html('<strong>Error!</strong> Connection failed. Please try again.').fadeIn();
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-truck-medical me-2"></i> Confirm Emergency Booking');
            }
        });
    });
});
</script>
</body>
</html>
