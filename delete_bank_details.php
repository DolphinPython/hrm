<?php 
include 'layouts/session.php'; 
include 'include/function.php';
// include 'db.php'; // Ensure database connection is included

header('Content-Type: application/json'); // Ensure JSON output
header('Access-Control-Allow-Origin: *'); // Allow cross-origin requests
header('Access-Control-Allow-Methods: GET, POST');
// Ensure no unwanted output
ob_start();

$emp_id = $_SESSION['id'];
$conn = connect();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bank_id'])) {
    $bank_id = $_POST['bank_id'];
    // Delete query to remove bank details
    $query = "DELETE FROM hrm_bank_detail WHERE id = '$bank_id'";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>