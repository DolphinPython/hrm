<?php


include 'include/db.php';
$conn = connect();

session_start();
$voter_id = 13; // logged-in employee
$current_batch = "EOM_Dec_2025";
$total_points = 30;

/* Duplicate vote check */
$check = mysqli_query($conn, "
    SELECT id FROM eom_votes 
    WHERE voter_id = '$voter_id' 
    AND eom_batch = '$current_batch'
");

if (mysqli_num_rows($check) > 0) {
    die("❌ You have already voted");
}

/* Eligible employees count */
$countRes = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM eom 
    WHERE eom_batch = '$current_batch'
");
$total_eligible = mysqli_fetch_assoc($countRes)['total'];

$points = $total_points / $total_eligible;

$eom_id = $_POST['eom_id'];

/* Get existing points */
$getOld = mysqli_query($conn, "
    SELECT emp_point FROM eom WHERE id = '$eom_id'
");
$old_point = mysqli_fetch_assoc($getOld)['emp_point'];

$new_point = $old_point + $points;

/* Update points */
mysqli_query($conn, "
    UPDATE eom 
    SET emp_point = '$new_point' 
    WHERE id = '$eom_id'
");

/* Save vote record */
mysqli_query($conn, "
    INSERT INTO eom_votes (voter_id, eom_batch) 
    VALUES ('$voter_id', '$current_batch')
");

echo "✅ Vote submitted successfully";




?>