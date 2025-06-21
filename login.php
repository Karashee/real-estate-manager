<?php
require 'db.php';
session_start();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = $user;
        if ($user['role'] == 'tenant') {
            header("Location: tenant_dashboard.php");
        } else {
            header("Location: manager_dashboard.php");
        }
        exit();
    } else {
        $msg = "Invalid login credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Estate | Secure Login</title>
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
            background-color: #f8f9fa;
            height: 100vh;
            overflow-x: hidden;
        }
        
        .login-container {
            height: 100vh;
        }
        
        .brand-section {
            background: linear-gradient(rgba(44, 62, 80, 0.85), rgba(44, 62, 80, 0.9)), 
                        url('https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .brand-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(26, 188, 156, 0.1);
        }
        
        .brand-logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 2;
        }
        
        .brand-logo i {
            color: var(--accent);
            margin-right: 10px;
        }
        
        .brand-slogan {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }
        
        .feature-list {
            position: relative;
            z-index: 2;
            list-style: none;
            padding-left: 0;
        }
        
        .feature-list li {
            margin-bottom: 1rem;
            padding-left: 2rem;
            position: relative;
        }
        
        .feature-list li i {
            position: absolute;
            left: 0;
            top: 0.25rem;
            color: var(--accent);
            font-size: 1.2rem;
        }
        
        .login-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            transition: all 0.3s ease;
        }
        
        .login-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            color: #6c757d;
            font-size: 1rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
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
        
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        
        .btn-login {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn-login:hover {
            background-color: #16a085;
            transform: translateY(-2px);
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #6c757d;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        
        .divider::before {
            margin-right: 1rem;
        }
        
        .divider::after {
            margin-left: 1rem;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .social-btn.google {
            color: #DB4437;
        }
        
        .social-btn.facebook {
            color: #4267B2;
        }
        
        .social-btn.twitter {
            color: #1DA1F2;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .login-footer a {
            color: var(--accent);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .login-footer a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .property-highlight {
            position: absolute;
            bottom: 2rem;
            left: 2rem;
            right: 2rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            align-items: center;
            z-index: 2;
        }
        
        .property-highlight img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 1rem;
        }
        
        .property-info h5 {
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }
        
        .property-info p {
            margin-bottom: 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .highlight-tag {
            position: absolute;
            top: -10px;
            right: 10px;
            background: var(--accent);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        @media (max-width: 992px) {
            .brand-section {
                display: none;
            }
            
            .login-section {
                padding: 1rem;
            }
        }
        
        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .floating-label input {
            padding-top: 1.5rem;
        }
        
        .floating-label label {
            position: absolute;
            top: 0.75rem;
            left: 1rem;
            color: #6c757d;
            transition: all 0.2s ease;
            pointer-events: none;
        }
        
        .floating-label input:focus ~ label,
        .floating-label input:not(:placeholder-shown) ~ label {
            top: 0.25rem;
            left: 1rem;
            font-size: 0.75rem;
            color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="container-fluid login-container">
        <div class="row g-0">
            <!-- Brand/Image Section -->
            <div class="col-lg-7 d-none d-lg-block">
                <div class="brand-section">
                    <div class="brand-overlay"></div>
                    <div class="brand-logo">
                        <i class="fas fa-home"></i>PRIME ESTATE
                    </div>
                    <h2 class="brand-slogan">Your Trusted Real Estate Partner</h2>
                    
                    <ul class="feature-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Access thousands of premium properties
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Manage your properties efficiently
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Connect with professional agents
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Secure transaction processing
                        </li>
                    </ul>
                    
                    <div class="property-highlight">
                        <div class="highlight-tag">FEATURED</div>
                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Luxury Villa">
                        <div class="property-info">
                            <h5>Seaside Luxury Villa</h5>
                            <p>Miami Beach, FL • $1.2M</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Login Form Section -->
            <div class="col-lg-5">
                <div class="login-section">
                    <div class="login-card">
                        <div class="login-header">
                            <h2 class="login-title">Welcome Back</h2>
                            <p class="login-subtitle">Sign in to access your account</p>
                        </div>
                        
                        <!-- PHP Error Message -->
                        <?php if ($msg): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $msg ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <!-- Email Field -->
                            <div class="mb-4 floating-label">
                                <input type="email" name="email" id="email" class="form-control" placeholder=" " required>
                                <label for="email">Email Address</label>
                            </div>
                            
                            <!-- Password Field -->
                            <div class="mb-4 floating-label">
                                <div class="password-container">
                                    <input type="password" name="password" id="password" class="form-control" placeholder=" " required>
                                    <label for="password">Password</label>
                                    <span class="toggle-password" onclick="togglePassword()">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="forgot-password.php" class="text-decoration-none">Forgot password?</a>
                            </div>
                            
                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Account
                            </button>
                            
                            <div class="divider">Or continue with</div>
                            
                            <div class="social-login">
                                <a href="#" class="social-btn google">
                                    <i class="fab fa-google"></i>
                                </a>
                                <a href="#" class="social-btn facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-btn twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </div>
                            
                            <div class="login-footer">
                                <p>Don't have an account? <a href="register.php">Register now</a></p>
                                <p class="mt-2"><a href="landing.php"><i class="fas fa-arrow-left me-1"></i> Back to Home</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Add floating label functionality
        document.querySelectorAll('.floating-label input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentNode.classList.remove('focused');
                }
            });
            
            // Initialize based on existing value
            if (input.value) {
                input.parentNode.classList.add('focused');
            }
        });
        
        // Simple animation for login card
        document.querySelector('.login-card').style.opacity = '0';
        document.querySelector('.login-card').style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            document.querySelector('.login-card').style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            document.querySelector('.login-card').style.opacity = '1';
            document.querySelector('.login-card').style.transform = 'translateY(0)';
        }, 100);
        
        // Console message
        console.log("Prime Estate Login System");
        console.log("PHP functionality preserved - session handling and authentication intact");
    </script>
</body>
</html>