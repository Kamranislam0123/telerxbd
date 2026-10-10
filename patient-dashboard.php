<?php
/**
 * Patient Dashboard - TeleRx Bangladesh
 * Dynamic dashboard showing logged-in patient's information
 */

// Include configuration and start session
$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.php');
    exit;
}
require_once $config_path;
require_once __DIR__ . '/php/subscription-helper.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit;
}

// Get patient information from session
$patient_id = $_SESSION['patient_id'];
$patient_sub = null;

try {
    $conn = getDBConnection();
    $patient_sub = getActiveSubscription($patient_id, $conn);

    // Fetch patient's basic information
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: login.php');
        exit;
    }

    $patient = $result->fetch_assoc();
    $stmt->close();

    // Fetch appointments for dashboard
    $appointments = [];
    $upcoming = [];
    $prescriptions = [];
    
    $apt_stmt = $conn->prepare("SELECT a.*, d.name as doctor_name, dp.specialty FROM appointments a JOIN doctors d ON a.doctor_id = d.id LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id WHERE a.patient_id = ? ORDER BY a.appointment_date DESC");
    if ($apt_stmt) {
        $apt_stmt->bind_param("i", $patient_id);
        $apt_stmt->execute();
        $result = $apt_stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
            if (in_array(strtolower($row['status']), ['booked', 'confirmed', 'pending'])) {
                $upcoming[] = $row;
            }
            if (!empty($row['prescription_path'])) {
                $prescriptions[] = $row;
            }
        }
        $apt_stmt->close();
    }
    $conn->close();

    // Set default values if profile data is missing
    $patient['profile_image'] = !empty($patient['profile_image']) ? $patient['profile_image'] : 'assets/img/patients/patient.jpg';

    // Extract variables for template use
    $patient_name = $patient['name'];
    $patient_email = $patient['email'];
    $patient_profile_image = $patient['profile_image'];

} catch (Exception $e) {
    error_log("Patient dashboard error: " . $e->getMessage());
    header('Location: login.php');
    exit;
}

include 'header.php';
?>

<style>
    .dashboard-welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 30px;
        color: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .welcome-content h3 {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .welcome-content p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
    }
</style>

