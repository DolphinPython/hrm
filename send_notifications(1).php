<?php
include 'layouts/config.php'; 

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set timezone
date_default_timezone_set('Asia/Kolkata'); // Replace with your timezone

// Ensure the script runs only at 10:00 AM
$currentHour = date('H');
$currentMinute = date('i');
if ($currentHour != 9 || $currentMinute != 0) {
    exit("The script runs only at 9:00 AM.<br>");
}

// Function to send email
function sendEmail($email, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pythondolphin@gmail.com'; // Replace with your email
        $mail->Password = '1solutions.biz';         // Replace with your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email settings
        $mail->setFrom('pythondolphin@gmail.com', 'HR Notification');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Send email
        $mail->send();
        echo "Email sent to $email.<br>";
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}<br>";
    }
}

// Function to notify all employees
function notifyAllEmployees($subject, $message, $allEmails) {
    foreach ($allEmails as $email) {
        sendEmail($email, $subject, $message);
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
        $allEmails[] = $rowAll['email'];
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
                $message = "Dear $fullName,<br><br>
                            Wishing you a wonderful birthday filled with happiness and joy!<br>
                            Have an amazing day!<br><br>
                            Best Regards,<br>HR Team";
                sendEmail($email, $subject, $message);
                notifyAllEmployees("Birthday Alert!", "Dear Team,<br><br>It's $fullName's Birthday! Send your warm wishes to $email.<br><br>Best Regards,<br>HR Team", $allEmails);
            }

            // Work Anniversary
            if (date('m-d', strtotime($doj)) === "$todayMonth-$todayDay") {
                $subject = "Happy Work Anniversary!";
                $message = "Dear $fullName,<br><br>
                            Congratulations on your work anniversary!<br>
                            Thank you for your dedication and hard work.<br><br>
                            Best Regards,<br>HR Team";
                sendEmail($email, $subject, $message);
                notifyAllEmployees("Work Anniversary Alert!", "Dear Team,<br><br>It's $fullName's Work Anniversary! Congratulate them at $email.<br><br>Best Regards,<br>HR Team", $allEmails);
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
            $message = "Dear Team,<br><br>
                        Wishing you all a very happy $holidayName!<br>
                        Enjoy this special occasion with your loved ones.<br><br>
                        Best Regards,<br>HR Team";

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
