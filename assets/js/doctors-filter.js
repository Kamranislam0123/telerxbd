/**
 * Doctors page: Specialities filter – show/hide doctor cards by selected checkboxes.
 * Targets both list (.doctor-list-card) and grid (.doctor-grid-item) layouts.
 */
(function() {
    var doctorCardSelector = '.doctor-list-card, .doctor-grid-item';

    function filterDoctorsBySpeciality() {
        var selectedSpecialities = [];
        jQuery('.speciality-filter:checked').each(function() {
            var speciality = jQuery(this).val().trim();
            if (speciality !== '') {
                selectedSpecialities.push(speciality);
            }
        });

        if (selectedSpecialities.length === 0) {
            jQuery(doctorCardSelector).fadeIn(300);
            updateDoctorCount();
            return;
        }

        jQuery(doctorCardSelector).each(function() {
            var doctorSpecialityStr = jQuery(this).data('speciality') || '';
            doctorSpecialityStr = doctorSpecialityStr.trim();
            if (doctorSpecialityStr === '' || doctorSpecialityStr === 'null') {
                doctorSpecialityStr = 'General Physician';
            }
            var doctorSpecialities = doctorSpecialityStr.split(',').map(function(s) {
                return s.trim();
            }).filter(function(s) {
                return s !== '';
            });
            if (doctorSpecialities.length === 0) {
                doctorSpecialities = ['General Physician'];
            }
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
            if (matches) {
                jQuery(this).fadeIn(300);
            } else {
                jQuery(this).fadeOut(300);
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
                    '<p class="text-muted">No doctors found matching the selected specialities.</p>' +
                    '</div>'
                );
            }
        } else {
            jQuery('.no-doctors-message').remove();
        }
    }

    jQuery(document).ready(function() {
        jQuery(document).on('change', '.speciality-filter', function() {
            filterDoctorsBySpeciality();
        });
        jQuery(document).on('click', '.clear-all-filters', function(e) {
            e.preventDefault();
            jQuery('.speciality-filter').prop('checked', false);
            jQuery(doctorCardSelector).fadeIn(300);
            updateDoctorCount();
        });
        updateDoctorCount();
    });
})();
