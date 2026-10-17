<?php
/**
 * Oxionex Web Solutions - Admin Helper Functions
 * Common functions for admin panel pages
 */

require_once __DIR__ . '/../includes/form-security.php';

function admin_csrf_token(): string
{
    return csrf_token();
}

function admin_csrf_field(): string
{
    return '<input type="hidden" name="admin_csrf" value="' . htmlspecialchars(admin_csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function require_admin_csrf(): void
{
    $token = $_POST['admin_csrf'] ?? '';
    if (!is_string($token) || !hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
        http_response_code(403);
        exit('Invalid security token. Please refresh and try again.');
    }
}

function admin_logout_form(string $class = ''): string
{
    return '<form method="post" action="logout.php" class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '">' . admin_csrf_field() . '<button type="submit" class="btn btn-link p-0 text-decoration-none"><i class="fas fa-sign-out-alt"></i> Logout</button></form>';
}

function admin_login_key(string $username): string
{
    return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '|' . strtolower($username));
}

function admin_login_attempts(string $username): array
{
    $file = dirname(LOG_FILE) . '/admin-login-attempts.json';
    $records = is_readable($file) ? json_decode((string) file_get_contents($file), true) : [];
    $records = is_array($records) ? $records : [];
    $key = admin_login_key($username);
    $record = $records[$key] ?? ['count' => 0, 'locked_until' => 0];
    if ((int) $record['locked_until'] < time() && (int) $record['count'] > 0) {
        $record = ['count' => 0, 'locked_until' => 0];
    }
    return [$file, $records, $key, $record];
}

function admin_login_is_locked(string $username): bool
{
    [, , , $record] = admin_login_attempts($username);
    return (int) $record['locked_until'] > time();
}

function admin_record_login_failure(string $username): void
{
    [$file, $records, $key, $record] = admin_login_attempts($username);
    $record['count']++;
    if ($record['count'] >= MAX_LOGIN_ATTEMPTS) {
        $record['locked_until'] = time() + LOCKOUT_DURATION;
    }
    $records[$key] = $record;
    @mkdir(dirname($file), 0750, true);
    file_put_contents($file, json_encode($records), LOCK_EX);
}

function admin_clear_login_failures(string $username): void
{
    [$file, $records, $key] = admin_login_attempts($username);
    unset($records[$key]);
    file_put_contents($file, json_encode($records), LOCK_EX);
}

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

    if (!empty($_SESSION['force_password_change']) && basename($_SERVER['SCRIPT_NAME'] ?? '') !== 'change_password.php') {
        header('Location: change_password.php');
        exit;
    }
    
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

function valid_project_url(string $url): bool
{
    if ($url === '') {
        return true;
    }
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true) && filter_var($url, FILTER_VALIDATE_URL) !== false;
}
?>
