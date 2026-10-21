<?php
/**
 * TAB 3 - Illness History
 *   1. History of Present Illness  (table, "+" adds a row)
 *   2. Past Medical History        (ticked diseases are copied into the text box)
 *   3. Previous hospitalization    (with surgical history, "Add more" adds another one)
 *
 * Same three jobs as section-1.php ('form', 'save', 'pdf').
 */

$hpi_onset    = array('Sudden', 'Gradual', 'Unknown');
$hpi_severity = array('Mild', 'Moderate', 'Severe');
$hpi_course   = array('Improving', 'Worsening', 'Static', 'Intermittent', 'Recurrent');

$pmh_diseases = array(
    'Hypertension', 'Diabetes mellitus', 'Heart disease', 'Stroke/TIA', 'Asthma/COPD', 'Tuberculosis', 'Kidney disease',
    'Liver disease', 'Thyroid disease', 'Epilepsy', 'Cancer', 'Psychiatric illness', 'Infectious disease',
);

$hpi_spec = array(
    'complaint' => array('text', 200),
    'onset'     => array('pick', $hpi_onset),
    'severity'  => array('pick', $hpi_severity),
    'character' => array('text', 200),
    'duration'  => array('text', 100),
    'course'    => array('many', $hpi_course),
);

$hosp_spec = array(
    'reason'        => array('text', 200),
    'hospital'      => array('text', 200),
    'date'          => array('text', 60),
    'icu'           => array('bool'),
    'surgery'       => array('bool'),
    'surg_previous' => array('pick', array('Yes', 'No')),
    'surg_type'     => array('text', 200),
    'surg_date'     => array('text', 60),
    'surg_reason'   => array('text', 200),
    'surg_complications' => array('text', 300),
    'surg_anaesthesia'   => array('text', 300),
    'surg_transfusion'   => array('text', 300),
);

/* =====================================================================
 * MODE: FORM
 * ===================================================================== */
