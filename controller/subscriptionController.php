<?php
require("../class/auth.php");
$auth = new Auth(new Database);
$conn = $auth->getConnection();
$user_id = $auth->getUserId();
$txn_ref = time();
$user_id =  $user_id !== "" ? is_numeric($user_id): 0;
if($user_id > 0):

   $getuser = $conn->prepare("SELECT * FROM user_profile WHERE id = ?");
   $getuser->bind_param("i",$user_id);
   if($getuser->execute()):
     $userResult = $getuser->get_result();
     $user = $userResult->fetch_assoc();
      include("../contents/user-details.php");
   endif;
endif;

?>