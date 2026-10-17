<?php
/**
 * Oxionex Web Solutions - Admin Login
 * Secure authentication for admin panel
 */

require_once '../includes/form-security.php';
oxionex_start_secure_session();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../includes/db.php';
require_once 'functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {
        try {
            if (strlen($username) > 100 || admin_login_is_locked($username)) {
                $error = 'Invalid username or password.';
                admin_record_login_failure($username);
            } elseif (!$pdo) {
                $error = 'Database connection error. Please try again later.';
            } else {
                $query = "SELECT id, username, password FROM admin_users WHERE username = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    admin_clear_login_failures($username);
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['last_activity'] = time();
                    $_SESSION['force_password_change'] = hash_equals(
                        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                        (string) $user['password']
                    );
                    header('Location: ' . ($_SESSION['force_password_change'] ? 'change_password.php' : 'dashboard.php'));
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                    admin_record_login_failure($username);
                    error_log('Failed admin login attempt.');
                }
            }
        } catch (PDOException $e) {
            $error = 'Authentication error. Please try again.';
            error_log('Login error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oxionex Admin - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a3e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #a0aec0;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.2);
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background: rgba(0, 212, 255, 0.05);
            border-color: #00d4ff;
            box-shadow: 0 0 10px rgba(0, 212, 255, 0.2);
            outline: none;
            color: #fff;
        }
        .form-control::placeholder {
            color: #666;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%);
            border: none;
            border-radius: 10px;
            color: #0f0f23;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 212, 255, 0.3);
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
            border: none;
            padding: 12px 15px;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; position: relative;">
                    <div style="position: absolute; width: 18px; height: 18px; background: #000; top: 0; left: 0;"></div>
                    <div style="position: absolute; width: 14px; height: 14px; background: #000; top: 10px; left: 12px;"></div>
                    <div style="position: absolute; width: 10px; height: 10px; background: #000; top: 20px; left: 24px;"></div>
                    <div style="position: absolute; width: 6px; height: 6px; background: #000; top: 30px; left: 36px;"></div>
                </div>
                <div>
                    <h1 style="font-size: 28px; font-weight: 800; color: #000; margin: 0; letter-spacing: 1px;">OXIONEX</h1>
                    <p style="color: #666; font-size: 12px; letter-spacing: 0.5px; margin: 4px 0 0 0;">ADMIN PANEL</p>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <?php echo admin_csrf_field(); ?>
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <div style="text-align: center; margin-top: 30px; color: #a0aec0; font-size: 12px;">
            <p>Use the administrator credentials configured for this installation.</p>
            <p><a href="/" style="color: #00d4ff; text-decoration: none;">Back to Website</a></p>
        </div>
    </div>
</body>
</html>
