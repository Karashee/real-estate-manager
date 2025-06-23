<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$manager_id = $_SESSION['user']['id'];

// Handle new lease creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_lease'])) {
    $tenant_id = $_POST['tenant_id'];
    $property_id = $_POST['property_id'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $rent = $_POST['monthly_rent'];

    $pdo->prepare("INSERT INTO leases (tenant_id, property_id, start_date, end_date, monthly_rent)
                   VALUES (?, ?, ?, ?, ?)")
        ->execute([$tenant_id, $property_id, $start, $end, $rent]);

    // Update property availability
    $pdo->prepare("UPDATE properties SET available = FALSE WHERE id = ?")->execute([$property_id]);
    // Notify tenant about lease approval
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$tenant_id, "Your lease application for property ID $property_id has been approved."]);
    // Notify manager about lease creation
    $stmt->execute([$manager_id, "Lease created for tenant ID $tenant_id on property ID $property_id."]);
    // Redirect to avoid resubmission

    
    // Mark application as Approved
    $pdo->prepare("UPDATE applications SET status = 'Approved' WHERE tenant_id = ? AND property_id = ?")
        ->execute([$tenant_id, $property_id]);
}

// Fetch applications pending approval
$apps = $pdo->query("
    SELECT a.*, u.name AS tenant_name, p.title AS property_title
    FROM applications a
    JOIN users u ON a.tenant_id = u.id
    JOIN properties p ON a.property_id = p.id
    WHERE a.status = 'Pending'
")->fetchAll();

// Fetch existing leases
$leases = $pdo->query("
    SELECT l.*, u.name AS tenant_name, p.title AS property_title
    FROM leases l
    JOIN users u ON l.tenant_id = u.id
    JOIN properties p ON l.property_id = p.id
    ORDER BY l.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leases | Prime Estate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #1abc9c;
            --light: #ecf0f1;
            --dark: #34495e;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: var(--dark);
            overflow-x: hidden;
            min-height: 100vh;
            padding-bottom: 4rem;
        }
        
        .navbar {
            background: linear-gradient(to right, var(--primary), var(--dark));
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
        }
        
        .hero-section {
            background: linear-gradient(rgba(44, 62, 80, 0.85), rgba(44, 62, 80, 0.9));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            width: 70%;
            height: 4px;
            background: var(--accent);
            bottom: -10px;
            left: 15%;
            border-radius: 2px;
        }
        
        .btn-primary {
            background-color: var(--secondary);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .btn-success {
            background-color: var(--accent);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background-color: #16a085;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .table-custom {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }
        
        .table-custom thead {
            background: linear-gradient(to right, var(--primary), var(--dark));
            color: white;
        }
        
        .table-custom th {
            font-weight: 600;
            padding: 1rem;
        }
        
        .table-custom td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .badge-success {
            background-color: var(--accent);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.25rem rgba(26, 188, 156, 0.25);
        }
        
        .application-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .application-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stats-banner {
            background: linear-gradient(to right, var(--secondary), var(--accent));
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .footer {
            background: linear-gradient(to right, var(--primary), var(--dark));
            color: white;
            padding: 2rem 0;
            margin-top: 4rem;
            border-radius: 20px 20px 0 0;
        }
        
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .back-to-top.show {
            opacity: 1;
        }
        
        .highlight {
            color: var(--accent);
            font-weight: 700;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 1.5rem 0;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-home me-2"></i>
                <span class="fw-bold">PRIME ESTATE</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="manager_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_properties.php">Properties</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_leases.php">Leases</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-5 fw-bold mb-2">Lease Management</h1>
                    <p class="mb-0">Manage lease applications and existing agreements</p>
                </div>
                <a href="manager_dashboard.php" class="btn btn-light btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Banner -->
    <div class="container">
        <div class="stats-banner">
            <div class="row text-center">
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="stat-number"><?= count($apps) ?></div>
                    <p class="mb-0">Pending Applications</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="stat-number"><?= count($leases) ?></div>
                    <p class="mb-0">Active Leases</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="stat-number">98%</div>
                    <p class="mb-0">Lease Renewal Rate</p>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">25+</div>
                    <p class="mb-0">Properties Managed</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Pending Applications Section -->
        <div class="mb-5">
            <h3 class="section-title">Pending Lease Applications</h3>
            
            <?php if ($apps): ?>
                <div class="row">
                    <?php foreach ($apps as $app): ?>
                        <div class="col-md-6">
                            <div class="application-card">
                                <form method="POST">
                                    <input type="hidden" name="tenant_id" value="<?= $app['tenant_id'] ?>">
                                    <input type="hidden" name="property_id" value="<?= $app['property_id'] ?>">
                                    
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-accent text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-0"><?= htmlspecialchars($app['tenant_name']) ?></h5>
                                            <p class="text-muted mb-0">Tenant ID: <?= $app['tenant_id'] ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-home"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-0"><?= htmlspecialchars($app['property_title']) ?></h5>
                                            <p class="text-muted mb-0">Property ID: <?= $app['property_id'] ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Application Date:</label>
                                        
                                    </div>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" name="start_date" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" name="end_date" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Monthly Rent (KES)</label>
                                            <input type="number" name="monthly_rent" class="form-control" placeholder="Enter rent amount" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" name="create_lease" class="btn btn-success w-100">
                                            <i class="fas fa-file-contract me-2"></i>Approve & Create Lease
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-check-circle text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="mt-4">No Pending Applications</h4>
                        <p class="text-muted">All lease applications have been processed.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Existing Leases Section -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="section-title mb-0">Existing Leases</h3>
                
            </div>
            
            <?php if ($leases): ?>
                <div class="table-responsive">
                    <table class="table table-custom table-hover">
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Property</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Rent (KES)</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leases as $lease): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-accent text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="ms-2">
                                                <?= htmlspecialchars($lease['tenant_name']) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($lease['property_title']) ?></td>
                                    <td><?= date('M d, Y', strtotime($lease['start_date'])) ?></td>
                                    <td><?= date('M d, Y', strtotime($lease['end_date'])) ?></td>
                                    <td class="fw-bold">KES <?= number_format($lease['monthly_rent']) ?></td>
                                    <td>
                                        <span class="badge-success"><?= $lease['status'] ?></span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($lease['created_at'])) ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-file-contract text-secondary" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="mt-4">No Active Leases</h4>
                        <p class="text-muted">You don't have any active leases at the moment.</p>
                        <a href="#" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>Create New Lease
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h3 class="mb-3"><i class="fas fa-home me-2"></i>PRIME ESTATE</h3>
                    <p class="mb-0">Professional real estate management solutions for property managers and tenants.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2023 Prime Estate Management System</p>
                    <p class="mb-0">Designed for efficient property management</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Back to top button functionality
        const backToTopButton = document.querySelector('.back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Console welcome message
        console.log("Prime Estate Lease Management System");
        console.log("Manager Dashboard - Lease Management Module");
    </script>
</body>
</html>