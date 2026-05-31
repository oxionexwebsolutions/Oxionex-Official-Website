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
    <title>Inquiries - Pixelara Admin</title>
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
        .modal-content { background: #1a1a3e; border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 15px; padding: 30px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto; position: relative; }
        .modal-close { position: absolute; top: 15px; right: 15px; background: none; border: none; color: #a0aec0; font-size: 24px; cursor: pointer; }
        .modal-close:hover { color: #00d4ff; }
        .modal-field { margin-bottom: 20px; }
        .modal-field strong { color: #00d4ff; display: block; margin-bottom: 5px; }
        .modal-field p { margin: 0; color: #a0aec0; word-break: break-word; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-brand"><i class="fas fa-diamond"></i> Pixelara</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="projects.php"><i class="fas fa-images"></i> Projects</a></li>
                <li><a href="inquiries.php" class="active"><i class="fas fa-envelope"></i> Inquiries</a></li>
                <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li style="margin-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
                            <td><?php if ($inquiry['status'] === 'new'): ?><span class="badge-new">New</span><?php else: ?><?php echo ucfirst(htmlspecialchars($inquiry['status'])); ?><?php endif; ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($inquiry['created_at'])); ?></td>
                            <td>
                                <button class="btn-view" onclick='viewInquiry(<?php echo json_encode($inquiry); ?>)'>
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
        function viewInquiry(inquiry) {
            let detailsHTML = `
                <div class="modal-field">
                    <strong>Name</strong>
                    <p>${inquiry.name}</p>
                </div>
                <div class="modal-field">
                    <strong>Email</strong>
                    <p>${inquiry.email}</p>
                </div>
                <div class="modal-field">
                    <strong>Phone</strong>
                    <p>${inquiry.phone || 'Not provided'}</p>
                </div>
                <div class="modal-field">
                    <strong>Message</strong>
                    <p>${inquiry.message || 'No message provided'}</p>
                </div>
                <div class="modal-field">
                    <strong>Status</strong>
                    <p>${inquiry.status}</p>
                </div>
                <div class="modal-field">
                    <small style="color: #666;">Submitted: ${new Date(inquiry.created_at).toLocaleString()}</small>
                </div>
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
