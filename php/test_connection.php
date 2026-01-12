<?php
/**
 * Database Connection Test
 * Use this file to test if your database connection is working
 * Access it via: http://your-domain/php/test_connection.php
 */

require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Database Connection Test</h1>";
echo "<hr>";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>";
try {
    $conn = getDBConnection();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
    echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>User:</strong> " . DB_USER . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check your database credentials in config.php</p>";
    exit;
}

// Test 2: Check if doctors table exists
echo "<hr><h2>Test 2: Check Doctors Table</h2>";
try {
    $result = $conn->query("SHOW TABLES LIKE 'doctors'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Doctors table exists!</p>";
        
        // Check table structure
        $result = $conn->query("DESCRIBE doctors");
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Count records
        $result = $conn->query("SELECT COUNT(*) as count FROM doctors");
        $row = $result->fetch_assoc();
        echo "<p><strong>Total Doctors:</strong> " . $row['count'] . "</p>";
        
    } else {
        echo "<p style='color: red;'>✗ Doctors table does not exist!</p>";
        echo "<p>Please run the SQL commands from database.sql to create the table.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error checking doctors table!</p>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
}

// Test 3: Check if doctor_sessions table exists
echo "<hr><h2>Test 3: Check Doctor Sessions Table</h2>";
try {
    $result = $conn->query("SHOW TABLES LIKE 'doctor_sessions'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Doctor sessions table exists!</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Doctor sessions table does not exist (optional, but recommended).</p>";
        echo "<p>You can create it by running the SQL commands from database.sql</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠ Error checking sessions table (this is optional).</p>";
}

// Test 4: Test a sample query
echo "<hr><h2>Test 4: Sample Query Test</h2>";
try {
    $stmt = $conn->prepare("SELECT id, name, email FROM doctors LIMIT 5");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        echo "<p style='color: green;'>✓ Query execution successful!</p>";
        
        if ($result->num_rows > 0) {
            echo "<h3>Sample Doctors:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No doctors found in database. You can register a new doctor.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red;'>✗ Failed to prepare statement!</p>";
        echo "<p><strong>Error:</strong> " . $conn->error . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Query test failed!</p>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
}

// Test 5: PHP Configuration
echo "<hr><h2>Test 5: PHP Configuration</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Not Active") . "</p>";
echo "<p><strong>Error Display:</strong> " . (ini_get('display_errors') ? "On" : "Off") . "</p>";
echo "<p><strong>Error Reporting:</strong> " . error_reporting() . "</p>";

$conn->close();

echo "<hr>";
echo "<p><strong>Note:</strong> If all tests pass, your database is configured correctly.</p>";
echo "<p>If tests fail, please check:</p>";
echo "<ul>";
echo "<li>Database credentials in config.php</li>";
echo "<li>Database and tables exist (run database.sql)</li>";
echo "<li>MySQL service is running</li>";
echo "</ul>";
?>

