<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');

include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';
include 'email/mailer.php'; // Include mailer.php for email sending

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <title>Leaves Applying</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Leaves</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Leaves</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="alert alert-info text-center" role="alert">
                            <strong>Processing.......</strong> 
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>


        </div>
    </div>

<?php

// Get user name and other details
$emp_id = $_SESSION['id'] ?? null;
$emp_session_id = $_SESSION['id'] ?? null;

if ($emp_id === null || $emp_session_id === null) {
    die("Session ID is not set.");
}

$conn = connect();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
$row = mysqli_fetch_array($result) ?? [];

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . ($row['image'] ?? 'default_image.jpg');

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

// Check if Casual Leave is already applied this month
$current_month = date('Y-m');
$casual_leave_query = "SELECT COUNT(*) as casual_count FROM hrm_leave_applied WHERE emp_id='$emp_session_id' AND leave_type_id=1 AND DATE_FORMAT(start_date, '%Y-%m')='$current_month'";
$casual_leave_result = mysqli_query($conn, $casual_leave_query);
if (!$casual_leave_result) {
    die("Casual leave query failed: " . mysqli_error($conn));
}
$casual_leave_row = mysqli_fetch_array($casual_leave_result);
$casual_leave_applied = $casual_leave_row['casual_count'] > 0;

// Handle leave application form submission
if (isset($_POST['b1_leave'])) {
    $leave_type_id = mysqli_real_escape_string($conn, $_POST['leave_type_id'] ?? '');
    $day_type = mysqli_real_escape_string($conn, $_POST['day_type'] ?? '');
    $from_date = mysqli_real_escape_string($conn, $_POST['from_date'] ?? '');
    $to_date = mysqli_real_escape_string($conn, $_POST['to_date'] ?? '');
    $no_of_days = mysqli_real_escape_string($conn, $_POST['no_of_days'] ?? '');
    $leave_reason = mysqli_real_escape_string($conn, $_POST['leave_reason'] ?? '');

    // Server-side validation
    if (empty($leave_type_id) || empty($day_type) || empty($from_date) || empty($to_date) || empty($no_of_days) || empty($leave_reason)) {
        echo "<script>alert('All fields are required!');</script>";
    } else {
        // Validate Short Leave
        if ($leave_type_id == 4) { // Short Leave
            if ($day_type != 3) { // Must be Short Leave day type
                echo "<script>alert('Short Leave must have Day Type set to Short Leave!');</script>";
                exit;
            }
            $from_date_obj = new DateTime($from_date);
            $to_date_obj = new DateTime($to_date);
            $interval = $from_date_obj->diff($to_date_obj);
            $hours = $interval->h + ($interval->days * 24);
            if ($hours != 2 || $no_of_days != '0.25') { // Ensure 2-hour duration and 0.25 days
                echo "<script>alert('Short Leave must be exactly 2 hours (0.25 days)!');</script>";
                exit;
            }
        }

        $from_date = date('Y-m-d H:i:s', strtotime($from_date));
        $to_date = date('Y-m-d H:i:s', strtotime($to_date));

        $emp_name = get_value("hrm_employee", "fname", $emp_id);
        $emp_email = get_value("hrm_employee", "email", $emp_session_id);

        $query = "INSERT INTO hrm_leave_applied (leave_type_id, day_type, start_date, end_date, no_of_days, leave_reason, emp_id) VALUES ('$leave_type_id', '$day_type', '$from_date', '$to_date', '$no_of_days', '$leave_reason', '$emp_session_id')";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Insert query failed: " . mysqli_error($conn));
        }

        if ($leave_type_id == 1) {
            $leave_type_name = "Casual Leave 1 Days";
        } elseif ($leave_type_id == 3) {
            $leave_type_name = "Loss of Pay";
        } elseif ($leave_type_id == 4) {
            $leave_type_name = "Short Leave";
        } else {
            $leave_type_name = "Unknown";
        }

        if ($day_type == 1) {
            $day_type_name = "Half Day";
        } elseif ($day_type == 2) {
            $day_type_name = "Full Day";
        } elseif ($day_type == 3) {
            $day_type_name = "Short Leave";
        } else {
            $day_type_name = "Unknown";
        }

        // Clean and normalize leave_reason
        $clean_leave_reason = stripcslashes($leave_reason); // Remove escaped backslashes
        $normalized_leave_reason = str_replace(array("\r\n", "\r", "\n"), "\n", $clean_leave_reason); // Normalize line endings
        $formatted_leave_reason = nl2br(htmlspecialchars($normalized_leave_reason, ENT_QUOTES, 'UTF-8')); // Convert to HTML

        // Prepare email content
        $subject = 'HRM->Leave Applied By ' . $emp_name;
        $message = "<html>\n<head>\n<meta charset=\"UTF-8\">\n</head>\n<body>\n";
        $message .= '<p><strong>Leave Type</strong> - ' . htmlspecialchars($leave_type_name, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
        $message .= '<p><strong>Day Type</strong> - ' . htmlspecialchars($day_type_name, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
        $message .= '<p><strong>Start Date</strong> - ' . htmlspecialchars(date('d-m-Y H:i', strtotime($from_date)), ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
        $message .= '<p><strong>End Date</strong> - ' . htmlspecialchars(date('d-m-Y H:i', strtotime($to_date)), ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
        $message .= '<p><strong>No. Of Days</strong> - ' . htmlspecialchars($no_of_days, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
        $message .= '<p><strong>Leave Reason</strong> - <br> <br>' . htmlspecialchars($formatted_leave_reason, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
        $message .= "</body>\n</html>";

        // Debug: Log the leave reason processing
        error_log("Raw leave_reason: " . $leave_reason);
        error_log("Clean leave_reason: " . $clean_leave_reason);
        error_log("Normalized leave_reason: " . $normalized_leave_reason);
        error_log("Formatted leave_reason: " . $formatted_leave_reason);

        // Send email using mailer.php
        $cc_emails = [$emp_email, 'pythondolphin@gmail.com', 'shivam@1solutions.biz'];
        $mail_success = send_email('hr@1solutions.biz', $subject, $message, $cc_emails);

        if ($mail_success) {
            // echo "<script>alert('Leave Applied Successfully!'); window.location.href='leaves-employee.php';</script>";
            echo "<script>window.location.href='leaves-employee.php';</script>";
        } else {
            echo "<script>alert('Leave applied, but failed to send email. Please check server configuration.');</script>";
        }
    }
}

?>


<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>

</body>
</html>