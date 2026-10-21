<?php
/**
 * TAB 1 - Patient Information
 *
 * This one file does three jobs, depending on $mh_mode:
 *   'form' -> the HTML fields shown in the tab
 *   'save' -> checks the submitted answers (server side) and cleans them
 *   'pdf'  -> the PDF page for this tab
 *
 * Available variables: $mh_no, $mh_title, $mh_data (saved answers), $mh_input (submitted answers)
 * Return value for 'save': set $mh_result = array('data' => ..., 'errors' => ...)
 */

/* =====================================================================
 * MODE: FORM  (HTML fields)
 * ===================================================================== */
if ($mh_mode === 'form') {
?>
<!-- ====== Patient details ====== -->
<div class="mhf-group">Patient details</div>

<!-- Line 1 -->
<div class="mhf-row">
    <div class="mhf-field mhf-grow-22" data-field="patient_name" data-required="1">
        <label class="mhf-label" for="mh_patient_name">Patient name <span class="mhf-req">*</span></label>
        <input type="text" id="mh_patient_name" name="patient_name" class="mhf-control" maxlength="120" autocomplete="off" placeholder="Full name">
        <div class="mhf-error" data-error-for="patient_name"></div>
    </div>

    <div class="mhf-field mhf-grow-15" data-field="dob" data-required="1">
        <label class="mhf-label" for="mh_dob_display">Date of Birth <span class="mhf-req">*</span></label>
        <div class="mhf-dob" id="mh_dob_wrap">
            <input type="text" id="mh_dob_display" class="mhf-control" placeholder="1 Jan 2000" autocomplete="off">
            <span class="mhf-dob-icon" aria-hidden="true"><i class="fa-regular fa-calendar"></i></span>
            <input type="hidden" id="mh_dob" name="dob" value="">
        </div>
        <div class="mhf-error" data-error-for="dob"></div>
    </div>

    <div class="mhf-field mhf-grow-1">
        <label class="mhf-label" for="mh_age">Age <span class="mhf-tag">auto</span></label>
        <input type="text" id="mh_age" class="mhf-control mhf-auto" readonly tabindex="-1" aria-readonly="true" placeholder="&mdash;">
    </div>

    <div class="mhf-field mhf-grow-14">
        <label class="mhf-label" for="mh_age_group">Age Group <span class="mhf-tag">auto</span></label>
        <input type="text" id="mh_age_group" class="mhf-control mhf-auto" readonly tabindex="-1" aria-readonly="true" placeholder="&mdash;">
    </div>
</div>

<!-- Line 2 -->
<div class="mhf-row">
    <div class="mhf-field" data-field="sex" data-required="1">
        <label class="mhf-label" for="mh_sex">Sex <span class="mhf-req">*</span></label>
        <select id="mh_sex" name="sex" class="mhf-control mhf-select">
            <option value="Male" selected>Male</option>
            <option value="Female">Female</option>
            <option value="Intersex">Intersex</option>
            <option value="Other">Other</option>
        </select>
        <div class="mhf-error" data-error-for="sex"></div>
    </div>

    <div class="mhf-field" data-field="marital_status" data-required="1">
        <label class="mhf-label" for="mh_marital_status">Marital Status <span class="mhf-req">*</span></label>
        <select id="mh_marital_status" name="marital_status" class="mhf-control mhf-select">
            <option value="">Select</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>
        <div class="mhf-error" data-error-for="marital_status"></div>
    </div>

    <!-- Shown only when Sex = Female AND Marital Status = Yes -->
    <div class="mhf-field mhf-hidden" id="mh_pregnancy_field" data-field="pregnancy_status">
        <label class="mhf-label" for="mh_pregnancy_status">Pregnancy status</label>
        <select id="mh_pregnancy_status" name="pregnancy_status" class="mhf-control mhf-select">
            <option value="No" selected>No</option>
            <option value="Yes">Yes</option>
            <option value="Possibly">Possibly</option>
        </select>
        <div class="mhf-error" data-error-for="pregnancy_status"></div>
    </div>

    <div class="mhf-field" data-field="religion">
        <label class="mhf-label" for="mh_religion">Religious Status</label>
        <select id="mh_religion" name="religion" class="mhf-control mhf-select">
            <option value="Islam" selected>Islam</option>
            <option value="Hindu">Hindu</option>
            <option value="Christian">Christian</option>
            <option value="Buddhist">Buddhist</option>
            <option value="Others">Others</option>
        </select>
        <div class="mhf-error" data-error-for="religion"></div>
    </div>
</div>

<!-- ====== Body measurements ====== -->
<div class="mhf-group">Body measurements</div>

<!-- Line 3 : BMI calculator -->
<div class="mhf-row">
    <div class="mhf-field" data-field="weight_kg" data-required="1">
        <label class="mhf-label" for="mh_weight_kg">Weight <span class="mhf-req">*</span></label>
        <div class="mhf-unit">
            <input type="number" id="mh_weight_kg" name="weight_kg" class="mhf-control" min="0.3" max="500" step="any" inputmode="decimal" placeholder="e.g. 62">
            <span class="mhf-unit-text">kg</span>
        </div>
        <div class="mhf-error" data-error-for="weight_kg"></div>
    </div>

    <div class="mhf-field mhf-grow-15" data-field="height" data-required="1">
        <label class="mhf-label" for="mh_height_ft">Height <span class="mhf-req">*</span></label>
        <div class="mhf-pair">
            <div class="mhf-unit">
                <input type="number" id="mh_height_ft" name="height_ft" class="mhf-control" min="0" max="8" step="1" inputmode="numeric" placeholder="5" aria-label="Height in feet">
                <span class="mhf-unit-text">ft</span>
            </div>
            <div class="mhf-unit">
                <input type="number" id="mh_height_in" name="height_in" class="mhf-control" min="0" max="11.9" step="any" inputmode="decimal" placeholder="6" aria-label="Height in inches">
                <span class="mhf-unit-text">in</span>
            </div>
        </div>
        <div class="mhf-error" data-error-for="height_ft"></div>
        <div class="mhf-error" data-error-for="height_in"></div>
    </div>

    <div class="mhf-field mhf-grow-15">
        <label class="mhf-label" for="mh_bmi">BMI <span class="mhf-tag">auto</span></label>
        <input type="text" id="mh_bmi" class="mhf-control mhf-auto" readonly tabindex="-1" aria-readonly="true" placeholder="&mdash;">
    </div>
</div>

<!-- ====== Occupation ====== -->
<div class="mhf-group">Occupation</div>

<!-- Line 4 -->
<div class="mhf-row">
    <div class="mhf-field" data-field="occupation" data-required="1">
        <label class="mhf-label" for="mh_occupation">Occupation <span class="mhf-req">*</span></label>
        <select id="mh_occupation" name="occupation" class="mhf-control mhf-select">
            <option value="">Select</option>
            <option value="Private Service">Private Service</option>
            <option value="Government Service">Government Service</option>
            <option value="Retired">Retired</option>
            <option value="Others">Others</option>
        </select>
        <div class="mhf-error" data-error-for="occupation"></div>
    </div>

    <!-- Shown only when Occupation = Others -->
    <div class="mhf-field mhf-hidden" id="mh_occupation_other_field" data-field="occupation_other">
        <label class="mhf-label" for="mh_occupation_other">Write occupation <span class="mhf-req">*</span></label>
        <input type="text" id="mh_occupation_other" name="occupation_other" class="mhf-control" maxlength="100" autocomplete="off" placeholder="e.g. Farmer, Student">
        <div class="mhf-error" data-error-for="occupation_other"></div>
    </div>

    <div class="mhf-field" data-field="occupational_hazard">
        <label class="mhf-label" for="mh_occupational_hazard">Occupational Hazard</label>
        <select id="mh_occupational_hazard" name="occupational_hazard" class="mhf-control mhf-select">
            <option value="No" selected>No</option>
            <option value="Yes">Yes</option>
        </select>
        <div class="mhf-error" data-error-for="occupational_hazard"></div>
    </div>

    <!-- Shown only when Occupational Hazard = Yes -->
    <div class="mhf-field mhf-grow-15 mhf-hidden" id="mh_hazard_details_field" data-field="hazard_details">
        <label class="mhf-label" for="mh_hazard_details">Hazard details <span class="mhf-req">*</span></label>
        <input type="text" id="mh_hazard_details" name="hazard_details" class="mhf-control" maxlength="300" autocomplete="off" placeholder="Fill the Details">
        <div class="mhf-error" data-error-for="hazard_details"></div>
    </div>
</div>

<!-- ====== Consultation ====== -->
<div class="mhf-group">Consultation</div>

<!-- Line 5: consultation details -->
<div class="mhf-row">
    <div class="mhf-field" data-field="consultation_type" data-required="1">
        <label class="mhf-label" for="mh_consultation_type">Consultation type <span class="mhf-req">*</span></label>
        <select id="mh_consultation_type" name="consultation_type" class="mhf-control mhf-select">
            <option value="">Select</option>
            <option value="New">New</option>
            <option value="Follow-up">Follow-up</option>
            <option value="Emergency">Emergency</option>
        </select>
        <div class="mhf-error" data-error-for="consultation_type"></div>
    </div>

    <div class="mhf-field" data-field="source_of_history" data-required="1">
        <label class="mhf-label" for="mh_source_of_history">Source of history <span class="mhf-req">*</span></label>
        <select id="mh_source_of_history" name="source_of_history" class="mhf-control mhf-select">
            <option value="">Select</option>
            <option value="Patient">Patient</option>
            <option value="Parent">Parent</option>
            <option value="Guardian">Guardian</option>
            <option value="Caregiver">Caregiver</option>
            <option value="Other">Other</option>
        </select>
        <div class="mhf-error" data-error-for="source_of_history"></div>
    </div>

    <div class="mhf-field" data-field="reliability" data-required="1">
        <label class="mhf-label" for="mh_reliability">Reliability <span class="mhf-req">*</span></label>
        <select id="mh_reliability" name="reliability" class="mhf-control mhf-select">
            <option value="">Select</option>
            <option value="Good">Good</option>
            <option value="Fair">Fair</option>
            <option value="Poor">Poor</option>
        </select>
        <div class="mhf-error" data-error-for="reliability"></div>
    </div>

    <div class="mhf-field" data-field="urgency" data-required="1">
        <label class="mhf-label" for="mh_urgency">Urgency <span class="mhf-req">*</span></label>
        <select id="mh_urgency" name="urgency" class="mhf-control mhf-select">
            <option value="">Select</option>
            <option value="Routine">Routine</option>
            <option value="Urgent">Urgent</option>
            <option value="Emergency">Emergency</option>
        </select>
        <div class="mhf-error" data-error-for="urgency"></div>
    </div>
</div>
<?php
    return;
}

