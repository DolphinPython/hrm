<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';
include 'email/mailer.php'; // Include mailer.php for email functionality

// Get user name and other details
$emp_id = $_SESSION['id'];
$emp_session_id = $_SESSION['id'];
$conn = connect();

// Define admin/HR IDs who can see all employees
// $admin_ids = [10, 14]; 
// $is_admin = in_array($emp_id, $admin_ids);
$role_query = "SELECT role FROM hrm_employee WHERE id = '$emp_id'";
$role_result = mysqli_query($conn, $role_query) or die(mysqli_error($conn));
$role_row = mysqli_fetch_assoc($role_result);
$is_admin = ($role_row && in_array(strtolower($role_row['role']), ['admin', 'super admin']));

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

// Handle leave status update
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $emp_id = $_GET['emp_id'];
    $leave_id = $_GET['leave_id'];
    $query = "UPDATE hrm_leave_applied SET status='$status' WHERE id='$leave_id';";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

    $emp_name = get_value("hrm_employee", "fname", $emp_id);
    $emp_email = get_value("hrm_employee", "email", $emp_id);
    $admin_email = get_value("hrm_employee", "email", $emp_session_id);

    if ($status == 1) {
        $update_status = "Pending";
    } else if ($status == 0) {
        $update_status = "New";
    } else if ($status == 2) {
        $update_status = "Approved";
    } else if ($status == 3) {
        $update_status = "Declined";
    }

    $to = 'hr@1solutions.biz';
    $subject = 'HR-Leave Status Updated';
    $message = '<p><strong>Hi ' . $emp_name . '</strong><br><br> Your leave status is ' . $update_status . '</p>';
    $cc_emails = ['pythondolphin@gmail.com', 'pythondolphin@gmail.com',$emp_email]; // CC emails

    // Send email using mailer.php
    send_email($to, $subject, $message, $cc_emails);

    header("Location: leaves.php");
    exit();
}

// Handle leave deletion
if (isset($_GET['delete_leave_id'])) {
    $leave_id = $_GET['delete_leave_id'];
    $query = "DELETE FROM hrm_leave_applied WHERE id='$leave_id'";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
    header("Location: leaves.php");
    exit();
}

// Handle leave edit form submission
if (isset($_POST['b1_edit_leave'])) {
    $leave_id = mysqli_real_escape_string($conn, $_POST['leave_id']);
    $leave_type_id = mysqli_real_escape_string($conn, $_POST['leave_type_id']);
    $day_type = mysqli_real_escape_string($conn, $_POST['day_type']);
    $from_date = mysqli_real_escape_string($conn, $_POST['from_date']);
    $to_date = mysqli_real_escape_string($conn, $_POST['to_date']);
    $from_date = date('Y-m-d H:i:s', strtotime($from_date));
    $to_date = date('Y-m-d H:i:s', strtotime($to_date));
    $no_of_days = mysqli_real_escape_string($conn, $_POST['no_of_days']);
    $leave_reason = mysqli_real_escape_string($conn, $_POST['leave_reason']);

    $query = "UPDATE hrm_leave_applied SET leave_type_id='$leave_type_id', day_type='$day_type', start_date='$from_date', end_date='$to_date', no_of_days='$no_of_days', leave_reason='$leave_reason' WHERE id='$leave_id'";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

    if ($result) {
        echo "<script>alert('Leave Updated Successfully!'); window.location.href='leaves.php';</script>";
    } else {
        echo "<script>alert('Failed to update leave. Please try again.');</script>";
    }
}

// Function to convert day_type to readable format
function get_day_type_name($day_type) {
    switch ($day_type) {
        case 1:
            return "Half Day";
        case 2:
            return "Full Day";
        case 3:
            return "Short Leave";
        default:
            return "Unknown";
    }
}
?>

<head>
    <?php include 'layouts/head-css.php'; ?>
    <title>Leaves - HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .leave-filter-btn {
            margin-right: 10px;
            margin-bottom: 15px;
        }
        .modal-lg {
            max-width: 90%;
        }
        .modal-table {
            width: 100%;
            margin-bottom: 0;
        }
        
