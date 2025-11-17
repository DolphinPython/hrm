<?php
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['pdf']) && isset($_POST['email'])) {
        $email = $_POST['email'];
        $pdf = $_FILES['pdf'];

        // Check if the file was uploaded without errors
        if ($pdf['error'] === UPLOAD_ERR_OK) {
            $filePath = $pdf['tmp_name'];
            $fileName = 'Salary_Slip.pdf';

            $mail = new PHPMailer(true);

            try {
                // SMTP Configuration
                $mail->isSMTP();
                $mail->Host = 'expetize.com'; // Your SMTP host
                $mail->SMTPAuth = true;
                $mail->Username = 'hr@expetize.com'; // Your SMTP username
                $mail->Password = 'kanu12345!@#$%'; // Your SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
                $mail->Port = 587; // SMTP port

                // Email Headers
                $mail->setFrom('hr@expetize.com', 'HR');
                $mail->addAddress($email); // Recipient email address

                // Email Content
                $mail->isHTML(true);
                $mail->Subject = "Your Salary Slip";
                $mail->Body = "Dear Employee,<br><br>Please find your salary slip attached.<br><br>Best regards,<br>HR Team";

                // Attach the PDF
                $mail->addAttachment($filePath, $fileName);

                // Send email
                $mail->send();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => "Mailer Error: {$mail->ErrorInfo}"]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload PDF.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
