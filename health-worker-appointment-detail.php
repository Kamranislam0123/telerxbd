<?php
require_once __DIR__ . '/php/config.php';

if (!isset($_SESSION['special_tid_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['user_type'] ?? '') !== 'special_tid') {
    header('Location: login.php');
    exit;
}

$special_tid_id = (int) $_SESSION['special_tid_id'];
$appointment_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($appointment_id <= 0) {
    header('Location: health-worker-dashboard.php');
    exit;
}

$appointment = null;
$healthcare = null;

try {
    $conn = getDBConnection();

    $hs = $conn->prepare("SELECT id, name, email, mobile AS phone, tid FROM special_tid_users WHERE id = ?");
    $hs->bind_param("i", $special_tid_id);
    $hs->execute();
    $healthcare = $hs->get_result()->fetch_assoc();
    $hs->close();

    if (!$healthcare) {
        $conn->close();
        header('Location: login.php');
        exit;
    }

    $tid = trim((string) ($healthcare['tid'] ?? ''));
    $has_referrer_tid = false;
    $has_created_by_special_tid = false;

    $ref_col = $conn->query("SHOW COLUMNS FROM appointments LIKE 'referrer_tid'");
    if ($ref_col && $ref_col->num_rows > 0) {
        $has_referrer_tid = true;
    }
    $created_col = $conn->query("SHOW COLUMNS FROM appointments LIKE 'created_by_special_tid_id'");
    if ($created_col && $created_col->num_rows > 0) {
        $has_created_by_special_tid = true;
    }

    if ($has_created_by_special_tid) {
        $stmt = $conn->prepare("
            SELECT a.*, d.name AS doctor_name, dp.specialty AS doctor_specialty, p.email AS patient_email, p.phone AS patient_phone
            FROM appointments a
            LEFT JOIN doctors d ON d.id = a.doctor_id
            LEFT JOIN doctor_profiles dp ON dp.doctor_id = d.id
            LEFT JOIN patients p ON p.id = a.patient_id
            WHERE a.id = ? AND a.created_by_special_tid_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $appointment_id, $special_tid_id);
    } else {
        $stmt = $conn->prepare("
            SELECT a.*, d.name AS doctor_name, dp.specialty AS doctor_specialty, p.email AS patient_email, p.phone AS patient_phone
            FROM appointments a
            LEFT JOIN doctors d ON d.id = a.doctor_id
            LEFT JOIN doctor_profiles dp ON dp.doctor_id = d.id
            LEFT JOIN patients p ON p.id = a.patient_id
            WHERE a.id = ? AND UPPER(TRIM(a.referrer_tid)) = UPPER(TRIM(?))
            LIMIT 1
        ");
        $stmt->bind_param("is", $appointment_id, $tid);
    }
    $stmt->execute();
    $appointment = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log('health-worker-appointment-detail: ' . $e->getMessage());
}

if (!$appointment) {
    header('Location: health-worker-dashboard.php');
    exit;
}

$current_page = 'health-worker-dashboard.php';
include 'header.php';
?>
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <h2 class="breadcrumb-title">Appointment Details</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="health-worker-dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Patient Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><strong>Appointment No:</strong> #<?php echo htmlspecialchars($appointment['appointment_number'] ?? ('APT' . str_pad($appointment['id'], 5, '0', STR_PAD_LEFT))); ?></li>
                                    <li class="mb-2"><strong>Patient Name:</strong> <?php echo htmlspecialchars($appointment['patient_name'] ?? '—'); ?></li>
                                    <li class="mb-2"><strong>Mobile:</strong> <?php echo htmlspecialchars($appointment['mobile'] ?? ($appointment['patient_phone'] ?? '—')); ?></li>
                                    <li class="mb-2"><strong>Patient Email:</strong> <?php echo htmlspecialchars($appointment['patient_email'] ?? '—'); ?></li>
                                    <li class="mb-2"><strong>Date & Time:</strong> <?php echo htmlspecialchars(($appointment['appointment_date'] ?? '—') . ' ' . ($appointment['slot_time'] ?? '')); ?></li>
                                    <li class="mb-2"><strong>Status:</strong> <?php echo htmlspecialchars($appointment['status'] ?? 'confirmed'); ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><strong>Doctor:</strong> <?php echo htmlspecialchars($appointment['doctor_name'] ?? '—'); ?></li>
                                    <li class="mb-2"><strong>Specialty:</strong> <?php echo htmlspecialchars($appointment['doctor_specialty'] ?? '—'); ?></li>
                                    <li class="mb-2"><strong>Age:</strong> <?php echo htmlspecialchars($appointment['age'] ?? 'N/A'); ?></li>
                                    <li class="mb-2"><strong>Weight:</strong> <?php echo htmlspecialchars($appointment['weight'] ?? 'N/A'); ?></li>
                                    <li class="mb-2"><strong>Body Temp:</strong> <?php echo htmlspecialchars($appointment['body_temperature'] ?? 'N/A'); ?></li>
                                    <li class="mb-2"><strong>Blood Pressure:</strong> <?php echo htmlspecialchars($appointment['blood_pressure'] ?? 'N/A'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <h5>Symptoms / Notes</h5>
                        <p class="bg-light p-3 rounded mb-0"><?php echo nl2br(htmlspecialchars($appointment['notes'] ?? 'No notes provided.')); ?></p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="health-worker-dashboard.php" class="btn btn-secondary">Back to List</a>
                    <a href="video-call.php?appointment_id=<?php echo (int) $appointment['id']; ?>" class="btn btn-primary">Join Video Call</a>
                    <?php if (!empty($appointment['prescription_path'])): ?>
                        <a href="<?php echo htmlspecialchars($appointment['prescription_path']); ?>" target="_blank" class="btn btn-success">View Prescription</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
