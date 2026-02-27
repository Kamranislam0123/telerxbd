<?php
session_start();
include 'header.php';
require_once 'php/config.php';

try {
    $conn = getDBConnection();

    // Fetch all doctors with their profiles for list view
    $stmt = $conn->prepare("
        SELECT
            d.*,
            dp.bio,
            dp.specialty,
            dp.languages_spoken,
            dp.consultation_fee,
            dp.experience_years,
            dp.total_appointments,
            dp.total_reviews,
            dp.average_rating,
            dp.is_available,
            dp.address,
            dp.city,
            dp.state,
            dp.zip_code,
            dp.district,
            dp.profile_image,
            (SELECT COUNT(*) FROM doctor_experiences de WHERE de.doctor_id = d.id) as experience_count,
            (SELECT COUNT(*) FROM doctor_education ded WHERE ded.doctor_id = d.id) as education_count,
            (SELECT COUNT(*) FROM doctor_awards da WHERE da.doctor_id = d.id) as awards_count
        FROM doctors d
        LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id
        ORDER BY d.created_at DESC
    ");
    $stmt->execute();
    $doctors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Build specialities with counts from doctors (split comma-separated specialties so each counts separately)
    $specialities_with_counts = [];
    foreach ($doctors as $d) {
        $spec_str = $d['specialty'] ?? '';
        if ($spec_str === '') continue;
        $specs = array_map('trim', explode(',', $spec_str));
        foreach ($specs as $spec) {
            if ($spec !== '') {
                $specialities_with_counts[$spec] = ($specialities_with_counts[$spec] ?? 0) + 1;
            }
        }
    }

    // Define all available specialities from doctor-profile-settings.php
    $all_specialities = [
        'General Physician',
        'Pediatrician',
        'Gynecologist',
        'Dermatologist',
        'ENT Specialist',
        'Psychiatrist',
        'Diabetologist',
        'Cardiologist',
        'Neurologist',
        'Orthopedic Specialist',
        'Urologist',
        'Gastroenterologist',
        'Physiotherapist',
        'Pulmonologist',
        'Nephrologist',
        'Oncologist',
        'Sexologist',
        'Rheumatologist',
        'Allergist/Immunologist',
        'Ophthalmologist',
        'Psychologist',
        'Internal Medicine',
        'Family Medicine',
        'Physical Medicine'
    ];

    // Merge database specialities with predefined list, ensuring all are included
    $final_specialities = [];
    foreach ($all_specialities as $spec) {
        $final_specialities[$spec] = isset($specialities_with_counts[$spec]) ? $specialities_with_counts[$spec] : 0;
    }
    // Also add any specialities from database that aren't in the predefined list
    foreach ($specialities_with_counts as $spec => $count) {
        if (!isset($final_specialities[$spec])) {
            $final_specialities[$spec] = $count;
        }
    }

    $conn->close();
} catch (Exception $e) {
    error_log("Error fetching doctors: " . $e->getMessage());
    $doctors = [];
    $final_specialities = [];
}
?>
    <style>                               
        /* Specialities filter with scroll */
        .specialities-scroll {
            max-height: 265px; /* ৬টি আইটেম দেখানোর জন্য প্রায় */
            overflow-y: auto;
            padding-right: 10px;
        }

        /* Scrollbar styling (optional) */
        .specialities-scroll::-webkit-scrollbar {
            width: 7px;
        }

        .specialities-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .specialities-scroll::-webkit-scrollbar-thumb {
            background: #0c77c9;
            border-radius: 10px;
        }

        .specialities-scroll::-webkit-scrollbar-thumb:hover {
            background: #15558d;
        }
        
    </style>

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar overflow-visible">
        <div class="container">
            <div class="row align-items-center inner-banner">
                <div class="col-md-12 col-12 text-center">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <h2 class="breadcrumb-title">Search Doctors</h2>
                    </nav>
                </div>
            </div>
            <div class="bg-primary-gradient rounded-pill doctors-search-box">
                <div class="search-box-one rounded-pill">
                    <form action="doctors.php">
                        <div class="search-input search-line">
                            <i class="isax isax-hospital5 bficon"></i>
                            <div class=" mb-0">
                                <input type="text" class="form-control" placeholder="Search for Doctors">
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
                            <button class="btn btn-primary d-inline-flex align-items-center rounded-pill" type="submit"><i class="isax isax-search-normal-15 me-2"></i>Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="breadcrumb-bg">
            <img src="assets/img/bg/breadcrumb-bg-01.png" alt="img" class="breadcrumb-bg-01">
            <img src="assets/img/bg/breadcrumb-bg-02.png" alt="img" class="breadcrumb-bg-02">
            <img src="assets/img/bg/breadcrumb-icon.png" alt="img" class="breadcrumb-bg-03">
            <img src="assets/img/bg/breadcrumb-icon.png" alt="img" class="breadcrumb-bg-04">
        </div>
    </div>
    <!-- /Breadcrumb -->

    <div class="content mt-5">
        <div class="container">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card filter-lists">
                        <div class="card-header">
                            <div class="d-flex align-items-center filter-head justify-content-between">
                                <h4>Filter Doctors</h4>
                                <a href="#" class="text-secondary text-decoration-underline clear-all-filters">Clear All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="accordion-item border-bottom">
                                <div class="accordion-header" id="heading1">
                                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-controls="collapse1" role="button">
                                        <div class="d-flex align-items-center w-100">
                                            <h5>Specialities</h5>
                                            <div class="ms-auto">
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="collapse1" class="accordion-collapse show" aria-labelledby="heading1">
                                    <div class="accordion-body pt-3">
                                        <div class="specialities-scroll">
                                            <?php
                                            if (!empty($final_specialities)):
                                                $speciality_index = 2; // Starting from checkebox-sm2
                                                
                                                arsort($final_specialities);
                                                
                                                // Display all specialities
                                                foreach ($final_specialities as $speciality => $count):
                                                    $checkbox_id = 'checkebox-sm' . $speciality_index;
                                            ?>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input speciality-filter" type="checkbox" 
                                                        value="<?php echo htmlspecialchars($speciality); ?>" 
                                                        id="<?php echo $checkbox_id; ?>" 
                                                        data-speciality="<?php echo htmlspecialchars($speciality); ?>">
                                                    <label class="form-check-label" for="<?php echo $checkbox_id; ?>">
                                                        <?php echo htmlspecialchars($speciality); ?>
                                                    </label>
                                                </div>
                                                <span class="filter-badge"><?php echo $count; ?></span>
                                            </div>
                                            <?php
                                                    $speciality_index++;
                                                endforeach;
                                            else:
                                            ?>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="checkebox-sm2" checked="">
                                                    <label class="form-check-label" for="checkebox-sm2">
                                                        No Specialities Available
                                                    </label>
                                                </div>
                                                <span class="filter-badge">0</span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                        <div class="view-all">
                                            <a href="javascript:void(0);" class="viewall-button-two text-secondary text-decoration-underline">View More</a>
                                        </div>
                                </div>
                            </div>
                            <div class="accordion-item border-bottom">
                                <div class="accordion-header" id="heading2">
                                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-controls="collapse2" role="button">
                                        <div class="d-flex align-items-center w-100">
                                            <h5>Gender</h5>
                                            <div class="ms-auto">
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse2" class="accordion-collapse show" aria-labelledby="heading2">
                                    <div class="accordion-body pt-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm11" checked="">
                                                <label class="form-check-label" for="checkebox-sm11">
                                                    Male
                                                </label>
                                            </div>
                                            <span class="filter-badge">21</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm12">
                                                <label class="form-check-label" for="checkebox-sm12">
                                                    Female
                                                </label>
                                            </div>
                                            <span class="filter-badge">21</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item border-bottom">
                                <div class="accordion-header" id="heading5">
                                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-controls="collapse5" role="button">
                                        <div class="d-flex align-items-center w-100">
                                            <h5>Experience</h5>
                                            <div class="ms-auto">
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse5" class="accordion-collapse show" aria-labelledby="heading5">
                                    <div class="accordion-body pt-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm21" checked="">
                                                <label class="form-check-label" for="checkebox-sm21">
                                                    2+ Years
                                                </label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm22">
                                                <label class="form-check-label" for="checkebox-sm22">
                                                    5+ Years
                                                </label>
                                            </div>
                                        </div>
                                        <div class="view-content">
                                            <div class="viewall-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="checkebox-sm23">
                                                        <label class="form-check-label" for="checkebox-sm23">
                                                            7+ Years
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="checkebox-sm24">
                                                        <label class="form-check-label" for="checkebox-sm24">
                                                            10+ Years
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="view-all">
                                                <a href="javascript:void(0);" class="viewall-button-3 text-secondary text-decoration-underline">View More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <div class="accordion-header" id="heading9">
                                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-controls="collapse9" role="button">
                                        <div class="d-flex align-items-center w-100">
                                            <h5>Reviews</h5>
                                            <div class="ms-auto">
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse9" class="accordion-collapse show" aria-labelledby="heading9">
                                    <div class="accordion-body pt-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm38" checked="">
                                                <label class="form-check-label" for="checkebox-sm38">
                                                    <span>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                    </span>
                                                    5 Star
                                                </label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm39">
                                                <label class="form-check-label" for="checkebox-sm39">
                                                    <span>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                    </span>
                                                    4 Star
                                                </label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm40">
                                                <label class="form-check-label" for="checkebox-sm40">
                                                    <span>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                    </span>
                                                    3 Star
                                                </label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm41">
                                                <label class="form-check-label" for="checkebox-sm41">
                                                    <span>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                    </span>
                                                    2 Star
                                                </label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm42">
                                                <label class="form-check-label" for="checkebox-sm42">
                                                    <span>
                                                        <i class="fa-solid fa-star text-orange me-1"></i>
                                                    </span>
                                                    1 Star
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h3>Showing <span class="text-secondary" id="doctor-count"><?php echo count($doctors); ?></span> Doctors For You</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-end mb-4">
                                <div class="doctor-filter-availability me-3">
                                    <p>Availability</p>
                                    <div class="status-toggle status-tog">
                                        <input type="checkbox" id="status_6" class="check">
                                        <label for="status_6" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                                <div class="dropdown header-dropdown me-2">
                                    <a class="dropdown-toggle sort-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);" aria-expanded="false">
                                        <span>Sort By</span>Price (Low to High)
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            Price (Low to High)
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            Price (High to Low)
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <?php if (empty($doctors)): ?>
                                <div class="text-center">
                                    <p>No doctors found.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($doctors as $doctor): ?>
                                    <div class="card doctor-list-card" 
                                        data-speciality="<?php echo htmlspecialchars($doctor['specialty'] ?? 'General Physician'); ?>">
                                        <div class="d-md-flex align-items-center">
                                            <div class="card-img card-img-hover">
                                                <a href="doctor-profile.php?id=<?php echo $doctor['id']; ?>">
                                                    <img src="<?php echo $doctor['profile_image'] ?? 'assets/img/doctors/default-doctor.jpg'; ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                                                </a>
                                                <div class="grid-overlay-item d-flex align-items-center justify-content-between">
                                                    <span class="badge bg-orange">
                                                        <i class="fa-solid fa-star me-1"></i>
                                                        <?php echo number_format($doctor['average_rating'] ?? 0, 1); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="d-flex align-items-center justify-content-between border-bottom p-3">
                                                    <a href="#" class="text-teal fw-medium fs-14">
                                                        <?php echo htmlspecialchars($doctor['specialty'] ?? 'General Physician'); ?>
                                                    </a>
                                                    <span class="badge <?php echo ($doctor['is_available'] ?? false) ? 'bg-success-light' : 'bg-danger-light'; ?> d-inline-flex align-items-center">
                                                        <i class="fa-solid fa-circle fs-5 me-1"></i>
                                                        <?php echo ($doctor['is_available'] ?? false) ? 'Available' : 'Unavailable'; ?>
                                                    </span>
                                                </div>
                                                <div class="p-3">
                                                    <div class="doctor-info-detail pb-3">
                                                        <div class="row align-items-center gy-3">
                                                            <div class="col-sm-6">
                                                                <div>
                                                                    <h6 class="d-flex align-items-center mb-1">
                                                                        <a href="doctor-profile.php?id=<?php echo $doctor['id']; ?>">
                                                                            Dr. <?php echo htmlspecialchars($doctor['name']); ?>
                                                                        </a>
                                                                        <i class="isax isax-tick-circle5 text-success ms-2"></i>
                                                                    </h6>
                                                                    <p class="mb-2">
                                                                        <?php
                                                                        // Display multiple specialities
                                                                        $speciality_display = $doctor['specialty'] ?? 'Medical Doctor';
                                                                        if (!empty($speciality_display)) {
                                                                            $specialities_array = array_map('trim', explode(',', $speciality_display));
                                                                            echo htmlspecialchars(implode(', ', $specialities_array));
                                                                        } else {
                                                                            echo 'Medical Doctor';
                                                                        }
                                                                        ?>
                                                                    </p>
                                                                    <p class="d-flex align-items-center mb-0 fs-14">
                                                                        <i class="isax isax-location me-2"></i>
                                                                        <?php
                                                                        $location_parts = array_filter([$doctor['district'] ?? '', $doctor['city'] ?? '', $doctor['state'] ?? '']);
                                                                        $location_display = !empty($location_parts) ? implode(', ', $location_parts) : 'Dhaka, Bangladesh';
                                                                        echo htmlspecialchars($location_display);
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div>
                                                                    <p class="d-flex align-items-center mb-0 fs-14 mb-2">
                                                                        <i class="isax isax-archive-14 text-dark me-2"></i>
                                                                        <?php echo $doctor['experience_years'] ?? 5; ?> Years of Experience
                                                                    </p>
                                                                    <p class="d-flex align-items-center mb-0 fs-14 mb-2">
                                                                        <i class="isax isax-like-1 text-dark me-2"></i>
                                                                        <?php echo number_format($doctor['average_rating'] ?? 0, 1); ?>%
                                                                        (<?php echo $doctor['total_reviews'] ?? 0; ?> Votes)
                                                                    </p>
                                                                    <p class="d-flex align-items-center mb-0 fs-14 mb-2">
                                                                        <i class="isax isax-language-circle text-dark me-2"></i>
                                                                        <?php echo htmlspecialchars($doctor['languages_spoken'] ?? 'English, Bengali'); ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <p class="mb-1">Consultation Fees</p>
                                                            <h4 class="text-orange">
                                                                $<?php echo number_format($doctor['consultation_fee'] ?? 100, 0); ?>
                                                            </h4>
                                                        </div>
                                                        <a href="booking.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-md btn-dark d-inline-flex align-items-center rounded-pill">
                                                            <i class="isax isax-calendar-1 me-2"></i>
                                                            Book Now
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rangeslider JS -->
    <script src="assets/plugins/ion-rangeslider/js/ion.rangeSlider.js"></script>
    <script src="assets/plugins/ion-rangeslider/js/custom-rangeslider.js"></script>
    <script src="assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
    
    <!-- Speciality Filter Script -->
    <script>
    $(document).ready(function() {
        console.log('✅ Filter script initialized');
        console.log('📊 Total doctors found:', $('.doctor-list-card').length);
        console.log('🔍 Total speciality filters found:', $('.speciality-filter').length);
        
        // Function to filter doctors by speciality
        function filterDoctorsBySpeciality() {
            var selectedSpecialities = [];
            
            // Get all checked speciality checkboxes
            $('.speciality-filter:checked').each(function() {
                var speciality = $(this).val().trim();
                if (speciality !== '') {
                    selectedSpecialities.push(speciality);
                }
            });
            
            console.log('✅ Selected specialities:', selectedSpecialities);
            
            // If no specialities are selected, show all doctors
            if (selectedSpecialities.length === 0) {
                $('.doctor-list-card').fadeIn(300);
                updateDoctorCount();
                return;
            }
            
            // Filter doctors based on selected specialities
            $('.doctor-list-card').each(function() {
                var doctorSpecialityStr = $(this).data('speciality') || '';
                doctorSpecialityStr = doctorSpecialityStr.trim();
                
                if (doctorSpecialityStr === '' || doctorSpecialityStr === 'null') {
                    doctorSpecialityStr = 'General Physician';
                }
                
                // Split doctor's specialties (e.g. "Cardiologist, Dermatologist" -> ["Cardiologist", "Dermatologist"])
                var doctorSpecialities = doctorSpecialityStr.split(',').map(function(s) { 
                    return s.trim(); 
                }).filter(function(s) { 
                    return s !== ''; 
                });
                
                if (doctorSpecialities.length === 0) {
                    doctorSpecialities = ['General Physician'];
                }
                
                console.log('Doctor specialities:', doctorSpecialities, 'Selected:', selectedSpecialities);
                
                // Check if any selected speciality matches doctor's specialties
                var matches = false;
                for (var i = 0; i < selectedSpecialities.length; i++) {
                    for (var j = 0; j < doctorSpecialities.length; j++) {
                        if (doctorSpecialities[j].toLowerCase().trim() === selectedSpecialities[i].toLowerCase().trim()) {
                            matches = true;
                            break;
                        }
                    }
                    if (matches) break;
                }
                
                // Show or hide based on match
                if (matches) {
                    $(this).fadeIn(300);
                } else {
                    $(this).fadeOut(300);
                }
            });
            
            updateDoctorCount();
        }
        
        // Function to update the doctor count
        function updateDoctorCount() {
            var visibleCount = $('.doctor-list-card:visible').length;
            $('#doctor-count').text(visibleCount);
            
            // Show message if no doctors match the filter
            if (visibleCount === 0) {
                if ($('.no-doctors-message').length === 0) {
                    $('.col-lg-12').append(
                        '<div class="text-center no-doctors-message mt-4">' +
                        '<p class="text-muted">No doctors found matching the selected specialities.</p>' +
                        '</div>'
                    );
                }
            } else {
                $('.no-doctors-message').remove();
            }
        }
        
        // Handle speciality checkbox changes
        $(document).on('change', '.speciality-filter', function() {
            console.log('🔄 Checkbox changed:', $(this).val(), 'Checked:', $(this).is(':checked'));
            filterDoctorsBySpeciality();
        });
        
        // Handle "Clear All" link
        $(document).on('click', '.clear-all-filters', function(e) {
            e.preventDefault();
            console.log('🧹 Clear All clicked');
            
            // Uncheck all speciality filters
            $('.speciality-filter').prop('checked', false);
            
            // Show all doctors
            $('.doctor-list-card').fadeIn(300);
            
            // Update count
            updateDoctorCount();
        });
        
        // Initialize - show all doctors by default
        updateDoctorCount();
        
        // Log all doctor specialities for debugging
        $('.doctor-list-card').each(function(index) {
            console.log(`👨‍⚕️ Doctor ${index + 1} data-speciality:`, $(this).data('speciality'));
        });
    });
    </script>

<?php include 'footer.php'; ?>