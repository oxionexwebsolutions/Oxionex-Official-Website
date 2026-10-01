// Keeps --navbar-height in sync with the navbar's ACTUAL rendered height.
// Fixes content being hidden under the fixed navbar on mobile, where the
// navbar wraps onto two rows (logo row + theme toggle / hamburger row).
(function () {
    function setNavbarHeight() {
        var nav = document.querySelector('nav.navbar');
        if (!nav) return;
        var height = nav.offsetHeight;
        document.documentElement.style.setProperty('--navbar-height', height + 'px');
    }

    // Run as early as possible
    setNavbarHeight();

    // Re-run after fonts/images load (these can change navbar height)
    window.addEventListener('load', setNavbarHeight);

    // Re-run on resize / orientation change (rotating phone, resizing browser)
    window.addEventListener('resize', setNavbarHeight);
    window.addEventListener('orientationchange', setNavbarHeight);

    // Re-run if the navbar's own size changes (e.g. text wraps differently
    // after a font swap, or the mobile menu toggles)
    var nav = document.querySelector('nav.navbar');
    if (nav && window.ResizeObserver) {
        new ResizeObserver(setNavbarHeight).observe(nav);
    }
})();