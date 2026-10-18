<?php
/**
 * Oxionex Web Solutions - Admin User Management
 * Add or remove administrator accounts. Requires login.
 */
require_once '../includes/form-security.php';
oxionex_start_secure_session();
require_once '../includes/db.php';
require_once 'functions.php';

verify_admin_session();

if (!$pdo) {
    exit('Database connection unavailable.');
}

$error = '';
$message = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $username = trim((string) ($_POST['username'] ?? ''));
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!preg_match('/^[A-Za-z0-9._-]{3,100}$/', $username) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
            $error = 'Provide a valid username (3–100 chars), email, and password of at least 12 characters.';
        } else {
            $check = $pdo->prepare('SELECT COUNT(*) FROM admin_users WHERE username = ? OR email = ?');
            $check->execute([$username, $email]);
            if ((int) $check->fetchColumn() > 0) {
                $error = 'Username or email already exists.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)');
                $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $email]);
                $message = 'Admin "' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '" created successfully.';
            }
        }
    } elseif ($action === 'delete') {
        $deleteId = (int) ($_POST['id'] ?? 0);
        if ($deleteId === (int) $_SESSION['admin_id']) {
            $error = 'You cannot delete your own account.';
        } else {
            $total = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
            if ($total <= 1) {
                $error = 'Cannot delete the last admin account.';
            } else {
                $pdo->prepare('DELETE FROM admin_users WHERE id = ?')->execute([$deleteId]);
                $message = 'Admin account deleted.';
            }
        }
    }
}

$admins = $pdo->query('SELECT id, username, email FROM admin_users ORDER BY id ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - Oxionex</title>
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
        .main-content { padding: 30px; overflow-y: auto; }
        .card-dark { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 24px; margin-bottom: 30px; }
        label { display: block; margin-bottom: 6px; color: #00d4ff; font-weight: 600; font-size: 14px; }
        input[type=text], input[type=email], input[type=password] { width: 100%; padding: 11px 14px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; font-family: 'Inter', sans-serif; margin-bottom: 16px; }
        input:focus { outline: none; border-color: #00d4ff; box-shadow: 0 0 8px rgba(0,212,255,0.25); }
        .btn-submit { background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); border: none; color: #0f0f23; padding: 11px 24px; border-radius: 8px; cursor: pointer; font-weight: 700; }
        .alert { border-radius: 10px; border: none; padding: 12px 16px; margin-bottom: 20px; }
        .alert-success { background: rgba(16,185,129,0.1); color: #6ee7b7; }
        .alert-danger { background: rgba(239,68,68,0.1); color: #fca5a5; }
        .data-table { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; overflow: hidden; }
        .data-table table { width: 100%; margin: 0; border-collapse: collapse; }
        .data-table th { background: rgba(0,212,255,0.1); padding: 14px 16px; font-weight: 600; text-align: left; color: #00d4ff; }
        .data-table td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #a0aec0; }
        .you-badge { background: rgba(0,212,255,0.15); color: #00d4ff; font-size: 11px; padding: 2px 8px; border-radius: 20px; margin-left: 8px; font-weight: 700; }
        .btn-del { background: rgba(239,68,68,0.15); color: #fca5a5; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; }
        .btn-del:hover { background: rgba(239,68,68,0.3); }
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
            <li><a href="setup.php" class="active"><i class="fas fa-users-cog"></i> Manage Admins</a></li>
            <li style="margin-top:20px;border-top:1px solid rgba(255,255,255,0.1);padding-top:20px;">
                <?php echo admin_logout_form(); ?>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <h1 style="font-size:28px;font-weight:700;margin-bottom:30px;">Manage Admin Users</h1>

        <?php if ($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>

        <!-- Current Admins -->
        <div class="data-table" style="margin-bottom:30px;">
            <table>
                <thead><tr><th>#</th><th>Username</th><th>Email</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?php echo (int) $admin['id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($admin['username'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php if ((int) $admin['id'] === (int) $_SESSION['admin_id']): ?>
                                <span class="you-badge">You</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($admin['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php if ((int) $admin['id'] !== (int) $_SESSION['admin_id']): ?>
                            <form method="post" onsubmit="return confirm('Delete this admin account?');" style="display:inline;">
                                <?php echo admin_csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int) $admin['id']; ?>">
                                <button type="submit" class="btn-del"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                            <?php else: ?>
                                <span style="color:#64748b;font-size:13px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add New Admin -->
        <div class="card-dark" style="max-width:480px;">
            <h2 style="font-size:18px;font-weight:700;margin-bottom:20px;">Add New Admin</h2>
            <form method="post">
                <?php echo admin_csrf_field(); ?>
                <input type="hidden" name="action" value="add">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="off">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autocomplete="off">
                <label for="password">Password <span style="color:#94a3b8;font-weight:400;">(min 12 chars)</span></label>
                <input type="password" id="password" name="password" minlength="12" required autocomplete="new-password">
                <button type="submit" class="btn-submit"><i class="fas fa-user-plus"></i> Create Admin</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
