<?php
/**
 * Oxionex Web Solutions - Database Configuration
 * Secure PDO-based database connection
 */

// Load config
require_once __DIR__ . '/../config.php';

try {
    // Create PDO connection with proper configuration
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // Log error
    if (LOG_ERRORS && LOG_FILE) {
        @mkdir(dirname(LOG_FILE), 0755, true);
        error_log('Database connection failed: ' . $e->getMessage() . "\n", 3, LOG_FILE);
    }
    // If debugging is enabled, show the error details to the browser for diagnostics.
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Database connection failed: " . $e->getMessage() . "\n\n";
        echo "DSN: mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . "\n";
        echo "User: " . DB_USER . "\n";
        // Stop execution to allow immediate debugging feedback on the deployed host
        exit;
    }

    // Send JSON response for AJAX requests
    if (strpos($_SERVER['REQUEST_URI'], '/ajax/') !== false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error.']);
        exit;
    }

    // For regular pages, set $pdo to null (graceful degradation)
    $pdo = null;
}

/**
 * Helper Functions for Database Operations
 */

function executeQuery($query, $params = []) {
    global $pdo;
    if (!$pdo) return false;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log('Query error: ' . $e->getMessage());
        return false;
    }
}

function fetchOne($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt ? $stmt->fetch() : null;
}

function fetchAll($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt ? $stmt->fetchAll() : [];
}

function insert($table, $data) {
    global $pdo;
    if (!$pdo) return false;
    try {
        $keys = array_keys($data);
        $placeholders = array_fill(0, count($keys), '?');
        $query = 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $pdo->prepare($query);
        $stmt->execute(array_values($data));
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log('Insert error: ' . $e->getMessage());
        return false;
    }
}
