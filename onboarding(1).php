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


<?php
include 'layouts/config.php';

// Database connection
$conn = $con;

// Handle employee selection
$selected_employee_id = $_GET['employee_id'] ?? null;

// Auto-populate steps for the selected employee
if ($selected_employee_id) {
    $check_sql = "SELECT COUNT(*) FROM employee_onboarding_steps WHERE employee_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $selected_employee_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $step_count = $result->fetch_row()[0];

    if ($step_count == 0) {
        $insert_sql = "INSERT INTO employee_onboarding_steps (employee_id, step_id, status, comment, created_at) 
                       SELECT ?, step_id, 0, '', NOW() 
                       FROM onboarding_steps";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("i", $selected_employee_id);
        $insert_stmt->execute();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && $selected_employee_id) {
    $id = $_POST['id']; // ID from employee_onboarding_steps
    $completed = isset($_POST['completed']) ? 1 : 0;
    $comment = $_POST['comment'] ?? '';
    $update_date = date("Y-m-d H:i:s");

    $sql = "UPDATE employee_onboarding_steps SET status = ?, comment = ?, update_date = ? 
            WHERE id = ? AND employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issii", $completed, $comment, $update_date, $id, $selected_employee_id);
    $stmt->execute();

    // Get step_id for file upload
    $step_sql = "SELECT step_id FROM employee_onboarding_steps WHERE id = ?";
    $step_stmt = $conn->prepare($step_sql);
    $step_stmt->bind_param("i", $id);
    $step_stmt->execute();
    $step_result = $step_stmt->get_result();
    $step_row = $step_result->fetch_assoc();
    $step_id = $step_row['step_id'];

    if (!empty($_FILES['files']['name'][0])) {
        foreach ($_FILES['files']['name'] as $key => $fileName) {
            $fileTmp = $_FILES['files']['tmp_name'][$key];
            $filePath = "uploads/" . $selected_employee_id . "_" . time() . "_" . $fileName;
            if (move_uploaded_file($fileTmp, $filePath)) {
                $insertFile = "INSERT INTO onboarding_files (step_id, employee_id, document_name, file_path) 
                               VALUES (?, ?, ?, ?)";
                $stmtFile = $conn->prepare($insertFile);
                $stmtFile->bind_param("iiss", $step_id, $selected_employee_id, $fileName, $filePath);
                $stmtFile->execute();
            }
        }
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?employee_id=" . $selected_employee_id);
    exit();
}

// Handle file deletion
if (isset($_GET['delete_file']) && $selected_employee_id) {
    $file_id = $_GET['delete_file'];
    $sql = "SELECT file_path FROM onboarding_files WHERE id = ? AND employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $file_id, $selected_employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();

    if ($file && unlink($file['file_path'])) {
        $deleteSql = "DELETE FROM onboarding_files WHERE id = ? AND employee_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $file_id, $selected_employee_id);
        $deleteStmt->execute();
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?employee_id=" . $selected_employee_id);
    exit();
}

// Fetch employees
$emp_query = "SELECT id, fname, lname, email, doj FROM hrm_employee";
$emp_result = $conn->query($emp_query);
$employees = $emp_result->fetch_all(MYSQLI_ASSOC);

// Fetch all onboarding steps for the selected employee
$steps = [];
if ($selected_employee_id) {
    $sql = "SELECT eos.id, eos.step_id, os.step_name, os.description, eos.status, eos.comment, eos.update_date, eos.created_at 
            FROM employee_onboarding_steps eos 
            JOIN onboarding_steps os ON eos.step_id = os.step_id 
            WHERE eos.employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $steps = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Onboarding</title>
   
 <?php include 'layouts/title-meta.php'; ?>

<?php include 'layouts/head-css.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"> 
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        /* .container { max-width: 900px; } */
        
        .accordion-button { background-color: #007bff !important; color: white !important; }
        .accordion-button.submitted { background-color: #ffc1cc !important; color: #000 !important; }
        .accordion-button.completed { background-color: #28a745 !important; }
        .accordion-button:not(.collapsed) { background-color: #0056b3 !important; }
        .accordion-button.submitted:not(.collapsed) { background-color: #ffb3c1 !important; }
        .accordion-button.completed:not(.collapsed) { background-color: #218838 !important; }
        
        .progress {
            height: 25px;
            background-color: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(45deg, #00cc00, #28a745);
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: width 0.6s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .update-date { font-size: 12px; color: #6c757d; float: right; font-style: italic; }
        .icon-btn { cursor: pointer; font-size: 18px; margin-left: 10px; }
        .file-list { margin-top: 10px; }
        #incompleteSteps { display: none; }
        .search-container { margin-bottom: 20px; }
        .search-row { display: flex; flex-wrap: wrap; gap: 10px; }
        .search-field { flex: 1; min-width: 150px; }
        /* .accordion-button::after{
            background-color: red;
        } */
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="container padding-top-ams">
            <div class="container mt-5">
                <h1 class="mb-4">Employee Onboarding Process Management System</h1>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Onboarding Process Management System</li>
                </ul>
              
    <!-- <h1 class="text-center mb-4">Employee Onboarding Process</h1> -->

    <!-- Employee Search and Selection -->
    <div class="search-container">
        <div class="search-row">
            <!-- <div class="search-field">
                <label for="searchId">ID:</label>
                <input type="text" class="form-control" id="searchId" placeholder="Search by ID">
            </div>
            <div class="search-field">
                <label for="searchFname">First Name:</label>
                <input type="text" class="form-control" id="searchFname" placeholder="Search by First Name">
            </div>
            <div class="search-field">
                <label for="searchLname">Last Name:</label>
                <input type="text" class="form-control" id="searchLname" placeholder="Search by Last Name">
            </div>
            <div class="search-field">
                <label for="searchEmail">Email:</label>
                <input type="text" class="form-control" id="searchEmail" placeholder="Search by Email">
            </div>
            <div class="search-field">
                <label for="searchDoj">DOJ:</label>
                <input type="text" class="form-control" id="searchDoj" placeholder="Search by DOJ">
            </div> -->
        </div>
        <div class="mt-2">
            <button class="btn btn-primary" id="searchButton" style="display:none;">Search</button>
        <a href="onboardingtest.php" class="btn btn-primary">Add Onboarding Steps</a>

        </div>

        <div class="row align-items-end mt-3">
    <div class="col-md-8 col-12">
        <label for="selectEmployee" class="form-label">Select Employee:</label>
        <select class="form-select" id="selectEmployee" name="employee_id">
            <option value="">Select an employee</option>
            <?php foreach ($employees as $emp): ?>
                <option value="<?= $emp['id']; ?>" 
                        data-id="<?= $emp['id']; ?>" 
                        data-fname="<?= strtolower($emp['fname']); ?>" 
                        data-lname="<?= strtolower($emp['lname']); ?>" 
                        data-email="<?= strtolower($emp['email']); ?>" 
                        data-doj="<?= strtolower($emp['doj']); ?>"
                        <?= $selected_employee_id == $emp['id'] ? 'selected' : ''; ?>>
                    <?= $emp['id'] . ' - ' . $emp['fname'] . ' ' . $emp['lname']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4 col-12 mt-2 mt-md-0">
        <button class="btn btn-success w-100" id="viewOnboardingButton">View Onboarding</button>
    </div>
</div>

    </div>

    <?php if ($selected_employee_id): ?>
        <?php if (!empty($steps)): ?>
            <!-- Progress Bar -->
            <div class="progress mb-3">
                <div class="progress-bar" role="progressbar" id="progressBar"></div>
            </div>

            <!-- Incomplete Steps Toggle -->
            <button class="btn btn-warning mt-3" id="toggleIncomplete">Show Incomplete Steps</button>
            <div class="accordion mt-3" id="incompleteSteps">
                <?php foreach ($steps as $index => $step): ?>
                    <?php if (!$step['status']): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" 
                                        type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#incomplete<?= $index; ?>">
                                    <?= $step['step_name']; ?>
                                </button>
                            </h2>
                            <div id="incomplete<?= $index; ?>" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <?= $step['description']; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="accordion mt-3" id="onboardingAccordion">
                <?php foreach ($steps as $index => $step): ?>
                    <?php
                        $filesQuery = "SELECT * FROM onboarding_files WHERE step_id = ? AND employee_id = ?";
                        $stmtFiles = $conn->prepare($filesQuery);
                        $stmtFiles->bind_param("ii", $step['step_id'], $selected_employee_id);
                        $stmtFiles->execute();
                        $filesResult = $stmtFiles->get_result();
                        $files = $filesResult->fetch_all(MYSQLI_ASSOC);
                        $isSubmitted = !empty($step['update_date']);
                    ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $step['status'] ? 'completed' : ($isSubmitted ? 'submitted' : '') ?>" 
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?= $index; ?>">
                                <?= $step['step_name']; ?>
                            </button>
                            <span class="update-date">Last Updated: <?= !empty($step['update_date']) ? date("d M Y, H:i", strtotime($step['update_date'])) : 'Not updated yet'; ?></span>
                        </h2>
                        <div id="collapse<?= $index; ?>" class="accordion-collapse collapse" data-bs-parent="#onboardingAccordion">
                            <div class="accordion-body">
                                <?= $step['description']; ?>

                                <form action="" method="POST" enctype="multipart/form-data" class="step-form mt-3">
                                    <input type="hidden" name="id" value="<?= $step['id']; ?>">
                                    <div class="form-check">
                                        <input class="form-check-input step-checkbox" 
                                               type="checkbox" 
                                               name="completed"
                                               <?= $step['status'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Mark as Completed</label>
                                    </div>
                                    <textarea name="comment" class="form-control mt-2" rows="3" placeholder="Add comment"><?= $step['comment']; ?></textarea>
                                    <input type="file" name="files[]" multiple class="form-control mt-2">
                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                </form>

                                <div class="file-list mt-3">
                                    <?php foreach ($files as $file): ?>
                                        <div>
                                            <span><?= $file['document_name']; ?></span>
                                            <i class="fa fa-eye text-primary icon-btn" onclick="viewFile('<?= $file['file_path']; ?>')"></i>
                                            <a href="<?= $file['file_path']; ?>" download class="fa fa-download text-success icon-btn"></a>
                                            <a href="?employee_id=<?= $selected_employee_id; ?>&delete_file=<?= $file['id']; ?>" 
                                               class="fa fa-trash text-danger icon-btn" 
                                               onclick="return confirm('Are you sure you want to delete this file?');"></a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info mt-3">No onboarding steps found for this employee or default steps missing.</div>
        <?php endif; ?>
    <?php endif; ?>


<!-- View File Modal -->
<div class="modal fade" id="fileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe id="fileViewer" width="100%" height="400px"></iframe>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>
 <?php 
include 'layouts/customizer.php';
 ?>
 <?php 
 include 'layouts/vendor-scripts.php';
 ?>

<script>
    // Toggle incomplete steps
    document.getElementById('toggleIncomplete')?.addEventListener('click', function () {
        let incompleteSteps = document.getElementById('incompleteSteps');
        if (incompleteSteps.style.display === "none") {
            incompleteSteps.style.display = "block";
            this.textContent = "Hide Incomplete Steps";
        } else {
            incompleteSteps.style.display = "none";
            this.textContent = "Show Incomplete Steps";
        }
    });

    // View file in modal
    function viewFile(url) {
        document.getElementById("fileViewer").src = url;
        new bootstrap.Modal(document.getElementById("fileModal")).show();
    }

    // Progress bar and color functionality
    function updateProgress() {
        const checkboxes = document.querySelectorAll('.step-checkbox');
        const total = checkboxes.length;
        const completed = Array.from(checkboxes).filter(cb => cb.checked).length;
        const percentage = Math.round((completed / total) * 100);
        
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = percentage + '%';
        progressBar.textContent = percentage + '% Completed';
        
        checkboxes.forEach(checkbox => {
            const accordionButton = checkbox.closest('.accordion-item')
                .querySelector('.accordion-button');
            const isSubmitted = accordionButton.nextElementSibling.textContent.includes('Last Updated:') && 
                              !accordionButton.nextElementSibling.textContent.includes('Not updated yet');
            
            accordionButton.classList.remove('completed', 'submitted');
            if (checkbox.checked) {
                accordionButton.classList.add('completed');
            } else if (isSubmitted) {
                accordionButton.classList.add('submitted');
            }
        });
    }

    // Initialize progress bar if steps exist
    if (document.querySelector('.step-checkbox')) {
        updateProgress();
        document.querySelectorAll('.step-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateProgress();
                this.closest('form').submit();
            });
        });
    }

    // Search functionality with button
    const searchInputs = {
        id: document.getElementById('searchId'),
        fname: document.getElementById('searchFname'),
        lname: document.getElementById('searchLname'),
        email: document.getElementById('searchEmail'),
        doj: document.getElementById('searchDoj')
    };
    const selectEmployee = document.getElementById('selectEmployee');
    const searchButton = document.getElementById('searchButton');
    const viewOnboardingButton = document.getElementById('viewOnboardingButton');

    searchButton.addEventListener('click', function() {
        const anyFieldFilled = Object.values(searchInputs).some(input => input.value.trim() !== '');
        if (!anyFieldFilled) {
            alert('Please fill at least one search field.');
            return;
        }
        filterEmployees();
    });

    viewOnboardingButton.addEventListener('click', function() {
        const selectedValue = selectEmployee.value;
        if (selectedValue) {
            window.location.href = '?employee_id=' + selectedValue;
        } else {
            alert('Please select an employee.');
        }
    });

    function filterEmployees() {
        const filters = {
            id: searchInputs.id.value.toLowerCase(),
            fname: searchInputs.fname.value.toLowerCase(),
            lname: searchInputs.lname.value.toLowerCase(),
            email: searchInputs.email.value.toLowerCase(),
            doj: searchInputs.doj.value.toLowerCase()
        };

        const options = selectEmployee.querySelectorAll('option:not(:first-child)');
        options.forEach(option => {
            const id = option.getAttribute('data-id');
            const fname = option.getAttribute('data-fname');
            const lname = option.getAttribute('data-lname');
            const email = option.getAttribute('data-email');
            const doj = option.getAttribute('data-doj');

            const matches = (!filters.id || id.includes(filters.id)) &&
                           (!filters.fname || fname.includes(filters.fname)) &&
                           (!filters.lname || lname.includes(filters.lname)) &&
                           (!filters.email || email.includes(filters.email)) &&
                           (!filters.doj || doj.includes(filters.doj));

            option.style.display = matches ? '' : 'none';
        });
    }
</script>
</body>
</html>