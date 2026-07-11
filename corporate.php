<?php
$page_title = 'Corporate & NGO Healthcare Plans | TeleRx';
$lead_saved = false;
$lead_error = '';

function trx_corp_clean($value) {
    return trim(filter_var((string)$value, FILTER_SANITIZE_SPECIAL_CHARS));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trx_corporate_lead'])) {
    $lead = [
        'created_at' => date('Y-m-d H:i:s'),
        'organization_name' => trx_corp_clean($_POST['organization_name'] ?? ''),
        'organization_type' => trx_corp_clean($_POST['organization_type'] ?? ''),
        'contact_person' => trx_corp_clean($_POST['contact_person'] ?? ''),
        'phone' => trx_corp_clean($_POST['phone'] ?? ''),
        'email' => trx_corp_clean($_POST['email'] ?? ''),
        'employee_count' => trx_corp_clean($_POST['employee_count'] ?? ''),
        'preferred_package' => trx_corp_clean($_POST['preferred_package'] ?? ''),
        'message' => trx_corp_clean($_POST['message'] ?? ''),
        'status' => 'New Lead'
    ];

    if ($lead['organization_name'] === '' || $lead['contact_person'] === '' || $lead['phone'] === '') {
        $lead_error = 'Please fill in organization name, contact person and phone number.';
    } else {
        $lead_file = __DIR__ . '/corporate_inquiries.jsonl';
        $encoded = json_encode($lead, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        if (@file_put_contents($lead_file, $encoded, FILE_APPEND | LOCK_EX) !== false) {
            $lead_saved = true;
        } else {
            $lead_error = 'Your request could not be saved automatically. Please contact TeleRx support directly.';
        }
    }
}

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
    <title>Corporate & NGO Healthcare Plans | TeleRx</title>
</head>
<body>
<?php } ?>

<?php
$plans = [
    [
        'name' => 'Corporate Basic',
        'icon' => '🏢',
        'price' => '৳4,999',
        'period' => '/ Month',
        'badge' => 'For Small Teams',
        'suitable' => 'Small Businesses / NGOs with 5–20 employees',
        'highlight' => false,
        'cta' => 'Choose Corporate Basic',
        'features' => [
            '1 online health campaign per month',
            '200 free 24/7 emergency doctor consultations per month',
            'GP consultation discount up to 5%',
            'Lab test discount 10%',
            'Home healthcare discount up to 15%',
            'Employee management up to 20 employees',
            'Monthly health record report included',
            'Employee health dashboard included',
        ],
    ],
    [
        'name' => 'Corporate Standard',
        'icon' => '⭐',
        'price' => '৳9,999',
        'period' => '/ Month',
        'badge' => 'Best Value',
        'suitable' => 'Growing businesses / NGOs with 21–100 employees',
        'highlight' => true,
        'cta' => 'Choose Corporate Standard',
        'features' => [
            '2 online health campaigns per month',
            '500 free 24/7 emergency doctor consultations per month',
            'GP discount up to 8% and specialist discount up to 5%',
            'Lab test discount 15%',
            'Home healthcare discount up to 25%',
            'Employee management up to 100 employees',
            'Quarterly wellness report and annual corporate health review',
            'Dedicated account manager and care coordinator',
        ],
    ],
    [
        'name' => 'Corporate Premium',
        'icon' => '👑',
        'price' => 'Custom',
        'period' => 'Pricing',
        'badge' => 'Recommended',
        'suitable' => 'Enterprises, factories, large NGOs and 100+ employee teams',
        'highlight' => false,
        'cta' => 'Contact Sales',
        'features' => [
            'Custom online health campaigns and physical health campaign',
            'Unlimited or customized 24/7 emergency doctor consultations',
            'GP discount up to 12% and specialist discount up to 10%',
            'Lab test discount up to 20%',
            'Home healthcare discount up to 35%',
            'Unlimited employee management',
            'Family coverage option up to 4 persons',
            'Corporate Success Manager and SLA-backed priority support',
        ],
    ],
];

