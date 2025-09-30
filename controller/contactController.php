<?php
require("../class/auth.php");
$auth = new Auth(new Database);
$conn = $auth->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // ✅ Collect inputs safely
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $subject    = trim($_POST['subject'] ?? '');
    $message  = trim($_POST['message'] ?? '');
    $date = $date ?: date('Y-m-d H:i:s');

    // Optional: basic sanitization
    $fullname = htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
    $email    = filter_var($email, FILTER_SANITIZE_EMAIL);
    $message  = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $subject  = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');

 $success = $auth->contactUs( $fullname, $subject, $email, $message, $date);
 if($success):
   echo"1";
 else:
    echo "Error in sending message";
 endif;
}

