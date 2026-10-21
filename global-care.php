<?php
session_start();
include 'header.php';

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = "";
$error = "";

/* =========================
   CONFIG SECTION
========================= */
$siteKey   = "6LfAbHEsAAAAAOAAg1cecbi-gX8aqLhr1_1EaFu_";
$secretKey = "6LfAbHEsAAAAAOkgRCKVAjyZArUKKsuOVxzzCwyP";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function clean($data){
        return htmlspecialchars(trim($data));
    }

    $name  = clean($_POST['name']);
    $phone = clean($_POST['phone']);
    $email = !empty($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : "";
    $desc  = clean($_POST['description']);

    /* ===== reCAPTCHA VERIFY ===== */
    $recaptcha = $_POST['g-recaptcha-response'];
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptcha");
    $captcha_success = json_decode($verify);

    if (!$captcha_success->success) {
        $error = "Captcha verification failed.";
    }
    elseif (empty($name) || empty($phone) || empty($desc)) {
        $error = "Please fill required fields.";
    }
    else {

        $mail = new PHPMailer(true);

        try {

            /* ===== SMTP CONFIG ===== */
            $mail->isSMTP();
            $mail->Host       = 'mail.telerxbd.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'care@telerxbd.com';
            $mail->Password   = 'TelerX@#25@#';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('care@telerxbd.com', 'TelerX BD');
            $mail->addAddress('care@telerxbd.com');

            if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
                $mail->addReplyTo($email, $name);
            }

            /* ===== MULTIPLE FILE UPLOAD ===== */
            if(!empty($_FILES['attachment']['name'][0])){

                $allowed = ['pdf','jpg','jpeg','png'];
                foreach($_FILES['attachment']['name'] as $key => $fileName){

                    $tmpName = $_FILES['attachment']['tmp_name'][$key];
                    $size    = $_FILES['attachment']['size'][$key];
                    $ext     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if(in_array($ext, $allowed) && $size <= 5*1024*1024){

                        $newName = uniqid().".".$ext;
                        $uploadPath = "uploads/".$newName;

                        move_uploaded_file($tmpName, $uploadPath);
                        $mail->addAttachment($uploadPath, $fileName);
                    }
                }
            }

            $mail->isHTML(true);
            $mail->Subject = "New Quote Request";

            $mail->Body = "
            <h3>New Quote Request</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Mobile:</strong> $phone</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Description:</strong><br>$desc</p>
            ";

            $mail->send();
            $success = "Your request has been submitted successfully.";

        } catch (Exception $e) {
            $error = "Mail error: ".$mail->ErrorInfo;
        }
    }
}

$hm_asset_v = '1';   // change this number after editing the CSS/JS if the browser keeps showing the old design
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/home-modern.css?v=<?php echo $hm_asset_v; ?>">
<link rel="stylesheet" href="assets/css/pages-modern.css?v=<?php echo $hm_asset_v; ?>">

<main class="hm">
<script>document.documentElement.classList.add('hm-js');</script>

<section class="hm-hero pgx-center pgx-hero-pad" aria-label="Global Care">
    <div class="hm-wrap hm-hero-grid">
        <div class="hm-hero-copy">
            <span class="hm-kicker hm-reveal"><i class="fa-solid fa-earth-asia" aria-hidden="true"></i> Global Care</span>
            <h1 class="hm-reveal" style="--d:.05s;margin-top:12px">Tell Us About <span class="hm-grad">Your Problem</span></h1>
        </div>
    </div>
</section>

<section class="hm-section" style="padding-top:8px">
    <div class="hm-wrap" style="max-width:760px">
        <div class="pgx-card hm-reveal">
            <div class="pgx-card-head">
                <span class="pgx-card-ic"><i class="fa-solid fa-file-medical" aria-hidden="true"></i></span>
                <h2>Tell Us About Your Problem</h2>
            </div>

            <form class="pgx-form" method="POST" enctype="multipart/form-data">
                <?php if($success) echo "<div class='pgx-alert is-ok'>$success</div>"; ?>
                <?php if($error) echo "<div class='pgx-alert is-err'>$error</div>"; ?>

                <div class="pgx-field">
                    <label for="gc_name">Full Name <em>*</em></label>
                    <input type="text" id="gc_name" name="name" class="pgx-control" placeholder="Full Name *" required>
                </div>
                <div class="pgx-field">
                    <label for="gc_phone">Mobile Number <em>*</em></label>
                    <input type="text" id="gc_phone" name="phone" class="pgx-control" placeholder="Mobile Number *" required>
                </div>
                <div class="pgx-field is-full">
                    <label for="gc_email">Email (Optional)</label>
                    <input type="email" id="gc_email" name="email" class="pgx-control" placeholder="Email (Optional)">
                </div>
                <div class="pgx-field is-full">
                    <label for="gc_file">Attach File</label>
                    <input type="file" id="gc_file" name="attachment[]" class="pgx-control" multiple>
                    <span class="pgx-hint">Optional - PDF/JPG/PNG, Max 5MB each</span>
                </div>
                <div class="pgx-field is-full">
                    <label for="gc_desc">Description <em>*</em></label>
                    <textarea id="gc_desc" name="description" class="pgx-control" rows="5" placeholder="Description *" required></textarea>
                </div>

                <div class="pgx-captcha">
                    <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
                </div>

                <div class="pgx-submit">
                    <button type="submit" class="hm-btn">Send Quote <i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
                </div>
            </form>
        </div>
    </div>
</section>

</main>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script defer src="assets/js/home-modern.js?v=<?php echo $hm_asset_v; ?>"></script>

<?php include 'footer.php'; ?>
