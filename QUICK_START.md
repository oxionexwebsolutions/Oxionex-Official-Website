# Pixelara Web Solutions - Quick Start Guide

**Version:** 1.0.0  
**Last Updated:** May 12, 2026

## 🚀 5-Minute Setup

### Step 1: Import Database (1 minute)
```bash
# Using command line
mysql -u root -p < database.sql

# Or use phpMyAdmin:
# 1. Create database: pixelara_db
# 2. Import database.sql
```

### Step 2: Update Configuration (2 minutes)
Edit `config.php`:
```php
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

// Email
define('ADMIN_EMAIL', 'your-email@example.com');

// Business Info
define('BUSINESS_NAME', 'Your Company');
define('BUSINESS_PHONE', 'Your Phone');
```

### Step 3: Change Admin Password (1 minute)
Generate hash:
```php
<?php echo password_hash('your_new_password', PASSWORD_BCRYPT); ?>
```

Update database:
```sql
UPDATE admin_users 
SET password = '$2y$10$YOUR_HASH_HERE' 
WHERE username = 'admin';
```

### Step 4: Access Website (1 minute)
```
Homepage: http://localhost/Pixelara
Admin: http://localhost/Pixelara/admin/login.php
Username: admin
Password: (your new password)
```

## 📁 Important Files

| File | Purpose | Edit? |
|------|---------|-------|
| config.php | All configuration | ✅ YES |
| database.sql | Database schema | ❌ No |
| includes/db.php | Database connection | ❌ No |
| admin/login.php | Admin login | ❌ No |
| ajax/contact_submit.php | Contact form handler | ❌ No |
| .htaccess | Server configuration | ⚠️ Advanced |
| logs/ | Error logs | 🔍 Monitor |

## 🔧 Common Tasks

### Add a New Project
1. Go to: Admin > Projects
2. Click "Add Project"
3. Fill in details
4. Click "Add Project"

### View Contact Inquiries
1. Go to: Admin > Inquiries
2. See all contact form submissions
3. Click email to respond

### Change Business Contact Info
Edit `config.php`:
```php
define('BUSINESS_PHONE', 'Your Phone');
define('BUSINESS_WHATSAPP', '+91...');
define('BUSINESS_EMAIL', 'hello@yoursite.com');
```

### Update Admin Password
```sql
UPDATE admin_users 
SET password = PASSWORD_HASH('new_password', PASSWORD_BCRYPT)
WHERE username = 'admin';
```

### Check Error Logs
```bash
tail logs/error.log  # Last 10 errors
tail -f logs/error.log  # Monitor in real-time
```

## 🔐 Security Tips

1. **Never** share admin credentials
2. **Always** change default password
3. **Keep** error logs clean
4. **Monitor** contact submissions
5. **Backup** database regularly
6. **Update** software regularly

## 📞 Contact Form

Users can submit inquiries via the contact form:
- Form validates on client and server side
- Data saved to `contacts` table
- Admin receives email notification
- User receives confirmation email

### Email Configuration
If emails aren't sending:
1. Check `config.php` email settings
2. Verify mail server is running
3. Check `logs/error.log` for errors
4. Use SMTP for reliability (PHPMailer/SendGrid)

## 🎨 Customize Website

### Change Logo/Branding
Edit in:
- `index.php` - HTML logo
- `includes/header.php` - Navigation
- `includes/footer.php` - Footer

### Change Colors
Edit `assets/css/style.css`:
```css
:root {
    --primary-color: #00d4ff;
    --secondary-color: #7c3aed;
    --accent-color: #ff006e;
    /* ... more colors ... */
}
```

### Update Social Links
Edit `config.php`:
```php
define('SOCIAL_INSTAGRAM', 'https://instagram.com/yourprofile');
define('SOCIAL_TWITTER', 'https://twitter.com/yourprofile');
```

## 🆘 Troubleshooting

### Admin Login Not Working
```bash
# Check database
mysql> SELECT * FROM admin_users;

# Verify password hash
# Should start with $2y$10$
```

### Contact Form Not Sending Emails
```bash
# Check error logs
tail logs/error.log

# Test mail function
php -r "mail('test@example.com', 'Test', 'Message');"
```

### Website Not Loading
1. Check database connection
   ```bash
   mysql -u root -p -e "USE pixelara_db; SHOW TABLES;"
   ```
2. Check file permissions
   ```bash
   ls -la admin/  # Should show readable/writable
   ```
3. Check error logs
   ```bash
   tail logs/error.log
   ```

### Slow Performance
1. Check database queries
2. Enable caching in .htaccess
3. Optimize images
4. Minify CSS/JS

## 📊 Database Tables

### contacts
Fields: id, name, email, phone, business_type, budget, message, status, created_at
Used by: Contact form

### projects
Fields: id, title, description, short_description, image, category, technologies, status, live_link, featured, sort_order, created_at
Used by: Portfolio section, Admin panel

### testimonials
Fields: id, client_name, client_role, client_company, client_image, rating, message, is_active, created_at
Used by: Testimonials section

### statistics
Fields: id, label, value, suffix, icon, sort_order
Used by: Hero section statistics

### admin_users
Fields: id, username, password, email, created_at
Used by: Admin login

## 🔄 Backup & Restore

### Backup Database
```bash
mysqldump -u root -p pixelara_db > backup.sql
```

### Restore Database
```bash
mysql -u root -p pixelara_db < backup.sql
```

### Backup Files
```bash
tar -czf pixelara_backup.tar.gz /path/to/pixelara
```

## 📈 Performance Tips

1. Enable compression in .htaccess (enabled by default)
2. Leverage browser caching (enabled by default)
3. Optimize images before uploading
4. Minify CSS/JS for production
5. Use CDN for static assets
6. Monitor error logs regularly

## 🚀 Going Live

### Before Launch
- [ ] Change all default passwords
- [ ] Update all email addresses
- [ ] Update business information
- [ ] Enable HTTPS/SSL
- [ ] Test all forms
- [ ] Test admin panel
- [ ] Clean up error logs
- [ ] Create database backup
- [ ] Configure email server

### After Launch
- [ ] Monitor website daily
- [ ] Review error logs weekly
- [ ] Back up database weekly
- [ ] Monitor uptime
- [ ] Track form submissions

## 📚 Documentation Files

- **README.md** - Complete project documentation
- **DEPLOYMENT_GUIDE.md** - Production deployment steps
- **PRODUCTION_CHECKLIST.md** - Pre-launch checklist
- **TESTING_OPTIMIZATION_REPORT.md** - Technical validation
- **config.php** - Configuration management

## 🎓 Learning Resources

- HTML/CSS: [MDN Web Docs](https://developer.mozilla.org)
- JavaScript: [JavaScript.info](https://javascript.info)
- PHP: [PHP.net](https://www.php.net)
- MySQL: [MySQL.com](https://www.mysql.com)

## ⚡ Tips & Tricks

### Enable Debug Mode (Development Only)
```php
// config.php
define('DEBUG_MODE', true);  // Shows errors
```

### Disable Debug Mode (Production)
```php
// config.php
define('DEBUG_MODE', false);  // Hide errors
```

### View Raw SQL Queries (Development)
```php
// Add to queries for debugging
echo $query;  // Print SQL before executing
```

### Test Email Locally
```bash
# Use Mailtrap or similar service
# Update SMTP settings in config.php
```

---

**Happy coding! 🎉**

For detailed information, see the complete documentation files in the project.
