<?php
/**
 * Generate Prescription PDF
 * Fetches appointment and doctor data to render a professional prescription.
 */

// Enable error display for debugging in live
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

// Using mPDF for PDF generation (pure PHP, works on shared hosting) with proper Bengali (complex text) shaping

function debug_log($message) {
    $dir = __DIR__ . '/../assets/prescriptions/';
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
    @file_put_contents($dir . 'debug.log', "[" . date('Y-m-d H:i:s') . "] " . $message . "\n", FILE_APPEND);
}

debug_log("=== STARTING PRESCRIPTION GENERATION ===");

if (!isset($_GET['appointment_id'])) {
    debug_log("Error: Appointment ID missing in GET request.");
    die("Appointment ID is required.");
}

$appointment_id = (int)$_GET['appointment_id'];

try {
    debug_log("Connecting to database...");
    $conn = getDBConnection();
    
    debug_log("Fetching appointment data for ID: $appointment_id...");
    // 1. Fetch Appointment Data
    $stmt = $conn->prepare("SELECT a.*, p.gender FROM appointments a LEFT JOIN patients p ON a.patient_id = p.id WHERE a.id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $appointment = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$appointment) {
        die("Appointment not found.");
    }

    // 2. Fetch Doctor Data
    $doctor_id = $appointment['doctor_id'];
    $stmt = $conn->prepare("SELECT d.*, dp.specialty, dp.bio, dc.clinic_logo FROM doctors d 
                            LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id 
                            LEFT JOIN doctor_clinics dc ON d.id = dc.doctor_id
                            WHERE d.id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $doctor = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // 3. Fetch Doctor Education (Degrees)
    $stmt = $conn->prepare("SELECT degree FROM doctor_education WHERE doctor_id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $education_result = $stmt->get_result();
    $degrees = [];
    while ($row = $education_result->fetch_assoc()) {
        $degrees[] = $row['degree'];
    }
    $stmt->close();
    $doctor_degrees = implode(', ', $degrees);

    // 4. Process Medications (stored as JSON)
    $medications = json_decode($appointment['medications'], true) ?: [];

    // 5. Prepare Date
    $prescription_date = date('d M Y', strtotime($appointment['appointment_date']));
    $generated_at = date('d M Y, h:i A');

    // 6. Generate HTML Content (Matches the requested design)
    // Dynamic Logo Logic
    $logo_data = '';
    $logo_path = '';
    
    if (!empty($doctor['clinic_logo'])) {
        $logo_path = realpath(__DIR__ . '/../' . $doctor['clinic_logo']);
    }
    
    if (empty($logo_path) || !file_exists($logo_path)) {
        // Fallback to logo.png for better PDF compatibility
        $logo_path = realpath(__DIR__ . '/../assets/img/logo.png');
    }

    debug_log("Logo path resolved to: " . ($logo_path ?: 'NOT FOUND'));

    // Convert to base64 for reliable PDF rendering
    if ($logo_path && file_exists($logo_path)) {
        debug_log("Converting logo to base64...");
        $type = pathinfo($logo_path, PATHINFO_EXTENSION);
        $data = @file_get_contents($logo_path);
        if ($data !== false) {
            $logo_data = 'data:image/' . ($type === 'svg' ? 'svg+xml' : $type) . ';base64,' . base64_encode($data);
        }
    }

    $is_html_preview = isset($_GET['preview']) && $_GET['preview'] === 'html';
    $page_content_height = '235mm';

    $html = '
    <!DOCTYPE html>
    <html lang="bn">
    <head>
        <meta charset="UTF-8">
        <style>
            @font-face {
                font-family: "HindSiliguri";
                src: url("../assets/fonts/HindSiliguri-Regular.ttf") format("truetype");
                font-weight: normal;
            }
            @font-face {
                font-family: "HindSiliguri";
                src: url("../assets/fonts/HindSiliguri-Bold.ttf") format("truetype");
                font-weight: bold;
            }
            body { font-family: hindsiliguri, "HindSiliguri", sans-serif; color: #000; line-height: 1.3; font-size: 10pt; margin: 0; padding: 0; }
            .page-container { position: relative; min-height: ' . $page_content_height . '; width: 100%; }
            .page-content { width: 100%; box-sizing: border-box; }
            .footer { width: 100%; border-top: 1px solid #000; padding-top: 8px; page-break-inside: avoid; }
            
            /* Header Section */
            .header { width: 100%; border-bottom: 1px solid #000; padding-bottom: 8px;  position: relative; }
            .doctor-info { float: left; width: 65%; }
            .doctor-name { font-size: 14pt; font-weight: bold; margin: 0; text-transform: uppercase; font-family: hindsiliguri; }
            .doctor-details { font-size: 9pt; margin: 1px 0; text-transform: uppercase; font-weight: bold; }
            .logo-info { float: right; width: 30%; text-align: right; }
            .logo-info img { width: 140px; height: auto; }
            .slogan { font-size: 8pt; color: #15558d; margin-top: 3px; font-weight: bold; }
            .clear { clear: both; }

            /* Patient Info Section */
            .patient-info { width: 100%; solid #000; padding: 12px 0; margin-bottom: 0px; font-size: 10pt; }
            .info-row { width: 100%; margin-bottom: 10px; }
            .info-item { display: inline-block; }
            .info-label { font-weight: bold; }
            .info-value { border-bottom: 1px solid #000; display: inline-block; padding: 0 5px; margin-right: 15px; }
            
            /* Prescription Body */
            .prescription-body { width: 100%; display: table; border-top: 1px solid #000; }
            .sidebar { display: table-cell; width: 25%; border-right: 1px solid #000; vertical-align: top; padding-right: 15px; padding-top: 15px; height: 600px; }
            .main-rx { display: table-cell; width: 75%; vertical-align: top; padding-left: 25px; padding-top: 15px; }
            
            .section-header { font-weight: bold; text-decoration: underline; padding-bottom: 5px; padding-top: 15px; font-size: 10pt; }
            .section-content { font-size: 9pt; padding-bottom: 20px; }
            
            .rx-symbol { font-size: 18pt; font-family: hindsiliguri; font-weight: bold; margin-bottom: 20px; }
            .medication-item { margin-bottom: 15px; font-size: 11pt; }
            .med-name { font-weight: bold; }
            .med-instruction { font-size: 9pt; font-style: italic; margin-left: 20px; margin-top: 3px; }

            /* Footer Section — pinned to bottom via .page-container */
            .footer-line
            .badges { width: 100%; text-align: center; margin-bottom: 15px; }
            .badge { 
                display: inline-block; 
                background: #c9302c; 
                color: #fff; 
                padding: 5px 12px; 
                border-radius: 4px; 
                font-size: 9pt; 
                margin: 0 8px; 
                font-weight: bold;
            }
            .address-box { 
                border: 2px solid #000; 
                background: #f4f4f4; 
                padding: 12px; 
                text-align: center; 
                font-size: 10pt;
                margin-top: 18px;
            }
            .address-title { font-weight: bold; font-size: 12pt; margin-bottom: 5px; }
            .note-reference-box {
                width: 100%;
                margin: 10px 0 14px 0;
                padding: 8px 10px;
                border: 1px dashed #999;
                border-radius: 6px;
                text-align: center;
                background: #fafafa;
                page-break-inside: avoid;
            }
            .note-reference-title {
                font-weight: bold;
                text-decoration: underline;
                margin-bottom: 4px;
                font-size: 9pt;
            }
            .note-reference-content {
                font-size: 9pt;
                line-height: 1.45;
            }
            .follow-up-text {
                margin-top: 28px;
                font-size: 10pt;
                font-weight: bold;
                line-height: 1.5;
            }
            
            /* Bengali Support */
            .bn { font-family: hindsiliguri; }
            
            .disclaimer { 
                text-align: center; 
                font-size: 8pt; 
                color: #555; 
                margin-top: 10px; 
                padding-top: 5px; 
                border-top: 1px solid #eee;
                line-height: 1.5;
                font-family: hindsiliguri;
            }
            .generated-time {
                text-align: right;
                font-size: 8pt;
                color: #666;
                margin-top: 8px;
                font-family: hindsiliguri;
            }

            /* HTML preview chrome (browser only) */
            .preview-toolbar {
                position: sticky;
                top: 0;
                z-index: 100;
                background: #1e293b;
                color: #fff;
                padding: 12px 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                font-family: system-ui, sans-serif;
                font-size: 14px;
            }
            .preview-toolbar a, .preview-toolbar button {
                background: #2563eb;
                color: #fff;
                border: none;
                border-radius: 6px;
                padding: 8px 14px;
                text-decoration: none;
                cursor: pointer;
                font-size: 13px;
            }
            .preview-toolbar a.secondary, .preview-toolbar button.secondary {
                background: #475569;
            }
            .preview-canvas {
                background: #e2e8f0;
                min-height: 100vh;
                padding: 24px 16px 40px;
            }
            .preview-page {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #fff;
                box-shadow: 0 4px 24px rgba(15, 23, 42, 0.15);
                padding: 15mm;
                box-sizing: border-box;
            }
            .preview-mpdf-footer {
                width: 210mm;
                margin: 0 auto;
                padding: 4px 15mm 0;
                box-sizing: border-box;
                border-top: 1px solid #000;
                text-align: center;
                font-size: 8pt;
                color: #555;
                line-height: 1.5;
            }
        </style>
    </head>
    <body>';

    if ($is_html_preview) {
        $pdf_url = 'generate-prescription.php?appointment_id=' . $appointment_id;
        $html .= '
        <div class="preview-toolbar">
            <span><strong>Prescription design preview</strong> — Appointment #' . $appointment_id . '</span>
            <span>
                <button type="button" class="secondary" onclick="location.reload()">Refresh</button>
                <a href="' . htmlspecialchars($pdf_url) . '" target="_blank">Open PDF</a>
            </span>
        </div>
        <div class="preview-canvas">
            <div class="preview-page">';
    }

    $html .= '
        <div class="page-container">
        <div class="page-content">
        <div class="header">
            <div class="doctor-info">
                <p class="doctor-name">DR. ' . htmlspecialchars(strtoupper($doctor['name'])) . '</p>
                <p class="doctor-details">' . htmlspecialchars($doctor_degrees ?: "MBBS, DMU") . '</p>
                <p class="doctor-details">' . htmlspecialchars(strtoupper($doctor['specialty'] ?: "GENERAL PHYSICIAN")) . '</p>
                <p class="doctor-details">BMDC Reg. No: ' . htmlspecialchars($doctor['bmdc_no'] ?: "—") . '</p>
                <p class="doctor-details">TELERxBD.COM</p>
            </div>
            <div class="logo-info">
                <img src="' . $logo_data . '" alt="Logo" style="width: 140px; height: auto;">
                <p class="slogan">Care Beyond Distance</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="patient-info" style="padding: 5px 0; margin-bottom: 0px; font-size: 10pt;">
            <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 10pt;">
                <tr>
                    <td width="40px"><b>Name:</b></td>
                    <td style="padding:3px 0 3px 5px;">&nbsp;' . htmlspecialchars($appointment['patient_name']) . '</td>
                    <td width="20px"></td>
                    <td width="30px"><b>Age:</b></td>
                    <td width="45px" style="text-align: center;">' . htmlspecialchars($appointment['age'] ?: "—") . '</td>
                    <td width="20px"></td>
                    <td width="30px"><b>Sex:</b></td>
                    <td width="45px" style=" text-align: center;">' . htmlspecialchars($appointment['gender'] ?: "M / F") . '</td>
                    <td width="20px"></td>
                    <td width="25px"><b>Wt:</b></td>
                    <td width="60px" style=" text-align: center;">' . htmlspecialchars($appointment['weight'] ?: "—") . ' kg</td>
                    <td width="20px"></td>
                    <td width="35px"><b>Date:</b></td>
                    <td width="100px" style="text-align: center;">' . $prescription_date . '</td>
                </tr>
            </table>
        </div>

        <table class="prescription-body" width="100%" cellpadding="0" cellspacing="0" style="border-top: 1px solid #000; margin-top: 0px;">
            <tr>
            <td class="sidebar" width="30%" valign="top" style="border-right: 1px solid #000; padding-right: 15px; padding-top: 15px;">';

                $chief = isset($appointment['chief_complaints']) ? trim((string)$appointment['chief_complaints']) : '';
                if ($chief !== '' && $chief !== '-') {
                    $html .= '
                <div class="section-header">Chief Complaints:</div>
                <div class="section-content">' . nl2br(htmlspecialchars($chief)) . '<br><br></div>';
                }
                
                $on_exam_content = '';
                $bp = isset($appointment['blood_pressure']) ? trim((string)$appointment['blood_pressure']) : '';
                if ($bp !== '' && $bp !== '-' && $bp !== '0') $on_exam_content .= "BP: {$bp} mmHg<br>";
                
                $temp = isset($appointment['body_temperature']) ? trim((string)$appointment['body_temperature']) : '';
                if ($temp !== '' && $temp !== '-' && $temp !== '0') $on_exam_content .= "Temp: {$temp} F<br>";
                
                $pulse = isset($appointment['pulse']) ? trim((string)$appointment['pulse']) : '';
                if ($pulse !== '' && $pulse !== '-' && $pulse !== '0') $on_exam_content .= "Pulse: {$pulse} /min<br>";
                
                $spo2 = isset($appointment['spo2']) ? trim((string)$appointment['spo2']) : '';
                if ($spo2 !== '' && $spo2 !== '-' && $spo2 !== '0') $on_exam_content .= "SpO2: {$spo2} %<br>";
                
                $on_exam = isset($appointment['on_examination']) ? trim((string)$appointment['on_examination']) : '';
                if ($on_exam !== '' && $on_exam !== '-') $on_exam_content .= nl2br(htmlspecialchars($on_exam)) . '<br>';
                
                if ($on_exam_content !== '') {
                    $html .= '
                <div class="section-header">On Examination:</div>
                <div class="section-content">
                    ' . $on_exam_content . '<br>
                </div>';
                }

                $diagnosis = isset($appointment['diagnosis']) ? trim((string)$appointment['diagnosis']) : '';
                if ($diagnosis !== '' && $diagnosis !== '-') {
                    $html .= '
                <div class="section-header">Diagnosis:</div>
                <div class="section-content">' . nl2br(htmlspecialchars($diagnosis)) . '<br><br></div>';
                }
                
                $advice = isset($appointment['advice']) ? trim((string)$appointment['advice']) : '';
                if ($advice !== '' && $advice !== '-') {
                    $html .= '
                <div class="section-header">Advice:</div>
                <div class="section-content">' . nl2br(htmlspecialchars($advice)) . '<br><br></div>';
                }
            $html .= '
            </td>
            
            <td class="main-rx" width="70%" valign="top" style="padding-left: 25px; padding-top: 15px;">
                <div class="rx-symbol">Rx,</div>
                <div class="medications-list">';
                
                if (empty($medications)) {
                    $html .= '<p style="color: #999; font-style: italic;">No medications prescribed.</p>';
                } else {
                    foreach ($medications as $med) {
                        $duration = trim($med['duration'] ?? '');
                        if (is_numeric($duration)) {
                            $duration .= ' Days';
                        }
                        $html .= '
                        <div class="medication-item">
                            <div class="med-name">' . htmlspecialchars($med['name']) . '</div>
                            <div class="med-instruction">' . htmlspecialchars($med['dose']) . ' — ' . htmlspecialchars($duration) . '</div>
                        </div>';
                    }
                }

                $selected_follow_up = $appointment['follow_up_type'] ?? '';
                $follow_up_date = $appointment['follow_up_date'] ?? '';
                
                if (!empty($selected_follow_up) && !empty($follow_up_date) && $follow_up_date !== '0000-00-00') {
                    if (is_numeric($follow_up_date)) {
                        $days_text = $follow_up_date . ' days';
                    } else {
                        $diff_days = round((strtotime($follow_up_date) - strtotime($appointment['appointment_date'])) / (60 * 60 * 24));
                        $days_text = ($diff_days > 0) ? $diff_days . ' days' : date('d M Y', strtotime($follow_up_date));
                    }
                    
                    if ($selected_follow_up === 'with_report') {
                        $html .= '
                <br><br>
                <div class="follow-up-text" style="font-size: 10pt; font-family: hindsiliguri; line-height: 1.5;">
                    Follow up after ' . $days_text . ' with report.
                </div>';
                    } else if ($selected_follow_up === 'without_report') {
                        $html .= '
                <br><br>
                <div class="follow-up-text" style="font-size: 10pt; font-family: hindsiliguri; line-height: 1.5;">
                    Follow up after ' . $days_text . '.
                </div>';
                    }
                }

    // Pin Note/Reference + Prescription Footer to the page bottom
    $footer_blocks = '';
    if (!empty($appointment['note_reference'])) {
        $footer_blocks .= '<strong>Note / Reference:</strong> ' . nl2br(htmlspecialchars($appointment['note_reference'])) . '<br>';
    }
    if (!empty($appointment['prescription_footer'])) {
        $footer_blocks .= nl2br(htmlspecialchars($appointment['prescription_footer'])) . '<br>';
    }

    $html .= '
                </div>
            </td>
            </tr>
        </table>
        </div>';

    $html .= '
        </div>';

    if ($is_html_preview) {
        $html .= '
            </div>
            <div class="preview-mpdf-footer">
                This is a computer-generated prescription. No signature is required. | TeleRx Bangladesh<br>
                www.telerxbd.com | Emergency Call: 01335053237
                <div class="generated-time">Generated: ' . htmlspecialchars($generated_at) . '</div>
            </div>
        </div>';
    }

    $html .= '
    </body>
    </html>';

    if ($is_html_preview) {
        echo $html;
        exit;
    }

    debug_log("HTML content built. Initializing mPDF...");

    if (!class_exists(\Mpdf\Mpdf::class)) {
        debug_log("Error: mPDF class not found.");
        die("mPDF is not installed. Please run: composer require mpdf/mpdf");
    }

    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];
    $fontDirs[] = __DIR__ . '/../assets/fonts';

    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];
    $fontData['hindsiliguri'] = [
        'R' => 'HindSiliguri-Regular.ttf',
        'B' => 'HindSiliguri-Bold.ttf',
        'useOTL' => 0xFF,
    ];

    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 15,
        'margin_bottom' => 20,
        'fontDir' => $fontDirs,
        'fontdata' => $fontData,
        'default_font' => 'hindsiliguri',
        'autoScriptToLang' => true,
        'autoLangToFont' => true,
    ]);

    debug_log("Rendering PDF with mPDF...");

    $footer_html = '<div style="font-family: hindsiliguri; line-height:1.5; text-align: center;">' . $footer_blocks . '
    <div style="width:100%; border-top:1px solid #000; padding-top:4px; text-align:center; font-size:8pt; color:#555; font-family: hindsiliguri; line-height:1.5;">
        This is a computer-generated prescription. No signature is required. | TeleRx Bangladesh<br>
        www.telerxbd.com | Emergency Call: 01335053237
        <div style="text-align:right; font-size:8pt; color:#666; margin-top:4px; font-family: hindsiliguri;">Generated: ' . htmlspecialchars($generated_at) . '</div>
    </div>
    </div>';
    $mpdf->SetHTMLFooter($footer_html);

    $mpdf->WriteHTML($html);
    $output = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
    debug_log("PDF rendered successfully.");

    $filename = 'prescription_' . $appointment_id . '_' . time() . '.pdf';
    $filepath = 'assets/prescriptions/' . $filename;

    // Ensure directory exists
    $save_dir = __DIR__ . '/../assets/prescriptions/';
    if (!is_dir($save_dir)) {
        if (!@mkdir($save_dir, 0777, true)) {
            debug_log("Error: Failed to create directory $save_dir");
            die("Error: Failed to create directory $save_dir. Please create it manually and set permissions to 777.");
        }
    }
    if (!is_writable($save_dir)) {
        debug_log("Error: Directory $save_dir is not writable");
        die("Error: Directory $save_dir is not writable. Please set permissions to 777.");
    }
    $save_path = $save_dir . $filename;

    debug_log("Saving PDF to $save_path...");
    // Save to server
    file_put_contents($save_path, $output);

    debug_log("Updating database with prescription path...");
    // Update DB with path
    $update_stmt = $conn->prepare("UPDATE appointments SET prescription_path = ? WHERE id = ?");
    $update_stmt->bind_param("si", $filepath, $appointment_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Stream to browser
    debug_log("Streaming PDF to browser...");
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    echo $output;

    $conn->close();
    debug_log("=== PRESCRIPTION GENERATION COMPLETED ===");

} catch (Exception $e) {
    debug_log("EXCEPTION CAUGHT: " . $e->getMessage());
    die("Error generating prescription: " . $e->getMessage());
}
?>