<?php

require("../class/auth.php");

$auth = new Auth(new Database());
$user_email    = trim($_POST['user_email']   ?? "");
$user_password = trim($_POST['user_password'] ?? "");

// 🔎 VALIDATION RULES
$errors = [];

// Required fields
if (empty($user_email))    $errors[] = "Email is required";
if (empty($user_password)) $errors[] = "Password is required";

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
$success = $auth->login($user_email, $user_password);

// ✅ Response handling
if ($success) {
    echo "1"; // success
} else {
    echo "Login failed"; // failure
}
