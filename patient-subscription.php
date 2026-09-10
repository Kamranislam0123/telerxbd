<?php
session_start();
require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/subscription-helper.php';

// Force patient login
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit;
}

$patient_id = (int)$_SESSION['patient_id'];
$message = '';
$message_type = '';

if (isset($_GET['activated'])) {
    $message = "🎉 Your TeleRx Subscription has been activated successfully! You can now enjoy free emergency calls and consultation discounts.";
    $message_type = "success";
}

try {
    $conn = getDBConnection();

    // Fetch patient info
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $patient = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$patient) {
        header('Location: login.php');
        exit;
    }

    $active_sub = getActiveSubscription($patient_id, $conn);

    // Handle adding a family member
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_family_member']) && $active_sub) {
        $member_name = trim($_POST['member_name'] ?? '');
        $relationship = trim($_POST['relationship'] ?? 'Family');
        $phone = trim($_POST['phone'] ?? '');
        $age = trim($_POST['age'] ?? '');
        $gender = trim($_POST['gender'] ?? 'Other');

        if (empty($member_name)) {
            $message = "Please enter the family member's name.";
            $message_type = "danger";
        } else {
            // Check quota
            $fam_check = $conn->prepare("SELECT COUNT(*) as cnt FROM subscription_family_members WHERE subscription_id = ?");
            $fam_check->bind_param("i", $active_sub['id']);
            $fam_check->execute();
            $curr_count = (int)$fam_check->get_result()->fetch_assoc()['cnt'];
            $fam_check->close();

            if ($curr_count >= (int)$active_sub['family_member_quota']) {
                $message = "Family member quota limit reached for your plan.";
                $message_type = "warning";
            } else {
                $add_stmt = $conn->prepare("
                    INSERT INTO subscription_family_members 
                    (subscription_id, patient_id, member_name, relationship, phone, age, gender) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $add_stmt->bind_param("iisssss", $active_sub['id'], $patient_id, $member_name, $relationship, $phone, $age, $gender);
                if ($add_stmt->execute()) {
                    $message = "Family member added successfully!";
                    $message_type = "success";
                }
                $add_stmt->close();
            }
        }
    }

    // Handle removing a family member
    if (isset($_GET['remove_member']) && $active_sub) {
        $remove_id = (int)$_GET['remove_member'];
        $del_stmt = $conn->prepare("DELETE FROM subscription_family_members WHERE id = ? AND subscription_id = ? AND patient_id = ?");
        $del_stmt->bind_param("iii", $remove_id, $active_sub['id'], $patient_id);
        $del_stmt->execute();
        $del_stmt->close();
        header("Location: patient-subscription.php");
        exit;
    }

    // Fetch family members
    $family_members = [];
    if ($active_sub) {
        $family_members = getSubscriptionFamilyMembers($active_sub['id'], $conn);
    }

    // Fetch subscription history
    $history_stmt = $conn->prepare("
        SELECT ps.*, sp.name as plan_name, sp.duration_label
        FROM patient_subscriptions ps
        JOIN subscription_plans sp ON ps.plan_id = sp.id
        WHERE ps.patient_id = ?
        ORDER BY ps.id DESC
    ");
    $history_stmt->bind_param("i", $patient_id);
    $history_stmt->execute();
    $history_res = $history_stmt->get_result();
    $subscription_history = [];
    while ($row = $history_res->fetch_assoc()) {
        $subscription_history[] = $row;
    }
    $history_stmt->close();

    $conn->close();
} catch (Exception $e) {
    error_log("Subscription page error: " . $e->getMessage());
}

$page_title = "My Subscription - TeleRx";
include 'header.php';
?>

<style>
.sub-plan-hero {
    background: linear-gradient(135deg, #0e82fd 0%, #0352bd 100%);
    border-radius: 16px;
    color: #fff;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 25px rgba(14, 130, 253, 0.2);
}
.sub-stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    height: 100%;
}
.sub-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
}
.sub-progress-bar {
    height: 10px;
    border-radius: 5px;
    background: #e2e8f0;
    overflow: hidden;
    margin-top: 10px;
}
.sub-progress-fill {
    height: 100%;
    background: #10b981;
    border-radius: 5px;
}
.badge-active-sub {
    background: #10b981;
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}
.family-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <h2 class="breadcrumb-title">My Subscription</h2>
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

