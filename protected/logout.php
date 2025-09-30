<?php
require("../class/auth.php");

// Initialize Auth
$auth = new Auth(new Database());

// Call logout method
$auth->logout();

// Redirect to login page
header("Location: ../public/login");

exit;

