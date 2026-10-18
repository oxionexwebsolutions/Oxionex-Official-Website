<?php
require_once '../includes/form-security.php';
oxionex_start_secure_session();
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
$editingTestimonial = null;

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'added') {
        $message = 'Testimonial added successfully.';
    } elseif ($_GET['success'] === 'updated') {
        $message = 'Testimonial updated successfully.';
    } elseif ($_GET['success'] === 'deleted') {
        $message = 'Testimonial deleted successfully.';
    }
}

if (!$pdo) {
    $error = 'Database connection error.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    require_admin_csrf();
    $action = $_POST['action'] ?? 'save';

    if ($action === 'delete') {
        $deleteId = (int) ($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare('DELETE FROM testimonials WHERE id = ?');
            $stmt->execute([$deleteId]);
            header('Location: testimonials.php?success=deleted');
            exit;
        } catch (PDOException $exception) {
            $error = 'Unable to delete testimonial.';
            error_log('Delete testimonial error: ' . $exception->getMessage());
        }
    } else {
        $testimonialId = (int) ($_POST['id'] ?? 0);
        $clientName = trim($_POST['client_name'] ?? '');
        $clientRole = trim($_POST['client_role'] ?? '');
        $clientCompany = trim($_POST['client_company'] ?? '');
        $clientImage = trim($_POST['client_image'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 5);
        $messageText = trim($_POST['message'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($clientName === '' || $messageText === '') {
            $error = 'Client name and testimonial message are required.';
        } else {
            try {
                if ($testimonialId > 0) {
                    $stmt = $pdo->prepare('UPDATE testimonials SET client_name = ?, client_role = ?, client_company = ?, client_image = ?, rating = ?, message = ?, is_active = ? WHERE id = ?');
                    $stmt->execute([$clientName, $clientRole, $clientCompany, $clientImage, $rating, $messageText, $isActive, $testimonialId]);
                    header('Location: testimonials.php?success=updated');
                    exit;
                }

                $stmt = $pdo->prepare('INSERT INTO testimonials (client_name, client_role, client_company, client_image, rating, message, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
                $stmt->execute([$clientName, $clientRole, $clientCompany, $clientImage, $rating, $messageText, $isActive]);
                header('Location: testimonials.php?success=added');
                exit;
            } catch (PDOException $exception) {
                $error = 'Unable to save testimonial.';
                error_log('Save testimonial error: ' . $exception->getMessage());
            }
        }
    }
}

$testimonials = [];
if ($pdo) {
    try {
        $testimonials = $pdo->query('SELECT * FROM testimonials ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        $tableWarning = 'The testimonials table is missing or not accessible yet. Import the updated database.sql file.';
        error_log('Load testimonials error: ' . $exception->getMessage());
    }
}

if (isset($_GET['edit_id']) && $pdo) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM testimonials WHERE id = ?');
        $stmt->execute([(int) $_GET['edit_id']]);
        $editingTestimonial = $stmt->fetch();
    } catch (PDOException $exception) {
        $error = 'Unable to load testimonial.';
        error_log('Load testimonial error: ' . $exception->getMessage());
    }
}

$formValues = [
    'id' => $editingTestimonial['id'] ?? 0,
    'client_name' => $editingTestimonial['client_name'] ?? ($_POST['client_name'] ?? ''),
    'client_role' => $editingTestimonial['client_role'] ?? ($_POST['client_role'] ?? ''),
    'client_company' => $editingTestimonial['client_company'] ?? ($_POST['client_company'] ?? ''),
    'client_image' => $editingTestimonial['client_image'] ?? ($_POST['client_image'] ?? ''),
    'rating' => $editingTestimonial['rating'] ?? ($_POST['rating'] ?? 5),
    'message' => $editingTestimonial['message'] ?? ($_POST['message'] ?? ''),
    'is_active' => isset($editingTestimonial['is_active']) ? (int) $editingTestimonial['is_active'] : (int) (($_POST['is_active'] ?? 1) ? 1 : 0),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials - Oxionex Admin</title>
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
        .panel-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; }
        .panel-header h1, .panel-header h2 { margin: 0; }
        .page-note { color: #94a3b8; margin: 0.35rem 0 0; max-width: 60ch; }
        .form-card, .table-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 18px; padding: 24px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-grid .full-width { grid-column: 1 / -1; }
        .form-group { margin-bottom: 18px; }
        .helper-text { display: block; margin-top: 0.45rem; color: #94a3b8; font-size: 0.85rem; }
        label { display: block; margin-bottom: 8px; color: #00d4ff; font-weight: 600; }
        input, textarea, select { width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #fff; font-family: 'Inter', sans-serif; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #00d4ff; box-shadow: 0 0 10px rgba(0, 212, 255, 0.3); }
        .btn-submit { background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); border: none; color: #0f0f23; padding: 12px 22px; border-radius: 8px; cursor: pointer; font-weight: 700; }
        .alert { border-radius: 10px; border: none; margin-bottom: 20px; }
        .alert-success { background: rgba(16, 185, 129, 0.1); color: #6ee7b7; }
        .alert-danger { background: rgba(239, 68, 68, 0.1); color: #fca5a5; }
        .alert-warning { background: rgba(245, 158, 11, 0.1); color: #fcd34d; }
        .table { color: #fff; }
        .table thead th { color: #a0aec0; border-color: rgba(255, 255, 255, 0.1); }
        .table td { border-color: rgba(255, 255, 255, 0.08); vertical-align: top; }
        .testimonial-image { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(0, 212, 255, 0.35); }
        .badge-status { display: inline-flex; align-items: center; padding: 0.35rem 0.65rem; border-radius: 999px; font-size: 0.85rem; font-weight: 700; }
        .badge-on { background: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
        .badge-off { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }
        .stars { color: #fbbf24; }
        .testimonial-quote { color: #a0aec0; font-style: italic; background: rgba(255,255,255,0.03); border-left: 3px solid rgba(0, 212, 255, 0.4); padding: 0.75rem 1rem; border-radius: 0 12px 12px 0; }
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
                <li><a href="testimonials.php" class="active"><i class="fas fa-star"></i> Testimonials</a></li>
                <li><a href="setup.php"><i class="fas fa-users-cog"></i> Manage Admins</a></li>
                <li style="margin-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;"><?php echo admin_logout_form(); ?></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="panel-header flex-wrap mb-4">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 800;">Testimonials Management</h1>
                    <p class="page-note">Publish client testimonials that appear on the homepage slider.</p>
                </div>
            </div>

            <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if ($tableWarning): ?><div class="alert alert-warning"><?php echo htmlspecialchars($tableWarning); ?></div><?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="form-card">
                        <div class="panel-header" style="margin-bottom: 1rem;">
                            <div>
                                <h2 style="font-size: 1.35rem;"><?php echo $formValues['id'] ? 'Edit Testimonial' : 'Add Testimonial'; ?></h2>
                                <span class="helper-text">Use this form to publish testimonials in the homepage slider.</span>
                            </div>
                        </div>
                        <form method="post">
                            <?php echo admin_csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int) $formValues['id']; ?>">
                            <input type="hidden" name="action" value="save">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="client_name">Client Name</label>
                                    <input type="text" id="client_name" name="client_name" required value="<?php echo htmlspecialchars($formValues['client_name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="rating">Rating</label>
                                    <select id="rating" name="rating">
                                        <?php for ($rating = 5; $rating >= 1; $rating--): ?>
                                            <option value="<?php echo $rating; ?>" <?php echo ((int) $formValues['rating'] === $rating) ? 'selected' : ''; ?>><?php echo $rating; ?> Stars</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="client_role">Client Role</label>
                                    <input type="text" id="client_role" name="client_role" value="<?php echo htmlspecialchars($formValues['client_role']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="client_company">Client Company</label>
                                    <input type="text" id="client_company" name="client_company" value="<?php echo htmlspecialchars($formValues['client_company']); ?>">
                                </div>
                                <div class="form-group full-width">
                                    <label for="client_image">Client Image URL</label>
                                    <input type="url" id="client_image" name="client_image" value="<?php echo htmlspecialchars($formValues['client_image']); ?>">
                                    <span class="helper-text">Optional. Use a direct image URL or leave empty for a generic avatar.</span>
                                </div>
                                <div class="form-group full-width">
                                    <label for="message">Testimonial Message</label>
                                    <textarea id="message" name="message" rows="7" required><?php echo htmlspecialchars($formValues['message']); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo $formValues['is_active'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active" style="color: #fff;">Active</label>
                                </div>
                            </div>
                            <button type="submit" class="btn-submit w-100"><?php echo $formValues['id'] ? 'Update Testimonial' : 'Add Testimonial'; ?></button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="table-card">
                        <div class="panel-header" style="margin-bottom: 1rem;">
                            <div>
                                <h2 style="font-size: 1.35rem;">Current Testimonials</h2>
                                <span class="helper-text">Edit a testimonial to load it back into the form.</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($testimonials): ?>
                                        <?php foreach ($testimonials as $testimonial): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <?php if (!empty($testimonial['client_image'])): ?>
                                                            <img src="<?php echo htmlspecialchars($testimonial['client_image']); ?>" alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>" class="testimonial-image">
                                                        <?php else: ?>
                                                            <div class="testimonial-image d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.08); color: #a0aec0;"><i class="fas fa-user"></i></div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($testimonial['client_name']); ?></strong><br>
                                                            <small style="color: #a0aec0;"><?php echo htmlspecialchars(trim(($testimonial['client_role'] ?? '') . ($testimonial['client_company'] ? ' at ' . $testimonial['client_company'] : ''))); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="stars">
                                                        <?php for ($i = 0; $i < (int) $testimonial['rating']; $i++): ?>
                                                            <i class="fas fa-star"></i>
                                                        <?php endfor; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge-status <?php echo $testimonial['is_active'] ? 'badge-on' : 'badge-off'; ?>">
                                                        <?php echo $testimonial['is_active'] ? 'Active' : 'Hidden'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="testimonials.php?edit_id=<?php echo (int) $testimonial['id']; ?>" class="btn btn-sm btn-outline-primary mb-2">Edit</a>
                                                    <form method="post" onsubmit="return confirm('Delete this testimonial?');" style="display:inline;">
                                                        <?php echo admin_csrf_field(); ?>
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo (int) $testimonial['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger mb-2">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="border-top: none; padding-top: 0;">
                                                    <div class="testimonial-quote">"<?php echo htmlspecialchars($testimonial['message']); ?>"</div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4" style="color: #a0aec0;">No testimonials found yet. Add the first one using the form.</td>
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
