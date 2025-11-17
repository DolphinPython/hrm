<?php
include 'layouts/config.php';
include 'email/mailer.php'; 
$conn = $con;


$sql = "SELECT 
            e.id, 
            CONCAT(e.fname, ' ', e.lname) AS employee_name,
            (SUM(CASE WHEN eos.status = 1 THEN 1 ELSE 0 END) / COUNT(eos.step_id) * 100) AS completion_percentage
        FROM hrm_employee e
        LEFT JOIN employee_onboarding_steps eos ON e.id = eos.employee_id
        GROUP BY e.id, e.fname, e.lname
        HAVING completion_percentage < 100 OR completion_percentage IS NULL";
$result = $conn->query($sql);

$employee_list = "";
$has_incomplete = false;

if ($result->num_rows > 0) {
    $has_incomplete = true;
    while ($row = $result->fetch_assoc()) {
        $percentage = $row['completion_percentage'] !== null ? round($row['completion_percentage'], 2) : 0;
        $employee_list .= "<tr><td>" . $row['id'] . "</td><td>" . $row['employee_name'] . "</td><td>" . $percentage . "%</td></tr>";
    }
} else {
    
    echo "No employees with incomplete steps. Email not sent.";
    $conn->close();
    exit; 
}


$to = "hr@1solutions.biz";
$cc_emails = ["pythondolphin@gmail.com", "dolphinpython@outlook.com"];
$subject = "Notification: Incomplete Employee Onboarding Steps";

$message = "
<html>
<head>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<p>Dear HR,</p>
<p>This is an automated notification from the HRM Pulse CRM dashboard. <br><br>

Please review the following employee profiles and complete the remaining processes:</p>
<h3>Employees with Incomplete Onboarding Process</h3>
<table>
    <tr>
        <th>Employee ID</th>
        <th>Employee Name</th>
        <th>Completed Percentage</th>
    </tr>
    " . $employee_list . "
</table>
<p><strong>Action Required:</strong><br>
Once the process is finalized, kindly update the status in the system.</p>
<p>Thank you.<br>
HRM Pulse CRM</p>
<p>(This is a system-generated email, no reply is required.)</p>
</body>
</html>
";

// Send email only if there are incomplete steps
if ($has_incomplete) {
    $email_sent = send_email($to, $subject, $message, $cc_emails);
    if ($email_sent) {
        echo "Email sent successfully.";
    } else {
        echo "Failed to send email.";
    }
}

$conn->close();
?>