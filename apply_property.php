<?php
session_start();
require 'db.php';

// Only allow tenants
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

// Fetch available properties
$properties = $pdo->query("SELECT id, title FROM properties WHERE available = TRUE")->fetchAll();

// Handle form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'];
    $message = $_POST['message'] ?? '';
    $tenant_id = $_SESSION['user']['id'];
    
    // Handle file upload
    if (isset($_FILES['document']) && $_FILES['document']['error'] == UPLOAD_ERR_OK) {
        $docTmp = $_FILES['document']['tmp_name'];
        $docName = basename($_FILES['document']['name']);
        $ext = pathinfo($docName, PATHINFO_EXTENSION);

        $allowed = ['pdf', 'jpg', 'png'];
        if (!in_array(strtolower($ext), $allowed)) {
            $error = "Only PDF, JPG, PNG files are allowed.";
        } else {
            $uploadPath = "uploads/" . time() . "_$docName";
            if (move_uploaded_file($docTmp, $uploadPath)) {
                // Save application
                $stmt = $pdo->prepare("INSERT INTO applications (tenant_id, property_id, message, document_path) VALUES (?, ?, ?, ?)");
                $stmt->execute([$tenant_id, $property_id, $message, $uploadPath]);
                // Notify property manager
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user']['id'], "New application submitted for property ID: $property_id"]);
                // Redirect or show success message
                    

                $success = "Application submitted successfully!";
            } else {
                $error = "Failed to upload file.";
            }
        }
    } else {
        $error = "Please upload a document.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Property | Prime Estates</title>
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
            min-height: 100vh;
        }
        
        .application-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 15px;
        }
        
        .application-header {
            background: linear-gradient(to right, var(--accent), #16a085);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .application-card {
            background: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .application-body {
            padding: 2.5rem;
        }
        
        .application-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .application-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 3px;
            background: #e9ecef;
            z-index: 1;
        }
        
        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 3px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            transition: all 0.3s ease;
        }
        
        .step.active .step-icon {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }
        
        .step-label {
            font-weight: 600;
            color: #6c757d;
        }
        
        .step.active .step-label {
            color: var(--accent);
        }
        
        .form-section {
            margin-bottom: 2.5rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f2f5;
        }
        
        .section-title i {
            margin-right: 10px;
            color: var(--accent);
        }
        
        .property-selector {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 1.5rem;
        }
        
        .property-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .property-option:hover {
            border-color: var(--accent);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .property-option.selected {
            border-color: var(--accent);
            background: rgba(26, 188, 156, 0.05);
        }
        
        .property-option input {
            position: absolute;
            opacity: 0;
        }
        
        .property-option .checkmark {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 20px;
            height: 20px;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .property-option.selected .checkmark {
            background: var(--accent);
            border-color: var(--accent);
        }
        
        .property-option.selected .checkmark::after {
            content: '';
            display: block;
            width: 10px;
            height: 5px;
            border: solid white;
            border-width: 0 0 2px 2px;
            transform: rotate(-45deg);
            margin-top: -2px;
        }
        
        .property-info h5 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .property-info p {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .upload-area:hover {
            border-color: var(--accent);
            background-color: rgba(26, 188, 156, 0.05);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }
        
        .upload-text h5 {
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .upload-text p {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 1rem;
        }
        
        .preview-item {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .preview-item .preview-doc {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            width: 100%;
            height: 100%;
        }
        
        .preview-item .preview-doc i {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 5px;
        }
        
        .preview-item .preview-doc span {
            font-size: 0.7rem;
            text-align: center;
            padding: 0 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100%;
        }
        
        .remove-file {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(231, 76, 60, 0.8);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            cursor: pointer;
        }
        
        .btn-submit {
            background: linear-gradient(to right, var(--accent), #16a085);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            font-size: 1.1rem;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .requirements {
            background: #e8f4fd;
            border-left: 4px solid var(--secondary);
            padding: 1rem;
            border-radius: 0 8px 8px 0;
            margin-bottom: 2rem;
        }
        
        .requirements h5 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .requirements ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }
        
        .requirements li {
            margin-bottom: 0.25rem;
        }
        
        .message-box textarea {
            min-height: 150px;
        }
        
        @media (max-width: 768px) {
            .application-body {
                padding: 1.5rem;
            }
            
            .application-steps {
                flex-direction: column;
                align-items: center;
                gap: 30px;
            }
            
            .application-steps::before {
                display: none;
            }
            
            .step {
                display: flex;
                align-items: center;
                gap: 15px;
                width: 100%;
                text-align: left;
            }
            
            .step::before {
                content: '';
                position: relative;
                width: 40px;
                height: 40px;
                min-width: 40px;
                background: #e9ecef;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .step-icon {
                position: absolute;
                left: 0;
                margin: 0;
            }
            
            .step.active::before {
                background: var(--accent);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(to right, var(--primary), var(--dark));">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-home me-2"></i>
                <span class="fw-bold">PRIME ESTATES</span>
            </a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3"><?= $_SESSION['user']['name'] ?></span>
                <a href="tenant_dashboard.php" class="btn btn-sm btn-outline-light me-2">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a href="logout.php" class="btn btn-sm btn-light">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="application-container">
        <div class="application-header">
            <h1><i class="fas fa-file-alt me-2"></i>Property Application</h1>
            <p class="mb-0">Submit your application to rent your dream property</p>
        </div>
        
        <div class="application-card">
            <div class="application-body">
                <div class="application-steps">
                    <div class="step active">
                        <div class="step-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="step-label">Select Property</div>
                    </div>
                    <div class="step">
                        <div class="step-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="step-label">Upload Documents</div>
                    </div>
                    <div class="step">
                        <div class="step-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="step-label">Submit Application</div>
                    </div>
                </div>
                
                <div class="requirements">
                    <h5><i class="fas fa-info-circle me-2"></i>Application Requirements</h5>
                    <ul>
                        <li>Upload a valid ID document (PDF, JPG, PNG)</li>
                        <li>Provide proof of income (pay stub, bank statement)</li>
                        <li>Submit rental history if available</li>
                        <li>Include any additional documents requested by the property manager</li>
                    </ul>
                </div>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i> <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <!-- Property Selection Section -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-building me-2"></i>Select a Property</h4>
                        
                        <div class="property-selector">
                            <?php if ($properties): ?>
                                <?php foreach ($properties as $prop): ?>
                                    <label class="property-option">
                                        <input type="radio" name="property_id" value="<?= $prop['id'] ?>" required>
                                        <span class="checkmark"></span>
                                        <div class="property-info">
                                            <h5><?= htmlspecialchars($prop['title']) ?></h5>
                                            <p>Property ID: PR-<?= $prop['id'] ?></p>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-warning w-100">
                                    <i class="fas fa-exclamation-triangle me-2"></i> No available properties found.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Document Upload Section -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-cloud-upload-alt me-2"></i>Upload Documents</h4>
                        
                        <div class="upload-area" id="uploadArea">
                            <input type="file" name="document" class="file-input" id="fileInput" accept=".pdf,.jpg,.png" required>
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="upload-text">
                                <h5>Drag & Drop Documents Here</h5>
                                <p>or click to browse files</p>
                                <p class="small text-muted">Accepted formats: PDF, JPG, PNG (Max file size: 5MB)</p>
                            </div>
                        </div>
                        
                        <div class="file-preview" id="filePreview">
                            <!-- File preview will be added here -->
                        </div>
                    </div>
                    
                    <!-- Message Section -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-comment-alt me-2"></i>Additional Information</h4>
                        
                        <div class="mb-4 message-box">
                            <label class="form-label">Message to Property Manager</label>
                            <textarea name="message" class="form-control" placeholder="Tell the manager about yourself, why you'd be a great tenant, or any special requirements..."></textarea>
                            <div class="form-text">Optional but recommended - increases your chances of approval</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="tenant_dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane me-2"></i> Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Property selection
        document.querySelectorAll('.property-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.property-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Check the radio input
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
        
        // File upload preview
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const filePreview = document.getElementById('filePreview');
        
        fileInput.addEventListener('change', function() {
            filePreview.innerHTML = '';
            if (this.files.length > 0) {
                const file = this.files[0];
                const fileName = file.name;
                const fileType = fileName.split('.').pop().toLowerCase();
                
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                
                if (fileType === 'pdf') {
                    previewItem.innerHTML = `
                        <div class="preview-doc">
                            <i class="fas fa-file-pdf"></i>
                            <span>${fileName}</span>
                        </div>
                        <div class="remove-file" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </div>
                    `;
                } else if (fileType === 'jpg' || fileType === 'jpeg' || fileType === 'png') {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <div class="remove-file" onclick="removeFile()">
                                <i class="fas fa-times"></i>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewItem.innerHTML = `
                        <div class="preview-doc">
                            <i class="fas fa-file"></i>
                            <span>${fileName}</span>
                        </div>
                        <div class="remove-file" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </div>
                    `;
                }
                
                filePreview.appendChild(previewItem);
            }
        });
        
        // Drag and drop functionality
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#1abc9c';
            uploadArea.style.backgroundColor = 'rgba(26, 188, 156, 0.1)';
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#dee2e6';
            uploadArea.style.backgroundColor = '';
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#dee2e6';
            uploadArea.style.backgroundColor = '';
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });
        
        // Remove file function
        function removeFile() {
            fileInput.value = '';
            filePreview.innerHTML = '';
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Please upload a document before submitting your application.');
                uploadArea.style.borderColor = '#e74c3c';
                setTimeout(() => {
                    uploadArea.style.borderColor = '';
                }, 2000);
            }
        });
    </script>
</body>
</html>