/* ======================================================
   OXIONEX - MAIN FUNCTIONALITY
   Form handling, AJAX calls, and core interactions
   ====================================================== */
(function () {
    'use strict';

    // ─── Constants ───────────────────────────────────────────────
    const THEME_STORAGE_KEY = 'oxionex-theme';

    // ─── Image Fallbacks ─────────────────────────────────────────
    function applyImageFallbacks() {
        const fallbackSrc = 'assets/images/placeholder.svg';

        document.querySelectorAll('img').forEach(function (img) {
            var handleError = function () {
                if (img.dataset.fallbackApplied === 'true') return;
                img.dataset.fallbackApplied = 'true';
                img.src = fallbackSrc;
            };
            img.addEventListener('error', handleError);
            if (img.complete && img.naturalWidth === 0) {
                handleError();
            }
        });
    }

    // ─── Flex Gap Detection ──────────────────────────────────────
    function detectFlexGap() {
        try {
            var flex = document.createElement('div');
            flex.style.display = 'flex';
            flex.style.flexDirection = 'row';
            flex.style.gap = '1px';
            flex.appendChild(document.createElement('div'));
            flex.appendChild(document.createElement('div'));
            document.body.appendChild(flex);
            var supported = flex.scrollHeight === 1 || flex.offsetHeight === 1;
            document.body.removeChild(flex);
            if (!supported) {
                document.documentElement.classList.add('no-flex-gap');
            }
        } catch (e) {
            document.documentElement.classList.add('no-flex-gap');
        }
    }

    // ─── Theme ───────────────────────────────────────────────────
    function getPreferredTheme() {
        try {
            var storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
            if (storedTheme === 'light' || storedTheme === 'dark') {
                return storedTheme;
            }
        } catch (e) {}
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches
            ? 'light'
            : 'dark';
    }

    function syncThemeToggle(theme) {
        var toggleButton = document.getElementById('themeToggle');
        if (!toggleButton) return;

        var toggleIcon  = toggleButton.querySelector('.theme-toggle-icon');
        var toggleLabel = toggleButton.querySelector('.theme-toggle-label');
        var nextTheme   = theme === 'light' ? 'dark' : 'light';

        toggleButton.setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');

        if (toggleIcon) {
            toggleIcon.classList.toggle('fa-sun',  theme === 'light');
            toggleIcon.classList.toggle('fa-moon', theme !== 'light');
        }
        if (toggleLabel) {
            toggleLabel.textContent = nextTheme === 'light' ? 'Light mode' : 'Dark mode';
        }

        toggleButton.setAttribute('aria-label', 'Switch to ' + nextTheme + ' mode');
        toggleButton.title = 'Switch to ' + nextTheme + ' mode';
    }

    function applyTheme(theme) {
        var resolved = theme === 'light' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', resolved);
        try { localStorage.setItem(THEME_STORAGE_KEY, resolved); } catch (e) {}
        syncThemeToggle(resolved);
    }

    function initializeThemeToggle() {
        applyTheme(getPreferredTheme());

        var toggleButton = document.getElementById('themeToggle');
        if (!toggleButton || toggleButton.dataset.bound === 'true') return;

        toggleButton.dataset.bound = 'true';
        toggleButton.addEventListener('click', function () {
            var current = document.documentElement.getAttribute('data-theme');
            applyTheme(current === 'light' ? 'dark' : 'light');
        });
    }

    // ─── Field Error Helpers ─────────────────────────────────────
    function showFieldError(fieldId, message) {
        var field = document.getElementById(fieldId);
        if (!field) return;
        field.classList.add('is-invalid');
        var feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = message;
    }

    function clearFieldError(fieldId) {
        var field = document.getElementById(fieldId);
        if (field) field.classList.remove('is-invalid');
    }

    // ─── Validation ──────────────────────────────────────────────
    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validatePhone(phone) {
        return /^[\d\s+\-()]*$/.test(phone) && phone.replace(/\D/g, '').length >= 10;
    }

    // ─── Debounce ────────────────────────────────────────────────
    function debounce(func, delay) {
        var timeoutId;
        return function () {
            var args = arguments;
            var ctx  = this;
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function () { func.apply(ctx, args); }, delay);
        };
    }

    // ─── GSAP helpers ────────────────────────────────────────────
    function gsapFadeIn(el) {
        if (typeof gsap !== 'undefined') {
            try { gsap.from(el, { opacity: 0, y: -20, duration: 0.5 }); return; } catch (e) {}
        }
        el.style.opacity = '1';
    }

    function gsapFadeOut(el, onComplete) {
        if (typeof gsap !== 'undefined') {
            try {
                gsap.to(el, { opacity: 0, y: -20, duration: 0.5, onComplete: onComplete });
                return;
            } catch (e) {}
        }
        onComplete();
    }

    function gsapShow(card) {
        card.style.display = 'block';
        if (typeof gsap !== 'undefined') {
            try { gsap.to(card, { opacity: 1, duration: 0.4 }); return; } catch (e) {}
        }
        card.style.opacity = '1';
    }

    function gsapHide(card) {
        if (typeof gsap !== 'undefined') {
            try {
                gsap.to(card, { opacity: 0, duration: 0.4, onComplete: function () { card.style.display = 'none'; } });
                return;
            } catch (e) {}
        }
        card.style.display = 'none';
    }

    // ─── Close Mobile Nav ────────────────────────────────────────
    function closeMobileNav() {
        var offcanvas = document.querySelector('.navbar-collapse');
        if (!offcanvas || !offcanvas.classList.contains('show')) return;
        if (typeof bootstrap !== 'undefined') {
            try { new bootstrap.Collapse(offcanvas, { toggle: false }).hide(); return; } catch (e) {}
        }
        offcanvas.classList.remove('show');
    }

    function initializeHamburgerMenu() {
        var hamburger = document.querySelector('.hamburger');
        var mobileMenu = document.querySelector('.mobile-menu');
        if (!hamburger || !mobileMenu || hamburger.dataset.bound === 'true') return;

        hamburger.dataset.bound = 'true';

        function setMenuState(isOpen) {
            mobileMenu.classList.toggle('open', isOpen);
            mobileMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            mobileMenu.toggleAttribute('inert', !isOpen);
            hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.documentElement.classList.toggle('mobile-menu-open', isOpen);
            if (isOpen) {
                var firstLink = mobileMenu.querySelector('.mobile-menu-link, .mobile-menu-close');
                if (firstLink) firstLink.focus();
            } else {
                hamburger.focus();
            }
        }

        hamburger.addEventListener('click', function () {
            setMenuState(!mobileMenu.classList.contains('open'));
        });

        mobileMenu.addEventListener('click', function (event) {
            if (event.target.classList.contains('mobile-menu') || event.target.classList.contains('mobile-menu-close') || event.target.classList.contains('mobile-menu-link')) {
                setMenuState(false);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                setMenuState(false);
            }
            if (event.key === 'Tab' && mobileMenu.classList.contains('open')) {
                var focusable = mobileMenu.querySelectorAll('button, a[href], input, select, textarea');
                if (!focusable.length) return;
                var first = focusable[0];
                var last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });

        mobileMenu.toggleAttribute('inert', true);
    }

    // ─── Accessibility: Skip Link ────────────────────────────────
    function injectSkipLink() {
        var skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.className = 'skip-to-main';
        skipLink.textContent = 'Skip to main content';
        skipLink.style.cssText =
            'position:absolute;top:-40px;left:0;background:#00d4ff;color:#0f0f23;' +
            'padding:8px;text-decoration:none;z-index:100;';
        skipLink.addEventListener('focus', function () { skipLink.style.top = '0'; });
        skipLink.addEventListener('blur',  function () { skipLink.style.top = '-40px'; });
        document.body.prepend(skipLink);
    }

    // ─── Contact Form ────────────────────────────────────────────
    function initContactForm() {
        var contactForm = document.getElementById('contactForm');
        var formMessage = document.getElementById('formMessage');
        if (!contactForm || !formMessage) return;
        if (contactForm.dataset.bound === 'true') return;
        contactForm.dataset.bound = 'true';

        var isSubmitting = false;

        // Restore auto-saved data
        try {
            var saved = localStorage.getItem('contactFormData');
            if (saved) {
                var savedData = JSON.parse(saved);
                Object.keys(savedData).forEach(function (key) {
                    var field = contactForm.elements[key];
                    if (field) field.value = savedData[key];
                });
            }
        } catch (e) {}

        // Auto-save on input
        contactForm.addEventListener('input', debounce(function () {
            try {
                var fd   = new FormData(contactForm);
                var data = Object.fromEntries(fd);
                localStorage.setItem('contactFormData', JSON.stringify(data));
            } catch (e) {}
        }, 500));

        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Hard guard: ignore if a request is already in flight
            if (isSubmitting) return;

            var name    = (document.getElementById('name')    || {}).value || '';
            var email   = (document.getElementById('email')   || {}).value || '';
            var phone   = (document.getElementById('phone')   || {}).value || '';
            var message = (document.getElementById('message') || {}).value || '';
            var isValid = true;

            if (!name.trim() || name.trim().length < 3) {
                showFieldError('name', 'Name must be at least 3 characters');
                isValid = false;
            } else { clearFieldError('name'); }
            if (!email.trim() || !validateEmail(email.trim())) {
                showFieldError('email', 'Please enter a valid email');
                isValid = false;
            } else { clearFieldError('email'); }
            if (phone.trim() && !validatePhone(phone.trim())) {
                showFieldError('phone', 'Please enter a valid phone number');
                isValid = false;
            } else { clearFieldError('phone'); }
            if (!message.trim() || message.trim().length < 10) {
                showFieldError('message', 'Message must be at least 10 characters');
                isValid = false;
            } else { clearFieldError('message'); }
            if (!isValid) return;

            var formData     = new FormData(contactForm);
            var submitButton = contactForm.querySelector('button[type="submit"]');
            var originalHTML = submitButton.innerHTML;

            isSubmitting = true;
            submitButton.disabled  = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            formMessage.style.display = 'none';

            fetch('ajax/contact_submit.php', {
                method: 'POST',
                body:   formData
            })
            .then(function (response) {
                // Always parse JSON; a non-2xx status still carries our JSON body
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                var data = result.data;
                formMessage.style.display = 'block';
                formMessage.innerHTML     = data.message || 'An unexpected error occurred.';

                if (data.success) {
                    formMessage.classList.remove('alert-danger');
                    formMessage.classList.add('alert-success');
                    contactForm.reset();
                    try { localStorage.removeItem('contactFormData'); } catch (err) {}

                    gsapFadeIn(formMessage);
                    setTimeout(function () {
                        gsapFadeOut(formMessage, function () {
                            formMessage.style.display = 'none';
                        });
                    }, 5000);
                } else {
                    formMessage.classList.remove('alert-success');
                    formMessage.classList.add('alert-danger');
                }
            })
            .catch(function (err) {
                console.error('Contact form error:', err);
                formMessage.style.display = 'block';
                formMessage.classList.remove('alert-success');
                formMessage.classList.add('alert-danger');
                formMessage.innerHTML = 'An error occurred. Please try again later.';
            })
            .finally(function () {
                isSubmitting           = false;
                submitButton.disabled  = false;
                submitButton.innerHTML = originalHTML;
            });
        });
    }

    // ─── Portfolio Filter ────────────────────────────────────────
    function initProjectCardAccessibility() {
        document.querySelectorAll('.project-card[role="button"]').forEach(function (card) {
            if (card.dataset.keyboardBound === 'true') return;
            card.dataset.keyboardBound = 'true';
            card.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                event.preventDefault();
                if (typeof openProjectPreview === 'function') {
                    openProjectPreview(card.dataset.projectId, card);
                }
            });
        });
    }

    function initPortfolioFilter() {
        var filterButtons = document.querySelectorAll('.filter-btn');
        if (!filterButtons.length) return;

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var filter = this.getAttribute('data-filter');

                filterButtons.forEach(function (btn) {
                    btn.classList.toggle('active', btn === button);
                    btn.setAttribute('aria-pressed', btn === button ? 'true' : 'false');
                });

                document.querySelectorAll('.project-card').forEach(function (card) {
                    var category = card.getAttribute('data-category');
                    var status   = card.getAttribute('data-status');
                    var show     = false;

                    if (filter === 'all') {
                        show = true;
                    } else if (filter === 'completed' || filter === 'ongoing') {
                        show = status === filter;
                    } else {
                        show = category === filter;
                    }

                    card.hidden = !show;
                    card.classList.toggle('is-hidden', !show);
                });
            });
        });
    }

    // ─── Smooth Anchor Scrolling ─────────────────────────────────
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                var href = this.getAttribute('href');
                if (!href || href === '#') return;
                var target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }

    // ─── Nav Scroll Links ────────────────────────────────────────
    function initNavScrollLinks() {
        document.querySelectorAll('.nav-scroll').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var targetSelector = this.dataset.target || this.getAttribute('href');
                if (!targetSelector) return;

                var targetEl = document.querySelector(targetSelector);
                if (targetEl) {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    closeMobileNav();
                } else {
                    var brandLink  = document.querySelector('.navbar-brand');
                    var brandHref  = brandLink ? brandLink.getAttribute('href') : 'index.php';
                    var homeUrl    = new URL(brandHref || 'index.php', window.location.href);
                    homeUrl.hash   = targetSelector.startsWith('#')
                        ? targetSelector.substring(1)
                        : targetSelector;
                    window.location.href = homeUrl.toString();
                }
            });
        });
    }

    // ─── Mobile Nav: close on section link click ─────────────────
    function initMobileNavClose() {
        document.querySelectorAll('.navbar-nav a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function () {
                var href = this.getAttribute('href');
                if (href && href !== '#') closeMobileNav();
            });
        });
    }

    // ─── Document-level navbar fallback ──────────────────────────
    function initNavbarFallback() {
        document.addEventListener('click', function (e) {
            var el = e.target.closest && e.target.closest('.navbar-nav a[href^="#"]');
            if (!el) return;
            try {
                var href   = el.getAttribute('href');
                if (!href || href === '#') return;
                var target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    closeMobileNav();
                }
            } catch (err) {
                console.debug('Navbar fallback error:', err);
            }
        });
    }

    // ─── Bootstrap Tooltips ──────────────────────────────────────
    function initTooltips() {
        if (typeof bootstrap === 'undefined') return;
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            try { new bootstrap.Tooltip(el); } catch (e) {}
        });
    }

    // ─── Keyboard Shortcuts ──────────────────────────────────────
    function initKeyboardShortcuts() {
        document.addEventListener('keydown', function (e) {
            if (e.altKey && e.key === 'c') {
                var contactForm = document.getElementById('contactForm');
                if (contactForm) {
                    var firstInput = contactForm.querySelector('input');
                    if (firstInput) firstInput.focus();
                }
            }
        });
    }

    // ─── Print Events ────────────────────────────────────────────
    function initPrintEvents() {
        window.addEventListener('beforeprint', function () { document.body.classList.add('printing'); });
        window.addEventListener('afterprint',  function () { document.body.classList.remove('printing'); });
    }

    // ─── Browser Notifications ───────────────────────────────────
    function initNotifications() {
        // Notifications require an explicit user action; never prompt on load.
    }

    // ─── Statistics ───────────────────────────────────────────────
    // Statistics are rendered server-side in index.php.
    // No AJAX refresh needed on page load.

    // ─── Console Greeting ────────────────────────────────────────
    function printConsoleGreeting() {
        console.log('%cOxionex Web Solutions',          'font-size:24px;font-weight:bold;color:#00d4ff;');
        console.log('%cBuilding Digital Experiences That Sell', 'font-size:14px;color:#7c3aed;');
    }

    // ─── DOMContentLoaded Bootstrap ──────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        applyImageFallbacks();
        detectFlexGap();
        initializeThemeToggle();
        initializeHamburgerMenu();
        initContactForm();
        initProjectCardAccessibility();
        initPortfolioFilter();
        initSmoothScroll();
        initNavScrollLinks();
        initMobileNavClose();
        initNavbarFallback();
        initTooltips();
        initKeyboardShortcuts();
        initPrintEvents();
        injectSkipLink();
        printConsoleGreeting();
    });

})();