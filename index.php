<?php
/**
 * TeleRx Bangladesh - Modern Home Page
 * Design: assets/css/home-modern.css  |  Script: assets/js/home-modern.js
 * Font sizes: headings 18px, sub-headings 14px, normal text 12px (set at the top of home-modern.css)
 *
 * Data (doctors, products, camp photos) is read exactly like the old home page did.
 */
session_start();
include 'header.php';

$hm_asset_v = '1';   // change this number after editing the CSS/JS if the browser keeps showing the old design

// ---------- 1) Doctors for the slider ----------
$doctors_slider = [];
try {
    require_once 'php/config.php';
    $conn = getDBConnection();

    // Fetch doctors with their profiles, limit to 8 for slider
    $stmt = $conn->prepare("
        SELECT
            d.*,
            dp.specialty,
            dp.consultation_fee,
            dp.total_reviews,
            dp.average_rating,
            dp.is_available,
            dp.district,
            dp.city,
            dp.state,
            dp.profile_image
        FROM doctors d
        LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id
        WHERE (dp.is_available = 1 OR dp.is_available IS NULL)
        ORDER BY COALESCE(dp.average_rating, 0) DESC, COALESCE(dp.total_reviews, 0) DESC, d.created_at DESC
        LIMIT 8
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $doctors_slider = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
} catch (Throwable $e) {
    error_log("Error fetching doctors for slider: " . $e->getMessage());
    $doctors_slider = [];
}

// ---------- 2) Products preview (reads Products/data.php) ----------
$trx_home_product_preview = (static function () {
    $data_file = __DIR__ . '/Products/data.php';
    $categories = [];
    $products = [];

    if (is_file($data_file)) {
        require $data_file;

        if (isset($trx_product_categories) && is_array($trx_product_categories)) {
            $categories = $trx_product_categories;
        }

        if (isset($trx_products) && is_array($trx_products)) {
            $products = $trx_products;
        }
    }

    if (!$products) {
        $categories = [
            'devices' => 'Health Devices',
            'home-care' => 'Home Care',
            'supplies' => 'Medical Supplies',
            'wellness' => 'Wellness',
        ];

        $products = [
            'digital-bp-machine' => [
                'id' => 'digital-bp-machine',
                'name' => 'Digital BP Machine',
                'category' => 'devices',
                'icon' => 'BP',
                'price' => 2400,
                'sale_price' => 2200,
                'stock' => 'In Stock',
                'image' => 'assets/products/Digital BP Machine.png',
                'short' => 'Easy blood pressure monitoring device for home use.',
            ],
            'glucometer-kit' => [
                'id' => 'glucometer-kit',
                'name' => 'Glucometer Starter Kit',
                'category' => 'devices',
                'icon' => 'G',
                'price' => 1850,
                'sale_price' => 1650,
                'stock' => 'In Stock',
                'image' => 'assets/products/Glucometer Starter Kit.png',
                'short' => 'Blood glucose monitoring kit for diabetic patients.',
            ],
            'pulse-oximeter' => [
                'id' => 'pulse-oximeter',
                'name' => 'Pulse Oximeter',
                'category' => 'devices',
                'icon' => 'O2',
                'price' => 1250,
                'sale_price' => 1050,
                'stock' => 'In Stock',
                'image' => 'assets/products/Pulse Oximeter.png',
                'short' => 'Finger pulse oximeter for SpO2 and pulse monitoring.',
            ],
            'infrared-thermometer' => [
                'id' => 'infrared-thermometer',
                'name' => 'Infrared Thermometer',
                'category' => 'devices',
                'icon' => 'C',
                'price' => 1450,
                'sale_price' => 1250,
                'stock' => 'In Stock',
                'image' => 'assets/products/Infrared Thermometer.png',
                'short' => 'Contactless thermometer for fast temperature checks.',
            ],
        ];
    }

    $selected = [];

    foreach ($products as $id => $product) {
        if (!empty($product['image'])) {
            $selected[$id] = $product;
        }

        if (count($selected) === 4) {
            break;
        }
    }

    if (count($selected) < 4) {
        foreach ($products as $id => $product) {
            if (!isset($selected[$id])) {
                $selected[$id] = $product;
            }

            if (count($selected) === 4) {
                break;
            }
        }
    }

    return [
        'categories' => $categories,
        'products' => $selected,
        'total' => count($products),
    ];
})();

$trx_product_escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

// ---------- 3) Medical camp photos ----------
$medicalCampShortImages = [
    [
        'src' => 'assets/img/medical-camp/Camp (3).jpeg',
        'alt' => 'TeleRx medical camp patient registration',
        'caption' => 'Patient registration',
        'icon' => 'fa-clipboard-user',
    ],
    [
        'src' => 'assets/img/medical-camp/Camp (1).jpeg',
        'alt' => 'Online doctor consultation at a TeleRx medical camp',
        'caption' => 'Online consultation',
        'icon' => 'fa-video',
    ],
    [
        'src' => 'assets/img/medical-camp/Camp (7).jpeg',
        'alt' => 'Basic health assessment at a TeleRx medical camp',
        'caption' => 'Basic health assessment',
        'icon' => 'fa-heart-pulse',
    ],
];

// ---------- 4) Small helper: makes a file path safe for use inside src="..." (spaces, brackets) ----------
$hm_url = static function ($path) {
    return implode('/', array_map('rawurlencode', explode('/', (string) $path)));
};
?>
<!-- Fonts + stylesheet of the modern home page (only affects this page) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/home-modern.css?v=<?php echo $hm_asset_v; ?>">

<main class="hm" id="hm-root">
<script>document.documentElement.classList.add('hm-js');</script>

<!-- ===================== HERO ===================== -->
<section class="hm-hero" aria-label="Find your doctor">
    <div class="hm-wrap hm-hero-grid">
        <div class="hm-hero-copy">
            <div class="hm-proof hm-reveal">
                <div class="hm-avatars" aria-hidden="true">
                    <span><img src="assets/img/doctors/doctor-thumb-22.jpg" alt="" loading="lazy"></span>
                    <span><img src="assets/img/doctors/doctor-thumb-23.jpg" alt="" loading="lazy"></span>
                    <span><img src="assets/img/doctors/doctor-thumb-24.jpg" alt="" loading="lazy"></span>
                </div>
                <div class="hm-proof-text">
                    <strong>2K+ Appointments</strong>
                    <div class="hm-stars" aria-label="Rated 4.9 out of 5">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        <span>4.9 Ratings</span>
                    </div>
                </div>
            </div>

            <div class="hm-eyebrow hm-reveal" style="--d:.05s">TeleRx Bangladesh &middot; Care Beyond Distance</div>

            <h1 class="hm-reveal" style="--d:.1s">Discover Health: Find Your Trusted <span class="hm-vid"><img src="assets/img/icons/video.svg" alt=""></span> <span class="hm-grad">Doctors</span> Today</h1>

            <form class="hm-search hm-reveal" style="--d:.15s" action="doctors" method="GET" role="search">
                <div class="hm-field">
                    <i class="isax isax-hospital5" aria-hidden="true"></i>
                    <input type="text" name="search" class="hm-input" placeholder="Search doctors, clinics, hospitals, etc" autocomplete="off" aria-label="Search doctors, clinics, hospitals">
                </div>
                <div class="hm-field">
                    <i class="isax isax-location5" aria-hidden="true"></i>
                    <input type="text" name="location" class="hm-input" placeholder="Location" autocomplete="off" aria-label="Location">
                </div>
                <div class="hm-field">
                    <i class="isax isax-calendar-tick5" aria-hidden="true"></i>
                    <input type="text" name="date" class="hm-input datetimepicker" placeholder="Date" autocomplete="off" aria-label="Date">
                </div>
                <button class="hm-btn" type="submit"><i class="isax isax-search-normal5" aria-hidden="true"></i> Search</button>
            </form>

            <nav class="hm-quick hm-reveal" style="--d:.2s" aria-label="Quick links">
                <span>Quick access:</span>
                <a href="doctors">Our Doctors</a>
                <a href="HomeService/home-service">Home Care</a>
                <a href="Products/products.php">Products</a>
                <a href="subscription">Subscription</a>
                <a href="medical-camp">Medical Camp</a>
            </nav>
        </div>

        <div class="hm-visual hm-reveal" style="--d:.15s">
            <div class="hm-visual-bg" aria-hidden="true"></div>
            <div class="hm-visual-photo">
                <img src="assets/img/banner/banner-doctor.webp" alt="TeleRx doctor ready to help the patient" width="500" height="647">
            </div>
            <div class="hm-float is-b">
                <span class="hm-float-ic"><i class="fa-solid fa-circle-check"></i></span>
                <div>
                    <strong>1K</strong>
                    <span class="hm-float-txt">Appointments<br>Completed</span>
                </div>
            </div>
            <div class="hm-float is-a">
                <div class="hm-avatars" aria-hidden="true">
                    <span><img src="assets/img/patients/patient19.jpg" alt="" loading="lazy"></span>
                    <span><img src="assets/img/patients/patient16.jpg" alt="" loading="lazy"></span>
                    <span><img src="assets/img/patients/patient18.jpg" alt="" loading="lazy"></span>
                </div>
                <div>
                    <strong>1K+</strong>
                    <span class="hm-float-txt">Satisfied Patients</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SERVICES STRIP ===================== -->
<section class="hm-marquee" aria-label="TeleRx services">
    <div class="hm-marquee-track">
        <ul class="hm-marquee-set">
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);">Multi Speciality Treatments &amp; Doctors</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);">Treatment Beyond Distance</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);">Medicines &amp; Supplies</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);">Global Care</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);">Old-age Care</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);">Talk to Doctors</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);">Home Care Services</a></li>
        </ul>
        <ul class="hm-marquee-set" aria-hidden="true">
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);" tabindex="-1">Multi Speciality Treatments &amp; Doctors</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);" tabindex="-1">Treatment Beyond Distance</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);" tabindex="-1">Medicines &amp; Supplies</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);" tabindex="-1">Global Care</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);" tabindex="-1">Old-age Care</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);" tabindex="-1">Talk to Doctors</a></li>
            <li class="hm-chip"><i class="fa-solid fa-circle"></i><a href="javascript:void(0);" tabindex="-1">Home Care Services</a></li>
        </ul>
    </div>
