<?php
/**
 * Save ONE tab of the Patient Medical History form.
 * Called by the browser (AJAX) when the patient clicks "Save and Next" / "Save and Finish".
 * Always answers with JSON.
 */

ini_set('display_errors', '0');
ob_start();

require_once __DIR__ . '/mh-helpers.php';
mh_db_strict(true);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        mh_json(array('success' => false, 'message' => 'Invalid request.'), 405);
    }

    // 1) Must be a logged-in patient
    $patient_id = mh_current_patient_id();
    if ($patient_id <= 0) {
        mh_json(array('success' => false, 'message' => 'Your session has expired. Please log in again.'), 401);
    }

    // 2) Security token
    if (!mh_csrf_ok(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        mh_json(array('success' => false, 'message' => 'Security check failed. Please reload the page and try again.'), 403);
    }

    // 3) Which tab?
    $sections   = mh_sections();
    $section_no = isset($_POST['section']) ? (int)$_POST['section'] : 0;
    if (!isset($sections[$section_no])) {
        mh_json(array('success' => false, 'message' => 'Unknown tab.'), 400);
    }
    $last_no = max(array_keys($sections));
    $form_id = isset($_POST['form_id']) ? (int)$_POST['form_id'] : 0;

    $conn = getDBConnection();

    $sub = mh_get_premium_subscription($conn, $patient_id);
    if (MH_REQUIRE_PREMIUM && !$sub) {
        mh_json(array('success' => false, 'message' => 'The medical history form is available for Premium subscribers.'), 403);
    }
    if (!mh_tables_ready($conn)) {
        mh_json(array('success' => false, 'message' => mh_setup_message()), 500);
    }

    // 4) Existing form? (must belong to this patient)
    $form  = null;
    $saved = array();
    if ($form_id > 0) {
        $form = mh_get_form($conn, $form_id, $patient_id);
        if (!$form) {
            mh_json(array('success' => false, 'message' => 'This form was not found.'), 404);
        }
        $saved = array_keys(mh_get_sections($conn, $form_id));
    } elseif ($section_no !== 1) {
        mh_json(array('success' => false, 'message' => 'Please fill and save the first tab first.'), 409);
    } else {
        // New form: it must be for the member himself/herself or a registered family member
        $person_key = isset($_POST['person']) && is_string($_POST['person']) ? $_POST['person'] : 'self';
        $person = mh_find_person(mh_get_people($conn, $patient_id, $sub), $person_key);
        if (!$person) {
            mh_json(array('success' => false, 'message' => 'This person is not registered in your package. Add the family member on the My Subscription page first.'), 403);
        }
        if (mh_get_form_by_person($conn, $patient_id, $person['key'])) {
            mh_json(array('success' => false, 'message' => 'A form already exists for this person. Please open it from the Medical History page.'), 409);
        }
    }

    // 5) Tabs must be completed in order
    for ($i = 1; $i < $section_no; $i++) {
        if (!in_array($i, $saved, true)) {
            mh_json(array('success' => false, 'message' => 'Please complete and save Tab ' . $i . ' first.'), 409);
        }
    }

    // 6) Check + clean the answers (the tab's own file decides the rules)
    $run    = mh_run_section($section_no, 'save', array(), $_POST);
    $errors = $run['result']['errors'];
    $data   = $run['result']['data'];
    if (!empty($errors)) {
        mh_json(array('success' => false, 'message' => 'Please fix the highlighted fields.', 'errors' => $errors), 422);
    }

    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new Exception('Could not prepare the data for saving.');
    }

    // 7) Save
    $status = $form ? $form['status'] : 'draft';
    $conn->begin_transaction();
    try {
        if (!$form) {
            $name = isset($data['patient_name']) ? $data['patient_name'] : '';
            $dob  = !empty($data['dob']) ? $data['dob'] : null;
            $pkey = $person['key'];
            $rel  = $person['relationship'];
            $stmt = $conn->prepare("INSERT INTO mh_forms (patient_id, person_key, relationship, patient_name, date_of_birth, status) VALUES (?, ?, ?, ?, ?, 'draft')");
            $stmt->bind_param('issss', $patient_id, $pkey, $rel, $name, $dob);
            $stmt->execute();
            $form_id = (int)$conn->insert_id;
            $stmt->close();
        } elseif ($section_no === 1) {
            $name = isset($data['patient_name']) ? $data['patient_name'] : '';
            $dob  = !empty($data['dob']) ? $data['dob'] : null;
            $stmt = $conn->prepare('UPDATE mh_forms SET patient_name = ?, date_of_birth = ? WHERE id = ? AND patient_id = ?');
            $stmt->bind_param('ssii', $name, $dob, $form_id, $patient_id);
            $stmt->execute();
            $stmt->close();
        }

        $stmt = $conn->prepare('INSERT INTO mh_form_sections (form_id, section_no, data) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE data = VALUES(data), saved_at = NOW()');
        $stmt->bind_param('iis', $form_id, $section_no, $json);
        $stmt->execute();
        $stmt->close();

        $saved_now = array_keys(mh_get_sections($conn, $form_id));

        // Last tab saved and every tab present -> form is complete
        if ($section_no === $last_no && count($saved_now) >= count($sections)) {
            $stmt = $conn->prepare("UPDATE mh_forms SET status = 'completed', completed_at = IFNULL(completed_at, NOW()) WHERE id = ? AND patient_id = ?");
            $stmt->bind_param('ii', $form_id, $patient_id);
            $stmt->execute();
            $stmt->close();
            $status = 'completed';
        }

        $conn->commit();
    } catch (Throwable $inner) {
        $conn->rollback();
        throw $inner;
    }

    $conn->close();

    mh_json(array(
        'success'   => true,
        'message'   => 'Saved.',
        'form_id'   => $form_id,
        'saved'     => array_values($saved_now),
        'status'    => $status,
        'completed' => ($status === 'completed'),
        'next'      => ($section_no < $last_no) ? ($section_no + 1) : null,
    ));
} catch (Throwable $e) {
    error_log('[mh-save-section] ' . $e->getMessage());
    mh_json(array(
        'success' => false,
        'message' => MH_DEBUG ? ('Server error: ' . $e->getMessage()) : 'Something went wrong. Please try again.',
    ), 500);
}
