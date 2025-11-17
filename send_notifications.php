<?php
include 'layouts/config.php'; // Include your database configuration

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set timezone
date_default_timezone_set('Asia/Kolkata'); // Replace with your timezone

// Ensure the script runs only at 9:00 AM
// $currentHour = date('H');
// $currentMinute = date('i');
// if ($currentHour != 9 || $currentMinute != 0) {
//     exit("The script runs only at 9:00 AM.<br>");
// }

// Function to validate email addresses
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to send email with CC or BCC recipients
function sendEmail($email, $ccRecipients, $bccRecipients, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'expetize.com'; // Your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'hr@expetize.com'; // Your SMTP username
        $mail->Password = 'kanu12345!@#$%'; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
        $mail->Port = 587; // SMTP port

        // Sender details
        $mail->setFrom('hr@expetize.com', 'HRM Notifications');

        // Add primary recipient (To field)
        if (is_array($email)) {
            foreach ($email as $recipient) {
                if (isValidEmail($recipient)) {
                    $mail->addAddress($recipient);
                }
            }
        } else {
            if (isValidEmail($email)) {
                $mail->addAddress($email); // For single recipient
            }
        }

        // Add CC recipients
        if (!empty($ccRecipients)) {
            if (is_array($ccRecipients)) {
                foreach ($ccRecipients as $ccRecipient) {
                    if (isValidEmail($ccRecipient)) {
                        $mail->addCC($ccRecipient);
                    }
                }
            } else {
                if (isValidEmail($ccRecipients)) {
                    $mail->addCC($ccRecipients); // For single CC recipient
                }
            }
        }

        // Add BCC recipients
        if (!empty($bccRecipients)) {
            if (is_array($bccRecipients)) {
                foreach ($bccRecipients as $bccRecipient) {
                    if (isValidEmail($bccRecipient)) {
                        $mail->addBCC($bccRecipient);
                    }
                }
            } else {
                if (isValidEmail($bccRecipients)) {
                    $mail->addBCC($bccRecipients); // For single BCC recipient
                }
            }
        }

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Send email
        $mail->send();
        echo "Email sent successfully.<br>";
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}<br>";
    }
}

// Function to generate a birthday card
function generateBirthdayCard($name) {
    // Base path and image list
    $basePath = "https://hrmpulse.com/assets/";
    $images = ["4.png", "5.png", "6.png"];

    // Random image selection
    $randomImage = $basePath . $images[array_rand($images)];

    return "
    <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 10px; max-width: 600px; margin: auto; background-color: #f9f9f9;'>
        <h1 style='color: #ff6f61;'> Happy Birthday, $name! </h1>
        <p style='font-size: 18px; color: #333;'>Wishing you a day filled with happiness, joy, and lots of cake! </p>
        <img src='$randomImage' alt='Birthday Celebration' style='width: 100%; border-radius: 10px;'>
        <p style='font-size: 16px; color: #555;'>Best wishes from the HR Team!</p>
    </div>
    ";
}

// Function to generate a work anniversary card
function generateWorkAnniversaryCard($name) {
    return "
    <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 10px; max-width: 600px; margin: auto; background-color: #f9f9f9;'>
        <h1 style='color: #4caf50;'>   Congratulations, $name!   </h1>
        <p style='font-size: 18px; color: #333;'>Thank you for your dedication and hard work. Here's to many more successful years! 🥂</p>
        <img src='https://hrmpulse.com/assets/workanniversary.jpg' alt='Work Anniversary' style='width: 100%; border-radius: 10px;'>
        <p style='font-size: 16px; color: #555;'>Best regards,<br>HR Team</p>
    </div>
    ";
}

// Function to generate a holiday card
function generateHolidayCard($holidayName) {
    return "
    <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 10px; max-width: 600px; margin: auto; background-color: #f9f9f9;'>
        <h1 style='color: #ff9800;'>   Happy $holidayName!   </h1>
        <p style='font-size: 18px; color: #333;'>Wishing you and your loved ones a wonderful celebration! </p>
        <img src='https://hrmpulse.com/assets/specialday.jpg' alt='Holiday Celebration' style='width: 100%; border-radius: 10px;'>
        <p style='font-size: 16px; color: #555;'>Best wishes from the HR Team!</p>
    </div>
    ";
}

// Function to generate a birthday alert card
function generateBirthdayAlertCard($fullName, $email) {
    // Base path and image list
    $basePath = "https://hrmpulse.com/assets/";
    $images = ["4.png", "5.png", "6.png"];

    // Random image selection
    $randomImage = $basePath . $images[array_rand($images)];

    return "
    <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 10px; max-width: 600px; margin: auto; background-color: #f9f9f9;'>
        <h1 style='color: #ff6f61;'>   Birthday Alert!   </h1>
        <p style='font-size: 18px; color: #333;'>Dear Team,<br><br>It's $fullName's Birthday! Send your warm wishes to $email.</p>
        <img src='$randomImage' alt='Birthday Celebration' style='width: 100%; border-radius: 10px;'>
        <p style='font-size: 16px; color: #555;'>Best wishes from the HR Team!</p>
    </div>
    ";
}

