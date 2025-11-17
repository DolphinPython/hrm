<?php
include "include/function.php"; // Include database connection file
$conn = connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    if (empty($id)) {
        echo "Invalid holiday ID.";
        exit;
    }

    $query = "DELETE FROM hrm_holidays WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        echo "Holiday deleted successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
