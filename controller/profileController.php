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
