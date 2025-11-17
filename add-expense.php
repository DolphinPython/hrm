<?php
// Verify external files
$required_files = ['layouts/session.php', 'layouts/head-main.php', 'layouts/config.php', 'include/function.php'];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Error: Required file '$file' not found.");
    }
    include $file;
}

if (!isset($_SESSION['id'])) {
    die("Please log in.");
}

$employee_id = $_SESSION['id'];
$alert = "";
$edit_id = 0;
$edit_data = [];

// Debug session and connection
error_log("Session employee_id: $employee_id");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $con->prepare("DELETE FROM employee_expenses WHERE id = ? AND employee_id = ? AND status = 'Pending'");
    $stmt->bind_param("ii", $delete_id, $employee_id);
    $stmt->execute();
    $alert = "<div class='alert alert-danger'>Expense deleted successfully!</div>";
}

// Handle Add/Update Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expense_id = isset($_POST['expense_id']) ? intval($_POST['expense_id']) : 0;
    $expense_date = $_POST['expense_date'] ?? '';
    $category_id = $_POST['category_id'] ?? 0;
    $amount = $_POST['amount'] ?? 0;
    $description = $_POST['description'] ?? '';

    error_log("POST data: expense_id=$expense_id, date=$expense_date, category=$category_id, amount=$amount, description=$description");

    $receipt_path = '';
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] == 0) {
        $targetDir = "Uploads/receipts/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $filename = basename($_FILES['receipt']['name']);
        $targetFile = $targetDir . time() . "_" . $filename;
        if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetFile)) {
            $receipt_path = $targetFile;
        }
    }

    if ($expense_id > 0) {
        $stmt = $con->prepare("SELECT id FROM employee_expenses WHERE id = ? AND employee_id = ?");
        $stmt->bind_param("ii", $expense_id, $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $alert = "<div class='alert alert-danger'>Invalid expense ID for update.</div>";
            error_log("Invalid expense_id=$expense_id for update");
        } else {
            $sql = "UPDATE employee_expenses SET expense_date = ?, category_id = ?, amount = ?, description = ?";
            if ($receipt_path) $sql .= ", receipt_path = ?";
            $sql .= " WHERE id = ? AND employee_id = ?";
            $stmt = $con->prepare($sql);
            if ($receipt_path) {
                $stmt->bind_param("sidssii", $expense_date, $category_id, $amount, $description, $receipt_path, $expense_id, $employee_id);
            } else {
                $stmt->bind_param("sidssi", $expense_date, $category_id, $amount, $description, $expense_id, $employee_id);
            }
            if ($stmt->execute()) {
                $alert = "<div class='alert alert-info'>Expense updated successfully!</div>";
            } else {
                $alert = "<div class='alert alert-danger'>Error updating expense: " . $stmt->error . "</div>";
                error_log("Update error: " . $stmt->error);
            }
        }
    } else {
        $stmt = $con->prepare("INSERT INTO employee_expenses (employee_id, expense_date, category_id, amount, description, receipt_path, status, submitted_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())");
        $stmt->bind_param("isidss", $employee_id, $expense_date, $category_id, $amount, $description, $receipt_path);
        if ($stmt->execute()) {
            $alert = "<div class='alert alert-success'>Expense added successfully!</div>";
        } else {
            $alert = "<div class='alert alert-danger'>Error adding expense: " . $stmt->error . "</div>";
            error_log("Insert error: " . $stmt->error);
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Edit
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    error_log("Edit request: edit_id=$edit_id, employee_id=$employee_id, GET=" . print_r($_GET, true));
    $raw_query = "SELECT id, employee_id, expense_date, category_id, amount, description, receipt_path, status FROM employee_expenses WHERE id = $edit_id AND employee_id = $employee_id";
    error_log("Raw query: $raw_query");
    $stmt = $con->prepare("SELECT id, employee_id, expense_date, category_id, amount, description, receipt_path, status FROM employee_expenses WHERE id = ? AND employee_id = ?");
    $stmt->bind_param("ii", $edit_id, $employee_id);
    if (!$stmt->execute()) {
        error_log("Edit query error: " . $stmt->error);
        $alert = "<div class='alert alert-danger'>Error fetching expense data: " . $stmt->error . "</div>";
    } else {
        $result = $stmt->get_result();
        error_log("Edit query rows: " . $result->num_rows);
        if ($result->num_rows == 1) {
            $edit_data = $result->fetch_assoc();
            error_log("Edit data fetched: " . print_r($edit_data, true));
            $alert = "<div class='alert alert-info'>Expense data loaded successfully for editing.</div>";
        } else {
            error_log("No expense found for edit_id=$edit_id, employee_id=$employee_id");
            $alert = "<div class='alert alert-danger'>Failed to load expense data for editing.</div>";
        }
    }
}

$status_filter = $_GET['status'] ?? '';
$month_filter = $_GET['month'] ?? '';
$status_query = $status_filter ? " AND ee.status = ?" : "";
$month_query = $month_filter ? " AND DATE_FORMAT(ee.expense_date, '%Y-%m') = ?" : "";

// Get user name and other details
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css" onerror="console.error('Failed to load local Bootstrap CSS')">
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .card-shadow { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .pdf-total { margin-top: 10px; font-weight: bold; }
        .modal-content, .form-control, .modal-body { display: block !important; visibility: visible !important; opacity: 1 !important; }
        .form-control { width: 100%; padding: 0.375rem 0.75rem; font-size: 1rem; border: 1px solid #ced4da; }
        .modal-body { padding: 1rem; }
        .table-responsive { overflow-x: auto; }
        .table th, .table td { font-size: 0.9rem; white-space: nowrap; }
        @media (max-width: 576px) {
            .table th, .table td { font-size: 0.8rem; }
            .modal-dialog { max-width: 95%; }
            .btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
            .d-flex.gap-3 { flex-wrap: wrap; gap: 0.5rem; }
        }
    </style>
</head>
<body class="bg-light">
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Expense</li>
                        </ul>
                    </div>
                </div>
            </div>    
            <div class="container py-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h4 class="mb-0">Expense Dashboard</h4>
                    <button class="btn btn-primary add-expense" data-bs-toggle="modal" data-bs-target="#expenseModal">+ Add Expense</button>
                </div>

                <?= $alert ?>

                <!-- Expense Modal -->
                <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="expenseModalLabel"><?= $edit_id ? 'Edit Expense' : 'Add Expense' ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data" id="expenseForm">
                                    <input type="hidden" name="expense_id" id="expense_id" value="<?= htmlspecialchars($edit_id) ?>">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label>Date of Expense</label>
                                            <input type="date" name="expense_date" id="expense_date" class="form-control" value="<?= htmlspecialchars($edit_data['expense_date'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label>Category</label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="">-- Select Category --</option>
                                                <?php
                                                $cat_query = $con->query("SELECT id, name FROM expense_categories");
                                                if ($cat_query) {
                                                    while ($cat = $cat_query->fetch_assoc()) {
                                                        $selected = ($edit_data['category_id'] ?? '') == $cat['id'] ? 'selected' : '';
                                                        echo "<option value='{$cat['id']}' $selected>" . htmlspecialchars($cat['name']) . "</option>";
                                                    }
                                                } else {
                                                    error_log("Category query error: " . $con->error);
                                                    echo "<option value=''>Error loading categories</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label>Amount (₹)</label>
                                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="<?= htmlspecialchars($edit_data['amount'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label>Description</label>
                                            <textarea name="description" id="description" class="form-control" rows="3" required><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label>Upload Receipt (optional)</label>
                                            <input type="file" name="receipt" id="receipt" class="form-control">
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

                <!-- Filters -->
                <div class="mb-3 d-flex gap-3 flex-wrap">
                    <form method="get" class="d-inline-block">
                        <select name="status" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= $status_filter === 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Rejected" <?= $status_filter === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </form>
                    <form method="get" class="d-inline-block">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        <select name="month" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
                            <option value="">All Months</option>
                            <?php
                            $months = $con->query("SELECT DISTINCT DATE_FORMAT(expense_date, '%Y-%m') AS month FROM employee_expenses WHERE employee_id = $employee_id ORDER BY month DESC");
                            while ($row = $months->fetch_assoc()) {
                                $month = $row['month'];
                                $selected = $month_filter === $month ? 'selected' : '';
                                echo "<option value='$month' $selected>" . date('F Y', strtotime("$month-01")) . "</option>";
                            }
                            ?>
                        </select>
                    </form>
                    <button class="btn btn-outline-primary" onclick="shareExpenses()">Share</button>
                    <button class="btn btn-outline-success" onclick="downloadPDF()">Download PDF</button>
                </div>

                <div class="row">
                    <!-- Expenses Table -->
                    <div class="col-12 col-lg-9 mb-3 mb-lg-0">
                        <div class="card card-shadow p-3">
                            <h5 class="mb-3">Your Expense History</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle" id="expenseTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Receipt</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $total = 0;
                                    $monthly = [];
                                    $sql = "SELECT ee.*, c.name AS category_name FROM employee_expenses ee LEFT JOIN expense_categories c ON ee.category_id = c.id WHERE ee.employee_id = ? $status_query $month_query ORDER BY ee.expense_date DESC";
                                    $stmt = $con->prepare($sql);
                                    if ($status_filter && $month_filter) {
                                        $stmt->bind_param("iss", $employee_id, $status_filter, $month_filter);
                                    } elseif ($status_filter) {
                                        $stmt->bind_param("is", $employee_id, $status_filter);
                                    } elseif ($month_filter) {
                                        $stmt->bind_param("is", $employee_id, $month_filter);
                                    } else {
                                        $stmt->bind_param("i", $employee_id);
                                    }
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if ($result->num_rows === 0) {
                                        echo "<tr><td colspan='7' class='text-center'>No expenses found. Try adjusting the filters or add a new expense.</td></tr>";
                                    } else {
                                        while ($row = $result->fetch_assoc()) {
                                            $total += $row['amount'];
                                            $month = date('Y-m', strtotime($row['expense_date']));
                                            $monthly[$month] = ($monthly[$month] ?? 0) + $row['amount'];
                                            $receipt_link = $row['receipt_path'] ? '<a href="' . htmlspecialchars($row['receipt_path']) . '" target="_blank">View</a>' : '-';

                                            echo "<tr>
                                                <td>" . htmlspecialchars($row['expense_date']) . "</td>
                                                <td>" . htmlspecialchars($row['category_name']) . "</td>
                                                <td>₹ " . number_format($row['amount'], 2) . "</td>
                                                <td>" . htmlspecialchars($row['description']) . "</td>
                                                <td><span class='badge bg-" . ($row['status'] === 'Approved' ? 'success' : ($row['status'] === 'Rejected' ? 'danger' : 'warning')) . "'>" . htmlspecialchars($row['status']) . "</span></td>
                                                <td>" . $receipt_link . "</td>
                                                <td>";
                                            if ($row['status'] === 'Pending') {
                                                echo "<button class='btn btn-sm btn-outline-secondary edit-expense-btn' data-id='" . htmlspecialchars($row['id']) . "'>Edit</button> ";
                                                echo "<a href='?delete=" . htmlspecialchars($row['id']) . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                                            } else {
                                                echo "-";
                                            }
                                            echo "</td></tr>";
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Sidebar -->
                    <div class="col-12 col-lg-3">
                        <div class="card card-shadow p-3 mb-3">
                            <h6>Total Expenditure</h6>
                            <h3 class="text-primary">₹ <?= number_format($total, 2) ?></h3>
                        </div>
                        <div class="card card-shadow p-3">
                            <h6>Monthly Totals</h6>
                            <ul class="list-group">
                                <?php foreach ($monthly as $m => $amt): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= date('F Y', strtotime($m . '-01')) ?>
                                        <span class="badge bg-info">₹ <?= number_format($amt, 2) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous"></script>
<script src="/assets/bootstrap/js/bootstrap.bundle.min.js" onerror="console.error('Failed to load local Bootstrap JS')"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Open modal for edit
    <?php if ($edit_id > 0): ?>
        console.log('Opening modal for edit_id: <?= $edit_id ?>');
        try {
            var expenseModal = new bootstrap.Modal(document.getElementById('expenseModal'));
            expenseModal.show();
            // Populate form fields explicitly
            document.getElementById('expense_id').value = '<?= htmlspecialchars($edit_id) ?>';
            document.getElementById('expense_date').value = '<?= htmlspecialchars($edit_data['expense_date'] ?? '') ?>';
            document.getElementById('category_id').value = '<?= htmlspecialchars($edit_data['category_id'] ?? '') ?>';
            document.getElementById('amount').value = '<?= htmlspecialchars($edit_data['amount'] ?? '') ?>';
            document.getElementById('description').value = '<?= htmlspecialchars($edit_data['description'] ?? '') ?>';
            document.querySelector('#expenseModalLabel').textContent = 'Edit Expense';
        } catch (e) {
            console.error('Error opening modal:', e);
        }
    <?php endif; ?>

    // Handle edit button clicks
    document.querySelectorAll('.edit-expense-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const expenseId = this.getAttribute('data-id');
            console.log('Edit button clicked for expense_id: ' + expenseId);
            window.location.href = `?edit=${expenseId}`;
        });
    });

    // Handle modal open for add/edit
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        button.addEventListener('click', function(e) {
            const form = document.getElementById('expenseForm');
            const modalLabel = document.querySelector('#expenseModalLabel');
            const expenseIdInput = document.getElementById('expense_id');

            if (this.classList.contains('add-expense')) {
                console.log('Opening modal for new expense');
                form.reset();
                modalLabel.textContent = 'Add Expense';
                expenseIdInput.value = '0';
            }
        });
    });

    // Conditional modal reset to preserve edit data
    document.getElementById('expenseModal').addEventListener('hidden.bs.modal', function () {
        if (!window.location.search.includes('edit')) {
            document.getElementById('expenseForm').reset();
            document.querySelector('#expenseModalLabel').textContent = 'Add Expense';
            document.querySelector('input[name="expense_id"]').value = '0';
        }
    });

    // Debug form submission
    document.getElementById('expenseForm').addEventListener('submit', function(e) {
        const expenseId = document.getElementById('expense_id').value;
        console.log('Form submitted with expense_id: ' + expenseId);
    });
});

