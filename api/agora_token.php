<?php
header('Content-Type: application/json');

$config_path = __DIR__ . '/../php/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['success' => false, 'error' => 'Server configuration missing.']);
    exit;
}
require_once $config_path;
require_once __DIR__ . '/../php/RtmTokenBuilder.php';

// Credentials provided by user
$appId = 'd4ab628137c74b519e71dec351b83c34';
$appCert = '562a2389c4be4b6e8ee2da83d59d14d9';

if (session_status() === PHP_SESSION_NONE) session_start();

$user_account = '';
if (isset($_SESSION['patient_id'])) {
    $user_account = 'patient_' . $_SESSION['patient_id'];
} elseif (isset($_SESSION['doctor_id'])) {
    $user_account = 'doctor_' . $_SESSION['doctor_id'];
}

if (!$user_account) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
    exit;
}

$expireTimeInSeconds = 3600;
$currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
$privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

try {
    // Generate token using the builder
    $token = RtmTokenBuilder::buildToken($appId, $appCert, $user_account, RtmTokenBuilder::RoleRtmUser, $privilegeExpiredTs);
    echo json_encode([
        'success' => true,
        'token' => $token,
        'appId' => $appId,
        'userAccount' => $user_account
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
