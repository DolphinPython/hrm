<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php'; ?>

<?php
$conn = connect();
$emp_id = $_SESSION['id'];

// Define admin/HR IDs who can see all employees
$admin_ids = [10, 14];
$is_admin = in_array($emp_id, $admin_ids);

// Fetch employees for the dropdown with doj
if ($is_admin) {
    $employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name, doj FROM hrm_employee";
} else {
    $employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name, doj FROM hrm_employee WHERE id = '$emp_id' 
                       UNION 
                       SELECT he.id, CONCAT(he.fname, ' ', he.lname) AS name, he.doj 
                       FROM hrm_employee he 
                       INNER JOIN hrm_reporting_manager hrm ON he.id = hrm.employee_id 
                       WHERE hrm.reporting_manager_id = '$emp_id'";
}
$employee_result = mysqli_query($conn, $employee_query) or die(mysqli_error($conn));

$query = "SELECT * FROM hrm_employee WHERE id='$emp_id'";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

if (isset($_GET['employee_id'])) {
    $emp_id = $_GET['employee_id'];
    $query = "SELECT * FROM hrm_employee WHERE id = '$emp_id'";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
    $row = mysqli_fetch_array($result);
    $name = $row['fname'] . ' ' . $row['lname'];
    $email = $row['email'];
}

if (isset($_GET['employee_id']) && isset($_GET['month']) && isset($_GET['year'])) {
    $employee_id = intval($_GET['employee_id']);
    $month = intval($_GET['month']);
    $year = intval($_GET['year']);
    $url = "calculate-salary.php?id={$employee_id}&month={$month}&year={$year}";
}

// Handle Delete Action
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_query = "DELETE FROM newuser_attendance WHERE id = '$delete_id'";
    mysqli_query($conn, $delete_query);
    exit();
}
?>

