<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';

$conn = connect();
$emp_id = $_SESSION['id'];     
$user_id = $_GET['id'];

// Fetch all employees for the dropdown
$employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name FROM hrm_employee ";
$employee_result = mysqli_query($conn, $employee_query) or die(mysqli_error($conn));

$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];
$employee_detail = "SELECT * FROM hrm_employee WHERE id=$user_id";

$employee_result = mysqli_query($conn, $employee_detail) or die(mysqli_error($conn));
$employee_row = mysqli_fetch_assoc($employee_result);

$salary = $employee_row['salary'];
$email = $employee_row['email'];

// Get month and year from GET parameters
$month = isset($_GET['month']) ? (int)$_GET['month'] : 0;
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;

// Fetch Saturday option from office_timing table
// $saturday_option_query = "SELECT saturday_option FROM office_timing WHERE id = 1";
// $saturday_option_result = mysqli_query($conn, $saturday_option_query);

// if ($saturday_option_result && mysqli_num_rows($saturday_option_result) > 0) {
//     $saturday_option_row = mysqli_fetch_assoc($saturday_option_result);
//     $saturday_option = $saturday_option_row['saturday_option']; // Fetch Saturday option
// } else {
//     $saturday_option = 'all-on'; // Default value if no record found
// }

// Query to count holidays
// $countQuery = "
//     SELECT COUNT(*) AS total_holidays
//     FROM hrm_holidays
//     WHERE year = $year
//       AND MONTH(STR_TO_DATE(date, '%Y-%m-%d')) = $month
// ";

// // Execute the count query
// $countResult = mysqli_query($conn, $countQuery);
// if ($countResult) {
//     $countRow = mysqli_fetch_assoc($countResult);
//     $totalHolidays = $countRow['total_holidays'];
// } else {
//     echo "Error: " . mysqli_error($conn);
//     exit;
// }









































// Tempoary Section Start Here

// Tempoary Section End Here














































































// Fetch Saturday option from office_timing table
$saturday_option_query = "SELECT saturday_option FROM office_timing WHERE id = 1";
$saturday_option_result = mysqli_query($conn, $saturday_option_query);

if ($saturday_option_result && mysqli_num_rows($saturday_option_result) > 0) {
    $saturday_option_row = mysqli_fetch_assoc($saturday_option_result);
    $saturday_option = $saturday_option_row['saturday_option']; // Fetch Saturday option
} else {
    $saturday_option = 'all-on'; // Default value if no record found
}

// Query to fetch holidays
$holidayQuery = "
    SELECT date
    FROM hrm_holidays
    WHERE year = $year
      AND MONTH(STR_TO_DATE(date, '%Y-%m-%d')) = $month
";
$holidayResult = mysqli_query($conn, $holidayQuery);

$totalHolidays = 0;

if ($holidayResult) {
    while ($holidayRow = mysqli_fetch_assoc($holidayResult)) {
        $holidayDate = new DateTime($holidayRow['date']);
        $dayOfWeek = $holidayDate->format('N'); // 1 (Monday) to 7 (Sunday)
        $dayOfMonth = $holidayDate->format('j'); // Day of the month (1 to 31)

        // Check if the holiday is on Sunday
        if ($dayOfWeek == 7) {
            continue; // Skip Sundays (always non-working day)
        }

        // Check if the holiday is on Saturday
        if ($dayOfWeek == 6) { // Saturday
            if ($saturday_option == 'all-off') {
                continue; // Skip if all Saturdays are non-working days
            } elseif ($saturday_option == '1st-3rd-on') {
                // Check if it's the 1st or 3rd Saturday
                $weekOfMonth = ceil($dayOfMonth / 7);
                if ($weekOfMonth != 1 && $weekOfMonth != 3) {
                    continue; // Skip if it's not the 1st or 3rd Saturday
                }
            }
            // For 'all-on', count Saturday as a holiday
        }

        // If the holiday is on a working day, count it as a holiday
        $totalHolidays++;
    }
} else {
    echo "Error: " . mysqli_error($conn);
    exit;
}
if ($month > 0 && $year > 0) {
    // Set the first and last date of the month
    $firstDay = new DateTime("$year-$month-01");
    $lastDay = new DateTime("$year-$month-01");
    $lastDay->modify('last day of this month');

    $totalDays = 0;

    // Loop through each day of the month
    while ($firstDay <= $lastDay) {
        $dayOfWeek = $firstDay->format('N'); // 1 (Monday) to 7 (Sunday)
        $dayOfMonth = $firstDay->format('j'); // Day of the month (1 to 31)

        // Check if the current day is not a Sunday
        if ($dayOfWeek < 7) { // Monday to Saturday
            // Check Saturday option
            if ($dayOfWeek == 6) { // Saturday
                if ($saturday_option == 'all-on') {
                    $totalDays++; // All Saturdays are working days
                } elseif ($saturday_option == '1st-3rd-on') {
                    // Check if it's the 1st or 3rd Saturday
                    $weekOfMonth = ceil($dayOfMonth / 7);
                    if ($weekOfMonth == 1 || $weekOfMonth == 3) {
                        $totalDays++; // Only 1st and 3rd Saturdays are working days
                    }
                }
                // For 'all-off', Saturdays are not counted
            } else {
                $totalDays++; // Monday to Friday are always working days
            }
        }

        // Move to the next day
        $firstDay->modify('+1 day');
    }
} else {
    echo "Please provide a valid month and year.";
}

$totalWorkingDays = $totalDays;
$perDaySalary = round($salary / $totalWorkingDays,2);


// Define your query with placeholders
$emp_id = (int) $_GET['id'];  // Or whatever method you're using to get emp_id
$month = (int) $_GET['month'];    // Month should be between 1-12
$year = (int) $_GET['year'];      // Year should be valid


// $attendance_query = "
//     SELECT * 
//     FROM newuser_attendance 
//     WHERE user_id = $emp_id 
//     AND MONTH(clock_in_time) = $month 
//     AND YEAR(clock_in_time) = $year
// ";
// $attendance_query = "
//     SELECT * 
//     FROM newuser_attendance 
//     WHERE user_id = $emp_id 
//     AND MONTH(clock_in_time) = $month 
//     AND YEAR(clock_in_time) = $year
//     AND clock_in_ip IS NOT NULL 
//     AND clock_in_ip <> ''
// ";
$attendance_query = "
    SELECT * 
    FROM newuser_attendance 
    WHERE user_id = $emp_id 
    AND MONTH(clock_in_time) = $month 
    AND YEAR(clock_in_time) = $year
    AND TIME(clock_in_time) > '00:00:00'
";


// Execute the query
$attendance_result = mysqli_query($conn, $attendance_query);

if (!$attendance_result) {
    die("Error executing query: " . mysqli_error($conn));
}

$presentDays = mysqli_num_rows($attendance_result) + $totalHolidays + 1;


// Use parameterized queries to avoid SQL injection
$starting_late_query = "
    SELECT 
        COUNT(CASE WHEN late_status = 'Late' AND status_color = 'orange' THEN 1 END) AS normal_late,
        COUNT(CASE WHEN late_status = 'Late' AND status_color = 'red' THEN 1 END) AS late_extra
    FROM (
        SELECT * 
        FROM newuser_attendance 
        WHERE user_id = ? 
        AND MONTH(clock_in_time) = ? 
        AND YEAR(clock_in_time) = ? 
        AND late_status NOT IN ('On Time', 'Half Day') 
        ORDER BY clock_in_time ASC
        LIMIT 3
    ) AS recent_attendance
";


