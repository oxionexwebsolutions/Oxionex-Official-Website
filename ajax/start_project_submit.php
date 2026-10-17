<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/form-security.php';
oxionex_validate_form_request('start-project');

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

if (strlen($client_name) > 120 || strlen($company_name) > 160 || strlen($client_email) > 254 || strlen($client_phone) > 40 || strlen($company_site) > 2048 || strlen($company_location) > 120 || strlen($main_service) > 120 || strlen($specific_service) > 160) {
    oxionex_json_error('Please shorten the submitted values and try again.');
}

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

// Store raw validated values. Escape only when rendering email HTML or UI.
$client_email = filter_var($client_email, FILTER_SANITIZE_EMAIL);

// Collect Step 2 data (dynamic based on service)
$step2_data = [];
foreach ($_POST as $key => $value) {
    if (!in_array($key, ['client_name', 'company_name', 'client_email', 'client_phone', 'company_site', 'company_location', 'main_service', 'specific_service'])) {
        if (is_array($value)) {
            $step2_data[$key] = json_encode($value);
        } else {
            $step2_data[$key] = is_string($value) ? mb_substr($value, 0, 5000) : $value;
        }
    }
}

// Verify uploads by content, not by the client-supplied filename or MIME type.
$uploaded_files = [];
$uploadCount = 0;
$totalUploadSize = 0;
$uploadDir = null;
$allowedMimes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
    'application/pdf' => 'pdf',
    'text/plain' => 'txt',
];

