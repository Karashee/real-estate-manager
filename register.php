<?php
require 'db.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$name, $email, $password, $role]);
        $msg = "Account created successfully!";
    } catch (PDOException $e) {
        $msg = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Prime Estates</title>
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
            height: 100vh;
            overflow-x: hidden;
        }
        
        .register-container {
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
        
        .register-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            transition: all 0.3s ease;
        }
        
        .register-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .register-subtitle {
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
        
        .btn-register {
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
        
        .btn-register:hover {
            background-color: #16a085;
            transform: translateY(-2px);
        }
        
        .progress-container {
            margin-bottom: 1.5rem;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
        }
        
        .password-strength {
            font-size: 0.85rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }
        
        .strength-weak {
            color: #dc3545;
        }
        
        .strength-medium {
            color: #ffc107;
        }
        
        .strength-strong {
            color: #28a745;
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
        
        .form-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-group input {
            padding-left: 45px;
        }
        
        @media (max-width: 992px) {
            .brand-section {
                display: none;
            }
            
            .register-section {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid register-container">
        <div class="row g-0">
            <!-- Brand/Image Section -->
            <div class="col-lg-7 d-none d-lg-block">
                <div class="brand-section">
                    <div class="brand-overlay"></div>
                    <div class="brand-logo">
                        <i class="fas fa-home"></i>PRIME ESTATE
                    </div>
                    <h2 class="brand-slogan">Join Our Real Estate Community</h2>
                    
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
            
            <!-- Registration Form Section -->
            <div class="col-lg-5">
                <div class="register-section">
                    <div class="register-card">
                        <div class="register-header">
                            <h2 class="register-title">Create Your Account</h2>
                            <p class="register-subtitle">Join our community of property owners and tenants</p>
                        </div>
                        
                        <!-- PHP Message -->
                        <?php if ($msg): ?>
                            <div class="alert alert-<?= strpos($msg, 'successfully') !== false ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                                <?= $msg ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <!-- Name Field -->
                            <div class="mb-4 form-group">
                                <i class="fas fa-user form-icon"></i>
                                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                            </div>
                            
                            <!-- Email Field -->
                            <div class="mb-4 form-group">
                                <i class="fas fa-envelope form-icon"></i>
                                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                            </div>
                            
                            <!-- Password Field -->
                            <div class="mb-4 form-group">
                                <i class="fas fa-lock form-icon"></i>
                                <div class="password-container">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Create Password" required>
                                    <span class="toggle-password" onclick="togglePassword()">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                
                                <!-- Password Strength Meter -->
                                <div class="progress-container mt-2">
                                    <div class="progress">
                                        <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div id="password-strength-text" class="password-strength"></div>
                                </div>
                            </div>
                            
                            <!-- Role Selection -->
                            <div class="mb-4">
                                <label class="form-label">I am registering as:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check flex-grow-1">
                                        <input class="form-check-input" type="radio" name="role" id="tenant" value="tenant" checked>
                                        <label class="form-check-label w-100 p-3 border rounded" for="tenant">
                                            <i class="fas fa-user me-2"></i>Tenant
                                            <p class="small mb-0 mt-1">Looking for properties to rent or buy</p>
                                        </label>
                                    </div>
                                    <div class="form-check flex-grow-1">
                                        <input class="form-check-input" type="radio" name="role" id="manager" value="manager">
                                        <label class="form-check-label w-100 p-3 border rounded" for="manager">
                                            <i class="fas fa-briefcase me-2"></i>Manager
                                            <p class="small mb-0 mt-1">Managing properties and listings</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Terms Agreement -->
                            <div class="mb-4 form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-register">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                            
                            <div class="login-footer">
                                <p>Already have an account? <a href="login.php">Sign in</a></p>
                                <p class="mt-2"><a href="index.php"><i class="fas fa-arrow-left me-1"></i> Back to Home</a></p>
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
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');
            
            // Reset
            let strength = 0;
            strengthBar.style.width = '0%';
            strengthBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');
            strengthText.textContent = '';
            
            if (password.length === 0) return;
            
            // Length check
            if (password.length >= 8) strength += 25;
            
            // Contains lowercase
            if (/[a-z]/.test(password)) strength += 25;
            
            // Contains uppercase
            if (/[A-Z]/.test(password)) strength += 25;
            
            // Contains number or special character
            if (/[0-9!@#$%^&*]/.test(password)) strength += 25;
            
            // Update UI
            strengthBar.style.width = strength + '%';
            
            if (strength < 50) {
                strengthBar.classList.add('bg-danger');
                strengthText.textContent = 'Weak password';
                strengthText.className = 'password-strength strength-weak';
            } else if (strength < 75) {
                strengthBar.classList.add('bg-warning');
                strengthText.textContent = 'Medium password';
                strengthText.className = 'password-strength strength-medium';
            } else {
                strengthBar.classList.add('bg-success');
                strengthText.textContent = 'Strong password';
                strengthText.className = 'password-strength strength-strong';
            }
        });
        
        // Simple animation for register card
        document.querySelector('.register-card').style.opacity = '0';
        document.querySelector('.register-card').style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            document.querySelector('.register-card').style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            document.querySelector('.register-card').style.opacity = '1';
            document.querySelector('.register-card').style.transform = 'translateY(0)';
        }, 100);
        
        // Console message
        console.log("Prime Estate Registration System");
        console.log("PHP functionality preserved - database operations intact");
    </script>
</body>
</html>