// Prepare the first query
$stmt = $conn->prepare($starting_late_query);
$stmt->bind_param('iii', $emp_id, $month, $year);
$stmt->execute();
$starting_late_result = $stmt->get_result();
$starting_late_data = $starting_late_result->fetch_assoc();

$normal_late_starting = $starting_late_data['normal_late'];
$late_extra_starting = $starting_late_data['late_extra'];


// Second query for all late data
$all_late_query = "
    SELECT 
        COUNT(CASE WHEN late_status = 'Late' AND status_color = 'orange' THEN 1 END) AS normal_late,
        COUNT(CASE WHEN late_status = 'Late' AND status_color = 'red' THEN 1 END) AS late_extra,
        COUNT(CASE WHEN late_status = 'Half Day' THEN 1 END) AS half_day
    FROM newuser_attendance 
    WHERE user_id = ? 
    AND MONTH(clock_in_time) = ? 
    AND YEAR(clock_in_time) = ? 
    AND late_status != 'On Time'
";

// Prepare the second query
$stmt = $conn->prepare($all_late_query);
$stmt->bind_param('iii', $emp_id, $month, $year);
$stmt->execute();
$all_late_result = $stmt->get_result();
$all_late_data = $all_late_result->fetch_assoc();

$normal_late_all = $all_late_data['normal_late'];
$late_extra_all = $all_late_data['late_extra'];
$half_day_all = $all_late_data['half_day'];

// Calculate the differences
$normal_late = $normal_late_all ;
$late_extra = $late_extra_all - $late_extra_starting;


// Close the statement
$stmt->close();


$fine = "Select * from office_timing where id=1";
$fine_result = mysqli_query($conn, $fine);

$row_fine = mysqli_fetch_array($fine_result);

$normal_fine = $row_fine['normal_fine'];
$relaxation_late = $row_fine['relaxation_late'];
$monthly_shorts = $row_fine['monthly_shorts'];
$monthly_half = $row_fine['monthly_half'];
$monthly_leaves = $row_fine['monthly_leaves'];
$extra_fine = $row_fine['extra_fine'];

// $half_day_fine = $perDaySalary / 2;
$half_day_fine = round($perDaySalary / 2,2);

// echo $leave_days_query = "
//     SELECT 
//         SUM(no_of_days) AS total_leave_days
//     FROM hrm_leave_applied
//     WHERE emp_id = ? 
//     AND YEAR(start_date) = ? 
//     AND MONTH(start_date) = ?
// ";

// echo "<br>";
$leave_days_query = "SELECT * FROM hrm_leave_applied WHERE emp_id = ? AND YEAR(start_date) = ? AND MONTH(start_date) = ?";
// echo "<br>".$emp_id." : ".$year." : ".$month."<br>";
// exit();

// Prepare the leave days query
$stmt = $conn->prepare($leave_days_query);

$stmt->bind_param('iii', $emp_id, $year, $month);
$stmt->execute();
$leave_days_result = $stmt->get_result();    

// $leave_days_data = $leave_days_result->fetch_assoc();
// $leave_days = $leave_days_data['total_leave_days'] - 1;


// echo "<br><br>";

$leavehalf = 0;
$leavehalfc = 0;
$leavefull = 0;
$leavefullc = 0;
$leaveshort = 0;
$leaveshortc = 0;

while ($row = $leave_days_result->fetch_assoc()) {
    // Half Day
    if ($row['day_type'] == 1 && $row['status'] == 2) {
        $leavehalf += $row['no_of_days'];
        $leavehalfc++;
    // Full Day
    } else if ($row['day_type'] == 2 && $row['status'] == 2) {
        $leavefull += $row['no_of_days'];
        $leavefullc++;
    // Short Day
    } else if ($row['day_type'] == 3 && $row['status'] == 2) {        
        $leaveshort += $row['no_of_days'];
        $leaveshortc++;
    }
}
// exit();


function calculateSalary($salary, $totalDays, $presentDays, $half_day_all, $normal_late, $late_extra, $normal_fine, $extra_fine, $half_day_fine, $perDaySalary){

    if ($presentDays > $totalDays) {
        $presentDays = $totalDays;
    }


    $absent_day = $totalDays - $presentDays;
    // $absent_days = $totalDays - 22;


    $salary_deductions = ($normal_late * $normal_fine) + ($late_extra * $extra_fine) + ($half_day_all * $half_day_fine) + ($absent_day * $perDaySalary);
    $salary_after_deductions = $salary - $salary_deductions;

    return $salary_after_deductions;
}

$after_deduction = calculateSalary($salary, $totalDays, $presentDays, $half_day_all, $normal_late, $late_extra, $normal_fine, $extra_fine, $half_day_fine, $perDaySalary);

function numberToWords($num){
    $belowTwenty = [
        'Zero',
        'One',
        'Two',
        'Three',
        'Four',
        'Five',
        'Six',
        'Seven',
        'Eight',
        'Nine',
        'Ten',
        'Eleven',
        'Twelve',
        'Thirteen',
        'Fourteen',
        'Fifteen',
        'Sixteen',
        'Seventeen',
        'Eighteen',
        'Nineteen'
    ];
    $tens = [
        '',
        '',
        'Twenty',
        'Thirty',
        'Forty',
        'Fifty',
        'Sixty',
        'Seventy',
        'Eighty',
        'Ninety'
    ];
    $aboveThousand = ['', 'Thousand', 'Million', 'Billion'];

    if ($num == 0) return 'Zero';

    $result = '';

    function helper($n, $belowTwenty, $tens)
    {
        if ($n < 20) return $belowTwenty[$n];
        else if ($n < 100) return $tens[intval($n / 10)] . ($n % 10 ? ' ' . $belowTwenty[$n % 10] : '');
        else if ($n < 1000) return $belowTwenty[intval($n / 100)] . ' Hundred' . ($n % 100 ? ' ' . helper($n % 100, $belowTwenty, $tens) : '');
        return '';
    }

    $i = 0;
    while ($num > 0) {
        if ($num % 1000 != 0) {
            $result = helper($num % 1000, $belowTwenty, $tens) . ($aboveThousand[$i] ? ' ' . $aboveThousand[$i] : '') . ' ' . $result;
        }
        $num = intval($num / 1000);
        $i++;
    }

    return trim($result);
}

// Function to round off and convert to words
function roundAndConvertToWords($decimal)
{
    $roundedNumber = round($decimal); // Round off the number
    return numberToWords($roundedNumber); // Convert to words
}
?>

<head>
    <title>Calculate Salary - HRMS</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/pay-slip.css">
    <!-- Include jsPDF and html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <style>
    .salary_in_word,
    .salary-structure,
    .employee-details {
        font-size: 25px;
    }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">





























































                <!-- Tempoary -->

                <?php


$conn = connect();
$emp_id = $_SESSION['id'];
$role_query = "SELECT role FROM hrm_employee WHERE id = '$emp_id'";
$role_result = mysqli_query($conn, $role_query) or die(mysqli_error($conn));
$role_row = mysqli_fetch_assoc($role_result);
$is_admin = ($role_row && in_array(strtolower($role_row['role']), ['admin', 'super admin']));
if ($is_admin) {
    $employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name, doj FROM hrm_employee WHERE id != 14 AND archive_status != 1";
} else {
    $employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name, doj FROM hrm_employee WHERE id = '$emp_id' AND archive_status != 1 AND id != 14 
                       UNION 
                       SELECT he.id, CONCAT(he.fname, ' ', he.lname) AS name, he.doj 
                       FROM hrm_employee he 
                       INNER JOIN hrm_reporting_manager hrm ON he.id = hrm.employee_id 
                       WHERE hrm.reporting_manager_id = '$emp_id' AND he.id != 14";
}
$employee_result = mysqli_query($conn, $employee_query) or die(mysqli_error($conn));


