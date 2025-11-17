<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';
include 'email/send_ticket_email.php';

$conn = connect();
$emp_id = $_SESSION['id'];

// Fetch employee details
$employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name, email FROM hrm_employee";
$employee_result = mysqli_query($conn, $employee_query) or die(mysqli_error($conn));

// Fetch logged-in user details
$query = "SELECT id, CONCAT(fname, ' ', lname) AS name, email FROM hrm_employee WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $emp_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result);
mysqli_stmt_close($stmt);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

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
$ticket_result = mysqli_query($conn, $ticket_query) or die(mysqli_error($conn));

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

// Handle session-based alerts
$alert_message = isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : "";
$alert_type = isset($_SESSION['alert_type']) ? $_SESSION['alert_type'] : "";
unset($_SESSION['alert_message']);
unset($_SESSION['alert_type']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $ticket_id = $_POST['ticket_id'];
        $new_status = $_POST['status'];
        $comment = trim($_POST['comment']);

        // Verify ticket_id exists
        $verify_query = "SELECT TicketID FROM tickets WHERE TicketID = ?";
        $verify_stmt = mysqli_prepare($conn, $verify_query);
        mysqli_stmt_bind_param($verify_stmt, "i", $ticket_id);
        mysqli_stmt_execute($verify_stmt);
        $verify_result = mysqli_stmt_get_result($verify_stmt);
        
        if (mysqli_num_rows($verify_result) == 0) {
            $_SESSION['alert_message'] = "Error: Invalid Ticket ID. Ticket does not exist.";
            $_SESSION['alert_type'] = "danger";
        } else {
            // Fetch ticket details
            $ticket_details_query = "SELECT tickets.*, hrm_employee.email AS EmployeeEmail, 
                                            CONCAT(hrm_employee.fname, ' ', hrm_employee.lname) AS EmployeeName
                                     FROM tickets 
                                     JOIN hrm_employee ON tickets.EmployeeID = hrm_employee.id 
                                     WHERE TicketID = ?";
            $ticket_stmt = mysqli_prepare($conn, $ticket_details_query);
            mysqli_stmt_bind_param($ticket_stmt, "i", $ticket_id);
            mysqli_stmt_execute($ticket_stmt);
            $ticket_details_result = mysqli_stmt_get_result($ticket_stmt);
            $ticket_details = mysqli_fetch_assoc($ticket_details_result);
            mysqli_stmt_close($ticket_stmt);

            // If status is "Resolved", update priority to "Low"
            $priority_update = $new_status === 'Resolved' ? ", Priority = 'Low'" : "";

            // Update ticket status
            $update_query = "UPDATE tickets SET Status = ? $priority_update WHERE TicketID = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "si", $new_status, $ticket_id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                // Save comment if provided
                if (!empty($comment)) {
                    $comment_query = "INSERT INTO ticket_comments (ticket_id, comment, commented_by) VALUES (?, ?, ?)";
                    $comment_stmt = mysqli_prepare($conn, $comment_query);
                    mysqli_stmt_bind_param($comment_stmt, "isi", $ticket_id, $comment, $emp_id);
                    if (!mysqli_stmt_execute($comment_stmt)) {
                        $_SESSION['alert_message'] = "Error saving comment: " . mysqli_error($conn);
                        $_SESSION['alert_type'] = "danger";
                    }
                    mysqli_stmt_close($comment_stmt);
                }

                $_SESSION['alert_message'] = "Ticket status updated successfully!";
                $_SESSION['alert_type'] = "success";

                // Send email to HR and employee with CC to shashank1 and shashank2
                $subject = "Ticket Status Updated: Ticket ID {$ticket_details['TicketID']}";
                $message = "The status of Ticket ID: {$ticket_details['TicketID']} has been updated to <strong>{$new_status}</strong>.<br><br>
                            <strong>Ticket Details:</strong><br>
                            Title: {$ticket_details['Title']}<br>
                            Description: {$ticket_details['Description']}<br>
                            Priority: {$ticket_details['Priority']}<br>
                            Employee: {$ticket_details['EmployeeName']}<br>";
                if (!empty($comment)) {
                    $message .= "<strong>Latest Comment:</strong><br>{$comment}<br>";
                }

                $cc_emails = [$ticket_details['EmployeeEmail'], 'pythondolphin@gmail.com', 'dolphinpython@outlook.com'];
                sendEmail(['hr@1solutions.biz'], $subject, $message, $cc_emails);
                sendEmail([$ticket_details['EmployeeEmail']], $subject, $message, ['pythondolphin@gmail.com', 'dolphinpython@outlook.com']);
            } else {
                $_SESSION['alert_message'] = "Error updating ticket status: " . mysqli_error($conn);
                $_SESSION['alert_type'] = "danger";
            }
            mysqli_stmt_close($update_stmt);
        }
        mysqli_stmt_close($verify_stmt);
        header("Location: view-ticket.php");
        exit();
    } elseif (isset($_POST['add_comment'])) {
        // Add Comment
        $ticket_id = $_POST['ticket_id'];
        $comment = trim($_POST['comment']);

        // Verify ticket exists and fetch details
        $verify_query = "SELECT tickets.TicketID, tickets.Title, tickets.Description, tickets.Priority, tickets.Status, 
                                CONCAT(e.fname, ' ', e.lname) AS employee_name, e.email AS creator_email
                         FROM tickets 
                         JOIN hrm_employee e ON tickets.EmployeeID = e.id 
                         WHERE tickets.TicketID = ?";
        $verify_stmt = mysqli_prepare($conn, $verify_query);
        mysqli_stmt_bind_param($verify_stmt, "i", $ticket_id);
        mysqli_stmt_execute($verify_stmt);
        $verify_result = mysqli_stmt_get_result($verify_stmt);
        
        if (mysqli_num_rows($verify_result) == 0) {
            $_SESSION['alert_message'] = "Error: Invalid Ticket ID.";
            $_SESSION['alert_type'] = "danger";
        } else {
            $ticket = mysqli_fetch_assoc($verify_result);
            $creator_email = $ticket['creator_email'];
            
            if ($ticket['Status'] === 'Closed') {
                $_SESSION['alert_message'] = "Error: Cannot add comments to a closed ticket.";
                $_SESSION['alert_type'] = "danger";
            } elseif (empty($comment)) {
                $_SESSION['alert_message'] = "Error: Comment cannot be empty.";
                $_SESSION['alert_type'] = "danger";
            } else {
                // Insert comment
                $comment_query = "INSERT INTO ticket_comments (ticket_id, comment, commented_by) VALUES (?, ?, ?)";
                $comment_stmt = mysqli_prepare($conn, $comment_query);
                mysqli_stmt_bind_param($comment_stmt, "isi", $ticket_id, $comment, $emp_id);
                
                if (mysqli_stmt_execute($comment_stmt)) {
                    // Send email to HR with creator, shashank1, and shashank2 in CC
                    $subject = "New Comment on Ticket ID $ticket_id by {$row['name']}";
                    $message = "A new comment has been added to Ticket ID: $ticket_id by {$row['name']}.<br><br>
                                <strong>Ticket Details:</strong><br>
                                Title: {$ticket['Title']}<br>
                                Description: {$ticket['Description']}<br>
                                Priority: {$ticket['Priority']}<br>
                                Employee: {$ticket['employee_name']}<br><br>
                                <strong>Latest Comment:</strong><br>
                                {$comment}";
                    $cc_emails = [$creator_email, 'pythondolphin@gmail.com', 'dolphinpython@outlook.com'];
                    sendEmail(['hr@1solutions.biz'], $subject, $message, $cc_emails);

                    $_SESSION['alert_message'] = "Comment added successfully!";
                    $_SESSION['alert_type'] = "success";
                } else {
                    $_SESSION['alert_message'] = "Error adding comment: " . mysqli_error($conn);
                    $_SESSION['alert_type'] = "danger";
                }
                mysqli_stmt_close($comment_stmt);
            }
        }
        mysqli_stmt_close($verify_stmt);
        header("Location: view-ticket.php");
        exit();
    }
}
?>
<?php

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

