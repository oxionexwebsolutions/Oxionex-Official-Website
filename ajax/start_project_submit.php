<?php
header('Content-Type: application/json');
session_start();
require_once '../includes/db.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

// Sanitize and validate Step 1 inputs
$client_name = isset($_POST['client_name']) ? trim($_POST['client_name']) : '';
$company_name = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
$client_email = isset($_POST['client_email']) ? trim($_POST['client_email']) : '';
$client_phone = isset($_POST['client_phone']) ? trim($_POST['client_phone']) : '';
$company_site = isset($_POST['company_site']) ? trim($_POST['company_site']) : '';
$company_location = isset($_POST['company_location']) ? trim($_POST['company_location']) : '';
$main_service = isset($_POST['main_service']) ? trim($_POST['main_service']) : '';
$specific_service = isset($_POST['specific_service']) ? trim($_POST['specific_service']) : '';

// Validate required fields from Step 1
if (empty($client_name) || strlen($client_name) < 2) {
    $response['message'] = 'Please provide a valid name.';
    echo json_encode($response);
    exit;
}
if (empty($client_email) || !filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Please provide a valid email.';
    echo json_encode($response);
    exit;
}
if (empty($client_phone) || strlen($client_phone) < 7) {
    $response['message'] = 'Please provide a valid phone number.';
    echo json_encode($response);
    exit;
}
if (empty($main_service)) {
    $response['message'] = 'Please select a main service.';
    echo json_encode($response);
    exit;
}

// Sanitize Step 1
$client_name = htmlspecialchars($client_name, ENT_QUOTES, 'UTF-8');
$company_name = htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8');
$client_email = filter_var($client_email, FILTER_SANITIZE_EMAIL);
$client_phone = htmlspecialchars($client_phone, ENT_QUOTES, 'UTF-8');
$company_site = htmlspecialchars($company_site, ENT_QUOTES, 'UTF-8');
$company_location = htmlspecialchars($company_location, ENT_QUOTES, 'UTF-8');
$main_service = htmlspecialchars($main_service, ENT_QUOTES, 'UTF-8');
$specific_service = htmlspecialchars($specific_service, ENT_QUOTES, 'UTF-8');

