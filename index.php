<?php
session_start();
include 'header.php';?>

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
                        <form action="search-2.php">
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
        <img src="assets/img/bg/banner-icon-01.svg" alt="img" class="banner-bg-05">
        <img src="assets/img/bg/banner-icon-01.svg" alt="img" class="banner-bg-06">
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

<!-- Specialities Section -->
<section class="specialities-section-one">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-7 aos" data-aos="fade-up">
                <div class="section-header-one section-header-slider">
                    <h2 class="section-title">Specialities</h2>
                </div>
            </div>
            <div class="col-md-6 col-5  aos" data-aos="fade-up">
                <div class="owl-nav slide-nav-1 text-end nav-control"></div>
            </div>
        </div>
        <div class="owl-carousel specialities-slider-one owl-theme aos" data-aos="fade-up">
            <div class="item">
                <div class="specialities-item">
                    <div class="specialities-img">
                        <span><img src="assets/img/specialities/specialities-01.svg" alt="heart-image"></span>
                    </div>
                    <p>Cardiology</p>
                </div>
            </div>
            <div class="item">
                <div class="specialities-item">
                    <div class="specialities-img">
                        <span><img src="assets/img/specialities/specialities-02.svg" alt="brain-image"></span>
                    </div>
                    <p>Neurology</p>
                </div>
            </div>
            <div class="item">
                <div class="specialities-item">
                    <div class="specialities-img">
                        <span><img src="assets/img/specialities/specialities-03.svg" alt="kidney-image"></span>
                    </div>
                    <p>Urology</p>
                </div>
            </div>
            <div class="item">
                <div class="specialities-item">
                    <div class="specialities-img">
                        <span><img src="assets/img/specialities/specialities-04.svg" alt="bone-image"></span>
                    </div>
                    <p>Orthopedic</p>
                </div>
            </div>
            <div class="item">
                <div class="specialities-item">
                    <div class="specialities-img">
                        <span><img src="assets/img/specialities/specialities-05.svg" alt="dentist"></span>
                    </div>
                    <p>Dentist</p>
                </div>
            </div>
            <div class="item">
                <div class="specialities-item">
                    <div class="specialities-img">
                        <span><img src="assets/img/specialities/specialities-06.svg" alt="eye-image"></span>
                    </div>
                    <p>Ophthalmology</p>
                </div>
            </div>
            <div class="item">
                <div class="specialities-item">
                    <div class="specialities-img">
                        <span><img src="assets/img/specialities/specialities-02.svg" alt="brain-image"></span>
                    </div>
                    <p>Neurology</p>
                </div>
            </div>
        </div>
        <div class="specialities-btn aos" data-aos="fade-up">
            <a href="search.php" class="btn btn-primary btn-xl">
                See All Specialities
            </a>
        </div>
    </div>
</section>
<!-- /Specialities Section -->

<!-- Work Section -->
<section class="work-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-12 work-img-info aos" data-aos="fade-up">
                <div class="work-img">
                    <img src="assets/img/work-img.png" class="img-fluid" alt="doctor-image">
                </div>
            </div>
            <div class="col-lg-8 col-md-12 work-details">
                <div class="section-header-one aos" data-aos="fade-up">
                    <h5>How it Works</h5>
                    <h2 class="section-title">4 easy steps to get your solution</h2>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 aos" data-aos="fade-up">
                        <div class="work-info">
                            <div class="work-icon">
                                <span><img src="assets/img/icons/work-01.svg" alt="search-doctor-icon"></span>
                            </div>
                            <div class="work-content">
                                <h5>Search Doctor</h5>
                                <p>Search for a doctor based on specialization, location, or availability. </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 aos" data-aos="fade-up">
                        <div class="work-info">
                            <div class="work-icon">
                                <span><img src="assets/img/icons/work-02.svg" alt="doctor-profile-icon"></span>
                            </div>
                            <div class="work-content">
                                <h5>Check Doctor Profile</h5>
                                <p>Explore detailed doctor profiles on our platform to make informed healthcare decisions.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 aos" data-aos="fade-up">
                        <div class="work-info">
                            <div class="work-icon">
                                <span><img src="assets/img/icons/work-03.svg" alt="calendar-icon"></span>
                            </div>
                            <div class="work-content">
                                <h5>Schedule Appointment</h5>
                                <p>After choose your preferred doctor, select a convenient time slot, & confirm your appointment.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 aos" data-aos="fade-up">
                        <div class="work-info">
                            <div class="work-icon">
                                <span><img src="assets/img/icons/work-04.svg" alt="solution-icon"></span>
                            </div>
                            <div class="work-content">
                                <h5>Get Your Solution</h5>
                                <p>Discuss your health concerns with the doctor and receive personalized advice & solution.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /Work Section -->

