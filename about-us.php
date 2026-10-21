<?php
session_start();
include 'header.php';

$hm_asset_v = '1';   // change this number after editing the CSS/JS if the browser keeps showing the old design

// ---------- Services (Welfare / Old-age / Emergency) - same data as before ----------
$service_sections = [
    [
        'id' => 'welfare',
        'image' => 'assets/img/slider-home/slide1.jpg',
        'title' => 'Welfare Project',
        'description' => 'TeleRx Bangladesh provides free medical treatment, medicines, and health camps for underprivileged communities. We ensure healthcare reaches those who need it most.',
        'icon' => 'isax isax-heart',
        'features' => [
            'Free Medical Camps',
            'Medicine Support',
            'Community Health Programs'
        ],
        'color_class' => 'welfare-section'
    ],
    [
        'id' => 'oldage',
        'image' => 'assets/img/slider-home/slide3.jpg',
        'title' => 'Old-age Care',
        'description' => 'Professional nursing care for the elderly – either at home or in partner old-age homes. Health monitoring, medication, and emotional support by TeleRx Bangladesh.',
        'icon' => 'isax isax-people',
        'features' => [
            '24/7 Nursing Care',
            'Health Monitoring',
            'Emotional Support'
        ],
        'color_class' => 'oldage-section'
    ],
    [
        'id' => 'emergency',
        'image' => 'assets/img/slider-home/slide4.jpg',
        'title' => 'Emergency Health Support',
        'description' => 'We take care our patient not with medicine but also with heart. 24/7 emergency response team ready to serve you anytime, anywhere in Bangladesh.',
        'icon' => 'isax isax-ambulance',
        'features' => [
            'Rapid Response',
            'Ambulance Service',
            'Emergency Consultation'
        ],
        'color_class' => 'emergency-section'
    ]
];

// ---------- Why choose TeleRx ----------
$why_cards = [
    ['assets/img/icons/choose-01.svg', 'Qualified Staff of Doctors', 'Our platform exclusively partners with highly qualified doctors who bring expertise & commitment to delivering top-notch healthcare.'],
    ['assets/img/icons/choose-02.svg', '24 Hours Service', 'Experience the healthcare access with our 24/7 service. Whether it\'s day or night, you can find & book appointments.'],
    ['assets/img/icons/choose-03.svg', 'Quality Test', 'Partnering with accredited labs, your health is our priority, and our quality lab services reflect our dedication to excellence.'],
    ['assets/img/icons/choose-04.svg', 'Free Consultations', 'Your well-being is important, and our commitment to providing accessible care begins with a free initial consultation.'],
];

// ---------- Frequently asked questions ----------
$faqs = [
    ['How do I book an appointment?', 'Simply visit our website and log in or create an account. Search for a doctor based on specialization, location, or availability & confirm your booking.'],
    ['Can i make an Appointment Online with any doctor?', 'Yes, simply visit our website and log in or create an account. Search for a doctor based on specialization, location, or availability & confirm your booking.'],
    ['Is my personal information secure?', 'Yes, simply visit our website and log in or create an account. Search for a doctor based on specialization, location, or availability & confirm your booking.'],
    ['Can I cancel or reschedule my appointment?', 'Yes, simply visit our website and log in or create an account. Search for a doctor based on specialization, location, or availability & confirm your booking.'],
    ['How do I find a specific doctor or specialist?', 'Yes, simply visit our website and log in or create an account. Search for a doctor based on specialization, location, or availability & confirm your booking.'],
];

// ---------- Patient reviews: [photo, name, place, title, text] ----------
$reviews = [
    ['assets/img/clients/client-07.jpg', 'ফারহানা আক্তার', 'চট্টগ্রাম, বাংলাদেশ', '“অসাধারণ সেবা”', 'ডাক্তার স্যার অত্যন্ত পেশাদার এবং আন্তরিক। আমার হৃদরোগের সমস্যা নিয়ে অনেক বছর ধরে ভুগছিলাম। TeleRx বাংলাদেশের মাধ্যমে প্রথমবারের মতো একজন বিশেষজ্ঞ ডাক্তারের সাথে পরামর্শ করি এবং সঠিক চিকিৎসা পেয়েছি। অত্যন্ত কৃতজ্ঞ তাদের প্রতি।'],
    ['assets/img/clients/client-08.jpg', 'Kamranul Islam', 'Kushtia, Bangladesh', '“I am very grateful”', 'Doctors explains everything clearly and helps me to understand even the most complex medical term consultation.'],
    ['assets/img/clients/client-09.jpg', 'Sarah Islam', 'Dhaka, Bangladesh', '“ভরসার জায়গা”', 'আমার মায়ের বয়স ৭২ বছর। তাকে নিয়ে হাসপাতালে যাওয়া খুব কষ্টকর ছিল। TeleRx এর মাধ্যমে ঘরে বসেই আমরা নিয়মিত ডাক্তারের পরামর্শ নিচ্ছি। ঔষধপত্রও বাসায় এসে পৌঁছে যায়। সত্যিই একটি অসাধারণ উদ্যোগ।'],
    ['assets/img/clients/client-10.jpg', 'Mehedi Hassan', 'Rangpur, Bangladesh', '“ Excellent Work ”', 'I was traveling in Bangladesh when my father fell ill. TeleRx Bangladesh connected me with a specialist within minutes. The video consultation was seamless, and the doctor was incredibly knowledgeable. They even arranged for medicine delivery to our hotel. Highly recommended!'],
];
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/home-modern.css?v=<?php echo $hm_asset_v; ?>">
<link rel="stylesheet" href="assets/css/pages-modern.css?v=<?php echo $hm_asset_v; ?>">

