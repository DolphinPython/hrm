<?php
include 'layouts/session.php';
include 'include/function.php';

$conn = connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $date = $_POST['date'] ?? '';
    $clock_in = $_POST['clock_in'] ?? '';
    $clock_out = $_POST['clock_out'] ?? '';
    $employee_id = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;

    // Validate inputs
    if (empty($date) || empty($employee_id) || empty($clock_in)) {
        error_log("Invalid input data: date=$date, employee_id=$employee_id, clock_in=$clock_in");
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Invalid input data");
        exit();
    }

    // Combine date with times
    $clock_in_time = "$date $clock_in:00";
    $clock_out_time = empty($clock_out) ? null : "$date $clock_out:00";

    // Fetch office timings
    $office_timing_query = "SELECT login_time, relaxation_time, extra_fine_time, half_day_time, logout_time, normal_fine, extra_fine 
                           FROM office_timing WHERE id = 1 LIMIT 1";
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
    $status = 'present'; // Set to present when clock-in is provided
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

    // Check if record exists
    if ($id > 0) {
        // Update existing record
        $query = "UPDATE newuser_attendance SET 
                  clock_in_time = ?, 
                  clock_out_time = ?, 
                  status = ?, 
                  late_status = ?, 
                  status_color = ?, 
                  total_working_time = ?, 
                  extra_or_remaining_time = ?, 
                  extra_or_remaining_label = ?, 
                  updated_at = NOW()
                  WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            'ssssssssi',
            $clock_in_time,
            $clock_out_time,
            $status,
            $late_status,
            $status_color,
            $total_working_time,
            $extra_or_remaining_time,
            $extra_or_remaining_label,
            $id
        );
    } else {
        // Insert new record
        $query = "INSERT INTO newuser_attendance (
            user_id, clock_in_time, clock_out_time, status, 
            late_status, status_color, total_working_time, 
            extra_or_remaining_time, extra_or_remaining_label, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            'issssssss',
            $employee_id,
            $clock_in_time,
            $clock_out_time,
            $status,
            $late_status,
            $status_color,
            $total_working_time,
            $extra_or_remaining_time,
            $extra_or_remaining_label
        );
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        $error = $stmt->error;
        $stmt->close();
        error_log("Error executing query: $error");
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=" . urlencode($error));
        exit();
    }
} else {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Invalid request method");
    exit();
}

$conn->close();
?>