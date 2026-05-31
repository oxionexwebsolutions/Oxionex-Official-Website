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

$submissions = [];
$total_submissions = 0;
$new_submissions = 0;

if ($pdo) {
    try {
        // Get total submissions
        $total_submissions = $pdo->query("SELECT COUNT(*) as count FROM project_submissions")->fetch()['count'] ?? 0;
        
        // Get new submissions
        $new_submissions = $pdo->query("SELECT COUNT(*) as count FROM project_submissions WHERE status = 'new'")->fetch()['count'] ?? 0;
        
        // Get all submissions ordered by newest first
        $submissions = $pdo->query("SELECT * FROM project_submissions ORDER BY created_at DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC) ?? [];
    } catch (PDOException $e) {
        error_log('Load submissions error: ' . $e->getMessage());
    }
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id']) && isset($_POST['action'])) {
    $submission_id = intval($_POST['submission_id']);
    $action = $_POST['action'];
    
    if ($action === 'mark_reviewed') {
        try {
            $pdo->prepare("UPDATE project_submissions SET status = 'reviewed' WHERE id = ?")->execute([$submission_id]);
        } catch (PDOException $e) {
            error_log('Update submission error: ' . $e->getMessage());
        }
    } elseif ($action === 'mark_new') {
        try {
            $pdo->prepare("UPDATE project_submissions SET status = 'new' WHERE id = ?")->execute([$submission_id]);
        } catch (PDOException $e) {
            error_log('Update submission error: ' . $e->getMessage());
        }
    } elseif ($action === 'delete') {
        try {
            $pdo->prepare("DELETE FROM project_submissions WHERE id = ?")->execute([$submission_id]);
        } catch (PDOException $e) {
            error_log('Delete submission error: ' . $e->getMessage());
        }
    }
    
    // Reload submissions
    $submissions = $pdo->query("SELECT * FROM project_submissions ORDER BY created_at DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC) ?? [];
    $new_submissions = $pdo->query("SELECT COUNT(*) as count FROM project_submissions WHERE status = 'new'")->fetch()['count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Submissions - Pixelara Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0f0f23; color: #fff; }
        .dashboard-container { display: grid; grid-template-columns: 250px 1fr; min-height: 100vh; }
        .sidebar { background: #1a1a3e; border-right: 1px solid rgba(255, 255, 255, 0.1); padding: 30px 20px; }
        .sidebar-brand { font-size: 20px; font-weight: 700; background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 30px; }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 10px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 10px; padding: 12px 15px; color: #a0aec0; text-decoration: none; border-radius: 10px; transition: all 0.3s ease; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(0, 212, 255, 0.1); color: #00d4ff; }
        .main-content { padding: 30px; overflow-y: auto; }
        .stat-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 20px; margin-bottom: 30px; display: inline-block; margin-right: 20px; }
        .stat-number { font-size: 32px; font-weight: 700; background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .stat-label { color: #a0aec0; font-size: 14px; margin-top: 5px; }
        .data-table { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; overflow: hidden; }
        .data-table table { width: 100%; margin: 0; }
        .data-table th { background: rgba(0, 212, 255, 0.1); padding: 15px; font-weight: 600; text-align: left; border: none; color: #00d4ff; }
        .data-table td { padding: 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .data-table tr:hover { background: rgba(0, 212, 255, 0.05); }
        .badge-new { background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); color: #0f0f23; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: 600; }
        .badge-reviewed { background: rgba(124, 58, 237, 0.2); color: #7c3aed; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: 600; }
        .btn-view { background: rgba(0, 212, 255, 0.1); color: #00d4ff; border: 1px solid rgba(0, 212, 255, 0.3); padding: 8px 15px; border-radius: 8px; cursor: pointer; font-size: 12px; transition: all 0.3s ease; }
        .btn-view:hover { background: rgba(0, 212, 255, 0.2); }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-content { background: #1a1a3e; border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 15px; padding: 30px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto; position: relative; }
        .modal-close { position: absolute; top: 15px; right: 15px; background: none; border: none; color: #a0aec0; font-size: 24px; cursor: pointer; }
        .modal-close:hover { color: #00d4ff; }
        .modal-field { margin-bottom: 20px; }
        .modal-field strong { color: #00d4ff; display: block; margin-bottom: 5px; }
        .modal-field p { margin: 0; color: #a0aec0; word-break: break-all; }
        .service-badge { display: inline-block; background: rgba(0, 212, 255, 0.1); color: #00d4ff; padding: 5px 10px; border-radius: 5px; margin-right: 5px; margin-bottom: 5px; font-size: 12px; }
        .action-buttons { display: flex; gap: 10px; margin-top: 20px; }
        .btn-action { padding: 8px 15px; border: none; border-radius: 8px; cursor: pointer; font-size: 12px; transition: all 0.3s ease; }
        .btn-reviewed { background: rgba(124, 58, 237, 0.2); color: #7c3aed; }
        .btn-reviewed:hover { background: rgba(124, 58, 237, 0.4); }
        .btn-delete { background: rgba(255, 0, 110, 0.2); color: #ff006e; }
        .btn-delete:hover { background: rgba(255, 0, 110, 0.4); }
        .page-title { font-size: 28px; font-weight: 700; margin-bottom: 30px; }
        .search-box { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 10px 15px; color: #fff; width: 300px; margin-bottom: 20px; }
        .search-box::placeholder { color: #666; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-cube"></i> PIXELARA
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="projects.php"><i class="fas fa-briefcase"></i> Projects</a></li>
                <li><a href="inquiries.php"><i class="fas fa-envelope"></i> Inquiries</a></li>
                <li><a href="project-submissions.php" class="active"><i class="fas fa-clipboard-list"></i> Project Submissions</a></li>
                <li><a href="services.php"><i class="fas fa-cogs"></i> Services</a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                    <a href="logout.php" style="color: #ff006e;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1 class="page-title">Project Submissions</h1>

            <!-- Statistics -->
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_submissions; ?></div>
                <div class="stat-label">Total Submissions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $new_submissions; ?></div>
                <div class="stat-label">New Submissions</div>
            </div>

            <!-- Submissions Table -->
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Email</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($submission['client_name']); ?></strong><br><small><?php echo htmlspecialchars($submission['company_name']); ?></small></td>
                            <td><?php echo htmlspecialchars($submission['client_email']); ?><br><small><?php echo htmlspecialchars($submission['client_phone']); ?></small></td>
                            <td>
                                <span class="service-badge"><?php echo htmlspecialchars($submission['main_service']); ?></span>
                                <?php if (!empty($submission['specific_service'])): ?>
                                <br><small style="color: #888;"><?php echo htmlspecialchars($submission['specific_service']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($submission['status'] === 'new'): ?>
                                <span class="badge-new">New</span>
                                <?php else: ?>
                                <span class="badge-reviewed">Reviewed</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?php echo date('M d, Y H:i', strtotime($submission['created_at'])); ?></small></td>
                            <td>
                                <button class="btn-view" onclick='viewSubmission(<?php echo $submission['id']; ?>, <?php echo json_encode($submission); ?>)'>
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (empty($submissions)): ?>
                <div style="padding: 40px; text-align: center; color: #a0aec0;">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
                    <p>No submissions yet</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing Submission Details -->
    <div id="submissionModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeSubmission()"><i class="fas fa-times"></i></button>
            <h2 style="margin-bottom: 25px; color: #00d4ff;">Submission Details</h2>
            
            <div id="submissionDetails"></div>
            
            <div class="action-buttons">
                <form method="POST" style="flex: 1;">
                    <input type="hidden" id="submissionId" name="submission_id">
                    <button type="submit" name="action" value="mark_reviewed" class="btn-action btn-reviewed" style="width: 100%;">
                        <i class="fas fa-check"></i> Mark as Reviewed
                    </button>
                </form>
                <form method="POST" style="flex: 1;">
                    <input type="hidden" id="submissionId2" name="submission_id">
                    <button type="submit" name="action" value="mark_new" class="btn-action btn-reviewed" style="width: 100%;">
                        <i class="fas fa-exclamation-circle"></i> Mark as New
                    </button>
                </form>
                <form method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                    <input type="hidden" id="submissionId3" name="submission_id">
                    <button type="submit" name="action" value="delete" class="btn-action btn-delete" style="width: 100%;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function viewSubmission(id, submission) {
            
            // Set form hidden inputs
            document.getElementById('submissionId').value = id;
            document.getElementById('submissionId2').value = id;
            document.getElementById('submissionId3').value = id;
            
            // Build details HTML
            let detailsHTML = `
                <div class="modal-field">
                    <strong>Client Information</strong>
                    <p><strong>Name:</strong> ${submission.client_name}</p>
                    <p><strong>Email:</strong> ${submission.client_email}</p>
                    <p><strong>Phone:</strong> ${submission.client_phone}</p>
                </div>
                
                <div class="modal-field">
                    <strong>Company Information</strong>
                    <p><strong>Company:</strong> ${submission.company_name}</p>
                    <p><strong>Website:</strong> ${submission.company_site || 'Not provided'}</p>
                    <p><strong>Location:</strong> ${submission.company_location || 'Not provided'}</p>
                </div>
                
                <div class="modal-field">
                    <strong>Project Details</strong>
                    <p><strong>Main Service:</strong> ${submission.main_service}</p>
                    <p><strong>Specific Service:</strong> ${submission.specific_service || 'Not selected'}</p>
                </div>
            `;
            
            // Add step 2 data if available
            if (submission.step2_data) {
                try {
                    const step2Data = JSON.parse(submission.step2_data);
                    detailsHTML += '<div class="modal-field"><strong>Project Requirements</strong>';
                    for (const [key, value] of Object.entries(step2Data)) {
                        if (value && value !== '') {
                            const keyLabel = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                            if (Array.isArray(value)) {
                                detailsHTML += `<p><strong>${keyLabel}:</strong> ${value.join(', ')}</p>`;
                            } else {
                                detailsHTML += `<p><strong>${keyLabel}:</strong> ${value}</p>`;
                            }
                        }
                    }
                    detailsHTML += '</div>';
                } catch (e) {
                    console.error('Error parsing step2 data:', e);
                }
            }
            
            // Add attachments if available
            if (submission.attachments) {
                try {
                    const attachments = JSON.parse(submission.attachments);
                    if (attachments && attachments.length > 0) {
                        detailsHTML += '<div class="modal-field"><strong>Attachments</strong>';
                        attachments.forEach(att => {
                            detailsHTML += `<p><i class="fas fa-file"></i> ${att.name}</p>`;
                        });
                        detailsHTML += '</div>';
                    }
                } catch (e) {
                    console.error('Error parsing attachments:', e);
                }
            }
            
            detailsHTML += `<div class="modal-field"><small style="color: #666;">Submitted: ${new Date(submission.created_at).toLocaleString()}</small></div>`;
            
            document.getElementById('submissionDetails').innerHTML = detailsHTML;
            document.getElementById('submissionModal').classList.add('active');
        }
        
        function closeSubmission() {
            document.getElementById('submissionModal').classList.remove('active');
        }
        
        // Close modal on background click
        document.getElementById('submissionModal').addEventListener('click', function(e) {
            if (e.target === this) closeSubmission();
        });
    </script>
</body>
</html>
