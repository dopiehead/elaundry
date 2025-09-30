<?php
// verify-user.php
session_start();
require_once("../class/auth.php"); // include your DB connection
$auth = new Auth(new Database);
$conn = $auth->getConnection();
$user_id = $auth->getUserId();
if(!$auth->isLoggedIn()){
    header("Location:login");
    exit;
}
// ✅ Check if vkey is passed
if (!isset($_GET['vkey']) || empty($_GET['vkey'])) {
    die("Invalid verification link.");
}

$vkey = $conn->real_escape_string($_GET['vkey']);

// ✅ Prepare query to check user
$stmt = $conn->prepare("SELECT id, verified FROM user_profile WHERE vkey = ? LIMIT 1");
if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("s", $vkey);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
   
    echo "<h3>Invalid verification link.</h3>";
    exit;
}

$user = $result->fetch_assoc();

if ((int)$user['verified'] === 1) {
    // ✅ Already verified
    echo "<h3>Your account is already verified. You can <a href='login'>login here</a>.</h3>";
} else {
    // ✅ Verify now
    $update = $conn->prepare("UPDATE user_profile SET verified = 1 WHERE vkey = ? LIMIT 1");
    $update->bind_param("s", $vkey);
    if ($update->execute()) {
        echo "<h3>Your account has been verified successfully! 🎉</h3>";
        echo "<p><a href='login'>Click here to login</a></p>";
    } else {
        echo "<h3>Error verifying your account. Please try again later.</h3>";
    }
    $update->close();
}

$stmt->close();
$conn->close();

