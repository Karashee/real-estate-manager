<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard | Prime Estates</title>
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
        
        /* Properties Section */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .property-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .property-img {
            height: 180px;
            width: 100%;
            object-fit: cover;
        }
        
        .property-body {
            padding: 20px;
        }
        
        .property-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .property-location {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .property-location i {
            margin-right: 5px;
        }
        
        .property-details {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .detail-item {
            text-align: center;
        }
        
        .detail-item i {
            display: block;
            font-size: 1.25rem;
            margin-bottom: 5px;
            color: var(--accent);
        }
        
        .detail-value {
            font-weight: 600;
        }
        
        .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Application Status */
        .status-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .status-card {
            border-radius: 10px;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--accent);
        }
        
        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
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
        
        .status-property {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .status-property img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .property-info h5 {
            margin-bottom: 5px;
        }
        
        .property-info p {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        
        .status-date {
            color: #6c757d;
            font-size: 0.85rem;
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
                <i class="fas fa-home"></i>
                <span class="link-text">Dashboard</span>
            </a>
            <a href="search_properties.php" class="nav-link">
                <i class="fas fa-search"></i>
                <span class="link-text">Search Properties</span>
            </a>
            <a href="apply_property.php" class="nav-link">
                <i class="fas fa-file-alt"></i>
                <span class="link-text">Apply for Unit</span>
            </a>
            <a href="view_applications.php" class="nav-link">
                <i class="fas fa-tasks"></i>
                <span class="link-text">Application Status</span>
            </a>
            <a href="notifications.php" class="nav-link">
                <i class="fas fa-bell"></i>
                <span class="link-text">Notifications</span>
            </a>
            <a href="tenant_profile.php" class="nav-link">
                <i class="fas fa-user"></i>
                <span class="link-text">My Profile</span>
            </a>
            <a href="payment_history.php" class="nav-link">
                <i class="fas fa-credit-card"></i>
                <span class="link-text">Payment History</span>
            </a>
            <a href="documents.php" class="nav-link">
                <i class="fas fa-file-contract"></i>
                <span class="link-text">Documents</span>
            </a>
            <a href="support.php" class="nav-link">
                <i class="fas fa-headset"></i>
                <span class="link-text">Support</span>
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
                    <h1>Hello, <?= htmlspecialchars($_SESSION['user']['name']) ?></h1>
                    <p>Welcome to your tenant dashboard</p>
                </div>
            </div>
            
            <div class="header-actions">
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count">3</span>
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
                    <i class="fas fa-home"></i>
                </div>
                <div class="stat-info">
                    <h3>2</h3>
                    <p>Properties Viewed</p>
                </div>
            </div>
            
            <div class="stat-card stat-2">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>3</h3>
                    <p>Applications Sent</p>
                </div>
            </div>
            
            <div class="stat-card stat-3">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>1</h3>
                    <p>Approved Applications</p>
                </div>
            </div>
            
            <div class="stat-card stat-4">
                <div class="stat-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-info">
                    <h3>3</h3>
                    <p>Notifications</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="dashboard-section">
            <div class="section-header">
                <h4 class="section-title">Quick Actions</h4>
            </div>
            
            <div class="quick-actions">
                <a href="search_properties.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5 class="action-title">Search Properties</h5>
                    <p class="action-desc">Find your next home</p>
                </a>
                
                <a href="apply_property.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5 class="action-title">Apply for Unit</h5>
                    <p class="action-desc">Submit a new application</p>
                </a>
                
                <a href="view_applications.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5 class="action-title">View Applications</h5>
                    <p class="action-desc">Check your application status</p>
                </a>
                
                <a href="tenant_profile.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5 class="action-title">Update Profile</h5>
                    <p class="action-desc">Manage your personal information</p>
                </a>
            </div>
        </div>
        
        <!-- Recent Properties -->
        <div class="dashboard-section">
            <div class="section-header">
                <h4 class="section-title">Recommended Properties</h4>
                <a href="search_properties.php" class="view-all">View All</a>
            </div>
            
            <div class="properties-grid">
                <!-- Property 1 -->
                <div class="property-card">
                    <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Modern Apartment" class="property-img">
                    <div class="property-body">
                        <h5 class="property-title">Luxury Downtown Apartment</h5>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i> Downtown, New York
                        </div>
                        <div class="property-details">
                            <div class="detail-item">
                                <i class="fas fa-bed"></i>
                                <div class="detail-value">2</div>
                                <div class="detail-label">Beds</div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-bath"></i>
                                <div class="detail-value">2</div>
                                <div class="detail-label">Baths</div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-ruler-combined"></i>
                                <div class="detail-value">1,200</div>
                                <div class="detail-label">Sq. Ft.</div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary w-100 mt-3">View Details</a>
                    </div>
                </div>
                
                <!-- Property 2 -->
                <div class="property-card">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Suburban House" class="property-img">
                    <div class="property-body">
                        <h5 class="property-title">Spacious Family Home</h5>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i> Green Valley, California
                        </div>
                        <div class="property-details">
                            <div class="detail-item">
                                <i class="fas fa-bed"></i>
                                <div class="detail-value">4</div>
                                <div class="detail-label">Beds</div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-bath"></i>
                                <div class="detail-value">3</div>
                                <div class="detail-label">Baths</div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-ruler-combined"></i>
                                <div class="detail-value">2,800</div>
                                <div class="detail-label">Sq. Ft.</div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary w-100 mt-3">View Details</a>
                    </div>
                </div>
                
                <!-- Property 3 -->
                <div class="property-card">
                    <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Waterfront Villa" class="property-img">
                    <div class="property-body">
                        <h5 class="property-title">Waterfront Luxury Villa</h5>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i> Ocean View, Florida
                        </div>
                        <div class="property-details">
                            <div class="detail-item">
                                <i class="fas fa-bed"></i>
                                <div class="detail-value">5</div>
                                <div class="detail-label">Beds</div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-bath"></i>
                                <div class="detail-value">4.5</div>
                                <div class="detail-label">Baths</div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-ruler-combined"></i>
                                <div class="detail-value">4,200</div>
                                <div class="detail-label">Sq. Ft.</div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary w-100 mt-3">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Application Status -->
        <div class="dashboard-section">
            <div class="section-header">
                <h4 class="section-title">Recent Applications</h4>
                <a href="view_applications.php" class="view-all">View All</a>
            </div>
            
            <div class="status-container">
                <!-- Application 1 -->
                <div class="status-card">
                    <div class="status-header">
                        <h5>Application #RE-2023-001</h5>
                        <span class="status-badge badge-approved">Approved</span>
                    </div>
                    <div class="status-property">
                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Property">
                        <div class="property-info">
                            <h5>Luxury Downtown Apartment</h5>
                            <p>Downtown, New York</p>
                        </div>
                    </div>
                    <p>Your application has been approved! Contact the property manager to schedule move-in.</p>
                    <div class="status-date">Submitted: Oct 15, 2023</div>
                </div>
                
                <!-- Application 2 -->
                <div class="status-card">
                    <div class="status-header">
                        <h5>Application #RE-2023-002</h5>
                        <span class="status-badge badge-pending">Pending</span>
                    </div>
                    <div class="status-property">
                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Property">
                        <div class="property-info">
                            <h5>Spacious Family Home</h5>
                            <p>Green Valley, California</p>
                        </div>
                    </div>
                    <p>Your application is under review. We'll notify you once a decision is made.</p>
                    <div class="status-date">Submitted: Oct 20, 2023</div>
                </div>
                
                <!-- Application 3 -->
                <div class="status-card">
                    <div class="status-header">
                        <h5>Application #RE-2023-003</h5>
                        <span class="status-badge badge-rejected">Rejected</span>
                    </div>
                    <div class="status-property">
                        <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Property">
                        <div class="property-info">
                            <h5>Waterfront Luxury Villa</h5>
                            <p>Ocean View, Florida</p>
                        </div>
                    </div>
                    <p>Unfortunately, your application was not approved. Contact support for more information.</p>
                    <div class="status-date">Submitted: Oct 18, 2023</div>
                </div>
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
        
        // Simple animation for cards
        document.querySelectorAll('.stat-card, .property-card, .status-card, .action-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
        });
        
        setTimeout(() => {
            document.querySelectorAll('.stat-card, .property-card, .status-card, .action-card').forEach((card, index) => {
                card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });
        }, 300);
        
        // Console welcome message
        console.log("Tenant Dashboard Loaded");
        console.log("User: <?= htmlspecialchars($_SESSION['user']['name']) ?>");
    </script>
</body>
</html>