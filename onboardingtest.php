<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

$emp_id = $_SESSION['id'];
$conn = connect();
$query = "select * from hrm_employee where id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);
$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

include 'layouts/config.php';

// Insert Step
if (isset($_POST['add'])) {
    $name = $_POST['step_name'];
    $desc = $_POST['description'];
    $order_query = "SELECT MAX(step_order) as max_order FROM onboarding_steps";
    $order_result = $con->query($order_query);
    $max_order = $order_result->fetch_assoc()['max_order'] ?? -1;
    $new_order = $max_order + 1;
    
    $con->query("INSERT INTO onboarding_steps (step_name, description, step_order) VALUES ('$name', '$desc', '$new_order')");
    echo "<script>window.location.href='onboardingtest.php';</script>";
}

// Update Step
if (isset($_POST['update'])) {
    $id = $_POST['step_id'];
    $name = $_POST['step_name'];
    $desc = $_POST['description'];
    $con->query("UPDATE onboarding_steps SET step_name='$name', description='$desc' WHERE step_id=$id");
    echo "<script>window.location.href='onboardingtest.php';</script>";
}

// Delete Step
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $con->query("DELETE FROM onboarding_steps WHERE step_id=$id");
    // Reorder remaining steps
    $con->query("SET @order = -1;");
    $con->query("UPDATE onboarding_steps SET step_order = (@order := @order + 1) ORDER BY step_order ASC");
    echo "<script>window.location.href='onboardingtest.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding Steps CRUD</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"> 
    <style>
        .sortable tbody tr { cursor: move; }
        .sortable tbody tr:hover { background-color: #f5f5f5; }
    </style>
</head>
<body class="bg-light">

<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="container padding-top-ams">
            <div class="container mt-5">
                <h1 class="mb-4">Onboarding Add Steps</h1>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Onboarding Add Steps Management System</li>
                </ul>
         
                <h2 class="text-center">Onboarding Steps</h2>

                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus px-2"></i>Add Step</a>
                    <a href="onboarding.php" class="btn btn-primary mb-3"><i class="fas fa-arrow-left px-2"></i>Return Boarding Management</a>
                </div>

                <!-- Table -->
                <table id="stepsTable" class="table table-striped sortable">
                    <thead class="table-dark">
                        <tr>
                            <th>Step Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM onboarding_steps ORDER BY step_order ASC";
                        $result = $con->query($sql);
                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr data-id="<?= $row['step_id'] ?>">
                            <td><?= $row['step_name'] ?></td>
                            <td><?= $row['description'] ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" 
                                    data-id="<?= $row['step_id'] ?>" 
                                    data-name="<?= $row['step_name'] ?>" 
                                    data-description="<?= $row['description'] ?>" 
                                    data-bs-toggle="modal" data-bs-target="#editModal">✏️</button>
                                <a href="?delete=<?= $row['step_id'] ?>" class="btn btn-danger btn-sm">🗑️</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Add Modal -->
                <div class="modal fade" id="addModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Step</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Step Name</label>
                                        <input type="text" name="step_name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="add" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Step</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="step_id" id="edit_id">
                                    <div class="mb-3">
                                        <label class="form-label">Step Name</label>
                                        <input type="text" name="step_name" id="edit_name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" id="edit_description" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>

<!-- JAVASCRIPT -->
<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
<!--<script src="https://code.jquery.com/jquery-3.7.1.js"></script>-->
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Disable DataTable sorting to avoid conflict with Sortable
    $('#stepsTable').DataTable({
        "ordering": false,
        "paging": true,
        "searching": true,
        "info": false
    });

    // Initialize Sortable
    $('.sortable tbody').sortable({
        items: 'tr',
        cursor: 'move',
        opacity: 0.6,
        revert: true,
        update: function(event, ui) {
            var orderData = [];
            $('.sortable tbody tr').each(function() {
                orderData.push($(this).data('id'));
            });

            // Send updated order to server
            $.ajax({
                url: 'update_order.php',
                method: 'POST',
                data: { order: orderData },
                success: function(response) {
                    console.log('Order updated successfully:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating order:', error);
                }
            });
        }
    }).disableSelection();

    // Edit button click handler
    $('.edit-btn').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_description').val($(this).data('description'));
    });
});
</script>
</body>
</html>