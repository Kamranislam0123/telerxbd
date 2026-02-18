<?php
session_start();
include 'header.php';
?>
<!-- Home Banner -->
<section class="banner-section banner-sec-one">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="banner-content aos" data-aos="fade-up">
                    <div class="rating-appointment d-inline-flex align-items-center gap-2">
                        <div class="avatar-list-stacked avatar-group-lg">
                            <span class="avatar avatar-rounded">
                                <img class="border border-white" src="assets/img/doctors/doctor-thumb-22.jpg" alt="img">
                            </span>
                            <span class="avatar avatar-rounded">
                                <img class="border border-white" src="assets/img/doctors/doctor-thumb-23.jpg" alt="img">
                            </span>
                            <span class="avatar avatar-rounded">
                                <img class="border border-white" src="assets/img/doctors/doctor-thumb-24.jpg" alt="img">
                            </span>
                        </div>
                        <div class="me-2">
                            <h6 class="mb-1">2K+ Appointments</h6>
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-star text-orange me-1"></i>
                                    <i class="fa-solid fa-star text-orange me-1"></i>
                                    <i class="fa-solid fa-star text-orange me-1"></i>
                                    <i class="fa-solid fa-star text-orange me-1"></i>
                                    <i class="fa-solid fa-star text-orange me-1"></i>
                                </div>
                                <p>4.8 Ratings</p>
                            </div>
                        </div>
                    </div>
                    <h1 class="display-5">Discover Health: Find Your Trusted <span class="banner-icon"><img src="assets/img/icons/video.svg" alt="img"></span> <span class="text-gradient">Doctors</span> Today</h1>
                    <div class="search-box-one aos" data-aos="fade-up">
                        <form action="doctors.php">
                            <div class="search-input search-line">
                                <i class="isax isax-hospital5 bficon"></i>
                                <div class=" mb-0">
                                    <input type="text" class="form-control" placeholder="Search doctors, clinics, hospitals, etc">
                                </div>
                            </div>
                            <div class="search-input search-map-line">
                                <i class="isax isax-location5"></i>
                                <div class=" mb-0">
                                    <input type="text" class="form-control" placeholder="Location">
                                </div>
                            </div>
                            <div class="search-input search-calendar-line">
                                <i class="isax isax-calendar-tick5"></i>
                                <div class=" mb-0">
                                    <input type="text" class="form-control datetimepicker" placeholder="Date">
                                </div>
                            </div>
                            <div class="form-search-btn">
                                <button class="btn btn-primary" type="submit"><i class="isax isax-search-normal5 me-2"></i>Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="banner-img aos" data-aos="fade-up">
                    <img src="assets/img/banner/banner-doctor.webp" class="img-fluid" alt="patient-image">
                    <div class="banner-appointment">
                        <h6>1K</h6>
                        <p>Appointments <span class="d-block">Completed</span></p>
                    </div>
                    <div class="banner-patient">
                        <div class="avatar-list-stacked avatar-group-sm">
                            <span class="avatar avatar-rounded">
                                <img src="assets/img/patients/patient19.jpg" alt="img">
                            </span>
                            <span class="avatar avatar-rounded">
                                <img  src="assets/img/patients/patient16.jpg" alt="img">
                            </span>
                            <span class="avatar avatar-rounded">
                                <img src="assets/img/patients/patient18.jpg" alt="img">
                            </span>
                        </div>
                        <p>1K+</p>
                        <p>Satisfied Patients</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="banner-bg">
        <img src="assets/img/bg/banner-bg-02.png" alt="img" class="banner-bg-01">
        <img src="assets/img/bg/banner-bg-03.png" alt="img" class="banner-bg-02">
        <img src="assets/img/bg/banner-bg-04.png" alt="img" class="banner-bg-03">
        <img src="assets/img/bg/banner-bg-05.png" alt="img" class="banner-bg-04">
    </div>
</section>
<!-- /Home Banner -->

