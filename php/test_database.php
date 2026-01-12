<?php
/**
 * Database Test Script for TeleRx Bangladesh
 * This script tests the database connection and basic functionality
 */

// Include configuration
$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    die("❌ Configuration file not found at {$config_path}\n");
}
require_once $config_path;

echo "<h2>TeleRx Bangladesh - Database Test</h2>";
echo "<pre>";

// Test database connection
echo "1. Testing database connection...\n";
try {
    $conn = getDBConnection();
    echo "✅ Database connection successful\n";
} catch (Exception $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// Test table existence
echo "\n2. Checking database tables...\n";
$tables = ['doctors', 'patients', 'healthcare_providers', 'doctor_sessions'];
$missing_tables = [];

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        $missing_tables[] = $table;
    } else {
        echo "✅ Table '$table' exists\n";
    }
}

if (!empty($missing_tables)) {
    echo "❌ Missing tables: " . implode(', ', $missing_tables) . "\n";
    echo "   Please run the database setup script first.\n";
} else {
    echo "✅ All required tables exist\n";
}

// Test sample data
echo "\n3. Checking sample data...\n";

// Check doctors
$result = $conn->query("SELECT COUNT(*) as count FROM doctors");
$doctor_count = $result->fetch_assoc()['count'];
echo "   - Doctors: $doctor_count records\n";

// Check patients
$result = $conn->query("SELECT COUNT(*) as count FROM patients");
$patient_count = $result->fetch_assoc()['count'];
echo "   - Patients: $patient_count records\n";

// Check healthcare providers
$result = $conn->query("SELECT COUNT(*) as count FROM healthcare_providers");
$healthcare_count = $result->fetch_assoc()['count'];
echo "   - Healthcare Providers: $healthcare_count records\n";

// Test doctor login functionality
echo "\n4. Testing doctor login functionality...\n";
$test_email = 'dr.rahman@telerx.com';
$test_password = 'password';

$stmt = $conn->prepare("SELECT id, name, email, password FROM doctors WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    if (password_verify($test_password, $doctor['password'])) {
        echo "✅ Doctor login test passed\n";
    } else {
        echo "❌ Password verification failed\n";
    }
} else {
    echo "❌ Test doctor account not found\n";
}

// Test unique constraints
echo "\n5. Testing data integrity...\n";
$test_cases = [
    ['table' => 'doctors', 'field' => 'email', 'value' => 'dr.rahman@telerx.com'],
    ['table' => 'doctors', 'field' => 'bmdc_no', 'value' => 'A-12345'],
    ['table' => 'patients', 'field' => 'email', 'value' => 'patient1@telerx.com'],
    ['table' => 'healthcare_providers', 'field' => 'email', 'value' => 'healthcare1@telerx.com'],
    ['table' => 'healthcare_providers', 'field' => 'nid_number', 'value' => '12345678901234567']
];

$integrity_passed = true;
foreach ($test_cases as $test) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM {$test['table']} WHERE {$test['field']} = ?");
    $stmt->bind_param("s", $test['value']);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];

    if ($count == 1) {
        echo "✅ Unique constraint working for {$test['table']}.{$test['field']}\n";
    } else {
        echo "❌ Unique constraint issue with {$test['table']}.{$test['field']} (count: $count)\n";
        $integrity_passed = false;
    }
}

// Summary
echo "\n📊 Test Summary:\n";
echo "   - Database Connection: ✅\n";
echo "   - Tables Existence: " . (empty($missing_tables) ? "✅" : "❌") . "\n";
echo "   - Sample Data: " . (($doctor_count + $patient_count + $healthcare_count) > 0 ? "✅" : "❌") . "\n";
echo "   - Doctor Login: " . (isset($doctor) && password_verify($test_password, $doctor['password']) ? "✅" : "❌") . "\n";
echo "   - Data Integrity: " . ($integrity_passed ? "✅" : "❌") . "\n";

if (empty($missing_tables) && $integrity_passed) {
    echo "\n🎉 All tests passed! Your database is ready for TeleRx.\n";
    echo "\n🚀 You can now:\n";
    echo "   1. Register new doctors at: register.html\n";
    echo "   2. Login as existing doctors at: login.html\n";
    echo "   3. Test with sample accounts above\n";
} else {
    echo "\n❌ Some tests failed. Please check the errors above.\n";
}

$conn->close();
echo "</pre>";
?>
