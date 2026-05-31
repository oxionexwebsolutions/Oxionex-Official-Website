<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Your Project - Pixelara Web Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/cursor-follower.css">
    <link rel="stylesheet" href="assets/css/start-project.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="start-project-page">
        <div class="container">
            <!-- Hero Section -->
            <div class="sp-hero">
                <h1>Let's Build Something Extraordinary Together</h1>
                <p>Tell us about your project and our team will get back to you with the perfect solution.</p>
            </div>

            <!-- Progress Bar -->
            <div class="sp-progress-container">
                <div class="sp-progress-bar">
                    <div class="sp-progress-fill"></div>
                </div>
                <div class="sp-progress-label">
                    <span id="sp-progress-text">0%</span> Complete
                </div>
            </div>

            <!-- Form Container -->
            <div class="sp-form-container">
                <div class="sp-form-wrapper">
                    <form id="sp-form" enctype="multipart/form-data">
                        <!-- STEP 1: BASIC CLIENT INFORMATION -->
                        <div id="sp-step-1" class="sp-form-step active">
                            <div class="sp-step-header">
                                <div class="sp-step-number">1</div>
                                <div class="sp-step-info">
                                    <h2>Tell Us About You</h2>
                                    <p>Basic information about your company and project</p>
                                </div>
                            </div>

                            <div class="sp-form-row">
                                <div class="sp-form-group">
                                    <label for="sp-client-name">Full Name <span class="sp-required">*</span></label>
                                    <input type="text" id="sp-client-name" name="client_name" placeholder="Your Name" required>
                                </div>
                                <div class="sp-form-group">
                                    <label for="sp-company-name">Business/Company Name <span class="sp-required">*</span></label>
                                    <input type="text" id="sp-company-name" name="company_name" placeholder="Your Company" required>
                                </div>
                                <div class="sp-form-group">
                                    <label for="sp-client-email">Email Address <span class="sp-required">*</span></label>
                                    <input type="email" id="sp-client-email" name="client_email" placeholder="your@email.com" required>
                                </div>
                                <div class="sp-form-group">
                                    <label for="sp-client-phone">Phone Number <span class="sp-required">*</span></label>
                                    <input type="tel" id="sp-client-phone" name="client_phone" placeholder="+91 XXXXX XXXXX" required>
                                </div>
                                <div class="sp-form-group">
                                    <label for="sp-company-site">Company Website</label>
                                    <input type="url" id="sp-company-site" name="company_site" placeholder="https://yoursite.com">
                                </div>
                                <div class="sp-form-group">
                                    <label for="sp-company-location">Country/City</label>
                                    <input type="text" id="sp-company-location" name="company_location" placeholder="e.g., Rajkot, India">
                                </div>
                            </div>

                            <div class="sp-form-row">
                                <div class="sp-form-group" style="grid-column: span 2;">
                                    <label for="sp-main-service">Select Main Service Category <span class="sp-required">*</span></label>
                                    <select id="sp-main-service" name="main_service" required>
                                        <option value="">-- Choose a Service --</option>
                                        <option value="website-development">🌐 Website Development</option>
                                        <option value="website-design">🎨 Website Design & Optimization</option>
                                        <option value="social-media">📱 Social Media Management</option>
                                        <option value="video-production">🎬 Video Production & Editing</option>
                                        <option value="digital-marketing">📊 Digital Marketing & Advertising</option>
                                        <option value="branding-design">✨ Branding & Graphic Design</option>
                                        <option value="hosting-technical">⚙️ Hosting & Technical Services</option>
                                    </select>
                                </div>
                            </div>

                            <div class="sp-button-group">
                                <div></div>
                                <button type="button" id="sp-btn-next-step1" class="sp-btn sp-btn-primary">Next Step →</button>
                            </div>
                        </div>

                        <!-- STEP 2: PROJECT DETAILS (Dynamic) -->
                        <div id="sp-step-2" class="sp-form-step">
                            <div class="sp-step-header">
                                <div class="sp-step-number">2</div>
                                <div class="sp-step-info">
                                    <h2>Project Details</h2>
                                    <p>Tell us more about your project requirements</p>
                                </div>
                            </div>

                            <div id="sp-service-fields">
                                <!-- Dynamic fields will be rendered here -->
                            </div>

                            <!-- File Upload Section -->
                            <div class="sp-form-row">
                                <div class="sp-form-group" style="grid-column: span 2;">
                                    <label>Upload Reference Files / Assets</label>
                                    <div id="sp-file-upload" class="sp-file-upload">
                                        <div class="sp-file-icon">📁</div>
                                        <p><strong>Drag & drop files here</strong> or click to browse</p>
                                        <small>Images, PDFs, videos, documents (Max 100MB total)</small>
                                    </div>
                                    <input type="file" id="sp-file-input" multiple>
                                    <div id="sp-file-list" class="sp-file-list"></div>
                                </div>
                            </div>

                            <div class="sp-button-group">
                                <button type="button" id="sp-btn-prev-step2" class="sp-btn sp-btn-secondary">← Back</button>
                                <button type="submit" class="sp-btn sp-btn-primary">Submit Project Idea</button>
                            </div>
                        </div>
                    </form>

                    <div class="sp-save-draft">
                        <p>Need a break? <button id="sp-save-draft-btn" type="button">Save & Continue Later</button></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Success Modal -->
    <div id="sp-success-modal" class="sp-success-modal">
        <div class="sp-modal-content">
            <div class="sp-modal-icon">✅</div>
            <h2>Project Submitted Successfully!</h2>
            <p>Our executive team will review your project and reach out within 24 hours with a customized proposal.</p>
            <button type="button" id="sp-modal-close-btn" class="sp-modal-button">Continue</button>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/start-project-form.js"></script>
</body>
</html>