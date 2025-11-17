<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

// Get user name and other details
$emp_id = $_SESSION['id'];
$conn = connect();
$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

// Count active and inactive employees
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Management System</title>
        <?php include 'layouts/title-meta.php'; ?>

<?php include 'layouts/head-css.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"> 
    <style>
        .selectable td{
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="container padding-top-ams">
            <div class="container mt-5">
                <h1 class="mb-4">Salary Management System</h1>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Salary Management System</li>
                </ul>

                <!-- Employee Table -->
                <table id="employeeTable" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Designation</th>
                            <th>Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // $sql = "SELECT e.id, e.fname, e.lname, d.name as designation, e.salary 
                        //         FROM hrm_employee e 
                        //         LEFT JOIN hrm_designation d ON e.designation_id = d.id";
                        $sql = "SELECT e.id, e.fname, e.lname, d.name as designation, e.salary 
        FROM hrm_employee e 
        LEFT JOIN hrm_designation d ON e.designation_id = d.id 
        WHERE e.id != 14";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr data-id='{$row['id']}' class='selectable'>
                                        <td>{$row['id']}</td>
                                        <td>{$row['fname']} {$row['lname']}</td>
                                        <td>{$row['designation']}</td>
                                        <td>₹{$row['salary']}</td>
                                      </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Edit Employee Modal -->
            <div class="modal fade" id="editEmployeeModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Employee Salary</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editEmployeeForm">
                                <input type="hidden" id="employeeId">
                                
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" id="employeeName" readonly>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Designation</label>
                                    <input type="text" class="form-control" id="designation" readonly>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Salary</label>
                                    <input type="number" class="form-control" id="salary" required>
                                </div>
                                
                                <button type="submit" class="btn btn-success">Update Salary</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
             <div class="d-flex gap-2">
                    <a href="advancesalary.php" class="btn btn-primary mb-3">Advance Salary Management</a>
                    <a href="attandance-all-employee.php" class="btn btn-primary mb-3">Attendance HRM</a>
        </div>
           
        </div>
     
    </div>
    
</div>

 <?php 
include 'layouts/customizer.php';
 ?>
 <?php 
 include 'layouts/vendor-scripts.php';
 ?>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $("#employeeTable").DataTable({
        responsive: true,
        paging: false,
    });

    $(".selectable").click(function() {
        let id = $(this).data("id");

        $.ajax({
            url: "fetch_employee.php",
            type: "POST",
            data: { id: id },
            dataType: "json",
            success: function(data) {
                $("#employeeId").val(data.id);
                $("#employeeName").val(data.fname + ' ' + data.lname);
                $("#designation").val(data.designation);
                $("#salary").val(data.salary);
                $("#editEmployeeModal").modal("show");
            }
        });
    });

    $("#editEmployeeForm").submit(function(e) {
        e.preventDefault();
        let id = $("#employeeId").val();
        let salary = $("#salary").val();

        $.ajax({
            url: "update_salary.php",
            type: "POST",
            data: { id: id, salary: salary },
            success: function(response) {
                alert(response);
                location.reload();
            }
        });
    });
});
</script>

</body>
</html>