<head>
    <title>Attendance Reports - HRMS</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Attendance Reports Employee</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Attendance Reports</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="row">
                    <div class="col-md-12">
                        <form method="GET" action="" id="attendanceFilterForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="employee">Select Employee</label>
                                    <select name="employee_id" id="employee" class="form-control">
                                        <option value="">Select Employee</option>
                                        <?php 
                                        while ($employee = mysqli_fetch_assoc($employee_result)) { ?>
                                            <option value="<?= $employee['id']; ?>" data-doj="<?= $employee['doj']; ?>">
                                                <?= $employee['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="month">Select Month</label>
                                    <select name="month" id="month" class="form-control">
                                        <option value="">Select Month</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="year">Select Year</label>
                                    <select name="year" id="year" class="form-control">
                                        <option value="">Select Year</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label> </label>
                                    <button type="submit" class="btn btn-success w-100">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Add Export Button and Present Days Count -->
                <div class="row mt-4">
                    <div class="col-md-12 text-right">
                        <button id="exportButton" class="btn btn-success">Export to CSV</button>
                        <button id="sendEmailButton" class="btn btn-info">Send Email</button>
                        <i id="loadingIcon" class="fas fa-spinner fa-spin" style="display: none;"></i>
                        <?php if ($is_admin && isset($url)) { ?>
                            <a href="<?php echo $url; ?>" class="btn btn-primary">Calculate Salary</a>
                        <?php } ?>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <?php
                            $present_days = 0;
                            if (isset($_GET['employee_id'], $_GET['month'], $_GET['year']) &&
                                !empty($_GET['employee_id']) &&
                                !empty($_GET['month']) &&
                                !empty($_GET['year'])) {
                                $employee_id = $_GET['employee_id'];
                                $month = $_GET['month'];
                                $year = $_GET['year'];
                                
                                $emp_query = "SELECT CONCAT(fname, ' ', lname) as employee_name FROM hrm_employee WHERE id = '$employee_id'";
                                $emp_result = mysqli_query($conn, $emp_query);
                                $emp_row = mysqli_fetch_assoc($emp_result);
                                $employee_name = $emp_row['employee_name'];
                            ?>
                            <h2 class="text-center m-3 text-danger" id="name"><?= $employee_name ?? ""; ?></h2>
                            <h4 class="text-center m-3">Present Days: <span id="presentDaysCount"><?= $present_days ?></span></h4>
                            <?php } else { ?>
                            <h2 class="text-center m-3 text-danger" id="name"><?= $name ?? ""; ?></h2>
                            <?php } ?>
                            <input type="hidden" name="email" id="email" value="<?= $email ?? ''; ?>" />
                            <table class="table table-striped custom-table mb-0" id="attendanceTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee Name</th>
                                        <th>Date</th>
                                        <th>Clock In</th>
                                        <th>Clock Out</th>
                                        <th>Total Working Time</th>
                                        <th>Extra / Remaining Time</th>
                                        <th>Late</th>
                                        <?php if ($is_admin) { ?>
                                            <th>Action</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($_GET['employee_id'], $_GET['month'], $_GET['year']) &&
                                        !empty($_GET['employee_id']) &&
                                        !empty($_GET['month']) &&
                                        !empty($_GET['year'])) {
                                        $employee_id = $_GET['employee_id'];
                                        $month = $_GET['month'];
                                        $year = $_GET['year'];

                                        $manager_check_query = "SELECT * FROM hrm_reporting_manager WHERE employee_id = '$employee_id' AND reporting_manager_id = '{$_SESSION['id']}'";
                                        $manager_check_result = mysqli_query($conn, $manager_check_query);
                                        $is_manager = mysqli_num_rows($manager_check_result) > 0;

                                        if ($is_admin || $is_manager || $employee_id == $_SESSION['id']) {
                                            $emp_query = "SELECT CONCAT(fname, ' ', lname) as employee_name, doj 
                                                         FROM hrm_employee 
                                                         WHERE id = '$employee_id'";
                                            $emp_result = mysqli_query($conn, $emp_query);
                                            $emp_row = mysqli_fetch_assoc($emp_result);
                                            $employee_name = $emp_row['employee_name'];
                                            $doj = $emp_row['doj'];

                                            // Fetch holidays for the selected year and convert date format
                                            $holiday_query = "SELECT name, date FROM hrm_holidays WHERE year = '$year'";
                                            $holiday_result = mysqli_query($conn, $holiday_query);
                                            $holidays = [];
                                            while ($holiday = mysqli_fetch_assoc($holiday_result)) {
                                                $date_parts = explode('-', $holiday['date']);
                                                if (count($date_parts) === 3) {
                                                    $converted_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
                                                    $holidays[$converted_date] = $holiday['name'];
                                                }
                                            }

                                            // Fetch approved leaves for the employee and month/year
                                            $leave_query = "SELECT start_date, end_date 
                                                          FROM hrm_leave_applied 
                                                          WHERE emp_id = '$employee_id' 
                                                          AND status = 2 
                                                          AND YEAR(start_date) = '$year' 
                                                          AND MONTH(start_date) = '$month'";
                                            $leave_result = mysqli_query($conn, $leave_query);
                                            $approved_leaves = [];
                                            while ($leave = mysqli_fetch_assoc($leave_result)) {
                                                $start_date = new DateTime($leave['start_date']);
                                                $end_date = new DateTime($leave['end_date']);
                                                $interval = new DateInterval('P1D');
                                                $date_range = new DatePeriod($start_date, $interval, $end_date->modify('+1 day'));
                                                foreach ($date_range as $date) {
                                                    $approved_leaves[$date->format('Y-m-d')] = true;
                                                }
                                            }

                                            $attendance_query = "SELECT id, user_id, clock_in_time, clock_in_ip, 
                                                clock_out_time, clock_out_ip, status, created_at, updated_at,
                                                late_status, status_color, total_working_time, 
                                                extra_or_remaining_time, extra_or_remaining_label
                                                FROM newuser_attendance 
                                                WHERE user_id = '$employee_id' 
                                                AND MONTH(clock_in_time) = '$month' 
                                                AND YEAR(clock_in_time) = '$year'
                                                AND clock_in_time >= '$doj'
                                                ORDER BY clock_in_time ASC";
                                            
                                            $attendance_result = mysqli_query($conn, $attendance_query) or die(mysqli_error($conn));
                                            
                                            $attendance_records = [];
                                            while ($row = mysqli_fetch_assoc($attendance_result)) {
                                                $attendance_records[$row['id']] = $row;
                                            }

                                            $today = new DateTime('2025-04-11'); // Current date as per your system
                                            $today_str = $today->format('Y-m-d');
                                            $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                            $count = 1;
                                            $doj_date = new DateTime($doj);

                                            for ($day = 1; $day <= $days_in_month; $day++) {
                                                $current_date = sprintf("%d-%02d-%02d", $year, $month, $day);
                                                $date_obj = new DateTime($current_date);
                                                if ($date_obj < $doj_date) continue; // Skip dates before doj
                                                
                                                $day_of_week = $date_obj->format('N');
                                                $is_future_date = $date_obj > $today;

                                                $record_found = false;
                                                $row = null;
                                                foreach ($attendance_records as $id => $attendance) {
                                                    if (date('Y-m-d', strtotime($attendance['clock_in_time'])) == $current_date && $attendance['status'] !== 'absent') {
                                                        $row = $attendance;
                                                        $record_found = true;
                                                        break;
                                                    }
                                                }

                                                if ($record_found) {
                                                    $present_days++;
                                                    $login_timestamp = strtotime($row['clock_in_time']);
                                                    $clock_out_time = $row['clock_out_time'];
                                                    $logout_display = $clock_out_time ? date("h:i A", strtotime($clock_out_time)) : "N/A";
                                                    $late_status = $row['late_status'];
                                                    $status_color = $row['status_color'];
                                                    $total_working_time = $row['total_working_time'];
                                                    $extra_or_remaining_time = $row['extra_or_remaining_time'];
                                                    $extra_or_remaining_label = $row['extra_or_remaining_label'];
                                    ?>
                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= $employee_name ?></td>
                                                <td><?= $current_date ?></td>
                                                <td><?= date("h:i A", $login_timestamp) ?></td>
                                                <td><?= $logout_display ?></td>
                                                <td><?= $total_working_time ?? 'N/A' ?></td>
                                                <td><?= $extra_or_remaining_label ? "$extra_or_remaining_label: $extra_or_remaining_time" : 'N/A' ?></td>
                                                <td style="color:<?= $status_color ?>"><?= $late_status ?></td>
                                                <?php if ($is_admin) { ?>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary edit-btn" 
                                                    data-id="<?= $row['id'] ?>"
                                                    data-date="<?= $current_date ?>"
                                                    data-clockin="<?= date('H:i', strtotime($row['clock_in_time'])) ?>"
                                                    data-clockout="<?= $clock_out_time ? date('H:i', strtotime($clock_out_time)) : '' ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger delete-btn" 
                                                            data-id="<?= $row['id'] ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                    <?php
                                                } else {
                                                    // Check if the date is a holiday
                                                    if (isset($holidays[$current_date])) {
                                    ?>
                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= $employee_name ?></td>
                                                <td><?= $current_date ?></td>
                                                <td colspan="4" class="text-center text-success"><?= $holidays[$current_date] ?></td>
                                                <td></td>
                                                <?php if ($is_admin) { ?>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary edit-btn" 
                                                    data-id=""
                                                    data-date="<?= $current_date ?>"
                                                    data-clockin=""
                                                    data-clockout="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                    <?php
                                                    } elseif (isset($approved_leaves[$current_date])) {
                                                        // Display Leave for approved leave dates
                                    ?>
                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= $employee_name ?></td>
                                                <td><?= $current_date ?></td>
                                                <td colspan="4" class="text-center text-warning">Leave</td>
                                                <td></td>
                                                <?php if ($is_admin) { ?>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary edit-btn" 
                                                    data-id=""
                                                    data-date="<?= $current_date ?>"
                                                    data-clockin=""
                                                    data-clockout="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                    <?php
                                                    } elseif ($day_of_week == 6 && !$is_future_date) {
                                                        // Display weekends only for past or current dates
                                    ?>
                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= $employee_name ?></td>
                                                <td><?= $current_date ?></td>
                                                <td colspan="4" class="text-center text-primary">Saturday</td>
                                                <td></td>
                                                <?php if ($is_admin) { ?>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary edit-btn" 
                                                    data-id=""
                                                    data-date="<?= $current_date ?>"
                                                    data-clockin=""
                                                    data-clockout="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                    <?php
                                                    } elseif ($day_of_week == 7 && !$is_future_date) {
                                    ?>
                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= $employee_name ?></td>
                                                <td><?= $current_date ?></td>
                                                <td colspan="4" class="text-center text-primary">Sunday</td>
                                                <td></td>
                                                <?php if ($is_admin) { ?>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary edit-btn" 
                                                    data-id=""
                                                    data-date="<?= $current_date ?>"
                                                    data-clockin=""
                                                    data-clockout="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                    <?php
                                                    } elseif (!$is_future_date) {
                                                        // Display Absent for past/current dates without records, but don't insert
                                                        $check_query = "SELECT id, status FROM newuser_attendance WHERE user_id = '$employee_id' AND DATE(clock_in_time) = '$current_date'";
                                                        $check_result = mysqli_query($conn, $check_query);
                                                        $absent_id = '';
                                                        if (mysqli_num_rows($check_result) > 0) {
                                                            $absent_row = mysqli_fetch_assoc($check_result);
                                                            $absent_id = $absent_row['id'];
                                                            if ($absent_row['status'] !== 'absent') {
                                                                continue;
                                                            }
                                                        }
                                    ?>
                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= $employee_name ?></td>
                                                <td><?= $current_date ?></td>
                                                <td colspan="4" class="text-center text-danger">Absent</td>
                                                <td></td>
                                                <?php if ($is_admin) { ?>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary edit-btn" 
                                                    data-id="<?= $absent_id ?>"
                                                    data-date="<?= $current_date ?>"
                                                    data-clockin=""
                                                    data-clockout="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($absent_id) { ?>
                                                    <button class="btn btn-sm btn-danger delete-btn" 
                                                            data-id="<?= $absent_id ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <?php } ?>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                    <?php
                                                    }
                                                }
                                            }
                                            echo "<script>document.getElementById('presentDaysCount').textContent = '$present_days';</script>";
                                        } else {
                                            echo '<tr><td colspan="9" class="text-center">You are not authorized to view this employee\'s attendance.</td></tr>';
                                        }
                                    } else {
                                        $current_date = date('Y-m-d');
                                        $attendance_query = "SELECT na.id, na.user_id, na.clock_in_time, na.clock_in_ip, 
                                            na.clock_out_time, na.clock_out_ip, na.status, na.created_at, na.updated_at,
                                            na.late_status, na.status_color, na.total_working_time, 
                                            na.extra_or_remaining_time, na.extra_or_remaining_label,
                                            CONCAT(he.fname, ' ', he.lname) as employee_name,
                                            he.doj
                                            FROM newuser_attendance na
                                            LEFT JOIN hrm_employee he ON na.user_id = he.id
                                            WHERE DATE(na.clock_in_time) = '$current_date'
                                            AND na.clock_in_time >= he.doj
                                            ORDER BY na.id DESC";

                                        $attendance_result = mysqli_query($conn, $attendance_query) or die(mysqli_error($conn));
                                        
                                        if (mysqli_num_rows($attendance_result) > 0) {
                                            $count = 1;
                                            while ($row = mysqli_fetch_assoc($attendance_result)) {
                                                list($date, $time) = explode(' ', $row['clock_in_time']);
                                                $clock_out_time = $row['clock_out_time'];
                                                $login_timestamp = strtotime($row['clock_in_time']);
                                                $logout_display = $clock_out_time ? date("h:i A", strtotime($clock_out_time)) : "N/A";
                                                $late_status = $row['late_status'];
                                                $status_color = $row['status_color'];
                                                $total_working_time = $row['total_working_time'];
                                                $extra_or_remaining_time = $row['extra_or_remaining_time'];
                                                $extra_or_remaining_label = $row['extra_or_remaining_label'];
                                    ?>
                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= $row['employee_name'] ?></td>
                                                <td><?= $date ?></td>
                                                <td><?= date("h:i A", $login_timestamp) ?></td>
                                                <td><?= $logout_display ?></td>
                                                <td><?= $total_working_time ?? 'N/A' ?></td>
                                                <td><?= $extra_or_remaining_label ? "$extra_or_remaining_label: $extra_or_remaining_time" : 'N/A' ?></td>
                                                <td style="color:<?= $status_color ?>"><?= $late_status ?></td>
                                                <?php if ($is_admin) { ?>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary edit-btn" 
                                                    data-id="<?= $row['id'] ?>"
                                                    data-date="<?= $date ?>"
                                                    data-clockin="<?= date('H:i', strtotime($row['clock_in_time'])) ?>"
                                                    data-clockout="<?= $clock_out_time ? date('H:i', strtotime($clock_out_time)) : '' ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger delete-btn" 
                                                            data-id="<?= $row['id'] ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                    <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="9" class="text-center">No attendance data found for today. Please use the form above to search for specific attendance data.</td></tr>';
                                        }
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editForm" method="post" action="update_attendance.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Attendance</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" id="editDate" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Clock In Time</label>
                            <input type="time" name="clock_in" id="editClockIn" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Clock Out Time</label>
                            <input type="time" name="clock_out" id="editClockOut" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]]
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const employeeSelect = document.getElementById('employee');
        const monthSelect = document.getElementById('month');
        const yearSelect = document.getElementById('year');

        function updateDateOptions() {
            const selectedEmployee = employeeSelect.options[employeeSelect.selectedIndex];
            const doj = selectedEmployee ? selectedEmployee.getAttribute('data-doj') : null;
            
            monthSelect.innerHTML = '<option value="">Select Month</option>';
            yearSelect.innerHTML = '<option value="">Select Year</option>';

            if (doj) {
                const dojDate = new Date(doj);
                const currentDate = new Date('2025-04-11');
                const dojYear = dojDate.getFullYear();
                const dojMonth = dojDate.getMonth() + 1;
                const currentYear = currentDate.getFullYear();

                for (let y = dojYear; y <= currentYear + 5; y++) {
                    const option = document.createElement('option');
                    option.value = y;
                    option.text = y;
                    yearSelect.appendChild(option);
                }

                const selectedYear = yearSelect.value || currentYear;
                for (let m = 1; m <= 12; m++) {
                    if (selectedYear > dojYear || (selectedYear == dojYear && m >= dojMonth)) {
                        const option = document.createElement('option');
                        option.value = m;
                        option.text = new Date(0, m - 1).toLocaleString('default', { month: 'long' });
                        monthSelect.appendChild(option);
                    }
                }

                if (selectedYear == currentYear) {
                    monthSelect.value = currentDate.getMonth() + 1;
                }
                yearSelect.value = currentYear;
            } else {
                const currentYear = new Date('2025-04-11').getFullYear();
                for (let m = 1; m <= 12; m++) {
                    const option = document.createElement('option');
                    option.value = m;
                    option.text = new Date(0, m - 1).toLocaleString('default', { month: 'long' });
                    monthSelect.appendChild(option);
                }
                for (let y = currentYear - 5; y <= currentYear + 5; y++) {
                    const option = document.createElement('option');
                    option.value = y;
                    option.text = y;
                    yearSelect.appendChild(option);
                }
                monthSelect.value = new Date('2025-04-11').getMonth() + 1;
                yearSelect.value = currentYear;
            }
        }

        updateDateOptions();

        employeeSelect.addEventListener('change', updateDateOptions);
        
        yearSelect.addEventListener('change', function() {
            const selectedEmployee = employeeSelect.options[employeeSelect.selectedIndex];
            const doj = selectedEmployee ? selectedEmployee.getAttribute('data-doj') : null;
            monthSelect.innerHTML = '<option value="">Select Month</option>';

            if (doj) {
                const dojDate = new Date(doj);
                const dojYear = dojDate.getFullYear();
                const dojMonth = dojDate.getMonth() + 1;
                const selectedYear = parseInt(this.value);

                for (let m = 1; m <= 12; m++) {
                    if (selectedYear > dojYear || (selectedYear == dojYear && m >= dojMonth)) {
                        const option = document.createElement('option');
                        option.value = m;
                        option.text = new Date(0, m - 1).toLocaleString('default', { month: 'long' });
                        monthSelect.appendChild(option);
                    }
                }
            } else {
                for (let m = 1; m <= 12; m++) {
                    const option = document.createElement('option');
                    option.value = m;
                    option.text = new Date(0, m - 1).toLocaleString('default', { month: 'long' });
                    monthSelect.appendChild(option);
                }
            }
        });
    });

    document.getElementById('exportButton').addEventListener('click', function() {
        const name = document.getElementById('name').innerText;
        const table = document.getElementById('attendanceTable');
        let csv = [];
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const cols = row.querySelectorAll('th, td');
            let rowCsv = [];
            cols.forEach(col => rowCsv.push('"' + col.innerText + '"'));
            csv.push(rowCsv.join(","));
        });

        csv.push('"Present Days","' + document.getElementById('presentDaysCount').textContent + '"');
        const csvBlob = new Blob([csv.join("\n")], { type: 'text/csv' });
        const url = URL.createObjectURL(csvBlob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${name}_attendance_report.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    document.getElementById('sendEmailButton').addEventListener('click', function() {
        const button = document.getElementById('sendEmailButton');
        const loadingIcon = document.getElementById('loadingIcon');
        button.disabled = true;
        loadingIcon.style.display = 'inline-block';

        const name = document.getElementById('name').innerText;
        const table = document.getElementById('attendanceTable');
        const email = document.getElementById('email').value;
        let csv = [];
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const data = Array.from(cols).map(col => col.innerText.replace(/,/g, ''));
            csv.push(data.join(','));
        });

        csv.push('Present Days,' + document.getElementById('presentDaysCount').textContent);
        const csvContent = csv.join('\n');
        const formData = new FormData();
        formData.append('csv', csvContent);
        formData.append('name', name);
        formData.append('email', email);

        fetch('email/send_attendance_email.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            button.disabled = false;
            loadingIcon.style.display = 'none';
            if (data.success) {
                alert('Email sent successfully!');
            } else {
                alert('Failed to send email: ' + data.error);
            }
        })
        .catch(error => {
            button.disabled = false;
            loadingIcon.style.display = 'none';
            alert('An error occurred: ' + error.message);
        });
    });

    $('.edit-btn').click(function() {
        var id = $(this).data('id');
        var date = $(this).data('date');
        var clockIn = $(this).data('clockin');
        var clockOut = $(this).data('clockout');

        $('#editId').val(id);
        $('#editDate').val(date);
        $('#editClockIn').val(clockIn);
        $('#editClockOut').val(clockOut);
        $('#editModal').modal('show');
    });

    $('.delete-btn').click(function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                type: 'POST',
                url: '',
                data: { delete_id: id },
                success: function() {
                    location.reload();
                }
            });
        }
    });
    </script>
</body>
</html>