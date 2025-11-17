<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');

include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';
include 'email/mailer.php'; // Include mailer.php for email sending

// Get user name and other details
$emp_id = $_SESSION['id'] ?? null;
$emp_session_id = $_SESSION['id'] ?? null;

if ($emp_id === null || $emp_session_id === null) {
    die("Session ID is not set.");
}

$conn = connect();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
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

// Check if Casual Leave is already applied this month
$current_month = date('Y-m');
$casual_leave_query = "SELECT COUNT(*) as casual_count FROM hrm_leave_applied WHERE emp_id='$emp_session_id' AND leave_type_id=1 AND DATE_FORMAT(start_date, '%Y-%m')='$current_month'";
$casual_leave_result = mysqli_query($conn, $casual_leave_query);
if (!$casual_leave_result) {
    die("Casual leave query failed: " . mysqli_error($conn));
}
$casual_leave_row = mysqli_fetch_array($casual_leave_result);
$casual_leave_applied = $casual_leave_row['casual_count'] > 0;

// Handle leave application form submission
// if (isset($_POST['b1_leave'])) {
//     $leave_type_id = mysqli_real_escape_string($conn, $_POST['leave_type_id'] ?? '');
//     $day_type = mysqli_real_escape_string($conn, $_POST['day_type'] ?? '');
//     $from_date = mysqli_real_escape_string($conn, $_POST['from_date'] ?? '');
//     $to_date = mysqli_real_escape_string($conn, $_POST['to_date'] ?? '');
//     $no_of_days = mysqli_real_escape_string($conn, $_POST['no_of_days'] ?? '');
//     $leave_reason = mysqli_real_escape_string($conn, $_POST['leave_reason'] ?? '');

//     // Server-side validation
//     if (empty($leave_type_id) || empty($day_type) || empty($from_date) || empty($to_date) || empty($no_of_days) || empty($leave_reason)) {
//         echo "<script>alert('All fields are required!');</script>";
//     } else {
//         // Validate Short Leave
//         if ($leave_type_id == 4) { // Short Leave
//             if ($day_type != 3) { // Must be Short Leave day type
//                 echo "<script>alert('Short Leave must have Day Type set to Short Leave!');</script>";
//                 exit;
//             }
//             $from_date_obj = new DateTime($from_date);
//             $to_date_obj = new DateTime($to_date);
//             $interval = $from_date_obj->diff($to_date_obj);
//             $hours = $interval->h + ($interval->days * 24);
//             if ($hours != 2 || $no_of_days != '0.25') { // Ensure 2-hour duration and 0.25 days
//                 echo "<script>alert('Short Leave must be exactly 2 hours (0.25 days)!');</script>";
//                 exit;
//             }
//         }

//         $from_date = date('Y-m-d H:i:s', strtotime($from_date));
//         $to_date = date('Y-m-d H:i:s', strtotime($to_date));

//         $emp_name = get_value("hrm_employee", "fname", $emp_id);
//         $emp_email = get_value("hrm_employee", "email", $emp_session_id);

//         $query = "INSERT INTO hrm_leave_applied (leave_type_id, day_type, start_date, end_date, no_of_days, leave_reason, emp_id) VALUES ('$leave_type_id', '$day_type', '$from_date', '$to_date', '$no_of_days', '$leave_reason', '$emp_session_id')";
//         $result = mysqli_query($conn, $query);
//         if (!$result) {
//             die("Insert query failed: " . mysqli_error($conn));
//         }

//         if ($leave_type_id == 1) {
//             $leave_type_name = "Casual Leave 1 Days";
//         } elseif ($leave_type_id == 3) {
//             $leave_type_name = "Loss of Pay";
//         } elseif ($leave_type_id == 4) {
//             $leave_type_name = "Short Leave";
//         } else {
//             $leave_type_name = "Unknown";
//         }

//         if ($day_type == 1) {
//             $day_type_name = "Half Day";
//         } elseif ($day_type == 2) {
//             $day_type_name = "Full Day";
//         } elseif ($day_type == 3) {
//             $day_type_name = "Short Leave";
//         } else {
//             $day_type_name = "Unknown";
//         }

//         // Clean and normalize leave_reason
//         $clean_leave_reason = stripcslashes($leave_reason); // Remove escaped backslashes
//         $normalized_leave_reason = str_replace(array("\r\n", "\r", "\n"), "\n", $clean_leave_reason); // Normalize line endings
//         $formatted_leave_reason = nl2br(htmlspecialchars($normalized_leave_reason, ENT_QUOTES, 'UTF-8')); // Convert to HTML

