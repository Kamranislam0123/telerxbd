<?php
/**
 * Database Setup Script for TeleRx Bangladesh
 * This script creates the database and tables from the schema file
 */

// Start output buffering
ob_start();

// Include configuration
$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    die("Error: Configuration file not found at {$config_path}\n");
}
require_once $config_path;

echo "<h2>TeleRx Bangladesh - Database Setup</h2>";
echo "<pre>";

// Check if we can connect to MySQL server
echo "1. Connecting to MySQL server...\n";
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error . "\n");
}
echo "✅ Connected to MySQL server successfully\n";

// Create database if it doesn't exist
echo "\n2. Creating database '" . DB_NAME . "' if it doesn't exist...\n";
$sql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "✅ Database created or already exists\n";
} else {
    die("❌ Error creating database: " . $conn->error . "\n");
}

// Select the database
$conn->select_db(DB_NAME);

// Read and execute the schema file
$schema_file = __DIR__ . '/../database_schema.sql';
if (!file_exists($schema_file)) {
    die("❌ Schema file not found at {$schema_file}\n");
}

echo "\n3. Executing database schema...\n";
$sql_content = file_get_contents($schema_file);

// Split SQL commands by semicolon
$sql_commands = array_filter(array_map('trim', explode(';', $sql_content)));

$executed_count = 0;
$errors = [];

foreach ($sql_commands as $command) {
    if (!empty($command) && !preg_match('/^--/', $command)) {
        if ($conn->query($command) === TRUE) {
            $executed_count++;
            echo "✅ Executed: " . substr($command, 0, 50) . "...\n";
        } else {
            $errors[] = "❌ Error: " . $conn->error . " (Command: " . substr($command, 0, 50) . "...)";
        }
    }
}

echo "\n4. Setup Summary:\n";
echo "   - Commands executed: {$executed_count}\n";

if (empty($errors)) {
    echo "   - Errors: 0\n";
    echo "\n🎉 Database setup completed successfully!\n";
    echo "\n📋 Next Steps:\n";
    echo "   1. Test doctor registration at: register.html\n";
    echo "   2. Test doctor login at: login.html\n";
    echo "   3. Sample accounts:\n";
    echo "      - Doctor: dr.rahman@telerx.com / password\n";
    echo "      - Doctor: dr.begum@telerx.com / password\n";
    echo "      - Doctor: dr.hossain@telerx.com / password\n";
} else {
    echo "   - Errors: " . count($errors) . "\n";
    echo "\n❌ Setup completed with errors:\n";
    foreach ($errors as $error) {
        echo "   {$error}\n";
    }
}

$conn->close();
echo "</pre>";
?>
