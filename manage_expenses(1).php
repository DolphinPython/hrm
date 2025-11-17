<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'layouts/session.php';
include 'layouts/head-main.php';
include 'layouts/config.php';
include 'include/function.php';

// Only allow Admins (IDs 10, 14)
$admin_id = $_SESSION['id'] ?? 0;
// if (!in_array($admin_id, [10, 14])) {
//     die("Access denied.");
// }
$role_query = "SELECT role FROM hrm_employee WHERE id = '$admin_id'";
$role_result = mysqli_query($con, $role_query) or die(mysqli_error($conn));
$role_row = mysqli_fetch_assoc($role_result);
$is_admin = ($role_row && in_array(strtolower($role_row['role']), ['admin', 'super admin']));
if (!$is_admin) {
    die("Access denied.");
}
// Initialize alert message
$alert = "";

// Handle success/error messages from query parameters
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $alert = "<div class='alert alert-success'>Expense added successfully!</div>";
            break;
        case 'updated':
            $alert = "<div class='alert alert-info'>Expense updated successfully!</div>";
            break;
        case 'approved':
            $alert = "<div class='alert alert-success'>Expense approved successfully!</div>";
            break;
        case 'rejected':
            $alert = "<div class='alert alert-success'>Expense rejected successfully!</div>";
            break;
        case 'deleted':
            $alert = "<div class='alert alert-success'>Expense deleted successfully!</div>";
            break;
    }
}
if (isset($_GET['error'])) {
    $alert = "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error']) . "</div>";
}