<!-- How We Work Section -->
<section class="articles-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 aos" data-aos="fade-up">
                <div class="section-header-one section-header-slider text-center">
                    <h2 class="section-title">TeleRx Bangladesh at a Glance</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="service-sec-one">
            <div class="row row-cols-7 row-cols-xxl-7 row-cols-xl-4 row-cols-lg-4 rows-cols-md-6 justify-content-center">
                <div class="col-12 d-flex col-xxl col-lg-3 col-sm-6">
                    <a href="javascript:void(0);" class="serv-wrap blue-bg flex-fill">
                        <span>
                            <img src="assets/img/icons/service-01.svg" alt="heart-image">
                        </span>
                        <h4>Book Appointment</h4>
                    </a>
                </div>
                <div class="col-12 d-flex col-xxl col-lg-3 col-sm-6">
                    <a href="javascript:void(0);" class="serv-wrap green-bg flex-fill">
                        <span>
                            <img src="assets/img/icons/service-02.svg" alt="heart-image">
                        </span>
                        <h4>Lab Testing Services</h4>
                    </a>
                </div>
                <div class="col-12 d-flex col-xxl col-lg-3 col-sm-6">
                    <a href="javascript:void(0);" class="serv-wrap info-bg flex-fill">
                        <span>
                            <img src="assets/img/icons/service-03.svg" alt="heart-image">
                        </span>
                        <h4>Medicines & Supplies</h4>
                    </a>
                </div>
                <div class="col-12 d-flex col-xxl col-lg-3 col-sm-6">
                    <a href="javascript:void(0);" class="serv-wrap red-bg flex-fill">
                        <span>
                            <img src="assets/img/icons/service-04.svg" alt="heart-image">
                        </span>
                        <h4>Hospitals / Clinics</h4>
                    </a>
                </div>
                <div class="col-12 d-flex col-xxl col-lg-3 col-sm-6">
                    <a href="javascript:void(0);" class="serv-wrap success-bg flex-fill">
                        <span>
                            <img src="assets/img/icons/service-05.svg" alt="heart-image">
                        </span>
                        <h4>Health Care Services</h4>
                    </a>
                </div>
                <div class="col-12 d-flex col-xxl col-lg-3 col-sm-6">
                    <a href="javascript:void(0);" class="serv-wrap pink-bg flex-fill">
                        <span>
                            <img src="assets/img/icons/service-06.svg" alt="heart-image">
                        </span>
                        <h4>Talk to Doctor’s</h4>
                    </a>
                </div>
                <div class="col-12 d-flex col-xxl col-lg-3 col-sm-6">
                    <a href="javascript:void(0);" class="serv-wrap danger-bg flex-fill">
                        <span>
                            <img src="assets/img/icons/service-07.svg" alt="heart-image">
                        </span>
                        <h4>Home Care Services</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /How We Work Section -->

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
                    <div class="testimonial-grid">
                        <div class="testimonial-info">
                            <div class="testimonial-img">
                                <img src="assets/img/clients/client-03.jpg" class="img-fluid" alt="Amanda Warren">
                            </div>
                            <div class="testimonial-content">
                                <div class="section-header section-inner-header testimonial-header">
                                    <h5>Testimonials</h5>
                                    <h2>What Our Client Says</h2>
                                </div>
                                <div class="testimonial-details">
                                    <p>As a busy professional, I don't have time to wait on hold or play phone tag to schedule doctor appointments. Thanks to TeleRx Bangladesh, booking appointments has never been easier! The user-friendly interface allows me to quickly find available appointment slots that fit my schedule and book them with just a few clicks. It's a game-changer for anyone looking to streamline their healthcare management.</p>
                                    <h6><span class="d-block">Andrew Denner</span> Nevada</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-grid">
                        <div class="testimonial-info">
                            <div class="testimonial-img">
                                <img src="assets/img/clients/client-11.jpg" class="img-fluid" alt="Betty Carlson">
                            </div>
                            <div class="testimonial-content">
                                <div class="section-header section-inner-header testimonial-header">
                                    <h5>Testimonials</h5>
                                    <h2>What Our Client Says</h2>
                                </div>
                                <div class="testimonial-details">
                                    <p>As a parent, coordinating doctor appointments for my family can be overwhelming. TeleRx Bangladesh has simplified the process and made scheduling appointments a breeze! I love being able to see all available appointment times in one place and book appointments for multiple family members with ease. Plus, the automatic reminders ensure we never miss an appointment. I highly recommend TeleRx Bangladesh to other busy parents!</p>
                                    <h6><span class="d-block">Niya Patel</span> New York</h6>
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
                        <img class="img-fluid" src="assets/img/partners/partners-1.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-2.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-3.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-4.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-5.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-6.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-1.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-2.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-3.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-4.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-5.svg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/partners-6.svg" alt="partners">
                    </a>
                </li>
             </ul>
        </div>
    </div>
</section>
<!-- /Partners Section -->
<?php include 'footer.php'; ?>