// Fetch employees on leave for current date
$today = new DateTime(); // Use actual current date
$current_date = $today->format('Y-m-d');
$leave_query = "SELECT he.id, CONCAT(he.fname, ' ', he.lname) AS name, hla.start_date, hla.end_date
                FROM hrm_employee he
                INNER JOIN hrm_leave_applied hla ON he.id = hla.emp_id
                WHERE hla.status = 2 
                AND '$current_date' BETWEEN hla.start_date AND hla.end_date";
$leave_result = mysqli_query($conn, $leave_query);
$employees_on_leave = [];
while ($leave = mysqli_fetch_assoc($leave_result)) {
    $employees_on_leave[] = $leave;
}

// Fetch employees absent for current date (no clock-in and not on leave), excluding emp_id = 14
$absent_query = "SELECT he.id, CONCAT(he.fname, ' ', he.lname) AS name
                 FROM hrm_employee he
                 LEFT JOIN newuser_attendance na ON he.id = na.user_id 
                     AND DATE(na.clock_in_time) = '$current_date'
                 LEFT JOIN hrm_leave_applied hla ON he.id = hla.emp_id 
                     AND hla.status = 2 
                     AND '$current_date' BETWEEN hla.start_date AND hla.end_date
                 WHERE he.status = 1
                 AND he.id != 14
                 AND hla.id IS NULL
                 AND archive_status = 0
                 AND (na.id IS NULL OR na.status = 'absent')";
