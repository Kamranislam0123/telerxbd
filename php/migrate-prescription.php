<?php
/**
 * Migration: Add prescription_path column to appointments table
 */
require_once __DIR__ . '/config.php';

try {
    $conn = getDBConnection();
    
    // Check if column exists
    $result = $conn->query("SHOW COLUMNS FROM appointments LIKE 'prescription_path'");
    if ($result->num_rows == 0) {
        $sql = "ALTER TABLE appointments ADD COLUMN prescription_path VARCHAR(255) DEFAULT NULL AFTER attachment_path";
        if ($conn->query($sql)) {
            echo "Successfully added prescription_path column to appointments table.\n";
        } else {
            echo "Error adding column: " . $conn->error . "\n";
        }
    } else {
        echo "Column prescription_path already exists in appointments table.\n";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
