<?php

require("../class/auth.php");

$auth = new Auth(new Database());
$conn = $auth->getConnection();

// ✅ Collect inputs safely from POST
$user_email    = trim($_POST['user_email']   ?? "");
$vkey          = $_POST['vkey'] ?? bin2hex(random_bytes(8));
$created_at    = date("Y-m-d H:i:s");

// 🔎 VALIDATION RULES
$errors = [];

// Required fields
if (empty($user_email))    $errors[] = "Email is required";


// Email format
if (!empty($user_email) && !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

// Stop if validation fails
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    exit;
}

// ✅ Attempt login
$success = $auth->forgotPassword($user_email, $vkey, $created_at);

// ✅ Response handling
if ($success) {
    require '../PHPMailer-master/PHPMailer-master/PHPMailerAutoload.php';
    include("../components/mailer.php");
    $mail->addAddress($email);
    $mail->AddEmbeddedImage('assets/img/logo.png', 'pic'); // Correctly embed image using CID
    $mail->addReplyTo('info@elaundry.ng');
    $mail->isHTML(true);
    $mail->Subject = "Password Reset";
    $mail->Body = "
        <div class='text-center'>
            <img src='https://elaundry.ng/assets/icons/logo.png' width='60' height='60'>
        </div>
        <br><br>
        <div>
            Click on the link provided to <b><a href='https://elaundry.ng/verify.php?vkey=".$vkey.">Change password</a></b>
        </div>
    ";

    if (!$mail->send()) {
        echo "Error in sending link: " . $mail->ErrorInfo;
    } else {
        echo "1";
    }
} else {
    echo "Login failed"; // failure
}
