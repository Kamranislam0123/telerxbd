<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "PHP is working. Version: " . phpversion() . "\n";
echo "mysqli available: " . (function_exists('mysqli_connect') ? 'yes' : 'NO') . "\n";
try {
    require_once __DIR__ . '/php/config.php';
    $conn = getDBConnection();
    echo "DB connection: OK\n";
} catch (\Throwable $e) {
    echo "DB connection FAILED: " . $e->getMessage() . "\n";
}