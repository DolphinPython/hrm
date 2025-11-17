<?php
require './phpmailer/src/Exception.php';
require './phpmailer/src/PHPMailer.php';
require './phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

function send_email($to, $subject, $message, $cc_emails = []) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'expetize.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hr@expetize.com';
        $mail->Password = 'kanu12345!@#$%';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set sender
        $mail->setFrom('hr@expetize.com', 'HR');

        // Set recipient (HR)
        $mail->addAddress($to);

        // Add CC emails (if provided)
        if (!empty($cc_emails)) {
            foreach ($cc_emails as $cc) {
                if (filter_var($cc, FILTER_VALIDATE_EMAIL)) { // Validate email
                    $mail->addCC($cc);
                }
            }
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo); // Log error for debugging
        return false;
    }
}
?>
