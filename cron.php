<?php
// Start output buffering
ob_start();

// Include database configuration
include 'layouts/config.php';

// Check database connection
if ($con->connect_error) {
    error_log('Cron: Database connection failed: ' . $con->connect_error);
    ob_end_flush();
    exit;
}

// Configure error handling
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_errors.log');

// Get current month and year
$month_year = date('Y-m');
$deduction_date = date('Y-m-d H:i:s');

// Reset salaries
$sql = "UPDATE hrm_employee e
        JOIN hrm_salary_management s ON e.id = s.emp_id
        SET e.salary = s.actual_salary, s.current_salary = s.actual_salary";
if (!$con->query($sql)) {
    error_log('Cron: Failed to reset salaries: ' . $con->error);
    ob_end_flush();
    exit;
}

// Apply active deductions
$sql = "SELECT a.id AS advance_id, a.emp_id, a.monthly_deduction, a.advance_amount, a.remaining_amount, s.actual_salary
        FROM hrm_advance_salary a
        JOIN hrm_salary_management s ON a.emp_id = s.emp_id
        WHERE a.status = 1";
$result = $con->query($sql);
if (!$result) {
    error_log('Cron: Failed to fetch active advances: ' . $con->error);
    ob_end_flush();
    exit;
}

while ($row = $result->fetch_assoc()) {
    $advance_id = $row['advance_id'];
    $emp_id = $row['emp_id'];
    $monthly_deduction = floatval($row['monthly_deduction']);
    $advance_amount = floatval($row['advance_amount']);
    $remaining_amount = floatval($row['remaining_amount']);
    $actual_salary = floatval($row['actual_salary']);

    // Calculate new remaining amount
    $new_remaining = max(0, $remaining_amount - $monthly_deduction);

    // Update salaries and advance
    $sql = "UPDATE hrm_employee e
            JOIN hrm_salary_management s ON e.id = s.emp_id
            JOIN hrm_advance_salary a ON a.emp_id = e.id
            SET e.salary = e.salary - $monthly_deduction,
                s.current_salary = s.current_salary - $monthly_deduction,
                a.remaining_amount = $new_remaining,
                a.status = IF($new_remaining <= 0, 0, a.status)
            WHERE e.id = $emp_id AND a.id = $advance_id";
    if (!$con->query($sql)) {
        error_log("Cron: Failed to update for emp_id $emp_id, advance_id $advance_id: " . $con->error);
        continue;
    }

    // Log deduction in history
    $sql = "INSERT INTO hrm_deduction_history (advance_id, emp_id, actual_salary, advance_amount, deduction_amount, remaining_amount, deduction_date, month_year)
            VALUES ($advance_id, $emp_id, $actual_salary, $advance_amount, $monthly_deduction, $new_remaining, '$deduction_date', '$month_year')";
    if (!$con->query($sql)) {
        error_log("Cron: Failed to log deduction for emp_id $emp_id: " . $con->error);
        continue;
    }
}

error_log('Cron: Monthly deductions processed successfully');
ob_end_flush();
$con->close();
?>