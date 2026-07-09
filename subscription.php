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

<?php
$plan_groups = [
    'monthly' => [
        'tab' => '1 Month',
        'title' => 'Monthly Plans',
        'subtitle' => 'Flexible telemedicine support for short-term and occasional healthcare needs.',
        'plans' => [
            [
                'name' => 'Basic',
                'badge' => 'Occasional Users',
                'price' => '৳499',
                'duration' => '1 Month',
                'icon' => 'B',
                'featured' => false,
                'cta' => 'Choose Basic',
                'features' => [
                    'GP consultation discount up to 5%',
                    '10 free 24/7 emergency doctor calls',
                    'Digital prescription included',
                    'Home service from TeleRx up to 10%',
                    'TeleRx purchase discount up to 7%',
                    'Family member addition: 1 person',
                    'Monthly basic health follow-up support',
                ],
            ],
            [
                'name' => 'Standard',
                'badge' => 'Best Value',
                'price' => '৳999',
                'duration' => '1 Month',
                'icon' => 'S',
                'featured' => true,
                'cta' => 'Choose Standard',
                'features' => [
                    'GP consultation discount up to 7%',
                    '25 free 24/7 emergency doctor calls',
                    'Digital prescription included',
                    'Home service from TeleRx up to 15%',
                    'TeleRx purchase discount up to 10%',
                    'Family member addition: 2 persons',
                    'Monthly basic health follow-up support',
                    'Annual health review included',
                ],
            ],
            [
                'name' => 'Premium',
                'badge' => 'Recommended',
                'price' => '৳1,499',
                'duration' => '1 Month',
                'icon' => 'P',
                'featured' => false,
                'cta' => 'Choose Premium',
                'features' => [
                    'GP consultation discount up to 12%',
                    'Specialist consultation discount up to 10%',
                    '40 free 24/7 emergency doctor calls',
                    'Digital prescription included',
                    'Lab test discount up to 15%',
                    'Home service from TeleRx up to 30%',
                    'TeleRx purchase discount up to 12% with free home delivery',
                    'Family member addition: 4 persons',
                    'Priority response and dedicated care coordinator',
                    'Personal health history creation and management',
                ],
            ],
        ],
    ],
    'sixmonths' => [
        'tab' => '6 Months',
        'title' => '6-Month Plans',
        'subtitle' => 'Better value for users and families who need regular online doctor support.',
        'plans' => [
            [
                'name' => 'Basic 6 Months',
                'badge' => 'Basic',
                'price' => '৳2,499',
                'duration' => '6 Months',
                'icon' => 'B',
                'featured' => false,
                'cta' => 'Choose Basic',
                'features' => [
                    'GP consultation discount up to 5%',
                    '70 free 24/7 emergency doctor calls',
                    'Digital prescription included',
                    'Home service from TeleRx up to 10%',
                    'TeleRx purchase discount up to 7%',
                    'Family member coverage: 1 person',
                    'Basic health record storage',
                    'Monthly basic health follow-up support',
                ],
            ],
            [
                'name' => 'Standard 6 Months',
                'badge' => 'Best Value',
                'price' => '৳5,499',
                'duration' => '6 Months',
                'icon' => 'S',
                'featured' => true,
                'cta' => 'Choose Standard',
                'features' => [
                    'GP consultation discount up to 7%',
                    '150 free 24/7 emergency doctor calls',
                    'Digital prescription included',
                    'Home service from TeleRx up to 15%',
                    'TeleRx purchase discount up to 10%',
                    'Family member coverage: 2 persons',
                    'Standard health record storage',
                    'Monthly basic health follow-up support',
                    'Mid-term health review included',
                ],
            ],
            [
                'name' => 'Premium 6 Months',
                'badge' => 'Maximum Support',
                'price' => '৳7,999',
                'duration' => '6 Months',
                'icon' => 'P',
                'featured' => false,
                'cta' => 'Choose Premium',
                'features' => [
                    'GP consultation discount up to 12%',
                    'Specialist consultation discount up to 10%',
                    '225 free 24/7 emergency doctor calls',
                    'Digital prescription included',
                    'Lab test discount up to 15%',
                    'Home service from TeleRx up to 30%',
                    'TeleRx purchase discount up to 12% with free home delivery',
                    'Family member coverage: up to 4 persons',
                    'Unlimited health record storage',
                    'Priority response and dedicated care coordinator',
                    'Comprehensive health review included',
                ],
            ],
        ],
    ],
    'yearly' => [
        'tab' => '12 Months',
        'title' => '12-Month Plans',
        'subtitle' => 'Best yearly value for family healthcare, elderly care and long-term support.',
        'plans' => [
            [
                'name' => 'Basic 12 Months',
                'badge' => 'Basic',
                'price' => '৳4,499',
                'duration' => '12 Months',
                'icon' => 'B',
                'featured' => false,
                'cta' => 'Choose Basic',
                'features' => [
                    'GP consultation discount up to 5%',
                    '120 free 24/7 emergency doctor calls',
                    'Digital prescription included',
                    'Home service from TeleRx up to 10%',
                    'TeleRx purchase discount up to 7%',
                    'Family member coverage: 1 person',
                    'Basic health record storage',
                    'Monthly basic health follow-up support',
                ],
            ],
            [
                'name' => 'Standard 12 Months',
                'badge' => 'Best Value',
                'price' => '৳9,999',
                'duration' => '12 Months',
                'icon' => 'S',
                'featured' => true,
                'cta' => 'Choose Standard',
                'features' => [
                    'GP consultation discount up to 7%',
                    '250 free 24/7 emergency doctor calls',
                    'Digital prescription included',
                    'Home service from TeleRx up to 15%',
                    'TeleRx purchase discount up to 10%',
                    'Family member coverage: 2 persons',
                    'Standard health record storage',
                    'Monthly basic health follow-up support',
                    'Biannual health review included',
                ],
            ],
            [
                'name' => 'Premium 12 Months',
                'badge' => 'Recommended',
                'price' => '৳14,499',
                'duration' => '12 Months',
                'icon' => 'P',
                'featured' => false,
                'cta' => 'Choose Premium',
                'features' => [
                    'GP consultation discount up to 12%',
                    'Specialist consultation discount up to 10%',
                    '400 free 24/7 emergency doctor calls',
                    'Digital prescription included',
                    'Lab test discount up to 15%',
                    'Home service from TeleRx up to 30%',
                    'TeleRx purchase discount up to 12% with free home delivery',
                    'Family member coverage: up to 4 persons',
                    'Unlimited health record storage',
                    'Priority response and dedicated care coordinator',
                    'Comprehensive annual health review included',
                ],
            ],
        ],
    ],
];