//         // Prepare email content
//         $subject = 'HRM->Leave Applied By ' . $emp_name;
//         $message = "<html>\n<head>\n<meta charset=\"UTF-8\">\n</head>\n<body>\n";
//         $message .= '<p><strong>Leave Type</strong> - ' . htmlspecialchars($leave_type_name, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
//         $message .= '<p><strong>Day Type</strong> - ' . htmlspecialchars($day_type_name, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
//         $message .= '<p><strong>Start Date</strong> - ' . htmlspecialchars(date('d-m-Y H:i', strtotime($from_date)), ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
//         $message .= '<p><strong>End Date</strong> - ' . htmlspecialchars(date('d-m-Y H:i', strtotime($to_date)), ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
//         $message .= '<p><strong>No. Of Days</strong> - ' . htmlspecialchars($no_of_days, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
//         $message .= '<p><strong>Leave Reason</strong> - <br> <br>' . htmlspecialchars($formatted_leave_reason, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
//         $message .= "</body>\n</html>";

//         // Debug: Log the leave reason processing
//         error_log("Raw leave_reason: " . $leave_reason);
//         error_log("Clean leave_reason: " . $clean_leave_reason);
//         error_log("Normalized leave_reason: " . $normalized_leave_reason);
//         error_log("Formatted leave_reason: " . $formatted_leave_reason);

//         // Send email using mailer.php
//         $cc_emails = [$emp_email, 'pythondolphin@gmail.com', 'shivam@1solutions.biz'];
//         $mail_success = send_email('hr@1solutions.biz', $subject, $message, $cc_emails);

//         if ($mail_success) {
//             echo "<script>alert('Leave Applied Successfully!'); window.location.href='leaves-employee.php';</script>";
//         } else {
//             echo "<script>alert('Leave applied, but failed to send email. Please check server configuration.');</script>";
//         }
//     }
// }

