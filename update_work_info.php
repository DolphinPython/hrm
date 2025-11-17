<?php
include "include/function.php";

$conn = connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve and sanitize input data
$emp_id = isset($_GET['emp_id']) ? mysqli_real_escape_string($conn, $_GET['emp_id']) : '';
$doj = isset($_GET['doj']) ? mysqli_real_escape_string($conn, $_GET['doj']) : '';
$probation_period = isset($_GET['probation_period']) ? mysqli_real_escape_string($conn, $_GET['probation_period']) : '';
$employee_type = isset($_GET['employee_type']) ? mysqli_real_escape_string($conn, $_GET['employee_type']) : '';
$work_location = isset($_GET['work_location']) ? mysqli_real_escape_string($conn, $_GET['work_location']) : '';
$experience = isset($_GET['experience']) ? mysqli_real_escape_string($conn, $_GET['experience']) : '';
$designation_id = isset($_GET['designation_id']) ? mysqli_real_escape_string($conn, $_GET['designation_id']) : '';
$job_title = isset($_GET['job_title']) ? mysqli_real_escape_string($conn, $_GET['job_title']) : '';
$department_id = isset($_GET['department_id']) ? mysqli_real_escape_string($conn, $_GET['department_id']) : '';

// Validate required fields
if (empty($emp_id) || empty($doj) || empty($work_location) || empty($designation_id) || empty($department_id)) {
    echo "Error: Required fields are missing.";
    exit;
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Check if the employee exists
    $query = "SELECT * FROM hrm_employee WHERE id = '$emp_id'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 0) {
        throw new Exception("Employee not found.");
    }

    // Generate emp_id if not already set
    // Generate emp_id if not already set
    $row = mysqli_fetch_assoc($result);
    if (empty($row['emp_id'])) {
        // Get the year from doj (last two digits)
        $year = date("y", strtotime($doj));

        // Get the department code from hrm_department code column
        $dept_query = "SELECT code FROM hrm_department WHERE id = '$department_id'";
        $dept_result = mysqli_query($conn, $dept_query);
        if (!$dept_result || mysqli_num_rows($dept_result) == 0) {
            throw new Exception("Invalid department ID.");
        }
        $dept_row = mysqli_fetch_assoc($dept_result);
        $dept_code = strtoupper($dept_row['code']);

        // Count employees in the current doj year to determine sequential number
        $count_query = "SELECT COUNT(*) as count FROM hrm_employee WHERE emp_id LIKE 'EXP-$year%'";
        $count_result = mysqli_query($conn, $count_query);
        $count_row = mysqli_fetch_assoc($count_result);
        $sequential_number = sprintf("%04d", $count_row['count'] + 1);

        // Generate emp_id
        $generated_emp_id = "EXP-$year-$sequential_number-$dept_code";

        // Update emp_id in the employee record
        $emp_id_query = "UPDATE hrm_employee SET emp_id = '$generated_emp_id' WHERE id = '$emp_id'";
        if (!mysqli_query($conn, $emp_id_query)) {
            throw new Exception("Failed to set emp_id: " . mysqli_error($conn));
        }
    }

    // Update work information
    $query = "UPDATE hrm_employee 
              SET doj = '$doj', 
                  probation_period = '$probation_period', 
                  employee_type = '$employee_type', 
                  work_location = '$work_location', 
                  experience = '$experience', 
                  designation_id = '$designation_id', 
                  job_title = '$job_title', 
                  department_id = '$department_id' 
              WHERE id = '$emp_id'";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        throw new Exception("Update failed: " . mysqli_error($conn));
    }

    // Commit transaction
    mysqli_commit($conn);
    
    echo "Work Info Updated";
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    echo "Error: " . $e->getMessage();
}

mysqli_close($conn);
?>