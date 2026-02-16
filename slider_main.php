<?php
/**
 * Creative Slider - TeleRx Bangladesh
 * Include this file anywhere to show a 4-slide slider with image left, title+description right.
 */

// Slide data array - এখানে আপনার ছবির পাথ ও কন্টেন্ট দিন
$slides = [
        [
                'image' => 'assets/img/slider-home/slide1.jpg', // আপনার ইমেজ পাথ দিন
                'title' => 'Welfare Project',
                'description' => 'TeleRx Bangladesh provides free medical treatment, medicines, and health camps for underprivileged communities. We ensure healthcare reaches those who need it most.'
        ],
        [
                'image' => 'assets/img/slider-home/slide2.jpg',
                'title' => 'Global Care',
                'description' => 'Planning treatment abroad? TeleRx Bangladesh offers complete guidance – from hospital selection to visa support and travel arrangements. Safe, smooth, and stress-free.'
        ],
        [
                'image' => 'assets/img/slider-home/slide3.jpg',
                'title' => 'Old-age Care',
                'description' => 'Professional nursing care for the elderly – either at home or in partner old-age homes. Health monitoring, medication, and emotional support by TeleRx Bangladesh.'
        ],
        [
                'image' => 'assets/img/slider-home/slide4.jpg',
                'title' => 'Emergency Health Support',
                'description' => 'We take care our patient not with medicine but also with heart.'
        ]
];
?>
<section class="slider-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 aos" data-aos="fade-up">
                <div class="slider-wrapper">
                    <div class="slider-container" id="sliderContainer">
                        <?php foreach ($slides as $index => $slide): ?>
                            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                <div class="slide-image">
                                    <img src="<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                                </div>
                                <div class="slide-content">
                                    <h2 class="slide-title"><?php echo htmlspecialchars($slide['title']); ?></h2>
                                    <p class="slide-description"><?php echo htmlspecialchars($slide['description']); ?></p>
                                    <a href="about-us.php" class="slide-btn">Know More<i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Navigation Buttons -->
                    <button class="slider-btn prev-btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                    <button class="slider-btn next-btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>

                    <!-- Dots -->
                    <div class="slider-dots" id="sliderDots">
                        <?php foreach ($slides as $index => $slide): ?>
                            <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- স্লাইডারের সিএসএস -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .slider-section {
        padding: 30px 0;
        background: color (FFFFFF 0%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .slider-wrapper {
        position: relative;
        background: white;
        border-radius: 40px;
        overflow: hidden;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .slider-container {
        display: flex;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        will-change: transform;
    }

    .slide {
        flex: 0 0 100%;
        display: flex;
        align-items: center;
        padding: 40px;
        gap: 40px;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        transition: opacity 0.5s ease;
    }

    .slide.active {
        opacity: 1;
    }

    .slide-image {
        flex: 1;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.2);
        transform: rotate(2deg) scale(0.98);
        transition: transform 0.3s ease;
    }

    .slide:hover .slide-image {
        transform: rotate(0deg) scale(1);
    }

    .slide-image img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.5s ease;
    }

    .slide:hover .slide-image img {
        transform: scale(1.05);
    }

    .slide-content {
        flex: 1;
        padding: 20px;
    }

    .slide-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: #15558d;
        line-height: 1.2;
        position: relative;
        animation: slideInRight 0.7s ease;
    }

    .slide-title::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 0;
        width: 100px;
        height: 3px;
        background: linear-gradient(90deg, #15558d, #0c77c9);
        border-radius: 2px;
    }

    .slide-description {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #4a5568;
        margin: 25px 0 25px;
        animation: slideInRight 0.9s ease;
    }

    .slide-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 30px;
        background: linear-gradient(90deg, #15558d, #0c77c9);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 10px 20px -5px rgba(0, 123, 255, 0.4);
        transition: all 0.3s ease;
        animation: slideInRight 1.1s ease;
        border: none;
        cursor: pointer;
    }

    .slide-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px -5px rgba(0, 123, 255, 0.6);
    }

    .slide-btn i {
        transition: transform 0.3s ease;
    }

    .slide-btn:hover i {
        transform: translateX(5px);
    }

    /* Navigation Buttons */
    .slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        background: white;
        border: none;
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #333;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .slider-btn:hover {
        background: #15558d;
        color: white;
        box-shadow: 0 8px 20px rgba(0, 123, 255, 0.4);
    }

    .prev-btn {
        left: 20px;
    }

    .next-btn {
        right: 20px;
    }

    /* Dots */
    .slider-dots {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 12px;
        z-index: 10;
    }

    .dot {
        width: 12px;
        height: 12px;
        background: rgba(12, 119, 201, 0.2);
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .dot.active {
        background: #15558d;
        transform: scale(1.3);
        border-color: white;
    }

    /* Animations */
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .slide {
            flex-direction: column;
            padding: 20px;
            gap: 20px;
        }

        .slide-image {
            width: 100%;
            transform: rotate(0deg);
        }

        .slide-title {
            font-size: 1.8rem;
        }

        .slide-description {
            font-size: 1rem;
        }

        .slider-btn {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .prev-btn {
            left: 10px;
        }

        .next-btn {
            right: 10px;
        }
    }
</style>

<!-- স্লাইডারের জাভাস্ক্রিপ্ট -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('sliderContainer');
        const slides = document.querySelectorAll('.slide');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const dots = document.querySelectorAll('.dot');
        let currentIndex = 0;
        const totalSlides = slides.length;

        function showSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;

            container.style.transform = `translateX(-${index * 100}%)`;

            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });

            currentIndex = index;
        }

        prevBtn.addEventListener('click', () => {
            showSlide(currentIndex - 1);
        });

        nextBtn.addEventListener('click', () => {
            showSlide(currentIndex + 1);
        });

        dots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                const index = parseInt(e.target.dataset.index);
                showSlide(index);
            });
        });

        // অটোপ্লে চালু করতে চাইলে নিচের লাইন আনকমেন্ট করুন
        setInterval(() => {
             showSlide(currentIndex + 1);
        }, 5000);

        showSlide(0);
    });
</script>
<!-- স্লাইডার শেষ -->