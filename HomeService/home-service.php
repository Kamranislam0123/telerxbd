<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/data.php';
hs_include_header('TeleRx Home Service');
?>

<style>
:root {
    --hs-primary:#0E82FD;
    --hs-primary-dark:#0867CA;
    --hs-secondary:#00B894;
    --hs-ink:#102033;
    --hs-muted:#667085;
    --hs-soft:#F4F8FF;
    --hs-border:#E5EDF7;
    --hs-white:#FFFFFF;
    --hs-shadow:0 18px 40px rgba(16,32,51,.10);
    --hs-radius:24px;
}
.hs-page, .hs-page * { box-sizing:border-box; }
.hs-page { width:100%; overflow-x:hidden; color:var(--hs-ink);  background:#fff; }
.hs-container { width:min(1180px, calc(100% - 32px)); margin:0 auto; }
.hs-hero { padding:86px 0 70px; background:radial-gradient(circle at 12% 10%, rgba(14,130,253,.16), transparent 30%), linear-gradient(135deg,#F7FBFF 0%,#fff 56%,#EFFFFA 100%); }
.hs-hero-grid { display:grid; grid-template-columns:1.05fr .95fr; gap:38px; align-items:center; }
.hs-kicker { display:inline-flex; gap:8px; align-items:center; padding:9px 14px; border-radius:999px; background:rgba(14,130,253,.10); color:var(--hs-primary-dark); font-weight:800; font-size:14px; margin-bottom:18px; }
.hs-kicker::before { content:""; width:8px; height:8px; border-radius:50%; background:var(--hs-secondary); }
.hs-hero h1 { margin:0 0 18px; font-size:clamp(36px,5vw,60px); line-height:1.08; letter-spacing:-1.2px; }
.hs-hero h1 span { color:var(--hs-primary); }
.hs-hero p { margin:0; color:var(--hs-muted); font-size:18px; line-height:1.75; max-width:660px; }
.hs-actions { display:flex; flex-wrap:wrap; gap:14px; margin-top:30px; }
.hs-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:48px; padding:13px 22px; border-radius:999px; font-weight:800; text-decoration:none; transition:.22s ease; border:1px solid transparent; }
.hs-btn-primary { color:#fff; background:linear-gradient(135deg,var(--hs-primary),var(--hs-secondary)); box-shadow:0 14px 28px rgba(14,130,253,.22); }
.hs-btn-primary:hover { color:#fff; transform:translateY(-2px); }
.hs-btn-outline { color:var(--hs-primary-dark); background:#fff; border-color:rgba(14,130,253,.20); }
.hs-btn-outline:hover { color:var(--hs-primary-dark); border-color:var(--hs-primary); transform:translateY(-2px); }
.hs-hero-card { position:relative; border-radius:30px; background:#fff; border:1px solid var(--hs-border); box-shadow:var(--hs-shadow); padding:20px; overflow:hidden; }
.hs-hero-card img { width:100%; height:auto; display:block; border-radius:24px; }
.hs-stat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-top:14px; }
.hs-stat { padding:16px 12px; border-radius:18px; background:var(--hs-soft); text-align:center; }
.hs-stat strong { display:block; color:var(--hs-primary); font-size:24px; line-height:1; margin-bottom:6px; }
.hs-stat span { display:block; color:var(--hs-muted); font-size:13px; font-weight:700; }
.hs-section { padding:72px 0; }
.hs-section-soft { background:linear-gradient(180deg,#fff 0%, #F7FBFF 100%); }
.hs-title { text-align:center; max-width:780px; margin:0 auto 42px; }
.hs-title h2 { margin:0 0 14px; font-size:clamp(30px,3.4vw,44px); line-height:1.18; }
.hs-title p { margin:0 auto; color:var(--hs-muted); font-size:16px; line-height:1.75; }
.hs-service-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:24px; }
.hs-card { background:#fff; border:1px solid var(--hs-border); border-radius:var(--hs-radius); box-shadow:0 14px 32px rgba(16,32,51,.08); overflow:hidden; display:flex; flex-direction:column; transition:.22s ease; }
.hs-card:hover { transform:translateY(-6px); box-shadow:0 18px 44px rgba(16,32,51,.12); }
.hs-card-img { position:relative; height:190px; background:#F7FBFF; overflow:hidden; }
.hs-card-img img { width:100%; height:100%; object-fit:cover; display:block; }
.hs-card-badge { position:absolute; left:16px; top:16px; padding:7px 11px; background:#fff; color:var(--hs-primary-dark); border-radius:999px; font-size:12px; font-weight:800; box-shadow:0 8px 18px rgba(16,32,51,.10); }
.hs-card-body { padding:24px; display:flex; flex-direction:column; flex:1; }
.hs-card h3 { margin:0 0 10px; font-size:21px; line-height:1.3; }
.hs-card p { margin:0; color:var(--hs-muted); font-size:14px; line-height:1.7; }
.hs-card-footer { margin-top:auto; padding-top:18px; display:flex; gap:10px; flex-wrap:wrap; }
.hs-read { color:#fff; background:linear-gradient(135deg,var(--hs-primary),#19B2D8); padding:11px 16px; border-radius:999px; font-weight:800; font-size:14px; text-decoration:none; }
.hs-contact { color:var(--hs-primary-dark); background:rgba(14,130,253,.08); padding:11px 16px; border-radius:999px; font-weight:800; font-size:14px; text-decoration:none; }
.hs-feature-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:18px; }
.hs-feature { background:#fff; border:1px solid var(--hs-border); border-radius:22px; padding:24px; box-shadow:0 10px 26px rgba(16,32,51,.06); }
.hs-feature strong { display:block; font-size:18px; margin-bottom:8px; color:var(--hs-ink); }
.hs-feature p { margin:0; color:var(--hs-muted); font-size:14px; line-height:1.65; }
.hs-steps { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:18px; counter-reset:steps; }
.hs-step { position:relative; background:#fff; border:1px solid var(--hs-border); border-radius:22px; padding:26px 22px; box-shadow:0 10px 26px rgba(16,32,51,.06); }
.hs-step::before { counter-increment:steps; content:counter(steps); display:inline-flex; width:38px; height:38px; border-radius:50%; align-items:center; justify-content:center; color:#fff; background:linear-gradient(135deg,var(--hs-primary),var(--hs-secondary)); font-weight:900; margin-bottom:16px; }
.hs-step h3 { margin:0 0 8px; font-size:18px; }
.hs-step p { margin:0; color:var(--hs-muted); font-size:14px; line-height:1.65; }
.hs-cta { background:linear-gradient(135deg,#0E82FD,#00B894); border-radius:30px; padding:42px; color:#fff; display:grid; grid-template-columns:1.2fr .8fr; gap:24px; align-items:center; }
.hs-cta h2 { color:#fff; margin:0 0 12px; font-size:clamp(28px,3vw,40px); }
.hs-cta p { color:rgba(255,255,255,.9); margin:0; line-height:1.75; }
.hs-cta-actions { display:flex; justify-content:flex-end; gap:12px; flex-wrap:wrap; }
.hs-cta .hs-btn-outline { background:#fff; }
@media (max-width: 991.98px) { .hs-hero-grid,.hs-cta { grid-template-columns:1fr; } .hs-service-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .hs-feature-grid,.hs-steps { grid-template-columns:repeat(2,minmax(0,1fr)); } .hs-cta-actions { justify-content:flex-start; } }
@media (max-width: 575.98px) { .hs-container { width:min(100%, calc(100% - 24px)); } .hs-service-grid,.hs-feature-grid,.hs-steps,.hs-stat-grid { grid-template-columns:1fr; } .hs-hero { padding:58px 0 46px; } .hs-section { padding:54px 0; } .hs-cta { padding:28px 22px; } }
</style>

<main class="hs-page">
    <section class="hs-hero">
        <div class="hs-container">
            <div class="hs-hero-grid">
                <div>
                    <div class="hs-kicker">TeleRx Home Service</div>
                    <h1>Trusted healthcare support, <span>right at your home.</span></h1>
                    <p>TeleRx helps families arrange nursing care, caregiver support, doctor home visits, physiotherapy, oxygen support, sample collection, medicine delivery and other essential home healthcare services.</p>
                    <div class="hs-actions">
                        <a class="hs-btn hs-btn-primary" href="#services">Explore Services</a>
                        <a class="hs-btn hs-btn-outline" href="<?php echo hs_e(hs_contact_url()); ?>">Contact TeleRx</a>
                    </div>
                </div>
                <div class="hs-hero-card">
                    <img src="<?php echo hs_e(hs_image('assets/services/home-service-hero.svg')); ?>" alt="TeleRx Home Service">
                    <div class="hs-stat-grid">
                        <div class="hs-stat"><strong>12+</strong><span>Home Services</span></div>
                        <div class="hs-stat"><strong>24/7</strong><span>Support Access</span></div>
                        <div class="hs-stat"><strong>Home</strong><span>Care Focus</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="hs-section" id="services">
        <div class="hs-container">
            <div class="hs-title">
                <div class="hs-kicker">Our Services</div>
                <h2>Home healthcare services for patients, families and elderly care needs</h2>
                <p>Choose a service and click Read More to view the full details, service process and suitable use cases.</p>
            </div>
            <div class="hs-service-grid">
                <?php foreach ($home_services as $slug => $service): ?>
                    <article class="hs-card">
                        <div class="hs-card-img">
                            <img src="<?php echo hs_e(hs_image($service['image'])); ?>" alt="<?php echo hs_e($service['title']); ?>">
                            <span class="hs-card-badge"><?php echo hs_e($service['category']); ?></span>
                        </div>
                        <div class="hs-card-body">
                            <h3><?php echo hs_e($service['title']); ?></h3>
                            <p><?php echo hs_e($service['short']); ?></p>
                            <div class="hs-card-footer">
                                <a class="hs-read" href="<?php echo hs_e(hs_service_url($slug)); ?>">Read More</a>
                                <a class="hs-contact" href="<?php echo hs_e(hs_contact_url()); ?>">Contact</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="hs-section hs-section-soft">
        <div class="hs-container">
            <div class="hs-title">
                <div class="hs-kicker">Why TeleRx</div>
                <h2>Built for convenient and coordinated home care</h2>
                <p>Our goal is to make healthcare support easier for families by connecting consultation, care coordination and essential home services in one place.</p>
            </div>
            <div class="hs-feature-grid">
                <div class="hs-feature"><strong>Patient-first support</strong><p>Service is planned based on patient condition, family need and available care resources.</p></div>
                <div class="hs-feature"><strong>Home convenience</strong><p>Reduce unnecessary travel by arranging selected care and support services at home.</p></div>
                <div class="hs-feature"><strong>Care coordination</strong><p>TeleRx helps coordinate doctor advice, nursing support, diagnostics, medicine and equipment needs.</p></div>
                <div class="hs-feature"><strong>Family updates</strong><p>Families can stay connected with care progress, service status and next-step guidance.</p></div>
            </div>
        </div>
    </section>

    <section class="hs-section">
        <div class="hs-container">
            <div class="hs-title">
                <div class="hs-kicker">How It Works</div>
                <h2>Simple process to request a home service</h2>
            </div>
            <div class="hs-steps">
                <div class="hs-step"><h3>Choose Service</h3><p>Select the home service your patient or family member needs.</p></div>
                <div class="hs-step"><h3>Share Details</h3><p>Tell us patient condition, location, preferred time and any prescription if available.</p></div>
                <div class="hs-step"><h3>Confirm Schedule</h3><p>TeleRx checks availability, service scope and cost before confirming the schedule.</p></div>
                <div class="hs-step"><h3>Receive Support</h3><p>Service is arranged at home with follow-up guidance where needed.</p></div>
            </div>
        </div>
    </section>

    <section class="hs-section hs-section-soft">
        <div class="hs-container">
            <div class="hs-cta">
                <div>
                    <h2>Need home healthcare support today?</h2>
                    <p>Contact TeleRx with patient details. Our team will guide you about the right service, availability and next steps.</p>
                </div>
                <div class="hs-cta-actions">
                    <a class="hs-btn hs-btn-outline" href="<?php echo hs_e(hs_contact_url()); ?>">Contact Now</a>
                    <a class="hs-btn hs-btn-primary" href="#services">View Services</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php hs_include_footer(); ?>
