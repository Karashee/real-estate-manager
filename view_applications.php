<?php
session_start();
require 'db.php';

// Ensure only tenants can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$tenant_id = $_SESSION['user']['id'];

// Fetch applications with property title
$stmt = $pdo->prepare("
    SELECT a.*, p.title AS property_title 
    FROM applications a
    JOIN properties p ON a.property_id = p.id
    WHERE a.tenant_id = :tenant_id
    ORDER BY a.created_at DESC
");
$stmt->execute(['tenant_id' => $tenant_id]);
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications | Prime Estate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #1abc9c;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: var(--dark);
        }
        
        .application-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 15px;
        }
        
        .application-header {
            background: linear-gradient(to right, var(--accent), #16a085);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .application-card {
            background: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .application-body {
            padding: 2rem;
        }
        
        .application-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .application-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background-color: rgba(26, 188, 156, 0.1);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .property-title {
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-pending {
            background: rgba(241, 196, 15, 0.15);
            color: #f1c40f;
            border: 1px solid #f1c40f;
        }
        
        .badge-approved {
            background: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            border: 1px solid #2ecc71;
        }
        
        .badge-rejected {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            margin-bottom: 1rem;
            color: #495057;
        }
        
        .document-btn {
            background: rgba(26, 188, 156, 0.1);
            color: var(--accent);
            border: 1px solid var(--accent);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .document-btn:hover {
            background: var(--accent);
            color: white;
            text-decoration: none;
        }
        
        .application-meta {
            display: flex;
            border-top: 1px solid #eee;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            margin-right: 1.5rem;
        }
        
        .meta-icon {
            width: 32px;
            height: 32px;
            background: rgba(26, 188, 156, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: var(--accent);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1.5rem;
        }
        
        .btn-back {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: var(--dark);
            color: white;
            text-decoration: none;
            transform: translateX(-5px);
        }
        
        .application-actions {
            display: flex;
            gap: 10px;
            margin-top: 1rem;
        }
        
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .action-btn i {
            margin-right: 8px;
        }
        
        
    </style>
</head>
<body>
    <!-- Application Header -->
    <div class="application-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="tenant_dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
            <h2 class="mb-0"><i class="fas fa-file-alt me-2"></i>Your Rental Applications</h2>
        </div>
        
        <div class="application-card">
            <div class="application-header">
                <h3 class="mb-0"><i class="fas fa-list-check me-2"></i>Application History</h3>
                <span class="badge bg-light text-dark"><?= count($applications) ?> application(s)</span>
            </div>
            
            <div class="application-body">
                <?php if ($applications): ?>
                    <div class="row">
                        <?php foreach ($applications as $app): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="application-card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="property-title"><?= htmlspecialchars($app['property_title']) ?></div>
                                        <?php
                                            $badgeClass = 'badge-pending';
                                            if (strtolower($app['status']) == 'approved') $badgeClass = 'badge-approved';
                                            if (strtolower($app['status']) == 'rejected') $badgeClass = 'badge-rejected';
                                        ?>
                                        <span class="status-badge <?= $badgeClass ?>"><?= $app['status'] ?></span>
                                    </div>
                                    
                                    <div class="card-body">
                                        <div class="detail-label">Your Message</div>
                                        <div class="detail-value">
                                            <?= $app['message'] ? nl2br(htmlspecialchars($app['message'])) : '<span class="text-muted">No message provided</span>' ?>
                                        </div>
                                        
                                        <div class="detail-label">Supporting Document</div>
                                        <div class="detail-value">
                                            <?php if ($app['document_path']): ?>
                                                <a href="<?= $app['document_path'] ?>" target="_blank" class="document-btn">
                                                    <i class="fas fa-file-pdf me-2"></i>View Document
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">No document uploaded</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="application-meta">
                                            <div class="meta-item">
                                                <div class="meta-icon">
                                                    <i class="fas fa-calendar"></i>
                                                </div>
                                                <div>
                                                    <div class="detail-label">Submitted</div>
                                                    <div class="detail-value"><?= date('M d, Y', strtotime($app['created_at'])) ?></div>
                                                </div>
                                            </div>
                                            
                                            <div class="meta-item">
                                                <div class="meta-icon">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div>
                                                    <div class="detail-label">Last Updated</div>
                                                    <div class="detail-value"><?= date('M d, Y', strtotime($app['created_at'])) ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="application-actions">
                                            
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-file-circle-question"></i>
                        </div>
                        <h3 class="mb-3">No Applications Found</h3>
                        <p class="text-muted mb-4">You haven't submitted any rental applications yet. Start your journey to find your perfect home.</p>
                        <a href="search_properties.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Browse Properties
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="application-card mt-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>Application Status Guide</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="d-flex align-items-start">
                            <span class="status-badge badge-pending me-3">Pending</span>
                            <div>
                                <h5>Under Review</h5>
                                <p class="text-muted mb-0">Your application is being reviewed by the property manager.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="d-flex align-items-start">
                            <span class="status-badge badge-approved me-3">Approved</span>
                            <div>
                                <h5>Application Accepted</h5>
                                <p class="text-muted mb-0">Congratulations! Your application has been approved.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <span class="status-badge badge-rejected me-3">Rejected</span>
                            <div>
                                <h5>Not Selected</h5>
                                <p class="text-muted mb-0">This application wasn't approved for this property.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple animation for application cards
        document.querySelectorAll('.application-card').forEach((card, index) => {
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            
            setTimeout(() => {
                card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }, 100);
        });
        
        // Withdraw application confirmation
        document.querySelectorAll('.btn-withdraw').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm("Are you sure you want to withdraw this application? This action cannot be undone.")) {
                    // In a real application, this would trigger an AJAX request
                    alert("Application withdrawal would be processed here.");
                }
            });
        });
        
        // Console message
        console.log("Prime Estate Tenant Application System");
        console.log("PHP functionality preserved - application data loaded successfully");
    </script>
</body>
</html>