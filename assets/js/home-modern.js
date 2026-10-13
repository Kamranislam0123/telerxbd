/**
 * TeleRx Bangladesh - Modern Home Page
 *  1) smooth "reveal on scroll"
 *  2) doctor carousel arrows
 *  3) search suggestions (same feature the old home page had)
 * Plain JavaScript - needs nothing else.
 */
(function () {
    'use strict';

    /* ---------- 1) reveal on scroll ---------- */
    function initReveal() {
        var items = document.querySelectorAll('.hm-reveal');
        if (!items.length) { return; }

        if (!('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('is-in'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        items.forEach(function (el) { io.observe(el); });

        // safety net: never leave anything hidden
        setTimeout(function () { items.forEach(function (el) { el.classList.add('is-in'); }); }, 4000);
    }

    /* ---------- 2) doctor carousel ---------- */
    function initCarousel() {
        var track = document.getElementById('hmDocTrack');
        var prev = document.getElementById('hmDocPrev');
        var next = document.getElementById('hmDocNext');
        if (!track || !prev || !next) { return; }

        function step() {
            var card = track.querySelector('.hm-doc');
            if (!card) { return track.clientWidth; }
            var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 16;
            return card.getBoundingClientRect().width + gap;
        }
        function update() {
            var max = track.scrollWidth - track.clientWidth - 2;
            prev.disabled = track.scrollLeft <= 2;
            next.disabled = track.scrollLeft >= max;
        }
        prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
        next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
        track.addEventListener('scroll', function () { window.requestAnimationFrame(update); }, { passive: true });
        window.addEventListener('resize', update);
        update();
    }

    /* ---------- 3) search suggestions ---------- */
    var debounceTimers = {};

    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function highlightMatch(text, query) {
        var escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return escapeHtml(text).replace(new RegExp('(' + escaped + ')', 'gi'), '<mark>$1</mark>');
    }
    function createDropdown(inputEl) {
        var holder = inputEl.parentNode;
        var existing = holder.querySelector('.hm-suggest');
        if (existing) { return existing; }
        var drop = document.createElement('div');
        drop.className = 'hm-suggest';
        drop.style.display = 'none';
        holder.style.position = 'relative';
        holder.appendChild(drop);
        return drop;
    }
    function hideDropdown(drop) { drop.style.display = 'none'; drop._activeIdx = -1; }
    function showDropdown(drop, items, query, onSelect) {
        if (!items.length) { hideDropdown(drop); return; }
        drop.innerHTML = '';
        items.forEach(function (item) {
            var div = document.createElement('div');
            div.className = 'suggestion-item';
            div.innerHTML = '<span>' + highlightMatch(item.label, query) + '</span>';
            div.addEventListener('mousedown', function (e) { e.preventDefault(); onSelect(item.label); });
            drop.appendChild(div);
        });
        drop.style.display = 'block';
        drop._activeIdx = -1;
    }

    function initAutocomplete(inputEl, apiType) {
        var drop = createDropdown(inputEl);
        var form = inputEl.closest('form');

        inputEl.addEventListener('input', function () {
            var q = this.value.trim();
            clearTimeout(debounceTimers[apiType + inputEl.name]);
            if (q.length < 2) { hideDropdown(drop); return; }
            debounceTimers[apiType + inputEl.name] = setTimeout(function () {
                fetch('api/search-suggestions.php?type=' + apiType + '&q=' + encodeURIComponent(q))
                    .then(function (r) { return r.json(); })
                    .then(function (items) {
                        showDropdown(drop, items, q, function (val) {
                            inputEl.value = val;
                            hideDropdown(drop);
                            if (form) { form.submit(); }
                        });
                    }).catch(function () { hideDropdown(drop); });
            }, 220);
        });

        inputEl.addEventListener('keydown', function (e) {
            if (drop.style.display === 'none') { return; }
            var items = drop.querySelectorAll('.suggestion-item');
            var idx = drop._activeIdx !== undefined ? drop._activeIdx : -1;
            if (e.key === 'ArrowDown') { e.preventDefault(); idx = Math.min(idx + 1, items.length - 1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); idx = Math.max(idx - 1, -1); }
            else if (e.key === 'Enter') {
                if (idx >= 0 && items[idx]) {
                    e.preventDefault();
                    inputEl.value = items[idx].textContent.trim();
                    hideDropdown(drop);
                    if (form) { form.submit(); }
                    return;
                }
                hideDropdown(drop);
                return;
            } else if (e.key === 'Escape') { hideDropdown(drop); return; }
            else { return; }
            items.forEach(function (el, i) { el.classList.toggle('active', i === idx); });
            drop._activeIdx = idx;
        });
        inputEl.addEventListener('blur', function () { setTimeout(function () { hideDropdown(drop); }, 150); });
        inputEl.addEventListener('focus', function () {
            if (this.value.trim().length >= 2) { this.dispatchEvent(new Event('input')); }
        });
    }

    function initSearch() {
        var searchInput = document.querySelector('.hm-hero input[name="search"]');
        var locationInput = document.querySelector('.hm-hero input[name="location"]');
        if (searchInput) { initAutocomplete(searchInput, 'search'); }
        if (locationInput) { initAutocomplete(locationInput, 'location'); }
    }

    /* ---------- start ---------- */
    function start() {
        initReveal();
        initCarousel();
        initSearch();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
