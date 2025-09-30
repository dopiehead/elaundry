<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Service</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="../assets/css/locations.css">
    <link rel="stylesheet" href="../assets/css/how-it-works.css">
    <link rel="stylesheet" href="../assets/css/price-break-down.css">
    <link rel="stylesheet" href="../assets/css/groups.css">
    <link rel="stylesheet" href="../assets/css/video.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <style>
     
    </style>
</head>
<body>
     <?php include("../components/nav.php") ?>
     <?php include("../controller/indexController.php") ?>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Laundry today for your steeze tomorrow</h1>
                        <p class="hero-subtitle">Our Laundry service will wash, dry, and fold your laundry at an affordable price. Pickup and drop-off options available!</p>
                        
                        <a href='how-it-works' class="btn btn-how-works">How it works</a>
                        
                        <div class="experience-stats">
                            <div class="row">
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="stat-big">18m+</div>
                                        <div class="stat-desc">Happy<br>Customers</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="stat-small">10+</div>
                                        <div class="stat-desc">Years of<br>Experience</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-illustration">
                        <div class="purple-blob"></div>
                        
                        <div class="floating-bubbles">
                            <div class="bubble bubble-1"></div>
                            <div class="bubble bubble-2"></div>
                            <div class="bubble bubble-3"></div>
                            <div class="bubble bubble-4"></div>
                            <div class="bubble bubble-5"></div>
                            <div class="bubble bubble-6"></div>
                        </div>
                        
                        <div class="washing-machine">
                            <div class="machine-top">
                                <div class="machine-controls">
                                    <div class="control-dot"></div>
                                    <div class="control-dot"></div>
                                </div>
                            </div>
                            <div class="machine-door">
                                <div class="machine-inner"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= (int)$countWashers ?></div>
                        <div class="stat-label">Washers</div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Assurance</div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Confidence</div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= (int)$countUsers ?></div>
                        <div class="stat-label">Users Nationwide</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("../components/how-it-works.php")  ?>

    <!-- Services Section -->
    <!-- <section class="services-section section-padding py-5">
        <div class="container">
            <div class="section-subtitle">SERVICES</div>
            <h2 class="section-title">Services & Packages</h2>
            
            <div class="row">
                 Regular Package -->
                <!-- <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-header">
                            <div class="service-icon">
                                <i class="fas fa-tshirt"></i>
                            </div>
                            <div>
                                <h3 class="service-name">REGULAR</h3>
                                <p class="service-subtitle">Perfect for people who live alone.</p>
                            </div>
                        </div>
                        
                        <div class="service-features">
                            <h6>What's included</h6>
                            <ul class="feature-list">
                                <li>2 cloth per week</li>
                                <li>up to 10 lbs per cloth</li>
                            </ul>
                        </div>
                        
                        <div class="price-section">
                            <span class="price">10 NGN</span>
                            <span class="price-period">/per month</span>
                        </div>
                        
                        <button class="btn btn-choose secondary">Choose</button>
                    </div>
                </div>-->
                
                <!-- Brownies Package (Featured) -->
                <!-- <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card featured">
                        <div class="service-header">
                            <div class="service-icon">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div>
                                <h3 class="service-name">BROWNIES</h3>
                                <p class="service-subtitle">Perfect for couples size 2-6</p>
                            </div>
                        </div>
                        
                        <div class="service-features">
                            <h6>What's included</h6>
                            <ul class="feature-list">
                                <li>4 cloth per week</li>
                                <li>up to 12 lbs per cloth</li>
                                <li>Special garments</li>
                                <li>Pickup & drop off</li>
                            </ul>
                        </div>
                        
                        <div class="price-section">
                            <span class="price">20 NGN</span>
                            <span class="price-period">/per month</span>
                        </div>
                        
                        <button class="btn btn-choose primary">Choose</button>
                    </div>
                </div> -->
                
                <!-- Prime Package -->
                <!-- <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-header">
                            <div class="service-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <h3 class="service-name">PRIME</h3>
                                <p class="service-subtitle">Perfect for families size 4-6</p>
                            </div>
                        </div>
                        
                        <div class="service-features">
                            <h6>What's included</h6>
                            <ul class="feature-list">
                                <li>6 cloth per week</li>
                                <li>up to 15 lbs per cloth</li>
                                <li>Special garments</li>
                                <li>Pickup & drop off</li>
                                <li>Free detergent samples</li>
                            </ul>
                        </div>
                        
                        <div class="price-section">
                            <span class="price">30 NGN</span>
                            <span class="price-period">/per month</span>
                        </div>
                        
                        <button class="btn btn-choose secondary">Choose</button>
                    </div>
                </div>
            </div>
        </div>
    </section> -->  

    <?php include("../components/price-break-down.php") ?>
    <!-- Registration Section -->
    <section class="ls-registration-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-6">
                    <div class="ls-hero-content">
                        <h1 class="ls-hero-title">Become a registered Laundry service under e-wash</h1>
                        <p class="ls-hero-subtitle">Organize your clients with no marketing at all, just register under e-laundry</p>
                        <a href='create-account' class="btn ls-btn-register text-decoration-none">Register</a>
                    </div>
                </div>
                
                <div class="col-lg-5 col-md-6">
                    <div class="ls-hero-illustration">
                        <div class="ls-illustration-container">
                            <div class="ls-floating-elements">
                                <div class="ls-bubble-float ls-bubble-1"></div>
                                <div class="ls-bubble-float ls-bubble-2"></div>
                                <div class="ls-bubble-float ls-bubble-3"></div>
                                <div class="ls-bubble-float ls-bubble-4"></div>
                                <div class="ls-bubble-float ls-bubble-5"></div>
                            </div>
                            
                            <div class="ls-service-person">
                                <div class="ls-person-head">
                                    <div class="ls-person-cap"></div>
                                </div>
                                <div class="ls-person-body"></div>
                                <div class="ls-washing-machine-mini">
                                    <div class="ls-machine-door-mini"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("../components/locations.php") ?>

    <div class="container-fluid">
        <!-- Video Section -->
    
    <?php include("../components/video.php") ?>

        <!-- CTA Banner -->
        <section class="sv-cta-banner">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7">
                        <div class="sv-cta-content">
                            <h3 class="sv-cta-title">Do you want to register as an Errander or a Messenger?</h3>
                            <p class="sv-cta-subtitle">
                                Make More Revenue! St-Tek per day by just turning simple
                                and fast errands along your route... no hidden charges.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="sv-cta-image">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" 
                                 alt="Person" class="sv-person-avatar">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="container">
        <!-- Groups Section -->
        <?php include("../components/groups.php") ?>

        <!-- Promotional Banner -->
        <section class="gr-promo-banner">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <div class="gr-promo-content">
                        <h3 class="gr-promo-title">Hard time deciding what's best for you?</h3>
                        <p class="gr-promo-subtitle">Let us help you find the perfect solution for your needs</p>
                        <button class="btn gr-promo-btn">Show me</button>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="gr-promo-illustration">
                        <div class="gr-illustration-container">
                            <div class="gr-washing-machine">
                                <div class="gr-machine-door"></div>
                            </div>
                            <div class="gr-detergent-bottle"></div>
                            <div class="gr-basket"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    
        <!-- Footer Banner -->
      <?php include("../components/footer.php") ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>