function shareExpenses() {
    const table = document.getElementById('expenseTable');
    let text = 'Expense History\n\n';
    
    const monthFilter = '<?= addslashes($month_filter) ?>';
    const total = '<?= number_format($total, 2) ?>';
    const monthlyTotals = <?php echo json_encode($monthly); ?>;
    
    if (monthFilter && monthlyTotals[monthFilter]) {
        text += `Monthly Total for ${new Date(monthFilter + '-01').toLocaleString('default', { month: 'long', year: 'numeric' })}: ₹${monthlyTotals[monthFilter].toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}\n\n`;
    } else {
        text += `Total Expenditure: ₹${total}\n\n`;
    }

    const rows = table.querySelectorAll('tr');
    rows.forEach((row, index) => {
        if (index === 0) {
            text += Array.from(row.cells).map(cell => cell.textContent).join(' | ') + '\n';
        } else {
            text += Array.from(row.cells).map(cell => cell.textContent.replace('View', '')).join(' | ') + '\n';
        }
    });

    if (monthFilter && monthlyTotals[monthFilter]) {
        text += `\nMonthly Total for ${new Date(monthFilter + '-01').toLocaleString('default', { month: 'long', year: 'numeric' })}: ₹${monthlyTotals[monthFilter].toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    } else {
        text += `\nTotal Expenditure: ₹${total}`;
    }

    if (navigator.share) {
        navigator.share({
            title: 'Expense History',
            text: text
        }).catch(console.error);
    } else {
        alert('Share feature not supported. Copy this text:\n\n' + text);
    }
}

function downloadPDF() {
    const table = document.getElementById('expenseTable');
    const monthFilter = '<?= addslashes($month_filter) ?>';
    const total = '<?= number_format($total, 2) ?>';
    const monthlyTotals = <?php echo json_encode($monthly); ?>;
    
    const container = document.createElement('div');
    container.appendChild(table.cloneNode(true));
    
    const totalDiv = document.createElement('div');
    totalDiv.className = 'pdf-total';
    if (monthFilter && monthlyTotals[monthFilter]) {
        totalDiv.textContent = `Monthly Total for ${new Date(monthFilter + '-01').toLocaleString('default', { month: 'long', year: 'numeric' })}: ₹${monthlyTotals[monthFilter].toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    } else {
        totalDiv.textContent = `Total Expenditure: ₹${total}`;
    }
    container.appendChild(totalDiv);

    container.querySelector('#expenseTable').classList.remove('hidden-for-pdf');
    
    const opt = {
        margin: 1,
        filename: 'expense_history.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(container).save();
}
</script>
</body>
</html>