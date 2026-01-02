<?php

    include 'include/db.php';
    $conn=connect();


    $allemp = "SELECT * FROM hrm_employee WHERE status = 1 and archive_status =0 ";
    $empres = mysqli_query($conn, $allemp) or die(mysqli_error($conn));
    // mysqli_num_rows($empres);
    while ($row = mysqli_fetch_array($empres)) {

        if($row['role']=='super admin'){
            continue;
        }

        $emp_id = $row['id'];




        $query = "SELECT *, doj FROM hrm_employee WHERE id='$emp_id'";
        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
        $row = mysqli_fetch_array($result);
        $doj = $row['doj']; // Get date of joining
        
        
        $latefineque = "SELECT * FROM office_timing WHERE id = 1";
        $latefinequeres = mysqli_query($conn, $latefineque);
        $latefine = mysqli_fetch_assoc($latefinequeres);
        $normal_fine = $latefine['extra_fine'];
        

                                    // Initialize counters
                                    $present_days = 0;
                                    $absent_days = 0;
                                    $saturday_days = 0;
                                    $sunday_days = 0;
                                    $holiday_days = 0;
                                    $leave_days = 0;
        
                                    // Get selected month and year with doj consideration
                                    $doj_date = new DateTime($doj);
                                    // $today = new DateTime(); // Use current date
                                    // $current_month = $today->format('n');
                                    // $current_year = $today->format('Y');

$today = new DateTime('first day of last month');

$current_month = $today->format('n'); // month number (1–12)
$current_year  = $today->format('Y'); // year
$m_name    = $today->format('M'); // month name (optional)


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
                                
                                
                            
        
                                        $late = 0;
                                        $extra_late = 0;
                                        $halfd_late = 0;
                                        $extra_fine = 0;
                                        $total_late = 0;
                                        $fine_amount = 0;
                                        $totalwork_days = 0;
                                        $halfd_late_cover = 0;
                                        $halfd_late_not_cover = 0;
                                        $late_cover = 0;
                                        $late_not_cover = 0;
                                    
                                        $employee_id = $emp_id;
                                        
                                        $emp_query = "SELECT CONCAT(fname, ' ', lname) as employee_name FROM hrm_employee WHERE id = '$employee_id' AND id != 14";
                                        $emp_result = mysqli_query($conn, $emp_query);
                                        $emp_row = mysqli_fetch_assoc($emp_result);
                                        $employee_name = $emp_row['employee_name'] ?? '';


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



                                                        
                                                        if($late_status == "Late" AND ($extra_or_remaining_label == "Extra Time")){
                                                            $late_cover++;
                                                        }
                                                        if($late_status == "Late (Extra Late)" AND ($extra_or_remaining_label == "Extra Time")){
                                                            $late_cover++;
                                                        }                   
                                                        if($late_status == "Late (Extra Fine)" AND ($extra_or_remaining_label == "Extra Time")){
                                                            $late_cover++;
                                                        }     
                                                        
                                                        if($late_status == "Late" AND ($extra_or_remaining_label != "Extra Time")){
                                                            $late_not_cover++;
                                                        }
                                                        if($late_status == "Late (Extra Late)" AND ($extra_or_remaining_label != "Extra Time")){
                                                            $late_not_cover++;
                                                        }                   
                                                        if($late_status == "Late (Extra Fine)" AND ($extra_or_remaining_label != "Extra Time")){
                                                            $late_not_cover++;
                                                        }     
                                                        
                                                        
                                                        if($late_status == "Late (Half Day)" AND ($extra_or_remaining_label == "Extra Time")){
                                                            $halfd_late_cover++;
                                                        }
                                                        if($late_status == "Late (Half Day)" AND ($extra_or_remaining_label != "Extra Time")){
                                                            $halfd_late_not_cover++;
                                                        }

                                                        // echo $late_status;
                                                
                                                    } elseif (isset($holidays[$current_date])) {
                                                        $holiday_days++;
                                                
                                                    } elseif (isset($approved_leaves[$current_date])) {
                                                        $leave_days++;
                                                
                                                    } elseif ($day_of_week == 6) {
                                                        $saturday_days++;

                                                    } elseif ($day_of_week == 7) {
                                                        $sunday_days++;

                                                    } elseif (!$is_future_date) {
                                                        $absent_days++;

                                                    }
                                                }
        
                                                $totalwork_days = $present_days + $holiday_days + $leave_days + $absent_days;
                                                $total_late = $late + $extra_late + $extra_fine + $halfd_late;
                                                $totallateFine = $normal_fine * $total_late; 
        
                                          
                      
        echo "<br><br><hr><br><br>";
        echo "Emp id: ";
        echo $emp_id;
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
        echo "<br> Late Cover: ";
        echo $late_cover;
        echo "<br> Late Not Cover: ";
        echo $late_not_cover;
        echo "<br> Late (Extra Late): ";
        echo $extra_late; 
        echo "<br> Late (Extra Fine): ";
        echo $extra_fine; 
        echo "<br> Late (Half Day): ";
        echo $halfd_late;
        echo "<br> Late (Half Day) Cover: ";
        echo $halfd_late_cover;
        echo "<br> Late (Half Day) Not Cover: ";
        echo $halfd_late_not_cover;
        echo "<br> Late Cover: ";
        echo $late_cover;
        echo "<br> Late Not Cover: ";
        echo $late_not_cover;
        echo "<br>Total Lates: ";
        echo $total_late;
        echo "<br><br>";


        if($halfd_late){
            $attandance_point = 0;
        }elseif($late_not_cover){
            $attandance_point = 0;
        }else{
            $attandance_point = 60;

            $eom_batch = "EOM_".$m_name."_".$current_year;
            $eomq = "INSERT INTO eom (`eom_batch`, `emp_id`, `full_name`, `month`, `year`, `attandance_point`, `emp_point`, `hr_point`, `sadmin_point`) 
            VALUES ('$eom_batch', '$emp_id', '$employee_name', '$month_name', '$current_year', '$attandance_point', '0', '0', '0')";

            $result = mysqli_query($conn, $eomq);

            if ($result) {
                echo "✅ EOM data saved successfully";
            } else {
                echo "❌ Error: " . mysqli_error($conn);
            }
        }


    }


    
?>
