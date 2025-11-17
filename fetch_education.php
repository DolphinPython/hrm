<?php 
include 'layouts/session.php'; 
include 'include/function.php';

header('Content-Type: application/json'); // Ensure JSON output
header('Access-Control-Allow-Origin: *'); // Allow cross-origin requests
header('Access-Control-Allow-Methods: GET, POST');

// Ensure no unwanted output
ob_start();

$emp_id = $_SESSION['id'];
$conn = connect();

// Fetch education details
$sql = "SELECT * FROM hrm_employee_education WHERE emp_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    if ($row['start_date'] === '0000-00-00') $row['start_date'] = '';
    if ($row['end_date'] === '0000-00-00') $row['end_date'] = '';
    $data[] = $row;
}

// Clean unwanted output and return JSON
ob_clean();
echo json_encode($data);
exit;
?>
