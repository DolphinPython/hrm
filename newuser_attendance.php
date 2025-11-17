<?php
include 'include/db.php';

// Capture POST data
$ipAddress = $_SERVER['REMOTE_ADDR']; // Capture actual client IP
$currentTime = date("Y-m-d H:i:s");
$conn = connect();

session_start();
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in.']);
    exit;
}

try {
    // Fetch office timings
    $officeTimingsSql = "SELECT * FROM office_timing WHERE id = 1 LIMIT 1";
    $officeTimingsResult = mysqli_query($conn, $officeTimingsSql);
    if ($officeTimingsResult && mysqli_num_rows($officeTimingsResult) > 0) {
        $officeTimingRow = mysqli_fetch_assoc($officeTimingsResult);
        $office_start = strtotime($officeTimingRow['login_time'] ?? "09:00 AM");
        $relaxation_time = strtotime($officeTimingRow['relaxation_time'] ?? "09:15 AM");
        $extra_fine_time = strtotime($officeTimingRow['extra_fine_time'] ?? "09:30 AM");
        $half_day_time = strtotime($officeTimingRow['half_day_time'] ?? "09:45 AM");
        $evening_half_time = strtotime($officeTimingRow['evening_half_time'] ?? "01:00 PM");
        $fourThirtyPM = strtotime(date("Y-m-d") . " 16:30:00");
        $office_end = strtotime($officeTimingRow['logout_time'] ?? "06:30 PM");
        $office_hours_seconds = $office_end - $office_start;
    } else {
        throw new Exception("Office timings not found.");
    }

    // Check if user logged in today
    $sql = "SELECT id, clock_in_time, clock_out_time FROM newuser_attendance WHERE user_id = ? AND DATE(created_at) = CURDATE() ORDER BY created_at DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $record = mysqli_fetch_assoc($result);

        // Check if user already logged out
        if (!empty($record['clock_out_time'])) {
            echo json_encode(['success' => false, 'message' => 'User already logged out.']);
            exit;
        }

        // If user is logged in, calculate logout details
        if ($record['clock_in_time']) {
            $clockInTime = strtotime($record['clock_in_time']);
            $clockOutTime = strtotime($currentTime);

            // Calculate total working time
            $totalWorkingSeconds = $clockOutTime - $clockInTime;
            $totalWorkingTime = gmdate("H:i:s", $totalWorkingSeconds);

            // Calculate extra or remaining time
            $extraOrRemainingSeconds = $totalWorkingSeconds - $office_hours_seconds;
            $extraOrRemainingTime = gmdate("H:i:s", abs($extraOrRemainingSeconds));
            $extraOrRemainingLabel = $extraOrRemainingSeconds > 0 ? 'Extra Time' : 'Remaining Time';

            // Fetch existing late_status and status_color
            $fetchSql = "SELECT late_status, status_color FROM newuser_attendance WHERE id = ?";
            $fetchStmt = mysqli_prepare($conn, $fetchSql);
            mysqli_stmt_bind_param($fetchStmt, "i", $record['id']);
            mysqli_stmt_execute($fetchStmt);
            $fetchResult = mysqli_stmt_get_result($fetchStmt);
            $fetchRow = mysqli_fetch_assoc($fetchResult);
            $lateStatus = $fetchRow['late_status'];
            $statusColor = $fetchRow['status_color'];

            // Apply evening half-day logic
            if ($clockOutTime >= $evening_half_time && $clockOutTime <= $fourThirtyPM) {
                $lateStatus = "Half Day";
                $statusColor = "red";
            }

            // Update record
            $updateSql = "
                UPDATE newuser_attendance
                SET clock_out_time = ?, 
                    clock_out_ip = ?, 
                    status = ?, 
                    total_working_time = ?, 
                    extra_or_remaining_time = ?, 
                    extra_or_remaining_label = ?,
                    late_status = ?,
                    status_color = ?
                WHERE id = ?
            ";
            $stmt = mysqli_prepare($conn, $updateSql);
            $status = 'logout';
            mysqli_stmt_bind_param($stmt, "ssssssssi", $currentTime, $ipAddress, $status, $totalWorkingTime, $extraOrRemainingTime, $extraOrRemainingLabel, $lateStatus, $statusColor, $record['id']);
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User logged out successfully.',
                    'total_working_time' => $totalWorkingTime,
                    'extra_or_remaining_time' => $extraOrRemainingTime,
                    'extra_or_remaining_label' => $extraOrRemainingLabel,
                    'late_status' => $lateStatus
                ]);
            } else {
                throw new Exception("Error updating attendance: " . mysqli_error($conn));
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User has no clock-in time.']);
        }
    } else {
        // New login record
        $currentTimestamp = strtotime($currentTime);
        $lateStatus = "On Time";
        $statusColor = "green";

        if ($currentTimestamp > $half_day_time) {
            $lateStatus = "Late (Half Day)";
            $statusColor = "red";
        } elseif ($currentTimestamp > $extra_fine_time) {
            $lateStatus = "Late (Extra Fine)";
            $statusColor = "red";
        } elseif ($currentTimestamp > $relaxation_time) {
            $lateStatus = "Late";
            $statusColor = "orange";
        }

        $insertSql = "
            INSERT INTO newuser_attendance (user_id, clock_in_time, clock_in_ip, status, created_at, late_status, status_color)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = mysqli_prepare($conn, $insertSql);
        $status = 'login';
        mysqli_stmt_bind_param($stmt, "issssss", $userId, $currentTime, $ipAddress, $status, $currentTime, $lateStatus, $statusColor);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode([
                'success' => true,
                'message' => 'User logged in successfully.',
                'late_status' => $lateStatus
            ]);
        } else {
            throw new Exception("Error inserting attendance record: " . mysqli_error($conn));
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>