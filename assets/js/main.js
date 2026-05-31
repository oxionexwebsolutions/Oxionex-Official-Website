/* ======================================================
   PIXELARA - MAIN FUNCTIONALITY
   Form handling, AJAX calls, and core interactions
   ====================================================== */

function applyImageFallbacks() {
    const fallbackSrc = 'assets/images/placeholder.svg';

    document.querySelectorAll('img').forEach((img) => {
        const handleError = () => {
            if (img.dataset.fallbackApplied === 'true') {
                return;
            }

            img.dataset.fallbackApplied = 'true';
            img.src = fallbackSrc;
        };

        img.addEventListener('error', handleError);

        if (img.complete && img.naturalWidth === 0) {
            handleError();
        }
    });

        // Flex gap support detection: add `no-flex-gap` class if unsupported
        (function(){
            try {
                var flex = document.createElement('div');
                flex.style.display = 'flex';
                flex.style.flexDirection = 'row';
                flex.style.gap = '1px';
                flex.appendChild(document.createElement('div'));
                flex.appendChild(document.createElement('div'));
                document.body.appendChild(flex);
                var supported = (flex.scrollHeight === 1 || flex.offsetHeight === 1);
                document.body.removeChild(flex);
                if (!supported) {
                    document.documentElement.classList.add('no-flex-gap');
                }
            } catch (e) {
                // If any error, assume no support and add fallback class
                document.documentElement.classList.add('no-flex-gap');
            }
        })();
}

// Contact Form AJAX Submission
document.addEventListener('DOMContentLoaded', function() {
    applyImageFallbacks();

    const contactForm = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');

    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            try {
                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                // Send AJAX request
                const response = await fetch('ajax/contact_submit.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // Display message
                formMessage.style.display = 'block';
                formMessage.innerHTML = data.message;

                if (data.success) {
                    formMessage.classList.remove('alert-danger');
                    formMessage.classList.add('alert-success');
                    contactForm.reset();
                    
                    // Show success animation
                    if (typeof gsap !== 'undefined') {
                        try {
                            gsap.from(formMessage, {
                                opacity: 0,
                                y: -20,
                                duration: 0.5
                            });
                        } catch (e) {
                            formMessage.style.opacity = '1';
                        }
                    } else {
                        formMessage.style.opacity = '1';
                    }

                    // Auto-hide message after 5 seconds
                    setTimeout(() => {
                        if (typeof gsap !== 'undefined') {
                            try {
                                gsap.to(formMessage, {
                                    opacity: 0,
                                    y: -20,
                                    duration: 0.5,
                                    onComplete: () => {
                                        formMessage.style.display = 'none';
                                    }
                                });
                            } catch (e) {
                                formMessage.style.display = 'none';
                            }
                        } else {
                            formMessage.style.display = 'none';
                        }
                    }, 5000);
                } else {
                    formMessage.classList.remove('alert-success');
                    formMessage.classList.add('alert-danger');
                }
            } catch (error) {
                console.error('Error:', error);
                formMessage.style.display = 'block';
                formMessage.classList.remove('alert-success');
                formMessage.classList.add('alert-danger');
                formMessage.innerHTML = 'An error occurred. Please try again later.';
            } finally {
                // Restore button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
    }
});

// Project Modal Functions
function openProjectModal(projectId) {
    // This would open a modal with project details
    // For now, we'll show an alert - you can enhance this with a real modal
    alert('Project details for project #' + projectId + ' would open here.');
}

// Project Filter with AJAX (Optional enhancement)
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // For frontend-only filtering, just filter the displayed items
            const projectCards = document.querySelectorAll('.project-card');
            
            projectCards.forEach(card => {
                const category = card.getAttribute('data-category');
                const status = card.getAttribute('data-status');
                let shouldShow = false;

                if (filter === 'all') {
                    shouldShow = true;
                } else if (filter === 'completed' || filter === 'ongoing') {
                    shouldShow = status === filter;
                } else {
                    shouldShow = category === filter;
                }

                if (shouldShow) {
                    card.style.display = 'block';
                    if (typeof gsap !== 'undefined') {
                        try { gsap.to(card, { opacity: 1, duration: 0.4 }); } catch(e) { card.style.opacity = 1; }
                    } else { card.style.opacity = 1; }
                } else {
                    if (typeof gsap !== 'undefined') {
                        try {
                            gsap.to(card, { opacity: 0, duration: 0.4, onComplete: () => { card.style.display = 'none'; } });
                        } catch(e) { card.style.display = 'none'; }
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });
});

// Initialize tooltips if Bootstrap tooltips are used
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (typeof bootstrap !== 'undefined') {
        tooltips.forEach(tooltip => {
            try { new bootstrap.Tooltip(tooltip); } catch(e) {}
        });
    }
});

