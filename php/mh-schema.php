<?php
/**
 * TeleRx Bangladesh - Patient Medical History Form
 * "Schema" helpers: a tab that has MANY simple questions (tab 5) is written as a list of
 * fields, and these functions build the website form, check/clean the answers and
 * print the PDF from that same list - so the three can never disagree.
 *
 * One field = array(
 *   't'   => type:  'text' | 'date' | 'select' | 'auto' | 'radio' | 'checks' | 'textarea'
 *                   | 'grid' | 'ftable' | 'sub' | 'note'
 *   'k'   => answer key (unique inside the tab)
 *   'l'   => label
 *   'o'   => options (select / radio / checks / grid)
 *   'ph'  => placeholder,  'u' => unit shown after the box (kg, cm ...),  'w' => 'wide' | 'full'
 *   'other' => (checks only) key of an extra "Other: ____" text box
 *   'rows'  => (grid) key => label,  (ftable) key => array(label, placeholder)
 *   'head'  => (grid / ftable) the two column titles
 *   'src'   => (auto) value copied from Tab 1: name, pid, dob, age_full, age_years, sex, weight, height_cm, bmi
 * )
 * 'note' and the intro texts are shown on the website only (guidance), never printed in the PDF.
 *
 * Works with PHP 7.2 and newer.
 */

/* ---------------------------------------------------------------------
 * WHO IS THE PATIENT? (age / sex from the saved Tab 1)
 * ------------------------------------------------------------------- */

/** "5 years, 3 months, 2 days" */
function mh_age_full_text($info)
{
    if (!$info) {
        return '';
    }
    $parts = array();
    foreach (array('years' => 'year', 'months' => 'month', 'days' => 'day') as $k => $word) {
        if ($info[$k] > 0) {
            $parts[] = $info[$k] . ' ' . $word . ($info[$k] === 1 ? '' : 's');
        }
    }
    return count($parts) ? implode(', ', $parts) : '0 days';
}

/**
 * Which age/sex dependent parts apply to this patient.
 * @return array condition => true/false  (all, adult, female12, male12, child, senior)
 */
function mh_profile_flags($tab1)
{
    $tab1 = is_array($tab1) ? $tab1 : array();
    $age  = mh_age_info(isset($tab1['dob']) ? (string)$tab1['dob'] : '');
    $sex  = isset($tab1['sex']) ? (string)$tab1['sex'] : '';
    $y    = $age ? $age['years'] : null;
    return array(
        'all'      => true,
        'adult'    => $y !== null && $y >= 18,
        'female12' => $y !== null && $y >= 12 && $sex === 'Female',
        'male12'   => $y !== null && $y >= 12 && $sex === 'Male',
        'child'    => $y !== null && $y < 12,
        'senior'   => $y !== null && $y >= 60,
    );
}

/** Values of the 'auto' fields, worked out from the saved Tab 1. */
function mh_profile_values($tab1, $pid = '')
{
    $tab1 = is_array($tab1) ? $tab1 : array();
    $g = function ($k) use ($tab1) {
        return (isset($tab1[$k]) && !is_array($tab1[$k])) ? (string)$tab1[$k] : '';
    };
    $age = mh_age_info($g('dob'));
    $height_cm = '';
    if ($g('height_ft') !== '') {
        $height_cm = (string)round(mh_height_total_inches($g('height_ft'), $g('height_in') === '' ? 0 : $g('height_in')) * 2.54, 1);
    }
    $bmi = null;
    if ($g('weight_kg') !== '' && $g('height_ft') !== '') {
        $bmi = mh_bmi_info($g('weight_kg'), $g('height_ft'), $g('height_in') === '' ? 0 : $g('height_in'), $age ? $age['years'] : null);
    }
    return array(
        'name'      => $g('patient_name'),
        'pid'       => (string)$pid,
        'dob'       => mh_format_date($g('dob')),
        'age_full'  => mh_age_full_text($age),
        'age_years' => $age ? $age['years'] . ' years' : '',
        'sex'       => $g('sex'),
        'weight'    => $g('weight_kg') !== '' ? $g('weight_kg') . ' kg' : '',
        'height_cm' => $height_cm !== '' ? $height_cm . ' cm' : '',
        'bmi'       => $bmi ? $bmi['text'] : '',
    );
}

