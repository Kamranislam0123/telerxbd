<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'doctor') {
    header('Location: login.php');
    exit;
}

require_once 'php/config.php';

// Ensure the logged-in doctor is the emergency doctor
$doctor_id = $_SESSION['doctor_id'];
if (!isEmergencyDoctor($doctor_id)) {
    header('Location: doctor-dashboard.php');
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
                    <h2 class="breadcrumb-title">Emergency Cases Dashboard</h2>
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
            <div class="col-md-12">
                <div class="card dash-card">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 text-white"><i class="fa-solid fa-truck-medical me-2"></i> Active Emergency Cases</h4>
                        <span class="badge bg-white text-danger" id="case-count">0</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient Mobile</th>
                                        <th>Status</th>
                                        <th>Pulse Rate</th>
                                        <th>Blood Pressure</th>
                                        <th>SpO2</th>
                                        <th>Temperature</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="emergency-cases-list">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Loading emergency cases...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
function fetchEmergencyCases() {
    $.ajax({
        url: 'php/get-emergency-cases.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var html = '';
                var count = response.data.length;
                $('#case-count').text(count + ' Active');
                
                if (count > 0) {
                    $.each(response.data, function(index, caseItem) {
                        html += '<tr>';
                        html += '<td>' + (caseItem.patient_phone || 'N/A') + '</td>';
                        html += '<td><span class="badge bg-warning"><i class="fa-solid fa-circle-notch fa-spin me-1"></i> Waiting</span></td>';
                        html += '<td><h5 class="time-title p-0">' + (caseItem.pulse ? caseItem.pulse + ' bpm' : '--') + '</h5></td>';
                        html += '<td><h5 class="time-title p-0">' + (caseItem.blood_pressure ? caseItem.blood_pressure + ' mmHg' : '--') + '</h5></td>';
                        html += '<td><h5 class="time-title p-0">' + (caseItem.spo2 ? caseItem.spo2 + '%' : '--') + '</h5></td>';
                        html += '<td><h5 class="time-title p-0">' + (caseItem.body_temperature ? caseItem.body_temperature + ' &deg;F' : '--') + '</h5></td>';
                        html += '<td>';
                        html += '<a href="video-call.php?appointment_id=' + caseItem.id + '" class="btn btn-sm bg-success-light"><i class="fa-solid fa-video"></i> Accept Call</a>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center py-5 text-muted">No active emergency cases at the moment.</td></tr>';
                }
                $('#emergency-cases-list').html(html);
            }
        }
    });
}

$(document).ready(function() {
    fetchEmergencyCases();
    // Poll every 5 seconds
    setInterval(fetchEmergencyCases, 5000);
});
</script>
</body>
</html>
