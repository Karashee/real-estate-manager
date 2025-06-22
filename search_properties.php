<?php
session_start();
require 'db.php';

// Only allow tenant
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

// Search Logic
$search_term = $_GET['search'] ?? '';
$availability = $_GET['available'] ?? '';
$params = [];
$sql = "SELECT * FROM properties WHERE 1=1";

if (!empty($search_term)) {
    $sql .= " AND (title ILIKE :term OR location ILIKE :term)";
    $params[':term'] = "%$search_term%";
}

if ($availability === 'available') {
    $sql .= " AND available = TRUE";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Home | Prime Estates</title>
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
        
        .search-header {
            background: linear-gradient(rgba(44, 62, 80, 0.85), rgba(44, 62, 80, 0.9)), 
                        url('https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
            border-radius: 0 0 20px 20px;
        }
        
        .navbar {
            background: transparent;
            padding: 1rem 2rem;
            position: absolute;
            width: 100%;
            top: 0;
            z-index: 100;
        }
        
        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .search-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }
        
        .search-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .search-title i {
            margin-right: 12px;
            color: var(--accent);
        }
        
        .filter-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .filter-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .filter-title i {
            margin-right: 10px;
            color: var(--accent);
        }
        
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 2rem;
        }
        
        .property-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .property-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .property-header {
            position: relative;
            height: 220px;
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
            font-size: 1.1rem;
        }
        
        .property-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
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
            flex-grow: 1;
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
            margin-top: auto;
        }
        
        .btn-apply {
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 0;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-apply:hover {
            background: #16a085;
            transform: translateY(-2px);
        }
        
        .btn-apply:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        
        .no-results {
            background: white;
            border-radius: 15px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            grid-column: 1 / -1;
        }
        
        .no-results i {
            font-size: 4rem;
            color: #e9ecef;
            margin-bottom: 1.5rem;
        }
        
        .no-results h3 {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .no-results p {
            color: #868e96;
            max-width: 500px;
            margin: 0 auto 2rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 3rem;
        }
        
        .page-link {
            color: var(--accent);
            border: 1px solid #dee2e6;
            margin: 0 5px;
            border-radius: 8px !important;
        }
        
        .page-link:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }
        
        .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 1.5rem;
        }
        
        .filter-group {
            margin-bottom: 1rem;
        }
        
        .filter-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .range-slider {
            width: 100%;
            height: 5px;
            margin-top: 20px;
        }
        
        .range-values {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .tag {
            display: inline-block;
            background: #e8f4fd;
            color: var(--secondary);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 0 5px 5px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .tag:hover {
            background: var(--secondary);
            color: white;
        }
        
        .tag.active {
            background: var(--secondary);
            color: white;
        }
        
        .sort-options {
            display: flex;
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        
        .sort-btn {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .sort-btn.active, .sort-btn:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }
        
        @media (max-width: 992px) {
            .properties-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .search-card {
                padding: 1.5rem;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
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
    
    <!-- Search Header -->
    <div class="search-header">
        <div class="container text-center">
            <h1 class="display-5 fw-bold mb-3">Find Your Dream Home</h1>
            <p class="lead mb-4">Search through our premium collection of available properties</p>
        </div>
    </div>
    
    <div class="search-container">
        <div class="search-card">
            <h2 class="search-title"><i class="fas fa-search me-2"></i>Property Search</h2>
            
            <form method="GET">
                <div class="filter-section">
                    <h4 class="filter-title"><i class="fas fa-filter me-2"></i>Search Filters</h4>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Search by Location or Property Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Enter location, property name, or keyword" 
                                           value="<?= htmlspecialchars($search_term) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Availability</label>
                                <select name="available" class="form-select">
                                    <option value="">All Properties</option>
                                    <option value="available" <?= $availability === 'available' ? 'selected' : '' ?>>Available Only</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Price Range (KES)</label>
                            <input type="range" class="form-range range-slider" min="0" max="500000" step="5000" value="250000">
                            <div class="range-values">
                                <span>0</span>
                                <span>250,000</span>
                                <span>500,000+</span>
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <label>Property Type</label>
                            <div>
                                <span class="tag">All</span>
                                <span class="tag">Apartment</span>
                                <span class="tag">House</span>
                                <span class="tag">Villa</span>
                                <span class="tag">Townhouse</span>
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <label>Bedrooms</label>
                            <div>
                                <span class="tag">Any</span>
                                <span class="tag">1</span>
                                <span class="tag">2</span>
                                <span class="tag">3+</span>
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <label>Bathrooms</label>
                            <div>
                                <span class="tag">Any</span>
                                <span class="tag">1</span>
                                <span class="tag">2</span>
                                <span class="tag">3+</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="search_properties.php" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-sync me-2"></i>Reset
                            </a>
                        </div>
                        <div class="sort-options">
                            <span class="me-2">Sort by:</span>
                            <button type="button" class="sort-btn active">Newest</button>
                            <button type="button" class="sort-btn">Price Low to High</button>
                            <button type="button" class="sort-btn">Price High to Low</button>
                        </div>
                    </div>
                </div>
            </form>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-home me-2 text-primary"></i>
                    <?php if ($results): ?>
                        <?= count($results) ?> Properties Found
                    <?php else: ?>
                        No Properties Found
                    <?php endif; ?>
                </h4>
                <div>
                    <a href="tenant_dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
            
            <?php if ($results): ?>
                <div class="properties-grid">
                    <?php foreach ($results as $prop): ?>
                        <div class="property-card">
                            <div class="property-header">
                                <img src="https://images.unsplash.com/photo-<?= substr(md5($prop['id']), 0, 10) ?>?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                     alt="<?= htmlspecialchars($prop['title']) ?>" class="property-image">
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
                                    <?= htmlspecialchars($prop['description'] ?: 'Beautiful property in a prime location. Contact for more details.') ?>
                                </p>
                                
                                <div class="property-meta">
                                    <div class="meta-item">
                                        <div class="meta-icon">
                                            <i class="fas fa-bed"></i>
                                        </div>
                                        <div>
                                            <div class="meta-label">Bedrooms</div>
                                            <div class="meta-value">2</div>
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
                                    <a href="apply_property.php?property_id=<?= $prop['id'] ?>" 
                                       class="btn-apply <?= !$prop['available'] ? 'disabled' : '' ?>">
                                        <i class="fas fa-file-alt me-2"></i>
                                        <?= $prop['available'] ? 'Apply Now' : 'Not Available' ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h3>No Properties Found</h3>
                    <p>We couldn't find any properties matching your search criteria. Try adjusting your filters.</p>
                    <a href="search_properties.php" class="btn btn-primary">
                        <i class="fas fa-sync me-2"></i> Reset Search
                    </a>
                </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <nav class="pagination">
                <ul class="pagination">
                    <li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                </ul>
            </nav>
        </div>
    </div>

    <script>
        // Update tag selection
        document.querySelectorAll('.tag').forEach(tag => {
            tag.addEventListener('click', function() {
                // Remove active class from siblings
                this.parentElement.querySelectorAll('.tag').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Toggle active class on clicked tag
                this.classList.toggle('active');
            });
        });
        
        // Sort button functionality
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from siblings
                this.parentElement.querySelectorAll('.sort-btn').forEach(b => {
                    b.classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
            });
        });
        
        // Range slider value display
        const rangeSlider = document.querySelector('.range-slider');
        const rangeValue = document.querySelector('.range-values span:nth-child(2)');
        
        if (rangeSlider) {
            rangeSlider.addEventListener('input', function() {
                const value = parseInt(this.value).toLocaleString('en-KE');
                rangeValue.textContent = value;
            });
        }
        
        // Apply button disabled state
        document.querySelectorAll('.btn-apply.disabled').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('This property is not currently available for rent.');
            });
        });
    </script>
</body>
</html>