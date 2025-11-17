<?php
// Start output buffering to prevent stray output
ob_start();

// Include database configuration
include 'layouts/config.php';

// Check database connection
if ($con->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $con->connect_error]);
    ob_end_flush();
    exit;
}

// Configure error handling (log errors, don't display)
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_errors.log');

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'get_employees':
            $filter = $_POST['filter'] ?? 'all';
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';

            // Sanitize inputs
            $start_date = $con->real_escape_string($start_date);
            $end_date = $con->real_escape_string($end_date);

            $sql = "SELECT e.id, CONCAT(e.fname, ' ', e.lname) AS name, e.email, d.name AS department, 
                           des.name AS designation, e.salary AS current_salary,
                           (SELECT COUNT(*) FROM hrm_advance_salary a WHERE a.emp_id = e.id AND a.status = 1) AS has_active_advance
                    FROM hrm_employee e 
                    LEFT JOIN hrm_department d ON e.department_id = d.id
                    LEFT JOIN hrm_designation des ON e.designation_id = des.id
                    WHERE e.status = 1";
            
            if ($filter === 'with_advance') {
                $sql .= " AND EXISTS (SELECT 1 FROM hrm_advance_salary a WHERE a.emp_id = e.id AND a.status = 1";
                if ($start_date && $end_date) {
                    $sql .= " AND a.advance_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
                } elseif ($start_date) {
                    $sql .= " AND a.advance_date >= '$start_date 00:00:00'";
                } elseif ($end_date) {
                    $sql .= " AND a.advance_date <= '$end_date 23:59:59'";
                }
                $sql .= ")";
            }

            $result = $con->query($sql);
            if (!$result) {
                error_log('Get employees query failed: ' . $con->error);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $con->error]);
                ob_end_flush();
                exit;
            }

            $output = '';
            while ($row = $result->fetch_assoc()) {
                $current_salary = floatval($row['current_salary']);
                $department = isset($row['department']) ? htmlspecialchars($row['department']) : 'N/A';
                $designation = isset($row['designation']) ? htmlspecialchars($row['designation']) : 'N/A';
                $name = htmlspecialchars($row['name'] ?? '');
                $email = htmlspecialchars($row['email'] ?? '');
                $advance_badge = $row['has_active_advance'] > 0 ? '<span class="badge bg-warning advance-badge">Has Advance</span>' : '';

                $output .= "<tr>
                    <td>{$row['id']}</td>
                    <td>$name</td>
                    <td>$email</td>
                    <td>$department</td>
                    <td>$designation</td>
                    <td>" . number_format($current_salary, 2) . "</td>
                    <td>$advance_badge</td>
                    <td>
                        <button class='btn btn-sm btn-primary action-btn' onclick='openAdvanceModal({$row['id']}, \"" . htmlspecialchars($name, ENT_QUOTES) . "\")'>Advance</button>
                        <button class='btn btn-sm btn-info action-btn' onclick='manageDeductions({$row['id']}, \"" . htmlspecialchars($name, ENT_QUOTES) . "\")'>Manage</button>
                    </td>
                </tr>";
            }
            header('Content-Type: text/html');
            echo $output;
            break;

        case 'add_advance':
            header('Content-Type: application/json');

            $emp_id = intval($_POST['emp_id'] ?? 0);
            $advance_amount = floatval($_POST['advance_amount'] ?? 0);
            $monthly_deduction = floatval($_POST['monthly_deduction'] ?? 0);
            $advance_date = $_POST['advance_date'] ?? '';

            // Validate inputs
            if ($emp_id <= 0 || $advance_amount <= 0 || $monthly_deduction <= 0 || empty($advance_date)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
                ob_end_flush();
                exit;
            }

            // Get current employee salary
            $sql = "SELECT salary FROM hrm_employee WHERE id = $emp_id";
            $result = $con->query($sql);
            if (!$result || $result->num_rows == 0) {
                echo json_encode(['status' => 'error', 'message' => 'Employee not found']);
                ob_end_flush();
                exit;
            }
            $actual_salary = floatval($result->fetch_assoc()['salary'] ?? 0);

            // Initialize or update salary management
            $sql = "INSERT INTO hrm_salary_management (emp_id, actual_salary, current_salary)
                    VALUES ($emp_id, $actual_salary, $actual_salary)
                    ON DUPLICATE KEY UPDATE actual_salary = $actual_salary, current_salary = $actual_salary";
            if (!$con->query($sql)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update salary management: ' . $con->error]);
                ob_end_flush();
                exit;
            }

            // Add advance record
            $sql = "INSERT INTO hrm_advance_salary (emp_id, advance_amount, monthly_deduction, remaining_amount, advance_date)
                    VALUES ($emp_id, $advance_amount, $monthly_deduction, $advance_amount, '$advance_date')";
            if (!$con->query($sql)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add advance: ' . $con->error]);
                ob_end_flush();
                exit;
            }

            // Log advance in deduction history
            $advance_id = $con->insert_id;
            $month_year = date('Y-m', strtotime($advance_date));
            $sql = "INSERT INTO hrm_deduction_history (advance_id, emp_id, actual_salary, advance_amount, deduction_amount, remaining_amount, deduction_date, month_year)
                    VALUES ($advance_id, $emp_id, $actual_salary, $advance_amount, 0, $advance_amount, '$advance_date', '$month_year')";
            if (!$con->query($sql)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to log advance history: ' . $con->error]);
                ob_end_flush();
                exit;
            }

            echo json_encode(['status' => 'success', 'message' => 'Advance added successfully']);
            ob_end_flush();
            break;

        case 'edit_deduction':
            header('Content-Type: application/json');

            $advance_id = intval($_POST['advance_id'] ?? 0);
            $monthly_deduction = floatval($_POST['monthly_deduction'] ?? 0);

            // Validate inputs
            if ($advance_id <= 0 || $monthly_deduction <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid advance ID or deduction amount']);
                ob_end_flush();
                exit;
            }

            // Update monthly deduction
            $sql = "UPDATE hrm_advance_salary 
                    SET monthly_deduction = $monthly_deduction 
                    WHERE id = $advance_id";
            if ($con->query($sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Monthly deduction updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update deduction: ' . $con->error]);
            }
            ob_end_flush();
            break;

        case 'delete_advance':
            header('Content-Type: application/json');

            $advance_id = intval($_POST['advance_id'] ?? 0);

            // Validate input
            if ($advance_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid advance ID']);
                ob_end_flush();
                exit;
            }

            // Delete deduction history
            $sql = "DELETE FROM hrm_deduction_history WHERE advance_id = $advance_id";
            if (!$con->query($sql)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete deduction history: ' . $con->error]);
                ob_end_flush();
                exit;
            }

            // Delete advance
            $sql = "DELETE FROM hrm_advance_salary WHERE id = $advance_id";
            if ($con->query($sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Advance deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete advance: ' . $con->error]);
            }
            ob_end_flush();
            break;

        case 'get_deductions':
            header('Content-Type: application/json');

            $emp_id = intval($_POST['emp_id'] ?? 0);
            if ($emp_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid employee ID']);
                ob_end_flush();
                exit;
            }

            // Get employee details
            $sql = "SELECT id, CONCAT(fname, ' ', lname) AS name, email, salary AS current_salary 
                    FROM hrm_employee WHERE id = $emp_id";
            $result = $con->query($sql);
            if (!$result || $result->num_rows == 0) {
                echo json_encode(['status' => 'error', 'message' => 'Employee not found']);
                ob_end_flush();
                exit;
            }
            $employee = $result->fetch_assoc();
            $current_salary = number_format(floatval($employee['current_salary'] ?? 0), 2);
            $emp_name = htmlspecialchars($employee['name'] ?? 'N/A');
            $emp_email = htmlspecialchars($employee['email'] ?? 'N/A');
            $emp_id_display = $employee['id'];

            // Get advance details
            $sql = "SELECT id, advance_amount, monthly_deduction, remaining_amount, advance_date, status
                    FROM hrm_advance_salary
                    WHERE emp_id = $emp_id";
            $result = $con->query($sql);
            if (!$result) {
                echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $con->error]);
                ob_end_flush();
                exit;
            }
            $advance_html = '';
            while ($row = $result->fetch_assoc()) {
                $status_text = $row['status'] == 1 ? 'Active' : ($row['status'] == 2 ? 'Paused' : 'Completed');
                $action = '';
                if ($row['status'] == 1) {
                    $action .= "<button class='btn btn-sm btn-warning action-btn' onclick='toggleDeduction({$row['id']}, 2)'>Pause</button>";
                } elseif ($row['status'] == 2) {
                    $action .= "<button class='btn btn-sm btn-success action-btn' onclick='toggleDeduction({$row['id']}, 1)'>Resume</button>";
                }
                if ($row['status'] != 0) {
                    $action .= "<button class='btn btn-sm btn-primary action-btn' onclick='openEditDeductionModal({$row['id']}, {$row['monthly_deduction']})'>✏️</button>";
                    $action .= "<button class='btn btn-sm btn-danger action-btn' onclick='deleteAdvance({$row['id']})'>🗑️</button>";
                }
                $advance_html .= "<tr>
                    <td>" . number_format(floatval($row['advance_amount'] ?? 0), 2) . "</td>
                    <td>" . number_format(floatval($row['remaining_amount'] ?? 0), 2) . "</td>
                    <td>" . number_format(floatval($row['monthly_deduction'] ?? 0), 2) . "</td>
                    <td>" . htmlspecialchars($row['advance_date'] ?? '') . "</td>
                    <td>$status_text</td>
                    <td>$action</td>
                </tr>";
            }

            // Get deduction history
            $sql = "SELECT actual_salary, advance_amount, deduction_amount, remaining_amount, deduction_date, month_year
                    FROM hrm_deduction_history
                    WHERE emp_id = $emp_id
                    ORDER BY deduction_date DESC";
            $result = $con->query($sql);
            if (!$result) {
                echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $con->error]);
                ob_end_flush();
                exit;
            }
            $history_html = '';
            while ($row = $result->fetch_assoc()) {
                $history_html .= "<tr>
                    <td>$emp_id_display</td>
                    <td>$emp_name</td>
                    <td>$emp_email</td>
                    <td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>
                    <td>" . number_format(floatval($row['actual_salary'] ?? 0), 2) . "</td>
                    <td>" . number_format(floatval($row['advance_amount'] ?? 0), 2) . "</td>
                    <td>" . number_format(floatval($row['remaining_amount'] ?? 0), 2) . "</td>
                    <td>" . number_format(floatval($row['deduction_amount'] ?? 0), 2) . "</td>
                    <td>" . htmlspecialchars($row['deduction_date'] ?? '') . "</td>
                </tr>";
            }

            echo json_encode([
                'status' => 'success',
                'emp_id' => $emp_id_display,
                'emp_name' => $emp_name,
                'emp_email' => $emp_email,
                'current_salary' => $current_salary,
                'advance_html' => $advance_html,
                'history_html' => $history_html
            ]);
            ob_end_flush();
            break;

        case 'toggle_deduction':
            header('Content-Type: application/json');

            $advance_id = intval($_POST['advance_id'] ?? 0);
            $status = intval($_POST['status'] ?? 0);

            if ($advance_id <= 0 || !in_array($status, [1, 2])) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid advance ID or status']);
                ob_end_flush();
                exit;
            }

            $sql = "UPDATE hrm_advance_salary SET status = $status WHERE id = $advance_id";
            if ($con->query($sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Deduction status updated']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update deduction: ' . $con->error]);
            }
            ob_end_flush();
            break;

        default:
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            ob_end_flush();
            break;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
    ob_end_flush();
}

$con->close();
ob_end_flush();
?>