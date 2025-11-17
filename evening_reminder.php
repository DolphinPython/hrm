<?php
require 'email/mailer.php';
include 'layouts/config.php';

// HR email
$hr_email = "hr@1solutions.biz";

// Check if today is Saturday (6) or Sunday (7)
$dayOfWeek = date('N'); // 1 (Monday) - 7 (Sunday)
if ($dayOfWeek == 6 || $dayOfWeek == 7) {
    echo "No emails sent today (Saturday or Sunday).";
    exit;
}

// Get employees whose last recorded status is 'login' (should receive logout reminder)
$sql_login = "SELECT e.email FROM hrm_employee e 
              JOIN (
                  SELECT user_id, status 
                  FROM newuser_attendance 
                  WHERE (user_id, id) IN (
                      SELECT user_id, MAX(id) 
                      FROM newuser_attendance 
                      WHERE DATE(created_at) = CURDATE() 
                      GROUP BY user_id
                  )
              ) a ON e.id = a.user_id
              WHERE a.status = 'login' AND e.email IS NOT NULL";

$result_login = $con->query($sql_login);
$cc_login = [];

while ($row = $result_login->fetch_assoc()) {
    $cc_login[] = $row['email'];
}

// Get employees whose last recorded status is 'logout' (should receive absent warning)
$sql_logout = "SELECT e.email FROM hrm_employee e 
               JOIN (
                   SELECT user_id, status 
                   FROM newuser_attendance 
                   WHERE (user_id, id) IN (
                       SELECT user_id, MAX(id) 
                       FROM newuser_attendance 
                       WHERE DATE(created_at) = CURDATE() 
                       GROUP BY user_id
                   )
               ) a ON e.id = a.user_id
               WHERE a.status = 'logout' AND e.email IS NOT NULL";

$result_logout = $con->query($sql_logout);
$cc_logout = [];

while ($row = $result_logout->fetch_assoc()) {
    $cc_logout[] = $row['email'];
}

// 1️⃣ Email for Logged-in Employees (Reminder to Logout)
if (!empty($cc_login)) {
    $subject_login = "Reminder: Please Logout by 6:30 PM";
    $message_login = "
        <html>
        <body>
            <p>Dear Team,</p>
            <p>This is a gentle reminder to <b>log out by 6:30 PM</b> to ensure proper attendance tracking.</p>
            <p><b>Why is this important?</b></p>
            <ul>
                <li>Failure to log out might impact your attendance records.</li>
                <li>Ensure you have completed your tasks before logging out.</li>
            </ul>
            <p>Thank you for your cooperation!</p>
            <p>Best Regards,<br>HR Department</p>
        </body>
        </html>
    ";
    send_email($hr_email, $subject_login, $message_login, $cc_login);
}

// 2️⃣ Email for Logged-out Employees (Marked as Absent)
if (!empty($cc_logout)) {
    $subject_logout = "Attendance Alert: You Are Marked as Absent";
    $message_logout = "
        <html>
        <body>
            <p>Dear Team,</p>
            <p>Our records show that some employees have either <b>logged out early</b> or did not log in today.</p>
            <p>If this was a mistake, please contact HR immediately to rectify your attendance.</p>
            <p><b>Action Required:</b></p>
            <ul>
                <li>If you left early, ensure that HR is informed.</li>
                <li>If you missed logging in, report the issue as soon as possible.</li>
            </ul>
            <p>Thank you for your attention.</p>
            <p>Best Regards,<br>HR Department</p>
        </body>
        </html>
    ";
    send_email($hr_email, $subject_logout, $message_logout, $cc_logout);
}

echo "Emails sent successfully!";
?>
