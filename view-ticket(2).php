<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';
include 'email/send_ticket_email.php';

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Start session and validate user
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
error_log("Session ID: " . session_id() . ", CSRF Token: " . ($_SESSION['csrf_token'] ?? 'none'));
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Generate CSRF token only if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    error_log("Generated new CSRF token: " . $_SESSION['csrf_token']);
}

$conn = connect();
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection error");
}

$emp_id = $_SESSION['id']; // Current logged-in user

// Fetch employee details
$employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name, email FROM hrm_employee";
$employee_result = mysqli_query($conn, $employee_query);
if (!$employee_result) {
    error_log("Employee query error: " . mysqli_error($conn));
    die("Database error");
}

// Fetch logged-in user details
$query = "SELECT * FROM hrm_employee WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $emp_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

if (!$row) {
    error_log("User not found: ID $emp_id");
    header("Location: login.php");
    exit();
}

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$profile_image_dir = "upload-image";
$profile_image = file_exists($profile_image_dir . "/" . $row['image']) 
    ? $profile_image_dir . "/" . $row['image'] 
    : $profile_image_dir . "/default.jpg";

// Fetch all tickets with employee details
$ticket_query = "SELECT tickets.TicketID, tickets.Title, tickets.Status, tickets.Priority, 
                        hrm_ticket_categories.name AS CategoryName, 
                        CONCAT(hrm_employee.fname, ' ', hrm_employee.lname) AS EmployeeName,
                        hrm_employee.email AS EmployeeEmail,
                        tickets.Description
                 FROM tickets 
                 JOIN hrm_ticket_categories ON tickets.CategoryID = hrm_ticket_categories.id 
                 JOIN hrm_employee ON tickets.EmployeeID = hrm_employee.id 
                 ORDER BY tickets.CreatedAt DESC";
$ticket_result = mysqli_query($conn, $ticket_query);
if (!$ticket_result) {
    error_log("Ticket query error: " . mysqli_error($conn));
    die("Database error");
}

// Fetch counts for cards
$open_count_query = "SELECT COUNT(*) AS open_count FROM tickets WHERE Status = 'Open'";
$in_progress_count_query = "SELECT COUNT(*) AS in_progress_count FROM tickets WHERE Status = 'In Progress'";
$resolved_count_query = "SELECT COUNT(*) AS resolved_count FROM tickets WHERE Status = 'Resolved' AND MONTH(CreatedAt) = MONTH(CURRENT_DATE())";
$closed_count_query = "SELECT COUNT(*) AS closed_count FROM tickets WHERE Status = 'Closed' AND MONTH(CreatedAt) = MONTH(CURRENT_DATE())";

$open_count_result = mysqli_query($conn, $open_count_query);
$in_progress_count_result = mysqli_query($conn, $in_progress_count_query);
$resolved_count_result = mysqli_query($conn, $resolved_count_query);
$closed_count_result = mysqli_query($conn, $closed_count_query);

$open_count = mysqli_fetch_assoc($open_count_result)['open_count'];
$in_progress_count = mysqli_fetch_assoc($in_progress_count_result)['in_progress_count'];
$resolved_count = mysqli_fetch_assoc($resolved_count_result)['resolved_count'];
$closed_count = mysqli_fetch_assoc($closed_count_result)['closed_count'];

