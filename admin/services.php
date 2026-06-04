<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
require_once 'functions.php';

verify_admin_session();

$message = '';
$error = '';
$tableWarning = '';
$editingService = null;

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'added') {
        $message = 'Service added successfully.';
    } elseif ($_GET['success'] === 'updated') {
        $message = 'Service updated successfully.';
    } elseif ($_GET['success'] === 'deleted') {
        $message = 'Service deleted successfully.';
    }
}

if (!$pdo) {
    $error = 'Database connection error.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $action = $_POST['action'] ?? 'save';

    if ($action === 'delete') {
        $deleteId = (int) ($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
            $stmt->execute([$deleteId]);
            header('Location: services.php?success=deleted');
            exit;
        } catch (PDOException $exception) {
            $error = 'Unable to delete service.';
            error_log('Delete service error: ' . $exception->getMessage());
        }
    } else {
        $serviceId = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-cube');
        $points = trim($_POST['points'] ?? '');
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($title === '' || $subtitle === '' || $points === '') {
            $error = 'Title, subtitle, and points are required.';
        } else {
            try {
                if ($serviceId > 0) {
                    $stmt = $pdo->prepare('UPDATE services SET title = ?, subtitle = ?, icon = ?, points = ?, sort_order = ?, is_active = ? WHERE id = ?');
                    $stmt->execute([$title, $subtitle, $icon, $points, $sortOrder, $isActive, $serviceId]);
                    header('Location: services.php?success=updated');
                    exit;
                }

                $stmt = $pdo->prepare('INSERT INTO services (title, subtitle, icon, points, sort_order, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
                $stmt->execute([$title, $subtitle, $icon, $points, $sortOrder, $isActive]);
                header('Location: services.php?success=added');
                exit;
            } catch (PDOException $exception) {
                $error = 'Unable to save service.';
                error_log('Save service error: ' . $exception->getMessage());
            }
        }
    }
}

$services = [];
if ($pdo) {
    try {
        $services = $pdo->query('SELECT * FROM services ORDER BY sort_order ASC, created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        $tableWarning = 'The services table is missing or not accessible yet. Import the updated database.sql file.';
        error_log('Load services error: ' . $exception->getMessage());
    }
}

if (isset($_GET['edit_id']) && $pdo) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute([(int) $_GET['edit_id']]);
        $editingService = $stmt->fetch();
    } catch (PDOException $exception) {
        $error = 'Unable to load service.';
        error_log('Load service error: ' . $exception->getMessage());
    }
}

