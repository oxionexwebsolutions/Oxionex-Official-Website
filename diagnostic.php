<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

function out(string $label, $value): void {
    if (is_bool($value)) {
        $value = $value ? 'true' : 'false';
    } elseif (is_array($value) || is_object($value)) {
        $value = print_r($value, true);
    }
    echo $label . ': ' . $value . PHP_EOL;
}

echo "Oxionex Diagnostic Report" . PHP_EOL;
echo str_repeat('=', 40) . PHP_EOL;
out('PHP Version', PHP_VERSION);
out('SAPI', PHP_SAPI);
out('Document Root', $_SERVER['DOCUMENT_ROOT'] ?? 'unknown');
out('Current Dir', __DIR__);
out('Display Errors', ini_get('display_errors'));
out('Error Reporting', error_reporting());

echo PHP_EOL . 'Extensions' . PHP_EOL;
echo str_repeat('-', 40) . PHP_EOL;
foreach (['pdo', 'pdo_mysql', 'mysqli', 'mbstring', 'json', 'session'] as $ext) {
    out($ext, extension_loaded($ext));
}

echo PHP_EOL . 'Files' . PHP_EOL;
echo str_repeat('-', 40) . PHP_EOL;
$files = [
    'config.php',
    'includes/db.php',
    'includes/header.php',
    'includes/footer.php',
    '.htaccess',
];
foreach ($files as $file) {
    $path = __DIR__ . DIRECTORY_SEPARATOR . $file;
    out($file . ' exists', file_exists($path));
    if (file_exists($path)) {
        out($file . ' readable', is_readable($path));
    }
}

echo PHP_EOL . 'Session' . PHP_EOL;
echo str_repeat('-', 40) . PHP_EOL;
try {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    out('Session status', session_status());
    out('Session ID', session_id());
} catch (Throwable $e) {
    out('Session error', $e->getMessage());
}

echo PHP_EOL . 'Config' . PHP_EOL;
echo str_repeat('-', 40) . PHP_EOL;
try {
    require_once __DIR__ . '/config.php';
    out('DB_HOST', DB_HOST);
    out('DB_USER', DB_USER);
    out('DB_NAME', DB_NAME);
    out('DB_PORT', DB_PORT);
    out('DEBUG_MODE', DEBUG_MODE);
} catch (Throwable $e) {
    out('Config load error', $e->getMessage());
}

echo PHP_EOL . 'Database' . PHP_EOL;
echo str_repeat('-', 40) . PHP_EOL;
try {
    require_once __DIR__ . '/includes/db.php';
    if (isset($pdo) && $pdo instanceof PDO) {
        out('PDO connection', 'success');
        $stmt = $pdo->query('SELECT DATABASE() AS db_name, VERSION() AS mysql_version');
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : [];
        out('Database name', $row['db_name'] ?? 'unknown');
        out('MySQL version', $row['mysql_version'] ?? 'unknown');
    } else {
        out('PDO connection', 'not available');
    }
} catch (Throwable $e) {
    out('Database error', $e->getMessage());
}

echo PHP_EOL . 'Routing' . PHP_EOL;
echo str_repeat('-', 40) . PHP_EOL;
out('Request URI', $_SERVER['REQUEST_URI'] ?? 'unknown');
out('Script name', $_SERVER['SCRIPT_NAME'] ?? 'unknown');
out('Query string', $_SERVER['QUERY_STRING'] ?? '');
