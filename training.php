<?php
/**
 * TeleRx Bangladesh — Medical Equipment Training main page
 */

declare(strict_types=1);

ob_start();
include __DIR__ . '/header.php';
$siteHeader = (string) ob_get_clean();
$siteHeader = preg_replace(
    '/<title>.*?<\/title>/is',
    '<title>Medical Equipment Training | TeleRx Bangladesh</title>',
    $siteHeader,
    1
) ?? $siteHeader;
$siteHeader = str_replace(
    '</head>',
    "    <link rel=\"stylesheet\" href=\"assets/css/training.css\">\n</head>",
    $siteHeader
);
echo $siteHeader;
?>

<main class="trxtr-page">
    <section class="trxtr-hero">
        <div class="container">
            <div class="trxtr-hero-grid">
                <div class="trxtr-hero-copy">
                    <span class="trxtr-kicker">
                        <i class="fa-solid fa-person-chalkboard" aria-hidden="true"></i>
                        Practical healthcare skills
                    </span>
                    <h1>Medical equipment training built around safe, confident and correct use.</h1>
                    <p>
                        TeleRx Bangladesh provides practical training for medical equipment supplied by us,
                        along with foundational skills used during primary patient assessment. Our long-term
                        plan also includes structured training relevant to Type C diagnostic-center services.
                    </p>

                    <div class="trxtr-hero-actions">
                        <a class="trxtr-btn trxtr-btn-primary" href="#training-programs">
                            Explore training areas
                            <i class="fa-solid fa-arrow-down"></i>
                        </a>
                        <a class="trxtr-btn trxtr-btn-outline" href="contact">
                            Request training
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </a>
                    </div>

                    <div class="trxtr-hero-meta">
                        <span><i class="fa-solid fa-screwdriver-wrench"></i> Device operation</span>
                        <span><i class="fa-solid fa-heart-pulse"></i> Primary assessment</span>
                        <span><i class="fa-solid fa-shield-heart"></i> Safety & documentation</span>
                    </div>
                </div>

                <div class="trxtr-hero-visual" aria-label="Medical equipment training modules">
                    <div class="trxtr-dashboard-card">
                        <div class="trxtr-dashboard-head">
                            <div>
                                <small>TeleRx Training</small>
                                <strong>Practical Learning Dashboard</strong>
                            </div>
                            <span><i class="fa-solid fa-graduation-cap"></i></span>
                        </div>
                        <div class="trxtr-dashboard-progress">
                            <div class="trxtr-progress-title"><span>Training pathway</span><strong>4 stages</strong></div>
                            <div class="trxtr-progress-bar"><span></span></div>
                        </div>
                        <div class="trxtr-dashboard-list">
                            <div><i class="fa-solid fa-book-medical"></i><span><strong>Understand</strong><small>Purpose, parts and limits</small></span><b>01</b></div>
                            <div><i class="fa-solid fa-eye"></i><span><strong>Observe</strong><small>Trainer demonstration</small></span><b>02</b></div>
                            <div><i class="fa-solid fa-hands"></i><span><strong>Practice</strong><small>Supervised operation</small></span><b>03</b></div>
                            <div><i class="fa-solid fa-clipboard-check"></i><span><strong>Assess</strong><small>Skills verification</small></span><b>04</b></div>
                        </div>
                    </div>
                    <div class="trxtr-floating-module trxtr-floating-module-one"><i class="fa-solid fa-gauge-high"></i><span>BP Monitor</span></div>
                    <div class="trxtr-floating-module trxtr-floating-module-two"><i class="fa-solid fa-droplet"></i><span>Glucometer</span></div>
                    <div class="trxtr-floating-module trxtr-floating-module-three"><i class="fa-solid fa-wave-square"></i><span>Pulse Oximeter</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="trxtr-intro trxtr-section">
        <div class="container">
            <div class="trxtr-intro-grid">
                <div class="trxtr-section-heading">
                    <span class="trxtr-label">Why this training matters</span>
                    <h2>Correct equipment use improves measurement quality and supports safer decisions.</h2>
                    <p>
                        Medical devices can produce misleading results when the user applies the wrong technique,
                        prepares the patient incorrectly, overlooks device limitations or fails to maintain the equipment.
                    </p>
                    <p>
                        Our training approach explains the full workflow: when the device is appropriate, how to prepare,
                        how to operate it, how to record the result, what common mistakes to avoid and when to seek clinical guidance.
                    </p>
                </div>

                <div class="trxtr-benefit-panel">
                    <div class="trxtr-benefit-title"><i class="fa-solid fa-circle-check"></i><span><strong>Training outcomes</strong>What participants should gain</span></div>
                    <div class="trxtr-benefit-grid">
                        <div><span>01</span><p>Identify equipment components and intended use.</p></div>
                        <div><span>02</span><p>Follow a consistent operating procedure.</p></div>
                        <div><span>03</span><p>Recognize common errors and unsafe practice.</p></div>
                        <div><span>04</span><p>Document results and escalate abnormal findings.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="trxtr-programs trxtr-section" id="training-programs">
        <div class="container">
            <div class="trxtr-centered-heading">
                <span class="trxtr-label">Training programs</span>
                <h2>Three connected learning areas.</h2>
                <p>Programs can be delivered as individual modules or combined according to the organization’s needs.</p>
            </div>

            <div class="trxtr-program-grid">
                <article class="trxtr-program-card trxtr-program-card-primary">
                    <div class="trxtr-program-icon"><i class="fa-solid fa-kit-medical"></i></div>
                    <span class="trxtr-program-status">Core program</span>
                    <h3>TeleRx-supplied medical equipment</h3>
                    <p>Product-specific training for equipment supplied to customers, institutions and healthcare teams.</p>
                    <ul>
                        <li><i class="fa-solid fa-check"></i> Device setup and correct operation</li>
                        <li><i class="fa-solid fa-check"></i> Accessories and consumables</li>
                        <li><i class="fa-solid fa-check"></i> Cleaning, storage and routine care</li>
                        <li><i class="fa-solid fa-check"></i> Common errors and basic troubleshooting</li>
                    </ul>
                </article>

                <article class="trxtr-program-card">
                    <div class="trxtr-program-icon"><i class="fa-solid fa-heart-pulse"></i></div>
                    <span class="trxtr-program-status">Foundation skills</span>
                    <h3>Primary patient-assessment support</h3>
                    <p>Basic equipment and observation skills often needed during an initial patient assessment.</p>
                    <ul>
                        <li><i class="fa-solid fa-check"></i> Vital-sign measurement workflow</li>
                        <li><i class="fa-solid fa-check"></i> Patient preparation and positioning</li>
                        <li><i class="fa-solid fa-check"></i> Recording and communicating results</li>
                        <li><i class="fa-solid fa-check"></i> Warning signs and referral boundaries</li>
                    </ul>
                </article>

                <article class="trxtr-program-card trxtr-program-card-future">
                    <div class="trxtr-program-icon"><i class="fa-solid fa-microscope"></i></div>
                    <span class="trxtr-program-status">Future expansion</span>
                    <h3>Type C diagnostic-center training pathway</h3>
                    <p>Planned modules related to selected tests, equipment and workflows used in Type C diagnostic-center settings.</p>
                    <ul>
                        <li><i class="fa-solid fa-check"></i> Equipment workflow principles</li>
                        <li><i class="fa-solid fa-check"></i> Specimen, safety and quality awareness</li>
                        <li><i class="fa-solid fa-check"></i> Documentation and result-handling basics</li>
                        <li><i class="fa-solid fa-check"></i> Scope defined by applicable requirements</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section class="trxtr-equipment trxtr-section">
        <div class="container">
            <div class="trxtr-equipment-grid">
                <div class="trxtr-equipment-copy">
                    <span class="trxtr-label trxtr-label-light">Example equipment modules</span>
                    <h2>Hands-on guidance for devices used in everyday healthcare support.</h2>
                    <p>
                        Final modules depend on the equipment supplied and the participant group. The examples below
                        show the type of device-based training TeleRx can organize.
                    </p>
                    <a class="trxtr-text-link trxtr-text-link-light" href="Products/products.php">View TeleRx products <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>

                <div class="trxtr-device-grid">
                    <article><i class="fa-solid fa-gauge-high"></i><span><strong>Blood pressure monitor</strong><small>Cuff selection, positioning and reading technique</small></span></article>
                    <article><i class="fa-solid fa-droplet"></i><span><strong>Blood glucose meter</strong><small>Strip handling, sampling and device hygiene</small></span></article>
                    <article><i class="fa-solid fa-wave-square"></i><span><strong>Pulse oximeter</strong><small>Probe placement, signal quality and limitations</small></span></article>
                    <article><i class="fa-solid fa-temperature-half"></i><span><strong>Digital thermometer</strong><small>Measurement method, cleaning and recording</small></span></article>
                    <article><i class="fa-solid fa-lungs"></i><span><strong>Nebulizer</strong><small>Assembly, operation, cleaning and safe storage</small></span></article>
                    <article><i class="fa-solid fa-weight-scale"></i><span><strong>Basic measurements</strong><small>Weight, height and structured data recording</small></span></article>
                </div>
            </div>
        </div>
    </section>

    <section class="trxtr-primary trxtr-section">
        <div class="container">
            <div class="trxtr-centered-heading">
                <span class="trxtr-label">Primary assessment foundation</span>
                <h2>Training beyond the device button.</h2>
                <p>Participants learn the supporting steps that make a measurement more useful and clinically responsible.</p>
            </div>

            <div class="trxtr-primary-grid">
                <article>
                    <span>01</span>
                    <i class="fa-solid fa-user-check"></i>
                    <h3>Patient preparation</h3>
                    <p>Explain the procedure, confirm appropriate rest or positioning and reduce avoidable measurement errors.</p>
                </article>
                <article>
                    <span>02</span>
                    <i class="fa-solid fa-list-check"></i>
                    <h3>Systematic observation</h3>
                    <p>Follow a consistent sequence and note relevant symptoms, history and basic observations.</p>
                </article>
                <article>
                    <span>03</span>
                    <i class="fa-solid fa-file-pen"></i>
                    <h3>Accurate documentation</h3>
                    <p>Record the result, device, time and important circumstances without changing or guessing data.</p>
                </article>
                <article>
                    <span>04</span>
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <h3>Escalation awareness</h3>
                    <p>Recognize when an abnormal value or symptom requires review by a qualified medical professional.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="trxtr-method trxtr-section">
        <div class="container">
            <div class="trxtr-method-layout">
                <div class="trxtr-method-copy">
                    <span class="trxtr-label">How training is delivered</span>
                    <h2>A practical learning cycle with demonstration and assessment.</h2>
                    <p>Training format may vary by equipment, group size, venue and required learning outcome.</p>
                </div>

                <div class="trxtr-method-steps">
                    <article><b>01</b><div><i class="fa-solid fa-book-medical"></i></div><span><strong>Orientation</strong><small>Purpose, components, limits and safety.</small></span></article>
                    <article><b>02</b><div><i class="fa-solid fa-person-chalkboard"></i></div><span><strong>Demonstration</strong><small>The trainer shows the complete workflow.</small></span></article>
                    <article><b>03</b><div><i class="fa-solid fa-hands"></i></div><span><strong>Guided practice</strong><small>Participants practise under supervision.</small></span></article>
                    <article><b>04</b><div><i class="fa-solid fa-clipboard-check"></i></div><span><strong>Skills check</strong><small>Key steps and safety points are assessed.</small></span></article>
                </div>
            </div>
        </div>
    </section>

    <section class="trxtr-audience trxtr-section">
        <div class="container">
            <div class="trxtr-audience-grid">
                <div class="trxtr-section-heading">
                    <span class="trxtr-label">Who can benefit</span>
                    <h2>Programs adapted to the role and existing skill level of participants.</h2>
                    <p>Eligibility and course depth should be finalized according to the equipment and scope of practice involved.</p>
                </div>

                <div class="trxtr-audience-list">
                    <div><i class="fa-solid fa-user-nurse"></i><span><strong>Healthcare support staff</strong><small>Clinic, care and patient-support teams</small></span></div>
                    <div><i class="fa-solid fa-people-group"></i><span><strong>NGO field teams</strong><small>Community and project-based health workers</small></span></div>
                    <div><i class="fa-solid fa-building"></i><span><strong>Institutional teams</strong><small>Schools, organizations and care facilities</small></span></div>
                    <div><i class="fa-solid fa-truck-medical"></i><span><strong>Equipment users</strong><small>Operators responsible for supplied devices</small></span></div>
                    <div><i class="fa-solid fa-microscope"></i><span><strong>Diagnostic support personnel</strong><small>Eligible staff seeking structured skills</small></span></div>
                    <div><i class="fa-solid fa-user-graduate"></i><span><strong>Qualified learners</strong><small>Participants meeting module requirements</small></span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="trxtr-coming trxtr-section">
        <div class="container">
            <div class="trxtr-coming-panel">
                <div class="trxtr-coming-duration">
                    <span><i class="fa-solid fa-hourglass-half"></i> Coming soon</span>
                    <strong>6</strong>
                    <small>Month Course</small>
                </div>

                <div class="trxtr-coming-copy">
                    <span class="trxtr-label trxtr-label-light">Extended training pathway</span>
                    <h2>Six-Month Medical Equipment & Primary Diagnostic Skills Course</h2>
                    <p>
                        A planned structured course combining equipment handling, foundational patient assessment,
                        infection-prevention awareness, documentation, supervised practical sessions and selected
                        diagnostic-center workflow modules.
                    </p>
                    <div class="trxtr-coming-tags">
                        <span>Structured syllabus</span>
                        <span>Practical sessions</span>
                        <span>Skills assessment</span>
                        <span>Future Type C modules</span>
                    </div>
                </div>

                <a class="trxtr-btn trxtr-btn-white" href="contact">
                    Register interest
                    <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="trxtr-standards trxtr-section">
        <div class="container">
            <div class="trxtr-standards-panel">
                <i class="fa-solid fa-shield-heart"></i>
                <div>
                    <h2>Training supports safe practice; it does not replace professional authorization.</h2>
                    <p>
                        Completing a TeleRx training module does not independently authorize a person to diagnose,
                        operate a diagnostic facility or perform regulated clinical work. Participant eligibility,
                        test scope and facility activity must follow applicable qualifications, licensing and regulatory requirements.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="trxtr-cta trxtr-section">
        <div class="container">
            <div class="trxtr-cta-panel">
                <div>
                    <span class="trxtr-label trxtr-label-light">Plan a training session</span>
                    <h2>Tell us which equipment and skills your team needs.</h2>
                    <p>TeleRx can discuss the participant group, venue, module depth and practical learning outcome.</p>
                </div>
                <a class="trxtr-btn trxtr-btn-white" href="contact">
                    Contact TeleRx
                    <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>
