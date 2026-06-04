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
define('DB_HOST', 'sql206.infinityfree.com');      // Database host
define('DB_USER', 'if0_42061746');                 // Database username
define('DB_PASS', 'OxionexWebSol');                // Database password
define('DB_NAME', 'if0_42061746_oxionexwebsloutions'); // Database name
define('DB_PORT', 3306);             // Database port

// =============================================================================
// EMAIL CONFIGURATION
// =============================================================================
// Admin email where inquiries are sent
define('ADMIN_EMAIL', 'hello@oxionex.com');

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
define('BUSINESS_EMAIL', 'hello@oxionex.com');
define('BUSINESS_LOCATION', 'Rajkot, India');

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
define('DEBUG_MODE', true);

// Log errors to file
define('LOG_ERRORS', true);
define('LOG_FILE', __DIR__ . '/../logs/error.log');

// =============================================================================
// API CONFIGURATION
// =============================================================================
// Enable API rate limiting
define('RATE_LIMIT_ENABLED', true);

// Rate limit: max requests per minute from single IP
define('RATE_LIMIT_REQUESTS', 60);

// =============================================================================
// UPLOAD CONFIGURATION
// =============================================================================
// Maximum file upload size (in bytes) - 5MB
define('MAX_UPLOAD_SIZE', 5242880);

// =============================================================================
// DEBUG / ERROR REPORTING
// =============================================================================
// Enable full error reporting during troubleshooting. Set DEBUG_MODE to true
// to display errors on-page. In production set DEBUG_MODE to false.
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
