<?php
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_harassment_email($recipients, $ccRecipients, $subject, $message, $attachment = null) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'expetize.com'; // Your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'hr@expetize.com'; // Your SMTP username
        $mail->Password = 'kanu12345!@#$%'; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender details
        $mail->setFrom('hr@expetize.com', 'HRM Notifications');

        // Add primary recipients (To field)
        if (is_array($recipients)) {
            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }
        } else {
            $mail->addAddress($recipients); // For single recipient
        }

        // Add CC recipients
        if (!empty($ccRecipients)) {
            if (is_array($ccRecipients)) {
                foreach ($ccRecipients as $ccRecipient) {
                    $mail->addCC($ccRecipient);
                }
            } else {
                $mail->addCC($ccRecipients); // For single CC recipient
            }
        }

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Add attachment if provided
        if ($attachment && isset($attachment['path']) && file_exists($attachment['path'])) {
            $mail->addAttachment($attachment['path'], $attachment['name']);
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>