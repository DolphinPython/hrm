<?php 
include 'layouts/config.php';

$emp_id = $_GET['emp_id'];
$query_family = "SELECT f.id, f.name, f.relationship_id, f.dependent, f.phone, r.name AS rname
                 FROM hrm_employee_family f
                 JOIN hrm_family_relationship_member r ON f.relationship_id = r.id
                 WHERE f.emp_id=?";
$stmt = mysqli_prepare($con, $query_family);
mysqli_stmt_bind_param($stmt, "s", $emp_id);
mysqli_stmt_execute($stmt);
$result_family = mysqli_stmt_get_result($stmt);

$output = "";
while ($row_family = mysqli_fetch_assoc($result_family)) {
    $output .= "<tr>";
    $output .= "<td>" . htmlspecialchars($row_family['name']) . "</td>";
    $output .= "<td>" . htmlspecialchars($row_family['rname']) . "</td>";
  
    $output .= "<td>" . htmlspecialchars($row_family['phone']) . "</td>";
    $output .= "<td class='text-end'>";
    $output .= "<a href='javascript:void(0);' class='edit-btn' data-id='" . $row_family['id'] . "' 
                data-name='" . htmlspecialchars($row_family['name']) . "' 
                data-relationship='" . $row_family['relationship_id'] . "' 
               
                data-phone='" . htmlspecialchars($row_family['phone']) . "' 
                data-dependent='" . $row_family['dependent'] . "'>
                <i class='fa-solid fa-pencil m-r-5'></i> Edit</a>";
    $output .= " <a href='javascript:void(0);' class='delete-btn' data-id='" . $row_family['id'] . "'>
                <i class='fa-regular fa-trash-can m-r-5'></i> Delete</a>";
    $output .= "</td>";
    $output .= "</tr>";
}

echo $output;
mysqli_close($con);
?>