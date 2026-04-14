<?php
session_start();
require_once 'php/config.php';

// Check if doctor is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'doctor') {
    header('Location: login.php');
    exit;
}

$doctor_id = $_SESSION['doctor_id'];
$appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($appointment_id <= 0) {
    die("Invalid Appointment ID");
}

try {
    $conn = getDBConnection();
    
    // Fetch appointment details ensuring it belongs to the logged-in doctor
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            d.name as doctor_name,
            dp.specialty as doctor_specialty,
            dp.profile_image as doctor_image
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id
        WHERE a.id = ? AND a.doctor_id = ?
    ");
    $stmt->bind_param("ii", $appointment_id, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
    
    if (!$appointment) {
        die("Appointment not found or access denied.");
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

include 'header.php';
?>

<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <h2 class="breadcrumb-title">Appointment Details</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="doctor-dashboard.php">Dashboard</a></li>
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
                                    <li class="mb-2"><strong>Appointment No:</strong> #<?php echo htmlspecialchars($appointment['appointment_number']); ?></li>
                                    <li class="mb-2"><strong>Patient Name:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?></li>
                                    <li class="mb-2"><strong>Mobile:</strong> <?php echo htmlspecialchars($appointment['mobile']); ?></li>
                                    <li class="mb-2"><strong>Date & Time:</strong> <?php echo date('d M Y', strtotime($appointment['appointment_date'])) . ' ' . $appointment['slot_time']; ?></li>
                                    <li class="mb-2"><strong>Status:</strong> 
                                        <span class="badge <?php 
                                            echo $appointment['status'] == 'completed' ? 'bg-success-light' : 
                                                ($appointment['status'] == 'cancelled' ? 'bg-danger-light' : 'bg-info-light'); 
                                        ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><strong>Age:</strong> <?php echo htmlspecialchars($appointment['age'] ?? 'N/A'); ?></li>
                                    <li class="mb-2"><strong>Weight:</strong> <?php echo htmlspecialchars($appointment['weight'] ?? 'N/A'); ?> kg</li>
                                    <li class="mb-2"><strong>Body Temp:</strong> <?php echo htmlspecialchars($appointment['body_temperature'] ?? 'N/A'); ?> °F</li>
                                    <li class="mb-2"><strong>Blood Pressure:</strong> <?php echo htmlspecialchars($appointment['blood_pressure'] ?? 'N/A'); ?></li>
                                    <li class="mb-2"><strong>Pulse:</strong> <?php echo htmlspecialchars($appointment['pulse'] ?? 'N/A'); ?> bpm</li>
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <li class="mb-2"><strong>SPO2:</strong> <?php echo htmlspecialchars($appointment['spo2'] ?? 'N/A'); ?>%</li>
                                <li class="mb-2"><strong>RBS/FBS:</strong> <?php echo htmlspecialchars($appointment['rbs_fbs'] ?? 'N/A'); ?></li>
                            </div>
                            <div class="col-md-12 mt-3">
                                <h5>Symptoms/Notes:</h5>
                                <p class="bg-light p-3 rounded"><?php echo nl2br(htmlspecialchars($appointment['notes'] ?? 'No notes provided.')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="appointments.php" class="btn btn-secondary">Back to List</a>
                    <?php if ($appointment['status'] !== 'completed' && $appointment['status'] !== 'cancelled'): ?>
                        <a href="video-call.php?appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-primary">Start Consultation</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