$absent_result = mysqli_query($conn, $absent_query);
$employees_absent = [];
while ($absent = mysqli_fetch_assoc($absent_result)) {
    $employees_absent[] = $absent;
}
?>

                <!-- Temp Section Start -->

                <!-- Temp -->

                <div class="row">
                    <div class="col-md-12">
                        <form id="salaryForm" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="employee">Select Employee</label>
                                    <select name="employee_id" id="employee" class="form-control">
                                        <option value="">Select Employee</option>
                                        <?php
                                        mysqli_data_seek($employee_result, 0);
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

                        <script>
                        document.getElementById("salaryForm").addEventListener("submit", function(e) {
                            e.preventDefault(); // form ke default submit ko rokna

                            const empId = document.getElementById("employee").value;
                            const month = document.getElementById("month").value;
                            const year = document.getElementById("year").value;

                            if (!empId || !month || !year) {
                                alert("Please select Employee, Month and Year");
                                return;
                            }

                            // Redirect to required URL
                            window.location.href =
                                `calculate-salary.php?employee_id=${empId}&id=${empId}&month=${month}&year=${year}`;
                        });
                        </script>

                    </div>
                </div>



                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                    const employeeSelect = document.getElementById('employee');
                    const monthSelect = document.getElementById('month');
                    const yearSelect = document.getElementById('year');

                    function updateDateOptions() {
                        const selectedEmployee = employeeSelect.options[employeeSelect.selectedIndex];
                        const doj = selectedEmployee ? selectedEmployee.getAttribute('data-doj') : null;

                        monthSelect.innerHTML = '<option value="">Select Month</option>';
                        yearSelect.innerHTML = '<option value="">Select Year</option>';

                        const currentDate = new Date();
                        let currentYear = currentDate.getFullYear();
                        let currentMonth = currentDate.getMonth() + 1;

                        // 👉 Last month calculate
                        let lastMonth = currentMonth - 1;
                        let lastMonthYear = currentYear;

                        if (lastMonth === 0) {
                            lastMonth = 12;
                            lastMonthYear = currentYear - 1;
                        }

                        if (doj) {
                            const dojDate = new Date(doj);
                            const dojYear = dojDate.getFullYear();
                            const dojMonth = dojDate.getMonth() + 1;

                            // Year loop only till current year (no future year)
                            for (let y = dojYear; y <= currentYear; y++) {
                                const option = document.createElement('option');
                                option.value = y;
                                option.text = y;
                                yearSelect.appendChild(option);
                            }

                            // Default year = last month ka year
                            yearSelect.value = lastMonthYear;

                            // Month loop
                            for (let m = 1; m <= 12; m++) {
                                if (lastMonthYear > dojYear || (lastMonthYear == dojYear && m >= dojMonth)) {
                                    const option = document.createElement('option');
                                    option.value = m;
                                    option.text = new Date(0, m - 1).toLocaleString('default', {
                                        month: 'long'
                                    });
                                    monthSelect.appendChild(option);
                                }
                            }

                            // Default month = last month
                            monthSelect.value = lastMonth;

                        } else {
                            // Agar DOJ nahi hai to normal range
                            for (let y = currentYear - 1; y <= currentYear; y++) {
                                const option = document.createElement('option');
                                option.value = y;
                                option.text = y;
                                yearSelect.appendChild(option);
                            }

                            yearSelect.value = lastMonthYear;

                            for (let m = 1; m <= 12; m++) {
                                const option = document.createElement('option');
                                option.value = m;
                                option.text = new Date(0, m - 1).toLocaleString('default', {
                                    month: 'long'
                                });
                                monthSelect.appendChild(option);
                            }

                            monthSelect.value = lastMonth;
                        }
                    }

                    updateDateOptions();

                    employeeSelect.addEventListener('change', updateDateOptions);

                    yearSelect.addEventListener('change', function() {
                        const selectedEmployee = employeeSelect.options[employeeSelect.selectedIndex];
                        const doj = selectedEmployee ? selectedEmployee.getAttribute('data-doj') : null;
                        monthSelect.innerHTML = '<option value="">Select Month</option>';

                        const selectedYear = parseInt(this.value);
                        const currentDate = new Date();
                        const currentYear = currentDate.getFullYear();
                        const currentMonth = currentDate.getMonth() + 1;

                        let lastMonth = currentMonth - 1;
                        let lastMonthYear = currentYear;
                        if (lastMonth === 0) {
                            lastMonth = 12;
                            lastMonthYear = currentYear - 1;
                        }

                        if (doj) {
                            const dojDate = new Date(doj);
                            const dojYear = dojDate.getFullYear();
                            const dojMonth = dojDate.getMonth() + 1;

                            for (let m = 1; m <= 12; m++) {
                                if (selectedYear > dojYear || (selectedYear == dojYear && m >=
                                        dojMonth)) {
                                    const option = document.createElement('option');
                                    option.value = m;
                                    option.text = new Date(0, m - 1).toLocaleString('default', {
                                        month: 'long'
                                    });
                                    monthSelect.appendChild(option);
                                }
                            }
                        } else {
                            for (let m = 1; m <= 12; m++) {
                                const option = document.createElement('option');
                                option.value = m;
                                option.text = new Date(0, m - 1).toLocaleString('default', {
                                    month: 'long'
                                });
                                monthSelect.appendChild(option);
                            }
                        }

                        // Default month = last month (agar current year hi selected hai)
                        if (selectedYear === lastMonthYear) {
                            monthSelect.value = lastMonth;
                        }
                    });
                });
                </script>
                <!-- Temp  -->









                
                <!-- Attendance Table -->
                        <?php

                            echo $leaveshort;
                            echo " |S: ";
                            echo $leaveshortc;
                            $total_short_day = $leaveshortc;
                            echo "<br>";
                            echo $leavehalf;
                            echo " |H: ";
                            echo $leavehalfc;
                            echo "<br>";
                            echo $leavefull;
                            echo " |C: ";
                            echo $leavefullc;
                            echo "<br>";
                            // echo $leavefulld;
                            // echo " |L: ";
                            // echo $leavefulldc;

                            
                            $present_days = 0;
                            $saturday_days = 0;
                            $sunday_days = 0;
                            $holiday_days = 0;
                            $leave_days = 0;
                            $absent_days = 0;
                            $totalwork_days = 0;
                            $late = 0;
                            $extra_late = 0;
                            $extra_fine = 0;
                            $total_late = 0;
                            $late_cover = 0;
                            $total_half_day = 0;
                            $total_half_day_late = 0;
                            


                            if (
                                isset($_GET['employee_id'], $_GET['month'], $_GET['year']) &&
                                !empty($_GET['employee_id']) &&
                                !empty($_GET['month']) &&
                                !empty($_GET['year'])
                            ) {
                                $employee_id = $_GET['employee_id'];
                                $month = $_GET['month'];
                                $year = $_GET['year'];

                                $emp_query = "SELECT CONCAT(fname, ' ', lname) as employee_name FROM hrm_employee WHERE id = '$employee_id' AND id != 14";
                                $emp_result = mysqli_query($conn, $emp_query);
                                $emp_row = mysqli_fetch_assoc($emp_result);
                                $employee_name = $emp_row['employee_name'] ?? '';
                                }


                                    if (
                                        isset($_GET['employee_id'], $_GET['month'], $_GET['year']) &&
                                        !empty($_GET['employee_id']) &&
                                        !empty($_GET['month']) &&
                                        !empty($_GET['year'])
                                    ) {
                                        $employee_id = $_GET['employee_id'];
                                        $month = $_GET['month'];
                                        $year = $_GET['year'];

                                        $manager_check_query = "SELECT * FROM hrm_reporting_manager WHERE employee_id = '$employee_id' AND reporting_manager_id = '{$_SESSION['id']}'";
                                        $manager_check_result = mysqli_query($conn, $manager_check_query);
                                        $is_manager = mysqli_num_rows($manager_check_result) > 0;

                                        if (($is_admin || $is_manager || $employee_id == $_SESSION['id']) && $employee_id != 14) {
                                            $emp_query = "SELECT CONCAT(fname, ' ', lname) as employee_name, doj 
                                                         FROM hrm_employee 
                                                         WHERE id = '$employee_id' AND id != 14";
                                            $emp_result = mysqli_query($conn, $emp_query);
                                            $emp_row = mysqli_fetch_assoc($emp_result);
                                            $employee_name = $emp_row['employee_name'] ?? '';
                                            $doj = $emp_row['doj'] ?? '';

                                            if (empty($employee_name)) {
                                                echo '<tr><td colspan="9" class="text-center">No data available for this employee.</td></tr>';
                                            } else {
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
                                                $leave_query = "SELECT start_date, end_date ,status 
                                                              FROM hrm_leave_applied 
                                                              WHERE emp_id = '$employee_id' 
                                                              AND status = 2 
                                                              AND status = 3
                                                              AND status = 4
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

                                                $attendance_query = "SELECT * FROM newuser_attendance 
                                                    WHERE user_id = '$employee_id' 
                                                    AND MONTH(clock_in_time) = '$month' 
                                                    AND YEAR(clock_in_time) = '$year'
                                                    AND clock_in_time >= '$doj'
                                                    ORDER BY clock_in_time ASC";

                                                $attendance_result = mysqli_query($conn, $attendance_query) or die(mysqli_error($conn));

                                                $attendance_records = [];
                                                while ($row = mysqli_fetch_assoc($attendance_result)) {
                                                    $attendance_records[date('Y-m-d', strtotime($row['clock_in_time']))] = $row;
                                                }

                                                $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                                $count = 1;
                                                $doj_date = new DateTime($doj);
                                                $today_date = new DateTime();

                                                for ($day = 1; $day <= $days_in_month; $day++) {
                                                    $current_date = sprintf("%d-%02d-%02d", $year, $month, $day);
                                                    $date_obj = new DateTime($current_date);
                                                    if ($date_obj < $doj_date)
                                                        continue; // Skip dates before doj
                                                    if ($date_obj > $today_date)
                                                        continue; // Skip future dates
                                    
                                                    $day_of_week = $date_obj->format('N');

                                                    if (isset($attendance_records[$current_date]) && $attendance_records[$current_date]['status'] !== 'absent') {
                                                        $present_days++;
                                                        $row = $attendance_records[$current_date];
                                                        $login_timestamp = strtotime($row['clock_in_time']);
                                                        $clock_out_time = $row['clock_out_time'];
                                                        $logout_display = $clock_out_time ? date("h:i A", strtotime($clock_out_time)) : "N/A";
                                                        $late_status = $row['late_status'];
                                                        $status_color = $row['status_color'];
                                                        $total_working_time = $row['total_working_time'];
                                                        $extra_or_remaining_time = $row['extra_or_remaining_time'];
                                                        $extra_or_remaining_label = $row['extra_or_remaining_label'];
                                                        ?>


                <?php
                                                     
                                                        $count++;
                                                        $employee_name;
                                                        $current_date;
                                                        date("h:i A", $login_timestamp);
                                                        $logout_display;
                                                        $total_working_time;
                                                        $extra_or_remaining_label;
                                                       
                                                       if ($late_status == "Late") {
                                                            $late++;
                                                        }
                                                        if ($late_status == "Late (Extra Late)") {
                                                            $extra_late++;
                                                        }
                                                        if($late_status == "Late (Extra Fine)") {
                                                            $extra_fine++;
                                                        }
                                                        if($late_status == "Half Day") {
                                                            $total_half_day++;
                                                        }
                                                        if($late_status == "Late (Half Day)") {
                                                            $total_half_day_late++;
                                                        }

                                                        if($extra_or_remaining_label == "Extra Time" AND ($late_status == "Late" || $late_status == "Late (Extra Late)" || $late_status == "Late (Extra Fine)")){
                                                            $late_cover++;
                                                        }else{
                                                            $late_status;
                                                        }
                                                    ?>


                <?php if ($is_admin) { ?>

                <?php } ?>
                </tr>
                <?php
                                                    } elseif (isset($holidays[$current_date])) {
                                                        $holiday_days++;
                                                    } elseif (isset($approved_leaves[$current_date])) {
                                                        $leave_days++;
                                                    } elseif ($day_of_week == 6) {
                                                        $saturday_days++;
                                                    } elseif ($day_of_week == 7) {
                                                        $sunday_days++;
                                                    } else {
                                                        $absent_days++;
                                                        // Check for existing absent record
                                                        $check_query = "SELECT id, status FROM newuser_attendance 
                                                                      WHERE user_id = '$employee_id' 
                                                                      AND DATE(clock_in_time) = '$current_date'";
                                                        $check_result = mysqli_query($conn, $check_query);
                                                        $absent_id = '';
                                                        if (mysqli_num_rows($check_result) > 0) {
                                                            $absent_row = mysqli_fetch_assoc($check_result);
                                                            $absent_id = $absent_row['id'];
                                                            // Only display absent if not on leave
                                                            if ($absent_row['status'] === 'absent' && !isset($approved_leaves[$current_date])) {
                                                                
                                                            }
                                                        } elseif (!isset($approved_leaves[$current_date])) {
                                                            // No record exists and not on leave, display Absent
                                                           
                                                        }
                                                    }
                                                }

                                                $totalwork_days = $present_days + $holiday_days + $leave_days + $absent_days;

                                                $total_late = $late + $extra_late + $extra_fine;

                                                // Update counters in DOM
                                                echo "<script>
                                                    document.getElementById('latestatus').textContent = '$late_cover';
                                                    document.getElementById('totalhalfday').textContent = '$total_half_day';
                                                    document.getElementById('totalworkingdays').textContent = '$totalwork_days';
                                                    document.getElementById('totallateCount').textContent = '$total_late';
                                                    document.getElementById('presentDaysCount').textContent = '$present_days';
                                                    document.getElementById('saturdayDaysCount').textContent = '$saturday_days';
                                                    document.getElementById('sundayDaysCount').textContent = '$sunday_days';
                                                    document.getElementById('holidayDaysCount').textContent = '$holiday_days';
                                                    document.getElementById('leaveDaysCount').textContent = '$leave_days';
                                                    document.getElementById('absentDaysCount').textContent = '$absent_days';
                                                </script>";
                                            }
                                        } else {
                                            echo '<tr><td colspan="9" class="text-center">You are not authorized to view this employee\'s attendance or employee is restricted.</td></tr>';
                                        }
                                    } else {
                                        $current_date = $today->format('Y-m-d');
                                        // Modify attendance query based on user role
                                        if ($is_admin) {
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
                                                 AND he.id != 14
                                                 ORDER BY na.clock_in_time ASC";

                                        } else {
                                            // Managers see only their assigned employees
                                            $attendance_query = "SELECT na.id, na.user_id, na.clock_in_time, na.clock_in_ip, 
                                                na.clock_out_time, na.clock_out_ip, na.status, na.created_at, na.updated_at,
                                                na.late_status, na.status_color, na.total_working_time, 
                                                na.extra_or_remaining_time, na.extra_or_remaining_label,
                                                CONCAT(he.fname, ' ', he.lname) as employee_name,
                                                he.doj
                                                FROM newuser_attendance na
                                                LEFT JOIN hrm_employee he ON na.user_id = he.id
                                                INNER JOIN hrm_reporting_manager hrm ON he.id = hrm.employee_id
                                                WHERE DATE(na.clock_in_time) = '$current_date'
                                                AND na.clock_in_time >= he.doj
                                                AND he.id != 14
                                                AND hrm.reporting_manager_id = '$emp_id'
                                                ORDER BY na.id DESC";
                                        }





                                     
                                    }
  

