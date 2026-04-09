<?php
header('Content-Type: application/json');

$config_path = __DIR__ . '/../php/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['success' => false, 'error' => 'Server configuration missing.']);
    exit;
}
require_once $config_path;

if (session_status() === PHP_SESSION_NONE) session_start();

// Determine sender account
$sender_account = null;
if (!empty($_SESSION['doctor_id'])) {
    $sender_account = 'doctor_' . (int)$_SESSION['doctor_id'];
} elseif (!empty($_SESSION['patient_id'])) {
    $sender_account = 'patient_' . (int)$_SESSION['patient_id'];
} else {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$receiver = $_POST['receiver'] ?? '';
$message = $_POST['message'] ?? '';

$receiver = trim($receiver);
$message = trim($message);

if (!$receiver || !$message) {
    echo json_encode(['success' => false, 'error' => 'Missing receiver or message']);
    exit;
}

// validate receiver format doctor_# or patient_#
if (!preg_match('/^(doctor|patient)_[0-9]+$/', $receiver)) {
    echo json_encode(['success' => false, 'error' => 'Invalid receiver']);
    exit;
}

try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO chat_messages (sender_account, receiver_account, message) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $sender_account, $receiver, $message);
    $ok = $stmt->execute();
    if (!$ok) throw new Exception('DB insert failed');
    $insert_id = $stmt->insert_id;
    $stmt->close();

    // fetch inserted row
    $s = $conn->prepare("SELECT id, sender_account, receiver_account, message, created_at FROM chat_messages WHERE id = ? LIMIT 1");
    $s->bind_param('i', $insert_id);
    $s->execute();
    $res = $s->get_result();
    $row = $res->fetch_assoc();
    $s->close();
    $conn->close();

    echo json_encode(['success' => true, 'message' => $row['message'], 'created_at' => $row['created_at'], 'id' => $row['id'], 'sender_account' => $row['sender_account']]);
    exit;

} catch (Exception $e) {
    error_log('send_message error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
    exit;
}

?>
