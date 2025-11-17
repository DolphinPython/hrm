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

<?php
$conn = connect();
$query = "SELECT ae.id, ae.fname, ae.lname, ae.email, ae.mobile1, ae.mobile2, ae.fathers_name, 
          DATE_FORMAT(ae.dob, '%Y-%m-%d') AS dob, 
          DATE_FORMAT(ae.doj, '%Y-%m-%d') AS doj, 
          DATE_FORMAT(ae.created_at, '%Y-%m-%d') AS created_at, 
          ae.department_id, ae.designation_id, hd.name AS designation_name, hdept.name AS department_name, ae.image 
          FROM archived_employees ae
          LEFT JOIN hrm_designation hd ON ae.designation_id = hd.id
          LEFT JOIN hrm_department hdept ON ae.department_id = hdept.id ORDER BY ae.created_at DESC;";
$result1 = mysqli_query($conn, $query);
if (!$result1) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<head>
    
    <!--<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">-->
    <title>Archived Employees</title>
   <?php include 'layouts/title-meta.php'; ?>

<?php include 'layouts/head-css.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"> 
    
</head>

<body>
    <div class="main-wrapper">
        <?php 
        include 'layouts/menu.php';
        ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Former Employees</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Former Employees</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="container mt-4">
                    <h2 class="text-center mb-4">Former Employees</h2>
                   
                    <div class="table-responsive">
                        <table id="employeeTable" class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>

                                    <th>Employee ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Mobile </th>
                                    <!--<th>Mobile 2</th>-->
                                    <!--<th>Father's Name</th>-->
                                    <th>Date of Birth</th>
                                    <th>Date of Joining</th>
                                    <th>Last Date</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $serial_number = 1;
                                while ($row = mysqli_fetch_assoc($result1)) { 
                                ?>
                                    <tr>
                                       
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['fname']; ?></td>
                                        <td><?php echo $row['lname']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['mobile1']; ?></td>
                                        <!--<td>-->
                                        <?php
                                        // echo $row['mobile2']; 
                                         ?>
                                        <!--</td>-->
                                        <!--<td>-->
                                          <?php 
                                        //   echo $row['fathers_name'];
                                         ?>
                                        <!--    </td>-->
                                        <td><?php echo $row['dob']; ?></td>
                                        <td><?php echo $row['doj']; ?></td>
                                        <td><?php echo $row['created_at']; ?></td>
                                        <td><?php echo $row['department_name']; ?></td>
                                        <td><?php echo $row['designation_name']; ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm viewDetails"
                                                data-id="<?php echo $row['id']; ?>" data-bs-toggle="modal"
                                                data-bs-target="#employeeModal">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Employee Details Modal -->
                <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Employee Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"><i class="fa-solid fa-xmark"></i>
</button>
                            </div>
                            <div class="modal-body" id="employeeDetails">
                                <!-- Full employee details will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>
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
        $(document).ready(function () {
            $('#employeeTable').DataTable();

            $(".viewDetails").click(function () {
                let empId = $(this).data("id");

                $.ajax({
                    url: "fetch_employee_details.php",
                    type: "POST",
                    data: { id: empId },
                    success: function (response) {
                        $("#employeeDetails").html(response);
                    }
                });
            });
        });
    </script>
    <!--<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>-->
</body>
</html>