<!-- Page Content -->
<div class="content">
    <div class="container">
        <div class="row">
            <!-- Left Sidebar -->
            <?php include 'patient-leftside-menu.php'; ?>

            <!-- Main Content Area -->
            <div class="col-lg-8 col-xl-9">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($active_sub): 
                    $total_calls = (int)$active_sub['emergency_calls_total'];
                    $used_calls = (int)$active_sub['emergency_calls_used'];
                    $remaining_calls = max(0, $total_calls - $used_calls);
                    $progress_percent = $total_calls > 0 ? min(100, round(($remaining_calls / $total_calls) * 100)) : 0;
                    
                    $days_left = max(0, round((strtotime($active_sub['end_date']) - time()) / (60 * 60 * 24)));
                ?>
                    <!-- Active Subscription Hero -->
                    <div class="sub-plan-hero">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge-active-sub"><i class="fa-solid fa-circle-check me-1"></i> ACTIVE PLAN</span>
                                    <span class="badge bg-white text-dark"><?php echo htmlspecialchars($active_sub['plan_badge'] ?? 'TeleRx Member'); ?></span>
                                </div>
                                <h2 class="text-white mb-1 fw-bold"><?php echo htmlspecialchars($active_sub['plan_name']); ?> Package</h2>
                                <p class="text-white-50 mb-3">
                                    Valid until <strong><?php echo date('d M, Y', strtotime($active_sub['end_date'])); ?></strong> 
                                    (<?php echo $days_left; ?> days remaining)
                                </p>
                                
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="subscription.php" class="btn btn-sm btn-light fw-bold text-primary">
                                        <i class="fa-solid fa-arrows-rotate me-1"></i> Upgrade / Renew Plan
                                    </a>
                                    <a href="emergency-booking.php" class="btn btn-sm btn-outline-light">
                                        <i class="fa-solid fa-truck-medical me-1"></i> Use Emergency Call
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                                <div class="display-6 fw-bold text-white"><?php echo $remaining_calls; ?> / <?php echo $total_calls; ?></div>
                                <div class="text-white-50 small">Emergency Calls Remaining</div>
                            </div>
                        </div>
                    </div>

                    <!-- Quota & Benefits Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="sub-stat-card">
                                <div class="sub-stat-icon bg-danger-subtle text-danger">
                                    <i class="fa-solid fa-phone-volume"></i>
                                </div>
                                <h6 class="text-muted mb-1">Emergency Calls Quota</h6>
                                <h4 class="fw-bold mb-1"><?php echo $remaining_calls; ?> Available</h4>
                                <div class="sub-progress-bar">
                                    <div class="sub-progress-fill" style="width: <?php echo $progress_percent; ?>%;"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mt-1">
                                    <span>Used: <?php echo $used_calls; ?></span>
                                    <span>Total: <?php echo $total_calls; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="sub-stat-card">
                                <div class="sub-stat-icon bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-user-doctor"></i>
                                </div>
                                <h6 class="text-muted mb-1">Consultation Discount</h6>
                                <h4 class="fw-bold mb-1"><?php echo (int)$active_sub['gp_discount_percent']; ?>% OFF</h4>
                                <div class="text-muted small">
                                    Applicable to <strong>All Doctors & Specialists</strong>
                                </div>
                                <div class="text-success small mt-2">
                                    <i class="fa-solid fa-circle-check me-1"></i> Auto-applied during booking
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="sub-stat-card">
                                <div class="sub-stat-icon bg-warning-subtle text-warning">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <h6 class="text-muted mb-1">Additional Benefits</h6>
                                <h4 class="fw-bold mb-1">Digital Rx & Care</h4>
                                <div class="text-muted small">
                                    Home Service: <strong><?php echo (int)$active_sub['home_service_discount_percent']; ?>%</strong><br>
                                    Product Purchase: <strong><?php echo (int)$active_sub['purchase_discount_percent']; ?>%</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Members Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h5 class="card-title mb-0 fw-bold">
                                    <i class="fa-solid fa-people-roof text-primary me-2"></i>Covered Family Members
                                </h5>
                                <span class="text-muted small">
                                    Registered <?php echo count($family_members); ?> of <?php echo (int)$active_sub['family_member_quota']; ?> members allowed
                                </span>
                            </div>
                            <?php if (count($family_members) < (int)$active_sub['family_member_quota']): ?>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFamilyModal">
                                    <i class="fa-solid fa-plus me-1"></i> Add Member
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (empty($family_members)): ?>
                                <div class="text-center py-4">
                                    <i class="fa-solid fa-user-group text-muted fs-1 mb-2"></i>
                                    <p class="text-muted mb-2">No family members registered yet.</p>
                                    <p class="text-secondary small mb-3">Your package allows coverage for up to <?php echo (int)$active_sub['family_member_quota']; ?> persons.</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFamilyModal">
                                        <i class="fa-solid fa-plus me-1"></i> Add Family Member
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Member Name</th>
                                                <th>Relationship</th>
                                                <th>Mobile</th>
                                                <th>Age / Gender</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($family_members as $fam): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="family-avatar">
                                                                <?php echo strtoupper(substr($fam['member_name'], 0, 1)); ?>
                                                            </div>
                                                            <span class="fw-bold"><?php echo htmlspecialchars($fam['member_name']); ?></span>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($fam['relationship']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($fam['phone'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars(($fam['age'] ? $fam['age'] . ' yrs' : '') . ' ' . ($fam['gender'] ?? '')); ?></td>
                                                    <td class="text-end">
                                                        <a href="patient-subscription.php?remove_member=<?php echo (int)$fam['id']; ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           onclick="return confirm('Are you sure you want to remove this family member?');">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- No Active Subscription State -->
                    <div class="card text-center p-5 mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 70px; height: 70px; font-size: 32px;">
                                <i class="fa-solid fa-heart-pulse"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold">No Active TeleRx Subscription</h3>
                        <p class="text-muted mx-auto" style="max-width: 500px;">
                            Subscribe to TeleRx Packages for 24/7 free emergency doctor consultations, discounts on GP and Specialist visits, and digital prescription tracking for you and your family.
                        </p>
                        <div>
                            <a href="subscription.php" class="btn btn-primary btn-lg px-4">
                                <i class="fa-solid fa-shield-heart me-2"></i> Explore Subscription Plans
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Subscription & Payment History Table -->
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fa-solid fa-receipt text-primary me-2"></i>Subscription History & Invoices
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($subscription_history)): ?>
                            <div class="text-center py-4 text-muted">No subscription transactions found.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Plan</th>
                                            <th>Amount</th>
                                            <th>Payment</th>
                                            <th>TrxID</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subscription_history as $hist): ?>
                                            <tr>
                                                <td><span class="text-primary fw-bold">#SUB-<?php echo str_pad($hist['id'], 5, '0', STR_PAD_LEFT); ?></span></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($hist['plan_name']); ?></strong>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($hist['duration_label']); ?></div>
                                                </td>
                                                <td><strong class="text-dark">৳<?php echo number_format($hist['amount_paid'], 2); ?></strong></td>
                                                <td><span class="badge bg-light text-dark text-uppercase"><?php echo htmlspecialchars($hist['payment_method']); ?></span></td>
                                                <td><code><?php echo htmlspecialchars($hist['transaction_id']); ?></code></td>
                                                <td class="small text-muted">
                                                    <?php echo date('d M Y', strtotime($hist['start_date'])); ?> - <br>
                                                    <?php echo date('d M Y', strtotime($hist['end_date'])); ?>
                                                </td>
                                                <td>
                                                    <?php if ($hist['status'] === 'active'): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php elseif ($hist['status'] === 'expired'): ?>
                                                        <span class="badge bg-secondary">Expired</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark"><?php echo ucfirst($hist['status']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Add Family Member Modal -->
<?php if ($active_sub): ?>
<div class="modal fade" id="addFamilyModal" tabindex="-1" aria-labelledby="addFamilyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addFamilyModalLabel">Add Family Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="member_name" class="form-control" required placeholder="e.g. Sarah Rahman">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Relationship <span class="text-danger">*</span></label>
                            <select name="relationship" class="form-select" required>
                                <option value="Spouse">Spouse</option>
                                <option value="Child">Child</option>
                                <option value="Parent">Parent</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="Female">Female</option>
                                <option value="Male">Male</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-control" placeholder="e.g. 32">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="01XXXXXXXXX">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_family_member" class="btn btn-primary">Save Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
