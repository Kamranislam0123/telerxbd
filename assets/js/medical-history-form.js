/**
 * TeleRx Bangladesh - Patient Medical History Form
 * Tabs, "Save and Next", Tab 1 behaviour (age, age group, BMI, show/hide fields, complaint search).
 *
 * Needs (already loaded by footer.php): jQuery, moment, bootstrap-datetimepicker, select2.
 * The page gives us the saved data in window.MH_STATE.
 */

/* ---------------------------------------------------------------------
 * 1) PURE CALCULATIONS  (same steps as php/mh-helpers.php)
 * ------------------------------------------------------------------- */
var MHCalc = (function () {
    'use strict';

    function daysInMonth(y, m) {
        if (m === 2) {
            return ((y % 4 === 0 && y % 100 !== 0) || y % 400 === 0) ? 29 : 28;
        }
        return [4, 6, 9, 11].indexOf(m) !== -1 ? 30 : 31;
    }

    /**
     * @param {string} iso      date of birth "YYYY-MM-DD"
     * @param {string} [today]  "YYYY-MM-DD" (only used for testing; normally today)
     */
    function ageInfo(iso, today) {
        var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso || '');
        if (!m) { return null; }
        var by = +m[1], bm = +m[2], bd = +m[3];
        var chk = new Date(Date.UTC(by, bm - 1, bd));
        if (chk.getUTCFullYear() !== by || chk.getUTCMonth() !== bm - 1 || chk.getUTCDate() !== bd || by < 1900) {
            return null;
        }
        var ty, tm, td;
        if (today) {
            var t = /^(\d{4})-(\d{2})-(\d{2})$/.exec(today);
            ty = +t[1]; tm = +t[2]; td = +t[3];
        } else {
            var now = new Date();
            ty = now.getFullYear(); tm = now.getMonth() + 1; td = now.getDate();
        }
        var totalDays = Math.round((Date.UTC(ty, tm - 1, td) - Date.UTC(by, bm - 1, bd)) / 86400000);
        if (totalDays < 0) { return null; }

        var years = ty - by, months = tm - bm, days = td - bd;
        if (days < 0) {
            months--;
            var pm = tm - 1, py = ty;
            if (pm < 1) { pm = 12; py--; }
            days += daysInMonth(py, pm);
        }
        if (months < 0) { years--; months += 12; }

        var label;
        if (years >= 1) { label = years + (years === 1 ? ' Year' : ' Years'); }
        else if (months >= 1) { label = months + (months === 1 ? ' Month' : ' Months'); }
        else { label = days + (days === 1 ? ' Day' : ' Days'); }

        var group;
        if (totalDays <= 28) { group = 'Neonatal'; }
        else if (years < 1) { group = 'Infant'; }
        else if (years < 5) { group = 'Preschool'; }
        else if (years < 12) { group = 'School-age'; }
        else if (years < 18) { group = 'Adolescent'; }
        else if (years < 40) { group = 'Young adult'; }
        else if (years < 65) { group = 'Middle-aged adult'; }
        else { group = 'Older adult'; }

        return { years: years, months: months, days: days, totalDays: totalDays, label: label, group: group };
    }

    /** Returns the text to show in the BMI box ('' when not enough data). */
    function bmiText(kg, ft, inch, ageYears) {
        var totalIn = (ft * 12) + inch;
        if (!(kg > 0) || !(totalIn > 0)) { return ''; }
        var meters = totalIn * 0.0254;
        var bmi = Math.round((kg / (meters * meters)) * 10) / 10;
        if (ageYears === null || ageYears === undefined) { return bmi.toFixed(1); }
        if (ageYears < 2) { return 'Not applicable (under 2 years)'; }
        if (ageYears < 18) { return bmi.toFixed(1) + ' (use growth chart)'; }
        var cat = bmi < 18.5 ? 'Underweight' : (bmi < 25 ? 'Normal' : (bmi < 30 ? 'Overweight' : 'Obese'));
        return bmi.toFixed(1) + ' (' + cat + ')';
    }

    return { ageInfo: ageInfo, bmiText: bmiText };
})();

