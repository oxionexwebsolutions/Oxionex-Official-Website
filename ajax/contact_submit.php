<?php
/**
 * Oxionex Web Solutions - Contact Form AJAX Handler
 * Handles contact form submissions and sends email notifications
 */

header('Content-Type: application/json');

// Start session
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Require database
require_once '../includes/db.php';

// Response object
$response = [
    'success' => false,
    'message' => 'An error occurred.'
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$business_type = isset($_POST['business_type']) ? trim($_POST['business_type']) : '';
$budget = isset($_POST['budget']) ? trim($_POST['budget']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate inputs
if (empty($name) || strlen($name) < 3) {
    $response['message'] = 'Please enter a valid name.';
    echo json_encode($response);
    exit;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Please enter a valid email address.';
    echo json_encode($response);
    exit;
}

if (empty($message) || strlen($message) < 10) {
    $response['message'] = 'Message must be at least 10 characters long.';
    echo json_encode($response);
    exit;
}

// Validate phone if provided
if (!empty($phone) && !preg_match('/^[\d\s+\-()]*$/', $phone)) {
    $response['message'] = 'Please enter a valid phone number.';
    echo json_encode($response);
    exit;
}

// Sanitize inputs
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
$business_type = htmlspecialchars($business_type, ENT_QUOTES, 'UTF-8');
$budget = htmlspecialchars($budget, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Try to insert into database
try {
    $query = "INSERT INTO contacts (name, email, phone, business_type, budget, message, status, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, 'new', NOW())";
    
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$name, $email, $phone, $business_type, $budget, $message]);
    
    if ($result) {
        // Send email notification to admin
        $admin_email = ADMIN_EMAIL;
        $sender_email = SENDER_EMAIL;
        $business_name = BUSINESS_NAME;
        $subject = 'New Project Inquiry from ' . $name . ' - ' . $business_name;
        
        $email_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); color: white; padding: 20px; border-radius: 5px; }
                .section { margin: 20px 0; padding: 15px; background: #f5f5f5; border-left: 4px solid #00d4ff; }
                .label { font-weight: bold; color: #00d4ff; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #999; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>New Project Inquiry</h1>
                </div>
                
                <div class='section'>
                    <p><span class='label'>Name:</span> {$name}</p>
                    <p><span class='label'>Email:</span> {$email}</p>
                    <p><span class='label'>Phone:</span> {$phone}</p>
                </div>
                
                <div class='section'>
                    <p><span class='label'>Business Type:</span> {$business_type}</p>
                    <p><span class='label'>Budget Range:</span> {$budget}</p>
                </div>
                
                <div class='section'>
                    <p><span class='label'>Message:</span></p>
                    <p>{$message}</p>
                </div>
                
                <div class='footer'>
                    <p>This inquiry was submitted on " . date('Y-m-d H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Email headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: " . $sender_email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: Oxionex Web Solutions" . "\r\n";
        
        // Send email (use environment variable or config in production)
        @mail($admin_email, $subject, $email_body, $headers);
        
        // Send confirmation email to user
        $user_subject = 'We Received Your Inquiry - ' . BUSINESS_NAME;
        $user_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); color: white; padding: 20px; border-radius: 5px; }
                .message { padding: 20px; background: #f5f5f5; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Thank You, {$name}!</h1>
                </div>
                
                <div class='message'>
                    <p>We've received your project inquiry and it's very important to us. Our team will review your request and get back to you within 24 hours with custom solutions tailored to your needs.</p>
                    
                    <p><strong>Quick Response?</strong> You can also reach us on WhatsApp: <a href='https://wa.me/" . BUSINESS_WHATSAPP . "'>Click here to chat</a></p>
                    
                    <p>Looking forward to building something amazing with you!</p>
                    
                    <p><strong>Best regards,</strong><br/>
                    The " . BUSINESS_NAME . " Team</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $user_headers = "MIME-Version: 1.0" . "\r\n";
        $user_headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $user_headers .= "From: " . SENDER_EMAIL . "\r\n";
        $user_headers .= "Reply-To: " . SUPPORT_EMAIL . "\r\n";
        
        @mail($email, $user_subject, $user_body, $user_headers);
        
        $response['success'] = true;
        $response['message'] = 'Thank you! Your inquiry has been received. We will contact you soon.';
    } else {
        $response['message'] = 'Failed to save your inquiry. Please try again.';
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $response['message'] = 'An error occurred while processing your request.';
}

echo json_encode($response);
exit;
