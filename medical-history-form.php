<?php
/**
 * Patient Medical History FORM - TeleRx Bangladesh
 * Multi-tab form: Save and Next -> ... -> Save and Finish -> Generate PDF Now
 *
 *   medical-history-form            -> continues the latest unfinished form (or starts a new one)
 *   medical-history-form?new=1      -> starts a new, empty form
 *   medical-history-form?id=12      -> opens form number 12 (must belong to the logged-in patient)
 */

session_start();
require_once __DIR__ . '/php/mh-helpers.php';

// Force patient login (same check as the other patient pages)
$patient_id = mh_current_patient_id();
if ($patient_id <= 0) {
    header('Location: login.php');
    exit;
}

$base       = (defined('APP_BASE') && APP_BASE) ? APP_BASE : '';
$patient    = null;
$form       = null;
$tab_data   = array();
$saved_nos  = array();
$page_error = '';

mh_db_strict(true);
try {
    $conn = getDBConnection();

    // Patient row (the left menu needs it)
    $stmt = $conn->prepare('SELECT * FROM patients WHERE id = ?');
    $stmt->bind_param('i', $patient_id);
    $stmt->execute();
    $patient = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$patient) {
        header('Location: login.php');
        exit;
    }
    $patient['profile_image'] = !empty($patient['profile_image']) ? $patient['profile_image'] : 'assets/img/doctors-dashboard/profile-06.jpg';

    // Premium rule (can be switched off in php/mh-helpers.php)
    if (!mh_has_access($conn, $patient_id)) {
        header('Location: ' . $base . '/patient-medical-history');
        exit;
    }

    if (!mh_tables_ready($conn)) {
        $page_error = 'The database tables for this form are not created yet. Import the file database/mh_tables.sql in phpMyAdmin, then reload this page.';
    } else {
        $req_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($req_id > 0) {
            $form = mh_get_form($conn, $req_id, $patient_id);
            if (!$form) {
                header('Location: ' . $base . '/medical-history-form');
                exit;
            }
        } elseif (empty($_GET['new'])) {
            $form = mh_latest_draft($conn, $patient_id);   // may be null -> blank form
        }
        if ($form) {
            $tab_data  = mh_get_sections($conn, (int)$form['id']);
            $saved_nos = array_keys($tab_data);
        }
    }
    $conn->close();
} catch (Throwable $e) {
    error_log('[medical-history-form] ' . $e->getMessage());
    $page_error = MH_DEBUG ? ('Error: ' . $e->getMessage()) : 'Something went wrong while loading the form. Please try again.';
}

mh_db_strict(false);   // back to normal for header.php / footer.php

$sections    = mh_sections();
$total_tabs  = count($sections);
$is_complete = ($form && $form['status'] === 'completed');

// Which tab opens first?
$first_unsaved = 1;
while ($first_unsaved <= $total_tabs && in_array($first_unsaved, $saved_nos, true)) {
    $first_unsaved++;
}
$max_open = $is_complete ? $total_tabs : min($first_unsaved, $total_tabs);   // highest tab the patient may open
$start_tab = $is_complete ? 1 : $max_open;
if (isset($_GET['tab'])) {
    $want = (int)$_GET['tab'];
    if ($want >= 1 && $want <= $max_open) {
        $start_tab = $want;
    }
}

// Data handed to the JavaScript
$tabs_meta = array();
foreach ($sections as $no => $meta) {
    $tabs_meta[] = array('no' => (int)$no, 'title' => $meta['title']);
}
$state = array(
    'formId'    => $form ? (int)$form['id'] : 0,
    'status'    => $form ? $form['status'] : 'new',
    'csrf'      => mh_csrf_token(),
    'tabs'      => $tabs_meta,
    'saved'     => array_map('intval', $saved_nos),
    'data'      => $tab_data ? $tab_data : new stdClass(),
    'start'     => $start_tab,
    'urls'      => array(
        'save'    => $base . '/php/mh-save-section.php',
        'pdf'     => $base . '/php/mh-generate-pdf.php',
        'page'    => $base . '/medical-history-form',
        'history' => $base . '/patient-medical-history',
    ),
);
$state_json = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

$asset_v = '1';   // change this number if you edit the CSS/JS and the browser keeps showing the old one

include 'header.php';
?>

<link rel="stylesheet" href="<?php echo $base; ?>/assets/css/medical-history-form.css?v=<?php echo $asset_v; ?>">

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="patient-dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="patient-medical-history">Medical History</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Fill Form</li>
                    </ol>
                    <h2 class="breadcrumb-title">Patient Medical History Form</h2>
                </nav>
            </div>
        </div>
    </div>
    <div class="breadcrumb-bg">
        <img src="assets/img/bg/breadcrumb-bg-01.png" alt="img" class="breadcrumb-bg-01">
        <img src="assets/img/bg/breadcrumb-bg-02.png" alt="img" class="breadcrumb-bg-02">
        <img src="assets/img/bg/breadcrumb-icon.png" alt="img" class="breadcrumb-bg-03">
        <img src="assets/img/bg/breadcrumb-icon.png" alt="img" class="breadcrumb-bg-04">
    </div>
</div>
<!-- /Breadcrumb -->

