<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');
date_default_timezone_set('Asia/Kolkata');
include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

// get user name and other detail

$emp_id = $_SESSION['id'] ?? null;
$emp_session_id = $_SESSION['id'] ?? null;

if ($emp_id === null || $emp_session_id === null) {
    die("Session ID is not set.");
}

$conn = connect();
$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result) ?? [];

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . ($row['image'] ?? 'default_image.jpg');

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");
?>

<head>
    <title>Leaves Employees - HRMS admin template</title>
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
                            <h3 class="page-title">Leaves</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Leaves</li>
                            </ul>
                        </div>
                        <div class="col-auto float-end ms-auto">
                            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_leave"><i class="fa-solid fa-plus"></i> Add Leave</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Leave Statistics -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-info">
                            <h6>Monthly Leave (Casual Leave 1 Days)</h6>
                            <h4><?php echo display_leave_by_type(1, $emp_session_id); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-info">
                            <h6>Other Leave (Loss of Pay)</h6>
                            <h4><?php echo display_leave_by_type(3, $emp_session_id); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-info">
                            <h6>Remaining Leave</h6>
                            <h4><?php echo remaining_leave(1, $emp_session_id); ?></h4>
                        </div>
                    </div>
                </div>
                <!-- /Leave Statistics -->

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table leave-employee-table mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>No of Days</th>
                                        <th>Reason</th>
                                        <th class="text-center">Status</th>
                                        <th>Approved by</th>
                                        <?php if (isset($row['status']) && $row['status'] == 0) { ?>
                                            <th class="text-end">Actions</th>
                                        <?php } ?>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days, hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id, hrm_leave_applied.approved_by FROM hrm_leave_applied JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id WHERE hrm_employee.status=1 AND hrm_leave_applied.emp_id='$emp_session_id';";
                                    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                                    while ($row = mysqli_fetch_array($result)) {
                                        $leave_type = get_value("hrm_leave_type", "name", $row['leave_type_id']);
                                        $start_date = date("Y-m-d", strtotime($row['start_date']));
                                        $end_date = date("Y-m-d", strtotime($row['end_date']));
                                        $no_of_days = $row['no_of_days'];
                                        $leave_reason = $row['leave_reason'];
                                        $day_type = $row['day_type'];
                                        $approved_by = $row['approved_by'] != 0 ? get_value("hrm_employee", "fname", $row['approved_by']) : 0;
                                        $status_value = (int)$row['status'];
                                        $status = match ($status_value) {
                                            1 => "Pending",
                                            0 => "New",
                                            2 => "Approved",
                                            3 => "Declined",
                                            default => "Unknown",
                                        };
                                        
                                    ?>
                    
                                        <tr>
                                            <td><?php echo $leave_type; ?></td>
                                            <td><?php echo $start_date; ?></td>
                                            <td><?php echo $end_date; ?></td>
                                            <td><?php echo $no_of_days; ?></td>
                                            <td><?php echo $leave_reason; ?></td>
                                            <td><?php echo $status; ?></td>
                                            <td>
                                                <?php if ($row['approved_by'] == 1) { ?>
                                                    <h2 class="table-avatar">
                                                        <a href="profile.php?id=<?php echo $row['approved_by']; ?>" class="avatar avatar-xs"><img src="assets/img/profiles/avatar-09.jpg" alt="User Image"><?php echo $approved_by; ?></a>
                                                    </h2>
                                                <?php } ?>
                                            </td>
                                            <?php if ($row['status'] == 0) { ?>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#edit_leave"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_approve"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
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

            <!-- Add Leave Modal -->
            <div id="add_leave" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Leave</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="f1" id="f1" method="post" action="leaves-employee.php">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Type <span class="text-danger">*</span></label>
                                    <select name="leave_type_id" id="leave_type_id" class="form-control">
                                        <?php
                                        $query_leave_type = "SELECT * FROM hrm_leave_type;";
                                        $result_leave_type = mysqli_query($conn, $query_leave_type) or die(mysqli_error($conn));
                                        while ($row_leave_type = mysqli_fetch_array($result_leave_type)) { ?>
                                            <option value="<?php echo $row_leave_type['id']; ?>"><?php echo $row_leave_type['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-block mb-3">
                                    <label class="col-form-label">Day Type <span class="text-danger">*</span></label>
                                    <select class="select" name="day_type" id="day_type">
                                        <option value="2">Full Day</option>
                                        <option value="1">Half Day</option>
                                    </select>
                                </div>

                                <div class="input-block mb-3">
                                    <label class="col-form-label">From <span class="text-danger">*</span></label>
                                    <div>
                                        <input class="form-control" type="date" name="from_date" id="from_date">
                                    </div>
                                </div>

                                <div class="input-block mb-3">
                                    <label class="col-form-label">To <span class="text-danger">*</span></label>
                                    <div>
                                        <input class="form-control" type="date" name="to_date" id="to_date" onblur="Difference_In_Days();">
                                    </div>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Number of days <span class="text-danger">*</span></label>
                                    <input class="form-control" readonly type="text" value="" name="no_of_days" id="no_of_days" onclick="Difference_In_Days();">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Remaining Leaves <span class="text-danger">*</span></label>
                                    <input class="form-control" readonly value="1" type="text">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Reason <span class="text-danger">*</span></label>
                                    <textarea rows="4" class="form-control" name="leave_reason" id="leave_reason"></textarea>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn" name="b1_leave" id="b1_leave">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Add Leave Modal -->

            <?php
            if (isset($_POST['b1_leave'])) {
                $leave_type_id = mysqli_real_escape_string($conn, $_POST['leave_type_id']);
                $day_type = mysqli_real_escape_string($conn, $_POST['day_type']);
                $from_date = mysqli_real_escape_string($conn, $_POST['from_date']);
                $to_date = mysqli_real_escape_string($conn, $_POST['to_date']);
                $from_date = date('Y-m-d H:i:s', strtotime($from_date));
                $to_date = date('Y-m-d H:i:s', strtotime($to_date));
                $no_of_days = mysqli_real_escape_string($conn, $_POST['no_of_days']);
                $leave_reason = mysqli_real_escape_string($conn, $_POST['leave_reason']);

                $emp_name = get_value("hrm_employee", "fname", $emp_id);
                $emp_email = get_value("hrm_employee", "email", $emp_session_id);
                $admin_email = get_value("hrm_employee", "email", 10);

                $query = "INSERT INTO hrm_leave_applied (leave_type_id, day_type, start_date, end_date, no_of_days, leave_reason, emp_id) VALUES ('$leave_type_id', '$day_type', '$from_date', '$to_date', '$no_of_days', '$leave_reason', '$emp_session_id');";
                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

                $subject = 'HR-Leave Applied By ' . $emp_name;
                $headers = "From: " . strip_tags($admin_email) . "\r\n";
                $headers .= "Reply-To: " . strip_tags($admin_email) . "\r\n";
                $headers .= "CC: pythondolphin@gmail.com,pythondolphin@gmail.com\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                $leave_type_name = match ($leave_type_id) {
                    1 => "Casual Leave 1 Days",
                    3 => "Loss of Pay",
                    default => "Unknown",
                };

                $day_type_name = match ($day_type) {
                    1 => "Half Day",
                    2 => "Full Day",
                    default => "Unknown",
                };

                $message = '<p><strong>Leave Type</strong> - ' . $leave_type_name . '</p>' .
                    '<p><strong>Day Type</strong> - ' . $day_type_name . '</p>' .
                    '<p><strong>Start Date</strong> - ' . $from_date . '</p>' .
                    '<p><strong>End Date</strong> - ' . $to_date . '</p>' .
                    '<p><strong>No. Of Days</strong> - ' . $no_of_days . '</p>' .
                    '<p><strong>Leave Reason</strong> - ' . $leave_reason . '</p>';

                mail("pythondolphin@gmail.com,pythondolphin@gmail.com", $subject, $message, $headers);

                $subject = 'Leave Applied';
                $headers = "From: " . strip_tags("pythondolphin@gmail.com") . "\r\n";
                $headers .= "Reply-To: " . strip_tags("pythondolphin@gmail.com") . "\r\n";
                $headers .= "CC: pythondolphin@gmail.com,pythondolphin@gmail.com\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                mail($emp_email, $subject, $message, $headers);
                echo "<script>alert('Leave Applied Successfully!'); window.location.href='leaves-employee.php';</script>";
            }
            ?>

            <!-- Edit Leave Modal -->
            <div id="edit_leave" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Leave</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Type <span class="text-danger">*</span></label>
                                    <select class="select">
                                        <option>Select Leave Type</option>
                                        <option>Casual Leave 1 Days</option>
                                    </select>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">From <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" value="01-01-2019" type="text">
                                    </div>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">To <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" value="01-01-2019" type="text">
                                    </div>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Number of days <span class="text-danger">*</span></label>
                                    <input class="form-control" readonly type="text" value="2">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Remaining Leaves <span class="text-danger">*</span></label>
                                    <input class="form-control" readonly value="1" type="text">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Reason <span class="text-danger">*</span></label>
                                    <textarea rows="4" class="form-control">Going to hospital</textarea>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Edit Leave Modal -->

            <!-- Delete Leave Modal -->
            <div class="modal custom-modal fade" id="delete_approve" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Delete Leave</h3>
                                <p>Are you sure want to Cancel this leave?</p>
                            </div>
                            <div class="modal-btn delete-action">
                                <div class="row">
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-primary continue-btn">Delete</a>
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

    <script language="javascript">
        function Difference_In_Days() {
            let date1 = new Date(document.getElementById('from_date').value);
            let date2 = new Date(document.getElementById('to_date').value);
            let Difference_In_Time = date2.getTime() - date1.getTime();
            let Difference_In_Days = Math.round(Difference_In_Time / (1000 * 3600 * 24)) + 1;
            document.getElementById('no_of_days').value = Difference_In_Days;
        }
        
    </script>
</body>

</html>