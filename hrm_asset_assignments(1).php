<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';
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
$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];
//count_where($table, $column, $value)
//{
//$conn=connect();
//$query="select count(*) from $table where $column='$id'";
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

//echo "profile_image".$profile_image;


$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

if ($row['department_id'] != 4 and $row['department_id'] != 6) {
    header("Location:attendance-report-employee.php");
}

?>

<head>

    <title> Reports - HRMS admin template</title>

    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>

   
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>
       <?php

        // Handle Asset Assignment
        if (isset($_POST['assign'])) {
            $asset_id = $_POST['asset_id'];
            $assignee_id = $_POST['assignee_id'];
            $issued_date = $_POST['issued_date'];

            // Check if the asset exists
            $sql_check = "SELECT * FROM hrm_assets WHERE id = '$asset_id'";
            $result_check = $conn->query($sql_check);

            if ($result_check->num_rows == 0) {
                $message = "Asset with ID $asset_id does not exist.";
                $alert_class = "alert-danger";
            } else {
                // Check if the asset is already assigned
                $sql_check_assignment = "SELECT * FROM hrm_asset_assignments WHERE asset_id = '$asset_id' AND return_date IS NULL";
                $result_check_assignment = $conn->query($sql_check_assignment);

                if ($result_check_assignment->num_rows > 0) {
                    $message = "Asset is already assigned to another employee.";
                    $alert_class = "alert-warning";
                } else {
                    // Insert assignment
                    $sql_insert = "INSERT INTO hrm_asset_assignments (asset_id, assignee_id, issued_date) 
                           VALUES ('$asset_id', '$assignee_id', '$issued_date')";
                    if ($conn->query($sql_insert) === TRUE) {
                        $message = "Asset successfully assigned!";
                        $alert_class = "alert-success";
                    } else {
                        $message = "Error: " . $conn->error;
                        $alert_class = "alert-danger";
                    }
                }
            }
        }

        // Handle Asset Return
        if (isset($_POST['return'])) {
            $assignment_id = $_POST['assignment_id'];
            $return_date = date('Y-m-d');

            $sql_update = "UPDATE hrm_asset_assignments 
                   SET return_date = '$return_date' 
                   WHERE id = '$assignment_id' AND return_date IS NULL";
            if ($conn->query($sql_update) === TRUE) {
                $message = "Asset returned successfully.";
                $alert_class = "alert-success";
            } else {
                $message = "Error updating record: " . $conn->error;
                $alert_class = "alert-danger";
            }
        }

        // Handle Asset Deletion
        if (isset($_POST['delete'])) {
            $assignment_id = $_POST['assignment_id'];

            $sql_delete = "DELETE FROM hrm_asset_assignments WHERE id = '$assignment_id'";
            if ($conn->query($sql_delete) === TRUE) {
                $message = "Asset assignment deleted successfully.";
                $alert_class = "alert-success";
            } else {
                $message = "Error deleting record: " . $conn->error;
                $alert_class = "alert-danger";
            }
        }
        $search_term = isset($_GET['search']) ? $_GET['search'] : '';

        // Fetch dropdown data
        $sql_assets = "SELECT id, asset_id, asset_name, image FROM hrm_assets";
        $assets_result = $conn->query($sql_assets);

        $sql_employees = "SELECT id, fname, lname FROM hrm_employee";
        $employees_result = $conn->query($sql_employees);

        // Fetch assigned assets
        $sql_assigned = "SELECT aa.id AS assignment_id, a.asset_id, a.asset_name, a.image, 
                        e.id AS assignee_id, e.fname, e.lname, aa.issued_date, aa.return_date
                 FROM hrm_asset_assignments aa 
                 JOIN hrm_assets a ON aa.asset_id = a.id 
                 JOIN hrm_employee e ON aa.assignee_id = e.id
                 WHERE 
                     a.asset_id LIKE '%$search_term%' OR
                     a.asset_name LIKE '%$search_term%' OR
                     e.id LIKE '%$search_term%' OR
                     e.fname LIKE '%$search_term%' OR
                     e.lname LIKE '%$search_term%' OR
                     aa.issued_date LIKE '%$search_term%' OR
                     aa.return_date LIKE '%$search_term%'";
        $result_assigned = $conn->query($sql_assigned);
        ?>
        <!-- Page Wrapper -->
        <div class="page-wrapper">
        <div class="container padding-top-ams">
            <h1 class="mb-4">Asset Management System</h1>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Asset Management System</li>
            </ul>
            <a href="hrm_assets.php" class="m-4"><button class="btn btn-success">Add new assets</button></a>
            <!-- Display Messages -->
            <?php if (isset($message)): ?>
            <div class="alert <?= $alert_class ?>"><?= $message ?></div>
            <?php endif; ?>

            <!-- Asset Assignment Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Assign Asset
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="asset_id" class="form-label">Select Asset</label>
                            <select name="asset_id" id="asset_id" class="form-select" required>
                                <option value="">Select</option>
                                <?php while ($row = $assets_result->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>">
                                    <?= $row['asset_id'] . ' - ' . $row['asset_name'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="assignee_id" class="form-label">Select Employee</label>
                            <select name="assignee_id" id="assignee_id" class="form-select" required>
                                <option value="">Select</option>
                                <?php while ($row = $employees_result->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>">
                                    <?= $row['id'] . ' - ' . $row['fname'] . ' ' . $row['lname'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="issued_date" class="form-label">Issued Date</label>
                            <input type="date" name="issued_date" id="issued_date" class="form-control" required>
                        </div>
                        <button type="submit" name="assign" class="btn btn-success">Assign Asset</button>
                    </form>
                </div>
            </div>

            <!-- Assigned Assets Table -->
            <h2 class="text-center">Assigned Assets</h2>
            <div class="mb-4">
                <form method="GET" class="form-inline">
                    <div class="mb-3">
                        <label for="search" class="form-label">Search: </label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Search by Asset ID, Asset Name, Employee, etc."
                            value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Asset ID</th>
                            <th>Asset Name</th>
                            <th>Employee ID</th>
                            <th>Employee</th>
                            <th>Issued Date</th>
                            <th>Return Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_assigned->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                <img src="<?= $row['image'] ?>" alt="Image" width="50" height="50" class="rounded">
                                <?php else: ?>
                                <span>No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['asset_id'] ?></td>
                            <td><?= $row['asset_name'] ?></td>
                            <td><?= $row['assignee_id'] ?></td>
                            <td><?= $row['fname'] . ' ' . $row['lname'] ?></td>
                            <td><?= $row['issued_date'] ?></td>
                            <td><?= $row['return_date'] ?? 'N/A' ?></td>
                            <td>
                                <?php if (!$row['return_date']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="assignment_id" value="<?= $row['assignment_id'] ?>">
                                    <button type="submit" name="return" class="btn btn-warning btn-sm">Return</button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="assignment_id" value="<?= $row['assignment_id'] ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        <!-- /Page Wrapper -->

    </div>
    <!-- end main wrapper-->
    <?php include 'layouts/customizer.php'; ?>
    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>



</body>

</html>