<?php
require("../class/auth.php");

header('Content-Type: application/json');

$auth = new Auth(new Database());
$conn = $auth->getConnection();

// ✅ Collect & sanitize inputs
$vendor_name   = $conn->real_escape_string(trim($_POST['vendor_name'] ?? ""));
$vendor_email  = $conn->real_escape_string(trim($_POST['vendor_email'] ?? ""));
$user_type     = $conn->real_escape_string(trim($_POST['user_type'] ?? ""));
$sender_email  = $conn->real_escape_string(trim($_POST['sender_email'] ?? ""));
$issue         = $conn->real_escape_string(trim($_POST['issue'] ?? ""));
$date          = $_POST['date'] ?? ''; // optional

// ✅ Validate required fields
if (!$vendor_name || !$vendor_email || !$user_type || !$sender_email || !$issue) {
    echo json_encode([
        "success" => false,
        "message" => "Please fill all required fields."
    ]);
    exit;
}

if (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid sender email."
    ]);
    exit;
}

// ✅ Call method
$success = $auth->reportUser(
    $vendor_name,
    $vendor_email,
    $user_type,
    $sender_email,
    $issue,
    $date
);

// ✅ Response
if ($success) {
    echo json_encode([
        "success" => true,
        "message" => "Report sent successfully."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error reporting user. Please try again."
    ]);
}
