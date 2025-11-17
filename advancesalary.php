<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

// get user name and other detail
$emp_id = $_SESSION['id'];
$conn = connect();
//$id=$_GET['id'];
$query = "select * from hrm_employee where id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$x = "";
$row = mysqli_fetch_array($result);
//echo "aaaaaaaaaaaaaaaa=".$query;

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;

$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];
//count_where($table, $column, $value)
//{
//$conn=connect();
//$query="select count(*) from $table where $column='$id'";
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

//echo "profile_image".$profile_image;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Advance Salary Management</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <style>
        .history-table { max-height: 300px; overflow-y: auto; }
        .advance-badge { font-size: 0.8em; vertical-align: middle; }
        .action-btn { margin-right: 5px; }
        .filter-container { margin-bottom: 15px; }
        .date-filter { display: inline-block; margin-left: 10px; }
        .date-filter input { width: 150px; }
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
                            <h3 class="page-title">Advance Salary Management System</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Advance Salary Management System</li>
                            </ul>
                        </div>
                    </div>
                </div>
<div class="container mt-4">
    <!-- <h2>Salary Management System</h2> -->

    <!-- Filter -->
    <div class="filter-container">
        <button id="advanceFilterBtn" class="btn btn-primary">Show Employees with Advances</button>
        <div class="date-filter">
            <label for="startDate" class="form-label">Advance Date Range:</label>
            <input type="date" id="startDate" class="form-control d-inline-block">
            <input type="date" id="endDate" class="form-control d-inline-block">
            <button id="applyDateFilter" class="btn btn-secondary btn-sm">Apply</button>
        </div>
    </div>

    <!-- Employee List -->
    <div class="card mb-4">
        <div class="card-header">Employees</div>
        <div class="card-body">
            <table class="table table-striped" id="employeeTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Salary</th>
                        <th>Advance Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Advance Salary Modal -->
    <div class="modal fade" id="advanceModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Advance Salary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <form id="advanceForm">
                        <input type="hidden" id="emp_id">
                        <div class="mb-3">
                            <label class="form-label">Employee Name</label>
                            <input type="text" class="form-control" id="emp_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Advance Amount</label>
                            <input type="number" step="0.01" class="form-control" id="advance_amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monthly Deduction</label>
                            <input type="number" step="0.01" class="form-control" id="monthly_deduction" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Advance Date</label>
                            <input type="datetime-local" class="form-control" id="advance_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Deduction Modal -->
    <div class="modal fade" id="editDeductionModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Monthly Deduction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <form id="editDeductionForm">
                        <input type="hidden" id="edit_advance_id">
                        <div class="mb-3">
                            <label class="form-label">Monthly Deduction</label>
                            <input type="number" step="0.01" class="form-control" id="edit_monthly_deduction" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Deduction Management Modal -->
    <div class="modal fade" id="deductionModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deductionModalTitle">Deduction Management</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deduction_emp_id">
                    <div class="mb-3">
                        <h6>Employee Details:</h6>
                        <p><strong>ID:</strong> <span id="emp_id_display"></span></p>
                        <p><strong>Name:</strong> <span id="emp_name_display"></span></p>
                        <p><strong>Email:</strong> <span id="emp_email_display"></span></p>
                    </div>
                    <div class="mb-3">
                        <h6>Current Salary: <span id="current_salary"></span></h6>
                    </div>
                    <div class="mb-3">
                        <h6>Advance Details</h6>
                        <table class="table" id="advanceTable">
                            <thead>
                                <tr>
                                    <th>Advance Amount</th>
                                    <th>Remaining Amount</th>
                                    <th>Monthly Deduction</th>
                                    <th>Advance Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <h6>Deduction History</h6>
                        <div class="history-table">
                            <table class="table table-striped" id="historyTable">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Month</th>
                                        <th>Actual Salary</th>
                                        <th>Advance Amount</th>
                                        <th>Remaining Amount</th>
                                        <th>Deduction Amount</th>
                                        <th>Deduction Date</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
                    <a href="salary-management.php" class="btn btn-primary mb-3">Salary Managemen System</a>
                    <a href="attandance-all-employee.php" class="btn btn-primary mb-3">Attendance HRM</a>
</div>
</div>
</div>

