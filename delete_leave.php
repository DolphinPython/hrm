<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');
date_default_timezone_set('Asia/Kolkata');

// Include necessary files
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';

// Establish database connection
$conn = connect(); // Ensure this function is defined in 'include/function.php'

// Check if the leave ID is set
if (isset($_POST['leave_id'])) {
    $leave_id = mysqli_real_escape_string($conn, $_POST['leave_id']);

    // Debugging: Print the leave ID to ensure it's being passed correctly
    echo "Leave ID to delete: " . $leave_id . "<br>";

    // Delete the leave application from the database
    $query = "DELETE FROM hrm_leave_applied WHERE id='$leave_id';";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('Leave Deleted Successfully!'); window.location.href='leaves-employee.php';</script>";
    } else {
        echo "<script>alert('Error Deleting Leave!'); window.location.href='leaves-employee.php';</script>";
    }
} else {
    echo "<script>alert('Invalid Request!'); window.location.href='leaves-employee.php';</script>";
}
?>