<?php
/**
 * Generate Prescription PDF
 * Fetches appointment and doctor data to render a professional prescription.
 */

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die("Error: vendor/autoload.php not found at " . realpath(__DIR__ . '/../') . "/vendor/autoload.php. Please run 'composer install' on the server.");
}
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Enable error display for debugging in live
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['appointment_id'])) {
    die("Appointment ID is required.");
}

$appointment_id = (int)$_GET['appointment_id'];

try {
    $conn = getDBConnection();
    
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

    // Convert to base64 for reliable PDF rendering
    if ($logo_path && file_exists($logo_path)) {
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
                src: url("https://fonts.gstatic.com/s/hindsiliguri/v12/ijwb88v_idY79uO6f7902E396U2Z8V2n.ttf") format("truetype");
            }
            @font-face {
                font-family: "HindSiliguri-Bold";
                src: url("https://fonts.gstatic.com/s/hindsiliguri/v12/ijwe88v_idY79uO6f7902E396V2W_W7i_H6i.ttf") format("truetype");
                font-weight: bold;
            }
            @font-face {
                font-family: "SolaimanLipi";
                src: url("https://raw.githubusercontent.com/at-shuvro/SolaimanLipi/master/SolaimanLipi.ttf") format("truetype");
            }
            @page { margin: 10mm; }
            body { font-family: "SolaimanLipi", "HindSiliguri", "DejaVu Sans", sans-serif; color: #000; line-height: 1.3; font-size: 11pt; }
            
            /* Header Section */
            .header { width: 100%; border-bottom: 1px solid #000; padding-bottom: 8px; margin-bottom: 15px; position: relative; }
            .doctor-info { float: left; width: 65%; }
            .doctor-name { font-size: 18pt; font-weight: bold; margin: 0; text-transform: uppercase; font-family: "Times New Roman", "HindSiliguri", serif; }
            .doctor-details { font-size: 10pt; margin: 1px 0; text-transform: uppercase; font-weight: bold; }
            .logo-info { float: right; width: 30%; text-align: right; }
            .logo-info img { width: 140px; height: auto; }
            .slogan { font-size: 9pt; color: #15558d; margin-top: 3px; font-weight: bold; }
            .clear { clear: both; }

            /* Patient Info Section */
            .patient-info { width: 100%; border-bottom: 1px solid #000; padding: 12px 0; margin-bottom: 0px; font-size: 11pt; }
            .info-row { width: 100%; margin-bottom: 10px; }
            .info-item { display: inline-block; }
            .info-label { font-weight: bold; }
            .info-value { border-bottom: 1px solid #000; display: inline-block; padding: 0 5px; margin-right: 15px; }
            
            /* Prescription Body */
            .prescription-body { width: 100%; min-height: 700px; display: table; border-top: 1px solid #000; }
            .sidebar { display: table-cell; width: 25%; border-right: 1px solid #000; vertical-align: top; padding-right: 15px; padding-top: 15px; }
            .main-rx { display: table-cell; width: 75%; vertical-align: top; padding-left: 25px; padding-top: 15px; }
            
            .section-header { font-weight: bold; text-decoration: underline; margin-bottom: 10px; margin-top: 15px; font-size: 11pt; }
            .section-content { font-size: 10pt; margin-bottom: 20px; min-height: 40px; }
            
            .rx-symbol { font-size: 32pt; font-family: "Times New Roman", "HindSiliguri", serif; font-weight: bold; margin-bottom: 20px; }
            .medication-item { margin-bottom: 15px; font-size: 12pt; }
            .med-name { font-weight: bold; }
            .med-instruction { font-size: 10pt; font-style: italic; margin-left: 20px; margin-top: 3px; }

            /* Footer Section */
            .footer { position: fixed; bottom: 0; width: 100%; padding-bottom: 5mm; }
            .footer-line { border-top: 1px solid #000; margin-bottom: 15px; }
            .badges { width: 100%; text-align: center; margin-bottom: 15px; }
            .badge { 
                display: inline-block; 
                background: #c9302c; 
                color: #fff; 
                padding: 5px 12px; 
                border-radius: 4px; 
                font-size: 10pt; 
                margin: 0 8px; 
                font-weight: bold;
            }
            .address-box { 
                border: 2px solid #000; 
                background: #f4f4f4; 
                padding: 12px; 
                text-align: center; 
                font-size: 11pt;
            }
            .address-title { font-weight: bold; font-size: 14pt; margin-bottom: 5px; }
            
            /* Bengali Support */
            .bn { font-family: "SolaimanLipi", "HindSiliguri", "DejaVu Sans", sans-serif; }
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

        <div class="patient-info">
            <div class="info-row">
                <span class="info-label">Patient Name:</span>
                <span class="info-value" style="width: 400px;">' . htmlspecialchars($appointment['patient_name']) . '</span>
                <span class="info-label">Age:</span>
                <span class="info-value" style="width: 60px;">' . htmlspecialchars($appointment['age'] ?: "—") . '</span>
                <span class="info-label">Sex:</span>
                <span class="info-value" style="width: 60px;">' . htmlspecialchars($appointment['gender'] ?: "M / F") . '</span>
            </div>
            <div class="info-row" style="margin-top: 15px;">
                <span class="info-label">Date:</span>
                <span class="info-value" style="width: 180px;">' . $prescription_date . '</span>
                <span class="info-label" style="margin-left: 40px;">Weight:</span>
                <span class="info-value" style="width: 100px;">' . htmlspecialchars($appointment['weight'] ?: "—") . '</span> <span class="info-label">kg</span>
            </div>
        </div>

        <div class="prescription-body">
            <div class="sidebar">
                <div class="section-header">Chief Complaints:</div>
                <div class="section-content">' . nl2br(htmlspecialchars($appointment['chief_complaints'] ?: "—")) . '</div>
                
                <div class="section-header">On Examination:</div>
                <div class="section-content">
                    ' . ($appointment['blood_pressure'] ? "BP: {$appointment['blood_pressure']} mmHg<br>" : "") . '
                    ' . ($appointment['body_temperature'] ? "Temp: {$appointment['body_temperature']} F<br>" : "") . '
                    ' . ($appointment['pulse'] ? "Pulse: {$appointment['pulse']} /min<br>" : "") . '
                    ' . ($appointment['spo2'] ? "SpO2: {$appointment['spo2']} %<br>" : "") . '
                    ' . nl2br(htmlspecialchars($appointment['on_examination'] ?: "")) . '
                </div>
                
                <div class="section-header">Advice:</div>
                <div class="section-content">' . nl2br(htmlspecialchars($appointment['advice'] ?: "—")) . '</div>
            </div>
            
            <div class="main-rx">
                <div class="rx-symbol">Rx,</div>
                <div class="medications-list">';
                
                if (empty($medications)) {
                    $html .= '<p style="color: #999; font-style: italic;">No medications prescribed.</p>';
                } else {
                    foreach ($medications as $med) {
                        $html .= '
                        <div class="medication-item">
                            <div class="med-name">' . htmlspecialchars($med['name']) . '</div>
                            <div class="med-instruction">' . htmlspecialchars($med['dose']) . ' — ' . htmlspecialchars($med['duration']) . '</div>
                        </div>';
                    }
                }

    $html .= '
                </div>
                ' . ($appointment['diagnosis'] ? '
                <div style="margin-top: 50px; border-top: 1px dotted #ccc; padding-top: 15px;">
                    <div class="section-header">Diagnosis:</div>
                    <div class="section-content" style="font-size: 12pt;"><strong>' . htmlspecialchars($appointment['diagnosis']) . '</strong></div>
                </div>' : '') . '
            </div>
        </div>

        <div class="footer">
            <div class="footer-line"></div>';

    if (!empty($appointment['prescription_footer'])) {
        $html .= '
            <div class="address-box">
                <div class="bn">' . nl2br(htmlspecialchars($appointment['prescription_footer'])) . '</div>
            </div>';
    } else {
        $html .= '
            <div class="badges">
                <span class="badge bn">ফ্রি মেডিকেল ক্যাম্প</span>
                <span class="badge bn">ফ্রি মেডিকেল ক্যাম্প</span>
                <span class="badge bn">ফ্রি মেডিকেল ক্যাম্প</span>
                <span class="badge bn">ফ্রি মেডিকেল ক্যাম্প</span>
            </div>
            <div class="address-box">
                <div class="address-title bn">তা\'লীমূল কোরআন নূরানী হাফিজিয়া মাদ্রাসা</div>
                <div class="bn">ঠিকানা: প-১৫২/৬, দক্ষিণ বাড্ডা, ঢাকা-১২১২</div>
            </div>';
    }

    $html .= '
        </div>
    </body>
    </html>';

    // 7. Initialize Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isFontSubsettingEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    
    // Set paper size
    $dompdf->setPaper('A4', 'portrait');
    
    // Render the PDF
    $dompdf->render();
    
    $filename = 'prescription_' . $appointment_id . '_' . time() . '.pdf';
    $output = $dompdf->output();
    $filepath = 'assets/prescriptions/' . $filename;
    
    // Ensure directory exists
    $save_dir = __DIR__ . '/../assets/prescriptions/';
    if (!is_dir($save_dir)) {
        if (!@mkdir($save_dir, 0777, true)) {
            die("Error: Failed to create directory $save_dir. Please create it manually and set permissions to 777.");
        }
    }
    if (!is_writable($save_dir)) {
        die("Error: Directory $save_dir is not writable. Please set permissions to 777.");
    }
    $save_path = $save_dir . $filename;
    
    // Save to server
    file_put_contents($save_path, $output);
    
    // Update DB with path
    $update_stmt = $conn->prepare("UPDATE appointments SET prescription_path = ? WHERE id = ?");
    $update_stmt->bind_param("si", $filepath, $appointment_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Stream to browser
    $dompdf->stream($filename, ["Attachment" => false]);

    $conn->close();

} catch (Exception $e) {
    die("Error generating prescription: " . $e->getMessage());
}
?>