</section>

<!-- ===================== BEST DOCTORS ===================== -->
<section class="hm-section" id="home-doctors" aria-labelledby="hm-doctors-title">
    <div class="hm-wrap">
        <div class="hm-row-head hm-reveal">
            <div class="hm-head">
                <span class="hm-kicker"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i> Our Doctors</span>
                <h2 id="hm-doctors-title">TeleRx Best Doctors</h2>
            </div>
            <div class="hm-nav">
                <a class="hm-link" href="doctors">View all doctors <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
                <button type="button" class="hm-nav-btn" id="hmDocPrev" aria-label="Previous doctors"><i class="fa-solid fa-arrow-left"></i></button>
                <button type="button" class="hm-nav-btn" id="hmDocNext" aria-label="Next doctors"><i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>

        <div class="hm-doc-track hm-reveal" id="hmDocTrack" tabindex="0" aria-label="Best doctors list">
<?php /* HM-DYNAMIC:doctors */ ?>
            <?php if (!empty($doctors_slider)): ?>
                <?php foreach ($doctors_slider as $doctor): ?>
                    <?php
                    $hm_doc_img   = $doctor['profile_image'] ?? 'assets/img/doctors/default-doctor.png';
                    $hm_available = ($doctor['is_available'] == 1);
                    $hm_loc_parts = array_filter([
                        $doctor['district'] ?? '',
                        $doctor['city'] ?? '',
                        $doctor['state'] ?? ''
                    ]);
                    $hm_loc = !empty($hm_loc_parts) ? implode(', ', $hm_loc_parts) : 'Dhaka, Bangladesh';
                    ?>
                    <article class="hm-doc">
                        <a class="hm-doc-img" href="doctor-profile?id=<?php echo $doctor['id']; ?>" tabindex="-1" aria-hidden="true">
                            <img src="<?php echo htmlspecialchars($hm_doc_img); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>" loading="lazy">
                            <span class="hm-fee">৳<?php echo number_format($doctor['consultation_fee'] ?? 0, 0); ?></span>
                            <span class="hm-avail <?php echo $hm_available ? 'is-on' : 'is-off'; ?>"><i class="fa-solid fa-circle"></i> <?php echo $hm_available ? 'Available' : 'Unavailable'; ?></span>
                        </a>
                        <div class="hm-doc-body">
                            <div class="hm-doc-top">
                                <div>
                                    <a class="hm-doc-name" href="doctor-profile?id=<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['name']); ?></a>
                                    <p class="hm-doc-spec"><?php echo htmlspecialchars($doctor['specialty'] ?? 'General Physician'); ?></p>
                                </div>
                                <span class="hm-rate"><i class="fa-solid fa-star"></i> <?php echo number_format($doctor['average_rating'] ?? 0, 1); ?> <small>(<?php echo $doctor['total_reviews'] ?? 0; ?>)</small></span>
                            </div>
                            <div class="hm-doc-loc"><i class="isax isax-location" aria-hidden="true"></i><span><?php echo htmlspecialchars($hm_loc); ?></span></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="hm-empty">No doctors available at the moment.</div>
            <?php endif; ?>
