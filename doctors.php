<?php
session_start();
include 'header.php';
require_once 'php/config.php';

$search_keyword = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
$location_filter = isset($_GET['location']) ? trim((string)$_GET['location']) : '';
$date_selected = isset($_GET['date']) ? trim((string)$_GET['date']) : '';
$availability_filter = isset($_GET['availability']) ? (int)$_GET['availability'] : 0;
$sort_by = isset($_GET['sort']) ? trim((string)$_GET['sort']) : '';

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

    if ($date_selected !== '' || $availability_filter === 1) {
        $where_clauses[] = 'dp.is_available = 1';
    }

    // Determine sorting
    $order_by = 'd.created_at DESC'; // Default
    if ($sort_by === 'price_low') {
        $order_by = 'dp.consultation_fee ASC';
    } elseif ($sort_by === 'price_high') {
        $order_by = 'dp.consultation_fee DESC';
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
            dp.degrees,
            (SELECT COUNT(*) FROM doctor_experiences de WHERE de.doctor_id = d.id) as experience_count,
            (SELECT COUNT(*) FROM doctor_education ded WHERE ded.doctor_id = d.id) as education_count,
            (SELECT COUNT(*) FROM doctor_awards da WHERE da.doctor_id = d.id) as awards_count
        FROM doctors d
        LEFT JOIN doctor_profiles dp ON d.id = dp.doctor_id
        WHERE " . implode(' AND ', $where_clauses) . "
        ORDER BY " . $order_by . "
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

$hm_asset_v = '1';   // change this number after editing the CSS/JS if the browser keeps showing the old design
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/home-modern.css?v=<?php echo $hm_asset_v; ?>">
<link rel="stylesheet" href="assets/css/pages-modern.css?v=<?php echo $hm_asset_v; ?>">

<main class="hm">
<script>document.documentElement.classList.add('hm-js');</script>

<!-- ===================== HERO + SEARCH ===================== -->
<section class="hm-hero pgx-center pgx-hero-pad" aria-label="Search doctors">
    <div class="hm-wrap hm-hero-grid">
        <div class="hm-hero-copy" style="width:100%">
            <span class="hm-kicker hm-reveal"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i> Our Doctors</span>
            <h1 class="hm-reveal" style="--d:.05s;margin-top:12px">Search <span class="hm-grad">Doctors</span></h1>

            <div class="pgx-dsearch-wrap hm-reveal" style="--d:.1s">
                <form class="hm-search pgx-dsearch" action="doctors.php" method="GET" role="search">
                    <div class="hm-field">
                        <i class="isax isax-hospital5" aria-hidden="true"></i>
                        <input type="text" name="search" class="hm-input" placeholder="Search for Doctors" value="<?php echo htmlspecialchars($search_keyword); ?>" autocomplete="off" aria-label="Search for doctors">
                    </div>
                    <div class="hm-field">
                        <i class="isax isax-location5" aria-hidden="true"></i>
                        <input type="text" name="location" class="hm-input" placeholder="Location" value="<?php echo htmlspecialchars($location_filter); ?>" autocomplete="off" aria-label="Location">
                    </div>
                    <div class="hm-field">
                        <i class="isax isax-calendar-tick5" aria-hidden="true"></i>
                        <input type="text" name="date" class="hm-input datetimepicker" placeholder="Date" value="<?php echo htmlspecialchars($date_selected); ?>" autocomplete="off" aria-label="Date">
                    </div>
                    <button class="hm-btn" type="submit"><i class="isax isax-search-normal-15" aria-hidden="true"></i> Search</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ===================== FILTERS + DOCTORS ===================== -->
<section class="hm-section" style="padding-top:8px" aria-label="Doctors list">
    <div class="hm-wrap">
        <button type="button" class="hm-btn is-ghost pgx-filter-toggle" id="pgxFilterToggle" aria-expanded="false" aria-controls="pgxFilter">
            <i class="fa-solid fa-sliders" aria-hidden="true"></i> Filter Doctors
        </button>

        <div class="pgx-dlayout">
            <!-- ---------- Filters ---------- -->
            <aside class="pgx-filter is-collapsed" id="pgxFilter" aria-label="Filter doctors">
                <div class="pgx-filter-head">
                    <h4>Filter Doctors</h4>
                    <a href="#" class="clear-all-filters">Clear All</a>
                </div>

                <div class="pgx-fsec">
                    <button type="button" class="pgx-fsec-btn" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                        <span>Specialities</span><i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div id="collapse1" class="collapse show">
                        <div class="pgx-fsec-body">
                            <div class="specialities-scroll">
                                <?php if (!empty($final_specialities)): $speciality_index = 2; arsort($final_specialities); foreach ($final_specialities as $speciality => $count): $checkbox_id = 'checkebox-sm' . $speciality_index; ?>
                                    <div class="pgx-frow">
                                        <div class="form-check">
                                            <input class="form-check-input speciality-filter" type="checkbox" value="<?php echo htmlspecialchars($speciality); ?>" id="<?php echo $checkbox_id; ?>" data-speciality="<?php echo htmlspecialchars($speciality); ?>">
                                            <label class="form-check-label" for="<?php echo $checkbox_id; ?>"><?php echo htmlspecialchars($speciality); ?></label>
                                        </div>
                                        <span class="filter-badge"><?php echo $count; ?></span>
                                    </div>
                                <?php $speciality_index++; endforeach; ?>
                                <?php else: ?>
                                    <div class="pgx-frow">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="checkebox-sm2" checked="">
                                            <label class="form-check-label" for="checkebox-sm2">No Specialities Available</label>
                                        </div>
                                        <span class="filter-badge">0</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pgx-fsec">
                    <button type="button" class="pgx-fsec-btn" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                        <span>Gender</span><i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div id="collapse2" class="collapse show">
                        <div class="pgx-fsec-body">
                            <?php foreach (['Male', 'Female'] as $idx => $gender): $cb_id = 'gender-filter-' . strtolower($gender); $count = $gender_counts[$gender] ?? 0; ?>
                                <div class="pgx-frow">
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

                <div class="pgx-fsec">
                    <button type="button" class="pgx-fsec-btn" data-bs-toggle="collapse" data-bs-target="#collapseDistrict" aria-expanded="true" aria-controls="collapseDistrict">
                        <span>District</span><i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div id="collapseDistrict" class="collapse show">
                        <div class="pgx-fsec-body">
                            <div class="specialities-scroll">
                                <?php if (!empty($district_counts)): ksort($district_counts); $dist_idx = 1; foreach ($district_counts as $district => $count): $cb_id = 'district-filter-' . $dist_idx; ?>
                                    <div class="pgx-frow">
                                        <div class="form-check">
                                            <input class="form-check-input district-filter" type="checkbox" value="<?php echo htmlspecialchars($district); ?>" id="<?php echo $cb_id; ?>">
                                            <label class="form-check-label" for="<?php echo $cb_id; ?>"><?php echo htmlspecialchars($district); ?></label>
                                        </div>
                                        <span class="filter-badge"><?php echo $count; ?></span>
                                    </div>
                                <?php $dist_idx++; endforeach; ?>
                                <?php else: ?>
                                    <div class="pgx-frow">
                                        <div class="form-check"><label class="form-check-label">No districts available</label></div>
                                        <span class="filter-badge">0</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pgx-fsec">
                    <button type="button" class="pgx-fsec-btn" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
                        <span>Reviews</span><i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div id="collapse9" class="collapse show">
                        <div class="pgx-fsec-body">
                        <div class="pgx-frow">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm38" checked="">
                                <label class="form-check-label" for="checkebox-sm38"><span class="pgx-stars-s"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span> 5 Star</label>
                            </div>
                        </div>
                        <div class="pgx-frow">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm39">
                                <label class="form-check-label" for="checkebox-sm39"><span class="pgx-stars-s"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span> 4 Star</label>
                            </div>
                        </div>
                        <div class="pgx-frow">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm40">
                                <label class="form-check-label" for="checkebox-sm40"><span class="pgx-stars-s"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span> 3 Star</label>
                            </div>
                        </div>
                        <div class="pgx-frow">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm41">
                                <label class="form-check-label" for="checkebox-sm41"><span class="pgx-stars-s"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span> 2 Star</label>
                            </div>
                        </div>
                        <div class="pgx-frow">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkebox-sm42">
                                <label class="form-check-label" for="checkebox-sm42"><span class="pgx-stars-s"><i class="fa-solid fa-star"></i></span> 1 Star</label>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ---------- Results ---------- -->
            <div>
                <div class="pgx-dtop">
                    <h3>Showing <span id="doctor-count"><?php echo count($doctors); ?></span> Doctors For You</h3>
                    <div class="pgx-dtools">
                        <div class="pgx-avail">
                            <span>Availability</span>
                            <label class="pgx-switch" for="status_6">
                                <input type="checkbox" id="status_6" class="check" <?php echo ($availability_filter === 1) ? 'checked' : ''; ?> aria-label="Show available doctors only">
                                <span></span>
                            </label>
                        </div>
                        <div class="dropdown pgx-sort">
                            <a class="dropdown-toggle sort-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);" aria-expanded="false">
                                <span>Sort By</span>
                                <?php if ($sort_by === 'price_low') echo 'Price (Low to High)'; elseif ($sort_by === 'price_high') echo 'Price (High to Low)'; else echo 'Latest'; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="price_low">Price (Low to High)</a>
                                <a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="price_high">Price (High to Low)</a>
                                <a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="latest">Latest</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row pgx-dgrid" id="doctors-grid-row">
                    <?php if (empty($doctors)): ?>
                        <div class="pgx-dempty">No doctors found.</div>
                    <?php else: $load_more_initial = 12; $doctor_index = 0; foreach ($doctors as $doctor): $hidden_class = ($doctor_index >= $load_more_initial) ? ' load-more-hidden' : ''; $doctor_index++; ?>
                        <?php
                        $doctorImage = trim($doctor['profile_image'] ?? '') !== '' ? $doctor['profile_image'] : 'assets/img/doctors/default-doctor.png';
                        $location_parts = array_filter([$doctor['district'] ?? '', $doctor['city'] ?? '', $doctor['state'] ?? '']);
                        $location_display = !empty($location_parts) ? implode(', ', $location_parts) : 'Dhaka, Bangladesh';
                        $is_available = ($doctor['is_available'] ?? false);
                        ?>
                        <article class="hm-doc doctor-grid-item<?php echo $hidden_class; ?>" data-speciality="<?php echo htmlspecialchars($doctor['specialty'] ?? 'General Physician'); ?>" data-gender="<?php echo htmlspecialchars($doctor['gender'] ?? 'Other'); ?>" data-district="<?php echo htmlspecialchars($doctor['district'] ?? ''); ?>">
                            <a class="hm-doc-img" href="doctor-profile.php?id=<?php echo $doctor['id']; ?>" tabindex="-1" aria-hidden="true">
                                <img src="<?php echo htmlspecialchars($doctorImage); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>" loading="lazy">
                                <span class="hm-avail <?php echo $is_available ? 'is-on' : 'is-off'; ?>"><i class="fa-solid fa-circle"></i> <?php echo $is_available ? 'Available' : 'Unavailable'; ?></span>
                            </a>
                            <div class="hm-doc-body">
                                <div class="hm-doc-top">
                                    <div>
                                        <a class="hm-doc-name" href="doctor-profile.php?id=<?php echo $doctor['id']; ?>">Dr. <?php echo htmlspecialchars($doctor['name']); ?></a>
                                        <p class="hm-doc-spec"><?php $speciality_display = $doctor['specialty'] ?? 'Medical Doctor'; echo htmlspecialchars(!empty($speciality_display) ? implode(', ', array_map('trim', explode(',', $speciality_display))) : 'Medical Doctor'); ?></p>
                                    </div>
                                    <span class="hm-rate"><i class="fa-solid fa-star"></i> <?php echo number_format($doctor['average_rating'] ?? 0, 1); ?> <small>(<?php echo $doctor['total_reviews'] ?? 0; ?>)</small></span>
                                </div>
                                <?php if (!empty(trim($doctor['degrees'] ?? ''))): ?>
                                    <p class="pgx-degrees"><?php echo htmlspecialchars(trim($doctor['degrees'])); ?></p>
                                <?php endif; ?>
                                <ul class="pgx-dmeta">
                                    <li><i class="isax isax-archive-14" aria-hidden="true"></i><span><?php echo $doctor['experience_years'] ?? 5; ?> Years of Experience</span></li>
                                    <li><i class="isax isax-like-1" aria-hidden="true"></i><span><?php echo number_format($doctor['average_rating'] ?? 0, 1); ?>% (<?php echo $doctor['total_reviews'] ?? 0; ?> Votes)</span></li>
                                    <li><i class="isax isax-language-circle" aria-hidden="true"></i><span><?php echo htmlspecialchars($doctor['languages_spoken'] ?? 'English, Bengali'); ?></span></li>
                                </ul>
                                <div class="hm-doc-loc"><i class="isax isax-location" aria-hidden="true"></i><span><?php echo htmlspecialchars($location_display); ?></span><i class="fa-solid fa-circle" style="font-size:4px;color:#0c77c9;margin:0 4px" aria-hidden="true"></i><span>30 Min</span></div>
                                <div class="pgx-dfoot">
                                    <div>
                                        <small>Consultation Fees</small>
                                        <strong>৳<?php echo number_format($doctor['consultation_fee'] ?? 100, 0); ?></strong>
                                    </div>
                                    <a href="booking.php?doctor_id=<?php echo $doctor['id']; ?>" class="hm-btn"><i class="isax isax-calendar-1" aria-hidden="true"></i> Book Now</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                        <?php if (count($doctors) > $load_more_initial): ?>
                            <div class="pgx-more">
                                <div id="load-more-doctors-wrap">
                                    <a href="javascript:void(0);" class="hm-btn is-ghost load-more-doctors-btn"><i class="isax isax-d-cube-scan5" aria-hidden="true"></i> Load More Doctors</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
</main>

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
            var searchInput   = document.querySelector('.pgx-dsearch input[name="search"]');
            var locationInput = document.querySelector('.pgx-dsearch input[name="location"]');
            if (searchInput)   initAutocomplete(searchInput,   'search');
            if (locationInput) initAutocomplete(locationInput, 'location');

            // --- Availability & Sort Logic ---
            var availabilityToggle = document.getElementById('status_6');
            var sortItems = document.querySelectorAll('.sort-item');

            function updateUrl(params) {
                var url = new URL(window.location.href);
                for (var key in params) {
                    if (params[key] === null) {
                        url.searchParams.delete(key);
                    } else {
                        url.searchParams.set(key, params[key]);
                    }
                }
                window.location.href = url.toString();
            }

            if (availabilityToggle) {
                availabilityToggle.addEventListener('change', function() {
                    updateUrl({ availability: this.checked ? 1 : null });
                });
            }

            sortItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    var sortVal = this.getAttribute('data-sort');
                    updateUrl({ sort: sortVal === 'latest' ? null : sortVal });
                });
            });
        });
    })();
    </script>
<script>
    // phone/tablet: show or hide the filter panel
    (function () {
        var btn = document.getElementById('pgxFilterToggle'), box = document.getElementById('pgxFilter');
        if (!btn || !box) { return; }
        btn.addEventListener('click', function () {
            var open = box.classList.toggle('is-collapsed') === false;
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        function sync() { if (window.innerWidth > 991) { box.classList.remove('is-collapsed'); } else if (btn.getAttribute('aria-expanded') !== 'true') { box.classList.add('is-collapsed'); } }
        window.addEventListener('resize', sync); sync();
    })();
</script>

<?php include 'footer.php'; ?>
