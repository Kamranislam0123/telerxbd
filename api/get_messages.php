<?php
header('Content-Type: application/json');

$config_path = __DIR__ . '/../php/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['success' => false, 'error' => 'Server configuration missing.']);
    exit;
}
require_once $config_path;

if (session_status() === PHP_SESSION_NONE) session_start();

$peer = $_GET['with'] ?? '';
$peer = trim($peer);
if (!$peer || !preg_match('/^(doctor|patient)_[0-9]+$/', $peer)) {
    echo json_encode(['success' => false, 'error' => 'Missing or invalid "with" parameter']);
    exit;
}

$sender_account = null;
if (!empty($_SESSION['doctor_id'])) {
    $sender_account = 'doctor_' . (int)$_SESSION['doctor_id'];
} elseif (!empty($_SESSION['patient_id'])) {
    $sender_account = 'patient_' . (int)$_SESSION['patient_id'];
} else {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, sender_account, receiver_account, message, created_at FROM chat_messages WHERE (sender_account=? AND receiver_account=?) OR (sender_account=? AND receiver_account=?) ORDER BY created_at ASC");
    $stmt->bind_param('ssss', $sender_account, $peer, $peer, $sender_account);
    $stmt->execute();
    $res = $stmt->get_result();
    $messages = [];
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true, 'messages' => $messages]);
    exit;

} catch (Exception $e) {
    error_log('get_messages error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
    exit;
}

?>
