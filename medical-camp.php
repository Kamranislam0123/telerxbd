<?php
/**
 * TeleRx Bangladesh — Medical Camp main page
 *
 * Gallery setup:
 * Upload camp photographs to: assets/img/medical-camp/
 * Supported formats: JPG, JPEG, PNG, WEBP and AVIF.
 * The page automatically discovers and displays new photographs.
 */

declare(strict_types=1);

$galleryDirectory = __DIR__ . '/assets/img/medical-camp';
$galleryPublicPath = 'assets/img/medical-camp';
$galleryImages = [];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

if (is_dir($galleryDirectory) && is_readable($galleryDirectory)) {
    $files = scandir($galleryDirectory) ?: [];

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $extension = strtolower((string) pathinfo($file, PATHINFO_EXTENSION));
        $absolutePath = $galleryDirectory . DIRECTORY_SEPARATOR . $file;

        if (!in_array($extension, $allowedExtensions, true) || !is_file($absolutePath)) {
            continue;
        }

        $name = (string) pathinfo($file, PATHINFO_FILENAME);
        $name = preg_replace('/[-_]+/', ' ', $name) ?? $name;
        $name = preg_replace('/\s+/', ' ', trim($name)) ?? trim($name);
        $caption = $name !== '' ? ucwords($name) : 'TeleRx Medical Camp';

        $galleryImages[] = [
            'file' => $file,
            'src' => $galleryPublicPath . '/' . rawurlencode($file),
            'caption' => $caption,
        ];
    }
}

usort(
    $galleryImages,
    static function (array $a, array $b): int {
        return strnatcasecmp($a['file'], $b['file']);
    }
);

/** Load the existing site header and inject the page-specific stylesheet into <head>. */
ob_start();
include __DIR__ . '/header.php';
$siteHeader = (string) ob_get_clean();
$siteHeader = preg_replace(
    '/<title>.*?<\/title>/is',
    '<title>Medical Camp | TeleRx Bangladesh</title>',
    $siteHeader,
    1
) ?? $siteHeader;
$siteHeader = str_replace(
    '</head>',
    "    <link rel=\"stylesheet\" href=\"assets/css/medical-camp.css\">\n</head>",
    $siteHeader
);
echo $siteHeader;
?>

