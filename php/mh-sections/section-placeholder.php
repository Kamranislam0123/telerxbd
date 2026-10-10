<?php
/**
 * DEMO TAB (used for Tab 2, 3, 4, 5, 6 until the real fields are made)
 *
 * To turn a demo tab into a real tab:
 *   1. copy section-1.php to section-N.php (N = tab number) and change the fields
 *   2. in php/mh-helpers.php, inside mh_sections(), set that tab's 'file' => 'section-N.php'
 *      and give it its real title.
 */

if ($mh_mode === 'form') {
?>
<div class="mhf-placeholder">
    <div class="mhf-placeholder-icon" aria-hidden="true"><i class="fa-regular fa-clipboard"></i></div>
    <h3 class="mhf-placeholder-title"><?php echo mh_h($mh_title); ?></h3>
    <p class="mhf-placeholder-text">The questions for this tab will be added soon. You can continue to the next step.</p>
</div>
<?php
    return;
}

if ($mh_mode === 'save') {
    // Nothing to collect yet
    $mh_result = array('data' => array(), 'errors' => array());
    return;
}

if ($mh_mode === 'pdf') {
    echo '<div class="sec-title">' . (int)$mh_no . '. ' . mh_h($mh_title) . '</div>';
    echo '<div class="empty-note">No information has been collected in this section yet.</div>';
    return;
}
