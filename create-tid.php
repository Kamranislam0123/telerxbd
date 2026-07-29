<?php
// Include your database configuration
require_once __DIR__ . '/php/config.php';

try {
    $conn = getDBConnection();
    
    // The details you provided
    $email = 'rabbani.mym@telerxbd.com';
    $password = '123456';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // We'll generate a random mobile and TID for now, you can update them later in DB
    $mobile = '01' . rand(100000000, 999999999);
    $tid = 'T' . rand(1000, 9999);
    
    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM special_tid_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "User with email $email already exists.<br>";
        
        // Update password just in case they exist but with a different password
        $stmt2 = $conn->prepare("UPDATE special_tid_users SET password = ? WHERE email = ?");
        $stmt2->bind_param("ss", $hashed_password, $email);
        $stmt2->execute();
        echo "Password updated successfully.";
        
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO special_tid_users (name, email, mobile, tid, password, status) VALUES (?, ?, ?, ?, ?, '1')");
        $name = 'Rabbani Mym';
        $stmt->bind_param("sssss", $name, $email, $mobile, $tid, $hashed_password);
        
        if ($stmt->execute()) {
            echo "Successfully created Special TID user: <strong>$email</strong><br>";
            echo "Password: <strong>$password</strong><br>";
            echo "Generated Mobile: <strong>$mobile</strong><br>";
            echo "Generated TID: <strong>$tid</strong><br>";
        } else {
            echo "Error creating user: " . $stmt->error;
        }
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
