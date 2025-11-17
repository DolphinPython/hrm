<?php 
include "include/function.php";
$conn = connect();

$emp_id = $_GET['emp_id'];
$office_email = $_GET['office_email'];
$email = $_GET['email'];
$current_address = $_GET['current_address'];
$permanent_address = $_GET['permanent_address'];
$house_type = $_GET['house_type'];
$staying_current_residence = $_GET['staying_current_residence'];
$living_current_city = $_GET['living_current_city'];
$facebook = $_GET['facebook'];
$twitter = $_GET['twitter'];
$linkedin = $_GET['linkedin'];

$date = date("Y-m-d");

// ✅ Check for email or office_email already used by another employee
$checkQuery = "SELECT * FROM hrm_employee 
               WHERE (email = '$email' OR office_email = '$office_email') 
               AND id != '$emp_id'";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    echo "Email or Office Email already exists. Please enter a different one.";
    exit; // ⛔ Stop execution here if duplicate found
}

// ✅ Now perform the update
$query = "UPDATE hrm_employee SET 
    office_email = '$office_email', 
    email = '$email', 
    current_address = '$current_address', 
    permanent_address = '$permanent_address', 
    house_type = '$house_type',
    staying_current_residence = '$staying_current_residence', 
    living_current_city = '$living_current_city' 
    WHERE id = '$emp_id'";

mysqli_query($conn, $query) or die("Error in updating employee info: " . mysqli_error($conn));

// ✅ Handle social media insert/update
$socialCheck = mysqli_query($conn, "SELECT * FROM hrm_employee_social WHERE emp_id = '$emp_id'");
if (mysqli_num_rows($socialCheck) > 0) {
    $updateSocial = "UPDATE hrm_employee_social SET 
        added_date = '$date', 
        facebook = '$facebook', 
        twitter = '$twitter', 
        linkedin = '$linkedin' 
        WHERE emp_id = '$emp_id'";
    mysqli_query($conn, $updateSocial) or die("Error updating social info: " . mysqli_error($conn));
} else {
    $insertSocial = "INSERT INTO hrm_employee_social 
        (emp_id, added_date, facebook, twitter, linkedin) 
        VALUES ('$emp_id', '$date', '$facebook', '$twitter', '$linkedin')";
    mysqli_query($conn, $insertSocial) or die("Error inserting social info: " . mysqli_error($conn));
}

// ✅ Final message
echo "Contact Info Updated";
?>
