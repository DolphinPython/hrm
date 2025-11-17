<?php
include 'layouts/session.php';
include 'include/function.php';

$conn = connect();
$emp_id = $_POST['emp_id'];
$edu_ids = $_POST['edu_id'];
$colleges = $_POST['college'];
// Add other fields similarly

foreach ($edu_ids as $index => $edu_id) {
    $college = $colleges[$index];
    // Get other field values
    
    if($edu_id === 'new') {
        // Insert new record
        $query = "INSERT INTO hrm_employee_education 
                 emp_id, college_name, qualification_type, 
                 VALUES (?, ?, ?, ...)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss...", $emp_id, $college, $qualification, ...);
        $stmt->execute();
    } else {
        // Update existing record
        $query = "UPDATE hrm_employee_education SET 
                 college_name = ?, qualification_type = ?, ...
                 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss...i", $college, $qualification, ..., $edu_id);
        $stmt->execute();
    }
}

header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit();
