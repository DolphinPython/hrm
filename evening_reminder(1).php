<?php
require 'email/mailer.php';
include 'layouts/config.php';
 // Include your database connection file

// Get logged-in employees' emails
$sql = "SELECT e.email, e.fname FROM hrm_employee e 
        JOIN newuser_attendance a ON e.id = a.user_id 
        WHERE a.status = 'login'";
$result = $con->query($sql);

while ($row = $result->fetch_assoc()) {
    $email = $row['email'];
    $name = $row['fname'];

    // Personalized email conntent
    $subject = "Good Evening, $name! Don't Forget to Log Out";
    $message = "
        <html>
        <body>
            <p>Dear $name,</p>
            <p>We appreciate your hard work today! Before you wrap up, please remember to log out of the system by 6:30 PM.</p>
            <p>Enjoy your evening and see you tomorrow!</p>
            <p>Best Regards,<br>HR </p>
        </body>
        </html>
    ";

    send_email($email, $subject, $message);
}

echo "Evening reminders sent!";
?>
