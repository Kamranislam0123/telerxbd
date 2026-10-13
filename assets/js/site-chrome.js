/**
 * TeleRx Bangladesh - Header behaviour
 *  1) phone / tablet menu (drawer)
 *  2) "More" dropdown (hover on desktop, tap on phones)
 *  3) header gets a soft shadow when the page is scrolled
 *  4) keeps the page content tall enough so the footer is never in the middle of the screen
 * Plain JavaScript - needs nothing else.
 */
(function () {
    'use strict';

    var root = document.documentElement;
    var header = document.getElementById('hf-header');
    if (!header) { return; }

    var burger = document.getElementById('hf_burger');
    var closeBtn = document.getElementById('hf_close');
    var backdrop = document.getElementById('hf_backdrop');
    var menu = document.getElementById('hf_menu');

    /* ---------- 1) drawer ---------- */
    function openMenu() {
        root.classList.add('hf-menu-open');
        if (burger) { burger.setAttribute('aria-expanded', 'true'); }
    }
    function closeMenu() {
        root.classList.remove('hf-menu-open');
        if (burger) { burger.setAttribute('aria-expanded', 'false'); }
    }
    if (burger) {
        burger.addEventListener('click', function () {
            root.classList.contains('hf-menu-open') ? closeMenu() : openMenu();
        });
    }
    if (closeBtn) { closeBtn.addEventListener('click', closeMenu); }
    if (backdrop) { backdrop.addEventListener('click', closeMenu); }

    // choosing a normal link inside the drawer closes it
    if (menu) {
        menu.addEventListener('click', function (e) {
            var a = e.target.closest ? e.target.closest('a[href]') : null;
            if (a && !a.classList.contains('hf-link')) { closeMenu(); }
            else if (a && a.classList.contains('hf-link') && a.getAttribute('href') !== 'javascript:void(0);') { closeMenu(); }
        });
    }

    /* ---------- 2) dropdowns ---------- */
    var dropdowns = header.querySelectorAll('.hf-dd');

    function closeDropdowns(except) {
        Array.prototype.forEach.call(dropdowns, function (dd) {
            if (dd !== except && dd.classList.contains('is-open')) {
                dd.classList.remove('is-open');
                var t = dd.querySelector('.hf-link');
                if (t) { t.setAttribute('aria-expanded', 'false'); }
            }
        });
    }
    Array.prototype.forEach.call(dropdowns, function (dd) {
        var toggle = dd.querySelector(':scope > .hf-link');
        if (!toggle) { return; }
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            var open = !dd.classList.contains('is-open');
            closeDropdowns(dd);
            dd.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });
    document.addEventListener('click', function (e) {
        if (!header.contains(e.target)) { closeDropdowns(null); }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeDropdowns(null); closeMenu(); }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) { closeMenu(); }
        setContentHeight();
    });

    /* ---------- 3) scroll shadow ---------- */
    var ticking = false;
    function onScroll() {
        header.classList.toggle('is-scrolled', (window.pageYOffset || root.scrollTop) > 8);
        ticking = false;
    }
    window.addEventListener('scroll', function () {
        if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
    }, { passive: true });
    onScroll();

    /* ---------- 4) page content minimum height ---------- */
    function setContentHeight() {
        var blocks = document.querySelectorAll('.content');
        if (!blocks.length) { return; }
        var spacer = document.querySelector('.hf-spacer');
        var footer = document.querySelector('.hf-footer');
        var h = window.innerHeight - (spacer ? spacer.offsetHeight : 0) - (footer ? footer.offsetHeight : 0);
        if (h > 0) {
            Array.prototype.forEach.call(blocks, function (el) { el.style.minHeight = h + 'px'; });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setContentHeight);
    } else {
        setContentHeight();
    }
    window.addEventListener('load', setContentHeight);
})();
