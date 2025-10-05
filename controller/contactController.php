<?php
require("../class/auth.php");

$auth = new Auth(new Database);
$conn = $auth->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    // ✅ 1. Collect and sanitize inputs
    $fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject  = htmlspecialchars(trim($_POST['subject'] ?? ''), ENT_QUOTES, 'UTF-8');
    $message  = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

    // ✅ 2. Fallback date (ensure it's always set)
    $date = date('Y-m-d H:i:s');

    // ✅ 3. Optional: Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
        exit;
    }

    // ✅ 4. Call method to handle contact
    $success = $auth->contactUs($fullname, $email, $subject, $message, $date);

    if ($success) {
        echo "1"; // Success
    } else {
        echo "Error in sending message.";
    }
}
?>
