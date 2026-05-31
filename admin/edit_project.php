<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
require_once 'functions.php';

// Verify session
verify_admin_session();

$project_id = $_GET['id'] ?? 0;
$project = null;
$message = '';
$error = '';

// Fetch project
if (!$pdo) {
    $error = 'Database connection error.';
    $project = null;
} else {
    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch();
        
        if (!$project) {
            header('Location: projects.php');
            exit;
        }
    } catch (PDOException $e) {
        $error = 'Error loading project.';
        error_log('Load project error: ' . $e->getMessage());
        header('Location: projects.php');
        exit;
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $short_description = $_POST['short_description'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $technologies = $_POST['technologies'] ?? '';
    $status = $_POST['status'] ?? 'upcoming';
    $live_link = $_POST['live_link'] ?? '';
    $featured = isset($_POST['featured']) ? 1 : 0;
    $image = $_POST['image'] ?? '';

    if (!$title || !$short_description) {
        $error = 'Title and short description are required.';
    } else if (!$pdo) {
        $error = 'Database connection error.';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE projects 
                SET title = ?, short_description = ?, description = ?, image = ?, category = ?, technologies = ?, status = ?, live_link = ?, featured = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $short_description, $description, $image, $category, $technologies, $status, $live_link, $featured, $project_id]);
            $message = 'Project updated successfully!';
            // Refresh project data
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            $project = $stmt->fetch();
        } catch (PDOException $e) {
            $error = 'Error updating project. Please try again.';
            error_log('Update project error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - Pixelara Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f0f23; color: #fff; }
        .dashboard-container { display: grid; grid-template-columns: 250px 1fr; min-height: 100vh; }
        .sidebar { background: #1a1a3e; border-right: 1px solid rgba(255, 255, 255, 0.1); padding: 30px 20px; }
        .sidebar-brand { font-size: 24px; font-weight: 700; background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 30px; }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 10px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 10px; padding: 12px 15px; color: #a0aec0; text-decoration: none; border-radius: 10px; transition: all 0.3s ease; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(0, 212, 255, 0.1); color: #00d4ff; }
        .main-content { padding: 30px; overflow-y: auto; }
        .form-container { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 30px; max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #00d4ff; font-weight: 600; }
        input, textarea, select { width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #fff; font-family: 'Inter', sans-serif; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #00d4ff; box-shadow: 0 0 10px rgba(0, 212, 255, 0.3); }
        .btn-submit { background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); border: none; color: #0f0f23; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: 600; width: 100%; }
        .alert { border-radius: 10px; border: none; margin-bottom: 20px; }
        .alert-success { background: rgba(16, 185, 129, 0.1); color: #6ee7b7; }
        .alert-danger { background: rgba(239, 68, 68, 0.1); color: #fca5a5; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        input[type="checkbox"] { width: auto; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-brand"><i class="fas fa-diamond"></i> Pixelara</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="projects.php" class="active"><i class="fas fa-images"></i> Projects</a></li>
                <li><a href="inquiries.php"><i class="fas fa-envelope"></i> Inquiries</a></li>
                <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li style="margin-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1 style="margin-bottom: 30px; font-size: 28px; font-weight: 700;">Edit Project</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-container">
                <div class="form-group">
                    <label for="title">Project Title</label>
                    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($project['title']); ?>">
                </div>

                <div class="form-group">
                    <label for="short_description">Short Description</label>
                    <textarea id="short_description" name="short_description" rows="2" required><?php echo htmlspecialchars($project['short_description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="description">Full Description</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($project['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image URL</label>
                    <input type="url" id="image" name="image" value="<?php echo htmlspecialchars($project['image']); ?>">
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="web" <?php echo $project['category'] === 'web' ? 'selected' : ''; ?>>Web Design</option>
                        <option value="app" <?php echo $project['category'] === 'app' ? 'selected' : ''; ?>>App Development</option>
                        <option value="branding" <?php echo $project['category'] === 'branding' ? 'selected' : ''; ?>>Branding</option>
                        <option value="other" <?php echo $project['category'] === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="technologies">Technologies</label>
                    <input type="text" id="technologies" name="technologies" value="<?php echo htmlspecialchars($project['technologies']); ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="completed" <?php echo $project['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="upcoming" <?php echo $project['status'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                        <option value="in-progress" <?php echo $project['status'] === 'in-progress' ? 'selected' : ''; ?>>In Progress</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="live_link">Live Link</label>
                    <input type="url" id="live_link" name="live_link" value="<?php echo htmlspecialchars($project['live_link']); ?>">
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="featured" name="featured" <?php echo $project['featured'] ? 'checked' : ''; ?>>
                        <label for="featured" style="margin-bottom: 0;">Featured Project</label>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Update Project</button>
            </form>
        </div>
    </div>
</body>
</html>
