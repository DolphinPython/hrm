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
    if (!empty($row['email'])) { // Ensure email is not empty
        $cc_employees[] = $row['email'];
    }
}

// Define recipient, subject, and message
$hr_email = "hr@1solutions.biz"; // HR in "To"
$subject = "Reminder: System Login Before 9:00 AM";
$message = "
    <html>
    <body>
        <p>Dear Team,</p>
        <p>I hope you had a wonderful start to your day. This is a gentle reminder to log in to the system on time.</p>
        <p>Wishing you a productive and successful day ahead.</p>
        <p>Best Regards,<br>HR</p>
    </body>
    </html>
";

// ✅ Send email with HR in "To" and CC only if available
if (send_email($hr_email, $subject, $message, !empty($cc_employees) ? $cc_employees : [])) {
    echo "Morning reminder sent successfully!";
} else {
    echo "Failed to send email.";
}
?>
