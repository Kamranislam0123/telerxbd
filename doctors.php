<?php
session_start();
include 'header.php';
require_once 'php/config.php';

$search_keyword = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
$location_filter = isset($_GET['location']) ? trim((string)$_GET['location']) : '';
$date_selected = isset($_GET['date']) ? trim((string)$_GET['date']) : '';

try {
    $conn = getDBConnection();

    // Fetch doctors with optional search and location filtering
    $where_clauses = ['1=1'];
    $params = [];
    $types = '';

    if ($search_keyword !== '') {
        $where_clauses[] = "(
            d.name LIKE ? OR
            dp.specialty LIKE ? OR
            dp.bio LIKE ? OR
            dp.languages_spoken LIKE ?
        )";
        $like_search = "%{$search_keyword}%";
        $params[] = $like_search;
        $params[] = $like_search;
        $params[] = $like_search;
        $params[] = $like_search;
        $types .= 'ssss';
    }

    if ($location_filter !== '') {
        $where_clauses[] = "(
            dp.district LIKE ? OR
            dp.city LIKE ? OR
            dp.state LIKE ? OR
            dp.address LIKE ?
        )";
        $like_location = "%{$location_filter}%";
        $params[] = $like_location;
        $params[] = $like_location;
        $params[] = $like_location;
        $params[] = $like_location;
        $types .= 'ssss';
    }

    if ($date_selected !== '') {
        $where_clauses[] = 'dp.is_available = 1';
    }

    $sql = "
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
            dp.gender,
            (SELECT COUNT(*) FROM doctor_experiences de WHERE de.doctor_id = d.id) as experience_count,
            (SELECT COUNT(*) FROM doctor_education ded WHERE ded.doctor_id = d.id) as education_count,
            (SELECT COUNT(*) FROM doctor_awards da WHERE da.doctor_id = d.id) as awards_count
        FROM doctors d
        LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id
        WHERE " . implode(' AND ', $where_clauses) . "
        ORDER BY d.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
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

    // Gender counts for filter (Male, Female, Other)
    $gender_counts = ['Male' => 0, 'Female' => 0, 'Other' => 0];
    $district_counts = [];
    foreach ($doctors as $d) {
        $g = isset($d['gender']) && $d['gender'] !== '' ? trim($d['gender']) : 'Other';
        if (!isset($gender_counts[$g])) {
            $gender_counts[$g] = 0;
        }
        $gender_counts[$g]++;

        $dist = isset($d['district']) && $d['district'] !== '' ? trim($d['district']) : '';
        if ($dist !== '') {
            $district_counts[$dist] = ($district_counts[$dist] ?? 0) + 1;
        }
    }

    $conn->close();
} catch (Exception $e) {
    error_log("Error fetching doctors: " . $e->getMessage());
    $doctors = [];
    $final_specialities = [];
    $gender_counts = ['Male' => 0, 'Female' => 0, 'Other' => 0];
    $district_counts = [];
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

        /* Doctor profile card image: 612x391 - fill box, focus on center so crop is even */
        .doctor-profile-card-img {
            width: 100%;
            aspect-ratio: 612 / 391;
            overflow: hidden;
            border-radius: 10px 10px 0 0;
            background: #f0f0f0;
        }
        .doctor-profile-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            display: block;
            vertical-align: middle;
        }

        /* Load more: hide items until revealed */
        .doctor-grid-item.load-more-hidden {
            display: none !important;
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
                    <form action="doctors.php" method="GET">
                        <div class="search-input search-line">
                            <i class="isax isax-hospital5 bficon"></i>
                            <div class=" mb-0">
                                <input type="text" name="search" class="form-control" placeholder="Search for Doctors" value="<?php echo htmlspecialchars($search_keyword); ?>">
                            </div>
                        </div>
                        <div class="search-input search-map-line">
                            <i class="isax isax-location5"></i>
                            <div class=" mb-0">
                                <input type="text" name="location" class="form-control" placeholder="Location" value="<?php echo htmlspecialchars($location_filter); ?>">
                            </div>
                        </div>
                        <div class="search-input search-calendar-line">
                            <i class="isax isax-calendar-tick5"></i>
                            <div class=" mb-0">
                                <input type="text" name="date" class="form-control datetimepicker" placeholder="Date" value="<?php echo htmlspecialchars($date_selected); ?>">
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
                                                $speciality_index = 2;
                                                arsort($final_specialities);
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
                                            <?php $speciality_index++; endforeach; ?>
                                            <?php
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
                                        <?php foreach (['Male', 'Female'] as $idx => $gender): $cb_id = 'gender-filter-' . strtolower($gender); $count = $gender_counts[$gender] ?? 0; ?>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input gender-filter" type="checkbox" value="<?php echo htmlspecialchars($gender); ?>" id="<?php echo $cb_id; ?>">
                                                <label class="form-check-label" for="<?php echo $cb_id; ?>"><?php echo htmlspecialchars($gender); ?></label>
                                            </div>
                                            <span class="filter-badge"><?php echo (int)$count; ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-bottom">
                                <div class="accordion-header" id="headingDistrict">
                                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseDistrict" aria-controls="collapseDistrict" role="button">
                                        <div class="d-flex align-items-center w-100">
                                            <h5>District</h5>
                                            <div class="ms-auto">
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapseDistrict" class="accordion-collapse show" aria-labelledby="headingDistrict">
                                    <div class="accordion-body pt-3">
                                        <div class="specialities-scroll">
                                            <?php
                                            if (!empty($district_counts)):
                                                ksort($district_counts);
                                                $dist_idx = 1;
                                                foreach ($district_counts as $district => $count):
                                                    $cb_id = 'district-filter-' . $dist_idx;
                                            ?>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input district-filter" type="checkbox" 
                                                        value="<?php echo htmlspecialchars($district); ?>" 
                                                        id="<?php echo $cb_id; ?>">
                                                    <label class="form-check-label" for="<?php echo $cb_id; ?>">
                                                        <?php echo htmlspecialchars($district); ?>
                                                    </label>
                                                </div>
                                                <span class="filter-badge"><?php echo $count; ?></span>
                                            </div>
                                            <?php $dist_idx++; endforeach; ?>
                                            <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        No districts available
                                                    </label>
                                                </div>
                                                <span class="filter-badge">0</span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- <div class="accordion-item border-bottom">
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
                            </div> -->
                            
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
                    <?php if (false): /* LIST DESIGN - commented out, use grid below */ ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <?php if (empty($doctors)): ?>
                                <div class="text-center"><p>No doctors found.</p></div>
                            <?php else: ?>
                                <?php foreach ($doctors as $doctor): ?>
                                    <div class="card doctor-list-card" data-speciality="<?php echo htmlspecialchars($doctor['specialty'] ?? 'General Physician'); ?>" data-gender="<?php echo htmlspecialchars($doctor['gender'] ?? 'Other'); ?>">
                                        <div class="d-md-flex align-items-center">
                                            <?php
                                                $doctorImage = trim($doctor['profile_image'] ?? '') !== ''
                                                    ? $doctor['profile_image']
                                                    : 'assets/img/doctors/default-doctor.png';
                                            ?>
                                            <div class="card-img card-img-hover">
                                                <a href="doctor-profile.php?id=<?php echo $doctor['id']; ?>">
                                                    <img src="<?php echo htmlspecialchars($doctorImage); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                                                </a>
                                                <div class="grid-overlay-item d-flex align-items-center justify-content-between">
                                                    <span class="badge bg-orange"><i class="fa-solid fa-star me-1"></i><?php echo number_format($doctor['average_rating'] ?? 0, 1); ?></span>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="d-flex align-items-center justify-content-between border-bottom p-3">
                                                    <a href="#" class="text-teal fw-medium fs-14"><?php echo htmlspecialchars($doctor['specialty'] ?? 'General Physician'); ?></a>
                                                    <span class="badge <?php echo ($doctor['is_available'] ?? false) ? 'bg-success-light' : 'bg-danger-light'; ?> d-inline-flex align-items-center">
                                                        <i class="fa-solid fa-circle fs-5 me-1"></i><?php echo ($doctor['is_available'] ?? false) ? 'Available' : 'Unavailable'; ?>
                                                    </span>
                                                </div>
                                                <div class="p-3">
                                                    <div class="doctor-info-detail pb-3">
                                                        <div class="row align-items-center gy-3">
                                                            <div class="col-sm-6">
                                                                <div>
                                                                    <h6 class="d-flex align-items-center mb-1">
                                                                        <a href="doctor-profile.php?id=<?php echo $doctor['id']; ?>">Dr. <?php echo htmlspecialchars($doctor['name']); ?></a>
                                                                        <i class="isax isax-tick-circle5 text-success ms-2"></i>
                                                                    </h6>
                                                                    <p class="mb-2"><?php $speciality_display = $doctor['specialty'] ?? 'Medical Doctor'; echo htmlspecialchars(!empty($speciality_display) ? implode(', ', array_map('trim', explode(',', $speciality_display))) : 'Medical Doctor'); ?></p>
                                                                    <p class="d-flex align-items-center mb-0 fs-14"><i class="isax isax-location me-2"></i><?php $location_parts = array_filter([$doctor['district'] ?? '', $doctor['city'] ?? '', $doctor['state'] ?? '']); echo htmlspecialchars(!empty($location_parts) ? implode(', ', $location_parts) : 'Dhaka, Bangladesh'); ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div>
                                                                    <p class="d-flex align-items-center mb-0 fs-14 mb-2"><i class="isax isax-archive-14 text-dark me-2"></i><?php echo $doctor['experience_years'] ?? 5; ?> Years of Experience</p>
                                                                    <p class="d-flex align-items-center mb-0 fs-14 mb-2"><i class="isax isax-like-1 text-dark me-2"></i><?php echo number_format($doctor['average_rating'] ?? 0, 1); ?>% (<?php echo $doctor['total_reviews'] ?? 0; ?> Votes)</p>
                                                                    <p class="d-flex align-items-center mb-0 fs-14 mb-2"><i class="isax isax-language-circle text-dark me-2"></i><?php echo htmlspecialchars($doctor['languages_spoken'] ?? 'English, Bengali'); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <p class="mb-1">Consultation Fees</p>
                                                            <h4 class="text-orange">৳<?php echo number_format($doctor['consultation_fee'] ?? 100, 0); ?></h4>
                                                        </div>
                                                        <a href="booking.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-md btn-dark d-inline-flex align-items-center rounded-pill"><i class="isax isax-calendar-1 me-2"></i>Book Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Dynamic grid: col-xxl-4 col-md-6 per doctor -->
                    <div class="row" id="doctors-grid-row">
                        <?php if (empty($doctors)): ?>
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <p class="mb-0">No doctors found.</p>
                                </div>
                            </div>
                        <?php else:
                            $load_more_initial = 12;
                            $doctor_index = 0;
                            foreach ($doctors as $doctor):
                                $hidden_class = ($doctor_index >= $load_more_initial) ? ' load-more-hidden' : '';
                                $doctor_index++;
                        ?>
                                <div class="col-xxl-4 col-md-6 doctor-grid-item<?php echo $hidden_class; ?>" 
                                    data-speciality="<?php echo htmlspecialchars($doctor['specialty'] ?? 'General Physician'); ?>" 
                                    data-gender="<?php echo htmlspecialchars($doctor['gender'] ?? 'Other'); ?>"
                                    data-district="<?php echo htmlspecialchars($doctor['district'] ?? ''); ?>">
                                    <div class="card">
                                        <div class="card-img card-img-hover doctor-profile-card-img">
                                            <?php
                                                $doctorImage = trim($doctor['profile_image'] ?? '') !== ''
                                                    ? $doctor['profile_image']
                                                    : 'assets/img/doctors/default-doctor.png';
                                            ?>
                                            <a href="doctor-profile.php?id=<?php echo $doctor['id']; ?>">
                                                <img src="<?php echo htmlspecialchars($doctorImage); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                                            </a>
                                            <div class="grid-overlay-item d-flex align-items-center justify-content-between">
                                                <span class="badge bg-orange"><i class="fa-solid fa-star me-1"></i><?php echo number_format($doctor['average_rating'] ?? 0, 1); ?></span>
                                                <a href="javascript:void(0)" class="fav-icon"><i class="fa fa-heart"></i></a>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="d-flex active-bar align-items-center justify-content-between p-3">
                                                <a href="doctor-profile.php?id=<?php echo $doctor['id']; ?>" class="text-indigo fw-medium fs-14"><?php echo htmlspecialchars($doctor['specialty'] ?? 'General Physician'); ?></a>
                                                <span class="badge <?php echo ($doctor['is_available'] ?? false) ? 'bg-success-light' : 'bg-danger-light'; ?> d-inline-flex align-items-center">
                                                    <i class="fa-solid fa-circle fs-5 me-1"></i>
                                                    <?php echo ($doctor['is_available'] ?? false) ? 'Available' : 'Unavailable'; ?>
                                                </span>
                                            </div>
                                            <div class="p-3 pt-0">
                                                <div class="doctor-info-detail mb-3 pb-3">
                                                    <h3 class="mb-1"><a href="doctor-profile.php?id=<?php echo $doctor['id']; ?>">Dr. <?php echo htmlspecialchars($doctor['name']); ?></a></h3>
                                                    <div class="d-flex align-items-center">
                                                        <?php
                                                        $location_parts = array_filter([$doctor['district'] ?? '', $doctor['city'] ?? '', $doctor['state'] ?? '']);
                                                        $location_display = !empty($location_parts) ? implode(', ', $location_parts) : 'Dhaka, Bangladesh';
                                                        ?>
                                                        <p class="d-flex align-items-center mb-0 fs-14"><i class="isax isax-location me-2"></i><?php echo htmlspecialchars($location_display); ?></p>
                                                        <i class="fa-solid fa-circle fs-5 text-primary mx-2 me-1"></i>
                                                        <span class="fs-14 fw-medium">30 Min</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <p class="mb-1">Consultation Fees</p>
                                                        <h3 class="text-orange">৳<?php echo number_format($doctor['consultation_fee'] ?? 100, 0); ?></h3>
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
                            <?php if (count($doctors) > $load_more_initial): ?>
                            <div class="col-12">
                                <div class="text-center mt-4 mb-4" id="load-more-doctors-wrap">
                                    <a href="javascript:void(0);" class="btn btn-md btn-primary-gradient d-inline-flex align-items-center rounded-pill load-more-doctors-btn">
                                        <i class="isax isax-d-cube-scan5 me-2"></i>
                                        Load More Doctors
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var loadMoreBtn = document.querySelector('.load-more-doctors-btn');
        if (!loadMoreBtn) return;
        var perPage = 12;
        loadMoreBtn.addEventListener('click', function() {
            var hidden = document.querySelectorAll('.doctor-grid-item.load-more-hidden');
            var toShow = Math.min(perPage, hidden.length);
            for (var i = 0; i < toShow; i++) {
                hidden[i].classList.remove('load-more-hidden');
            }
            if (document.querySelectorAll('.doctor-grid-item.load-more-hidden').length === 0) {
                document.getElementById('load-more-doctors-wrap').style.display = 'none';
            }
            var visibleCount = document.querySelectorAll('.doctor-grid-item:not(.load-more-hidden)').length;
            var countEl = document.getElementById('doctor-count');
            if (countEl) countEl.textContent = visibleCount;
        });
    })();
    </script>

    <!-- Rangeslider JS -->
    <script src="assets/plugins/ion-rangeslider/js/ion.rangeSlider.js"></script>
    <script src="assets/plugins/ion-rangeslider/js/custom-rangeslider.js"></script>
    <script src="assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>

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
    .search-input {
        position: relative;
    }
    </style>

    <script>
    (function() {
        var debounceTimers = {};

        var iconMap = {
            doctor:   'fa-user-doctor',
            specialty:'fa-stethoscope',
            district: 'fa-map-marker-alt',
            city:     'fa-city'
        };

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
                div.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    onSelect(item.label);
                });
                drop.appendChild(div);
            });

            drop.style.display = 'block';
            drop._activeIdx = -1;
        }

        function hideDropdown(drop) {
            drop.style.display = 'none';
            drop._activeIdx = -1;
        }

        function initAutocomplete(inputEl, apiType) {
            var drop = createDropdown(inputEl);
            var form = inputEl.closest('form');

            inputEl.addEventListener('input', function() {
                var q = this.value.trim();
                clearTimeout(debounceTimers[apiType]);
                if (q.length < 2) { hideDropdown(drop); return; }

                debounceTimers[apiType] = setTimeout(function() {
                    fetch('api/search-suggestions.php?type=' + apiType + '&q=' + encodeURIComponent(q))
                        .then(function(r) { return r.json(); })
                        .then(function(items) {
                            showDropdown(drop, items, q, function(val) {
                                inputEl.value = val;
                                hideDropdown(drop);
                                if (form) form.submit();
                            });
                        })
                        .catch(function() { hideDropdown(drop); });
                }, 220);
            });

            inputEl.addEventListener('keydown', function(e) {
                if (drop.style.display === 'none') return;
                var items = drop.querySelectorAll('.suggestion-item');
                var idx = drop._activeIdx !== undefined ? drop._activeIdx : -1;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    idx = Math.min(idx + 1, items.length - 1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    idx = Math.max(idx - 1, -1);
                } else if (e.key === 'Enter') {
                    if (idx >= 0 && items[idx]) {
                        e.preventDefault();
                        inputEl.value = items[idx].textContent.trim();
                        hideDropdown(drop);
                        if (form) form.submit();
                        return;
                    }
                    hideDropdown(drop);
                    return;
                } else if (e.key === 'Escape') {
                    hideDropdown(drop);
                    return;
                } else { return; }

                items.forEach(function(el, i) {
                    el.classList.toggle('active', i === idx);
                });
                drop._activeIdx = idx;
            });

            inputEl.addEventListener('blur', function() {
                setTimeout(function() { hideDropdown(drop); }, 150);
            });

            inputEl.addEventListener('focus', function() {
                if (this.value.trim().length >= 2) this.dispatchEvent(new Event('input'));
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            var searchInput   = document.querySelector('.doctors-search-box input[name="search"]');
            var locationInput = document.querySelector('.doctors-search-box input[name="location"]');
            if (searchInput)   initAutocomplete(searchInput,   'search');
            if (locationInput) initAutocomplete(locationInput, 'location');
        });
    })();
    </script>

<?php include 'footer.php'; ?>