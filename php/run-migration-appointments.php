<?php
/**
 * Run migration: appointments_table.sql
 * Run from browser: .../php/run-migration-appointments.php or CLI: php php/run-migration-appointments.php
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

$migrationFile = __DIR__ . '/migrations/appointments_table.sql';
if (!file_exists($migrationFile)) {
    echo "Error: Migration file not found: $migrationFile\n";
    exit(1);
}

$sql = file_get_contents($migrationFile);
$sql = trim(preg_replace('/--.*$/m', '', $sql));
if (empty($sql)) {
    echo "Error: Migration file is empty.\n";
    exit(1);
}

try {
    $conn = getDBConnection();
    if ($conn->query($sql)) {
        $conn->close();
        echo "Migration completed successfully.\n";
        echo "Table created: appointments\n";
        exit(0);
    }
    echo "Error: " . $conn->error . "\n";
    $conn->close();
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
