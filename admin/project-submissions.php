<?php
require_once '../includes/form-security.php';
oxionex_start_secure_session();
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
    require_admin_csrf();
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
    <title>Project Submissions - Oxionex Admin</title>
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
        .modal-content { background: #1a1a3e; border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 15px; padding: 30px; width: 90%; max-width: 920px; max-height: 86vh; overflow-y: auto; position: relative; }
        .modal-close { position: absolute; top: 15px; right: 15px; background: none; border: none; color: #a0aec0; font-size: 24px; cursor: pointer; }
        .modal-close:hover { color: #00d4ff; }
        .modal-field { margin-bottom: 20px; }
        .modal-field strong { color: #00d4ff; display: block; margin-bottom: 5px; }
        .modal-field p { margin: 0; color: #a0aec0; word-break: break-all; }
        .detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .detail-card { background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px; padding: 16px; }
        .detail-card.full-width { grid-column: 1 / -1; }
        .detail-label { color: #00d4ff; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem; }
        .detail-value { color: #a0aec0; margin: 0; word-break: break-word; white-space: pre-wrap; }
        .raw-block { white-space: pre-wrap; word-break: break-word; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 14px; color: #dbeafe; font-size: 0.88rem; }
        .section-title { color: #fff; margin: 24px 0 12px; font-size: 1.05rem; }
        .empty-value { color: #64748b; font-style: italic; }
        .attachment-list { display: grid; gap: 0.65rem; }
        .attachment-item { display: flex; align-items: center; gap: 0.65rem; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 10px 12px; color: #a0aec0; }
        .attachment-item i { color: #00d4ff; }
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
                <i class="fas fa-cube"></i> OXIONEX
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="projects.php"><i class="fas fa-briefcase"></i> Projects</a></li>
                <li><a href="inquiries.php"><i class="fas fa-envelope"></i> Inquiries</a></li>
                <li><a href="project-submissions.php" class="active"><i class="fas fa-clipboard-list"></i> Project Submissions</a></li>
                <li><a href="services.php"><i class="fas fa-cogs"></i> Services</a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                    <?php echo admin_logout_form(); ?>
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
                                <button class="btn-view" onclick='viewSubmission(<?php echo $submission['id']; ?>, <?php echo json_encode($submission, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
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
                    <?php echo admin_csrf_field(); ?>
                    <input type="hidden" id="submissionId" name="submission_id">
                    <button type="submit" name="action" value="mark_reviewed" class="btn-action btn-reviewed" style="width: 100%;">
                        <i class="fas fa-check"></i> Mark as Reviewed
                    </button>
                </form>
                <form method="POST" style="flex: 1;">
                    <?php echo admin_csrf_field(); ?>
                    <input type="hidden" id="submissionId2" name="submission_id">
                    <button type="submit" name="action" value="mark_new" class="btn-action btn-reviewed" style="width: 100%;">
                        <i class="fas fa-exclamation-circle"></i> Mark as New
                    </button>
                </form>
                <form method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                    <?php echo admin_csrf_field(); ?>
                    <input type="hidden" id="submissionId3" name="submission_id">
                    <button type="submit" name="action" value="delete" class="btn-action btn-delete" style="width: 100%;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function formatValue(value) {
            if (value === null || value === undefined || value === '') {
                return '<span class="empty-value">Not provided</span>';
            }
            if (Array.isArray(value)) {
                return escapeHtml(value.join(', '));
            }
            return escapeHtml(value).replace(/\n/g, '<br>');
        }

        function buildSection(title, items, fullWidthKeys = []) {
            let html = `<div class="section-title">${escapeHtml(title)}</div><div class="detail-grid">`;
            items.forEach(([key, label]) => {
                const isFullWidth = fullWidthKeys.includes(key);
                html += `
                    <div class="detail-card ${isFullWidth ? 'full-width' : ''}">
                        <div class="detail-label">${escapeHtml(label)}</div>
                        <div class="detail-value">${formatValue(items.__source ? items.__source[key] : '')}</div>
                    </div>
                `;
            });
            html += '</div>';
            return html;
        }

        function getStep2Label(key) {
            const labels = {
                website_purpose: 'Website Purpose',
                num_pages: 'Estimated Pages',
                style_preference: 'Style / Theme',
                features_needed: 'Key Features Needed',
                budget: 'Budget',
                deadline: 'Expected Timeline',
                reference_sites: 'Reference Websites',
                colors: 'Preferred Colors',
                assets: 'Existing Branding Assets',
                current_site: 'Current Website URL',
                design_goals: 'Design Goals',
                platforms: 'Platforms Needed',
                business_niche: 'Business Niche / Industry',
                posting_frequency: 'Posting Frequency',
                goal: 'Primary Goal',
                existing_links: 'Existing Social Links',
                video_type: 'Video Type',
                duration: 'Duration',
                platform: 'Target Platform',
                style: 'Editing Style',
                references: 'Reference Links',
                footage: 'Raw Footage',
                marketing_goal: 'Marketing Goal',
                audience: 'Target Audience',
                monthly_budget: 'Monthly Ad Budget',
                timeline: 'Campaign Timeline',
                existing_accounts: 'Existing Accounts',
                design_type: 'Design Type',
                brand_name: 'Brand Name',
                brand_style: 'Brand Style',
                service_type: 'Technical Service Needed',
                existing_hosting: 'Current Hosting Status',
                problem_description: 'Problem Description / Requirements'
            };
            return labels[key] || key.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
        }

        function parseStep2Data(submission) {
            if (!submission.step2_data) return {};
            try {
                return typeof submission.step2_data === 'string' ? JSON.parse(submission.step2_data) : submission.step2_data;
            } catch (e) {
                console.error('Error parsing step2 data:', e);
                return { __raw: submission.step2_data };
            }
        }

        function viewSubmission(id, submission) {
            
            // Set form hidden inputs
            document.getElementById('submissionId').value = id;
            document.getElementById('submissionId2').value = id;
            document.getElementById('submissionId3').value = id;
            
            const step2Data = parseStep2Data(submission);
            const attachments = (() => {
                try {
                    return submission.attachments ? (typeof submission.attachments === 'string' ? JSON.parse(submission.attachments) : submission.attachments) : [];
                } catch (e) {
                    console.error('Error parsing attachments:', e);
                    return [];
                }
            })();

            let detailsHTML = `
                <div class="section-title">Client Information</div>
                <div class="detail-grid">
                    <div class="detail-card"><div class="detail-label">Full Name</div><div class="detail-value">${formatValue(submission.client_name)}</div></div>
                    <div class="detail-card"><div class="detail-label">Company Name</div><div class="detail-value">${formatValue(submission.company_name)}</div></div>
                    <div class="detail-card"><div class="detail-label">Email Address</div><div class="detail-value">${formatValue(submission.client_email)}</div></div>
                    <div class="detail-card"><div class="detail-label">Phone Number</div><div class="detail-value">${formatValue(submission.client_phone)}</div></div>
                    <div class="detail-card"><div class="detail-label">Company Website</div><div class="detail-value">${formatValue(submission.company_site)}</div></div>
                    <div class="detail-card"><div class="detail-label">Company Location</div><div class="detail-value">${formatValue(submission.company_location)}</div></div>
                </div>

                <div class="section-title">Project Request</div>
                <div class="detail-grid">
                    <div class="detail-card"><div class="detail-label">Main Service</div><div class="detail-value">${formatValue(submission.main_service)}</div></div>
                    <div class="detail-card"><div class="detail-label">Specific Service</div><div class="detail-value">${formatValue(submission.specific_service)}</div></div>
                    <div class="detail-card full-width"><div class="detail-label">Submitted At</div><div class="detail-value">${formatValue(submission.created_at ? new Date(submission.created_at).toLocaleString() : '')}</div></div>
                </div>

                <div class="section-title">Step 2 Answers</div>
                <div class="detail-grid">
            `;

            const step2Keys = Object.keys(step2Data).filter((key) => key !== '__raw');
            if (step2Keys.length) {
                step2Keys.forEach((key) => {
                    const value = step2Data[key];
                    const isArray = Array.isArray(value);
                    detailsHTML += `
                        <div class="detail-card ${key === 'problem_description' || key === 'website_purpose' || key === 'features_needed' || key === 'design_goals' || key === 'audience' ? 'full-width' : ''}">
                            <div class="detail-label">${escapeHtml(getStep2Label(key))}</div>
                            <div class="detail-value">${formatValue(isArray ? value : value)}</div>
                        </div>
                    `;
                });
            } else {
                detailsHTML += `<div class="detail-card full-width"><div class="detail-label">Step 2 Answers</div><div class="detail-value"><span class="empty-value">No additional project details were submitted.</span></div></div>`;
            }
            detailsHTML += `</div>`;

            detailsHTML += `
                <div class="section-title">Attachments</div>
                <div class="attachment-list">
            `;
            if (attachments && attachments.length) {
                attachments.forEach((att) => {
                    const attachmentName = typeof att === 'string' ? att : (att.name || att.file || 'Attachment');
                    detailsHTML += `<div class="attachment-item"><i class="fas fa-paperclip"></i><span>${escapeHtml(attachmentName)}</span></div>`;
                });
            } else {
                detailsHTML += `<div class="attachment-item"><i class="fas fa-info-circle"></i><span>No attachments uploaded.</span></div>`;
            }
            detailsHTML += `</div>`;

            detailsHTML += `
                <div class="section-title">Raw Submission</div>
                <div class="raw-block">${escapeHtml(JSON.stringify(submission, null, 2))}</div>
            `;

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