// Function to generate a work anniversary alert card
function generateWorkAnniversaryAlertCard($fullName, $email) {
    return "
    <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 10px; max-width: 600px; margin: auto; background-color: #f9f9f9;'>
        <h1 style='color: #4caf50;'>   Work Anniversary Alert!   </h1>
        <p style='font-size: 18px; color: #333;'>Dear Team,<br><br>It's $fullName's Work Anniversary! Congratulate them at $email.</p>
        <img src='https://hrmpulse.com/assets/workanniversary.jpg' alt='Work Anniversary' style='width: 100%; border-radius: 10px;'>
        <p style='font-size: 16px; color: #555;'>Best regards,<br>HR Team</p>
    </div>
    ";
}

// Function to notify all employees with CC or BCC recipients
function notifyAllEmployees($subject, $message, $allEmails, $ccRecipients = [], $bccRecipients = []) {
    foreach ($allEmails as $email) {
        if (isValidEmail($email)) {
            sendEmail($email, $ccRecipients, $bccRecipients, $subject, $message);
        } else {
            echo "Invalid email address: $email<br>";
        }
    }
}

// Get today's date
$todayMonth = date('m');
$todayDay = date('d');

// Fetch all employees' emails
$allEmails = [];
$sqlAll = "SELECT email FROM hrm_employee";
$resultAll = $con->query($sqlAll);

if ($resultAll->num_rows > 0) {
    while ($rowAll = $resultAll->fetch_assoc()) {
        $email = $rowAll['email'];
        if (isValidEmail($email)) {
            $allEmails[] = $email;
        } else {
            echo "Invalid email address in database: $email<br>";
        }
    }
}

// Process birthdays and work anniversaries
function processEvents($con, $allEmails, $todayMonth, $todayDay) {
    $sql = "SELECT fname, lname, email, dob, doj FROM hrm_employee 
            WHERE (MONTH(dob) = ? AND DAY(dob) = ?) 
               OR (MONTH(doj) = ? AND DAY(doj) = ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iiii", $todayMonth, $todayDay, $todayMonth, $todayDay);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $fname = $row['fname'];
            $lname = $row['lname'];
            $email = $row['email'];
            $dob = $row['dob'];
            $doj = $row['doj'];
            $fullName = "$fname $lname";

            // Birthday
            if (date('m-d', strtotime($dob)) === "$todayMonth-$todayDay") {
                $subject = "Happy Birthday!";
                $message = generateBirthdayCard($fullName);
                sendEmail($email, [], [], $subject, $message);
                $birthdayAlertMessage = generateBirthdayAlertCard($fullName, $email);
                notifyAllEmployees("Birthday Alert!", $birthdayAlertMessage, $allEmails);
            }

            // Work Anniversary
            if (date('m-d', strtotime($doj)) === "$todayMonth-$todayDay") {
                $subject = "Happy Work Anniversary!";
                $message = generateWorkAnniversaryCard($fullName);
                sendEmail($email, [], [], $subject, $message);
                $anniversaryAlertMessage = generateWorkAnniversaryAlertCard($fullName, $email);
                notifyAllEmployees("Work Anniversary Alert!", $anniversaryAlertMessage, $allEmails);
            }
        }
    } else {
        echo "No birthdays or work anniversaries today.<br>";
    }
}

// Process holidays
function processHolidays($con, $allEmails, $todayMonth, $todayDay) {
    $sqlHoliday = "SELECT name FROM hrm_holidays WHERE MONTH(date) = ? AND DAY(date) = ?";
    $stmtHoliday = $con->prepare($sqlHoliday);
    $stmtHoliday->bind_param("ii", $todayMonth, $todayDay);
    $stmtHoliday->execute();
    $resultHoliday = $stmtHoliday->get_result();

    if ($resultHoliday->num_rows > 0) {
        while ($rowHoliday = $resultHoliday->fetch_assoc()) {
            $holidayName = $rowHoliday['name'];

            $subject = "Happy $holidayName!";
            $message = generateHolidayCard($holidayName);

            notifyAllEmployees($subject, $message, $allEmails);
        }
    } else {
        echo "No holidays today.<br>";
    }
}

// Execute the functions
processEvents($con, $allEmails, $todayMonth, $todayDay);
processHolidays($con, $allEmails, $todayMonth, $todayDay);

// Close connection
$con->close();
?>
