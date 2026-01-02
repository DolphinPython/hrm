<?php include 'layouts/session.php'; ?>

<?php 

include 'include/db.php';

// Get user name and other detail
$emp_id = $_SESSION['id'];
$conn = connect();






$query = "SELECT *, doj FROM hrm_employee WHERE id='$emp_id'";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);
$doj = $row['doj']; // Get date of joining


$latefineque = "SELECT * FROM office_timing WHERE id = 1";
$latefinequeres = mysqli_query($conn, $latefineque);
$latefine = mysqli_fetch_assoc($latefinequeres);
$normal_fine = $latefine['extra_fine'];

?>








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











                            <?php

                                $late = 0;
                                $extra_late = 0;
                                $halfd_late = 0;
                                $extra_fine = 0;
                                $total_late = 0;
                                $fine_amount = 0;
                                $totalwork_days = 0;
                            
                                $employee_id = $emp_id;
                                
                                $emp_query = "SELECT CONCAT(fname, ' ', lname) as employee_name FROM hrm_employee WHERE id = '$employee_id' AND id != 14";
                                $emp_result = mysqli_query($conn, $emp_query);
                                $emp_row = mysqli_fetch_assoc($emp_result);
                                $employee_name = $emp_row['employee_name'] ?? '';
                            ?>









                <!-- Full Details Start -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table mb-0" id="attendanceTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
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
                                            if ($date_obj < $doj_date)
                                                continue; // Skip dates before doj
                                        
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
                                                    $office_end = strtotime($current_date . ' ' . ($office_timing_row['logout_time'] ?? "06:00 PM"));
                                                    $office_hours_seconds = $office_end - $office_start;

                                                    $extra_or_remaining_seconds = $total_working_seconds - $office_hours_seconds;
                                                    $total_working_time = gmdate("H:i:s", $total_working_seconds);
                                                    $extra_or_remaining_time = gmdate("H:i:s", abs($extra_or_remaining_seconds));
                                                    $extra_or_remaining_label = $extra_or_remaining_seconds > 0 ? 'Extra Time' : 'Remaining Time';
                                                    $logout_display = date("h:i A", $logout_timestamp);
                                                }
                                                ?>
                                        <tr>
                                            <td><?= $count++; ?></td>
                                            <td><?= $current_date ?></td>
                                            <td><?= date("h:i A", $login_timestamp) ?></td>
                                            <td><?= $logout_display ?></td>
                                            <td><?= $total_working_time ?? 'N/A' ?></td>
                                            <td><?= $extra_or_remaining_label . ': ' . ($extra_or_remaining_time ?? 'N/A') ?>
                                            </td>
                                            <td style="color:<?= $late_color ?>">
                                                <?php
                                                if ($late_status == "Late") {
                                                    $late++;
                                                }
                                                if ($late_status == "Late (Extra Late)") {
                                                    $extra_late++;
                                                }
                                                if($late_status == "Late (Extra Fine)") {
                                                    $extra_fine++;
                                                }
                                                if($late_status == "Late (Half Day)") {
                                                    $halfd_late++;
                                                }
                                                echo $late_status;
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                            } elseif (isset($holidays[$current_date])) {
                                                $holiday_days++;
                                                ?>
                                        <tr>
                                            <td><?= $count++; ?></td>
                                            <td><?= $current_date ?></td>
                                            <td colspan="5" class="text-center text-success"><?= $holidays[$current_date] ?>
                                            </td>
                                        </tr>
                                        <?php
                                            } elseif (isset($approved_leaves[$current_date])) {
                                                $leave_days++;
                                                ?>
                                        <tr>
                                            <td><?= $count++; ?></td>
                                            <td><?= $current_date ?></td>
                                            <td colspan="5" class="text-center text-warning">Leave</td>
                                        </tr>
                                        <?php
                                            } elseif ($day_of_week == 6) {
                                                $saturday_days++;
                                                ?>
                                        <tr>
                                            <td><?= $count++; ?></td>
                                            <td><?= $current_date ?></td>
                                            <td colspan="5" class="text-center text-primary">Saturday</td>
                                        </tr>
                                        <?php
                                            } elseif ($day_of_week == 7) {
                                                $sunday_days++;
                                                ?>
                                        <tr>
                                            <td><?= $count++; ?></td>
                                            <td><?= $current_date ?></td>
                                            <td colspan="5" class="text-center text-primary">Sunday</td>
                                        </tr>
                                        <?php
                                            } elseif (!$is_future_date) {
                                                $absent_days++;
                                                ?>
                                        <tr>
                                            <td><?= $count++; ?></td>
                                            <td><?= $current_date ?></td>
                                            <td colspan="5" class="text-center text-danger">Absent</td>
                                        </tr>
                                        <?php
                                            }
                                        }

                                        $totalwork_days = $present_days + $holiday_days + $leave_days + $absent_days;
                                        $total_late = $late + $extra_late + $extra_fine + $halfd_late;
                                        $totallateFine = $normal_fine * $total_late; 

                                        // Update counters in DOM
                                        echo "<script>                                                    
                                            document.getElementById('totalworkingdays').textContent = '$totalwork_days';
                                            document.getElementById('totallateCount').textContent = '$total_late';
                                            document.getElementById('totallateFine').textContent = '$totallateFine';
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

                <!-- Full Details End -->


<?php
echo "<br>Name: ";
echo $employee_name;
echo "<br>Month: ";
echo $month_name." ".$current_year;
echo "<br>present: ";
echo $present_days;
echo "<br> Total Working Days: ";
echo $totalwork_days;
echo "<br> absent: ";
echo $absent_days;
echo "<br> Saturday: ";
echo $saturday_days;
echo "<br> sunday: ";
echo $sunday_days;
echo "<br> holiday: ";
echo $holiday_days;
echo "<br> leaves: ";
echo $leave_days;
echo "<br> Late: ";
echo $late;
echo "<br> Late (Extra Late): ";
echo $extra_late; 
echo "<br> Late (Extra Fine): ";
echo $extra_fine; 
echo "<br> Late (Half Day): ";
echo $halfd_late;
echo "<br>Total Lates: ";
echo $total_late;
?>


<!-- <h6 class="" title="Late Fine Only">Late Fine: <?= $fine_amount ?>/-</h6> -->