?>








<!--
    <input type="text" id="totalworkingdays" value="<?= $totalwork_days; ?>">
    <input type="text" id="presentDaysCount" value="<?= $present_days; ?>">
    <input type="text" id="saturdayDaysCount" value="<?= $saturday_days; ?>">
    <input type="text" id="sundayDaysCount" value="<?= $sunday_days; ?>">
    <input type="text" id="holidayDaysCount" value="<?= $holiday_days; ?>">
    <input type="text" id="leaveDaysCount" value="<?= $leave_days; ?>">
    <input type="text" id="absentDaysCount" value="<?= $absent_days; ?>">
    <input type="text" id="totallateCount" value="<?= $total_late; ?>">
    <input type="text" id="latestatus" value="<?= $late_cover; ?>">
    <input type="text" id="totalhalfday" value="<?= $total_half_day; ?>">
    <input type="text" id="totalhalfday" value="<?= $total_half_day_late; ?>">
-->

<?php
    $actual_lates = $total_late - $late_cover;
    $pdsalary = round($salary / $totalwork_days, 0);
    $hdsalary = round($pdsalary / 2, 0);
    $sdsalary = round($hdsalary / 2, 0);
?>









                <br><br>

                <div class="row">
                    <div class="col-md-3">
                        <div class="row mb-3">
                            <h4 class="text-blue"><?= $employee_name; ?></h4>
                        </div>
                        <div class="row mb-3">
                            <h6
                                title="<?= $present_days+$holiday_days; ?> Working Days = <?= $present_days ?> Present Days + <?= $holiday_days ?> Holidays">
                                Present Days : <?= $present_days; ?>*</h6>
                        </div>
                        <div class="row">
<!-- <h4 class="text-muted" style="font-size:13px">
    Total Working Days: <span id="totalworkingdays"><?= $totalwork_days ?></span> <br>
    Present Days: <span id="presentDaysCount"><?= $present_days ?></span> <br>
    Saturdays: <span id="saturdayDaysCount"><?= $saturday_days ?></span> <br>
    Sundays: <span id="sundayDaysCount"><?= $sunday_days ?></span> <br>
    Holidays: <span id="holidayDaysCount"><?= $holiday_days ?></span> <br>
    Leaves: <span id="leaveDaysCount"><?= $leave_days ?></span> <br>
    Absent: <span id="absentDaysCount"><?= $absent_days ?></span> <br>
    Total late: <span id="totallateCount"><?= $total_late ?></span> <br>
    Late-Cover: <span id="latestatus"><?= $late_cover ?></span> <br>
    Half-Days: <span id="totalhalfday"><?= $total_half_day ?></span>
    Late-Half-Days: <span id="totalhalfday"><?= $total_half_day_late ?></span>
</h4>-->
                        </div>
                    </div>
                    <div class="col-md-7">
                    </div>
                    <div class="col-md-2">
                        <div class="row mb-3">
                            <?php
                $month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
                $year  = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
                // Month ka naam banao
                $monthName = date("F", mktime(0, 0, 0, $month, 10));
                // Display kar do
                echo "<h4>$monthName $year</h4>";
            ?>
                        </div>
                        <div class="row mb-2">
                            <h6
                                title="<?= $totalwork_days ?> Working Days = <?= $totalwork_days-$holiday_days ?> Working Days + <?= $holiday_days ?> Holidays">
                                Working Days : <?= $totalwork_days-$holiday_days ?>*</h6>
                        </div>
                    </div>
                </div>


                <div class="container mt-4 mb-4">
                    <style>
                    .notes th,
                    .notes td {
                        border: 0px solid #dee2e6;
                        padding: 0px;
                        vertical-align: middle;
                        border-left-width: 6px;
                        padding-left: 10px;
                        padding-right: 10px;
                    }
                    </style>
                    <table class="notes">
                        <tr>
                            <td class="">*Note</td>
                            <?php
                                if($monthly_shorts){
                                    echo "<td class=''>Monthly Shorts : $monthly_shorts</td>";
                                }
                            ?>
                            <td class="">Monthly Half : <?= $monthly_half ?></td>
                            <td class="">Monthly Leaves : <?= $monthly_leaves ?></td>
                            <td class="">Relaxation Late : <?= $relaxation_late; ?></td>
                            <td class="">Informed Relaxation Late : 0</td>
                            <td class="">Late Fine : ₹<?= $normal_fine; ?></td>
                            <td></td>

                        </tr>
                    </table>
                </div>
                <div class="page-header">
                    <div class="row gap-2 justify-content-center">
                        <div class="col-lg-12 col-md-12 col-sm-12">




