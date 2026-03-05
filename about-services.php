<?php
// সেকশন ডেটা অ্যারে
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
?>

<!-- সেকশন ১: Welfare Project -->
<section class="about-service-section <?php echo $service_sections[0]['color_class']; ?>" id="<?php echo $service_sections[0]['id']; ?>">
    <div class="container">
        <div class="row align-items-center service-row">
            <div class="col-lg-6 col-md-12 order-lg-1 order-2" data-aos="fade-right">
                <div class="service-content-wrapper">
                    <div class="service-icon">
                        <span><i class="<?php echo $service_sections[0]['icon']; ?>"></i></span>
                    </div>
                    <h2 class="service-title"><?php echo $service_sections[0]['title']; ?></h2>
                    <p class="service-description"><?php echo $service_sections[0]['description']; ?></p>
                    
                    <ul class="service-features">
                        <?php foreach ($service_sections[0]['features'] as $feature): ?>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo $feature; ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <a href="welfare.php" class="service-btn">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-lg-2 order-1" data-aos="fade-left">
                <div class="service-image-wrapper">
                    <div class="service-image">
                        <img src="<?php echo $service_sections[0]['image']; ?>" alt="<?php echo $service_sections[0]['title']; ?>" class="img-fluid">
                    </div>
                    <div class="service-shape shape-1"></div>
                    <div class="service-shape shape-2"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- সেকশন ২: Old-age Care (Alternating Layout) -->
<section class="about-service-section <?php echo $service_sections[1]['color_class']; ?> alt-bg" id="<?php echo $service_sections[1]['id']; ?>">
    <div class="container">
        <div class="row align-items-center service-row">
            <div class="col-lg-6 col-md-12" data-aos="fade-right">
                <div class="service-image-wrapper">
                    <div class="service-image">
                        <img src="<?php echo $service_sections[1]['image']; ?>" alt="<?php echo $service_sections[1]['title']; ?>" class="img-fluid">
                    </div>
                    <div class="service-shape shape-3"></div>
                    <div class="service-shape shape-4"></div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12" data-aos="fade-left">
                <div class="service-content-wrapper">
                    <div class="service-icon">
                        <span><i class="<?php echo $service_sections[1]['icon']; ?>"></i></span>
                    </div>
                    <h2 class="service-title"><?php echo $service_sections[1]['title']; ?></h2>
                    <p class="service-description"><?php echo $service_sections[1]['description']; ?></p>
                    
                    <ul class="service-features">
                        <?php foreach ($service_sections[1]['features'] as $feature): ?>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo $feature; ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <a href="contact.php" class="service-btn">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- সেকশন ৩: Emergency Health Support -->
<section class="about-service-section <?php echo $service_sections[2]['color_class']; ?>" id="<?php echo $service_sections[2]['id']; ?>">
    <div class="container">
        <div class="row align-items-center service-row">
            <div class="col-lg-6 col-md-12 order-lg-1 order-2" data-aos="fade-right">
                <div class="service-content-wrapper">
                    <h2 class="service-title emergency-title"><?php echo $service_sections[2]['title']; ?></h2>
                    <p class="service-description"><?php echo $service_sections[2]['description']; ?></p>
                    
                    <div class="emergency-contact">
                        <div class="emergency-phone">
                            <i class="fas fa-phone-alt"></i>
                            <span>+880 1836 838888</span>
                        </div>
                        <p>Available 24/7 for emergency support</p>
                    </div>
                    
                    <ul class="service-features">
                        <?php foreach ($service_sections[2]['features'] as $feature): ?>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo $feature; ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <a href="tel:+8801836838888" class="service-btn emergency-btn">
                        Call Now <i class="fas fa-phone"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-lg-2 order-1" data-aos="fade-left">
                <div class="service-image-wrapper">
                    <div class="service-image emergency-image">
                        <img src="<?php echo $service_sections[2]['image']; ?>" alt="<?php echo $service_sections[2]['title']; ?>" class="img-fluid">
                    </div>
                    <div class="service-shape shape-5"></div>
                    <div class="service-badge">
                        <span>24/7</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* About Service Sections - Main Styles */
.about-service-section {
    padding: 30px 0;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%);
}

.about-service-section.alt-bg {
    background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
}

/* Container */
.container {
    max-width: auto;
    margin: 0 auto;
    padding: 0 20px;
}

/* Service Row */
.service-row {
    position: relative;
    z-index: 2;
}

