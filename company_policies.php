<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';

// Get user details
$emp_id = $_SESSION['id'];
$conn = connect();

// Fetch user details
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
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

if ($row['role'] != 'admin' && $row['role'] != 'super admin') {
    header("Location: attendance-report-employee.php");
}

// Handle search functionality
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Create the query based on the search term
$query = "SELECT * FROM company_policies WHERE policy_name LIKE ? OR policy_type LIKE ? OR updated_on LIKE ? OR file_path LIKE ?";
$stmt = $conn->prepare($query);
$searchWildcard = "%" . $searchTerm . "%";
$stmt->bind_param('ssss', $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard);

// Execute the statement and get results
$stmt->execute();
$result = $stmt->get_result();
$policies = $result->fetch_all(MYSQLI_ASSOC);

// Handle adding a policy
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_policy'])) {
    $policy_name = $_POST['policy_name'];
    $policy_type = $_POST['policy_type'];
    $updated_on = $_POST['updated_on'];

    // Handle file upload
    $file_path = null;
    if (!empty($_FILES['policy_file']['name'])) {
        $upload_dir = 'uploads/';
        $file_path = $upload_dir . basename($_FILES['policy_file']['name']);
        if (!move_uploaded_file($_FILES['policy_file']['tmp_name'], $file_path)) {
            die("Error uploading file.");
        }
    }

    $query = "INSERT INTO company_policies (policy_name, policy_type, updated_on, file_path) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssss', $policy_name, $policy_type, $updated_on, $file_path);

    if ($stmt->execute()) {
        header("Location: company_policies.php");
        exit;
    } else {
        die("Error adding policy: " . $conn->error);
    }
}

// Handle policy deletion
if (isset($_GET['delete'])) {
    $policy_id = $_GET['delete'];

    $query = "DELETE FROM company_policies WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $policy_id);

    if ($stmt->execute()) {
        header("Location: company_policies.php");
        exit;
    } else {
        die("Error deleting policy: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>HR Policies Dashboard</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .btn:hover { transform: scale(1.05); }
        table th, table td { text-align: center; }
        .table thead { background-color: #f1f1f1; }
        .table td { white-space: normal !important; }
        .btn-primary-edit { padding: 4px 16px; margin: 5px; }
        .margin-top-policies { margin-top: 5rem !important; }
    </style>
</head>
<body>
    <div class="page-wrapper">
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="container my-5 margin-top-policies">
            <h2 class="text-left mb-4">Company Policies</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Company Policies</li>
            </ul>

            <!-- Search Form -->
            <form method="GET" action="" class="mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="search" placeholder="Search by policy name, type, updated date or file path" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>

            <!-- Add Policy Form -->
            <div class="mb-10">
                <h4>Add New Policy</h4>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="policy_name" class="form-label">Policy Name</label>
                                <input type="text" class="form-control" name="policy_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="policy_type" class="form-label">Policy Type</label>
                                <input type="text" class="form-control" name="policy_type" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updated_on" class="form-label">Updated On</label>
                                <input type="date" class="form-control" name="updated_on" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="policy_file" class="form-label">Upload File</label>
                                <input type="file" class="form-control" name="policy_file">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary" name="add_policy">Add Policy</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Display Policies -->
            <h4 class="text-center mb-4">Company Policies</h4>
            <div class="row">
                <?php foreach ($policies as $policy): ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <div class="policy-icon bg-light rounded-circle text-primary d-flex justify-content-center align-items-center" style="width: 50px; height: 50px; font-size: 20px;">
                                    <span><?php echo strtoupper(substr($policy['policy_type'], 0, 2)); ?></span>
                                </div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($policy['policy_type']); ?></h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-3">
                                    <li><strong>Policy Name:</strong> <?php echo htmlspecialchars($policy['policy_name']); ?></li>
                                    <li><strong>Updated On:</strong> <?php echo date('d M Y', strtotime($policy['updated_on'])); ?></li>
                                </ul>
                                <div class="d-flex justify-content-between">
                                    <a href="<?php echo htmlspecialchars($policy['file_path']); ?>" download class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    <a href="<?php echo htmlspecialchars($policy['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <a href="company_policies.php?delete=<?php echo $policy['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this policy?');">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php include 'layouts/customizer.php'; ?>
        <?php include 'layouts/vendor-scripts.php'; ?>
    </div>
    </div>
</body>
</html>
