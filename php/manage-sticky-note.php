<?php
/**
 * Manage Sticky Note
 * AJAX handler for getting, saving/updating, and deleting doctor sticky notes.
 */

ini_set('display_errors', 0);
ob_start();
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$doctor_id = (int)$_SESSION['doctor_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? 'get';
    $appointment_id = (int)($_POST['appointment_id'] ?? 0);
    $sticky_note = isset($_POST['sticky_note']) ? trim($_POST['sticky_note']) : null;

    if ($appointment_id <= 0) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid Appointment ID']);
        exit;
    }

    try {
        $conn = getDBConnection();

        // Verify appointment ownership
        $stmt = $conn->prepare("SELECT id, sticky_note FROM appointments WHERE id = ? AND doctor_id = ?");
        $stmt->bind_param("ii", $appointment_id, $doctor_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Access denied or appointment not found']);
            $stmt->close();
            $conn->close();
            exit;
        }

        $row = $res->fetch_assoc();
        $stmt->close();

        // Ensure sticky_note column exists
        $col_check = $conn->query("SHOW COLUMNS FROM appointments LIKE 'sticky_note'");
        if ($col_check->num_rows === 0) {
            $conn->query("ALTER TABLE appointments ADD COLUMN sticky_note TEXT NULL");
        }

        if ($action === 'get') {
            ob_clean();
            echo json_encode([
                'success' => true,
                'appointment_id' => $appointment_id,
                'sticky_note' => $row['sticky_note'] ?? ''
            ]);
            $conn->close();
            exit;
        } elseif ($action === 'save') {
            $upd_stmt = $conn->prepare("UPDATE appointments SET sticky_note = ? WHERE id = ? AND doctor_id = ?");
            $upd_stmt->bind_param("sii", $sticky_note, $appointment_id, $doctor_id);
            if ($upd_stmt->execute()) {
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Sticky note saved successfully',
                    'appointment_id' => $appointment_id,
                    'sticky_note' => $sticky_note
                ]);
            } else {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $conn->error]);
            }
            $upd_stmt->close();
            $conn->close();
            exit;
        } elseif ($action === 'delete') {
            $upd_stmt = $conn->prepare("UPDATE appointments SET sticky_note = NULL WHERE id = ? AND doctor_id = ?");
            $upd_stmt->bind_param("ii", $appointment_id, $doctor_id);
            if ($upd_stmt->execute()) {
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Sticky note deleted successfully',
                    'appointment_id' => $appointment_id,
                    'sticky_note' => ''
                ]);
            } else {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $conn->error]);
            }
            $upd_stmt->close();
            $conn->close();
            exit;
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            $conn->close();
            exit;
        }
    } catch (Exception $e) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
} else {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
