<?php
/**
 * TeleRx Bangladesh - Patient Medical History Form
 * Shared helpers (used by the form page, the save endpoint and the PDF generator).
 *
 * Works with PHP 7.2 and newer.
 */

require_once __DIR__ . '/config.php';

/* ---------------------------------------------------------------------
 * SETTINGS  (the only two things you may want to change)
 * ------------------------------------------------------------------- */

// true  = only Premium subscribers can use the form (same rule as the existing Medical History page)
// false = every logged-in patient can use it
if (!defined('MH_REQUIRE_PREMIUM')) {
    define('MH_REQUIRE_PREMIUM', true);
}

// Total number of medical history forms ONE Premium member can have,
// counting the member himself/herself.  4 = the member + 3 family members.
if (!defined('MH_MAX_PEOPLE')) {
    define('MH_MAX_PEOPLE', 4);
}

// true  = show the real error text on screen (good while testing)
// false = show only a general message (set this when everything works)
if (!defined('MH_DEBUG')) {
    define('MH_DEBUG', true);
}

/**
 * Makes database problems throw errors that our code can catch (same on every PHP version).
 * mh_db_strict(true)  -> before our own database work
 * mh_db_strict(false) -> afterwards, puts PHP back to its normal behaviour so the
 *                        rest of the website (header/footer) is not affected
 */
function mh_db_strict($on)
{
    if ($on || PHP_VERSION_ID >= 80100) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);   // PHP 8.1+ does this by default
    } else {
        mysqli_report(MYSQLI_REPORT_OFF);
    }
}

/* ---------------------------------------------------------------------
 * TABS (SECTIONS)
 * To add a real Tab 2 later: create php/mh-sections/section-2.php and
 * change 'file' for number 2 below. Nothing else needs to change.
 * ------------------------------------------------------------------- */
function mh_sections()
{
    return array(
        1 => array('title' => 'Patient Information', 'file' => 'section-1.php'),
        2 => array('title' => 'Complaint & Risk Factors', 'file' => 'section-2.php'),
        3 => array('title' => 'Illness History', 'file' => 'section-3.php'),
        4 => array('title' => 'Medication & Family', 'file' => 'section-4.php'),
        5 => array('title' => 'Tab 5', 'file' => 'section-placeholder.php'),
        6 => array('title' => 'Tab 6', 'file' => 'section-placeholder.php'),
    );
}

/**
 * Loads one section file in a given mode.
 *   'form' -> returns the HTML fields for the tab
 *   'pdf'  -> returns the HTML for that tab's PDF page
 *   'save' -> validates $input, returns cleaned data + errors in ['result']
 */
function mh_run_section($no, $mode, $data = array(), $input = array())
{
    $sections = mh_sections();
    if (!isset($sections[$no])) {
        throw new Exception('Unknown tab number: ' . (int)$no);
    }
    $mh_no     = (int)$no;
    $mh_mode   = $mode;
    $mh_data   = is_array($data) ? $data : array();
    $mh_input  = is_array($input) ? $input : array();
    $mh_title  = $sections[$no]['title'];
    $mh_result = array('data' => array(), 'errors' => array());

    ob_start();
    include __DIR__ . '/mh-sections/' . $sections[$no]['file'];
    $html = ob_get_clean();

    return array('html' => $html, 'result' => $mh_result);
}

/* ---------------------------------------------------------------------
 * SMALL UTILITIES
 * ------------------------------------------------------------------- */
function mh_h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function mh_json($payload, $status = 200)
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Trim, collapse spaces, remove control characters, cut to $max characters. */
function mh_clean_text($value, $max = 255)
{
    if (is_array($value) || is_object($value)) {
        return '';
    }
    $v = (string)$value;
    $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v);
    if ($clean === null) {          // not valid UTF-8
        $clean = '';
    }
    $clean = preg_replace('/\s+/u', ' ', $clean);
    if ($clean === null) {
        $clean = '';
    }
    $clean = trim($clean);
    if (function_exists('mb_substr')) {
        $clean = mb_substr($clean, 0, $max, 'UTF-8');
    } else {
        $clean = substr($clean, 0, $max);
    }
    return $clean;
}

