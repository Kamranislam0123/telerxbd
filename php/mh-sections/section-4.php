<?php
/**
 * TAB 4 - Medication & Family
 *   1. Medication History    (current medicines, "+" adds a row)
 *   2. Allergy History       (drug allergy + other allergies copied into the findings box)
 *   3. Family History        (table, "+" adds a row)
 *
 * Same three jobs as section-1.php ('form', 'save', 'pdf').
 */

$med_adherence   = array('Regular', 'Irregular', 'Stopped', 'Unknown');
$other_allergies = array('Food', 'Environmental', 'Latex');

$med_spec = array(
    'medicine'  => array('text', 150),
    'dose'      => array('text', 60),
    'frequency' => array('text', 60),
    'route'     => array('text', 60),
    'since'     => array('text', 60),
    'reason'    => array('text', 150),
    'adherence' => array('pick', $med_adherence),
);

$drug_allergy_spec = array(
    'drug'     => array('text', 150),
    'reaction' => array('text', 200),
    'severity' => array('text', 60),
);

$family_spec = array(
    'relative'  => array('text', 60),
    'status'    => array('pick', array('Alive', 'Dead')),
    'age'       => array('text', 30),
    'disease'   => array('text', 200),
    'onset_age' => array('text', 30),
    'remarks'   => array('text', 200),
);

/* =====================================================================
 * MODE: FORM
 * ===================================================================== */
