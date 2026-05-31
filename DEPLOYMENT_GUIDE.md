# Pixelara Web Solutions - Production Deployment Guide

**Last Updated:** May 12, 2026  
**Version:** 1.0.0

## 📋 Pre-Deployment Checklist

### 1. Database Setup ✓

```bash
# Option 1: Import via command line
mysql -u root -p pixelara_db < database.sql

# Option 2: Import via phpMyAdmin
1. Create database: pixelara_db
2. Import database.sql
```

**Verify tables created:**
- [ ] projects
- [ ] contacts
- [ ] testimonials
- [ ] statistics
- [ ] admin_users

### 2. Configuration Files ✓

**Step 1: Update config.php**
```php
// Database credentials
define('DB_HOST', 'your-db-host');
define('DB_USER', 'your-db-user');
define('DB_PASS', 'your-db-password');
define('DB_NAME', 'pixelara_db');

// Email settings
define('ADMIN_EMAIL', 'your-email@yourdomain.com');
define('SENDER_EMAIL', 'noreply@yourdomain.com');

// Business info
define('BUSINESS_NAME', 'Your Company Name');
define('BUSINESS_PHONE', 'Your Phone');
define('BUSINESS_WHATSAPP', 'Your WhatsApp');
```

**Step 2: Update includes/db.php**
- No changes needed (uses config.php)

**Step 3: Create logs directory**
```bash
mkdir -p logs
chmod 755 logs
```

### 3. Security Hardening ✓

**Change Default Admin Credentials**

Generate new bcrypt password hash:
```php
<?php echo password_hash('your_new_password', PASSWORD_BCRYPT); ?>
```

Update database:
```sql
UPDATE admin_users 
SET password = '$2y$10$YOUR_NEW_HASH_HERE' 
WHERE username = 'admin';
```

**Or change username:**
```sql
UPDATE admin_users 
SET username = 'your_new_admin_username' 
WHERE id = 1;
```

**Enable HTTPS**
```apache
# In .htaccess (uncomment lines)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 4. Email Configuration ✓

**Option 1: Using PHP mail() function**
```php
// In config.php - ensure mail server is configured on server
// Update SENDER_EMAIL to a valid domain email
```

**Option 2: Using SMTP (Recommended)**

Update contact_submit.php to use PHPMailer or SwiftMailer:

```php
// Install via Composer
composer require phpmailer/phpmailer

// Use in contact_submit.php
require '../vendor/autoload.php';
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->Host = 'smtp.gmail.com';
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'app-password';
$mail->addAddress(ADMIN_EMAIL);
$mail->Subject = $subject;
$mail->Body = $email_body;
$mail->send();
```

**Option 3: Using SendGrid API**

```php
// Sign up at sendgrid.com
// Update contact_submit.php with SendGrid integration
$mailRequest = new \SendGrid\Mail\Mail();
$mailRequest->setFrom(SENDER_EMAIL);
$mailRequest->addTo(ADMIN_EMAIL);
$mailRequest->setSubject($subject);
$mailRequest->addContent("text/html", $email_body);
```

### 5. File Permissions ✓

```bash
# Set proper permissions
chmod 755 .          # Main directory
chmod 644 *.html     # HTML files
chmod 644 *.php      # PHP files
chmod 755 assets     # Assets directory
chmod 755 admin      # Admin directory
chmod 755 ajax       # AJAX directory
chmod 755 includes   # Includes directory
chmod 755 logs       # Logs directory
chmod 777 logs       # Logs writable
```

### 6. Backup Strategy ✓

**Automated Daily Backups:**

```bash
# Create backup script: backup.sh
#!/bin/bash
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u root -p$DB_PASSWORD pixelara_db > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html/pixelara

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

**Schedule with cron:**
```bash
# Run daily at 2 AM
0 2 * * * /home/user/backup.sh
```

### 7. Testing ✓

**Homepage**
- [ ] All sections load properly
- [ ] Animations work smoothly
- [ ] Responsive on mobile/tablet/desktop
- [ ] Images load correctly
- [ ] Links work (internal and external)

**Contact Form**
- [ ] Form submits without errors
- [ ] Success message displays
- [ ] Admin email received
- [ ] User confirmation email received
- [ ] Data saved in database

**Admin Panel**
- [ ] Login works with new credentials
- [ ] Dashboard displays statistics
- [ ] Can add projects
- [ ] Can edit projects
- [ ] Can delete projects
- [ ] Can view inquiries
- [ ] Logout works