</div>
</div>
<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script>
$(document).ready(function() {
    let employeeTable;
    let showAdvanceOnly = false;

    // Initialize DataTable for employee table
    function initEmployeeTable() {
        employeeTable = $('#employeeTable').DataTable({
            destroy: true,
            dom: 'Bfrtip',
            buttons: [
                'copy',
                'print',
                {
                    extend: 'pdfHtml5',
                    title: 'Employee Salary Report',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            columnDefs: [
                { targets: 7, orderable: false } // Disable sorting on Action column
            ]
        });
    }

    // Load employees with filter
    function loadEmployees(filter = 'all', startDate = '', endDate = '') {
        console.log('Loading employees with:', { filter, startDate, endDate });
        $.ajax({
            url: 'process.php',
            type: 'POST',
            dataType: 'html',
            data: { 
                action: 'get_employees',
                filter: filter,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                console.log('Employee response length:', response.length);
                $('#employeeTable tbody').html(response);
                if (employeeTable) {
                    employeeTable.destroy();
                }
                initEmployeeTable();
            },
            error: function(xhr, status, error) {
                console.error('Load employees error:', xhr.responseText, status, error);
                alert('Error loading employees: ' + xhr.responseText);
            }
        });
    }

    // Toggle advance filter
    $('#advanceFilterBtn').click(function() {
        showAdvanceOnly = !showAdvanceOnly;
        $(this).text(showAdvanceOnly ? 'Show All Employees' : 'Show Employees with Advances');
        let filter = showAdvanceOnly ? 'with_advance' : 'all';
        loadEmployees(filter, $('#startDate').val(), $('#endDate').val());
    });

    // Apply date filter
    $('#applyDateFilter').click(function() {
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        if (startDate && endDate && startDate > endDate) {
            alert('Start date cannot be after end date');
            return;
        }
        let filter = showAdvanceOnly ? 'with_advance' : 'all';
        loadEmployees(filter, startDate, endDate);
    });

    // Initial load
    loadEmployees();

    // Advance form submission
    $('#advanceForm').submit(function(e) {
        e.preventDefault();
        let advanceAmount = parseFloat($('#advance_amount').val());
        let monthlyDeduction = parseFloat($('#monthly_deduction').val());
        if (isNaN(advanceAmount) || isNaN(monthlyDeduction)) {
            alert('Please enter valid numbers for amount and deduction');
            return;
        }
        if (monthlyDeduction > advanceAmount) {
            alert('Monthly deduction cannot exceed advance amount');
            return;
        }
        $.ajax({
            url: 'process.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'add_advance',
                emp_id: $('#emp_id').val(),
                advance_amount: advanceAmount,
                monthly_deduction: monthlyDeduction,
                advance_date: $('#advance_date').val()
            },
            success: function(res) {
                console.log('Add advance response:', res);
                alert(res.message || 'No message returned');
                if (res.status === 'success') {
                    $('#advanceModal').modal('hide');
                    loadEmployees(showAdvanceOnly ? 'with_advance' : 'all', $('#startDate').val(), $('#endDate').val());
                }
            },
            error: function(xhr, status, error) {
                console.error('Add advance error:', xhr.responseText, status, error);
                alert('Error processing request: ' + (xhr.responseText || error));
            }
        });
    });

    // Edit deduction form submission
    $('#editDeductionForm').submit(function(e) {
        e.preventDefault();
        let monthlyDeduction = parseFloat($('#edit_monthly_deduction').val());
        if (isNaN(monthlyDeduction) || monthlyDeduction <= 0) {
            alert('Please enter a valid deduction amount');
            return;
        }
        $.ajax({
            url: 'process.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'edit_deduction',
                advance_id: $('#edit_advance_id').val(),
                monthly_deduction: monthlyDeduction
            },
            success: function(res) {
                console.log('Edit deduction response:', res);
                alert(res.message || 'No message returned');
                if (res.status === 'success') {
                    $('#editDeductionModal').modal('hide');
                    manageDeductions($('#deduction_emp_id').val(), $('#deductionModalTitle').text().replace('Deduction Management for ', ''));
                }
            },
            error: function(xhr, status, error) {
                console.error('Edit deduction error:', xhr.responseText, status, error);
                alert('Error updating deduction: ' + (xhr.responseText || error));
            }
        });
    });
});

function openAdvanceModal(emp_id, emp_name) {
    $('#emp_id').val(emp_id);
    $('#emp_name').val(emp_name);
    $('#advance_amount').val('');
    $('#monthly_deduction').val('');
    $('#advance_date').val('');
    $('#advanceModal').modal('show');
}