/* ---------------------------------------------------------------------
 * FORM (website)
 * ------------------------------------------------------------------- */
function mh_sc_id($k)
{
    return 'mh5_' . preg_replace('/[^a-z0-9_]/i', '_', $k);
}

function mh_sc_kind($t)
{
    if (in_array($t, array('text', 'date', 'select', 'auto'), true)) {
        return 'row';
    }
    if (in_array($t, array('radio', 'checks'), true)) {
        return 'q';
    }
    return '';
}

/** HTML of one field. */
function mh_sc_field_form($f)
{
    $t  = $f['t'];
    $k  = isset($f['k']) ? $f['k'] : '';
    $id = mh_sc_id($k);
    $l  = isset($f['l']) ? $f['l'] : '';
    $ph = isset($f['ph']) ? $f['ph'] : '';
    $err = '<div class="mhf-error" data-error-for="' . mh_h($k) . '"></div>';

    if ($t === 'sub') {
        return '<div class="mhf-subhead">' . mh_h($l) . '</div>';
    }
    if ($t === 'note') {
        return '<p class="mhf-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> ' . mh_h($f['text']) . '</p>';
    }

    if (mh_sc_kind($t) === 'row') {
        $w = isset($f['w']) ? ($f['w'] === 'full' ? ' mhf-full' : ' mhf-grow-22') : '';
        $tag = $t === 'auto' ? ' <span class="mhf-tag">auto</span>' : '';
        $html = '<div class="mhf-field' . $w . '" data-field="' . mh_h($k) . '"><label class="mhf-label" for="' . $id . '">' . mh_h($l) . $tag . '</label>';
        if ($t === 'select') {
            $html .= '<select id="' . $id . '" name="' . mh_h($k) . '" class="mhf-control mhf-select"><option value="">Select</option>';
            foreach ($f['o'] as $o) {
                $html .= '<option value="' . mh_h($o) . '">' . mh_h($o) . '</option>';
            }
            $html .= '</select>';
        } elseif ($t === 'date') {
            $html .= '<input type="date" id="' . $id . '" name="' . mh_h($k) . '" class="mhf-control" max="' . date('Y-m-d') . '" min="1900-01-01">';
        } elseif ($t === 'auto') {
            $html .= '<input type="text" id="' . $id . '" class="mhf-control mhf-auto" readonly tabindex="-1" aria-readonly="true" data-auto="' . mh_h($f['src']) . '" placeholder="&mdash;">';
        } else {
            $input = '<input type="text" id="' . $id . '" name="' . mh_h($k) . '" class="mhf-control" maxlength="' . (isset($f['max']) ? (int)$f['max'] : 200) . '" placeholder="' . mh_h($ph) . '" autocomplete="off">';
            $html .= isset($f['u']) ? '<div class="mhf-unit">' . $input . '<span class="mhf-unit-text">' . mh_h($f['u']) . '</span></div>' : $input;
        }
        return $html . $err . '</div>';
    }

    if (mh_sc_kind($t) === 'q') {
        $html = '<div class="mhf-q" data-field="' . mh_h($k) . '"><div class="mhf-q-label">' . mh_h($l) . '</div><div class="mhf-q-body"><div class="mhf-checks">';
        foreach ($f['o'] as $o) {
            $html .= $t === 'radio' ? mh_form_check('radio', $k, $o, $o) : mh_form_check('checkbox', $k . '[]', $o, $o);
        }
        $html .= '</div>';
        if (!empty($f['other'])) {
            $html .= '<input type="text" name="' . mh_h($f['other']) . '" class="mhf-control mhf-q-other" maxlength="200" placeholder="' . mh_h($ph !== '' ? $ph : 'Other (please specify)') . '" aria-label="' . mh_h($l) . ' - other">';
        }
        return $html . $err . '</div></div>';
    }

    if ($t === 'textarea') {
        return '<div class="mhf-row"><div class="mhf-field mhf-full" data-field="' . mh_h($k) . '"><label class="mhf-label" for="' . $id . '">' . mh_h($l) . '</label>'
             . (isset($f['hint']) ? '<div class="mhf-hint mhf-hint-top">' . mh_h($f['hint']) . '</div>' : '')
             . '<textarea id="' . $id . '" name="' . mh_h($k) . '" class="mhf-control mhf-textarea" rows="3" maxlength="1500" placeholder="' . mh_h($ph) . '"></textarea>' . $err . '</div></div>';
    }

    if ($t === 'grid' || $t === 'ftable') {
        $head = isset($f['head']) ? $f['head'] : array('Activity', 'Status');
        $html = (isset($f['l']) && $f['l'] !== '' ? '<div class="mhf-label mhf-mt">' . mh_h($l) . '</div>' : '')
              . '<div class="mhf-table-wrap"><table class="mhf-table mhf-sc-table"><thead><tr><th style="width:' . ($t === 'grid' ? '42' : '30') . '%">' . mh_h($head[0]) . '</th><th>' . mh_h($head[1]) . '</th></tr></thead><tbody>';
        foreach ($f['rows'] as $rk => $row) {
            $label = is_array($row) ? $row[0] : $row;
            $html .= '<tr><th scope="row">' . mh_h($label) . '</th><td>';
            if ($t === 'grid') {
                $html .= '<div class="mhf-checks">';
                foreach ($f['o'] as $o) {
                    $html .= mh_form_check('radio', $k . '[' . $rk . ']', $o, $o);
                }
                $html .= '</div>';
            } else {
                $html .= '<input type="text" name="' . mh_h($k . '[' . $rk . ']') . '" class="mhf-control" maxlength="300" placeholder="' . mh_h($row[1]) . '" aria-label="' . mh_h($label) . '">';
            }
            $html .= '</td></tr>';
        }
        return $html . '</tbody></table></div>';
    }
    return '';
}