**Database**
- [ ] Can connect and query
- [ ] All tables exist
- [ ] Sample data displays properly
- [ ] Can insert new data

### 8. Performance Optimization ✓

**Enable Compression**
```apache
# In .htaccess - already configured
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml
    ...
</IfModule>
```

**Browser Caching**
```apache
# In .htaccess - already configured
<IfModule mod_expires.c>
    ExpiresActive On
    ...
</IfModule>
```

**Minify Assets (Optional)**

```bash
# Using online tools or tools like:
# - cssnano for CSS
# - terser for JavaScript
# - imagemin for images
```

### 9. Monitoring & Logging ✓

**Enable Error Logging**
```php
// config.php
define('LOG_ERRORS', true);
define('LOG_FILE', __DIR__ . '/../logs/error.log');
```

**Monitor Log Files**
```bash
# Watch error log in real-time
tail -f logs/error.log

# Check for errors
grep ERROR logs/error.log
```

**Set Up Alerts**

Configure email alerts when errors occur:
```php
// In logs
if (LOG_ERRORS && count($errors) > 0) {
    mail(ADMIN_EMAIL, 'Pixelara Errors', implode("\n", $errors));
}
```

### 10. SSL Certificate ✓

**Get Free SSL with Let's Encrypt**
```bash
# Using certbot
sudo certbot certonly --webroot -w /var/www/pixelara -d yourdomain.com

# Auto-renew
sudo certbot renew --dry-run
```

**Configure in .htaccess**
```apache
# Redirect HTTP to HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## 🚀 Deployment Steps

### Step 1: Prepare Server
```bash
# SSH into server
ssh user@your-server.com

# Create directory
mkdir -p /var/www/html/pixelara
cd /var/www/html/pixelara
```

### Step 2: Upload Files
```bash
# Option 1: Using FTP/SFTP
sftp user@your-server.com
put -r pixelara /var/www/html/

# Option 2: Using Git
git clone your-repo-url .
```

### Step 3: Install Dependencies (if using composer)
```bash
cd /var/www/html/pixelara
composer install
```

### Step 4: Set Permissions
```bash
chmod 755 .
chmod 644 *.php
chmod 755 admin includes ajax assets logs
chmod 777 logs
```

### Step 5: Update Configuration
```bash
# Copy config template
cp config.php.example config.php

# Edit with your settings
nano config.php
```

### Step 6: Import Database
```bash
mysql -u root -p < database.sql
```

### Step 7: Test Website
```
Visit: https://yourdomain.com
Admin: https://yourdomain.com/admin/login.php
```

## 🔐 Security Checklist

- [ ] Changed default admin password
- [ ] Updated email configuration
- [ ] Enabled HTTPS/SSL
- [ ] Set proper file permissions
- [ ] Created backups
- [ ] Enabled error logging
- [ ] Updated business information
- [ ] Tested all forms
- [ ] Tested admin panel
- [ ] Configured firewall rules
- [ ] Set up monitoring
- [ ] Enabled rate limiting (if needed)

## 📊 Monitoring

**Check Status**
```bash
# Check MySQL
mysql -u root -p -e "SELECT VERSION();"

# Check disk space
df -h

# Check PHP errors
tail -f logs/error.log

# Check system logs
tail -f /var/log/syslog
```

**Set Up Cron Jobs**

```bash
# Daily backups at 2 AM
0 2 * * * /home/user/backup.sh

# Weekly database cleanup
0 3 * * 0 mysql -u root -p$PASSWORD pixelara_db -e "DELETE FROM contacts WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);"
```

## 🆘 Troubleshooting

**Contact form not sending emails**
```php
// Check mail configuration
mail('test@example.com', 'Test', 'Test message');
// Check logs for errors
tail -f logs/error.log
```

**Admin login not working**
- [ ] Database connected
- [ ] admin_users table has data
- [ ] Password hash is correct
- [ ] Check logs for errors

**Images not loading**
- [ ] Check file paths
- [ ] Verify file permissions
- [ ] Check web server error logs

**Slow performance**
- [ ] Check database queries
- [ ] Enable caching
- [ ] Minify CSS/JS
- [ ] Optimize images
- [ ] Check server resources

## 📞 Support

For issues or questions:
- Email: hello@pixelara.com
- WhatsApp: +91 805 555 5555
- Documentation: See README.md

---

**Deployment Status:** Ready for Production  
**Last Check:** May 12, 2026
