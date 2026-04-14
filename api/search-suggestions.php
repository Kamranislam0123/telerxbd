<?php
/**
 * Search Suggestions API
 * Returns doctor name, specialty, and location suggestions for autocomplete.
 */
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once '../php/config.php';

$type = isset($_GET['type']) ? trim($_GET['type']) : 'search';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Sanitize: only allow safe characters to prevent injection via LIKE
// bind_param handles parameterization, but we still limit input length
$q = mb_substr($q, 0, 100);
$like = '%' . $q . '%';

$suggestions = [];

try {
    $conn = getDBConnection();

    if ($type === 'search') {
        // Suggest doctor names and specialties
        $stmt = $conn->prepare("
            SELECT DISTINCT d.name AS label, 'doctor' AS type
            FROM doctors d
            WHERE d.name LIKE ?
            LIMIT 5
        ");
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $row) {
            $suggestions[] = ['label' => $row['label'], 'type' => 'doctor'];
        }
        $stmt->close();

        // Specialties
        $stmt2 = $conn->prepare("
            SELECT DISTINCT dp.specialty AS label
            FROM doctor_profiles dp
            WHERE dp.specialty LIKE ? AND dp.specialty IS NOT NULL AND dp.specialty != ''
            LIMIT 5
        ");
        $stmt2->bind_param('s', $like);
        $stmt2->execute();
        $rows2 = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        foreach ($rows2 as $row) {
            // specialty can be comma-separated; split and match individually
            $specs = array_map('trim', explode(',', $row['label']));
            foreach ($specs as $spec) {
                if ($spec !== '' && mb_stripos($spec, $q) !== false) {
                    $exists = false;
                    foreach ($suggestions as $s) {
                        if (mb_strtolower($s['label']) === mb_strtolower($spec)) { $exists = true; break; }
                    }
                    if (!$exists) {
                        $suggestions[] = ['label' => $spec, 'type' => 'specialty'];
                    }
                }
            }
        }
        $stmt2->close();

    } elseif ($type === 'location') {
        // Suggest distinct districts and cities
        $stmt = $conn->prepare("
            SELECT DISTINCT dp.district AS label, 'district' AS type
            FROM doctor_profiles dp
            WHERE dp.district LIKE ? AND dp.district IS NOT NULL AND dp.district != ''
            LIMIT 5
        ");
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $row) {
            $suggestions[] = ['label' => $row['label'], 'type' => 'district'];
        }
        $stmt->close();

        $stmt2 = $conn->prepare("
            SELECT DISTINCT dp.city AS label, 'city' AS type
            FROM doctor_profiles dp
            WHERE dp.city LIKE ? AND dp.city IS NOT NULL AND dp.city != ''
            LIMIT 5
        ");
        $stmt2->bind_param('s', $like);
        $stmt2->execute();
        $rows2 = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        foreach ($rows2 as $row) {
            $exists = false;
            foreach ($suggestions as $s) {
                if (mb_strtolower($s['label']) === mb_strtolower($row['label'])) { $exists = true; break; }
            }
            if (!$exists) {
                $suggestions[] = ['label' => $row['label'], 'type' => 'city'];
            }
        }
        $stmt2->close();
    }

    $conn->close();
} catch (Exception $e) {
    error_log('search-suggestions error: ' . $e->getMessage());
    echo json_encode([]);
    exit;
}

// Limit total results
$suggestions = array_slice($suggestions, 0, 8);

echo json_encode($suggestions);
