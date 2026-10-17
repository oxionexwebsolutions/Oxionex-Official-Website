<?php
/**
 * Shared protection for public AJAX forms.
 * Uses the session plus a small file counter so it works without Composer or
 * a cache service on shared hosting.
 */
require_once __DIR__ . '/../config.php';

function oxionex_start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function oxionex_csrf_token(): string
{
    oxionex_start_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_token(): string
{
    return oxionex_csrf_token();
}

function oxionex_form_started(): int
{
    return time();
}

function oxionex_json_error(string $message, int $status = 400): never
{
    http_response_code($status);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

function oxionex_rate_limit(string $bucket): bool
{
    if (!defined('RATE_LIMIT_ENABLED') || !RATE_LIMIT_ENABLED) {
        return true;
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = hash('sha256', $bucket . '|' . $ip);
    $directory = defined('LOG_FILE') ? dirname(LOG_FILE) : dirname(__DIR__) . '/../logs';
    if (!is_dir($directory) && !@mkdir($directory, 0750, true)) {
        return false;
    }

    $file = $directory . '/rate-' . $key . '.json';
    $handle = @fopen($file, 'c+');
    if (!$handle || !flock($handle, LOCK_EX)) {
        if (is_resource($handle)) {
            fclose($handle);
        }
        return false;
    }

    $raw = stream_get_contents($handle);
    $state = json_decode($raw ?: '', true);
    $now = time();
    $window = defined('RATE_LIMIT_WINDOW') ? RATE_LIMIT_WINDOW : 60;
    $limit = defined('RATE_LIMIT_REQUESTS') ? RATE_LIMIT_REQUESTS : 10;
    if (!is_array($state) || ($now - (int) ($state['started'] ?? 0)) >= $window) {
        $state = ['started' => $now, 'count' => 0];
    }

    $state['count']++;
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($state));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return $state['count'] <= $limit;
}

function oxionex_validate_form_request(string $bucket): void
{
    oxionex_start_secure_session();

    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        oxionex_json_error('Your form session expired. Please refresh and try again.', 403);
    }

    if (!empty($_POST['website'])) {
        oxionex_json_error('Unable to process this request.', 400);
    }

    $started = filter_var($_POST['form_started_at'] ?? ($_POST['form_started'] ?? 0), FILTER_VALIDATE_INT);
    if (!$started || time() - $started < 3 || time() - $started > 86400) {
        oxionex_json_error('Please take a moment to complete the form and try again.', 400);
    }

    if (!oxionex_rate_limit($bucket)) {
        oxionex_json_error('Too many requests. Please try again later.', 429);
    }
}
