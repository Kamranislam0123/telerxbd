/**
 * TeleRx Bangladesh - Patient Medical History Form
 * Tabs, "Save and Next", Tab 1 behaviour (age, age group, BMI, show/hide fields),
 * "+ add row" tables, tick-to-text boxes and the risk factor summary of tabs 2-4.
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
            // errors of a "+ add row" table sit next to the table, not inside a field
            if (!firstField) { firstField = { $field: $field.length ? $field : $msg.prev().add($msg).first(), name: name }; }
        });
        if (firstField) {
            var el = firstField.$field.find('input:visible:not([readonly]), select:visible, textarea:visible, .select2-search__field').first();
            if (!el.length) { el = firstField.$field.find('input, select, textarea').first(); }
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
    // typing in a "+ add row" table clears the table's error message
    $(document).on('input change', '.mhf-form [data-rows]', function () {
        var key = $(this).data('rows');
        $(this).closest('.mhf-form').find('.mhf-error[data-error-for="' + key + '"]').text('');
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
                if ($c.is(':radio, :checkbox')) {   // a group of choices: one of them must be ticked
                    if (!$field.find('[name="' + $c.attr('name') + '"]:checked').length) { errs[name] = 'Select an option.'; }
                    return;
                }
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
    function str(v) { return v === null || v === undefined ? '' : String(v); }

    /** Puts one saved value back into the field(s) called `name` (handles nested names like risk[cardio][]). */
    function fillNamed($f, name, val) {
        if ($.isArray(val)) {
            var $rows = $f.find('[data-rows="' + name + '"]');
            if ($rows.length) {   // a "+ add row" table
                $.each(val, function (i, row) { Dyn.addRow($f, name, row); });
                return;
            }
            var $many = $f.find('[name="' + name + '[]"]');
            if ($many.is(':checkbox')) {
                $many.each(function () { this.checked = val.indexOf(this.value) !== -1; });
            } else if ($many.is('select')) {
                $.each(val, function (i, item) {
                    var exists = false;
                    $many.find('option').each(function () { if (this.value === item) { exists = true; } });
                    if (!exists) { $many.append(new Option(item, item, false, false)); }
                });
                $many.val(val).trigger('change');
            }
            return;
        }
        if (val !== null && typeof val === 'object') {
            $.each(val, function (k, v) { fillNamed($f, name + '[' + k + ']', v); });
            return;
        }
        var $el = $f.find('[name="' + name + '"]');
        if ($el.is(':radio, :checkbox')) {
            $el.each(function () { this.checked = this.value === str(val); });
        } else if ($el.length) {
            $el.val(str(val));
        }
    }

    function hydrate(n, data) {
        var $f = $form(n);
        $.each(data || {}, function (key, val) { fillNamed($f, key, val); });
        if (hooks[n] && hooks[n].afterHydrate) { hooks[n].afterHydrate($f); }
    }

    /* ---------------------------------------------------------------------
     * SHARED BEHAVIOUR FOR TABS 2, 3, 4
     *   data-rows / data-repeat-add / data-repeat-remove -> "+ add row" tables
     *   data-show="#id"               -> tick box / choice that shows a part of the form
     *   data-show-in-row=".class"     -> same, but inside its own row
     *   data-append-to="#textarea"    -> ticked text is written into the box (one per line)
     * ------------------------------------------------------------------- */
    var Dyn = (function () {
        var counter = 0;

        function fillRow($row, data) {
            $row.find('[data-k]').each(function () {
                var k = $(this).data('k');
                var v = data ? data[k] : undefined;
                if ($(this).is(':radio, :checkbox')) {
                    this.checked = $.isArray(v) ? v.indexOf(this.value) !== -1 : this.value === str(v);
                } else {
                    $(this).val(str(v));
                }
            });
        }

        function renumber($list) {
            $list.children('[data-row]').each(function (i) { $(this).find('[data-row-no]').first().text(i + 1); });
        }

        function addRow($f, key, data) {
            var tpl = document.getElementById('mh-tpl-' + key);
            var $list = $f.find('[data-rows="' + key + '"]');
            if (!tpl || !$list.length) { return null; }
            var html = $.trim(tpl.innerHTML).replace(/__i__/g, String(counter++));
            var $row = $($.parseHTML(html));
            fillRow($row, data || null);
            $list.append($row);
            renumber($list);
            applyShows($f);
            return $row;
        }

        function ensureMinRows($f) {
            $f.find('[data-rows]').each(function () {
                var $list = $(this);
                var min = +$list.data('min-rows') || 0;
                while ($list.children('[data-row]').length < min) { addRow($f, $list.data('rows')); }
            });
        }

        function setOpen($target, open) {
            $target.toggleClass('mhf-hidden', !open);
            $target.find('input, select, textarea').prop('disabled', !open);
        }

        // show / hide parts of the form; hidden parts are disabled so they are not sent
        function applyShows($f) {
            $f.find('[data-show]').each(function () {
                setOpen($($(this).data('show')), this.checked && !this.disabled);
            });
            $f.find('[data-show-in-row]').each(function () {
                var $t = $(this).closest('[data-row]').find($(this).data('show-in-row'));
                setOpen($t, this.checked && !this.disabled);
            });
            $f.trigger('mhf:shows');   // tabs can refresh their own extras (e.g. Tab 4 titles)
        }

        function lines(text) { return str(text).split(/\r?\n/); }

        // tick -> add the text as a new line;  untick -> remove that line
        function appendTo(box) {
            var $box = $(box.getAttribute('data-append-to'));
            if (!$box.length) { return; }
            var value = box.value;
            var list = lines($box.val()).filter(function (l) { return $.trim(l) !== ''; });
            var pos = -1;
            $.each(list, function (i, l) { if ($.trim(l).toLowerCase() === value.toLowerCase()) { pos = i; } });
            if (box.checked && pos === -1) { list.push(value); }
            if (!box.checked && pos !== -1) { list.splice(pos, 1); }
            $box.val(list.join('\n')).trigger('input');
        }

        $(document).on('change', '.mhf-form [data-append-to]', function () { appendTo(this); });
        $(document).on('change', '.mhf-form [data-show], .mhf-form [data-show-in-row], .mhf-form :radio', function () {
            applyShows($(this).closest('.mhf-form'));
        });
        $(document).on('click', '.mhf-form [data-repeat-add]', function () {
            var $f = $(this).closest('.mhf-form');
            var $row = addRow($f, $(this).data('repeat-add'));
            if ($row) {
                dirty = true;
                try { $row.find('input:not(:radio):not(:checkbox), textarea').first().trigger('focus'); } catch (e) { /* ignore */ }
            }
        });
        $(document).on('click', '.mhf-form [data-repeat-remove]', function () {
            var $row = $(this).closest('[data-row]');
            var $list = $row.parent();
            var $f = $row.closest('.mhf-form');
            if ($list.children('[data-row]').length <= (+$list.data('min-rows') || 0)) {
                fillRow($row, null);       // the last row is emptied instead of removed
            } else {
                $row.remove();
            }
            renumber($list);
            applyShows($f);
            dirty = true;
            $f.trigger('change');
        });

        return { addRow: addRow, ensureMinRows: ensureMinRows, applyShows: applyShows, setOpen: setOpen };
    })();

    /* ---------------------------------------------------------------------
     * PATIENT PROFILE (age / sex saved in Tab 1)
     * Tab 5 shows only the parts that fit the patient:  data-cond="adult | female12 | male12 | child | senior"
     * and fills the read-only boxes:                    data-auto="name | pid | dob | age_full | age_years | sex | weight | height_cm | bmi"
     * The server uses the same rules (php/mh-schema.php -> mh_profile_flags).
     * ------------------------------------------------------------------- */
    var Profile = (function () {
        var p = null;   // the SAVED Tab 1 answers
        var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        function fromForm1() {
            p = {};
            $.each($form(1).serializeArray(), function (i, f) { p[f.name] = f.value; });
        }

        function num(v) { var x = parseFloat(str(v).replace(',', '.')); return isNaN(x) ? null : x; }

        function fmtDate(iso) {
            var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso || '');
            return m ? (+m[3]) + ' ' + MONTHS[+m[2] - 1] + ' ' + m[1] : '';
        }

        function ageFull(info) {
            if (!info) { return ''; }
            var parts = [];
            $.each([['years', 'year'], ['months', 'month'], ['days', 'day']], function (i, u) {
                if (info[u[0]] > 0) { parts.push(info[u[0]] + ' ' + u[1] + (info[u[0]] === 1 ? '' : 's')); }
            });
            return parts.length ? parts.join(', ') : '0 days';
        }

        function apply() {
            if (!p) { return; }
            var info = MHCalc.ageInfo(p.dob);
            var y = info ? info.years : null;
            var sex = p.sex || '';
            var flags = {
                adult: y !== null && y >= 18,
                female12: y !== null && y >= 12 && sex === 'Female',
                male12: y !== null && y >= 12 && sex === 'Male',
                child: y !== null && y < 12,
                senior: y !== null && y >= 60
            };
            $('[data-cond]').each(function () { Dyn.setOpen($(this), !!flags[$(this).data('cond')]); });

            var kg = num(p.weight_kg), ft = num(p.height_ft), inch = num(p.height_in);
            var totalIn = (ft || 0) * 12 + (inch || 0);
            var values = {
                name: p.patient_name || '',
                pid: S.pid || '',
                dob: fmtDate(p.dob),
                age_full: ageFull(info),
                age_years: info ? info.years + ' years' : '',
                sex: sex,
                weight: kg !== null ? kg + ' kg' : '',
                height_cm: totalIn > 0 ? (Math.round(totalIn * 2.54 * 10) / 10) + ' cm' : '',
                bmi: (kg !== null && totalIn > 0) ? MHCalc.bmiText(kg, ft || 0, inch || 0, y) : ''
            };
            $('[data-auto]').each(function () { $(this).val(values[$(this).data('auto')] || ''); });

            $('#mh5_profile_note span').text('Questions are chosen for: ' + (sex || 'Unknown sex') + ', ' + (info ? ageFull(info) : 'unknown age') +
                ' (from Tab 1). If this is wrong, correct and save Tab 1 first.');
        }

        return {
            init: function () { if (S.data && S.data[1]) { p = S.data[1]; } apply(); },
            fromForm1: fromForm1,
            apply: apply
        };
    })();

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
            if (n === 1) { Profile.fromForm1(); Profile.apply(); }   // age / sex may have changed

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

        return {
            init: function () {
                $dobDisplay = $('#mh_dob_display'); $dobIso = $('#mh_dob');
                $age = $('#mh_age'); $group = $('#mh_age_group'); $bmi = $('#mh_bmi');
                $weight = $('#mh_weight_kg'); $ft = $('#mh_height_ft'); $inch = $('#mh_height_in');
                $sex = $('#mh_sex');

                initDob();

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
                    height_in: 'Enter the height in inches.'
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
     * TAB 2 - Chief Complaint & Risk Factors
     * ticked risk factors are listed automatically under "Important risk factors identified"
     * ------------------------------------------------------------------- */
    hooks[2] = (function () {
        function esc(s) { return $('<div>').text(s).html(); }

        function updateIdentified() {
            var parts = [];
            $('.mhf-risk-table tr[data-risk-group]').each(function () {
                var $tr = $(this);
                var ticked = $tr.find('[data-risk-factor]:checked').map(function () { return this.value; }).get();
                var other = $.trim($tr.find('[data-risk-other]').val() || '');
                if (other !== '') { ticked.push(other); }
                $tr.toggleClass('has-risk', ticked.length > 0);
                if (ticked.length) {
                    parts.push('<div class="mhf-identified-line"><strong>' + esc($tr.data('risk-title')) + ':</strong> ' +
                        $.map(ticked, function (t) { return '<span class="mhf-chip">' + esc(t) + '</span>'; }).join(' ') + '</div>');
                }
            });
            $('#mh_risk_identified').html(parts.length ? parts.join('') : '<span class="mhf-identified-empty">No risk factor ticked yet.</span>');
        }

        return {
            init: function () {
                $(document).on('change input', '.mhf-risk-table [data-risk-factor], .mhf-risk-table [data-risk-other]', updateIdentified);
            },
            afterHydrate: updateIdentified,
            validate: function ($f, errs) {
                if (errs.main_problem) { errs.main_problem = 'Write the main problem.'; }
            }
        };
    })();

    /* ---------------------------------------------------------------------
     * TAB 3 - Illness History
     * ------------------------------------------------------------------- */
    hooks[3] = {
        init: function () {
            // first time "Previous hospitalization" is ticked, focus the first box
            $(document).on('change', '#mh_has_hosp', function () {
                if (this.checked) { $('#mh_hosp_section input[type="text"]').first().trigger('focus'); }
            });
        },
        validate: function ($f, errs) {
            var ok = false;
            $f.find('[data-rows="hpi"] [data-k="complaint"]').each(function () { if ($.trim($(this).val()) !== '') { ok = true; } });
            if (!ok) { errs.hpi = 'Write at least one complaint in the History of Present Illness.'; }
        }
    };

    /* ---------------------------------------------------------------------
     * TAB 4 - Medication & Family
     * ------------------------------------------------------------------- */
    hooks[4] = {
        // Family history: the small titles follow the Alive / Dead choice of the same row
        //   Alive -> "Age Now", "Disease"      Dead -> "Age at death", "Cause of death"
        init: function () {
            function familyTitles($scope) {
                $scope.find('[data-rows="family"] > [data-row]').each(function () {
                    var st = $(this).find('[data-family-status]').val();
                    var key = st === 'Alive' ? 'alive' : (st === 'Dead' ? 'dead' : 'none');
                    $(this).find('.mhf-cell-cap[data-cap-' + key + ']').each(function () { $(this).text($(this).attr('data-cap-' + key)); });
                });
            }
            $(document).on('change', '[data-family-status]', function () { familyTitles($(this).closest('.mhf-form')); });
            $(document).on('mhf:shows', '.mhf-form[data-section="4"]', function () { familyTitles($(this)); });
        },
        validate: function ($f, errs) {
            if (errs.drug_allergy) { errs.drug_allergy = 'Select "No known drug allergy" or "Yes".'; }
            if ($f.find('[name="drug_allergy"]:checked').val() === 'Yes') {
                var ok = false;
                $f.find('[data-rows="drug_allergies"] [data-k="drug"]').each(function () { if ($.trim($(this).val()) !== '') { ok = true; } });
                if (!ok) { errs.drug_allergies = 'Write the drug the patient is allergic to.'; }
            }
        }
    };

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
        // every "+ add row" table starts with at least one empty row; then show/hide the dependent parts
        $('.mhf-form').each(function () {
            Dyn.ensureMinRows($(this));
            Dyn.applyShows($(this));
        });
        Profile.init();
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
