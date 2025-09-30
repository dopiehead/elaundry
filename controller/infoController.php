<?php
require("../class/auth.php");
$auth = new Auth(new Database);
$conn = $auth->getConnection();

$user_id = $auth->getUserId();
if ($user_id) {
   $getuser = $conn->prepare("SELECT user_email, user_name FROM user_profile WHERE id = ?");
   if ($getuser) {
       $getuser->bind_param("i", $user_id);
       $getuser->execute();
       $getuser->store_result();
       if ($getuser->num_rows > 0) {
           $getuser->bind_result($user_email, $user_name);
           $getuser->fetch();
       }
       $getuser->close();
   }
}