<?php
require_once __DIR__ . '/php/config.php';
$conn = getDBConnection();

// Check if emergency doctor exists
$email = 'emergency@telerx.com';
$check = $conn->prepare("SELECT id FROM doctors WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$res = $check->get_result();

if ($res->num_rows == 0) {
    $name = 'Emergency Doctor';
    $phone = '00000000000';
    $bmdc_no = 'EMG-00000';
    $password = password_hash('123456', PASSWORD_DEFAULT);
    
    $ins = $conn->prepare("INSERT INTO doctors (name, email, phone, bmdc_no, password) VALUES (?, ?, ?, ?, ?)");
    if (!$ins) {
        die("Prepare failed: " . $conn->error);
    }
    $ins->bind_param("sssss", $name, $email, $phone, $bmdc_no, $password);
    if ($ins->execute()) {
        $doc_id = $conn->insert_id;
        echo "Emergency doctor created with ID: $doc_id\n";
        
        // Also add profile
        $ins_prof = $conn->prepare("INSERT INTO doctor_profiles (doctor_id, specialty, consultation_fee, is_available) VALUES (?, 'Emergency Care', 0, 1)");
        $ins_prof->bind_param("i", $doc_id);
        if ($ins_prof->execute()) {
             echo "Doctor profile created.\n";
        } else {
             echo "Failed to create doctor profile: " . $conn->error . "\n";
        }
    } else {
        echo "Failed to create doctor: " . $conn->error . "\n";
    }
} else {
    echo "Emergency doctor already exists.\n";
}

$conn->close();
?>
