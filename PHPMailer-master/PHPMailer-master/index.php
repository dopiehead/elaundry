
<?php
require 'PHPMailerAutoload.php';
$mail= new PHPMailer;




$mail->SMTPDebug = 1;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host='smtp.gmail.com';
    $mail->Host='smtp.gmail.com';
$mail->Port=587;
$mail->SMTPAuth=true;
$mail->SMTPSecure='tls';

$mail->Username='niyialabi10@gmail.com';
$mail->Password='Olaniyi.12';

$mail->setFrom('niyialabi10@gmail.com','getbusy');
$mail->addAddress('niyialabi10@gmail.com');
$mail->addAddress('niyialabi10@gmail.com');
$mail->addReplyTo('niyialabi10@gmail.com');

$mail->isHTML(true);
$mail->Subject='PHP Mailer Subject';
$mail->Body='<h1 align=center>subscribe my channel</h1><h4 align=center>like this site</h4>';

if (!$mail->send()) {echo "message not sent".$mail->ErrorInfo;
	
}
else{
	echo"message has been sent";}






    ?>