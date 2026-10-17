<?php
/**
 * One-time administrator creation. Delete this file automatically after success.
 */
require_once '../includes/form-security.php';
oxionex_start_secure_session();
require_once '../includes/db.php';
require_once 'functions.php';

if (!$pdo) {
    exit('Database connection unavailable.');
}
$count = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
if ($count > 0) {
    http_response_code(404);
    exit('Setup is already complete.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (!preg_match('/^[A-Za-z0-9._-]{3,100}$/', $username) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
        $error = 'Provide a valid username, email, and password of at least 12 characters.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)');
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $email]);
        @unlink(__FILE__);
        exit('Administrator created. The setup script has been removed.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Oxionex Admin Setup</title></head>
<body>
    <main>
        <h1>Create the first administrator</h1>
        <?php if ($error): ?><p role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
        <form method="post">
            <?php echo admin_csrf_field(); ?>
            <label>Username <input name="username" required autocomplete="username"></label>
            <label>Email <input name="email" type="email" required autocomplete="email"></label>
            <label>Password <input name="password" type="password" minlength="12" required autocomplete="new-password"></label>
            <button type="submit">Create administrator</button>
        </form>
    </main>
</body>
</html>
