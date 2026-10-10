<?php
/**
 * Patient Medical History - TeleRx Bangladesh
 * Lists the patient's medical history forms, lets them start / continue a form
 * and open or download the PDF of a finished form.
 * Access restricted exclusively to Premium Subscribers
 */

session_start();
require_once __DIR__ . '/php/mh-helpers.php';   // loads config.php + subscription-helper.php too

// Force patient login
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit;
}

$patient_id = (int)$_SESSION['patient_id'];
$patient = null;
$forms = [];
$is_premium_subscriber = false;
$patient_sub = null;
$setup_needed = false;
$base = (defined('APP_BASE') && APP_BASE) ? APP_BASE : '';

mh_db_strict(true);
try {
    $conn = getDBConnection();

    // Check patient active subscription status
    $patient_sub = getActiveSubscription($patient_id, $conn);

    // Check if the patient is on a PREMIUM subscription plan
    if ($patient_sub) {
        $plan_code = strtolower($patient_sub['plan_code'] ?? '');
        $plan_name = strtolower($patient_sub['plan_name'] ?? '');
        if (strpos($plan_code, 'premium') !== false || strpos($plan_name, 'premium') !== false) {
            $is_premium_subscriber = true;
        }
    }

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

    // Set default values if profile data is missing
    $patient['profile_image'] = !empty($patient['profile_image']) ? $patient['profile_image'] : 'assets/img/doctors-dashboard/profile-06.jpg';

    // Medical history forms of this patient
    if ($is_premium_subscriber) {
        if (mh_tables_ready($conn)) {
            $forms = mh_list_forms($conn, $patient_id);
        } else {
            $setup_needed = true;
        }
    }
    $conn->close();

} catch (Throwable $e) {
    error_log("Patient medical history error: " . $e->getMessage());
    if (!$patient) {
        $patient = [
            'id' => $patient_id,
            'name' => $_SESSION['patient_name'] ?? 'Patient',
            'profile_image' => 'assets/img/doctors-dashboard/profile-06.jpg'
        ];
    }
}
mh_db_strict(false);   // back to normal for header.php / footer.php

$total_tabs = count(mh_sections());

include 'header.php';
?>

<style>
.med-record-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
    background: #fff;
    transition: all 0.25s ease;
}
.med-record-card + .med-record-card { margin-top: 14px; }
.med-record-card:hover {
    border-color: #0e82fd;
    box-shadow: 0 6px 20px rgba(14, 130, 253, 0.08);
}

.download-btn-primary {
    background-color: #0e82fd;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: background 0.2s;
}
.download-btn-primary:hover {
    background-color: #0266d6;
    color: #fff;
}

.preview-btn-outline {
    background-color: #f8fafc;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s;
}
.preview-btn-outline:hover {
    background-color: #e2e8f0;
    color: #1e293b;
}

.mh-status-pill {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
}
.mh-status-pill.is-complete { background: #e6f6ec; color: #12a150; }
.mh-status-pill.is-draft { background: #fef3e2; color: #b45309; }

.mh-empty { text-align: center; padding: 40px 16px; }
.mh-empty-icon {
    width: 64px; height: 64px; margin: 0 auto 14px; border-radius: 50%;
    background: #e8f2ff; color: #0e82fd; display: flex; align-items: center; justify-content: center; font-size: 26px;
}

/* Premium Lock Blurry Styles */
.blurry-content-locked {
    filter: blur(3px);
    -webkit-filter: blur(3px);
    user-select: none;
    pointer-events: none;
    opacity: 0.75;
}

.subscription-locked-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.25);
    z-index: 10;
    padding: 20px;
}

