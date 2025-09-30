         <?php

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $_ENV['HOST']; // replace with your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['USERNAME']; // replace with your email
            $mail->Password   = $_ENV['PASSWORD'];;    // replace with your email password/app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->setFrom('info@elaundry.ng', 'E-Laundry Team');         
            $mail->isHTML(true);
