<?php
/**
 * Generate the Patient Medical History PDF (A4, one page per tab, font size 10).
 *
 *   php/mh-generate-pdf.php?form_id=12              -> opens the PDF in the browser
 *   php/mh-generate-pdf.php?form_id=12&download=1   -> downloads the PDF
 *
 * The PDF is created fresh every time from the saved answers (nothing is stored on
 * the server), so the age is always correct and nobody can open it by guessing a file name.
 * Uses mPDF - the same library the prescription PDF already uses.
 */

ini_set('display_errors', '0');

require_once __DIR__ . '/mh-helpers.php';
mh_db_strict(true);

function mh_pdf_fail($message, $status = 400)
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code($status);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Medical History PDF</title></head>'
        . '<body style="font-family:Arial,sans-serif;padding:40px;color:#1f2d3d;max-width:640px;margin:0 auto">'
        . '<h2 style="margin-top:0;color:#15558d">Unable to create the PDF</h2>'
        . '<p style="line-height:1.6">' . mh_h($message) . '</p>'
        . '<p><a href="javascript:history.back()" style="color:#0e82fd">Go back</a></p></body></html>';
    exit;
}

try {
    // 1) Login
    $patient_id = mh_current_patient_id();
    if ($patient_id <= 0) {
        header('Location: ' . ((defined('APP_BASE') && APP_BASE) ? APP_BASE : '') . '/login.php');
        exit;
    }

    // 2) mPDF must be installed (the prescription PDF already needs it)
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) {
        mh_pdf_fail('The PDF library (mPDF) was not found in the "vendor" folder of the website.', 500);
    }
    require_once $autoload;
    if (!class_exists('\Mpdf\Mpdf')) {
        mh_pdf_fail('The PDF library (mPDF) is not installed on this website.', 500);
    }

    // 3) Which form?
    $form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
    if ($form_id <= 0) {
        mh_pdf_fail('No form was selected.');
    }

    $conn = getDBConnection();
    if (!mh_has_access($conn, $patient_id)) {
        mh_pdf_fail('The medical history form is available for Premium subscribers.', 403);
    }
    if (!mh_tables_ready($conn)) {
        mh_pdf_fail(mh_setup_message(), 500);
    }

    $form = mh_get_form($conn, $form_id, $patient_id);   // only the owner can open it
    if (!$form) {
        mh_pdf_fail('This form was not found.', 404);
    }

    $sections = mh_sections();
    $data     = mh_get_sections($conn, $form_id);
    $conn->close();

    foreach ($sections as $no => $meta) {
        if (!isset($data[$no])) {
            mh_pdf_fail('Please complete and save all tabs before creating the PDF. Tab ' . $no . ' is not saved yet.');
        }
    }

    // 4) Header and footer values
    $tab1         = isset($data[1]) ? $data[1] : array();
    $patient_name = isset($tab1['patient_name']) ? (string)$tab1['patient_name'] : '';
    $form_no      = 'MH-' . str_pad((string)$form_id, 6, '0', STR_PAD_LEFT);
    $patient_code = 'PT' . str_pad((string)$patient_id, 6, '0', STR_PAD_LEFT);
    $relationship = isset($form['relationship']) ? (string)$form['relationship'] : 'Self';
    // Own form -> "PT000053";  family member's form -> "Spouse of PT000053"
    $id_text      = ($relationship === 'Self' || $relationship === '') ? $patient_code : ($relationship . ' of ' . $patient_code);
    $generated_at = date('d M Y, h:i A');

    $logo_html = '<span style="font-size:14pt;font-weight:bold;color:#15558d;">TeleRx Bangladesh</span>';
    $logo_path = realpath(__DIR__ . '/../assets/img/logo.png');
    if ($logo_path && file_exists($logo_path)) {
        $logo_bytes = @file_get_contents($logo_path);
        if ($logo_bytes !== false) {
            $logo_html = '<img src="data:image/png;base64,' . base64_encode($logo_bytes) . '" style="height:11mm;" />';
        }
    }

    $header_html =
        '<table width="100%" style="border-bottom:1.5px solid #15558d;"><tr>'
        . '<td width="35%" style="vertical-align:middle;padding-bottom:5px;">' . $logo_html . '</td>'
        . '<td width="65%" style="text-align:right;vertical-align:middle;padding-bottom:5px;">'
        . '<div style="font-size:14pt;font-weight:bold;color:#15558d;">Patient Medical History</div>'
        . '<div style="font-size:10pt;color:#475569;">' . mh_h($patient_name) . ' &nbsp;|&nbsp; ' . mh_h($form_no) . ' &nbsp;|&nbsp; ' . mh_h($id_text) . '</div>'
        . '</td></tr></table>';

    $footer_html =
        '<table width="100%" style="border-top:1px solid #cbd5e1;font-size:8pt;color:#64748b;"><tr>'
        . '<td width="75%" style="padding-top:3px;">TeleRx Bangladesh &nbsp;|&nbsp; www.telerxbd.com &nbsp;|&nbsp; Emergency Call: 01335053237<br />'
        . 'Confidential medical document &nbsp;|&nbsp; Generated: ' . mh_h($generated_at) . '</td>'
        . '<td width="25%" style="text-align:right;padding-top:3px;">Page {PAGENO} of {nb}</td>'
        . '</tr></table>';

    $css = '
        body { font-family: hindsiliguri; font-size: 10pt; color: #1f2d3d; line-height: 1.35; }
        .sec-title { background-color: #15558d; color: #ffffff; font-size: 10pt; font-weight: bold; padding: 6px 10px; margin-bottom: 4px; }
        .grp { font-size: 10pt; font-weight: bold; color: #15558d; border-bottom: 1px solid #15558d; padding-bottom: 2px; margin-top: 12px; margin-bottom: 5px; }
        table.kv { width: 100%; border-collapse: collapse; }
        table.kv td { border: 1px solid #d3dce8; padding: 5px 7px; font-size: 10pt; vertical-align: top; }
        table.kv td.k { background-color: #eef4fb; color: #334155; font-weight: bold; width: 22%; }
        table.kv td.v { width: 28%; color: #111827; }
        .empty-note { border: 1px dashed #cbd5e1; padding: 18px; text-align: center; color: #64748b; margin-top: 14px; font-size: 10pt; }
    ';

    // 5) mPDF (same setup as the prescription PDF)
    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs      = $defaultConfig['fontDir'];
    $fontDirs[]    = __DIR__ . '/../assets/fonts';

    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData          = $defaultFontConfig['fontdata'];
    $fontData['hindsiliguri'] = array(
        'R'      => 'HindSiliguri-Regular.ttf',
        'B'      => 'HindSiliguri-Bold.ttf',
        'useOTL' => 0xFF,
    );

    $mpdf = new \Mpdf\Mpdf(array(
        'mode'              => 'utf-8',
        'format'            => 'A4',
        'margin_left'       => 15,
        'margin_right'      => 15,
        'margin_top'        => 33,
        'margin_bottom'     => 22,
        'margin_header'     => 8,
        'margin_footer'     => 8,
        'fontDir'           => $fontDirs,
        'fontdata'          => $fontData,
        'default_font'      => 'hindsiliguri',
        'default_font_size' => 10,
        'autoScriptToLang'  => true,
        'autoLangToFont'    => true,
    ));
    $mpdf->SetTitle('Patient Medical History - ' . $form_no);
    $mpdf->SetAuthor('TeleRx Bangladesh');
    $mpdf->SetHTMLHeader($header_html);
    $mpdf->SetHTMLFooter($footer_html);
    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

    // 6) One tab = one page, in order
    $is_first = true;
    foreach ($sections as $no => $meta) {
        $page = mh_run_section($no, 'pdf', $data[$no]);
        if (!$is_first) {
            $mpdf->AddPage();
        }
        $mpdf->WriteHTML($page['html'], \Mpdf\HTMLParserMode::HTML_BODY);
        $is_first = false;
    }

    $pdf = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

    // 7) Send it to the browser
    $filename    = 'TeleRx_Medical_History_' . $form_no . '.pdf';
    $disposition = (isset($_GET['download']) && $_GET['download'] === '1') ? 'attachment' : 'inline';

    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/pdf');
    header('Content-Disposition: ' . $disposition . '; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($pdf));
    header('Cache-Control: private, no-store, max-age=0');
    echo $pdf;
    exit;
} catch (Throwable $e) {
    error_log('[mh-generate-pdf] ' . $e->getMessage());
    mh_pdf_fail(MH_DEBUG ? ('Error: ' . $e->getMessage()) : 'Something went wrong while creating the PDF. Please try again.', 500);
}