$storeUpload = static function (string $binary, string $sourceName) use (&$uploaded_files, &$uploadCount, &$totalUploadSize, &$uploadDir, $allowedMimes): void {
    $size = strlen($binary);
    $maxSize = defined('MAX_UPLOAD_SIZE') ? MAX_UPLOAD_SIZE : 5242880;
    $maxFiles = defined('MAX_UPLOAD_FILES') ? MAX_UPLOAD_FILES : 5;
    $maxTotal = defined('MAX_UPLOAD_TOTAL_SIZE') ? MAX_UPLOAD_TOTAL_SIZE : ($maxSize * $maxFiles);
    if ($uploadCount >= $maxFiles || $size < 1 || $size > $maxSize || $totalUploadSize + $size > $maxTotal) {
        throw new RuntimeException('Upload limits exceeded.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($binary);
    if (!isset($allowedMimes[$mime])) {
        throw new RuntimeException('Unsupported upload type.');
    }

    if ($uploadDir === null) {
        $folder = bin2hex(random_bytes(16));
        $uploadDir = __DIR__ . '/../uploads/start_projects/' . $folder;
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0750, true)) {
            throw new RuntimeException('Upload storage unavailable.');
        }
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowedMimes[$mime];
    $target = $uploadDir . '/' . $filename;
    if (file_put_contents($target, $binary, LOCK_EX) === false) {
        throw new RuntimeException('Upload storage unavailable.');
    }

    $uploaded_files[] = 'uploads/start_projects/' . basename($uploadDir) . '/' . $filename;
    $uploadCount++;
    $totalUploadSize += $size;
};

try {
    if (!empty($_FILES['ref_files']['tmp_name'])) {
        foreach ((array) $_FILES['ref_files']['tmp_name'] as $index => $tmpPath) {
            if (!is_uploaded_file($tmpPath) || ($_FILES['ref_files']['error'][$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }
            $storeUpload((string) file_get_contents($tmpPath), (string) ($_FILES['ref_files']['name'][$index] ?? 'upload'));
        }
    }

    // The current JS uploader sends data URLs; cap and verify them before storage.
    for ($i = 0; $i < 10; $i++) {
        $data = $_POST["file_$i"] ?? '';
        if (!is_string($data) || strpos($data, 'data:') !== 0) {
            continue;
        }
        $parts = explode(',', $data, 2);
        if (count($parts) !== 2 || !preg_match('/^data:[^;]+;base64$/', $parts[0])) {
            throw new RuntimeException('Invalid upload data.');
        }
        $binaryData = base64_decode($parts[1], true);
        if ($binaryData === false) {
            throw new RuntimeException('Invalid upload data.');
        }
        $storeUpload($binaryData, 'client-upload');
    }
} catch (Throwable $uploadError) {
    error_log('Upload rejected: ' . $uploadError->getMessage());
    oxionex_json_error('One or more files could not be uploaded. Please check the file limits and try again.');
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
        $subject = 'New Project Submission - ' . preg_replace('/[\r\n]+/', ' ', $main_service);
        $htmlClientName = htmlspecialchars($client_name, ENT_QUOTES, 'UTF-8');
        $htmlCompanyName = htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8');
        $htmlClientEmail = htmlspecialchars($client_email, ENT_QUOTES, 'UTF-8');
        $htmlClientPhone = htmlspecialchars($client_phone, ENT_QUOTES, 'UTF-8');
        $htmlMainService = htmlspecialchars($main_service, ENT_QUOTES, 'UTF-8');
        $htmlSpecificService = htmlspecialchars($specific_service, ENT_QUOTES, 'UTF-8');
        
        $body = '<html><body style="font-family:Arial,sans-serif; color:#333;">';
        $body .= '<h2 style="color:#00d4ff;">New Project Submission</h2>';
        $body .= '<p><strong>Client Name:</strong> ' . $htmlClientName . '</p>';
        $body .= '<p><strong>Company:</strong> ' . $htmlCompanyName . '</p>';
        $body .= '<p><strong>Email:</strong> ' . $htmlClientEmail . '</p>';
        $body .= '<p><strong>Phone:</strong> ' . $htmlClientPhone . '</p>';
        $body .= '<p><strong>Service:</strong> ' . $htmlMainService . ' &gt; ' . $htmlSpecificService . '</p>';
        
        if (!empty($step2_data)) {
            $body .= '<h3>Project Details:</h3><ul>';
            foreach ($step2_data as $key => $val) {
                $label = str_replace('_', ' ', ucfirst($key));
                $body .= '<li><strong>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ':</strong> ' . (is_string($val) ? nl2br(htmlspecialchars($val, ENT_QUOTES, 'UTF-8')) : $val) . '</li>';
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

        $adminMailSent = mail($admin_email, $subject, $body, $headers);

        // Send user confirmation email
        $user_subject = 'Project Idea Received - Oxionex Web Solutions';
        $user_body = '<html><body style="font-family:Arial,sans-serif; color:#333;">';
        $user_body .= '<h2 style="color:#00d4ff;">Thanks, ' . $htmlClientName . '!</h2>';
        $user_body .= '<p>We\'ve received your project idea and it\'s very important to us.</p>';
        $user_body .= '<p><strong>Our executive team will review your submission and reach out within 24 hours with a customized proposal.</strong></p>';
        $user_body .= '<p><em>In the meantime, feel free to reach us directly:</em></p>';
        $user_body .= '<p>Email: <a href="mailto:' . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@localhost') . '">' . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@localhost') . '</a><br/>Phone: +91 70488 11552</p>';
        $user_body .= '<p>Best regards,<br/><strong>Oxionex Web Solutions Team</strong></p>';
        $user_body .= '</body></html>';

        $user_headers = "MIME-Version: 1.0\r\n";
        $user_headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $user_headers .= "From: " . (defined('SENDER_EMAIL') ? SENDER_EMAIL : 'no-reply@localhost') . "\r\n";

        $userMailSent = mail($client_email, $user_subject, $user_body, $user_headers);

        if (!$adminMailSent || !$userMailSent) {
            error_log('Project submission mail delivery failed.');
        }

        $response['success'] = true;
        $response['message'] = $userMailSent
            ? 'Your project submission has been received successfully!'
            : 'Your project submission was saved successfully. We could not send the confirmation email, but our team can still review it.';
    } else {
        $response['message'] = 'Failed to save your submission. Please try again.';
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $response['message'] = 'We could not save your submission right now. Please try again later.';
}

echo json_encode($response);
exit;

