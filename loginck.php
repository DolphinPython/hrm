<?php
// jai shiva
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
date_default_timezone_set('Asia/Kolkata');

include 'layouts/config.php';
include 'email/mailer.php'; // for send_email()

$date = date("Y-m-d H:i:s");

if (isset($_POST['b1'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $query = "SELECT * FROM hrm_employee WHERE email ='$email' AND password ='$password' AND status = '1'";
    $result = mysqli_query($con, $query) or die(mysqli_error($con));
    $row = mysqli_fetch_array($result);

    if (!empty($row['id'])) {
        $_SESSION["id"] = $row['id'];
        $_SESSION["email"] = $row['email'];
        $_SESSION["password"] = $row['password'];
        $_SESSION["token"] = rand();

        $designation_id = $row['designation_id'];
        $department_id = $row['department_id'];

        $emp_id = $row['id'];

        // Get extra info
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $email_status = 'Not Sent';

        // Log admin login and send email
        if (in_array($emp_id, [10, 14])) {
            // Insert into admin_login_logs
            $email_log_query = "INSERT INTO admin_login_logs (admin_id, email, action, timestamp, ip_address, browser_info, email_status)
                VALUES ('$emp_id', '$email', 'login', '$date', '$ip_address', '$user_agent', 'Pending')";
            mysqli_query($con, $email_log_query);

            // Send email to HR
            $subject = "Admin Login Alert - ID: $emp_id";
            $message = "
                <h3>Admin Login Notification</h3>
                <p><strong>Admin ID:</strong> $emp_id</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Time:</strong> $date</p>
                <p><strong>IP Address:</strong> $ip_address</p>
                <p><strong>Browser Info:</strong> $user_agent</p>
            ";

            if (send_email("hr@1solutions.biz", $subject, $message)) {
                $email_status = 'Success';
            } else {
                $email_status = 'Failed';
            }

            // Update email status
            mysqli_query($con, "UPDATE admin_login_logs SET email_status = '$email_status' WHERE admin_id = '$emp_id' AND action = 'login' AND timestamp = '$date'");
        }

        // Insert into hrm_login_detail
        $query_insert = "INSERT INTO hrm_login_detail(date_time, emp_id) VALUES('$date', '$emp_id')";
        mysqli_query($con, $query_insert);

        // Redirect
        if ($department_id == 4 || $department_id == 6) {
            echo "<script>window.location='admin-dashboard.php';</script>";
        } else {
            echo "<script>window.location='employee-dashboard.php';</script>";
        }

    } else {
        echo "<script>alert('Wrong User or password. Try forgot password or contact HR'); history.go(-1);</script>";
    }
}
?>