<form id="salaryForm">

    <!-- Row One Start -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="salary" class="form-label">Salary</label>
                <input type="number" class="form-control" id="salary" name="salary" value="<?= $salary ?>" min="0" readonly>
            </div>
            <div class="col-md-3" title="<?= $totalwork_days ?> Working Days = <?= $totalwork_days-$holiday_days ?> Working Days + <?= $holiday_days ?> Holidays">
                <label for="totalDays" class="form-label">*Total Days</label>
                <input type="number" class="form-control" id="totalDays" name="totalDays" value="<?= $totalwork_days ?>" min="0" readonly>
            </div>
            <div class="col-md-3">
                <label for="perDaySalary" class="form-label">Per Day Salary</label>
                <input type="number" class="form-control" id="perDaySalary" name="perDaySalary" value="<?= $pdsalary ?>" min="0" readonly>
            </div>
            <div class="col-md-2">
                <label for="half_day_fine" class="form-label">Half Day</label>
                <input type="number" class="form-control" id="half_day_fine" name="half_day_fine" value="<?= $hdsalary ?>" min="0" readonly>
            </div>
            <div class="col-md-1">
                <label for="short_day_fine" class="form-label">Short Day</label>
                <input type="number" class="form-control" id="short_day_fine" name="short_day_fine" value="<?= $sdsalary ?>" min="0" readonly>
            </div>
        </div>
    <!-- Row One End -->

    <!-- Row Two Start -->
        <div class="row g-3 mb-4">
            <div class="col-md-3" title="Present Days">
                <label for="presentDays" class="form-label">Present Days</label>
                <input type="number" class="form-control" id="presentDays" name="presentDays" value="<?= $present_days ?>" min="0" readonly>
            </div>
            <?php
                // Leaves & Absents
                $totalLeaves = ($leave_days + $absent_days) - $monthly_leaves;
                $leaveDeduct = max(0, $totalLeaves * $pdsalary);
                $displayLeaves = ($totalLeaves == -1 ? "0" : $totalLeaves);

                // Half Day
                $total_half_day_new = max(0, $total_half_day-$monthly_half);
                $halfDayDeduct = $total_half_day_new * $hdsalary;
                // $leavehalfc

                // Short Day
                $total_short_day_new = max(0, $leaveshortc-$monthly_shorts);
                $shortDayDeduct = $total_short_day_new * $sdsalary;

                // Normal Lates
                $lateCount   = max(0, $actual_lates - $relaxation_late);
                $lateDeduct  = $lateCount * $normal_fine;

                // Late Cover (default 0, JS se handle hoga)
                $lateCoverDeduct = $late_cover * $normal_fine;

                // Initial Total (without late-cover checkbox)
                $totalDeduction = $leaveDeduct + $halfDayDeduct + $lateDeduct;
            ?>

            <!-- Leaves Deduction -->
            <div class="col-md-3" title="Leaves : <?= $leave_days ?> | Absents : <?= $absent_days ?> | Monthly Leaves Exempted : <?= $monthly_leaves ?>">
                <label class="form-label">Leaves + Absent - Monthly Leave</label>
                <input type="text" class="form-control" title="<?= $leaveDeduct ?> Deduction" value="<?= $leave_days ?> L + <?= $absent_days ?> A - <?= $monthly_leaves ?> M = <?= $displayLeaves ?> Total Leaves" readonly>
                <input type="hidden" class="deduction" value="<?= $leaveDeduct ?>">
            </div>


             <!-- Half Day Deduction -->
            <div class="col-md-3" title="<?= $total_half_day ?> Half Day | <?= $monthly_half ?> Monthly Half Exempted">
                <label class="form-label">Half Day 1</label>
                <input type="text" class="form-control"  title="Fine Deduction : <?= $halfDayDeduct ?>" value="<?= $total_half_day ?> H - <?= $monthly_half ?> M = <?= $total_half_day_new ?> Total Half" min="0" readonly>
                <input type="hidden" class="deduction" id="half_day_all" name="half_day_all" value="<?= $halfDayDeduct ?>">
            </div>

            <!-- Short Day Deduction -->
            <div class="col-md-3" title="Fine Deduction : <?= $lateDeduct ?>">
                <label class="form-label">Short Leave</label>
                <input type="text" class="form-control"  title="Fine Deduction : <?= $shortDayDeduct ?>" value="<?= $total_short_day ?> S - <?= $monthly_shorts ?> M = <?= $total_short_day_new ?> Total Short" min="0" readonly>
                <input type="hidden" class="deduction" id="short_day_all" name="short_day_all" value="<?= $shortDayDeduct ?>">
            </div>


            
        </div>
    <!-- Row Two End -->

    <!-- Row Third Start -->
        <!-- Total Deduction -->
        <div class="row g-3">
            <!-- Total Deduction -->  
            <div class="col-md-4">
                <label class="form-label">Total Deduction Amount</label>
                <input type="number" class="form-control" id="total_deduction"
                    value="<?= $totalDeduction ?>" readonly>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <label for="final_salary" class="form-label">Final Salary</label>
                <input type="number" class="form-control" id="final_salary" name="final_salary"
                    value="<?= $final_salary ?>" readonly>
                <input type="hidden" id="total_salary" value="<?= $salary ?>">
            </div>
        </div>
    <!-- Row Third End -->




<hr>



        <div class="row g-3 mb-5 mt-3">
            <!-- Half Day Deduction -->
            <div class="col-md-2" title="<?= $total_half_day ?> Half Day | <?= $monthly_half ?> Monthly Half Exempted">
                <label class="form-label">Half Day</label>
                <input type="text" class="form-control"  title="Fine Deduction : <?= $halfDayDeduct ?>" value="<?= $total_half_day ?> H - <?= $monthly_half ?> M = <?= $total_half_day_new ?> Total Half" min="0" readonly>
                <input type="hidden" class="deduction" id="half_day_all" name="half_day_all" value="<?= $halfDayDeduct ?>">
            </div>

            <!-- Lates Deduction -->
            <div class="col-md-3" title="Fine Deduction : <?= $lateDeduct ?>">
                <label class="form-label">Actual Lates - <?= $relaxation_late; ?> Relaxation</label>
                <input type="text" class="form-control" value="<?= $actual_lates ?> - <?= $relaxation_late ?> = <?= $lateCount ?>" readonly>
                <input type="hidden" class="deduction" value="<?= $lateDeduct ?>">
            </div>

            <!-- Late Cover Deduction -->
            <div class="col-md-2" title="Fine Deduction : <?= $lateCoverDeduct ?>">
                <input type="checkbox" class="form-check-input" id="late_cover_checkbox">
                <label class="form-label">Late-&-Cover</label>
                <input type="number" class="form-control" id="normal_late_cover" value="<?= htmlspecialchars($late_cover) ?>" min="0" readonly>
                <input type="hidden" class="deduction" id="late_cover_value" value="<?= $lateCoverDeduct ?>">
            </div>
        </div>



    <!-- <div class="mt-4">
        <button type="button" class="btn btn-success" id="calculateButton">Submit</button>
    </div> -->
</form>
       


  <script>
            document.addEventListener("DOMContentLoaded", function() {
                const totalField = document.getElementById("total_deduction");
                const lateCoverCheckbox = document.getElementById("late_cover_checkbox");
                const lateCoverValue = document.getElementById("late_cover_value");

                function calculateTotal() {
                    let total = 0;
                    document.querySelectorAll(".deduction").forEach(input => {
                        // agar ye late-cover hai to tabhi add kare jab checkbox checked hai
                        if (input.id === "late_cover_value") {
                            if (lateCoverCheckbox.checked) {
                                total += parseFloat(input.value) || 0;
                            }
                        } else {
                            total += parseFloat(input.value) || 0;
                        }
                    });
                    totalField.value = total;
                }

                // Event Listener
                lateCoverCheckbox.addEventListener("change", calculateTotal);

                // Page load pe bhi ek baar run kar do
                calculateTotal();
            });
        </script>
        <?php
            // Final Salary
            $final_salary = max(0, $salary - $totalDeduction); // negative avoid
        ?>
        <!--  Existing deductions code here ... -->

        <!-- Final Salary Display -->

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const totalField = document.getElementById("total_deduction");
        const finalField = document.getElementById("final_salary");
        const totalFieldslip = document.getElementById("total_deduction_slip");
        const finalFieldslip = document.getElementById("final_salary_slip");
        const baseSalary = parseFloat(document.getElementById("total_salary")
            .value) || 0;
        const lateCoverCheckbox = document.getElementById("late_cover_checkbox");
        const lateCoverValue = document.getElementById("late_cover_value");

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll(".deduction").forEach(input => {
                if (input.id === "late_cover_value") {
                    if (lateCoverCheckbox.checked) {
                        total += parseFloat(input.value) || 0;
                    }
                } else {
                    total += parseFloat(input.value) || 0;
                }
            });

            let finalSalary = baseSalary - total;
            if (finalSalary < 0) finalSalary = 0;

            // input fields update (2 decimal places)
            if (totalField) totalField.value = total.toFixed(0);
            if (finalField) finalField.value = finalSalary.toFixed(0);

            // span update (2 decimal places)
            totalFieldslip.textContent = total.toFixed(0);
            finalFieldslip.textContent = finalSalary.toFixed(0);
        }

        // Checkbox event
        lateCoverCheckbox.addEventListener("change", calculateTotal);

        // Initial calculation
        calculateTotal();
    });
    </script>