<main class="hm">
<script>document.documentElement.classList.add('hm-js');</script>

<!-- ===================== ABOUT HERO ===================== -->
<section class="hm-hero" aria-label="About TeleRx Bangladesh">
    <div class="hm-wrap hm-hero-grid pgx-about-grid">
        <div class="pgx-collage hm-reveal" aria-label="TeleRx doctors and care team">
            <div class="pgx-collage-col">
                <figure><img src="assets/img/about-img1.jpg" alt="about-image" loading="lazy"></figure>
                <figure><img src="assets/img/about-img2.jpg" alt="about-image" loading="lazy"></figure>
            </div>
            <div class="pgx-collage-col">
                <div class="pgx-exp"><h3>Over 20+ Years Experience</h3></div>
                <figure><img src="assets/img/about-img3.jpg" alt="about-image" loading="lazy"></figure>
            </div>
        </div>

        <div class="hm-hero-copy">
            <span class="hm-kicker hm-reveal"><i class="fa-solid fa-heart-pulse" aria-hidden="true"></i> About TeleRx Bangladesh</span>
            <h1 class="hm-reveal" style="--d:.05s;margin-top:12px">TeleRx always ensure best medical treatment for your health.</h1>
            <div class="pgx-paras hm-reveal" style="--d:.1s">
                <p>At TeleRx Bangladesh, we are committed to making healthcare simple, accessible, and reliable for everyone. Our mission is to connect patients with qualified doctors and essential medical services through a seamless and secure digital platform.</p>
                <p>Whether you need a routine check-up, specialist consultation, follow-up care, or urgent medical advice, TeleRx Bangladesh ensures a convenient and hassle-free experience. We combine technology with trusted healthcare expertise to deliver timely, patient-centered care—anytime, anywhere across Bangladesh.</p>
            </div>
            <div class="pgx-emergency hm-reveal" style="--d:.15s">
                <span class="pgx-emergency-ic"><i class="isax isax-call-calling5" aria-hidden="true"></i></span>
                <div>
                    <p>Need Emergency?</p>
                    <strong>+880 1836 838888</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== WHY CHOOSE ===================== -->
<section class="hm-section" aria-labelledby="why-title">
    <div class="hm-wrap">
        <div class="hm-head is-center hm-reveal">
            <span class="hm-kicker"><i class="fa-solid fa-shield-heart" aria-hidden="true"></i> Our promise</span>
            <h2 id="why-title">Why Choose TeleRx</h2>
        </div>
        <div class="pgx-why">
            <?php foreach ($why_cards as $i => $card): ?>
                <article class="pgx-why-card hm-reveal" style="--d:<?php echo number_format($i * 0.07, 2); ?>s">
                    <div class="pgx-why-ic"><img src="<?php echo htmlspecialchars($card[0]); ?>" alt="choose-image" loading="lazy"></div>
                    <h3><?php echo htmlspecialchars($card[1]); ?></h3>
                    <p><?php echo htmlspecialchars($card[2]); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== SERVICES ===================== -->