<main class="trxmc-page">
    <section class="trxmc-hero">
        <div class="container">
            <div class="trxmc-hero-grid">
                <div class="trxmc-hero-copy">
                    <span class="trxmc-kicker">
                        <i class="fa-solid fa-house-medical-circle-check" aria-hidden="true"></i>
                        Community healthcare initiative
                    </span>
                    <h1>Online medical care, delivered through organized community camps.</h1>
                    <p>
                        TeleRx Bangladesh arranges medical camps in villages, schools, educational
                        institutions, orphanages and other community locations. At each camp, patients
                        receive guided online consultation with doctors through the TeleRx website.
                    </p>

                    <div class="trxmc-hero-actions">
                        <a class="trxmc-btn trxmc-btn-primary" href="#how-it-works">
                            See how it works
                            <i class="fa-solid fa-arrow-down" aria-hidden="true"></i>
                        </a>
                        <a class="trxmc-btn trxmc-btn-light" href="contact">
                            Organize a medical camp
                            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                        </a>
                    </div>

                    <div class="trxmc-hero-points" aria-label="Medical camp highlights">
                        <span><i class="fa-solid fa-circle-check"></i> On-site patient support</span>
                        <span><i class="fa-solid fa-circle-check"></i> Online doctor consultation</span>
                        <span><i class="fa-solid fa-circle-check"></i> Digital advice and follow-up</span>
                    </div>
                </div>

                <div class="trxmc-hero-visual" aria-label="TeleRx medical camp service overview">
                    <?php if (!empty($galleryImages)): ?>
                        <div class="trxmc-hero-photo trxmc-hero-photo-main">
                            <img src="<?php echo htmlspecialchars($galleryImages[0]['src'], ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($galleryImages[0]['caption'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <?php if (isset($galleryImages[1])): ?>
                            <div class="trxmc-hero-photo trxmc-hero-photo-small">
                                <img src="<?php echo htmlspecialchars($galleryImages[1]['src'], ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo htmlspecialchars($galleryImages[1]['caption'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="trxmc-visual-placeholder">
                            <div class="trxmc-placeholder-icon"><i class="fa-solid fa-laptop-medical"></i></div>
                            <strong>TeleRx Medical Camp</strong>
                            <span>Community venue</span>
                            <div class="trxmc-placeholder-flow">
                                <span><i class="fa-solid fa-clipboard-user"></i> Registration</span>
                                <i class="fa-solid fa-arrow-right"></i>
                                <span><i class="fa-solid fa-user-doctor"></i> Doctor</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="trxmc-floating-card trxmc-floating-card-one">
                        <i class="fa-solid fa-wifi"></i>
                        <span><strong>Connected care</strong>Telemedicine-enabled camp</span>
                    </div>
                    <div class="trxmc-floating-card trxmc-floating-card-two">
                        <i class="fa-solid fa-file-prescription"></i>
                        <span><strong>After consultation</strong>Advice, prescription or referral</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="trxmc-overview trxmc-section">
        <div class="container">
            <div class="trxmc-overview-grid">
                <div class="trxmc-section-heading">
                    <span class="trxmc-label">About the program</span>
                    <h2>Healthcare access for communities that need a simpler connection to doctors.</h2>
                    <p>
                        A TeleRx medical camp combines local coordination with online medical consultation.
                        Our on-site team helps patients complete registration, records the information needed
                        for consultation and supports them while they speak with a doctor online.
                    </p>
                    <p>
                        The doctor assesses the patient remotely, provides appropriate advice and explains
                        whether follow-up, medicine, tests or an in-person medical visit may be needed.
                    </p>
                </div>

                <div class="trxmc-overview-card">
                    <div class="trxmc-overview-card-head">
                        <span>One camp, one coordinated care journey</span>
                        <i class="fa-solid fa-stethoscope"></i>
                    </div>
                    <ul>
                        <li><span>01</span><div><strong>Community coordination</strong><small>Location, schedule and patient flow are planned with the host.</small></div></li>
                        <li><span>02</span><div><strong>Supported digital access</strong><small>Patients receive help using the TeleRx online consultation process.</small></div></li>
                        <li><span>03</span><div><strong>Clear next steps</strong><small>Consultation ends with advice, follow-up instructions or referral guidance.</small></div></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="trxmc-venues trxmc-section">
        <div class="container">
            <div class="trxmc-centered-heading">
                <span class="trxmc-label">Where we organize camps</span>
                <h2>Flexible programs for different community settings.</h2>
                <p>The camp format can be adapted to the venue, patient group, connectivity and local support available.</p>
            </div>

            <div class="trxmc-venue-grid">
                <article>
                    <div class="trxmc-icon-box"><i class="fa-solid fa-tree-city"></i></div>
                    <h3>Village communities</h3>
                    <p>Organized access for people who face distance, transport or specialist-access barriers.</p>
                </article>
                <article>
                    <div class="trxmc-icon-box"><i class="fa-solid fa-school"></i></div>
                    <h3>Schools & institutions</h3>
                    <p>Planned consultation support for students, teachers, employees and nearby families.</p>
                </article>
                <article>
                    <div class="trxmc-icon-box"><i class="fa-solid fa-hands-holding-child"></i></div>
                    <h3>Orphanages</h3>
                    <p>Coordinated medical consultation for children and caregivers in a familiar environment.</p>
                </article>
                <article>
                    <div class="trxmc-icon-box"><i class="fa-solid fa-people-group"></i></div>
                    <h3>NGO & community programs</h3>
                    <p>Partner-led camps designed around a project area, beneficiary group or health campaign.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="trxmc-workflow trxmc-section" id="how-it-works">
        <div class="container">
            <div class="trxmc-centered-heading trxmc-heading-light">
                <span class="trxmc-label trxmc-label-light">How a TeleRx camp works</span>
                <h2>A clear process from planning to follow-up.</h2>
                <p>Every stage is designed to keep the patient journey organized and easy to understand.</p>
            </div>

            <div class="trxmc-workflow-grid">
                <article>
                    <span class="trxmc-step-number">01</span>
                    <i class="fa-solid fa-calendar-check"></i>
                    <h3>Camp planning</h3>
                    <p>We coordinate the venue, date, expected patients, internet access and support team.</p>
                </article>
                <article>
                    <span class="trxmc-step-number">02</span>
                    <i class="fa-solid fa-clipboard-user"></i>
                    <h3>Registration</h3>
                    <p>Patient details, relevant history and basic measurements are recorded before consultation.</p>
                </article>
                <article>
                    <span class="trxmc-step-number">03</span>
                    <i class="fa-solid fa-video"></i>
                    <h3>Online consultation</h3>
                    <p>The patient connects with a TeleRx doctor while the field team provides technical support.</p>
                </article>
                <article>
                    <span class="trxmc-step-number">04</span>
                    <i class="fa-solid fa-file-medical"></i>
                    <h3>Advice & follow-up</h3>
                    <p>The patient receives medical guidance, prescription support or referral instructions.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="trxmc-includes trxmc-section">
        <div class="container">
            <div class="trxmc-includes-grid">
                <div class="trxmc-includes-copy">
                    <span class="trxmc-label">What the camp may include</span>
                    <h2>Practical support before, during and after consultation.</h2>
                    <p>
                        The exact service mix depends on the camp plan and available resources. TeleRx can
                        coordinate the core digital consultation journey and selected supporting activities.
                    </p>
                    <a class="trxmc-text-link" href="contact">Discuss a camp plan <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>

                <div class="trxmc-check-grid">
                    <div><i class="fa-solid fa-user-plus"></i><span><strong>Patient registration</strong>Assistance with the TeleRx consultation process.</span></div>
                    <div><i class="fa-solid fa-heart-pulse"></i><span><strong>Basic measurements</strong>Selected vital signs or basic screening where appropriate.</span></div>
                    <div><i class="fa-solid fa-user-doctor"></i><span><strong>Doctor consultation</strong>Online medical consultation through the TeleRx platform.</span></div>
                    <div><i class="fa-solid fa-notes-medical"></i><span><strong>Digital guidance</strong>Advice, prescription information and next-step instructions.</span></div>
                    <div><i class="fa-solid fa-bullhorn"></i><span><strong>Health awareness</strong>Simple health information tailored to the participating group.</span></div>
                    <div><i class="fa-solid fa-route"></i><span><strong>Referral direction</strong>Guidance when physical examination or urgent care is required.</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="trxmc-gallery trxmc-section" id="medical-camp-gallery">
        <div class="container">
            <div class="trxmc-gallery-heading">
                <div>
                    <span class="trxmc-label">Medical camp gallery</span>
                    <h2>Moments from our community healthcare activities.</h2>
                </div>
                <p>
                    Add photographs to <code>assets/img/medical-camp/</code>. New images will appear here automatically.
                </p>
            </div>

            <?php if (!empty($galleryImages)): ?>
                <div class="trxmc-gallery-grid">
                    <?php foreach ($galleryImages as $index => $image): ?>
                        <button class="trxmc-gallery-item<?php echo $index === 0 ? ' trxmc-gallery-item-featured' : ''; ?>"
                                type="button"
                                data-trxmc-image="<?php echo htmlspecialchars($image['src'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-trxmc-caption="<?php echo htmlspecialchars($image['caption'], ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Open <?php echo htmlspecialchars($image['caption'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="<?php echo htmlspecialchars($image['src'], ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($image['caption'], ENT_QUOTES, 'UTF-8'); ?>"
                                 loading="lazy">
                            <span><i class="fa-solid fa-expand"></i><?php echo htmlspecialchars($image['caption'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="trxmc-gallery-empty">
                    <?php
                    $placeholders = [
                        ['fa-people-group', 'Community gathering'],
                        ['fa-user-doctor', 'Online consultation'],
                        ['fa-heart-pulse', 'Basic health check'],
                        ['fa-school', 'Institutional camp'],
                        ['fa-hands-holding-child', 'Child healthcare support'],
                        ['fa-handshake-angle', 'Partner collaboration'],
                    ];
                    foreach ($placeholders as [$icon, $label]):
                    ?>
                        <div><i class="fa-solid <?php echo $icon; ?>"></i><span><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <?php endforeach; ?>
                    <p><i class="fa-solid fa-images"></i> Upload your camp photographs to activate the live gallery.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="trxmc-partner trxmc-section">
        <div class="container">
            <div class="trxmc-partner-panel">
                <div>
                    <span class="trxmc-label trxmc-label-light">Host a TeleRx medical camp</span>
                    <h2>Bring guided online medical consultation to your community.</h2>
                    <p>Schools, NGOs, orphanages, community groups and institutions can contact us to discuss a suitable camp plan.</p>
                </div>
                <a class="trxmc-btn trxmc-btn-white" href="contact">
                    Contact TeleRx
                    <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="trxmc-faq trxmc-section">
        <div class="container">
            <div class="trxmc-centered-heading">
                <span class="trxmc-label">Frequently asked questions</span>
                <h2>Important information for host organizations.</h2>
            </div>

            <div class="trxmc-faq-grid">
                <details open>
                    <summary>Who can request a medical camp?</summary>
                    <p>Schools, educational institutions, orphanages, NGOs, community groups and other suitable organizations may contact TeleRx.</p>
                </details>
                <details>
                    <summary>How do patients consult a doctor?</summary>
                    <p>Patients use the TeleRx website for online consultation, with on-site assistance from the camp support team.</p>
                </details>
                <details>
                    <summary>Can more photographs be added later?</summary>
                    <p>Yes. Upload additional JPG, PNG, WebP or AVIF files to the gallery folder and the page will display them automatically.</p>
                </details>
                <details>
                    <summary>Does an online camp replace emergency treatment?</summary>
                    <p>No. Patients with emergency symptoms or those requiring physical examination must be referred to an appropriate healthcare facility.</p>
                </details>
            </div>
        </div>
    </section>
</main>

<?php if (!empty($galleryImages)): ?>
<div class="trxmc-lightbox" data-trxmc-lightbox aria-hidden="true" role="dialog" aria-modal="true" aria-label="Medical camp image viewer">
    <button class="trxmc-lightbox-close" type="button" aria-label="Close image viewer"><i class="fa-solid fa-xmark"></i></button>
    <button class="trxmc-lightbox-nav trxmc-lightbox-prev" type="button" aria-label="Previous image"><i class="fa-solid fa-chevron-left"></i></button>
    <figure>
        <img src="" alt="">
        <figcaption></figcaption>
    </figure>
    <button class="trxmc-lightbox-nav trxmc-lightbox-next" type="button" aria-label="Next image"><i class="fa-solid fa-chevron-right"></i></button>
</div>

<script>
(function () {
    'use strict';

    var items = Array.prototype.slice.call(document.querySelectorAll('[data-trxmc-image]'));
    var lightbox = document.querySelector('[data-trxmc-lightbox]');
    if (!items.length || !lightbox) return;

    var image = lightbox.querySelector('img');
    var caption = lightbox.querySelector('figcaption');
    var closeButton = lightbox.querySelector('.trxmc-lightbox-close');
    var previousButton = lightbox.querySelector('.trxmc-lightbox-prev');
    var nextButton = lightbox.querySelector('.trxmc-lightbox-next');
    var currentIndex = 0;

    function show(index) {
        currentIndex = (index + items.length) % items.length;
        var item = items[currentIndex];
        image.src = item.getAttribute('data-trxmc-image') || '';
        image.alt = item.getAttribute('data-trxmc-caption') || 'TeleRx Medical Camp';
        caption.textContent = image.alt;
    }

    function open(index) {
        show(index);
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('trxmc-no-scroll');
        closeButton.focus();
    }

    function close() {
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('trxmc-no-scroll');
        items[currentIndex].focus();
    }

    items.forEach(function (item, index) {
        item.addEventListener('click', function () { open(index); });
    });
    closeButton.addEventListener('click', close);
    previousButton.addEventListener('click', function () { show(currentIndex - 1); });
    nextButton.addEventListener('click', function () { show(currentIndex + 1); });
    lightbox.addEventListener('click', function (event) { if (event.target === lightbox) close(); });
    document.addEventListener('keydown', function (event) {
        if (!lightbox.classList.contains('is-open')) return;
        if (event.key === 'Escape') close();
        if (event.key === 'ArrowLeft') show(currentIndex - 1);
        if (event.key === 'ArrowRight') show(currentIndex + 1);
    });
}());
</script>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
