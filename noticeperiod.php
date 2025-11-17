<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php'; ?>
<?php include 'layouts/config.php'; ?>
<?php require './email/mailer.php'; ?>
<?php
$conn = $con;
$emp_id = $_SESSION['id'];

// Fetch admin/HR details
$query = "SELECT * FROM hrm_employee WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Handle employee selection
$selected_employee_id = $_GET['employee_id'] ?? null;
$view_history = isset($_GET['view_history']) && $_GET['view_history'] == 1;

// Handle resignation status or notice period update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_resignation']) && $selected_employee_id) {
    $resignation_id = $_POST['resignation_id'] ?? 0;
    $status = $_POST['resignation_status'] ?? 'Pending';
    $decline_reason = ($status === 'Declined') ? trim($_POST['decline_reason'] ?? '') : null;
    $notice_period_days = $_POST['notice_period_days'] ?? 15;
    if ($status !== 'Pending') {
        $approved_at = date("Y-m-d H:i:s");
        error_log("Setting approved_at to $approved_at for resignation_id $resignation_id");
    } else {
        $approved_at = null;
        error_log("Setting approved_at to NULL for resignation_id $resignation_id");
    }
    $updated_at = date("Y-m-d H:i:s");

    if ($status === 'Declined' && empty($decline_reason)) {
        $error_message = "Error: A reason is required when declining a resignation.";
    } else {
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Fetch current resignation details to detect changes
            $current_query = "SELECT status, notice_period_days FROM employee_resignations WHERE id = ? AND employee_id = ?";
            $current_stmt = $conn->prepare($current_query);
            $current_stmt->bind_param("ii", $resignation_id, $selected_employee_id);
            $current_stmt->execute();
            $current_result = $current_stmt->get_result();
            $current = $current_result->fetch_assoc();

            // Fetch employee details for email
            $emp_query = "SELECT fname, lname, email FROM hrm_employee WHERE id = ?";
            $emp_stmt = $conn->prepare($emp_query);
            $emp_stmt->bind_param("i", $selected_employee_id);
            $emp_stmt->execute();
            $emp_result = $emp_stmt->get_result();
            $employee = $emp_result->fetch_assoc();

            // Update resignation
            $sql = "UPDATE employee_resignations SET status = ?, notice_period_days = ?, decline_reason = ?, approved_by = ?, approved_at = ?, updated_at = ? 
                    WHERE id = ? AND employee_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sissisii", $status, $notice_period_days, $decline_reason, $emp_id, $approved_at, $updated_at, $resignation_id, $selected_employee_id);
            $stmt->execute();

            // Log changes to resignation_history and send emails
            if ($current['status'] !== $status) {
                $history_sql = "INSERT INTO resignation_history (resignation_id, employee_id, status, notice_period_days, changed_by, changed_at, comment) 
                                VALUES (?, ?, ?, ?, ?, NOW(), ?)";
                $history_stmt = $conn->prepare($history_sql);
                $comment = $status === 'Declined' ? "Status changed to Declined: $decline_reason" : "Status changed to $status";
                $history_stmt->bind_param("iisiis", $resignation_id, $selected_employee_id, $status, $notice_period_days, $emp_id, $comment);
                $history_stmt->execute();

                // Send email notification for status change
                $to = 'hr@1solutions.biz';
                $cc_emails = ['pythondolphin@gmail.com','dolphinpython@outlook.com', $employee['email']];
                $subject = "Resignation Status Updated for {$employee['fname']} {$employee['lname']}";
                $message = "
                    <h3>Resignation Status Update</h3>
                    <p><strong>Employee:</strong> {$employee['fname']} {$employee['lname']} (ID: {$selected_employee_id})</p>
                    <p><strong>Email:</strong> {$employee['email']}</p>
                    <p><strong>New Status:</strong> {$status}</p>
                    <p><strong>Notice Period:</strong> {$notice_period_days} days</p>
                    <p><strong>Updated By:</strong> {$admin['fname']} {$admin['lname']}</p>
                    <p><strong>Updated On:</strong> " . date("d M Y, H:i") . "</p>
                    <p><strong>Comment:</strong> {$comment}</p>
                    <p>Please check the Notice Period Management System for details.</p>
                ";
                try {
                    if (!send_email($to, $subject, $message, $cc_emails)) {
                        throw new Exception("Failed to send status update email for employee ID: {$selected_employee_id}");
                    }
                } catch (Exception $e) {
                    error_log("Email Error (Status Update): " . $e->getMessage());
                }
            }
            if ($current['notice_period_days'] != $notice_period_days) {
                $history_sql = "INSERT INTO resignation_history (resignation_id, employee_id, status, notice_period_days, changed_by, changed_at, comment) 
                                VALUES (?, ?, ?, ?, ?, NOW(), ?)";
                $history_stmt = $conn->prepare($history_sql);
                $comment = "Notice period changed to $notice_period_days days";
                $history_stmt->bind_param("iisiis", $resignation_id, $selected_employee_id, $status, $notice_period_days, $emp_id, $comment);
                $history_stmt->execute();

                // Send email notification for notice period change
                $to = 'hr@1solutions.biz';
                $cc_emails = ['pythondolphin@gmail.com','dolphinpython@outlook.com', $employee['email']];
                $subject = "Notice Period Updated for {$employee['fname']} {$employee['lname']}";
                $message = "
                    <h3>Notice Period Update</h3>
                    <p><strong>Employee:</strong> {$employee['fname']} {$employee['lname']} (ID: {$selected_employee_id})</p>
                    <p><strong>Email:</strong> {$employee['email']}</p>
                    <p><strong>Status:</strong> {$status}</p>
                    <p><strong>New Notice Period:</strong> {$notice_period_days} days</p>
                    <p><strong>Updated By:</strong> {$admin['fname']} {$admin['lname']}</p>
                    <p><strong>Updated On:</strong> " . date("d M Y, H:i") . "</p>
                    <p><strong>Comment:</strong> {$comment}</p>
                    <p>Please check the Notice Period Management System for details.</p>
                ";
                try {
                    if (!send_email($to, $subject, $message, $cc_emails)) {
                        throw new Exception("Failed to send notice period update email for employee ID: {$selected_employee_id}");
                    }
                } catch (Exception $e) {
                    error_log("Email Error (Notice Period Update): " . $e->getMessage());
                }
            }

            // If declined, delete notice period steps
            if ($status === 'Declined') {
                $delete_steps_sql = "DELETE FROM employee_notice_period_steps WHERE employee_id = ?";
                $delete_steps_stmt = $conn->prepare($delete_steps_sql);
                $delete_steps_stmt->bind_param("i", $selected_employee_id);
                $delete_steps_stmt->execute();
            }

            // Commit transaction
            $conn->commit();

            header("Location: " . $_SERVER['PHP_SELF'] . "?employee_id=" . $selected_employee_id);
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error updating resignation: " . $e->getMessage());
            $error_message = "Error updating resignation: " . $e->getMessage();
        }
    }
}

