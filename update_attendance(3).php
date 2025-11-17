<?php
include 'layouts/session.php';
include 'include/function.php';

$conn = connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $clock_in = mysqli_real_escape_string($conn, $_POST['clock_in']);
    $clock_out = mysqli_real_escape_string($conn, $_POST['clock_out']);

    // Combine date with times
    $clock_in_time = "$date $clock_in:00";
    $clock_out_time = empty($clock_out) ? null : "$date $clock_out:00";

    // Fetch office timings
    $office_timing_query = "SELECT login_time, relaxation_time, extra_fine_time, half_day_time, logout_time 
                           FROM office_timing WHERE id=1 LIMIT 1";
    $office_timing_result = mysqli_query($conn, $office_timing_query) or die(mysqli_error($conn));
    $office_timing_row = mysqli_fetch_assoc($office_timing_result);

 // Fetch office timings from the database
$office_start = strtotime("$date " . (!empty($office_timing_row['login_time']) ? $office_timing_row['login_time'] : '09:00:00'));
$relaxation_time = strtotime("$date " . (!empty($office_timing_row['relaxation_time']) ? $office_timing_row['relaxation_time'] : '09:05:00'));
$extra_fine_time = strtotime("$date " . (!empty($office_timing_row['extra_fine_time']) ? $office_timing_row['extra_fine_time'] : '09:15:00'));
$half_day_time = strtotime("$date " . (!empty($office_timing_row['half_day_time']) ? $office_timing_row['half_day_time'] : '10:30:00'));
$office_end = strtotime("$date " . (!empty($office_timing_row['logout_time']) ? $office_timing_row['logout_time'] : '18:30:00'));

// Employee login time
$login_timestamp = strtotime($clock_in_time);
$office_hours_seconds = $office_end - $office_start;

// Default values
$late_status = "On Time";
$status_color = "#05bb2c"; // Green
$fine_amount = 0;

// Determine late status & fine calculation
if ($login_timestamp > $relaxation_time) {
    if ($login_timestamp <= $extra_fine_time) {
        $late_status = "Late";
        $status_color = "#f65a03"; // Yellow
        $fine_amount = $office_timing_row['normal_fine'] ?? 0;
    } elseif ($login_timestamp <= $half_day_time) {
        $late_status = "Late (Extra Late)";
        $status_color = "#f70000"; // Orange
        $fine_amount = $office_timing_row['extra_fine'] ?? 0;
    } else {
        $late_status = "Half Day";
        $status_color = "#dc34ac"; // Red
    }
}

// Output result
// echo "Status: $late_status | Color: $status_color | Fine: ₹$fine_amount";

    // Time calculations
    $total_working_time = null;
    $extra_or_remaining_time = null;
    $extra_or_remaining_label = null;
    if ($clock_out_time) {
        $logout_timestamp = strtotime($clock_out_time);
        $total_working_seconds = $logout_timestamp - $login_timestamp;
        
        // Ensure working time can't be negative
        if ($total_working_seconds < 0) {
            $total_working_seconds = 0;
        }
        
        $total_working_time = gmdate("H:i:s", $total_working_seconds);
        $extra_or_remaining_seconds = $total_working_seconds - $office_hours_seconds;
        $extra_or_remaining_time = gmdate("H:i:s", abs($extra_or_remaining_seconds));
        $extra_or_remaining_label = $extra_or_remaining_seconds > 0 ? 'Extra Time' : 'Remaining Time';
    }

    // Update query with proper escaping
    $query = "UPDATE newuser_attendance SET 
        clock_in_time = '$clock_in_time',
        clock_out_time = " . ($clock_out_time ? "'$clock_out_time'" : "NULL") . ",
        late_status = '$late_status',
        status_color = '$status_color',
        total_working_time = " . ($total_working_time ? "'$total_working_time'" : "NULL") . ",
        extra_or_remaining_time = " . ($extra_or_remaining_time ? "'$extra_or_remaining_time'" : "NULL") . ",
        extra_or_remaining_label = " . ($extra_or_remaining_label ? "'$extra_or_remaining_label'" : "NULL") . ",
        updated_at = NOW()
        WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // Log error for debugging
        error_log("Error updating record: " . mysqli_error($conn));
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>