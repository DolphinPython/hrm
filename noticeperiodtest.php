<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php'; ?>
<?php include 'layouts/config.php'; ?>
<?php
$conn = $con;

// Function to sync new step for all employees with active resignations and incomplete steps
function syncNewStep($conn, $new_step_id) {
    // Fetch employees with active resignations and incomplete steps
    $emp_query = "
        SELECT DISTINCT e.id
        FROM hrm_employee e
        JOIN employee_resignations r ON e.id = r.employee_id
        JOIN employee_notice_period_steps enps ON e.id = enps.employee_id
        WHERE r.status != 'Declined'
        AND enps.status = 0
    ";
    $emp_result = $conn->query($emp_query);
    $employees = $emp_result->fetch_all(MYSQLI_ASSOC);

    // Insert the new step for each eligible employee
    $insert_sql = "INSERT INTO employee_notice_period_steps (employee_id, step_id, status, comment, created_at) 
                   VALUES (?, ?, 0, '', NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    
    foreach ($employees as $emp) {
        $employee_id = $emp['id'];
        $insert_stmt->bind_param("ii", $employee_id, $new_step_id);
        $insert_stmt->execute();
    }
}

// Handle form submission for adding a new step
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_step'])) {
    $step_name = $_POST['step_name'] ?? '';
    $description = $_POST['description'] ?? '';
    
    // Get the highest step_order to append the new step at the end
    $max_order_sql = "SELECT MAX(step_order) AS max_order FROM notice_period_steps";
    $max_order_result = $conn->query($max_order_sql);
    $max_order = $max_order_result->fetch_assoc()['max_order'] ?? -1;
    $step_order = $max_order + 1;

    $sql = "INSERT INTO notice_period_steps (step_name, description, step_order, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $step_name, $description, $step_order);
    $stmt->execute();
    
    // Get the ID of the newly inserted step
    $new_step_id = $conn->insert_id;
    
    // Sync the new step for all eligible employees
    syncNewStep($conn, $new_step_id);
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submission for editing a step
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_step'])) {
    $step_id = $_POST['step_id'] ?? 0;
    $step_name = $_POST['step_name'] ?? '';
    $description = $_POST['description'] ?? '';

    $sql = "UPDATE notice_period_steps SET step_name = ?, description = ? WHERE step_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $step_name, $description, $step_id);
    $stmt->execute();
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle deletion of a step
// Handle deletion of a step
if (isset($_GET['delete_step'])) {
    $step_id = $_GET['delete_step'];
    
    // Validate step_id
    if (!is_numeric($step_id) || $step_id <= 0) {
        die("Invalid step ID.");
    }

    // Check if step exists
    $check_sql = "SELECT COUNT(*) AS count FROM notice_period_steps WHERE step_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $step_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    if ($count == 0) {
        die("Error: Step does not exist.");
    }

    // Begin a transaction to ensure data consistency
    $conn->begin_transaction();
    
    try {
        // Step 1: Delete associated employee steps first
        $delete_employee_steps_sql = "DELETE FROM employee_notice_period_steps WHERE step_id = ?";
        $delete_employee_steps_stmt = $conn->prepare($delete_employee_steps_sql);
        $delete_employee_steps_stmt->bind_param("i", $step_id);
        $delete_employee_steps_stmt->execute();
        
        // Step 2: Delete the step from notice_period_steps
        $sql = "DELETE FROM notice_period_steps WHERE step_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $step_id);
        $stmt->execute();
        
        // Step 3: Reorder remaining steps
        // Initialize the order variable
        $init_sql = "SET @order = -1";
        if (!$conn->query($init_sql)) {
            throw new Exception("Failed to initialize order variable: " . $conn->error);
        }
        
        // Update step_order
        $reorder_sql = "UPDATE notice_period_steps SET step_order = (@order := @order + 1) ORDER BY step_order ASC";
        if (!$conn->query($reorder_sql)) {
            throw new Exception("Failed to reorder steps: " . $conn->error);
        }
        
        // Commit the transaction
        $conn->commit();
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        // Roll back the transaction on error
        $conn->rollback();
        // Log error to file (optional)
        error_log("Error deleting step: " . $e->getMessage(), 3, "error.log");
        die("Error deleting step: " . $e->getMessage());
    }
}