$formValues = [
    'id' => $editingService['id'] ?? 0,
    'title' => $editingService['title'] ?? ($_POST['title'] ?? ''),
    'subtitle' => $editingService['subtitle'] ?? ($_POST['subtitle'] ?? ''),
    'icon' => $editingService['icon'] ?? ($_POST['icon'] ?? 'fas fa-cube'),
    'points' => $editingService['points'] ?? ($_POST['points'] ?? ''),
    'sort_order' => $editingService['sort_order'] ?? ($_POST['sort_order'] ?? 0),
    'is_active' => isset($editingService['is_active']) ? (int) $editingService['is_active'] : (int) (($_POST['is_active'] ?? 1) ? 1 : 0),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Oxionex Admin</title>
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
        .form-card, .table-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 18px; padding: 24px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 8px; color: #00d4ff; font-weight: 600; }
        input, textarea, select { width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #fff; font-family: 'Inter', sans-serif; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #00d4ff; box-shadow: 0 0 10px rgba(0, 212, 255, 0.3); }
        .btn-submit { background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); border: none; color: #0f0f23; padding: 12px 22px; border-radius: 8px; cursor: pointer; font-weight: 700; }
        .btn-outline-danger, .btn-outline-primary { border-width: 1px; }
        .alert { border-radius: 10px; border: none; margin-bottom: 20px; }
        .alert-success { background: rgba(16, 185, 129, 0.1); color: #6ee7b7; }
        .alert-danger { background: rgba(239, 68, 68, 0.1); color: #fca5a5; }
        .alert-warning { background: rgba(245, 158, 11, 0.1); color: #fcd34d; }
        .table { color: #fff; }
        .table thead th { color: #a0aec0; border-color: rgba(255, 255, 255, 0.1); }
        .table td { border-color: rgba(255, 255, 255, 0.08); vertical-align: top; }
        .points-list { padding-left: 1rem; margin-bottom: 0; color: #a0aec0; }
        .badge-status { display: inline-flex; align-items: center; padding: 0.35rem 0.65rem; border-radius: 999px; font-size: 0.85rem; font-weight: 700; }
        .badge-on { background: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
        .badge-off { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }
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
                <li><a href="services.php" class="active"><i class="fas fa-tools"></i> Services</a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li style="margin-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="mb-2" style="font-size: 2rem; font-weight: 800;">Services Management</h1>
                    <p style="color: #a0aec0; margin: 0;">Add, edit, and reorder the services shown on the homepage.</p>
                </div>
            </div>

            <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if ($tableWarning): ?><div class="alert alert-warning"><?php echo htmlspecialchars($tableWarning); ?></div><?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="form-card">
                        <h2 style="font-size: 1.35rem; margin-bottom: 20px;"><?php echo $formValues['id'] ? 'Edit Service' : 'Add Service'; ?></h2>
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo (int) $formValues['id']; ?>">
                            <input type="hidden" name="action" value="save">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($formValues['title']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="subtitle">Subtitle</label>
                                <input type="text" id="subtitle" name="subtitle" required value="<?php echo htmlspecialchars($formValues['subtitle']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="icon">Icon Class</label>
                                <input type="text" id="icon" name="icon" placeholder="fas fa-globe" value="<?php echo htmlspecialchars($formValues['icon']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="points">Points</label>
                                <textarea id="points" name="points" rows="8" required placeholder="One point per line"><?php echo htmlspecialchars($formValues['points']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="sort_order">Sort Order</label>
                                <input type="number" id="sort_order" name="sort_order" value="<?php echo (int) $formValues['sort_order']; ?>">
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo $formValues['is_active'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active" style="color: #fff;">Active</label>
                                </div>
                            </div>
                            <button type="submit" class="btn-submit w-100"><?php echo $formValues['id'] ? 'Update Service' : 'Add Service'; ?></button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="table-card">
                        <h2 style="font-size: 1.35rem; margin-bottom: 20px;">Current Services</h2>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Service</th>
                                        <th>Points</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($services): ?>
                                        <?php foreach ($services as $service): ?>
                                            <tr>
                                                <td><?php echo (int) $service['sort_order']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($service['title']); ?></strong><br>
                                                    <small style="color: #a0aec0;"><?php echo htmlspecialchars($service['subtitle']); ?></small>
                                                </td>
                                                <td>
                                                    <?php
                                                        $servicePoints = preg_split('/\R+/', trim($service['points'] ?? '')) ?: [];
                                                        $servicePoints = array_values(array_filter(array_map('trim', $servicePoints)));
                                                    ?>
                                                    <ul class="points-list">
                                                        <?php foreach (array_slice($servicePoints, 0, 4) as $point): ?>
                                                            <li><?php echo htmlspecialchars($point); ?></li>
                                                        <?php endforeach; ?>
                                                        <?php if (count($servicePoints) > 4): ?>
                                                            <li>+<?php echo count($servicePoints) - 4; ?> more</li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <span class="badge-status <?php echo $service['is_active'] ? 'badge-on' : 'badge-off'; ?>">
                                                        <?php echo $service['is_active'] ? 'Active' : 'Hidden'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="services.php?edit_id=<?php echo (int) $service['id']; ?>" class="btn btn-sm btn-outline-primary mb-2">Edit</a>
                                                    <form method="post" onsubmit="return confirm('Delete this service?');" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo (int) $service['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger mb-2">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4" style="color: #a0aec0;">No services found yet. Add the first one using the form.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