<section class="hm-section is-tint" aria-label="TeleRx services">
    <div class="hm-wrap">
        <div class="pgx-rows">
            <?php foreach ($service_sections as $i => $svc):
                $links = ['welfare.php', 'contact.php', 'tel:+8801836838888'];
                $is_emergency = ($svc['id'] === 'emergency');
            ?>
                <article class="pgx-row hm-reveal<?php echo ($i % 2 === 1) ? ' is-reverse' : ''; ?>" id="<?php echo htmlspecialchars($svc['id']); ?>">
                    <div>
                        <div class="pgx-row-ic"><i class="<?php echo htmlspecialchars($svc['icon']); ?>" aria-hidden="true"></i></div>
                        <h2><?php echo htmlspecialchars($svc['title']); ?></h2>
                        <p><?php echo htmlspecialchars($svc['description']); ?></p>
                        <?php if ($is_emergency): ?>
                            <div class="pgx-emergency" style="margin-top:14px">
                                <span class="pgx-emergency-ic"><i class="fas fa-phone-alt" aria-hidden="true"></i></span>
                                <div>
                                    <strong>+880 1836 838888</strong>
                                    <p>Available 24/7 for emergency support</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <ul class="pgx-ticks">
                            <?php foreach ($svc['features'] as $feature): ?>
                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><span><?php echo htmlspecialchars($feature); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ($is_emergency): ?>
                            <a class="hm-btn is-danger" href="tel:+8801836838888">Call Now <i class="fas fa-phone" aria-hidden="true"></i></a>
                        <?php else: ?>
                            <a class="hm-link-btn is-brand" href="<?php echo htmlspecialchars($links[$i] ?? 'contact.php'); ?>">Learn More <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        <?php endif; ?>
                    </div>
                    <div class="pgx-row-media">
                        <img src="<?php echo htmlspecialchars($svc['image']); ?>" alt="<?php echo htmlspecialchars($svc['title']); ?>" loading="lazy">
                        <?php if ($is_emergency): ?><span class="pgx-row-badge">24/7</span><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== CALL TO ACTION ===================== -->
<section class="hm-section" style="padding:56px 0 8px">
    <div class="hm-wrap">
        <div class="pgx-way hm-reveal">
            <div>
                <h2>Be on Your Way to Feeling Better with the TeleRx</h2>
                <p>Be on your way to feeling better as we prioritize your health journey with personalized and accessible services.</p>
                <a href="contact.php" class="hm-link-btn">Contact With Us <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
            </div>
            <div class="pgx-way-img"><img src="assets/img/way-img.png" alt="doctor-way-image" loading="lazy"></div>
        </div>
    </div>
</section>

<!-- ===================== FAQ ===================== -->
<section class="hm-section" aria-labelledby="faq-title">
    <div class="hm-wrap">
        <div class="hm-head is-center hm-reveal">
            <span class="hm-kicker"><i class="fa-solid fa-circle-question" aria-hidden="true"></i> Good to know</span>
            <h2 id="faq-title">Frequently Asked Questions</h2>
        </div>
        <div class="pgx-faq-grid">
            <div class="pgx-faq-pic hm-reveal">
                <img src="assets/img/faq-img.png" alt="img" loading="lazy">
                <div class="pgx-faq-count">
                    <img src="assets/img/icons/smiling-icon.svg" alt="icon">
                    <div>
                        <strong><span class="count-digit">1</span>k+</strong>
                        <p>Happy Patients</p>
                    </div>
                </div>
            </div>
            <div class="pgx-acc accordion hm-reveal" id="accordionExample" style="--d:.08s">
                <?php $faq_ids = ['One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight']; ?>
                <?php foreach ($faqs as $i => $f): $fid = $faq_ids[$i] ?? ('N' . $i); ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $fid; ?>">
                            <a href="javascript:void(0)" class="accordion-button<?php echo $i === 0 ? '' : ' collapsed'; ?>" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $fid; ?>" aria-expanded="<?php echo $i === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $fid; ?>">
                                <?php echo htmlspecialchars($f[0]); ?>
                            </a>
                        </h2>
                        <div id="collapse<?php echo $fid; ?>" class="accordion-collapse collapse<?php echo $i === 0 ? ' show' : ''; ?>" aria-labelledby="heading<?php echo $fid; ?>" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="accordion-content">
                                    <p><?php echo htmlspecialchars($f[1]); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ===================== PATIENT REVIEWS ===================== -->
<section class="hm-section is-tint" aria-labelledby="rev-title">
    <div class="hm-wrap">
        <div class="hm-head is-center hm-reveal">
            <span class="hm-kicker"><i class="fa-solid fa-star" aria-hidden="true"></i> Testimonials</span>
            <h2 id="rev-title">Our Patients Reviews</h2>
            <p>Patients are very happy with their care and would recommend to anyone else.</p>
        </div>
        <div class="pgx-quotes">
            <?php foreach ($reviews as $i => $r): ?>
                <article class="pgx-quote-card hm-reveal" style="--d:<?php echo number_format($i * 0.07, 2); ?>s">
                    <div class="pgx-quote-top">
                        <div class="pgx-user">
                            <img src="<?php echo htmlspecialchars($r[0]); ?>" alt="<?php echo htmlspecialchars($r[1]); ?>" loading="lazy">
                            <div>
                                <h4><?php echo htmlspecialchars($r[1]); ?></h4>
                                <p><?php echo htmlspecialchars($r[2]); ?></p>
                            </div>
                        </div>
                        <div class="pgx-stars" aria-label="5 out of 5 stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                    </div>
                    <h5><?php echo htmlspecialchars($r[3]); ?></h5>
                    <p><?php echo htmlspecialchars($r[4]); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

</main>

<script defer src="assets/js/home-modern.js?v=<?php echo $hm_asset_v; ?>"></script>

<?php include 'footer.php'; ?>