if (typeof window !== 'undefined') { window.MHCalc = MHCalc; }

/* ---------------------------------------------------------------------
 * 2) THE FORM
 * ------------------------------------------------------------------- */
(function ($) {
    'use strict';

    if (!$ || typeof window === 'undefined' || !window.MH_STATE) { return; }

    var S = window.MH_STATE;
    var total = S.tabs.length;
    var saved = {};
    $.each(S.saved || [], function (i, n) { saved[n] = true; });

    var active = 0;
    var dirty = false;
    var hydrating = true;
    var toastTimer = null;
    var hooks = {};            // per-tab extra behaviour: hooks[tabNumber] = { init, afterHydrate, validate }

    /* ---------- small helpers ---------- */
    function $panel(n) { return $('#mhf-panel-' + n); }
    function $form(n) { return $('.mhf-form[data-section="' + n + '"]'); }

    function toast(msg, type) {
        var $t = $('#mhf-toast');
        $t.removeClass('is-show is-error is-ok').addClass(type === 'error' ? 'is-error' : 'is-ok').text(msg);
        void $t[0].offsetWidth;   // restart the animation
        $t.addClass('is-show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { $t.removeClass('is-show'); }, type === 'error' ? 5200 : 2600);
    }

    function scrollToCard() {
        var $c = $('.mhf-card');
        if ($c.length) {
            $('html, body').animate({ scrollTop: Math.max(0, $c.offset().top - 110) }, 220);
        }
    }

    /* ---------- tabs ---------- */
    function maxOpen() {
        var n = 1;
        while (n <= total && saved[n]) { n++; }
        return Math.min(n, total);
    }

    function refreshTabs() {
        var mo = maxOpen();
        $('.mhf-tab').each(function () {
            var $t = $(this);
            var n = +$t.data('section');
            var locked = n > mo;
            $t.toggleClass('is-active', n === active)
              .toggleClass('is-done', !!saved[n])
              .toggleClass('is-locked', locked)
              .attr('aria-selected', n === active ? 'true' : 'false')
              .attr('aria-disabled', locked ? 'true' : 'false')
              .attr('tabindex', n === active ? '0' : '-1');
        });
    }

    function openTab(n, quiet) {
        n = +n;
        if (n < 1 || n > total) { return; }
        if (n > maxOpen()) {
            toast('Please complete and save Tab ' + maxOpen() + ' first.', 'error');
            return;
        }
        active = n;
        $('.mhf-panel').prop('hidden', true);
        $panel(n).prop('hidden', false);
        refreshTabs();
        var tabEl = document.getElementById('mhf-tab-' + n);
        if (tabEl && tabEl.scrollIntoView) {
            try { tabEl.scrollIntoView({ block: 'nearest', inline: 'nearest' }); } catch (e) { /* old browser */ }
        }
        if (!quiet) { scrollToCard(); }
    }

    function showFinish() {
        active = 0;
        $('.mhf-panel').prop('hidden', true);
        $('#mhf-finish').prop('hidden', false);
        refreshTabs();
        scrollToCard();
    }

    /* ---------- errors ---------- */
    function clearErrors(n) {
        var $f = $form(n);
        $f.find('.mhf-error').text('');
        $f.find('.mhf-field').removeClass('has-error');
        $f.find('[aria-invalid]').removeAttr('aria-invalid');
    }

    function showErrors(n, errs) {
        clearErrors(n);
        var $f = $form(n);
        var firstField = null;
        $.each(errs, function (name, msg) {
            var $msg = $f.find('.mhf-error[data-error-for="' + name + '"]').first();
            if (!$msg.length) { return; }
            $msg.text(msg);
            var $field = $msg.closest('.mhf-field');
            $field.addClass('has-error');
            $f.find('[name="' + name + '"], [name="' + name + '[]"]').attr('aria-invalid', 'true');
            if (!firstField) { firstField = { $field: $field, name: name }; }
        });
        if (firstField) {
            var el = firstField.$field.find('input:visible:not([readonly]), select:visible, .select2-search__field').first();
            if (!el.length) { el = firstField.$field.find('input, select').first(); }
            try { el.trigger('focus'); } catch (e) { /* ignore */ }
            var top = firstField.$field.offset().top - 150;
            $('html, body').animate({ scrollTop: Math.max(0, top) }, 200);
        }
    }

    // typing / choosing clears that field's error
    $(document).on('input change', '.mhf-form .mhf-field', function () {
        var $field = $(this);
        if ($field.hasClass('has-error')) {
            $field.removeClass('has-error').find('.mhf-error').text('');
            $field.find('[aria-invalid]').removeAttr('aria-invalid');
        }
    });

    // Enter key must never submit/reload the page
    $(document).on('submit', '.mhf-form', function (e) { e.preventDefault(); });

    // remember unsaved changes
    $(document).on('input change', '.mhf-form', function () { if (!hydrating) { dirty = true; } });
    window.addEventListener('beforeunload', function (e) {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });

    /* ---------- validation (generic part) ---------- */
    function collectErrors(n) {
        var errs = {};
        $form(n).find('.mhf-field[data-required]').each(function () {
            var $field = $(this);
            if ($field.hasClass('mhf-hidden')) { return; }
            $field.find('[name]').each(function () {
                var $c = $(this);
                if ($c.is(':disabled')) { return; }
                var name = ($c.attr('name') || '').replace('[]', '');
                var v = $c.val();
                var empty = $.isArray(v) ? v.length === 0 : $.trim(v === null || v === undefined ? '' : String(v)) === '';
                if (empty) {
                    errs[name] = ($c.is('select') && !$c.prop('multiple')) ? 'Select an option.' : 'This field is required.';
                }
            });
        });
        if (hooks[n] && hooks[n].validate) {
            hooks[n].validate($form(n), errs);
        }
        return errs;
    }

    /* ---------- hydrate (fill the fields with saved answers) ---------- */
    function hydrate(n, data) {
        var $f = $form(n);
        $.each(data || {}, function (key, val) {
            if ($.isArray(val)) {
                var $multi = $f.find('[name="' + key + '[]"]');
                if ($multi.length) {
                    $.each(val, function (i, item) {
                        var exists = false;
                        $multi.find('option').each(function () { if (this.value === item) { exists = true; } });
                        if (!exists) { $multi.append(new Option(item, item, false, false)); }
                    });
                    $multi.val(val).trigger('change');
                }
                return;
            }
            var $el = $f.find('[name="' + key + '"]');
            if ($el.length) { $el.val(val === null || val === undefined ? '' : String(val)); }
        });
        if (hooks[n] && hooks[n].afterHydrate) { hooks[n].afterHydrate($f); }
    }

    /* ---------- save ---------- */
    function setLoading($btn, on) {
        $btn.toggleClass('is-loading', on).prop('disabled', on);
    }

    function pdfUrl() { return S.urls.pdf + '?form_id=' + S.formId; }

    function refreshFormHeader() {
        var id = String(S.formId);
        while (id.length < 6) { id = '0' + id; }
        $('#mhf-formno').text('Form MH-' + id);
        var complete = S.status === 'completed';
        $('#mhf-status').text(complete ? 'Completed' : 'Draft').toggleClass('is-complete', complete).toggleClass('is-draft', !complete);
        $('#mhf-head-pdf').attr('href', pdfUrl()).toggleClass('mhf-hidden', !complete);
        $('#mhf-pdf-btn').attr('href', pdfUrl());
        if (window.history && history.replaceState) {
            history.replaceState(null, '', S.urls.page + '?id=' + S.formId);
        }
    }

    function save(n) {
        var $btn = $panel(n).find('[data-action="save"]');
        if ($btn.hasClass('is-loading')) { return; }

        var errs = collectErrors(n);
        if (!$.isEmptyObject(errs)) {
            showErrors(n, errs);
            toast('Please fix the highlighted fields.', 'error');
            return;
        }
        clearErrors(n);

        var fd = new FormData($form(n)[0]);
        fd.append('section', n);
        fd.append('form_id', S.formId || 0);
        fd.append('person', S.person || 'self');   // whose form (used only when the form is created)
        fd.append('csrf', S.csrf);

        setLoading($btn, true);
        $.ajax({
            url: S.urls.save, type: 'POST', data: fd, processData: false, contentType: false, dataType: 'json', cache: false
        }).done(function (res) {
            if (!res || !res.success) {
                toast((res && res.message) || 'Could not save. Please try again.', 'error');
                return;
            }
            S.formId = res.form_id;
            S.status = res.status || S.status;
            $.each(res.saved || [], function (i, no) { saved[no] = true; });
            dirty = false;
            refreshFormHeader();

            if (n === total && res.completed) {
                toast('All sections saved.', 'ok');
                showFinish();
            } else {
                toast('Tab ' + n + ' saved.', 'ok');
                openTab(n + 1);
            }
        }).fail(function (xhr) {
            var res = xhr.responseJSON;
            if (res && res.errors) {
                showErrors(n, res.errors);
            }
            toast((res && res.message) || 'Could not save. Check your internet connection and try again.', 'error');
        }).always(function () {
            setLoading($btn, false);
        });
    }

    /* ---------------------------------------------------------------------
     * TAB 1 - Patient Information
     * ------------------------------------------------------------------- */
    hooks[1] = (function () {
        var $dobDisplay, $dobIso, $age, $group, $bmi, $weight, $ft, $inch, $sex;
        var hasPicker = false;

        function setVisible($field, show, resetValue) {
            $field.toggleClass('mhf-hidden', !show);
            $field.find('input, select').prop('disabled', !show);
            if (!show) {
                $field.removeClass('has-error').find('.mhf-error').text('');
                if (resetValue !== undefined) { $field.find('input, select').val(resetValue); }
            }
        }

        // Pregnancy status: only when Sex = Female AND Marital Status = Yes
        function applySex(reset) {
            var show = ($sex.val() === 'Female' && $('#mh_marital_status').val() === 'Yes');
            setVisible($('#mh_pregnancy_field'), show, reset ? 'No' : undefined);
        }
        function applyOccupation(reset) {
            var others = $('#mh_occupation').val() === 'Others';
            setVisible($('#mh_occupation_other_field'), others, reset ? '' : undefined);
            if (others && reset) { $('#mh_occupation_other').trigger('focus'); }
        }
        function applyHazard(reset) {
            var yes = $('#mh_occupational_hazard').val() === 'Yes';
            setVisible($('#mh_hazard_details_field'), yes, reset ? '' : undefined);
            if (yes && reset) { $('#mh_hazard_details').trigger('focus'); }
        }

        function num(v) { var x = parseFloat(String(v).replace(',', '.')); return isNaN(x) ? null : x; }

        function updateAgeAndBmi() {
            var info = MHCalc.ageInfo($dobIso.val());
            $age.val(info ? info.label : '');
            $group.val(info ? info.group : '');

            var kg = num($weight.val());
            var ft = num($ft.val());
            var inch = num($inch.val());
            if (kg === null || (ft === null && inch === null)) { $bmi.val(''); return; }
            $bmi.val(MHCalc.bmiText(kg, ft === null ? 0 : ft, inch === null ? 0 : inch, info ? info.years : null));
        }

        function setDobFromIso(iso) {
            var m = window.moment ? window.moment(iso, 'YYYY-MM-DD', true) : null;
            if (hasPicker && m && m.isValid()) {
                $dobDisplay.data('DateTimePicker').date(m);
            } else if (!hasPicker) {
                $dobDisplay.val(iso);
            }
            $dobIso.val(m && m.isValid() ? iso : '');
        }

        function initDob() {
            hasPicker = !!($.fn.datetimepicker && window.moment);
            if (!hasPicker) {
                // Safety net: plain browser calendar
                $dobDisplay.attr('type', 'date').attr('max', new Date().toISOString().slice(0, 10));
                $dobDisplay.on('change', function () { $dobIso.val($dobDisplay.val()); updateAgeAndBmi(); });
                return;
            }
            $dobDisplay.datetimepicker({
                format: 'D MMM YYYY',
                extraFormats: ['D/M/YYYY', 'DD/MM/YYYY', 'D-M-YYYY', 'DD-MM-YYYY', 'D MMMM YYYY'],
                useCurrent: false,
                viewDate: window.moment('2000-01-01', 'YYYY-MM-DD'),   // calendar opens near the example date
                viewMode: 'years',
                allowInputToggle: true,
                minDate: window.moment('1900-01-01', 'YYYY-MM-DD'),
                maxDate: window.moment().endOf('day'),
                widgetParent: $('#mh_dob_wrap'),
                widgetPositioning: { horizontal: 'left', vertical: 'bottom' },
                icons: { up: 'fas fa-chevron-up', down: 'fas fa-chevron-down', next: 'fas fa-chevron-right', previous: 'fas fa-chevron-left' }
            });
            $dobDisplay.on('dp.change', function (e) {
                $dobIso.val(e.date ? e.date.format('YYYY-MM-DD') : '');
                if (!hydrating) { dirty = true; }
                $('[data-field="dob"]').trigger('change');
                updateAgeAndBmi();
            });
            $('#mh_dob_wrap .mhf-dob-icon').on('click', function () { $dobDisplay.trigger('focus'); });
        }

        function initComplaint() {
            if (!$.fn.select2) { return; }
            $('#mh_main_complaint').select2({
                width: '100%',
                placeholder: 'Type to search or add (e.g. Fever, Diabetes, যক্ষা)',
                tags: true,
                tokenSeparators: [','],
                closeOnSelect: true,
                dropdownParent: $('#mh_complaint_wrap'),
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') { return null; }
                    term = term.substring(0, 100);
                    return { id: term, text: term };
                },
                // put "your own text" LAST, so Enter picks the first matching name from the list
                insertTag: function (data, tag) { data.push(tag); },
                language: {
                    noResults: function () { return 'No match. Type the name and press Enter to add it.'; }
                }
            });
        }

        return {
            init: function () {
                $dobDisplay = $('#mh_dob_display'); $dobIso = $('#mh_dob');
                $age = $('#mh_age'); $group = $('#mh_age_group'); $bmi = $('#mh_bmi');
                $weight = $('#mh_weight_kg'); $ft = $('#mh_height_ft'); $inch = $('#mh_height_in');
                $sex = $('#mh_sex');

                initDob();
                initComplaint();

                $sex.add('#mh_marital_status').on('change', function () { applySex(true); });
                $('#mh_occupation').on('change', function () { applyOccupation(true); });
                $('#mh_occupational_hazard').on('change', function () { applyHazard(true); });
                $weight.add($ft).add($inch).on('input change', updateAgeAndBmi);

                applySex(false); applyOccupation(false); applyHazard(false);
            },

            afterHydrate: function () {
                var iso = $dobIso.val();
                if (iso) { setDobFromIso(iso); }
                applySex(false); applyOccupation(false); applyHazard(false);
                updateAgeAndBmi();
            },

            validate: function ($f, errs) {
                var friendly = {
                    patient_name: 'Enter the patient name.',
                    dob: 'Select the date of birth.',
                    weight_kg: 'Enter the weight in kg.',
                    height_ft: 'Enter the height in feet.',
                    height_in: 'Enter the height in inches.',
                    main_complaint: 'Add at least one main complaint.'
                };
                $.each(friendly, function (name, msg) { if (errs[name]) { errs[name] = msg; } });

                if (errs.dob === undefined && !MHCalc.ageInfo($dobIso.val())) {
                    errs.dob = 'Select a valid date of birth (it cannot be a future date).';
                }
                if ($dobDisplay.val() && !$dobIso.val()) {
                    errs.dob = 'Select a valid date of birth. Example: 1 Jan 2000';
                }

                var w = num($weight.val()), ft = num($ft.val()), inch = num($inch.val());
                if (!errs.weight_kg && (w < 0.3 || w > 500)) { errs.weight_kg = 'Weight must be between 0.3 and 500 kg.'; }
                if (!errs.height_ft && (ft < 0 || ft > 8 || ft !== Math.floor(ft))) { errs.height_ft = 'Feet must be a whole number from 0 to 8.'; }
                if (!errs.height_in && (inch < 0 || inch >= 12)) { errs.height_in = 'Inches must be from 0 to 11.9.'; }
                if (!errs.height_ft && !errs.height_in) {
                    var tin = (ft * 12) + inch;
                    if (tin < 10 || tin > 100) { errs.height_ft = 'Please check the height.'; }
                }

                if (!$('#mh_occupation_other_field').hasClass('mhf-hidden') && $.trim($('#mh_occupation_other').val()) === '') {
                    errs.occupation_other = 'Write the occupation.';
                }
                if (!$('#mh_hazard_details_field').hasClass('mhf-hidden') && $.trim($('#mh_hazard_details').val()) === '') {
                    errs.hazard_details = 'Fill the details.';
                }
            }
        };
    })();

    /* ---------------------------------------------------------------------
     * START
     * ------------------------------------------------------------------- */
    $(function () {
        // tab clicks
        $(document).on('click', '.mhf-tab', function () { openTab($(this).data('section')); });

        // arrow keys move between tabs
        $(document).on('keydown', '.mhf-tab', function (e) {
            var n = +$(this).data('section');
            if (e.key === 'ArrowRight' && n < total) { openTab(n + 1, true); $('#mhf-tab-' + (n + 1)).trigger('focus'); e.preventDefault(); }
            if (e.key === 'ArrowLeft' && n > 1) { openTab(n - 1, true); $('#mhf-tab-' + (n - 1)).trigger('focus'); e.preventDefault(); }
        });

        // buttons
        $(document).on('click', '[data-action="save"]', function () { save(+$(this).data('section')); });
        $(document).on('click', '[data-action="back"]', function () { openTab(+$(this).data('section') - 1); });
        $('#mhf-pdf-btn').on('click', function () {
            var $b = $(this);
            if (!S.formId) { return false; }
            $b.addClass('is-busy');
            setTimeout(function () { $b.removeClass('is-busy'); }, 3500);
        });

        // show the first panel before starting the plugins (so they can measure their size)
        active = S.start || 1;
        $('.mhf-panel').prop('hidden', true);
        $panel(active).prop('hidden', false);

        $.each(hooks, function (n, h) { if (h.init) { h.init(); } });

        // fill saved answers
        $.each(S.data || {}, function (n, data) { hydrate(+n, data); });
        // brand-new form: suggest the person's name (and sex / date of birth when known)
        if (!S.formId && S.prefill && !$.isEmptyObject(S.prefill)) { hydrate(1, S.prefill); }
        hydrating = false;
        dirty = false;

        refreshFormHeader_safe();
        refreshTabs();
        openTab(active, true);
    });

    function refreshFormHeader_safe() {
        if (S.formId) { refreshFormHeader(); }
    }
})(window.jQuery);
