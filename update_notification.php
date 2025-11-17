<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'layouts/session.php';
include 'include/function.php';

if (isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    $conn = connect();
    $update_query = "UPDATE hrm_notification SET title='$title', description='$description' WHERE id='$edit_id'";
    
    if (mysqli_query($conn, $update_query)) {
        echo "Announcement updated successfully!";
    } else {
        echo "Error updating announcement: " . mysqli_error($conn);
    }
    
    mysqli_close($conn);
    exit;
}
?>