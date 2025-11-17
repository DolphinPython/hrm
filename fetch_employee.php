<?php
include 'layouts/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if ID is provided
    if (!isset($_POST["id"]) || empty($_POST["id"])) {
        echo json_encode(['error' => 'No employee ID provided']);
        exit;
    }

    $id = $_POST["id"];

    // Verify database connection
    if (!$con) {
        echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
        exit;
    }

    $sql = "SELECT e.id, e.fname, e.lname, d.name as designation, e.salary 
            FROM hrm_employee e 
            LEFT JOIN hrm_designation d ON e.designation_id = d.id 
            WHERE e.id = ?";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare failed: ' . $con->error]);
        exit;
    }

    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
        exit;
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo json_encode($employee);
    } else {
        echo json_encode(['error' => 'No employee found with ID: ' . $id]);
    }

    $stmt->close();
    $con->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>