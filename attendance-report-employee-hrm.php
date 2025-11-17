<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

// Get user name and other detail
$emp_id = $_SESSION['id'];
$conn = connect();
$query = "SELECT *, doj FROM hrm_employee WHERE id='$emp_id'";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);
$doj = $row['doj']; // Get date of joining

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];
?>

<head>
    <title>Reports - HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
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
                            <h3 class="page-title">Attendance Reports Employee</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Attendance Reports Employee</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Clock In/Out Buttons -->
                <!--<div class="row mb-4">-->
                <!--    <div class="col-md-12">-->
                <!--        <button id="clockInBtn" class="btn btn-success">Clock In</button>-->
                <!--        <button id="clockOutBtn" class="btn btn-danger">Clock Out</button>-->
                <!--    </div>-->
                <!--</div>-->

                <!-- Month Filter -->
                <div class="row mb-4">
                    <div class="">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <select name="month" id="month" class="form-control">
                                        <option value="">Select Month</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="year" id="year" class="form-control">
                                        <option value="">Select Year</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php
                        // Initialize counters
                        $present_days = 0;
                        $absent_days = 0;
                        $saturday_days = 0;
                        $sunday_days = 0;
                        $holiday_days = 0;
                        $leave_days = 0;

                        // Get selected month and year with doj consideration
                        $today = new DateTime(); // Use current date
                        $doj_date = new DateTime($doj);
                        $current_month = isset($_GET['month']) ? intval($_GET['month']) : $today->format('n');
                        $current_year = isset($_GET['year']) ? intval($_GET['year']) : $today->format('Y');
                        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);
                        $month_name = date('F', mktime(0, 0, 0, $current_month, 1));

                        // Fetch holidays for the selected year and convert date format
                        $holiday_query = "SELECT name, date FROM hrm_holidays WHERE year = '$current_year'";
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
                                      WHERE emp_id = '$emp_id' 
                                      AND status = 2 
                                      AND YEAR(start_date) = '$current_year' 
                                      AND MONTH(start_date) = '$current_month'";
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

                        // Fetch all attendance records for the month
                        $query = "SELECT * FROM newuser_attendance WHERE user_id=$emp_id 
                                AND MONTH(clock_in_time) = $current_month 
                                AND YEAR(clock_in_time) = $current_year 
                                AND clock_in_time >= '$doj'
                                ORDER BY clock_in_time ASC";
                        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

                        $attendance_records = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $attendance_records[date('Y-m-d', strtotime($row['clock_in_time']))] = $row;
                        }

                        // Fetch office timings
                        $office_timing_query = "SELECT login_time, relaxation_time, extra_fine_time, half_day_time, logout_time 
                                              FROM office_timing WHERE id=1 LIMIT 1";
                        $office_timing_result = mysqli_query($conn, $office_timing_query);
                        $office_timing_row = mysqli_fetch_assoc($office_timing_result);

                        // Check today's attendance status for button visibility
                        $today_date = $today->format('Y-m-d');
                        $has_clocked_in_today = isset($attendance_records[$today_date]) && !empty($attendance_records[$today_date]['clock_in_time']);
                        $has_clocked_out_today = $has_clocked_in_today && !empty($attendance_records[$today_date]['clock_out_time']);
                        ?>


<div class="row">
    <div class="col-md-4">
        <h4 class="m-3">
            Present: <span id="presentDaysCount"><?= $present_days ?></span> <br>
            Absent: <span id="absentDaysCount"><?= $absent_days ?></span> <br>
            Holidays: <span id="holidayDaysCount"><?= $holiday_days ?></span> <br> 
            Leaves: <span id="leaveDaysCount"><?= $leave_days ?></span>
        </h4>
    </div>
    <div class="col-md-4">
        <h4 class="text-center m-3"><?= "$month_name $current_year" ?></h4>
    </div>
    <div class="col-md-4">
        <h4 class="m-3" style="text-align: end;">
            Fine: <span id="saturdayDaysCount"><?= $saturday_days ?></span> <br>
            Late: <span id="holidayDaysCount"><?= $holiday_days ?></span> <br>
            Late Fine: <span id="sundayDaysCount"><?= $sunday_days ?></span> <br>
        </h4>
    </div>