if ($mh_mode === 'form') {
?>
<!-- Suggestions for the free-text boxes (the patient can still type anything) -->
<datalist id="mh_dl_frequency">
    <option value="Once daily (OD)"><option value="Twice daily (BD)"><option value="Three times daily (TDS)"><option value="Four times daily (QID)">
    <option value="At night (HS)"><option value="When needed (SOS)"><option value="Weekly"><option value="Monthly">
</datalist>
<datalist id="mh_dl_route">
    <option value="Oral"><option value="Injection (IV)"><option value="Injection (IM)"><option value="Injection (SC)"><option value="Inhaler">
    <option value="Nebulizer"><option value="Topical (skin)"><option value="Eye drop"><option value="Ear drop"><option value="Nasal"><option value="Rectal">
</datalist>
<datalist id="mh_dl_severity">
    <option value="Mild"><option value="Moderate"><option value="Severe"><option value="Life-threatening (Anaphylaxis)">
</datalist>
<datalist id="mh_dl_relative">
    <option value="Father"><option value="Mother"><option value="Brother"><option value="Sister">
    <option value="Paternal grandfather"><option value="Paternal grandmother"><option value="Maternal grandfather"><option value="Maternal grandmother">
</datalist>

<!-- ====== 1. Medication History ====== -->
<div class="mhf-group">1. Medication History</div>
<p class="mhf-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Current medications: write every medicine the patient takes now, including medicines bought without a prescription, vitamins and herbal medicines.</p>

<div class="mhf-table-wrap">
    <table class="mhf-table mhf-med-table">
        <thead>
            <tr>
                <th style="width:18%">Medicine</th>
                <th style="width:10%">Dose</th>
                <th style="width:13%">Frequency</th>
                <th style="width:11%">Route</th>
                <th style="width:11%">Since when</th>
                <th style="width:15%">Reason</th>
                <th style="width:16%">Medication adherence</th>
                <th class="mhf-col-del" style="width:6%" aria-label="Remove"></th>
            </tr>
        </thead>
        <tbody data-rows="meds" data-min-rows="1"></tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="mhf-add-cell">
                    <button type="button" class="mhf-add-row" data-repeat-add="meds"><i class="fa-solid fa-plus"></i> Add another medicine</button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<template id="mh-tpl-meds">
    <tr data-row>
        <td><input type="text" name="meds[__i__][medicine]" data-k="medicine" class="mhf-control" maxlength="150" placeholder="e.g. Amlodipine" aria-label="Medicine"></td>
        <td><input type="text" name="meds[__i__][dose]" data-k="dose" class="mhf-control" maxlength="60" placeholder="5 mg" aria-label="Dose"></td>
        <td><input type="text" name="meds[__i__][frequency]" data-k="frequency" class="mhf-control" maxlength="60" list="mh_dl_frequency" placeholder="e.g. BD" aria-label="Frequency"></td>
        <td><input type="text" name="meds[__i__][route]" data-k="route" class="mhf-control" maxlength="60" list="mh_dl_route" placeholder="Oral" aria-label="Route"></td>
        <td><input type="text" name="meds[__i__][since]" data-k="since" class="mhf-control" maxlength="60" placeholder="e.g. 2 years" aria-label="Since when"></td>
        <td><input type="text" name="meds[__i__][reason]" data-k="reason" class="mhf-control" maxlength="150" placeholder="e.g. BP" aria-label="Reason"></td>
        <td><div class="mhf-checks mhf-checks-stack"><?php foreach ($med_adherence as $o) { echo mh_form_check('radio', 'meds[__i__][adherence]', $o, $o, 'data-k="adherence"'); } ?></div></td>
        <td class="mhf-col-del"><button type="button" class="mhf-del-row" data-repeat-remove title="Remove this row" aria-label="Remove this row"><i class="fa-solid fa-xmark"></i></button></td>
    </tr>
</template>

<!-- ====== 2. Allergy History ====== -->
<div class="mhf-group">2. Allergy History</div>

<div class="mhf-field" data-field="drug_allergy" data-required="1">
    <div class="mhf-row mhf-row-center">
        <span class="mhf-label mhf-label-inline">Drug allergy <span class="mhf-req">*</span></span>
        <div class="mhf-checks">
            <?php echo mh_form_check('radio', 'drug_allergy', 'No', 'No known drug allergy'); ?>
            <?php echo mh_form_check('radio', 'drug_allergy', 'Yes', 'Yes', 'data-show="#mh_drug_allergy_section"'); ?>
        </div>
    </div>
    <div class="mhf-error" data-error-for="drug_allergy"></div>
</div>

<div id="mh_drug_allergy_section" class="mhf-hidden mhf-reveal">
    <div data-rows="drug_allergies" data-min-rows="1"></div>
    <button type="button" class="mhf-add-row mhf-add-row-block" data-repeat-add="drug_allergies"><i class="fa-solid fa-plus"></i> Add another drug allergy</button>
    <div class="mhf-error" data-error-for="drug_allergies"></div>
</div>

<template id="mh-tpl-drug_allergies">
    <div class="mhf-row mhf-allergy-row" data-row>
        <div class="mhf-field mhf-grow-15">
            <label class="mhf-label">Drug</label>
            <input type="text" name="drug_allergies[__i__][drug]" data-k="drug" class="mhf-control" maxlength="150" placeholder="e.g. Penicillin">
        </div>
        <div class="mhf-field mhf-grow-15">
            <label class="mhf-label">Reaction</label>
            <input type="text" name="drug_allergies[__i__][reaction]" data-k="reaction" class="mhf-control" maxlength="200" placeholder="e.g. Rash, swelling">
        </div>
        <div class="mhf-field">
            <label class="mhf-label">Severity</label>
            <input type="text" name="drug_allergies[__i__][severity]" data-k="severity" class="mhf-control" maxlength="60" list="mh_dl_severity" placeholder="e.g. Moderate">
        </div>
        <button type="button" class="mhf-del-row mhf-del-inline" data-repeat-remove title="Remove" aria-label="Remove this drug allergy"><i class="fa-solid fa-xmark"></i></button>
    </div>
</template>

<div class="mhf-label mhf-mt">Other allergies <span class="mhf-hint-inline">(tick to add to the box below)</span></div>
<div class="mhf-checks mhf-suggest">
    <?php foreach ($other_allergies as $a) {
        echo mh_form_check('checkbox', 'allergy_selected[]', $a, $a, 'data-append-to="#mh_allergy_findings"');
    } ?>
</div>
<div class="mhf-row">
    <div class="mhf-field mhf-full" data-field="allergy_findings">
        <label class="mhf-label" for="mh_allergy_findings">Allergy History findings</label>
        <textarea id="mh_allergy_findings" name="allergy_findings" class="mhf-control mhf-textarea" rows="3" maxlength="1500" placeholder="Ticked allergies appear here, one per line. Add details, e.g. Food: shrimp - itching."></textarea>
    </div>
</div>

<!-- ====== 3. Family History ====== -->
<div class="mhf-group">3. Family History</div>
<p class="mhf-note mhf-note-warn"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Please note that, Father, Mother, Brother, Sister, Paternal grandfather/mother, Maternal grandfather/mother medical history must be taken.</p>

<div class="mhf-table-wrap">
    <table class="mhf-table mhf-family-table">
        <thead>
            <tr>
                <th style="width:16%">Relative</th>
                <th style="width:13%">Alive / Death</th>
                <th style="width:14%">Age</th>
                <th style="width:22%">Disease / Cause of death</th>
                <th style="width:13%">Age at Onset Diseases</th>
                <th style="width:17%">Remarks</th>
                <th class="mhf-col-del" aria-label="Remove"></th>
            </tr>
        </thead>
        <tbody data-rows="family" data-min-rows="1"></tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="mhf-add-cell">
                    <button type="button" class="mhf-add-row" data-repeat-add="family"><i class="fa-solid fa-plus"></i> Add another relative</button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<template id="mh-tpl-family">
    <tr data-row>
        <td><input type="text" name="family[__i__][relative]" data-k="relative" class="mhf-control" maxlength="60" list="mh_dl_relative" placeholder="e.g. Father" aria-label="Relative"></td>
        <td>
            <select name="family[__i__][status]" data-k="status" data-family-status class="mhf-control mhf-select" aria-label="Alive or death">
                <option value="">Select</option>
                <option value="Alive">Alive</option>
                <option value="Dead">Dead</option>
            </select>
        </td>
        <!-- the small titles change with Alive / Dead (see assets/js/medical-history-form.js, Tab 4) -->
        <td>
            <span class="mhf-cell-cap" data-cap-alive="Age Now" data-cap-dead="Age at death" data-cap-none="Age now / at death">Age now / at death</span>
            <input type="text" name="family[__i__][age]" data-k="age" class="mhf-control" maxlength="30" placeholder="e.g. 65" aria-label="Age now or age at death">
        </td>
        <td>
            <span class="mhf-cell-cap" data-cap-alive="Disease" data-cap-dead="Cause of death" data-cap-none="Disease / Cause of death">Disease / Cause of death</span>
            <input type="text" name="family[__i__][disease]" data-k="disease" class="mhf-control" maxlength="200" placeholder="e.g. Diabetes" aria-label="Disease or cause of death">
        </td>
        <td>
            <span class="mhf-cell-cap">Age at Onset Diseases</span>
            <input type="text" name="family[__i__][onset_age]" data-k="onset_age" class="mhf-control" maxlength="30" placeholder="e.g. 50" aria-label="Age at onset of diseases">
        </td>
        <td><input type="text" name="family[__i__][remarks]" data-k="remarks" class="mhf-control" maxlength="200" placeholder="Remarks" aria-label="Remarks"></td>
        <td class="mhf-col-del"><button type="button" class="mhf-del-row" data-repeat-remove title="Remove this row" aria-label="Remove this row"><i class="fa-solid fa-xmark"></i></button></td>
    </tr>
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

    $d['meds'] = mh_clean_rows(isset($in['meds']) ? $in['meds'] : array(), $med_spec, 30);

    $d['drug_allergy'] = mh_pick(isset($in['drug_allergy']) ? $in['drug_allergy'] : '', array('No', 'Yes'), '');
    if ($d['drug_allergy'] === '') {
        $e['drug_allergy'] = 'Select "No known drug allergy" or "Yes".';
    }
    $d['drug_allergies'] = array();
    if ($d['drug_allergy'] === 'Yes') {
        $d['drug_allergies'] = mh_clean_rows(isset($in['drug_allergies']) ? $in['drug_allergies'] : array(), $drug_allergy_spec, 15);
        if (count($d['drug_allergies']) === 0) {
            $e['drug_allergies'] = 'Write the drug the patient is allergic to.';
        }
    }

    $d['allergy_selected'] = mh_pick_many(isset($in['allergy_selected']) ? $in['allergy_selected'] : array(), $other_allergies);
    $d['allergy_findings'] = mh_clean_multiline(isset($in['allergy_findings']) ? $in['allergy_findings'] : '', 1500);

    // older forms saved "Deceased" as free text -> now the dropdown value "Dead"
    $family_in = (isset($in['family']) && is_array($in['family'])) ? $in['family'] : array();
    foreach ($family_in as $i => $row) {
        if (is_array($row) && isset($row['status']) && $row['status'] === 'Deceased') {
            $family_in[$i]['status'] = 'Dead';
        }
    }
    $d['family'] = mh_clean_rows($family_in, $family_spec, 30);

    $mh_result = array('data' => $d, 'errors' => $e);
    return;
}