// Fetch all notice period steps
$sql = "SELECT * FROM notice_period_steps ORDER BY step_order ASC";
$result = $conn->query($sql);
$steps = $result->fetch_all(MYSQLI_ASSOC);
?>
<?php
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
$maxDate = date('Y-m-d', strtotime('-18 years'));
$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notice Period Steps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-wrapper {
            min-height: 100vh;
        }
        .page-wrapper {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-title {
            color: #1a3c34;
            font-weight: 600;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-warning, .btn-danger {
            border-radius: 5px;
            padding: 5px 10px;
        }
        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }
        .dataTables_wrapper {
            padding: 10px;
        }
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        .dataTables_wrapper .dataTables_length select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        .table {
            font-size: 14px;
 >width: 100% !important;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 12px;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }
        .sortable tbody tr {
            cursor: move;
        }
        .sortable .ui-sortable-helper {
            background: #e9ecef;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .dataTables_scrollBody {
            border-bottom: 1px solid #dee2e6 !important;
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            .card-body {
                padding: 15px;
            }
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
            .dataTables_wrapper .dataTables_filter input,
            .dataTables_wrapper .dataTables_length select {
                width: 100%;
                margin-bottom: 10px;
            }
        }
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .modal-header {
            background-color: #007bff;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .modal-title {
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="container padding-top-ams">
            <div class="container mt-5">
                <h1 class="mb-4 text-primary">Manage Notice Period Steps</h1>
                <ul class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Manage Notice Period Steps</li>
                </ul>

                <!-- Add New Step Form -->
                <a href="noticeperiod.php" class="btn btn-primary mb-3"><i class="fas fa-arrow-left"></i> Return to Notice Period Management System</a>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Add New Step</h5>
                        <form action="" method="POST">
                            <input type="hidden" name="add_step" value="1">
                            <div class="mb-3">
                                <label for="step_name" class="form-label">Step Name</label>
                                <input type="text" class="form-control" id="step_name" name="step_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Step</button>
                        </form>
                    </div>
                </div>

                <!-- Steps List (DataTable) -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Notice Period Steps</h5>
                        <table id="stepsTable" class="table table-bordered table-striped sortable">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Step Order</th>
                                    <th style="width: 20%;">Step Name</th>
                                    <th style="width: 40%;">Description</th>
                                    <th style="width: 15%;">Created At</th>
                                    <th style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($steps as $step): ?>
                                    <tr data-id="<?= $step['step_id']; ?>">
                                        <td><?= $step['step_order']; ?></td>
                                        <td><?= htmlspecialchars($step['step_name']); ?></td>
                                        <td><?= htmlspecialchars($step['description']); ?></td>
                                        <td><?= date("d M Y, H:i", strtotime($step['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-step" 
                                                    data-id="<?= $step['step_id']; ?>" 
                                                    data-name="<?= htmlspecialchars($step['step_name']); ?>" 
                                                    data-description="<?= htmlspecialchars($step['description']); ?>" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"><i class="fas fa-edit"></i> Edit</button>
                                            <a href="?delete_step=<?= $step['step_id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this step?');"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Edit Step Modal -->
                <div class="modal fade" id="editModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Step</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="POST">
                                    <input type="hidden" name="edit_step" value="1">
                                    <input type="hidden" name="step_id" id="edit_step_id">
                                    <div class="mb-3">
                                        <label for="edit_step_name" class="form-label">Step Name</label>
                                        <input type="text" class="form-control" id="edit_step_name" name="step_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#stepsTable').DataTable({
            scrollX: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: 4 },
                { width: "15%", targets: 4 }
            ],
            responsive: true,
            autoWidth: false
        });

        // Initialize sortable for drag-and-drop
        $("#stepsTable tbody").sortable({
            placeholder: "ui-sortable-placeholder",
            helper: function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            },
            update: function(event, ui) {
                let order = [];
                $("#stepsTable tbody tr").each(function() {
                    order.push($(this).data("id"));
                });

                // Send updated order to server
                $.ajax({
                    url: "update_notice_period_order.php",
                    method: "POST",
                    data: { order: order },
                    success: function(response) {
                        console.log("Order updated successfully");
                        $("#stepsTable tbody tr").each(function(index) {
                            $(this).find("td:first").text(index);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error updating order: " + error);
                    }
                });
            }
        }).disableSelection();

        // Populate edit modal
        $(document).on('click', '.edit-step', function() {
            let stepId = $(this).data("id");
            let stepName = $(this).data("name");
            let description = $(this).data("description");

            $("#edit_step_id").val(stepId);
            $("#edit_step_name").val(stepName);
            $("#edit_description").val(description);
        });
    });
</script>
</body>
</html>