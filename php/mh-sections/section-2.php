<?php
/**
 * TAB 2 - Chief Complaint & Risk Factors
 *
 * Same three jobs as section-1.php ('form', 'save', 'pdf').
 * The "Doctor asks" questions are shown on the website only - they are not printed in the PDF.
 */

// Risk table: key => array(category title, tick boxes)
$risk_groups = array(
    'cardio'     => array('Cardiovascular', array('Hypertension', 'Dyslipidemia', 'Obesity', 'Smoking', 'Family history of premature CVD')),
    'thrombo'    => array('Thromboembolic', array('Previous DVT/PE', 'Recent surgery', 'Prolonged immobilization', 'Cancer', 'Pregnancy/postpartum', 'OCP/HRT')),
    'metabolic'  => array('Diabetes / Metabolic', array('Diabetes', 'Obesity', 'Sedentary lifestyle', 'Family history', 'Previous gestational DM', 'PCOS')),
    'resp'       => array('Respiratory', array('Smoking', 'Passive smoking', 'Asthma', 'COPD', 'TB exposure', 'Occupational exposure')),
    'infect'     => array('Infectious Disease', array('Recent travel', 'Sick contact', 'TB contact', 'Unsafe food/water', 'High-risk exposure')),
    'cancer'     => array('Cancer', array('Family history', 'Smoking', 'Alcohol', 'Previous cancer', 'Occupational exposure')),
    'renal'      => array('Renal', array('Diabetes', 'Hypertension', 'Recurrent UTI', 'Stones', 'NSAID use', 'Family history')),
    'liver'      => array('Liver', array('Hepatitis exposure', 'Alcohol', 'Obesity/NAFLD risk', 'Hepatotoxic drugs')),
    'neuro'      => array('Neurological', array('Previous stroke/TIA', 'Hypertension', 'Diabetes', 'Seizure history', 'Head injury')),
    'medication' => array('Medication-related', array('Steroids', 'Anticoagulants', 'Antiplatelets', 'NSAIDs', 'Immunosuppressants')),
    'lifestyle'  => array('Lifestyle', array('Physical inactivity', 'Unhealthy diet', 'Sleep deprivation', 'High stress')),
);

/* =====================================================================
 * MODE: FORM
 * ===================================================================== */
if ($mh_mode === 'form') {
?>
<!-- ====== 1. Chief Complaint ====== -->
<div class="mhf-group">1. Chief Complaint</div>

<?php echo mh_form_ask(array(
    'What is the main problem that brought you to the doctor today?',
    'What other problems are you having?',
)); ?>

<div class="mhf-row">
    <div class="mhf-field mhf-full" data-field="main_problem" data-required="1">
        <label class="mhf-label" for="mh_main_problem">Main problem <span class="mhf-req">*</span></label>
        <textarea id="mh_main_problem" name="main_problem" class="mhf-control mhf-textarea" rows="3" maxlength="1500" placeholder="Write the main problem in the patient's own words"></textarea>
        <div class="mhf-error" data-error-for="main_problem"></div>
    </div>
</div>
<div class="mhf-row">
    <div class="mhf-field mhf-full" data-field="other_problems">
        <label class="mhf-label" for="mh_other_problems">Other problems</label>
        <textarea id="mh_other_problems" name="other_problems" class="mhf-control mhf-textarea" rows="3" maxlength="1500" placeholder="Any other problems the patient is having"></textarea>
    </div>
</div>

<!-- ====== 2. Risk Factors ====== -->
<div class="mhf-group">2. Risk Factors</div>

<div class="mhf-table-wrap">
    <table class="mhf-table mhf-risk-table">
        <thead>
            <tr>
                <th style="width:19%">Risk Category</th>
                <th>Risk Factors</th>
                <th style="width:24%">Status / Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($risk_groups as $key => $group): ?>
                <tr data-risk-group="<?php echo mh_h($key); ?>" data-risk-title="<?php echo mh_h($group[0]); ?>">
                    <th scope="row"><?php echo mh_h($group[0]); ?></th>
                    <td>
                        <div class="mhf-checks">
                            <?php foreach ($group[1] as $factor) {
                                echo mh_form_check('checkbox', 'risk[' . $key . '][]', $factor, $factor, 'data-risk-factor="1"');
                            } ?>
                        </div>
                    </td>
                    <td><input type="text" name="risk_details[<?php echo mh_h($key); ?>]" class="mhf-control" maxlength="200" placeholder="Details" aria-label="<?php echo mh_h($group[0]); ?> details"></td>
                </tr>
            <?php endforeach; ?>
            <tr data-risk-group="other" data-risk-title="Other Relevant Risk">
                <th scope="row">Other Relevant Risk</th>
                <td><input type="text" id="mh_risk_other" name="risk_other" class="mhf-control" maxlength="300" placeholder="Type any other relevant risk" data-risk-other="1"></td>
                <td><input type="text" name="risk_details[other]" class="mhf-control" maxlength="200" placeholder="Details" aria-label="Other relevant risk details"></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="mhf-identified" aria-live="polite">
    <div class="mhf-identified-title"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Important risk factors identified <span class="mhf-tag">auto</span></div>
    <div class="mhf-identified-body" id="mh_risk_identified"><span class="mhf-identified-empty">No risk factor ticked yet.</span></div>
</div>
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

    $d['main_problem'] = mh_clean_multiline(isset($in['main_problem']) ? $in['main_problem'] : '', 1500);
    if ($d['main_problem'] === '') {
        $e['main_problem'] = 'Write the main problem.';
    }
    $d['other_problems'] = mh_clean_multiline(isset($in['other_problems']) ? $in['other_problems'] : '', 1500);

    $risk_in    = (isset($in['risk']) && is_array($in['risk'])) ? $in['risk'] : array();
    $details_in = (isset($in['risk_details']) && is_array($in['risk_details'])) ? $in['risk_details'] : array();

    $d['risk'] = array();
    $d['risk_details'] = array();
    foreach ($risk_groups as $key => $group) {
        $d['risk'][$key] = mh_pick_many(isset($risk_in[$key]) ? $risk_in[$key] : array(), $group[1]);
        $d['risk_details'][$key] = mh_clean_text(isset($details_in[$key]) ? $details_in[$key] : '', 200);
    }
    $d['risk_other'] = mh_clean_text(isset($in['risk_other']) ? $in['risk_other'] : '', 300);
    $d['risk_details']['other'] = mh_clean_text(isset($details_in['other']) ? $details_in['other'] : '', 200);

    $mh_result = array('data' => $d, 'errors' => $e);
    return;
}

