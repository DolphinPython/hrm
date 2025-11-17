<?php
require 'email/mailer.php';
include 'layouts/config.php';

// Check if today is Saturday (6) or Sunday (7)
$dayOfWeek = date('N'); // 1 (Monday) - 7 (Sunday)
if ($dayOfWeek == 6 || $dayOfWeek == 7) {
    echo "No emails sent today (Saturday or Sunday).";
    exit;
}

// Fetch active employees
$sql = "SELECT email FROM hrm_employee WHERE status = 1";
$result = $con->query($sql);

$cc_employees = [];
while ($row = $result->fetch_assoc()) {
    $cc_employees[] = $row['email']; // Collect all employee emails for CC
}

// Define recipient, subject, and message
$hr_email = "hr@1solutions.biz"; // HR in "To"
$subject = "Good Morning! Please Log In";
$message = "
    <html>
    <body>
        <p>Dear Team,</p>
        <p>Hope you had a great start to your day! This is a friendly reminder to log in to the system before 9:00 AM.</p>
        <p>Stay productive and have a wonderful day ahead!</p>
        <p>Best Regards,<br>HR</p>
    </body>
    </html>
";

// Send email with HR as "To" and all employees in "CC"
send_email($hr_email, $subject, $message, $cc_employees);

echo "Morning reminder sent successfully!";
?>
