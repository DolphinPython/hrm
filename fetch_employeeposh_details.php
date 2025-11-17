<?php
include 'include/function.php'; // Include your database connection and helper functions

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
    $conn = connect();
    $employee_id = (int)$_POST['employee_id'];

    // Fetch employee details with department and designation names
    $query = "SELECT e.department_id, e.designation_id, 
                     d.name AS department_name, 
                     des.name AS designation_name 
              FROM hrm_employee e
              LEFT JOIN hrm_department d ON e.department_id = d.id
              LEFT JOIN hrm_designation des ON e.designation_id = des.id
              WHERE e.id = '$employee_id'";
    $result = mysqli_query($conn, $query);

    $response = ['department' => '', 'designation' => ''];

    if ($row = mysqli_fetch_assoc($result)) {
        $response['department'] = $row['department_name'] ?: '';
        $response['designation'] = $row['designation_name'] ?: '';
    }

    mysqli_close($conn);
    echo json_encode($response);
    exit();
}
?>