<!-- Page Content -->
<div class="content">
    <div class="container">
        <div class="row">
            <?php include 'patient-leftside-menu.php'; ?>

            <div class="col-lg-8 col-xl-9">
                <div class="mhf" id="mhf-root">

                    <?php if ($page_error !== ''): ?>
                        <div class="mhf-card">
                            <div class="mhf-alert" role="alert">
                                <strong>The form cannot open yet.</strong><br>
                                <?php echo mh_h($page_error); ?>
                            </div>
                        </div>
                    <?php else: ?>

                    <div class="mhf-card">

                        <!-- Card header -->
                        <div class="mhf-head">
                            <div class="mhf-head-text">
                                <h3 class="mhf-title">Patient Medical History</h3>
                                <p class="mhf-subtitle" id="mhf-subtitle">
                                    <?php if ($form): ?>
                                        Form MH-<?php echo str_pad((string)(int)$form['id'], 6, '0', STR_PAD_LEFT); ?>
                                    <?php else: ?>
                                        New form
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="mhf-head-side">
                                <span class="mhf-status <?php echo $is_complete ? 'is-complete' : 'is-draft'; ?>" id="mhf-status">
                                    <?php echo $is_complete ? 'Completed' : 'Draft'; ?>
                                </span>
                                <a class="mhf-btn mhf-btn-outline mhf-btn-sm<?php echo $is_complete ? '' : ' mhf-hidden'; ?>"
                                   id="mhf-head-pdf" target="_blank" rel="noopener"
                                   href="<?php echo $form ? mh_h($base . '/php/mh-generate-pdf.php?form_id=' . (int)$form['id']) : '#'; ?>">
                                    <i class="fa-solid fa-file-pdf"></i> PDF
                                </a>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="mhf-tabs" role="tablist" aria-label="Medical history sections">
                            <?php foreach ($sections as $no => $meta): ?>
                                <button type="button" class="mhf-tab" role="tab"
                                        id="mhf-tab-<?php echo (int)$no; ?>"
                                        data-section="<?php echo (int)$no; ?>"
                                        aria-controls="mhf-panel-<?php echo (int)$no; ?>">
                                    <span class="mhf-tab-num"><span class="mhf-tab-num-text"><?php echo (int)$no; ?></span><i class="fa-solid fa-check mhf-tab-check" aria-hidden="true"></i></span>
                                    <span class="mhf-tab-label"><?php echo mh_h($meta['title']); ?></span>
                                    <i class="fa-solid fa-lock mhf-tab-lock" aria-hidden="true"></i>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Panels -->
                        <div class="mhf-body">
                            <?php foreach ($sections as $no => $meta):
                                $rendered = mh_run_section($no, 'form', isset($tab_data[$no]) ? $tab_data[$no] : array());
                                $is_last  = ((int)$no === $total_tabs);
                            ?>
                                <section class="mhf-panel" id="mhf-panel-<?php echo (int)$no; ?>"
                                         role="tabpanel" aria-labelledby="mhf-tab-<?php echo (int)$no; ?>"
                                         data-section="<?php echo (int)$no; ?>" hidden>

                                    <form class="mhf-form" data-section="<?php echo (int)$no; ?>" novalidate autocomplete="off">
                                        <?php echo $rendered['html']; ?>
                                    </form>

                                    <div class="mhf-actions">
                                        <?php if ($no > 1): ?>
                                            <button type="button" class="mhf-btn mhf-btn-ghost" data-action="back" data-section="<?php echo (int)$no; ?>">
                                                <i class="fa-solid fa-arrow-left"></i> Back
                                            </button>
                                        <?php endif; ?>
                                        <span class="mhf-actions-note"><span class="mhf-req">*</span> Required</span>
                                        <button type="button" class="mhf-btn mhf-btn-primary" data-action="save" data-section="<?php echo (int)$no; ?>" data-last="<?php echo $is_last ? '1' : '0'; ?>">
                                            <span class="mhf-btn-label"><?php echo $is_last ? 'Save and Finish' : 'Save and Next'; ?></span>
                                            <span class="mhf-spinner" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </section>
                            <?php endforeach; ?>

                            <!-- Shown after "Save and Finish" -->
                            <section class="mhf-panel mhf-finish" id="mhf-finish" role="status" hidden>
                                <div class="mhf-finish-icon" aria-hidden="true"><i class="fa-solid fa-circle-check"></i></div>
                                <h3 class="mhf-finish-title">All sections are saved</h3>
                                <p class="mhf-finish-text">The medical history is complete. Create the PDF to view, print or download it.</p>
                                <div class="mhf-finish-actions">
                                    <a class="mhf-btn mhf-btn-primary mhf-btn-lg" id="mhf-pdf-btn" target="_blank" rel="noopener" href="#">
                                        <i class="fa-solid fa-file-pdf"></i> Generate PDF Now
                                    </a>
                                    <a class="mhf-btn mhf-btn-ghost mhf-btn-lg" href="<?php echo mh_h($base . '/patient-medical-history'); ?>">Back to Medical History</a>
                                </div>
                            </section>
                        </div>
                    </div>

                    <!-- Message box (top right) -->
                    <div class="mhf-toast" id="mhf-toast" role="status" aria-live="polite"></div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($page_error === ''): ?>
<script>window.MH_STATE = <?php echo $state_json; ?>;</script>
<!-- "defer" = runs after jQuery, Select2 and the date picker (loaded by footer.php) are ready -->
<script defer src="<?php echo $base; ?>/assets/js/medical-history-form.js?v=<?php echo $asset_v; ?>"></script>
<?php endif; ?>

<?php include 'footer.php'; ?>
