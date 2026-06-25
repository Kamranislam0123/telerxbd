<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit;
}

$appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;
if ($appointment_id <= 0) {
    header('Location: index.php');
    exit;
}

include 'header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <h2 class="breadcrumb-title">Live Emergency Dashboard</h2>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->

<!-- Page Content -->
<div class="content">
    <div class="container">
        <div class="row">
            <!-- Vitals Section -->
            <div class="col-md-5 col-lg-4">
                <div class="card dash-card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="card-title mb-0 text-white"><i class="fa-solid fa-heart-pulse me-2"></i> Real-time Vitals</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Update your current condition below so the doctor can monitor it in real time during the call.</p>
                        
                        <form id="emergency-vitals-form">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Pulse Rate (bpm)</label>
                                <input type="number" class="form-control" name="pulse" placeholder="e.g. 72">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Blood Pressure (mmHg)</label>
                                <input type="text" class="form-control" name="blood_pressure" placeholder="e.g. 120/80">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SpO2 (%)</label>
                                <input type="number" class="form-control" name="spo2" placeholder="e.g. 98">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Body Temperature (&deg;F)</label>
                                <input type="text" class="form-control" name="body_temperature" placeholder="e.g. 98.6">
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="update-vitals-btn">Update Vitals</button>
                        </form>
                        <div id="vitals-msg" class="mt-2 text-success" style="display:none; font-size:13px;"><i class="fa-solid fa-check"></i> Vitals updated</div>
                    </div>
                </div>
            </div>

            <!-- Call Section -->
            <div class="col-md-7 col-lg-8">
                <div class="card dash-card text-center py-5">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="emergency-icon-wrap mx-auto mb-3" style="width:80px;height:80px;background:#dc3545;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px;">
                                <i class="fa-solid fa-video pulse-animation"></i>
                            </div>
                            <h3 class="mb-2">Doctor is Available</h3>
                            <p class="text-muted">Start the live video consultation immediately. The doctor is on standby.</p>
                        </div>
                        
                        <a href="video-call.php?appointment_id=<?php echo $appointment_id; ?>" class="btn btn-danger btn-lg px-5 py-3" style="font-size: 18px; font-weight: bold; border-radius: 10px;">
                            <i class="fa-solid fa-video me-2"></i> Join Video Call
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<style>
.pulse-animation {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>

<?php include 'footer.php'; ?>

<!-- jQuery -->
<script src="assets/js/jquery-3.7.1.min.js"></script>

<!-- Bootstrap Core JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="assets/js/script.js"></script>

<script>
$(document).ready(function() {
    $('#update-vitals-btn').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).text('Updating...');
        
        $.ajax({
            url: 'php/patient-update-vitals.php',
            type: 'POST',
            data: $('#emergency-vitals-form').serialize(),
            success: function(response) {
                btn.prop('disabled', false).text('Update Vitals');
                $('#vitals-msg').fadeIn().delay(2000).fadeOut();
            },
            error: function() {
                btn.prop('disabled', false).text('Update Vitals');
                alert('Failed to update vitals.');
            }
        });
    });
});
</script>
</body>
</html>
