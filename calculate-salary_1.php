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
$perDaySalary = $salary / $totalWorkingDays;


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
$extra_fine = $row_fine['extra_fine'];

$half_day_fine = $perDaySalary / 2;

$leave_days_query = "
    SELECT 
        SUM(no_of_days) AS total_leave_days
    FROM hrm_leave_applied
    WHERE emp_id = ? 
    AND YEAR(start_date) = ? 
    AND MONTH(start_date) = ?
";

// Prepare the leave days query
$stmt = $conn->prepare($leave_days_query);

$stmt->bind_param('iii', $emp_id, $year, $month);
$stmt->execute();
$leave_days_result = $stmt->get_result();

$leave_days_data = $leave_days_result->fetch_assoc();
$leave_days = $leave_days_data['total_leave_days'] - 1;


function calculateSalary($salary, $totalDays, $presentDays, $half_day_all, $normal_late, $late_extra, $normal_fine, $extra_fine, $half_day_fine, $perDaySalary)
{

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

function numberToWords($num)
{
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
        .salary_in_word,.salary-structure ,.employee-details{
            font-size: 25px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row gap-2 justify-content-center">
                        <div class="col-lg-3 col-md-6 col-sm-12 card p-3">
                            <h3>Total Holidays : <?= $totalHolidays; ?></h3>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 card p-3">
                            <h3>Total Working Days : <?= $totalWorkingDays; ?></h3>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 card p-3">
                            <h3>Total Present Days : <?= $presentDays; ?></h3>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 card">
                            <table class="table">

                                <thead>
                                    <tr>

                                        <th class="text-center">Normal Late</th>
                                        <th class="text-center">Extra Late</th>
                                        <th class="text-center">Half Days</th>
                                        <th class="text-center">Leaves</th>
                                        <th class="text-center">Normal Fine</th>
                                        <th class="text-center">Extra fine</th>
                                        <th class="text-center">Half Day Fine</th>
                                        <th class="text-center">Per Day Salary</th>
                                        <th>Salary</th>
                                        <th>final Salary</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    <td class="text-center"><?= $normal_late; ?></td>
                                    <td class="text-center"><?= $late_extra; ?></td>
                                    <td class="text-center"><?= $half_day_all; ?></td>
                                    <td class="text-center"><?= $leave_days; ?></td>
                                    <td class="text-center">₹<?= $normal_fine; ?></td>
                                    <td class="text-center">₹<?= $extra_fine; ?></td>
                                    <td class="text-center">₹<?= $half_day_fine; ?></td>

                                    <td class="text-center">₹<?= $perDaySalary; ?></td>
                                    <td>₹<?= $salary; ?></td>
                                    <td class="text-center">₹<?= $after_deduction; ?></td>

                                </tbody>
                            </table>

                        </div>



                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <form id="salaryForm">
                                <div class="row g-3">

                                    <div class="col-md-4">
                                        <label for="salary" class="form-label">Salary</label>
                                        <input type="number" class="form-control" id="salary" name="salary" value="<?= htmlspecialchars($salary) ?>" required min="0" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="totalDays" class="form-label">Total Days</label>
                                        <input type="number" class="form-control" id="totalDays" name="totalDays" value="<?= htmlspecialchars($totalDays) ?>" required min="0" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="presentDays" class="form-label">Present Days</label>
                                        <input type="number" class="form-control" id="presentDays" name="presentDays" value="<?= htmlspecialchars($presentDays) ?>" required min="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="half_day_all" class="form-label">Half Day Allowance</label>
                                        <input type="number" class="form-control" id="half_day_all" name="half_day_all" value="<?= htmlspecialchars($half_day_all) ?>" required min="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="normal_late" class="form-label">Normal Late</label>
                                        <input type="number" class="form-control" id="normal_late" name="normal_late" value="<?= htmlspecialchars($normal_late) ?>" required min="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="late_extra" class="form-label">Late Extra</label>
                                        <input type="number" class="form-control" id="late_extra" name="late_extra" value="<?= htmlspecialchars($late_extra) ?>" required min="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="normal_fine" class="form-label">Normal Fine</label>
                                        <input type="number" class="form-control" id="normal_fine" name="normal_fine" value="<?= htmlspecialchars($normal_fine) ?>" required min="0" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="extra_fine" class="form-label">Extra Fine</label>
                                        <input type="number" class="form-control" id="extra_fine" name="extra_fine" value="<?= htmlspecialchars($extra_fine) ?>" required min="0" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="half_day_fine" class="form-label">Half Day Fine</label>
                                        <input type="number" class="form-control" id="half_day_fine" name="half_day_fine" value="<?= htmlspecialchars($half_day_fine) ?>" required min="0" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="perDaySalary" class="form-label">Per Day Salary</label>
                                        <input type="number" class="form-control" id="perDaySalary" name="perDaySalary" value="<?= htmlspecialchars($perDaySalary) ?>" required min="0" readonly>
                                    </div>
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>" id="email">
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-success" id="calculateButton">Submit</button>
                                </div>
                            </form>
                            <div class="mt-4">
                                <h4>Calculated Salary After Deductions: <span id="resultSalary">0</span></h4>
                            </div>
                        </div>



                        <div class="col-lg-12 col-md-12 col-sm-12 card" id="salarySlip">
                            <div class="salary-header">
                                <div class="salary-logo">
                                    <!--<img src="assets/img/company-crop-logo.webp" alt="company logo" width="200" height="200">-->
                                </div>
                                <div class="company-title">
                                    <h1 class="company-name">Expetize Private Limited</h1>
                                    <p class="company-address text-center">401, Vinayak Complex, Plot No 76, Vijay Block, Laxmi Nagar, Near Pillar<br>No-51-52, Delhi, Delhi-110092<br>CIN: U74999DL2016PTC307712
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
                                        Effective Work Days : <?= $totalDays; ?>
                                    </p>
                                    <p>
                                        LOP : <span id="lop"><?= $totalDays - ($presentDays + 1) ?></span>
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
                                        Bank Account Number : <?= isset($bank_row['account_number']) ? $bank_row['account_number'] : ''; ?>
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
                                            <p id="medical_allowance">₹<?= isset($medical_allowance) ? $medical_allowance : ''; ?></p>
                                            <p id="conveyance_allowance">₹<?= isset($conveyance_allowance) ? $conveyance_allowance : ''; ?></p>
                                            <p id="special_allowance">₹<?= isset($special_allowance) ? $special_allowance : ''; ?></p>

                                        </div>

                                    </div>
                                    <div class="total-earning">
                                        <div class="total_allowance">Total Earning (Rs)</div>
                                        <div id="total_allowance" class="total_allowance">₹<?= isset($total_allowance) ? $total_allowance : ''; ?></div>
                                    </div>

                                    <div class="net_pay">
                                        <div>Net Pay For The Month</div>
                                        <div>
                                            <span id="net-salary">₹<?= isset($net_pay) ? $net_pay : ''; ?></span>
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
                                           ₹ <?= isset($total_deduction) ? $total_deduction : '0'; ?>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="salary_in_word">
                                 Rupees-<span id="salary-in-word"><?= isset($pay_in_word) ? $pay_in_word : ''  ?> </span> 
                            </div>

                            <hr>
                            <!--<div class="mb-3">-->
                            <!--    <p class="text-center"> This is a system generated payslip and does not require any signature.</p>-->
                            <!--</div>-->
                        </div>

                    </div>
                </div>

                <button id="downloadSalarySlip" class="btn btn-primary  ">Download Salary Slip</button>
                <button id="submitButton" onclick="generateAndSendPDF()" class="btn btn-info">Generate and Send PDF</button>
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
                else if (n < 1000) return belowTwenty[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' ' + helper(n % 100) : '');
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
                pdf.addImage(imgData, 'JPEG', 10, 10, imgWidth, imgHeight, undefined, 'FAST'); // Use 'FAST' compression mode

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