<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

require '../class/auth.php';
$auth = new Auth(new Database());
$conn = $auth->getConnection(); // ✅ Ensure $conn is defined

$user_id = $auth->getUserId();
if (!isset($user_id)) {
    echo "Session expired. Please log in again.";
    exit;
}

$sid = $user_id ?? null;

if (
    isset(
        $_POST['user_name'], $_POST['user_email'], $_POST['user_type'],
        $_POST['password'], $_POST['cpassword'], $_POST['user_address'],
        $_POST['user_phone']
    )
) {
    // ✅ Sanitize & collect input
    $user_name     = trim($_POST['user_name']);
    $user_email    = trim($_POST['user_email']);
    $user_type     = trim($_POST['user_type']);
    $user_address  = trim($_POST['user_address']);
    $password      = $_POST['password'];
    $cpassword     = $_POST['cpassword'];
    $user_phone    = trim($_POST['user_phone']);

    // Optional fields (use fallback if not provided)
    $bank_name      = trim($_POST['bank_name'] ?? '');
    $account_number = trim($_POST['account_number'] ?? '');
    $whatsapp       = trim($_POST['whatsapp'] ?? '');
    $location       = trim($_POST['location'] ?? '');
    $lga            = trim($_POST['lga'] ?? '');
    $about          = trim($_POST['about'] ?? '');
    $service        = trim($_POST['services'] ?? '');
    $days           = trim($_POST['days'] ?? '');
    $opening_time   = trim($_POST['opening_time'] ?? '');
    $closing_time   = trim($_POST['closing_time'] ?? '');
    $facebook       = trim($_POST['facebook'] ?? '');
    $twitter        = trim($_POST['twitter'] ?? '');
    $linkedin       = trim($_POST['linkedin'] ?? '');
    $instagram      = trim($_POST['instagram'] ?? '');

    $date = date("D, F d, Y g:iA", strtotime('+1 hours'));

    // ✅ Validation
    if (strlen($user_name) > 22) {
        echo "Character number limit exceeded for user_name";
        exit;
    }

    if ($password !== $cpassword) {
        echo "Password mismatch";
        exit;
    }

    if (empty($user_phone)) {
        echo "Contact field cannot be empty";
        exit;
    }

    if (empty($location)) {
        echo "Location field cannot be empty";
        exit;
    }

    // ✅ Hash password AFTER validation
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // ✅ INSERT user_information
    $stmt = $conn->prepare(
        "INSERT INTO user_information 
            (sid, user_name, user_type, about, service, password, user_phone, user_address, 
             bank_name, account_number, whatsapp, location, lga, facebook, twitter, linkedin, 
             instagram, days, opening_time, closing_time, date) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if ($stmt === false) {
        echo "Error preparing the insert query: " . $conn->error;
        exit;
    }

    $stmt->bind_param(
        'sssssssssssssssssssss',
        $sid,
        $user_name,
        $user_type,
        $about,
        $service,
        $hashed_password,
        $user_phone,
        $user_address,
        $bank_name,
        $account_number,
        $whatsapp,
        $location,
        $lga,
        $facebook,
        $twitter,
        $linkedin,
        $instagram,
        $days,
        $opening_time,
        $closing_time,
        $date
    );

    if ($stmt->execute()) {
        // ✅ UPDATE user_profile
        $update_stmt = $conn->prepare(
            "UPDATE user_profile 
             SET user_name=?, user_phone=?, user_password=?, user_location=?, user_address=? 
             WHERE id=?"
        );

        if ($update_stmt === false) {
            echo "Error preparing update query: " . $conn->error;
            exit;
        }

        $update_stmt->bind_param(
            'sssssi',
            $user_name,
            $user_phone,
            $hashed_password,
            $location,
            $user_address,
            $sid
        );

        if ($update_stmt->execute()) {
            echo "1"; // ✅ Success
        } else {
            echo "Error updating user profile: " . $update_stmt->error;
        }

        $update_stmt->close();
    } else {
        echo "Error inserting user info: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Required fields are missing.";
}
?>