/** Like mh_clean_text() but keeps line breaks (for big text boxes). */
function mh_clean_multiline($value, $max = 2000)
{
    if (is_array($value) || is_object($value)) {
        return '';
    }
    $v = str_replace(array("\r\n", "\r"), "\n", (string)$value);
    $lines = array();
    foreach (explode("\n", $v) as $line) {
        $line = mh_clean_text($line, $max);
        if ($line !== '') {
            $lines[] = $line;
        }
    }
    $clean = implode("\n", $lines);
    if (function_exists('mb_substr')) {
        $clean = mb_substr($clean, 0, $max, 'UTF-8');
    } else {
        $clean = substr($clean, 0, $max);
    }
    return $clean;
}

/** Keeps only the values of $raw that are in $allowed (in the order of $allowed). */
function mh_pick_many($raw, $allowed)
{
    if (!is_array($raw)) {
        $raw = ($raw === null || $raw === '') ? array() : array($raw);
    }
    $out = array();
    foreach ($allowed as $a) {
        if (in_array($a, $raw, true)) {
            $out[] = $a;
        }
    }
    return $out;
}

/**
 * Cleans the rows of a "+ add row" table.
 * $spec = array( key => array('text', maxLength) | array('pick', allowedList) | array('many', allowedList) | array('bool') )
 * Rows where every value is empty are dropped.
 */
function mh_clean_rows($raw, $spec, $max_rows = 30)
{
    $rows = array();
    if (!is_array($raw)) {
        return $rows;
    }
    foreach ($raw as $item) {
        if (!is_array($item)) {
            continue;
        }
        $row = array();
        $filled = false;
        foreach ($spec as $key => $rule) {
            $v = isset($item[$key]) ? $item[$key] : null;
            if ($rule[0] === 'text') {
                $row[$key] = mh_clean_text($v, $rule[1]);
                $filled = $filled || $row[$key] !== '';
            } elseif ($rule[0] === 'pick') {
                $row[$key] = mh_pick($v, $rule[1], '');
                $filled = $filled || $row[$key] !== '';
            } elseif ($rule[0] === 'many') {
                $row[$key] = mh_pick_many($v, $rule[1]);
                $filled = $filled || count($row[$key]) > 0;
            } else {   // bool
                $row[$key] = ($v === '1' || $v === 1 || $v === true) ? 1 : 0;
                $filled = $filled || $row[$key] === 1;
            }
        }
        if ($filled) {
            $rows[] = $row;
        }
        if (count($rows) >= $max_rows) {
            break;
        }
    }
    return $rows;
}

/** Returns $value only if it is one of $allowed, otherwise $default. */
function mh_pick($value, $allowed, $default = '')
{
    if (is_string($value) && in_array($value, $allowed, true)) {
        return $value;
    }
    return $default;
}

/** Reads a decimal number such as "62", "62.5" or "62,5". Returns null if not a number. */
function mh_number($value)
{
    if (is_array($value) || $value === null) {
        return null;
    }
    $v = str_replace(',', '.', trim((string)$value));
    if ($v === '' || !is_numeric($v)) {
        return null;
    }
    return (float)$v;
}

/* ---------------------------------------------------------------------
 * LOGIN, ACCESS, CSRF
 * ------------------------------------------------------------------- */
function mh_current_patient_id()
{
    if (isset($_SESSION['patient_id'], $_SESSION['logged_in'], $_SESSION['user_type'])
        && $_SESSION['logged_in'] === true
        && $_SESSION['user_type'] === 'patient') {
        return (int)$_SESSION['patient_id'];
    }
    return 0;
}

/**
 * The patient's OWN active Premium subscription (or null).
 * Strict on purpose: only a patient who bought a Premium package gets it.
 * (It does not use getActiveSubscription(), which can return another patient's package.)
 */