// Mobile menu smooth scrolling
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.navbar-nav a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href !== '#') {
                const offcanvas = document.querySelector('.navbar-collapse');
                if (offcanvas && offcanvas.classList.contains('show')) {
                    if (typeof bootstrap !== 'undefined') {
                        try {
                            const bsCollapse = new bootstrap.Collapse(offcanvas, { toggle: false });
                            bsCollapse.hide();
                        } catch (e) {}
                    } else {
                        // Fallback: remove 'show' class to close
                        offcanvas.classList.remove('show');
                    }
                }
            }
        });
    });
});

// Dedicated handler for nav-scroll links: try to scroll in-page, otherwise navigate to index.php with hash
document.addEventListener('DOMContentLoaded', function() {
    const navScrollLinks = document.querySelectorAll('.nav-scroll');
    navScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSelector = this.dataset.target || this.getAttribute('href');
            if (!targetSelector) return;

            const targetEl = document.querySelector(targetSelector);
            if (targetEl) {
                console.debug('nav-scroll: scrolling to', targetSelector);
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // close mobile nav if open
                const offcanvas = document.querySelector('.navbar-collapse');
                if (offcanvas && offcanvas.classList.contains('show')) {
                    if (typeof bootstrap !== 'undefined') {
                        try { new bootstrap.Collapse(offcanvas, { toggle: false }).hide(); } catch(e) { offcanvas.classList.remove('show'); }
                    } else {
                        offcanvas.classList.remove('show');
                    }
                }
            } else {
                // If target not on this page, resolve home URL via navbar brand href
                const brandLink = document.querySelector('.navbar-brand');
                const brandHref = brandLink ? brandLink.getAttribute('href') : 'index.php';
                const homeUrl = new URL(brandHref || 'index.php', window.location.href);
                homeUrl.hash = targetSelector.startsWith('#') ? targetSelector.substring(1) : targetSelector;
                console.debug('nav-scroll: target not found, navigating to', homeUrl.toString());
                window.location.href = homeUrl.toString();
            }
        });
    });
});

// Dynamic statistics update (if needed)
async function updateStatistics() {
    try {
        const response = await fetch('ajax/get_statistics.php');
        const data = await response.json();
        
        if (data.success) {
            const statsContainer = document.querySelector('.hero-stats');
            if (statsContainer) {
                statsContainer.innerHTML = '';
                data.statistics.forEach(stat => {
                    const statItem = document.createElement('div');
                    statItem.className = 'stat-item';
                    statItem.innerHTML = `
                        <h3 class="stat-number"><span class="counter">${stat.value}</span>${stat.suffix || ''}</h3>
                        <p class="stat-label">${stat.label}</p>
                    `;
                    statsContainer.appendChild(statItem);
                });
                
                // Re-initialize counters
                document.querySelectorAll('.counter').forEach(counter => {
                    if (!counter.classList.contains('counted')) {
                        counter.classList.add('counted');
                        animateCounter(counter);
                    }
                });
            }
        }
    } catch (error) {
        console.error('Error fetching statistics:', error);
    }
}

