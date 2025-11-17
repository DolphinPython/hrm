<?php
include 'layouts/config.php';
// Function to generate a random password
function generatePassword($length = 10) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#&!"), 0, $length);
}

// Handle bulk password reset
if (isset($_POST['reset_password'])) {
    $selected_ids = $_POST['selected_ids'];
    foreach ($selected_ids as $id) {
        $new_password = generatePassword();
        $con->query("UPDATE hrm_employee SET password='$new_password' WHERE id=$id");
    }
    echo json_encode(["status" => "success", "message" => "Passwords updated successfully"]);
    exit;
}

// Handle individual password reset
if (isset($_POST['reset_single_password'])) {
    $id = $_POST['id'];
    $new_password = generatePassword();
    $con->query("UPDATE hrm_employee SET password='$new_password' WHERE id=$id");
    echo json_encode(["status" => "success", "password" => "$new_password"]);
    exit;
}

// Fetch Employees Data
$employees = $con->query("SELECT id, fname, lname, email, mobile1, password FROM hrm_employee");
?>
  

<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';
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

$conn = connect();

// Handle Add Department
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_department'])) {
    $name = $_POST['name'];
    $query = "INSERT INTO hrm_department (name) VALUES ('$name')";
    if (mysqli_query($conn, $query)) {
        header("Location: departments.php"); // Redirect to refresh the page
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle Edit Department
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_department'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $query = "UPDATE hrm_department SET name='$name' WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        header("Location: departments.php"); // Redirect to refresh the page
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle Delete Department
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM hrm_department WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        header("Location: departments.php"); // Redirect to refresh the page
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch all departments
$query = "SELECT * FROM hrm_department";
$result = mysqli_query($conn, $query);
$departments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
} else {
    echo "Error fetching departments: " . mysqli_error($conn);
}

?>





<!-- Support Page -->
    <style>
        a{
            text-decoration: none !important;
        }
    </style>
    <?php include 'layouts/title-meta.php'; ?>

<?php include 'layouts/head-css.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"> 
<!-- Supprot Page -->


    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            <div class="container mt-3">
                <h2 class="text-center mb-4">Employee Password Management</h2>
               
               
                <div class="table-responsive">
                    <table id="employeeTable" class="table table-bordered table-striped w-100">
                        <thead>
                            <tr>
                                <th class="text-nowrap"><input type="checkbox" id="selectAll"></th>
                                <th class="text-nowrap">Name</th>
                                <th class="text-nowrap">Email</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">Password</th>
                                <th class="text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $employees->fetch_assoc()): ?>
                                <tr>
                                    <td><input type="checkbox" class="selectEmp" value="<?= $row['id'] ?>"></td>
                                    <td><?= $row['fname'] . ' ' . $row['lname'] ?></td>
                                    <td><?= $row['email'] ?></td>
                                    <td><?= $row['mobile1'] ?></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="password" class="form-control password-field"
                                                value="<?= $row['password'] ?>" readonly>
                                            <button class="btn btn-sm btn-secondary togglePassword"><i
                                                    class="fa fa-eye"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning resetSingle"
                                            data-id="<?= $row['id'] ?>">Reset</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-grid gap-2 d-md-flex justify-content-md-end">
                    <button id="resetSelected" class="btn btn-danger w-100 w-md-auto">Reset Selected Passwords</button>
                </div>
            </div>


        </div>
        <!-- end main wrapper-->


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
                $('#employeeTable').DataTable({
                    responsive: true,
                    paging:false,
                    columnDefs: [
                        { responsivePriority: 1, targets: 0 }, // Checkbox column
                        { responsivePriority: 2, targets: 5 }, // Action column
                        { responsivePriority: 3, targets: 1 }  // Name column
                    ]
                });

                $('#selectAll').on('click', function () {
                    $('.selectEmp').prop('checked', this.checked);
                });

                $('#resetSelected').on('click', function () {
                    let selectedIds = $('.selectEmp:checked').map(function () {
                        return this.value;
                    }).get();

                    if (selectedIds.length === 0) {
                        alert('No employees selected');
                        return;
                    }

                    $.post('', { reset_password: true, selected_ids: selectedIds }, function (response) {
                        alert(response.message);
                        location.reload();
                    }, 'json');
                });

                $('.resetSingle').on('click', function () {
                    let id = $(this).data('id');
                    if (!confirm('Are you sure you want to reset this password?')) return;

                    $.post('', { reset_single_password: true, id: id }, function (response) {
                        alert('New Password: ' + response.password);
                        location.reload();
                    }, 'json');
                });

                $('.togglePassword').on('click', function () {
                    let passwordField = $(this).closest('.input-group').find('.password-field');
                    let type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                    passwordField.attr('type', type);
                    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
                });
            });
        </script>


</body>

</html>