// Collect Step 2 data (dynamic based on service)
$step2_data = [];
foreach ($_POST as $key => $value) {
    if (!in_array($key, ['client_name', 'company_name', 'client_email', 'client_phone', 'company_site', 'company_location', 'main_service', 'specific_service'])) {
        if (is_array($value)) {
            $step2_data[$key] = htmlspecialchars(json_encode($value), ENT_QUOTES, 'UTF-8');
        } else {
            $step2_data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
}

// Handle file uploads
$uploaded_files = [];
if (!empty($_FILES)) {
    $uploadDir = __DIR__ . '/../uploads/start_projects/' . time() . '_' . rand(1000, 9999);
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Handle regular file input
    if (!empty($_FILES['ref_files'])) {
        foreach ($_FILES['ref_files']['name'] as $i => $filename) {
            if (empty($_FILES['ref_files']['tmp_name'][$i])) continue;
            $tmp = $_FILES['ref_files']['tmp_name'][$i];
            $orig = basename($_FILES['ref_files']['name'][$i]);
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
            $target = $uploadDir . '/' . $safe;
            if (move_uploaded_file($tmp, $target)) {
                $uploaded_files[] = 'uploads/start_projects/' . basename($uploadDir) . '/' . $safe;
            }
        }
    }

    // Handle base64-encoded files from JS
    for ($i = 0; $i < 100; $i++) {
        if (empty($_POST["file_$i"])) continue;
        $data = $_POST["file_$i"];
        $name = $_POST["file_$i_name"] ?? "file_$i";
        
        // Parse data URL
        if (strpos($data, 'data:') === 0) {
            $parts = explode(',', $data, 2);
            if (count($parts) === 2) {
                $binaryData = base64_decode($parts[1]);
                $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
                $target = $uploadDir . '/' . $safe;
                if (file_put_contents($target, $binaryData)) {
                    $uploaded_files[] = 'uploads/start_projects/' . basename($uploadDir) . '/' . $safe;
                }
            }
        }
    }
}

// Ensure project_submissions table exists
try {
    $createSql = "CREATE TABLE IF NOT EXISTS project_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(255) NOT NULL,
        company_name VARCHAR(255),
        client_email VARCHAR(255) NOT NULL,
        client_phone VARCHAR(100),
        company_site VARCHAR(255),
        company_location VARCHAR(255),
        main_service VARCHAR(255),
        specific_service VARCHAR(255),
        step2_data LONGTEXT,
        attachments LONGTEXT,
        status VARCHAR(50) DEFAULT 'new',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($createSql);
} catch (PDOException $e) {
    // Table likely already exists
}

// Insert into database
try {
    $step2_json = json_encode($step2_data);
    $attachments_json = json_encode($uploaded_files);
    
    $stmt = $pdo->prepare("INSERT INTO project_submissions (client_name, company_name, client_email, client_phone, company_site, company_location, main_service, specific_service, step2_data, attachments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ok = $stmt->execute([$client_name, $company_name, $client_email, $client_phone, $company_site, $company_location, $main_service, $specific_service, $step2_json, $attachments_json]);

    if ($ok) {
        // Send admin email
        $admin_email = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@localhost';
        $subject = 'New Project Submission - ' . $main_service;
        
        $body = '<html><body style="font-family:Arial,sans-serif; color:#333;">';
        $body .= '<h2 style="color:#00d4ff;">New Project Submission</h2>';
        $body .= '<p><strong>Client Name:</strong> ' . $client_name . '</p>';
        $body .= '<p><strong>Company:</strong> ' . $company_name . '</p>';
        $body .= '<p><strong>Email:</strong> ' . $client_email . '</p>';
        $body .= '<p><strong>Phone:</strong> ' . $client_phone . '</p>';
        $body .= '<p><strong>Service:</strong> ' . $main_service . ' > ' . $specific_service . '</p>';
        
        if (!empty($step2_data)) {
            $body .= '<h3>Project Details:</h3><ul>';
            foreach ($step2_data as $key => $val) {
                $label = str_replace('_', ' ', ucfirst($key));
                $body .= '<li><strong>' . $label . ':</strong> ' . (is_string($val) ? nl2br($val) : $val) . '</li>';
            }
            $body .= '</ul>';
        }
        
        if (!empty($uploaded_files)) {
            $body .= '<h3>Attachments:</h3><ul>';
            foreach ($uploaded_files as $f) {
                $body .= '<li><a href="' . htmlspecialchars($f) . '">' . basename($f) . '</a></li>';
            }
            $body .= '</ul>';
        }
        
        $body .= '<p style="margin-top:2rem; color:#999; font-size:12px;">Submitted on ' . date('Y-m-d H:i:s') . '</p>';
        $body .= '</body></html>';

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . (defined('SENDER_EMAIL') ? SENDER_EMAIL : 'no-reply@localhost') . "\r\n";

        @mail($admin_email, $subject, $body, $headers);

        // Send user confirmation email
        $user_subject = 'Project Idea Received - Pixelara Web Solutions';
        $user_body = '<html><body style="font-family:Arial,sans-serif; color:#333;">';
        $user_body .= '<h2 style="color:#00d4ff;">Thanks, ' . htmlspecialchars($client_name) . '!</h2>';
        $user_body .= '<p>We\'ve received your project idea and it\'s very important to us.</p>';
        $user_body .= '<p><strong>Our executive team will review your submission and reach out within 24 hours with a customized proposal.</strong></p>';
        $user_body .= '<p><em>In the meantime, feel free to reach us directly:</em></p>';
        $user_body .= '<p>Email: <a href="mailto:' . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@localhost') . '">' . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@localhost') . '</a><br/>Phone: +91 70488 11552</p>';
        $user_body .= '<p>Best regards,<br/><strong>Pixelara Web Solutions Team</strong></p>';
        $user_body .= '</body></html>';

        $user_headers = "MIME-Version: 1.0\r\n";
        $user_headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $user_headers .= "From: " . (defined('SENDER_EMAIL') ? SENDER_EMAIL : 'no-reply@localhost') . "\r\n";

        @mail($client_email, $user_subject, $user_body, $user_headers);

        $response['success'] = true;
        $response['message'] = 'Your project submission has been received successfully!';
    } else {
        $response['message'] = 'Failed to save your submission. Please try again.';
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
exit;

