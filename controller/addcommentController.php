<?php
require("../class/auth.php");

header('Content-Type: application/json');

$auth = new Auth(new Database());
$conn = $auth->getConnection();

// ✅ Collect & sanitize inputs
$sender_email  = $conn->real_escape_string(trim($_POST['sender_email'] ?? ""));
$sender_name   = $conn->real_escape_string(trim($_POST['sender_name'] ?? ""));
$comment       = $conn->real_escape_string(trim($_POST['comment'] ?? ""));
$user_id       = (int) ($_POST['user_id'] ?? 0);
$date          = $_POST['date'] ?? ''; // optional

// ✅ Validate required fields
if (!$sender_email || !$sender_name || !$comment || !$user_id) {
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
$success = $auth->addComment($sender_email, $sender_name, $comment, $user_id, $date);

// ✅ Response
if ($success) {
    echo json_encode([
        "success" => true,
        "message" => "Comment added successfully."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error adding comment. Please try again."
    ]);
}
