<?php
/**
 * Oxionex Web Solutions - Global Footer
 */
$businessEmail = defined('BUSINESS_EMAIL') ? BUSINESS_EMAIL : '';
$businessPhone = defined('BUSINESS_PHONE') ? BUSINESS_PHONE : '';
$businessWhatsapp = defined('BUSINESS_WHATSAPP') ? preg_replace('/\D+/', '', BUSINESS_WHATSAPP) : '';
$businessLocation = defined('BUSINESS_LOCATION') ? BUSINESS_LOCATION : '';
$linkedinUrl = defined('SOCIAL_LINKEDIN') ? SOCIAL_LINKEDIN : '';
?>

<footer>
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="logo mb-3">
                    <i class="fas fa-diamond"></i> Oxionex
                </div>
                <p class="mb-4">Building premium digital experiences that convert visitors into loyal clients. We specialize in modern web design, development, and digital solutions.</p>
                <div class="social-links social-footer-links">
                    <a href="<?php echo htmlspecialchars(SOCIAL_INSTAGRAM, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" title="Instagram" class="social-link instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="<?php echo htmlspecialchars($linkedinUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" title="LinkedIn" class="social-link linkedin">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://wa.me/<?php echo htmlspecialchars($businessWhatsapp, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" title="WhatsApp" class="social-link whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5>Quick Links</h5>
                <ul>
                    <li><a href="#hero">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#portfolio">Portfolio</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5>Services</h5>
                <ul>
                    <li><a href="#services">Business Websites</a></li>
                    <li><a href="#services">eCommerce Stores</a></li>
                    <li><a href="#services">Admin Dashboards</a></li>
                    <li><a href="#services">Custom Web Systems</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5>Contact</h5>
                <ul>
                    <li><a href="mailto:<?php echo htmlspecialchars($businessEmail, ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($businessEmail, ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <li><a href="tel:<?php echo htmlspecialchars($businessPhone, ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($businessPhone, ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <li><a href="https://wa.me/<?php echo htmlspecialchars($businessWhatsapp, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp me-2"></i>WhatsApp: <?php echo htmlspecialchars($businessPhone, ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <li><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($businessLocation, ENT_QUOTES, 'UTF-8'); ?></li>
                    <li><i class="fas fa-user-tie me-2"></i>General Manager: Bharmal Ammar <br>
                        <i class="fas fa-user-friends me-2"></i>Executive Director: Ibrahim Bharmal
</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Oxionex Web Solutions. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Swiper.js -->
<script defer src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Bootstrap JS (bundle includes Popper) -->
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- GSAP for animations -->
<script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.2/dist/gsap.min.js"></script>

<!-- Cursor follower (small emoji that follows mouse) -->
<script defer src="assets/js/cursor-follower.js"></script>

</body>
</html>
