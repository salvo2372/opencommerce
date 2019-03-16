<?php
namespace App\core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
class Email {

    private function __construct(){}

    public static function sendEmail($type, $email, $userData, $data){
         $mail             = new PHPMailer();
         $mail->IsSMTP();

         // good for debugging, otherwise keep it commented
         // $mail->SMTPDebug  = EMAIL_SMTP_DEBUG;
         $mail->SMTPAuth    = Config::get('EMAIL_SMTP_AUTH');
         $mail->SMTPSecure  = Config::get('EMAIL_SMTP_SECURE');
         $mail->Host        = Config::get('EMAIL_SMTP_HOST');
         $mail->Mailer      = Config::get('EMAIL_MAILER');
         $mail->Port        = Config::get('EMAIL_SMTP_PORT');
         $mail->SMTPOptions = Config::get('EMAIL_SMTP_OPTION');
         $mail->Username    = Config::get('EMAIL_SMTP_USERNAME');
         $mail->Password    = Config::get('EMAIL_SMTP_PASSWORD');

         $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
         $mail->AddReplyTo(Config::get('EMAIL_REPLY_TO'));

         switch($type){
             case (Config::get('EMAIL_EMAIL_VERIFICATION')):
                 $mail->Body = self::getEmailVerificationBody($userData, $data);
                 $mail->Subject    = Config::get('EMAIL_EMAIL_VERIFICATION_SUBJECT');
                 $mail->AddAddress($email);
                 break;
             case (Config::get('EMAIL_REVOKE_EMAIL')):
                 $mail->Body = self::getRevokeEmailBody($userData, $data);
                 $mail->Subject    = Config::get('EMAIL_REVOKE_EMAIL_SUBJECT');
                 $mail->AddAddress($email);
                 break;
             case (Config::get('EMAIL_UPDATE_EMAIL')):
                 $mail->Body = self::getUpdateEmailBody($userData, $data);
                 $mail->Subject    = Config::get('EMAIL_UPDATE_EMAIL_SUBJECT');
                 $mail->AddAddress($email);
                 break;
             case (Config::get('EMAIL_PASSWORD_RESET')):
                 $mail->Body = self::getPasswordResetBody($userData, $data);
                 $mail->Subject    = Config::get('EMAIL_PASSWORD_RESET_SUBJECT');
                 $mail->AddAddress($email);
                 break;
             case (Config::get('EMAIL_REPORT_BUG')):
                 $mail->Body = self::getReportBugBody($userData, $data);
                 $mail->Subject    = "[".ucfirst($data["label"])."] " . Config::get('EMAIL_REPORT_BUG_SUBJECT') . " | " . $data["subject"];
                 $mail->AddAddress($email);
                 break;
         }

         // If you don't have an email setup, you can instead save emails in log.txt file using Logger.
         // Logger::log("EMAIL", $mail->Body);
         if(!$mail->Send()) {
             throw new Exception("Email couldn't be sent to ". $userData["id"] ." for type: ". $type);
         }
    }

     public static function sendEmailContact($name, $email, $subject, $message){
         $mail             = new PHPMailer();
         $mail->IsSMTP();

         // good for debugging, otherwise keep it commented
         // $mail->SMTPDebug  = EMAIL_SMTP_DEBUG;
         $mail->SMTPDebug   = Config::get('EMAIL_SMTP_DEBUG');
         //$mail->SMTPAuth    = Config::get('EMAIL_SMTP_AUTH');
         $mail->SMTPSecure  = Config::get('EMAIL_SMTP_SECURE');
         //$mail->Host        = Config::get('EMAIL_SMTP_HOST');
         $mail->Host        = gethostbyname(Config::get('EMAIL_SMTP_HOST'));
         $mail->Mailer      = Config::get('EMAIL_MAILER');
         $mail->Port        = Config::get('EMAIL_SMTP_PORT');
         $mail->SMTPOptions = Config::get('EMAIL_SMTP_OPTION');
         $mail->Username    = Config::get('EMAIL_SMTP_USERNAME');
         $mail->Password    = Config::get('EMAIL_SMTP_PASSWORD');
        //Set who the message is to be sent from
        $mail->setFrom($email, $name);
        //Set an alternative reply-to address
        $mail->addReplyTo($email, 'Reply To');
        //Set who the message is to be sent to
        $mail->addAddress(Config::get('EMAIL_SMTP_USERNAME'), Config::get('EMAIL_FROM_NAME'));      
        //Set the subject line
        $mail->Subject = $subject;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        $mail->isHTML(true);
        $mail->Body = $message;
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        //Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.png');
        //send the message, check for errors
        if (!$mail->send()) {
             //throw new Exception("Email couldn't be sent to ");
            return false;
        }
        return true;
     }
     private static function getEmailVerificationBody($userData, $data){

         $body  = "";
         $body .= "Dear " . $userData["name"] . ", \n\nPlease verify your email from the following link: ";
         $body .= Config::get('EMAIL_EMAIL_VERIFICATION_URL') . "?id=" . urlencode($userData["id"]) . "&token=" . urlencode($data["email_token"]);
         $body .= "\n\nIf you didn't edit/add your email, Please contact the admin directly.";
         $body .= "\n\nBest Regards\nMini Crociere \nIsole Eolie";

         return $body;
     }
     public static function inviaEmail($name,$email,$subject,$message){
       $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
       try {
         $mail->isSMTP();
         //Enable SMTP debugging
         // 0 = off (for production use)
         // 1 = client messages
         // 2 = client and server messages
         $mail->SMTPDebug = Config::get('EMAIL_SMTP_DEBUG');
         $mail->Mailer = Config::get('EMAIL_MAILER');
         //Set the hostname of the mail server
         $mail->Host = 'imap.gmail.com';
         // use
         // $mail->Host = gethostbyname('smtp.gmail.com');
         // if your network does not support SMTP over IPv6
         //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
         $mail->Port = 587;
         //Set the encryption system to use - ssl (deprecated) or tls
         $mail->SMTPSecure = 'tls';
         //Whether to use SMTP authentication
         $mail->SMTPOptions = array(
             'tls' => array(
                 'verify_peer' => false,
                 'verify_peer_name' => false,
                 'allow_self_signed' => true
             )
         );
         $mail->SMTPAuth = true;

           //Recipients
           $mail->setFrom(Config::get('EMAIL_SMTP_USERNAME'), Config::get('EMAIL_FROM_NAME'));
           $mail->addAddress($email, $name);
           $mail->addReplyTo(Config::get('EMAIL_SMTP_USERNAME'), Config::get('EMAIL_REPLY_NAME'));

           //Attachments
           //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
           //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

           //Content
           $mail->isHTML(true);                                  // Set email format to HTML
           $mail->Subject = $subject;
           $mail->Body    = $message;
           $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

           $mail->send();
           return true;
       } catch (Exception $e) {
           return false;
       }
     }
}