<?php /* HM-END */ ?>
        </div>
    </div>
</section>

<!-- ===================== SUBSCRIPTION ===================== -->
<section class="hm-section" id="home-subscription" aria-labelledby="home-subscription-title" style="padding-top:0">
    <div class="hm-wrap">
        <div class="hm-panel hm-reveal">
            <div class="hm-split">
                <div>
                    <span class="hm-kicker is-light"><i class="fa-solid fa-shield-heart" aria-hidden="true"></i> Smarter everyday healthcare</span>
                    <h2 id="home-subscription-title" style="margin-top:12px;color:#fff">Healthcare support that stays with you</h2>
                    <p style="margin-top:8px;color:rgba(255,255,255,.78)">
                        Choose a TeleRx subscription for convenient doctor support, digital prescriptions,
                        selected service discounts and organized care for you or your family.
                    </p>

                    <div class="hm-feat-list" aria-label="Subscription benefits">
                        <article class="hm-feat">
                            <span class="hm-feat-ic"><i class="fa-solid fa-user-doctor"></i></span>
                            <div>
                                <h3>Doctor Access</h3>
                                <p>Talk to qualified doctors with less waiting and easier follow-up.</p>
                            </div>
                        </article>
                        <article class="hm-feat">
                            <span class="hm-feat-ic"><i class="fa-solid fa-file-prescription"></i></span>
                            <div>
                                <h3>Digital Care Record</h3>
                                <p>Keep prescriptions and care guidance organized in one place.</p>
                            </div>
                        </article>
                        <article class="hm-feat">
                            <span class="hm-feat-ic"><i class="fa-solid fa-people-roof"></i></span>
                            <div>
                                <h3>Individual &amp; Family Plans</h3>
                                <p>Select a package that matches personal or family healthcare needs.</p>
                            </div>
                        </article>
                    </div>

                    <a class="hm-link is-light" href="subscription">Know more about Subscription <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
                </div>

                <div class="hm-sub-visual" aria-label="TeleRx subscription overview">
                    <div class="hm-sub-photo">
                        <img src="assets/img/about-img1.jpg" alt="Patient receiving convenient healthcare support" loading="lazy">
                    </div>
                    <div class="hm-badge is-top"><i class="fa-solid fa-bolt"></i><span><strong>Priority</strong> support</span></div>
                    <div class="hm-badge is-bottom"><i class="fa-solid fa-percent"></i><span>Selected service benefits</span></div>
                    <div class="hm-plan">
                        <div class="hm-plan-head">
                            <span class="hm-plan-ic"><i class="fa-solid fa-heart-pulse"></i></span>
                            <span class="hm-plan-tag">Flexible care</span>
                        </div>
                        <strong>TeleRx Health Plans</strong>
                        <p>Monthly, 6-month and 12-month options.</p>
                        <div class="hm-pills"><span>Consultation</span><span>Prescription</span><span>Follow-up</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== PRODUCTS ===================== -->
