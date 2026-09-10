<?php
/**
 * Run Migration for Subscriptions
 * Creates subscription_plans, patient_subscriptions, subscription_family_members tables
 * and seeds standard plans.
 */

require_once __DIR__ . '/config.php';

echo "<h2>TeleRx Bangladesh - Subscription Database Migration</h2>";
echo "<pre>";

try {
    $conn = getDBConnection();
    echo "✅ Connected to database: " . DB_NAME . "\n";

    $sqlFile = __DIR__ . '/../subscription_schema_live.sql';
    if (!file_exists($sqlFile)) {
        die("❌ SQL schema file not found at $sqlFile\n");
    }

    $sqlContent = file_get_contents($sqlFile);
    
    // Split queries by semicolon (ignoring semicolons inside quotes or comments if any, simple split for standard DDL)
    // We can use multi_query
    if ($conn->multi_query($sqlContent)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        
        echo "✅ Subscription tables created and seeded successfully!\n";
    } else {
        echo "❌ multi_query error: " . $conn->error . "\n";
    }

    // Verify plans count
    $res = $conn->query("SELECT COUNT(*) as cnt FROM subscription_plans");
    if ($res) {
        $row = $res->fetch_assoc();
        echo "✅ Total subscription plans seeded: " . $row['cnt'] . "\n";
    }

    $conn->close();
    echo "\n🎉 Migration completed successfully!\n";
} catch (Exception $e) {
    echo "❌ Error during migration: " . $e->getMessage() . "\n";
}

echo "</pre>";