<!-- 
                            <div class="mt-4">
                                <h4>Calculated Salary After Deductions: <span id="resultSalary">0</span></h4>
                            </div> -->
                        </div>



<br><br><br>
<hr>
<br><br><br>




























                        <div class="col-lg-12 col-md-12 col-sm-12 card" id="salarySlip">
                            <div class="salary-header">
                                <div class="salary-logo">
                                    <!--<img src="assets/img/company-crop-logo.webp" alt="company logo" width="200" height="200">-->
                                </div>
                                <div class="company-title">
                                    <h1 class="company-name">Expetize Private Limited</h1>
                                    <p class="company-address text-center">401, Vinayak Complex, Plot No 76, Vijay
                                        Block, Laxmi Nagar, Near Pillar<br>No-51-52, Delhi, Delhi-110092<br>CIN:
                                        U74999DL2016PTC307712
                                    </p>
                                    <br>
                                </div>
                            </div>
                            <?php

                            $month_number = $month;
                            $month_name = date("F", mktime(0, 0, 0, $month_number, 1));
                            $designation_id = $employee_row['designation_id'];
                            $department_id = $employee_row['department_id'];
                            $designation = "Select * from hrm_designation where id=$designation_id";
                            $department = "Select * from hrm_department where id=$department_id";
                            $designation_result = mysqli_query($conn, $designation);
                            $department_result = mysqli_query($conn, $department);
                            $designation_row = mysqli_fetch_assoc($designation_result);
                            $department_row = mysqli_fetch_assoc($department_result);

                            $bank_details = "select * from hrm_bank_detail where emp_id=$emp_id";
                            $bank_result = mysqli_query($conn, $bank_details);
                            $bank_row = mysqli_fetch_assoc($bank_result);

                            $basic = $salary * 0.40;
                            $hra = $basic * 0.50;
                            $medical_allowance = 800;

                            $conveyance_allowance = 1200;
                            $special_allowance = $salary - ($basic + $medical_allowance + $hra + $conveyance_allowance);
                            $total_allowance = $basic + $medical_allowance + $hra + $conveyance_allowance + $special_allowance;
                            $total_deduction = round($salary - $after_deduction);
                            $net_pay = round($after_deduction);
                            $pay_in_word = roundAndConvertToWords($after_deduction);

                            ?>
                            <div class="salary-title">
                                Payslip for the Month of <?= $month_name; ?>, <?= $year; ?>
                            </div>

                            <div class="employee-details">
                                <div class="normal-details">
                                    <p>
                                        Name : <?php echo $employee_row['fname'] . " " . $employee_row['lname']; ?>
                                    </p>
                                    <p>Designation : <?= $designation_row['name']; ?> </p>

                                    <p>Department : <?= $department_row['name']; ?> </p>
                                    <p>
                                        Location : Delhi
                                    </p>
                                    <p>
                                        <br>
                                        <!-- Effective Work Days : <?= $totalDays; ?> -->
                                    </p>
                                    <p>
                                        <br>
                                        <!-- LOP : <span id="lop"><?= $totalDays - ($presentDays + 1) ?></span> -->
                                    </p>
                                </div>
                                <div class="bank-details">
                                    <p>
                                        Employee ID :
                                        <?
                                        // = isset($emp_id) ? $emp_id : ''; 
                                        ?>
                                        <?php echo $employee_row['emp_id']; ?>
                                    </p>
                                    <p>
                                        <?php

