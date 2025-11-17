<?php
include 'layouts/session.php';
include 'include/function.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

$conn = connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bank_id'])) {
    $bank_id = mysqli_real_escape_string($conn, $_POST['bank_id']);
    $query = "SELECT * FROM hrm_bank_detail WHERE id = '$bank_id'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No record found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>