<section class="hm-section is-tint" id="home-products" aria-labelledby="home-products-title">
    <div class="hm-wrap">
        <div class="hm-prod-head hm-reveal">
            <div class="hm-head">
                <span class="hm-kicker"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i> TeleRx Health Products</span>
                <h2 id="home-products-title">Trusted health essentials for everyday home care</h2>
                <p>
                    Explore useful health devices, home-care items, medical supplies and wellness
                    products with simple ordering and delivery support.
                </p>
            </div>
            <div class="hm-notes" aria-label="Product ordering benefits">
                <span><i class="fa-solid fa-phone-volume"></i> Order confirmation</span>
                <span><i class="fa-solid fa-truck-fast"></i> Delivery support</span>
                <span><i class="fa-solid fa-money-bill-wave"></i> Cash on delivery</span>
            </div>
        </div>

        <div class="hm-prod-grid hm-reveal" style="--d:.08s">
            <aside class="hm-prod-side">
                <span class="hm-eyebrow-s"><i class="fa-solid fa-heart-pulse" aria-hidden="true"></i> Care made more convenient</span>
                <h3>Choose the right product for monitoring, recovery and daily support</h3>
                <p>
                    TeleRx combines practical healthcare products with guidance, order confirmation
                    and a clear path to professional medical support when needed.
                </p>
                <ul class="hm-checks">
                    <li><i class="fa-solid fa-circle-check"></i><span>Home monitoring devices</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span>Caregiver and patient essentials</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span>Easy product selection and ordering</span></li>
                </ul>
                <div class="hm-count">
                    <span class="hm-count-ic"><i class="fa-solid fa-box-open"></i></span>
                    <div>