if ($mh_mode === 'form') {
?>
<!-- ====== 1. History of Present Illness ====== -->
<div class="mhf-group">1. History of Present Illness</div>

<div class="mhf-table-wrap" data-field="hpi">
    <table class="mhf-table mhf-hpi-table">
        <thead>
            <tr>
                <th style="width:20%">Complaint <span class="mhf-req">*</span></th>
                <th style="width:12%">Onset</th>
                <th style="width:12%">Severity</th>
                <th style="width:19%">Character</th>
                <th style="width:15%">Duration</th>
                <th style="width:15%">Course</th>
                <th class="mhf-col-del" aria-label="Remove"></th>
            </tr>
        </thead>
        <tbody data-rows="hpi" data-min-rows="1"></tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="mhf-add-cell">
                    <button type="button" class="mhf-add-row" data-repeat-add="hpi"><i class="fa-solid fa-plus"></i> Add another complaint</button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="mhf-error" data-error-for="hpi"></div>

<template id="mh-tpl-hpi">
    <tr data-row>
        <td><textarea name="hpi[__i__][complaint]" data-k="complaint" class="mhf-control mhf-textarea mhf-cell-text" rows="2" maxlength="200" placeholder="e.g. Chest pain" aria-label="Complaint"></textarea></td>
        <td><div class="mhf-checks mhf-checks-stack"><?php foreach ($hpi_onset as $o) { echo mh_form_check('radio', 'hpi[__i__][onset]', $o, $o, 'data-k="onset"'); } ?></div></td>
        <td><div class="mhf-checks mhf-checks-stack"><?php foreach ($hpi_severity as $o) { echo mh_form_check('radio', 'hpi[__i__][severity]', $o, $o, 'data-k="severity"'); } ?></div></td>
        <td><textarea name="hpi[__i__][character]" data-k="character" class="mhf-control mhf-textarea mhf-cell-text" rows="2" maxlength="200" placeholder="e.g. Burning, pressing" aria-label="Character"></textarea></td>
        <td><input type="text" name="hpi[__i__][duration]" data-k="duration" class="mhf-control" maxlength="100" placeholder="e.g. 3 days" aria-label="Duration"></td>
        <td><div class="mhf-checks mhf-checks-stack"><?php foreach ($hpi_course as $o) { echo mh_form_check('checkbox', 'hpi[__i__][course][]', $o, $o, 'data-k="course"'); } ?></div></td>
        <td class="mhf-col-del"><button type="button" class="mhf-del-row" data-repeat-remove title="Remove this row" aria-label="Remove this row"><i class="fa-solid fa-xmark"></i></button></td>
    </tr>
</template>

<!-- ====== 2. Past Medical History ====== -->
<div class="mhf-group">2. Past Medical History</div>

<?php echo mh_form_ask(array(
    'Have you ever been diagnosed with any major medical problem?',
    'Are you currently under treatment for any disease?',
)); ?>

<div class="mhf-label">Previous diseases <span class="mhf-hint-inline">(tick to add to the box below)</span></div>
<div class="mhf-checks mhf-suggest">
    <?php foreach ($pmh_diseases as $dz) {
        echo mh_form_check('checkbox', 'pmh_selected[]', $dz, $dz, 'data-append-to="#mh_pmh_identified"');
    } ?>
</div>

<div class="mhf-row">
    <div class="mhf-field mhf-full" data-field="pmh_identified">
        <label class="mhf-label" for="mh_pmh_identified">Past Medical History identified</label>
        <textarea id="mh_pmh_identified" name="pmh_identified" class="mhf-control mhf-textarea" rows="4" maxlength="2000" placeholder="Ticked diseases appear here, one per line. You can also type here (e.g. year of diagnosis, current treatment)."></textarea>
    </div>
</div>

<!-- ====== 3. Previous hospitalization ====== -->
<div class="mhf-group">3. Previous Hospitalization</div>

<div class="mhf-checks">
    <?php echo mh_form_check('checkbox', 'has_hosp', '1', 'Previous hospitalization', 'id="mh_has_hosp" data-show="#mh_hosp_section"'); ?>
</div>

<div id="mh_hosp_section" class="mhf-hidden mhf-reveal">
    <div data-rows="hosp" data-min-rows="1"></div>
    <button type="button" class="mhf-add-row mhf-add-row-block" data-repeat-add="hosp"><i class="fa-solid fa-plus"></i> Add more Previous Hospitalization</button>
</div>

<template id="mh-tpl-hosp">
    <div class="mhf-block" data-row>
        <div class="mhf-block-head">
            <span class="mhf-block-title">Hospitalization <span data-row-no></span></span>
            <button type="button" class="mhf-del-row" data-repeat-remove title="Remove" aria-label="Remove this hospitalization"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="mhf-row">
            <div class="mhf-field mhf-grow-15">
                <label class="mhf-label">Reason</label>
                <input type="text" name="hosp[__i__][reason]" data-k="reason" class="mhf-control" maxlength="200" placeholder="Reason for admission">
            </div>
            <div class="mhf-field mhf-grow-15">
                <label class="mhf-label">Hospital</label>
                <input type="text" name="hosp[__i__][hospital]" data-k="hospital" class="mhf-control" maxlength="200" placeholder="Hospital name">
            </div>
            <div class="mhf-field">
                <label class="mhf-label">Date</label>
                <input type="text" name="hosp[__i__][date]" data-k="date" class="mhf-control" maxlength="60" placeholder="e.g. Mar 2023">
            </div>
        </div>
        <div class="mhf-checks">
            <?php echo mh_form_check('checkbox', 'hosp[__i__][icu]', '1', 'ICU admission?', 'data-k="icu"'); ?>
            <?php echo mh_form_check('checkbox', 'hosp[__i__][surgery]', '1', 'Surgery?', 'data-k="surgery" data-show-in-row=".mhf-surgery"'); ?>
        </div>

        <div class="mhf-surgery mhf-hidden mhf-reveal">
            <div class="mhf-subgroup">Surgical History</div>
            <div class="mhf-row mhf-row-center">
                <span class="mhf-label mhf-label-inline">Previous operation?</span>
                <div class="mhf-checks">
                    <?php echo mh_form_check('radio', 'hosp[__i__][surg_previous]', 'Yes', 'Yes', 'data-k="surg_previous"'); ?>
                    <?php echo mh_form_check('radio', 'hosp[__i__][surg_previous]', 'No', 'No', 'data-k="surg_previous"'); ?>
                </div>
            </div>
            <div class="mhf-row">
                <div class="mhf-field mhf-grow-15">
                    <label class="mhf-label">Type</label>
                    <input type="text" name="hosp[__i__][surg_type]" data-k="surg_type" class="mhf-control" maxlength="200" placeholder="Type of operation">
                </div>
                <div class="mhf-field">
                    <label class="mhf-label">Date</label>
                    <input type="text" name="hosp[__i__][surg_date]" data-k="surg_date" class="mhf-control" maxlength="60" placeholder="e.g. 2021">
                </div>
                <div class="mhf-field mhf-grow-15">
                    <label class="mhf-label">Reason</label>
                    <input type="text" name="hosp[__i__][surg_reason]" data-k="surg_reason" class="mhf-control" maxlength="200" placeholder="Reason for operation">
                </div>
            </div>
            <div class="mhf-row">
                <div class="mhf-field">
                    <label class="mhf-label">Complications</label>
                    <input type="text" name="hosp[__i__][surg_complications]" data-k="surg_complications" class="mhf-control" maxlength="300" placeholder="Any complication">
                </div>
                <div class="mhf-field">
                    <label class="mhf-label">Anaesthesia problem</label>
                    <input type="text" name="hosp[__i__][surg_anaesthesia]" data-k="surg_anaesthesia" class="mhf-control" maxlength="300" placeholder="Any problem with anaesthesia">
                </div>
                <div class="mhf-field">
                    <label class="mhf-label">Previous blood transfusion</label>
                    <input type="text" name="hosp[__i__][surg_transfusion]" data-k="surg_transfusion" class="mhf-control" maxlength="300" placeholder="e.g. 2 units, 2021">
                </div>
            </div>
        </div>
    </div>
</template>
<?php
    return;
}

