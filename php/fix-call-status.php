<?php
/**
 * TeleRx - Call Status Column Fix
 * Run from browser: https://yourdomain.com/php/fix-call-status.php
 *
 * This script:
 *  1. Checks if call_status column exists in appointments table
 *  2. Adds it if missing
 *  3. Verifies the column definition
 *  4. Tests the UPDATE query directly
 *  5. Shows the current state of appointment #57 (or any ID you pass via ?id=XX)
 */

require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');
echo '<pre style="font-family:monospace;font-size:14px;padding:20px;">';
echo "=== TeleRx Call Status Column Fix ===\n\n";

$test_appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : 57;

try {
    $conn = getDBConnection();

    // --- Step 1: Check both columns ---
    echo "--- Step 1: Checking columns ---\n";

    $r1 = $conn->query("SHOW COLUMNS FROM `appointments` LIKE 'call_status'");
    $hasCallStatus = ($r1 && $r1->num_rows > 0);

    $r2 = $conn->query("SHOW COLUMNS FROM `appointments` LIKE 'call_started_at'");
    $hasCallStartedAt = ($r2 && $r2->num_rows > 0);

    echo "call_status column exists:    " . ($hasCallStatus    ? "YES" : "NO - MISSING!") . "\n";
    echo "call_started_at column exists: " . ($hasCallStartedAt ? "YES" : "NO - MISSING!") . "\n\n";

    // --- Step 2: Add missing columns ---
    echo "--- Step 2: Adding missing columns ---\n";

    if (!$hasCallStatus && !$hasCallStartedAt) {
        $sql = "ALTER TABLE `appointments`
                ADD COLUMN `call_status` VARCHAR(20) DEFAULT NULL,
                ADD COLUMN `call_started_at` TIMESTAMP NULL DEFAULT NULL";
        if ($conn->query($sql)) {
            echo "SUCCESS: Added both call_status and call_started_at columns.\n";
        } else {
            echo "ERROR: " . $conn->error . "\n";
        }
    } elseif (!$hasCallStatus) {
        $sql = "ALTER TABLE `appointments` ADD COLUMN `call_status` VARCHAR(20) DEFAULT NULL";
        if ($conn->query($sql)) {
            echo "SUCCESS: Added missing call_status column.\n";
        } else {
            echo "ERROR adding call_status: " . $conn->error . "\n";
        }
    } elseif (!$hasCallStartedAt) {
        $sql = "ALTER TABLE `appointments` ADD COLUMN `call_started_at` TIMESTAMP NULL DEFAULT NULL";
        if ($conn->query($sql)) {
            echo "SUCCESS: Added missing call_started_at column.\n";
        } else {
            echo "ERROR adding call_started_at: " . $conn->error . "\n";
        }
    } else {
        echo "Both columns already exist. No changes needed.\n";
    }

    // --- Step 3: Show column definitions ---
    echo "\n--- Step 3: Column definitions ---\n";
    $cols = $conn->query("SHOW COLUMNS FROM `appointments` WHERE Field IN ('call_status', 'call_started_at')");
    if ($cols) {
        while ($row = $cols->fetch_assoc()) {
            echo "Field: {$row['Field']}  |  Type: {$row['Type']}  |  Null: {$row['Null']}  |  Default: {$row['Default']}\n";
        }
    }

    // --- Step 4: Direct UPDATE test ---
    echo "\n--- Step 4: Direct UPDATE test on appointment ID = $test_appointment_id ---\n";
    $stmt = $conn->prepare("UPDATE appointments SET call_status = 'calling', call_started_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $test_appointment_id);
    $stmt->execute();
    echo "Rows affected: " . $stmt->affected_rows . "\n";
    if ($stmt->affected_rows > 0) {
        echo "SUCCESS: call_status set to 'calling' for appointment $test_appointment_id\n";
    } elseif ($stmt->affected_rows === 0) {
        echo "WARNING: No rows updated. Either the appointment doesn't exist or the value was already 'calling'.\n";
    } else {
        echo "ERROR: " . $stmt->error . "\n";
    }
    $stmt->close();

    // --- Step 5: Verify the row ---
    echo "\n--- Step 5: Current state of appointment $test_appointment_id ---\n";
    $r = $conn->query("SELECT id, patient_id, doctor_id, status, call_status, call_started_at,
                               TIMESTAMPDIFF(SECOND, call_started_at, NOW()) as seconds_ago
                        FROM appointments WHERE id = $test_appointment_id");
    if ($r && $r->num_rows > 0) {
        $row = $r->fetch_assoc();
        foreach ($row as $k => $v) {
            echo "  $k: $v\n";
        }
    } else {
        echo "  Appointment not found.\n";
    }

    // --- Step 6: Check if check-incoming-calls.php would now find it ---
    echo "\n--- Step 6: Simulating check-incoming-calls.php query ---\n";
    $pid = 4; // patient_id from session
    $r2 = $conn->query("
        SELECT a.id, a.patient_id, a.call_status, a.call_started_at,
               d.name as doctor_name,
               TIMESTAMPDIFF(SECOND, a.call_started_at, NOW()) as seconds_ago
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.patient_id = $pid
          AND a.call_status = 'calling'
          AND a.call_started_at >= NOW() - INTERVAL 5 MINUTE
        ORDER BY a.call_started_at DESC
        LIMIT 1
    ");
    if ($r2 && $r2->num_rows > 0) {
        $found = $r2->fetch_assoc();
        echo "  MATCH FOUND! Appointment {$found['id']} for patient {$found['id']} - Doctor: {$found['doctor_name']}\n";
        echo "  call_status: {$found['call_status']}\n";
        echo "  call_started_at: {$found['call_started_at']} ({$found['seconds_ago']} seconds ago)\n";
        echo "\n  => The patient notification will NOW work correctly!\n";
    } else {
        echo "  NO MATCH found. Something else is still wrong.\n";
        echo "  Check: Does patient_id=$pid have an appointment with call_status='calling'?\n";
    }

    $conn->close();

} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}

echo "\n</pre>";
?>