function mh_get_premium_subscription($conn, $patient_id)
{
    $sql = "SELECT ps.id, ps.end_date, sp.plan_code, sp.name AS plan_name, sp.family_member_quota
            FROM patient_subscriptions ps
            JOIN subscription_plans sp ON sp.id = ps.plan_id
            WHERE ps.patient_id = ?
              AND ps.status = 'active'
              AND ps.end_date >= NOW()
              AND (LOWER(sp.plan_code) LIKE '%premium%' OR LOWER(sp.name) LIKE '%premium%')
            ORDER BY ps.id DESC
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $patient_id = (int)$patient_id;
    $stmt->bind_param('i', $patient_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? $row : null;
}

/** May this patient use the form? (Premium buyers only, unless switched off at the top of this file) */
function mh_has_access($conn, $patient_id)
{
    if (!MH_REQUIRE_PREMIUM) {
        return true;
    }
    return mh_get_premium_subscription($conn, $patient_id) !== null;
}

/**
 * How many FAMILY members (not counting the member) can have a form:
 * the package allows family_member_quota, but the total is capped by MH_MAX_PEOPLE.
 */
function mh_family_limit($sub)
{
    if (!$sub) {
        return 0;
    }
    return max(0, min((int)$sub['family_member_quota'], MH_MAX_PEOPLE - 1));
}

/** "Male" / "female" / "OTHER" -> one of the form's Sex options, or '' when unknown */
function mh_map_sex($value)
{
    $v = ucfirst(strtolower(trim((string)$value)));
    return in_array($v, array('Male', 'Female', 'Intersex', 'Other'), true) ? $v : '';
}

/**
 * The people this Premium member can fill a form for:
 * the member himself/herself + the family members registered on the My Subscription page
 * (up to the package's family_member_quota, 4 for Premium).
 *
 * @return array list of array('key','family_id','name','relationship','sex','dob')
 */
function mh_get_people($conn, $patient_id, $sub)
{
    $patient_id = (int)$patient_id;
    $people = array();

    $stmt = $conn->prepare('SELECT name, gender, date_of_birth FROM patients WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $patient_id);
    $stmt->execute();
    $me = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $dob = '';
    if ($me && !empty($me['date_of_birth']) && $me['date_of_birth'] !== '0000-00-00') {
        $dob = substr((string)$me['date_of_birth'], 0, 10);
    }
    $people[] = array(
        'key'          => 'self',
        'family_id'    => 0,
        'name'         => ($me && trim((string)$me['name']) !== '') ? trim((string)$me['name']) : 'Myself',
        'relationship' => 'Self',
        'sex'          => $me ? mh_map_sex($me['gender']) : '',
        'dob'          => $dob,
    );

    if ($sub) {
        $quota = mh_family_limit($sub);
        if ($quota > 0) {
            $sub_id = (int)$sub['id'];
            $stmt = $conn->prepare('SELECT id, member_name, relationship, gender FROM subscription_family_members
                                    WHERE subscription_id = ? AND patient_id = ? ORDER BY id ASC LIMIT ?');
            $stmt->bind_param('iii', $sub_id, $patient_id, $quota);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $people[] = array(
                    'key'          => 'fm-' . (int)$row['id'],
                    'family_id'    => (int)$row['id'],
                    'name'         => trim((string)$row['member_name']),
                    'relationship' => trim((string)$row['relationship']) !== '' ? trim((string)$row['relationship']) : 'Family',
                    'sex'          => mh_map_sex($row['gender']),
                    'dob'          => '',
                );
            }
            $stmt->close();
        }
    }
    return $people;
}

function mh_find_person($people, $key)
{
    foreach ($people as $p) {
        if ($p['key'] === $key) {
            return $p;
        }
    }
    return null;
}

/** "Myself" or "Rahim (Spouse)" */
function mh_person_label($name, $relationship)
{
    if ($relationship === 'Self' || $relationship === '' || $relationship === null) {
        return 'Myself';
    }
    return trim((string)$name) . ' (' . $relationship . ')';
}

function mh_csrf_token()
{
    if (empty($_SESSION['mh_csrf'])) {
        $_SESSION['mh_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['mh_csrf'];
}

function mh_csrf_ok($token)
{
    return is_string($token)
        && !empty($_SESSION['mh_csrf'])
        && hash_equals($_SESSION['mh_csrf'], $token);
}

/* ---------------------------------------------------------------------
 * DATABASE
 * ------------------------------------------------------------------- */
function mh_tables_ready($conn)
{
    $ok = true;
    foreach (array('mh_forms', 'mh_form_sections') as $table) {
        $res = $conn->query("SHOW TABLES LIKE '" . $table . "'");
        if (!$res || $res->num_rows === 0) {
            $ok = false;
        }
    }
    if ($ok) {
        // Version 2 column (whose form is it). Missing = mh_tables_update_v2.sql was not imported yet.
        $col = $conn->query("SHOW COLUMNS FROM mh_forms LIKE 'person_key'");
        if (!$col || $col->num_rows === 0) {
            $ok = false;
        }
    }
    return $ok;
}

/** Returns the form row if it belongs to this patient, otherwise null. */
function mh_get_form($conn, $form_id, $patient_id)
{
    $stmt = $conn->prepare('SELECT * FROM mh_forms WHERE id = ? AND patient_id = ? LIMIT 1');
    $form_id = (int)$form_id;
    $patient_id = (int)$patient_id;
    $stmt->bind_param('ii', $form_id, $patient_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? $row : null;
}

/** The form of one person (self or a family member) of this patient, or null if not started. */
function mh_get_form_by_person($conn, $patient_id, $person_key)
{
    $stmt = $conn->prepare('SELECT * FROM mh_forms WHERE patient_id = ? AND person_key = ? LIMIT 1');
    $patient_id = (int)$patient_id;
    $stmt->bind_param('is', $patient_id, $person_key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? $row : null;
}

/** All forms of a patient with the number of saved tabs. */
function mh_list_forms($conn, $patient_id)
{
    $sql = 'SELECT f.*, (SELECT COUNT(*) FROM mh_form_sections s WHERE s.form_id = f.id) AS saved_count
            FROM mh_forms f WHERE f.patient_id = ? ORDER BY f.updated_at DESC, f.id DESC';
    $stmt = $conn->prepare($sql);
    $patient_id = (int)$patient_id;
    $stmt->bind_param('i', $patient_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = array();
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    return $rows;
}

/** Saved answers of every tab: array( tabNumber => array(answers) ) */
function mh_get_sections($conn, $form_id)
{
    $stmt = $conn->prepare('SELECT section_no, data FROM mh_form_sections WHERE form_id = ? ORDER BY section_no ASC');
    $form_id = (int)$form_id;
    $stmt->bind_param('i', $form_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $out = array();
    while ($row = $res->fetch_assoc()) {
        $decoded = json_decode($row['data'], true);
        $out[(int)$row['section_no']] = is_array($decoded) ? $decoded : array();
    }
    $stmt->close();
    return $out;
}

/* ---------------------------------------------------------------------
 * AGE, AGE GROUP, BMI
 * The age is NEVER stored. It is calculated from the date of birth every
 * time it is shown, so it is always correct.
 * The same steps are repeated in assets/js/medical-history-form.js
 * ------------------------------------------------------------------- */
function mh_days_in_month($year, $month)
{
    if ($month == 2) {
        return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0) ? 29 : 28;
    }
    return in_array($month, array(4, 6, 9, 11)) ? 30 : 31;
}

/**
 * @param string      $dob   date of birth as YYYY-MM-DD
 * @param string|null $today YYYY-MM-DD (leave empty for today)
 * @return array|null  null when the date is not valid or is in the future
 */
function mh_age_info($dob, $today = null)
{
    if (!is_string($dob) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dob, $m)) {
        return null;
    }
    $by = (int)$m[1];
    $bm = (int)$m[2];
    $bd = (int)$m[3];
    if (!checkdate($bm, $bd, $by) || $by < 1900) {
        return null;
    }
    if ($today === null || $today === '') {
        $today = date('Y-m-d');
    }
    $t  = explode('-', $today);
    $ty = (int)$t[0];
    $tm = (int)$t[1];
    $td = (int)$t[2];

    $dob_obj   = new DateTime(sprintf('%04d-%02d-%02d', $by, $bm, $bd));
    $today_obj = new DateTime(sprintf('%04d-%02d-%02d', $ty, $tm, $td));
    if ($dob_obj > $today_obj) {
        return null;
    }
    $total_days = (int)$dob_obj->diff($today_obj)->days;

    $years  = $ty - $by;
    $months = $tm - $bm;
    $days   = $td - $bd;
    if ($days < 0) {
        $months--;
        $prev_month = $tm - 1;
        $prev_year  = $ty;
        if ($prev_month < 1) {
            $prev_month = 12;
            $prev_year--;
        }
        $days += mh_days_in_month($prev_year, $prev_month);
    }
    if ($months < 0) {
        $years--;
        $months += 12;
    }

    if ($years >= 1) {
        $label = $years . ($years === 1 ? ' Year' : ' Years');
    } elseif ($months >= 1) {
        $label = $months . ($months === 1 ? ' Month' : ' Months');
    } else {
        $label = $days . ($days === 1 ? ' Day' : ' Days');
    }

    if ($total_days <= 28) {
        $group = 'Neonatal';
    } elseif ($years < 1) {
        $group = 'Infant';
    } elseif ($years < 5) {
        $group = 'Preschool';
    } elseif ($years < 12) {
        $group = 'School-age';
    } elseif ($years < 18) {
        $group = 'Adolescent';
    } elseif ($years < 40) {
        $group = 'Young adult';
    } elseif ($years < 65) {
        $group = 'Middle-aged adult';
    } else {
        $group = 'Older adult';
    }

    return array(
        'years'      => $years,
        'months'     => $months,
        'days'       => $days,
        'total_days' => $total_days,
        'label'      => $label,
        'group'      => $group,
    );
}

/** Height typed as feet + inches -> total inches */
function mh_height_total_inches($ft, $in)
{
    return ((float)$ft * 12) + (float)$in;
}

/**
 * BMI = weight (kg) / height (m) squared.
 * Under 2 years  : BMI is not used.
 * 2 to 17 years  : value is shown, but it must be read on a BMI-for-age growth chart.
 * 18 years and up: WHO adult categories.
 *
 * @return array|null array('bmi' => float|null, 'category' => string, 'text' => string)
 */
function mh_bmi_info($kg, $ft, $in, $age_years)
{
    $kg = (float)$kg;
    $total_in = mh_height_total_inches($ft, $in);
    if ($kg <= 0 || $total_in <= 0) {
        return null;
    }
    $meters = $total_in * 0.0254;
    $bmi = round($kg / ($meters * $meters), 1);

    if ($age_years === null) {
        return array('bmi' => $bmi, 'category' => '', 'text' => number_format($bmi, 1));
    }
    if ($age_years < 2) {
        return array('bmi' => null, 'category' => 'Not applicable (under 2 years)', 'text' => 'Not applicable (under 2 years)');
    }
    if ($age_years < 18) {
        return array('bmi' => $bmi, 'category' => 'Use BMI-for-age growth chart', 'text' => number_format($bmi, 1) . ' (use growth chart)');
    }
    if ($bmi < 18.5) {
        $cat = 'Underweight';
    } elseif ($bmi < 25) {
        $cat = 'Normal';
    } elseif ($bmi < 30) {
        $cat = 'Overweight';
    } else {
        $cat = 'Obese';
    }
    return array('bmi' => $bmi, 'category' => $cat, 'text' => number_format($bmi, 1) . ' (' . $cat . ')');
}

/** "20 Feb 2000" from "2000-02-20" */
function mh_format_date($iso)
{
    $ts = strtotime((string)$iso);
    return $ts ? date('j M Y', $ts) : '';
}

/* ---------------------------------------------------------------------
 * PDF HELPERS (small building blocks reused by every tab's PDF page)
 * ------------------------------------------------------------------- */
function mh_pdf_group($title)
{
    return '<div class="grp">' . mh_h($title) . '</div>';
}

/** One label/value cell pair for a 4-column table row. */
function mh_pdf_pair($label, $value, $colspan = 1)
{
    $v = ($value === '' || $value === null) ? '&mdash;' : mh_h($value);
    $span = ($colspan > 1) ? ' colspan="' . (int)$colspan . '"' : '';
    return '<td class="k">' . mh_h($label) . '</td><td class="v"' . $span . '>' . $v . '</td>';
}

/** Text with line breaks, or a dash when empty. */
function mh_pdf_text($text)
{
    $text = trim((string)$text);
    return $text === '' ? '&mdash;' : nl2br(mh_h($text));
}

/** Label + long value rows (label 25%, value 75%). $rows = list of array(label, ALREADY ESCAPED html) */
function mh_pdf_wide($rows)
{
    $html = '<table class="kv">';
    foreach ($rows as $r) {
        $html .= '<tr><td class="k" width="25%">' . mh_h($r[0]) . '</td><td class="v" width="75%">' . $r[1] . '</td></tr>';
    }
    return $html . '</table>';
}

/** "A, B, C" from a list, or a dash when empty. */
function mh_pdf_list($items, $sep = ', ')
{
    if (!is_array($items) || count($items) === 0) {
        return '&mdash;';
    }
    return mh_h(implode($sep, $items));
}

/**
 * A table with a header row. $headers = array('Title' => 'width%', ...),
 * $rows = list of rows, each row a list of ALREADY ESCAPED cell HTML.
 */
function mh_pdf_table($headers, $rows, $empty_text = 'Not recorded.')
{
    $html = '<table class="grid"><thead><tr>';
    foreach ($headers as $title => $width) {
        $html .= '<th width="' . mh_h($width) . '">' . mh_h($title) . '</th>';
    }
    $html .= '</tr></thead><tbody>';
    if (count($rows) === 0) {
        $html .= '<tr><td colspan="' . count($headers) . '" class="none">' . mh_h($empty_text) . '</td></tr>';
    }
    foreach ($rows as $i => $cells) {
        $html .= '<tr' . ($i % 2 === 1 ? ' class="alt"' : '') . '>';
        foreach ($cells as $cell) {
            $html .= '<td>' . ($cell === '' ? '&mdash;' : $cell) . '</td>';
        }
        $html .= '</tr>';
    }
    return $html . '</tbody></table>';
}

/* ---------------------------------------------------------------------
 * FORM HELPERS (tick boxes and choice buttons used by tabs 2, 3, 4)
 * ------------------------------------------------------------------- */
/** One tick box / choice button: $type = 'checkbox' or 'radio' */
function mh_form_check($type, $name, $value, $label, $extra = '')
{
    return '<label class="mhf-check"><input type="' . $type . '" name="' . mh_h($name) . '" value="' . mh_h($value) . '"' . ($extra !== '' ? ' ' . $extra : '') . '>'
         . '<span>' . mh_h($label) . '</span></label>';
}

/** The "Doctor asks" box. Shown on the website only, never printed in the PDF. */
function mh_form_ask($questions)
{
    $html = '<div class="mhf-ask"><div class="mhf-ask-head"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i> Doctor asks'
          . '<span class="mhf-ask-note">Not printed in the PDF</span></div><ol class="mhf-ask-list">';
    foreach ($questions as $q) {
        $html .= '<li>&ldquo;' . mh_h($q) . '&rdquo;</li>';
    }
    return $html . '</ol></div>';
}

/** Text shown when the database tables are missing or old */
function mh_setup_message()
{
    return 'The database tables for this form are missing or old. New installation: import database/mh_tables.sql in phpMyAdmin. '
         . 'If you installed an earlier version before: import database/mh_tables_update_v2.sql once. Then reload this page.';
}