/* =====================================================================
 * MODE: SAVE
 * ===================================================================== */
if ($mh_mode === 'save') {
    $in = $mh_input;
    $d  = array();
    $e  = array();

    $d['hpi'] = mh_clean_rows(isset($in['hpi']) ? $in['hpi'] : array(), $hpi_spec, 20);
    $has_complaint = false;
    foreach ($d['hpi'] as $row) {
        if ($row['complaint'] !== '') {
            $has_complaint = true;
        }
    }
    if (!$has_complaint) {
        $e['hpi'] = 'Write at least one complaint in the History of Present Illness.';
    }

    $d['pmh_selected']   = mh_pick_many(isset($in['pmh_selected']) ? $in['pmh_selected'] : array(), $pmh_diseases);
    $d['pmh_identified'] = mh_clean_multiline(isset($in['pmh_identified']) ? $in['pmh_identified'] : '', 2000);

    $d['has_hosp'] = (isset($in['has_hosp']) && $in['has_hosp'] === '1') ? 1 : 0;
    $d['hosp'] = array();
    if ($d['has_hosp'] === 1) {
        $rows = mh_clean_rows(isset($in['hosp']) ? $in['hosp'] : array(), $hosp_spec, 15);
        foreach ($rows as $row) {
            if ($row['surgery'] !== 1) {   // surgical details only belong to a hospitalization with surgery
                foreach ($row as $k => $v) {
                    if (strpos($k, 'surg_') === 0) {
                        $row[$k] = '';
                    }
                }
            }
            $d['hosp'][] = $row;
        }
    }

    $mh_result = array('data' => $d, 'errors' => $e);
    return;
}

