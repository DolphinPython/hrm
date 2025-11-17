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

if ($row['role'] != 'admin' and $row['role'] != 'super admin') {
    header("Location:attendance-report-employee.php");
}

?>
<head>

    <title> Reports - HRMS admin template</title>

    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>
    <style>
    .alert {
        font-weight: bold;
    }
    .card-body {
        background-color: #f9f9f9;
        border-radius: 8px;
    }
    .btn {
        transition: transform 0.2s;
    }
    .btn:hover {
        transform: scale(1.05);
    }
    table th, table td {
        text-align: center;
    }
    .table thead {
        background-color: #f1f1f1;
    }
    /* .table img {
        max-width: 50px;
        border-radius: 5px;
    } */
     .eom{
        margin-top:5rem !important;
     }
     .table td {
    white-space:normal !important;
}
.btn-primary-edit{
    padding: 4px 16px;
    margin: 5px;
}
</style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>
        <?php
        // feedback message
        $message = "";

        // Handle Add/Edit/Delete
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_employee'])) {
                // Add Employee
                $name = $conn->real_escape_string($_POST['name']);
                $designation = $conn->real_escape_string($_POST['designation']);
                $messageContent = $conn->real_escape_string($_POST['message']);
                $imageUrl = 'assets/upload-image/default.jpg'; // Default image
        
                if (!empty($_FILES['image']['name'])) {
                    $image = $_FILES['image']['name'];
                    $targetDir = "assets/upload-image/";
                    $target = $targetDir . basename($image);

                    // Ensure directory exists
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                        $imageUrl = $target;
                    }
                }

                $sql = $conn->prepare("INSERT INTO hrm_employee_of_the_month (name, designation, message, image_url) VALUES (?, ?, ?, ?)");
                $sql->bind_param("ssss", $name, $designation, $messageContent, $imageUrl);

                if ($sql->execute()) {
                    $message = "Employee added successfully!";
                } else {
                    $message = "Failed to add employee!";
                }
            } elseif (isset($_POST['edit_employee'])) {
                // Edit Employee
                $id = $_POST['id'];
                $name = $conn->real_escape_string($_POST['name']);
                $designation = $conn->real_escape_string($_POST['designation']);
                $messageContent = $conn->real_escape_string($_POST['message']);
                $imageUrl = $_POST['current_image'];

                if (!empty($_FILES['image']['name'])) {
                    $image = $_FILES['image']['name'];
                    $targetDir = "assets/upload-image/";
                    $target = $targetDir . basename($image);

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                        $imageUrl = $target;
                    }
                }

                $sql = $conn->prepare("UPDATE hrm_employee_of_the_month SET name=?, designation=?, message=?, image_url=? WHERE id=?");
                $sql->bind_param("ssssi", $name, $designation, $messageContent, $imageUrl, $id);

                if ($sql->execute()) {
                    $message = "Employee updated successfully!";
                } else {
                    $message = "Failed to update employee!";
                }
            } elseif (isset($_POST['delete_employee'])) {
                // Delete Employee
                $id = $_POST['id'];
                $sql = $conn->prepare("DELETE FROM hrm_employee_of_the_month WHERE id=?");
                $sql->bind_param("i", $id);

                if ($sql->execute()) {
                    $message = "Employee deleted successfully!";
                } else {
                    $message = "Failed to delete employee!";
                }
            }
        }

        // Fetch Employees
        $employees = [];
        $result = $conn->query("SELECT * FROM hrm_employee_of_the_month ORDER BY created_at DESC");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['message'] = stripslashes($row['message']); // Remove backslashes here
                $employees[] = $row;
            }
        }
        ?>
        <!-- Page Wrapper -->
        <div class="page-wrapper">
        <div class="container mt-5 eom">
            <h2 class="text-left mb-4">Employee of the Month</h2>
            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Employee of the Month</li>
                            </ul>
            <!-- Display Feedback -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?= $message; ?></div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3"><?= isset($_GET['edit']) ? 'Edit Employee' : 'Add Employee'; ?></h4>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="employeeId">
                        <input type="hidden" name="current_image" id="currentImage">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" name="designation" id="designation" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image(size:120x146)</label>
                            <input type="file" name="image" id="image" class="form-control">
                        </div>
                        <button type="submit" name="add_employee" id="addBtn" class="btn btn-success">Add
                            Employee</button>
                        <button type="submit" name="edit_employee" id="editBtn" class="btn btn-primary d-none">Update
                            Employee</button>
                    </form>
                </div>
            </div>

            <!-- Employee Table -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Employees</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Message</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td><?= $employee['id'] ?></td>
                                    <td><?= $employee['name'] ?></td>
                                    <td><?= $employee['designation'] ?></td>
                                    <td><?= $employee['message'] ?></td>
                                    <td><img src="<?= $employee['image_url'] ?>" alt="Image"></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm edit-btn btn-primary-edit" data-id="<?= $employee['id'] ?>"
                                            data-name="<?= $employee['name'] ?>"
                                            data-designation="<?= $employee['designation'] ?>"
                                            data-message="<?= $employee['message'] ?>"
                                            data-image="<?= $employee['image_url'] ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $employee['id'] ?>">
                                            <button type="submit" name="delete_employee" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
        <!-- /Page Wrapper -->

    </div>
    <!-- end main wrapper-->

    <script>
        // Handle Edit Button Click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const designation = this.dataset.designation;
                const message = this.dataset.message;
                const image = this.dataset.image;

                document.getElementById('employeeId').value = id;
                document.getElementById('name').value = name;
                document.getElementById('designation').value = designation;
                document.getElementById('message').value = message;
                document.getElementById('currentImage').value = image;

                document.getElementById('addBtn').classList.add('d-none');
                document.getElementById('editBtn').classList.remove('d-none');
            });
        });
    </script>

    <?php include 'layouts/customizer.php'; ?>
    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>



</body>

</html>