<?php /* HM-DYNAMIC:product-count */ ?>
                        <strong><?php echo (int) $trx_home_product_preview['total']; ?>+</strong>
<?php /* HM-END */ ?>
                        <small>Health products available</small>
                    </div>
                </div>
            </aside>

            <div class="hm-products" aria-label="Featured TeleRx products">
<?php /* HM-DYNAMIC:products */ ?>
                <?php foreach ($trx_home_product_preview['products'] as $product_id => $product): ?>
                    <?php
                    $product_name = $product['name'] ?? 'Health Product';
                    $product_category_key = $product['category'] ?? '';
                    $product_category = $trx_home_product_preview['categories'][$product_category_key] ?? 'Health Product';
                    $product_stock = $product['stock'] ?? 'Available';
                    $product_image = trim((string) ($product['image'] ?? ''));
                    $product_icon = $product['icon'] ?? 'Rx';
                    $product_price = !empty($product['sale_price']) ? $product['sale_price'] : ($product['price'] ?? 0);
                    $product_old_price = !empty($product['sale_price']) ? ($product['price'] ?? 0) : 0;
                    $details_url = 'Products/product-details.php?id=' . rawurlencode((string) $product_id);
                    ?>
                    <article class="hm-prod">
                        <a class="hm-prod-media" href="<?php echo $trx_product_escape($details_url); ?>" aria-label="View <?php echo $trx_product_escape($product_name); ?> details">
                            <span class="hm-stock"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> <?php echo $trx_product_escape($product_stock); ?></span>
                            <?php if ($product_image !== ''): ?>
                                <img src="<?php echo $trx_product_escape($hm_url('Products/' . ltrim($product_image, '/'))); ?>" alt="<?php echo $trx_product_escape($product_name); ?>" loading="lazy">
                            <?php else: ?>
                                <span class="hm-prod-ph"><?php echo $trx_product_escape($product_icon); ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="hm-prod-body">
                            <span class="hm-cat"><?php echo $trx_product_escape($product_category); ?></span>
                            <h3><a href="<?php echo $trx_product_escape($details_url); ?>"><?php echo $trx_product_escape($product_name); ?></a></h3>
                            <p><?php echo $trx_product_escape($product['short'] ?? 'Practical healthcare support for home use.'); ?></p>
                            <div class="hm-prod-foot">
                                <div class="hm-price">
                                    <strong>৳<?php echo number_format((float) $product_price); ?></strong>
                                    <?php if ($product_old_price): ?>
                                        <del>৳<?php echo number_format((float) $product_old_price); ?></del>
                                    <?php endif; ?>
                                </div>
                                <a class="hm-arrow" href="<?php echo $trx_product_escape($details_url); ?>" aria-label="Open <?php echo $trx_product_escape($product_name); ?>"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
<?php /* HM-END */ ?>
            </div>
        </div>

        <div class="hm-prod-bottom hm-reveal">
            <p>
                Health products support monitoring and care. They do not replace doctor consultation
                or emergency treatment.
            </p>
            <a class="hm-link" href="Products/products.php">View more Product details <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
        </div>
    </div>
</section>

