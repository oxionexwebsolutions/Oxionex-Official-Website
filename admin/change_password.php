<?php
require_once '../includes/form-security.php';
oxionex_start_secure_session();
require_once '../includes/db.php';
require_once 'functions.php';
verify_admin_session();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $password      = (string) ($_POST['password'] ?? '');
    $confirmation  = (string) ($_POST['password_confirmation'] ?? '');
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Oxionex Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f0f23; color: #fff; }
        .dashboard-container { display: grid; grid-template-columns: 250px 1fr; min-height: 100vh; }
        .sidebar { background: #1a1a3e; border-right: 1px solid rgba(255,255,255,0.1); padding: 30px 20px; }
        .sidebar-brand { font-size: 24px; font-weight: 700; background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 30px; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li { margin-bottom: 10px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 10px; padding: 12px 15px; color: #a0aec0; text-decoration: none; border-radius: 10px; transition: all 0.3s ease; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(0,212,255,0.1); color: #00d4ff; }
        .main-content { padding: 30px; display: flex; align-items: flex-start; }
        .card-dark { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 30px; width: 100%; max-width: 440px; }
        label { display: block; margin-bottom: 7px; color: #00d4ff; font-weight: 600; font-size: 14px; }
        input[type=password] { width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; font-family: 'Inter', sans-serif; margin-bottom: 18px; }
        input:focus { outline: none; border-color: #00d4ff; box-shadow: 0 0 8px rgba(0,212,255,0.25); }
        .btn-submit { width: 100%; background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); border: none; color: #0f0f23; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 15px; }
        .alert { border-radius: 10px; border: none; padding: 12px 16px; margin-bottom: 20px; }
        .alert-danger { background: rgba(239,68,68,0.1); color: #fca5a5; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-diamond"></i> Oxionex</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="projects.php"><i class="fas fa-images"></i> Projects</a></li>
            <li><a href="inquiries.php"><i class="fas fa-envelope"></i> Inquiries</a></li>
            <li><a href="project-submissions.php"><i class="fas fa-clipboard-list"></i> Project Submissions</a></li>
            <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
            <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
            <li><a href="setup.php"><i class="fas fa-users-cog"></i> Manage Admins</a></li>
            <li style="margin-top:20px;border-top:1px solid rgba(255,255,255,0.1);padding-top:20px;">
                <?php echo admin_logout_form(); ?>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="card-dark">
            <h1 style="font-size:22px;font-weight:700;margin-bottom:24px;">Change Password</h1>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="post">
                <?php echo admin_csrf_field(); ?>
                <label for="password">New Password</label>
                <input id="password" name="password" type="password" minlength="12" required autocomplete="new-password" placeholder="At least 12 characters">
                <label for="password_confirmation">Confirm New Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" minlength="12" required autocomplete="new-password" placeholder="Repeat password">
                <button type="submit" class="btn-submit"><i class="fas fa-key"></i> Update Password</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
