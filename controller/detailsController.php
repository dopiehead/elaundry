<?php
// ✅ Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);

ini_set('display_errors', 1);


// ✅ Initialize Auth & Database
$auth    = new Auth(new Database());
$user_id = $auth->getUserId();       // Current logged-in user ID
$conn    = $auth->getConnection();   // Database connection

// ✅ Get user ID from URL (sanitize & cast to int)
$id = isset($_GET['id']) && !empty($_GET['id']) ? (int) $_GET['id'] : null;

if ($id) {

    // 🔄 Increment profile views (only for verified users)
    $updateviews = $conn->prepare("UPDATE user_profile SET user_views = user_views + 1 WHERE id = ? AND verified = 1");
    $updateviews->bind_param("i", $id);  // ✅ corrected from bid_param → bind_param
    $updateviews->execute();

    // 📌 Fetch user profile (only if verified)
    $getuser = $conn->prepare("SELECT * FROM user_profile WHERE id = ? AND verified = 1");
    $getuser->bind_param("i", $id);

    if ($getuser->execute()) {
        $result = $getuser->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // 📄 Include template to display user details
            include("../contents/user-details.php");

            // 🖼️ Validate user profile image
            $extension = strtolower(pathinfo($user_image, PATHINFO_EXTENSION));
            $image_extension  = array('jpg', 'jpeg', 'png');

            if (!in_array($extension, $image_extension)) {
                // 🚫 If not a valid image extension → show placeholder
                $image = "https://placehold.co/400";  
            } else {
                // ✅ Valid image
                $image = $user_image;
            }
        } else {
            // ⚠️ No user found or not verified
            echo "<p class='text-danger'>User not found or not verified.</p>";
        }
    } else {
        // ❌ Query failed → show error
        echo "<p class='text-danger'>Database query failed: " . htmlspecialchars($getuser->error) . "</p>";
    }

    $getuser->close();
} else {
    // ⚠️ No valid user ID in request
    echo "<p class='text-warning'>Invalid user ID.</p>";
}


// ******** */ more pictures from artisan (static gallery example)
$galleries = [
    ['img'=>'../assets/images/laundry/image2.png', 'alt'=>'laundry man working'], 
    ['img'=>'../assets/images/laundry/image3.png', 'alt'=>'Barber shop interior'],  
    ['img'=>'../assets/images/laundry/image4.png', 'alt'=>'Hair styling'],
    ['img'=>'../assets/images/laundry/image.png',  'alt'=>'Barber tools'], 
    ['img'=>'../assets/images/laundry/image2.png', 'alt'=>'Customer service'],
    ['img'=>'../assets/images/laundry/image3.png', 'alt'=>'Barber shop']           
];

// 🔀 Slice arrays for specific gallery sections
$firstTwo  = array_slice($galleries, 0, 2);  // First 2 images
$lastThree = array_slice($galleries, -3);    // Last 3 images


// 📝 Fetch reviews/comments for artisan
$getcomment = $conn->prepare("
    SELECT sp_comment.*, user_profile.id AS user_id, user_profile.user_name AS user_name
    FROM sp_comment
    INNER JOIN user_profile ON sp_comment.user_id = user_profile.id
    WHERE sp_comment.user_id = ?
");

$getcomment->bind_param("i", $id);
$getcomment->execute();
$commentResult = $getcomment->get_result();
$countComments = $commentResult->num_rows;  


$redirectUrl = $_SERVER['REQUEST_URI'];