<!-- Page Content -->
<div class="content">
    <div class="container">

        <div class="row">
            <?php
            include 'patient-leftside-menu.php';
            ?>
            <div class="col-lg-8 col-xl-9">
                <div class="dashboard-header">
                    <h3>Dashboard</h3>
                </div>

                <!-- TeleRx Subscription Quick Widget -->
                <?php if ($patient_sub):
                    $rem_calls = max(0, (int)$patient_sub['emergency_calls_total'] - (int)$patient_sub['emergency_calls_used']);
                ?>
                    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #0e82fd 0%, #0352bd 100%); border-radius: 12px; color: #fff;">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Active Plan</span>
                                        <span class="badge bg-white text-dark"><?php echo htmlspecialchars($patient_sub['plan_name']); ?> (<?php echo htmlspecialchars($patient_sub['duration_label']); ?>)</span>
                                    </div>
                                    <h4 class="text-white fw-bold mb-1">TeleRx Health Membership</h4>
                                    <p class="mb-0 text-white-50 small">
                                        <strong><?php echo $rem_calls; ?></strong> Free Emergency Calls Left &bull;
                                        <strong><?php echo (int)$patient_sub['gp_discount_percent']; ?>%</strong> Consultation Discount &bull;
                                        Valid until <?php echo date('d M, Y', strtotime($patient_sub['end_date'])); ?>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="patient-subscription.php" class="btn btn-sm btn-light fw-bold text-primary px-3">
                                        <i class="fa-solid fa-sliders me-1"></i> Manage Plan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); border-radius: 12px; color: #fff;">
                        <div class="card-body p-3 p-md-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="text-white fw-bold mb-1"><i class="fa-solid fa-shield-heart me-2"></i>TeleRx Subscription Packages</h5>
                                    <p class="mb-0 text-white-50 small">
                                        Get 24/7 free emergency doctor consultations, doctor booking discounts, and family member coverage.
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="subscription.php" class="btn btn-sm btn-warning fw-bold text-dark px-3">
                                        <i class="fa-solid fa-arrow-right me-1"></i> View Packages
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Health Records Section (Top) -->
                <div class="row">
                    <div class="col-xl-8 d-flex">
                        <div class="dashboard-card w-100">
                            <div class="dashboard-card-head">
                                <div class="header-title">
                                    <h5>Health Records</h5>
                                </div>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="health-records icon-orange">
                                                    <span><i class="fa-solid fa-heart"></i>Heart Rate</span>
                                                    <h3>140 Bpm <sup> 2%</sup></h3>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="health-records icon-amber">
                                                    <span><i class="fa-solid fa-temperature-high"></i>Body Temperature </span>
                                                    <h3>37.5 C</h3>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="health-records icon-dark-blue">
                                                    <span><i class="fa-solid fa-notes-medical"></i>Glucose Level</span>
                                                    <h3>70 - 90<sup> 7%</sup></h3>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="health-records icon-blue">
                                                    <span><i class="fa-solid fa-highlighter"></i>SPo2</span>
                                                    <h3>96%</h3>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="health-records icon-red">
                                                    <span><i class="fa-solid fa-syringe"></i>Blood Pressure</span>
                                                    <h3>100 mg/dl<sup> 2%</sup></h3>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="health-records icon-purple">
                                                    <span><i class="fa-solid fa-user-pen"></i>BMI </span>
                                                    <h3>20.1 kg/m2</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="report-gen-date">
                                                    <p>Report generated on last visit : <?php echo date('d M Y'); ?> <span><i class="fa-solid fa-copy"></i></span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="chart-over-all-report">
                                            <h6>Overall Report</h6>
                                            <div class="circle-bar circle-bar3 report-chart">
                                                <div class="circle-graph3" data-percent="66">
                                                    <p>Last visit
                                                        <?php echo date('d M Y'); ?></p>
                                                </div>
                                            </div>
                                            <span class="health-percentage">Your health is 95% Normal</span>
                                            <a href="patient-medical-history" class="btn btn-dark w-100 rounded-pill">View Details<i class="fa-solid fa-chevron-right ms-2"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 d-flex">
                        <div class="favourites-dashboard w-100">
                            <div class="book-appointment-head">
                                <h3><span>Book a new</span>Appointment</h3>
                                <span class="add-icon"><a href="search.php"><i class="fa-solid fa-circle-plus"></i></a></span>
                            </div>
                            <div class="dashboard-card w-100">
                                <div class="dashboard-card-head">
                                    <div class="header-title">
                                        <h5>Upcoming Appointments</h5>
                                    </div>
                                        <div class="card-view-link">
                                        <a href="<?php echo (defined('APP_BASE') && APP_BASE) ? APP_BASE . '/patient-appointments.php' : 'patient-appointments.php'; ?>">View All</a>
                                    </div>
                                </div>
                                <div class="dashboard-card-body">
                                    <?php if (empty($upcoming)): ?>
                                    <p class="text-muted text-center mb-0">No upcoming appointments</p>
                                    <?php else: ?>
                                    <ul class="list-unstyled mb-0">
                                        <?php foreach (array_slice($upcoming, 0, 5) as $apt): ?>
                                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <span><strong><?php echo htmlspecialchars($apt['doctor_name'] ?? 'Doctor'); ?></strong><br>
                                            <small class="text-muted"><?php echo date('M j, Y', strtotime($apt['appointment_date'])); ?> <?php echo date('g:i A', strtotime($apt['slot_time'])); ?></small></span>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($apt['status'] ?? 'Booked'); ?></span>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Section with Tabs -->
                <div class="row">
                    <div class="col-xl-12 d-flex">
                        <div class="dashboard-card w-100">
                            <div class="dashboard-card-head">
                                <div class="header-title">
                                    <h5>Reports</h5>
                                </div>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="account-detail-table">
                                    <!-- Tab Menu -->
                                    <nav class="patient-dash-tab border-0 pb-0">
                                       <ul class="nav nav-tabs-bottom">
                                            <li class="nav-item">
                                               <a class="nav-link active" href="#appoint-tab" data-bs-toggle="tab">Appointments</a>
                                            </li>
                                            <li class="nav-item">
                                               <a class="nav-link" href="#medical-tab" data-bs-toggle="tab">Medical Records</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#prsc-tab" data-bs-toggle="tab">Prescriptions</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#invoice-tab" data-bs-toggle="tab">Invoices</a>
                                            </li>
                                       </ul>
                                   </nav>
                                   <!-- /Tab Menu -->

                                   <!-- Tab Content -->
                                   <div class="tab-content pt-0">

                                       <!-- Appointments Tab -->
                                       <div id="appoint-tab" class="tab-pane fade show active">
                                            <div class="custom-new-table">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-center mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Doctor</th>
                                                                <th>Date & Time</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (empty($appointments)): ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center py-5">
                                                                    <p class="text-muted">No appointments found</p>
                                                                </td>
                                                            </tr>
                                                            <?php else: foreach ($appointments as $apt): ?>
                                                            <tr>
                                                                <td>#APT<?php echo str_pad($apt['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                                                <td><?php echo htmlspecialchars($apt['doctor_name'] ?? '—'); ?><br><small class="text-muted"><?php echo htmlspecialchars($apt['specialty'] ?? ''); ?></small></td>
                                                                <td><?php echo date('M j, Y', strtotime($apt['appointment_date'])); ?> <?php echo date('g:i A', strtotime($apt['slot_time'])); ?></td>
                                                                <td><span class="badge bg-success"><?php echo htmlspecialchars($apt['status'] ?? 'Booked'); ?></span></td>
                                                            </tr>
                                                            <?php endforeach; endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                       </div>
                                       <!-- /Appointments Tab -->

                                       <!-- Medical Records Tab -->
                                       <div class="tab-pane fade" id="medical-tab">
                                            <div class="custom-table">
                                                <div class="table-responsive">
                                                    <table class="table table-center mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Date</th>
                                                                <th>Record For</th>
                                                                <th>Comments</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="6" class="text-center py-5">
                                                                    <p class="text-muted">No medical records found</p>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                       </div>
                                       <!-- /Medical Records Tab -->

                                       <!-- Prescriptions Tab -->
                                       <div class="tab-pane fade" id="prsc-tab">
                                            <div class="custom-table">
                                                <div class="table-responsive">
                                                    <table class="table table-center mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Doctor</th>
                                                                <th>Date</th>
                                                                <th>Prescription</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (empty($prescriptions)): ?>
                                                            <tr>
                                                                <td colspan="5" class="text-center py-5">
                                                                    <p class="text-muted">No prescriptions found</p>
                                                                </td>
                                                            </tr>
                                                            <?php else: foreach ($prescriptions as $prsc): ?>
                                                            <tr>
                                                                <td>#APT<?php echo str_pad($prsc['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                                                <td><?php echo htmlspecialchars($prsc['doctor_name'] ?? '—'); ?></td>
                                                                <td><?php echo date('M j, Y', strtotime($prsc['appointment_date'])); ?></td>
                                                                <td>Prescription File</td>
                                                                <td>
                                                                    <a href="<?php echo htmlspecialchars((defined('APP_BASE') && APP_BASE ? APP_BASE . '/' : '') . $prsc['prescription_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary shadow-sm"><i class="fa-solid fa-eye me-1"></i> View</a>
                                                                    <a href="<?php echo htmlspecialchars((defined('APP_BASE') && APP_BASE ? APP_BASE . '/' : '') . $prsc['prescription_path']); ?>" download class="btn btn-sm btn-outline-success shadow-sm ms-2"><i class="fa-solid fa-download me-1"></i> Download</a>
                                                                </td>
                                                            </tr>
                                                            <?php endforeach; endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                       </div>
                                       <!-- /Prescriptions Tab -->

                                       <!-- Invoices Tab -->
                                       <div class="tab-pane fade" id="invoice-tab">
                                            <div class="custom-table">
                                                <div class="table-responsive">
                                                    <table class="table table-center mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Invoice ID</th>
                                                                <th>Doctor</th>
                                                                <th>Date</th>
                                                                <th>Amount</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="6" class="text-center py-5">
                                                                    <p class="text-muted">No invoices found</p>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                       </div>
                                       <!-- /Invoices Tab -->

                                   </div>
                                   <!-- /Tab Content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Reports Section -->

            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->
   
		</div>
		<!-- /Main Wrapper -->

<?php include 'footer.php'; ?>
