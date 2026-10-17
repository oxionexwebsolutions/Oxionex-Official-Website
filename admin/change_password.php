<?php
require_once '../includes/form-security.php';
oxionex_start_secure_session();
require_once '../includes/db.php';
require_once 'functions.php';
verify_admin_session();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');
    if (strlen($password) < 12) {
        $error = 'Use a password with at least 12 characters.';
    } elseif (!hash_equals($password, $confirmation)) {
        $error = 'The passwords do not match.';
    } elseif (!$pdo) {
        $error = 'Database connection error.';
    } else {
        $stmt = $pdo->prepare('UPDATE admin_users SET password = ? WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
        $_SESSION['force_password_change'] = false;
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Change Admin Password - Oxionex</title></head>
<body>
    <main>
        <h1>Change your administrator password</h1>
        <?php if ($error): ?><p role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
        <form method="post">
            <?php echo admin_csrf_field(); ?>
            <label for="password">New password</label>
            <input id="password" name="password" type="password" minlength="12" required autocomplete="new-password">
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" minlength="12" required autocomplete="new-password">
            <button type="submit">Update password</button>
        </form>
    </main>
</body>
</html>
