<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);

require_once 'layouts/config.php';
require_once 'email/mailer.php';

$previousMonday = date('Y-m-d', strtotime('monday last week'));
$previousSunday = date('Y-m-d', strtotime('sunday last week'));
$startDateTime = "$previousMonday 00:00:00";
$endDateTime = "$previousSunday 23:59:59";

$employeeQuery = "
    SELECT 
        e.id,
        CONCAT(e.fname, ' ', e.lname) as employee_name,
        e.email,
        d.name as department_name
    FROM hrm_employee e
    LEFT JOIN hrm_department d ON e.department_id = d.id
    WHERE e.status = 1
";

$result = mysqli_query($con, $employeeQuery);
if (!$result) {
    die("Error in employee query: " . mysqli_error($con));
}

function sum_times($timeArr) {
    $totalSeconds = 0;
    foreach ($timeArr as $time) {
        if (preg_match('/^(\d+):(\d+):(\d+)$/', $time, $matches)) {
            $totalSeconds += ($matches[1] * 3600) + ($matches[2] * 60) + $matches[3];
        }
    }
    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;
    return sprintf("%02d hours %02d minutes %02d seconds", $hours, $minutes, $seconds);
}

ob_start();

while ($employee = mysqli_fetch_assoc($result)) {
    $empId = $employee['id'];
    if ($empId == 14) continue;

    $empName = $employee['employee_name'];
    $empEmail = $employee['email'];
    $deptName = $employee['department_name'] ?? 'N/A';

    $attendanceQuery = "
        SELECT 
            clock_in_time,
            clock_out_time,
            late_status,
            total_working_time,
            extra_or_remaining_time,
            extra_or_remaining_label
        FROM newuser_attendance
        WHERE user_id = ?
        AND clock_in_time BETWEEN ? AND ?
        ORDER BY clock_in_time ASC
    ";

    $stmt = mysqli_prepare($con, $attendanceQuery);
    if (!$stmt) {
        echo "Prepare failed for employee $empId: " . mysqli_error($con) . "\n";
        continue;
    }

    mysqli_stmt_bind_param($stmt, "sss", $empId, $startDateTime, $endDateTime);
    mysqli_stmt_execute($stmt);
    $attendanceResult = mysqli_stmt_get_result($stmt);

    if (!$attendanceResult) {
        echo "Error in attendance query for employee $empId: " . mysqli_error($con) . "\n";
        mysqli_stmt_close($stmt);
        continue;
    }

    $totalWorkingArr = [];
    $extraArr = [];
    $remainingArr = [];

    $emailBody = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2 style='color: #333;'>Weekly Attendance Report</h2>
        <p>Dear " . htmlspecialchars($empName) . ",</p>
        <p><strong>Department:</strong> " . htmlspecialchars($deptName) . "</p>
        <p><strong>Period:</strong> " . htmlspecialchars($previousMonday) . " to " . htmlspecialchars($previousSunday) . "</p>
        
        <table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
            <thead style='background-color: #f2f2f2;'>
                <tr>
                    <th>Date</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Late Status</th>
                    <th>Total Working Time</th>
                    <th>Extra/Remaining Time</th>
                </tr>
            </thead>
            <tbody>";

    if (mysqli_num_rows($attendanceResult) > 0) {
        while ($record = mysqli_fetch_assoc($attendanceResult)) {
            $date = $record['clock_in_time'] ? date('Y-m-d', strtotime($record['clock_in_time'])) : '-';
            $clockIn = $record['clock_in_time'] ? date('H:i:s', strtotime($record['clock_in_time'])) : '-';
            $clockOut = $record['clock_out_time'] ? date('H:i:s', strtotime($record['clock_out_time'])) : '-';

            $lateStatus = $record['late_status'] ?? '-';
            $lateColor = '';
            if (stripos($lateStatus, 'Half Day') !== false) {
                $lateColor = 'style="color:red;font-weight:bold;"';
            } elseif (stripos($lateStatus, 'Late') !== false) {
                $lateColor = 'style="color:red;"';
            }

            $totalTime = $record['total_working_time'] ?? '-';
            $extraTime = ($record['extra_or_remaining_time'] ?? '-') . ' ' . ($record['extra_or_remaining_label'] ?? '');

            if ($totalTime !== '-') $totalWorkingArr[] = $record['total_working_time'];

            if ($record['extra_or_remaining_label'] === 'Extra Time') {
                $extraArr[] = $record['extra_or_remaining_time'];
            } elseif ($record['extra_or_remaining_label'] === 'Remaining Time') {
                $remainingArr[] = $record['extra_or_remaining_time'];
            }

            $emailBody .= "
                <tr>
                    <td>$date</td>
                    <td>$clockIn</td>
                    <td>$clockOut</td>
                    <td $lateColor>" . htmlspecialchars($lateStatus) . "</td>
                    <td>" . htmlspecialchars($totalTime) . "</td>
                    <td>" . htmlspecialchars($extraTime) . "</td>
                </tr>";
        }

        // Extra note rows
        foreach ($extraArr as $et) {
            $emailBody .= "
            <tr>
                <td colspan='6' style='text-align:right; font-style:italic; color:green;'>Extra Time: $et</td>
            </tr>";
        }
        foreach ($remainingArr as $rt) {
            $emailBody .= "
            <tr>
                <td colspan='6' style='text-align:right; font-style:italic; color:#FF5733;'>Remaining Time: $rt</td>
            </tr>";
        }

        // Summary
        $emailBody .= "
            <tr>
                <td colspan='6' style='padding: 20px; background-color:#f9f9f9;'>
                    <strong>Total Working Time:</strong> " . sum_times($totalWorkingArr) . "<br>
                    <strong>Total Extra Time:</strong> " . sum_times($extraArr) . "<br>
                    <strong>Total Remaining Time:</strong> " . sum_times($remainingArr) . "
                </td>
            </tr>";
    } else {
        $emailBody .= "
            <tr>
                <td colspan='6' style='text-align:center; padding: 15px;'>No attendance records found for this period</td>
            </tr>";
    }

    $emailBody .= "
            </tbody>
        </table>
        <p style='margin-top: 20px;'>Regards,<br>HR</p>
    </body>
    </html>";

    $subject = "Weekly Attendance Report: {$previousMonday} to {$previousSunday}";
    $cc_emails = [];

    $emailSent = send_email($empEmail, $subject, $emailBody, $cc_emails);

    if ($emailSent) {
        echo "Email sent successfully to {$empName} ({$empEmail})\n";
    } else {
        echo "Failed to send email to {$empName} ({$empEmail})\n";
    }

    ob_flush();
    flush();
    mysqli_stmt_close($stmt);
}

mysqli_free_result($result);
mysqli_close($con);
ob_end_flush();
