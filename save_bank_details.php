<?php
include 'layouts/session.php';
include 'include/function.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
ob_start();

$emp_id = $_SESSION['id'];
$conn = connect();

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Debug: Log received POST data
file_put_contents('debug.log', print_r($_POST, true));

$bank_id = isset($_POST['bank_id']) && !empty($_POST['bank_id']) ? mysqli_real_escape_string($conn, $_POST['bank_id']) : null;
$new_id = isset($_POST['new_id']) ? mysqli_real_escape_string($conn, $_POST['new_id']) : null;
$bank_name = mysqli_real_escape_string($conn, $_POST['bank_name'] ?? '');
$account_type = mysqli_real_escape_string($conn, $_POST['account_type'] ?? '');
$account_holder_name = mysqli_real_escape_string($conn, $_POST['account_holder_name'] ?? '');
$account_number = mysqli_real_escape_string($conn, $_POST['account_number'] ?? '');
$ifsc = mysqli_real_escape_string($conn, $_POST['ifsc'] ?? '');
$branch = mysqli_real_escape_string($conn, $_POST['branch'] ?? '');
$pan = mysqli_real_escape_string($conn, $_POST['pan'] ?? '');

if ($bank_id) {
    // Update existing record
    $query = "UPDATE hrm_bank_detail SET 
                bank_name='$bank_name', 
                account_type='$account_type',
                account_holder_name='$account_holder_name',  
                account_number='$account_number',
                ifsc='$ifsc',
                branch='$branch',
                pan='$pan' 
              WHERE id='$bank_id' AND emp_id='$emp_id'";
} else {
    // Insert new record
    $query = "INSERT INTO hrm_bank_detail (bank_name, account_type, account_holder_name, account_number, ifsc, branch, pan, emp_id) 
              VALUES ('$bank_name', '$account_type', '$account_holder_name', '$account_number', '$ifsc', '$branch', '$pan', '$new_id')";
}

if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success', 'message' => 'Saved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save data: ' . mysqli_error($conn)]);
}

ob_end_flush();
?>