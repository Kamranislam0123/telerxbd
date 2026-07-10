    <!-- Footer -->
    <footer class="footer-twelve">
        <div class="footer-top-sec">
            <div class="container">
                <div class="footer-top">
                    <div class="logo-footer">
                        <img src="assets/img/logo-03.png" alt="Img">
                    </div>
                    <div class="footer-buy-btn">
                        <h6>Get started with TeleRx Bangladesh?</h6>
                        <a href="doctors" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="footer-middle">
                    <div class="row">
                        <div class="col-lg-3 col-md-5 col-sm-6">
                            <div class="footer-links-middle">
                                <h4>Contact Us</h4>
                                <ul>
                                    <li><span><i class="feather-map-pin"></i></span>Badda, Dhaka, Bangladesh</li>
                                    <li><span><i class="feather-phone"></i></span>+880 1836 838888</li>
                                    <li><span><i class="feather-mail"></i></span>care@telerxbd.com</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="footer-links-middle">
                                <h4>TeleRx</h4>
                                <ul>
                                    <li><a href="about-us">About Us</a></li>
                                    <li><a href="blog">Blogs</a></li>
                                    <li><a href="javascript:void(0);">Careers</a></li>
                                    <li><a href="javascript:void(0);">Success Stories</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="footer-links-middle">
                                <h4>Specialities</h4>
                                <ul>
                                    <li><a href="javascript:void(0);">Pregnancy Test</a></li>
                                    <li><a href="javascript:void(0);">Vitamin D & B12 Combo</a></li>
                                    <li><a href="javascript:void(0);">Cancer Screening - Male</a></li>
                                    <li><a href="javascript:void(0);">Advanced Renal Package</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="footer-links-middle">
                                <h4>Our Activities on Social Media</h4>
                                <ul class="social-icons">
                                    <li><a href="https://www.facebook.com/telerxbd" class="social-media"  target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
                                    <li><a href="https://www.youtube.com/@TeleRxBD" class="social-media"  target="_blank"><i class="fa-brands fa-youtube"></i></a></li>
                                    <li><a href="https://www.instagram.com/telerxbd" class="social-media"  target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
                                    <li><a href="https://bd.linkedin.com/company/telerxbd" class="social-media"  target="_blank"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- /Footer -->

    <!-- Footer -->
    <footer class="footer footer-thirteen">
         <div class="footer-bottom">
            <div class="container">
                <!-- Copyright -->
                <div class="copyright">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="copyright-text">
                                <p class="mb-0">Copyright &copy; <script>document.write(new Date().getFullYear());</script> TeleRx Bangladesh | All Rights Reserved</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Copyright -->
            </div>
        </div>
    </footer>
    <!-- /Footer -->

    <!-- ScrollToTop -->
    <div class="progress-wrap active-progress">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;"></path>
        </svg>
    </div>
    <!-- /ScrollToTop -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Bundle JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Feather Icon JS -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Datepicker JS -->
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <!-- select JS -->
    <script src="assets/plugins/select2/js/select2.min.js"></script>

    <!-- Owl Carousel JS -->
    <script src="assets/js/owl.carousel.min.js"></script>

    <!-- Slick JS -->
    <script src="assets/js/slick.js"></script>

    <!-- Animation JS -->
    <script src="assets/js/aos.js"></script>

    <!-- Counter JS -->
    <script src="assets/js/counter.js"></script>

    <!-- BacktoTop JS -->
    <script src="assets/js/backToTop.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

    <!-- Sticky Sidebar JS -->
    <script src="assets/plugins/theia-sticky-sidebar/ResizeSensor.js"></script>
    <script src="assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js"></script>

    <!-- Apexchart JS -->
    <script src="assets/plugins/apex/apexcharts.min.js"></script>
    <script src="assets/plugins/apex/chart-data.js"></script>
    
    <!-- Circle Progress JS -->
    <script src="assets/js/circle-progress.min.js"></script>

    <?php if (isset($current_page) && $current_page === 'doctors.php'): ?>
    <!-- Doctors page: Specialities filter (runs after jQuery) -->
    <script src="assets/js/doctors-filter.js"></script>
    <?php endif; ?>

    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && ($_SESSION['user_type'] === 'patient' || $_SESSION['user_type'] === 'healthcare' || $_SESSION['user_type'] === 'special_tid') && basename($_SERVER['PHP_SELF']) !== 'video-call.php'): ?>
    <!-- Incoming Call Overlay & Card -->
    <div id="incoming-call-container" style="display: none;">
        <div class="call-overlay"></div>
        <div class="call-card-wrapper">
            <div class="call-card">
                <div class="call-avatar-container">
                    <div class="pulse-ring ring-1"></div>
                    <div class="pulse-ring ring-2"></div>
                    <img id="caller-avatar" src="assets/img/doctors-dashboard/doctor-profile-img.jpg" alt="Doctor" class="caller-image">
                </div>
                <h3 class="call-title" id="caller-name">Doctor</h3>
                <p class="call-subtitle">Incoming video consultation call...</p>
                <div id="call-patient-info" class="call-patient-name" style="display: none;">
                    For Patient: <strong id="call-patient-name-val"></strong>
                </div>
                <div class="call-actions">
                    <button id="decline-call-btn" class="call-btn decline-btn" title="Decline Call">
                        <i class="fa-solid fa-phone-slash me-1"></i> Decline
                    </button>
                    <button id="accept-call-btn" class="call-btn accept-btn" title="Accept Call">
                        <i class="fa-solid fa-video me-1"></i> Accept
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Glassmorphic Calling UI Styling -->
    <style>
    #incoming-call-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Poppins', 'Inter', sans-serif;
    }

    .call-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: all 0.5s ease;
    }

    .call-card-wrapper {
        position: relative;
        z-index: 10;
        animation: callCardSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes callCardSlideIn {
        0% {
            transform: scale(0.8) translateY(100px);
            opacity: 0;
        }
        100% {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }

    .call-card {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15), 
                    inset 0 0 0 1px rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 24px;
        padding: 40px;
        width: 380px;
        text-align: center;
        color: #0f172a;
    }

    .call-avatar-container {
        position: relative;
        width: 110px;
        height: 110px;
        margin: 0 auto 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .caller-image {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.1);
        z-index: 3;
    }

    .pulse-ring {
        position: absolute;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: rgba(34, 197, 94, 0.3);
        z-index: 1;
        animation: ringPulse 2s infinite ease-out;
    }

    .ring-2 {
        animation-delay: 0.6s;
    }

    @keyframes ringPulse {
        0% {
            transform: scale(1);
            opacity: 0.8;
        }
        100% {
            transform: scale(1.6);
            opacity: 0;
        }
    }

    .call-title {
        font-size: 1.45rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: #1e293b;
        letter-spacing: -0.5px;
    }

    .call-subtitle {
        font-size: 0.95rem;
        color: #64748b;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .call-patient-name {
        font-size: 0.85rem;
        background: rgba(30, 41, 59, 0.05);
        padding: 6px 12px;
        border-radius: 30px;
        display: inline-block;
        color: #475569;
        margin-bottom: 24px;
    }

    .call-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
    }

    .call-btn {
        flex: 1;
        padding: 12px 20px;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }

    .decline-btn {
        background: #ef4444;
        color: #fff;
    }

    .decline-btn:hover {
        background: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.3);
    }

    .decline-btn:active {
        transform: translateY(0);
    }

    .accept-btn {
        background: #22c55e;
        color: #fff;
        animation: acceptPulse 2s infinite;
    }

    .accept-btn:hover {
        background: #16a34a;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(34, 197, 94, 0.3);
    }

    .accept-btn:active {
        transform: translateY(0);
    }

    @keyframes acceptPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
    }
    </style>

    <!-- Real-time Polling & Audio Synthesizer Script -->
    <script>
    $(document).ready(function() {
        let callPollInterval = null;
        let ringtoneInterval = null;
        let audioCtx = null;
        let activeAppointmentId = null;
        let isRinging = false;

        function playRingtone() {
            if (audioCtx) return;
            try {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                let playTone = () => {
                    if (!isRinging || !audioCtx) return;
                    
                    let osc1 = audioCtx.createOscillator();
                    let osc2 = audioCtx.createOscillator();
                    let gainNode = audioCtx.createGain();
                    
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(480, audioCtx.currentTime); // Standard ringback tone 480Hz
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(440, audioCtx.currentTime); // 440Hz
                    
                    gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
                    gainNode.gain.linearRampToValueAtTime(0.15, audioCtx.currentTime + 0.1);
                    gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.8);
                    
                    osc1.connect(gainNode);
                    osc2.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    
                    osc1.start();
                    osc2.start();
                    
                    osc1.stop(audioCtx.currentTime + 1.8);
                    osc2.stop(audioCtx.currentTime + 1.8);
                };
                
                isRinging = true;
                playTone();
                ringtoneInterval = setInterval(playTone, 2200);
            } catch (e) {
                console.error("AudioContext ringtone error:", e);
            }
        }

        function stopRingtone() {
            isRinging = false;
            if (ringtoneInterval) {
                clearInterval(ringtoneInterval);
                ringtoneInterval = null;
            }
            if (audioCtx) {
                try {
                    audioCtx.close();
                } catch(e){}
                audioCtx = null;
            }
        }

        function checkCalls() {
            $.ajax({
                url: '<?php echo (defined('APP_BASE') ? APP_BASE : ''); ?>/php/check-incoming-calls.php',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.call) {
                        const call = res.call;
                        activeAppointmentId = call.appointment_id;
                        
                        // Update UI fields
                        $('#caller-name').text(call.doctor_name);
                        
                        // Handle absolute paths for doctor avatar if required
                        let avatarSrc = call.doctor_image;
                        if (avatarSrc.indexOf('http') !== 0 && avatarSrc.indexOf('/') !== 0) {
                            avatarSrc = '<?php echo (defined('APP_BASE') ? APP_BASE : ''); ?>/' + avatarSrc;
                        }
                        $('#caller-avatar').attr('src', avatarSrc);
                        
                        if (call.patient_name) {
                            $('#call-patient-name-val').text(call.patient_name);
                            $('#call-patient-info').show();
                        } else {
                            $('#call-patient-info').hide();
                        }
                        
                        // Display calling overlay if not visible
                        if ($('#incoming-call-container').is(':hidden')) {
                            $('#incoming-call-container').fadeIn(300);
                            playRingtone();
                        }
                    } else {
                        // Dismiss calling overlay if active call was cancelled or completed
                        if ($('#incoming-call-container').is(':visible')) {
                            $('#incoming-call-container').fadeOut(300);
                            stopRingtone();
                            activeAppointmentId = null;
                        }
                    }
                },
                error: function() {
                    // Suppress check errors to keep general browsing smooth
                }
            });
        }

        // Accept Call - Redirects to Video Room
        $('#accept-call-btn').click(function() {
            if (!activeAppointmentId) return;
            stopRingtone();
            
            // Build absolute URL for video room
            window.location.href = '<?php echo (defined('APP_BASE') ? APP_BASE : ''); ?>/video-call.php?appointment_id=' + activeAppointmentId;
        });

        // Decline Call - Clear DB state and close overlay
        $('#decline-call-btn').click(function() {
            if (!activeAppointmentId) return;
            
            const btn = $(this);
            btn.prop('disabled', true);
            
            $.ajax({
                url: '<?php echo (defined('APP_BASE') ? APP_BASE : ''); ?>/php/handle-call-status.php',
                type: 'POST',
                data: {
                    appointment_id: activeAppointmentId,
                    action: 'decline_call'
                },
                dataType: 'json',
                success: function() {
                    $('#incoming-call-container').fadeOut(300);
                    stopRingtone();
                    activeAppointmentId = null;
                    btn.prop('disabled', false);
                },
                error: function() {
                    $('#incoming-call-container').fadeOut(300);
                    stopRingtone();
                    activeAppointmentId = null;
                    btn.prop('disabled', false);
                }
            });
        });

        // Start background polling (every 4 seconds)
        callPollInterval = setInterval(checkCalls, 4000);
        
        // Run initial check after 1 second
        setTimeout(checkCalls, 1000);
    });
    </script>
    <?php endif; ?>

</body>
</html>