$active_employee = count_where("hrm_employee", "status", "2");
$inactive_employee = count_where("hrm_employee", "status", "0");
?>
<head>
    <title>HR Ticket Management</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
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
        .comment-box {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .comment-meta {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
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
                                                    <td>
    <?php
    $words = explode(' ', $ticket['Title']);
    $wrapped = '';
    foreach ($words as $i => $word) {
        $wrapped .= htmlspecialchars($word) . ' ';
        if (($i + 1) % 4 == 0) {
            $wrapped .= '<br>';
        }
    }
    echo $wrapped;
    ?>
</td>

                                                    <td><?php echo htmlspecialchars($ticket['CategoryName']); ?></td>
                                                    <td><?php echo htmlspecialchars($ticket['EmployeeName']); ?></td>
                                                    <td>
                                                        <span class="badge 
                                                            <?php echo $ticket['Priority'] === 'Low' ? 'bg-info' : ($ticket['Priority'] === 'Medium' ? 'bg-warning' : 'bg-danger'); ?>">
                                                            <?php echo $ticket['Priority']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge 
                                                            <?php echo $ticket['Status'] === 'Open' ? 'bg-success' : ($ticket['Status'] === 'In Progress' ? 'bg-warning' : ($ticket['Status'] === 'Resolved' ? 'bg-secondary' : 'bg-danger')); ?>">
                                                            <?php echo $ticket['Status']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($ticket['Status'] !== 'Closed' && $ticket['Status'] !== 'Resolved'): ?>
                                                            <select class="form-select form-select-sm status-select" 
                                                                    data-ticket-id="<?php echo $ticket['TicketID']; ?>"
                                                                    data-current-status="<?php echo $ticket['Status']; ?>">
                                                                <option value="Open" <?php echo $ticket['Status'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                                                                <option value="In Progress" <?php echo $ticket['Status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                                <option value="Resolved" <?php echo $ticket['Status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                                            </select>
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

                                                <!-- Modal for ticket details -->
                                                <div class="modal fade" id="ticketModal<?php echo $ticket['TicketID']; ?>" tabindex="-1" aria-labelledby="ticketModalLabel<?php echo $ticket['TicketID']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="ticketModalLabel<?php echo $ticket['TicketID']; ?>">Ticket Details - ID: <?php echo $ticket['TicketID']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Title:</strong> <?php echo htmlspecialchars($ticket['Title']); ?></p>
                                                                <p><strong>Category:</strong> <?php echo htmlspecialchars($ticket['CategoryName']); ?></p>
                                                                <p><strong>Description:</strong> <?php echo htmlspecialchars($ticket['Description']); ?></p>
                                                                <p><strong>Priority:</strong> <?php echo htmlspecialchars($ticket['Priority']); ?></p>
                                                                <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['Status']); ?></p>
                                                                <p><strong>Employee:</strong> <?php echo htmlspecialchars($ticket['EmployeeName']); ?></p>
                                                                <hr>
                                                                <h6>Comments</h6>
                                                                <?php
                                                                // Fetch comments for this ticket
                                                                $comment_query = "SELECT tc.comment, tc.created_at, CONCAT(e.fname, ' ', e.lname) AS commenter_name
                                                                                 FROM ticket_comments tc
                                                                                 JOIN hrm_employee e ON tc.commented_by = e.id
                                                                                 WHERE tc.ticket_id = ?
                                                                                 ORDER BY tc.created_at DESC";
                                                                $comment_stmt = mysqli_prepare($conn, $comment_query);
                                                                mysqli_stmt_bind_param($comment_stmt, "i", $ticket['TicketID']);
                                                                mysqli_stmt_execute($comment_stmt);
                                                                $comment_result = mysqli_stmt_get_result($comment_stmt);
                                                                
                                                                if (mysqli_num_rows($comment_result) > 0) {
                                                                    while ($comment = mysqli_fetch_assoc($comment_result)) {
                                                                        echo '<div class="comment-box">';
                                                                        echo '<div class="comment-meta">By ' . htmlspecialchars($comment['commenter_name']) . ' on ' . date('d M Y, H:i', strtotime($comment['created_at'])) . '</div>';
                                                                        echo '<p>' . htmlspecialchars($comment['comment']) . '</p>';
                                                                        echo '</div>';
                                                                    }
                                                                } else {
                                                                    echo '<p>No comments yet.</p>';
                                                                }
                                                                mysqli_stmt_close($comment_stmt);
                                                                ?>
                                                                <?php if ($ticket['Status'] !== 'Closed'): ?>
                                                                    <hr>
                                                                    <h6>Add a Comment</h6>
                                                                    <form method="POST" action="">
                                                                        <div class="form-group">
                                                                            <label for="comment_<?php echo $ticket['TicketID']; ?>">Comment</label>
                                                                            <textarea class="form-control" id="comment_<?php echo $ticket['TicketID']; ?>" name="comment" rows="4" required></textarea>
                                                                        </div>
                                                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['TicketID']; ?>">
                                                                        <button type="submit" name="add_comment" class="btn btn-primary mt-3">Submit Comment</button>
                                                                    </form>
                                                                <?php else: ?>
                                                                    <p class="text-muted mt-3">Commenting is disabled for closed tickets.</p>
                                                                <?php endif; ?>
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

                <!-- Comment Modal for Status Change -->
                <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="commentModalLabel">Add Comment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="comment" class="form-label">Comment</label>
                                        <textarea class="form-control" id="comment" name="comment" rows="4"></textarea>
                                    </div>
                                    <input type="hidden" name="ticket_id" id="modal_ticket_id">
                                    <input type="hidden" name="status" id="modal_status">
                                    <input type="hidden" name="update_status">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#ticketsTable').DataTable({
                paging: true,
                searching: true,
                ordering: false,
                responsive: true
            });

            // Handle status select change
            $('.status-select').on('change', function() {
                var ticketId = $(this).data('ticket-id');
                var newStatus = $(this).val();
                var currentStatus = $(this).data('current-status');

                if (newStatus !== currentStatus) {
                    // Set modal fields
                    $('#modal_ticket_id').val(ticketId);
                    $('#modal_status').val(newStatus);
                    $('#comment').val('');
                    
                    // Show modal
                    $('#commentModal').modal('show');
                } else {
                    // Reset select to current status
                    $(this).val(currentStatus);
                }
            });

            // Reset form on modal close
            $('#commentModal').on('hidden.bs.modal', function() {
                $('#comment').val('');
                $('#modal_ticket_id').val('');
                $('#modal_status').val('');
                // Reset all dropdowns to their current status
                $('.status-select').each(function() {
                    $(this).val($(this).data('current-status'));
                });
            });
        });
    </script>
</body>
</html>