</div>

                        <!-- <h4 class="text-center m-3">
                            Attendance Report for <?= "$month_name $current_year" ?><br>
                            Present: <span id="presentDaysCount"><?= $present_days ?></span> |
                            Absent: <span id="absentDaysCount"><?= $absent_days ?></span> |
                            Saturdays: <span id="saturdayDaysCount"><?= $saturday_days ?></span> |
                            Sundays: <span id="sundayDaysCount"><?= $sunday_days ?></span> |
                            Holidays: <span id="holidayDaysCount"><?= $holiday_days ?></span> |
                            Leaves: <span id="leaveDaysCount"><?= $leave_days ?></span>
                        </h4> -->

                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0" id="attendanceTable">
                                <thead>
                                    <tr>
                                        <!-- <th>#</th> -->
                                        <th>Date</th>
                                        <th>Clock In</th>
                                        <th>Clock Out</th>
                                        <th>Total Working Time</th>
                                        <th>Extra / Remaining Time</th>
                                        <th>Late</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;

                                    for ($day = 1; $day <= $days_in_month; $day++) {
                                        $current_date = sprintf("%d-%02d-%02d", $current_year, $current_month, $day);
                                        $date_obj = new DateTime($current_date);
                                        if ($date_obj < $doj_date) continue; // Skip dates before doj

                                        $day_of_week = $date_obj->format('N');
                                        $is_future_date = $date_obj > $today;

                                        if (isset($attendance_records[$current_date]) && $attendance_records[$current_date]['status'] !== 'absent') {
                                            $present_days++;
                                            $row = $attendance_records[$current_date];
                                            list($date, $time) = explode(' ', $row['clock_in_time']);
                                            $clock_out_time = $row['clock_out_time'];

                                            $login_timestamp = strtotime($row['clock_in_time']);
                                            $late_status = $row['late_status'];
                                            $late_color = $row['status_color'];

                                            if (empty($clock_out_time)) {
                                                $logout_display = "N/A";
                                                $total_working_time = "N/A";
                                                $extra_or_remaining_time = "N/A";
                                                $extra_or_remaining_label = "N/A";
                                            } else {
                                                $logout_timestamp = strtotime($clock_out_time);
                                                $total_working_seconds = $logout_timestamp - $login_timestamp;

                                                $office_start = strtotime($current_date . ' ' . ($office_timing_row['login_time'] ?? "09:00 AM"));
                                                $office_end = strtotime($current_date . ' ' . ($office_timing_row['logout_time'] ?? "06:30 PM"));
                                                $office_hours_seconds = $office_end - $office_start;

                                                $extra_or_remaining_seconds = $total_working_seconds - $office_hours_seconds;
                                                $total_working_time = gmdate("H:i:s", $total_working_seconds);
                                                $extra_or_remaining_time = gmdate("H:i:s", abs($extra_or_remaining_seconds));
                                                $extra_or_remaining_label = $extra_or_remaining_seconds > 0 ? 'Extra Time' : 'Remaining Time';
                                                $logout_display = date("h:i A", $logout_timestamp);
                                            }
                                    ?>
                                            <tr>
                                                <?php
                                                    $count++;
                                                ?>
                                                <!-- <td><?= $count++; ?></td> -->
                                                <td><?= $current_date ?></td>
                                                <td><?= date("h:i A", $login_timestamp) ?></td>
                                                <td><?= $logout_display ?></td>
                                                <td><?= $total_working_time ?? 'N/A' ?></td>
                                                <td><?= $extra_or_remaining_label . ': ' . ($extra_or_remaining_time ?? 'N/A') ?></td>
                                                <td style="color:<?= $late_color ?>"><?= $late_status ?></td>
                                            </tr>
                                    <?php
                                        } elseif (isset($holidays[$current_date])) {
                                            $holiday_days++;
                                    ?>
                                            <tr>
                                                <?php
                                                    $count++;
                                                ?>
                                                <!-- <td><?= $count++; ?></td> -->
                                                <td><?= $current_date ?></td>
                                                <td colspan="5" class="text-center text-success"><?= $holidays[$current_date] ?></td>
                                            </tr>
                                    <?php
                                        } elseif (isset($approved_leaves[$current_date])) {
                                            $leave_days++;
                                    ?>
                                            <tr>
                                                <?php
                                                    $count++;
                                                ?>
                                                <!-- <td><?= $count++; ?></td> -->
                                                <td><?= $current_date ?></td>
                                                <td colspan="5" class="text-center text-warning">Leave</td>
                                            </tr>
                                    <?php
                                        } elseif ($day_of_week == 6) {
                                            $saturday_days++;
                                    ?>
                                            <tr>
                                                <?php
                                                    $count++;
                                                ?>
                                                <!-- <td><?= $count++; ?></td> -->
                                                <td><?= $current_date ?></td>
                                                <td colspan="5" class="text-center text-primary">Saturday</td>
                                            </tr>
                                    <?php
                                        } elseif ($day_of_week == 7) {
                                            $sunday_days++;
                                    ?>
                                            <tr>
                                                <?php
                                                    $count++;
                                                ?>
                                                <!-- <td><?= $count++; ?></td> -->
                                                <td><?= $current_date ?></td>
                                                <td colspan="5" class="text-center text-primary">Sunday</td>
                                            </tr>
                                    <?php
                                        } elseif (!$is_future_date) {
                                            $absent_days++;
                                    ?>
                                            <tr>
                                                <?php
                                                    $count++;
                                                ?>
                                                <!-- <td><?= $count++; ?></td> -->
                                                <td><?= $current_date ?></td>
                                                <td colspan="5" class="text-center text-danger">Absent</td>
                                            </tr>
                                    <?php
                                        }
                                    }

                                    // Update counters in DOM
                                    echo "<script>
                                        document.getElementById('presentDaysCount').textContent = '$present_days';
                                        document.getElementById('absentDaysCount').textContent = '$absent_days';
                                        document.getElementById('saturdayDaysCount').textContent = '$saturday_days';
                                        document.getElementById('sundayDaysCount').textContent = '$sunday_days';
                                        document.getElementById('holidayDaysCount').textContent = '$holiday_days';
                                        document.getElementById('leaveDaysCount').textContent = '$leave_days';
                                    </script>";
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /Page Content -->
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- end main wrapper-->

    <?php include 'layouts/customizer.php'; ?>
    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable({
            "pageLength": 31,
            "order": [[1, "asc"]] // Sort by date
        });

        // Clock In/Out button visibility
        const clockInBtn = $('#clockInBtn');
        const clockOutBtn = $('#clockOutBtn');
        const hasClockedInToday = <?php echo $has_clocked_in_today ? 'true' : 'false'; ?>;
        const hasClockedOutToday = <?php echo $has_clocked_out_today ? 'true' : 'false'; ?>;

        if (hasClockedInToday && !hasClockedOutToday) {
            clockInBtn.hide();
            clockOutBtn.show();
        } else if (hasClockedOutToday) {
            clockInBtn.hide();
            clockOutBtn.hide();
        } else {
            clockInBtn.show();
            clockOutBtn.hide();
        }

        // Clock In
        clockInBtn.click(function() {
            $.ajax({
                url: 'newuser_attendance.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Clocked in successfully: ' + response.message);
                        clockInBtn.hide();
                        clockOutBtn.show();
                        location.reload(); // Refresh to update table
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error connecting to server.');
                }
            });
        });

        // Clock Out
        clockOutBtn.click(function() {
            if (confirm('Are you sure you want to clock out?')) {
                $.ajax({
                    url: 'newuser_attendance.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Clocked out successfully: ' + response.message);
                            clockInBtn.hide();
                            clockOutBtn.hide();
                            location.reload(); // Refresh to update table
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error connecting to server.');
                    }
                });
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const monthSelect = document.getElementById('month');
        const yearSelect = document.getElementById('year');
        const doj = '<?php echo $doj; ?>';
        const dojDate = new Date(doj);
        const currentDate = new Date();
        const dojYear = dojDate.getFullYear();
        const dojMonth = dojDate.getMonth() + 1; // JS months are 0-based
        const currentYear = currentDate.getFullYear();

        function updateDateOptions() {
            monthSelect.innerHTML = '<option value="">Select Month</option>';
            yearSelect.innerHTML = '<option value="">Select Year</option>';

            // Populate years from doj year to current year
            for (let y = dojYear; y <= currentYear; y++) {
                const option = document.createElement('option');
                option.value = y;
                option.text = y;
                yearSelect.appendChild(option);
            }

            // Populate months based on selected year and doj
            const selectedYear = yearSelect.value || currentYear;
            for (let m = 1; m <= 12; m++) {
                if (selectedYear > dojYear || (selectedYear == dojYear && m >= dojMonth)) {
                    const option = document.createElement('option');
                    option.value = m;
                    option.text = new Date(0, m - 1).toLocaleString('default', { month: 'long' });
                    monthSelect.appendChild(option);
                }
            }

            // Set default values
            if (selectedYear == currentYear) {
                monthSelect.value = currentDate.getMonth() + 1;
            }
            yearSelect.value = currentYear;

            // Preserve selected values from GET parameters if they exist and are valid
            const urlMonth = '<?php echo isset($_GET['month']) ? $_GET['month'] : ''; ?>';
            const urlYear = '<?php echo isset($_GET['year']) ? $_GET['year'] : ''; ?>';
            if (urlYear && urlYear >= dojYear) {
                yearSelect.value = urlYear;
                if (urlMonth && (urlYear > dojYear || (urlYear == dojYear && urlMonth >= dojMonth))) {
                    monthSelect.value = urlMonth;
                }
            }
        }

        // Initial population
        updateDateOptions();

        // Update months when year changes
        yearSelect.addEventListener('change', function() {
            monthSelect.innerHTML = '<option value="">Select Month</option>';
            const selectedYear = parseInt(this.value);

            for (let m = 1; m <= 12; m++) {
                if (selectedYear > dojYear || (selectedYear == dojYear && m >= dojMonth)) {
                    const option = document.createElement('option');
                    option.value = m;
                    option.text = new Date(0, m - 1).toLocaleString('default', { month: 'long' });
                    monthSelect.appendChild(option);
                }
            }

            // Set to current month if current year is selected
            if (selectedYear == currentYear) {
                monthSelect.value = currentDate.getMonth() + 1;
            }
        });
    });
    </script>

</body>
</html>