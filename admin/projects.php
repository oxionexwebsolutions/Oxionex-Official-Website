<?php
/**
 * Oxionex Web Solutions - Manage Projects
 */

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
require_once 'functions.php';

// Verify session
verify_admin_session();

$message = isset($_GET['added']) ? 'Project added successfully!' : '';
$error = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!$pdo) {
        $error = 'Database connection error.';
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            if ($stmt->execute([$_GET['delete']])) {
                $message = 'Project deleted successfully.';
            }
        } catch (PDOException $e) {
            $error = 'Error deleting project.';
            error_log('Delete project error: ' . $e->getMessage());
        }
    }
}

// Get all projects
$projects = [];
if ($pdo) {
    try {
        $projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll() ?? [];
    } catch (PDOException $e) {
        $error = 'Error loading projects.';
        error_log('Load projects error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Oxionex Admin</title>
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
            background: #0f0f23;
            color: #fff;
        }
        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: #1a1a3e;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 30px 20px;
        }
        .sidebar-brand {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
        }
        .sidebar-menu {
            list-style: none;
        }
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            color: #a0aec0;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(0, 212, 255, 0.1);
            color: #00d4ff;
        }
        .main-content {
            padding: 30px;
            overflow-y: auto;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-add {
            background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%);
            border: none;
            color: #0f0f23;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
        }
        .data-table {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }
        .data-table table {
            width: 100%;
            margin: 0;
        }
        .data-table th {
            background: rgba(0, 212, 255, 0.1);
            padding: 15px;
            font-weight: 600;
            text-align: left;
            border: none;
            color: #00d4ff;
        }
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .btn-action {
            padding: 6px 12px;
            margin-right: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-edit {
            background: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
        }
        .btn-delete {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-diamond"></i> Oxionex
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="projects.php" class="active"><i class="fas fa-images"></i> Projects</a></li>
                <li><a href="inquiries.php"><i class="fas fa-envelope"></i> Inquiries</a></li>
                <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li style="margin-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;">
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <h1 style="margin: 0; font-size: 28px; font-weight: 700;">Projects</h1>
                <a href="add_project.php" class="btn-add"><i class="fas fa-plus"></i> Add Project</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(substr($project['title'], 0, 30)); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($project['category'])); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($project['status'])); ?></td>
                            <td><?php echo $project['featured'] ? '✓' : '-'; ?></td>
                            <td><?php echo date('M d, Y', strtotime($project['created_at'])); ?></td>
                            <td>
                                <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn-action btn-edit">Edit</a>
                                <a href="?delete=<?php echo $project['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
