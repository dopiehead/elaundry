<?php include("../controller/profileController.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>

    <!-- Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard/sidebar.css">
    <link rel="stylesheet" href="../assets/css/dashboard/overview.css">
    <link rel="stylesheet" href="../assets/css/dashboard/settings.css">
    <link rel="stylesheet" href="../assets/css/pricingcontroller.css">
    <link rel="stylesheet" href="../assets/css/pricingcontroller.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ✅ Tabs fix */
        .tabcontent { display: none; }
        .tablinks.active { background-color: #0d6efd; color: #fff; }
    </style>
</head>
<body>
 

<?php include("components/sidebar.php"); ?>

<div class="main-content">
    
<?php
 $overview = "Profile";
include("components/overview.php");
 ?>
    
<div class="container-fluid py-4">
    <!-- Tabs -->
    <div class="text-center mb-4">
        <div class="btn-group">
            <button class="btn btn-primary tablinks" id="defaultOpen" onclick="openTab(event,'London')">My Profile</button>
            <button class="btn btn-secondary tablinks" onclick="openTab(event,'Paris')">Edit Profile</button>
            <button class="btn btn-warning tablinks" onclick="openTab(event,'Lagos')">Add Prices</button>

        </div>
    </div>

    <!-- My Profile -->
    <div id="London" class="tabcontent">
        <div class="card p-4 mb-4">
            <h5 class="card-title border-bottom pb-2">Personal Details</h5>
            <div class="card-body">
                <p><small>Name: <?= htmlspecialchars($user_name) ?></small></p>
                <p><small>Email: <?= htmlspecialchars($user_email) ?></small></p>
                <p><small>Phone: <?= htmlspecialchars($user_phone ?? "") ?></small></p>
                <p><small>Dial code: +234</small></p>

                <form id="editpage-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user_id ?? "" ) ?>">
                    <div class="mb-3">
                        <label for="fileupload" class="form-label">Change Photo</label>
                        <input type="file" class="form-control"  accept="image/*" name="fileupload" id="fileupload">
                    </div>
                    <button type="submit" class="btn btn-success">Change photo (Max 4MB)</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Profile -->
    <div id="Paris" class="tabcontent">
        <?php include("../components/edit-profile-form.php"); ?>
    </div>

    <div id="Lagos" class='tabcontent'>
        <?php include("../controller/pricingController.php") ?>
    </div>

    
</div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src='../assets/js/profile.js'></script>
</body>
</html>
