# Pixelara Web Solutions - Premium Portfolio Website

A luxurious, modern, highly animated portfolio and business showcase website built with HTML5, CSS3, JavaScript, Bootstrap 5, GSAP, and PHP with MySQL.

## 🎯 Project Overview

Pixelara Web Solutions is a **premium digital agency showcase website** designed to:
- Impress potential clients visiting from Instagram bio links
- Convert visitors into paying clients
- Showcase completed projects and ongoing work
- Display services with premium animations
- Collect client inquiries and manage them efficiently
- Provide an admin panel for managing content

## 📋 Features

### Frontend Features
✅ Stunning hero section with animated gradient background  
✅ Particle system with canvas animation  
✅ Smooth scroll animations with GSAP and ScrollTrigger  
✅ Service cards with hover effects  
✅ Dynamic project gallery with filtering  
✅ Testimonial slider with Swiper.js  
✅ Process timeline with step-by-step workflow  
✅ Contact form with AJAX submission  
✅ Responsive design (mobile, tablet, desktop)  
✅ Dark premium theme with neon gradients  
✅ Glassmorphism UI elements  
✅ Smooth transitions and micro-interactions  

### Backend Features
✅ Secure admin panel with login authentication  
✅ Project management system  
✅ Contact inquiry management  
✅ Email notifications for inquiries  
✅ Database-driven content  
✅ PDO-based MySQL queries (secure)  
✅ Form validation and sanitization  

## 🛠️ Tech Stack

**Frontend:**
- HTML5
- CSS3 (with custom animations)
- JavaScript (ES6+)
- Bootstrap 5
- jQuery
- GSAP (animation library)
- AOS (Animate on Scroll)
- Swiper.js (carousel/slider)
- Particles.js (background effects)

**Backend:**
- PHP 7.4+
- MySQL 5.7+
- PDO (PHP Data Objects)

**Optional Libraries:**
- Lenis (smooth scroll)
- Three.js (3D effects)

## 📁 Folder Structure

```
Pixelara/
├── index.php                    # Main homepage
├── database.sql                 # Database schema
├── assets/
│   ├── css/
│   │   ├── style.css           # Main styles
│   │   ├── animations.css      # Animation library
│   │   └── responsive.css      # Responsive design
│   ├── js/
│   │   ├── main.js            # Core functionality
│   │   ├── animations.js      # GSAP animations
│   │   └── particles.js       # Particle effects
│   ├── images/                 # Image assets
│   └── videos/                 # Video assets
├── includes/
│   ├── db.php                  # Database configuration
│   ├── header.php              # Navigation header
│   └── footer.php              # Footer section
├── ajax/
│   ├── contact_submit.php      # Form submission handler
│   └── get_statistics.php      # Stats API
├── admin/
│   ├── index.php               # Admin redirect
│   ├── login.php               # Admin login page
│   ├── logout.php              # Admin logout
│   ├── dashboard.php           # Admin dashboard
│   ├── projects.php            # Manage projects
│   ├── inquiries.php           # View inquiries
│   ├── services.php            # Manage services
│   └── testimonials.php        # Manage testimonials
└── README.md                    # This file
```

## 🚀 Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Local server (Laragon, XAMPP, WAMP, etc.)
- Modern web browser

### Step 1: Database Setup

1. Open `database.sql` in your MySQL client
2. Execute all queries to create tables and sample data
3. Or import via phpMyAdmin

**Default Database Credentials:**
```
Host: localhost
User: root
Password: (empty)
Database: pixelara_db
```

### Step 2: Update Configuration

Edit `config.php` with your database and email settings:
```php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pixelara_db');

// Email settings
define('ADMIN_EMAIL', 'your-email@example.com');
define('SENDER_EMAIL', 'noreply@yourdomain.com');

// Business information
define('BUSINESS_NAME', 'Your Company Name');
define('BUSINESS_PHONE', 'Your Phone');
define('BUSINESS_WHATSAPP', 'Your WhatsApp Number');
```

**Or for development:** The default configuration will work with local setup (localhost, empty password)

### Step 3: Admin Credentials

Default admin login (MUST change for production):
- **Username:** admin
- **Password:** admin123

To change admin password securely:
1. Generate bcrypt hash:
   ```php
   <?php echo password_hash('your_new_password', PASSWORD_BCRYPT); ?>
   ```
2. Update in database:
   ```sql
   UPDATE admin_users 
   SET password = '$2y$10$YOUR_HASH_HERE' 
   WHERE username = 'admin';
   ```

### Step 4: Test the Installation

1. Open browser and go to `http://localhost/Pixelara`
2. Homepage should load with all sections
3. Test contact form submission
4. Access admin at `http://localhost/Pixelara/admin/login.php`
5. Login with admin / admin123
6. Check dashboard and project management

## 🔐 Security Notes

⚠️ **Important for Production:**

1. **Update Default Admin Credentials:**
   - MUST change default admin password before deployment
   - Use bcrypt hashing: `password_hash('newpassword', PASSWORD_BCRYPT)`
   - Store hash in database, never commit to version control

2. **Update Configuration:**
   - Edit `config.php` with production database credentials
   - NEVER commit actual credentials to git
   - Use `.env` file for environment-specific values
   - Template provided in `.env.example`

