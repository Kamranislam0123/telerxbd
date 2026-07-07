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

// Using Dompdf for PDF generation

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

    $html = '
    <!DOCTYPE html>
    <html lang="bn">
    <head>
        <meta charset="UTF-8">
        <style>
            @font-face {
                font-family: "HindSiliguri";
                src: url("../assets/fonts/HindSiliguri-Regular.ttf") format("truetype");
            }
            @font-face {
                font-family: "HindSiliguri-Bold";
                src: url("../assets/fonts/HindSiliguri-Bold.ttf") format("truetype");
                font-weight: bold;
            }
            @font-face {
                font-family: "SolaimanLipi";
                src: url("https://raw.githubusercontent.com/at-shuvro/SolaimanLipi/master/SolaimanLipi.ttf") format("truetype");
            }
            @page { margin: 10mm; }
            body { font-family: "SolaimanLipi", "HindSiliguri", "DejaVu Sans", sans-serif; color: #000; line-height: 1.3; font-size: 10pt; }
            
            /* Header Section */
            .header { width: 100%; border-bottom: 1px solid #000; padding-bottom: 8px;  position: relative; }
            .doctor-info { float: left; width: 65%; }
            .doctor-name { font-size: 14pt; font-weight: bold; margin: 0; text-transform: uppercase; font-family: "HindSiliguri", "Times New Roman", serif; }
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
            .prescription-body { width: 100%; height: 750px; display: table; border-top: 1px solid #000; }
            .sidebar { display: table-cell; width: 25%; border-right: 1px solid #000; vertical-align: top; padding-right: 15px; padding-top: 15px; }
            .main-rx { display: table-cell; width: 75%; vertical-align: top; padding-left: 25px; padding-top: 15px; }
            
            .section-header { font-weight: bold; text-decoration: underline; padding-bottom: 5px; padding-top: 15px; font-size: 10pt; }
            .section-content { font-size: 9pt; padding-bottom: 20px; }
            
            .rx-symbol { font-size: 18pt; font-family: "Times New Roman", "HindSiliguri", serif; font-weight: bold; margin-bottom: 20px; }
            .medication-item { margin-bottom: 15px; font-size: 11pt; }
            .med-name { font-weight: bold; }
            .med-instruction { font-size: 9pt; font-style: italic; margin-left: 20px; margin-top: 3px; }

            /* Footer Section */
            .footer { position: fixed; bottom: 0; width: 100%; }
            .footer-line {  margin-bottom: 15px; }
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
            .bn { font-family: "SolaimanLipi", "HindSiliguri", "DejaVu Sans", sans-serif; }
            
            .disclaimer { 
                text-align: center; 
                font-size: 8pt; 
                color: #555; 
                margin-top: 10px; 
                padding-top: 5px; 
                border-top: 1px solid #eee;
                line-height: 1.5;
                font-family: "HindSiliguri", "DejaVu Sans", sans-serif;
            }
            .generated-time {
                text-align: right;
                font-size: 8pt;
                color: #666;
                margin-top: 8px;
                font-family: "HindSiliguri", "DejaVu Sans", sans-serif;
            }
        </style>
    </head>
    <body>
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

        <table class="prescription-body" width="100%" cellpadding="0" cellspacing="0" style="border-top: 1px solid #000; margin-top: 0px; height: 750px;">
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
                if ($selected_follow_up === 'with_report') {
                    $html .= '
                <div class="follow-up-text"><strong>Follow-up:</strong> Follow-up with Report</div>';
                } else if ($selected_follow_up === 'without_report') {
                    $html .= '
                <div class="follow-up-text"><strong>Follow-up:</strong> Follow-up without Report</div>';
                }

    $html .= '
                </div>
            </td>
            </tr>
        </table>

        <div class="footer">
            <div class="footer-line"></div>';

    $html .= '
            ' . (!empty($appointment['note_reference']) ? '
            <div class="note-reference-box">
                <div class="note-reference-title">Note / Reference</div>
                <div class="note-reference-content">' . nl2br(htmlspecialchars($appointment['note_reference'])) . '</div>
            </div>' : '') . '

            ' . (!empty($appointment['prescription_footer']) ? '
            <div class="address-box">
                <div class="bn">' . nl2br(htmlspecialchars($appointment['prescription_footer'])) . '</div>
            </div>' : '') . '

            <div class="disclaimer">
                This is a computer-generated prescription. No signature is required. | TeleRx Bangladesh<br>
                www.telerxbd.com | Emergency Call: 01335053237
            </div>

            <div class="generated-time">Generated: ' . htmlspecialchars($generated_at) . '</div>

        </div>
    </body>
    </html>';

    if (isset($_GET['preview']) && $_GET['preview'] == 'html') {
        echo $html;
        exit;
    }

    debug_log("HTML content built. Initializing Dompdf...");

    if (!class_exists(\Dompdf\Dompdf::class)) {
        debug_log("Error: Dompdf class not found.");
        die("Dompdf is not installed. Please install dompdf/dompdf via composer.");
    }

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isFontSubsettingEnabled', true);
    $options->set('fontDir', __DIR__ . '/../assets/fonts');
    $options->set('fontCache', __DIR__ . '/../assets/prescriptions');
    $options->set('tempDir', __DIR__ . '/../assets/prescriptions');

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->setBasePath(__DIR__ . '/');
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');

    debug_log("Rendering PDF with Dompdf...");
    $dompdf->render();
    debug_log("PDF rendered successfully.");

    $filename = 'prescription_' . $appointment_id . '_' . time() . '.pdf';
    $output = $dompdf->output();
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
