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

// Query to fetch tickets with status 'Open', 'In Progress', 'Reopen', or 'Reopened'
$ticket_query = "SELECT tickets.TicketID, tickets.Title, tickets.Description, tickets.Status, tickets.Priority, hrm_ticket_categories.name AS CategoryName, tickets.Rating 
                 FROM tickets 
                 JOIN hrm_ticket_categories ON tickets.CategoryID = hrm_ticket_categories.id 
                 WHERE tickets.Status IN ('Open', 'In Progress', 'Reopened')
                 ORDER BY tickets.CreatedAt DESC";

$ticket_result = mysqli_query($conn, $ticket_query) or die(mysqli_error($conn));

// Check if there are any tickets
if (mysqli_num_rows($ticket_result) > 0) {
    // Start HTML table
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
        <h2> Daily HRM System Reminder: Pending Ticket Alerts:</h2>
        <table>
            <tr>
                <th>Ticket ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Category</th>
                <th>Rating</th>
            </tr>";

    // Loop through the tickets and prepare the email message
    while ($row = mysqli_fetch_assoc($ticket_result)) {
        $message .= "<tr>
                        <td>" . $row['TicketID'] . "</td>
                        <td>" . $row['Title'] . "</td>
                        <td>" . $row['Description'] . "</td>
                        <td>" . $row['Status'] . "</td>
                        <td>" . $row['Priority'] . "</td>
                        <td>" . $row['CategoryName'] . "</td>
                        <td>" . $row['Rating'] . "</td>
                    </tr>";
    }

    // Close HTML table
    $message .= "</table>
    </body>
    </html>";

    // Email subject
    $subject = "Daily Report: Tickets - Open, In Progress, or Reopened Status";

    // Send email
    sendEmail(['hr@1solutions.biz'], $subject, $message);

    echo "Email sent successfully!";
} else {
    echo "No tickets found with status 'Open', 'In Progress', or 'Reopened'.";
}

// Close the database connection
mysqli_close($conn);
?>