<?php
$page_title = 'TeleRx Subscription Packages';
$has_site_header = file_exists(__DIR__ . '/header.php');
$has_site_footer = file_exists(__DIR__ . '/footer.php');

if ($has_site_header) {
    include __DIR__ . '/header.php';
} else {
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TeleRx Subscription Packages</title>
</head>
<body>
<?php } ?>

<style>
    :root {
        --trx-primary: #0E82FD;
        --trx-primary-dark: #0867CA;
        --trx-secondary: #00B894;
        --trx-ink: #102033;
        --trx-muted: #667085;
        --trx-soft: #F4F8FF;
        --trx-soft-green: #EAFBF5;
        --trx-border: #E7EEF8;
        --trx-white: #FFFFFF;
        --trx-warning: #FFB547;
        --trx-shadow: 0 18px 45px rgba(16, 32, 51, 0.10);
        --trx-radius: 22px;
    }

    .trx-subscription-page {
        font-family: inherit;
        color: var(--trx-ink);
        background: #ffffff;
        overflow: hidden;
    }

    .trx-container {
        width: min(1180px, calc(100% - 32px));
        margin: 0 auto;
    }

    .trx-hero {
        position: relative;
        padding: 90px 0 70px;
        background:
            radial-gradient(circle at top left, rgba(14, 130, 253, 0.16), transparent 38%),
            linear-gradient(135deg, #F7FBFF 0%, #FFFFFF 52%, #EFFFFA 100%);
    }

    .trx-hero-grid {
        display: grid;
        grid-template-columns: 1.05fr 0.95fr;
        gap: 38px;
        align-items: center;
    }

    .trx-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 999px;
        color: var(--trx-primary-dark);
        background: rgba(14, 130, 253, 0.10);
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .trx-eyebrow span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--trx-secondary);
        display: inline-block;
    }

    .trx-hero h1 {
        font-size: clamp(36px, 5vw, 62px);
        line-height: 1.05;
        margin: 0 0 18px;
        letter-spacing: -1.4px;
        color: var(--trx-ink);
    }

    .trx-hero h1 strong {
        color: var(--trx-primary);
        font-weight: 800;
    }

    .trx-hero p {
        margin: 0;
        color: var(--trx-muted);
        font-size: 18px;
        line-height: 1.75;
        max-width: 640px;
    }

    .trx-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 30px;
    }

    .trx-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 48px;
        padding: 13px 22px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 800;
        transition: 0.25s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .trx-btn-primary {
        color: #fff;
        background: linear-gradient(135deg, var(--trx-primary), var(--trx-secondary));
        box-shadow: 0 14px 30px rgba(14, 130, 253, 0.24);
    }

    .trx-btn-primary:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(14, 130, 253, 0.30);
    }

    .trx-btn-outline {
        color: var(--trx-primary-dark);
        background: #fff;
        border-color: rgba(14, 130, 253, 0.20);
    }

    .trx-btn-outline:hover {
        color: var(--trx-primary-dark);
        transform: translateY(-2px);
        border-color: var(--trx-primary);
    }

    .trx-hero-card {
        position: relative;
        background: rgba(255, 255, 255, 0.86);
        border: 1px solid rgba(231, 238, 248, 0.95);
        border-radius: 30px;
        box-shadow: var(--trx-shadow);
        padding: 28px;
    }

    .trx-hero-card::before {
        content: "";
        position: absolute;
        inset: -1px;
        border-radius: 30px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(14, 130, 253, 0.34), rgba(0, 184, 148, 0.25));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    .trx-stat-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }

    .trx-stat {
        background: var(--trx-soft);
        border-radius: 18px;
        padding: 18px 14px;
        text-align: center;
    }

    .trx-stat strong {
        display: block;
        font-size: 27px;
        line-height: 1;
        color: var(--trx-primary);
        margin-bottom: 7px;
    }

    .trx-stat span {
        color: var(--trx-muted);
        font-size: 13px;
        font-weight: 700;
    }

    .trx-care-box {
        padding: 22px;
        border-radius: 22px;
        background: linear-gradient(135deg, #0E82FD, #00B894);
        color: #fff;
    }

    .trx-care-box h3 {
        color: #fff;
        margin: 0 0 12px;
        font-size: 24px;
    }

    .trx-care-box p {
        color: rgba(255, 255, 255, 0.90);
        font-size: 15px;
        line-height: 1.7;
        margin: 0 0 16px;
    }

    .trx-care-list {
        display: grid;
        gap: 10px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .trx-care-list li {
        display: flex;
        gap: 9px;
        align-items: flex-start;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
    }

    .trx-section {
        padding: 72px 0;
    }

    .trx-section-soft {
        background: linear-gradient(180deg, #fff 0%, #F7FBFF 100%);
    }

    .trx-section-title {
        text-align: center;
        max-width: 780px;
        margin: 0 auto 42px;
    }

    .trx-section-title .trx-kicker {
        color: var(--trx-secondary);
        font-size: 14px;
        font-weight: 900;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .trx-section-title h2 {
        font-size: clamp(30px, 4vw, 46px);
        line-height: 1.14;
        margin: 0 0 14px;
        color: var(--trx-ink);
    }

    .trx-section-title p {
        color: var(--trx-muted);
        font-size: 17px;
        line-height: 1.7;
        margin: 0;
    }

    .trx-pricing-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
        align-items: stretch;
    }

    .trx-plan {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid var(--trx-border);
        border-radius: var(--trx-radius);
        background: #fff;
        box-shadow: 0 10px 30px rgba(16, 32, 51, 0.06);
        padding: 28px;
        transition: 0.25s ease;
    }

    .trx-plan:hover {
        transform: translateY(-6px);
        box-shadow: var(--trx-shadow);
    }

    .trx-plan-featured {
        border: 2px solid rgba(14, 130, 253, 0.30);
        box-shadow: 0 18px 42px rgba(14, 130, 253, 0.15);
    }

    .trx-badge {
        position: absolute;
        top: 18px;
        right: 18px;
        background: var(--trx-soft-green);
        color: #087A62;
        border-radius: 999px;
        padding: 7px 11px;
        font-size: 12px;
        font-weight: 900;
    }

    .trx-plan-icon {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 25px;
        font-weight: 900;
        background: linear-gradient(135deg, var(--trx-primary), var(--trx-secondary));
        margin-bottom: 18px;
    }

    .trx-plan h3 {
        font-size: 24px;
        line-height: 1.2;
        margin: 0 0 8px;
        color: var(--trx-ink);
    }

    .trx-plan .trx-subtitle {
        min-height: 52px;
        color: var(--trx-muted);
        line-height: 1.55;
        margin: 0 0 18px;
    }

    .trx-price {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin: 4px 0 5px;
    }

    .trx-price strong {
        font-size: 42px;
        line-height: 1;
        color: var(--trx-ink);
        letter-spacing: -1px;
    }

    .trx-price span {
        color: var(--trx-muted);
        font-weight: 700;
    }

    .trx-validity {
        display: inline-flex;
        width: fit-content;
        padding: 7px 10px;
        border-radius: 999px;
        background: var(--trx-soft);
        color: var(--trx-primary-dark);
        font-weight: 900;
        font-size: 13px;
        margin: 8px 0 20px;
    }

    .trx-feature-list {
        display: grid;
        gap: 12px;
        list-style: none;
        padding: 0;
        margin: 0 0 24px;
    }

    .trx-feature-list li {
        display: grid;
        grid-template-columns: 24px 1fr;
        gap: 10px;
        color: #344054;
        line-height: 1.55;
        font-size: 15px;
    }

    .trx-check {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: rgba(0, 184, 148, 0.12);
        color: var(--trx-secondary);
        font-size: 13px;
        font-weight: 900;
        margin-top: 1px;
    }

    .trx-plan .trx-btn {
        width: 100%;
        margin-top: auto;
    }

    .trx-support-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .trx-support-card {
        border: 1px solid var(--trx-border);
        border-radius: 20px;
        padding: 22px;
        background: #fff;
        box-shadow: 0 10px 24px rgba(16, 32, 51, 0.05);
    }

    .trx-support-card .trx-mini-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: var(--trx-soft);
        color: var(--trx-primary);
        display: grid;
        place-items: center;
        font-weight: 900;
        margin-bottom: 14px;
    }

    .trx-support-card h3 {
        margin: 0 0 10px;
        font-size: 19px;
        color: var(--trx-ink);
    }

    .trx-support-card p {
        margin: 0;
        color: var(--trx-muted);
        line-height: 1.65;
        font-size: 15px;
    }

    .trx-compare-wrap {
        overflow-x: auto;
        border: 1px solid var(--trx-border);
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(16, 32, 51, 0.05);
    }

    .trx-compare-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
    }

    .trx-compare-table th,
    .trx-compare-table td {
        padding: 18px 20px;
        border-bottom: 1px solid var(--trx-border);
        text-align: left;
        vertical-align: top;
    }

    .trx-compare-table th {
        color: var(--trx-ink);
        background: #F7FBFF;
        font-size: 15px;
    }

    .trx-compare-table td {
        color: #344054;
        font-size: 15px;
        line-height: 1.6;
    }

    .trx-compare-table tr:last-child td {
        border-bottom: 0;
    }

    .trx-process {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .trx-step {
        position: relative;
        padding: 24px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid var(--trx-border);
    }

    .trx-step-number {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: #fff;
        background: var(--trx-primary);
        font-weight: 900;
        margin-bottom: 15px;
    }

    .trx-step h3 {
        margin: 0 0 8px;
        font-size: 18px;
        color: var(--trx-ink);
    }

    .trx-step p {
        margin: 0;
        color: var(--trx-muted);
        line-height: 1.6;
        font-size: 15px;
    }

    .trx-terms {
        display: grid;
        grid-template-columns: 0.85fr 1.15fr;
        gap: 28px;
        align-items: start;
        padding: 34px;
        border-radius: 28px;
        background: #102033;
        color: #fff;
    }

    .trx-terms h2 {
        color: #fff;
        margin: 0 0 12px;
        font-size: 34px;
    }

    .trx-terms p {
        color: rgba(255, 255, 255, 0.72);
        line-height: 1.75;
        margin: 0;
    }

    .trx-terms ul {
        display: grid;
        gap: 12px;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .trx-terms li {
        display: grid;
        grid-template-columns: 24px 1fr;
        gap: 10px;
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.55;
        font-size: 15px;
    }

    .trx-faq-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .trx-faq-item {
        padding: 22px;
        border: 1px solid var(--trx-border);
        border-radius: 20px;
        background: #fff;
    }

    .trx-faq-item h3 {
        margin: 0 0 8px;
        color: var(--trx-ink);
        font-size: 18px;
    }

    .trx-faq-item p {
        margin: 0;
        color: var(--trx-muted);
        line-height: 1.65;
        font-size: 15px;
    }

    .trx-bottom-cta {
        text-align: center;
        padding: 56px 28px;
        border-radius: 30px;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.24), transparent 35%),
            linear-gradient(135deg, var(--trx-primary), var(--trx-secondary));
        color: #fff;
        box-shadow: var(--trx-shadow);
    }

    .trx-bottom-cta h2 {
        color: #fff;
        font-size: clamp(30px, 4vw, 44px);
        margin: 0 0 12px;
    }

    .trx-bottom-cta p {
        color: rgba(255, 255, 255, 0.90);
        margin: 0 auto 26px;
        max-width: 720px;
        line-height: 1.75;
        font-size: 17px;
    }

    .trx-bottom-cta .trx-btn {
        background: #fff;
        color: var(--trx-primary-dark);
        border-color: #fff;
    }

    @media (max-width: 991px) {
        .trx-hero {
            padding: 64px 0 56px;
        }

        .trx-hero-grid,
        .trx-pricing-grid,
        .trx-terms {
            grid-template-columns: 1fr;
        }

        .trx-support-grid,
        .trx-process {
            grid-template-columns: repeat(2, 1fr);
        }

        .trx-plan .trx-subtitle {
            min-height: auto;
        }
    }

    @media (max-width: 575px) {
        .trx-container {
            width: min(100% - 22px, 1180px);
        }

        .trx-hero h1 {
            letter-spacing: -0.6px;
        }

        .trx-hero p,
        .trx-section-title p {
            font-size: 16px;
        }

        .trx-stat-row,
        .trx-support-grid,
        .trx-process,
        .trx-faq-grid {
            grid-template-columns: 1fr;
        }

        .trx-hero-card,
        .trx-plan,
        .trx-terms {
            padding: 22px;
        }

        .trx-section {
            padding: 54px 0;
        }

        .trx-hero-actions .trx-btn {
            width: 100%;
        }
    }
</style>

<main class="trx-subscription-page">
    <section class="trx-hero">
        <div class="trx-container trx-hero-grid">
            <div>
                <div class="trx-eyebrow"><span></span> 1-Year Telemedicine Subscription</div>
                <h1>Healthcare support made simple with <strong>TeleRx 365</strong></h1>
                <p>
                    Choose a yearly plan for online doctor consultation, family health support, medicine assistance, emergency guidance and elderly care coordination. Each package is valid for 1 year from activation.
                </p>
                <div class="trx-hero-actions">
                    <a class="trx-btn trx-btn-primary" href="#trx-pricing">View Packages</a>
                    <a class="trx-btn trx-btn-outline" href="tel:+8801836838888">Call +880 1836 838888</a>
                </div>
            </div>

            <div class="trx-hero-card">
                <div class="trx-stat-row">
                    <div class="trx-stat">
                        <strong>24/7</strong>
                        <span>Emergency Support</span>
                    </div>
                    <div class="trx-stat">
                        <strong>1 Year</strong>
                        <span>Plan Validity</span>
                    </div>
                    <div class="trx-stat">
                        <strong>Video</strong>
                        <span>Doctor Consult</span>
                    </div>
                </div>

                <div class="trx-care-box">
                    <h3>What TeleRx members get</h3>
                    <p>Members can book doctors online, talk through video, voice or chat, get follow-up guidance and receive support for medicine, tests and home care services.</p>
                    <ul class="trx-care-list">
                        <li><span>✓</span> General Physician consultation quota</li>
                        <li><span>✓</span> Discount on extra doctor consultation</li>
                        <li><span>✓</span> Appointment and support coordination</li>
                        <li><span>✓</span> Elderly care and home care guidance</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="trx-section" id="trx-pricing">
        <div class="trx-container">
            <div class="trx-section-title">
                <div class="trx-kicker">Subscription Packages</div>
                <h2>Pick the right yearly healthcare plan</h2>
                <p>Start with an individual plan, cover your family, or choose premium support for elderly parents and regular follow-up needs.</p>
            </div>

            <div class="trx-pricing-grid">
                <article class="trx-plan">
                    <div class="trx-plan-icon">E</div>
                    <h3>TeleRx Essential 365</h3>
                    <p class="trx-subtitle">Best for one person who wants easy online access to doctors.</p>
                    <div class="trx-price"><strong>৳1,499</strong><span>/ year</span></div>
                    <div class="trx-validity">Valid for 1 year</div>
                    <ul class="trx-feature-list">
                        <li><span class="trx-check">✓</span><span>4 free General Physician video consultations</span></li>
                        <li><span class="trx-check">✓</span><span>15% discount on extra GP consultation</span></li>
                        <li><span class="trx-check">✓</span><span>Video, voice or chat-based consultation support</span></li>
                        <li><span class="trx-check">✓</span><span>Doctor appointment coordination</span></li>
                        <li><span class="trx-check">✓</span><span>Medicine and supplies support guidance</span></li>
                        <li><span class="trx-check">✓</span><span>Basic emergency guidance</span></li>
                    </ul>
                    <a class="trx-btn trx-btn-outline" href="contact.php?package=TeleRx%20Essential%20365">Choose Essential</a>
                </article>

                <article class="trx-plan trx-plan-featured">
                    <div class="trx-badge">Popular</div>
                    <div class="trx-plan-icon">F</div>
                    <h3>TeleRx Family 365</h3>
                    <p class="trx-subtitle">Best for small families who need regular doctor access.</p>
                    <div class="trx-price"><strong>৳3,999</strong><span>/ year</span></div>
                    <div class="trx-validity">Valid for 1 year</div>
                    <ul class="trx-feature-list">
                        <li><span class="trx-check">✓</span><span>Coverage for up to 4 family members</span></li>
                        <li><span class="trx-check">✓</span><span>10 free General Physician consultations</span></li>
                        <li><span class="trx-check">✓</span><span>20% discount on extra GP consultation</span></li>
                        <li><span class="trx-check">✓</span><span>10% discount on specialist consultation</span></li>
                        <li><span class="trx-check">✓</span><span>Priority appointment coordination</span></li>
                        <li><span class="trx-check">✓</span><span>Family follow-up and health record support</span></li>
                        <li><span class="trx-check">✓</span><span>Lab test and medicine support guidance</span></li>
                    </ul>
                    <a class="trx-btn trx-btn-primary" href="contact.php?package=TeleRx%20Family%20365">Choose Family</a>
                </article>

                <article class="trx-plan">
                    <div class="trx-plan-icon">P</div>
                    <h3>TeleRx Premium Care 365</h3>
                    <p class="trx-subtitle">Best for family, elderly parents and chronic care follow-up.</p>
                    <div class="trx-price"><strong>৳7,999</strong><span>/ year</span></div>
                    <div class="trx-validity">Valid for 1 year</div>
                    <ul class="trx-feature-list">
                        <li><span class="trx-check">✓</span><span>Coverage for up to 5 family members</span></li>
                        <li><span class="trx-check">✓</span><span>16 free General Physician consultations</span></li>
                        <li><span class="trx-check">✓</span><span>25% discount on extra GP consultation</span></li>
                        <li><span class="trx-check">✓</span><span>15% discount on specialist consultation</span></li>
                        <li><span class="trx-check">✓</span><span>Monthly basic health follow-up support</span></li>
                        <li><span class="trx-check">✓</span><span>Elderly care and home care coordination</span></li>
                        <li><span class="trx-check">✓</span><span>Emergency priority support</span></li>
                    </ul>
                    <a class="trx-btn trx-btn-outline" href="contact.php?package=TeleRx%20Premium%20Care%20365">Choose Premium</a>
                </article>
            </div>
        </div>
    </section>

    <section class="trx-section trx-section-soft">
        <div class="trx-container">
            <div class="trx-section-title">
                <div class="trx-kicker">Member Benefits</div>
                <h2>What your yearly plan can cover</h2>
                <p>TeleRx plans are designed around the services people need most: online doctor consultation, quick support and care coordination.</p>
            </div>

            <div class="trx-support-grid">
                <div class="trx-support-card">
                    <div class="trx-mini-icon">01</div>
                    <h3>Online Doctor Consultation</h3>
                    <p>Consult with doctors through video, voice or chat without visiting a clinic first.</p>
                </div>
                <div class="trx-support-card">
                    <div class="trx-mini-icon">02</div>
                    <h3>Emergency Guidance</h3>
                    <p>Get quick guidance and appointment direction during urgent health concerns.</p>
                </div>
                <div class="trx-support-card">
                    <div class="trx-mini-icon">03</div>
                    <h3>Medicine Support</h3>
                    <p>Receive coordination support for medicine and essential medical supplies.</p>
                </div>
                <div class="trx-support-card">
                    <div class="trx-mini-icon">04</div>
                    <h3>Elderly & Home Care</h3>
                    <p>Get support for elderly care, home care service and regular follow-up needs.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="trx-section">
        <div class="trx-container">
            <div class="trx-section-title">
                <div class="trx-kicker">Package Comparison</div>
                <h2>Compare all 3 plans</h2>
                <p>Use this table to help visitors quickly decide which package fits their need.</p>
            </div>

            <div class="trx-compare-wrap">
                <table class="trx-compare-table">
                    <thead>
                        <tr>
                            <th>Benefit</th>
                            <th>Essential 365</th>
                            <th>Family 365</th>
                            <th>Premium Care 365</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Best for</td>
                            <td>Individual user</td>
                            <td>Small family</td>
                            <td>Family, elderly care and chronic follow-up</td>
                        </tr>
                        <tr>
                            <td>Member coverage</td>
                            <td>1 person</td>
                            <td>Up to 4 members</td>
                            <td>Up to 5 members</td>
                        </tr>
                        <tr>
                            <td>Free GP consultations</td>
                            <td>4 per year</td>
                            <td>10 per year</td>
                            <td>16 per year</td>
                        </tr>
                        <tr>
                            <td>Extra GP consultation discount</td>
                            <td>15%</td>
                            <td>20%</td>
                            <td>25%</td>
                        </tr>
                        <tr>
                            <td>Specialist consultation discount</td>
                            <td>Not included</td>
                            <td>10%</td>
                            <td>15%</td>
                        </tr>
                        <tr>
                            <td>Care support</td>
                            <td>Basic support</td>
                            <td>Priority family support</td>
                            <td>Priority support with monthly follow-up</td>
                        </tr>
                        <tr>
                            <td>Validity</td>
                            <td>1 year</td>
                            <td>1 year</td>
                            <td>1 year</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="trx-section trx-section-soft">
        <div class="trx-container">
            <div class="trx-section-title">
                <div class="trx-kicker">How It Works</div>
                <h2>Start your TeleRx subscription in 4 steps</h2>
            </div>

            <div class="trx-process">
                <div class="trx-step">
                    <div class="trx-step-number">1</div>
                    <h3>Choose a package</h3>
                    <p>Select Essential, Family or Premium Care based on your healthcare need.</p>
                </div>
                <div class="trx-step">
                    <div class="trx-step-number">2</div>
                    <h3>Register members</h3>
                    <p>Add your name, phone number and eligible family member details.</p>
                </div>
                <div class="trx-step">
                    <div class="trx-step-number">3</div>
                    <h3>Book consultation</h3>
                    <p>Choose doctor, schedule time and consult through video, voice or chat.</p>
                </div>
                <div class="trx-step">
                    <div class="trx-step-number">4</div>
                    <h3>Get support</h3>
                    <p>Receive guidance for follow-up, medicine, tests or home care coordination.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="trx-section">
        <div class="trx-container">
            <div class="trx-terms">
                <div>
                    <h2>Terms & notes</h2>
                    <p>Please keep these points visible on the subscription page so users clearly understand what is included.</p>
                </div>
                <ul>
                    <li><span class="trx-check">✓</span><span>All packages are valid for 1 year from the activation date.</span></li>
                    <li><span class="trx-check">✓</span><span>Free consultations apply to TeleRx panel General Physicians only.</span></li>
                    <li><span class="trx-check">✓</span><span>Specialist consultation depends on doctor availability.</span></li>
                    <li><span class="trx-check">✓</span><span>Medicine, lab test, home care and nursing services may have separate charges.</span></li>
                    <li><span class="trx-check">✓</span><span>Emergency support means quick guidance or consultation arrangement. It does not replace hospital emergency treatment.</span></li>
                    <li><span class="trx-check">✓</span><span>Unused consultation quota expires after the package validity period.</span></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="trx-section trx-section-soft">
        <div class="trx-container">
            <div class="trx-section-title">
                <div class="trx-kicker">FAQ</div>
                <h2>Common questions</h2>
            </div>

            <div class="trx-faq-grid">
                <div class="trx-faq-item">
                    <h3>Can I use the package for my family?</h3>
                    <p>Yes. Family 365 covers up to 4 members and Premium Care 365 covers up to 5 members.</p>
                </div>
                <div class="trx-faq-item">
                    <h3>How long is the package valid?</h3>
                    <p>Each package is valid for 1 year from the activation date.</p>
                </div>
                <div class="trx-faq-item">
                    <h3>Can I talk to doctors by video call?</h3>
                    <p>Yes. TeleRx supports video, voice and chat-based consultation depending on service availability.</p>
                </div>
                <div class="trx-faq-item">
                    <h3>Is this for emergency treatment?</h3>
                    <p>TeleRx can provide emergency guidance and support coordination. For life-threatening situations, visit the nearest hospital immediately.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="trx-section">
        <div class="trx-container">
            <div class="trx-bottom-cta">
                <h2>Need help choosing the right plan?</h2>
                <p>Talk to TeleRx support. We will help you choose the right yearly plan for individual use, family care or elderly support.</p>
                <a class="trx-btn" href="tel:+8801836838888">Call Now</a>
            </div>
        </div>
    </section>
</main>

<?php
if ($has_site_footer) {
    include __DIR__ . '/footer.php';
} else {
?>
</body>
</html>
<?php } ?>