/** HTML of a list of fields: text boxes side by side, choice questions as a neat list. */
function mh_sc_form($fields)
{
    $out = '';
    $open = '';
    foreach ($fields as $f) {
        $kind = mh_sc_kind($f['t']);
        if ($kind !== $open) {
            if ($open !== '') {
                $out .= '</div>';
            }
            if ($kind === 'row') {
                $out .= '<div class="mhf-row">';
            } elseif ($kind === 'q') {
                $out .= '<div class="mhf-qlist">';
            }
            $open = $kind;
        }
        $out .= mh_sc_field_form($f);
    }
    if ($open !== '') {
        $out .= '</div>';
    }
    return $out;
}

/** One block (e.g. "Pediatric History"); shown only when its condition is true for the patient. */
function mh_sc_block_form($block)
{
    $cond = $block['cond'] === 'all' ? '' : ' data-cond="' . mh_h($block['cond']) . '"';
    $html = '<div class="mhf-sc-block"' . $cond . '><div class="mhf-group">' . mh_h($block['title']) . '</div>';
    if (!empty($block['intro'])) {
        $html .= '<p class="mhf-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> ' . mh_h($block['intro']) . '</p>';
    }
    return $html . mh_sc_form($block['fields']) . '</div>';
}

/* ---------------------------------------------------------------------
 * SAVE (server-side check)
 * ------------------------------------------------------------------- */
function mh_sc_save($fields, $in, &$d, &$e)
{
    foreach ($fields as $f) {
        $t = $f['t'];
        if (!isset($f['k']) || in_array($t, array('auto', 'sub', 'note'), true)) {
            continue;
        }
        $k = $f['k'];
        $v = isset($in[$k]) ? $in[$k] : null;
        if ($t === 'select' || $t === 'radio') {
            $d[$k] = mh_pick($v, $f['o'], '');
        } elseif ($t === 'checks') {
            $d[$k] = mh_pick_many($v, $f['o']);
            if (!empty($f['other'])) {
                $d[$f['other']] = mh_clean_text(isset($in[$f['other']]) ? $in[$f['other']] : '', 200);
            }
        } elseif ($t === 'textarea') {
            $d[$k] = mh_clean_multiline($v, 1500);
        } elseif ($t === 'date') {
            $iso = is_string($v) ? trim($v) : '';
            $d[$k] = '';
            if ($iso !== '') {
                $age = mh_age_info($iso);   // valid, not in the future
                if ($age === null) {
                    $e[$k] = 'Select a valid date (it cannot be a future date).';
                } else {
                    $d[$k] = $iso;
                }
            }
        } elseif ($t === 'grid' || $t === 'ftable') {
            $d[$k] = array();
            $raw = is_array($v) ? $v : array();
            foreach ($f['rows'] as $rk => $row) {
                $rv = isset($raw[$rk]) ? $raw[$rk] : '';
                $d[$k][$rk] = $t === 'grid' ? mh_pick($rv, $f['o'], '') : mh_clean_text($rv, 300);
            }
        } else {
            $d[$k] = mh_clean_text($v, isset($f['max']) ? (int)$f['max'] : 200);
        }
    }
}

