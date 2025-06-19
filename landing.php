<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Estate | Real Estate Management</title>
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
            overflow-x: hidden;
        }
        
        .navbar {
            background: linear-gradient(to right, var(--primary), var(--dark));
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
        }
        
        .hero {
            background: linear-gradient(rgba(44, 62, 80, 0.85), rgba(44, 62, 80, 0.9)), url('https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 5rem 0;
            border-radius: 0 0 20px 20px;
            margin-bottom: 4rem;
        }
        
        .btn-primary {
            background-color: var(--secondary);
            border: none;
            padding: 0.75rem 2rem;
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
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background-color: #16a085;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            border: none;
        }
        
        .feature-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            background: var(--accent);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }
        
        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 3rem;
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
        
        .property-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            background: white;
        }
        
        .property-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .property-img {
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .property-card:hover .property-img {
            transform: scale(1.05);
        }
        
        .price-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent);
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .stats-banner {
            background: linear-gradient(to right, var(--secondary), var(--accent));
            color: white;
            padding: 4rem 0;
            border-radius: 20px;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            margin: 1.5rem 0;
        }
        
        .testimonial-card:before {
            content: """;
            position: absolute;
            top: -25px;
            left: 20px;
            font-size: 6rem;
            color: var(--accent);
            opacity: 0.1;
            font-family: Georgia, serif;
        }
        
        .testimonial-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--accent);
        }
        
        .contact-form {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
        }
        
        .footer {
            background: linear-gradient(to right, var(--primary), var(--dark));
            color: white;
            padding: 4rem 0 2rem;
            margin-top: 5rem;
        }
        
        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background: var(--accent);
            transform: translateY(-5px);
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
        
        .highlight {
            color: var(--accent);
            font-weight: 700;
        }
        
        .cta-section {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.95)), url('https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            border-radius: 20px;
            padding: 5rem 0;
            color: white;
            margin: 4rem 0;
        }
        
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .feature-icon {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1);
            background: var(--secondary);
        }
        
        @media (max-width: 768px) {
            .hero {
                padding: 3rem 0;
            }
            
            .stat-number {
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
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Properties</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Agents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3 animate-on-scroll">🏡 Find Your Dream Property</h1>
            <p class="lead mb-4 animate-on-scroll">Discover the perfect home from our premium collection of properties</p>
            <div class="mt-4 animate-on-scroll">
                <a href="login.php" class="btn btn-primary btn-lg me-3"><i class="fas fa-sign-in-alt me-2"></i>Login</a>
                <a href="register.php" class="btn btn-success btn-lg"><i class="fas fa-user-plus me-2"></i>Register</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features py-5">
        <div class="container">
            <h2 class="text-center section-title mb-5 animate-on-scroll">Why Choose Us</h2>
            <div class="row g-4">
                <div class="col-md-4 animate-on-scroll">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-search-location"></i>
                        </div>
                        <h3 class="mb-3">Advanced Search</h3>
                        <p class="mb-0">Find properties based on your specific criteria with our powerful search tools and filters.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-on-scroll">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <h3 class="mb-3">Best Prices</h3>
                        <p class="mb-0">Get the best market prices with our competitive rates and transparent pricing policy.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-on-scroll">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="mb-3">24/7 Support</h3>
                        <p class="mb-0">Our dedicated support team is available around the clock to assist with your needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Properties Section -->
    <section class="properties py-5 bg-light">
        <div class="container">
            <h2 class="text-center section-title mb-5 animate-on-scroll">Featured Properties</h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="property-card">
                        <div class="position-relative">
                            <img src="https://images.unsplash.com/photo-1575517111839-3a3843ee7f5d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Modern Apartment" class="property-img w-100">
                            <div class="price-tag">$350,000</div>
                        </div>
                        <div class="p-4">
                            <h4 class="mb-2">Luxury Downtown Apartment</h4>
                            <p class="text-muted"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Downtown, New York</p>
                            <div class="d-flex justify-content-between border-top pt-3">
                                <span><i class="fas fa-bed me-2"></i>3 Beds</span>
                                <span><i class="fas fa-bath me-2"></i>2 Baths</span>
                                <span><i class="fas fa-ruler-combined me-2"></i>1,250 sq.ft.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="property-card">
                        <div class="position-relative">
                            <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Suburban House" class="property-img w-100">
                            <div class="price-tag">$675,000</div>
                        </div>
                        <div class="p-4">
                            <h4 class="mb-2">Spacious Family Home</h4>
                            <p class="text-muted"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Green Valley, California</p>
                            <div class="d-flex justify-content-between border-top pt-3">
                                <span><i class="fas fa-bed me-2"></i>4 Beds</span>
                                <span><i class="fas fa-bath me-2"></i>3 Baths</span>
                                <span><i class="fas fa-ruler-combined me-2"></i>2,800 sq.ft.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="property-card">
                        <div class="position-relative">
                            <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Waterfront Villa" class="property-img w-100">
                            <div class="price-tag">$1,250,000</div>
                        </div>
                        <div class="p-4">
                            <h4 class="mb-2">Waterfront Luxury Villa</h4>
                            <p class="text-muted"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Ocean View, Florida</p>
                            <div class="d-flex justify-content-between border-top pt-3">
                                <span><i class="fas fa-bed me-2"></i>5 Beds</span>
                                <span><i class="fas fa-bath me-2"></i>4.5 Baths</span>
                                <span><i class="fas fa-ruler-combined me-2"></i>4,200 sq.ft.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5 animate-on-scroll">
                <a href="#" class="btn btn-outline-primary btn-lg">View All Properties</a>
            </div>
        </div>
    </section>

    <!-- Stats Banner -->
    <section class="stats-banner py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4 mb-md-0 animate-on-scroll">
                    <div class="stat-number">5,000+</div>
                    <p class="mb-0">Properties Listed</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0 animate-on-scroll">
                    <div class="stat-number">98%</div>
                    <p class="mb-0">Client Satisfaction</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0 animate-on-scroll">
                    <div class="stat-number">25+</div>
                    <p class="mb-0">Years Experience</p>
                </div>
                <div class="col-md-3 animate-on-scroll">
                    <div class="stat-number">200+</div>
                    <p class="mb-0">Professional Agents</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials py-5">
        <div class="container">
            <h2 class="text-center section-title mb-5 animate-on-scroll">What Our Clients Say</h2>
            <div class="row">
                <div class="col-md-4 animate-on-scroll">
                    <div class="testimonial-card">
                        <p class="mb-4">"Prime Estate helped me find my dream home in just two weeks! Their agents are professional and truly care about their clients."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Client" class="testimonial-img me-3">
                            <div>
                                <h5 class="mb-0">Sarah Johnson</h5>
                                <p class="text-muted mb-0">Home Owner</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate-on-scroll">
                    <div class="testimonial-card">
                        <p class="mb-4">"The entire process was smooth and transparent. I got a great deal on my new apartment thanks to their negotiation skills."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/men/54.jpg" alt="Client" class="testimonial-img me-3">
                            <div>
                                <h5 class="mb-0">Michael Torres</h5>
                                <p class="text-muted mb-0">Property Investor</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate-on-scroll">
                    <div class="testimonial-card">
                        <p class="mb-4">"As a first-time buyer, I was nervous about the process. Their team guided me through every step and made it stress-free."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Client" class="testimonial-img me-3">
                            <div>
                                <h5 class="mb-0">Jennifer Lee</h5>
                                <p class="text-muted mb-0">New Home Owner</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4 animate-on-scroll">Ready to Find Your Dream Home?</h2>
            <p class="lead mb-5 animate-on-scroll">Join thousands of satisfied clients who found their perfect property with us</p>
            <div class="animate-on-scroll">
                <a href="register.php" class="btn btn-success btn-lg me-3">Create Account</a>
                <a href="login.php" class="btn btn-light btn-lg">Sign In</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h3 class="mb-4"><i class="fas fa-home me-2"></i>PRIME ESTATE</h3>
                    <p class="mb-4">Your trusted partner in real estate. We connect buyers and sellers with the perfect properties across the nation.</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white">Home</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Properties</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Agents</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Services</a></li>
                        <li><a href="#" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="mb-4">Services</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white">Buying</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Selling</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Renting</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Property Management</a></li>
                        <li><a href="#" class="text-white">Consultation</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="mb-4">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> 123 Estate Ave, New York, NY 10001</li>
                        <li class="mb-3"><i class="fas fa-phone me-2"></i> +1 (555) 123-4567</li>
                        <li class="mb-3"><i class="fas fa-envelope me-2"></i> info@primeestate.com</li>
                        <li><i class="fas fa-clock me-2"></i> Mon-Fri: 9AM - 6PM</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-5 mb-4" style="background: rgba(255,255,255,0.2);">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="mb-0">&copy; 2023 Prime Estate. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Designed with <i class="fas fa-heart text-danger"></i> for real estate professionals</p>
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
        
        // Animation on scroll
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;
                
                if (elementPosition < screenPosition) {
                    element.classList.add('visible');
                }
            });
        };
        
        window.addEventListener('scroll', animateOnScroll);
        window.addEventListener('load', animateOnScroll);
        
        // Simple property search simulation
        document.querySelectorAll('.property-card').forEach(card => {
            card.addEventListener('click', function() {
                const propertyTitle = this.querySelector('h4').textContent;
                alert(`You clicked on: ${propertyTitle}\nThis would navigate to property details in a full application.`);
            });
        });
        
        // Console welcome message
        console.log("Welcome to Prime Estate Management System!");
        console.log("This is a frontend demonstration. The PHP login/register functionality is preserved.");
    </script>
</body>
</html>