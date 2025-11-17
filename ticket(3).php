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
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Generate CSRF token only if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$conn = connect();
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection error");
}

$emp_id = $_SESSION['id']; // Current logged-in user

// Fetch employee details
$employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name FROM hrm_employee";
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

// Fetch categories
$category_query = "SELECT id, name FROM hrm_ticket_categories";
$category_result = mysqli_query($conn, $category_query);
if (!$category_result) {
    error_log("Category query error: " . mysqli_error($conn));
    die("Database error");
}

// Fetch tickets created by the logged-in user
$ticket_query = "SELECT tickets.TicketID, tickets.Title, tickets.Description, tickets.Status, tickets.Priority, 
                        hrm_ticket_categories.name AS CategoryName, tickets.Rating 
                 FROM tickets 
                 JOIN hrm_ticket_categories ON tickets.CategoryID = hrm_ticket_categories.id 
                 WHERE tickets.EmployeeID = ? 
                 ORDER BY tickets.CreatedAt DESC";
$stmt = mysqli_prepare($conn, $ticket_query);
mysqli_stmt_bind_param($stmt, "i", $emp_id);
mysqli_stmt_execute($stmt);
$ticket_result = mysqli_stmt_get_result($stmt);

// Validate CategoryID and EmployeeID
function validateCategory($conn, $category_id) {
    $query = "SELECT id FROM hrm_ticket_categories WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

function validateEmployee($conn, $employee_id) {
    $query = "SELECT id FROM hrm_employee WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $employee_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['form_data'] = $_POST;
        error_log("CSRF token validation failed. Submitted: " . ($_POST['csrf_token'] ?? 'none') . ", Expected: " . ($_SESSION['csrf_token'] ?? 'none'));
        header("Location: ticket.php?error=Please keep patience, we will reach out to you shortly(csrf).");
        exit();
    }

    if (isset($_POST['create_ticket'])) {
        // Create Ticket
        $category_id = filter_var($_POST['category_id'], FILTER_SANITIZE_NUMBER_INT);
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $priority = in_array($_POST['priority'], ['Low', 'Medium', 'High']) ? $_POST['priority'] : 'Low';

        // Validate inputs and foreign keys
        if ($category_id && $title && $description && validateCategory($conn, $category_id) && validateEmployee($conn, $emp_id)) {
            $insert_query = "INSERT INTO tickets (CategoryID, Title, Description, Priority, EmployeeID, Status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            if ($stmt) {
                $status = "Open";
                mysqli_stmt_bind_param($stmt, "isssis", $category_id, $title, $description, $priority, $emp_id, $status);
                if (mysqli_stmt_execute($stmt)) {
                    $ticket_id = mysqli_insert_id($conn);
                    try {
                        $subject = "Ticket created";
                        $message = "A new ticket has been created by {$row['fname']} {$row['lname']}.<br><br>
                                    Ticket ID: $ticket_id<br>
                                    Title: $title<br>
                                    Description: $description<br>
                                    Priority: $priority";
                        sendEmail(['hr@1solutions.biz'], $subject, $message);
                    } catch (Exception $e) {
                        error_log("Email sending failed: " . $e->getMessage());
                    }
                    unset($_SESSION['form_data']);
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Regenerate CSRF token
                    header("Location: ticket.php?success=Ticket created successfully");
                    exit();
                } else {
                    $_SESSION['form_data'] = $_POST;
                    error_log("Insert query error: " . mysqli_error($conn));
                    header("Location: ticket.php?error=Error creating ticket");
                    exit();
                }
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['form_data'] = $_POST;
                error_log("Prepare failed: " . mysqli_error($conn));
                header("Location: ticket.php?error=Database error");
                exit();
            }
        } else {
            $_SESSION['form_data'] = $_POST;
            header("Location: ticket.php?error=Invalid input data or category/employee not found");
            exit();
        }
    } elseif (isset($_POST['close_ticket'])) {
        // Close Ticket with Rating
        $ticket_id = filter_var($_POST['ticket_id'], FILTER_SANITIZE_NUMBER_INT);
        $rating = filter_var($_POST['rating'], FILTER_SANITIZE_NUMBER_INT);

        if ($rating < 1 || $rating > 10) {
            header("Location: ticket.php?error=Rating must be between 1 and 10");
            exit();
        }

        $close_query = "UPDATE tickets SET Status = ?, Rating = ? WHERE TicketID = ? AND EmployeeID = ?";
        $stmt = mysqli_prepare($conn, $close_query);
        if ($stmt) {
            $status = "Closed";
            mysqli_stmt_bind_param($stmt, "siii", $status, $rating, $ticket_id, $emp_id);
            if (mysqli_stmt_execute($stmt)) {
                try {
                    $subject = "Ticket Closed";
                    $message = "Ticket ID: $ticket_id has been closed by {$row['fname']} {$row['lname']}.<br><br>
                                Rating: $rating";
                    sendEmail(['hr@1solutions.biz'], $subject, $message);
                } catch (Exception $e) {
                    error_log("Email sending failed: " . $e->getMessage());
                }
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Regenerate CSRF token
                header("Location: ticket.php?success=Ticket closed successfully");
                exit();
            } else {
                error_log("Close query error: " . mysqli_error($conn));
                header("Location: ticket.php?error=Error closing ticket");
                exit();
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Prepare failed: " . mysqli_error($conn));
            header("Location: ticket.php?error=Database error");
            exit();
        }
    } elseif (isset($_POST['reopen_ticket'])) {
        // Reopen Ticket
        $ticket_id = filter_var($_POST['ticket_id'], FILTER_SANITIZE_NUMBER_INT);

        $reopen_query = "UPDATE tickets SET Status = ? WHERE TicketID = ? AND EmployeeID = ?";
        $stmt = mysqli_prepare($conn, $reopen_query);
        if ($stmt) {
            $status = "Reopened";
            mysqli_stmt_bind_param($stmt, "sii", $status, $ticket_id, $emp_id);
            if (mysqli_stmt_execute($stmt)) {
                try {
                    $subject = "Ticket Reopened";
                    $message = "Ticket ID: $ticket_id has been reopened by {$row['fname']} {$row['lname']}.";
                    sendEmail(['hr@1solutions.biz'], $subject, $message);
                } catch (Exception $e) {
                    error_log("Email sending failed: " . $e->getMessage());
                }
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Regenerate CSRF token
                header("Location: ticket.php?success=Ticket reopened successfully");
                exit();
            } else {
                error_log("Reopen query error: " . mysqli_error($conn));
                header("Location: ticket.php?error=Error reopening ticket");
                exit();
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Prepare failed: " . mysqli_error($conn));
            header("Location: ticket.php?error=Database error");
            exit();
        }
    } elseif (isset($_POST['delete_ticket'])) {
        // Delete Ticket
        $ticket_id = filter_var($_POST['ticket_id'], FILTER_SANITIZE_NUMBER_INT);

        $delete_query = "DELETE FROM tickets WHERE TicketID = ? AND EmployeeID = ? AND Status IN ('Open', 'Reopened', 'Resolved')";
        $stmt = mysqli_prepare($conn, $delete_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $ticket_id, $emp_id);
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Regenerate CSRF token
                    header("Location: ticket.php?success=Ticket deleted successfully");
                    exit();
                } else {
                    error_log("No rows affected for delete query: TicketID $ticket_id, EmployeeID $emp_id");
                    header("Location: ticket.php?error=Cannot delete ticket. Only Open, Reopened, or Resolved tickets can be deleted.");
                    exit();
                }
            } else {
                error_log("Delete query error: " . mysqli_error($conn));
                header("Location: ticket.php?error=Error deleting ticket: " . mysqli_error($conn));
                exit();
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Prepare failed: " . mysqli_error($conn));
            header("Location: ticket.php?error=Database error");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Ticket</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    
    <style>
        .fixed-description {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                            <h3 class="page-title">Ticket</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Ticket</li>
                            </ul>
                        </div>
                    </div>
                </div>
                

                <!-- Display Success/Error Messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
<!-- User's Ticket List -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Your Tickets</h5>
                            </div>
                            <div class="card-body">
                                <?php if (mysqli_num_rows($ticket_result) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="ticketTable">
                                            <thead>
                                                <tr>
                                                    <th>Ticket ID</th>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th>Category</th>
                                                    <th>Priority</th>
                                                    <th>Status</th>
                                                    <th>Rating</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($ticket = mysqli_fetch_assoc($ticket_result)): ?>
                                                    <tr>
                                                        <td><?php echo $ticket['TicketID']; ?></td>
                                                        <td><?php echo htmlspecialchars($ticket['Title']); ?></td>
                                                        <td class="fixed-description" title="<?php echo htmlspecialchars($ticket['Description']); ?>">
                                                            <?php echo htmlspecialchars($ticket['Description']); ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($ticket['CategoryName']); ?></td>
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
                                                                      ($ticket['Status'] === 'Resolved' ? 'bg-primary' : 
                                                                      ($ticket['Status'] === 'Closed' ? 'bg-danger' : 'bg-secondary'))); ?>">
                                                                <?php echo $ticket['Status']; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php echo $ticket['Rating'] ? $ticket['Rating'] : 'N/A'; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($ticket['Status'] === 'Closed' || $ticket['Status'] === 'Resolved'): ?>
                                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#reopenModal<?php echo $ticket['TicketID']; ?>">Reopen</button>
                                                            <?php endif; ?>
                                                            <?php if ($ticket['Status'] === 'Open' || $ticket['Status'] === 'In Progress' || $ticket['Status'] === 'Resolved' || $ticket['Status'] === 'Reopened'): ?>
                                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#feedbackModal<?php echo $ticket['TicketID']; ?>">Close</button>
                                                            <?php endif; ?>
                                                            <?php if ($ticket['Status'] !== 'Closed'): ?>
                                                                <form method="POST" action="" style="display: inline;">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['TicketID']; ?>">
                                                                    <button type="submit" name="delete_ticket" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this ticket?');">Delete</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>

                                                    <!-- Feedback Modal -->
                                                    <div class="modal fade" id="feedbackModal<?php echo $ticket['TicketID']; ?>" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="feedbackModalLabel">Provide Rating</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="">
                                                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['TicketID']; ?>">
                                                                        <div class="form-group">
                                                                            <label for="rating">Rating (1-10)</label>
                                                                            <input type="number" class="form-control" id="rating" name="rating" min="1" max="10" required>
                                                                        </div>
                                                                        <button type="submit" name="close_ticket" class="btn btn-primary mt-3">Submit Rating & Close Ticket</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Reopen Modal -->
                                                    <div class="modal fade" id="reopenModal<?php echo $ticket['TicketID']; ?>" tabindex="-1" aria-labelledby="reopenModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="reopenModalLabel">Reopen Ticket</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to reopen this ticket?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <form method="POST" action="">
                                                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['TicketID']; ?>">
                                                                        <button type="submit" name="reopen_ticket" class="btn btn-warning">Reopen Ticket</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>No tickets found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End User's Ticket List -->
                <!-- Create Ticket Form -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Create New Ticket</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <div class="form-group">
                                        <label for="category_id">Category</label>
                                        <select class="form-control" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php 
                                            mysqli_data_seek($category_result, 0); // Reset category result pointer
                                            while ($category = mysqli_fetch_assoc($category_result)): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo isset($_SESSION['form_data']['category_id']) && $_SESSION['form_data']['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($_SESSION['form_data']['title']) ? htmlspecialchars($_SESSION['form_data']['title']) : ''; ?>" required>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : ''; ?></textarea>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="priority">Priority</label>
                                        <select class="form-control" id="priority" name="priority" required>
                                            <option value="Low" <?php echo isset($_SESSION['form_data']['priority']) && $_SESSION['form_data']['priority'] === 'Low' ? 'selected' : ''; ?>>Low</option>
                                            <option value="Medium" <?php echo isset($_SESSION['form_data']['priority']) && $_SESSION['form_data']['priority'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                            <option value="High" <?php echo isset($_SESSION['form_data']['priority']) && $_SESSION['form_data']['priority'] === 'High' ? 'selected' : ''; ?>>High</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="create_ticket" class="btn btn-primary mt-3">Create Ticket</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Create Ticket Form -->

                

            </div>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <!-- JavaScript for DataTable Initialization -->
    <script>
        $(document).ready(function() {
            $('#ticketTable').DataTable();
        });
    </script>
</body>
</html>