// Function to convert day_type to readable format
function get_day_type_name($day_type)
{
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



<!DOCTYPE html>
<html lang="en">

<head>
    <title>Leaves Employees - HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="page-wrapper">
            <div class="content container-fluid">
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
                            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_leave"><i
                                    class="fa-solid fa-plus"></i> Add Leave</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-info">
                            <h6>Monthly Leave (Casual Leave 1 Days)</h6>
                            <h4><?php echo display_leave_by_type(1, $emp_session_id); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-info">
                            <h6>Other Leave (Loss of Pay)</h6>
                            <h4><?php echo display_leave_by_type(3, $emp_session_id); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-info">
                            <h6>Remaining Leave</h6>
                            <h4><?php echo remaining_leave(1, $emp_session_id); ?></h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table leave-employee-table mb-0" id="leaveTable">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Day Type</th>
                                        <th>Applied Time</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>No of Days</th>
                                        <th>Reason</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT hrm_leave_applied.id AS leave_applied_id, hrm_leave_applied.status, hrm_leave_applied.leave_type_id, hrm_leave_applied.start_date, hrm_leave_applied.end_date, hrm_leave_applied.no_of_days, hrm_leave_applied.created_at, hrm_leave_applied.leave_reason, hrm_leave_applied.day_type, hrm_leave_applied.emp_id, hrm_leave_applied.approved_by FROM hrm_leave_applied JOIN hrm_employee ON hrm_leave_applied.emp_id = hrm_employee.id WHERE hrm_employee.status=1 AND hrm_leave_applied.emp_id='$emp_session_id' ORDER BY hrm_leave_applied.created_at DESC";
                                    $result = mysqli_query($conn, $query);
                                    if (!$result) {
                                        die("Table query failed: " . mysqli_error($conn));
                                    }
                                    while ($row = mysqli_fetch_array($result)) {
                                        $leave_type = get_value("hrm_leave_type", "name", $row['leave_type_id']);
                                        $day_type_name = get_day_type_name($row['day_type']);
                                        $start_date = date("Y-m-d H:i", strtotime($row['start_date']));
                                        $end_date = date("Y-m-d H:i", strtotime($row['end_date']));
                                        $no_of_days = $row['no_of_days'];
                                        $created_at = $row['created_at'];
                                        $leave_reason = $row['leave_reason'];
                                        $approved_by = $row['approved_by'] != 0 ? get_value("hrm_employee", "fname", $row['approved_by']) : 0;
                                        $status_value = (int) $row['status'];
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
                                            <td><?php echo $day_type_name; ?></td>
                                            <td><?php echo date("d-m-Y H:i", strtotime($created_at)); ?></td>
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
                                            <td class="text-center"><?php echo $status; ?></td>
                                            <td class="text-end">
                                                <?php if ($row['status'] == 0) { ?>
                                                    <div class="dropdown dropdown-action">
                                                        <a href="#" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" style="display:none;" href="#"
                                                                data-bs-toggle="modal" data-bs-target="#edit_leave"><i
                                                                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#delete_approve"
                                                                onclick="setLeaveId(<?php echo $row['leave_applied_id']; ?>)">
                                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

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
                            <form name="f1" id="f1" method="post" action="leaves-employee_1.php">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Type <span class="text-danger">*</span></label>
                                    <select name="leave_type_id" id="leave_type_id" class="form-control" required>
                                        <?php
                                        // $query_leave_type = "SELECT * FROM hrm_leave_type WHERE id != '4';";
                                        $query_leave_type = "SELECT * FROM hrm_leave_type;";
                                        $result_leave_type = mysqli_query($conn, $query_leave_type);
                                        if (!$result_leave_type) {
                                            die("Leave type query failed: " . mysqli_error($conn));
                                        }
                                        while ($row_leave_type = mysqli_fetch_array($result_leave_type)) {
                                            $disabled = ($row_leave_type['id'] == 1 && $casual_leave_applied) ? 'disabled' : '';
                                            ?>
                                            <option value="<?php echo $row_leave_type['id']; ?>" <?php echo $disabled; ?>>
                                                <?php echo $row_leave_type['name']; ?>
                                                <?php if ($row_leave_type['id'] == 1 && $casual_leave_applied)
                                                    echo " (Already Applied This Month)"; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-block mb-3">
                                    <label class="col-form-label">Day Type <span class="text-danger">*</span></label>
                                    <select class="select" name="day_type" id="day_type" required>
                                        <option value="2">Full Day</option>
                                        <option value="1">Half Day</option>
                                        <option value="3">Short Leave</option>
                                    </select>
                                </div>

                                <div class="input-block mb-3">
                                    <label class="col-form-label">From <span class="text-danger">*</span></label>
                                    <div>
                                        <input class="form-control" type="date" name="from_date" id="from_date"
                                            required>
                                    </div>
                                </div>

                                <div class="input-block mb-3">
                                    <label class="col-form-label">To <span class="text-danger">*</span></label>
                                    <div>
                                        <input class="form-control" type="date" name="to_date" id="to_date" required>
                                    </div>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Number of days <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" readonly type="text" value="" name="no_of_days"
                                        id="no_of_days" required>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Remaining Leaves <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" readonly value="1" type="text">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Leave Reason <span
                                            class="text-danger">*</span></label>
                                    <textarea rows="4" class="form-control" name="leave_reason" id="leave_reason"
                                        required></textarea>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn" type="submit" name="b1_leave"
                                        id="b1_leave">Submit</button>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Add Leave Modal -->


            <!-- Delete Leave Modal -->
            <div class="modal custom-modal fade" id="delete_approve" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Delete Leave</h3>
                                <p>Are you sure you want to cancel this leave?</p>
                            </div>
                            <form id="deleteLeaveForm" method="post" action="delete_leave.php">
                                <input type="hidden" name="leave_id" id="leave_id" value="">
                                <div class="modal-btn delete-action">
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="submit" class="btn btn-primary continue-btn">Delete</button>
                                        </div>
                                        <div class="col-6">
                                            <a href="javascript:void(0);" data-bs-dismiss="modal"
                                                class="btn btn-primary cancel-btn">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Delete Leave Modal -->
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#leaveTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "lengthMenu": [5, 10, 25, 50, 100, 500],
                "pageLength": 10
            });

            // Handle leave type change
            $('#leave_type_id').on('change', function () {
                let leaveType = $(this).val();
                let fromDateInput = $('#from_date');
                let toDateInput = $('#to_date');
                let dayTypeSelect = $('#day_type');
                let noOfDaysInput = $('#no_of_days');

                // Reset fields
                fromDateInput.val('');
                toDateInput.val('');
                noOfDaysInput.val('');

                if (leaveType == 4) { // Short Leave
                    // Change input types to datetime-local
                    fromDateInput.attr('type', 'datetime-local');
                    toDateInput.attr('type', 'datetime-local');

                    // Set Day Type to Short Leave and restrict other options
                    dayTypeSelect.val('3');
                    dayTypeSelect.find('option').prop('disabled', true);
                    dayTypeSelect.find('option[value="3"]').prop('disabled', false);

                    // Set number of days for Short Leave (2 hours = 0.25 days)
                    noOfDaysInput.val('0.25');
                    noOfDaysInput.prop('readonly', true);
                } else {
                    // Revert to date inputs
                    fromDateInput.attr('type', 'date');
                    toDateInput.attr('type', 'date');

                    // Enable all day type options and set default
                    dayTypeSelect.find('option').prop('disabled', false);
                    dayTypeSelect.val('2'); // Default to Full Day

                    // Make number of days editable
                    noOfDaysInput.prop('readonly', false);
                    Difference_In_Days(); // Recalculate days
                }
                toDateInput.prop('readonly', leaveType == 4); // Readonly for Short Leave
            });

            // Handle from date and to date changes
            $('#from_date, #to_date').on('change', function () {
                let leaveType = $('#leave_type_id').val();
                let fromDate = $('#from_date').val();
                let toDateInput = $('#to_date');
                let noOfDaysInput = $('#no_of_days');
                let dayType = $('#day_type').val();

                if (leaveType == 4 && fromDate) { // Short Leave
                    // Parse the input date in local time
                    let fromDateObj = new Date(fromDate);
                    if (isNaN(fromDateObj)) {
                        alert('Invalid date format');
                        return;
                    }
                    // Add exactly 2 hours
                    let toDateObj = new Date(fromDateObj.getTime() + (2 * 60 * 60 * 1000));
                    // Format as YYYY-MM-DDTHH:mm for datetime-local
                    let toDateFormatted = toDateObj.getFullYear() + '-' +
                        String(toDateObj.getMonth() + 1).padStart(2, '0') + '-' +
                        String(toDateObj.getDate()).padStart(2, '0') + 'T' +
                        String(toDateObj.getHours()).padStart(2, '0') + ':' +
                        String(toDateObj.getMinutes()).padStart(2, '0');
                    toDateInput.val(toDateFormatted);
                    toDateInput.prop('readonly', true); // Make To field readonly
                    noOfDaysInput.val('0.25'); // Ensure no_of_days is 0.25
                } else {
                    toDateInput.prop('readonly', false); // Allow editing for other leave types
                    Difference_In_Days();
                }
            });

            // Handle day type change
            $('#day_type').on('change', function () {
                Difference_In_Days();
            });

            // Form validation
            $('#f1').on('submit', function (e) {
                let leaveType = $('#leave_type_id').val();
                let dayType = $('#day_type').val();
                let fromDate = $('#from_date').val();
                let toDate = $('#to_date').val();
                let noOfDays = $('#no_of_days').val();
                let leaveReason = $('#leave_reason').val().trim();

                if (!leaveType || !dayType || !fromDate || !toDate || !noOfDays || !leaveReason) {
                    e.preventDefault();
                    alert('All fields are required!');
                    return false;
                }

                if (leaveType == 4 && dayType != 3) {
                    e.preventDefault();
                    alert('Short Leave must have Day Type set to Short Leave!');
                    return false;
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            let today = new Date();
            let sevenDaysBefore = new Date();
            let sevenDaysAfter = new Date();

            sevenDaysBefore.setDate(today.getDate() - 7);
            sevenDaysAfter.setDate(today.getDate() + 7);

            let fromDateInput = document.getElementById("from_date");
            fromDateInput.setAttribute("min", sevenDaysBefore.toISOString().split("T")[0]);
            fromDateInput.setAttribute("max", sevenDaysAfter.toISOString().split("T")[0]);

            // Update min/max for datetime-local when Short Leave is selected
            $('#leave_type_id').on('change', function () {
                let leaveType = $(this).val();
                if (leaveType == 4) {
                    fromDateInput.setAttribute("min", sevenDaysBefore.toISOString().slice(0, 16));
                    fromDateInput.setAttribute("max", sevenDaysAfter.toISOString().slice(0, 16));
                } else {
                    fromDateInput.setAttribute("min", sevenDaysBefore.toISOString().split("T")[0]);
                    fromDateInput.setAttribute("max", sevenDaysAfter.toISOString().split("T")[0]);
                }
            });
        });

        function Difference_In_Days() {
            let leaveType = document.getElementById("leave_type_id").value;
            let fromDate = document.getElementById("from_date").value;
            let toDate = document.getElementById("to_date").value;
            let dayType = document.getElementById("day_type").value;
            let noOfDaysInput = document.getElementById("no_of_days");

            if (leaveType != 4 && fromDate && toDate) { // Not Short Leave
                let fromDateObj = new Date(fromDate);
                let toDateObj = new Date(toDate);
                if (isNaN(fromDateObj) || isNaN(toDateObj)) {
                    noOfDaysInput.value = "";
                    return;
                }
                let differenceInTime = toDateObj.getTime() - fromDateObj.getTime();
                let differenceInDays = Math.ceil(differenceInTime / (1000 * 3600 * 24));

                if (dayType == 1) { // Half Day
                    if (differenceInDays === 0) {
                        noOfDaysInput.value = "0.5"; // Same day half-day leave
                    } else {
                        noOfDaysInput.value = differenceInDays * 0.5;
                    }
                } else if (dayType == 2) { // Full Day
                    noOfDaysInput.value = differenceInDays + 1; // Include both start and end dates
                } else {
                    noOfDaysInput.value = "";
                }
            } else if (leaveType == 4) {
                noOfDaysInput.value = "0.25"; // Fixed for Short Leave (2 hours)
            } else {
                noOfDaysInput.value = "";
            }
        }

        function setLeaveId(leaveId) {
            document.getElementById('leave_id').value = leaveId;
        }
    </script>

</body>

</html>