.table>:not(caption)>*>* {
    background-color: #fff0 !important;
}
.table thead{
    background-color: black !important;
}
.table thead tr th{
    color:#ffffff !important;
}
.table-striped tbody tr.status-approved-current {
    background-color: #a7dca5 !important; /* Blue for Approved leaves including current date */
}
 /* Note board styles */
.note-board {
    background-color: #f8f9fa;
    border: 1px solid #e3e3e3;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 20px;
    width: 100%;
    max-width: 800px; /* Wide enough for all statuses in one row */
}
.note-board h6 {
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
}
.status-container {
    display: flex;
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
    gap: 15px; /* Space between status items */
    align-items: center;
}
.status-item {
    display: flex;
    align-items: center;
    flex: 0 0 auto; /* Prevent stretching */
}
.color-square {
    width: 16px;
    height: 16px;
    margin-right: 6px;
    border: 1px solid #ccc;
}
.status-new-square {
    background-color: #ffb3b3 ;
}
.status-approved-square {
    background-color: #fff;
}
.status-pending-square {
    background-color: #add8e6;
}
.status-declined-square {
    background-color: #ff0000;
}
.status-approved-current-square {
    background-color: #a7dca5;
}
.status-item span {
    font-size: 13px;
    white-space: nowrap;
}
/* Responsive adjustments */
@media (max-width: 576px) {
    .note-board {
        padding: 8px;
    }
    .status-container {
        gap: 10px;
    }
    .status-item {
        flex: 1 0 45%;
    }
    .color-square {
        width: 14px;
        height: 14px;
    }
    .status-item span {
        font-size: 12px;
    }
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
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Leaves</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Leaves</li>
                            </ul>
                        </div>
                        <div class="col-auto float-end ms-auto" style="display:none;">
                            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_leave"><i class="fa-solid fa-plus"></i> Add Leave</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Leave Statistics -->
                <?php if ($is_admin) { ?>
                <div class="row">
                    <div class="col-md-3 d-flex">
                        <div class="stats-info w-100">
                            <h6>New Leave</h6>
                            <h4><?php echo leave_status_count(0); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex">
                        <div class="stats-info w-100">
                            <h6>Pending Leave</h6>
                            <h4><?php echo leave_status_count(1); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex">
                        <div class="stats-info w-100">
                            <h6>Approved Leave</h6>
                            <h4><?php echo leave_status_count(2); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex">
                        <div class="stats-info w-100">
                            <h6>Declined Leave</h6>
                            <h4><?php echo leave_status_count(3); ?></h4>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <!-- /Leave Statistics -->
<div class="note-board">
    <h6>Status Legend</h6>
    <div class="status-container">
        <div class="status-item">
            <div class="color-square status-new-square"></div>
            <span>New</span>
        </div>
        <div class="status-item">
            <div class="color-square status-approved-square"></div>
            <span>All</span>
        </div>
        <div class="status-item">
            <div class="color-square status-pending-square"></div>
            <span>Pending</span>
        </div>
        <div class="status-item">
            <div class="color-square status-declined-square"></div>
            <span>Declined</span>
        </div>
        <div class="status-item">
            <div class="color-square status-approved-current-square"></div>
            <span>Approved (Today Leaves)</span>
        </div>
    </div>
</div>
                <!-- Leave Filter Buttons -->
                <div class="row mb-3">
                    <div class="col">
                        <button class="btn btn-primary leave-filter-btn" data-bs-toggle="modal" data-bs-target="#todayLeavesModal">Today's Leaves</button>
                        <button class="btn btn-primary leave-filter-btn" data-bs-toggle="modal" data-bs-target="#tomorrowLeavesModal">Tomorrow's Leaves</button>
                        <button class="btn btn-primary leave-filter-btn" data-bs-toggle="modal" data-bs-target="#allLeavesModal">All Leaves</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>Day Type</th>
                                        <th>Applied Time</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>No of Days</th>
                                        <th>Reason</th>
                                        <?php if ($is_admin) { ?>
                                        <th class="text-center">Status</th>
                                        <th class="text-end">Actions</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($is_admin) {
                                        $query = "SELECT hrm_leave_applied.id AS leave_id, hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.created_at,
                                            hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days,
                                            hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id,
                                            hrm_leave_applied.approved_by, hrm_employee.fname, hrm_employee.lname
                                            FROM hrm_leave_applied
                                            JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id
                                            WHERE hrm_employee.status = 1 ORDER BY hrm_leave_applied.created_at DESC;";
                                    } else {
                                        $query = "SELECT hrm_leave_applied.id AS leave_id, hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.created_at,
                                            hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days,
                                            hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id,
                                            hrm_leave_applied.approved_by, hrm_employee.fname, hrm_employee.lname
                                            FROM hrm_leave_applied
                                            JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id
                                            WHERE hrm_employee.status = 1
                                            AND (hrm_leave_applied.emp_id = '$emp_session_id'
                                                OR hrm_leave_applied.emp_id IN (
                                                    SELECT employee_id
                                                    FROM hrm_reporting_manager
                                                    WHERE reporting_manager_id = '$emp_session_id'
                                                )
                                            ) ORDER BY hrm_leave_applied.created_at DESC;";
                                    }
                                    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                                    $serial_number = 1;

$today = date('Y-m-d'); // Get current date for comparison
while ($row = mysqli_fetch_array($result)) {
    $leave_type = get_value("hrm_leave_type", "name", $row['leave_type_id']);
    $day_type_name = get_day_type_name($row['day_type']);
    $start_date = date("Y-m-d", strtotime($row['start_date']));
    $end_date = date("Y-m-d", strtotime($row['end_date']));
    $created_at = $row['created_at'];
    $no_of_days = $row['no_of_days'];
    $leave_reason = $row['leave_reason'];
    $day_type = $row['day_type'];

    if ($row['approved_by'] != 0) {
        $approved_by = get_value("hrm_employee", "fname", $row['approved_by']);
    } else {
        $approved_by = 0;
    }

    // Set status and text color
    $class = ''; // Initialize class
    if ($row['status'] == 1) {
        $status = "Pending";
        $class1 = "text-info";
        $class = "status-pending";
    } else if ($row['status'] == 0) {
        $status = "New";
        $class1 = "text-purple";
        $class = "status-new";
    } else if ($row['status'] == 2) {
        $status = "Approved";
        $class1 = "text-success";
        // Check if today is between start_date and end_date
        if ($today >= $start_date && $today <= $end_date) {
            $class = "status-approved-current"; // Blue for current approved leaves
        } else {
            $class = "status-approved"; // Light green for other approved leaves
        }
    } else if ($row['status'] == 3) {
        $status = "Declined";
        $class1 = "text-danger";
        $class = "status-declined";
    }
?>
<tr class="<?php echo $class; ?>">
    <td><?php echo $serial_number++; ?></td>
    <td>
        <h2 class="table-avatar">
            <a href="profile.php?id=<?php echo $row['emp_id']; ?>">
                <?php echo $row['fname'] . " " . $row['lname']; ?>
            </a>
        </h2>
    </td>
    <td><?php echo $leave_type; ?></td>
    <td><?php echo $day_type_name; ?></td>
    <td><?php echo $created_at; ?></td>
    <td><?php echo $start_date; ?></td>
    <td><?php echo $end_date; ?></td>
    <td><?php echo $no_of_days; ?></td>
    <td>
        <?php
        $words = explode(" ", $leave_reason);
        $formattedText = "";
        foreach ($words as $index => $word) {
            $formattedText .= $word . " ";
            if (($index + 1) % 5 == 0) {
                $formattedText .= "<br>";
            }
        }
        echo $formattedText;
        ?>
    </td>
    <?php if ($is_admin) { ?>
    <td class="text-center">
        <div class="dropdown action-label">
            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-regular fa-circle-dot <?php echo $class1; ?>"></i>
                <?php echo $status; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="leaves.php?emp_id=<?php echo $row['emp_id']; ?>&leave_id=<?php echo $row['leave_id']; ?>&status=0">
                    <i class="fa-regular fa-circle-dot text-purple"></i> New
                </a>
                <a class="dropdown-item" href="leaves.php?emp_id=<?php echo $row['emp_id']; ?>&leave_id=<?php echo $row['leave_id']; ?>&status=1">
                    <i class="fa-regular fa-circle-dot text-info"></i> Pending
                </a>
                <a class="dropdown-item" href="leaves.php?emp_id=<?php echo $row['emp_id']; ?>&leave_id=<?php echo $row['leave_id']; ?>&status=2">
                    <i class="fa-regular fa-circle-dot text-success"></i> Approved
                </a>
                <a class="dropdown-item" href="leaves.php?emp_id=<?php echo $row['emp_id']; ?>&leave_id=<?php echo $row['leave_id']; ?>&status=3">
                    <i class="fa-regular fa-circle-dot text-danger"></i> Declined
                </a>
            </div>
        </div>
    </td>
    <td class="text-end">
        <div class="dropdown dropdown-action">
            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="material-icons">more_vert</i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item edit-leave-btn" href="#" data-bs-toggle="modal" data-bs-target="#edit_leave"
                   data-leave-id="<?php echo $row['leave_id']; ?>"
                   data-leave-type-id="<?php echo $row['leave_type_id']; ?>"
                   data-day-type="<?php echo $row['day_type']; ?>"
                   data-from-date="<?php echo $start_date; ?>"
                   data-to-date="<?php echo $end_date; ?>"
                   data-no-of-days="<?php echo $no_of_days; ?>"
                   data-leave-reason="<?php echo htmlspecialchars($row['leave_reason']); ?>">
                    <i class="fa-solid fa-pencil m-r-5"></i> Edit
                </a>
                <a class="dropdown-item delete-leave-btn" href="#" data-bs-toggle="modal" data-bs-target="#delete_approve" data-leave-id="<?php echo $row['leave_id']; ?>">
                    <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                </a>
            </div>
        </div>
    </td>
    <?php } ?>
</tr>
<?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Content -->

            <!-- Today's Leaves Modal -->
            <div id="todayLeavesModal" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Today's Leaves</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped modal-table">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Employee</th>
                                            <th>Leave Type</th>
                                            <th>Day Type</th>
                                            <th>Applied Time</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>No of Days</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $today = date('Y-m-d');
                                        $query_today = $is_admin ?
                                            "SELECT hrm_leave_applied.id AS leave_id, hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.created_at,
                                                    hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days,
                                                    hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id, hrm_employee.fname, hrm_employee.lname
                                            FROM hrm_leave_applied
                                            JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id
                                            WHERE hrm_employee.status = 1
                                            AND '$today' BETWEEN hrm_leave_applied.start_date AND hrm_leave_applied.end_date
                                            ORDER BY hrm_leave_applied.created_at DESC;" :
                                            "SELECT hrm_leave_applied.id AS leave_id, hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.created_at,
                                                    hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days,
                                                    hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id, hrm_employee.fname, hrm_employee.lname
                                            FROM hrm_leave_applied
                                            JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id
                                            WHERE hrm_employee.status = 1
                                            AND '$today' BETWEEN hrm_leave_applied.start_date AND hrm_leave_applied.end_date
                                            AND (
                                                hrm_leave_applied.emp_id = '$emp_session_id'
                                                OR hrm_leave_applied.emp_id IN (
                                                    SELECT employee_id
                                                    FROM hrm_reporting_manager
                                                    WHERE reporting_manager_id = '$emp_session_id'
                                                )
                                            )
                                            ORDER BY hrm_leave_applied.created_at DESC;";

                                        $result_today = mysqli_query($conn, $query_today) or die(mysqli_error($conn));
                                        $serial_number_today = 1;
                                        while ($row_today = mysqli_fetch_array($result_today)) {
                                            $leave_type = get_value("hrm_leave_type", "name", $row_today['leave_type_id']);
                                            $day_type_name = get_day_type_name($row_today['day_type']);
                                            $status = $row_today['status'] == 0 ? "New" : ($row_today['status'] == 1 ? "Pending" : ($row_today['status'] == 2 ? "Approved" : "Declined"));
                                        ?>
                                        <tr>
                                            <td><?php echo $serial_number_today++; ?></td>
                                            <td><?php echo $row_today['fname'] . " " . $row_today['lname']; ?></td>
                                            <td><?php echo $leave_type; ?></td>
                                            <td><?php echo $day_type_name; ?></td>
                                            <td><?php echo $row_today['created_at']; ?></td>
                                            <td><?php echo date("Y-m-d", strtotime($row_today['start_date'])); ?></td>
                                            <td><?php echo date("Y-m-d", strtotime($row_today['end_date'])); ?></td>
                                            <td><?php echo $row_today['no_of_days']; ?></td>
                                            <td><?php echo $row_today['leave_reason']; ?></td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Today's Leaves Modal -->

            <!-- Tomorrow's Leaves Modal -->
            <div id="tomorrowLeavesModal" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tomorrow's Leaves</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped modal-table">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Employee</th>
                                            <th>Leave Type</th>
                                            <th>Day Type</th>
                                            <th>Applied Time</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>No of Days</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $tomorrow = date('Y-m-d', strtotime('+1 day'));
                                        $query_tomorrow = $is_admin ?
                                            "SELECT hrm_leave_applied.id AS leave_id, hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.created_at,
                                                    hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days,
                                                    hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id, hrm_employee.fname, hrm_employee.lname
                                            FROM hrm_leave_applied
                                            JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id
                                            WHERE hrm_employee.status = 1
                                            AND '$tomorrow' BETWEEN hrm_leave_applied.start_date AND hrm_leave_applied.end_date
                                            ORDER BY hrm_leave_applied.created_at DESC;" :
                                            "SELECT hrm_leave_applied.id AS leave_id, hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.created_at,
                                                    hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days,
                                                    hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id, hrm_employee.fname, hrm_employee.lname
                                            FROM hrm_leave_applied
                                            JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id
                                            WHERE hrm_employee.status = 1
                                            AND '$tomorrow' BETWEEN hrm_leave_applied.start_date AND hrm_leave_applied.end_date
                                            AND (
                                                hrm_leave_applied.emp_id = '$emp_session_id'
                                                OR hrm_leave_applied.emp_id IN (
                                                    SELECT employee_id
                                                    FROM hrm_reporting_manager
                                                    WHERE reporting_manager_id = '$emp_session_id'
                                                )
                                            )
                                            ORDER BY hrm_leave_applied.created_at DESC;";

                                        $result_tomorrow = mysqli_query($conn, $query_tomorrow) or die(mysqli_error($conn));
                                        $serial_number_tomorrow = 1;
                                        while ($row_tomorrow = mysqli_fetch_array($result_tomorrow)) {
                                            $leave_type = get_value("hrm_leave_type", "name", $row_tomorrow['leave_type_id']);
                                            $day_type_name = get_day_type_name($row_tomorrow['day_type']);
                                            $status = $row_tomorrow['status'] == 0 ? "New" : ($row_tomorrow['status'] == 1 ? "Pending" : ($row_tomorrow['status'] == 2 ? "Approved" : "Declined"));
                                        ?>
                                        <tr>
                                            <td><?php echo $serial_number_tomorrow++; ?></td>
                                            <td><?php echo $row_tomorrow['fname'] . " " . $row_tomorrow['lname']; ?></td>
                                            <td><?php echo $leave_type; ?></td>
                                            <td><?php echo $day_type_name; ?></td>
                                            <td><?php echo $row_tomorrow['created_at']; ?></td>
                                            <td><?php echo date("Y-m-d", strtotime($row_tomorrow['start_date'])); ?></td>
                                            <td><?php echo date("Y-m-d", strtotime($row_tomorrow['end_date'])); ?></td>
                                            <td><?php echo $row_tomorrow['no_of_days']; ?></td>
                                            <td><?php echo $row_tomorrow['leave_reason']; ?></td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Tomorrow's Leaves Modal -->

            <!-- All Leaves Modal -->
            <div id="allLeavesModal" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">All Leaves</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped modal-table">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Employee</th>
                                            <th>Leave Type</th>
                                            <th>Day Type</th>
                                            <th>Applied Time</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>No of Days</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query_all = $is_admin ?
                                            "SELECT hrm_leave_applied.id AS leave_id, hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.created_at,
                                            hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days,
                                            hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id, hrm_employee.fname, hrm_employee.lname
                                            FROM hrm_leave_applied
                                            JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id
                                            WHERE hrm_employee.status = 1
                                            ORDER BY hrm_leave_applied.created_at DESC;" :
                                            "SELECT hrm_leave_applied.id AS leave_id, hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.created_at,
                                            hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days,
                                            hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id, hrm_employee.fname, hrm_employee.lname
                                            FROM hrm_leave_applied
                                            JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id
                                            WHERE hrm_employee.status = 1
                                            AND (hrm_leave_applied.emp_id = '$emp_session_id'
                                                OR hrm_leave_applied.emp_id IN (
                                                    SELECT employee_id
                                                    FROM hrm_reporting_manager
                                                    WHERE reporting_manager_id = '$emp_session_id'
                                                )
                                            ) ORDER BY hrm_leave_applied.created_at DESC;";
                                        $result_all = mysqli_query($conn, $query_all) or die(mysqli_error($conn));
                                        $serial_number_all = 1;
                                        while ($row_all = mysqli_fetch_array($result_all)) {
                                            $leave_type = get_value("hrm_leave_type", "name", $row_all['leave_type_id']);
                                            $day_type_name = get_day_type_name($row_all['day_type']);
                                            $status = $row_all['status'] == 0 ? "New" : ($row_all['status'] == 1 ? "Pending" : ($row_all['status'] == 2 ? "Approved" : "Declined"));
                                        ?>
                                        <tr>
                                            <td><?php echo $serial_number_all++; ?></td>
                                            <td><?php echo $row_all['fname'] . " " . $row_all['lname']; ?></td>
                                            <td><?php echo $leave_type; ?></td>
                                            <td><?php echo $day_type_name; ?></td>
                                            <td><?php echo $row_all['created_at']; ?></td>
                                            <td><?php echo date("Y-m-d", strtotime($row_all['start_date'])); ?></td>
                                            <td><?php echo date("Y-m-d", strtotime($row_all['end_date'])); ?></td>
                                            <td><?php echo $row_all['no_of_days']; ?></td>
                                            <td><?php echo $row_all['leave_reason']; ?></td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /All Leaves Modal -->

            <!-- Add Leave Modal -->
            <div id="add_leave" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Leave</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Type <span class="text-danger">*</span></label>
                                    <select class="select">
                                        <option>Select Leave Type</option>
                                        <option>Casual Leave 12 Days</option>
                                        <option>Medical Leave</option>
                                        <option>Loss of Pay</option>
                                    </select>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">From <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text">
                                    </div>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">To <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text">
                                    </div>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Number of days <span class="text-danger">*</span></label>
                                    <input class="form-control" readonly type="text">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Remaining Leaves <span class="text-danger">*</span></label>
                                    <input class="form-control" readonly value="12" type="text">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Reason <span class="text-danger">*</span></label>
                                    <textarea rows="4" class="form-control"></textarea>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Add Leave Modal -->

            <!-- Edit Leave Modal -->
            <div id="edit_leave" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Leave</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="f1_edit" id="f1_edit" method="post" action="leaves.php">
                                <input type="hidden" name="leave_id" id="edit_leave_id">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Type <span class="text-danger">*</span></label>
                                    <select name="leave_type_id" id="edit_leave_type_id" class="form-control">
                                        <?php
                                        $query_leave_type = "SELECT * FROM hrm_leave_type;";
                                        $result_leave_type = mysqli_query($conn, $query_leave_type) or die(mysqli_error($conn));
                                        while ($row_leave_type = mysqli_fetch_array($result_leave_type)) {
                                        ?>
                                            <option value="<?php echo $row_leave_type['id']; ?>">
                                                <?php echo $row_leave_type['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Day Type <span class="text-danger">*</span></label>
                                    <select class="select" name="day_type" id="edit_day_type">
                                        <option value="2">Full Day</option>
                                        <option value="1">Half Day</option>
                                        <option value="3">Short Leave</option>
                                    </select>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">From <span class="text-danger">*</span></label>
                                    <div>
                                        <input class="form-control" type="date" name="from_date" id="edit_from_date">
                                    </div>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">To <span class="text-danger">*</span></label>
                                    <div>
                                        <input class="form-control" type="date" name="to_date" id="edit_to_date" onblur="Difference_In_Days_Edit();">
                                    </div>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Number of days <span class="text-danger">*</span></label>
                                    <input class="form-control" readonly type="text" name="no_of_days" id="edit_no_of_days" onclick="Difference_In_Days_Edit();">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Remaining Leaves <span class="text-danger">*</span></label>
                                    <input class="form-control" readonly value="1" type="text">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Reason <span class="text-danger">*</span></label>
                                    <textarea rows="4" class="form-control" name="leave_reason" id="edit_leave_reason"></textarea>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn" name="b1_edit_leave" id="b1_edit_leave">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Edit Leave Modal -->

            <!-- Approve Leave Modal -->
            <div class="modal custom-modal fade" id="approve_leave" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Leave Approve</h3>
                                <p>Are you sure want to approve for this leave?</p>
                            </div>
                            <div class="modal-btn delete-action">
                                <div class="row">
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-primary continue-btn">Approve</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Decline</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Approve Leave Modal -->

            <!-- Delete Leave Modal -->
            <div class="modal custom-modal fade" id="delete_approve" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Delete Leave</h3>
                                <p>Are you sure want to delete this leave?</p>
                            </div>
                            <div class="modal-btn delete-action">
                                <div class="row">
                                    <div class="col-6">
                                        <a href="#" id="confirm-delete-btn" class="btn btn-primary continue-btn">Delete</a>
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
            <!-- /Delete Leave Modal -->
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- end main wrapper-->

    <?php include 'layouts/customizer.php'; ?>
    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
$(document).ready(function() {
    $('.datatable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        createdRow: function(row, data, dataIndex) {
            var isAdmin = data.length > 9; // Admin table has 11 columns, non-admin has 9
            if (isAdmin) {
                var status = data[9].trim(); // Status column (index 9)
                var fromDate = data[5]; // From column (index 5)
                var toDate = data[6]; // To column (index 6)
                var today = new Date().toISOString().split('T')[0]; // Current date in YYYY-MM-DD

                if (status === 'Approved' && fromDate <= today && toDate >= today) {
                    $(row).addClass('status-approved-current');
                } else if (status === 'New') {
                    $(row).addClass('status-new');
                } else if (status === 'Approved') {
                    $(row).addClass('status-approved');
                } else if (status === 'Pending') {
                    $(row).addClass('status-pending');
                } else if (status === 'Declined') {
                    $(row).addClass('status-declined');
                }
            }
        }
    });

    $('.modal-table').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        createdRow: function(row, data, dataIndex) {
            var status = data[9].trim(); // Status column (index 9)
            var fromDate = data[5]; // From column (index 5)
            var toDate = data[6]; // To column (index 6)
            var today = new Date().toISOString().split('T')[0]; // Current date in YYYY-MM-DD

            if (status === 'Approved' && fromDate <= today && toDate >= today) {
                $(row).addClass('status-approved-current');
            } else if (status === 'New') {
                $(row).addClass('status-new');
            } else if (status === 'Approved') {
                $(row).addClass('status-approved');
            } else if (status === 'Pending') {
                $(row).addClass('status-pending');
            } else if (status === 'Declined') {
                $(row).addClass('status-declined');
            }
        }
    });

    $('.delete-leave-btn').on('click', function() {
        var leaveId = $(this).data('leave-id');
        $('#confirm-delete-btn').attr('href', 'leaves.php?delete_leave_id=' + leaveId);
    });

    $('.edit-leave-btn').on('click', function() {
        var leaveId = $(this).data('leave-id');
        var leaveTypeId = $(this).data('leave-type-id');
        var dayType = $(this).data('day-type');
        var fromDate = $(this).data('from-date');
        var toDate = $(this).data('to-date');
        var noOfDays = $(this).data('no-of-days');
        var leaveReason = $(this).data('leave-reason');

        $('#edit_leave_id').val(leaveId);
        $('#edit_leave_type_id').val(leaveTypeId);
        $('#edit_day_type').val(dayType);
        $('#edit_from_date').val(fromDate);
        $('#edit_to_date').val(toDate);
        $('#edit_no_of_days').val(noOfDays);
        $('#edit_leave_reason').val(leaveReason);
    });
});

function Difference_In_Days_Edit() {
    let fromDate = document.getElementById("edit_from_date").value;
    let toDate = document.getElementById("edit_to_date").value;
    let noOfDaysInput = document.getElementById("edit_no_of_days");

    if (fromDate && toDate) {
        let fromDateObj = new Date(fromDate);
        let toDateObj = new Date(toDate);
        let differenceInTime = toDateObj.getTime() - fromDateObj.getTime();
        let differenceInDays = Math.round(differenceInTime / (1000 * 3600 * 24)) + 1;
        noOfDaysInput.value = differenceInDays;
    } else {
        noOfDaysInput.value = "";
    }
}
    </script>
</body>
</html>