/* =====================================================================
 * MODE: SAVE  (server-side check - never trust the browser)
 * ===================================================================== */
if ($mh_mode === 'save') {
    $in = $mh_input;
    $d  = array();   // cleaned answers
    $e  = array();   // error messages  (field name => message)

    // ---- Line 1 ----
    $d['patient_name'] = mh_clean_text(isset($in['patient_name']) ? $in['patient_name'] : '', 120);
    if ($d['patient_name'] === '') {
        $e['patient_name'] = 'Enter the patient name.';
    }

    $dob = (isset($in['dob']) && is_string($in['dob'])) ? trim($in['dob']) : '';
    $age = mh_age_info($dob);
    if ($age === null) {
        $d['dob'] = '';
        $e['dob'] = 'Select a valid date of birth (it cannot be a future date).';
    } elseif ($age['years'] > 120) {
        $d['dob'] = '';
        $e['dob'] = 'Please check the date of birth.';
    } else {
        $d['dob'] = $dob;
    }

    // ---- Line 2 ----
    $d['sex'] = mh_pick(isset($in['sex']) ? $in['sex'] : '', array('Male', 'Female', 'Intersex', 'Other'), 'Male');

    $d['marital_status'] = mh_pick(isset($in['marital_status']) ? $in['marital_status'] : '', array('Yes', 'No'), '');
    if ($d['marital_status'] === '') {
        $e['marital_status'] = 'Select an option.';
    }

    // Pregnancy status exists only for Female AND Marital Status = Yes
    if ($d['sex'] === 'Female' && $d['marital_status'] === 'Yes') {
        $d['pregnancy_status'] = mh_pick(isset($in['pregnancy_status']) ? $in['pregnancy_status'] : '', array('No', 'Yes', 'Possibly'), 'No');
    } else {
        $d['pregnancy_status'] = '';
    }

    $d['religion'] = mh_pick(isset($in['religion']) ? $in['religion'] : '', array('Islam', 'Hindu', 'Christian', 'Buddhist', 'Others'), 'Islam');

    // ---- Line 3 : weight and height ----
    $w    = mh_number(isset($in['weight_kg']) ? $in['weight_kg'] : null);
    $ft   = mh_number(isset($in['height_ft']) ? $in['height_ft'] : null);
    $inch = mh_number(isset($in['height_in']) ? $in['height_in'] : null);

    if ($w === null || $w < 0.3 || $w > 500) {
        $e['weight_kg'] = 'Enter the weight in kg (between 0.3 and 500).';
        $d['weight_kg'] = '';
    } else {
        $d['weight_kg'] = round($w, 1);
    }

    if ($ft === null || $ft < 0 || $ft > 8 || floor($ft) != $ft) {
        $e['height_ft'] = 'Enter whole feet (0 to 8).';
        $d['height_ft'] = '';
    } else {
        $d['height_ft'] = (int)$ft;
    }

    if ($inch === null || $inch < 0 || $inch >= 12) {
        $e['height_in'] = 'Enter inches (0 to 11.9).';
        $d['height_in'] = '';
    } else {
        $d['height_in'] = round($inch, 1);
    }

    if (!isset($e['height_ft']) && !isset($e['height_in'])) {
        $total_in = mh_height_total_inches($d['height_ft'], $d['height_in']);
        if ($total_in < 10 || $total_in > 100) {
            $e['height_ft'] = 'Please check the height.';
        }
    }

    // ---- Line 4 ----
    $d['occupation'] = mh_pick(isset($in['occupation']) ? $in['occupation'] : '', array('Private Service', 'Government Service', 'Retired', 'Others'), '');
    if ($d['occupation'] === '') {
        $e['occupation'] = 'Select an option.';
    }
    if ($d['occupation'] === 'Others') {
        $d['occupation_other'] = mh_clean_text(isset($in['occupation_other']) ? $in['occupation_other'] : '', 100);
        if ($d['occupation_other'] === '') {
            $e['occupation_other'] = 'Write the occupation.';
        }
    } else {
        $d['occupation_other'] = '';
    }

    $d['occupational_hazard'] = mh_pick(isset($in['occupational_hazard']) ? $in['occupational_hazard'] : '', array('Yes', 'No'), 'No');
    if ($d['occupational_hazard'] === 'Yes') {
        $d['hazard_details'] = mh_clean_text(isset($in['hazard_details']) ? $in['hazard_details'] : '', 300);
        if ($d['hazard_details'] === '') {
            $e['hazard_details'] = 'Fill the details.';
        }
    } else {
        $d['hazard_details'] = '';
    }

    // ---- Line 5 ----
    $d['consultation_type'] = mh_pick(isset($in['consultation_type']) ? $in['consultation_type'] : '', array('New', 'Follow-up', 'Emergency'), '');
    if ($d['consultation_type'] === '') {
        $e['consultation_type'] = 'Select an option.';
    }

    $d['source_of_history'] = mh_pick(isset($in['source_of_history']) ? $in['source_of_history'] : '', array('Patient', 'Parent', 'Guardian', 'Caregiver', 'Other'), '');
    if ($d['source_of_history'] === '') {
        $e['source_of_history'] = 'Select an option.';
    }

    $d['reliability'] = mh_pick(isset($in['reliability']) ? $in['reliability'] : '', array('Good', 'Fair', 'Poor'), '');
    if ($d['reliability'] === '') {
        $e['reliability'] = 'Select an option.';
    }

    $d['urgency'] = mh_pick(isset($in['urgency']) ? $in['urgency'] : '', array('Routine', 'Urgent', 'Emergency'), '');
    if ($d['urgency'] === '') {
        $e['urgency'] = 'Select an option.';
    }

    $mh_result = array('data' => $d, 'errors' => $e);
    return;
}

