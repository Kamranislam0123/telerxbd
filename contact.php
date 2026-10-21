<?php
session_start();
include 'header.php';

$hm_asset_v = '1';   // change this number after editing the CSS/JS if the browser keeps showing the old design
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/home-modern.css?v=<?php echo $hm_asset_v; ?>">
<link rel="stylesheet" href="assets/css/pages-modern.css?v=<?php echo $hm_asset_v; ?>">

<main class="hm">
<script>document.documentElement.classList.add('hm-js');</script>

<!-- ===================== HERO ===================== -->
<section class="hm-hero pgx-center pgx-hero-pad" aria-label="Contact TeleRx">
    <div class="hm-wrap hm-hero-grid">
        <div class="hm-hero-copy">
            <div class="hm-eyebrow hm-reveal">TeleRx Bangladesh &middot; Care Beyond Distance</div>
            <span class="hm-kicker hm-reveal" style="margin-top:14px"><i class="fa-solid fa-headset" aria-hidden="true"></i> Get in touch with TeleRx</span>
            <h1 class="hm-reveal" style="--d:.05s;margin-top:12px">Have any <span class="hm-grad">question?</span></h1>
        </div>
    </div>
</section>

<!-- ===================== CONTACT DETAILS + FORM ===================== -->
<section class="hm-section" style="padding-top:8px" aria-label="Contact details and message form">
    <div class="hm-wrap">
        <div class="pgx-two">
            <div class="pgx-stack hm-reveal">
                <div class="pgx-info">
                    <span class="pgx-info-ic"><i class="isax isax-location5" aria-hidden="true"></i></span>
                    <div>
                        <h3>Address</h3>
                        <p>RSH Tower, Mirpur 11, Dhaka, Bangladesh</p>
                    </div>
                </div>
                <a class="pgx-info" href="tel:+8801836838888">
                    <span class="pgx-info-ic"><i class="isax isax-call5" aria-hidden="true"></i></span>
                    <div>
                        <h3>Phone Number</h3>
                        <p>+880 1836 838888</p>
                    </div>
                </a>
                <a class="pgx-info" href="mailto:care@telerxbd.com">
                    <span class="pgx-info-ic"><i class="isax isax-sms5" aria-hidden="true"></i></span>
                    <div>
                        <h3>Email Address</h3>
                        <p>care@telerxbd.com</p>
                    </div>
                </a>
            </div>

            <div class="pgx-card hm-reveal" style="--d:.08s">
                <div class="pgx-card-head">
                    <span class="pgx-card-ic"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i></span>
                    <h2>Send us a message</h2>
                </div>
                <form class="pgx-form" action="#">
                    <div class="pgx-field">
                        <label for="ct_name">Name</label>
                        <input type="text" id="ct_name" name="name" class="pgx-control" autocomplete="name">
                    </div>
                    <div class="pgx-field">
                        <label for="ct_phone">Phone Number</label>
                        <input type="text" id="ct_phone" name="phone" class="pgx-control" autocomplete="tel">
                    </div>
                    <div class="pgx-field is-full">
                        <label for="ct_email">Email</label>
                        <input type="text" id="ct_email" name="email" class="pgx-control" autocomplete="email">
                    </div>
                    <div class="pgx-field is-full">
                        <label for="ct_msg">Message</label>
                        <textarea id="ct_msg" name="message" class="pgx-control" rows="5"></textarea>
                    </div>
                    <div class="pgx-submit">
                        <button type="submit" class="hm-btn">Send Message <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="pgx-map hm-reveal" style="margin-top:24px">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d228.12547705030644!2d90.36555726081133!3d23.81832761873093!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c1353216afbf%3A0xbbd3def02f07f660!2sRSH%20Tower!5e0!3m2!1sen!2sbd!4v1771758963012!5m2!1sen!2sbd" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="TeleRx Bangladesh location"></iframe>
        </div>
    </div>
</section>

</main>

<script defer src="assets/js/home-modern.js?v=<?php echo $hm_asset_v; ?>"></script>

<?php include 'footer.php'; ?>
