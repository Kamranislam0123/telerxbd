<?php
/**
 * Run migration: Add call status columns to appointments table
 * Run from browser: http://yoursite/php/run-migration-call-status.php
 * Or from CLI: php php/run-migration-call-status.php
 */
$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<pre>';
}

$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    echo "Error: config.php not found.\n";
    exit(1);
}
require_once $config_path;

try {
    $conn = getDBConnection();
    
    // Check if call_status already exists
    $result = $conn->query("SHOW COLUMNS FROM `appointments` LIKE 'call_status'");
    $columnExists = ($result && $result->num_rows > 0);
    
    if (!$columnExists) {
        $sql = "ALTER TABLE `appointments` 
                ADD COLUMN `call_status` VARCHAR(20) DEFAULT NULL,
                ADD COLUMN `call_started_at` TIMESTAMP DEFAULT NULL";
                
        if ($conn->query($sql)) {
            echo "Migration completed successfully. Added call_status and call_started_at to appointments table.\n";
        } else {
            echo "Error adding columns: " . $conn->error . "\n";
            $conn->close();
            exit(1);
        }
    } else {
        echo "Migration skipped: call_status column already exists in appointments table.\n";
    }
    
    $conn->close();
    exit(0);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
