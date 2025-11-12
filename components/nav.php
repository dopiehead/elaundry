<!-- Navigation -->
<?php 
require_once __DIR__ . '/../class/auth.php';
$auth = new Auth(new Database());
$conn = $auth->getConnection();
$user_id = $auth->isLoggedIn();
?>
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index"></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="index">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="how-it-works">How it works</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="services" role="button" data-bs-toggle="dropdown">
                        Services
                    </a>
                    <ul class="dropdown-menu shadow-sm">
                        <li><a class="dropdown-item" href="services?service=wash&fold">Wash & Fold</a></li>
                        <li><a class="dropdown-item" href="services?service=dry cleaning">Dry Cleaning</a></li>
                        <li><a class="dropdown-item" href="services?service=express service">Express Service</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="locations">Locations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact">Contact us</a>
                </li>

                <!-- Auth Buttons -->
                <ul class="nav-item list-unstyled d-flex ms-lg-3 gap-2">
                    
                  <?php if(!$user_id): ?>
                    <li>
                        <a class="nav-link login-btn" href="login">Login</a>
                    </li>
                    <li>
                        <a class="nav-link signup-btn text-white" href="create-account">Sign up</a>
                    </li>
                  <?php else : ?>
                    <li>
                    <a class="nav-link signup-btn text-white" href="protected/profile">Profile</a>
                    </li>
                  <?php endif ?>
                    
                </ul>
            </ul>
        </div>
    </div>
</nav>