/* =====================================================================
 * MODE: PDF
 * ===================================================================== */
if ($mh_mode === 'pdf') {
    $d       = $mh_data;
    $risk    = (isset($d['risk']) && is_array($d['risk'])) ? $d['risk'] : array();
    $details = (isset($d['risk_details']) && is_array($d['risk_details'])) ? $d['risk_details'] : array();
    $other   = isset($d['risk_other']) ? (string)$d['risk_other'] : '';

    echo '<div class="sec-title">' . (int)$mh_no . '. ' . mh_h($mh_title) . '</div>';

    // 1. Chief complaint (only what was typed)
    echo mh_pdf_group('1. Chief Complaint');
    echo mh_pdf_wide(array(
        array('Main Problem', mh_pdf_text(isset($d['main_problem']) ? $d['main_problem'] : '')),
        array('Other Problems', mh_pdf_text(isset($d['other_problems']) ? $d['other_problems'] : '')),
    ));

    // 2. Risk factors
    echo mh_pdf_group('2. Risk Factors');
    $rows = array();
    $identified = array();
    foreach ($risk_groups as $key => $group) {
        $ticked = (isset($risk[$key]) && is_array($risk[$key])) ? $risk[$key] : array();
        if (count($ticked) > 0) {
            $identified[] = '<b>' . mh_h($group[0]) . ':</b> ' . mh_h(implode(', ', $ticked));
        }
        $rows[] = array(
            '<b>' . mh_h($group[0]) . '</b>',
            count($ticked) > 0 ? mh_h(implode(', ', $ticked)) : '<span class="muted">None</span>',
            mh_h(isset($details[$key]) ? $details[$key] : ''),
        );
    }
    if ($other !== '') {
        $identified[] = '<b>Other Relevant Risk:</b> ' . mh_h($other);
    }
    $rows[] = array(
        '<b>Other Relevant Risk</b>',
        $other !== '' ? mh_h($other) : '<span class="muted">None</span>',
        mh_h(isset($details['other']) ? $details['other'] : ''),
    );
    echo mh_pdf_table(array('Risk Category' => '22%', 'Risk Factors' => '50%', 'Status / Details' => '28%'), $rows);

    echo '<div class="callout"><div class="callout-title">Important risk factors identified</div>';
    echo count($identified) > 0 ? implode('<br />', $identified) : '<span class="muted">No risk factor identified.</span>';
    echo '</div>';
    return;
}
