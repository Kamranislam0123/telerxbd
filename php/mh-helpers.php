<?php
/**
 * TeleRx Bangladesh - Patient Medical History Form
 * Shared helpers (used by the form page, the save endpoint and the PDF generator).
 *
 * Works with PHP 7.2 and newer.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/subscription-helper.php';

/* ---------------------------------------------------------------------
 * SETTINGS  (the only two things you may want to change)
 * ------------------------------------------------------------------- */

// true  = only Premium subscribers can use the form (same rule as the existing Medical History page)
// false = every logged-in patient can use it
if (!defined('MH_REQUIRE_PREMIUM')) {
    define('MH_REQUIRE_PREMIUM', true);
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
        2 => array('title' => 'Tab 2', 'file' => 'section-placeholder.php'),
        3 => array('title' => 'Tab 3', 'file' => 'section-placeholder.php'),
        4 => array('title' => 'Tab 4', 'file' => 'section-placeholder.php'),
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

function mh_is_premium($conn, $patient_id)
{
    $sub = getActiveSubscription((int)$patient_id, $conn);
    if (!$sub) {
        return false;
    }
    $code = strtolower(isset($sub['plan_code']) ? (string)$sub['plan_code'] : '');
    $name = strtolower(isset($sub['plan_name']) ? (string)$sub['plan_name'] : '');
    return (strpos($code, 'premium') !== false || strpos($name, 'premium') !== false);
}

function mh_has_access($conn, $patient_id)
{
    if (!MH_REQUIRE_PREMIUM) {
        return true;
    }
    return mh_is_premium($conn, $patient_id);
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

/** The patient's most recently edited unfinished form (or null). */
function mh_latest_draft($conn, $patient_id)
{
    $stmt = $conn->prepare("SELECT * FROM mh_forms WHERE patient_id = ? AND status = 'draft' ORDER BY updated_at DESC, id DESC LIMIT 1");
    $patient_id = (int)$patient_id;
    $stmt->bind_param('i', $patient_id);
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
