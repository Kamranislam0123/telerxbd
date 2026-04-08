<?php
require_once 'config.php';
require_once 'AgoraDynamicKey/src/Message.php';
require_once 'AgoraDynamicKey/src/AccessToken.php';
require_once 'AgoraDynamicKey/src/RtcTokenBuilder.php';

use Agora\AgoraDynamicKey\RtcTokenBuilder;

// Agora credentials (এখানে আপনার তথ্য বসান)
define('AGORA_APP_ID', 'd4ab628137c74b519e71dec351b83c34');
define('AGORA_APP_CERTIFICATE', '562a2389c4be4b6e8ee2da83d59d14d9');

header('Content-Type: application/json');

// শুধু লগইন করা ইউজার
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$channelName = $_POST['channel'] ?? '';
$uid = (int)($_POST['uid'] ?? 0);

// Debug logging for developers
error_log("Agora Token Request - Channel: $channelName, UID: $uid, Session User: " . ($_SESSION['user_type'] ?? 'none'));

if (empty($channelName)) {
    echo json_encode(['success' => false, 'message' => 'Channel name required']);
    exit;
}

$token = RtcTokenBuilder::buildTokenWithUid(
    AGORA_APP_ID,
    AGORA_APP_CERTIFICATE,
    $channelName,
    $uid,
    RtcTokenBuilder::RoleAttendee,
    time() + 1800 // টোকেন ৩০ মিনিট বৈধ
);

echo json_encode([
    'success' => true,
    'app_id' => AGORA_APP_ID,
    'channel' => $channelName,
    'token' => $token,
    'uid' => $uid
]);
?>