<?php

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($recipients, $subject, $message, $cc = []) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'expetize.com'; // Your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'hr@expetize.com'; // Your SMTP username
        $mail->Password = 'kanu12345!@#$%'; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
        $mail->Port = 587; // SMTP port

        // Sender details
        $mail->setFrom('hr@expetize.com', 'HRM Notifications');

        // Recipients
        if (is_array($recipients)) {
            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }
        } else {
            $mail->addAddress($recipients); // For single recipient
        }

        // CC Recipients
        if (!empty($cc)) {
            if (is_array($cc)) {
                foreach ($cc as $cc_recipient) {
                    $mail->addCC($cc_recipient);
                }
            } else {
                $mail->addCC($cc); // For single CC recipient
            }
        }

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>