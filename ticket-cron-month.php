<?php
// Include necessary files
include 'include/db.php';
include 'email/send_ticket_email.php';

// Establish database connection using the connect() function from function.php
$conn = connect();

// Check if the connection was successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get last month's start and end dates
$last_month_start = date('Y-m-01', strtotime('last month'));
$last_month_end = date('Y-m-t', strtotime('last month'));

// Fetch counts for cards
$open_count_query = "SELECT COUNT(*) AS open_count FROM tickets WHERE Status = 'Open' AND CreatedAt BETWEEN '$last_month_start' AND '$last_month_end'";
$in_progress_count_query = "SELECT COUNT(*) AS in_progress_count FROM tickets WHERE Status = 'In Progress' AND CreatedAt BETWEEN '$last_month_start' AND '$last_month_end'";
$resolved_count_query = "SELECT COUNT(*) AS resolved_count FROM tickets WHERE Status = 'Resolved' AND CreatedAt BETWEEN '$last_month_start' AND '$last_month_end'";
$closed_count_query = "SELECT COUNT(*) AS closed_count FROM tickets WHERE Status = 'Closed' AND CreatedAt BETWEEN '$last_month_start' AND '$last_month_end'";
$reopened_count_query = "SELECT COUNT(*) AS reopened_count FROM tickets WHERE Status = 'Reopened' AND CreatedAt BETWEEN '$last_month_start' AND '$last_month_end'";

$open_count_result = mysqli_query($conn, $open_count_query);
$in_progress_count_result = mysqli_query($conn, $in_progress_count_query);
$resolved_count_result = mysqli_query($conn, $resolved_count_query);
$closed_count_result = mysqli_query($conn, $closed_count_query);
$reopened_count_result = mysqli_query($conn, $reopened_count_query);

$open_count = mysqli_fetch_assoc($open_count_result)['open_count'];
$in_progress_count = mysqli_fetch_assoc($in_progress_count_result)['in_progress_count'];
$resolved_count = mysqli_fetch_assoc($resolved_count_result)['resolved_count'];
$closed_count = mysqli_fetch_assoc($closed_count_result)['closed_count'];
$reopened_count = mysqli_fetch_assoc($reopened_count_result)['reopened_count'];

// Prepare email message
$subject = "Monthly Ticket Report for " . date('F Y', strtotime('last month'));

$message = "<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Monthly Ticket Report for " . date('F Y', strtotime('last month')) . "</h2>
    <table>
        <tr>
            <th>Status</th>
            <th>Count</th>
        </tr>
        <tr>
            <td>Open</td>
            <td>" . $open_count . "</td>
        </tr>
        <tr>
            <td>In Progress</td>
            <td>" . $in_progress_count . "</td>
        </tr>
        <tr>
            <td>Resolved</td>
            <td>" . $resolved_count . "</td>
        </tr>
        <tr>
            <td>Closed</td>
            <td>" . $closed_count . "</td>
        </tr>
        <tr>
            <td>Reopened</td>
            <td>" . $reopened_count . "</td>
        </tr>
    </table>
</body>
</html>";

// Send email
sendEmail(['hr@1solutions.biz'], $subject, $message);

echo "Email sent successfully!";

// Close the database connection
mysqli_close($conn);
?>