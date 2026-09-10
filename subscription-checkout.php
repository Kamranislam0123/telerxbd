<?php
session_start();
require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/subscription-helper.php';

// Force patient log in
$plan_code = isset($_GET['plan']) ? trim($_GET['plan']) : 'monthly_standard';
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php?redirect=' . urlencode('subscription-checkout.php?plan=' . $plan_code));
    exit;
}

$patient_id = (int)$_SESSION['patient_id'];
$error = '';
$success = '';

$plan = getPlanByCode($plan_code);
if (!$plan) {
    // Fallback to monthly_standard
    $plan = getPlanByCode('monthly_standard');
}

if (!$plan) {
    die("Plan details not found. Please contact support.");
}

$active_sub = getActiveSubscription($patient_id);

// Handle Checkout Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_subscription'])) {
    $trx_id = trim($_POST['transaction_id'] ?? '');
    $selected_plan_id = (int)($_POST['plan_id'] ?? $plan['id']);

    if (strlen($trx_id) < 8) {
        $error = "Please enter a valid bKash Transaction ID (minimum 8 characters).";
    } else {
        try {
            $conn = getDBConnection();

            // Re-verify plan
            $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE id = ? AND is_active = 1");
            $stmt->bind_param("i", $selected_plan_id);
            $stmt->execute();
            $target_plan = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$target_plan) {
                throw new Exception("Invalid package selected.");
            }

            $months = (int)$target_plan['duration_months'];
            $price = (float)$target_plan['price'];
            $call_quota = (int)$target_plan['emergency_call_quota'];

            $start_date = date('Y-m-d H:i:s');
            $end_date = date('Y-m-d H:i:s', strtotime("+{$months} months"));

            // If user already has an active subscription, mark old active ones as replaced/renewed
            $expire_old = $conn->prepare("UPDATE patient_subscriptions SET status = 'expired' WHERE patient_id = ? AND status = 'active'");
            $expire_old->bind_param("i", $patient_id);
            $expire_old->execute();
            $expire_old->close();

            // Insert new active subscription
            $ins = $conn->prepare("
                INSERT INTO patient_subscriptions 
                (patient_id, plan_id, amount_paid, payment_method, transaction_id, status, start_date, end_date, emergency_calls_total, emergency_calls_used) 
                VALUES (?, ?, ?, 'bkash', ?, 'active', ?, ?, ?, 0)
            ");
            $ins->bind_param("iidsssi", $patient_id, $selected_plan_id, $price, $trx_id, $start_date, $end_date, $call_quota);
            
            if ($ins->execute()) {
                $new_sub_id = $ins->insert_id;
                $ins->close();

                // Process initial family members if entered
                if (!empty($_POST['family_name']) && is_array($_POST['family_name'])) {
                    $family_limit = (int)$target_plan['family_member_quota'];
                    $added_count = 0;

                    $fam_ins = $conn->prepare("
                        INSERT INTO subscription_family_members 
                        (subscription_id, patient_id, member_name, relationship, phone, age, gender) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");

                    foreach ($_POST['family_name'] as $idx => $name) {
                        $name = trim($name);
                        if (!empty($name) && $added_count < $family_limit) {
                            $rel = trim($_POST['family_relation'][$idx] ?? 'Family');
                            $phone = trim($_POST['family_phone'][$idx] ?? '');
                            $age = trim($_POST['family_age'][$idx] ?? '');
                            $gender = trim($_POST['family_gender'][$idx] ?? 'Other');

                            $fam_ins->bind_param("iisssss", $new_sub_id, $patient_id, $name, $rel, $phone, $age, $gender);
                            $fam_ins->execute();
                            $added_count++;
                        }
                    }
                    $fam_ins->close();
                }

                $conn->close();
                header("Location: patient-subscription.php?activated=1");
                exit;
            } else {
                throw new Exception("Could not save subscription: " . $conn->error);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$page_title = "TeleRx Subscription Checkout";
include 'header.php';
?>

<style>
.trx-checkout-wrap {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 15px;
}
.trx-checkout-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    overflow: hidden;
    border: 1px solid #edf2f7;
}
.trx-checkout-header {
    background: linear-gradient(135deg, #0e82fd 0%, #0352bd 100%);
    color: #fff;
    padding: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}
.trx-checkout-header h3 {
    color: #fff;
    margin: 0;
    font-size: 24px;
    font-weight: 700;
}
.trx-badge-plan {
    background: rgba(255,255,255,0.2);
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 600;
}
.trx-plan-highlight {
    background: #f8fafc;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 25px;
    border: 1px solid #e2e8f0;
}
.trx-plan-price-box {
    text-align: right;
}
.trx-plan-price-box .price {
    font-size: 32px;
    font-weight: 800;
    color: #0e82fd;
}
.bkash-pay-box {
    background: #fff0f6;
    border: 1px solid #ffd8e7;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 25px;
}
.bkash-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
}
.bkash-head img {
    height: 36px;
}
.bkash-num-tag {
    font-size: 20px;
    font-weight: 700;
    color: #E2136E;
    letter-spacing: 0.5px;
}
.btn-trx-confirm {
    background: #E2136E;
    color: #fff;
    border: none;
    padding: 14px 28px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 8px;
    width: 100%;
    transition: all 0.2s ease;
}
.btn-trx-confirm:hover {
    background: #b80c54;
    color: #fff;
    box-shadow: 0 4px 15px rgba(226,19,110,0.3);
}
.family-member-row {
    background: #f8fafc;
    border: 1px solid #edf2f7;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 10px;
}
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <h2 class="breadcrumb-title">Subscription Checkout</h2>
                </nav>
            </div>
        </div>
    </div>
    <div class="breadcrumb-bg">
        <img src="assets/img/bg/breadcrumb-bg-01.png" alt="img" class="breadcrumb-bg-01">
        <img src="assets/img/bg/breadcrumb-bg-02.png" alt="img" class="breadcrumb-bg-02">
    </div>
</div>
<!-- /Breadcrumb -->

<div class="container">
    <div class="trx-checkout-wrap">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-4 alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($active_sub): ?>
            <div class="alert alert-info mb-4">
                <i class="fa-solid fa-circle-info me-2"></i> You currently have an active <strong><?php echo htmlspecialchars($active_sub['plan_name']); ?></strong> (Valid until <?php echo date('d M Y', strtotime($active_sub['end_date'])); ?>). Subscribing to this package will renew/upgrade your plan.
            </div>
        <?php endif; ?>

        <div class="trx-checkout-card">
            <div class="trx-checkout-header">
                <div>
                    <h3>Complete Your Subscription</h3>
                    <p class="mb-0 text-white-50">Activate unlimited benefits, 24/7 doctor calls, and consultation discounts.</p>
                </div>
                <div class="trx-badge-plan">
                    <i class="fa-solid fa-award me-1"></i> <?php echo htmlspecialchars($plan['duration_label']); ?>
                </div>
            </div>

            <div class="p-4 p-md-5">
                <form method="POST" id="subscription-checkout-form">
                    <input type="hidden" name="plan_id" value="<?php echo (int)$plan['id']; ?>">

                    <!-- Plan Overview Box -->
                    <div class="trx-plan-highlight">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($plan['badge'] ?? 'TeleRx Care'); ?></span>
                                <h4 class="mb-1 fw-bold"><?php echo htmlspecialchars($plan['name']); ?> Plan</h4>
                                <p class="text-muted mb-2">Duration: <strong><?php echo htmlspecialchars($plan['duration_label']); ?></strong></p>
                                
                                <div class="row g-2 mt-2">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center text-secondary small">
                                            <i class="fa-solid fa-circle-check text-success me-2"></i>
                                            <span><strong><?php echo (int)$plan['emergency_call_quota']; ?></strong> Free 24/7 Emergency Calls</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center text-secondary small">
                                            <i class="fa-solid fa-circle-check text-success me-2"></i>
                                            <span>Doctor Consultation Discount: <strong><?php echo (int)$plan['gp_discount_percent']; ?>% OFF</strong> (All Doctors)</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center text-secondary small">
                                            <i class="fa-solid fa-circle-check text-success me-2"></i>
                                            <span>Family Coverage: <strong><?php echo (int)$plan['family_member_quota']; ?> <?php echo ((int)$plan['family_member_quota'] > 1) ? 'Persons' : 'Person'; ?></strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 trx-plan-price-box mt-3 mt-md-0">
                                <div class="text-muted small">Subscription Fee</div>
                                <div class="price">৳<?php echo number_format($plan['price'], 0); ?></div>
                                <div class="text-muted small">Valid for <?php echo htmlspecialchars($plan['duration_label']); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Member Registration (Optional initial setup) -->
                    <?php if ((int)$plan['family_member_quota'] > 0): ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold mb-0">
                                <i class="fa-solid fa-people-roof text-primary me-2"></i>Add Family Members (Optional)
                            </h5>
                            <span class="text-muted small">Max <?php echo (int)$plan['family_member_quota']; ?> members</span>
                        </div>
                        <p class="text-muted small mb-3">You can add family members now or manage them later from your subscription dashboard.</p>
                        
                        <div id="family-members-container">
                            <?php for ($i = 0; $i < min(1, (int)$plan['family_member_quota']); $i++): ?>
                            <div class="family-member-row">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-sm" name="family_name[]" placeholder="Full Name">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select form-select-sm" name="family_relation[]">
                                            <option value="Spouse">Spouse</option>
                                            <option value="Child">Child</option>
                                            <option value="Parent">Parent</option>
                                            <option value="Sibling">Sibling</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control form-control-sm" name="family_phone[]" placeholder="Mobile (optional)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control form-control-sm" name="family_age[]" placeholder="Age">
                                    </div>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- bKash Payment Instructions -->
                    <div class="bkash-pay-box">
                        <div class="bkash-head">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fa-solid fa-mobile-screen-button me-2" style="color: #E2136E;"></i> bKash Payment Method
                            </h5>
                        </div>

                        <ol class="ps-3 mb-3 text-secondary" style="line-height: 1.8;">
                            <li>Go to your bKash App or dial <strong>*247#</strong> and select <strong>Send Money</strong>.</li>
                            <li>Enter our Official bKash Number: <strong class="bkash-num-tag">01933-890894</strong></li>
                            <li>Enter the exact package amount: <strong>৳<?php echo number_format($plan['price'], 2); ?> BDT</strong>.</li>
                            <li>Use your TeleRx registered mobile as reference.</li>
                            <li>Enter your bKash PIN to complete the transfer.</li>
                            <li>Copy the <strong>Transaction ID (TrxID)</strong> and paste it below to activate your package immediately.</li>
                        </ol>

                        <div class="mt-3">
                            <label class="form-label fw-bold">bKash Transaction ID (TrxID) <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="transaction_id" 
                                   class="form-control form-control-lg bg-white" 
                                   placeholder="e.g. BL92XK9P10" 
                                   required 
                                   style="border: 2px solid #ffd8e7; font-weight: 600; letter-spacing: 1px;">
                            <div class="form-text text-muted">You will receive the TrxID via SMS from bKash upon payment completion.</div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" name="confirm_subscription" class="btn-trx-confirm">
                        <i class="fa-solid fa-circle-check me-2"></i> Confirm Payment & Activate Subscription
                    </button>

                    <div class="text-center mt-3">
                        <a href="subscription.php" class="text-muted small">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back to all subscription packages
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
