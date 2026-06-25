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

if (!isset($_SESSION['special_tid_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || (($_SESSION['user_type'] ?? '') !== 'special_tid')) {
    header('Location: login.php');
    exit;
}

$special_tid_id = (int) $_SESSION['special_tid_id'];
$healthcare = null;
$referred_patients = [];

try {
    $conn = getDBConnection();

    // Get Special TID account info; optional linked healthcare profile for image
    $stmt = $conn->prepare("
        SELECT s.id, s.name, s.email, s.mobile AS phone, s.tid, s.healthcare_provider_id
        FROM special_tid_users s
        WHERE s.id = ?
    ");
    $stmt->bind_param("i", $special_tid_id);
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

    // Get profile_image from linked healthcare profile if available
    $profile_img = 'assets/img/doctors-dashboard/doctor-profile-img.jpg';
    try {
        if (!empty($healthcare['healthcare_provider_id'])) {
            $linked_id = (int) $healthcare['healthcare_provider_id'];
            $pstmt = $conn->prepare("SELECT profile_image FROM healthcare_providers_profiles WHERE healthcare_provider_id = ? LIMIT 1");
            if ($pstmt) {
                $pstmt->bind_param("i", $linked_id);
                $pstmt->execute();
                $pres = $pstmt->get_result();
                if ($pres && $pres->num_rows > 0 && ($row = $pres->fetch_assoc()) && !empty(trim($row['profile_image'] ?? ''))) {
                    $profile_img = $row['profile_image'];
                }
                $pstmt->close();
            }
        }
    } catch (Exception $e) {
        // Table may not exist; use default image
    }
    $healthcare['profile_image'] = $profile_img;
    $tid = $healthcare['tid'] ?? '';

    // Fetch appointments created by this health worker account.
    // Fallback to referrer_tid for old records before created_by_special_tid_id existed.
    $has_referrer_tid = false;
    $has_created_by_special_tid = false;
    $col_check_ref = $conn->query("SHOW COLUMNS FROM appointments LIKE 'referrer_tid'");
    if ($col_check_ref && $col_check_ref->num_rows > 0) {
        $has_referrer_tid = true;
    }
    $col_check_created = $conn->query("SHOW COLUMNS FROM appointments LIKE 'created_by_special_tid_id'");
    if ($col_check_created && $col_check_created->num_rows > 0) {
        $has_created_by_special_tid = true;
    }

    if ($has_created_by_special_tid) {
        $apt_stmt = $conn->prepare("
            SELECT a.id, a.patient_id, a.appointment_number, a.patient_name, a.mobile, a.appointment_date, a.slot_time,
                   a.age, a.notes, a.status, a.created_at, a.prescription_path,
                   d.name AS doctor_name, dp.specialty AS doctor_specialty
            FROM appointments a
            LEFT JOIN doctors d ON d.id = a.doctor_id
            LEFT JOIN doctor_profiles dp ON dp.doctor_id = d.id
            WHERE a.created_by_special_tid_id = ?
            ORDER BY a.appointment_date DESC, a.slot_time DESC
        ");
        $apt_stmt->bind_param("i", $special_tid_id);
        $apt_stmt->execute();
        $referred_patients = $apt_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $apt_stmt->close();
    } elseif ($tid !== '' && $has_referrer_tid) {
        $apt_stmt = $conn->prepare("
            SELECT a.id, a.patient_id, a.appointment_number, a.patient_name, a.mobile, a.appointment_date, a.slot_time,
                   a.age, a.notes, a.status, a.created_at, a.prescription_path,
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
                    <h3>Special TID Appointments</h3>
                    <p class="text-muted mb-0">Appointments created from your Special TID account (TID: <?php echo htmlspecialchars($healthcare['tid'] ?? '—'); ?>).</p>
                </div>
                <div class="mb-3">
                    <a href="doctors.php" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i> Book New Appointment
                    </a>
                </div>
                <div class="dashboard-card w-100">
                    <div class="dashboard-card-head">
                        <div class="header-title">
                            <h5>Appointment List</h5>
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
                                        <th>Status</th>
                                        <th>Doctor</th>
                                        <th>Booking #</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($referred_patients)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No appointments created yet from your Special TID account.</td>
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
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge table-badge mt-1"><?php echo htmlspecialchars($apt['status'] ?? 'confirmed'); ?></span>
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
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <a class="btn btn-sm btn-outline-primary" href="video-call.php?appointment_id=<?php echo (int) $apt['id']; ?>" title="Video Call">
                                                    <i class="fa-solid fa-video"></i>
                                                </a>
                                                <a class="btn btn-sm btn-outline-secondary" href="health-worker-appointment-detail.php?id=<?php echo (int) $apt['id']; ?>" title="Appointment Details">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <?php if (!empty($apt['prescription_path'])): ?>
                                                <a class="btn btn-sm btn-outline-success" href="<?php echo htmlspecialchars($apt['prescription_path']); ?>" target="_blank" title="Prescription">
                                                    <i class="fa-solid fa-file-prescription"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
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