// Auto-populate and sync steps for the selected employee
if ($selected_employee_id && !$view_history) {
    $all_steps_sql = "SELECT step_id FROM notice_period_steps ORDER BY step_order ASC";
    $all_steps_result = $conn->query($all_steps_sql);
    $all_steps = $all_steps_result->fetch_all(MYSQLI_ASSOC);

    $existing_steps_sql = "SELECT step_id FROM employee_notice_period_steps WHERE employee_id = ?";
    $existing_stmt = $conn->prepare($existing_steps_sql);
    $existing_stmt->bind_param("i", $selected_employee_id);
    $existing_stmt->execute();
    $existing_result = $existing_stmt->get_result();
    $existing_steps = array_column($existing_result->fetch_all(MYSQLI_ASSOC), 'step_id');

    $missing_steps = array_diff(array_column($all_steps, 'step_id'), $existing_steps);

    if (!empty($missing_steps)) {
        $insert_sql = "INSERT INTO employee_notice_period_steps (employee_id, step_id, status, comment, created_at) 
                       VALUES (?, ?, 0, '', NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        foreach ($missing_steps as $step_id) {
            $insert_stmt->bind_param("ii", $selected_employee_id, $step_id);
            $insert_stmt->execute();
            error_log("Inserted step_id $step_id for employee_id $selected_employee_id in noticeperiod.php");
        }
    }
}

// Handle form submission for step updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && $selected_employee_id && !$view_history) {
    $id = $_POST['id'];
    $completed = isset($_POST['completed']) ? 1 : 0;
    $comment = $_POST['comment'] ?? '';
    $update_date = date("Y-m-d H:i:s");

    // Update employee_notice_period_steps
    $sql = "UPDATE employee_notice_period_steps SET status = ?, comment = ?, update_date = ? 
            WHERE id = ? AND employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issii", $completed, $comment, $update_date, $id, $selected_employee_id);
    if (!$stmt->execute()) {
        error_log("Failed to update employee_notice_period_steps for id $id: " . $stmt->error);
    }

    // Fetch step_id
    $step_sql = "SELECT step_id FROM employee_notice_period_steps WHERE id = ?";
    $step_stmt = $conn->prepare($step_sql);
    $step_stmt->bind_param("i", $id);
    $step_stmt->execute();
    $step_result = $step_stmt->get_result();
    $step_row = $step_result->fetch_assoc();
    $step_id = $step_row['step_id'] ?? null;

    if (!$step_id) {
        error_log("Invalid step_id for employee_notice_period_steps id $id");
    } else {
        // Handle file uploads
        $upload_dir = "Uploads/notice_period/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
            error_log("Created upload directory: $upload_dir");
        }
        if (!empty($_FILES['files']['name'][0])) {
            foreach ($_FILES['files']['name'] as $key => $fileName) {
                if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['files']['tmp_name'][$key];
                    $filePath = $upload_dir . $selected_employee_id . "_" . time() . "_" . basename($fileName);
                    if (move_uploaded_file($fileTmp, $filePath)) {
                        $insertFile = "INSERT INTO notice_period_files (step_id, employee_id, document_name, file_path, created_at) 
                                       VALUES (?, ?, ?, ?, NOW())";
                        $stmtFile = $conn->prepare($insertFile);
                        $stmtFile->bind_param("iiss", $step_id, $selected_employee_id, $fileName, $filePath);
                        if ($stmtFile->execute()) {
                            error_log("Successfully inserted file $fileName for step_id $step_id, employee_id $selected_employee_id");
                        } else {
                            error_log("Failed to insert file $fileName into notice_period_files: " . $stmtFile->error);
                        }
                    } else {
                        error_log("Failed to move uploaded file $fileName to $filePath");
                    }
                } else {
                    error_log("File upload error for $fileName: " . $_FILES['files']['error'][$key]);
                }
            }
        } else {
            error_log("No files uploaded for step_id $step_id, employee_id $selected_employee_id");
        }
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?employee_id=" . $selected_employee_id);
    exit();
}