/* ---------------------------------------------------------------------
 * PDF
 * ------------------------------------------------------------------- */

/** Prints label/value pairs two per line. */
function mh_sc_pdf_pairs($pairs)
{
    if (count($pairs) === 0) {
        return '';
    }
    $html = '<table class="kv">';
    for ($i = 0; $i < count($pairs); $i += 2) {
        $html .= '<tr>' . mh_pdf_pair($pairs[$i][0], $pairs[$i][1]);
        $html .= isset($pairs[$i + 1]) ? mh_pdf_pair($pairs[$i + 1][0], $pairs[$i + 1][1]) : '<td class="k"></td><td class="v"></td>';
        $html .= '</tr>';
    }
    return $html . '</table>';
}

function mh_sc_pdf($fields, $d, $auto)
{
    $out = '';
    $pairs = array();
    $get = function ($k) use ($d) {
        return isset($d[$k]) ? $d[$k] : '';
    };
    foreach ($fields as $f) {
        $t = $f['t'];
        $k = isset($f['k']) ? $f['k'] : '';
        $l = isset($f['l']) ? $f['l'] : '';
        if ($t === 'note') {
            continue;
        }
        if (in_array($t, array('text', 'date', 'select', 'radio', 'auto'), true)) {
            if ($t === 'auto') {
                $val = isset($auto[$f['src']]) ? $auto[$f['src']] : '';
            } elseif ($t === 'date') {
                $val = mh_format_date($get($k));
            } else {
                $val = is_array($get($k)) ? '' : (string)$get($k);
                if ($val !== '' && isset($f['u'])) {
                    $val .= ' ' . $f['u'];
                }
            }
            $pairs[] = array($l, $val);
            continue;
        }
        // everything else needs the full width -> print the pairs collected so far
        $out .= mh_sc_pdf_pairs($pairs);
        $pairs = array();

        if ($t === 'sub') {
            $out .= '<div class="sub">' . mh_h($l) . '</div>';
        } elseif ($t === 'checks') {
            $list = is_array($get($k)) ? $get($k) : array();
            if (!empty($f['other']) && trim((string)$get($f['other'])) !== '') {
                $list[] = 'Other: ' . $get($f['other']);
            }
            $out .= mh_pdf_wide(array(array($l, count($list) ? mh_h(implode(', ', $list)) : '&mdash;')));
        } elseif ($t === 'textarea') {
            $out .= mh_pdf_wide(array(array($l, mh_pdf_text($get($k)))));
        } elseif ($t === 'grid' || $t === 'ftable') {
            $head = isset($f['head']) ? $f['head'] : array('Activity', 'Status');
            $vals = is_array($get($k)) ? $get($k) : array();
            $rows = array();
            foreach ($f['rows'] as $rk => $row) {
                $rows[] = array('<b>' . mh_h(is_array($row) ? $row[0] : $row) . '</b>', mh_h(isset($vals[$rk]) ? $vals[$rk] : ''));
            }
            if ($l !== '') {
                $out .= '<div class="sub">' . mh_h($l) . '</div>';
            }
            $out .= mh_pdf_table(array($head[0] => ($t === 'grid' ? '42%' : '30%'), $head[1] => ($t === 'grid' ? '58%' : '70%')), $rows);
        }
    }
    return $out . mh_sc_pdf_pairs($pairs);
}
