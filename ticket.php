<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';
include 'email/send_ticket_email.php';

$conn = connect();
$emp_id = $_SESSION['id']; // Current logged-in user

// Fetch employee details
$employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name, email FROM hrm_employee";
$employee_result = mysqli_query($conn, $employee_query) or die(mysqli_error($conn));

// Fetch logged-in user details
$query = "SELECT id, CONCAT(fname, ' ', lname) AS name, email, image FROM hrm_employee WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $emp_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result);
mysqli_stmt_close($stmt);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$profile_image_dir = "upload-image";
$profile_image = !empty($row['image']) ? $profile_image_dir . "/" . $row['image'] : $profile_image_dir . "/default.png";

// Fetch categories from hrm_ticket_categories
$category_query = "SELECT id, name FROM hrm_ticket_categories";
$category_result = mysqli_query($conn, $category_query) or die(mysqli_error($conn));

// Fetch tickets created by the logged-in user
$ticket_query = "SELECT tickets.TicketID, tickets.Title, tickets.Description, tickets.Status, tickets.Priority, 
                        hrm_ticket_categories.name AS CategoryName, tickets.Rating 
                 FROM tickets 
                 JOIN hrm_ticket_categories ON tickets.CategoryID = hrm_ticket_categories.id 
                 WHERE tickets.EmployeeID = ? 
                 ORDER BY tickets.CreatedAt DESC";
$ticket_stmt = mysqli_prepare($conn, $ticket_query);
mysqli_stmt_bind_param($ticket_stmt, "i", $emp_id);
mysqli_stmt_execute($ticket_stmt);
$ticket_result = mysqli_stmt_get_result($ticket_stmt);

