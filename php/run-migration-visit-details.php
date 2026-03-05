<?php
/**
 * Add visit detail columns to appointments table.
 * Run from browser: .../php/run-migration-visit-details.php or CLI: php php/run-migration-visit-details.php
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

$columns = [
    'patient_name' => 'VARCHAR(100) NULL',
    'mobile' => 'VARCHAR(20) NULL',
    'age' => 'VARCHAR(10) NULL',
    'weight' => 'VARCHAR(20) NULL',
    'body_temperature' => 'VARCHAR(20) NULL',
    'blood_pressure' => 'VARCHAR(20) NULL',
    'pulse' => 'VARCHAR(20) NULL',
    'spo2' => 'VARCHAR(20) NULL',
    'rbs_fbs' => 'VARCHAR(20) NULL',
    'attachment_path' => 'VARCHAR(255) NULL',
];

try {
    $conn = getDBConnection();
    $tbl = $conn->query("SHOW TABLES LIKE 'appointments'");
    if (!$tbl || $tbl->num_rows === 0) {
        echo "appointments table not found; skipping.\n";
        exit(0);
    }
    // Ensure notes column exists (book-appointment.php uses it for symptoms)
    $check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'notes'");
    if ($check && $check->num_rows === 0) {
        if ($conn->query("ALTER TABLE appointments ADD COLUMN notes TEXT NULL")) {
            echo "Added column: notes\n";
        } else {
            echo "Note: could not add notes - " . $conn->error . "\n";
        }
    }
    // Ensure status column exists (book-appointment.php uses it)
    $check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'status'");
    if ($check && $check->num_rows === 0) {
        if ($conn->query("ALTER TABLE appointments ADD COLUMN status VARCHAR(20) DEFAULT 'confirmed'")) {
            echo "Added column: status\n";
        } else {
            echo "Note: could not add status - " . $conn->error . "\n";
        }
    }
    $after = null;
    foreach ($columns as $col => $def) {
        $sql = $after
            ? "ALTER TABLE appointments ADD COLUMN `$col` $def AFTER `$after`"
            : "ALTER TABLE appointments ADD COLUMN `$col` $def";
        try {
            if ($conn->query($sql)) {
                echo "Added column: $col\n";
                $after = $col;
            } else {
                $err = $conn->error;
                $dup = ($conn->errno === 1060 || stripos($err, 'duplicate column') !== false);
                if ($dup) {
                    echo "Column $col already exists, skip.\n";
                    $after = $col;
                } else {
                    echo "Error adding $col: $err\n";
                }
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            if ($conn->errno === 1060 || stripos($msg, 'duplicate column') !== false) {
                echo "Column $col already exists, skip.\n";
                $after = $col;
            } else {
                echo "Error adding $col: $msg\n";
            }
        }
    }
    $conn->close();
    echo "Done.\n";
    exit(0);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
