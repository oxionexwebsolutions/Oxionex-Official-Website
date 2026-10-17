<?php
/**
 * Oxionex Web Solutions - Configuration File
 * Store all sensitive configuration in one place
 * 
 * IMPORTANT: Update these values before going to production!
 */

// =============================================================================
// DATABASE CONFIGURATION
// =============================================================================
// Keep deployment credentials in config.local.php, preferably one directory
// above the web root. The repository contains only the placeholder example.
$localConfigPaths = [
	dirname(__DIR__) . '/config.local.php',
	__DIR__ . '/config.local.php',
];
foreach ($localConfigPaths as $localConfigPath) {
	if (is_readable($localConfigPath)) {
		require_once $localConfigPath;
		break;
	}
}

define('DB_HOST', defined('DB_HOST') ? DB_HOST : '');
define('DB_USER', defined('DB_USER') ? DB_USER : '');
define('DB_PASS', defined('DB_PASS') ? DB_PASS : '');
define('DB_NAME', defined('DB_NAME') ? DB_NAME : '');
define('DB_PORT', defined('DB_PORT') ? DB_PORT : 3306);

// =============================================================================
// EMAIL CONFIGURATION
// =============================================================================
// Admin email where inquiries are sent
define('ADMIN_EMAIL', 'oxionexwebsolutions@gmail.com');

// Sender email (must be valid for your mail server)
define('SENDER_EMAIL', 'noreply@oxionex.com');

// Support email for user-facing messages
define('SUPPORT_EMAIL', 'support@oxionex.com');

// =============================================================================
// BUSINESS CONFIGURATION
// =============================================================================
define('BUSINESS_NAME', 'Oxionex Web Solutions');
define('BUSINESS_PHONE', '+91 7048811552');
define('BUSINESS_WHATSAPP', '+917048811552');
define('BUSINESS_EMAIL', 'oxionexwebsolutions@gmail.com');
define('BUSINESS_LOCATION', 'Rajkot, India');
define('SITE_URL', defined('SITE_URL') ? SITE_URL : 'https://oxionex.com');

// Social media links
define('SOCIAL_INSTAGRAM', 'https://instagram.com/oxionex');
define('SOCIAL_TWITTER', 'https://twitter.com/oxionex');
define('SOCIAL_LINKEDIN', 'https://linkedin.com/company/oxionex');

// =============================================================================
// SECURITY CONFIGURATION
// =============================================================================
// Session timeout (in seconds) - 30 minutes
define('SESSION_TIMEOUT', 1800);

// Max login attempts before lockout
define('MAX_LOGIN_ATTEMPTS', 5);

// Lockout duration (in seconds)
define('LOCKOUT_DURATION', 900);

// =============================================================================
// ERROR HANDLING
// =============================================================================
// Show errors (false in production)
define('DEBUG_MODE', false);

// Log errors to file
define('LOG_ERRORS', true);
define('LOG_FILE', __DIR__ . '/../logs/error.log');

// =============================================================================
// API CONFIGURATION
// =============================================================================
// Enable API rate limiting
define('RATE_LIMIT_ENABLED', true);

// Rate limit: max requests per minute from single IP
define('RATE_LIMIT_REQUESTS', 10);
define('RATE_LIMIT_WINDOW', 60);
define('FORM_MIN_SECONDS', 3);
define('FORM_MAX_SECONDS', 86400);

// =============================================================================
// UPLOAD CONFIGURATION
// =============================================================================
// Maximum file upload size (in bytes) - 5MB
define('MAX_UPLOAD_SIZE', 5242880);
define('MAX_UPLOAD_FILES', 5);
define('MAX_UPLOAD_TOTAL_SIZE', 15728640);

// =============================================================================
// DEBUG / ERROR REPORTING
// =============================================================================
// Never display internal errors to visitors. Enable DEBUG_MODE only in a
// private local environment when troubleshooting.
error_reporting(E_ALL);
if (defined('DEBUG_MODE') && DEBUG_MODE) {
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
} else {
	ini_set('display_errors', '0');
}

// Allowed file types for uploads
define('ALLOWED_UPLOAD_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Upload directory
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// =============================================================================
// PRODUCTION CHECKLIST
// =============================================================================
/*
 * Before deploying to production, ensure you:
 * 
 * 1. DATABASE
 *    - Update DB_HOST, DB_USER, DB_PASS, DB_NAME
 *    - Use strong database password
 *    - Enable database backups
 *    - Update database credentials in includes/db.php
 * 
 * 2. EMAIL
 *    - Configure mail server (SMTP/SendGrid/AWS SES)
 *    - Update ADMIN_EMAIL and SENDER_EMAIL
 *    - Test email delivery
 * 
 * 3. SECURITY
 *    - Change default admin password
 *    - Enable HTTPS with SSL certificate
 *    - Update .htaccess for production
 *    - Set DEBUG_MODE to false
 * 
 * 4. BUSINESS INFO
 *    - Update all BUSINESS_* constants with real info
 *    - Update SOCIAL_* links
 *    - Verify contact information
 * 
 * 5. FILES & PERMISSIONS
 *    - Set proper file permissions (644 for files, 755 for dirs)
 *    - Create logs directory with proper permissions
 *    - Create uploads directory with proper permissions
 *    - Remove sample data from database (optional)
 * 
 * 6. MONITORING
 *    - Enable error logging
 *    - Set up uptime monitoring
 *    - Configure backup system
 *    - Monitor database performance
 * 
 * 7. TESTING
 *    - Test all forms
 *    - Test email notifications
 *    - Test admin panel
 *    - Test on mobile devices
 *    - Run security audit
 */
?>
