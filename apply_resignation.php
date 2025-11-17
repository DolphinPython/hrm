<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php'; ?>
<?php include 'layouts/config.php'; ?>
<?php require './email/mailer.php'; ?>
<?php
$conn = $con;
$emp_id = $_SESSION['id'];
$error_message = '';

// Generate CSRF token for form submissions
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch employee details
$query = "SELECT * FROM hrm_employee WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

// Fetch current resignation details (if any)
$resignation = null;
$check_query = "SELECT * FROM employee_resignations WHERE employee_id = ? AND status != 'Declined'";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $emp_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
if ($check_result->num_rows > 0) {
    $resignation = $check_result->fetch_assoc();
}

// Fetch declined resignation (if any) to display details
$declined_resignation = null;
$declined_query = "SELECT * FROM employee_resignations WHERE employee_id = ? AND status = 'Declined' ORDER BY updated_at DESC LIMIT 1";
$declined_stmt = $conn->prepare($declined_query);
$declined_stmt->bind_param("i", $emp_id);
$declined_stmt->execute();
$declined_result = $declined_stmt->get_result();
if ($declined_result->num_rows > 0) {
    $declined_resignation = $declined_result->fetch_assoc();
}

// Fetch resignation history with resignation status
$history_query = "
    SELECT rh.*, e.fname, e.lname, r.status AS resignation_status 
    FROM resignation_history rh 
    LEFT JOIN hrm_employee e ON rh.changed_by = e.id 
    LEFT JOIN employee_resignations r ON rh.resignation_id = r.id 
    WHERE rh.employee_id = ? 
    ORDER BY rh.changed_at DESC";
