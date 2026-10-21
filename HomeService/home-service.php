<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/data.php';
hs_include_header('TeleRx Home Service');

$hm_asset_v = '1';   // change this number after editing the CSS/JS if the browser keeps showing the old design
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/home-modern.css?v=<?php echo $hm_asset_v; ?>">
<link rel="stylesheet" href="../assets/css/pages-modern.css?v=<?php echo $hm_asset_v; ?>">

<main class="hm hs-page">
<script>document.documentElement.classList.add('hm-js');</script>

<!-- ===================== HERO ===================== -->
<section class="hm-hero" aria-label="TeleRx Home Service">
    <div class="hm-wrap hm-hero-grid">
        <div class="hm-hero-copy">
            <span class="hm-kicker hm-reveal"><i class="fa-solid fa-house-medical" aria-hidden="true"></i> TeleRx Home Service</span>
            <h1 class="hm-reveal" style="--d:.05s;margin-top:12px">Trusted healthcare support, <span class="hm-grad">right at your home.</span></h1>
            <p class="pgx-lead hm-reveal" style="--d:.1s">TeleRx helps families arrange nursing care, caregiver support, doctor home visits, physiotherapy, oxygen support, sample collection, medicine delivery and other essential home healthcare services.</p>
            <div class="pgx-actions hm-reveal" style="--d:.15s">
                <a class="hm-btn" href="#services">Explore Services <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                <a class="hm-btn is-ghost" href="<?php echo hs_e(hs_contact_url()); ?>">Contact TeleRx</a>
            </div>
        </div>

        <div class="pgx-hero-card hm-reveal" style="--d:.12s">
            <img src="<?php echo hs_e(hs_image('assets/services/home-service-hero.svg')); ?>" alt="TeleRx Home Service">
            <div class="pgx-stat-row">
                <div class="pgx-stat"><strong>12+</strong><span>Home Services</span></div>
                <div class="pgx-stat"><strong>24/7</strong><span>Support Access</span></div>
                <div class="pgx-stat"><strong>Home</strong><span>Care Focus</span></div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SERVICES ===================== -->
<section class="hm-section" id="services" aria-labelledby="hs-services-title">
    <div class="hm-wrap">
        <div class="hm-head is-center hm-reveal">
            <span class="hm-kicker"><i class="fa-solid fa-hand-holding-medical" aria-hidden="true"></i> Our Services</span>
            <h2 id="hs-services-title">Home healthcare services for patients, families and elderly care needs</h2>
            <p>Choose a service and click Read More to view the full details, service process and suitable use cases.</p>
        </div>

        <div class="pgx-svc-grid">
            <?php foreach ($home_services as $slug => $service): ?>
                <article class="pgx-svc-card hm-reveal">
                    <div class="pgx-svc-img">
                        <img src="<?php echo hs_e(hs_image($service['image'])); ?>" alt="<?php echo hs_e($service['title']); ?>" loading="lazy">
                        <span class="pgx-badge"><?php echo hs_e($service['category']); ?></span>
                    </div>
                    <div class="pgx-svc-body">
                        <h3><?php echo hs_e($service['title']); ?></h3>
                        <p><?php echo hs_e($service['short']); ?></p>
                        <div class="pgx-svc-foot">
                            <a class="hm-btn" href="<?php echo hs_e(hs_service_url($slug)); ?>">Read More</a>
                            <a class="hm-btn is-ghost" href="<?php echo hs_e(hs_contact_url()); ?>">Contact</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== WHY TELERX ===================== -->
