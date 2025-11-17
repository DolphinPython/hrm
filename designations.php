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

// Handle Add Designation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_designation'])) {
    $designation_name = $_POST['designation_name'];
    $department_name = $_POST['department_name'];

    $query = "INSERT INTO hrm_designation (name, department_name) VALUES ('$designation_name', '$department_name')";
    if (mysqli_query($conn, $query)) {
        header("Location: designations.php"); // Redirect to refresh the page
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle Edit Designation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_designation'])) {
    $id = $_POST['id'];
    $designation_name = $_POST['designation_name'];
    $department_name = $_POST['department_name'];

    $query = "UPDATE hrm_designation SET name='$designation_name', department_name='$department_name' WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        header("Location: designations.php"); // Redirect to refresh the page
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle Delete Designation
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM hrm_designation WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        header("Location: designations.php"); // Redirect to refresh the page
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch all designations
$query = "SELECT * FROM hrm_designation";
$result = mysqli_query($conn, $query);
$designations = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $designations[] = $row;
    }
} else {
    echo "Error fetching designations: " . mysqli_error($conn);
}

// Fetch all departments for the dropdown
$query = "SELECT name FROM hrm_department";
$result = mysqli_query($conn, $query);
$departments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row['name'];
    }
} else {
    echo "Error fetching departments: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Designations - HRMS Admin Template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>
<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            <div class="content container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Designations</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Designations</li>
                            </ul>
                        </div>
                        <div class="col-auto float-end ms-auto">
                            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_designation">
                                <i class="fa-solid fa-plus"></i> Add Designation
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Designation Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Designation</th>
                                        <th>Department</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($designations)): ?>
                                        <?php foreach ($designations as $index => $designation): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><?php echo $designation['name']; ?></td>
                                                <td><?php echo $designation['department_name']; ?></td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="material-icons">more_vert</i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_designation_<?php echo $designation['id']; ?>">
                                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                            </a>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_designation_<?php echo $designation['id']; ?>">
                                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Designation Modal -->
                                            <div id="edit_designation_<?php echo $designation['id']; ?>" class="modal custom-modal fade" role="dialog">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Designation</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" action="">
                                                                <input type="hidden" name="id" value="<?php echo $designation['id']; ?>">
                                                                <div class="input-block mb-3">
                                                                    <label class="col-form-label">Designation Name <span class="text-danger">*</span></label>
                                                                    <input class="form-control" name="designation_name" value="<?php echo $designation['name']; ?>" type="text" required>
                                                                </div>
                                                                <div class="input-block mb-3">
                                                                    <label class="col-form-label">Department <span class="text-danger">*</span></label>
                                                                    <select class="select" name="department_name" required>
                                                                        <option>Select Department</option>
                                                                        <?php foreach ($departments as $department): ?>
                                                                            <option value="<?php echo $department; ?>" <?php echo ($department == $designation['department_name']) ? 'selected' : ''; ?>>
                                                                                <?php echo $department; ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="submit-section">
                                                                    <button type="submit" name="edit_designation" class="btn btn-primary submit-btn">Save</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Edit Designation Modal -->

                                            <!-- Delete Designation Modal -->
                                            <div id="delete_designation_<?php echo $designation['id']; ?>" class="modal custom-modal fade" role="dialog">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-body">
                                                            <div class="form-header">
                                                                <h3>Delete Designation</h3>
                                                                <p>Are you sure you want to delete?</p>
                                                            </div>
                                                            <div class="modal-btn delete-action">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <a href="?delete_id=<?php echo $designation['id']; ?>" class="btn btn-primary continue-btn">Delete</a>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Delete Designation Modal -->
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No designations found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /Designation Table -->
            </div>
            <!-- /Page Content -->
        </div>
        <!-- /Page Wrapper -->

        <!-- Add Designation Modal -->
        <div id="add_designation" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Designation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Designation Name <span class="text-danger">*</span></label>
                                <input class="form-control" name="designation_name" type="text" required>
                            </div>
                            <div class="input-block mb-3">
                                <label class="col-form-label">Department <span class="text-danger">*</span></label>
                                <select class="select" name="department_name" required>
                                    <option>Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo $department; ?>"><?php echo $department; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="submit-section">
                                <button type="submit" name="add_designation" class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Designation Modal -->
    </div>
    <!-- end main wrapper-->

    <?php include 'layouts/customizer.php'; ?>
    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>
</body>
</html>