<?php
include 'layouts/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $salary = $_POST["salary"];

    $sql = "UPDATE hrm_employee SET salary = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("di", $salary, $id); // "di" -> d (double/decimal) for salary, i (integer) for id

    if ($stmt->execute()) {
        echo "Salary updated successfully!";
    } else {
        echo "Error updating salary.";
    }

    $stmt->close();
}
?>