$comparison_rows = [
    'monthly' => [
        ['Price', '৳499', '৳999', '৳1,499'],
        ['Validity', '1 Month', '1 Month', '1 Month'],
        ['GP Consultation Discount', 'Up to 5%', 'Up to 7%', 'Up to 12%'],
        ['Specialist Consultation Discount', 'Not included', 'Not included', 'Up to 10%'],
        ['Free 24/7 Emergency Doctor Calls', '10 Calls', '25 Calls', '40 Calls'],
        ['Digital Prescription', 'Included', 'Included', 'Included'],
        ['Lab Test Discount', 'Not included', 'Not included', 'Up to 15%'],
        ['Home Service from TeleRx', 'Up to 10%', 'Up to 15%', 'Up to 30%'],
        ['TeleRx Purchase Discount', 'Up to 7%', 'Up to 10%', 'Up to 12% with free home delivery'],
        ['Family Member Addition', '1 Person', '2 Persons', '4 Persons'],
        ['Health Record Storage', 'Good for occasional users', 'Best value', 'Recommended'],
        ['Priority Response', 'Not included', 'Not included', 'Included'],
        ['Dedicated Care Coordinator', 'Not included', 'Not included', 'Included'],
        ['Personal Health History Creation & Management', 'Not included', 'Not included', 'Included'],
        ['Monthly Basic Health Follow-up Support', 'Included', 'Included', 'Included'],
        ['Annual Health Review', 'Not included', 'Included', 'Included'],
    ],
    'sixmonths' => [
        ['Price', '৳2,499', '৳5,499', '৳7,999'],
        ['Validity', '6 Months', '6 Months', '6 Months'],
        ['GP Consultation Discount', 'Up to 5%', 'Up to 7%', 'Up to 12%'],
        ['Specialist Consultation Discount', 'Not included', 'Not included', 'Up to 10%'],
        ['Free 24/7 Emergency Doctor Calls', '70 Calls', '150 Calls', '225 Calls'],
        ['Digital Prescription', 'Included', 'Included', 'Included'],
        ['Lab Test Discount', 'Not included', 'Not included', 'Up to 15%'],
        ['Home Service from TeleRx', 'Up to 10%', 'Up to 15%', 'Up to 30%'],
        ['TeleRx Purchase Discount', 'Up to 7%', 'Up to 10%', 'Up to 12% with free home delivery'],
        ['Family Member Coverage', '1 Person', '2 Persons', 'Up to 4 Persons'],
        ['Health Record Storage', 'Basic', 'Standard', 'Unlimited'],
        ['Priority Response', 'Not included', 'Not included', 'Included'],
        ['Dedicated Care Coordinator', 'Not included', 'Not included', 'Included'],
        ['Personal Health History Creation & Management', 'Not included', 'Not included', 'Included'],
        ['Monthly Basic Health Follow-up Support', 'Included', 'Included', 'Included'],
        ['Health Review', 'Not included', 'Mid-term Review', 'Comprehensive Review'],
    ],
    'yearly' => [
        ['Price', '৳4,499', '৳9,999', '৳14,499'],
        ['Validity', '12 Months', '12 Months', '12 Months'],
        ['GP Consultation Discount', 'Up to 5%', 'Up to 7%', 'Up to 12%'],
        ['Specialist Consultation Discount', 'Not included', 'Not included', 'Up to 10%'],
        ['Free 24/7 Emergency Doctor Calls', '120 Calls', '250 Calls', '400 Calls'],
        ['Digital Prescription', 'Included', 'Included', 'Included'],
        ['Lab Test Discount', 'Not included', 'Not included', 'Up to 15%'],
        ['Home Service from TeleRx', 'Up to 10%', 'Up to 15%', 'Up to 30%'],
        ['TeleRx Purchase Discount', 'Up to 7%', 'Up to 10%', 'Up to 12% with free home delivery'],
        ['Family Member Coverage', '1 Person', '2 Persons', 'Up to 4 Persons'],
        ['Health Record Storage', 'Basic', 'Standard', 'Unlimited'],
        ['Priority Response', 'Not included', 'Not included', 'Included'],
        ['Dedicated Care Coordinator', 'Not included', 'Not included', 'Included'],
        ['Personal Health History Creation & Management', 'Not included', 'Not included', 'Included'],
        ['Monthly Basic Health Follow-up Support', 'Included', 'Included', 'Included'],
        ['Health Review', 'Not included', 'Biannual Health Review', 'Comprehensive Annual Health Review'],
    ],
];
?>

