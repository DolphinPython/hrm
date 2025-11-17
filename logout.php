<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include 'layouts/config.php';

$date = date("Y-m-d H:i:s");

if (isset($_SESSION['id']) && in_array($_SESSION['id'], [10, 14])) {
    $admin_id = $_SESSION['id'];
    $email = $_SESSION['email'];

    $logout_query = "INSERT INTO admin_login_logs (admin_id, email, action, timestamp)
                     VALUES ('$admin_id', '$email', 'logout', '$date')";
    mysqli_query($con, $logout_query);
}

session_destroy();
header("Location: index.php");
exit;
?>
