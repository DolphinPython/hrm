<?php 
include 'layouts/config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['family_id'] ?? '';
    $name = $_POST['family_name'];
    $relationship_id = $_POST['relationship_id'];

    $phone = $_POST['phone'];
    $dependent = $_POST['dependent'];
    $emp_id = $_POST['emp_id'];

    if ($id) {
        // Update existing record
        $query = "UPDATE hrm_employee_family SET name=?, relationship_id=?, phone=?, dependent=? WHERE id=? AND emp_id=?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $name, $relationship_id,  $phone, $dependent, $id, $emp_id);
    } else {
        // Insert new record
        $query = "INSERT INTO hrm_employee_family (emp_id, name, relationship_id,  phone, dependent) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $emp_id, $name, $relationship_id, $phone, $dependent);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo "Success";
    } else {
        echo "Error: " . mysqli_error($con);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}
?>