$history_stmt = $conn->prepare($history_query);
$history_stmt->bind_param("i", $emp_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
$history = $history_result->fetch_all(MYSQLI_ASSOC);

// Handle resignation submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_resignation']) && !$resignation && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $resignation_reason = trim($_POST['resignation_reason'] ?? '');
    $intended_last_date = trim($_POST['intended_last_date'] ?? '');
    $notice_period_days = $_POST['notice_period_days'] ?? 15;

    if (empty($resignation_reason)) {
        $error_message = "Error: Resignation reason is required.";
    } elseif (empty($intended_last_date)) {
        $error_message = "Error: Intended last working date is required.";
    } else {
        // Begin transaction for data consistency
        $conn->begin_transaction();
        try {
            // Insert resignation
            $sql = "INSERT INTO employee_resignations (employee_id, resignation_reason, intended_last_date, notice_period_days, status, submitted_at, updated_at) 
                    VALUES (?, ?, ?, ?, 'Pending', NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issi", $emp_id, $resignation_reason, $intended_last_date, $notice_period_days);
            $stmt->execute();

            // Get the resignation ID
            $resignation_id = $conn->insert_id;

            // Log to resignation_history
            $history_sql = "INSERT INTO resignation_history (resignation_id, employee_id, status, notice_period_days, changed_at, comment) 
                            VALUES (?, ?, 'Pending', ?, NOW(), 'Resignation submitted')";
            $history_stmt = $conn->prepare($history_sql);
            $history_stmt->bind_param("iii", $resignation_id, $emp_id, $notice_period_days);
            $history_stmt->execute();

            // Initialize notice period steps
            $all_steps_sql = "SELECT step_id FROM notice_period_steps ORDER BY step_order ASC";
            $all_steps_result = $conn->query($all_steps_sql);
            $all_steps = $all_steps_result->fetch_all(MYSQLI_ASSOC);

            $insert_sql = "INSERT INTO employee_notice_period_steps (employee_id, step_id, status, comment, created_at) 
                           VALUES (?, ?, 0, '', NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $check_sql = "SELECT COUNT(*) as count FROM employee_notice_period_steps WHERE employee_id = ? AND step_id = ?";
            $check_stmt = $conn->prepare($check_sql);

            foreach ($all_steps as $step) {
                $step_id = $step['step_id'];
                // Check for existing step
                $check_stmt->bind_param("ii", $emp_id, $step_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                $count = $check_result->fetch_assoc()['count'];

                if ($count == 0) {
                    $insert_stmt->bind_param("ii", $emp_id, $step_id);
                    $insert_stmt->execute();
                    error_log("Inserted step_id $step_id for employee_id $emp_id in apply_resignation.php");
                } else {
                    error_log("Skipped duplicate step_id $step_id for employee_id $emp_id in apply_resignation.php");
                }
            }

            // Commit transaction
            $conn->commit();

            // Send email notification
            $to = 'hr@1solutions.biz';
            $cc_emails = ['pythondolphin@gmail.com','dolphinpython@outlook.com',$employee['email']];
            $subject = "New Resignation Submitted by {$employee['fname']} {$employee['lname']}";
            $message = "
                <h3>New Resignation Submission</h3>
                <p><strong>Employee:</strong> {$employee['fname']} {$employee['lname']} (ID: {$emp_id})</p>
                <p><strong>Email:</strong> {$employee['email']}</p>
                <p><strong>Reason:</strong> " . htmlspecialchars($resignation_reason) . "</p>
                <p><strong>Intended Last Working Date:</strong> {$intended_last_date}</p>
                <p><strong>Notice Period:</strong> {$notice_period_days} days</p>
                <p><strong>Status:</strong> Pending</p>
                <p><strong>Submitted On:</strong> " . date("d M Y, H:i") . "</p>
                <p>Please review the resignation in the Notice Period Management System.</p>
            ";
            try {
                if (!send_email($to, $subject, $message, $cc_emails)) {
                    throw new Exception("Failed to send resignation submission email for employee ID: {$emp_id}");
                }
            } catch (Exception $e) {
                error_log("Email Error (Submission): " . $e->getMessage());
            }

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            // Roll back transaction on error
            $conn->rollback();
            error_log("Error submitting resignation: " . $e->getMessage());
            $error_message = "Error submitting resignation: " . $e->getMessage();
        }
    }
}

// Handle resignation edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_resignation']) && $resignation && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if ($resignation['status'] === 'Pending') {
        $resignation_id = $_POST['resignation_id'] ?? 0;
        $resignation_reason = trim($_POST['resignation_reason'] ?? '');
        $intended_last_date = trim($_POST['intended_last_date'] ?? '');
        $notice_period_days = $_POST['notice_period_days'] ?? 15;

        if (empty($resignation_reason)) {
            $error_message = "Error: Resignation reason is required.";
        } elseif (empty($intended_last_date)) {
            $error_message = "Error: Intended last working date is required.";
        } else {
            // Begin transaction
            $conn->begin_transaction();
            try {
                // Check if any changes were made
                $changes_made = (
                    $resignation['resignation_reason'] !== $resignation_reason ||
                    $resignation['intended_last_date'] !== $intended_last_date ||
                    $resignation['notice_period_days'] != $notice_period_days
                );

                if ($changes_made) {
                    // Update resignation
                    $sql = "UPDATE employee_resignations SET resignation_reason = ?, intended_last_date = ?, notice_period_days = ?, updated_at = NOW() 
                            WHERE id = ? AND employee_id = ? AND status = 'Pending'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssiii", $resignation_reason, $intended_last_date, $notice_period_days, $resignation_id, $emp_id);
                    $stmt->execute();

                    // Log to resignation_history
                    $history_sql = "INSERT INTO resignation_history (resignation_id, employee_id, status, notice_period_days, changed_by, changed_at, comment) 
                                    VALUES (?, ?, ?, ?, ?, NOW(), ?)";
                    $history_stmt = $conn->prepare($history_sql);
                    $comment = "Resignation details updated";
                    $history_stmt->bind_param("iisisi", $resignation_id, $emp_id, $resignation['status'], $notice_period_days, $emp_id, $comment);
                    $history_stmt->execute();

                    // Commit transaction
                    $conn->commit();

                    // Send email notification
                    $to = 'hr@1solutions.biz';
                    $cc_emails = ['dolphinpython@outlook.com','pythondolphin@gmail.com',$employee['email']];
                    $subject = "Resignation Updated by {$employee['fname']} {$employee['lname']}";
                    $message = "
                        <h3>Resignation Update</h3>
                        <p><strong>Employee:</strong> {$employee['fname']} {$employee['lname']} (ID: {$emp_id})</p>
                        <p><strong>Email:</strong> {$employee['email']}</p>
                        <p><strong>Reason:</strong> " . htmlspecialchars($resignation_reason) . "</p>
                        <p><strong>Intended Last Working Date:</strong> {$intended_last_date}</p>
                        <p><strong>Notice Period:</strong> {$notice_period_days} days</p>
                        <p><strong>Status:</strong> Pending</p>
                        <p><strong>Updated On:</strong> " . date("d M Y, H:i") . "</p>
                        <p>Please review the updated resignation in the Notice Period Management System.</p>
                    ";
                    try {
                        if (!send_email($to, $subject, $message, $cc_emails)) {
                            throw new Exception("Failed to send resignation update email for employee ID: {$emp_id}");
                        }
                    } catch (Exception $e) {
                        error_log("Email Error (Update): " . $e->getMessage());
                    }
                } else {
                    $conn->commit(); // Commit empty transaction
                    $error_message = "No changes made to resignation details.";
                }

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Error updating resignation: " . $e->getMessage());
                $error_message = "Error updating resignation: " . $e->getMessage();
            }
        }
    } else {
        error_log("Attempted to edit resignation for employee_id $emp_id with non-Pending status: {$resignation['status']}");
        $error_message = "Error: Cannot edit resignation with status '{$resignation['status']}'.";
    }
}

// Handle resignation deletion
if (isset($_GET['delete_resignation']) && $resignation && $resignation['status'] === 'Pending') {
    $resignation_id = $_GET['delete_resignation'];
    // Begin transaction
    $conn->begin_transaction();
    try {
        // Log to resignation_history before deletion
        $history_sql = "INSERT INTO resignation_history (resignation_id, employee_id, status, notice_period_days, changed_by, changed_at, comment) 
                        VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        $history_stmt = $conn->prepare($history_sql);
        $status = 'Deleted';
        $comment = 'Resignation deleted by employee';
        $history_stmt->bind_param("iisisi", $resignation_id, $emp_id, $status, $resignation['notice_period_days'], $emp_id, $comment);
        $history_stmt->execute();

        // Delete associated notice period steps
        $delete_steps_sql = "DELETE FROM employee_notice_period_steps WHERE employee_id = ? AND EXISTS 
                             (SELECT 1 FROM employee_resignations WHERE id = ? AND status = 'Pending')";
        $delete_steps_stmt = $conn->prepare($delete_steps_sql);
        $delete_steps_stmt->bind_param("ii", $emp_id, $resignation_id);
        $delete_steps_stmt->execute();

        // Delete associated files
        $file_sql = "SELECT file_path FROM notice_period_files WHERE employee_id = ?";
        $file_stmt = $conn->prepare($file_sql);
        $file_stmt->bind_param("i", $emp_id);
        $file_stmt->execute();
        $file_result = $file_stmt->get_result();
        while ($file = $file_result->fetch_assoc()) {
            if (file_exists($file['file_path'])) {
                unlink($file['file_path']);
            }
        }
        $delete_files_sql = "DELETE FROM notice_period_files WHERE employee_id = ?";
        $delete_files_stmt = $conn->prepare($delete_files_sql);
        $delete_files_stmt->bind_param("i", $emp_id);
        $delete_files_stmt->execute();

        // Delete resignation
        $delete_sql = "DELETE FROM employee_resignations WHERE id = ? AND employee_id = ? AND status = 'Pending'";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $resignation_id, $emp_id);
        $delete_stmt->execute();

        // Commit transaction
        $conn->commit();

        // Send email notification
        $to = 'hr@1solutions.biz';
        $cc_emails = ['pythondolphin@gmail.com','dolphinpython@outlook.com',$employee['email']];
        $subject = "Resignation Deleted by {$employee['fname']} {$employee['lname']}";
        $message = "
            <h3>Resignation Deleted</h3>
            <p><strong>Employee:</strong> {$employee['fname']} {$employee['lname']} (ID: {$emp_id})</p>
            <p><strong>Email:</strong> {$employee['email']}</p>
            <p><strong>Resignation ID:</strong> {$resignation_id}</p>
            <p><strong>Deleted On:</strong> " . date("d M Y, H:i") . "</p>
            <p>The resignation has been deleted from the Notice Period Management System.</p>
        ";
        try {
            if (!send_email($to, $subject, $message, $cc_emails)) {
                throw new Exception("Failed to send resignation deletion email for employee ID: {$emp_id}");
            }
        } catch (Exception $e) {
            error_log("Email Error (Deletion): " . $e->getMessage());
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error deleting resignation: " . $e->getMessage());
        $error_message = "Error deleting resignation: " . $e->getMessage();
    }
}

?>
<?php
$emp_id = $_SESSION['id'];
$conn = connect();
$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

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
    <title>Apply for Resignation</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <!-- Bootstrap CSS (Explicitly included to avoid conflicts) -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .main-wrapper {
            min-height: 100vh;
        }
        .page-wrapper {
            padding: 15px;
        }
        .container {
            max-width: 100%;
            padding-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }
        .card-body {
            padding: 1.5rem;
        }
        .form-label {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }
        .btn-primary, .btn-warning, .btn-danger {
            border-radius: 5px;
            padding: 5px 10px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            border-radius: 5px;
            margin-top: 1rem;
        }
        .breadcrumb {
            background-color: transparent;
            padding: 0.5rem 0;
        }
        .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #6c757d;
        }
        .history-table {
            margin-top: 1.5rem;
        }
        .table {
            font-size: 0.875rem;
            width: 100%;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 0.75rem;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }
        .text-primary {
            color: #007bff !important;
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
        .modal, .modal-dialog, .modal-content, .btn-warning {
            transition: none !important;
        }
        .edit-resignation:hover, .edit-resignation:focus {
            transform: none !important;
            outline: none;
        }
        @media (max-width: 576px) {
            .page-wrapper {
                padding: 10px;
            }
            .card-body {
                padding: 1rem;
            }
            h1 {
                font-size: 1.5rem;
            }
            .form-label {
                font-size: 0.9rem;
            }
            .form-control, .form-select {
                font-size: 0.85rem;
            }
            .btn-primary, .btn-warning, .btn-danger {
                width: 100%;
                padding: 8px;
                margin-bottom: 5px;
            }
            .table {
                font-size: 0.75rem;
            }
            .table th, .table td {
                padding: 0.5rem;
            }
            .table-responsive {
                overflow-x: auto;
            }
            .table th, .table td {
                white-space: nowrap;
            }
            .breadcrumb {
                font-size: 0.8rem;
            }
        }
        @media (min-width: 576px) and (max-width: 768px) {
            h1 {
                font-size: 1.75rem;
            }
            .card {
                max-width: 90%;
            }
            .form-control, .form-select {
                font-size: 0.9rem;
            }
            .btn-primary, .btn-warning, .btn-danger {
                padding: 8px 15px;
            }
            .table {
                font-size: 0.85rem;
            }
        }
        @media (min-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            .form-control, .form-select {
                font-size: 1rem;
            }
            .table {
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="container padding-top-ams">
            <div class="container-fluid mt-4">
                <h1 class="mb-3 text-primary">Apply for Resignation</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Apply for Resignation</li>
                    </ol>
                </nav>

                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($resignation): ?>
                            <div class="alert alert-info">
                                <h5 class="mb-3">Resignation Status: <?= $resignation['status']; ?></h5>
                                <p class="mb-2"><strong>Reason:</strong> <?= htmlspecialchars($resignation['resignation_reason']); ?></p>
                                <p class="mb-2"><strong>Intended Last Working Date:</strong> <?= $resignation['intended_last_date']; ?></p>
                                <p class="mb-2"><strong>Notice Period:</strong> <?= $resignation['notice_period_days']; ?> days</p>
                                <p class="mb-2"><strong>Submitted On:</strong> <?= date("d M Y, H:i", strtotime($resignation['submitted_at'])); ?></p>
                                <?php if ($resignation['updated_at']): ?>
                                    <p class="mb-2"><strong>Last Updated On:</strong> <?= date("d M Y, H:i", strtotime($resignation['updated_at'])); ?></p>
                                <?php endif; ?>
                                <?php if ($resignation['status'] !== 'Pending'): ?>
                                    <p class="mb-2"><strong>Processed On:</strong> <?= date("d M Y, H:i", strtotime($resignation['approved_at'])); ?></p>
                                <?php endif; ?>
                                <?php if ($resignation['status'] === 'Declined' && $resignation['decline_reason']): ?>
                                    <p class="mb-2"><strong>Decline Reason:</strong> <?= htmlspecialchars($resignation['decline_reason']); ?></p>
                                <?php endif; ?>
                                <?php if ($resignation['status'] === 'Pending'): ?>
                                    <button class="btn btn-warning edit-resignation mt-2" data-bs-toggle="modal" data-bs-target="#editResignationModal">
                                        <i class="fas fa-edit"></i> Edit Resignation
                                    </button>
                                    <a href="?delete_resignation=<?= $resignation['id']; ?>" class="btn btn-danger mt-2" 
                                       onclick="return confirm('Are you sure you want to delete your resignation?');">
                                        <i class="fas fa-trash"></i> Delete Resignation
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($declined_resignation && !$resignation): ?>
                            <div class="alert alert-warning">
                                <h5 class="mb-3">Previous Resignation Declined</h5>
                                <p class="mb-2"><strong>Reason:</strong> <?= htmlspecialchars($declined_resignation['resignation_reason']); ?></p>
                                <p class="mb-2"><strong>Intended Last Working Date:</strong> <?= $declined_resignation['intended_last_date']; ?></p>
                                <p class="mb-2"><strong>Notice Period:</strong> <?= $declined_resignation['notice_period_days']; ?> days</p>
                                <p class="mb-2"><strong>Submitted On:</strong> <?= date("d M Y, H:i", strtotime($declined_resignation['submitted_at'])); ?></p>
                                <?php if ($declined_resignation['updated_at']): ?>
                                    <p class="mb-2"><strong>Last Updated On:</strong> <?= date("d M Y, H:i", strtotime($declined_resignation['updated_at'])); ?></p>
                                <?php endif; ?>
                                <p class="mb-2"><strong>Processed On:</strong> <?= date("d M Y, H:i", strtotime($declined_resignation['approved_at'])); ?></p>
                                <p class="mb-2"><strong>Decline Reason:</strong> <?= htmlspecialchars($declined_resignation['decline_reason']); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!$resignation): ?>
                            <h5 class="card-title">Submit Resignation</h5>
                            <form action="" method="POST" id="resignationForm">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="submit_resignation" value="1">
                                <div class="mb-3">
                                    <label for="resignation_reason" class="form-label">Resignation Reason</label>
                                    <textarea class="form-control" id="resignation_reason" name="resignation_reason" rows="5" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="intended_last_date" class="form-label">Intended Last Working Date</label>
                                    <input type="date" class="form-control" id="intended_last_date" name="intended_last_date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="notice_period_days" class="form-label">Notice Period</label>
                                    <select class="form-select" id="notice_period_days" name="notice_period_days">
                                        <option value="15">15 Days</option>
                                        <option value="30" selected>30 Days</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Resignation</button>
                            </form>
                        <?php endif; ?>

                        <!-- Edit Resignation Modal -->
                        <?php if ($resignation && $resignation['status'] === 'Pending'): ?>
                            <div class="modal fade" id="editResignationModal" tabindex="-1" aria-labelledby="editResignationModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editResignationModalLabel">Edit Resignation</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST" id="editResignationForm">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="edit_resignation" value="1">
                                                <input type="hidden" name="resignation_id" value="<?= $resignation['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="edit_resignation_reason" class="form-label">Resignation Reason</label>
                                                    <textarea class="form-control" id="edit_resignation_reason" name="resignation_reason" rows="5" required><?= htmlspecialchars($resignation['resignation_reason']); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_intended_last_date" class="form-label">Intended Last Working Date</label>
                                                    <input type="date" class="form-control" id="edit_intended_last_date" name="intended_last_date" value="<?= $resignation['intended_last_date']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_notice_period_days" class="form-label">Notice Period</label>
                                                    <select class="form-select" id="edit_notice_period_days" name="notice_period_days">
                                                        <option value="15" <?= $resignation['notice_period_days'] == 15 ? 'selected' : ''; ?>>15 Days</option>
                                                        <option value="30" <?= $resignation['notice_period_days'] == 30 ? 'selected' : ''; ?>>30 Days</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Resignation History -->
                        <?php if (!empty($history)): ?>
                            <div class="history-table">
                                <h5 class="mt-4">Resignation History</h5>
                                <div class="table-responsive">
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
                                                    <td><?= $entry['changed_by'] ? ($entry['fname'] . ' ' . $entry['lname']) : 'System'; ?></td>
                                                    <td><?= htmlspecialchars($entry['comment'] ?? '-'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
<!-- jQuery and Bootstrap JS -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script>
    $(document).ready(function() {
        // Prevent double form submission
        $('form').on('submit', function(e) {
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
        $('#editResignationModal').on('hidden.bs.modal', function() {
            $('#editResignationForm')[0].reset();
        });

        // Function to set intended last date based on notice period
        function setIntendedLastDate(noticePeriodDays, dateInputId) {
            let today = new Date();
            today.setDate(today.getDate() + parseInt(noticePeriodDays));
            let formattedDate = today.toISOString().split('T')[0];
            $(dateInputId).val(formattedDate);
        }

        // Initialize intended last date for submission form
        setIntendedLastDate($('#notice_period_days').val(), '#intended_last_date');

        // Update intended last date when notice period changes in submission form
        $('#notice_period_days').on('change', function() {
            setIntendedLastDate($(this).val(), '#intended_last_date');
        });

        // Initialize intended last date for edit form when modal opens
        $('#editResignationModal').on('shown.bs.modal', function() {
            setIntendedLastDate($('#edit_notice_period_days').val(), '#edit_intended_last_date');
        });

        // Update intended last date when notice period changes in edit form
        $('#edit_notice_period_days').on('change', function() {
            setIntendedLastDate($(this).val(), '#edit_intended_last_date');
        });
    });
</script>
</body>
</html>