function openEditDeductionModal(advance_id, current_deduction) {
    $('#edit_advance_id').val(advance_id);
    $('#edit_monthly_deduction').val(current_deduction);
    $('#editDeductionModal').modal('show');
}

function deleteAdvance(advance_id) {
    if (!confirm('Are you sure you want to delete this advance?')) {
        return;
    }
    $.ajax({
        url: 'process.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'delete_advance',
            advance_id: advance_id
        },
        success: function(res) {
            console.log('Delete advance response:', res);
            alert(res.message || 'No message returned');
            if (res.status === 'success') {
                manageDeductions($('#deduction_emp_id').val(), $('#deductionModalTitle').text().replace('Deduction Management for ', ''));
                loadEmployees(showAdvanceOnly ? 'with_advance' : 'all', $('#startDate').val(), $('#endDate').val());
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete advance error:', xhr.responseText, status, error);
            alert('Error deleting advance: ' + (xhr.responseText || error));
        }
    });
}

function manageDeductions(emp_id, emp_name) {
    $('#deduction_emp_id').val(emp_id);
    $('#deductionModalTitle').text('Deduction Management for ' + emp_name);
    $.ajax({
        url: 'process.php',
        type: 'POST',
        dataType: 'json',
        data: { 
            action: 'get_deductions',
            emp_id: emp_id
        },
        success: function(data) {
            console.log('Get deductions response:', data);
            if (data.status === 'error') {
                alert('Error: ' + data.message);
                return;
            }
            $('#current_salary').text(data.current_salary || 'N/A');
            $('#emp_id_display').text(data.emp_id || 'N/A');
            $('#emp_name_display').text(data.emp_name || 'N/A');
            $('#emp_email_display').text(data.emp_email || 'N/A');
            $('#advanceTable tbody').html(data.advance_html || '');
            $('#historyTable tbody').html(data.history_html || '');

            // Destroy existing DataTable if any
            if ($.fn.DataTable.isDataTable('#historyTable')) {
                $('#historyTable').DataTable().destroy();
            }

            // Initialize DataTable for history table
            $('#historyTable').DataTable({
                destroy: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        title: 'Deduction History for ' + emp_name,
                        customize: function(data) {
                            return 'Employee ID: ' + (data.emp_id || 'N/A') + '\n' +
                                   'Name: ' + emp_name + '\n' +
                                   'Email: ' + (data.emp_email || 'N/A') + '\n\n' + data;
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Deduction History for ' + emp_name,
                        customize: function(win) {
                            $(win.document.body).prepend(
                                '<h4>Employee Details</h4>' +
                                '<p><strong>ID:</strong> ' + (data.emp_id || 'N/A') + '</p>' +
                                '<p><strong>Name:</strong> ' + emp_name + '</p>' +
                                '<p><strong>Email:</strong> ' + (data.emp_email || 'N/A') + '</p><br>'
                            );
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Deduction History for ' + emp_name,
                        customize: function(doc) {
                            doc.content.splice(1, 0, {
                                text: [
                                    { text: 'Employee ID: ' + (data.emp_id || 'N/A') + '\n', bold: true },
                                    { text: 'Name: ' + emp_name + '\n', bold: true },
                                    { text: 'Email: ' + (data.emp_email || 'N/A') + '\n', bold: true }
                                ],
                                margin: [0, 0, 0, 10]
                            });
                        },
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ],
                pageLength: 10,
                order: [[8, 'desc']] // Sort by Deduction Date (adjusted for new columns)
            });

            $('#deductionModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Get deductions error:', xhr.responseText, status, error);
            alert('Error loading deductions: ' + (xhr.responseText || error));
        }
    });
}

function toggleDeduction(advance_id, status) {
    $.ajax({
        url: 'process.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'toggle_deduction',
            advance_id: advance_id,
            status: status
        },
        success: function(res) {
            console.log('Toggle deduction response:', res);
            alert(res.message || 'No message returned');
            if (res.status === 'success') {
                manageDeductions($('#deduction_emp_id').val(), $('#deductionModalTitle').text().replace('Deduction Management for ', ''));
            }
        },
        error: function(xhr, status, error) {
            console.error('Toggle deduction error:', xhr.responseText, status, error);
            alert('Error updating deduction: ' + (xhr.responseText || error));
        }
    });
}
</script>
</body>
</html>