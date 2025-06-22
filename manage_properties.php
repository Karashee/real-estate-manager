<?php
session_start();
require 'db.php';

// Only allow manager
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}


// UPDATE Property
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $title = $_POST['title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $available = isset($_POST['available']) ? true : false;

    $stmt = $pdo->prepare("UPDATE properties SET title = ?, location = ?, price = ?, description = ?, available = ? WHERE id = ?");
    $stmt->execute([$title, $location, $price, $description, $available, $id]);
    header("Location: manage_properties.php");
    exit();
}

// Fetch all properties
$properties = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties | Prime Estates</title>
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
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 15px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .page-title {
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            margin-right: 12px;
            color: var(--accent);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .property-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        .property-header {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .property-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .property-card:hover .property-image {
            transform: scale(1.05);
        }
        
        .property-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-available {
            background: rgba(46, 204, 113, 0.15);
            color: #27ae60;
        }
        
        .status-occupied {
            background: rgba(231, 76, 60, 0.15);
            color: #c0392b;
        }
        
        .property-price {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(44, 62, 80, 0.85);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 700;
        }
        
        .property-body {
            padding: 20px;
        }
        
        .property-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .property-location {
            display: flex;
            align-items: center;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .property-location i {
            margin-right: 8px;
        }
        
        .property-description {
            color: #495057;
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 0.95rem;
            max-height: 80px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        
        .property-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
        }
        
        .meta-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(26, 188, 156, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: var(--accent);
        }
        
        .meta-label {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .meta-value {
            font-weight: 600;
            font-size: 1.05rem;
        }
        
        .property-actions {
            display: flex;
            gap: 10px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .btn-edit {
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-edit:hover {
            background: #16a085;
            transform: translateY(-2px);
        }
        
        
        
        
        
        
        .edit-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1050;
            align-items: center;
            justify-content: center;
        }
        
        .edit-modal-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            overflow: hidden;
            animation: modalAppear 0.3s ease;
        }
        
        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            background: linear-gradient(to right, var(--accent), #16a085);
            color: white;
            padding: 1.5rem;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
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
            width: 100%;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.25rem rgba(26, 188, 156, 0.25);
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn-save {
            background: var(--accent);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-save:hover {
            background: #16a085;
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .no-properties {
            background: white;
            border-radius: 15px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        
        .no-properties i {
            font-size: 4rem;
            color: #e9ecef;
            margin-bottom: 1.5rem;
        }
        
        .no-properties h3 {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .no-properties p {
            color: #868e96;
            max-width: 500px;
            margin: 0 auto 2rem;
        }
        
        .property-id {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--accent);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        @media (max-width: 992px) {
            .properties-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .action-buttons {
                width: 100%;
            }
            
            .btn-action {
                flex: 1;
                text-align: center;
            }
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
        <div class="dashboard-header">
            <h1 class="page-title"><i class="fas fa-building"></i> Manage Property Listings</h1>
            <div class="action-buttons">
                <a href="add_property.php" class="btn btn-primary btn-action">
                    <i class="fas fa-plus-circle me-2"></i> Add New Property
                </a>
                <a href="manager_dashboard.php" class="btn btn-outline-secondary btn-action">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
        <?php if ($properties): ?>
            <div class="properties-grid">
                <?php foreach ($properties as $prop): ?>
                    <div class="property-card">
                        <div class="property-header">
                            
                                 
                            <div class="property-id">#PR-<?= $prop['id'] ?></div>
                            <div class="property-status <?= $prop['available'] ? 'status-available' : 'status-occupied' ?>">
                                <?= $prop['available'] ? 'Available' : 'Occupied' ?>
                            </div>
                            <div class="property-price">KES <?= number_format($prop['price'], 2) ?></div>
                        </div>
                        <div class="property-body">
                            <h3 class="property-title"><?= htmlspecialchars($prop['title']) ?></h3>
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($prop['location']) ?>
                            </div>
                            <p class="property-description">
                                <?= htmlspecialchars($prop['description'] ?: 'No description provided') ?>
                            </p>
                            
                            <div class="property-meta">
                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-bed"></i>
                                    </div>
                                    <div>
                                        <div class="meta-label">Bedrooms</div>
                                        <div class="meta-value">3</div>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-bath"></i>
                                    </div>
                                    <div>
                                        <div class="meta-label">Bathrooms</div>
                                        <div class="meta-value">2</div>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-ruler-combined"></i>
                                    </div>
                                    <div>
                                        <div class="meta-label">Area</div>
                                        <div class="meta-value">1,250 sqft</div>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <div class="meta-label">Listed</div>
                                        <div class="meta-value"><?= date('M d, Y', strtotime($prop['created_at'])) ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="property-actions">
                                <button class="btn-edit" data-id="<?= $prop['id'] ?>"
                                        data-title="<?= htmlspecialchars($prop['title']) ?>"
                                        data-location="<?= htmlspecialchars($prop['location']) ?>"
                                        data-price="<?= htmlspecialchars($prop['price']) ?>"
                                        data-description="<?= htmlspecialchars($prop['description']) ?>"
                                        data-available="<?= $prop['available'] ? 'true' : 'false' ?>">
                                    <i class="fas fa-edit me-2"></i> Edit
                                </button>
                                
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-properties">
                <i class="fas fa-building"></i>
                <h3>No Properties Found</h3>
                <p>You haven't added any properties yet. Start by adding your first property to list it for rent or sale.</p>
                <a href="add_property.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle me-2"></i> Add Your First Property
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Edit Property Modal -->
    <div class="edit-modal" id="editModal">
        <div class="edit-modal-content">
            <form method="POST" id="editForm">
                <div class="modal-header">
                    <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Property</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="editId">
                    
                    <div class="form-group">
                        <label class="form-label">Property Title</label>
                        <input type="text" name="title" id="editTitle" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" id="editLocation" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Price (KES)</label>
                                <input type="number" step="0.01" name="price" id="editPrice" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Availability</label>
                                <div class="d-flex align-items-center">
                                    <label class="switch">
                                        <input type="checkbox" name="available" id="editAvailable">
                                        <span class="slider"></span>
                                    </label>
                                    <span class="ms-3" id="availabilityLabel">Available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="4" placeholder="Property description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="closeModal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save me-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Edit Modal Functionality
        const editModal = document.getElementById('editModal');
        const editButtons = document.querySelectorAll('.btn-edit');
        const closeModal = document.getElementById('closeModal');
        const availabilityLabel = document.getElementById('availabilityLabel');
        const availabilityToggle = document.getElementById('editAvailable');
        
        // Open modal when edit button is clicked
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const location = this.getAttribute('data-location');
                const price = this.getAttribute('data-price');
                const description = this.getAttribute('data-description');
                const available = this.getAttribute('data-available') === 'true';
                
                document.getElementById('editId').value = id;
                document.getElementById('editTitle').value = title;
                document.getElementById('editLocation').value = location;
                document.getElementById('editPrice').value = price;
                document.getElementById('editDescription').value = description;
                document.getElementById('editAvailable').checked = available;
                
                availabilityLabel.textContent = available ? 'Available' : 'Occupied';
                
                editModal.style.display = 'flex';
            });
        });
        
        // Close modal
        closeModal.addEventListener('click', function() {
            editModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
        });
        
        // Update availability label when toggle changes
        availabilityToggle.addEventListener('change', function() {
            availabilityLabel.textContent = this.checked ? 'Available' : 'Occupied';
        });
        
        
    </script>
</body>
</html>