<?php
/**
 * Patient Medical History - TeleRx Bangladesh
 * View, preview and download TeleRx Comprehensive Medical History document
 * Access restricted exclusively to Premium Subscribers
 */

session_start();
require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/subscription-helper.php';

// Force patient login
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit;
}

$patient_id = (int)$_SESSION['patient_id'];
$patient = null;
$medical_records = [];
$is_premium_subscriber = false;
$patient_sub = null;

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

    // Auto-create medical history table if missing
    $table_sql = "CREATE TABLE IF NOT EXISTS `patient_medical_history` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `patient_id` INT NOT NULL,
        `member_name` VARCHAR(255) DEFAULT 'Self',
        `relationship` VARCHAR(100) DEFAULT 'Self',
        `title` VARCHAR(255) NOT NULL,
        `file_path` VARCHAR(255) NOT NULL,
        `file_size` VARCHAR(50) DEFAULT '150 KB',
        `description` TEXT DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX (`patient_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($table_sql);

    // Clean up old demo records that aren't TeleRx Comprehensive Medical History
    $clean_stmt = $conn->prepare("DELETE FROM patient_medical_history WHERE patient_id = ? AND title != 'TeleRx Comprehensive Medical History'");
    if ($clean_stmt) {
        $clean_stmt->bind_param("i", $patient_id);
        $clean_stmt->execute();
        $clean_stmt->close();
    }

    // Check existing records count
    $check_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM patient_medical_history WHERE patient_id = ?");
    $check_stmt->bind_param("i", $patient_id);
    $check_stmt->execute();
    $count = (int)$check_stmt->get_result()->fetch_assoc()['cnt'];
    $check_stmt->close();

    // Seed default demo record if none exist
    if ($count === 0) {
        $seed_stmt = $conn->prepare("
            INSERT INTO patient_medical_history (patient_id, member_name, relationship, title, file_path, file_size, description, created_at)
            VALUES 
            (?, 'Self', 'Self', 'TeleRx Comprehensive Medical History', 'uploads/medical_history/TeleRx_Comprehensive_Medical_History.pdf', '145 KB', 'Comprehensive patient health summary and medical evaluation document.', '2026-08-15 10:30:00')
        ");
        $seed_stmt->bind_param("i", $patient_id);
        $seed_stmt->execute();
        $seed_stmt->close();
    }

    // Fetch medical records
    $rec_stmt = $conn->prepare("SELECT * FROM patient_medical_history WHERE patient_id = ? ORDER BY created_at DESC, id DESC");
    $rec_stmt->bind_param("i", $patient_id);
    $rec_stmt->execute();
    $res = $rec_stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $medical_records[] = $row;
    }
    $rec_stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Patient medical history error: " . $e->getMessage());
    if (!$patient) {
        $patient = [
            'id' => $patient_id,
            'name' => $_SESSION['patient_name'] ?? 'Patient',
            'profile_image' => 'assets/img/doctors-dashboard/profile-06.jpg'
        ];
    }
    if (empty($medical_records)) {
        $medical_records = [
            [
                'id' => 1,
                'patient_id' => $patient_id,
                'member_name' => 'Self',
                'relationship' => 'Self',
                'title' => 'TeleRx Comprehensive Medical History',
                'file_path' => 'uploads/medical_history/TeleRx_Comprehensive_Medical_History.pdf',
                'file_size' => '145 KB',
                'description' => 'Comprehensive patient health summary and medical evaluation document.',
                'created_at' => '2026-08-15 10:30:00'
            ]
        ];
    }
}

include 'header.php';
?>

<style>
.med-record-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    background: #fff;
    transition: all 0.25s ease;
}
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
                    
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="isax isax-document-text text-primary me-2"></i>Medical History
                        </h5>
                        <?php if ($is_premium_subscriber): ?>
                            <span class="badge bg-success-light text-success px-3 py-2 rounded-pill fw-medium">
                                <i class="fa-solid fa-crown me-1"></i> Premium Unlocked
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Card Body Wrapper (Blurry if NOT a Premium Subscriber) -->
                    <div class="card-body p-4 <?php echo !$is_premium_subscriber ? 'blurry-content-locked' : ''; ?>">
                        <?php 
                        // Show single demo card for TeleRx Comprehensive Medical History
                        $file_url = 'uploads/medical_history/TeleRx_Comprehensive_Medical_History.pdf';
                        $download_url = 'php/download-medical-file.php?file=TeleRx_Comprehensive_Medical_History.pdf';
                        $uploaded_at = !empty($medical_records[0]['created_at']) ? date('d M Y, h:i A', strtotime($medical_records[0]['created_at'])) : '15 Aug 2026, 10:30 AM';
                        $title = !empty($medical_records[0]['title']) ? $medical_records[0]['title'] : 'TeleRx Comprehensive Medical History';
                        $description = !empty($medical_records[0]['description']) ? $medical_records[0]['description'] : 'Comprehensive patient health summary and medical evaluation document.';
                        $file_size = !empty($medical_records[0]['file_size']) ? $medical_records[0]['file_size'] : '145 KB';
                        ?>

                        <div class="med-record-card">
                            <div class="row align-items-center">
                                <div class="col-md-7 mb-3 mb-md-0">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 p-3 rounded-3 text-danger" style="background-color: #fee2e2; border-radius: 10px;">
                                            <i class="fa-solid fa-file-pdf fa-2x"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-semibold text-dark"><?php echo htmlspecialchars($title); ?></h5>
                                            <p class="text-muted text-sm mb-2"><?php echo htmlspecialchars($description); ?></p>
                                            
                                            <div class="d-flex flex-wrap align-items-center gap-3 text-secondary text-sm">
                                                <span>
                                                    <i class="fa-solid fa-user text-primary me-1"></i> 
                                                    Patient: <strong><?php echo htmlspecialchars($patient['name']); ?> (Self)</strong>
                                                </span>
                                                <span><i class="fa-regular fa-clock me-1 text-success"></i> Uploaded: <?php echo htmlspecialchars($uploaded_at); ?></span>
                                                <span class="badge bg-light text-secondary border"><i class="fa-regular fa-file me-1"></i> <?php echo htmlspecialchars($file_size); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5 text-md-end">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2">
                                        <a href="<?php echo htmlspecialchars($file_url); ?>" target="_blank" class="preview-btn-outline">
                                            <i class="fa-regular fa-eye"></i> Preview
                                        </a>
                                        <a href="<?php echo htmlspecialchars($download_url); ?>" class="download-btn-primary">
                                            <i class="fa-solid fa-download"></i> Download File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
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
