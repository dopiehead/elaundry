<?php session_start();

    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    if(!empty($latitude.$longitude)){
        $_SESSION['latitude']= $latitude;
        $_SESSION['longitude']=$longitude;

        echo "1";

    }
    else{
        
        echo 0;  // failure response

        return;
    }

 



?>