// Handle Approve/Reject/Delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expense_id'], $_POST['action'])) {
    $expense_id = intval($_POST['expense_id']);
    $action = $_POST['action'];

    try {
        if ($action === 'Approved' || $action === 'Rejected') {
            $stmt = $con->prepare("UPDATE employee_expenses SET status=?, approved_by=?, approved_at=NOW() WHERE id=?");
            $stmt->bind_param("sii", $action, $admin_id, $expense_id);
            if ($stmt->execute()) {
                $redirect_url = $_SERVER['PHP_SELF'] . "?success=" . strtolower($action);
                if (isset($_GET['status'])) $redirect_url .= "&status=" . urlencode($_GET['status']);
                if (isset($_GET['employee'])) $redirect_url .= "&employee=" . urlencode($_GET['employee']);
                if (isset($_GET['company'])) $redirect_url .= "&company=" . urlencode($_GET['company']);
                if (isset($_GET['month'])) $redirect_url .= "&month=" . urlencode($_GET['month']);
                if (isset($_GET['week'])) $redirect_url .= "&week=" . urlencode($_GET['week']);
                if (isset($_GET['from_date'])) $redirect_url .= "&from_date=" . urlencode($_GET['from_date']);
                if (isset($_GET['to_date'])) $redirect_url .= "&to_date=" . urlencode($_GET['to_date']);
                header("Location: $redirect_url");
                exit;
            } else {
                $alert = "<div class='alert alert-danger'>Error updating expense: " . $con->error . "</div>";
            }
            $stmt->close();
        } elseif ($action === 'Delete') {
            $stmt = $con->prepare("DELETE FROM employee_expenses WHERE id=?");
            $stmt->bind_param("i", $expense_id);
            if ($stmt->execute()) {
                $redirect_url = $_SERVER['PHP_SELF'] . "?success=deleted";
                if (isset($_GET['status'])) $redirect_url .= "&status=" . urlencode($_GET['status']);
                if (isset($_GET['employee'])) $redirect_url .= "&employee=" . urlencode($_GET['employee']);
                if (isset($_GET['company'])) $redirect_url .= "&company=" . urlencode($_GET['company']);
                if (isset($_GET['month'])) $redirect_url .= "&month=" . urlencode($_GET['month']);
                if (isset($_GET['week'])) $redirect_url .= "&week=" . urlencode($_GET['week']);
                if (isset($_GET['from_date'])) $redirect_url .= "&from_date=" . urlencode($_GET['from_date']);
                if (isset($_GET['to_date'])) $redirect_url .= "&to_date=" . urlencode($_GET['to_date']);
                header("Location: $redirect_url");
                exit;
            } else {
                $alert = "<div class='alert alert-danger'>Error deleting expense: " . $con->error . "</div>";
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $alert = "<div class='alert alert-danger'>Error processing action: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Handle Add/Update Expense
$edit_id = 0;
$edit_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expense_form'])) {
    try {
        $employee_id = isset($_POST['employee_id']) && $_POST['employee_id'] !== '' ? intval($_POST['employee_id']) : null;
        $company_id = isset($_POST['company_id']) && $_POST['company_id'] !== '' ? intval($_POST['company_id']) : null;
        $expense_date = $_POST['expense_date'];
        $category_id = intval($_POST['category_id']);
        $amount = floatval($_POST['amount']);
        $description = trim($_POST['description']);
        $payment_method = $_POST['payment_method'] ?? null;
        $reference_id = trim($_POST['reference_id'] ?? '');
        $expense_id = isset($_POST['expense_id']) ? intval($_POST['expense_id']) : 0;

        if ($employee_id === null && $company_id === null) {
            throw new Exception('Either an employee or a company must be selected.');
        }

        $receipt_path = '';
        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "Uploads/receipts/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $filename = basename($_FILES['receipt']['name']);
            $targetFile = $targetDir . time() . "_" . $filename;
            if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetFile)) {
                $receipt_path = $targetFile;
            } else {
                $alert = "<div class='alert alert-danger'>Error uploading receipt.</div>";
            }
        }

        if ($expense_id > 0) {
            $sql = "UPDATE employee_expenses SET employee_id=?, company_id=?, expense_date=?, category_id=?, amount=?, description=?, payment_method=?, reference_id=?";
            $params = [$employee_id, $company_id, $expense_date, $category_id, $amount, $description, $payment_method, $reference_id];
            $types = ($employee_id !== null ? "i" : "s") . ($company_id !== null ? "i" : "s") . "sids";

            if ($receipt_path) {
                $sql .= ", receipt_path=?";
                $params[] = $receipt_path;
                $types .= "s";
            }
            $sql .= " WHERE id=?";
            $params[] = $expense_id;
            $types .= "sss"; // payment_method, reference_id, and id

            $stmt = $con->prepare($sql);
            $stmt->bind_param($types, ...$params);
            if ($stmt->execute()) {
                $redirect_url = $_SERVER['PHP_SELF'] . "?success=updated";
                if (isset($_GET['status'])) $redirect_url .= "&status=" . urlencode($_GET['status']);
                if (isset($_GET['employee'])) $redirect_url .= "&employee=" . urlencode($_GET['employee']);
                if (isset($_GET['company'])) $redirect_url .= "&company=" . urlencode($_GET['company']);
                if (isset($_GET['month'])) $redirect_url .= "&month=" . urlencode($_GET['month']);
                if (isset($_GET['week'])) $redirect_url .= "&week=" . urlencode($_GET['week']);
                if (isset($_GET['from_date'])) $redirect_url .= "&from_date=" . urlencode($_GET['from_date']);
                if (isset($_GET['to_date'])) $redirect_url .= "&to_date=" . urlencode($_GET['to_date']);
                header("Location: $redirect_url");
                exit;
            } else {
                $alert = "<div class='alert alert-danger'>Error updating expense: " . $con->error . "</div>";
            }
            $stmt->close();
        } else {
            $sql = "INSERT INTO employee_expenses (employee_id, company_id, expense_date, category_id, amount, description, payment_method, reference_id, receipt_path, status, submitted_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sisidssss", $employee_id, $company_id, $expense_date, $category_id, $amount, $description, $payment_method, $reference_id, $receipt_path);
            if ($stmt->execute()) {
                $redirect_url = $_SERVER['PHP_SELF'] . "?success=added";
                if (isset($_GET['status'])) $redirect_url .= "&status=" . urlencode($_GET['status']);
                if (isset($_GET['employee'])) $redirect_url .= "&employee=" . urlencode($_GET['employee']);
                if (isset($_GET['company'])) $redirect_url .= "&company=" . urlencode($_GET['company']);
                if (isset($_GET['month'])) $redirect_url .= "&month=" . urlencode($_GET['month']);
                if (isset($_GET['week'])) $redirect_url .= "&week=" . urlencode($_GET['week']);
                if (isset($_GET['from_date'])) $redirect_url .= "&from_date=" . urlencode($_GET['from_date']);
                if (isset($_GET['to_date'])) $redirect_url .= "&to_date=" . urlencode($_GET['to_date']);
                header("Location: $redirect_url");
                exit;
            } else {
                $alert = "<div class='alert alert-danger'>Error adding expense: " . $con->error . "</div>";
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $alert = "<div class='alert alert-danger'>Error processing expense: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Handle Edit
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    try {
        $edit_id = intval($_GET['edit']);
        $stmt = $con->prepare("SELECT * FROM employee_expenses WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $edit_data = $result->fetch_assoc();
            error_log("Edit Data: " . print_r($edit_data, true));
        } else {
            $alert = "<div class='alert alert-warning'>Expense not found.</div>";
        }
        $stmt->close();
    } catch (Exception $e) {
        $alert = "<div class='alert alert-danger'>Error fetching expense: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Filters
$status_filter = $_GET['status'] ?? '';
$employee_filter = isset($_GET['employee']) && is_numeric($_GET['employee']) ? intval($_GET['employee']) : 0;
$company_filter = isset($_GET['company']) && is_numeric($_GET['company']) ? intval($_GET['company']) : 0;
$month_filter = $_GET['month'] ?? '';
$week_filter = $_GET['week'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

$status_query = $status_filter ? " AND ee.status='" . $con->real_escape_string($status_filter) . "'" : "";
$employee_query = $employee_filter ? " AND ee.employee_id=$employee_filter" : "";
$company_query = $company_filter ? " AND ee.company_id=$company_filter" : "";
$month_query = $month_filter ? " AND DATE_FORMAT(ee.expense_date, '%Y-%m') = '" . $con->real_escape_string($month_filter) . "'" : "";
$week_query = "";
if ($month_filter && $week_filter && in_array($week_filter, ['1', '2', '3', '4', '5'])) {
    try {
        $month_start = new DateTime("$month_filter-01");
        $week_start = clone $month_start;
        $week_start->modify("+ " . (($week_filter - 1) * 7) . " days");
        $week_end = clone $week_start;
        $week_end->modify("+6 days");
        if ($week_end->format('Y-m') !== $month_filter) {
            $week_end = new DateTime("$month_filter-" . $month_start->format('t'));
        }
        $week_query = " AND ee.expense_date BETWEEN '" . $week_start->format('Y-m-d') . "' AND '" . $week_end->format('Y-m-d') . "'";
    } catch (Exception $e) {
        $alert = "<div class='alert alert-danger'>Invalid week filter: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
$date_query = ($from_date && $to_date) ? " AND ee.expense_date BETWEEN '" . $con->real_escape_string($from_date) . "' AND '" . $con->real_escape_string($to_date) . "'" : "";

// Get available months for filter
$months = [];
try {
    $month_result = $con->query("SELECT DISTINCT DATE_FORMAT(expense_date, '%Y-%m') AS month FROM employee_expenses ORDER BY month DESC");
    while ($row = $month_result->fetch_assoc()) {
        $months[] = $row['month'];
    }
} catch (Exception $e) {
    $alert = "<div class='alert alert-danger'>Error fetching months: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Calculate Summaries
$total = 0;
$monthly_totals = [];
$entity_totals = [];
$entity_monthly_totals = [];

try {
    $sql = "SELECT ee.*, 
                   COALESCE(CONCAT(e.fname, ' ', e.lname), c.name) AS entity_name,
                   DATE_FORMAT(ee.expense_date, '%Y-%m') AS month,
                   ee.employee_id,
                   ee.company_id
            FROM employee_expenses ee
            LEFT JOIN hrm_employee e ON ee.employee_id = e.id
            LEFT JOIN companiesexpense c ON ee.company_id = c.id
            WHERE 1=1 $status_query $employee_query $company_query $month_query $week_query $date_query";
    $result = $con->query($sql);

    while ($row = $result->fetch_assoc()) {
        $total += $row['amount'];
        $month = $row['month'];
        $entity = $row['entity_name'];
        $entity_id = $row['employee_id'] ? 'emp_' . $row['employee_id'] : 'com_' . $row['company_id'];

        $monthly_totals[$month] = ($monthly_totals[$month] ?? 0) + $row['amount'];
        $entity_totals[$entity] = ($entity_totals[$entity] ?? 0) + $row['amount'];
        $entity_monthly_totals[$entity][$month] = ($entity_monthly_totals[$entity][$month] ?? 0) + $row['amount'];
    }
} catch (Exception $e) {
    $alert = "<div class='alert alert-danger'>Error calculating summaries: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Fetch categories
$categories = [];
try {
    $cat_query = $con->query("SELECT id, name FROM expense_categories ORDER BY name");
    while ($cat = $cat_query->fetch_assoc()) {
        $categories[] = $cat;
    }
} catch (Exception $e) {
    $alert = "<div class='alert alert-danger'>Error fetching categories: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Fetch companiesexpense
$companiesexpense = [];
try {
    $com_query = $con->query("SELECT id, name FROM companiesexpense ORDER BY name");
    while ($com = $com_query->fetch_assoc()) {
        $companiesexpense[] = $com;
    }
} catch (Exception $e) {
    $alert = "<div class='alert alert-danger'>Error fetching companiesexpense: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Get user details
try {
    $emp_id = $_SESSION['id'];
    $conn = connect();
    $stmt = $conn->prepare("SELECT * FROM hrm_employee WHERE id = ?");
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();

    $user_detail_array = get_user_detail($emp_id);
    $user_roll_array = get_user_roll($emp_id);
    $designation = "";
    $department = "";
    $profile_image = "";
    $active_employee = count_where("hrm_employee", "status", "1");
    $inactive_employee = count_where("hrm_employee", "status", "0");

    $profile_image_dir = "upload-image";
    $profile_image = $profile_image_dir . "/" . $row['image'];
} catch (Exception $e) {
    $alert = "<div class='alert alert-danger'>Error fetching user details: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Generate or fetch invoice number
$invoice_number = 0;
try {
    $invoice_query = $con->query("SELECT invoice_number FROM invoice_numbers ORDER BY id DESC LIMIT 1");
    if ($invoice_query->num_rows > 0) {
        $last_invoice = $invoice_query->fetch_assoc();
        $invoice_number = $last_invoice['invoice_number'] + 1;
    } else {
        $invoice_number = 1000;
    }
} catch (Exception $e) {
    $alert = "<div class='alert alert-danger'>Error fetching invoice number: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Insert new invoice number into the table when PDF is generated
if (isset($_GET['download']) && $_GET['download'] === 'pdf') {
    try {
        $stmt = $con->prepare("INSERT INTO invoice_numbers (invoice_number) VALUES (?)");
        $stmt->bind_param("i", $invoice_number);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        $alert = "<div class='alert alert-danger'>Error inserting invoice number: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Get entity name for filter if applicable
$entity_name = '';
if ($employee_filter) {
    try {
        $stmt = $con->prepare("SELECT CONCAT(fname, ' ', lname) AS name FROM hrm_employee WHERE id = ?");
        $stmt->bind_param("i", $employee_filter);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $entity_name = $result->fetch_assoc()['name'];
        }
        $stmt->close();
    } catch (Exception $e) {
        $alert = "<div class='alert alert-danger'>Error fetching employee name: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} elseif ($company_filter) {
    try {
        $stmt = $con->prepare("SELECT name FROM companiesexpense WHERE id = ?");
        $stmt->bind_param("i", $company_filter);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $entity_name = $result->fetch_assoc()['name'];
        }
        $stmt->close();
    } catch (Exception $e) {
        $alert = "<div class='alert alert-danger'>Error fetching company name: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Define payment methods
$payment_methods = [
    'CREDIT CARD',
    'DEBIT CARD',
    'NET BANKING',
    'UPI',
    'CHEQUE',
    'DD',
    'OFFLINE'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Manager - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .card-shadow { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        @media (max-width: 576px) {
            .table-responsive { font-size: 0.85rem; }
            .table th, .table td { padding: 0.5rem; }
            .btn-sm { font-size: 0.75rem; }
            .filter-group { flex-direction: column; }
            .filter-group .form-select, .filter-group .input-group { width: 100% !important; }
        }
        .filter-group { gap: 1rem; }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-light">
<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Management Expense</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="container py-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                    <h4 class="mb-3 mb-md-0">Expense Dashboard - Admin</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#expenseModal">+ Add Expense</button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                            <i class="bi bi-gear"></i> Manage Categories
                        </button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manageCompaniesexpenseModal">
                            <i class="bi bi-gear"></i> Manage Companies
                        </button>
                        <button class="btn btn-success btn-sm" id="downloadPDFButton">
                            <i class="bi bi-file-pdf"></i> Download PDF
                        </button>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="bi bi-share"></i> Share
                        </button>
                    </div>
                </div>

                <?= $alert ?>

                <!-- Expense Modal -->
                <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="expenseModalLabel"><?= $edit_id ? 'Edit Expense' : 'Add Expense' ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data" id="expenseForm">
                                    <input type="hidden" name="expense_form" value="1">
                                    <input type="hidden" name="expense_id" value="<?= $edit_id ?>">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Employee</label>
                                            <select name="employee_id" class="form-select" id="employeeSelect">
                                                <option value="">-- Select Employee --</option>
                                                <?php
                                                $emp_query = $con->query("SELECT id, CONCAT(fname, ' ', lname) AS name FROM hrm_employee ORDER BY name");
                                                while ($emp = $emp_query->fetch_assoc()) {
                                                    $selected = ($edit_data['employee_id'] ?? '') == $emp['id'] ? 'selected' : '';
                                                    echo "<option value='{$emp['id']}' $selected>" . htmlspecialchars($emp['name']) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Company</label>
                                            <select name="company_id" class="form-select" id="companySelect">
                                                <option value="">-- Select Company --</option>
                                                <?php foreach ($companiesexpense as $com): ?>
                                                    <option value="<?= $com['id'] ?>" <?= ($edit_data['company_id'] ?? '') == $com['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($com['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Date of Expense</label>
                                            <input type="date" name="expense_date" class="form-control" value="<?= $edit_data['expense_date'] ?? '' ?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Category</label>
                                            <select name="category_id" class="form-select" required>
                                                <option value="">-- Select Category --</option>
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?= $cat['id'] ?>" <?= ($edit_data['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cat['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Amount (₹)</label>
                                            <input type="number" step="0.01" name="amount" class="form-control" value="<?= $edit_data['amount'] ?? '' ?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Payment Method</label>
                                            <select name="payment_method" class="form-select">
                                                <option value="">-- Select Payment Method --</option>
                                                <?php foreach ($payment_methods as $method): ?>
                                                    <option value="<?= $method ?>" <?= ($edit_data['payment_method'] ?? '') == $method ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($method) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Reference ID</label>
                                            <input type="text" name="reference_id" class="form-control" value="<?= htmlspecialchars($edit_data['reference_id'] ?? '') ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Upload Receipt (optional)</label>
                                            <input type="file" name="receipt" class="form-control" accept="image/*,application/pdf">
                                            <?php if (!empty($edit_data['receipt_path'])): ?>
                                                <small>Current: <a href="<?= htmlspecialchars($edit_data['receipt_path']) ?>" target="_blank">View</a></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success"><?= $edit_id ? 'Update' : 'Submit' ?> Expense</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manage Categories Modal -->
                <div class="modal fade" id="manageCategoriesModal" tabindex="-1" aria-labelledby="manageCategoriesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="manageCategoriesModalLabel">Manage Categories</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h6>Add New Category</h6>
                                <form id="addCategoryForm" class="mb-4">
                                    <div class="input-group">
                                        <input type="text" name="category_name" class="form-control" placeholder="Enter category name" required>
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn btn-success">Add</button>
                                    </div>
                                </form>

                                <form id="editCategoryForm" class="mb-4" style="display: none;">
                                    <h6>Edit Category</h6>
                                    <div class="input-group">
                                        <input type="text" name="category_name" class="form-control" required>
                                        <input type="hidden" name="category_id">
                                        <input type="hidden" name="action" value="edit">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <button type="button" class="btn btn-secondary" onclick="cancelCategoryEdit()">Cancel</button>
                                    </div>
                                </form>

                                <h6>Existing Categories</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categoryList">
                                            <?php foreach ($categories as $cat): ?>
                                                <tr data-id="<?= $cat['id'] ?>">
                                                    <td><?= htmlspecialchars($cat['name']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary edit-btn" data-id="<?= $cat['id'] ?>" data-name="<?= htmlspecialchars($cat['name']) ?>">Edit</button>
                                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="<?= $cat['id'] ?>">Delete</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manage Companiesexpense Modal -->
                <div class="modal fade" id="manageCompaniesexpenseModal" tabindex="-1" aria-labelledby="manageCompaniesexpenseModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="manageCompaniesexpenseModalLabel">Manage Companies</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h6>Add New Company</h6>
                                <form id="addCompanyForm" class="mb-4">
                                    <div class="input-group">
                                        <input type="text" name="category_name" class="form-control" placeholder="Enter company name" required>
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn btn-success">Add</button>
                                    </div>
                                </form>

                                <form id="editCompanyForm" class="mb-4" style="display: none;">
                                    <h6>Edit Company</h6>
                                    <div class="input-group">
                                        <input type="text" name="category_name" class="form-control" required>
                                        <input type="hidden" name="category_id">
                                        <input type="hidden" name="action" value="edit">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <button type="button" class="btn btn-secondary" onclick="cancelCompanyEdit()">Cancel</button>
                                    </div>
                                </form>

                                <h6>Existing Companies</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="companyList">
                                            <?php foreach ($companiesexpense as $com): ?>
                                                <tr data-id="<?= $com['id'] ?>">
                                                    <td><?= htmlspecialchars($com['name']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary edit-btn" data-id="<?= $com['id'] ?>" data-name="<?= htmlspecialchars($com['name']) ?>">Edit</button>
                                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="<?= $com['id'] ?>">Delete</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Share Modal -->
                <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="shareModalLabel">Share Expenditure History</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Share the expenditure history via email:</p>
                                <form id="shareForm">
                                    <div class="mb-3">
                                        <label class="form-label">Recipient Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Message (optional)</label>
                                        <textarea name="message" class="form-control" rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send Email</button>
                                </form>
                                <hr>
                                <p>Or copy the shareable link:</p>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="shareLink" value="<?= htmlspecialchars("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" readonly>
                                    <button class="btn btn-outline-secondary" onclick="copyLink()">Copy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteExpenseModal" tabindex="-1" aria-labelledby="deleteExpenseModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteExpenseModalLabel">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this expense? This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form method="POST" id="deleteExpenseForm">
                                    <input type="hidden" name="expense_id" id="deleteExpenseId">
                                    <input type="hidden" name="action" value="Delete">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toast Container -->
                <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastBox"></div>

                <!-- Filters -->
                <div class="mb-3 filter-group d-flex flex-wrap">
                    <form method="get" class="d-inline-block flex-fill me-2 mb-2">
                        <select name="status" onchange="this.form.submit()" class="form-select w-100">
                            <option value="">All Status</option>
                            <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= $status_filter === 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Rejected" <?= $status_filter === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                        <input type="hidden" name="employee" value="<?= $employee_filter ?>">
                        <input type="hidden" name="company" value="<?= $company_filter ?>">
                        <input type="hidden" name="month" value="<?= htmlspecialchars($month_filter) ?>">
                        <input type="hidden" name="week" value="<?= htmlspecialchars($week_filter) ?>">
                        <input type="hidden" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
                        <input type="hidden" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
                    </form>
                    <form method="get" class="d-inline-block flex-fill me-2 mb-2">
                        <select name="employee" onchange="this.form.submit()" class="form-select w-100">
                            <option value="">All Employees</option>
                            <?php
                            $emp_query = $con->query("SELECT id, CONCAT(fname, ' ', lname) AS name FROM hrm_employee ORDER BY name");
                            while ($emp = $emp_query->fetch_assoc()) {
                                $selected = $employee_filter == $emp['id'] ? 'selected' : '';
                                echo "<option value='{$emp['id']}' $selected>" . htmlspecialchars($emp['name']) . "</option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        <input type="hidden" name="company" value="<?= $company_filter ?>">
                        <input type="hidden" name="month" value="<?= htmlspecialchars($month_filter) ?>">
                        <input type="hidden" name="week" value="<?= htmlspecialchars($week_filter) ?>">
                        <input type="hidden" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
                        <input type="hidden" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
                    </form>
                    <form method="get" class="d-inline-block flex-fill me-2 mb-2">
                        <select name="company" onchange="this.form.submit()" class="form-select w-100">
                            <option value="">All Companies</option>
                            <?php foreach ($companiesexpense as $com): ?>
                                <option value="<?= $com['id'] ?>" <?= $company_filter == $com['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($com['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        <input type="hidden" name="employee" value="<?= $employee_filter ?>">
                        <input type="hidden" name="month" value="<?= htmlspecialchars($month_filter) ?>">
                        <input type="hidden" name="week" value="<?= htmlspecialchars($week_filter) ?>">
                        <input type="hidden" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
                        <input type="hidden" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
                    </form>
                    <form method="get" class="d-inline-block flex-fill me-2 mb-2">
                        <select name="month" onchange="this.form.submit()" class="form-select w-100">
                            <option value="">All Months</option>
                            <?php foreach ($months as $month): ?>
                                <option value="<?= $month ?>" <?= $month_filter === $month ? 'selected' : '' ?>>
                                    <?= date('F Y', strtotime($month . '-01')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        <input type="hidden" name="employee" value="<?= $employee_filter ?>">
                        <input type="hidden" name="company" value="<?= $company_filter ?>">
                        <input type="hidden" name="week" value="<?= htmlspecialchars($week_filter) ?>">
                        <input type="hidden" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
                        <input type="hidden" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
                    </form>
                    <form method="get" class="d-inline-block flex-fill me-2 mb-2">
                        <select name="week" onchange="this.form.submit()" class="form-select w-100" <?= !$month_filter ? 'disabled' : '' ?>>
                            <option value="">All Weeks</option>
                            <option value="1" <?= $week_filter === '1' ? 'selected' : '' ?>>First Week</option>
                            <option value="2" <?= $week_filter === '2' ? 'selected' : '' ?>>Second Week</option>
                            <option value="3" <?= $week_filter === '3' ? 'selected' : '' ?>>Third Week</option>
                            <option value="4" <?= $week_filter === '4' ? 'selected' : '' ?>>Fourth Week</option>
                            <option value="5" <?= $week_filter === '5' ? 'selected' : '' ?>>Fifth Week</option>
                        </select>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        <input type="hidden" name="employee" value="<?= $employee_filter ?>">
                        <input type="hidden" name="company" value="<?= $company_filter ?>">
                        <input type="hidden" name="month" value="<?= htmlspecialchars($month_filter) ?>">
                        <input type="hidden" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
                        <input type="hidden" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
                    </form>
                    <form method="get" class="d-inline-block flex-fill mb-2">
                        <div class="input-group w-100">
                            <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($from_date) ?>" placeholder="From Date">
                            <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($to_date) ?>" placeholder="To Date">
                            <button type="submit" class="btn btn-outline-primary">Filter</button>
                        </div>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        <input type="hidden" name="employee" value="<?= $employee_filter ?>">
                        <input type="hidden" name="company" value="<?= $company_filter ?>">
                        <input type="hidden" name="month" value="<?= htmlspecialchars($month_filter) ?>">
                        <input type="hidden" name="week" value="<?= htmlspecialchars($week_filter) ?>">
                    </form>
                </div>

                <!-- Summaries -->
                <div class="row mb-4">
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <div class="card card-shadow p-3">
                            <h6>Total Expenditure</h6>
                            <h3 class="text-primary">₹ <?= number_format($total, 2) ?></h3>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <div class="card card-shadow p-3">
                            <h6>Monthly Totals</h6>
                            <ul class="list-group list-group-flush">
                                <?php
                                krsort($monthly_totals);
                                foreach ($monthly_totals as $month => $amount):
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= date('F Y', strtotime($month . '-01')) ?>
                                        <span class="badge bg-info">₹ <?= number_format($amount, 2) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card card-shadow p-3">
                            <h6>Entity Totals</h6>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($entity_totals as $entity => $amount): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($entity) ?>
                                        <span class="badge bg-info">₹ <?= number_format($amount, 2) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Entity Monthly Totals -->
                <?php if (!empty($entity_monthly_totals)): ?>
                    <div class="card card-shadow p-3 mb-4">
                        <h6>Entity Monthly Breakdown</h6>
                        <div class="accordion" id="entityMonthlyAccordion">
                            <?php foreach ($entity_monthly_totals as $entity => $months): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= md5($entity) ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= md5($entity) ?>" aria-expanded="false" aria-controls="collapse<?= md5($entity) ?>">
                                            <?= htmlspecialchars($entity) ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= md5($entity) ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= md5($entity) ?>" data-bs-parent="#entityMonthlyAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-group list-group-flush">
                                                <?php
                                                krsort($months);
                                                foreach ($months as $month => $amount):
                                                ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <?= date('F Y', strtotime($month . '-01')) ?>
                                                        <span class="badge bg-info">₹ <?= number_format($amount, 2) ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Expenses Table -->
                <div class="card card-shadow p-3">
                    <h5 class="mb-3">Expense History</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle" id="expenseTable">
                            <thead>
                                <tr>
                                    <th>Entity</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Payment Method</th>
                                    <th>Reference ID</th>
                                    <th>Receipt</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            try {
                                $sql = "SELECT ee.*, 
                                        COALESCE(CONCAT(e.fname, ' ', e.lname), c.name) AS entity_name,
                                        cat.name AS category_name
                                        FROM employee_expenses ee
                                        LEFT JOIN hrm_employee e ON ee.employee_id = e.id
                                        LEFT JOIN companiesexpense c ON ee.company_id = c.id
                                        LEFT JOIN expense_categories cat ON ee.category_id = cat.id
                                        WHERE 1=1 $status_query $employee_query $company_query $month_query $week_query $date_query
                                        ORDER BY ee.expense_date DESC";
                                $result = $con->query($sql);

                                if ($result->num_rows === 0) {
                                    echo "<tr><td colspan='10' class='text-center'>No expenses found.</td></tr>";
                                }

                                while ($row = $result->fetch_assoc()) {
                                    $isAdvanceDisbursement = isset($row['category_name']) && $row['category_name'] === 'ADVANCE DISBURSEMENT AMOUNT';
                                    $rowClass = $isAdvanceDisbursement ? 'table-danger' : '';
                                    echo "<tr class='{$rowClass}'>
                                        <td>" . htmlspecialchars($row['entity_name']) . "</td>
                                        <td>" . htmlspecialchars($row['expense_date']) . "</td>
                                        <td>" . htmlspecialchars($row['category_name'] ?? '-') . "</td>
                                        <td>₹ " . number_format($row['amount'], 2) . "</td>
                                        <td>" . htmlspecialchars($row['description']) . "</td>
                                        <td>" . htmlspecialchars($row['payment_method'] ?? '-') . "</td>
                                        <td>" . htmlspecialchars($row['reference_id'] ?? '-') . "</td>
                                        <td>" . ($row['receipt_path'] ? "<a href='" . htmlspecialchars($row['receipt_path']) . "' target='_blank'>View</a>" : "-") . "</td>
                                        <td><span class='badge bg-" . ($row['status'] === 'Approved' ? 'success' : ($row['status'] === 'Rejected' ? 'danger' : 'warning')) . "'>" . htmlspecialchars($row['status']) . "</span></td>
                                        <td>";
                                    if ($row['status'] === 'Pending') {
                                        echo "<form method='POST' class='d-inline'>
                                                <input type='hidden' name='expense_id' value='{$row['id']}'>
                                                <button type='submit' name='action' value='Approved' class='btn btn-success btn-sm'>Approve</button>
                                                <button type='submit' name='action' value='Rejected' class='btn btn-danger btn-sm'>Reject</button>
                                              </form>
                                              <button class='btn btn-sm btn-outline-danger ms-1 delete-expense-btn' data-id='{$row['id']}' data-bs-toggle='modal' data-bs-target='#deleteExpenseModal'>🗑️</button>
                                              <button class='btn btn-sm btn-outline-secondary ms-1 edit-expense-btn' data-id='{$row['id']}'>✏️</button>";
                                    } else {
                                        echo "<button class='btn btn-sm btn-outline-danger delete-expense-btn' data-id='{$row['id']}' data-bs-toggle='modal' data-bs-target='#deleteExpenseModal'>🗑️</button>
                                              <button class='btn btn-sm btn-outline-secondary ms-1 edit-expense-btn' data-id='{$row['id']}'>✏️</button>";
                                    }
                                    echo "</td></tr>";
                                }
                            } catch (Exception $e) {
                                echo "<tr><td colspan='10' class='text-center text-danger'>Error loading expenses: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
// Category Management
document.getElementById("addCategoryForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("manage_categories.php", {
        method: "POST",
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
        return res.json();
    })
    .then(data => {
        showToast(data.message, data.status === 'success' ? 'bg-success' : (data.status === 'exists' ? 'bg-warning' : 'bg-danger'));
        if (data.status === 'success') {
            this.reset();
            updateCategoryList();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to add category. Please try again.', 'bg-danger');
    });
});

document.getElementById("editCategoryForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("manage_categories.php", {
        method: "POST",
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
        return res.json();
    })
    .then(data => {
        showToast(data.message, data.status === 'success' ? 'bg-success' : (data.status === 'exists' ? 'bg-warning' : 'bg-danger'));
        if (data.status === 'success') {
            document.getElementById("editCategoryForm").style.display = 'none';
            document.getElementById("addCategoryForm").style.display = 'block';
            this.reset();
            updateCategoryList();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update category. Please try again.', 'bg-danger');
    });
});

document.getElementById("categoryList").addEventListener("click", function(e) {
    if (e.target.classList.contains("edit-btn")) {
        const id = e.target.dataset.id;
        const name = e.target.dataset.name;
        document.getElementById("addCategoryForm").style.display = 'none';
        const editForm = document.getElementById("editCategoryForm");
        editForm.style.display = 'block';
        editForm.querySelector('input[name="category_name"]').value = name;
        editForm.querySelector('input[name="category_id"]').value = id;
    } else if (e.target.classList.contains("delete-btn")) {
        if (!confirm("Are you sure you want to delete this category?")) return;
        const id = e.target.dataset.id;
        const formData = new FormData();
        formData.append("action", "delete");
        formData.append("category_id", id);

        fetch("manage_categories.php", {
            method: "POST",
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
            return res.json();
        })
        .then(data => {
            showToast(data.message, data.status === 'success' ? 'bg-success' : 'bg-danger');
            if (data.status === 'success') {
                updateCategoryList();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to delete category. Please try again.', 'bg-danger');
        });
    }
});

function cancelCategoryEdit() {
    document.getElementById("editCategoryForm").style.display = 'none';
    document.getElementById("addCategoryForm").style.display = 'block';
    document.getElementById("editCategoryForm").reset();
}

function updateCategoryList() {
    fetch("manage_categories.php?action=list")
        .then(res => res.json())
        .then(data => {
            const categoryList = document.getElementById("categoryList");
            categoryList.innerHTML = '';
            data.categories.forEach(cat => {
                categoryList.innerHTML += `
                    <tr data-id="${cat.id}">
                        <td>${cat.name}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${cat.id}" data-name="${cat.name}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${cat.id}">Delete</button>
                        </td>
                    </tr>`;
            });
            const categorySelect = document.querySelector('select[name="category_id"]');
            categorySelect.innerHTML = '<option value="">-- Select Category --</option>';
            data.categories.forEach(cat => {
                categorySelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
            });
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to load categories.', 'bg-danger');
        });
}

// Company Management
document.getElementById("addCompanyForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("manage_companies.php", {
        method: "POST",
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
        return res.json();
    })
    .then(data => {
        showToast(data.message, data.status === 'success' ? 'bg-success' : (data.status === 'exists' ? 'bg-warning' : 'bg-danger'));
        if (data.status === 'success') {
            this.reset();
            updateCompanyList();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to add company. Please try again.', 'bg-danger');
    });
});

document.getElementById("editCompanyForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("manage_companies.php", {
        method: "POST",
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
        return res.json();
    })
    .then(data => {
        showToast(data.message, data.status === 'success' ? 'bg-success' : (data.status === 'exists' ? 'bg-warning' : 'bg-danger'));
        if (data.status === 'success') {
            document.getElementById("editCompanyForm").style.display = 'none';
            document.getElementById("addCompanyForm").style.display = 'block';
            this.reset();
            updateCompanyList();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update company. Please try again.', 'bg-danger');
    });
});

document.getElementById("companyList").addEventListener("click", function(e) {
    if (e.target.classList.contains("edit-btn")) {
        const id = e.target.dataset.id;
        const name = e.target.dataset.name;
        document.getElementById("addCompanyForm").style.display = 'none';
        const editForm = document.getElementById("editCompanyForm");
        editForm.style.display = 'block';
        editForm.querySelector('input[name="category_name"]').value = name;
        editForm.querySelector('input[name="category_id"]').value = id;
    } else if (e.target.classList.contains("delete-btn")) {
        if (!confirm("Are you sure you want to delete this company?")) return;
        const id = e.target.dataset.id;
        const formData = new FormData();
        formData.append("action", "delete");
        formData.append("category_id", id);

        fetch("manage_companies.php", {
            method: "POST",
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
            return res.json();
        })
        .then(data => {
            showToast(data.message, data.status === 'success' ? 'bg-success' : 'bg-danger');
            if (data.status === 'success') {
                updateCompanyList();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to delete company. Please try again.', 'bg-danger');
        });
    }
});

function cancelCompanyEdit() {
    document.getElementById("editCompanyForm").style.display = 'none';
    document.getElementById("addCompanyForm").style.display = 'block';
    document.getElementById("editCompanyForm").reset();
}

function updateCompanyList() {
    fetch("manage_companies.php?action=list")
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
            return res.json();
        })
        .then(data => {
            console.log('Fetched companies:', data);
            const companyList = document.getElementById("companyList");
            companyList.innerHTML = '';
            data.companies.forEach(com => {
                companyList.innerHTML += `
                    <tr data-id="${com.id}">
                        <td>${com.name}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${com.id}" data-name="${com.name}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${com.id}">Delete</button>
                        </td>
                    </tr>`;
            });
            const companySelect = document.querySelector('select[name="company_id"]');
            const currentValue = companySelect.value;
            companySelect.innerHTML = '<option value="">-- Select Company --</option>';
            data.companies.forEach(com => {
                companySelect.innerHTML += `<option value="${com.id}">${com.name}</option>`;
            });
            companySelect.value = currentValue; // Restore previous selection if valid
            companySelect.disabled = document.getElementById('employeeSelect').value !== '';
        })
        .catch(error => {
            console.error('Error fetching companies:', error);
            showToast('Failed to load companies.', 'bg-danger');
        });
}

// Share Form
document.getElementById("shareForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const email = this.querySelector('input[name="email"]').value;
    const message = this.querySelector('textarea[name="message"]').value;
    const shareLink = document.getElementById('shareLink').value;

    let summary = `Expense Summary:\n`;
    summary += `Total Expenditure: ₹ <?= number_format($total, 2) ?>\n`;
    summary += `Monthly Totals:\n`;
    <?php foreach ($monthly_totals as $month => $amount): ?>
        summary += `- <?= date('F Y', strtotime($month . '-01')) ?>: ₹ <?= number_format($amount, 2) ?>\n`;
    <?php endforeach; ?>
    summary += `\nView the expenditure history: ${shareLink}`;

    const subject = encodeURIComponent('Expenditure History Report');
    const body = encodeURIComponent(`${message}\n\n${summary}`);
    window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
    bootstrap.Modal.getInstance(document.getElementById('shareModal')).hide();
});

function showToast(message, toastClass = 'bg-success') {
    const toastEl = document.createElement("div");
    toastEl.className = `toast align-items-center text-white ${toastClass}`;
    toastEl.role = "alert";
    toastEl.setAttribute("aria-live", "assertive");
    toastEl.setAttribute("aria-atomic", "true");
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.getElementById("toastBox").appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 3000 });
    toast.show();
}

function copyLink() {
    const shareLink = document.getElementById('shareLink');
    shareLink.select();
    document.execCommand('copy');
    showToast('Link copied to clipboard!', 'bg-success');
}

function numberToWords(num) {
    const units = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
    const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    const thousands = ['', 'Thousand', 'Million', 'Billion'];

    if (num === 0) return units[0];

    function convertLessThanThousand(n) {
        if (n === 0) return '';
        if (n < 10) return units[n];
        if (n < 20) return teens[n - 10];
        if (n < 100) {
            const ten = Math.floor(n / 10);
            const unit = n % 10;
            return tens[ten] + (unit ? ' ' + units[unit] : '');
        }
        const hundred = Math.floor(n / 100);
        const remainder = n % 100;
        return units[hundred] + ' Hundred' + (remainder ? ' ' + convertLessThanThousand(remainder) : '');
    }

    let result = '';
    let thousandIndex = 0;

    while (num > 0) {
        const chunk = num % 1000;
        if (chunk) {
            result = convertLessThanThousand(chunk) + (thousands[thousandIndex] ? ' ' + thousands[thousandIndex] : '') + (result ? ' ' + result : '');
        }
        num = Math.floor(num / 1000);
        thousandIndex++;
    }

    return result.trim();
}

function decimalToWords(decimal) {
    const whole = Math.floor(decimal);
    const fraction = Math.round((decimal - whole) * 100);
    let result = numberToWords(whole) + ' Rupees';
    if (fraction > 0) {
        result += ' and ' + numberToWords(fraction) + ' Paise';
    }
    return result;
}

function generatePDF() {
    try {
        if (typeof html2pdf === 'undefined') {
            console.error('html2pdf.js is not loaded.');
            showToast('Failed to generate PDF: html2pdf.js is not loaded.', 'bg-danger');
            document.getElementById('loadingOverlay').style.display = 'none';
            return;
        }

        console.log('Starting PDF generation');

        const total = '<?= number_format($total, 2) ?>';
        const currentDate = new Date().toLocaleDateString('en-GB').split('/').join('-');
        const fromDate = '<?= htmlspecialchars($from_date) ?>';
        const toDate = '<?= htmlspecialchars($to_date) ?>';
        const monthFilter = '<?= htmlspecialchars($month_filter) ?>';
        const weekFilter = '<?= htmlspecialchars($week_filter) ?>';
        const invoiceNumber = '<?= $invoice_number ?>';
        const entityName = '<?= htmlspecialchars($entity_name) ?>';

        let dateRange = 'All Dates';
        let filterText = '';
        if (fromDate && toDate) {
            dateRange = `${fromDate} to ${toDate}`;
            filterText = `Date Range: ${dateRange}`;
        } else if (monthFilter && weekFilter) {
            const weekNames = { '1': 'First Week', '2': 'Second Week', '3': 'Third Week', '4': 'Fourth Week', '5': 'Fifth Week' };
            dateRange = `${weekNames[weekFilter]} of ${new Date(monthFilter + '-01').toLocaleString('default', { month: 'long', year: 'numeric' })}`;
            filterText = `${weekNames[weekFilter]} of ${new Date(monthFilter + '-01').toLocaleString('default', { month: 'long', year: 'numeric' })}`;
        } else if (monthFilter) {
            dateRange = new Date(monthFilter + '-01').toLocaleString('default', { month: 'long', year: 'numeric' });
            filterText = `Month: ${dateRange}`;
        }

        if (entityName) {
            filterText = `${entityName} (${filterText})`;
        }

        const expenses = [];
<?php
try {
    $sql = "SELECT ee.*, 
            cat.name AS category_name
            FROM employee_expenses ee
            LEFT JOIN hrm_employee e ON ee.employee_id = e.id
            LEFT JOIN companiesexpense c ON ee.company_id = c.id
            LEFT JOIN expense_categories cat ON ee.category_id = cat.id
            WHERE 1=1 $status_query $employee_query $company_query $month_query $week_query $date_query
            ORDER BY ee.submitted_at DESC";
    $result = $con->query($sql);
    if (!$result) {
        echo "console.error('Database query failed: " . addslashes($con->error) . "');";
    } else {
        while ($row = $result->fetch_assoc()) {
            $formatted_date = date('d-m-Y', strtotime($row['expense_date']));
            $category = json_encode($row['category_name'] ?? '-');
            $expense_date = json_encode($formatted_date);
            $description = json_encode($row['description']);
            $amount = json_encode(number_format($row['amount'], 2));
            $payment_method = json_encode($row['payment_method'] ?? '-');
            $reference_id = json_encode($row['reference_id'] ?? '-');
            echo "expenses.push({
                category: $category,
                expense_date: $expense_date,
                description: $description,
                amount: $amount,
                payment_method: $payment_method,
                reference_id: $reference_id
            });";
        }
    }
} catch (Exception $e) {
    echo "console.error('Error generating expense data: " . addslashes($e->getMessage()) . "');";
}
?>

        console.log('Expenses loaded:', expenses);

        const container = document.createElement('div');
        container.style.fontFamily = 'Arial, sans-serif';
        container.style.width = '210mm';
        container.style.padding = '10mm';
        container.style.backgroundColor = '#E6F7FA';

        const header = document.createElement('div');
        header.style.display = 'flex';
        header.style.justifyContent = 'space-between';
        header.style.marginBottom = '20px';
        header.innerHTML = `
            <div>
                <h1 style="font-size: 24px; color: #006064; margin: 0;">EXPENSE</h1>
                <p style="font-size: 14px; color: #006064; margin: 0;">${filterText}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0;">RECEIPT NO: ${invoiceNumber}</p>
                <p style="margin: 0;">DATE: ${currentDate}</p>
            </div>
        `;
        container.appendChild(header);

        const companyDetails = document.createElement('div');
        companyDetails.style.textAlign = 'center';
        companyDetails.style.marginBottom = '20px';
        companyDetails.innerHTML = `
            <h2 style="font-size: 20px; color: #006064;">Expetize Private Limited</h2>
            <p style="margin: 0;">401, Vinayak Complex, Plot No 76, Vijay Block, Laxmi Nagar, Near Pillar</p>
            <p style="margin: 0;">No-51-52, Delhi, Delhi-110092</p>            <p style="margin: 0;">Contact: info@expetize.com | +91-1234567890</p>
        `;
        container.appendChild(companyDetails);

        const table = document.createElement('div');
        table.innerHTML = `
            <table style="width: 100%; border-collapse: collapse; background-color: white; table-layout: fixed; font-size: 10px;">
                <thead>
                    <tr style="background-color: #006064; color: white;">
                        <th style="padding: 6px; border: 1px solid #ddd; width: 12%; text-align: left; white-space: normal;">Date</th>
                        <th style="padding: 6px; border: 1px solid #ddd; width: 15%; text-align: left; white-space: normal;">Category</th>
                        <th style="padding: 6px; border: 1px solid #ddd; width: 28%; text-align: left; white-space: normal;">Description</th>
                        <th style="padding: 6px; border: 1px solid #ddd; width: 15%; text-align: left; white-space: normal;">Payment Method</th>
                        <th style="padding: 6px; border: 1px solid #ddd; width: 15%; text-align: left; white-space: normal;">Reference ID</th>
                        <th style="padding: 6px; border: 1px solid #ddd; width: 15%; text-align: right; white-space: normal;">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    ${expenses.map(exp => `
                        <tr>
                            <td style="padding: 6px; border: 1px solid #ddd; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">${exp.expense_date}</td>
                            <td style="padding: 6px; border: 1px solid #ddd; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">${exp.category}</td>
                            <td style="padding: 6px; border: 1px solid #ddd; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">${exp.description}</td>
                            <td style="padding: 6px; border: 1px solid #ddd; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">${exp.payment_method}</td>
                            <td style="padding: 6px; border: 1px solid #ddd; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">${exp.reference_id}</td>
                            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; white-space: normal;">${exp.amount}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        container.appendChild(table);

        const totals = document.createElement('div');
        totals.style.marginTop = '15px';
        totals.innerHTML = `
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background-color: #006064; color: white;">
                    <td style="padding: 6px; font-weight: bold; background-color: #006064; color: white;">Total</td>
                    <td style="padding: 6px; text-align: right; background-color: #006064; color: white;">₹ ${total}</td>
                </tr>
            </table>
        `;
        container.appendChild(totals);

        const totalInWords = document.createElement('div');
        totalInWords.style.marginBottom = '20px';
        const totalAmount = parseFloat(total.replace(/[^0-9.]/g, ''));
        const totalInWordsText = decimalToWords(totalAmount);
        totalInWords.innerHTML = `<p style="margin: 0;"><strong>TOTAL AMOUNT (In Words):</strong> ${totalInWordsText}</p>`;
        container.appendChild(totalInWords);

        const footer = document.createElement('div');
        footer.style.display = 'flex';
        footer.style.justifyContent = 'space-between';
        footer.innerHTML = `
            <p style="margin: 0;">For: Expetize Private Limited</p>
            <p style="margin: 0;">Authorised Signatory</p>
        `;
        container.appendChild(footer);

        const opt = {
            margin: 0,
            filename: 'expense_receipt_' + currentDate + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        console.log('Generating PDF with html2pdf');
        html2pdf().from(container).set(opt).save().then(() => {
            console.log('PDF generated successfully');
            showToast('PDF downloaded successfully!', 'bg-success');
            document.getElementById('loadingOverlay').style.display = 'none';
            const params = new URLSearchParams(window.location.search);
            params.delete('download');
            const cleanUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.replaceState({}, document.title, cleanUrl);
        }).catch(error => {
            console.error('PDF generation failed:', error);
            showToast('Failed to generate PDF: ' + error.message, 'bg-danger');
            document.getElementById('loadingOverlay').style.display = 'none';
        });
    } catch (error) {
        console.error('Error in generatePDF:', error);
        showToast('Failed to generate PDF: ' + error.message, 'bg-danger');
        document.getElementById('loadingOverlay').style.display = 'none';
    }
}

function downloadPDF() {
    document.getElementById('loadingOverlay').style.display = 'flex';
    if (window.location.search.includes('download=pdf')) {
        generatePDF();
    } else {
        const params = new URLSearchParams(window.location.search);
        params.set('download', 'pdf');
        params.set('status', '<?= urlencode($status_filter) ?>');
        params.set('employee', '<?= $employee_filter ?>');
        params.set('company', '<?= $company_filter ?>');
        params.set('month', '<?= urlencode($month_filter) ?>');
        params.set('week', '<?= urlencode($week_filter) ?>');
        params.set('from_date', '<?= urlencode($from_date) ?>');
        params.set('to_date', '<?= urlencode($to_date) ?>');
        
        const redirectUrl = window.location.pathname + '?' + params.toString();
        console.log('Redirecting to:', redirectUrl);
        window.location.href = redirectUrl;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    <?php if ($edit_id > 0): ?>
        var expenseModal = new bootstrap.Modal(document.getElementById('expenseModal'));
        expenseModal.show();
    <?php endif; ?>

    document.querySelectorAll('.edit-expense-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const expenseId = this.getAttribute('data-id');
            window.location.href = `?edit=${expenseId}`;
        });
    });

    document.getElementById('expenseModal').addEventListener('hidden.bs.modal', function () {
        if (!window.location.search.includes('edit')) {
            document.getElementById('expenseForm').reset();
            document.querySelector('#expenseModalLabel').textContent = 'Add Expense';
            document.querySelector('input[name="expense_id"]').value = '0';
            document.getElementById('employeeSelect').value = '';
            document.getElementById('companySelect').value = '';
        }
    });

    document.querySelectorAll('.delete-expense-btn').forEach(button => {
        button.addEventListener('click', function() {
            const expenseId = this.getAttribute('data-id');
            document.getElementById('deleteExpenseId').value = expenseId;
        });
    });

    const fromDateInput = document.querySelector('input[name="from_date"]');
    const toDateInput = document.querySelector('input[name="to_date"]');
    fromDateInput.addEventListener('change', function() {
        toDateInput.min = this.value;
    });
    toDateInput.addEventListener('change', function() {
        fromDateInput.max = this.value;
    });

    const employeeSelect = document.getElementById('employeeSelect');
    const companySelect = document.getElementById('companySelect');

    employeeSelect.addEventListener('change', function() {
        if (this.value !== '') {
            companySelect.value = '';
            companySelect.disabled = true;
        } else {
            companySelect.disabled = false;
        }
    });

    companySelect.addEventListener('change', function() {
        if (this.value !== '') {
            employeeSelect.value = '';
            employeeSelect.disabled = true;
        } else {
            employeeSelect.disabled = false;
        }
    });

    const downloadPDFButton = document.getElementById('downloadPDFButton');
    if (downloadPDFButton) {
        downloadPDFButton.addEventListener('click', downloadPDF);
    } else {
        console.error('Download PDF button not found!');
    }

    if (window.location.search.includes('download=pdf')) {
        downloadPDF();
    }
});
</script>
</body>
</html>