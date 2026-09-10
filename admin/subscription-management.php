<?php
require_once __DIR__ . '/../php/config.php';

$message = '';
$message_type = '';

try {
    $conn = getDBConnection();

    // Handle status update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $sub_id = (int)$_POST['subscription_id'];
        $new_status = trim($_POST['status']);
        $new_calls_used = (int)$_POST['emergency_calls_used'];

        if (in_array($new_status, ['active', 'pending', 'expired', 'cancelled'])) {
            $upd = $conn->prepare("UPDATE patient_subscriptions SET status = ?, emergency_calls_used = ? WHERE id = ?");
            $upd->bind_param("sii", $new_status, $new_calls_used, $sub_id);
            if ($upd->execute()) {
                $message = "Subscription #SUB-" . str_pad($sub_id, 5, '0', STR_PAD_LEFT) . " updated successfully.";
                $message_type = "success";
            }
            $upd->close();
        }
    }

    // Fetch subscriptions
    $sql = "
        SELECT ps.*, 
               p.name as patient_name, p.phone as patient_phone, p.email as patient_email,
               sp.name as plan_name, sp.duration_label
        FROM patient_subscriptions ps
        JOIN patients p ON ps.patient_id = p.id
        JOIN subscription_plans sp ON ps.plan_id = sp.id
        ORDER BY ps.id DESC
    ";
    $result = $conn->query($sql);
    $subscriptions = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $subscriptions[] = $row;
        }
    }

    // Summary stats
    $total_subscribers = count($subscriptions);
    $active_count = 0;
    $total_revenue = 0;
    foreach ($subscriptions as $s) {
        if ($s['status'] === 'active') $active_count++;
        $total_revenue += (float)$s['amount_paid'];
    }

    $conn->close();
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    $message_type = "danger";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeleRx Admin - Subscription Management</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="header-left">
                <a href="../index.php" class="logo">
                    <img src="../assets/img/logo.svg" alt="Logo" style="height: 40px;">
                </a>
            </div>
            <a href="javascript:void(0);" id="toggle_btn">
                <i class="fe fe-text-align-left"></i>
            </a>
            <ul class="nav user-menu">
                <li class="nav-item">
                    <a href="../subscription.php" target="_blank" class="nav-link">
                        <i class="fe fe-external-link"></i> View Plans Live
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title"><span>Main</span></li>
                        <li><a href="index.html"><i class="fe fe-home"></i> <span>Dashboard</span></a></li>
                        <li><a href="appointment-list.html"><i class="fe fe-layout"></i> <span>Appointments</span></a></li>
                        <li><a href="patient-list.html"><i class="fe fe-user"></i> <span>Patients</span></a></li>
                        <li class="active"><a href="subscription-management.php"><i class="fe fe-star-o"></i> <span>Subscriptions</span></a></li>
                        <li><a href="transactions-list.html"><i class="fe fe-activity"></i> <span>Transactions</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Subscription Management</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Subscriptions</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Stats Row -->
                <div class="row">
                    <div class="col-xl-4 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon text-primary border-primary">
                                        <i class="fe fe-users"></i>
                                    </span>
                                    <div class="dash-count">
                                        <h3><?php echo $total_subscribers; ?></h3>
                                    </div>
                                </div>
                                <div class="dash-widget-info">
                                    <h6 class="text-muted">Total Subscriptions</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon text-success border-success">
                                        <i class="fe fe-check-circle"></i>
                                    </span>
                                    <div class="dash-count">
                                        <h3><?php echo $active_count; ?></h3>
                                    </div>
                                </div>
                                <div class="dash-widget-info">
                                    <h6 class="text-muted">Active Plans</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon text-warning border-warning">
                                        <i class="fe fe-money"></i>
                                    </span>
                                    <div class="dash-count">
                                        <h3>৳<?php echo number_format($total_revenue, 0); ?></h3>
                                    </div>
                                </div>
                                <div class="dash-widget-info">
                                    <h6 class="text-muted">Total Subscription Revenue</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscriptions Table -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">All Patient Subscriptions</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Patient Name</th>
                                                <th>Phone</th>
                                                <th>Plan</th>
                                                <th>Fee</th>
                                                <th>TrxID</th>
                                                <th>Emergency Calls</th>
                                                <th>Validity</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($subscriptions)): ?>
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted py-4">No subscriptions found.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($subscriptions as $sub): ?>
                                                    <tr>
                                                        <td>#SUB-<?php echo str_pad($sub['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                                        <td>
                                                            <h2 class="table-avatar">
                                                                <strong><?php echo htmlspecialchars($sub['patient_name']); ?></strong>
                                                            </h2>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($sub['patient_phone']); ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($sub['plan_name']); ?></strong>
                                                            <div class="text-muted small"><?php echo htmlspecialchars($sub['duration_label']); ?></div>
                                                        </td>
                                                        <td>৳<?php echo number_format($sub['amount_paid'], 2); ?></td>
                                                        <td><code><?php echo htmlspecialchars($sub['transaction_id']); ?></code></td>
                                                        <td>
                                                            <span class="badge bg-info-light">
                                                                <?php echo (int)$sub['emergency_calls_used']; ?> / <?php echo (int)$sub['emergency_calls_total']; ?> used
                                                            </span>
                                                        </td>
                                                        <td class="small">
                                                            <?php echo date('d M Y', strtotime($sub['start_date'])); ?> - <br>
                                                            <?php echo date('d M Y', strtotime($sub['end_date'])); ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($sub['status'] === 'active'): ?>
                                                                <span class="badge badge-pill bg-success-light">Active</span>
                                                            <?php elseif ($sub['status'] === 'expired'): ?>
                                                                <span class="badge badge-pill bg-secondary-light">Expired</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-pill bg-warning-light"><?php echo ucfirst($sub['status']); ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-primary"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editSubModal<?php echo $sub['id']; ?>">
                                                                <i class="fe fe-edit"></i> Manage
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Edit Modal -->
                                                    <div class="modal fade" id="editSubModal<?php echo $sub['id']; ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form method="POST">
                                                                    <input type="hidden" name="subscription_id" value="<?php echo $sub['id']; ?>">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Manage Subscription #SUB-<?php echo str_pad($sub['id'], 5, '0', STR_PAD_LEFT); ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p><strong>Patient:</strong> <?php echo htmlspecialchars($sub['patient_name']); ?> (<?php echo htmlspecialchars($sub['patient_phone']); ?>)</p>
                                                                        <p><strong>Plan:</strong> <?php echo htmlspecialchars($sub['plan_name']); ?> (৳<?php echo number_format($sub['amount_paid'], 0); ?>)</p>
                                                                        <p><strong>TrxID:</strong> <code><?php echo htmlspecialchars($sub['transaction_id']); ?></code></p>
                                                                        
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Subscription Status</label>
                                                                            <select name="status" class="form-select">
                                                                                <option value="active" <?php echo $sub['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                                                <option value="pending" <?php echo $sub['status'] === 'pending' ? 'selected' : ''; ?>>Pending Verification</option>
                                                                                <option value="expired" <?php echo $sub['status'] === 'expired' ? 'selected' : ''; ?>>Expired</option>
                                                                                <option value="cancelled" <?php echo $sub['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label">Emergency Calls Used (Total: <?php echo $sub['emergency_calls_total']; ?>)</label>
                                                                            <input type="number" name="emergency_calls_used" class="form-control" value="<?php echo (int)$sub['emergency_calls_used']; ?>" min="0" max="<?php echo (int)$sub['emergency_calls_total']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" name="update_status" class="btn btn-primary">Save Changes</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
