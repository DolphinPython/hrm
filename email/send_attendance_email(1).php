<?php
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csvContent = $_POST['csv'];
    $name = $_POST['name'];
    $email = $_POST['email']; // Get email from POST request

    // Save CSV to a temporary file
    $filePath = sys_get_temp_dir() . '/attendance_report_' . time() . '.csv';
    file_put_contents($filePath, $csvContent);

    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'expetize.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hr@expetize.com';
        $mail->Password = 'kanu12345!@#$%';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Headers
        $mail->setFrom('hr@expetize.com', 'HR');
        $mail->addAddress($email); // Send email to the provided address

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "Attendance Report for $name";
        $mail->Body = "Hello $name,<br><br>Please find the attached attendance report.";

        // Add attachment
        $mail->addAttachment($filePath, "attendance_report.csv");

        // Send email
        $mail->send();
        unlink($filePath); // Delete the temporary file
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => "Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