/* =====================================================================
 * MODE: PDF
 * ===================================================================== */
if ($mh_mode === 'pdf') {
    $d = $mh_data;
    $cell = function ($row, $k) {
        return mh_h(isset($row[$k]) ? $row[$k] : '');
    };
    echo '<div class="sec-title">' . (int)$mh_no . '. ' . mh_h($mh_title) . '</div>';

    // 1. Medication
    echo mh_pdf_group('1. Medication History - Current Medications');
    $rows = array();
    foreach ((isset($d['meds']) && is_array($d['meds'])) ? $d['meds'] : array() as $m) {
        $rows[] = array('<b>' . $cell($m, 'medicine') . '</b>', $cell($m, 'dose'), $cell($m, 'frequency'), $cell($m, 'route'),
                        $cell($m, 'since'), $cell($m, 'reason'), $cell($m, 'adherence'));
    }
    echo mh_pdf_table(array('Medicine' => '19%', 'Dose' => '10%', 'Frequency' => '15%', 'Route' => '11%', 'Since when' => '12%', 'Reason' => '18%', 'Adherence' => '15%'),
                      $rows, 'No current medication.');

    // 2. Allergy
    echo mh_pdf_group('2. Allergy History');
    $drug_allergy = isset($d['drug_allergy']) ? (string)$d['drug_allergy'] : '';
    echo mh_pdf_wide(array(array('Drug allergy', $drug_allergy === 'No' ? 'No known drug allergy' : ($drug_allergy === '' ? '&mdash;' : mh_h($drug_allergy)))));
    if ($drug_allergy === 'Yes') {
        $rows = array();
        foreach ((isset($d['drug_allergies']) && is_array($d['drug_allergies'])) ? $d['drug_allergies'] : array() as $a) {
            $rows[] = array('<b>' . $cell($a, 'drug') . '</b>', $cell($a, 'reaction'), $cell($a, 'severity'));
        }
        echo mh_pdf_table(array('Drug' => '34%', 'Reaction' => '44%', 'Severity' => '22%'), $rows);
    }
    echo mh_pdf_wide(array(array('Allergy History findings', mh_pdf_text(isset($d['allergy_findings']) ? $d['allergy_findings'] : ''))));

    // 3. Family history
    echo mh_pdf_group('3. Family History');
    $rows = array();
    foreach ((isset($d['family']) && is_array($d['family'])) ? $d['family'] : array() as $f) {
        $status = isset($f['status']) ? (string)$f['status'] : '';
        if ($status === 'Deceased') {
            $status = 'Dead';
        }
        // the PDF says which kind of age / disease it is, like the website does
        $age = $cell($f, 'age');
        $dis = $cell($f, 'disease');
        if ($status === 'Alive') {
            $age = $age !== '' ? $age . ' <span class="muted">(now)</span>' : '';
        } elseif ($status === 'Dead') {
            $age = $age !== '' ? $age . ' <span class="muted">(at death)</span>' : '';
            $dis = $dis !== '' ? '<span class="muted">Cause of death:</span> ' . $dis : '';
        }
        $rows[] = array('<b>' . $cell($f, 'relative') . '</b>', mh_h($status), $age, $dis, $cell($f, 'onset_age'), $cell($f, 'remarks'));
    }
    echo mh_pdf_table(array('Relative' => '16%', 'Alive / Death' => '11%', 'Age now / at death' => '14%', 'Disease / Cause of death' => '24%', 'Age at Onset Diseases' => '13%', 'Remarks' => '22%'), $rows);
    return;
}
