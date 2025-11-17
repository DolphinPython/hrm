<?php 
include 'layouts/session.php'; 
include 'include/function.php';

header('Content-Type: application/json'); 
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST');

// Ensure no unwanted output
ob_start();

$emp_id = $_SESSION['id'];
$conn = connect();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);  // Sanitize the input

    // Prepare the SQL query to fetch the education details by ID
    $sql = "SELECT * FROM hrm_employee_education WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);  // Bind the ID parameter to the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a record is found
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode($row);  // Return the record as JSON
        } else {
            echo json_encode(['error' => 'Education record not found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'Failed to prepare the query']);
    }

    $conn->close();
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>