$comparison_rows = [
    ['Feature', 'Corporate Basic', 'Corporate Standard', 'Corporate Premium'],
    ['Monthly Price', '৳4,999', '৳9,999', 'Custom Pricing'],
    ['Suitable For', 'Small Businesses (5–20 Employees)', 'Growing Businesses (21–100 Employees)', 'Enterprises (100+ Employees)'],
    ['Online Health Campaign', 'Included – 1 time/month', 'Included – 2 times/month', 'Custom health campaign'],
    ['Physical Health Campaign', '—', '—', 'Included'],
    ['Contract Validity', '1 Month', '1 Month', 'Custom Contract'],
    ['GP Consultation Discount', 'Up to 5%', 'Up to 8%', 'Up to 12%'],
    ['Specialist Consultation Discount', '—', 'Up to 5%', 'Up to 10%'],
    ['24/7 Emergency Doctor Consultations', '200 Free/Month', '500 Free/Month', 'Unlimited / Customized'],
    ['Digital Prescription', 'Included', 'Included', 'Included'],
    ['Lab Test Discount', '10%', '15%', 'Up to 20%'],
    ['Home Healthcare Discount', 'Up to 15%', 'Up to 25%', 'Up to 35%'],
    ['TeleRx Product Purchase Discount', 'Up to 7%', 'Up to 10%', 'Up to 15% + Free Delivery'],
    ['Employee Management', 'Up to 20 Employees', 'Up to 100 Employees', 'Unlimited'],
    ['Monthly Employee Follow-up', 'Yes', 'Yes', 'Yes'],
    ['Quarterly Wellness Report', '—', 'Included', 'Included'],
    ['Annual Corporate Health Review', '—', 'Included', 'Included'],
    ['Monthly Report of Health Record', 'Included', 'Included', 'Included'],
    ['Employee Health Dashboard', 'Included', 'Included', 'Included'],
    ['Family Coverage Option', '—', 'Available with max 2 persons', 'Available with 4 persons'],
    ['Dedicated Account Manager', '—', 'Included', 'Corporate Success Manager'],
    ['Dedicated Care Coordinator', '—', 'Included', 'Included'],
    ['General Health Check-up Program', '—', 'Included', 'Included'],
    ['Medical Document Verification', 'Included', 'Included', 'Included'],
    ['Fitness Certificate', 'Included', 'Included', 'Included'],
    ['Referral of Critically Ill Patient', 'Included', 'Included', 'Included'],
    ['Custom Wellness Campaigns', '—', '—', 'Included'],
    ['Corporate Billing & Invoicing', 'Included', 'Included', 'Included'],
    ['Priority Support', 'Yes', 'Yes', 'SLA-backed Priority Support'],
];

$benefits = [
    ['icon' => '🩺', 'title' => '24/7 Doctor Access', 'text' => 'Employees can receive emergency doctor consultation support anytime through TeleRx.'],
    ['icon' => '📋', 'title' => 'Digital Health Records', 'text' => 'Consultation records, prescriptions and health history can be organized for better follow-up.'],
    ['icon' => '📊', 'title' => 'HR Health Dashboard', 'text' => 'Employers can track employee health engagement, reports and usage in a structured way.'],
    ['icon' => '🏥', 'title' => 'Lab & Home Care Support', 'text' => 'Employees get access to discounted lab tests, home healthcare and TeleRx product purchase support.'],
    ['icon' => '👨‍👩‍👧‍👦', 'title' => 'Family Coverage Options', 'text' => 'Standard and Premium plans can include employee family members depending on package policy.'],
    ['icon' => '🤝', 'title' => 'Corporate Care Team', 'text' => 'Dedicated coordination support helps HR teams manage employee healthcare smoothly.'],
];