<!-- Doctors Section -->
<section class="doctors-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 aos" data-aos="fade-up">
                <div class="section-header-one section-header-slider text-center">
                    <h2 class="section-title">Find Best Doctors</h2>
                </div>
            </div>
        </div>
        <div class="owl-carousel doctor-slider-one owl-theme aos" data-aos="fade-up">

            <!-- Doctor Item -->
            <div class="item">
                <div class="doctor-profile-widget doc-item">
                    <div class="doc-pro-img">
                        <a href="doctor-profile.php">
                            <div class="doctor-profile-img">
                                <img src="assets/img/doctors/doctor-03.jpg" class="img-fluid" alt="Ruby Perrin">
                            </div>
                        </a>
                        <div class="doctor-amount">
                            <span>$200</span>
                        </div>
                    </div>
                    <div class="doc-content">
                        <div class="doc-pro-info">
                            <div class="doc-pro-name">
                                <a href="doctor-profile.php">Dr. Downer</a>
                                <p>Orthopedic</p>
                            </div>
                            <div class="reviews-ratings">
                                <p>
                                    <span><i class="fas fa-star"></i> 4.5</span> (35)
                                </p>
                            </div>
                        </div>
                        <div class="doc-pro-location">
                            <p><i class="isax isax-location"></i> Newyork, USA</p>
                            <span class="badge badge-success doc-badge"><i class="fa-solid fa-circle"></i>Available</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Doctor Item -->

            <!-- Doctor Item -->
            <div class="item">
                <div class="doctor-profile-widget doc-item">
                    <div class="doc-pro-img">
                        <a href="doctor-profile.php">
                            <div class="doctor-profile-img">
                                <img src="assets/img/doctors/doctor-02.jpg" class="img-fluid" alt="Paul Richard">
                            </div>
                        </a>
                        <div class="doctor-amount">
                            <span>$300</span>
                        </div>
                    </div>
                    <div class="doc-content">
                        <div class="doc-pro-info">
                            <div class="doc-pro-name">
                                <a href="doctor-profile.php">Dr. John Doe</a>
                                <p>Dentist</p>
                            </div>
                            <div class="reviews-ratings">
                                <p>
                                    <span><i class="fas fa-star"></i> 4.3</span> (45)
                                </p>
                            </div>
                        </div>
                        <div class="doc-pro-location">
                            <p><i class="isax isax-location"></i> Austin, TX</p>
                            <span class="badge badge-success doc-badge"><i class="fa-solid fa-circle"></i>Available</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Doctor Item -->

            <!-- Doctor Item -->
            <div class="item">
                <div class="doctor-profile-widget doc-item">
                    <div class="doc-pro-img">
                        <a href="doctor-profile.php">
                            <div class="doctor-profile-img">
                                <img src="assets/img/doctors/doctor-04.jpg" class="img-fluid" alt="Darren Elder">
                            </div>
                        </a>
                        <div class="doctor-amount">
                            <span>$100</span>
                        </div>
                    </div>
                    <div class="doc-content">
                        <div class="doc-pro-info">
                            <div class="doc-pro-name">
                                <a href="doctor-profile.php">Dr. Aviles</a>
                                <p>Neurology</p>
                            </div>
                            <div class="reviews-ratings">
                                <p>
                                    <span><i class="fas fa-star"></i> 4.0</span> (20)
                                </p>
                            </div>
                        </div>
                        <div class="doc-pro-location">
                            <p><i class="isax isax-location"></i> Newyork, USA</p>
                            <span class="badge badge-danger doc-badge"><i class="fa-solid fa-circle"></i>Unavailable</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Doctor Item -->

            <!-- Doctor Item -->
            <div class="item">
                <div class="doctor-profile-widget doc-item">
                    <div class="doc-pro-img">
                        <a href="doctor-profile.php">
                            <div class="doctor-profile-img">
                                <img src="assets/img/doctors/doctor-05.jpg" class="img-fluid" alt="Sofia Brient">
                            </div>
                        </a>
                        <div class="doctor-amount">
                            <span>$250</span>
                        </div>
                    </div>
                    <div class="doc-content">
                        <div class="doc-pro-info">
                            <div class="doc-pro-name">
                                <a href="doctor-profile.php">Dr. Palmore</a>
                                <p>Immunologist</p>
                            </div>
                            <div class="reviews-ratings">
                                <p>
                                    <span><i class="fas fa-star"></i> 4.5</span> (35)
                                </p>
                            </div>
                        </div>
                        <div class="doc-pro-location">
                            <p><i class="isax isax-location"></i> Waipahu, HI</p>
                            <span class="badge badge-success doc-badge"><i class="fa-solid fa-circle"></i>Available</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Doctor Item -->

            <!-- Doctor Item -->
            <div class="item">
                <div class="doctor-profile-widget doc-item">
                    <div class="doc-pro-img">
                        <a href="doctor-profile.php">
                            <div class="doctor-profile-img">
                                <img src="assets/img/doctors/doctor-01.jpg" class="img-fluid" alt="John Doe">
                            </div>
                        </a>
                        <div class="doctor-amount">
                            <span>$880</span>
                        </div>
                    </div>
                    <div class="doc-content">
                        <div class="doc-pro-info">
                            <div class="doc-pro-name">
                                <a href="doctor-profile.php">Dr. Paul Richard</a>
                                <p>Dentist</p>
                            </div>
                            <div class="reviews-ratings">
                                <p>
                                    <span><i class="fas fa-star"></i> 4.4</span> (50)
                                </p>
                            </div>
                        </div>
                        <div class="doc-pro-location">
                            <p><i class="isax isax-location"></i> California, USA</p>
                            <span class="badge badge-success doc-badge"><i class="fa-solid fa-circle"></i>Available</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Doctor Item -->

        </div>
    </div>
