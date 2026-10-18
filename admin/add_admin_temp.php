<?php
/**
 * TEMPORARY: Add a new admin user.
 * DELETE THIS FILE immediately after use.
 */
require_once '../includes/db.php';

if (!$pdo) {
    exit('Database connection unavailable.');
}

// ── CONFIGURE THESE ──────────────────────────────────────────
$new_username = 'new_admin';          // change this
$new_email    = 'admin@example.com';  // change this
$new_password = 'StrongPassword123!'; // change this (min 12 chars)
// ─────────────────────────────────────────────────────────────

if (!preg_match('/^[A-Za-z0-9._-]{3,100}$/', $new_username)) {
    exit('Invalid username format.');
}
if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email address.');
}
if (strlen($new_password) < 12) {
    exit('Password must be at least 12 characters.');
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM admin_users WHERE username = ? OR email = ?');
$stmt->execute([$new_username, $new_email]);
if ((int) $stmt->fetchColumn() > 0) {
    exit('Username or email already exists.');
}

$stmt = $pdo->prepare('INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)');
$stmt->execute([$new_username, password_hash($new_password, PASSWORD_DEFAULT), $new_email]);

@unlink(__FILE__); // self-delete after success
echo 'Admin "' . htmlspecialchars($new_username, ENT_QUOTES, 'UTF-8') . '" created successfully. This file has been deleted.';
