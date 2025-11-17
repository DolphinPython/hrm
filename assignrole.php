<?php
include 'layouts/config.php';
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';

$current_user_role = "super admin";

// Handle role update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $employee_id = $_POST['employee_id'];
    $new_role = $_POST['role'];
    if ($current_user_role === 'super admin') {
        $stmt = $con->prepare("UPDATE hrm_employee SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $employee_id);
        $stmt->execute();
        $stmt->close();
    }
}

$sql = "SELECT id, fname, lname, email, mobile1, role FROM hrm_employee ORDER BY id DESC";
$resultabc = $con->query($sql);




// get user name and other detail
$emp_id = $_SESSION['id'];
$conn = connect();
//$id=$_GET['id'];
$query = "select * from hrm_employee where id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$x = "";
$row = mysqli_fetch_array($result);
//echo "aaaaaaaaaaaaaaaa=".$query;

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;

$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];
//count_where($table, $column, $value)
//{
//$conn=connect();
//$query="select count(*) from $table where $column='$id'";
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

//echo "profile_image".$profile_image;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Assign Roles</title>
    <?php include 'layouts/title-meta.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <?php include 'layouts/head-css.php'; ?>
    <style>
        body {
            background-color: #f9f9f9;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .filter-container {
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php 
            include 'layouts/menu.php';
?>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            <div class="content container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Role</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Role</li>
                            </ul>
                        </div>

                    </div>
                </div>
                <h2 class="mb-4 text-center">Employee Role Assignment</h2>
                <div class="filter-container">
                    <label for="roleFilter" class="form-label">Filter by Role:</label>
                    <select id="roleFilter" class="form-select w-auto d-inline-block">
                        <option value="">All Roles</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <option value="super admin">Super Admin</option>
                    </select>
                </div>
                <div class="container">
                <div class="table-responsive">
                    <table id="roleTable" class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Current Role</th>
                                <?php if ($current_user_role === 'super admin'): ?>
                                    <th>Change Role</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $resultabc->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['mobile1']) ?></td>
                                    <td data-search="<?= htmlspecialchars($row['role']) ?>"
                                        data-order="<?= htmlspecialchars($row['role']) ?>">
                                        <?= htmlspecialchars($row['role']) ?>
                                    </td>
                                    <?php if ($current_user_role === 'super admin'): ?>
                                        <td>
                                            <form method="post" class="d-flex align-items-center gap-2">
                                                <input type="hidden" name="employee_id" value="<?= $row['id'] ?>">
                                                <select name="role" class="form-select form-select-sm w-auto" required>
                                                    <option value="user" <?= $row['role'] === 'user' ? 'selected' : '' ?>>User
                                                    </option>
                                                    <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin
                                                    </option>
                                                    <option value="super admin" <?= $row['role'] === 'super admin' ? 'selected' : '' ?>>Super Admin</option>
                                                </select>
                                                <button type="submit" name="update_role"
                                                    class="btn btn-sm btn-success">Update</button>
                                            </form>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                            <?php $con->close(); ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <?php include 'layouts/customizer.php'; ?>
    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>
    <!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            const table = $('#roleTable').DataTable({
                "columnDefs": [
                    { "orderable": false, "targets": <?= ($current_user_role === 'super admin') ? '[4]' : '[]' ?> },
                    { "searchable": true, "targets": [4] } 
                ],
                "search": {
                    // "return": true 
                },
                // Custom search function for the role column
                initComplete: function () {
                    this.api()
                        .columns([3])
                        .every(function () {
                            let column = this;
                            column.data().each(function (d, j) {
                                let cell = $(column.nodes()[j]);
                                cell.attr('data-search', cell.text().trim());
                            });
                        });
                }
            });

            // Role filter dropdown
            $('#roleFilter').on('change', function () {
                const selectedRole = $(this).val();
                if (selectedRole) {
                    table.column(3).search('^' + selectedRole + '$', true, false).draw();
                } else {
                    table.column(3).search('').draw();
                }
            });
        });
    </script>

</body>

</html>