<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php'; ?>

<?php
$conn = connect();
$emp_id = $_SESSION['id']; // Current logged-in user
$employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name FROM hrm_employee ";
$employee_result = mysqli_query($conn, $employee_query) or die(mysqli_error($conn));

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
// Insert Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    // Insert category into database
    $query = "INSERT INTO hrm_ticket_categories (name) VALUES ('$category_name')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Category added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Update Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];

    // Update category in database
    $query = "UPDATE hrm_ticket_categories SET name='$category_name' WHERE id='$category_id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Category updated successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Delete Category
if (isset($_GET['delete_id'])) {
    $category_id = $_GET['delete_id'];

    // Delete category from database
    $query = "DELETE FROM hrm_ticket_categories WHERE id='$category_id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Category deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

?>

<head>
    <title>Add Ticket Category</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <style>
        .form-inline {
            display: flex;
            align-items: center;
        }
        .form-inline .form-control {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Category</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Category</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Add/Edit Category Form -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <?php echo isset($_GET['edit_id']) ? 'Edit Category' : 'Add New Category'; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $category_name = '';
                                if (isset($_GET['edit_id'])) {
                                    $edit_id = $_GET['edit_id'];
                                    $edit_query = "SELECT * FROM hrm_ticket_categories WHERE id='$edit_id'";
                                    $edit_result = mysqli_query($conn, $edit_query);
                                    $edit_row = mysqli_fetch_assoc($edit_result);
                                    $category_name = $edit_row['name'];
                                }
                                ?>
                                <form method="POST" action="">
                                    <?php if (isset($_GET['edit_id'])) { ?>
                                        <input type="hidden" name="category_id" value="<?php echo $edit_id; ?>">
                                    <?php } ?>
                                    <div class="form-group form-inline">
                                        <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo $category_name; ?>" required>
                                        <button type="submit" name="<?php echo isset($_GET['edit_id']) ? 'update_category' : 'add_category'; ?>" class="btn btn-primary">
                                            <?php echo isset($_GET['edit_id']) ? 'Update Category' : 'Add Category'; ?>
                                        </button>
                                        <?php if (isset($_GET['edit_id'])) { ?>
                                            <a href="add-ticket-category.php" class="btn btn-secondary">Cancel</a>
                                        <?php } ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Display Categories Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Category List</h5>
                            </div>
                            <div class="card-body">
                                <table id="categoryTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Category Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch all categories
                                        $query_fetch = "SELECT * FROM hrm_ticket_categories";
                                        $result = mysqli_query($conn, $query_fetch);

                                        if (!$result) {
                                            die("Query failed: " . mysqli_error($conn));
                                        }
                                        if (mysqli_num_rows($result) > 0) {
                                            $sno = 1;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>
                                                        <td>{$sno}</td>
                                                        <td>{$row['name']}</td>
                                                        <td>
                                                            <a href='?edit_id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                                            <a href='?delete_id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this category?\")'>Delete</a>
                                                        </td>
                                                      </tr>";
                                                $sno++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='3'>No categories found!</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#categoryTable').DataTable();
        });
    </script>
</body>

</html>