.locked-text-overlay {
    font-size: 18px;
    font-weight: 700;
    color: #0e82fd;
    text-align: center;
    padding: 14px 28px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 30px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    border: 1px solid #cbd5e1;
    text-decoration: none;
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.locked-text-overlay:hover {
    background: #0e82fd;
    color: #ffffff;
    border-color: #0e82fd;
    box-shadow: 0 10px 30px rgba(14, 130, 253, 0.3);
    transform: translateY(-2px);
}
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="patient-dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Medical History</li>
                    </ol>
                    <h2 class="breadcrumb-title">Medical History</h2>
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
    <div class="container">
        <div class="row">
            <!-- Left Sidebar Navigation -->
            <?php include 'patient-leftside-menu.php'; ?>

            <!-- Main Content Section -->
            <div class="col-lg-8 col-xl-9">
                <div class="card border-0 shadow-sm rounded-3 position-relative overflow-hidden">

                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="isax isax-document-text text-primary me-2"></i>Medical History
                        </h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <?php if ($is_premium_subscriber): ?>
                                <span class="badge bg-success-light text-success px-3 py-2 rounded-pill fw-medium">
                                    <i class="fa-solid fa-crown me-1"></i> Premium Unlocked
                                </span>
                                <?php if (!$setup_needed): ?>
                                    <a href="<?php echo htmlspecialchars($base . '/medical-history-form?new=1'); ?>" class="download-btn-primary">
                                        <i class="fa-solid fa-plus"></i> Fill New Form
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card Body Wrapper (Blurry if NOT a Premium Subscriber) -->
                    <div class="card-body p-4 <?php echo !$is_premium_subscriber ? 'blurry-content-locked' : ''; ?>">

                        <?php if ($setup_needed): ?>
                            <div class="alert alert-warning mb-0">
                                <strong>Setup needed.</strong> The database tables for the medical history form are not created yet.
                                Import <code>database/mh_tables.sql</code> in phpMyAdmin, then reload this page.
                            </div>

                        <?php elseif (empty($forms)): ?>
                            <div class="mh-empty">
                                <div class="mh-empty-icon"><i class="fa-regular fa-clipboard"></i></div>
                                <h5 class="fw-semibold text-dark mb-1">No medical history yet</h5>
                                <p class="text-muted mb-3">Fill the medical history form once. It takes a few minutes and you can download it as a PDF.</p>
                                <?php if ($is_premium_subscriber): ?>
                                    <a href="<?php echo htmlspecialchars($base . '/medical-history-form?new=1'); ?>" class="download-btn-primary">
                                        <i class="fa-solid fa-plus"></i> Fill New Form
                                    </a>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <?php foreach ($forms as $f):
                                $is_done = ($f['status'] === 'completed');
                                $form_no = 'MH-' . str_pad((string)(int)$f['id'], 6, '0', STR_PAD_LEFT);
                                $shown_name = trim((string)$f['patient_name']) !== '' ? $f['patient_name'] : 'Unnamed patient';
                                $updated = !empty($f['updated_at']) ? date('d M Y, h:i A', strtotime($f['updated_at'])) : '';
                            ?>
                                <div class="med-record-card">
                                    <div class="row align-items-center">
                                        <div class="col-md-7 mb-3 mb-md-0">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3 p-3 rounded-3 <?php echo $is_done ? 'text-danger' : 'text-warning'; ?>" style="background-color: <?php echo $is_done ? '#fee2e2' : '#fef3e2'; ?>; border-radius: 10px;">
                                                    <i class="fa-solid <?php echo $is_done ? 'fa-file-pdf' : 'fa-file-pen'; ?> fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-1 fw-semibold text-dark"><?php echo htmlspecialchars($shown_name); ?></h5>
                                                    <div class="d-flex flex-wrap align-items-center gap-3 text-secondary text-sm">
                                                        <span><?php echo htmlspecialchars($form_no); ?></span>
                                                        <?php if ($is_done): ?>
                                                            <span class="mh-status-pill is-complete">Completed</span>
                                                        <?php else: ?>
                                                            <span class="mh-status-pill is-draft">Draft &middot; <?php echo (int)$f['saved_count']; ?> of <?php echo (int)$total_tabs; ?> tabs saved</span>
                                                        <?php endif; ?>
                                                        <?php if ($updated): ?>
                                                            <span><i class="fa-regular fa-clock me-1 text-success"></i> <?php echo htmlspecialchars($updated); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5 text-md-end">
                                            <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                                <a href="<?php echo htmlspecialchars($base . '/medical-history-form?id=' . (int)$f['id']); ?>" class="preview-btn-outline">
                                                    <i class="fa-regular fa-pen-to-square"></i> <?php echo $is_done ? 'Edit' : 'Continue'; ?>
                                                </a>
                                                <?php if ($is_done): ?>
                                                    <a href="<?php echo htmlspecialchars($base . '/php/mh-generate-pdf.php?form_id=' . (int)$f['id']); ?>" target="_blank" rel="noopener" class="preview-btn-outline">
                                                        <i class="fa-regular fa-eye"></i> Preview
                                                    </a>
                                                    <a href="<?php echo htmlspecialchars($base . '/php/mh-generate-pdf.php?form_id=' . (int)$f['id'] . '&download=1'); ?>" class="download-btn-primary">
                                                        <i class="fa-solid fa-download"></i> Download PDF
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Overlay Text for Non-Premium Subscribers -->
                    <?php if (!$is_premium_subscriber): ?>
                        <div class="subscription-locked-overlay">
                            <a href="subscription" class="locked-text-overlay">
                                Please Purchase a Premium Subscription <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
