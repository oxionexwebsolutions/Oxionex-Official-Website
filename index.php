<?php
// Oxionex Web Solutions - Main Portfolio Website
// Modern, Premium, Highly Animated Portfolio

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database connection
require_once 'includes/db.php';

// Get projects for filter
try {
    $projects = $pdo->query("SELECT * FROM projects ORDER BY featured DESC, sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
    $services = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    $testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    $statistics = $pdo->query("SELECT * FROM statistics ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $projects = [];
    $services = [];
    $testimonials = [];
    $statistics = [];
}

if (empty($services)) {
    $services = [
        ['id' => 1, 'title' => 'Website Development', 'subtitle' => 'Build modern websites and web systems', 'icon' => 'fas fa-globe', 'points' => "Landing Page Development\nBusiness Website Development\nE-Commerce Website Development\nPortfolio Website Design\nCustom Web Applications\nAdmin Panel & Dashboard Development\nOnline Booking & Registration Systems"],
        ['id' => 2, 'title' => 'Website Design & Optimization', 'subtitle' => 'Improve UI, speed, and site upkeep', 'icon' => 'fas fa-paint-brush', 'points' => "Website Redesign & UI/UX Improvement\nResponsive Mobile-Friendly Websites\nWebsite Speed & Security Optimization\nWebsite Maintenance & Management"],
        ['id' => 3, 'title' => 'Social Media Management', 'subtitle' => 'Run pages, content, and designs', 'icon' => 'fas fa-share-alt', 'points' => "Social Media Page Handling\nInstagram Page Management\nFacebook Page Management\nContent Creation for Social Media\nSocial Media Post Design"],
        ['id' => 4, 'title' => 'Video Production & Marketing', 'subtitle' => 'Create videos and drive traffic', 'icon' => 'fas fa-video', 'points' => "Reel Editing & Short Video Editing\nPromotional Video Editing\nAdvertisement Video Creation\nFacebook & Instagram Ads\nDigital Marketing Services\nSEO & Online Visibility Optimization\nWhatsApp Marketing Integration\nEmail Marketing Campaigns\nBrand Advertisement Campaigns"],
        ['id' => 5, 'title' => 'Branding & Graphic Design', 'subtitle' => 'Build a strong visual identity', 'icon' => 'fas fa-palette', 'points' => "Branding & Identity Design\nLogo Design\nBrochure Design\nFlyer & Pamphlet Design\nBanner & Poster Design\nBusiness Card Design\nProduct Catalog Design\nCustom Templates & Creative Assets\nContent Writing for Websites & Brands"],
        ['id' => 6, 'title' => 'Hosting & Technical Services', 'subtitle' => 'Deploy, secure, and support systems', 'icon' => 'fas fa-cube', 'points' => "Website Hosting & Domain Setup\nTechnical Support & IT Consultation\nStartup Online Presence Setup\nCreative Marketing Solutions\nCloud Hosting & Server Management\nSSL & Website Security Integration\nBug Fixing & Website Error Resolution\nAPI Integration & Automation\nDatabase Design & Integration (MySQL / SQL)\nAI-Powered Website Features & Automation\nOngoing Website Management & Support\nCustom Project Development for Businesses"],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Oxionex Web Solutions - Premium Web Design, Development, and Digital Solutions. We create stunning websites that convert visitors into paying clients.">
    <meta name="keywords" content="web design, web development, eCommerce, admin dashboard, SaaS, digital agency">
    <meta name="author" content="Oxionex Web Solutions">

    <script>
        (function() {
            try {
                var storedTheme = localStorage.getItem('oxionex-theme');
                var preferredTheme = storedTheme === 'light' || storedTheme === 'dark'
                    ? storedTheme
                    : (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                document.documentElement.setAttribute('data-theme', preferredTheme);
            } catch (e) {}
        })();
    </script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Swiper JS for Testimonials -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/cursor-follower.css">
    <link rel="stylesheet" href="assets/css/project-preview.css">
    
    <title>Oxionex Web Solutions - Premium Web Design & Development</title>
</head>
<body>
    <!-- Preloader -->
    <div id="preloader" class="preloader">
        <div class="preloader-content">
            <div class="loader-diagonal">
                <div class="loader-square square-1"></div>
                <div class="loader-square square-2"></div>
                <div class="loader-square square-3"></div>
                <div class="loader-square square-4"></div>
            </div>
            <div class="preloader-logo">
                <p style="font-weight: 800; font-size: 1.5rem; letter-spacing: 2px; margin: 0;">OXIONEX</p>
                <p style="font-size: 0.85rem; letter-spacing: 1px; color: #999; margin: 0;">WEB SOLUTIONS</p>
            </div>
            <p class="preloader-text">Building your experience...</p>
        </div>
    </div>

    <!-- Navigation Header -->
    <?php include 'includes/header.php'; ?>

    <!-- HERO SECTION -->
    <section id="hero" class="hero-section">
        <div class="hero-background">
            <div class="animated-gradient"></div>
            <div class="floating-blob blob-1"></div>
            <div class="floating-blob blob-2"></div>
            <div class="floating-blob blob-3"></div>
            <canvas id="particleCanvas"></canvas>
        </div>

        <div class="container-fluid">
            <div class="row align-items-start align-items-lg-center">
                <div class="col-lg-6 hero-content">
                    <div class="hero-title">
                        <h1 class="display-1 fw-bold mb-4">
                            <span class="gradient-text">Building Digital</span>
                            <span class="gradient-text-secondary">Experiences</span>
                            <span class="highlight-text"> That Sell.</span>
                        </h1>
                    </div>

                    <p class="hero-subtitle lead mb-5">
                        We craft premium websites, web systems, eCommerce stores, admin panels, and modern digital solutions for ambitious businesses that demand excellence.
                    </p>

                    <div class="hero-stats mb-5">
                        <div class="stat-item">
                            <h3 class="stat-number"><span class="counter">50</span>+</h3>
                            <p class="stat-label">Projects Delivered</p>
                        </div>
                        <div class="stat-item">
                            <h3 class="stat-number"><span class="counter">40</span>+</h3>
                            <p class="stat-label">Happy Clients</p>
                        </div>
                        <div class="stat-item">
                            <h3 class="stat-number"><span class="counter">3</span>+</h3>
                            <p class="stat-label">Years of Excellence</p>
                        </div>
                    </div>

                    <div class="hero-buttons">
                        <button type="button" class="btn btn-primary-glow btn-lg" onclick="document.getElementById('portfolio')?.scrollIntoView({behavior:'smooth', block:'start'}); window.location.hash='portfolio';">
                            <span>View Our Portfolio</span>
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <a href="start_project.php" class="btn btn-outline-light btn-lg ms-3">
                            <span>Start Your Project</span>
                            <i class="fas fa-star ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 hero-visual" id="hero-cards">
                    <div class="floating-card card-1">
                        <div class="card-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <p>Custom Development</p>
                        <small style="color: var(--gray-text); font-size: 0.85rem; margin-top: 0.5rem; display: block;">Robust & Scalable</small>
                    </div>
                    <div class="floating-card card-2">
                        <div class="card-icon">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <p>Modern Design</p>
                        <small style="color: var(--gray-text); font-size: 0.85rem; margin-top: 0.5rem; display: block;">Stunning UX/UI</small>
                    </div>
                    <div class="floating-card card-3">
                        <div class="card-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <p>Fast Performance</p>
                        <small style="color: var(--gray-text); font-size: 0.85rem; margin-top: 0.5rem; display: block;">Lightning Quick</small>
                    </div>
                    <div class="hero-image-wrapper">
                        <div class="hero-image-frame">
                            <img src="assets/images/hero-showcase.svg" alt="Digital Solutions" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    <!-- SERVICES SECTION -->
    <section id="services" class="services-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-label">OUR EXPERTISE</span>
                <h2 class="section-title">Services We Offer</h2>
                <p class="section-subtitle">Tap a topic to open the detailed services inside</p>
            </div>

            <div class="services-grid services-accordion-grid">
                <?php foreach ($services as $service): ?>
                <?php
                    $serviceId = 'service-' . (int) $service['id'];
                    $points = preg_split('/\R+/', trim($service['points'] ?? '')) ?: [];
                ?>
                <div class="service-card">
                    <button class="service-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $serviceId; ?>" aria-expanded="false" aria-controls="<?php echo $serviceId; ?>">
                        <span class="service-heading">
                            <span class="service-icon"><i class="<?php echo htmlspecialchars($service['icon'] ?? 'fas fa-cube'); ?>"></i></span>
                            <span>
                                <span class="service-kicker"><?php echo htmlspecialchars($service['title']); ?></span>
                                <span class="service-title-inline"><?php echo htmlspecialchars($service['subtitle']); ?></span>
                            </span>
                        </span>
                        <i class="fas fa-chevron-down service-arrow"></i>
                    </button>
                    <div class="collapse service-collapse" id="<?php echo $serviceId; ?>">
                        <ul class="service-points">
                            <?php foreach ($points as $point): ?>
                                <?php if (trim($point) !== ''): ?>
                                    <li><?php echo htmlspecialchars(trim($point)); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- PORTFOLIO SECTION -->
    <section id="portfolio" class="portfolio-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-label">OUR WORK</span>
                <h2 class="section-title">Featured Projects</h2>
                <p class="section-subtitle">Explore our latest projects and see what we're capable of creating</p>
            </div>

            <!-- Filter Buttons -->
            <div class="filter-container text-center mb-5">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <button class="filter-btn" data-filter="completed">Completed</button>
                <button class="filter-btn" data-filter="ongoing">Ongoing</button>
                <button class="filter-btn" data-filter="ecommerce">eCommerce</button>
                <button class="filter-btn" data-filter="business">Business</button>
                <button class="filter-btn" data-filter="portfolio">Portfolio</button>
                <button class="filter-btn" data-filter="custom">Custom Systems</button>
                <button class="filter-btn" data-filter="dashboard">Dashboards</button>
            </div>

            <!-- Projects Grid -->
            <div class="projects-grid" id="projectsContainer">
                <?php foreach ($projects as $project): ?>
                <div class="project-card" data-project-id="<?php echo $project['id']; ?>" data-category="<?php echo htmlspecialchars($project['category']); ?>" data-status="<?php echo htmlspecialchars($project['status']); ?>" onclick="openProjectPreview(<?php echo $project['id']; ?>, this)" style="cursor: pointer;">
                    <div class="project-image">
                        <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="img-fluid">
                        <div class="project-overlay">
                            <div class="overlay-content">
                                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                                <p><?php echo htmlspecialchars($project['short_description']); ?></p>
                                <div class="project-tech">
                                    <?php 
                                    $techs = explode(', ', $project['technologies']);
                                    foreach (array_slice($techs, 0, 3) as $tech): 
                                    ?>
                                        <span class="tech-badge"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="overlay-buttons">
                                    <button class="btn btn-sm btn-primary-glow" onclick="event.stopPropagation(); openProjectPreview(<?php echo $project['id']; ?>, this.closest('.project-card'))">
                                        <i class="fas fa-eye"></i> Live Preview
                                    </button>
                                    <?php if (!empty($project['live_link'])): ?>
                                    <a href="<?php echo htmlspecialchars($project['live_link']); ?>" target="_blank" class="btn btn-sm btn-outline-light" onclick="event.stopPropagation();">
                                        <i class="fas fa-external-link"></i> Visit Site
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="project-status-badge">
                            <span class="status-<?php echo htmlspecialchars($project['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($project['status'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="project-info">
                        <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                        <p class="project-category"><?php echo htmlspecialchars(ucfirst($project['category'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US SECTION -->
    <section id="why-us" class="why-us-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-label">WHY OXIONEX</span>
                <h2 class="section-title">Why Choose Us</h2>
                <p class="section-subtitle">We deliver more than just websites - we deliver results</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="why-us-item">
                        <div class="why-us-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Lightning Fast</h3>
                        <p>Optimized performance that keeps your website blazing fast, improving user experience and SEO rankings.</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="why-us-item">
                        <div class="why-us-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Fully Responsive</h3>
                        <p>Perfect on all devices. Your website looks and works flawlessly on mobile, tablet, and desktop.</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="why-us-item">
                        <div class="why-us-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3>Secure & Protected</h3>
                        <p>Enterprise-grade security with SSL encryption, secure databases, and protection against common vulnerabilities.</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="why-us-item">
                        <div class="why-us-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3>Data-Driven Results</h3>
                        <p>Analytics integration to track performance, understand user behavior, and optimize for conversions.</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="why-us-item">
                        <div class="why-us-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Premium Support</h3>
                        <p>Dedicated support team ready to help. We're here before, during, and after your project launch.</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="why-us-item">
                        <div class="why-us-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3>Future Proof</h3>
                        <p>Scalable solutions built with modern technologies that grow with your business needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS SECTION -->
    <section id="testimonials" class="testimonials-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-label">WHAT CLIENTS SAY</span>
                <h2 class="section-title">Testimonials</h2>
                <p class="section-subtitle">Hear from our satisfied clients and successful projects</p>
            </div>

            <!-- Testimonials Slider -->
            <div class="swiper testimonials-slider">
                <div class="swiper-wrapper">
                    <?php foreach ($testimonials as $testimonial): ?>
                    <div class="swiper-slide">
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <div class="quote-icon">
                                    <i class="fas fa-quote-left"></i>
                                </div>
                                <div class="client-info">
                                    <img src="<?php echo htmlspecialchars($testimonial['client_image']); ?>" alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>" class="client-image">
                                    <div>
                                        <h4><?php echo htmlspecialchars($testimonial['client_name']); ?></h4>
                                        <p><?php echo htmlspecialchars($testimonial['client_role']); ?> at <?php echo htmlspecialchars($testimonial['client_company']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <p class="testimonial-message"><?php echo htmlspecialchars($testimonial['message']); ?></p>
                            <div class="testimonial-footer">
                                <div class="rating">
                                    <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                        <i class="fas fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>

    <!-- PROCESS SECTION -->
    <section id="process" class="process-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-label">OUR WORKFLOW</span>
                <h2 class="section-title">Our Process</h2>
                <p class="section-subtitle">From concept to launch - a streamlined approach to building excellence</p>
            </div>

            <div class="process-timeline">
                <div class="process-step">
                    <div class="step-number">01</div>
                    <div class="step-content">
                        <h3>Consultation</h3>
                        <p>We understand your vision, goals, and target audience through in-depth discussions and research.</p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">02</div>
                    <div class="step-content">
                        <h3>Planning & Strategy</h3>
                        <p>We create a detailed project roadmap with timelines, deliverables, and technology stack selection.</p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">03</div>
                    <div class="step-content">
                        <h3>Design & Prototyping</h3>
                        <p>Beautiful UI/UX designs and interactive prototypes to visualize the final product before development.</p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">04</div>
                    <div class="step-content">
                        <h3>Development</h3>
                        <p>Expert coding with clean, scalable architecture and best practices for performance and security.</p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">05</div>
                    <div class="step-content">
                        <h3>Testing & QA</h3>
                        <p>Rigorous testing across devices and browsers to ensure everything works perfectly.</p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">06</div>
                    <div class="step-content">
                        <h3>Launch & Support</h3>
                        <p>Smooth deployment and ongoing support to ensure your website thrives in the real world.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA BANNER SECTION -->
    <section id="cta-banner" class="cta-banner py-5">
        <div class="cta-background">
            <div class="animated-gradient-cta"></div>
        </div>
        <div class="container position-relative z-1">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="cta-title">Ready to Transform Your Digital Presence?</h2>
                    <p class="cta-subtitle">Let's build a website that impresses and converts</p>
                </div>
                <div class="col-lg-4 text-lg-end text-center mt-4 mt-lg-0">
                    <a href="start_project.php" class="btn btn-primary-glow btn-lg">
                        <span>Start Your Project</span>
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section id="contact" class="contact-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-label">GET IN TOUCH</span>
                <h2 class="section-title">Contact Us</h2>
                <p class="section-subtitle">Let's discuss your next project and create something amazing together</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <form id="contactForm" class="contact-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-dark border-secondary text-light" id="name" name="name" placeholder="Your Name" required>
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control bg-dark border-secondary text-light" id="email" name="email" placeholder="Your Email" required>
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control bg-dark border-secondary text-light" id="phone" name="phone" placeholder="Phone Number">
                                    <label for="phone">Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select bg-dark border-secondary text-light" id="business_type" name="business_type">
                                        <option value="">Select Business Type</option>
                                        <option value="startups">Startups</option>
                                        <option value="small-business">Small Business</option>
                                        <option value="enterprise">Enterprise</option>
                                        <option value="agency">Agency</option>
                                        <option value="freelancer">Freelancer</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <label for="business_type">Business Type</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <select class="form-select bg-dark border-secondary text-light" id="budget" name="budget">
                                        <option value="">Select Budget Range</option>
                                        <option value="5000-10000">₹5K - ₹10K</option>
                                        <option value="10000-25000">₹10K - ₹25K</option>
                                        <option value="25000-50000">₹25K - ₹50K</option>
                                        <option value="50000-100000">₹50K - ₹100K</option>
                                        <option value="100000+">₹100K+</option>
                                    </select>
                                    <label for="budget">Budget Range</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control bg-dark border-secondary text-light" id="message" name="message" placeholder="Tell us about your project" style="min-height: 150px;" required></textarea>
                                    <label for="message">Message</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-glow btn-lg w-100">
                                    <span>Send Inquiry</span>
                                    <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="formMessage" class="alert mt-3" style="display: none;"></div>
                </div>

                <div class="col-lg-4">
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email</h4>
                                <p><a href="mailto:hello@oxionex.com">hello@oxionex.com</a></p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Phone</h4>
                                <p><a href="tel:+917048811552">+91 70488 11552</a></p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Location</h4>
                                <p>Rajkot, Gujarat, India</p>
                            </div>
                        </div>

                        <div class="contact-socials mt-4">
                            <p class="mb-3">Connect With Us</p>
                            <div class="social-links">
                                <a href="https://instagram.com/oxionex" target="_blank" class="social-link">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="https://twitter.com/oxionex" target="_blank" class="social-link">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://linkedin.com/company/oxionex" target="_blank" class="social-link">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="https://wa.me/917048811552" target="_blank" class="social-link">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    
    <!-- GSAP Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
    <!-- Custom JS Files -->
    <script src="assets/js/particles.js"></script>
    <script src="assets/js/animations.js"></script>
    <script src="assets/js/project-preview.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
