<?php
/**
 * Shared form security helpers. Keep these dependency-free for shared hosting.
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function check_form_request(string $formName): void
{
    global $response;

    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    $honeypot = trim((string) ($_POST['website'] ?? ''));
    $startedAt = (int) ($_POST['form_started_at'] ?? 0);

    if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) $token)) {
        respond_form_error('Your session expired. Please refresh the page and try again.');
    }

    if ($honeypot !== '') {
        respond_form_error('Your submission could not be accepted.');
    }

    if ($startedAt < 1 || (time() - $startedAt) < FORM_MIN_SECONDS || (time() - $startedAt) > FORM_MAX_SECONDS) {
        respond_form_error('Please take a moment to complete the form and try again.');
    }

    if (!rate_limit_allows($formName, $_SERVER['REMOTE_ADDR'] ?? 'unknown')) {
        respond_form_error('Too many requests. Please try again later.');
    }
}

function respond_form_error(string $message): void
{
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

function rate_limit_allows(string $formName, string $ip): bool
{
    if (!defined('RATE_LIMIT_ENABLED') || !RATE_LIMIT_ENABLED) {
        return true;
    }

    $path = dirname(LOG_FILE) . '/form-rate-limit.json';
    $handle = @fopen($path, 'c+');
    if (!$handle || !flock($handle, LOCK_EX)) {
        if ($handle) {
            fclose($handle);
        }
        return true;
    }

    $contents = stream_get_contents($handle);
    $records = json_decode($contents ?: '{}', true);
    $records = is_array($records) ? $records : [];
    $key = hash('sha256', $formName . '|' . $ip);
    $now = time();
    $window = max(60, (int) RATE_LIMIT_WINDOW);
    $record = $records[$key] ?? ['started_at' => $now, 'count' => 0];

    if (($now - (int) $record['started_at']) >= $window) {
        $record = ['started_at' => $now, 'count' => 0];
    }

    $record['count']++;
    $allowed = $record['count'] <= (int) RATE_LIMIT_REQUESTS;
    $records[$key] = $record;

    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($records));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return $allowed;
}

function clean_header_value(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}