// Handle status update
$alert_message = "";
$alert_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed. Submitted: " . ($_POST['csrf_token'] ?? 'none') . ", Expected: " . $_SESSION['csrf_token']);
        // $alert_message = "This is a CSRF token-related security measure and does not affect your work or functionality.";
        // $alert_type = "danger";
    } else {
        $ticket_id = filter_var($_POST['ticket_id'], FILTER_SANITIZE_NUMBER_INT);
        $new_status = in_array($_POST['status'], ['Open', 'In Progress', 'Resolved']) ? $_POST['status'] : null;

        if ($ticket_id && $new_status) {
            // Fetch ticket details before updating
            $ticket_details_query = "SELECT tickets.*, hrm_employee.email AS EmployeeEmail, 
                                            CONCAT(hrm_employee.fname, ' ', hrm_employee.lname) AS EmployeeName
                                     FROM tickets 
                                     JOIN hrm_employee ON tickets.EmployeeID = hrm_employee.id 
                                     WHERE TicketID = ?";
            $stmt = mysqli_prepare($conn, $ticket_details_query);
            mysqli_stmt_bind_param($stmt, "i", $ticket_id);
            mysqli_stmt_execute($stmt);
            $ticket_details_result = mysqli_stmt_get_result($stmt);
            $ticket_details = mysqli_fetch_assoc($ticket_details_result);
            mysqli_stmt_close($stmt);

            if ($ticket_details) {
                // Prepare update query
                $priority = $new_status === 'Resolved' ? 'Low' : $ticket_details['Priority'];
                $update_query = "UPDATE tickets SET Status = ?, Priority = ?, UpdatedAt = NOW() WHERE TicketID = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "ssi", $new_status, $priority, $ticket_id);

                if (mysqli_stmt_execute($stmt)) {
                    $alert_message = "Ticket status updated successfully!";
                    $alert_type = "success";

                    // Send email to HR and employee
                    $subject = "Ticket Status Updated";
                    $message = "The status of Ticket ID: {$ticket_details['TicketID']} has been updated to <strong>{$new_status}</strong>.<br><br>
                                Title: {$ticket_details['Title']}<br>
                                Description: {$ticket_details['Description']}<br>
                                Priority: {$priority}<br>
                                Employee: {$ticket_details['EmployeeName']}";

                    // Send email to HR
                    $hr_email_result = sendEmail(['hr@1solutions.biz'], $subject, $message);
                    if ($hr_email_result !== true) {
                        error_log("Failed to send HR email: $hr_email_result");
                        $alert_message .= " (Warning: HR email failed to send)";
                    }

                    // Send email to the employee (if email is valid)
                    if (!empty($ticket_details['EmployeeEmail'])) {
                        $employee_email_result = sendEmail([$ticket_details['EmployeeEmail']], $subject, $message);
                        if ($employee_email_result !== true) {
                            error_log("Failed to send employee email: $employee_email_result");
                            $alert_message .= " (Warning: Employee email failed to send)";
                        }
                    } else {
                        error_log("Employee email missing for Ticket ID: $ticket_id");
                        $alert_message .= " (Warning: Employee email not found)";
                    }

                    // Regenerate CSRF token
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    error_log("New CSRF token after status update: " . $_SESSION['csrf_token']);

                    // Refresh ticket list
                    $ticket_result = mysqli_query($conn, $ticket_query);
                    if (!$ticket_result) {
                        error_log("Ticket refresh query error: " . mysqli_error($conn));
                        $alert_message = "Error refreshing ticket list: " . mysqli_error($conn);
                        $alert_type = "danger";
                    }
                } else {
                    error_log("Update query error: " . mysqli_error($conn));
                    $alert_message = "Error updating ticket status: " . mysqli_error($conn);
                    $alert_type = "danger";
                }
                mysqli_stmt_close($stmt);
            } else {
                $alert_message = "Ticket not found.";
                $alert_type = "danger";
            }
        } else {
            $alert_message = "Invalid ticket ID or status.";
            $alert_type = "danger";
        }
    }
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <title>HR Ticket Management</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .card-title {
            font-size: 1.2rem;
            color: #333;
        }
        .count-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }
        .section-heading {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">HR Ticket Management</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">HR Ticket Management</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Bootstrap Alert -->
                <?php if (!empty($alert_message)): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($alert_message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- End Bootstrap Alert -->

                <!-- Cards Section -->
                <h2 class="section-heading">Ticket Status</h2>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Open Tickets</p>
                                <h5 class="count-number"><?php echo $open_count; ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">In Progress</p>
                                <h5 class="count-number"><?php echo $in_progress_count; ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Resolved</p>
                                <h5 class="count-number"><?php echo $resolved_count; ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Closed</p>
                                <h5 class="count-number"><?php echo $closed_count; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Cards Section -->

                <!-- All Tickets Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Tickets</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="ticketsTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Ticket ID</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Employee</th>
                                                <th>Priority</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($ticket = mysqli_fetch_assoc($ticket_result)): ?>
                                                <tr>
                                                    <td><?php echo $ticket['TicketID']; ?></td>
                                                    <td><?php echo htmlspecialchars($ticket['Title']); ?></td>
                                                    <td><?php echo htmlspecialchars($ticket['CategoryName']); ?></td>
                                                    <td><?php echo htmlspecialchars($ticket['EmployeeName']); ?></td>
                                                    <td>
                                                        <span class="badge 
                                                            <?php echo $ticket['Priority'] === 'Low' ? 'bg-info' : 
                                                                  ($ticket['Priority'] === 'Medium' ? 'bg-warning' : 'bg-danger'); ?>">
                                                            <?php echo $ticket['Priority']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge 
                                                            <?php echo $ticket['Status'] === 'Open' ? 'bg-success' : 
                                                                  ($ticket['Status'] === 'In Progress' ? 'bg-warning' : 
                                                                  ($ticket['Status'] === 'Resolved' ? 'bg-secondary' : 'bg-danger')); ?>">
                                                            <?php echo $ticket['Status']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($ticket['Status'] !== 'Closed' && $ticket['Status'] !== 'Resolved'): ?>
                                                            <form method="POST" action="" style="display: inline;" id="statusForm<?php echo $ticket['TicketID']; ?>">
                                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['TicketID']; ?>">
                                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                                    <option value="Open" <?php echo $ticket['Status'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                                                                    <option value="In Progress" <?php echo $ticket['Status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                                    <option value="Resolved" <?php echo $ticket['Status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                                                </select>
                                                                <input type="hidden" name="update_status">
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Action</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ticketModal<?php echo $ticket['TicketID']; ?>">
                                                            View
                                                        </button>
                                                    </td>
                                                </tr>

                                                <!-- Modal for each ticket -->
                                                <div class="modal fade" id="ticketModal<?php echo $ticket['TicketID']; ?>" tabindex="-1" aria-labelledby="ticketModalLabel<?php echo $ticket['TicketID']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="ticketModalLabel<?php echo $ticket['TicketID']; ?>">Ticket Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Ticket ID:</strong> <?php echo $ticket['TicketID']; ?></p>
                                                                <p><strong>Title:</strong> <?php echo htmlspecialchars($ticket['Title']); ?></p>
                                                                <p><strong>Category:</strong> <?php echo htmlspecialchars($ticket['CategoryName']); ?></p>
                                                                <p><strong>Employee:</strong> <?php echo htmlspecialchars($ticket['EmployeeName']); ?></p>
                                                                <p><strong>Description:</strong> <?php echo htmlspecialchars($ticket['Description']); ?></p>
                                                                <p><strong>Priority:</strong> <?php echo $ticket['Priority']; ?></p>
                                                                <p><strong>Status:</strong> <?php echo $ticket['Status']; ?></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End All Tickets Table -->

            </div>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <!-- jQuery -->
    <!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#ticketsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                responsive: true
            });

            // Disable select on change to prevent double submissions
            $('select[name="status"]').on('change', function() {
                $(this).closest('form').find('select').prop('disabled', true);
                setTimeout(function() {
                    $(this).closest('form').find('select').prop('disabled', false);
                }.bind(this), 5000); // Re-enable after 5 seconds
            });
        });
    </script>
</body>
</html>