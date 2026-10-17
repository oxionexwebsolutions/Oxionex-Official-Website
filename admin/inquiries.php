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

$inquiries = [];
if ($pdo) {
    try {
        $inquiries = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 50")->fetchAll() ?? [];
    } catch (PDOException $e) {
        error_log('Load inquiries error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries - Oxionex Admin</title>
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
        .data-table { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; overflow: hidden; }
        .data-table table { width: 100%; margin: 0; }
        .data-table th { background: rgba(0, 212, 255, 0.1); padding: 15px; font-weight: 600; text-align: left; border: none; color: #00d4ff; }
        .data-table td { padding: 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .badge-new { background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%); color: #0f0f23; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: 600; }
        .btn-view { background: rgba(0, 212, 255, 0.1); color: #00d4ff; border: 1px solid rgba(0, 212, 255, 0.3); padding: 8px 15px; border-radius: 8px; cursor: pointer; font-size: 12px; border: none; }
        .btn-view:hover { background: rgba(0, 212, 255, 0.2); }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-content { background: #1a1a3e; border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 15px; padding: 30px; width: 90%; max-width: 860px; max-height: 85vh; overflow-y: auto; position: relative; }
        .modal-close { position: absolute; top: 15px; right: 15px; background: none; border: none; color: #a0aec0; font-size: 24px; cursor: pointer; }
        .modal-close:hover { color: #00d4ff; }
        .modal-field { margin-bottom: 20px; }
        .modal-field strong { color: #00d4ff; display: block; margin-bottom: 5px; }
        .modal-field p { margin: 0; color: #a0aec0; word-break: break-word; }
        .detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .detail-card { background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px; padding: 16px; }
        .detail-card.full-width { grid-column: 1 / -1; }
        .detail-label { color: #00d4ff; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem; }
        .detail-value { color: #a0aec0; margin: 0; word-break: break-word; white-space: pre-wrap; }
        .raw-block { white-space: pre-wrap; word-break: break-word; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 14px; color: #dbeafe; font-size: 0.88rem; }
        .section-title { color: #fff; margin: 24px 0 12px; font-size: 1.05rem; }
        .empty-value { color: #64748b; font-style: italic; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-brand"><i class="fas fa-diamond"></i> Oxionex</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="projects.php"><i class="fas fa-images"></i> Projects</a></li>
                <li><a href="inquiries.php" class="active"><i class="fas fa-envelope"></i> Inquiries</a></li>
                <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li style="margin-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;"><?php echo admin_logout_form(); ?></li>
            </ul>
        </div>
        <div class="main-content">
            <h1 style="margin-bottom: 30px; font-size: 28px; font-weight: 700;">Client Inquiries</h1>
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Business Type</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['phone'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['business_type'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['budget'] ?? '-'); ?></td>
                            <td><?php if ($inquiry['status'] === 'new'): ?><span class="badge-new">New</span><?php else: ?><?php echo ucfirst(htmlspecialchars($inquiry['status'])); ?><?php endif; ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($inquiry['created_at'])); ?></td>
                            <td>
                                <button class="btn-view" onclick='viewInquiry(<?php echo json_encode($inquiry, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing Inquiry Details -->
    <div id="inquiryModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeInquiry()"><i class="fas fa-times"></i></button>
            <h2 style="margin-bottom: 25px; color: #00d4ff;">Inquiry Details</h2>
            <div id="inquiryDetails"></div>
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

        function formatInquiryValue(value) {
            if (value === null || value === undefined || value === '') {
                return '<span class="empty-value">Not provided</span>';
            }
            return escapeHtml(value).replace(/\n/g, '<br>');
        }

        function viewInquiry(inquiry) {
            const fieldMap = [
                ['name', 'Full Name'],
                ['email', 'Email Address'],
                ['phone', 'Phone Number'],
                ['business_type', 'Business Type'],
                ['budget', 'Budget Range'],
                ['message', 'Project Message'],
                ['status', 'Status'],
                ['created_at', 'Submitted At']
            ];

            let detailsHTML = '<div class="detail-grid">';
            fieldMap.forEach(([key, label]) => {
                const isFullWidth = key === 'message';
                detailsHTML += `
                    <div class="detail-card ${isFullWidth ? 'full-width' : ''}">
                        <div class="detail-label">${label}</div>
                        <div class="detail-value">${formatInquiryValue(inquiry[key])}</div>
                    </div>
                `;
            });
            detailsHTML += `</div>`;

            detailsHTML += `
                <div class="section-title">Raw Submission</div>
                <div class="raw-block">${escapeHtml(JSON.stringify(inquiry, null, 2))}</div>
            `;
            document.getElementById('inquiryDetails').innerHTML = detailsHTML;
            document.getElementById('inquiryModal').classList.add('active');
        }
        
        function closeInquiry() {
            document.getElementById('inquiryModal').classList.remove('active');
        }
        
        document.getElementById('inquiryModal').addEventListener('click', function(e) {
            if (e.target === this) closeInquiry();
        });
    </script>
</body>
</html>
