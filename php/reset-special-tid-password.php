<?php
/**
 * Reset Special TID user password.
 *
 * Usage (CLI):
 *   php php/reset-special-tid-password.php --id=1 --password=12345
 *   php php/reset-special-tid-password.php --email=user@example.com --password=12345
 *   php php/reset-special-tid-password.php --mobile=017XXXXXXXX --password=12345
 */

require_once __DIR__ . '/config.php';

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "This script is CLI only.\n";
    exit(1);
}

$options = getopt('', ['id::', 'email::', 'mobile::', 'password:']);
$id = isset($options['id']) ? (int) $options['id'] : 0;
$email = isset($options['email']) ? trim((string) $options['email']) : '';
$mobile = isset($options['mobile']) ? preg_replace('/[^0-9]/', '', (string) $options['mobile']) : '';
$password = isset($options['password']) ? (string) $options['password'] : '';

if ($password === '') {
    echo "Missing --password\n";
    exit(1);
}

if ($id <= 0 && $email === '' && $mobile === '') {
    echo "Provide one identifier: --id or --email or --mobile\n";
    exit(1);
}

try {
    $conn = getDBConnection();
    $hash = password_hash($password, PASSWORD_DEFAULT);

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE special_tid_users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $id);
    } elseif ($email !== '') {
        $stmt = $conn->prepare("UPDATE special_tid_users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hash, $email);
    } else {
        $stmt = $conn->prepare("UPDATE special_tid_users SET password = ? WHERE mobile = ?");
        $stmt->bind_param("ss", $hash, $mobile);
    }

    if (!$stmt->execute()) {
        throw new Exception("Update failed: " . $stmt->error);
    }

    $affected = $stmt->affected_rows;
    $stmt->close();
    $conn->close();

    if ($affected > 0) {
        echo "Password reset successful.\n";
    } else {
        echo "No user updated. Check identifier value.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
