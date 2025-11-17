<?php
include "include/function.php";
$conn = connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve and sanitize input data
$fname = isset($_GET['fname']) ? mysqli_real_escape_string($conn, $_GET['fname']) : '';
$lname = isset($_GET['lname']) ? mysqli_real_escape_string($conn, $_GET['lname']) : '';
$dob = isset($_GET['dob']) ? mysqli_real_escape_string($conn, $_GET['dob']) : '';
$gender = isset($_GET['gender']) ? mysqli_real_escape_string($conn, $_GET['gender']) : '';
$bgroup = isset($_GET['bgroup']) ? mysqli_real_escape_string($conn, $_GET['bgroup']) : '';
$marital_status = isset($_GET['marital_status']) ? mysqli_real_escape_string($conn, $_GET['marital_status']) : '';

// Validate required fields
if (empty($fname) || empty($dob) || empty($gender)) {
    echo "Error: Required fields (First Name, Date of Birth, Gender) are missing.";
    exit;
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Insert personal info into hrm_employee
    $query = "INSERT INTO hrm_employee (fname, lname, dob, gender, bgroup, marital_status, status) 
              VALUES ('$fname', '$lname', '$dob', '$gender', '$bgroup', '$marital_status', '1')";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        throw new Exception("Insert failed: " . mysqli_error($conn));
    }

    // Get the last inserted ID
    $last_id = mysqli_insert_id($conn);
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Return the last inserted ID
    echo $last_id;
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    echo "Error: " . $e->getMessage();
}

mysqli_close($conn);
?>