$query = "SELECT doj FROM hrm_employee WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $doj = date("F j, Y", strtotime($row['doj'])); 
?>
                                        Date of joining: <?php echo htmlspecialchars($doj); ?>

                                        <?php
} else {
?>
                                        Date of joining:00-00-0000
                                        <?php
}
?>



                                    </p>
                                    <p>
                                        Bank Name : <?= isset($bank_row['bank_name']) ? $bank_row['bank_name'] : ''; ?>
                                    </p>
                                    <p>
                                        Bank Account Number :
                                        <?= isset($bank_row['account_number']) ? $bank_row['account_number'] : ''; ?>
                                    </p>
                                    <p>
                                        IFSC Code : <?= isset($bank_row['ifsc']) ? $bank_row['ifsc'] : ''; ?>
                                    </p>
                                </div>


                            </div>

                            <div class="salary-structure">

                                <div class="left-side">
                                    <div class="left-heading">
                                        <div>Earning
                                        </div>
                                        <div>Amount</div>
                                    </div>
                                    <div class="earning-heading">
                                        <div>

                                            <p>Basic</p>
                                            <p>HRA</p>
                                            <p>Medical Allowance</p>
                                            <p>Conveyance Allowance</p>
                                            <p>Special Allowance</p>

                                        </div>
                                        <div>
                                            <p id="basic">₹<?= isset($basic) ? $basic : ''; ?></p>
                                            <p id="hra">₹<?= isset($hra) ? $hra : ''; ?></p>
                                            <p id="medical_allowance">
                                                ₹<?= isset($medical_allowance) ? $medical_allowance : ''; ?></p>
                                            <p id="conveyance_allowance">
                                                ₹<?= isset($conveyance_allowance) ? $conveyance_allowance : ''; ?></p>
                                            <p id="special_allowance">
                                                ₹<?= isset($special_allowance) ? $special_allowance : ''; ?></p>

                                        </div>

                                    </div>
                                    <div class="total-earning">
                                        <div class="total_allowance">Total Earning (Rs)</div>
                                        <div id="total_allowance" class="total_allowance">
                                            ₹<?= isset($total_allowance) ? $total_allowance : ''; ?></div>
                                    </div>

                                    <div class="net_pay">
                                        <div>Net Pay For The Month</div>
                                        <div>
                                            <span id="net-salary">₹
                                                <!-- <?= isset($net_pay) ? $net_pay : ''; ?> -->
                                                <span id="final_salary_slip"> ***** </span>
                                            </span>

                                        </div>

                                    </div>
                                </div>
                                <div class="right-side">
                                    <div class="right-heading">
                                        <div>Deduction</div>
                                        <div>Amount</div>
                                    </div>
                                    <div class="deduction-heading">
                                        <div>
                                            <p style="visibility: hidden;">1</p>
                                            <p style="visibility: hidden;">2</p>
                                            <p style="visibility: hidden;"> 3</p>
                                            <p style="visibility: hidden;">4</p>
                                            <p style="visibility: hidden;">5</p>
                                        </div>
                                        <div>
                                            <p style="visibility: hidden;">1</p>
                                            <p style="visibility: hidden;">2</p>
                                            <p style="visibility: hidden;">3</p>
                                            <p style="visibility: hidden;">4</p>
                                            <p style="visibility: hidden;">5</p>
                                        </div>
                                    </div>
                                    <div class="total-deduction">
                                        <div>
                                            Total Deduction (Rs)
                                        </div>
                                        <div id="total-deduction">
                                            ₹
                                            <!-- <?= isset($total_deduction) ? $total_deduction : '0'; ?> -->
                                            <span id="total_deduction_slip"></span>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <!--<div class="salary_in_word">-->
                            <!--    Rupees-<span id="salary-in-word"><?= isset($pay_in_word) ? $pay_in_word : ''  ?> </span>-->
                            <!--</div>-->

                            <hr>
                            <!--<div class="mb-3">-->
                            <!--    <p class="text-center"> This is a system generated payslip and does not require any signature.</p>-->
                            <!--</div>-->
                        </div>

                    </div>
                </div>

                <button id="downloadSalarySlip" class="btn btn-primary  ">Download Salary Slip</button>
                <button id="submitButton" onclick="generateAndSendPDF()" class="btn btn-info">Generate and Send
                    PDF</button>
                <i id="loadingIcon" class="fas fa-spinner fa-spin" style="display: none;"></i>
            </div>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>


    <script>
    document.getElementById('calculateButton').addEventListener('click', function() {
        // Retrieve form values
        const salary = parseFloat(document.getElementById('salary').value) || 0;
        const totalDays = parseFloat(document.getElementById('totalDays').value) || 0;
        let presentDays = parseFloat(document.getElementById('presentDays').value) || 0;
        const halfDayAll = parseFloat(document.getElementById('half_day_all').value) || 0;
        const normalLate = parseFloat(document.getElementById('normal_late').value) || 0;
        const lateExtra = parseFloat(document.getElementById('late_extra').value) || 0;
        const normalFine = parseFloat(document.getElementById('normal_fine').value) || 0;
        const extraFine = parseFloat(document.getElementById('extra_fine').value) || 0;
        const halfDayFine = parseFloat(document.getElementById('half_day_fine').value) || 0;
        const perDaySalary = parseFloat(document.getElementById('perDaySalary').value) || 0;

        // Calculation for present and absent days
        presentDays = presentDays;
        if (presentDays > totalDays) {
            presentDays = totalDays;
        }

        const absentDays = totalDays - presentDays;
        document.getElementById('lop').textContent = absentDays;

        const salaryDeductions = (normalLate * normalFine) +
            (lateExtra * extraFine) +
            (halfDayAll * halfDayFine) +
            (absentDays * perDaySalary);
        const salaryAfterDeductions = salary - salaryDeductions;

        // Display result salary
        document.getElementById('resultSalary').textContent = salaryAfterDeductions.toFixed(2);
        document.getElementById('net-salary').textContent = salaryAfterDeductions.toFixed(2);
        const salaryInWords = (roundAndConvertToWords(salaryAfterDeductions));
        document.getElementById('salary-in-word').textContent = salaryInWords;

        const basic = salary * 0.40;

        const hra = basic * 0.50;

        const medical_allowance = 800;



        const conveyance_allowance = 1200;

        const special_allowance = salary - (basic + hra + medical_allowance + conveyance_allowance);
        const total_allowance = basic + hra + medical_allowance + conveyance_allowance + special_allowance;
        const total_deduction = salaryDeductions;
        document.getElementById('total_allowance').textContent = round(total_allowance).toFixed(2);
        document.getElementById('total-deduction').textContent = round(total_deduction).toFixed(2);

        // Update the displayed values
        document.getElementById('basic').textContent = basic.toFixed(2);
        document.getElementById('hra').textContent = hra.toFixed(2);
        document.getElementById('medical_allowance').textContent = medical_allowance.toFixed(2);
        document.getElementById('conveyance_allowance').textContent = conveyance_allowance.toFixed(2);
        document.getElementById('special_allowance').textContent = special_allowance.toFixed(2);
    });

    // Helper function to round values
    function round(value) {
        return Math.round(value * 100) / 100; // rounding to two decimal places
    }

    function numberToWords(num) {
        const belowTwenty = [
            'Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
        ];
        const tens = [
            '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
        ];
        const aboveThousand = ['', 'Thousand', 'Million', 'Billion'];

        if (num === 0) return 'Zero';

        function helper(n) {
            if (n < 20) return belowTwenty[n];
            else if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + belowTwenty[n % 10] : '');
            else if (n < 1000) return belowTwenty[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' ' + helper(n % 100) :
                '');
            return '';
        }

        let word = '';
        let i = 0;

        while (num > 0) {
            if (num % 1000 !== 0) {
                word = helper(num % 1000) + (aboveThousand[i] ? ' ' + aboveThousand[i] : '') + (word ? ' ' + word : '');
            }
            num = Math.floor(num / 1000);
            i++;
        }

        return word.trim();
    }

    // Function to round off and convert to words
    function roundAndConvertToWords(decimal) {
        const roundedNumber = Math.round(decimal); // Round off the number
        return numberToWords(roundedNumber); // Convert to words
    }

    async function generatePDF() {
        const salarySlip = document.getElementById('salarySlip');

        if (!salarySlip) {
            alert('Salary slip not found!');
            return;
        }

        // Convert the salary slip to a canvas
        const canvas = await html2canvas(salarySlip, {
            scale: 2
        });
        const imgData = canvas.toDataURL('image/png');

        // Generate PDF
        const {
            jsPDF
        } = window.jspdf;
        const pdf = new jsPDF();
        const imgWidth = 190; // Adjust based on A4 size
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);

        // Save the PDF
        pdf.save('Salary_Slip.pdf'); // This will download the file
    }

    // Attach the click event to the button
    document.getElementById('downloadSalarySlip').addEventListener('click', generatePDF);


    async function generateAndSendPDF() {
        const salarySlip = document.getElementById('salarySlip');
        const submitButton = document.getElementById('submitButton'); // Button ID
        const loadingIcon = document.getElementById('loadingIcon'); // Loading spinner ID

        if (!salarySlip) {
            alert('Salary slip not found!');
            return;
        }

        // Show the loading spinner and disable the button
        if (loadingIcon) loadingIcon.style.display = 'inline-block';
        if (submitButton) submitButton.disabled = true;

        try {
            // Convert the salary slip to a canvas
            const canvas = await html2canvas(salarySlip, {
                scale: 2, // Higher scale for better quality
            });
            const imgData = canvas.toDataURL('image/jpeg', 0.7); // Compress image data (JPEG format)

            // Generate PDF with compression
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4',
                compress: true, // Enable PDF compression
            });

            const imgWidth = 190; // Adjust for A4 width
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            pdf.addImage(imgData, 'JPEG', 10, 10, imgWidth, imgHeight, undefined,
                'FAST'); // Use 'FAST' compression mode

            // Convert PDF to a Blob
            const pdfBlob = pdf.output('blob');

            // Get email input value
            const email = document.getElementById('email').value;
            if (!email) {
                alert('Please enter a valid email address.');
                return;
            }

            // Create FormData and append the PDF file and email
            const formData = new FormData();
            formData.append('pdf', pdfBlob, 'salary_slip.pdf');
            formData.append('email', email);

            // Send the PDF to the server
            const response = await fetch('email/send-salary-slip.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json();

            if (data.success) {
                alert('Salary slip sent successfully!');
            } else {
                alert(`Failed to send salary slip: ${data.error || 'Unknown error'}`);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while sending the salary slip.');
        } finally {
            // Hide the loading spinner and enable the button
            if (loadingIcon) loadingIcon.style.display = 'none';
            if (submitButton) submitButton.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('submitButton').onclick = generateAndSendPDF;
    });
    </script>


</body>

</html>