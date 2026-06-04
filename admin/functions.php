<?php
/**
 * Oxionex Web Solutions - Admin Helper Functions
 * Common functions for admin panel pages
 */

// Check if admin is authenticated
function check_admin_auth() {
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
        header('Location: login.php');
        exit;
    }
}

// Verify admin session is still valid
function verify_admin_session() {
    check_admin_auth();
    
    // Check session timeout (30 minutes)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
}

// Get admin user info
function get_admin_info() {
    global $pdo;
    
    if (!isset($_SESSION['admin_id']) || !$pdo) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, email FROM admin_users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Safe redirect
function safe_redirect($url) {
    // Ensure URL is relative or from same domain
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $parsed = parse_url($url);
        if ($parsed['host'] !== $_SERVER['HTTP_HOST']) {
            $url = 'dashboard.php';
        }
    }
    header('Location: ' . $url);
    exit;
}

// Format date for display
function format_date($date) {
    return date('M d, Y', strtotime($date));
}

// Format datetime for display
function format_datetime($datetime) {
    return date('M d, Y H:i', strtotime($datetime));
}

// Truncate text
function truncate_text($text, $length = 50) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}
?>
