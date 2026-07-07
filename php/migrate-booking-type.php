<?php
require_once __DIR__ . '/config.php';

try {
    $conn = getDBConnection();
    $result = $conn->query("SHOW COLUMNS FROM appointments LIKE 'booking_type'");
    if ($result->num_rows === 0) {
        $sql = "ALTER TABLE appointments ADD COLUMN booking_type VARCHAR(32) DEFAULT 'regular' COMMENT 'regular, follow_up_with_report, follow_up_without_report' AFTER status";
        if ($conn->query($sql) === TRUE) {
            echo "Successfully added booking_type column to appointments table.\n";
        } else {
            echo "Error adding column: " . $conn->error . "\n";
        }
    } else {
        echo "Column booking_type already exists in appointments table.\n";
    }
    $conn->close();
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