// Handle file deletion
if (isset($_GET['delete_file']) && $selected_employee_id && !$view_history) {
    $file_id = $_GET['delete_file'];
    $sql = "SELECT file_path FROM notice_period_files WHERE id = ? AND employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $file_id, $selected_employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();

    if ($file && unlink($file['file_path'])) {
        $deleteSql = "DELETE FROM notice_period_files WHERE id = ? AND employee_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $file_id, $selected_employee_id);
        $deleteStmt->execute();
        error_log("Deleted file ID $file_id for employee_id $selected_employee_id");
    } else {
        error_log("Failed to delete file ID $file_id for employee_id $selected_employee_id");
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?employee_id=" . $selected_employee_id);
    exit();
}

// Fetch employees with active resignations
$emp_query = "
    SELECT e.id, e.fname, e.lname, e.email, e.doj, r.id AS resignation_id, r.status 
    FROM hrm_employee e 
    JOIN employee_resignations r ON e.id = r.employee_id 
    WHERE r.status != 'Declined' 
    GROUP BY e.id
";
/* Alternative query to include employees with any resignation history (uncomment if needed)
$emp_query = "
    SELECT DISTINCT e.id, e.fname, e.lname, e.email, e.doj, r.id AS resignation_id, r.status 
    FROM hrm_employee e 
    LEFT JOIN employee_resignations r ON e.id = r.employee_id 
    LEFT JOIN resignation_history rh ON r.id = rh.resignation_id 
    WHERE r.id IS NOT NULL OR rh.resignation_id IS NOT NULL 
    GROUP BY e.id
";
*/
$emp_result = $conn->query($emp_query);
$employees = $emp_result->fetch_all(MYSQLI_ASSOC);
error_log("Employee query returned " . count($employees) . " employees");
foreach ($employees as $emp) {
    error_log("Employee in dropdown: ID={$emp['id']}, Name={$emp['fname']} {$emp['lname']}, Status={$emp['status']}");
}

// Fetch all notice period steps and resignation details for the selected employee
$steps = [];
$resignation = null;
$history = [];
if ($selected_employee_id && !$view_history) {
    $sql = "SELECT enps.id, enps.step_id, nps.step_name, nps.description, enps.status, enps.comment, enps.update_date, enps.created_at 
            FROM employee_notice_period_steps enps 
            JOIN notice_period_steps nps ON enps.step_id = nps.step_id 
            WHERE enps.employee_id = ? 
            ORDER BY nps.step_order ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $steps = $result->fetch_all(MYSQLI_ASSOC);

    // Fetch resignation details
    $res_query = "SELECT * FROM employee_resignations WHERE employee_id = ? AND status != 'Declined'";
    $res_stmt = $conn->prepare($res_query);
    $res_stmt->bind_param("i", $selected_employee_id);
    $res_stmt->execute();
    $res_result = $res_stmt->get_result();
    $resignation = $res_result->fetch_assoc();

    // Fetch resignation history
    $history_query = "SELECT rh.*, e.fname, e.lname 
                     FROM resignation_history rh 
                     LEFT JOIN hrm_employee e ON rh.changed_by = e.id 
                     WHERE rh.employee_id = ? 
                     ORDER BY rh.changed_at DESC";
    $history_stmt = $conn->prepare($history_query);
    $history_stmt->bind_param("i", $selected_employee_id);
    $history_stmt->execute();
    $history_result = $history_stmt->get_result();
    $history = $history_result->fetch_all(MYSQLI_ASSOC);
}

// Fetch all resignation history for all employees
$all_history = [];
if ($view_history) {
    $all_history_query = "SELECT r.id AS resignation_id, r.employee_id, r.status, r.notice_period_days, r.submitted_at, r.updated_at, 
                         r.approved_at, r.resignation_reason, r.decline_reason, r.intended_last_date, e.fname, e.lname, rh.changed_at, rh.comment, 
                         rh.status AS history_status, rh.notice_period_days AS history_notice_period, 
                         cb.fname AS changed_by_fname, cb.lname AS changed_by_lname 
                         FROM employee_resignations r 
                         JOIN hrm_employee e ON r.employee_id = e.id 
                         LEFT JOIN resignation_history rh ON r.id = rh.resignation_id 
                         LEFT JOIN hrm_employee cb ON rh.changed_by = cb.id 
                         ORDER BY r.submitted_at DESC, rh.changed_at DESC";
    $all_history_result = $conn->query($all_history_query);
    $all_history = $all_history_result->fetch_all(MYSQLI_ASSOC);
}

// Fetch admin details (securely)
$query = "SELECT * FROM hrm_employee WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array(MYSQLI_ASSOC);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;
$maxDate = date('Y-m-d', strtotime('-18 years'));
$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Notice Period</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-wrapper {
            min-height: 100vh;
        }
        .page-wrapper {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-title {
            color: #1a3c34;
            font-weight: 600;
        }
        .btn-primary, .btn-success, .btn-info, .btn-warning {
            border-radius: 5px;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-info:hover {
            background-color: #138496;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }
        .accordion-button {
            background-color: rgb(246, 4, 0) !important;
            color: white !important;
            border-radius: 5px;
        }
        .accordion-button.submitted {
            background-color: #ffc1cc !important;
            color: #000 !important;
        }
        .accordion-button.completed {
            background-color: #28a745 !important;
        }
        .accordion-button:not(.collapsed) {
            background-color: #0056b3 !important;
        }
        .accordion-button.submitted:not(.collapsed) {
            background-color: #ffb3c1 !important;
        }
        .accordion-button.completed:not(.collapsed) {
            background-color: #218838 !important;
        }
        .progress {
            height: 25px;
            background-color: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(45deg, #00cc00, #28a745);
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: width 0.6s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .update-date {
            font-size: 12px;
            color: #6c757d;
            float: right;
            font-style: italic;
        }
        .icon-btn {
            cursor: pointer;
            font-size: 18px;
            margin-left: 10px;
        }
        .file-list {
            margin-top: 10px;
        }
        #incompleteSteps {
            display: none;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .search-field {
            flex: 1;
            min-width: 150px;
        }
        .resignation-info {
            margin-bottom: 20px;
        }
        .form-select {
            display: inline-block;
            width: auto;
            margin-right: 10px;
        }
        .history-table {
            margin-top: 20px;
        }
        .table {
            font-size: 14px;
            width: 100% !important;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 12px;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }
        .dataTables_wrapper {
            padding: 10px;
        }
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        .dataTables_wrapper .dataTables_length select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        .dataTables_scrollBody {
            border-bottom: 1px solid #dee2e6 !important;
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
            .form-select {
                width: 100%;
            }
            .search-row {
                flex-direction: column;
            }
            .dataTables_wrapper .dataTables_filter input,
            .dataTables_wrapper .dataTables_length select {
                width: 100%;
                margin-bottom: 10px;
            }
        }
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .modal-header {
            background-color: #007bff;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .modal-title {
            font-weight: 600;
        }
        /* Remove vibration-causing transitions */
        .modal, .modal-dialog, .modal-content {
            transition: none !important;
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="container padding-top-ams">
            <div class="container mt-5">
                <h1 class="mb-4 text-primary">Employee Notice Period Management System</h1>
                <ul class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notice Period Management System</li>
                </ul>

                <!-- Display Error Message -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <!-- Employee Search and Selection -->
                <div class="search-container">
                    <div class="row align-items-end mt-3">
                        <div class="col-md-8 col-12">
                            <label for="selectEmployee" class="form-label">Select Employee:</label>
                            <select class="form-select" id="selectEmployee" name="employee_id">
                                <option value="">Select an employee</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id']; ?>" 
                                            data-id="<?= $emp['id']; ?>" 
                                            data-fname="<?= strtolower($emp['fname']); ?>" 
                                            data-lname="<?= strtolower($emp['lname']); ?>" 
                                            data-email="<?= strtolower($emp['email']); ?>" 
                                            data-doj="<?= strtolower($emp['doj']); ?>"
                                            <?= $selected_employee_id == $emp['id'] ? 'selected' : ''; ?>>
                                        <?= $emp['id'] . ' - ' . $emp['fname'] . ' ' . $emp['lname'] . ' (' . $emp['status'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 col-12 mt-2 mt-md-0">
                            <button class="btn btn-success w-100" id="viewNoticePeriodButton"><i class="fas fa-eye"></i> View Notice Period</button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="noticeperiodtest.php" class="btn btn-primary"><i class="fas fa-cog"></i> Manage Notice Period Steps</a>
                        <a href="?view_history=1" class="btn btn-info"><i class="fas fa-history"></i> View All Resignation History</a>
                    </div>
                </div>

                <?php if ($view_history): ?>
                    <!-- All Resignation History -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">All Resignation History</h5>
                            <?php if (!empty($all_history)): ?>
                                <div class="history-table">
                                    <table id="historyTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Employee</th>
                                                <th style="width: 8%;">Resignation ID</th>
                                                <th style="width: 8%;">Status</th>
                                                <th style="width: 8%;">Notice Period</th>
                                                <th style="width: 12%;">Reason</th>
                                                <th style="width: 10%;">Intended Last Date</th>
                                                <th style="width: 10%;">Submitted On</th>
                                                <th style="width: 10%;">Last Updated On</th>
                                                <th style="width: 10%;">Processed On</th>
                                                <th style="width: 10%;">History Change</th>
                                                <th style="width: 8%;">Changed By</th>
                                                <th style="width: 16%;">Comment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($all_history as $entry): ?>
                                                <tr>
                                                    <td><?= $entry['fname'] . ' ' . $entry['lname']; ?></td>
                                                    <td><?= $entry['resignation_id']; ?></td>
                                                    <td><?= $entry['status']; ?></td>
                                                    <td><?= $entry['notice_period_days']; ?> days</td>
                                                    <td>
                                                        <?= htmlspecialchars($entry['resignation_reason']); ?>
                                                        <?php if ($entry['status'] === 'Declined' && $entry['decline_reason']): ?>
                                                            <br><strong>Decline Reason:</strong> <?= htmlspecialchars($entry['decline_reason']); ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $entry['intended_last_date']; ?></td>
                                                    <td><?= date("d M Y, H:i", strtotime($entry['submitted_at'])); ?></td>
                                                    <td><?= $entry['updated_at'] ? date("d M Y, H:i", strtotime($entry['updated_at'])) : '-'; ?></td>
                                                    <td><?= $entry['approved_at'] && strtotime($entry['approved_at']) !== false ? date("d M Y, H:i", strtotime($entry['approved_at'])) : '-'; ?></td>
                                                    <td>
                                                        <?= $entry['history_status'] ? 
                                                            'Status: ' . $entry['history_status'] . ', Notice: ' . ($entry['history_notice_period'] ? $entry['history_notice_period'] . ' days' : '-') : 
                                                            'Initial Submission'; ?>
                                                        <?= $entry['changed_at'] ? '<br>' . date("d M Y, H:i", strtotime($entry['changed_at'])) : ''; ?>
                                                    </td>
                                                    <td><?= $entry['changed_by_fname'] ? $entry['changed_by_fname'] . ' ' . $entry['changed_by_lname'] : 'System'; ?></td>
                                                    <td><?= htmlspecialchars($entry['comment'] ?? '-'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">No resignation history available.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($selected_employee_id && $resignation): ?>
                    <div class="card resignation-info">
                        <div class="card-body">
                            <h5 class="card-title">Resignation Details</h5>
                            <p><strong>Reason:</strong> <?= htmlspecialchars($resignation['resignation_reason']); ?></p>
                            <p><strong>Intended Last Working Date:</strong> <?= $resignation['intended_last_date']; ?></p>
                            <p><strong>Submitted On:</strong> <?= date("d M Y, H:i", strtotime($resignation['submitted_at'])); ?></p>
                            <?php if ($resignation['updated_at']): ?>
                                <p><strong>Last Updated On:</strong> <?= date("d M Y, H:i", strtotime($resignation['updated_at'])); ?></p>
                            <?php endif; ?>
                            <form action="" method="POST" class="d-inline" id="resignationStatusForm">
                                <input type="hidden" name="update_resignation" value="1">
                                <input type="hidden" name="resignation_id" value="<?= $resignation['id']; ?>">
                                <label for="resignation_status" class="form-label">Resignation Status:</label>
                                <select name="resignation_status" id="resignation_status" class="form-select">
                                    <option value="Pending" <?= $resignation['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Approved" <?= $resignation['status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="Declined" <?= $resignation['status'] === 'Declined' ? 'selected' : ''; ?>>Declined</option>
                                </select>
                                <label for="notice_period_days" class="form-label">Notice Period:</label>
                                <select name="notice_period_days" class="form-select" onchange="this.form.submit()">
                                    <option value="15" <?= $resignation['notice_period_days'] == 15 ? 'selected' : ''; ?>>15 Days</option>
                                    <option value="30" <?= $resignation['notice_period_days'] == 30 ? 'selected' : ''; ?>>30 Days</option>
                                </select>
                            </form>
                            <?php if ($resignation['status'] !== 'Pending' && !empty($resignation['approved_at']) && strtotime($resignation['approved_at']) !== false): ?>
                                <p><strong>Processed On:</strong> <?= date("d M Y, H:i", strtotime($resignation['approved_at'])); ?></p>
                            <?php else: ?>
                                <p><strong>Processed On:</strong> Not yet processed</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Decline Reason Modal -->
                    <div class="modal fade" id="declineReasonModal" tabindex="-1" aria-labelledby="declineReasonModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="declineReasonModalLabel">Reason for Declining Resignation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="declineReasonForm" action="" method="POST">
                                        <input type="hidden" name="update_resignation" value="1">
                                        <input type="hidden" name="resignation_id" value="<?= $resignation['id']; ?>">
                                        <input type="hidden" name="resignation_status" value="Declined">
                                        <input type="hidden" name="notice_period_days" value="<?= $resignation['notice_period_days']; ?>">
                                        <div class="mb-3">
                                            <label for="decline_reason" class="form-label">Decline Reason</label>
                                            <textarea class="form-control" id="decline_reason" name="decline_reason" rows="5" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resignation History -->
                    <div class="card history-table">
                        <div class="card-body">
                            <h5 class="card-title">Resignation History</h5>
                            <?php if (!empty($history)): ?>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Notice Period</th>
                                            <th>Changed By</th>
                                            <th>Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($history as $entry): ?>
                                            <tr>
                                                <td><?= date("d M Y, H:i", strtotime($entry['changed_at'])); ?></td>
                                                <td><?= $entry['status'] ?? '-'; ?></td>
                                                <td><?= $entry['notice_period_days'] ? $entry['notice_period_days'] . ' days' : '-'; ?></td>
                                                <td><?= $entry['changed_by'] ? $entry['fname'] . ' ' . $entry['lname'] : 'System'; ?></td>
                                                <td><?= htmlspecialchars($entry['comment'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info">No resignation history available.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($steps)): ?>
                        <!-- Progress Bar -->
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" id="progressBar"></div>
                        </div>

                        <!-- Incomplete Steps Toggle -->
                        <button class="btn btn-warning mt-3" id="toggleIncomplete"><i class="fas fa-filter"></i> Show Incomplete Steps</button>
                        <div class="accordion mt-3" id="incompleteSteps">
                            <?php foreach ($steps as $index => $step): ?>
                                <?php if (!$step['status']): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" 
                                                    type="button" data-bs-toggle="collapse" 
                                                    data-bs-target="#incomplete<?= $index; ?>">
                                                <?= $step['step_name']; ?>
                                            </button>
                                        </h2>
                                        <div id="incomplete<?= $index; ?>" class="accordion-collapse collapse">
                                            <div class="accordion-body">
                                                <p><strong>Description:</strong> <?= $step['description']; ?></p>
                                                <form action="" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="id" value="<?= $step['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" name="completed" <?= $step['status'] ? 'checked' : ''; ?>> Mark as Completed
                                                        </label>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="comment<?= $index; ?>" class="form-label">Comment</label>
                                                        <textarea class="form-control" name="comment" id="comment<?= $index; ?>" rows="3"><?= htmlspecialchars($step['comment']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="files<?= $index; ?>" class="form-label">Upload Files</label>
                                                        <input type="file" class="form-control" name="files[]" id="files<?= $index; ?>" multiple>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                                                </form>
                                                <?php
                                                $file_sql = "SELECT id, document_name, file_path FROM notice_period_files WHERE step_id = ? AND employee_id = ?";
                                                $file_stmt = $conn->prepare($file_sql);
                                                $file_stmt->bind_param("ii", $step['step_id'], $selected_employee_id);
                                                $file_stmt->execute();
                                                $file_result = $file_stmt->get_result();
                                                $files = $file_result->fetch_all(MYSQLI_ASSOC);
                                                ?>
                                                <?php if (!empty($files)): ?>
                                                    <div class="file-list">
                                                        <h6>Uploaded Files:</h6>
                                                        <ul>
                                                            <?php foreach ($files as $file): ?>
                                                                <li>
                                                                    <a href="<?= $file['file_path']; ?>" target="_blank"><?= htmlspecialchars($file['document_name']); ?></a>
                                                                    <a href="?employee_id=<?= $selected_employee_id; ?>&delete_file=<?= $file['id']; ?>" 
                                                                       class="icon-btn text-danger" 
                                                                       onclick="return confirm('Are you sure you want to delete this file?');">
                                                                        <i class="fas fa-trash"></i>
                                                                    </a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($step['update_date']): ?>
                                                    <div class="update-date">
                                                        Last Updated: <?= date("d M Y, H:i", strtotime($step['update_date'])); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- All Steps Accordion -->
                        <div class="accordion mt-3" id="noticePeriodSteps">
                            <?php foreach ($steps as $index => $step): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed <?= $step['status'] ? 'completed' : 'submitted'; ?>" 
                                                type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse<?= $index; ?>">
                                            <?= $step['step_name']; ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $index; ?>" class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <p><strong>Description:</strong> <?= $step['description']; ?></p>
                                            <form action="" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="id" value="<?= $step['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="completed" <?= $step['status'] ? 'checked' : ''; ?>> Mark as Completed
                                                    </label>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="comment<?= $index; ?>" class="form-label">Comment</label>
                                                    <textarea class="form-control" name="comment" id="comment<?= $index; ?>" rows="3"><?= htmlspecialchars($step['comment']); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="files<?= $index; ?>" class="form-label">Upload Files</label>
                                                    <input type="file" class="form-control" name="files[]" id="files<?= $index; ?>" multiple>
                                                </div>
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                                            </form>
                                            <?php
                                            $file_sql = "SELECT id, document_name, file_path FROM notice_period_files WHERE step_id = ? AND employee_id = ?";
                                            $file_stmt = $conn->prepare($file_sql);
                                            $file_stmt->bind_param("ii", $step['step_id'], $selected_employee_id);
                                            $file_stmt->execute();
                                            $file_result = $file_stmt->get_result();
                                            $files = $file_result->fetch_all(MYSQLI_ASSOC);
                                            ?>
                                            <?php if (!empty($files)): ?>
                                                <div class="file-list">
                                                    <h6>Uploaded Files:</h6>
                                                    <ul>
                                                        <?php foreach ($files as $file): ?>
                                                            <li>
                                                                <a href="<?= $file['file_path']; ?>" target="_blank"><?= htmlspecialchars($file['document_name']); ?></a>
                                                                <a href="?employee_id=<?= $selected_employee_id; ?>&delete_file=<?= $file['id']; ?>" 
                                                                   class="icon-btn text-danger" 
                                                                   onclick="return confirm('Are you sure you want to delete this file?');">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        </ul>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($step['update_date']): ?>
                                                <div class="update-date">
                                                    Last Updated: <?= date("d M Y, H:i", strtotime($step['update_date'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php elseif ($selected_employee_id): ?>
                    <div class="alert alert-info">No active resignation found for this employee.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
<!-- jQuery and Bootstrap JS -->
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>-->
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable for history
        $('#historyTable').DataTable({
            scrollX: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            responsive: true,
            autoWidth: false
        });

        // Handle employee selection
        $('#viewNoticePeriodButton').click(function() {
            let employeeId = $('#selectEmployee').val();
            if (employeeId) {
                window.location.href = '?employee_id=' + employeeId;
            } else {
                alert('Please select an employee.');
            }
        });

        // Calculate and update progress bar
        let totalSteps = <?= count($steps); ?>;
        let completedSteps = <?= count(array_filter($steps, function($step) { return $step['status']; })); ?>;
        let progress = totalSteps ? (completedSteps / totalSteps) * 100 : 0;
        $('#progressBar').css('width', progress + '%').text(Math.round(progress) + '% Completed');

        // Toggle incomplete steps
        $('#toggleIncomplete').click(function() {
            $('#incompleteSteps').toggle();
            $('#noticePeriodSteps').toggle();
            $(this).text(function(i, text) {
                return text === 'Show Incomplete Steps' ? 'Show All Steps' : 'Show Incomplete Steps';
            });
        });

        // Show decline reason modal when Declined is selected
        $('#resignation_status').on('change', function() {
            if ($(this).val() === 'Declined') {
                $('#declineReasonModal').modal('show');
                return false; // Prevent immediate form submission
            } else {
                $('#resignationStatusForm').submit();
            }
        });

        // Prevent double form submission
        $('#declineReasonForm').on('submit', function(e) {
            let $form = $(this);
            let $submitBtn = $form.find('button[type="submit"]');
            if ($form.data('submitted')) {
                e.preventDefault();
                return;
            }
            $form.data('submitted', true);
            $submitBtn.prop('disabled', true);
            setTimeout(function() {
                $form.data('submitted', false);
                $submitBtn.prop('disabled', false);
            }, 5000); // Reset after 5 seconds
        });

        // Clear modal data on close
        $('#declineReasonModal').on('hidden.bs.modal', function() {
            $('#declineReasonForm')[0].reset();
        });
    });
</script>
</body>
</html>