/* =====================================================================
 * MODE: PDF  (this tab's page in the PDF)
 * ===================================================================== */
if ($mh_mode === 'pdf') {
    $d = $mh_data;
    $g = function ($key) use ($d) {
        return (isset($d[$key]) && !is_array($d[$key])) ? (string)$d[$key] : '';
    };

    // Age is calculated NOW from the date of birth, so it is always up to date
    $age = mh_age_info($g('dob'));
    $age_label = $age ? $age['label'] : '';
    $age_group = $age ? $age['group'] : '';
    $age_years = $age ? $age['years'] : null;

    $bmi = null;
    if ($g('weight_kg') !== '' && $g('height_ft') !== '') {
        $bmi = mh_bmi_info($g('weight_kg'), $g('height_ft'), $g('height_in') === '' ? 0 : $g('height_in'), $age_years);
    }

    $height_text = ($g('height_ft') !== '') ? ($g('height_ft') . ' ft ' . ($g('height_in') === '' ? '0' : $g('height_in')) . ' in') : '';
    $weight_text = ($g('weight_kg') !== '') ? ($g('weight_kg') . ' kg') : '';
    $bmi_text    = $bmi ? ($bmi['bmi'] === null ? $bmi['text'] : ($bmi['text'] . ' kg/m²')) : '';

    $occupation = $g('occupation');
    if ($occupation === 'Others' && $g('occupation_other') !== '') {
        $occupation = 'Others (' . $g('occupation_other') . ')';
    }

    echo '<div class="sec-title">' . (int)$mh_no . '. ' . mh_h($mh_title) . '</div>';

    // Patient details
    echo mh_pdf_group('Patient details');
    echo '<table class="kv">';
    echo '<tr>' . mh_pdf_pair('Patient Name', $g('patient_name')) . mh_pdf_pair('Date of Birth', mh_format_date($g('dob'))) . '</tr>';
    echo '<tr>' . mh_pdf_pair('Age', $age_label) . mh_pdf_pair('Age Group', $age_group) . '</tr>';
    echo '<tr>' . mh_pdf_pair('Sex', $g('sex')) . mh_pdf_pair('Marital Status', $g('marital_status')) . '</tr>';
    if ($g('sex') === 'Female' && $g('marital_status') === 'Yes') {
        echo '<tr>' . mh_pdf_pair('Religious Status', $g('religion')) . mh_pdf_pair('Pregnancy Status', $g('pregnancy_status')) . '</tr>';
    } else {
        echo '<tr>' . mh_pdf_pair('Religious Status', $g('religion'), 3) . '</tr>';
    }
    echo '</table>';

    // Body measurements
    echo mh_pdf_group('Body measurements');
    echo '<table class="kv">';
    echo '<tr>' . mh_pdf_pair('Weight', $weight_text) . mh_pdf_pair('Height', $height_text) . '</tr>';
    echo '<tr>' . mh_pdf_pair('BMI', $bmi_text, 3) . '</tr>';
    echo '</table>';

    // Occupation
    echo mh_pdf_group('Occupation');
    echo '<table class="kv">';
    echo '<tr>' . mh_pdf_pair('Occupation', $occupation) . mh_pdf_pair('Occupational Hazard', $g('occupational_hazard')) . '</tr>';
    if ($g('occupational_hazard') === 'Yes') {
        echo '<tr>' . mh_pdf_pair('Hazard Details', $g('hazard_details'), 3) . '</tr>';
    }
    echo '</table>';

    // Consultation
    echo mh_pdf_group('Consultation');
    echo '<table class="kv">';
    echo '<tr>' . mh_pdf_pair('Consultation Type', $g('consultation_type')) . mh_pdf_pair('Source of History', $g('source_of_history')) . '</tr>';
    echo '<tr>' . mh_pdf_pair('Reliability', $g('reliability')) . mh_pdf_pair('Urgency', $g('urgency')) . '</tr>';
    echo '</table>';
    return;
}
