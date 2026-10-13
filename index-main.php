<?php
session_start();
include 'header.php';

// Fetch doctors for the slider section
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
} catch (Exception $e) {
    error_log("Error fetching doctors for slider: " . $e->getMessage());
    $doctors_slider = [];
}
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
                                <p>4.9 Ratings</p>
                            </div>
                        </div>
                    </div>
                    <h1 class="display-5">Discover Health: Find Your Trusted <span class="banner-icon"><img src="assets/img/icons/video.svg" alt="img"></span> <span class="text-gradient">Doctors</span> Today</h1>
                    <div class="search-box-one aos" data-aos="fade-up">
                        <form action="doctors" method="GET">
                            <div class="search-input search-line">
                                <i class="isax isax-hospital5 bficon"></i>
                                <div class=" mb-0">
                                    <input type="text" name="search" class="form-control" placeholder="Search doctors, clinics, hospitals, etc">
                                </div>
                            </div>
                            <div class="search-input search-map-line">
                                <i class="isax isax-location5"></i>
                                <div class=" mb-0">
                                    <input type="text" name="location" class="form-control" placeholder="Location">
                                </div>
                            </div>
                            <div class="search-input search-calendar-line">
                                <i class="isax isax-calendar-tick5"></i>
                                <div class=" mb-0">
                                    <input type="text" name="date" class="form-control datetimepicker" placeholder="Date">
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
            <div class="col-md-6 aos" data-aos="fade-up">
                <div class="section-header-one section-header-slider">
                    <h2 class="section-title">TeleRx Best Doctors</h2>
                </div>
            </div>
            <div class="col-md-6 aos" data-aos="fade-up">
                <div class="owl-nav slide-nav-2 text-end nav-control"></div>
            </div>
        </div>
        <div class="owl-carousel doctor-slider-one owl-theme aos" data-aos="fade-up">
            <?php if (!empty($doctors_slider)): ?>
                <?php foreach ($doctors_slider as $doctor): ?>
                    <!-- Doctor Item -->
                    <div class="item">
                        <div class="doctor-profile-widget doc-item">
                            <div class="doc-pro-img">
                                <a href="doctor-profile?id=<?php echo $doctor['id']; ?>">
                                    <div class="doctor-profile-img">
                                        <img src="<?php echo htmlspecialchars($doctor['profile_image'] ?? 'assets/img/doctors/default-doctor.png'); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                                    </div>
                                </a>
                                <div class="doctor-amount">
                                    <span>৳<?php echo number_format($doctor['consultation_fee'] ?? 0, 0); ?></span>
                                </div>
                            </div>
                            <div class="doc-content">
                                <div class="doc-pro-info">
                                    <div class="doc-pro-name">
                                        <a href="doctor-profile?id=<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['name']); ?></a>
                                        <p><?php echo htmlspecialchars($doctor['specialty'] ?? 'General Physician'); ?></p>
                                    </div>
                                    <div class="reviews-ratings">
                                        <p>
                                            <span><i class="fas fa-star"></i> <?php echo number_format($doctor['average_rating'] ?? 0, 1); ?></span> (<?php echo $doctor['total_reviews'] ?? 0; ?>)
                                        </p>
                                    </div>
                                </div>
                                <div class="doc-pro-location">
                                    <p><i class="isax isax-location"></i> <?php 
                                        $location_parts = array_filter([
                                            $doctor['district'] ?? '', 
                                            $doctor['city'] ?? '', 
                                            $doctor['state'] ?? ''
                                        ]);
                                        $location_display = !empty($location_parts) ? implode(', ', $location_parts) : 'Dhaka, Bangladesh';
                                        echo htmlspecialchars($location_display);
                                    ?></p>
                                    <span class="badge <?php echo ($doctor['is_available'] == 1) ? 'badge-success' : 'badge-danger'; ?> doc-badge">
                                        <i class="fa-solid fa-circle"></i><?php echo ($doctor['is_available'] == 1) ? 'Available' : 'Unavailable'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Doctor Item -->
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback: Show message if no doctors found -->
                <div class="item">
                    <div class="doctor-profile-widget doc-item">
                        <div class="doc-content text-center py-5">
                            <p class="text-muted">No doctors available at the moment.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- /Doctors Section -->

<!-- Services Section -->
<section class="services-section aos" data-aos="fade-up">
    <div class="horizontal-slide d-flex" data-direction="left" data-speed="slow">
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

<?php include __DIR__ . '/index-subscription-section.php'; ?>
<?php include __DIR__ . '/index-products-section.php'; ?>
<?php include __DIR__ . '/index-homecare-section.php'; ?>
<?php include __DIR__ . '/index-corporate-section.php'; ?>
<?php include __DIR__ . '/index-welfare-section.php'; ?>
<?php include __DIR__ . '/index-medical-camp-section.php'; ?>

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
                        <img class="img-fluid" src="assets/img/partners/rac.jpg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/haam.jpg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/abaid.jpg" alt="partners">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);">
                        <img class="img-fluid" src="assets/img/partners/angm.jpg" alt="partners">
                    </a>
                </li>
             </ul>
        </div>
    </div>
</section>
<!-- /Partners Section -->

<?php include 'footer-banner.php'; ?>

<style>
.search-suggestion-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 9999;
    overflow: hidden;
    max-height: 280px;
    overflow-y: auto;
}
.search-suggestion-dropdown .suggestion-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 14px;
    cursor: pointer;
    font-size: 14px;
    color: #2d3748;
    transition: background 0.15s;
}
.search-suggestion-dropdown .suggestion-item:hover,
.search-suggestion-dropdown .suggestion-item.active {
    background: #f0f7ff;
    color: #0c77c9;
}
.search-suggestion-dropdown .suggestion-item mark {
    background: transparent;
    color: #0c77c9;
    font-weight: 600;
    padding: 0;
}
.search-input { position: relative; }
</style>