<main class="trx-subscription-page">
    <section class="trx-hero">
        <div class="trx-container trx-hero-grid">
            <div>
                <div class="trx-eyebrow"><span></span> TeleRx Subscription Packages</div>
                <h1>Simple healthcare plans for <strong>online doctor support</strong></h1>
                <p>
                    Choose a 1-month, 6-month or 12-month TeleRx package for GP consultation discounts, emergency doctor calls, digital prescriptions, home service benefits, TeleRx purchase discounts and family healthcare support.
                </p>
                <div class="trx-hero-actions">
                    <a class="trx-btn trx-btn-primary" href="#trx-pricing">View Packages</a>
                    <a class="trx-btn trx-btn-outline" href="tel:+8801836838888">Call +880 1836 838888</a>
                </div>
            </div>

            <div class="trx-hero-card">
                <div class="trx-stat-row">
                    <div class="trx-stat">
                        <strong>3</strong>
                        <span>Plan Types</span>
                    </div>
                    <div class="trx-stat">
                        <strong>12M</strong>
                        <span>Maximum Validity</span>
                    </div>
                    <div class="trx-stat">
                        <strong>24/7</strong>
                        <span>Doctor Calls</span>
                    </div>
                </div>

                <div class="trx-care-box">
                    <h3>What TeleRx members get</h3>
                    <p>Members can consult doctors online, receive digital prescriptions, get support for lab tests, home service, health records and follow-up care.</p>
                    <ul class="trx-care-list">
                        <li><span>✓</span> GP and specialist consultation discounts</li>
                        <li><span>✓</span> Free 24/7 emergency doctor calls</li>
                        <li><span>✓</span> Family member coverage options</li>
                        <li><span>✓</span> Premium care coordinator and priority response</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="trx-section" id="trx-pricing">
        <div class="trx-container">
            <div class="trx-section-title">
                <div class="trx-kicker">Subscription Packages</div>
                <h2>Choose your TeleRx plan</h2>
                <p>Select Basic, Standard or Premium based on your support need. You can choose monthly, 6-month or 12-month validity.</p>
            </div>

            <div class="trx-duration-nav" role="tablist" aria-label="Package duration">
                <?php $first_tab = true; foreach ($plan_groups as $group_id => $group) : ?>
                    <button class="trx-duration-pill<?php echo $first_tab ? ' is-active' : ''; ?>" type="button" data-trx-tab="<?php echo htmlspecialchars($group_id); ?>" aria-selected="<?php echo $first_tab ? 'true' : 'false'; ?>">
                        <?php echo htmlspecialchars($group['tab']); ?>
                    </button>
                <?php $first_tab = false; endforeach; ?>
            </div>

            <?php $first_panel = true; foreach ($plan_groups as $group_id => $group) : ?>
                <div class="trx-package-group<?php echo $first_panel ? ' is-active' : ''; ?>" id="trx-<?php echo htmlspecialchars($group_id); ?>" data-trx-panel="<?php echo htmlspecialchars($group_id); ?>">
                    <div class="trx-package-heading">
                        <div>
                            <h3><?php echo htmlspecialchars($group['title']); ?></h3>
                            <p><?php echo htmlspecialchars($group['subtitle']); ?></p>
                        </div>
                    </div>

                    <div class="trx-pricing-grid">
                        <?php foreach ($group['plans'] as $plan) : ?>
                            <article class="trx-plan<?php echo $plan['featured'] ? ' trx-plan-featured' : ''; ?>">
                                <div class="trx-badge"><?php echo htmlspecialchars($plan['badge']); ?></div>
                                <div class="trx-plan-icon"><?php echo htmlspecialchars($plan['icon']); ?></div>
                                <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
                                <div class="trx-price"><strong><?php echo htmlspecialchars($plan['price']); ?></strong><span>/ <?php echo htmlspecialchars($plan['duration']); ?></span></div>
                                <div class="trx-validity">Valid for <?php echo htmlspecialchars($plan['duration']); ?></div>
                                <ul class="trx-feature-list">
                                    <?php foreach ($plan['features'] as $feature) : ?>
                                        <li><span class="trx-check">✓</span><span><?php echo htmlspecialchars($feature); ?></span></li>
                                    <?php endforeach; ?>
                                </ul>
                                <a class="trx-btn <?php echo $plan['featured'] ? 'trx-btn-primary' : 'trx-btn-outline'; ?>" href="contact.php?package=<?php echo urlencode($plan['name']); ?>">
                                    <?php echo htmlspecialchars($plan['cta']); ?>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php $first_panel = false; endforeach; ?>
        </div>
    </section>

    <section class="trx-section trx-section-soft">
        <div class="trx-container">
            <div class="trx-section-title">
                <div class="trx-kicker">Member Benefits</div>
                <h2>What your package can cover</h2>
                <p>TeleRx plans are designed around online consultation, emergency doctor calls, digital prescriptions, family coverage and care coordination.</p>
            </div>

            <div class="trx-support-grid">
                <div class="trx-support-card">
                    <div class="trx-mini-icon">01</div>
                    <h3>Doctor Consultation Discount</h3>
                    <p>Get GP consultation discount in every plan. Premium users also get specialist consultation discount.</p>
                </div>
                <div class="trx-support-card">
                    <div class="trx-mini-icon">02</div>
                    <h3>24/7 Emergency Doctor Calls</h3>
                    <p>Use your package call quota for urgent doctor support through TeleRx.</p>
                </div>
                <div class="trx-support-card">
                    <div class="trx-mini-icon">03</div>
                    <h3>Digital Prescription</h3>
                    <p>All plans include digital prescription support after eligible online consultation.</p>
                </div>
                <div class="trx-support-card">
                    <div class="trx-mini-icon">04</div>
                    <h3>Premium Care Support</h3>
                    <p>Premium plans include priority response, dedicated care coordinator and health history management.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="trx-section">
        <div class="trx-container">
            <div class="trx-section-title">
                <div class="trx-kicker">Package Comparison</div>
                <h2>Compare plan benefits</h2>
                <p>Use these tables to compare Basic, Standard and Premium benefits for each validity option.</p>
            </div>

            <div class="trx-duration-nav" role="tablist" aria-label="Comparison duration">
                <?php $first_table_tab = true; foreach ($plan_groups as $group_id => $group) : ?>
                    <button class="trx-duration-pill<?php echo $first_table_tab ? ' is-active' : ''; ?>" type="button" data-trx-tab="<?php echo htmlspecialchars($group_id); ?>" aria-selected="<?php echo $first_table_tab ? 'true' : 'false'; ?>">
                        <?php echo htmlspecialchars($group['tab']); ?>
                    </button>
                <?php $first_table_tab = false; endforeach; ?>
            </div>

            <?php $first_compare = true; foreach ($comparison_rows as $group_id => $rows) : ?>
                <div class="trx-compare-block<?php echo $first_compare ? ' is-active' : ''; ?>" id="trx-compare-<?php echo htmlspecialchars($group_id); ?>" data-trx-compare="<?php echo htmlspecialchars($group_id); ?>">
                    <div class="trx-compare-title">
                        <h3><?php echo htmlspecialchars($plan_groups[$group_id]['title']); ?> Comparison</h3>
                    </div>
                    <div class="trx-compare-wrap">
                        <table class="trx-compare-table">
                            <thead>
                                <tr>
                                    <th>Feature</th>
                                    <th>Basic</th>
                                    <th>Standard</th>
                                    <th>Premium</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row[0]); ?></td>
                                        <td><?php echo htmlspecialchars($row[1]); ?></td>
                                        <td><?php echo htmlspecialchars($row[2]); ?></td>
                                        <td><?php echo htmlspecialchars($row[3]); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php $first_compare = false; endforeach; ?>
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
                    <h3>Choose validity</h3>
                    <p>Select a 1-month, 6-month or 12-month package based on your healthcare need.</p>
                </div>
                <div class="trx-step">
                    <div class="trx-step-number">2</div>
                    <h3>Select plan</h3>
                    <p>Pick Basic, Standard or Premium based on call quota, discounts and care support.</p>
                </div>
                <div class="trx-step">
                    <div class="trx-step-number">3</div>
                    <h3>Register members</h3>
                    <p>Add your phone number and eligible family member details for coverage.</p>
                </div>
                <div class="trx-step">
                    <div class="trx-step-number">4</div>
                    <h3>Use support</h3>
                    <p>Book consultation, receive prescriptions, use emergency calls and request follow-up support.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="trx-section">
        <div class="trx-container">
            <div class="trx-terms">
                <div>
                    <h2>Terms & notes</h2>
                    <p>Keep these points visible so users clearly understand what is included in their package.</p>
                </div>
                <ul>
                    <li><span class="trx-check">✓</span><span>Package validity starts from the activation date.</span></li>
                    <li><span class="trx-check">✓</span><span>Free 24/7 emergency doctor calls are limited to the selected package quota.</span></li>
                    <li><span class="trx-check">✓</span><span>GP and specialist consultation discounts apply according to the selected package.</span></li>
                    <li><span class="trx-check">✓</span><span>Specialist consultation, lab tests, home service and product delivery depend on service availability.</span></li>
                    <li><span class="trx-check">✓</span><span>Telemedicine support does not replace hospital emergency treatment for life-threatening conditions.</span></li>
                    <li><span class="trx-check">✓</span><span>Unused call quota or benefits may expire after the package validity period.</span></li>
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
                    <h3>Can I choose monthly or yearly package?</h3>
                    <p>Yes. TeleRx offers 1-month, 6-month and 12-month subscription options.</p>
                </div>
                <div class="trx-faq-item">
                    <h3>Which plan includes specialist consultation discount?</h3>
                    <p>Premium plans include up to 10% specialist consultation discount.</p>
                </div>
                <div class="trx-faq-item">
                    <h3>Does every plan include digital prescription?</h3>
                    <p>Yes. Basic, Standard and Premium plans include digital prescription support.</p>
                </div>
                <div class="trx-faq-item">
                    <h3>Which plan has priority response?</h3>
                    <p>Premium plans include priority response, dedicated care coordinator and personal health history management.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="trx-section">
        <div class="trx-container">
            <div class="trx-bottom-cta">
                <h2>Need help choosing the right plan?</h2>
                <p>Talk to TeleRx support. We will help you choose the right monthly, 6-month or 12-month plan for individual or family healthcare support.</p>
                <a class="trx-btn" href="tel:+8801836838888">Call Now</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    function syncTeleRxTabs(key) {
        document.querySelectorAll('[data-trx-tab]').forEach(function (button) {
            var active = button.getAttribute('data-trx-tab') === key;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        document.querySelectorAll('[data-trx-panel]').forEach(function (panel) {
            panel.classList.toggle('is-active', panel.getAttribute('data-trx-panel') === key);
        });

        document.querySelectorAll('[data-trx-compare]').forEach(function (panel) {
            panel.classList.toggle('is-active', panel.getAttribute('data-trx-compare') === key);
        });
    }

    document.querySelectorAll('[data-trx-tab]').forEach(function (button) {
        button.addEventListener('click', function () {
            syncTeleRxTabs(this.getAttribute('data-trx-tab'));
        });
    });
})();
</script>

<?php
if ($has_site_footer) {
    include __DIR__ . '/footer.php';
} else {
?>
</body>
</html>
<?php } ?>
