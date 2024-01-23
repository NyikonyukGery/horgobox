<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require(ROOT_PATH . "/app/mailer/src/Exception.php");
require(ROOT_PATH . "/app/mailer/src/PHPMailer.php");
require(ROOT_PATH . "/app/mailer/src/SMTP.php");

function sendMail($emailFrom, $mailTo, $subject, $body, $mailFromName="Horgobox - Csipcsiripp") {
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    
    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                   //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->isHTML(true);
        $mail->CharSet="UTF-8";
        $mail->Host       = 'mail.csipcsiripp.hu';                  //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $emailFrom;                             //SMTP username
        $mail->Password   = "&c(!8VoD-]d+";                         //SMTP password
        $mail->SMTPSecure = "ssl";                                  //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom($emailFrom, $mailFromName);
        $mail->addAddress($mailTo);                                 //Add a recipient
    
        //Content
        $mail->isHTML(true);                                        //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);
    
        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}