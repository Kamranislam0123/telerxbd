<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/data.php';

$slug = isset($_GET['service']) ? preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['service'])) : '';
$service = hs_get_service($slug);

if (!$service) {
    hs_include_header('Service Not Found');
    ?>
    <style>
    .hs-missing{padding:90px 16px;text-align:center;font-family:inherit}.hs-missing h1{font-size:42px;margin-bottom:12px}.hs-missing p{color:#667085}.hs-missing a{display:inline-flex;margin-top:18px;padding:12px 20px;border-radius:999px;background:#0E82FD;color:#fff;text-decoration:none;font-weight:800}
    </style>
    <main class="hs-missing"><h1>Service not found</h1><p>The service you are looking for may have been moved or renamed.</p><a href="<?php echo hs_e(hs_home_url()); ?>">Back to Home Service</a></main>
    <?php
    hs_include_footer();
    exit;
}

hs_include_header($service['title'] . ' | TeleRx Home Service');
?>

<style>
:root { --hs-primary:#0E82FD; --hs-primary-dark:#0867CA; --hs-secondary:#00B894; --hs-ink:#102033; --hs-muted:#667085; --hs-soft:#F4F8FF; --hs-border:#E5EDF7; --hs-white:#fff; --hs-shadow:0 18px 40px rgba(16,32,51,.10); --hs-radius:24px; }
.hs-detail-page,.hs-detail-page *{box-sizing:border-box}.hs-detail-page{width:100%;overflow-x:hidden;color:var(--hs-ink);background:#fff}.hs-container{width:min(1180px,calc(100% - 32px));margin:0 auto}.hs-detail-hero{padding:70px 0;background:radial-gradient(circle at 12% 10%,rgba(14,130,253,.16),transparent 32%),linear-gradient(135deg,#F7FBFF 0%,#fff 56%,#EFFFFA 100%)}.hs-breadcrumb{display:flex;gap:8px;flex-wrap:wrap;color:var(--hs-muted);font-size:14px;font-weight:700;margin-bottom:18px}.hs-breadcrumb a{color:var(--hs-primary-dark);text-decoration:none}.hs-detail-grid{display:grid;grid-template-columns:1fr .92fr;gap:40px;align-items:center}.hs-kicker{display:inline-flex;gap:8px;align-items:center;padding:9px 14px;border-radius:999px;background:rgba(14,130,253,.10);color:var(--hs-primary-dark);font-weight:800;font-size:14px;margin-bottom:16px}.hs-kicker::before{content:"";width:8px;height:8px;border-radius:50%;background:var(--hs-secondary)}.hs-detail-hero h1{margin:0 0 16px;font-size:clamp(34px,4.6vw,56px);line-height:1.08;letter-spacing:-1px}.hs-detail-hero p{margin:0;color:var(--hs-muted);font-size:18px;line-height:1.75}.hs-actions{display:flex;flex-wrap:wrap;gap:14px;margin-top:28px}.hs-btn{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:13px 22px;border-radius:999px;font-weight:800;text-decoration:none;transition:.22s ease}.hs-btn-primary{background:linear-gradient(135deg,var(--hs-primary),var(--hs-secondary));color:#fff;box-shadow:0 14px 28px rgba(14,130,253,.22)}.hs-btn-primary:hover{color:#fff;transform:translateY(-2px)}.hs-btn-outline{background:#fff;color:var(--hs-primary-dark);border:1px solid rgba(14,130,253,.20)}.hs-btn-outline:hover{color:var(--hs-primary-dark);transform:translateY(-2px)}.hs-hero-img{border-radius:30px;background:#fff;border:1px solid var(--hs-border);box-shadow:var(--hs-shadow);padding:18px}.hs-hero-img img{width:100%;height:auto;border-radius:24px;display:block}.hs-section{padding:70px 0}.hs-main-grid{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:34px;align-items:start}.hs-article{background:#fff;border:1px solid var(--hs-border);border-radius:28px;box-shadow:var(--hs-shadow);padding:34px}.hs-article h2{font-size:30px;line-height:1.2;margin:0 0 16px}.hs-article p{color:var(--hs-muted);line-height:1.85;margin:0 0 26px}.hs-list-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin:18px 0 30px}.hs-list-box{background:var(--hs-soft);border:1px solid var(--hs-border);border-radius:22px;padding:22px}.hs-list-box h3{margin:0 0 14px;font-size:20px}.hs-list-box ul{margin:0;padding:0;list-style:none;display:grid;gap:10px}.hs-list-box li{display:flex;gap:10px;color:#4D5E70;line-height:1.55;font-size:14px}.hs-list-box li::before{content:"✓";color:#22C55E;font-weight:900;flex:0 0 auto}.hs-process{counter-reset:steps;display:grid;gap:12px;margin-top:18px}.hs-process li{list-style:none;display:grid;grid-template-columns:40px 1fr;gap:12px;align-items:start;color:#4D5E70;line-height:1.65}.hs-process li::before{counter-increment:steps;content:counter(steps);display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--hs-primary),var(--hs-secondary));color:#fff;font-weight:900}.hs-note{border-left:4px solid var(--hs-primary);background:#F7FBFF;border-radius:18px;padding:18px 20px;color:var(--hs-muted);line-height:1.7}.hs-sidebar{display:grid;gap:18px;position:sticky;top:100px}.hs-side-card{background:#fff;border:1px solid var(--hs-border);border-radius:24px;box-shadow:0 14px 32px rgba(16,32,51,.08);padding:24px}.hs-side-card h3{margin:0 0 14px;font-size:22px}.hs-side-card p{margin:0 0 16px;color:var(--hs-muted);line-height:1.7}.hs-service-links{display:grid;gap:8px}.hs-service-links a{display:block;padding:11px 13px;border-radius:14px;background:#F7FBFF;color:#344054;text-decoration:none;font-weight:700;font-size:14px}.hs-service-links a.active,.hs-service-links a:hover{background:rgba(14,130,253,.10);color:var(--hs-primary-dark)}.hs-contact-box{background:linear-gradient(135deg,#0E82FD,#00B894);color:#fff}.hs-contact-box h3,.hs-contact-box p{color:#fff}.hs-contact-box p{opacity:.9}@media(max-width:991.98px){.hs-detail-grid,.hs-main-grid{grid-template-columns:1fr}.hs-sidebar{position:static}.hs-list-grid{grid-template-columns:1fr}}@media(max-width:575.98px){.hs-container{width:min(100%,calc(100% - 24px))}.hs-detail-hero{padding:50px 0}.hs-section{padding:52px 0}.hs-article{padding:24px 18px}}
</style>

<main class="hs-detail-page">
    <section class="hs-detail-hero">
        <div class="hs-container">
            <div class="hs-breadcrumb"><a href="<?php echo hs_e(hs_home_url()); ?>">Home Service</a><span>/</span><span><?php echo hs_e($service['title']); ?></span></div>
            <div class="hs-detail-grid">
                <div>
                    <div class="hs-kicker"><?php echo hs_e($service['category']); ?></div>
                    <h1><?php echo hs_e($service['title']); ?></h1>
                    <p><?php echo hs_e($service['short']); ?></p>
                    <div class="hs-actions">
                        <a class="hs-btn hs-btn-primary" href="<?php echo hs_e(hs_contact_url()); ?>">Contact TeleRx</a>
                        <a class="hs-btn hs-btn-outline" href="<?php echo hs_e(hs_home_url()); ?>">View All Services</a>
                    </div>
                </div>
                <div class="hs-hero-img"><img src="<?php echo hs_e(hs_image($service['image'])); ?>" alt="<?php echo hs_e($service['title']); ?>"></div>
            </div>
        </div>
    </section>

    <section class="hs-section">
        <div class="hs-container">
            <div class="hs-main-grid">
                <article class="hs-article">
                    <h2>Service Overview</h2>
                    <p><?php echo hs_e($service['overview']); ?></p>

                    <div class="hs-list-grid">
                        <div class="hs-list-box">
                            <h3>Key Highlights</h3>
                            <ul><?php foreach ($service['highlights'] as $item): ?><li><?php echo hs_e($item); ?></li><?php endforeach; ?></ul>
                        </div>
                        <div class="hs-list-box">
                            <h3>Suitable For</h3>
                            <ul><?php foreach ($service['ideal_for'] as $item): ?><li><?php echo hs_e($item); ?></li><?php endforeach; ?></ul>
                        </div>
                    </div>

                    <div class="hs-list-box" style="margin-bottom:30px;">
                        <h3>What This Service May Include</h3>
                        <ul><?php foreach ($service['included'] as $item): ?><li><?php echo hs_e($item); ?></li><?php endforeach; ?></ul>
                    </div>

                    <h2>How to Request This Service</h2>
                    <ol class="hs-process"><?php foreach ($service['process'] as $step): ?><li><?php echo hs_e($step); ?></li><?php endforeach; ?></ol>

                    <div class="hs-note" style="margin-top:30px;"><strong>Important Note:</strong> <?php echo hs_e($service['note']); ?></div>
                </article>

                <aside class="hs-sidebar">
                    <div class="hs-side-card hs-contact-box">
                        <h3>Need this service?</h3>
                        <p>Contact TeleRx with patient condition, location and preferred time. Our team will guide you about availability and next steps.</p>
                        <a class="hs-btn hs-btn-outline" href="<?php echo hs_e(hs_contact_url()); ?>">Contact Now</a>
                    </div>
                    <div class="hs-side-card">
                        <h3>All Home Services</h3>
                        <div class="hs-service-links">
                            <?php foreach ($home_services as $item_slug => $item): ?>
                                <a class="<?php echo $item_slug === $slug ? 'active' : ''; ?>" href="<?php echo hs_e(hs_service_url($item_slug)); ?>"><?php echo hs_e($item['title']); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

<?php hs_include_footer(); ?>