// Handle session-based alerts
$alert_message = isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : "";
$alert_type = isset($_SESSION['alert_type']) ? $_SESSION['alert_type'] : "";
// Clear session alerts after fetching
unset($_SESSION['alert_message']);
unset($_SESSION['alert_type']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_ticket'])) {
        // Create Ticket
        $category_id = $_POST['category_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $priority = $_POST['priority'];

        $insert_query = "INSERT INTO tickets (CategoryID, Title, Description, Priority, EmployeeID, Status) VALUES (?, ?, ?, ?, ?, 'Open')";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "isssi", $category_id, $title, $description, $priority, $emp_id);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $ticket_id = mysqli_insert_id($conn);

            // Send email to HR with creator, shashank1, and shashank2 in CC
            $subject = "Ticket Created: Ticket ID $ticket_id";
            $message = "A new ticket has been created by {$row['name']}.<br><br>
                        <strong>Ticket Details:</strong><br>
                        Ticket ID: $ticket_id<br>
                        Title: $title<br>
                        Description: $description<br>
                        Priority: $priority<br>
                        Employee: {$row['name']}";
            $cc_emails = [$row['email'], 'pythondolphin@gmail.com', 'dolphinpython@outlook.com'];
            sendEmail(['hr@1solutions.biz'], $subject, $message, $cc_emails);

            $_SESSION['alert_message'] = "Ticket created successfully!";
            $_SESSION['alert_type'] = "success";
        } else {
            $_SESSION['alert_message'] = "Error creating ticket: " . mysqli_error($conn);
            $_SESSION['alert_type'] = "danger";
        }
        mysqli_stmt_close($insert_stmt);
        header("Location: ticket.php");
        exit();
    } elseif (isset($_POST['close_ticket'])) {
        // Close Ticket with Rating
        $ticket_id = $_POST['ticket_id'];
        $rating = $_POST['rating'];

        // Validate rating (1 to 10)
        if ($rating < 1 || $rating > 10) {
            $_SESSION['alert_message'] = "Rating must be between 1 and 10.";
            $_SESSION['alert_type'] = "danger";
        } else {
            // Verify ticket exists and belongs to the employee, fetch ticket details and creator's email
            $verify_query = "SELECT tickets.TicketID, tickets.Title, tickets.Description, tickets.Priority, 
                                    e.email AS creator_email, CONCAT(e.fname, ' ', e.lname) AS employee_name
                             FROM tickets 
                             JOIN hrm_employee e ON tickets.EmployeeID = e.id 
                             WHERE tickets.TicketID = ? AND tickets.EmployeeID = ?";
            $verify_stmt = mysqli_prepare($conn, $verify_query);
            mysqli_stmt_bind_param($verify_stmt, "ii", $ticket_id, $emp_id);
            mysqli_stmt_execute($verify_stmt);
            $verify_result = mysqli_stmt_get_result($verify_stmt);

            if (mysqli_num_rows($verify_result) == 0) {
                $_SESSION['alert_message'] = "Error: Invalid Ticket ID or unauthorized action.";
                $_SESSION['alert_type'] = "danger";
            } else {
                $ticket = mysqli_fetch_assoc($verify_result);
                $creator_email = $ticket['creator_email'];

                // Update ticket status to Closed and add rating
                $close_query = "UPDATE tickets SET Status = 'Closed', Rating = ? WHERE TicketID = ? AND EmployeeID = ?";
                $close_stmt = mysqli_prepare($conn, $close_query);
                mysqli_stmt_bind_param($close_stmt, "iii", $rating, $ticket_id, $emp_id);
                
                if (mysqli_stmt_execute($close_stmt)) {
                    // Send email to HR with creator, shashank1, and shashank2 in CC
                    $subject = "Ticket Closed: Ticket ID $ticket_id";
                    $message = "Ticket ID: $ticket_id has been closed by {$row['name']}.<br><br>
                                <strong>Ticket Details:</strong><br>
                                Title: {$ticket['Title']}<br>
                                Description: {$ticket['Description']}<br>
                                Priority: {$ticket['Priority']}<br>
                                Employee: {$ticket['employee_name']}<br>
                                Rating: $rating";
                    $cc_emails = [$creator_email, 'pythondolphin@gmail.com', 'dolphinpython@outlook.com'];
                    sendEmail(['hr@1solutions.biz'], $subject, $message, $cc_emails);

                    $_SESSION['alert_message'] = "Ticket closed successfully!";
                    $_SESSION['alert_type'] = "success";
                } else {
                    $_SESSION['alert_message'] = "Error closing ticket: " . mysqli_error($conn);
                    $_SESSION['alert_type'] = "danger";
                }
                mysqli_stmt_close($close_stmt);
            }
            mysqli_stmt_close($verify_stmt);
        }
        header("Location: ticket.php");
        exit();
    } elseif (isset($_POST['reopen_ticket'])) {
        // Reopen Ticket
        $ticket_id = $_POST['ticket_id'];

        // Verify ticket exists and belongs to the employee, fetch ticket details and creator's email
        $verify_query = "SELECT tickets.TicketID, tickets.Title, tickets.Description, tickets.Priority, 
                                e.email AS creator_email, CONCAT(e.fname, ' ', e.lname) AS employee_name
                         FROM tickets 
                         JOIN hrm_employee e ON tickets.EmployeeID = e.id 
                         WHERE tickets.TicketID = ? AND tickets.EmployeeID = ?";
        $verify_stmt = mysqli_prepare($conn, $verify_query);
        mysqli_stmt_bind_param($verify_stmt, "ii", $ticket_id, $emp_id);
        mysqli_stmt_execute($verify_stmt);
        $verify_result = mysqli_stmt_get_result($verify_stmt);

        if (mysqli_num_rows($verify_result) == 0) {
            $_SESSION['alert_message'] = "Error: Invalid Ticket ID or unauthorized action.";
            $_SESSION['alert_type'] = "danger";
        } else {
            $ticket = mysqli_fetch_assoc($verify_result);
            $creator_email = $ticket['creator_email'];

            // Update ticket status to Reopened
            $reopen_query = "UPDATE tickets SET Status = 'Reopened' WHERE TicketID = ? AND EmployeeID = ?";
            $reopen_stmt = mysqli_prepare($conn, $reopen_query);
            mysqli_stmt_bind_param($reopen_stmt, "ii", $ticket_id, $emp_id);
            
            if (mysqli_stmt_execute($reopen_stmt)) {
                // Send email to HR with creator, shashank1, and shashank2 in CC
                $subject = "Ticket Reopened: Ticket ID $ticket_id";
                $message = "Ticket ID: $ticket_id has been reopened by {$row['name']}.<br><br>
                            <strong>Ticket Details:</strong><br>
                            Title: {$ticket['Title']}<br>
                            Description: {$ticket['Description']}<br>
                            Priority: {$ticket['Priority']}<br>
                            Employee: {$ticket['employee_name']}";
                $cc_emails = [$creator_email, 'pythondolphin@gmail.com', 'dolphinpython@outlook.com'];
                sendEmail(['hr@1solutions.biz'], $subject, $message, $cc_emails);

                $_SESSION['alert_message'] = "Ticket reopened successfully!";
                $_SESSION['alert_type'] = "success";
            } else {
                $_SESSION['alert_message'] = "Error reopening ticket: " . mysqli_error($conn);
                $_SESSION['alert_type'] = "danger";
            }
            mysqli_stmt_close($reopen_stmt);
        }
        mysqli_stmt_close($verify_stmt);
        header("Location: ticket.php");
        exit();
    } elseif (isset($_POST['delete_ticket'])) {
        // Delete Ticket
        $ticket_id = $_POST['ticket_id'];

        // Verify ticket exists and belongs to the employee, fetch ticket details and creator's email
        $verify_query = "SELECT tickets.TicketID, tickets.Title, tickets.Description, tickets.Priority, 
                                e.email AS creator_email, CONCAT(e.fname, ' ', e.lname) AS employee_name
                         FROM tickets 
                         JOIN hrm_employee e ON tickets.EmployeeID = e.id 
                         WHERE tickets.TicketID = ? AND tickets.EmployeeID = ?";
        $verify_stmt = mysqli_prepare($conn, $verify_query);
        mysqli_stmt_bind_param($verify_stmt, "ii", $ticket_id, $emp_id);
        mysqli_stmt_execute($verify_stmt);
        $verify_result = mysqli_stmt_get_result($verify_stmt);

        if (mysqli_num_rows($verify_result) == 0) {
            $_SESSION['alert_message'] = "Error: Invalid Ticket ID or unauthorized action.";
            $_SESSION['alert_type'] = "danger";
        } else {
            $ticket = mysqli_fetch_assoc($verify_result);
            $creator_email = $ticket['creator_email'];

            // Delete associated comments to avoid foreign key constraint
            $delete_comments_query = "DELETE FROM ticket_comments WHERE ticket_id = ?";
            $delete_comments_stmt = mysqli_prepare($conn, $delete_comments_query);
            mysqli_stmt_bind_param($delete_comments_stmt, "i", $ticket_id);
            mysqli_stmt_execute($delete_comments_stmt);
            mysqli_stmt_close($delete_comments_stmt);

            // Delete ticket
            $delete_query = "DELETE FROM tickets WHERE TicketID = ? AND EmployeeID = ?";
            $delete_stmt = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($delete_stmt, "ii", $ticket_id, $emp_id);
            
            if (mysqli_stmt_execute($delete_stmt)) {
                // Send email to HR with creator, shashank1, and shashank2 in CC
                $subject = "Ticket Deleted: Ticket ID $ticket_id";
                $message = "Ticket ID: $ticket_id has been deleted by {$row['name']}.<br><br>
                            <strong>Ticket Details:</strong><br>
                            Title: {$ticket['Title']}<br>
                            Description: {$ticket['Description']}<br>
                            Priority: {$ticket['Priority']}<br>
                            Employee: {$ticket['employee_name']}";
                $cc_emails = [$creator_email, 'pythondolphin@gmail.com', 'dolphinpython@outlook.com'];
                sendEmail(['hr@1solutions.biz'], $subject, $message, $cc_emails);

                $_SESSION['alert_message'] = "Ticket deleted successfully!";
                $_SESSION['alert_type'] = "success";
            } else {
                $_SESSION['alert_message'] = "Error deleting ticket: " . mysqli_error($conn);
                $_SESSION['alert_type'] = "danger";
            }
            mysqli_stmt_close($delete_stmt);
        }
        mysqli_stmt_close($verify_stmt);
        header("Location: ticket.php");
        exit();
    } elseif (isset($_POST['add_comment'])) {
        // Add Comment
        $ticket_id = $_POST['ticket_id'];
        $comment = trim($_POST['comment']);

        // Verify ticket exists and belongs to the employee, fetch ticket details and creator's email
        $verify_query = "SELECT tickets.TicketID, tickets.Title, tickets.Description, tickets.Priority, tickets.Status, 
                                CONCAT(e.fname, ' ', e.lname) AS employee_name, e.email AS creator_email
                         FROM tickets 
                         JOIN hrm_employee e ON tickets.EmployeeID = e.id 
                         WHERE tickets.TicketID = ? AND tickets.EmployeeID = ?";
        $verify_stmt = mysqli_prepare($conn, $verify_query);
        mysqli_stmt_bind_param($verify_stmt, "ii", $ticket_id, $emp_id);
        mysqli_stmt_execute($verify_stmt);
        $verify_result = mysqli_stmt_get_result($verify_stmt);
        
        if (mysqli_num_rows($verify_result) == 0) {
            $_SESSION['alert_message'] = "Error: Invalid Ticket ID or unauthorized action.";
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
                    $subject = "New Comment on Ticket ID $ticket_id";
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
        header("Location: ticket.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Ticket</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <style>
        .fixed-description {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                            <h3 class="page-title">Ticket</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Ticket</li>
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

                <!-- Create Ticket Form -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Create New Ticket</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="category_id">Category</label>
                                        <select class="form-control" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php while ($category = mysqli_fetch_assoc($category_result)): ?>
                                                <option value="<?php echo $category['id']; ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="priority">Priority</label>
                                        <select class="form-control" id="priority" name="priority" required>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="create_ticket" class="btn btn-primary mt-3">Create Ticket</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Create Ticket Form -->

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
                                                    <th>View</th>
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
                                                            <?php echo $ticket['Rating'] ? htmlspecialchars($ticket['Rating']) : 'N/A'; ?>
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
                                                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['TicketID']; ?>">
                                                                    <button type="submit" name="delete_ticket" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this ticket?');">Delete</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $ticket['TicketID']; ?>">View</button>
                                                        </td>
                                                    </tr>

                                                    <!-- View Modal -->
                                                    <div class="modal fade" id="viewModal<?php echo $ticket['TicketID']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?php echo $ticket['TicketID']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="viewModalLabel<?php echo $ticket['TicketID']; ?>">Ticket Details - ID: <?php echo $ticket['TicketID']; ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>Title:</strong> <?php echo htmlspecialchars($ticket['Title']); ?></p>
                                                                    <p><strong>Category:</strong> <?php echo htmlspecialchars($ticket['CategoryName']); ?></p>
                                                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($ticket['Description']); ?></p>
                                                                    <p><strong>Priority:</strong> <?php echo htmlspecialchars($ticket['Priority']); ?></p>
                                                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['Status']); ?></p>
                                                                    <p><strong>Rating:</strong> <?php echo $ticket['Rating'] ? htmlspecialchars($ticket['Rating']) : 'N/A'; ?></p>
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

                                                    <!-- Feedback Modal -->
                                                    <div class="modal fade" id="feedbackModal<?php echo $ticket['TicketID']; ?>" tabindex="-1" aria-labelledby="feedbackModalLabel<?php echo $ticket['TicketID']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="feedbackModalLabel<?php echo $ticket['TicketID']; ?>">Provide Rating</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="">
                                                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['TicketID']; ?>">
                                                                        <div class="form-group">
                                                                            <label for="rating_<?php echo $ticket['TicketID']; ?>">Rating (1-10)</label>
                                                                            <input type="number" class="form-control" id="rating_<?php echo $ticket['TicketID']; ?>" name="rating" min="1" max="10" required>
                                                                        </div>
                                                                        <button type="submit" name="close_ticket" class="btn btn-primary mt-3">Submit Rating & Close Ticket</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Reopen Modal -->
                                                    <div class="modal fade" id="reopenModal<?php echo $ticket['TicketID']; ?>" tabindex="-1" aria-labelledby="reopenModalLabel<?php echo $ticket['TicketID']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="reopenModalLabel<?php echo $ticket['TicketID']; ?>">Reopen Ticket</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to reopen this ticket?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <form method="POST" action="">
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
            </div>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('#ticketTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                responsive: true
            });
        });
    </script>
</body>
</html>
<?php mysqli_stmt_close($ticket_stmt); ?>