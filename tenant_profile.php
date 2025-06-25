<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Fetch current profile
$stmt = $pdo->prepare("SELECT name, email, phone, address FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, password_hash = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $address, $password_hash, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $address, $user_id]);
    }

    $_SESSION['user']['name'] = $name;
    $msg = "Profile updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Profile | Prime Estate</title>
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
            transition: all 0.3s ease;
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
            margin-bottom: 1.5rem;
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
        
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--light);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(to right, var(--secondary), var(--accent));
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .stats-banner {
            background: linear-gradient(to right, var(--secondary), var(--accent));
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .stat-item {
            text-align: center;
            padding: 1rem;
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
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
        
        .form-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-group input, .form-group textarea {
            padding-left: 45px;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 1.5rem 0;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
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
                        <a class="nav-link" href="tenant_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="properties.php">Properties</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link active" href="tenant_profile.php">Profile</a>
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
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="display-5 fw-bold mb-2">Tenant Profile</h1>
                    <p class="mb-0">Manage your personal information and account settings</p>
                </div>
                <a href="tenant_dashboard.php" class="btn btn-light mt-3 mt-md-0">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </section>

    <div class="container">
        
        
        <!-- Profile Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h2 class="section-title">Personal Information</h2>
                        <p class="text-muted">Update your profile details below</p>
                    </div>
                    
                    <!-- PHP Success Message -->
                    <?php if (isset($msg)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $msg ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <!-- Name Field -->
                        <div class="form-group">
                            <i class="fas fa-user form-icon"></i>
                            <input type="text" name="name" class="form-control" 
                                   value="<?= htmlspecialchars($profile['name']) ?>" 
                                   placeholder="Full Name" required>
                        </div>
                        
                        <!-- Email Field -->
                        <div class="form-group">
                            <i class="fas fa-envelope form-icon"></i>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($profile['email']) ?>" 
                                   placeholder="Email Address" required>
                        </div>
                        
                        <!-- Phone Field -->
                        <div class="form-group">
                            <i class="fas fa-phone form-icon"></i>
                            <input type="text" name="phone" class="form-control" 
                                   value="<?= htmlspecialchars($profile['phone']) ?>" 
                                   placeholder="Phone Number">
                        </div>
                        
                        <!-- Address Field -->
                        <div class="form-group">
                            <i class="fas fa-map-marker-alt form-icon"></i>
                            <textarea name="address" class="form-control" 
                                      placeholder="Your Address" rows="3"><?= htmlspecialchars($profile['address']) ?></textarea>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="form-group">
                            <i class="fas fa-lock form-icon"></i>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Change Password (leave blank to keep current)">
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
       
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h3 class="mb-3"><i class="fas fa-home me-2"></i>PRIME ESTATE</h3>
                    <p class="mb-0">Your trusted partner in real estate. We connect tenants with the perfect properties.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2023 Prime Estate Management System</p>
                    <p class="mb-0">Tenant Portal v2.0</p>
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
        console.log("Prime Estate Tenant Profile System");
        console.log("PHP functionality preserved - profile updates working");
    </script>
</body>
</html>