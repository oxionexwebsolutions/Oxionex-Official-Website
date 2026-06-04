/* ======================================================
    OXIONEX - ADVANCED ANIMATIONS
   GSAP and ScrollTrigger animations for premium effects
   ====================================================== */

// Register GSAP plugins - REMOVED ScrollTrigger

// Counter Animation Function
function animateCounter(element, duration = 2) {
    if (!element.textContent) return;

    const target = parseInt(element.textContent);
    const increment = target / (duration * 60);
    let current = 0;

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 1000 / 60);
}

// Initialize Counter Animations
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    
    const observerOptions = {
        threshold: 0.5
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                entry.target.classList.add('counted');
                animateCounter(entry.target);
            }
        });
    }, observerOptions);

    counters.forEach(counter => observer.observe(counter));
});

// Scroll animations with GSAP
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for hero buttons
    const buttons = document.querySelectorAll('a[href^="#"]');
    if (!window.gsap) {
        return;
    }
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href !== '#') {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    gsap.to(window, {
                        scrollTo: {
                            y: target,
                            autoKill: false
                        },
                        duration: 1,
                        ease: "power2.inOut"
                    });
                }
            }
        });
    });
});

// Hero scroll cue for the translucent cards - REMOVED

// Service cards hover animation
document.addEventListener('DOMContentLoaded', function() {
    const serviceCards = document.querySelectorAll('.service-card');
    if (!window.gsap) {
        return;
    }
    
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            gsap.to(this, {
                y: -10,
                duration: 0.4,
                ease: "power2.out"
            });
        });

        card.addEventListener('mouseleave', function() {
            gsap.to(this, {
                y: 0,
                duration: 0.4,
                ease: "power2.out"
            });
        });
    });
});

// Project cards enhanced animations
document.addEventListener('DOMContentLoaded', function() {
    const projectCards = document.querySelectorAll('.project-card');
    if (!window.gsap) {
        return;
    }
    
    projectCards.forEach(card => {
        const image = card.querySelector('.project-image img');
        
        card.addEventListener('mouseenter', function() {
            if (image) {
                gsap.to(image, {
                    scale: 1.15,
                    duration: 0.5,
                    ease: "power2.out"
                });
            }
        });

        card.addEventListener('mouseleave', function() {
            if (image) {
                gsap.to(image, {
                    scale: 1,
                    duration: 0.5,
                    ease: "power2.out"
                });
            }
        });
    });
});

// Floating buttons animation - REMOVED

// Scroll reveal animations for sections - REMOVED

// Animated heading text effect
document.addEventListener('DOMContentLoaded', function() {
    const headings = document.querySelectorAll('.section-title');
    if (!window.gsap) {
        return;
    }
    
    headings.forEach(heading => {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    gsap.from(entry.target, {
                        opacity: 0,
                        y: 20,
                        duration: 0.8,
                        ease: "power2.out"
                    });
                    observer.unobserve(entry.target);
                }
            });
        });
        observer.observe(heading);
    });
});

// Button glow effect on click
document.addEventListener('DOMContentLoaded', function() {
    const glowButtons = document.querySelectorAll('.btn-primary-glow');
    
    glowButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple-effect');

            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// Form focus animations
document.addEventListener('DOMContentLoaded', function() {
    const formInputs = document.querySelectorAll('.form-control, .form-select');
    if (!window.gsap) {
        return;
    }
    
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            gsap.to(this, {
                boxShadow: '0 0 20px rgba(0, 212, 255, 0.3)',
                duration: 0.3
            });
        });

        input.addEventListener('blur', function() {
            gsap.to(this, {
                boxShadow: '0 0 0px rgba(0, 212, 255, 0)',
                duration: 0.3
            });
        });
    });
});

// Parallax effect on mouse move
document.addEventListener('DOMContentLoaded', function() {
    const parallaxElements = document.querySelectorAll('.floating-blob');
    if (!window.gsap) {
        return;
    }
    
    document.addEventListener('mousemove', (e) => {
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;

        parallaxElements.forEach((element, index) => {
            const moveX = (x - 0.5) * (50 * (index + 1));
            const moveY = (y - 0.5) * (50 * (index + 1));

            gsap.to(element, {
                x: moveX,
                y: moveY,
                duration: 0.5,
                ease: "power2.out",
                overwrite: 'auto'
            });
        });
    });
});

// Testimonial slider initialization
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.Swiper === 'undefined') {
        return;
    }
    const swiperTestimonials = new Swiper('.testimonials-slider', {
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            dynamicBullets: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false
        },
        speed: 800,
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        breakpoints: {
            640: {
                slidesPerView: 1,
                spaceBetween: 20
            },
            1024: {
                slidesPerView: 1,
                spaceBetween: 30
            }
        }
    });
});

// Preloader fade out
window.addEventListener('load', function() {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 3000);
    }
});

// Navigation active link animation
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    window.addEventListener('scroll', () => {
        let current = '';
        
        const sections = document.querySelectorAll('section');
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 200) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').slice(1) === current) {
                link.classList.add('active');
            }
        });
    });
});

// Project filter animation
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');
    if (!window.gsap) {
        return;
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Animate projects
            projectCards.forEach(card => {
                const category = card.getAttribute('data-category');
                const status = card.getAttribute('data-status');

                if (filter === 'all') {
                    gsap.to(card, {
                        opacity: 1,
                        duration: 0.4,
                        display: 'block'
                    });
                } else if (filter === 'completed' || filter === 'ongoing') {
                    if (status === filter) {
                        gsap.to(card, {
                            opacity: 1,
                            duration: 0.4,
                            display: 'block'
                        });
                    } else {
                        gsap.to(card, {
                            opacity: 0.3,
                            duration: 0.4
                        });
                    }
                } else {
                    if (category === filter) {
                        gsap.to(card, {
                            opacity: 1,
                            duration: 0.4,
                            display: 'block'
                        });
                    } else {
                        gsap.to(card, {
                            opacity: 0.3,
                            duration: 0.4
                        });
                    }
                }
            });
        });
    });
});

// Stagger animation for cards on initial load - removed to avoid blank cards on slower devices

// Magnetic button effect
document.addEventListener('DOMContentLoaded', function() {
    const magneticButtons = document.querySelectorAll('.btn-primary-glow');
    if (!window.gsap) {
        return;
    }
    
    magneticButtons.forEach(button => {
        button.addEventListener('mousemove', (e) => {
            const rect = button.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            gsap.to(button, {
                x: x * 0.2,
                y: y * 0.2,
                duration: 0.3,
                overwrite: 'auto'
            });
        });

        button.addEventListener('mouseleave', () => {
            gsap.to(button, {
                x: 0,
                y: 0,
                duration: 0.3
            });
        });
    });
});

// Smooth scroll for mobile menu
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuLinks = document.querySelectorAll('.navbar-nav a');
    const navbar = document.querySelector('.navbar');

    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', () => {
            const navbarToggle = document.querySelector('.navbar-toggler');
            if (navbarToggle && window.innerWidth < 992) {
                navbarToggle.click();
            }
        });
    });
});

// Lazy load images with fade-in effect
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[data-src]');
    if (!window.gsap) {
        return;
    }
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.getAttribute('data-src');
                img.removeAttribute('data-src');
                
                gsap.to(img, {
                    opacity: 1,
                    duration: 0.5
                });
                
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => {
        img.style.opacity = '0';
        imageObserver.observe(img);
    });
});