// Email validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Phone validation
function validatePhone(phone) {
    const phoneRegex = /^[\d\s+\-()]*$/;
    return phoneRegex.test(phone) && phone.replace(/\D/g, '').length >= 10;
}

// Custom form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const message = document.getElementById('message').value.trim();

            let isValid = true;

            if (!name || name.length < 3) {
                showFieldError('name', 'Name must be at least 3 characters');
                isValid = false;
            } else {
                clearFieldError('name');
            }

            if (!email || !validateEmail(email)) {
                showFieldError('email', 'Please enter a valid email');
                isValid = false;
            } else {
                clearFieldError('email');
            }

            if (phone && !validatePhone(phone)) {
                showFieldError('phone', 'Please enter a valid phone number');
                isValid = false;
            } else {
                clearFieldError('phone');
            }

            if (!message || message.length < 10) {
                showFieldError('message', 'Message must be at least 10 characters');
                isValid = false;
            } else {
                clearFieldError('message');
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.add('is-invalid');
        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }
}

function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.remove('is-invalid');
    }
}

// Smooth anchor links
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Fallback: capture navbar link clicks at document level in case other handlers fail
document.addEventListener('click', function(e) {
    const el = e.target.closest && e.target.closest('.navbar-nav a[href^="#"]');
    if (!el) return;

    try {
        const href = el.getAttribute('href');
        if (!href || href === '#') return;
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            console.debug('Navbar fallback: scrolling to', href);
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Close mobile menu if open
            const offcanvas = document.querySelector('.navbar-collapse');
            if (offcanvas && offcanvas.classList.contains('show')) {
                if (typeof bootstrap !== 'undefined') {
                    try { new bootstrap.Collapse(offcanvas, { toggle: false }).hide(); } catch(e) { offcanvas.classList.remove('show'); }
                } else {
                    offcanvas.classList.remove('show');
                }
            }
        }
    } catch (err) {
        // swallow errors silently
        console.debug('Navbar fallback click handler error:', err);
    }
});

// Performance optimization: Debounce function
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

// Local storage for form data (auto-save)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    
    if (form) {
        // Load saved data
        const savedData = localStorage.getItem('contactFormData');
        if (savedData) {
            const data = JSON.parse(savedData);
            Object.keys(data).forEach(key => {
                const field = form.elements[key];
                if (field) field.value = data[key];
            });
        }

        // Auto-save on input
        form.addEventListener('input', debounce(() => {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            localStorage.setItem('contactFormData', JSON.stringify(data));
        }, 500));

        // Clear saved data on successful submit
        form.addEventListener('submit', function() {
            localStorage.removeItem('contactFormData');
        });
    }
});

// Browser notification for messages (optional)
function notifyUser(title, options = {}) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, options);
    }
}

// Request notification permission
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Keyboard navigation support
document.addEventListener('keydown', function(e) {
    // Alt + C to focus contact form
    if (e.altKey && e.key === 'c') {
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.querySelector('input').focus();
        }
    }
});

// Accessibility: Skip to main content
document.addEventListener('DOMContentLoaded', function() {
    const skipLink = document.createElement('a');
    skipLink.href = '#main-content';
    skipLink.className = 'skip-to-main';
    skipLink.textContent = 'Skip to main content';
    skipLink.style.cssText = `
        position: absolute;
        top: -40px;
        left: 0;
        background: #00d4ff;
        color: #0f0f23;
        padding: 8px;
        text-decoration: none;
        z-index: 100;
    `;
    skipLink.addEventListener('focus', () => {
        skipLink.style.top = '0';
    });
    skipLink.addEventListener('blur', () => {
        skipLink.style.top = '-40px';
    });
    document.body.prepend(skipLink);
});

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});

// Console greeting
console.log('%cPixelara Web Solutions', 'font-size: 24px; font-weight: bold; color: #00d4ff;');
console.log('%cBuilding Digital Experiences That Sell', 'font-size: 14px; color: #7c3aed;');