<section class="hm-section is-tint" aria-labelledby="hs-why-title">
    <div class="hm-wrap">
        <div class="hm-head is-center hm-reveal">
            <span class="hm-kicker"><i class="fa-solid fa-shield-heart" aria-hidden="true"></i> Why TeleRx</span>
            <h2 id="hs-why-title">Built for convenient and coordinated home care</h2>
            <p>Our goal is to make healthcare support easier for families by connecting consultation, care coordination and essential home services in one place.</p>
        </div>
        <div class="pgx-why">
            <article class="pgx-why-card hm-reveal">
                <div class="pgx-why-ic"><i class="fa-solid fa-heart-pulse" style="color:#fff;font-size:22px" aria-hidden="true"></i></div>
                <h3>Patient-first support</h3>
                <p>Service is planned based on patient condition, family need and available care resources.</p>
            </article>
            <article class="pgx-why-card hm-reveal" style="--d:.07s">
                <div class="pgx-why-ic"><i class="fa-solid fa-house-chimney-medical" style="color:#fff;font-size:22px" aria-hidden="true"></i></div>
                <h3>Home convenience</h3>
                <p>Reduce unnecessary travel by arranging selected care and support services at home.</p>
            </article>
            <article class="pgx-why-card hm-reveal" style="--d:.14s">
                <div class="pgx-why-ic"><i class="fa-solid fa-diagram-project" style="color:#fff;font-size:22px" aria-hidden="true"></i></div>
                <h3>Care coordination</h3>
                <p>TeleRx helps coordinate doctor advice, nursing support, diagnostics, medicine and equipment needs.</p>
            </article>
            <article class="pgx-why-card hm-reveal" style="--d:.21s">
                <div class="pgx-why-ic"><i class="fa-solid fa-people-roof" style="color:#fff;font-size:22px" aria-hidden="true"></i></div>
                <h3>Family updates</h3>
                <p>Families can stay connected with care progress, service status and next-step guidance.</p>
            </article>
        </div>
    </div>
</section>

<!-- ===================== HOW IT WORKS ===================== -->
<section class="hm-section" aria-labelledby="hs-how-title">
    <div class="hm-wrap">
        <div class="hm-head is-center hm-reveal">
            <span class="hm-kicker"><i class="fa-solid fa-route" aria-hidden="true"></i> How It Works</span>
            <h2 id="hs-how-title">Simple process to request a home service</h2>
        </div>
        <div class="hm-steps">
            <article class="hm-step hm-reveal">
                <span class="hm-step-ic"><i class="fa-solid fa-list-check" aria-hidden="true"></i><span class="hm-step-no">1</span></span>
                <div><h3>Choose Service</h3><p>Select the home service your patient or family member needs.</p></div>
            </article>
            <article class="hm-step hm-reveal" style="--d:.08s">
                <span class="hm-step-ic"><i class="fa-solid fa-file-lines" aria-hidden="true"></i><span class="hm-step-no">2</span></span>
                <div><h3>Share Details</h3><p>Tell us patient condition, location, preferred time and any prescription if available.</p></div>
            </article>
            <article class="hm-step hm-reveal" style="--d:.16s">
                <span class="hm-step-ic"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i><span class="hm-step-no">3</span></span>
                <div><h3>Confirm Schedule</h3><p>TeleRx checks availability, service scope and cost before confirming the schedule.</p></div>
            </article>
            <article class="hm-step hm-reveal" style="--d:.24s">
                <span class="hm-step-ic"><i class="fa-solid fa-house-medical-circle-check" aria-hidden="true"></i><span class="hm-step-no">4</span></span>
                <div><h3>Receive Support</h3><p>Service is arranged at home with follow-up guidance where needed.</p></div>
            </article>
        </div>
    </div>
</section>

<!-- ===================== CALL TO ACTION ===================== -->
<section class="hm-section" style="padding-top:0">
    <div class="hm-wrap">
        <div class="hm-cta hm-reveal">
            <div>
                <h2>Need home healthcare support today?</h2>
                <p style="margin-top:8px;color:rgba(255,255,255,.8);max-width:520px">Contact TeleRx with patient details. Our team will guide you about the right service, availability and next steps.</p>
            </div>
            <div class="pgx-actions" style="margin-top:0">
                <a class="hm-btn is-light" href="<?php echo hs_e(hs_contact_url()); ?>">Contact Now</a>
                <a class="hm-btn is-light" style="background:transparent;color:#fff;border:1px solid rgba(255,255,255,.5)" href="#services">View Services</a>
            </div>
        </div>
    </div>
</section>
</main>

<script defer src="../assets/js/home-modern.js?v=<?php echo $hm_asset_v; ?>"></script>

<?php hs_include_footer(); ?>
