<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';

// Common Code
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

// Fetch complaints for the logged-in employee
$query = "SELECT shc.*, CONCAT(he.fname, ' ', he.lname) AS complainant_name 
          FROM sexual_harassment_complaints shc
          JOIN hrm_employee he ON shc.emp_id = he.id
          ";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

$complaints = [];
while ($row = mysqli_fetch_assoc($result)) {
    $complaints[] = $row;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Employee Complaints</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <!-- Custom CSS for Scrollable Table Cells -->
    <style>
        /* Add scrollbar to specific table cells */
      

        /* Ensure horizontal scrolling for the table */
        .dataTables_wrapper {
            overflow-y: auto;
        }
    </style>
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
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Your Complaints</h3>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Complaints Table -->
                <div class="card">
                    <div class="card-body">
                        <table id="complaintsTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Complainant Name</th>
                                    <th>Incident Date</th>
                                    <th>Incident Location</th>
                                    <th>Harasser Details</th>
                                    <th>Description</th>
                                    <th>Witness Details</th>
                                    <th>Evidence</th>
                                    <th>Submission Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($complaints as $complaint) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($complaint['id']); ?></td>
                                        <td><?php echo htmlspecialchars($complaint['complainant_name']); ?></td>
                                        <td><?php echo htmlspecialchars($complaint['incident_date']); ?></td>
                                        <td><?php echo htmlspecialchars($complaint['incident_location']); ?></td>
                                        <td><?php echo htmlspecialchars($complaint['harasser_details']); ?></td>
                                        <td class="scrollable-cell"><?php echo htmlspecialchars($complaint['incident_description']); ?></td>
                                        <td class="scrollable-cell"><?php echo htmlspecialchars($complaint['witness_details']); ?></td>
                                        <td>
                                            <?php if (!empty($complaint['evidence_path'])) : ?>
                                                <a href="<?php echo htmlspecialchars($complaint['evidence_path']); ?>" target="_blank" style="text-decoration:underline;color:blue;">View Evidence</a>
                                            <?php else : ?>
                                                No Evidence
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($complaint['submission_date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /Complaints Table -->
            </div>
            <!-- /Page Content -->
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- end main wrapper-->

    <?php include 'layouts/customizer.php'; ?>
    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Responsive JS -->
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Initialize DataTable with Responsive -->
    <script>
        $(document).ready(function() {
            $('#complaintsTable').DataTable({
                responsive: true, // Enable responsive feature
                order: [[0, 'desc']], // Sort by ID in descending order
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // ID column
                    { responsivePriority: 2, targets: 1 }, // Complainant Name column
                    { responsivePriority: 3, targets: 2 }, // Incident Date column
                    { responsivePriority: 4, targets: -1 } // Submission Date column
                ]
            });
        });
    </script>
</body>

</html>