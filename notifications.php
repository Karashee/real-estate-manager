<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

// Mark all as seen
$pdo->prepare("UPDATE notifications SET seen = TRUE WHERE user_id = ?")->execute([$user_id]);

// Fetch notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications | Prime Estate</title>
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
      min-height: 100vh;
    }
    
    .navbar {
      background: linear-gradient(to right, var(--primary), var(--dark));
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      padding: 1rem 2rem;
    }
    
    .notification-header {
      background: linear-gradient(rgba(44, 62, 80, 0.85), rgba(44, 62, 80, 0.9)), 
                  url('https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
      background-size: cover;
      background-position: center;
      color: white;
      padding: 3rem 0;
      border-radius: 0 0 20px 20px;
      margin-bottom: 2rem;
    }
    
    .notification-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 1.5rem;
      border-left: 4px solid transparent;
      transition: all 0.3s ease;
      overflow: hidden;
    }
    
    .notification-card.unread {
      border-left-color: var(--accent);
      background-color: rgba(26, 188, 156, 0.05);
    }
    
    .notification-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .notification-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      flex-shrink: 0;
    }
    
    .notification-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: var(--accent);
    }
    
    .notification-type {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
      margin-bottom: 5px;
    }
    
    .notification-type.info {
      background: rgba(52, 152, 219, 0.15);
      color: var(--secondary);
    }
    
    .notification-type.alert {
      background: rgba(231, 76, 60, 0.15);
      color: #e74c3c;
    }
    
    .notification-type.success {
      background: rgba(46, 204, 113, 0.15);
      color: #2ecc71;
    }
    
    .notification-actions {
      border-top: 1px solid #e9ecef;
      padding: 0.75rem 1.25rem;
      background: #f8f9fa;
      display: flex;
      gap: 0.5rem;
    }
    
    .empty-state {
      background: white;
      border-radius: 15px;
      padding: 3rem;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .empty-state-icon {
      font-size: 4rem;
      color: var(--accent);
      margin-bottom: 1.5rem;
    }
    
    .filter-buttons {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }
    
    .stats-card {
      background: linear-gradient(to right, var(--secondary), var(--accent));
      color: white;
      padding: 1.5rem;
      border-radius: 15px;
      margin-bottom: 2rem;
    }
    
    .footer {
      background: linear-gradient(to right, var(--primary), var(--dark));
      color: white;
      padding: 2rem 0;
      margin-top: 3rem;
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
    
    @media (max-width: 768px) {
      .notification-header {
        padding: 2rem 0;
      }
      
      .notification-card {
        padding: 1rem;
      }
      
      .notification-content {
        flex-direction: column;
      }
      
      .notification-icon {
        margin-right: 0;
        margin-bottom: 1rem;
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
            <a class="nav-link" href="<?= $role === 'manager' ? 'manager_dashboard.php' : 'tenant_dashboard.php' ?>">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="properties.php">Properties</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="notifications.php">Notifications</a>
          </li>
          <li class="nav-item">
            
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-user-circle me-1"></i><?= $_SESSION['user']['name'] ?>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="tenant_profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
              <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Notification Header -->
  <div class="notification-header">
    <div class="container text-center">
      <h1><i class="fas fa-bell me-2"></i>Notifications</h1>
      <p class="lead">Stay updated with your account activities</p>
    </div>
  </div>

  <div class="container mb-5">
    <!-- Stats Card -->
    <div class="row">
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <i class="fas fa-bell fa-2x"></i>
            </div>
            <div>
              <div class="h4 mb-0"><?= count($notifications) ?></div>
              <div>Total Notifications</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <i class="fas fa-envelope fa-2x"></i>
            </div>
            <div>
              <div class="h4 mb-0">12</div>
              <div>Unread Messages</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <i class="fas fa-calendar-check fa-2x"></i>
            </div>
            <div>
              <div class="h4 mb-0">3</div>
              <div>Upcoming Events</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <i class="fas fa-tasks fa-2x"></i>
            </div>
            <div>
              <div class="h4 mb-0">5</div>
              <div>Pending Actions</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Notification Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="mb-0">Your Notifications</h3>
        <p class="text-muted mb-0">All your account activities in one place</p>
      </div>
      <div>
        <button class="btn btn-outline-secondary me-2">
          <i class="fas fa-filter me-1"></i>Filter
        </button>
        
      </div>
    </div>
    
   
    
    <!-- Notifications List -->
    <?php if ($notifications): ?>
      <div class="notifications-list">
        <?php foreach ($notifications as $note): ?>
          <div class="notification-card <?= $note['seen'] ? '' : 'unread' ?>">
            <div class="p-3 position-relative">
              <?php if (!$note['seen']): ?>
                <span class="notification-badge"></span>
              <?php endif; ?>
              
              <div class="d-flex notification-content">
                <div class="notification-icon bg-light text-primary">
                  <i class="fas fa-bell"></i>
                </div>
                
                <div class="flex-grow-1">
                  <span class="notification-type info">Account Activity</span>
                  <h5 class="mb-2"><?= htmlspecialchars($note['message']) ?></h5>
                
                  
                </div>
                
                
                <div class="text-end">
                  <small class="text-muted"><?= date('M d, Y H:i', strtotime($note['created_at'])) ?></small>
                </div>
              </div>
            </div>
            
            <div class="notification-actions">
              <button class="btn btn-sm btn-outline-primary">
                <i class="fas fa-eye me-1"></i>Notification
              </button>
              
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-state-icon">
          <i class="far fa-bell-slash"></i>
        </div>
        <h4>No Notifications Yet</h4>
        <p class="text-muted mb-4">
          You don't have any notifications at the moment. We'll notify you when there's new activity.
        </p>
        <a href="properties.php" class="btn btn-primary">
          <i class="fas fa-home me-2"></i>Browse Properties
        </a>
      </div>
    <?php endif; ?>
    
    <!-- Notification Tips -->
    <div class="card mt-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-4 mb-md-0">
            <h5><i class="fas fa-cog me-2 text-primary"></i>Notification Settings</h5>
            <p class="text-muted">
              Customize how you receive notifications to stay updated without being overwhelmed.
            </p>
            <a href="settings.php" class="btn btn-sm btn-outline-primary">
              <i class="fas fa-sliders-h me-1"></i>Manage Settings
            </a>
          </div>
          <div class="col-md-6">
            <h5><i class="fas fa-question-circle me-2 text-primary"></i>Need Help?</h5>
            <p class="text-muted">
              Our support team is ready to assist you with any questions about your notifications.
            </p>
            <a href="contact.php" class="btn btn-sm btn-outline-primary">
              <i class="fas fa-headset me-1"></i>Contact Support
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h3 class="mb-3"><i class="fas fa-home me-2"></i>PRIME ESTATE</h3>
          <p>Your trusted partner in real estate. Stay connected with our notification system.</p>
        </div>
        
       
      <hr class="my-4" style="background: rgba(255,255,255,0.2);">
      <div class="text-center">
        <p class="mb-0">&copy; 2023 Prime Estate. All rights reserved.</p>
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
    
    // Notification card click handler
    document.querySelectorAll('.notification-card').forEach(card => {
      card.addEventListener('click', function() {
        this.classList.remove('unread');
        const badge = this.querySelector('.notification-badge');
        if (badge) badge.remove();
      });
    });
    
    // Console message
    console.log("Prime Estate Notifications System");
  </script>
</body>
</html>