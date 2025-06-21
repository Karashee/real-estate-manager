<?php
session_start();
require 'db.php';

// Only allow manager
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

// Fetch properties
$propertiesStmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC");
$properties = $propertiesStmt->fetchAll();

// Fetch applications and join tenant and property
$applicationsStmt = $pdo->query("
    SELECT a.id, u.name AS tenant_name, p.title AS property_title, a.status, a.created_at
    FROM applications a
    JOIN users u ON a.tenant_id = u.id
    JOIN properties p ON a.property_id = p.id
    ORDER BY a.created_at DESC
");
$applications = $applicationsStmt->fetchAll();

// Get stats
$totalProperties = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$activeLeases = $pdo->query("SELECT COUNT(*) FROM leases WHERE end_date > NOW()")->fetchColumn();
$pendingApplications = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'")->fetchColumn();
$occupiedUnits = $pdo->query("SELECT COUNT(*) FROM leases WHERE start_date <= NOW() AND end_date >= NOW()")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard | Prime Estates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #1abc9c;
            --light: #f8f9fa;
            --dark: #343a40;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: var(--dark);
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(to bottom, var(--primary), var(--dark));
            color: white;
            padding-top: 20px;
            z-index: 100;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .sidebar-brand {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-brand h3 {
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        
        .sidebar-brand i {
            color: var(--accent);
            margin-right: 10px;
        }
        
        .sidebar-menu {
            padding: 0 15px;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-link i {
            width: 25px;
            font-size: 18px;
            margin-right: 10px;
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .header {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .welcome-text h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .welcome-text p {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
        }
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--dark);
            margin-right: 20px;
        }
        
        .notification-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-logout {
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            background: #16a085;
            transform: translateY(-2px);
        }
        
        /* Dashboard Stats */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }
        
        .stat-1 .stat-icon { background: rgba(26, 188, 156, 0.1); color: var(--accent); }
        .stat-2 .stat-icon { background: rgba(52, 152, 219, 0.1); color: var(--secondary); }
        .stat-3 .stat-icon { background: rgba(155, 89, 182, 0.1); color: #9b59b6; }
        .stat-4 .stat-icon { background: rgba(241, 196, 15, 0.1); color: #f1c40f; }
        
        .stat-info h3 {
            font-size: 1.75rem;
            margin-bottom: 0;
        }
        
        .stat-info p {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        /* Dashboard Sections */
        .dashboard-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0;
        }
        
        .view-all {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }
        
        /* Tables */
        .table-container {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 15px;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(26, 188, 156, 0.05);
        }
        
        .table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-pending {
            background: rgba(241, 196, 15, 0.1);
            color: #f1c40f;
        }
        
        .badge-approved {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }
        
        .badge-rejected {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .action-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--dark);
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: var(--accent);
        }
        
        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: rgba(26, 188, 156, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: var(--accent);
        }
        
        .action-card:hover .action-icon {
            background: var(--accent);
            color: white;
        }
        
        .action-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        /* Chart Container */
        .chart-container {
            height: 300px;
            margin-top: 20px;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .sidebar:hover {
                width: var(--sidebar-width);
            }
            
            .brand-text, .link-text {
                opacity: 0;
                transition: opacity 0.3s;
            }
            
            .sidebar:hover .brand-text,
            .sidebar:hover .link-text {
                opacity: 1;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                padding: 0;
            }
            
            .sidebar.active {
                width: var(--sidebar-width);
                padding-top: 20px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                margin-top: 15px;
                width: 100%;
                justify-content: space-between;
            }
        }
        
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 101;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h3><i class="fas fa-home"></i> <span class="brand-text">PRIME ESTATES</span></h3>
        </div>
        
        <div class="sidebar-menu">
            <a href="#" class="nav-link active">
                <i class="fas fa-tachometer-alt"></i>
                <span class="link-text">Dashboard</span>
            </a>
            <a href="add_property.php" class="nav-link">
                <i class="fas fa-plus-circle"></i>
                <span class="link-text">Add Property</span>
            </a>
            <a href="manage_properties.php" class="nav-link">
                <i class="fas fa-building"></i>
                <span class="link-text">Manage Properties</span>
            </a>
            <a href="view_applications.php" class="nav-link">
                <i class="fas fa-file-alt"></i>
                <span class="link-text">View Applications</span>
            </a>
            <a href="manage_leases.php" class="nav-link">
                <i class="fas fa-file-contract"></i>
                <span class="link-text">Manage Leases</span>
            </a>
            <a href="notifications.php" class="nav-link">
                <i class="fas fa-bell"></i>
                <span class="link-text">Notifications</span>
            </a>
            <a href="tenant_profile.php" class="nav-link">
                <i class="fas fa-users"></i>
                <span class="link-text">Tenant Profiles</span>
            </a>
            <a href="reports.php" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                <span class="link-text">Reports</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="user-info">
                <div class="user-avatar">
                    <?= substr($_SESSION['user']['name'], 0, 1) ?>
                </div>
                <div class="welcome-text">
                    <h1>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></h1>
                    <p>Property Manager Dashboard</p>
                </div>
            </div>
            
            <div class="header-actions">
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count">5</span>
                </button>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Dashboard Stats -->
        <div class="stats-container">
            <div class="stat-card stat-1">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalProperties ?></h3>
                    <p>Total Properties</p>
                </div>
            </div>
            
            <div class="stat-card stat-2">
                <div class="stat-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $activeLeases ?></h3>
                    <p>Active Leases</p>
                </div>
            </div>
            
            <div class="stat-card stat-3">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $pendingApplications ?></h3>
                    <p>Pending Applications</p>
                </div>
            </div>
            
            <div class="stat-card stat-4">
                <div class="stat-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $occupiedUnits ?></h3>
                    <p>Occupied Units</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="dashboard-section">
            <div class="section-header">
                <h4 class="section-title">Quick Actions</h4>
            </div>
            
            <div class="quick-actions">
                <a href="add_property.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h5 class="action-title">Add Property</h5>
                    <p class="action-desc">List a new property</p>
                </a>
                
                <a href="manage_properties.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h5 class="action-title">Manage Properties</h5>
                    <p class="action-desc">Edit existing listings</p>
                </a>
                
                <a href="view_applications.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5 class="action-title">Review Applications</h5>
                    <p class="action-desc">View rental applications</p>
                </a>
                
                <a href="manage_leases.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h5 class="action-title">Manage Leases</h5>
                    <p class="action-desc">Create and edit lease agreements</p>
                </a>
            </div>
        </div>
        
        <div class="row">
            <!-- Properties Section -->
            <div class="col-lg-6">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h4 class="section-title">Recent Properties</h4>
                        <a href="manage_properties.php" class="view-all">Manage All</a>
                    </div>
                    
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($properties as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['title']) ?></td>
                                        <td><?= htmlspecialchars($p['location']) ?></td>
                                        <td>KES <?= number_format($p['price'], 2) ?></td>
                                        <td>
                                            <?php if ($p['available']): ?>
                                                <span class="status-badge badge-approved">Available</span>
                                            <?php else: ?>
                                                <span class="status-badge badge-rejected">Occupied</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (!$properties): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p>No properties listed yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Applications Section -->
            <div class="col-lg-6">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h4 class="section-title">Recent Applications</h4>
                        <a href="view_applications.php" class="view-all">View All</a>
                    </div>
                    
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Property</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($app['tenant_name']) ?></td>
                                        <td><?= htmlspecialchars($app['property_title']) ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = 'badge-pending';
                                            if ($app['status'] == 'approved') $badgeClass = 'badge-approved';
                                            if ($app['status'] == 'rejected') $badgeClass = 'badge-rejected';
                                            ?>
                                            <span class="status-badge <?= $badgeClass ?>"><?= htmlspecialchars($app['status']) ?></span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (!$applications): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p>No applications submitted yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Occupancy Chart -->
        <div class="dashboard-section">
            <div class="section-header">
                <h4 class="section-title">Property Occupancy</h4>
            </div>
            <div class="chart-container">
                <canvas id="occupancyChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileBtn = document.getElementById('mobileMenuBtn');
            
            if (window.innerWidth < 768 && 
                !sidebar.contains(event.target) && 
                !mobileBtn.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
        
        // Initialize occupancy chart
        const ctx = document.getElementById('occupancyChart').getContext('2d');
        const occupancyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Downtown', 'Westside', 'North Hills', 'South Park', 'East End'],
                datasets: [{
                    label: 'Occupancy Rate (%)',
                    data: [85, 92, 78, 95, 88],
                    backgroundColor: 'rgba(26, 188, 156, 0.7)',
                    borderColor: 'rgba(26, 188, 156, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Occupancy: ${context.parsed.y}%`;
                            }
                        }
                    }
                }
            }
        });
        
        // Simple animation for cards
        document.querySelectorAll('.stat-card, .action-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
        });
        
        setTimeout(() => {
            document.querySelectorAll('.stat-card, .action-card').forEach((card, index) => {
                card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });
        }, 300);
    </script>
</body>
</html>