/* Content Wrapper */
.service-content-wrapper {
    padding: 40px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 30px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(21, 85, 141, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Service Icon */
.service-icon {
    margin-bottom: 25px;
}

.service-icon span {
    display: inline-flex;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #15558d, #0c77c9);
    border-radius: 20px;
    align-items: center;
    justify-content: center;
}

.service-icon i {
    font-size: 40px;
    color: #ffffff;
}

.welfare-section .service-icon span {
    background: linear-gradient(135deg, #15558d, #2a7fbe);
}

.oldage-section .service-icon span {
    background: linear-gradient(135deg, #0c77c9, #4a9fe0);
}

.emergency-section .service-icon span {
    background: linear-gradient(135deg, #d43f3f, #ff6b6b);
}

/* Service Title */
.service-title {
    font-size: 36px;
    font-weight: 700;
    color: #012047;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 15px;
}

.service-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #15558d, #0c77c9);
    border-radius: 2px;
}

.emergency-title::after {
    background: linear-gradient(90deg, #d43f3f, #ff6b6b);
}

/* Service Description */
.service-description {
    font-size: 16px;
    text-align: justify;
    line-height: 1.8;
    color: #4a5568;
    margin-bottom: 25px;
}

/* Service Features */
.service-features {
    list-style: none;
    padding: 0;
    margin: 0 0 30px 0;
}

.service-features li {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    font-size: 16px;
    color: #2d3748;
}

.service-features li i {
    color: #15558d;
    font-size: 20px;
}

.emergency-section .service-features li i {
    color: #d43f3f;
}

/* Service Button */
.service-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 35px;
    background: linear-gradient(90deg, #15558d, #0c77c9);
    color: #ffffff;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    box-shadow: 0 10px 20px rgba(21, 85, 141, 0.3);
}

.service-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(21, 85, 141, 0.4);
    color: #ffffff;
}

.service-btn i {
    transition: transform 0.3s ease;
}

.service-btn:hover i {
    transform: translateX(5px);
}

.emergency-btn {
    background: linear-gradient(90deg, #d43f3f, #ff6b6b);
    box-shadow: 0 10px 20px rgba(212, 63, 63, 0.3);
}

.emergency-btn:hover {
    box-shadow: 0 15px 30px rgba(212, 63, 63, 0.4);
}

/* Emergency Contact */
.emergency-contact {
    background: rgba(212, 63, 63, 0.1);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 25px;
    border-left: 4px solid #d43f3f;
}

.emergency-phone {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 24px;
    font-weight: 700;
    color: #d43f3f;
    margin-bottom: 5px;
}

.emergency-phone i {
    font-size: 28px;
}

.emergency-contact p {
    margin: 0;
    color: #4a5568;
    font-size: 14px;
}

/* Service Image Wrapper */
.service-image-wrapper {
    position: relative;
    padding: 10px;
}

.service-image {
    position: relative;
    border-radius: 30px;
    overflow: hidden;
    box-shadow: 0 30px 40px rgba(0, 32, 71, 0.15);
    transform: rotate(2deg);
    transition: all 0.5s ease;
}

.service-image:hover {
    transform: rotate(0deg) scale(1.02);
}

.service-image img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.5s ease;
}

.service-image:hover img {
    transform: scale(1.05);
}

.emergency-image {
    transform: rotate(-2deg);
}

.emergency-image:hover {
    transform: rotate(0deg) scale(1.02);
}

/* Service Shapes */
.service-shape {
    position: absolute;
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, rgba(21, 85, 141, 0.1), rgba(12, 119, 201, 0.1));
    border-radius: 50%;
    z-index: -1;
}

.shape-1 {
    top: -20px;
    right: -20px;
    width: 150px;
    height: 150px;
}

.shape-2 {
    bottom: -30px;
    left: -30px;
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, rgba(12, 119, 201, 0.1), rgba(21, 85, 141, 0.1));
}

.shape-3 {
    top: -40px;
    left: -40px;
    width: 180px;
    height: 180px;
}

.shape-4 {
    bottom: -20px;
    right: -20px;
    width: 160px;
    height: 160px;
}

.shape-5 {
    top: -30px;
    right: -30px;
    width: 170px;
    height: 170px;
    background: linear-gradient(135deg, rgba(212, 63, 63, 0.1), rgba(255, 107, 107, 0.1));
}

/* Service Badge */
.service-badge {
    position: absolute;
    top: 40px;
    right: 40px;
    background: linear-gradient(135deg, #d43f3f, #ff6b6b);
    color: #ffffff;
    padding: 10px 20px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 18px;
    box-shadow: 0 10px 20px rgba(212, 63, 63, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

/* Responsive Design */
@media (max-width: 991px) {
    .about-service-section {
        padding: 60px 0;
    }
    
    .service-content-wrapper {
        padding: 30px;
        margin-top: 30px;
    }
    
    .service-title {
        font-size: 30px;
    }
    
    .service-image-wrapper {
        margin-bottom: 30px;
    }
}

@media (max-width: 768px) {
    .service-content-wrapper {
        padding: 25px;
    }
    
    .service-title {
        font-size: 26px;
    }
    
    .service-icon span {
        width: 60px;
        height: 60px;
    }
    
    .service-icon i {
        font-size: 30px;
    }
    
    .emergency-phone {
        font-size: 20px;
    }
}

@media (max-width: 576px) {
    .about-service-section {
        padding: 40px 0;
    }
    
    .service-content-wrapper {
        padding: 20px;
    }
    
    .service-title {
        font-size: 24px;
    }
    
    .service-btn {
        padding: 12px 25px;
        font-size: 14px;
    }
}
</style>