<?php

require("../class/auth.php");

$auth = new Auth(new Database());

// ✅ Collect & sanitize inputs
$sender_email    = trim($_POST['sender_email'] ?? '');
$name            = trim($_POST['name'] ?? '');
$subject         = trim($_POST['subject'] ?? '');
$compose         = trim($_POST['compose'] ?? '');
$receiver_email  = trim($_POST['receiver_email'] ?? '');
$date            = $_POST['date'] ?? date('Y-m-d H:i:s');

// ✅ Default values
$has_read            = 0;
$is_receiver_deleted = 0;
$is_sender_deleted   = 0;

// ✅ Validate required fields
if (!$sender_email || !$receiver_email || !$compose) {
    echo "Please fill all required fields.";
    exit;
}

if (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid sender email.";
    exit;
}

if (!filter_var($receiver_email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid receiver email.";
    exit;
}

// ✅ Call Auth method
$success = $auth->sendMessage(
    $sender_email, $name, $subject, $compose, $receiver_email,
    $has_read, $is_receiver_deleted, $is_sender_deleted, $date
);

if ($success) {
    echo "1";
} else {
    echo "Error in sending message. Please try later.";
}
