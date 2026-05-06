<?php

//Clean input data
function dataclean($input=null){
    return htmlspecialchars(stripcslashes(trim($input)));
    
}




function dbConn()
{
   $conn = new mysqli("localhost", "root", "", "Sciencemore");
   if($conn->connect_error){
      die("connection failed:" . $conn->connect_error);
   }else{

       return $conn;
   }

}

require_once 'email/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function sendEmail($email, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'prashankannangara@gmail.com'; // Replace with your Gmail
        $mail->Password   = 'lhwwfieuzlrmbznz'; // Use Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Content
        $mail->setFrom('prashankannangara@gmail.com', 'BIT 2025');
        $mail->addAddress($email);
        $mail->Subject = $subject;


        $mail->Body = $body;
        $mail->isHTML(true);

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
 
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
