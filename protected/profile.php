<?php
require("../class/auth.php");

$auth = new Auth(new Database());
$conn = $auth->getConnection();

// ✅ Check login
if (!$auth->checkLogin()) {
    echo "<p class='text-warning'>Invalid user ID. Please login again.</p>";
    header("Location:../public/login");
    exit;
}

$user_id = $auth->getUserId();

// ✅ Fetch user basic details
$getuser = $conn->prepare("SELECT * FROM user_profile WHERE id = ? AND verified = 1");
$getuser->bind_param("i", $user_id);

if ($getuser->execute()) {
    $result = $getuser->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // ✅ User image check
        $user_image = $user['user_image'] ?? '';
        $extension = strtolower(pathinfo($user_image, PATHINFO_EXTENSION));
        $image_extension  = ['jpg','jpeg','png']; 

        if (!in_array($extension, $image_extension) || empty($user_image)) {
            $image = "https://placehold.co/400";  
        } else {
            $image = $user_image;
        }

        include("../contents/user-details.php");

    } else {
        echo "<p class='text-danger'>User not found or not verified.</p>";
    }
} else {
    echo "<p class='text-danger'>Database query failed: " . htmlspecialchars($getuser->error) . "</p>";
}


// ✅ Fetch extra info
$getuser = $conn->prepare("SELECT * FROM user_information WHERE sid = ? ORDER BY id DESC LIMIT 1");
$getuser->bind_param("i", $_SESSION['user_id']);
if ($getuser->execute()) {
    $userResult = $getuser->get_result();
    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        
        // Assign fields safely
        $about = $user['about'] ?? 'N/A';
        $service = $user['service'] ?? 'N/A'; 
        $user_phone = $user['phone_number'] ?? 'N/A';
        $business_name = $user['name'] ?? 'N/A';
        $user_address = $user['user_address'] ?? 'N/A';
        $pricing = $user['pricing'] ?? 'N/A';
        $bank_name = $user['bank_name'] ?? 'N/A';
        $account_number = $user['account_number'] ?? 'N/A';
        $whatsapp = $user['whatsapp'] ?? 'N/A'; 
        $state = $user['state'] ?? 'N/A';
        $lga = $user['lga'] ?? 'N/A';
        $facebook = $user['facebook'] ?? 'N/A';
        $twitter = $user['twitter'] ?? 'N/A';
        $linkedin = $user['linkedin'] ?? 'N/A';
        $instagram = $user['instagram'] ?? 'N/A';
        $day = $user['day'] ?? 'N/A';
        $opening_time = $user['opening_time'] ?? 'N/A';
        $closing_time = $user['closing_time'] ?? 'N/A';
    }
}

?>

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
    
<?php include("components/overview.php"); ?>
    
<div class="container-fluid py-4">
    <!-- Tabs -->
    <div class="text-center mb-4">
        <div class="btn-group">
            <button class="btn btn-primary tablinks" id="defaultOpen" onclick="openTab(event,'London')">My Profile</button>
            <button class="btn btn-secondary tablinks" onclick="openTab(event,'Paris')">Edit Profile</button>
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
</div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
function openTab(evt, tabName) {
    $(".tabcontent").hide(); // hide all
    $(".tablinks").removeClass("active"); // remove active
    $("#" + tabName).show(); // show clicked tab
    evt.currentTarget.classList.add("active");
}
document.getElementById("defaultOpen").click();
</script>

<script>
$('.location').on('change', function () {
    var location = $(this).val();
    $.post("../engine/get-lga", { location: location }, function (data) {
        $('#lg').html(data);
    });
});

$('#editpage-details').on('submit', function (e) {
    e.preventDefault();
    $("#loading-image").show();

    $.ajax({
        type: "POST",
        url: "../engine/edit-page",
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
            $("#loading-image").hide();
            if (response.status === "success") {
                Swal.fire("Success", response.message, "success");
                $("#editpage-details")[0].reset();
            } else {
                Swal.fire("Error", response.message || "Unknown error.", "error");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $("#loading-image").hide();
            Swal.fire("Error", "Request failed: " + textStatus, "error");
        }
    });
});

function cancel() {
    $("#editpage-details")[0].reset();
}

$('#editpage-form').on('submit', function (e) {
    e.preventDefault();
    if (!confirm("Are you sure you want to change this?")) return;

    $("#loading-image").show();
    
    $.ajax({
        type: "POST",
        url: "../changeprofilepic",
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function (response) {
            $("#loading-image").hide();
            if (response.includes("1")) {
                Swal.fire("Success", "Image has been changed", "success");
                $("#editpage-form")[0].reset();
            } else {
                Swal.fire("Error", response, "error");
            }
        },
        error: function (xhr, status, error) {
            $("#loading-image").hide();
            Swal.fire("Error", "Request failed: " + error, "error");
        }
    });
});
</script>
</body>
</html>
