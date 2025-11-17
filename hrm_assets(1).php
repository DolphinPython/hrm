<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php'; ?>
<?php 
// get user name and other details
$emp_id = $_SESSION['id'];
$conn = connect();
$query = "select * from hrm_employee where id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

if ($row['department_id'] != 4 && $row['department_id'] != 6) {
    header("Location:attendance-report-employee.php");
}

// Handle Add Asset
if (isset($_POST['add'])) {
    $asset_name = $_POST['asset_name'];
    $asset_id = $_POST['asset_id'];
    $quantity = $_POST['quantity'];

    // Handle file upload
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $target_dir = "upload-image/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $target_file = "";
    }

    $query = "INSERT INTO hrm_assets (asset_name, asset_id, quantity, image) VALUES ('$asset_name', '$asset_id', $quantity, '$target_file')";
    if ($conn->query($query)) {
        $success_message = "Asset added successfully.";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}

// Handle Edit Asset
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $asset_name = $_POST['asset_name'];
    $asset_id = $_POST['asset_id'];
    $quantity = $_POST['quantity'];

    // Handle file upload
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $target_dir = "upload-image/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_update = ", image = '$target_file'";
    } else {
        $image_update = "";
    }

    $query = "UPDATE hrm_assets SET asset_name = '$asset_name', asset_id = '$asset_id', quantity = $quantity $image_update WHERE id = $id";
    if ($conn->query($query)) {
        $success_message = "Asset updated successfully.";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}

// Handle Delete Asset
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM hrm_assets WHERE id = $id";
    if ($conn->query($query)) {
        $success_message = "Asset deleted successfully.";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}
?>

<head>
    <title> Reports - HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .padding-top-ams {
            margin-top: 5rem;
        }
    </style>
</head>

<body>
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="container padding-top-ams">
            <h1 class="mb-4">Manage HRM Assets</h1>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Manage HRM Assets</li>
            </ul>
            <a href="hrm_asset_assignments.php" class="m-4"><button class="btn btn-success">Assign Asset</button></a>

            <!-- Success/Error Message -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"> <?= $success_message ?> </div>
            <?php elseif (isset($error_message)): ?>
                <div class="alert alert-danger"> <?= $error_message ?> </div>
            <?php endif; ?>

            <!-- Add New Asset Form -->
            <form method="POST" enctype="multipart/form-data">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>Add New Asset</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" placeholder="Enter Quantity" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="asset_name" class="form-label">Asset Name</label>
                            <input type="text" name="asset_name" placeholder="Enter Asset Name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="asset_id" class="form-label">Asset ID</label>
                            <input type="text" name="asset_id" placeholder="Enter Asset ID" class="form-control">
                        </div>
                        <button type="submit" name="add" class="btn btn-success">Add Asset</button>
                    </div>
                </div>
            </form>

            <!-- Search Form -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="asset_name" class="form-control" placeholder="Search by Asset Name" value="<?= isset($_GET['asset_name']) ? $_GET['asset_name'] : '' ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="asset_id" class="form-control" placeholder="Search by Asset ID" value="<?= isset($_GET['asset_id']) ? $_GET['asset_id'] : '' ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="quantity" class="form-control" placeholder="Search by Quantity" value="<?= isset($_GET['quantity']) ? $_GET['quantity'] : '' ?>">
                    </div>
                    <div class="col-md-12 mt-2">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>

            <!-- Asset Table -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Quantity</th>
                            <th>Asset Name</th>
                            <th>Asset ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Database connection and filtering logic
                        $asset_name = isset($_GET['asset_name']) ? $_GET['asset_name'] : '';
                        $asset_id = isset($_GET['asset_id']) ? $_GET['asset_id'] : '';
                        $quantity = isset($_GET['quantity']) ? $_GET['quantity'] : '';

                        // SQL query with filters
                        $sql = "SELECT * FROM hrm_assets WHERE 1=1";
                        if ($asset_name) {
                            $sql .= " AND asset_name LIKE '%" . $conn->real_escape_string($asset_name) . "%'";
                        }
                        if ($asset_id) {
                            $sql .= " AND asset_id LIKE '%" . $conn->real_escape_string($asset_id) . "%'";
                        }
                        if ($quantity) {
                            $sql .= " AND quantity = " . (int) $quantity;
                        }

                        // Fetch assets
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><img src="<?= $row['image'] ?>" alt="Asset Image" style="width: 50px; height: 50px;"></td>
                                <td><?= $row['quantity'] ?></td>
                                <td><?= $row['asset_name'] ?></td>
                                <td><?= $row['asset_id'] ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Asset</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="image" class="form-label">Image</label>
                                                    <input type="file" name="image" class="form-control">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="asset_name" class="form-label">Asset Name</label>
                                                    <input type="text" name="asset_name" class="form-control" value="<?= $row['asset_name'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="asset_id" class="form-label">Asset ID</label>
                                                    <input type="text" name="asset_id" class="form-control" value="<?= $row['asset_id'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="quantity" class="form-label">Quantity</label>
                                                    <input type="number" name="quantity" class="form-control" value="<?= $row['quantity'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" name="edit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include 'layouts/footer.php'; ?>
</body>
</html>
