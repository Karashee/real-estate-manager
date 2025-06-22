<?php
session_start();
require 'db.php';

// Only manager allowed
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $price = $_POST['price'] ?? '';

    if ($title && $price && $location) {
        $stmt = $pdo->prepare("INSERT INTO properties (title, description, location, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $location, $price]);
        $msg = "Property added successfully!";
    } else {
        $msg = "Please fill all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property | Prime Estates</title>
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
        
        .navbar {
            background: linear-gradient(to right, var(--primary), var(--dark));
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
        }
        
        .container-main {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 15px;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(to right, var(--accent), #16a085);
            color: white;
            padding: 1.5rem 2rem;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--primary);
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            color: var(--accent);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.25rem rgba(26, 188, 156, 0.25);
        }
        
        .input-group-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 4;
        }
        
        .input-group-icon input {
            padding-left: 45px;
        }
        
        .input-group-icon textarea {
            padding-left: 45px;
        }
        
        .btn-submit {
            background: linear-gradient(to right, var(--accent), #16a085);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .property-preview {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            height: 100%;
        }
        
        .preview-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .preview-price {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 1rem;
        }
        
        .preview-location {
            display: flex;
            align-items: center;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .preview-location i {
            margin-right: 8px;
        }
        
        .preview-description {
            color: #495057;
            line-height: 1.6;
        }
        
        .preview-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 1.5rem;
        }
        
        .detail-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        .detail-icon {
            font-size: 1.5rem;
            color: var(--accent);
            margin-bottom: 10px;
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
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
        
        .image-preview {
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
        
        .remove-image {
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
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
        }
        
        .feature-item input {
            margin-right: 8px;
        }
        
        @media (max-width: 992px) {
            .preview-column {
                margin-top: 2rem;
            }
        }
        
        .form-note {
            background-color: #e8f4fd;
            border-left: 4px solid var(--secondary);
            padding: 1rem;
            border-radius: 0 8px 8px 0;
            margin-bottom: 1.5rem;
        }
        
        .form-note p {
            margin-bottom: 0;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-home me-2"></i>
                <span class="fw-bold">PRIME ESTATE MANAGER</span>
            </a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3"><?= $_SESSION['user']['name'] ?></span>
                <a href="manager_dashboard.php" class="btn btn-sm btn-outline-light me-2">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a href="logout.php" class="btn btn-sm btn-light">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-plus-circle me-2 text-success"></i>Add New Property</h2>
            <a href="manager_dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
        
        <?php if ($msg): ?>
            <div class="alert alert-<?= strpos($msg, 'successfully') !== false ? 'success' : 'danger' ?> alert-dismissible fade show">
                <?= $msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <div class="form-header">
                <h3 class="mb-0"><i class="fas fa-home me-2"></i>Property Information</h3>
                <p class="mb-0">Fill in the details for your new property listing</p>
            </div>
            
            <form method="POST">
                <div class="form-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-section">
                                <h4 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h4>
                                
                                <div class="mb-4 input-group-icon">
                                    <i class="fas fa-heading input-icon"></i>
                                    <input type="text" name="title" class="form-control" placeholder="Property Title" required>
                                    <div class="form-text">e.g. Luxury 3-Bedroom Apartment with Sea View</div>
                                </div>
                                
                                <div class="mb-4 input-group-icon">
                                    <i class="fas fa-map-marker-alt input-icon"></i>
                                    <input type="text" name="location" class="form-control" placeholder="Full Address" required>
                                    <div class="form-text">e.g. 123 Beach Road, Mombasa</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Property Type</label>
                                        <select class="form-select">
                                            <option selected disabled>Select property type</option>
                                            <option>Apartment</option>
                                            <option>House</option>
                                            <option>Villa</option>
                                            <option>Townhouse</option>
                                            <option>Commercial</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Property Status</label>
                                        <select class="form-select">
                                            <option selected>For Rent</option>
                                            <option>For Sale</option>
                                            <option>Leased</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-4 input-group-icon">
                                    <i class="fas fa-money-bill-wave input-icon"></i>
                                    <input type="number" step="0.01" name="price" class="form-control" placeholder="Monthly Price (KES)" required>
                                    <div class="form-text">Enter the monthly rental price in KES</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Describe the property in detail..."></textarea>
                                    <div class="form-text">Provide a detailed description of the property and its features</div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4 class="section-title"><i class="fas fa-expand-arrows-alt"></i> Property Details</h4>
                                
                                <div class="row">
                                    <div class="col-md-3 mb-4">
                                        <label class="form-label">Bedrooms</label>
                                        <select class="form-select">
                                            <option selected disabled>Select</option>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5+</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <label class="form-label">Bathrooms</label>
                                        <select class="form-select">
                                            <option selected disabled>Select</option>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5+</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <label class="form-label">Area (sq ft)</label>
                                        <input type="number" class="form-control" placeholder="Sq. ft.">
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <label class="form-label">Year Built</label>
                                        <input type="number" class="form-control" placeholder="Year">
                                    </div>
                                </div>
                                
                                <div class="form-note">
                                    <p><i class="fas fa-info-circle me-2"></i>These details will help tenants find your property more easily</p>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4 class="section-title"><i class="fas fa-images"></i> Property Images</h4>
                                
                                <div class="upload-area" id="uploadArea">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h5>Drag & Drop Images Here</h5>
                                    <p class="text-muted">or click to browse files</p>
                                    <p class="small text-muted">Recommended size: 1200x800 pixels (max 10 images)</p>
                                    <input type="file" id="fileInput" accept="image/*" multiple style="display: none;">
                                </div>
                                
                                <div class="image-preview" id="imagePreview">
                                    <!-- Image previews will be added here -->
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4 class="section-title"><i class="fas fa-star"></i> Amenities & Features</h4>
                                
                                <div class="features-grid">
                                    <div class="feature-item">
                                        <input type="checkbox" class="form-check-input" id="swimmingPool">
                                        <label class="form-check-label" for="swimmingPool">Swimming Pool</label>
                                    </div>
                                    <div class="feature-item">
                                        <input type="checkbox" class="form-check-input" id="gym">
                                        <label class="form-check-label" for="gym">Gym</label>
                                    </div>
                                    <div class="feature-item">
                                        <input type="checkbox" class="form-check-input" id="parking">
                                        <label class="form-check-label" for="parking">Parking</label>
                                    </div>
                                    <div class="feature-item">
                                        <input type="checkbox" class="form-check-input" id="garden">
                                        <label class="form-check-label" for="garden">Garden</label>
                                    </div>
                                    <div class="feature-item">
                                        <input type="checkbox" class="form-check-input" id="security">
                                        <label class="form-check-label" for="security">Security</label>
                                    </div>
                                    <div class="feature-item">
                                        <input type="checkbox" class="form-check-input" id="wifi">
                                        <label class="form-check-label" for="wifi">Wi-Fi</label>
                                    </div>
                                    <div class="feature-item">
                                        <input type="checkbox" class="form-check-input" id="ac">
                                        <label class="form-check-label" for="ac">Air Conditioning</label>
                                    </div>
                                    <div class="feature-item">
                                        <input type="checkbox" class="form-check-input" id="balcony">
                                        <label class="form-check-label" for="balcony">Balcony</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 preview-column">
                            <div class="property-preview">
                                <h4 class="section-title"><i class="fas fa-eye"></i> Property Preview</h4>
                                
                                <div class="preview-image-container mb-4">
                                    <div class="ratio ratio-16x9 bg-light border rounded d-flex align-items-center justify-content-center">
                                        <i class="fas fa-home fa-3x text-muted"></i>
                                    </div>
                                </div>
                                
                                <h3 class="preview-title" id="previewTitle">Property Title</h3>
                                <div class="preview-price" id="previewPrice">KES 0.00</div>
                                
                                <div class="preview-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span id="previewLocation">Location not specified</span>
                                </div>
                                
                                <p class="preview-description" id="previewDescription">
                                    Property description will appear here once entered.
                                </p>
                                
                                <div class="preview-details">
                                    <div class="detail-item">
                                        <div class="detail-icon"><i class="fas fa-bed"></i></div>
                                        <div class="detail-label">Bedrooms</div>
                                        <div class="detail-value" id="previewBedrooms">0</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-icon"><i class="fas fa-bath"></i></div>
                                        <div class="detail-label">Bathrooms</div>
                                        <div class="detail-value" id="previewBathrooms">0</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-icon"><i class="fas fa-ruler-combined"></i></div>
                                        <div class="detail-label">Area</div>
                                        <div class="detail-value" id="previewArea">0 sqft</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-icon"><i class="fas fa-building"></i></div>
                                        <div class="detail-label">Type</div>
                                        <div class="detail-value" id="previewType">-</div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-submit w-100">
                                        <i class="fas fa-plus-circle me-2"></i>Add Property
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Update preview in real-time
        document.querySelectorAll('input[name="title"], input[name="location"], input[name="price"], textarea[name="description"]').forEach(input => {
            input.addEventListener('input', updatePreview);
        });
        
        function updatePreview() {
            document.getElementById('previewTitle').textContent = 
                document.querySelector('input[name="title"]').value || 'Property Title';
            
            document.getElementById('previewLocation').textContent = 
                document.querySelector('input[name="location"]').value || 'Location not specified';
            
            const price = document.querySelector('input[name="price"]').value;
            document.getElementById('previewPrice').textContent = 
                price ? `KES ${parseFloat(price).toLocaleString('en-KE', {minimumFractionDigits: 2})}` : 'KES 0.00';
            
            document.getElementById('previewDescription').textContent = 
                document.querySelector('textarea[name="description"]').value || 
                'Property description will appear here once entered.';
        }
        
        // Image upload preview
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const imagePreview = document.getElementById('imagePreview');
        
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            imagePreview.innerHTML = '';
            if (this.files.length > 10) {
                alert('Maximum 10 images allowed');
                return;
            }
            
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                if (!file.type.match('image.*')) continue;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="remove-image" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </div>
                    `;
                    imagePreview.appendChild(previewItem);
                }
                reader.readAsDataURL(file);
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
            
            fileInput.files = e.dataTransfer.files;
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        });
        
        // Initialize preview
        updatePreview();
        
        // Console message
        console.log("Prime Estate Property Management");
        console.log("PHP functionality preserved - form processing intact");
    </script>
</body>
</html>