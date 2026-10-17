-- =============================================
-- Oxionex Web Solutions - Database Schema
-- =============================================

CREATE DATABASE IF NOT EXISTS oxionex_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE oxionex_db;

-- Projects table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    short_description VARCHAR(500),
    image VARCHAR(500),
    category ENUM('ecommerce','business','portfolio','custom','dashboard') NOT NULL DEFAULT 'business',
    technologies VARCHAR(500),
    status ENUM('ongoing','completed') NOT NULL DEFAULT 'completed',
    live_link VARCHAR(500),
    featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contacts / Inquiries table
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    business_type VARCHAR(255),
    budget VARCHAR(100),
    message TEXT NOT NULL,
    status ENUM('new','read','replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Testimonials table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    client_role VARCHAR(255),
    client_company VARCHAR(255),
    client_image VARCHAR(500),
    rating TINYINT DEFAULT 5,
    message TEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Statistics table
CREATE TABLE IF NOT EXISTS statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    value INT NOT NULL,
    suffix VARCHAR(20),
    icon VARCHAR(100),
    sort_order INT DEFAULT 0
);

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- SAMPLE DATA
-- =============================================

-- Statistics
INSERT INTO statistics (label, value, suffix, icon, sort_order) VALUES
('Projects Completed', 50, '+', 'fa-rocket', 1),
('Happy Clients', 40, '+', 'fa-smile', 2),
('Years Experience', 3, '+', 'fa-calendar', 3),
('Technologies Mastered', 15, '+', 'fa-code', 4);

-- Sample Projects
INSERT INTO projects (title, description, short_description, image, category, technologies, status, live_link, featured, sort_order) VALUES
('LuxeShop eCommerce Platform', 'A full-featured eCommerce platform with advanced product filtering, cart management, payment gateway integration, and a powerful admin dashboard for inventory management.', 'Full-featured eCommerce with payment integration', 'assets/images/project-ecommerce.jpg', 'ecommerce', 'PHP, MySQL, Bootstrap, jQuery, Stripe API', 'completed', '#', 1, 1),
('TechCorp Business Website', 'A modern corporate website with dynamic content management, SEO optimization, multi-language support, and integrated lead generation forms.', 'Modern corporate website with CMS', 'assets/images/project-business.jpg', 'business', 'HTML5, CSS3, PHP, MySQL, GSAP', 'completed', '#', 1, 2),
('CreativeFlow Portfolio', 'An animated portfolio website for a creative agency featuring cinematic transitions, 3D hover effects, and dynamic project galleries.', 'Animated agency portfolio with 3D effects', 'assets/images/project-portfolio.jpg', 'portfolio', 'HTML5, CSS3, JavaScript, GSAP, Three.js', 'completed', '#', 0, 3),
('InvoPro Admin Dashboard', 'A comprehensive SaaS admin dashboard with real-time analytics, multi-user roles, invoice generation, and advanced reporting features.', 'SaaS dashboard with real-time analytics', 'assets/images/project-dashboard.jpg', 'dashboard', 'PHP, MySQL, Chart.js, Bootstrap, AJAX', 'completed', '#', 1, 4),
('MediCare Booking System', 'A custom healthcare appointment booking system with doctor profiles, time-slot management, email notifications, and patient dashboard.', 'Healthcare appointment booking platform', 'assets/images/project-custom.jpg', 'custom', 'PHP, MySQL, jQuery, AJAX, Bootstrap', 'completed', '#', 0, 5),
('StyleHive Fashion Store', 'An ongoing eCommerce project for a fashion brand featuring AR try-on integration, wishlist, loyalty points, and a mobile-first design.', 'Fashion eCommerce with AR integration', 'assets/images/project-ecommerce2.jpg', 'ecommerce', 'PHP, MySQL, Bootstrap, AR.js, Stripe', 'ongoing', '#', 0, 6),
('AcademiaHub Learning Platform', 'An ongoing EdTech platform with video courses, live sessions, progress tracking, certificates, and instructor dashboards.', 'EdTech platform with video courses', 'assets/images/project-custom2.jpg', 'custom', 'PHP, MySQL, Video.js, Bootstrap, AJAX', 'ongoing', '#', 0, 7),
('RealEstate Pro Portal', 'A property listing and management portal with map integration, virtual tours, mortgage calculator, and agent CRM.', 'Real estate portal with virtual tours', 'assets/images/project-business2.jpg', 'business', 'PHP, MySQL, Google Maps API, Bootstrap', 'completed', '#', 0, 8);

-- Sample Testimonials
INSERT INTO testimonials (client_name, client_role, client_company, client_image, rating, message) VALUES
('Aryan Kapoor', 'CEO', 'TechVentures India', 'assets/images/client1.jpg', 5, 'Oxionex delivered an absolutely stunning website that exceeded our expectations. The animations are smooth, the design is premium, and our client inquiries increased by 300% after launch!'),
('Sneha Malhotra', 'Founder', 'LuxeBoutique', 'assets/images/client2.jpg', 5, 'The eCommerce store they built for us is pure perfection. Fast, beautiful, and our customers love the experience. Sales went up significantly within the first month.'),
('Rahul Sharma', 'Director', 'InnovateTech Solutions', 'assets/images/client3.jpg', 5, 'Professional, communicative, and incredibly talented. Oxionex turned our idea into a world-class web application. Highly recommended for any serious business.'),
('Priya Desai', 'Marketing Head', 'GreenPath Organics', 'assets/images/client4.jpg', 5, 'We needed a complete digital transformation and Oxionex delivered beyond what we imagined. Our website now looks better than international competitors!'),
('Mohammed Al-Rashid', 'Owner', 'AlRashid Enterprises', 'assets/images/client5.jpg', 5, 'From consultation to launch, the entire experience was premium. The team is professional, deadline-oriented, and the final product is absolutely world-class.');

-- Create the first administrator through the one-time admin/setup.php script.
