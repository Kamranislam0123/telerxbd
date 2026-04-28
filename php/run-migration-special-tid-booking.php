<?php
/**
 * Migration for Special TID auth + booking ownership.
 * - Creates special_tid_users table (separate credentials from healthcare_providers)
 * - Adds appointments.created_by_special_tid_id
 */

require_once __DIR__ . '/config.php';

try {
    $conn = getDBConnection();

    $table_check = $conn->query("SHOW TABLES LIKE 'special_tid_users'");
    if (!$table_check || $table_check->num_rows === 0) {
        $create_sql = "
            CREATE TABLE special_tid_users (
                id INT NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                mobile VARCHAR(20) DEFAULT NULL,
                tid VARCHAR(50) NOT NULL,
                password VARCHAR(255) NOT NULL,
                healthcare_provider_id INT DEFAULT NULL,
                status TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uk_special_tid_email (email),
                UNIQUE KEY uk_special_tid_mobile (mobile),
                UNIQUE KEY uk_special_tid_tid (tid),
                KEY idx_special_tid_hcp (healthcare_provider_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
        ";
        if ($conn->query($create_sql)) {
            echo "Created table: special_tid_users\n";
        } else {
            throw new Exception("Failed to create special_tid_users: " . $conn->error);
        }
    } else {
        echo "Table already exists: special_tid_users\n";
    }

    // Backfill: create Special TID users from healthcare_providers if missing
    $backfill_sql = "
        INSERT INTO special_tid_users (name, email, mobile, tid, password, healthcare_provider_id)
        SELECT h.name, h.email, NULLIF(TRIM(h.phone), ''), h.tid, h.password, h.id
        FROM healthcare_providers h
        LEFT JOIN special_tid_users s ON s.email = h.email
        WHERE s.id IS NULL AND h.email IS NOT NULL AND h.tid IS NOT NULL
    ";
    if ($conn->query($backfill_sql)) {
        echo "Backfill completed: healthcare_providers -> special_tid_users\n";
    } else {
        echo "Backfill skipped/error: " . $conn->error . "\n";
    }

    $check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'created_by_special_tid_id'");
    if (!$check || $check->num_rows === 0) {
        $sql = "ALTER TABLE appointments ADD COLUMN created_by_special_tid_id INT NULL DEFAULT NULL COMMENT 'Special TID account that created this booking' AFTER referrer_tid";
        if ($conn->query($sql)) {
            echo "Added column: appointments.created_by_special_tid_id\n";
        } else {
            throw new Exception("Failed to add created_by_special_tid_id: " . $conn->error);
        }
    } else {
        echo "Column already exists: appointments.created_by_special_tid_id\n";
    }

    $idx_check = $conn->query("SHOW INDEX FROM appointments WHERE Key_name = 'idx_created_by_special_tid'");
    if (!$idx_check || $idx_check->num_rows === 0) {
        $idx_sql = "ALTER TABLE appointments ADD INDEX idx_created_by_special_tid (created_by_special_tid_id)";
        if ($conn->query($idx_sql)) {
            echo "Added index: idx_created_by_special_tid\n";
        } else {
            throw new Exception("Failed to add index idx_created_by_special_tid: " . $conn->error);
        }
    } else {
        echo "Index already exists: idx_created_by_special_tid\n";
    }

    $conn->close();
    echo "Migration completed successfully.\n";
} catch (Exception $e) {
    http_response_code(500);
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
