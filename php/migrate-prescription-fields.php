<?php
/**
 * Migration: Add Clinical Record Columns to Appointments Table
 * Adds fields necessary for generating a medical prescription.
 */
require_once __DIR__ . '/config.php';

$columns = [
    'chief_complaints' => 'TEXT NULL',
    'on_examination' => 'TEXT NULL',
    'diagnosis' => 'TEXT NULL',
    'medications' => 'TEXT NULL', // Will store JSON or text
    'advice' => 'TEXT NULL',
    'follow_up_type' => 'VARCHAR(32) NULL',
    'prescription_path' => 'VARCHAR(255) NULL'
];

try {
    $conn = getDBConnection();
    echo "Starting migration...\n";

    foreach ($columns as $col => $def) {
        $check = $conn->query("SHOW COLUMNS FROM appointments LIKE '$col'");
        if ($check && $check->num_rows === 0) {
            $sql = "ALTER TABLE appointments ADD COLUMN `$col` $def";
            if ($conn->query($sql)) {
                echo "Added column: $col\n";
            } else {
                echo "Error adding $col: " . $conn->error . "\n";
            }
        } else {
            echo "Column $col already exists.\n";
        }
    }

    $conn->close();
    echo "Migration completed successfully.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