/* =====================================================================
 * MODE: PDF
 * ===================================================================== */
if ($mh_mode === 'pdf') {
    $d = $mh_data;
    echo '<div class="sec-title">' . (int)$mh_no . '. ' . mh_h($mh_title) . '</div>';

    // 1. HPI
    echo mh_pdf_group('1. History of Present Illness');
    $rows = array();
    $hpi = (isset($d['hpi']) && is_array($d['hpi'])) ? $d['hpi'] : array();
    foreach ($hpi as $r) {
        $rows[] = array(
            '<b>' . mh_h(isset($r['complaint']) ? $r['complaint'] : '') . '</b>',
            mh_h(isset($r['onset']) ? $r['onset'] : ''),
            mh_h(isset($r['severity']) ? $r['severity'] : ''),
            mh_h(isset($r['character']) ? $r['character'] : ''),
            mh_h(isset($r['duration']) ? $r['duration'] : ''),
            (isset($r['course']) && is_array($r['course']) && $r['course']) ? mh_h(implode(', ', $r['course'])) : '',
        );
    }
    echo mh_pdf_table(array('Complaint' => '22%', 'Onset' => '11%', 'Severity' => '11%', 'Character' => '20%', 'Duration' => '14%', 'Course' => '22%'), $rows);

    // 2. Past medical history
    echo mh_pdf_group('2. Past Medical History');
    echo mh_pdf_wide(array(array('Past Medical History identified', mh_pdf_text(isset($d['pmh_identified']) ? $d['pmh_identified'] : ''))));

    // 3. Previous hospitalization
    echo mh_pdf_group('3. Previous Hospitalization');
    $hosp = (!empty($d['has_hosp']) && isset($d['hosp']) && is_array($d['hosp'])) ? $d['hosp'] : array();
    if (count($hosp) === 0) {
        echo mh_pdf_wide(array(array('Previous hospitalization', 'No')));
    }
    $g = function ($row, $k) {
        return isset($row[$k]) ? (string)$row[$k] : '';
    };
    foreach ($hosp as $i => $h) {
        echo '<div class="sub">Hospitalization ' . ($i + 1) . '</div>';
        echo '<table class="kv">';
        echo '<tr>' . mh_pdf_pair('Reason', $g($h, 'reason')) . mh_pdf_pair('Hospital', $g($h, 'hospital')) . '</tr>';
        echo '<tr>' . mh_pdf_pair('Date', $g($h, 'date')) . mh_pdf_pair('ICU admission', !empty($h['icu']) ? 'Yes' : 'No') . '</tr>';
        echo '<tr>' . mh_pdf_pair('Surgery', !empty($h['surgery']) ? 'Yes' : 'No', 3) . '</tr>';
        if (!empty($h['surgery'])) {
            echo '<tr><td class="k sk" colspan="4">Surgical History</td></tr>';
            echo '<tr>' . mh_pdf_pair('Previous operation', $g($h, 'surg_previous')) . mh_pdf_pair('Type', $g($h, 'surg_type')) . '</tr>';
            echo '<tr>' . mh_pdf_pair('Date', $g($h, 'surg_date')) . mh_pdf_pair('Reason', $g($h, 'surg_reason')) . '</tr>';
            echo '<tr>' . mh_pdf_pair('Complications', $g($h, 'surg_complications')) . mh_pdf_pair('Anaesthesia problem', $g($h, 'surg_anaesthesia')) . '</tr>';
            echo '<tr>' . mh_pdf_pair('Previous blood transfusion', $g($h, 'surg_transfusion'), 3) . '</tr>';
        }
        echo '</table>';
    }
    return;
}
