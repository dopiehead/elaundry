<?php
require("../class/auth.php");

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

$auth = new Auth(new Database());

// ✅ Collect inputs safely
$user_name       = trim($_POST['user_name'] ?? "");
$user_email      = trim($_POST['user_email'] ?? "");
$user_password   = trim($_POST['user_password'] ?? "");
$cpassword       = trim($_POST['cpassword'] ?? "");
$user_type       = trim($_POST['user_type'] ?? "");
$user_dob        = trim($_POST['user_dob'] ?? "");
$user_bio        = trim($_POST['user_bio'] ?? "");
$user_phone      = trim($_POST['user_phone'] ?? "");
$user_location   = trim($_POST['user_location'] ?? "");
$lga             = trim($_POST['lga'] ?? "");
$user_address    = trim($_POST['user_address'] ?? "");
$user_rating     = (float)($_POST['user_rating'] ?? 0);
$user_gender     = trim($_POST['user_gender'] ?? "");
$user_likes      = (int)($_POST['user_likes'] ?? 0);
$user_shares     = (int)($_POST['user_shares'] ?? 0);
$user_fee        = trim($_POST['user_fee'] ?? 0);
$user_views      = (int)($_POST['user_views'] ?? 0);
$user_preference = trim($_POST['user_preference'] ?? "");
$user_services   = trim($_POST['user_services'] ?? "");
$vkey            = $_POST['vkey'] ?? bin2hex(random_bytes(8));
$verified        = (int)($_POST['verified'] ?? 0);
$payment_status  = trim($_POST['payment_status'] ?? "pending");
$date_added      = date("Y-m-d H:i:s");

// ✅ Default empty image
$imageUrl = "";

// ✅ Cloudinary config
Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? "dhgqikloc",
        'api_key'    => $_ENV['CLOUDINARY_API_KEY'] ?? "379753199352113",
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? "1R8_WejnLXnAdb2PvCDXPE1CGEw"
    ],
    'url' => ['secure' => true]
]);

// ✅ Handle file upload
if (!empty($_FILES['user_image']['tmp_name'])) {
    $ext = pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array(strtolower($ext), $allowed)) {
        die("Invalid image format");
    }

    $uploadResult = (new UploadApi())->upload($_FILES['user_image']['tmp_name'], [
        'folder' => 'profiles/',
        'public_id' => bin2hex(random_bytes(8)),
        'overwrite' => true,
        'resource_type' => 'image'
    ]);

    $imageUrl = $uploadResult['secure_url'];
}

// ✅ Validation
$errors = [];
if (empty($user_name)) $errors[] = "Name is required";
if (empty($user_email)) $errors[] = "Email is required";
if (empty($user_password)) $errors[] = "Password is required";
if ($user_password !== $cpassword) $errors[] = "Passwords do not match";

if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}
if (strlen($user_password) < 6) {
    $errors[] = "Password must be at least 6 characters long";
}
if (!empty($user_phone) && !preg_match("/^[0-9+\-\s]{7,20}$/", $user_phone)) {
    $errors[] = "Invalid phone number";
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    exit;
}

// ✅ Hash password
$hashed_password = password_hash($user_password, PASSWORD_BCRYPT);

// ✅ Register user
$success = $auth->register(
    $user_name,
    $user_email,
    $hashed_password,
    $user_type,
    $imageUrl,
    $user_dob,
    $user_bio,
    $user_phone,
    $user_location,
    $lga,
    $user_address,
    $user_rating,
    $user_gender,
    $user_likes,
    $user_views,
    $user_shares,
    $user_fee,
    $user_preference,
    $user_services,
    $vkey,
    $verified,
    $payment_status,
    $date_added
);

// ✅ If registration success → send mail
if ($success) {
    echo "1";
    // require '../PHPMailer-master/PHPMailer-master/PHPMailerAutoload.php';

    // $mail = new PHPMailer(true);
    // try {
    //     include("../components/mailer.php");
    //     $mail->addAddress($user_email);
    //     $mail->isHTML(true);
    //     $mail->Subject = 'Email Verification - Elaundry';
    //     $mail->Body = "
    //     <html>
    //     <head>
    //         <meta name='color-scheme' content='light only'>
    //         <meta name='supported-color-schemes' content='light only'>
    //     </head>
    //     <body style='font-family: Arial, sans-serif; padding: 10px;'>
    //         <div class='text-left'>
    //             <img src='https://elaundry.ng/assets/icons/logo.png' height='50' width='50' alt='ElaundryNG Logo'>
    //         </div>
    //         <br><br>
    //         <div style='font-size: 15px;'>
    //             <h6>Hello {$user_name},</h6>
    //             <p>Thank you for signing up with ElaundryNG! We're excited to have you on board.</p>
    //             <p>To complete your registration and activate your account, please verify your email by clicking the link below:</p>
    //             <p><a href='https://elaundry.ng/public/verify-user?vkey={$vkey}' style='background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Verify Account</a></p>
    //             <br>
    //             <p>If you did not sign up for this account, please ignore this email.</p>
    //             <p>Need help? Contact our support team at <a href='mailto:info@elaundry.ng'>info@elaundry.ng</a>.</p>
    //             <p>Best regards,<br>The ElaundryNG Team</p>
    //         </div>
    //     </body>
    //     </html>";

    //     if (!$mail->send()) {
    //         echo "Error in sending verification email: " . $mail->ErrorInfo;
    //     } else {
    //         echo "1"; // ✅ success
    //     }
    // } catch (Exception $e) {
    //     echo "Mailer Error: " . $mail->ErrorInfo;
    // }
} else {
    echo "Registration failed";
}
