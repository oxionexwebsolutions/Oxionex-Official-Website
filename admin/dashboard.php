<?php
/**
 * Oxionex Web Solutions - Admin Dashboard
 * Manage projects, inquiries, and website content
 */

require_once '../includes/form-security.php';
oxionex_start_secure_session();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
require_once 'functions.php';

// Verify session and update activity
verify_admin_session();

// Get statistics
$projects_count = 0;
$inquiries_count = 0;
$total_inquiries = 0;
$submissions_count = 0;
$recent_inquiries = [];
$projects = [];

if ($pdo) {
    try {
        $projects_count = $pdo->query("SELECT COUNT(*) as count FROM projects")->fetch()['count'] ?? 0;
        $inquiries_count = $pdo->query("SELECT COUNT(*) as count FROM contacts WHERE status = 'new'")->fetch()['count'] ?? 0;
        $total_inquiries = $pdo->query("SELECT COUNT(*) as count FROM contacts")->fetch()['count'] ?? 0;
        $submissions_count = $pdo->query("SELECT COUNT(*) as count FROM project_submissions WHERE status = 'new'")->fetch()['count'] ?? 0;
        
        // Get recent inquiries
        $recent_inquiries = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll() ?? [];
        
        // Get projects
        $projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 10")->fetchAll() ?? [];
    } catch (PDOException $e) {
        error_log('Dashboard query error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oxionex Admin - Dashboard</title>
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
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .sidebar-brand-squares {
            width: 30px;
            height: 30px;
            position: relative;
        }

        .sidebar-brand-squares div {
            position: absolute;
            background: #00d4ff;
        }

        .sidebar-brand-squares .square-1 {
            width: 8px;
            height: 8px;
            top: 0;
            left: 0;
        }

        .sidebar-brand-squares .square-2 {
            width: 6px;
            height: 6px;
            top: 5px;
            left: 5px;
        }

        .sidebar-brand-squares .square-3 {
            width: 4px;
            height: 4px;
            top: 10px;
            left: 10px;
        }

        .sidebar-brand-squares .square-4 {
            width: 2px;
            height: 2px;
            top: 15px;
            left: 15px;
        }

        .sidebar-brand-text {
            font-size: 18px;
            font-weight: 700;
            color: #00d4ff;
            letter-spacing: 0.5px;
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
        .top-bar-title h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logout-btn {
            background: linear-gradient(135deg, #ff006e 0%, #ff6b9d 100%);
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .logout-btn:hover {
            opacity: 0.8;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
        }
        .stat-icon {
            font-size: 32px;
            background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
        }
        .stat-label {
            color: #a0aec0;
            font-size: 14px;
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
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
            color: #a0aec0;
        }
        .badge-new {
            background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%);
            color: #0f0f23;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-status {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-squares">
                    <div class="square-1"></div>
                    <div class="square-2"></div>
                    <div class="square-3"></div>
                    <div class="square-4"></div>
                </div>
                <div class="sidebar-brand-text">OXIONEX</div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="projects.php"><i class="fas fa-images"></i> Projects</a></li>
                <li><a href="inquiries.php"><i class="fas fa-envelope"></i> Inquiries</a></li>
                <li><a href="project-submissions.php"><i class="fas fa-clipboard-list"></i> Project Submissions</a></li>
                <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li><a href="setup.php"><i class="fas fa-users-cog"></i> Manage Admins</a></li>
                <li style="margin-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;">
                    <?php echo admin_logout_form(); ?>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <div class="top-bar-title">
                    <h1>Dashboard</h1>
                </div>
                <div class="user-info">
                    <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
                    <a href="/" class="logout-btn" style="background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); color: #0f0f23;">Back to Website</a>
                    <?php echo admin_logout_form('logout-btn'); ?>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stat-value"><?php echo $projects_count; ?></div>
                    <div class="stat-label">Total Projects</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-value"><?php echo $inquiries_count; ?></div>
                    <div class="stat-label">New Inquiries</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_inquiries; ?></div>
                    <div class="stat-label">Total Inquiries</div>
                </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-value"><?php echo $submissions_count; ?></div>
                        <div class="stat-label">New Project Submissions</div>
                    </div>
            </div>

            <!-- Recent Inquiries -->
            <div style="margin-bottom: 40px;">
                <div class="section-title">Recent Inquiries</div>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_inquiries as $inquiry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                <td>
                                    <?php if ($inquiry['status'] === 'new'): ?>
                                        <span class="badge-new">New</span>
                                    <?php else: ?>
                                        <span class="badge-status"><?php echo ucfirst(htmlspecialchars($inquiry['status'])); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Projects -->
            <div>
                <div class="section-title">Recent Projects</div>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($project['title']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($project['category'])); ?></td>
                                <td>
                                    <span class="badge-status"><?php echo ucfirst(htmlspecialchars($project['status'])); ?></span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($project['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
