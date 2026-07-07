<?php
session_start();
require_once __DIR__ . '/php/config.php';

// Force patient log in
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit;
}

$appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;
if ($appointment_id <= 0) {
    die("Invalid appointment ID.");
}

try {
    $conn = getDBConnection();
    
    // Fetch appointment and doctor details
    $stmt = $conn->prepare("
        SELECT a.*, d.name as doctor_name, dp.specialty, dp.consultation_fee 
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id
        WHERE a.id = ? AND a.patient_id = ?
    ");
    $stmt->bind_param("ii", $appointment_id, $_SESSION['patient_id']);
    $stmt->execute();
    $appointment = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$appointment) {
        die("Appointment not found or unauthorized access.");
    }
    
    $fee = (float)($appointment['consultation_fee'] ?? 500); // Default to 500 if not set
    
    if (isset($_POST['transaction_id'])) {
        $trx_id = trim($_POST['transaction_id']);
        if (strlen($trx_id) < 8) {
            $error = "Please enter a valid bKash Transaction ID (minimum 8 characters).";
        } else {
            // Confirm the appointment status and update payment info
            $update = $conn->prepare("UPDATE appointments SET status = 'confirmed' WHERE id = ?");
            $update->bind_param("i", $appointment_id);
            if ($update->execute()) {
                $update->close();
                $conn->close();
                
                // Redirect based on whether it is emergency or regular
                if ($appointment['is_emergency']) {
                    header('Location: emergency-live-dashboard.php?appointment_id=' . $appointment_id);
                } else {
                    header('Location: patient-appointments.php');
                }
                exit;
            } else {
                $error = "Failed to update payment status. Please try again.";
            }
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    error_log("Payment error: " . $e->getMessage());
    die("An error occurred. Please try again.");
}

include 'header.php';
?>

<style>
    .payment-container {
        max-width: 500px;
        margin: 50px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .payment-header {
        background: #E2136E; /* bKash Color */
        color: #fff;
        padding: 30px;
        text-align: center;
    }
    .payment-header img {
        height: 50px;
        margin-bottom: 10px;
    }
    .payment-body {
        padding: 30px;
    }
    .amount-box {
        background: #fff0f6;
        border: 1px solid #ffd8e7;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        margin-bottom: 25px;
    }
    .amount-value {
        font-size: 28px;
        font-weight: 700;
        color: #E2136E;
    }
    .bkash-instructions {
        font-size: 14px;
        color: #555;
        line-height: 1.6;
        margin-bottom: 25px;
        padding-left: 20px;
    }
    .bkash-instructions li {
        margin-bottom: 8px;
    }
    .btn-bkash {
        background: #E2136E;
        color: #fff;
        border: none;
        padding: 12px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 6px;
        width: 100%;
        transition: all 0.3s;
    }
    .btn-bkash:hover {
        background: #b80c54;
        color: #fff;
    }
</style>

<div class="container">
    <div class="payment-container">
        <div class="payment-header">
            <h4 class="mb-0 text-white"><i class="fa-solid fa-credit-card me-2"></i>bKash Payment Checkout</h4>
        </div>
        <div class="payment-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="amount-box">
                <p class="text-muted mb-1">Consultation Fee</p>
                <div class="amount-value">৳ <?php echo number_format($fee, 2); ?> BDT</div>
                <p class="small text-muted mb-0">Doctor: <?php echo htmlspecialchars($appointment['doctor_name']); ?> (<?php echo htmlspecialchars($appointment['specialty'] ?? 'Specialist'); ?>)</p>
            </div>
            
            <h6 class="mb-3 font-weight-bold">Payment Steps:</h6>
            <ol class="bkash-instructions">
                <li>Go to your bKash Menu or App and choose <strong>Send Money</strong>.</li>
                <li>Enter our Personal number: <strong>01933-890894</strong></li>
                <li>Enter Amount: <strong>৳ <?php echo number_format($fee, 2); ?> BDT</strong></li>
                <li>Enter your Transaction ID (TrxID) below to confirm your booking:</li>
            </ol>
            
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label">bKash Transaction ID (TrxID) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" name="transaction_id" placeholder="e.g. A1B2C3D4" required>
                </div>
                
                <button type="submit" class="btn btn-bkash btn-lg">
                    <i class="fa-solid fa-circle-check me-2"></i> Confirm Payment
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