<!-- ===================== HOME CARE ===================== -->
<section class="hm-section" id="home-care" aria-labelledby="home-care-title">
    <div class="hm-wrap">
        <div class="hm-head is-center hm-reveal">
            <span class="hm-kicker"><i class="fa-solid fa-house-medical" aria-hidden="true"></i> Care at your doorstep</span>
            <h2 id="home-care-title">Professional Home Care, arranged around your family</h2>
            <p>
                Get coordinated healthcare support at home for elderly patients, recovery needs,
                routine monitoring and selected clinical services.
            </p>
        </div>

        <div class="hm-care hm-reveal" style="--d:.08s">
            <div class="hm-care-hero">
                <div class="hm-care-art">
                    <img src="HomeService/assets/services/nursing-care-at-home.svg" alt="Nursing care at home illustration" loading="lazy">
                </div>
                <div class="hm-care-body">
                    <span class="hm-tag">Home nursing support</span>
                    <h3>Safe, compassionate care without repeated hospital travel</h3>
                    <p>
                        TeleRx helps families coordinate suitable home-based support according to the
                        patient’s condition, location and service availability.
                    </p>
                    <div class="hm-note">
                        <i class="fa-solid fa-phone-volume" aria-hidden="true"></i>
                        <span>Call the TeleRx helpline to discuss the patient’s care needs.</span>
                    </div>
                </div>
            </div>

            <div class="hm-svc-grid" aria-label="Popular home care services">
                <article class="hm-svc">
                    <span class="hm-svc-ic"><i class="fa-solid fa-user-nurse"></i></span>
                    <div>
                        <h3>Nursing &amp; Caregiver</h3>
                        <p>Daily support, monitoring and recovery assistance at home.</p>
                    </div>
                </article>
                <article class="hm-svc">
                    <span class="hm-svc-ic"><i class="fa-solid fa-stethoscope"></i></span>
                    <div>
                        <h3>Doctor Home Visit</h3>
                        <p>Consultation for patients who cannot easily travel to a clinic.</p>
                    </div>
                </article>
                <article class="hm-svc">
                    <span class="hm-svc-ic"><i class="fa-solid fa-person-walking"></i></span>
                    <div>
                        <h3>Physiotherapy</h3>
                        <p>Guided rehabilitation, mobility and pain-management support.</p>
                    </div>
                </article>
                <article class="hm-svc">
                    <span class="hm-svc-ic"><i class="fa-solid fa-vial-circle-check"></i></span>
                    <div>
                        <h3>Sample Collection</h3>
                        <p>Convenient home sample collection through available partners.</p>
                    </div>
                </article>
            </div>
        </div>

        <div class="hm-center hm-reveal">
            <a class="hm-link-btn is-brand" href="HomeService/home-service">View more Home Care details <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
        </div>
    </div>
</section>

<!-- ===================== CORPORATE / NGO ===================== -->
<section class="hm-section" id="home-corporate" aria-labelledby="home-corporate-title" style="padding-top:0">
    <div class="hm-wrap">
        <div class="hm-panel is-ink hm-reveal">
            <div class="hm-split">
                <div>
                    <span class="hm-kicker is-teal"><i class="fa-solid fa-building-shield" aria-hidden="true"></i> Corporate &amp; NGO healthcare</span>
                    <h2 id="home-corporate-title" style="margin-top:12px;color:#fff">A practical healthcare partner for your people</h2>
                    <p style="margin-top:8px;color:rgba(255,255,255,.78)">
                        Build a customized employee or beneficiary health plan with telemedicine,
                        health campaigns, coordinated referrals and reporting support.
                    </p>
                    <ul class="hm-checklist">
                        <li><i class="fa-solid fa-circle-check"></i><span>Employee and beneficiary teleconsultation</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>Health screening and awareness campaigns</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>Dedicated coordination for approved programs</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>Flexible plans for different organization sizes</span></li>
                    </ul>
                    <a class="hm-link-btn" href="corporate">View more Corporate / NGO details <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
                </div>

                <div class="hm-dash" aria-label="Corporate healthcare program features">
                    <div class="hm-dash-top">
                        <div>
                            <span>Organization Care</span>
                            <strong>Program overview</strong>
                        </div>
                        <span class="hm-live"><i class="fa-solid fa-circle"></i> Flexible</span>
                    </div>
                    <div class="hm-metrics">
                        <article class="hm-metric">
                            <span class="hm-metric-ic"><i class="fa-solid fa-users"></i></span>
                            <strong>Workforce</strong>
                            <small>Healthcare access for teams</small>
                        </article>
                        <article class="hm-metric">
                            <span class="hm-metric-ic"><i class="fa-solid fa-hand-holding-medical"></i></span>
                            <strong>Beneficiaries</strong>
                            <small>NGO program coordination</small>
                        </article>
                        <article class="hm-metric">
                            <span class="hm-metric-ic"><i class="fa-solid fa-chart-line"></i></span>
                            <strong>Reporting</strong>
                            <small>Program-level visibility</small>
                        </article>
                        <article class="hm-metric">
                            <span class="hm-metric-ic"><i class="fa-solid fa-headset"></i></span>
                            <strong>Support</strong>
                            <small>Dedicated coordination</small>
                        </article>
                    </div>
                    <div class="hm-flow">
                        <span>Needs assessment</span>
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>Plan design</span>
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>Service delivery</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== WELFARE ===================== -->
<section class="hm-section" id="home-welfare" aria-labelledby="home-welfare-title" style="padding-top:0">
    <div class="hm-wrap">
        <div class="hm-welfare hm-reveal">
            <div>
                <span class="hm-kicker is-warm"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i> Care for communities</span>
                <h2 id="home-welfare-title" style="margin-top:12px">Small support can create a healthier tomorrow</h2>
                <p style="margin-top:8px">
                    TeleRx Welfare connects compassionate support with people who face barriers to
                    healthcare, medicine and essential health services.
                </p>
                <div class="hm-actions">
                    <article class="hm-action">
                        <span class="hm-action-ic"><i class="fa-solid fa-heart-circle-plus"></i></span>
                        <div>
                            <h3>Support a patient</h3>
                            <p>Help eligible people access essential healthcare assistance.</p>
                        </div>
                    </article>
                    <article class="hm-action">
                        <span class="hm-action-ic"><i class="fa-solid fa-people-group"></i></span>
                        <div>
                            <h3>Strengthen communities</h3>
                            <p>Contribute to rural and underserved health initiatives.</p>
                        </div>
                    </article>
                    <article class="hm-action">
                        <span class="hm-action-ic"><i class="fa-solid fa-handshake-angle"></i></span>
                        <div>
                            <h3>Join the mission</h3>
                            <p>Participate as a donor, organization or community partner.</p>
                        </div>
                    </article>
                </div>
                <a class="hm-link" href="welfare">Know more about Welfare <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
            </div>

            <div>
                <div class="hm-story-img">
                    <img src="assets/img/bg/donation.jpg" alt="Community welfare and healthcare support" loading="lazy" decoding="async">
                    <div class="hm-quote">
                        <i class="fa-solid fa-quote-left" aria-hidden="true"></i>
                        <p>Healthcare becomes more meaningful when no one is left behind.</p>
                    </div>
                </div>
                <div class="hm-trust">
                    <span><i class="fa-solid fa-circle-check"></i> Human-centered</span>
                    <span><i class="fa-solid fa-circle-check"></i> Community focused</span>
                    <span><i class="fa-solid fa-circle-check"></i> Transparent support</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== MEDICAL CAMP ===================== -->
