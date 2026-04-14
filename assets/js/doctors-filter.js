/**
 * Doctors page: Specialities + Gender filter – show/hide doctor cards by selected checkboxes.
 * Targets both list (.doctor-list-card) and grid (.doctor-grid-item) layouts.
 */
(function() {
    var doctorCardSelector = '.doctor-list-card, .doctor-grid-item';

    function getSelectedGenders() {
        var selected = [];
        jQuery('.gender-filter:checked').each(function() {
            var g = jQuery(this).val().trim();
            if (g !== '') selected.push(g);
        });
        return selected;
    }

    function getSelectedSpecialities() {
        var selected = [];
        jQuery('.speciality-filter:checked').each(function() {
            var s = jQuery(this).val().trim();
            if (s !== '') selected.push(s);
        });
        return selected;
    }

    function getSelectedDistricts() {
        var selected = [];
        jQuery('.district-filter:checked').each(function() {
            var d = jQuery(this).val().trim();
            if (d !== '') selected.push(d);
        });
        return selected;
    }

    function filterDoctors() {
        var selectedSpecialities = getSelectedSpecialities();
        var selectedGenders = getSelectedGenders();
        var selectedDistricts = getSelectedDistricts();

        jQuery(doctorCardSelector).each(function() {
            var $card = jQuery(this);
            var showBySpeciality = true;
            var showByGender = true;
            var showByDistrict = true;

            if (selectedSpecialities.length > 0) {
                var doctorSpecialityStr = ($card.data('speciality') || '').trim();
                if (doctorSpecialityStr === '' || doctorSpecialityStr === 'null') doctorSpecialityStr = 'General Physician';
                var doctorSpecialities = doctorSpecialityStr.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s !== ''; });
                if (doctorSpecialities.length === 0) doctorSpecialities = ['General Physician'];
                showBySpeciality = false;
                for (var i = 0; i < selectedSpecialities.length; i++) {
                    for (var j = 0; j < doctorSpecialities.length; j++) {
                        if (doctorSpecialities[j].toLowerCase() === selectedSpecialities[i].toLowerCase()) {
                            showBySpeciality = true;
                            break;
                        }
                    }
                    if (showBySpeciality) break;
                }
            }

            if (selectedGenders.length > 0) {
                var doctorGender = ($card.data('gender') || 'Other').trim();
                showByGender = selectedGenders.indexOf(doctorGender) !== -1;
            }

            if (selectedDistricts.length > 0) {
                var doctorDistrict = ($card.data('district') || '').trim();
                showByDistrict = selectedDistricts.indexOf(doctorDistrict) !== -1;
            }

            if (showBySpeciality && showByGender && showByDistrict) {
                $card.fadeIn(300);
            } else {
                $card.fadeOut(300);
            }
        });
        updateDoctorCount();
    }

    function updateDoctorCount() {
        var visibleCount = jQuery(doctorCardSelector + ':visible').length;
        jQuery('#doctor-count').text(visibleCount);
        if (visibleCount === 0) {
            if (jQuery('.no-doctors-message').length === 0) {
                var container = jQuery(doctorCardSelector).first().closest('.row');
                if (!container.length) container = jQuery('.col-lg-12').first();
                container.append(
                    '<div class="text-center no-doctors-message mt-4">' +
                    '<p class="text-muted">No doctors found matching the selected filters.</p>' +
                    '</div>'
                );
            }
        } else {
            jQuery('.no-doctors-message').remove();
        }
    }

    jQuery(document).ready(function() {
        jQuery(document).on('change', '.speciality-filter', filterDoctors);
        jQuery(document).on('change', '.gender-filter', filterDoctors);
        jQuery(document).on('change', '.district-filter', filterDoctors);
        jQuery(document).on('click', '.clear-all-filters', function(e) {
            e.preventDefault();
            jQuery('.speciality-filter, .gender-filter, .district-filter').prop('checked', false);
            jQuery(doctorCardSelector).each(function() {
                var $el = jQuery(this);
                if ($el.hasClass('load-more-hidden')) $el.hide();
                else $el.fadeIn(300);
            });
            updateDoctorCount();
        });
        updateDoctorCount();
    });
})();
