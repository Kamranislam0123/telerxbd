<?php
session_start();

// Force patient log in before booking
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php?redirect=emergency-booking.php');
    exit;
}

// Prefill mobile number if available
$prefill_mobile = $_SESSION['patient_phone'] ?? '';
if (empty($prefill_mobile)) {
    try {
        require_once __DIR__ . '/php/config.php';
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

                <form id="emergency-booking-form">
                    <div class="mb-4">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="mobile" id="emergency-mobile" placeholder="e.g. 01XXXXXXXXX" value="<?php echo htmlspecialchars($prefill_mobile); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" id="emergency-payment-method">
                            <option value="bkash" selected>bKash (no payment required)</option>
                        </select>
                    </div>
                    
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
$(document).ready(function() {
    $('#emergency-booking-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
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
                    messageDiv.removeClass('alert-danger').addClass('alert-success').html('<strong>Booking Saved!</strong> Redirecting to payment page...').fadeIn();
                    setTimeout(function() {
                        window.location.href = 'payment.php?appointment_id=' + response.appointment_id;
                    }, 1000);
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
