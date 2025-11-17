<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

$emp_id = $_SESSION['id'];
$conn = connect();
$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);
$profile_image = "upload-image/" . $row['image'];

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");


$role_query = "SELECT role FROM hrm_employee WHERE id = '$emp_id'";
$role_result = mysqli_query($conn, $role_query) or die(mysqli_error($conn));
$role_row = mysqli_fetch_assoc($role_result);
$is_admin = ($role_row && in_array(strtolower($role_row['role']), ['admin', 'super admin']));
// Restrict access to admin ID 10 and 14 only
if (!$is_admin) {
    echo "Access denied";
    exit;
}
include 'layouts/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login Logs</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
</head>
<body class="bg-light">

<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="container mt-5">
            <h2 class="mb-4 text-center">Admin Login/Logout Logs</h2>

            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="date" id="startDate" class="form-control" placeholder="Start Date">
                </div>
                <div class="col-md-3">
                    <input type="date" id="endDate" class="form-control" placeholder="End Date">
                </div>
                <div class="col-md-3">
                    <select id="actionFilter" class="form-select">
                        <option value="">All Actions</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-secondary" onclick="resetFilters()">Reset Filters</button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="adminLogsTable" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr.</th>
                            <th>Admin ID</th>
                            <th>Email</th>
                            <th>Action</th>
                            <th>Timestamp</th>
                            <th>IP Address</th>
                            <th>Browser/Device</th>
                            <th>Email Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysqli_query($con, "SELECT * FROM admin_login_logs ORDER BY timestamp DESC");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['admin_id']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['action']}</td>
                                <td>{$row['timestamp']}</td>
                                <td>{$row['ip_address']}</td>
                                <td>{$row['browser_info']}</td>
                                <td><span class='badge bg-" . ($row['email_status'] === 'success' ? 'success' : 'danger') . "'>{$row['email_status']}</span></td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    let table = $('#adminLogsTable').DataTable({
        dom: 'Bfrtip',
        ordering: false,
        pageLength: 30,
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });

    $('#startDate, #endDate, #actionFilter').on('change', function () {
        filterTable(table);
    });
});

function filterTable(table) {
    let startDate = $('#startDate').val();
    let endDate = $('#endDate').val();
    let action = $('#actionFilter').val();

    table.rows().every(function () {
        let data = this.data();
        let actionMatch = !action || data[3].toLowerCase() === action.toLowerCase();
        let dateMatch = true;

        if (startDate || endDate) {
            let logDate = new Date(data[4]);
            if (startDate && logDate < new Date(startDate)) dateMatch = false;
            if (endDate && logDate > new Date(endDate)) dateMatch = false;
        }

        if (actionMatch && dateMatch) {
            $(this.node()).show();
        } else {
            $(this.node()).hide();
        }
    });
}

function resetFilters() {
    $('#startDate').val('');
    $('#endDate').val('');
    $('#actionFilter').val('');
    $('#adminLogsTable').DataTable().rows().every(function () {
        $(this.node()).show();
    });
}
</script>
</body>
</html>