<section class="hm-section is-tint" id="home-medical-camp" aria-labelledby="trxmc-short-title">
    <div class="hm-wrap">
        <div class="hm-camp hm-reveal">
            <div>
                <span class="hm-kicker"><i class="fa-solid fa-house-medical-circle-check" aria-hidden="true"></i> Community healthcare initiative</span>
                <h2 id="trxmc-short-title" style="margin-top:12px">Medical care brought closer through organized TeleRx camps.</h2>
                <p style="margin-top:8px">
                    TeleRx Bangladesh arranges medical camps in villages, schools, educational
                    institutions, orphanages and community locations. Patients receive supported
                    online consultation with doctors through the TeleRx website.
                </p>
                <div class="hm-points" aria-label="Medical camp highlights">
                    <div class="hm-point">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        <span><strong>Local access</strong><br>Camps organized inside the community.</span>
                    </div>
                    <div class="hm-point">
                        <i class="fa-solid fa-user-doctor" aria-hidden="true"></i>
                        <span><strong>Doctor consultation</strong><br>Guided online medical care.</span>
                    </div>
                    <div class="hm-point">
                        <i class="fa-solid fa-file-prescription" aria-hidden="true"></i>
                        <span><strong>Clear next steps</strong><br>Advice, prescription or referral guidance.</span>
                    </div>
                </div>
                <a class="hm-link-btn is-brand" href="medical-camp">Know more about Medical Camp <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
            </div>

            <div class="hm-gallery" aria-label="TeleRx medical camp photographs">