</section>
<!-- /Doctors Section -->

<!-- Services Section -->
<section class="services-section aos" data-aos="fade-up">
    <div class="horizontal-slide d-flex" data-direction="right" data-speed="slow">
        <div class="slide-list d-flex gap-4">
            <div class="services-slide">
                <h6><a href="javascript:void(0);">Multi Speciality Treatments & Doctors</a></h6>
            </div>
            <div class="services-slide">
                <h6><a href="javascript:void(0);">Treatment Beyond Distance</a></h6>
            </div>
            <div class="services-slide">
                <h6><a href="javascript:void(0);">Medecines & Supplies</a></h6>
            </div>
            <div class="services-slide">
                <h6><a href="javascript:void(0);">Global Care</a></h6>
            </div>
            <div class="services-slide">
                <h6><a href="javascript:void(0);">Old-age Care</a></h6>
            </div>
            <div class="services-slide">
                <h6><a href="javascript:void(0);">Talk to Doctors</a></h6>
            </div>
            <div class="services-slide">
                <h6><a href="javascript:void(0);">Home Care Services</a></h6>
            </div>
        </div>
    </div>
</section>
<!-- /Services Section -->

<!-- Slider Section -->
<?php include 'slider_main.php'; ?>
<!-- /Slider Section -->

<!-- How TeleRx Works -->
<section class="testimonial-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 aos" data-aos="fade-up">
                <div class="section-header-one section-header-slider text-center">
                    <h2 class="section-title">How TeleRx Works</h2>
                </div>
            </div>
        </div>
        <div class="bookus-sec" data-aos="fade-up">
            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="book-item">
                        <div class="book-icon bg-primary">
                            <i class="isax isax-search-normal5"></i>
                        </div>
                        <div class="book-info">
                            <h6 class="text-black mb-2">Search For Doctors</h6>
                            <p class="fs-14 text-black-50">Search for a doctor based on specialization, location, or availability for your Treatments</p>
                        </div>
                        <div class="way-icon">
                            <img src="assets/img/icons/way-icon.svg" alt="img">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="book-item">
                        <div class="book-icon bg-orange">
                            <i class="isax isax-security-user5"></i>
                        </div>
                        <div class="book-info">
                            <h6 class="text-black mb-2">Check Doctor Profile</h6>
                            <p class="fs-14 text-black-50">Explore detailed doctor profiles on our platform to make informed healthcare decisions.</p>
                        </div>
                        <div class="way-icon">
                            <img src="assets/img/icons/way-icon.svg" alt="img">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="book-item">
                        <div class="book-icon bg-cyan">
                            <i class="isax isax-calendar5"></i>
                        </div>
                        <div class="book-info">
                            <h6 class="text-black mb-2">Schedule Appointment</h6>
                            <p class="fs-14 text-black-50">After choose your preferred doctor, select a convenient time slot, & confirm your appointment.</p>
                        </div>
                        <div class="way-icon">
                            <img src="assets/img/icons/way-icon.svg" alt="img">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="book-item">
                        <div class="book-icon bg-indigo">
                            <i class="isax isax-blend5"></i>
                        </div>
                        <div class="book-info">
                            <h6 class="text-black mb-2">Get Your Solution</h6>
                            <p class="fs-14 text-black-50">Discuss your health concerns with the doctor and receive the personalized advice & with solution.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /How TeleRx Works -->