<script>
(function() {
    var debounceTimers = {};
    var iconMap = { doctor:'fa-user-doctor', specialty:'fa-stethoscope', district:'fa-map-marker-alt', city:'fa-city' };

    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
    function highlightMatch(text, query) {
        var escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return escapeHtml(text).replace(new RegExp('(' + escaped + ')', 'gi'), '<mark>$1</mark>');
    }
    function createDropdown(inputEl) {
        var existing = inputEl.parentNode.querySelector('.search-suggestion-dropdown');
        if (existing) return existing;
        var drop = document.createElement('div');
        drop.className = 'search-suggestion-dropdown';
        drop.style.display = 'none';
        inputEl.parentNode.style.position = 'relative';
        inputEl.parentNode.appendChild(drop);
        return drop;
    }
    function showDropdown(drop, items, query, onSelect) {
        if (!items.length) { drop.style.display = 'none'; return; }
        drop.innerHTML = '';
        items.forEach(function(item) {
            var div = document.createElement('div');
            div.className = 'suggestion-item';
            div.innerHTML = '<span>' + highlightMatch(item.label, query) + '</span>';
            div.addEventListener('mousedown', function(e) { e.preventDefault(); onSelect(item.label); });
            drop.appendChild(div);
        });
        drop.style.display = 'block';
        drop._activeIdx = -1;
    }
    function hideDropdown(drop) { drop.style.display = 'none'; drop._activeIdx = -1; }

    function initAutocomplete(inputEl, apiType) {
        var drop = createDropdown(inputEl);
        var form = inputEl.closest('form');
        inputEl.addEventListener('input', function() {
            var q = this.value.trim();
            clearTimeout(debounceTimers[apiType + inputEl.name]);
            if (q.length < 2) { hideDropdown(drop); return; }
            debounceTimers[apiType + inputEl.name] = setTimeout(function() {
                fetch('api/search-suggestions.php?type=' + apiType + '&q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(items) {
                        showDropdown(drop, items, q, function(val) {
                            inputEl.value = val;
                            hideDropdown(drop);
                            if (form) form.submit();
                        });
                    }).catch(function() { hideDropdown(drop); });
            }, 220);
        });
        inputEl.addEventListener('keydown', function(e) {
            if (drop.style.display === 'none') return;
            var items = drop.querySelectorAll('.suggestion-item');
            var idx = drop._activeIdx !== undefined ? drop._activeIdx : -1;
            if (e.key === 'ArrowDown') { e.preventDefault(); idx = Math.min(idx + 1, items.length - 1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); idx = Math.max(idx - 1, -1); }
            else if (e.key === 'Enter') {
                if (idx >= 0 && items[idx]) {
                    e.preventDefault();
                    inputEl.value = items[idx].textContent.trim();
                    hideDropdown(drop);
                    if (form) form.submit();
                    return;
                }
                hideDropdown(drop); return;
            } else if (e.key === 'Escape') { hideDropdown(drop); return; } else return;
            items.forEach(function(el, i) { el.classList.toggle('active', i === idx); });
            drop._activeIdx = idx;
        });
        inputEl.addEventListener('blur', function() { setTimeout(function() { hideDropdown(drop); }, 150); });
        inputEl.addEventListener('focus', function() { if (this.value.trim().length >= 2) this.dispatchEvent(new Event('input')); });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var searchInput   = document.querySelector('.banner-section input[name="search"]');
        var locationInput = document.querySelector('.banner-section input[name="location"]');
        if (searchInput)   initAutocomplete(searchInput,   'search');
        if (locationInput) initAutocomplete(locationInput, 'location');
    });
})();
</script>
<!-- 24/7 Emergency Floating Popup -->
<div class="emergency-popup-container" id="emergencyPopup">
    <div class="emergency-popup-content">
        <div class="emergency-icon-wrap">
            <i class="fa-solid fa-truck-medical pulse-animation"></i>
        </div>
        <div class="emergency-text">
            <h4>24/7 Emergency Care</h4>
            <p>Get immediate consultation with an available doctor now.</p>
        </div>
        <a href="emergency-booking.php" class="btn btn-danger emergency-btn">Book Now</a>
        <button class="emergency-close-btn" onclick="document.getElementById('emergencyPopup').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
    </div>
</div>

<style>
.emergency-popup-container {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    animation: slideUp 0.5s ease-out;
}
.emergency-popup-content {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(220,53,69,0.2);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    border-left: 5px solid #dc3545;
    position: relative;
    max-width: 450px;
}
.emergency-icon-wrap {
    background: #dc3545;
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.pulse-animation {
    animation: pulse 2s infinite;
}
.emergency-text {
    flex-grow: 1;
}
.emergency-text h4 {
    margin: 0 0 5px;
    font-size: 16px;
    font-weight: 700;
    color: #dc3545;
}
.emergency-text p {
    margin: 0;
    font-size: 13px;
    color: #6c757d;
    line-height: 1.4;
}
.emergency-btn {
    white-space: nowrap;
    padding: 8px 15px;
    font-weight: 600;
    border-radius: 6px;
}
.emergency-close-btn {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #fff;
    border: 1px solid #eee;
    color: #666;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
@keyframes slideUp {
    from { transform: translateY(100px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
@media (max-width: 768px) {
    .emergency-popup-container {
        bottom: 20px;
        right: 15px;
        left: 15px;
    }
    .emergency-popup-content {
        max-width: 100%;
        flex-direction: column;
        text-align: center;
        padding: 25px 20px 20px;
    }
    .emergency-btn {
        width: 100%;
    }
}
</style>

<?php include 'footer.php'; ?>