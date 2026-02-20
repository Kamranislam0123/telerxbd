<?php
session_start();
include 'header.php';

// Dynamic counters
$total_patient = 1500;
$total_partners = 12;
$total_doctors = 35;
$total_districts = 21;

// Bank and bKash details
$bank_name = "Eastern Bank PLC";
$bank_account = "1071450005619";
$account_name = "MD MEHEDI HASSAN";
$bkash_number = "01933-890894 (Personal)";
?>
<head>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            overflow-x: hidden;
        }
        
        /* Hero Section with Background */
        .welfare-hero {
            position: relative;
            min-height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            overflow: hidden;
            padding: 100px 0;
        }
        
        /* Background Image */
        .welfare-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/img/bg/donation.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            z-index: -2;
        }
        
        /* Dark Overlay */
        .welfare-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.6) 100%);
            z-index: -1;
        }
        
        /* Content Wrapper */
        .welfare-content {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Title Styles */
        .welfare-title {
            font-size: 4.5rem;
            font-weight: 800;
            margin: 50px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: fadeInDown 1s ease;
        }
        
        .welfare-title span {
            color: #ff6b6b;
            display: inline-block;
            position: relative;
        }
        
        .welfare-title span::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
            border-radius: 2px;
        }
        
        .welfare-tagline {
            font-size: 2.5rem;
            margin-bottom: 60px;
            opacity: 0.9;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 1s ease 0.2s both;
        }
        
        /* Counter Section */
        .counter-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            background: none;
            animation: fadeInUp 1s ease 0.4s both;
        }
        
        /* Counter Card */
        .counter-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .counter-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .counter-card:hover::before {
            left: 100%;
        }
        
        .counter-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.15);
        }
        
        /* Counter Icon */
        .counter-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #fff;
            transition: all 0.3s ease;
        }
        
        .counter-card:hover .counter-icon {
            background: #ff6b6b;
            color: white;
            transform: rotateY(180deg);
        }
        
        /* Counter Number */
        .counter-number {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            color: #fff;
            line-height: 1.2;
        }
        
        .counter-number span {
            font-size: 2rem;
            opacity: 0.8;
        }
        
        /* Counter Label */
        .counter-label {
            font-size: 1.2rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Counter Description */
        .counter-desc {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 10px;
            font-style: italic;
        }
        
        /* Donate Button */
        .donate-btn {
            margin-top: 60px;
            animation: fadeInUp 1s ease 0.6s both;
        }
        
        .btn-donate {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 18px 50px;
            font-size: 1.3rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-donate::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }
        
        .btn-donate:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-donate:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 107, 107, 0.4);
            color: white;
        }
        
        .btn-donate i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .btn-donate:hover i {
            transform: translateX(5px);
        }
        
        /* Payment Details Section - Hidden by default */
        .payment-details {
            max-width: 600px;
            margin: 30px auto 0;
            display: none;
            animation: slideDown 0.4s ease forwards;
        }
        
        .payment-details.show {
            display: block;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Payment Item */
        /* Payment Card */
        .payment-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .payment-card:last-child {
            margin-bottom: 0;
        }

        /* Payment Card Header */
        .payment-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f5;
        }

        .payment-card-icon {
            width: 45px;
            height: 45px;
            background: #ff6b6b;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
        }

        .payment-card-title {
            font-size: 18px;
            font-weight: 600;
            color: #212529;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }

        /* Payment Row */
        .payment-row {
            display: flex;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px dashed #e9ecef;
        }

        .payment-row:last-child {
            border-bottom: none;
        }

        .payment-row-label {
            font-size: 16px;
            font-weight: 500;
            color: #6c757d;
            font-family: 'Inter', sans-serif;
            min-width: 130px;  /* একটু বড় করুন */
            text-align: left;   /* বামে align নিশ্চিত করুন */
        }

        .payment-row-value {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            justify-content: flex-end;
        }

        .payment-row-value span {
            font-size: 16px;
            font-weight: 500;
            color: #212529;
            font-family: 'Poppins', sans-serif;
            word-break: break-word;
            text-align: right;
        }
        
        /* Copy Button */
        .copy-btn {
            background: transparent;
            border: 1px solid #dee2e6;
            color: #6c757d;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            flex-shrink: 0;
        }

        .copy-btn:hover {
            background: #ff6b6b;
            border-color: #ff6b6b;
            color: white;
            transform: scale(1.05);
        }

        /* Copy notification */
        .copy-notification {
            position: fixed;
            top: 100px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            font-size: 14px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transform: translateX(120%);
            transition: transform 0.3s ease;
            z-index: 9999;
            font-family: 'Inter', sans-serif;
        }

        .copy-notification.show {
            transform: translateX(0);
        }
                
                /* Floating Hearts Animation */
                .floating-heart {
                    position: absolute;
                    color: rgba(255, 107, 107, 0.3);
                    font-size: 1rem;
                    pointer-events: none;
                    z-index: -1;
                }
                
                @keyframes float {
                    0% {
                        transform: translateY(0) rotate(0deg);
                        opacity: 1;
                    }
                    100% {
                        transform: translateY(-100vh) rotate(360deg);
                        opacity: 0;
                    }
                }
                
                /* Copy notification */
                .copy-notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #ff6b6b;
                    color: white;
                    padding: 12px 25px;
                    border-radius: 50px;
                    font-size: 0.9rem;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                    transform: translateX(120%);
                    transition: transform 0.3s ease;
                    z-index: 9999;
                }
                
                .copy-notification.show {
                    transform: translateX(0);
                }
        
        /* Responsive Design */
        @media (max-width: 576px) {
            .payment-card {
                padding: 20px;
            }
            
            .payment-row {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }
            
            .payment-row-label {
                min-width: auto;
            }
            
            .payment-row-value {
                margin-left: 0;    
                width: 100%;
                justify-content: space-between;
            }
            
            .payment-row-value span {
                text-align: left;
            }
        }
        
        @media (max-width: 768px) {
            .welfare-title {
                font-size: 2.8rem;
            }
            
            .welfare-tagline {
                font-size: 1.2rem;
            }
            
            .counter-number {
                font-size: 2.5rem;
            }
            
            .btn-donate {
                padding: 15px 40px;
                font-size: 1.1rem;
            }
            
            .payment-item {
                flex-direction: column;
                text-align: center;
            }
            
            .payment-info {
                text-align: center;
            }
            
            .payment-value {
                flex-direction: column;
                gap: 10px;
            }
        }
        
        @media (max-width: 480px) {
            .welfare-title {
                font-size: 2rem;
            }
            
            .counter-section {
                grid-template-columns: 1fr;
            }
            
            .counter-card {
                padding: 30px 15px;
            }
        }
        
        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Pulse Animation for Numbers */
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
        
        .counter-number {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <!-- Copy Notification -->
    <div class="copy-notification" id="copyNotification">
        <i class="fas fa-check-circle me-2"></i>
        Copied to clipboard!
    </div>

    <!-- Hero Section with Background -->
    <section class="welfare-hero">
        <!-- Background Image -->
        <div class="welfare-bg" style="background-image: url('assets/img/bg/donation.jpg');"></div>
        <div class="welfare-overlay"></div>
        
        <!-- Floating Hearts (Decoration) -->
        <div class="floating-heart" style="top: 10%; left: 5%;">❤️</div>
        <div class="floating-heart" style="top: 30%; right: 8%;">❤️</div>
        <div class="floating-heart" style="bottom: 20%; left: 10%;">❤️</div>
        <div class="floating-heart" style="bottom: 40%; right: 15%;">❤️</div>
        
        <div class="welfare-content">
            <!-- Title -->
            <h1 class="welfare-title">
                <span>Donate</span> For Humanities
            </h1>
            
            <!-- Tagline -->
            <p class="welfare-tagline">
                <i class="fas fa-quote-left me-2" style="opacity: 0.5;"></i>
                Donation for rural people and help them to survive.
                <i class="fas fa-quote-right ms-2" style="opacity: 0.5;"></i>
            </p>
            <!-- Donate Button -->
            <div class="donate-btn">
                <button class="btn-donate" id="donateBtn">
                    <i class="fas fa-heart"></i>
                    Donate Now
                    <i class="fas fa-chevron-down" id="arrowIcon"></i>
                </button>
            </div>
            
            <!-- Payment Details (Hidden by default) -->
            <div class="payment-details" id="paymentDetails">
                <!-- Bank Details Card -->
                <div class="payment-card">
                    <div class="payment-card-header">
                        <div class="payment-card-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <h5 class="payment-card-title">Bank Details</h5>
                    </div>
                    
                    <!-- Bank Name -->
                    <div class="payment-row">
                        <div class="payment-row-label">Bank Name:</div>
                        <div class="payment-row-value">
                            <span id="bankName"><?php echo $bank_name; ?></span>
                            <button class="copy-btn" onclick="copyToClipboard('bankName', 'Bank name')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Account Number -->
                    <div class="payment-row">
                        <div class="payment-row-label">Account Number:</div>
                        <div class="payment-row-value">
                            <span id="bankAccount"><?php echo $bank_account; ?></span>
                            <button class="copy-btn" onclick="copyToClipboard('bankAccount', 'Account number')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Account Name -->
                    <div class="payment-row">
                        <div class="payment-row-label">Account Name:</div>
                        <div class="payment-row-value">
                            <span id="accountName"><?php echo $account_name; ?></span>
                            <button class="copy-btn" onclick="copyToClipboard('accountName', 'Account name')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- bKash Details Card (আলাদা কার্ড) -->
                <div class="payment-card">
                    <div class="payment-card-header">
                        <div class="payment-card-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5 class="payment-card-title">bKash Payment</h5>
                    </div>
                    
                    <!-- bKash Number -->
                    <div class="payment-row">
                        <div class="payment-row-label">bKash (Personal):</div>
                        <div class="payment-row-value">
                            <span id="bkashNumber"><?php echo $bkash_number; ?></span>
                            <button class="copy-btn" onclick="copyToClipboard('bkashNumber', 'bKash number')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Counter Section -->
            <div class="counter-section">
                <!-- Total Customer Card -->
                <div class="counter-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="counter-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="counter-number">
                        <?php echo $total_patient; ?><span>+</span>
                    </div>
                    <div class="counter-label">Total Patients</div>
                    <div class="counter-desc">Happy patients & families</div>
                </div>
                
                <!-- Partners Card -->
                <div class="counter-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="counter-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="counter-number">
                        <?php echo $total_partners; ?><span>+</span>
                    </div>
                    <div class="counter-label">Partners</div>
                    <div class="counter-desc">NGOs & healthcare providers</div>
                </div>
                
                <!-- Doctors Card -->
                <div class="counter-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="counter-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="counter-number">
                        <?php echo $total_doctors; ?><span>+</span>
                    </div>
                    <div class="counter-label">Doctors</div>
                    <div class="counter-desc">Specialist & general physicians</div>
                </div>
                
                <!-- District Card -->
                <div class="counter-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="counter-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="counter-number">
                        <?php echo $total_districts; ?><span>+</span>
                    </div>
                    <div class="counter-label">District</div>
                    <div class="counter-desc">Across Bangladesh</div>
                </div>
            </div>
            
            
        </div>
    </section>

    <!-- Scripts -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation JS -->
    <script src="assets/js/aos.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 1000,
                    once: true,
                    offset: 100
                });
            }
            
            // Floating hearts animation
            function createHeart() {
                const heart = document.createElement('div');
                heart.classList.add('floating-heart');
                heart.innerHTML = '❤️';
                heart.style.left = Math.random() * 100 + '%';
                heart.style.top = '100%';
                heart.style.fontSize = (Math.random() * 2 + 0.5) + 'rem';
                heart.style.opacity = '0.3';
                heart.style.animation = 'float ' + (Math.random() * 5 + 5) + 's linear infinite';
                document.querySelector('.welfare-hero').appendChild(heart);
                
                setTimeout(() => {
                    heart.remove();
                }, 10000);
            }
            
            // Create hearts every 2 seconds
            setInterval(createHeart, 2000);
            
            // Counter animation on scroll
            function animateCounter(element, start, end, duration) {
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    element.innerHTML = Math.floor(progress * (end - start) + start) + '<span>+</span>';
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }
            
            // Animate numbers when they come into view
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counterNumber = entry.target.querySelector('.counter-number');
                        const originalText = counterNumber.innerText;
                        const endValue = parseInt(originalText.replace('+', ''));
                        counterNumber.innerHTML = '0<span>+</span>';
                        animateCounter(counterNumber, 0, endValue, 2000);
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.counter-card').forEach(card => {
                observer.observe(card);
            });
            
            // Donate button click handler
            $('#donateBtn').on('click', function() {
                $('#paymentDetails').slideToggle(300);
                $('#arrowIcon').toggleClass('fa-chevron-down fa-chevron-up');
                
                // Smooth scroll to payment details if visible
                if ($('#paymentDetails').is(':visible')) {
                    $('html, body').animate({
                        scrollTop: $('#paymentDetails').offset().top - 100
                    }, 500);
                }
            });
        });
        
        // Copy to clipboard function
        function copyToClipboard(elementId, type) {
            var text = document.getElementById(elementId).innerText;
            
            // Create temporary input element
            var tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            
            // Select and copy
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile
            document.execCommand('copy');
            
            // Remove temporary input
            document.body.removeChild(tempInput);
            
            // Show notification
            var notification = document.getElementById('copyNotification');
            notification.classList.add('show');
            
            // Update notification text
            notification.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + type + ' copied to clipboard!';
            
            // Hide notification after 2 seconds
            setTimeout(function() {
                notification.classList.remove('show');
            }, 2000);
        }
    </script>
</body>
<?php include 'footer.php'; ?>