<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header-one aos" data-aos="fade-up">
                    <h5>Get Your Answer</h5>
                    <h2 class="section-title">Frequently Asked Questions</h2>
                </div>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 aos" data-aos="fade-up">
                <div class="faq-img">
                    <img src="assets/img/faq-img.png" class="img-fluid" alt="img">
                    <div class="faq-patients-count">
                        <div class="faq-smile-img">
                            <img src="assets/img/icons/smiling-icon.svg" alt="icon">
                        </div>
                        <div class="faq-patients-content">
                            <h4><span class="count-digit">1</span>k+</h4>
                            <p>Happy Patients</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="faq-info aos" data-aos="fade-up">
                    <div class="accordion" id="faq-details">

                        <!-- FAQ Item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <a href="javascript:void(0);" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    How do I book an appointment with a doctor?
                                </a>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faq-details">
                                <div class="accordion-body">
                                    <div class="accordion-content">
                                        <p>Yes, simply visit our website and log in or create an account. Search for a doctor based on specialization, location, or availability & confirm your booking.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /FAQ Item -->

                        <!-- FAQ Item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <a href="javascript:void(0);" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Can I request a specific doctor when booking my appointment?
                                </a>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faq-details">
                                <div class="accordion-body">
                                    <div class="accordion-content">
                                        <p>Yes, you can usually request a specific doctor when booking your appointment, though availability may vary based on their schedule.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /FAQ Item -->

                        <!-- FAQ Item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <a href="javascript:void(0);" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    What should I do if I need to cancel or reschedule my appointment?
                                </a>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faq-details">
                                <div class="accordion-body">
                                    <div class="accordion-content">
                                        <p>If you need to cancel or reschedule your appointment, contact the doctor as soon as possible to inform them and to reschedule for another available time slot.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /FAQ Item -->

                        <!-- FAQ Item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <a href="javascript:void(0);" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    What if I'm running late for my appointment?
                                </a>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faq-details">
                                <div class="accordion-body">
                                    <div class="accordion-content">
                                        <p>If you know you will be late, it's courteous to call the doctor's office and inform them. Depending on their policy and schedule, they may be able to accommodate you or reschedule your appointment.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /FAQ Item -->

                        <!-- FAQ Item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFive">
                                <a href="javascript:void(0);" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    Can I book appointments for family members or dependents?
                                </a>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faq-details">
                                <div class="accordion-body">
                                    <div class="accordion-content">
                                        <p>Yes, in many cases, you can book appointments for family members or dependents. However, you may need to provide their personal information and consent to do so.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /FAQ Item -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /FAQ Section -->

<!-- Testimonial Section -->
<section class="testimonial-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="testimonial-slider slick">
                    <div class="testimonial-grid">
                        <div class="testimonial-info">
                            <div class="testimonial-img">
                                <img src="assets/img/clients/client-01.jpg" class="img-fluid" alt="John Doe">
                            </div>
                            <div class="testimonial-content">
                                <div class="section-header-one section-header section-inner-header testimonial-header">
                                    <h5>Testimonials</h5>
                                    <h2 class="section-title">What Our Client Says</h2>
                                </div>
                                <div class="testimonial-details">
                                    <p>TeleRx Bangladesh exceeded my expectations in healthcare. The seamless booking process, coupled with the expertise of the doctors, made my experience exceptional. Their commitment to quality care and convenience truly sets them apart. I highly recommend TeleRx Bangladesh for anyone seeking reliable and accessible healthcare services.</p>
                                    <h6><span class="d-block">John Doe</span> New York</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /Testimonial Section -->

<!-- Partners Section -->
<section class="partners-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header-one text-center aos" data-aos="fade-up">
                    <h2 class="section-title">Our Partners</h2>
                </div>
            </div>
        </div>
        <div class="partners-info aos" data-aos="fade-up">
            <ul class="owl-carousel partners-slider d-flex">
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/brac.jpg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/dhaam.jpg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/labaid.jpg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/rangm.jpg" alt="partners">
                    </a>
                </li>
             </ul>
        </div>
    </div>
</section>
<!-- /Partners Section -->


<?php include 'testimonial.php'; ?>

<!-- Info Section -->
<section class="info-section">
    <div class="container">
        <div class="contact-info">
            <div class="d-lg-flex align-items-center justify-content-between w-100 gap-4">
                <div class="mb-4 mb-lg-0 aos" data-aos="fade-up">
                    <h6 class="display-6 text-white">Working for Your Better Health.</h6>
                </div>
                <div class="d-sm-flex align-items-center justify-content-lg-end gap-4 aos" data-aos="fade-up">
                    <div class="con-info d-flex align-items-center mb-3 mb-sm-0">
                                <span class="con-icon">
                                    <i class="isax isax-headphone"></i>
                                </span>
                        <div class="ms-2">
                            <p class="text-white mb-1">Customer Support</p>
                            <p class="text-white fw-medium mb-0">+880 1836 838888</p>
                        </div>
                    </div>
                    <div class="con-info d-flex align-items-center">
                                <span class="con-icon">
                                    <i class="isax isax-message-2"></i>
                                </span>
                        <div class="ms-2">
                            <p class="text-white mb-1">Drop Us an Email</p>
                            <p class="text-white fw-medium mb-0">care@telerxbd.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- /Info Section -->
<?php include 'footer.php'; ?>