$steps = [
    ['01', 'Corporate Need Assessment', 'TeleRx team discusses employee count, location, health risk, campaign needs and support expectations.'],
    ['02', 'Plan Selection & Agreement', 'Choose Basic, Standard or Premium. For large teams, TeleRx can prepare a customized proposal.'],
    ['03', 'Employee Onboarding', 'Employees are enrolled into the TeleRx corporate healthcare support system with access guidelines.'],
    ['04', 'Campaign, Care & Reporting', 'TeleRx runs online or physical health campaigns, provides follow-up and shares monthly/quarterly reports.'],
];

$use_cases = [
    'Corporate offices',
    'NGO field teams',
    'Factories and production units',
    'Schools and educational institutions',
    'Retail chains and service teams',
    'Remote teams and distributed workforce',
];
?>

<style>
:root {
    --trx-corp-primary: #0E82FD;
    --trx-corp-primary-dark: #0867CA;
    --trx-corp-secondary: #00B894;
    --trx-corp-ink: #102033;
    --trx-corp-muted: #667085;
    --trx-corp-soft: #F4F8FF;
    --trx-corp-soft-green: #EAFBF5;
    --trx-corp-border: #E6EEF8;
    --trx-corp-white: #FFFFFF;
    --trx-corp-warning: #F7B731;
    --trx-corp-shadow: 0 18px 42px rgba(16, 32, 51, 0.10);
    --trx-corp-radius: 24px;
}
.trx-corp-page,
.trx-corp-page * { box-sizing: border-box; }
.trx-corp-page {
    width: 100%;
    overflow-x: hidden;
    color: var(--trx-corp-ink);
    background: #fff;
    font-family: inherit;
}
.trx-corp-page a { text-decoration: none; }
.trx-corp-wrap {
    width: min(1180px, calc(100% - 32px));
    margin: 0 auto;
}
.trx-corp-hero {
    position: relative;
    padding: 86px 0 70px;
    background:
        radial-gradient(circle at 8% 12%, rgba(14, 130, 253, 0.16), transparent 34%),
        radial-gradient(circle at 90% 15%, rgba(0, 184, 148, 0.13), transparent 32%),
        linear-gradient(135deg, #F7FBFF 0%, #FFFFFF 58%, #EFFFFA 100%);
}
.trx-corp-hero-grid {
    display: grid;
    grid-template-columns: 1.08fr .92fr;
    gap: 38px;
    align-items: center;
}
.trx-corp-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 999px;
    background: rgba(14, 130, 253, .09);
    color: var(--trx-corp-primary-dark);
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 18px;
}
.trx-corp-kicker::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--trx-corp-secondary);
}
.trx-corp-hero h1 {
    margin: 0 0 18px;
    font-size: clamp(38px, 5vw, 64px);
    line-height: 1.05;
    letter-spacing: -1.35px;
    color: var(--trx-corp-ink);
}
.trx-corp-hero h1 span { color: var(--trx-corp-primary); }
.trx-corp-hero p {
    margin: 0;
    max-width: 650px;
    color: var(--trx-corp-muted);
    font-size: 18px;
    line-height: 1.75;
}
.trx-corp-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 30px;
}
.trx-corp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 50px;
    padding: 14px 22px;
    border-radius: 999px;
    font-weight: 900;
    border: 1px solid transparent;
    cursor: pointer;
    transition: .24s ease;
}
.trx-corp-btn-primary {
    color: #fff;
    background: linear-gradient(135deg, var(--trx-corp-primary), var(--trx-corp-secondary));
    box-shadow: 0 14px 30px rgba(14, 130, 253, 0.22);
}
.trx-corp-btn-primary:hover { color: #fff; transform: translateY(-2px); }
.trx-corp-btn-outline {
    color: var(--trx-corp-primary-dark);
    background: #fff;
    border-color: rgba(14,130,253,.18);
}
.trx-corp-btn-outline:hover {
    color: var(--trx-corp-primary-dark);
    border-color: var(--trx-corp-primary);
    transform: translateY(-2px);
}
.trx-corp-hero-card {
    background: rgba(255,255,255,.88);
    border: 1px solid rgba(230,238,248,.95);
    border-radius: 30px;
    padding: 26px;
    box-shadow: var(--trx-corp-shadow);
}
.trx-corp-stat-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}
.trx-corp-stat {
    padding: 18px 14px;
    border-radius: 20px;
    background: var(--trx-corp-soft);
    border: 1px solid var(--trx-corp-border);
}
.trx-corp-stat strong {
    display: block;
    color: var(--trx-corp-primary);
    font-size: 30px;
    line-height: 1;
    margin-bottom: 8px;
}
.trx-corp-stat span {
    display: block;
    color: var(--trx-corp-muted);
    font-size: 13px;
    font-weight: 800;
}
.trx-corp-care-box {
    margin-top: 16px;
    padding: 22px;
    border-radius: 24px;
    color: #fff;
    background: linear-gradient(135deg, #0E82FD, #00B894);
}
.trx-corp-care-box h3 {
    color: #fff;
    font-size: 24px;
    margin: 0 0 10px;
}
.trx-corp-care-box p {
    color: rgba(255,255,255,.92);
    font-size: 15px;
    line-height: 1.7;
    margin: 0;
}
.trx-corp-section { padding: 74px 0; }
.trx-corp-section-soft { background: linear-gradient(180deg, #fff 0%, #F7FBFF 100%); }
.trx-corp-title {
    max-width: 780px;
    margin: 0 auto 42px;
    text-align: center;
}
.trx-corp-title h2 {
    margin: 0 0 12px;
    font-size: clamp(30px, 3.4vw, 44px);
    line-height: 1.16;
    letter-spacing: -0.7px;
}
.trx-corp-title p {
    margin: 0 auto;
    max-width: 700px;
    color: var(--trx-corp-muted);
    font-size: 16px;
    line-height: 1.75;
}
.trx-corp-benefit-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
}
.trx-corp-benefit {
    padding: 26px;
    border-radius: var(--trx-corp-radius);
    background: #fff;
    border: 1px solid var(--trx-corp-border);
    box-shadow: 0 12px 26px rgba(16, 32, 51, 0.06);
}
.trx-corp-benefit .icon {
    width: 54px;
    height: 54px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    background: var(--trx-corp-soft);
    font-size: 25px;
    margin-bottom: 18px;
}
.trx-corp-benefit h3 { margin: 0 0 10px; font-size: 20px; }
.trx-corp-benefit p { margin: 0; color: var(--trx-corp-muted); line-height: 1.7; font-size: 15px; }
.trx-corp-plan-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
    align-items: stretch;
}
.trx-corp-plan {
    position: relative;
    display: flex;
    flex-direction: column;
    border-radius: 30px;
    background: #fff;
    border: 1px solid var(--trx-corp-border);
    box-shadow: var(--trx-corp-shadow);
    overflow: hidden;
}
.trx-corp-plan.featured {
    transform: translateY(-12px);
    border-color: rgba(247, 183, 49, 0.75);
}
.trx-corp-plan-ribbon {
    display: none;
    padding: 12px 18px;
    text-align: center;
    color: #fff;
    background: linear-gradient(90deg, var(--trx-corp-primary), var(--trx-corp-warning));
    font-weight: 900;
    font-size: 14px;
}
.trx-corp-plan.featured .trx-corp-plan-ribbon { display: block; }
.trx-corp-plan-body {
    padding: 28px;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.trx-corp-plan .plan-icon {
    width: 58px;
    height: 58px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--trx-corp-soft);
    font-size: 28px;
    margin-bottom: 18px;
}
.trx-corp-plan h3 { margin: 0 0 8px; font-size: 25px; line-height: 1.2; }
.trx-corp-plan .suitable {
    margin: 0 0 22px;
    color: var(--trx-corp-muted);
    font-size: 14px;
    line-height: 1.6;
    min-height: 44px;
}
.trx-corp-price {
    margin: 0 0 8px;
    font-size: 44px;
    line-height: 1;
    color: var(--trx-corp-primary);
    font-weight: 900;
}
.trx-corp-price span { color: var(--trx-corp-muted); font-size: 15px; font-weight: 800; }
.trx-corp-plan ul {
    list-style: none;
    margin: 22px 0 0;
    padding: 0;
    display: grid;
    gap: 12px;
}
.trx-corp-plan li {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    color: #4D5E70;
    font-size: 14px;
    line-height: 1.55;
}
.trx-corp-plan li::before {
    content: "✓";
    color: #22C55E;
    font-weight: 900;
    flex: 0 0 auto;
}
.trx-corp-plan .plan-footer { margin-top: auto; padding-top: 26px; }
.trx-corp-plan .trx-corp-btn { width: 100%; }
.trx-corp-comparison-box {
    background: #fff;
    border: 1px solid var(--trx-corp-border);
    border-radius: 28px;
    overflow: hidden;
    box-shadow: var(--trx-corp-shadow);
}
.trx-corp-table-wrap { overflow-x: auto; }
.trx-corp-table {
    width: 100%;
    min-width: 860px;
    border-collapse: collapse;
}
.trx-corp-table th,
.trx-corp-table td {
    padding: 16px 18px;
    border-bottom: 1px solid var(--trx-corp-border);
    vertical-align: top;
    font-size: 14px;
    line-height: 1.55;
}
.trx-corp-table th {
    background: #F7FBFF;
    color: var(--trx-corp-ink);
    font-size: 15px;
    font-weight: 900;
}
.trx-corp-table th:first-child,
.trx-corp-table td:first-child {
    width: 28%;
    color: var(--trx-corp-ink);
    font-weight: 900;
}
.trx-corp-table tbody tr:last-child td { border-bottom: 0; }
.trx-corp-steps {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
}
.trx-corp-step {
    padding: 24px;
    border-radius: 24px;
    background: #fff;
    border: 1px solid var(--trx-corp-border);
    box-shadow: 0 12px 26px rgba(16, 32, 51, 0.06);
}
.trx-corp-step strong {
    display: inline-flex;
    width: 42px;
    height: 42px;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: var(--trx-corp-soft-green);
    color: var(--trx-corp-secondary);
    margin-bottom: 16px;
}
.trx-corp-step h3 { margin: 0 0 10px; font-size: 18px; }
.trx-corp-step p { margin: 0; color: var(--trx-corp-muted); line-height: 1.65; font-size: 14px; }
.trx-corp-use-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: center;
}
.trx-corp-chip {
    padding: 12px 18px;
    border-radius: 999px;
    background: #fff;
    border: 1px solid var(--trx-corp-border);
    color: var(--trx-corp-ink);
    font-weight: 800;
    box-shadow: 0 10px 22px rgba(16, 32, 51, 0.05);
}
.trx-corp-lead {
    background:
        radial-gradient(circle at top left, rgba(14,130,253,.18), transparent 32%),
        linear-gradient(135deg, #0E82FD, #00B894);
    color: #fff;
}
.trx-corp-lead .trx-corp-title h2,
.trx-corp-lead .trx-corp-title p { color: #fff; }
.trx-corp-form-card {
    max-width: 960px;
    margin: 0 auto;
    background: #fff;
    color: var(--trx-corp-ink);
    border-radius: 30px;
    padding: 30px;
    box-shadow: 0 22px 56px rgba(0,0,0,.16);
}
.trx-corp-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}
.trx-corp-field.full { grid-column: 1 / -1; }
.trx-corp-field label {
    display: block;
    font-size: 14px;
    font-weight: 900;
    margin-bottom: 8px;
}
.trx-corp-field input,
.trx-corp-field select,
.trx-corp-field textarea {
    width: 100%;
    min-height: 50px;
    border: 1px solid var(--trx-corp-border);
    border-radius: 14px;
    padding: 12px 14px;
    color: var(--trx-corp-ink);
    background: #F9FBFE;
    outline: none;
    transition: .2s ease;
}
.trx-corp-field textarea { min-height: 115px; resize: vertical; }
.trx-corp-field input:focus,
.trx-corp-field select:focus,
.trx-corp-field textarea:focus {
    border-color: var(--trx-corp-primary);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(14,130,253,.08);
}
.trx-corp-alert {
    margin-bottom: 18px;
    padding: 14px 16px;
    border-radius: 16px;
    font-weight: 800;
}
.trx-corp-alert.success {
    color: #05603A;
    background: #EAFBF5;
    border: 1px solid #BAF3DB;
}
.trx-corp-alert.error {
    color: #B42318;
    background: #FEF3F2;
    border: 1px solid #FECDCA;
}
.trx-corp-form-note {
    margin: 14px 0 0;
    color: var(--trx-corp-muted);
    font-size: 13px;
    line-height: 1.7;
}
.trx-corp-notes {
    display: grid;
    gap: 10px;
    margin-top: 24px;
}
.trx-corp-notes p { margin: 0; color: var(--trx-corp-muted); font-size: 14px; line-height: 1.7; }
@media (max-width: 991.98px) {
    .trx-corp-hero-grid,
    .trx-corp-plan-grid,
    .trx-corp-benefit-grid,
    .trx-corp-steps { grid-template-columns: 1fr; }
    .trx-corp-plan.featured { transform: none; }
}
@media (max-width: 767.98px) {
    .trx-corp-hero { padding: 58px 0 48px; }
    .trx-corp-wrap { width: min(100%, calc(100% - 24px)); }
    .trx-corp-stat-grid,
    .trx-corp-form-grid { grid-template-columns: 1fr; }
    .trx-corp-form-card { padding: 22px; }
    .trx-corp-actions { flex-direction: column; }
    .trx-corp-actions .trx-corp-btn { width: 100%; }
}
</style>

<main class="trx-corp-page">
    <section class="trx-corp-hero">
        <div class="trx-corp-wrap">
            <div class="trx-corp-hero-grid">
                <div>
                    <div class="trx-corp-kicker">TeleRx for Employers & NGOs</div>
                    <h1>Corporate Healthcare Plans for a <span>Healthier Workforce</span></h1>
                    <p>
                        Give your employees, field teams and NGO staff fast access to online doctor consultation,
                        emergency doctor support, health campaigns, digital prescriptions, reports and coordinated care.
                    </p>
                    <div class="trx-corp-actions">
                        <a href="#corporate-plans" class="trx-corp-btn trx-corp-btn-primary">View Corporate Plans</a>
                        <a href="#request-proposal" class="trx-corp-btn trx-corp-btn-outline">Request Proposal</a>
                    </div>
                </div>
                <div class="trx-corp-hero-card">
                    <div class="trx-corp-stat-grid">
                        <div class="trx-corp-stat"><strong>24/7</strong><span>Emergency Doctor Consultation</span></div>
                        <div class="trx-corp-stat"><strong>500+</strong><span>Monthly Calls in Standard Plan</span></div>
                        <div class="trx-corp-stat"><strong>100+</strong><span>Employees Covered in Standard</span></div>
                        <div class="trx-corp-stat"><strong>SLA</strong><span>Priority Support for Premium</span></div>
                    </div>
                    <div class="trx-corp-care-box">
                        <h3>Built for HR, Admin & NGO Operations</h3>
                        <p>
                            TeleRx helps organizations support employee wellbeing through telemedicine,
                            campaigns, reports, health dashboard and dedicated coordination.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="trx-corp-section trx-corp-section-soft">
        <div class="trx-corp-wrap">
            <div class="trx-corp-title">
                <div class="trx-corp-kicker">Why Corporate Healthcare?</div>
                <h2>Employee health directly affects productivity, morale and retention.</h2>
                <p>
                    A structured workplace healthcare program helps employees receive care faster,
                    reduce unnecessary absence, and gives HR teams a clearer view of workforce health needs.
                </p>
            </div>
            <div class="trx-corp-benefit-grid">
                <?php foreach ($benefits as $benefit): ?>
                    <div class="trx-corp-benefit">
                        <div class="icon"><?php echo htmlspecialchars($benefit['icon']); ?></div>
                        <h3><?php echo htmlspecialchars($benefit['title']); ?></h3>
                        <p><?php echo htmlspecialchars($benefit['text']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="trx-corp-section" id="corporate-plans">
        <div class="trx-corp-wrap">
            <div class="trx-corp-title">
                <div class="trx-corp-kicker">Corporate Packages</div>
                <h2>Choose the right TeleRx plan for your organization.</h2>
                <p>
                    Start with a monthly package or request a custom enterprise contract for larger employee groups,
                    factories, NGOs, field offices or multi-branch organizations.
                </p>
            </div>

            <div class="trx-corp-plan-grid">
                <?php foreach ($plans as $plan): ?>
                    <div class="trx-corp-plan <?php echo $plan['highlight'] ? 'featured' : ''; ?>">
                        <?php if ($plan['highlight']): ?>
                            <div class="trx-corp-plan-ribbon">Best Value for Growing Teams</div>
                        <?php endif; ?>
                        <div class="trx-corp-plan-body">
                            <div class="plan-icon"><?php echo htmlspecialchars($plan['icon']); ?></div>
                            <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
                            <p class="suitable"><?php echo htmlspecialchars($plan['suitable']); ?></p>
                            <p class="trx-corp-price">
                                <?php echo htmlspecialchars($plan['price']); ?>
                                <span><?php echo htmlspecialchars($plan['period']); ?></span>
                            </p>
                            <ul>
                                <?php foreach ($plan['features'] as $feature): ?>
                                    <li><?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="plan-footer">
                                <a href="#request-proposal" class="trx-corp-btn <?php echo $plan['highlight'] ? 'trx-corp-btn-primary' : 'trx-corp-btn-outline'; ?>">
                                    <?php echo htmlspecialchars($plan['cta']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="trx-corp-section trx-corp-section-soft" id="comparison">
        <div class="trx-corp-wrap">
            <div class="trx-corp-title">
                <div class="trx-corp-kicker">Compare Benefits</div>
                <h2>Corporate plan comparison</h2>
                <p>Review the full feature comparison before choosing a package for your organization.</p>
            </div>
            <div class="trx-corp-comparison-box">
                <div class="trx-corp-table-wrap">
                    <table class="trx-corp-table">
                        <thead>
                            <tr>
                                <?php foreach ($comparison_rows[0] as $head): ?>
                                    <th><?php echo htmlspecialchars($head); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($comparison_rows, 1) as $row): ?>
                                <tr>
                                    <?php foreach ($row as $cell): ?>
                                        <td><?php echo htmlspecialchars($cell); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="trx-corp-notes">
                <p><strong>Note:</strong> Premium pricing, unlimited consultation limits, SLA and custom campaigns are finalized after organizational requirement assessment.</p>
                <p>Emergency doctor consultation support is telemedicine guidance. It is not a replacement for hospital emergency care when physical emergency treatment is needed.</p>
            </div>
        </div>
    </section>

    <section class="trx-corp-section">
        <div class="trx-corp-wrap">
            <div class="trx-corp-title">
                <div class="trx-corp-kicker">How It Works</div>
                <h2>Simple onboarding for companies and NGOs.</h2>
                <p>TeleRx can help your HR, admin or operations team launch a healthcare support program without complex setup.</p>
            </div>
            <div class="trx-corp-steps">
                <?php foreach ($steps as $step): ?>
                    <div class="trx-corp-step">
                        <strong><?php echo htmlspecialchars($step[0]); ?></strong>
                        <h3><?php echo htmlspecialchars($step[1]); ?></h3>
                        <p><?php echo htmlspecialchars($step[2]); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="trx-corp-section trx-corp-section-soft">
        <div class="trx-corp-wrap">
            <div class="trx-corp-title">
                <div class="trx-corp-kicker">Suitable For</div>
                <h2>Designed for different types of organizations.</h2>
                <p>Use TeleRx corporate healthcare support for office employees, distributed teams, NGO field workers, factories and frontline service teams.</p>
            </div>
            <div class="trx-corp-use-grid">
                <?php foreach ($use_cases as $case): ?>
                    <div class="trx-corp-chip"><?php echo htmlspecialchars($case); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="trx-corp-section trx-corp-lead" id="request-proposal">
        <div class="trx-corp-wrap">
            <div class="trx-corp-title">
                <div class="trx-corp-kicker" style="background:rgba(255,255,255,.16); color:#fff;">Request a Proposal</div>
                <h2>Talk to TeleRx corporate care team.</h2>
                <p>Submit your organization details. TeleRx team can prepare a suitable healthcare plan for your employees or NGO staff.</p>
            </div>

            <div class="trx-corp-form-card">
                <?php if ($lead_saved): ?>
                    <div class="trx-corp-alert success">Thank you. Your corporate inquiry has been received. TeleRx team will contact you shortly.</div>
                <?php elseif ($lead_error !== ''): ?>
                    <div class="trx-corp-alert error"><?php echo htmlspecialchars($lead_error); ?></div>
                <?php endif; ?>

                <form method="post" action="#request-proposal">
                    <input type="hidden" name="trx_corporate_lead" value="1">
                    <div class="trx-corp-form-grid">
                        <div class="trx-corp-field">
                            <label for="organization_name">Organization Name *</label>
                            <input type="text" id="organization_name" name="organization_name" required>
                        </div>
                        <div class="trx-corp-field">
                            <label for="organization_type">Organization Type</label>
                            <select id="organization_type" name="organization_type">
                                <option value="">Select Type</option>
                                <option>Corporate Office</option>
                                <option>NGO</option>
                                <option>Factory / Production Unit</option>
                                <option>Educational Institution</option>
                                <option>Retail / Service Business</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="trx-corp-field">
                            <label for="contact_person">Contact Person *</label>
                            <input type="text" id="contact_person" name="contact_person" required>
                        </div>
                        <div class="trx-corp-field">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="trx-corp-field">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email">
                        </div>
                        <div class="trx-corp-field">
                            <label for="employee_count">Employee Count</label>
                            <select id="employee_count" name="employee_count">
                                <option value="">Select Employee Range</option>
                                <option>5–20 Employees</option>
                                <option>21–100 Employees</option>
                                <option>100+ Employees</option>
                                <option>Custom / Multiple Branches</option>
                            </select>
                        </div>
                        <div class="trx-corp-field full">
                            <label for="preferred_package">Preferred Package</label>
                            <select id="preferred_package" name="preferred_package">
                                <option value="">Select Package</option>
                                <option>Corporate Basic</option>
                                <option>Corporate Standard</option>
                                <option>Corporate Premium</option>
                                <option>Need Recommendation</option>
                            </select>
                        </div>
                        <div class="trx-corp-field full">
                            <label for="message">Message / Requirement</label>
                            <textarea id="message" name="message" placeholder="Write employee location, health campaign need, field staff coverage, family coverage or any special requirement."></textarea>
                        </div>
                        <div class="trx-corp-field full">
                            <button type="submit" class="trx-corp-btn trx-corp-btn-primary">Submit Corporate Inquiry</button>
                            <p class="trx-corp-form-note">
                                Inquiry records are saved in <strong>corporate_inquiries.jsonl</strong> beside this PHP file if server write permission is available.
                            </p>
                        </div>
                    </div>
                </form>
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