<?php /* HM-DYNAMIC:camp */ ?>
                <?php foreach ($medicalCampShortImages as $index => $image): ?>
                    <?php $absoluteImagePath = __DIR__ . '/' . $image['src']; ?>
                    <figure class="hm-photo">
                        <?php if (is_file($absoluteImagePath)): ?>
                            <img src="<?php echo htmlspecialchars($hm_url($image['src']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($image['alt'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="hm-ph" aria-label="<?php echo htmlspecialchars($image['alt'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="fa-solid <?php echo htmlspecialchars($image['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                                <span>Replace with camp photo <?php echo $index + 1; ?></span>
                            </div>
                        <?php endif; ?>
                        <figcaption><i class="fa-solid <?php echo htmlspecialchars($image['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i> <?php echo htmlspecialchars($image['caption'], ENT_QUOTES, 'UTF-8'); ?></figcaption>
                    </figure>
                <?php endforeach; ?>
<?php /* HM-END */ ?>
                <div class="hm-camp-note">
                    <i class="fa-solid fa-wifi" aria-hidden="true"></i>
                    <span><strong>Connected care</strong>Telemedicine-enabled medical camp</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TELERX WORKS ===================== -->
<section class="hm-section" aria-labelledby="hm-how-title">
    <div class="hm-wrap">
        <div class="hm-head is-center hm-reveal">
            <span class="hm-kicker"><i class="fa-solid fa-route" aria-hidden="true"></i> Simple steps</span>
            <h2 id="hm-how-title">How TeleRx Works</h2>
        </div>

        <div class="hm-steps">
            <article class="hm-step hm-reveal">
                <span class="hm-step-ic"><i class="isax isax-search-normal5" aria-hidden="true"></i><span class="hm-step-no">1</span></span>
                <div>
                    <h3>Search For Doctors</h3>
                    <p>Search for a doctor based on specialization, location, or availability for your Treatments</p>
                </div>
            </article>
            <article class="hm-step hm-reveal" style="--d:.08s">
                <span class="hm-step-ic"><i class="isax isax-security-user5" aria-hidden="true"></i><span class="hm-step-no">2</span></span>
                <div>
                    <h3>Check Doctor Profile</h3>
                    <p>Explore detailed doctor profiles on our platform to make informed healthcare decisions.</p>
                </div>
            </article>
            <article class="hm-step hm-reveal" style="--d:.16s">
                <span class="hm-step-ic"><i class="isax isax-calendar5" aria-hidden="true"></i><span class="hm-step-no">3</span></span>
                <div>
                    <h3>Schedule Appointment</h3>
                    <p>After choose your preferred doctor, select a convenient time slot, &amp; confirm your appointment.</p>
                </div>
            </article>
            <article class="hm-step hm-reveal" style="--d:.24s">
                <span class="hm-step-ic"><i class="isax isax-blend5" aria-hidden="true"></i><span class="hm-step-no">4</span></span>
                <div>
                    <h3>Get Your Solution</h3>
                    <p>Discuss your health concerns with the doctor and receive the personalized advice &amp; with solution.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- ===================== PARTNERS ===================== -->
<section class="hm-section is-tint" aria-labelledby="hm-partners-title" style="padding:52px 0">
    <div class="hm-wrap">
        <div class="hm-head is-center hm-reveal" style="margin-bottom:22px">
            <h2 id="hm-partners-title">Our Partners</h2>
        </div>
        <div class="hm-partners hm-reveal">
            <a class="hm-partner" href="javascript:void(0);"><img src="assets/img/partners/rac.jpg" alt="partners" loading="lazy"></a>
            <a class="hm-partner" href="javascript:void(0);"><img src="assets/img/partners/haam.jpg" alt="partners" loading="lazy"></a>
            <a class="hm-partner" href="javascript:void(0);"><img src="assets/img/partners/abaid.jpg" alt="partners" loading="lazy"></a>
            <a class="hm-partner" href="javascript:void(0);"><img src="assets/img/partners/angm.jpg" alt="partners" loading="lazy"></a>
        </div>
    </div>
</section>

<!-- ===================== CONTACT BAND ===================== -->
<section class="hm-section" style="padding:56px 0 64px">
    <div class="hm-wrap">
        <div class="hm-cta hm-reveal">
            <h2>Working for Your Better Health.</h2>
            <div class="hm-contacts">
                <a class="hm-contact" href="tel:+8801836838888">
                    <span class="hm-contact-ic"><i class="isax isax-headphone" aria-hidden="true"></i></span>
                    <span><small>Customer Support</small><strong>+880 1836 838888</strong></span>
                </a>
                <a class="hm-contact" href="mailto:care@telerxbd.com">
                    <span class="hm-contact-ic"><i class="isax isax-message-2" aria-hidden="true"></i></span>
                    <span><small>Drop Us an Email</small><strong>care@telerxbd.com</strong></span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ===================== 24/7 EMERGENCY POPUP ===================== -->
<div class="hm-emg" id="emergencyPopup" role="dialog" aria-label="24/7 Emergency Care">
    <div class="hm-emg-ic"><i class="fa-solid fa-truck-medical" aria-hidden="true"></i></div>
    <div class="hm-emg-txt">
        <h4>24/7 Emergency Care</h4>
        <p>Get immediate consultation with an available doctor now.</p>
    </div>
    <a href="emergency-booking.php" class="hm-btn is-danger">Book Now</a>
    <button type="button" class="hm-emg-close" aria-label="Close emergency popup" onclick="document.getElementById('emergencyPopup').style.display='none'"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
</div>

</main>

<script defer src="assets/js/home-modern.js?v=<?php echo $hm_asset_v; ?>"></script>

<?php include 'footer.php'; ?>