3. **Email Configuration:**
   - Configure proper mail server (SMTP, SendGrid, AWS SES)
   - Update ADMIN_EMAIL, SENDER_EMAIL, SUPPORT_EMAIL in config.php
   - Test email delivery before going live

4. **Enable HTTPS/SSL:**
   - Required for production
   - Get free certificate with Let's Encrypt
   - Uncomment HTTPS redirect in `.htaccess`

5. **Session Security:**
   - Session timeout: 30 minutes (configurable in config.php)
   - Automatic logout after inactivity
   - Session activity tracking enabled

6. **Error Logging:**
   - Errors logged to `logs/error.log` (not displayed to users)
   - Check logs regularly for issues
   - Ensure logs directory is writable (777)

7. **Database Security:**
   - All queries use prepared statements (PDO)
   - SQL injection protection built-in
   - Proper NULL checks before operations

8. **Input Validation:**
   - All form inputs validated on server
   - XSS prevention with htmlspecialchars()
   - CSRF basic measures in place

**See [PRODUCTION_CHECKLIST.md](PRODUCTION_CHECKLIST.md) for complete security audit checklist.**

## 📝 Usage Guide

### For Visitors

1. **View Portfolio:** Browse the projects section
2. **Filter Projects:** Click filter buttons to view specific categories
3. **Submit Inquiry:** Fill the contact form to request a project
4. **Social Links:** Connect via Instagram, WhatsApp, etc.

### For Administrators

**Access Admin Panel:**
```
http://localhost/Pixelara/admin/login.php
```

**Dashboard:**
- View statistics
- See recent inquiries
- Manage projects overview

**Projects:**
- Add new projects
- Edit project details
- Delete projects
- Mark as featured

**Inquiries:**
- View all contact inquiries
- Mark as read/contacted
- Export inquiry data

## 🎨 Customization

### Colors
Edit CSS variables in `assets/css/style.css`:
```css
:root {
    --primary-color: #00d4ff;
    --secondary-color: #7c3aed;
    --accent-color: #ff006e;
    --dark-bg: #0f0f23;
}
```

### Content
Update static content in `index.php` HTML sections

### Services
Manage in admin panel or directly in database

### Contact Info
Update footer and contact section in `includes/footer.php`

## 📱 Responsive Breakpoints

- **Mobile:** 320px - 575px
- **Tablet:** 576px - 1024px
- **Desktop:** 1025px+

## 🔧 Configuration

### Mail Server
For sending emails, configure your mail server. Options:
- SMTP via PHP mailer
- SendGrid API
- AWS SES
- Local sendmail

### Analytics
Add Google Analytics to track visitors:
```html
<!-- Add before closing </head> tag in index.php -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_ID"></script>
```

### SEO
Update meta tags in `index.php`:
- Title
- Description
- Keywords
- Open Graph tags
- Schema markup

## 🐛 Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify database credentials
- Ensure database `pixelara_db` exists

### Form Not Submitting
- Check browser console for errors
- Verify `ajax/contact_submit.php` exists
- Check mail server configuration

### Admin Login Not Working
- Clear browser cookies/cache
- Verify MySQL is running
- Check admin user exists in database

### Animations Not Working
- Check all CDN libraries loaded
- Verify JavaScript files exist
- Check browser console for errors

## 📊 Performance Optimization

Implemented features:
- ✅ Lazy image loading
- ✅ CSS minification ready
- ✅ JavaScript debouncing
- ✅ Smooth scrolling
- ✅ Hardware acceleration
- ✅ Optimized animations

## 🌐 Deployment & Production Deployment

### Hosting Requirements
- PHP 7.4+ support
- MySQL 5.7+ support
- Write permissions for logs directory
- SSL certificate (required for production)
- Mail server configured

### Quick Start (5 minutes)
See **[QUICK_START.md](QUICK_START.md)** for:
- Database import steps
- Configuration file setup
- Admin credential changes
- Common tasks

### Complete Deployment Guide
See **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** for:
- Detailed setup instructions
- Database configuration
- Security hardening
- Email configuration (3 options)
- File permissions setup
- Backup strategy
- Performance optimization
- Monitoring setup
- Troubleshooting guide

### Pre-Deployment Checklist
See **[PRODUCTION_CHECKLIST.md](PRODUCTION_CHECKLIST.md)** for:
- All critical fixes completed ✅
- Pre-deployment testing checklist
- Database setup verification
- Security audit checklist
- Performance review
- Email configuration validation
- HTTPS/SSL setup
- Post-deployment testing

### Production Readiness Report
See **[PRODUCTION_READINESS_REPORT.md](PRODUCTION_READINESS_REPORT.md)** for:
- All issues found and fixed
- Code quality validation
- Security assessment
- Documentation created
- Deployment readiness confirmation

## 📄 License

This project is proprietary to Pixelara Web Solutions.

## 👥 Support

For issues or questions, contact:
- Email: hello@pixelara.com
- WhatsApp: +91 805 555 5555
- Instagram: @pixelara

## 🎉 Credits

Built with passion by Pixelara Web Solutions

---

**Last Updated:** May 2026
**Version:** 1.0.0
