<?php
/**
 * Health Worker Dashboard - TeleRx Bangladesh
 * Shows patients who entered this health worker's TeleRx ID (TID) when booking.
 */

$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.php');
    exit;
}
require_once $config_path;

if (!isset($_SESSION['healthcare_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$healthcare_id = (int) $_SESSION['healthcare_id'];
$healthcare = null;
$referred_patients = [];

try {
    $conn = getDBConnection();

    // Get basic info from healthcare_providers; profile_image may be in healthcare_providers_profiles
    $stmt = $conn->prepare("SELECT id, name, email, phone, tid FROM healthcare_providers WHERE id = ?");
    $stmt->bind_param("i", $healthcare_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        $stmt->close();
        $conn->close();
        header('Location: login.php');
        exit;
    }
    $healthcare = $res->fetch_assoc();
    $stmt->close();

    // Get profile_image from healthcare_providers_profiles if available (same as profile-settings page)
    $profile_img = 'assets/img/doctors-dashboard/doctor-profile-img.jpg';
    try {
        $pstmt = $conn->prepare("SELECT profile_image FROM healthcare_providers_profiles WHERE healthcare_provider_id = ? LIMIT 1");
        if ($pstmt) {
            $pstmt->bind_param("i", $healthcare_id);
            $pstmt->execute();
            $pres = $pstmt->get_result();
            if ($pres && $pres->num_rows > 0 && ($row = $pres->fetch_assoc()) && !empty(trim($row['profile_image'] ?? ''))) {
                $profile_img = $row['profile_image'];
            }
            $pstmt->close();
        }
    } catch (Exception $e) {
        // Table may not exist; use default image
    }
    $healthcare['profile_image'] = $profile_img;
    $tid = $healthcare['tid'] ?? '';

    // Check if appointments has referrer_tid column and fetch referred appointments (match TID case-insensitively)
    $col_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'referrer_tid'");
    if ($tid !== '' && $col_check && $col_check->num_rows > 0) {
        $apt_stmt = $conn->prepare("
            SELECT a.id, a.appointment_number, a.patient_name, a.mobile, a.appointment_date, a.slot_time,
                   a.age, a.notes, a.status, a.created_at,
                   d.name AS doctor_name, dp.specialty AS doctor_specialty
            FROM appointments a
            LEFT JOIN doctors d ON d.id = a.doctor_id
            LEFT JOIN doctor_profiles dp ON dp.doctor_id = d.id
            WHERE UPPER(TRIM(a.referrer_tid)) = UPPER(TRIM(?))
            ORDER BY a.appointment_date DESC, a.slot_time DESC
        ");
        $apt_stmt->bind_param("s", $tid);
        $apt_stmt->execute();
        $referred_patients = $apt_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $apt_stmt->close();
    }

    $conn->close();
} catch (Exception $e) {
    error_log('health-worker-dashboard: ' . $e->getMessage());
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Error</title></head><body style="font-family:sans-serif;padding:2rem;text-align:center;">';
    echo '<h1>Service temporarily unavailable</h1><p>Please try again later or contact support.</p>';
    echo '<p><a href="health-worker-dashboard.php">Try again</a> &middot; <a href="php/logout.php">Logout</a></p></body></html>';
    exit;
}

if (!$healthcare) {
    header('Location: login.php');
    exit;
}

$current_page = 'health-worker-dashboard.php';
include 'header.php';
?>
    <!-- Page Content (same structure as doctor-dashboard) -->
    <div class="content">
        <div class="container">
            <div class="row">
                <?php include 'health-worker-leftside-menu.php'; ?>
                </div>
                <div class="col-lg-8 col-xl-9">
                <div class="dashboard-header mt-4">
                    <h3>Patients Referred by You</h3>
                    <p class="text-muted mb-0">Patients who entered your TeleRx ID (TID: <?php echo htmlspecialchars($healthcare['tid'] ?? '—'); ?>) when booking appear here.</p>
                </div>
                <div class="dashboard-card w-100">
                    <div class="dashboard-card-head">
                        <div class="header-title">
                            <h5>Patient Details</h5>
                        </div>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="table-responsive">
                            <table class="table dashboard-table appoint-table">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Contact</th>
                                        <th>Date & Time</th>
                                        <th>Doctor</th>
                                        <th>Booking #</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($referred_patients)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No patients have used your TeleRx ID yet. When a patient enters your TID (<?php echo htmlspecialchars($healthcare['tid'] ?? ''); ?>) at booking, their details will appear here.</td>
                                    </tr>
                                    <?php else: foreach ($referred_patients as $apt): ?>
                                    <tr>
                                        <td>
                                            <div class="patient-info-profile">
                                                <div class="patient-name-info">
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($apt['patient_name'] ?? '—'); ?></h6>
                                                    <?php if (!empty($apt['age'])): ?>
                                                    <span class="text-muted small">Age: <?php echo htmlspecialchars($apt['age']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span><?php echo htmlspecialchars($apt['mobile'] ?? '—'); ?></span>
                                        </td>
                                        <td>
                                            <div class="appointment-date-created">
                                                <h6 class="mb-0"><?php echo $apt['appointment_date'] ? date('j M Y', strtotime($apt['appointment_date'])) : '—'; ?></h6>
                                                <span class="text-muted small"><?php echo $apt['slot_time'] ? date('g:i A', strtotime($apt['slot_time'])) : '—'; ?></span>
                                                <?php if (!empty($apt['status'])): ?>
                                                <span class="badge table-badge mt-1"><?php echo htmlspecialchars($apt['status']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span><?php echo htmlspecialchars($apt['doctor_name'] ?? '—'); ?></span>
                                            <?php if (!empty($apt['doctor_specialty'])): ?>
                                            <br><span class="text-muted small"><?php echo htmlspecialchars($apt['doctor_specialty']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="fw-medium">#<?php echo htmlspecialchars($apt['appointment_number'] ?? ('APT' . str_pad($apt['id'], 5, '0', STR_PAD_LEFT))); ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
