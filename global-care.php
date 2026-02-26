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
?>

<style>
.quote-wrapper{
    max-width:700px;
    margin:100px auto;
    background:#ffffff;
    padding:40px;
    border-radius:8px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}
.quote-wrapper h2{
    margin-bottom:25px;
    text-align:center;
}
.quote-wrapper input,
.quote-wrapper textarea{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border:1px solid #ddd;
    border-radius:6px;
}
.quote-wrapper button{
    width:100%;
    padding:14px;
    background-image: linear-gradient(90.08deg, #15558d 0.09%, #0c77c9 70.28%);
    color:#fff;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
.quote-wrapper button:hover{
    background:#15558d;
}
.success{
    background:#e6fffa;
    color:#065f46;
    padding:10px;
    margin-bottom:15px;
}
.error{
    background:#fee2e2;
    color:#991b1b;
    padding:10px;
    margin-bottom:15px;
}
</style>

<div class="quote-wrapper">
    <h2>Tell Us About Your Problem</h2>

    <?php if($success) echo "<div class='success'>$success</div>"; ?>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>

<form method="POST" enctype="multipart/form-data">

<input type="text" name="name" placeholder="Full Name *" required>
<input type="text" name="phone" placeholder="Mobile Number *" required>
<input type="email" name="email" placeholder="Email (Optional)">

<label style="font-size: 13px;">Attach File (Optional - PDF/JPG/PNG, Max 5MB each)</label>
<input type="file" name="attachment[]" multiple>

<textarea name="description" rows="5" placeholder="Description *" required></textarea>

<div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
<br>

<button type="submit">Send Quote</button>

</form>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php include('footer.php'); ?>