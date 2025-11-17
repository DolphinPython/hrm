<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<!-- <style>
    .header{
        display: none;
    }
</style> -->

<?php include 'include/function.php';

// Get user name and other details
$emp_id = $_SESSION['id'];
$conn = connect();
$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;
$maxDate = date('Y-m-d', strtotime('-18 years'));
$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

// Handle employee deletion
// Handle employee deletion
if (isset($_GET['id'])) {
   $delete_emp_id = intval($_GET['id']);
  
    error_log("Attempting to delete employee ID: $delete_emp_id at " . date('Y-m-d H:i:s'), 3, 'debug.log');

    $conn = connect();
    if (!$conn) {
        error_log("Database connection failed: " . mysqli_connect_error(), 3, 'debug.log');
        header("Location: employees.php?error=delete_failed&message=" . urlencode("Database connection failed"));
        exit();
    }

    // Fetch the target employee's details
    $query = "SELECT role FROM hrm_employee WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delete_emp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 0) {
        error_log("Invalid employee ID: $delete_emp_id", 3, 'debug.log');
        header("Location: employees.php?error=invalid_employee");
        exit();
    }

    $target_employee = $result->fetch_assoc();

    // Prevent deletion of employees with admin or super admin roles
    if ($target_employee['role'] == 'admin' || $target_employee['role'] == 'super admin') {
        error_log("Cannot delete employee ID $delete_emp_id: Admin or Super Admin role", 3, 'debug.log');
        header("Location: employees.php?error=cannot_delete");
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        // Archive employee with explicit columns
        $insert_query = "INSERT INTO archived_employees 
            (id, fname, lname, email, office_email, mobile1, role, department_id, designation_id, 
             status, password, image, dob, gender, bgroup, marital_status, current_address, 
             permanent_address, house_type, staying_current_residence, living_current_city, 
             doj, probation_period, employee_type, work_location, experience, job_title, created_at)
            SELECT id, fname, lname, email, office_email, mobile1, role, department_id, designation_id, 
                   status, password, image, dob, gender, bgroup, marital_status, current_address, 
                   permanent_address, house_type, staying_current_residence, living_current_city, 
                   doj, probation_period, employee_type, work_location, experience, job_title, NOW()
            FROM hrm_employee 
            WHERE id = ?";
        $stmt = $conn->prepare($insert_query);
        if (!$stmt) {
            throw new Exception("Prepare failed for archive query: " . $conn->error);
        }
        $stmt->bind_param("i", $delete_emp_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to archive employee: " . $stmt->error);
        }
        $stmt->close();
        error_log("Successfully archived employee ID: $delete_emp_id", 3, 'debug.log');

        // Delete from dependent tables
        $dependent_tables = [
            "hrm_chat_group_members" => "user_id",
            "hrm_employee_education" => "emp_id",
            "hrm_employee_social" => "emp_id",
            "hrm_employee_family" => "emp_id",
            "hrm_employee_documents" => "emp_id",
            "hrm_reporting_manager" => "employee_id",
            "hrm_reporting_manager" => "reporting_manager_id"
        ];
/*
        foreach ($dependent_tables as $table => $column) {
            $delete_query = "DELETE FROM $table WHERE $column = ?";
            $stmt = $conn->prepare($delete_query);
            if ($stmt) {
                $stmt->bind_param("i", $delete_emp_id);
                if (!$stmt->execute()) {
                    error_log("Failed to delete from $table for employee ID $delete_emp_id: " . $stmt->error, 3, 'debug.log');
                    // Continue instead of throwing to avoid stopping the transaction
                }
                $stmt->close();
            } else {
                error_log("Prepare failed for $table deletion: " . $conn->error, 3, 'debug.log');
            }
        }

        // Delete the employee
        $delete_employee_query = "DELETE FROM hrm_employee WHERE id = ?";
        $stmt = $conn->prepare($delete_employee_query);
        if (!$stmt) {
            throw new Exception("Prepare failed for employee deletion: " . $conn->error);
        }
        $stmt->bind_param("i", $delete_emp_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete employee: " . $stmt->error);
        }
        $stmt->close();
        error_log("Successfully deleted employee ID: $delete_emp_id", 3, 'debug.log');
*/

// temporary
//  echo   $statuschangetoarchive = "UPDATE hrm_employee SET archive_status = 1 WHERE id = $delete_emp_id ";
// //  exit();
//     $conn->prepare($statuschangetoarchive);

$conn->query("UPDATE `hrm_employee` SET `status`='3',`archive_status`='1' WHERE `id` = $delete_emp_id");

//  temporary


        // Commit the transaction
        mysqli_commit($conn);
        error_log("Transaction committed for employee ID: $delete_emp_id", 3, 'debug.log');
        header("Location: employees.php?success=deleted");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Transaction failed for employee ID $delete_emp_id: " . $e->getMessage(), 3, 'debug.log');
        header("Location: employees.php?error=delete_failed&message=" . urlencode($e->getMessage()));
        exit();
    }
}

// Error and success message handling
if (isset($_GET['success']) && $_GET['success'] === 'deleted') {
    echo '<div class="alert alert-success">Employee deleted successfully!</div>';
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    $message = match ($error) {
        'invalid_employee' => 'Invalid Employee!',
        'delete_failed' => 'Failed to delete employee.' . (isset($_GET['message']) ? ' ' . htmlspecialchars($_GET['message']) : ''),
        'cannot_delete' => 'This employee cannot be deleted.',
        'archive_failed' => 'Failed to archive employee.',
        default => 'An error occurred.',
    };
    echo '<div class="alert alert-danger">' . $message . '</div>';
}
?>
<style>
    .container .filter-row {
        margin: 20px 0px;
    }
</style>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Employees - HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>

    <script language="javascript">
        function update_personal_info(emp_id, fname, lname, dob, gender, bgroup, marital_status) {
            $.ajax({
                type: "GET",
                url: "update_personal_info.php",
                data: "emp_id=" + emp_id + "&fname=" + fname + "&lname=" + lname + "&dob=" + dob
                    + "&gender=" + gender + "&bgroup=" + bgroup + "&marital_status=" + marital_status,
                success: function (data) {
                    alert(data);
                }
            });
        }
        function add_personal_info(fname, lname, dob, gender, bgroup, marital_status) {
            $.ajax({
                type: "GET",
                url: "add_personal_info.php",
                data: "fname=" + fname + "&lname=" + lname + "&dob=" + dob
                    + "&gender=" + gender + "&bgroup=" + bgroup + "&marital_status=" + marital_status,
                success: function (data) {
                    alert(data);
                    $("#last_insert_id").val(data);
                }
            });
        }
        function update_contact_info(emp_id, office_email, email, current_address, permanent_address,
            house_type, staying_current_residence, living_current_city, facebook1, twitter1, linkedin1) {
            $.ajax({
                type: "GET",
                url: "update_contact_info.php",
                data: {
                    emp_id: emp_id,
                    office_email: office_email,
                    email: email,
                    current_address: current_address,
                    permanent_address: permanent_address,
                    house_type: house_type,
                    staying_current_residence: staying_current_residence,
                    living_current_city: living_current_city,
                    facebook: facebook1,
                    twitter: twitter1,
                    linkedin: linkedin1
                },
                success: function (response) {
                    if (response.includes("already exists")) {
                        alert("❌ " + response);
                    } else if (response.includes("Contact Info Updated")) {
                        alert("✅ " + response);
                    } else {
                        alert("ℹ️ " + response);
                    }
                },
                error: function (xhr, status, error) {
                    alert("An error occurred: " + error);
                }
            });
        }
        function update_work_info(emp_id, doj, probation_period, employee_type, work_location,
            experience, designation_id, job_title, department_id) {
            $.ajax({
                type: "GET",
                url: "update_work_info.php",
                data: "emp_id=" + emp_id + "&doj=" + doj + "&probation_period=" + probation_period
                    + "&employee_type=" + employee_type
                    + "&work_location=" + work_location + "&experience=" + experience +
                    "&designation_id=" + designation_id +
                    "&job_title=" + job_title + "&department_id=" + department_id,
                success: function (data) {
                    alert(data);
                    location.reload(); // Reload to reflect updated emp_id
                }
            });
        }
        function update_education(emp_id, qualification_type, course_name, course_type, stream, start_date,
            end_date, college_name, university_name, grade) {
            $.ajax({
                type: "GET",
                url: "update-education-ajax.php",
                data: "emp_id=" + emp_id + "&qualification_type=" + qualification_type + "&course_name=" + course_name
                    + "&course_type=" + course_type
                    + "&stream=" + stream + "&start_date=" + start_date +
                    "&end_date=" + end_date +
                    "&college_name=" + college_name + "&university_name=" + university_name
                    + "&grade=" + grade,
                success: function (data) {
                    alert(data);
                }
            });
        }
        function display_education_detail(emp_id, div_id) {
            $.ajax({
                type: "GET",
                url: "display-education-ajax.php",
                data: "emp_id=" + emp_id,
                success: function (data) {
                    div_id.innerHTML = data;
                },
                error: function () {
                    alert('Not OKay');
                }
            });
        }
        function update_reporting_manager(emp_id, reporting_manager_id, reporting_manager_type) {
            $.ajax({
                type: "GET",
                url: "update-reporting-manager-ajax.php",
                data: "emp_id=" + emp_id + "&reporting_manager_id=" + reporting_manager_id + "&reporting_manager_type=" + reporting_manager_type,
                success: function (data) {
                    alert(data);
                }
            });
        }
        function display_reporting_manager(emp_id, div_id) {
            $.ajax({
                type: "GET",
                url: "display-reporting-manager-ajax.php",
                data: "emp_id=" + emp_id,
                success: function (data) {
                    div_id.innerHTML = data;
                },
                error: function () {
                    alert('Not OKay');
                }
            });
        }
        function update_family(emp_id, name, relationship, phone, dependent) {
            $.ajax({
                type: "GET",
                url: "update-family-ajax.php",
                data: "emp_id=" + emp_id + "&name=" + name + "&relationship=" + relationship
                    + "&phone=" + phone + "&dependent=" + dependent,
                success: function (data) {
                    alert(data);
                }
            });
        }
        function add_documents(emp_id, document_type, document_number, file_document) {
            var checkboxes = document.getElementsByName('proof1[]');
            var vals = "";
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                if (checkboxes[i].checked) {
                    vals += "," + checkboxes[i].value;
                }
            }
            if (vals) vals = vals.substring(1);
            var input_file_field_id = "#" + file_document;
            var file_data = $(input_file_field_id).prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('emp_id', emp_id);
            form_data.append('document_type', document_type);
            form_data.append('document_number', document_number);
            form_data.append('proof_of', vals);

            $.ajax({
                url: 'upload.php',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (output) {
                    alert(output);
                },
                error: function (data) {
                    alert('error');
                }
            });
        }
        function submitProfileForm(empId) {
            var form = document.createElement("form");
            form.method = "POST";
            form.action = "profile.php";

            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "id";
            input.value = empId;
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }
        function confirmDelete(empId) {
            if (confirm("Are you sure you want to archive this employee?")) {
                window.location.href = "employees.php?id=" + empId;
            }
            return false;
        }
    </script>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            <div class="content container-fluid pb-0">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Employee</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Employee</li>
                            </ul>
                        </div>
                        <div class="col-auto float-end ms-auto">
                            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_employee"><i
                                    class="fa-solid fa-plus"></i> Add Employee </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Search Filter -->
                <div class="container">
                    <div class="row filter-row">
                        <form method="GET" action="employees.php" class="row g-3">
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="employee_id"
                                        value="<?php echo isset($_GET['employee_id']) ? htmlspecialchars($_GET['employee_id']) : ''; ?>"
                                        placeholder="Employee ID">
                                    <label>Employee ID</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="employee_name"
                                        value="<?php echo isset($_GET['employee_name']) ? htmlspecialchars($_GET['employee_name']) : ''; ?>"
                                        placeholder="Employee Name">
                                    <label>Employee Name</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="form-floating">
                                    <select class="form-select" name="designation">
                                        <option value="">Select Designation</option>
                                        <?php
                                        $query_designations = "SELECT * FROM hrm_designation";
                                        $result_designations = mysqli_query($conn, $query_designations);
                                        while ($row_designation = mysqli_fetch_array($result_designations)) {
                                            $selected = (isset($_GET['designation']) && $_GET['designation'] == $row_designation['id']) ? 'selected' : '';
                                            echo '<option value="' . $row_designation['id'] . '" ' . $selected . '>' . htmlspecialchars($row_designation['name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label>Designation</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3 d-grid">
                                <button type="submit" class="btn btn-success">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Search Filter -->

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Designation & Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $current_month = date("m") - 1;
                        $current_year = date("Y");

                        // Build the base query
                        $query = "SELECT * FROM hrm_employee WHERE status = 1 and archive_status =0";

                        // Add search filters
                        $conditions = [];
                        if (isset($_GET['employee_id']) && !empty($_GET['employee_id'])) {
                            $employee_id = mysqli_real_escape_string($conn, $_GET['employee_id']);
                            $conditions[] = "emp_id = '$employee_id'";
                        }
                        if (isset($_GET['employee_name']) && !empty($_GET['employee_name'])) {
                            $employee_name = mysqli_real_escape_string($conn, $_GET['employee_name']);
                            $conditions[] = "(fname LIKE '%$employee_name%' OR lname LIKE '%$employee_name%' OR CONCAT(fname, ' ', lname) LIKE '%$employee_name%')";
                        }
                        if (isset($_GET['designation']) && !empty($_GET['designation'])) {
                            $designation_id = mysqli_real_escape_string($conn, $_GET['designation']);
                            $conditions[] = "designation_id = '$designation_id'";
                        }

                        // Combine conditions with the query
                        if (!empty($conditions)) {
                            $query .= " AND " . implode(" AND ", $conditions);
                        }

                        $query .= " ORDER BY fname ASC";
                        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

                        // Check if there are results
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_array($result)) {
                                $profile_image = $profile_image_dir . "/" . $row['image'];
                                $designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
                                $department = get_value1("hrm_department", "name", "id", $row['department_id']);
                                if (empty($designation))
                                    $designation = "";
                                if (empty($department))
                                    $department = "";
                                ?>
                                <tr>
                                    <td>
                                        <a class="avatar" href="#" onclick="submitProfileForm(<?php echo $row['id']; ?>)">
                                            <img src="<?php echo $profile_image; ?>" alt="User Image" width="50">
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['emp_id']); ?></td>
                                    <td>
                                        <a href="#" onclick="submitProfileForm(<?php echo $row['id']; ?>)">
                                            <?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($designation . ", " . $department); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" onclick="submitProfileForm(<?php echo $row['id']; ?>)">
                                                    <i class="fa-solid fa-user m-r-5"></i> Profile
                                                </a>
                                                <!-- <a class="dropdown-item" href="edit-employees.php?empeditid=<?php echo $row['id']; ?>">
                                                    <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                </a> -->
                                                <!-- <a class="dropdown-item" href="" data-bs-toggle="modal"
                                                    data-bs-target="#edit_employee<?php echo $row['id']; ?>">
                                                    <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                </a> -->
                                                <a class="dropdown-item"
                                                    href="attendance-reports-admin.php?employee_id=<?php echo $row['id']; ?>&month=<?php echo $current_month; ?>&year=<?php echo $current_year; ?>&b1=Search">
                                                    <i class="fa-regular fa-calendar m-r-5"></i> Attendance Detail
                                                </a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="return confirmDelete(<?php echo $row['id']; ?>);">
                                                    <i class="fa-solid fa-archive m-r-5"></i> Archive
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Edit Employee Modal -->
                <?php
                $current_month = date("m") - 1;
                $current_year = date("Y");

                // Build the base query
                $query = "SELECT * FROM hrm_employee WHERE status = 1";

                // Add search filters
                $conditions = [];
                if (isset($_GET['employee_id']) && !empty($_GET['employee_id'])) {
                    $employee_id = mysqli_real_escape_string($conn, $_GET['employee_id']);
                    $conditions[] = "emp_id = '$employee_id'";
                }
                if (isset($_GET['employee_name']) && !empty($_GET['employee_name'])) {
                    $employee_name = mysqli_real_escape_string($conn, $_GET['employee_name']);
                    $conditions[] = "(fname LIKE '%$employee_name%' OR lname LIKE '%$employee_name%' OR CONCAT(fname, ' ', lname) LIKE '%$employee_name%')";
                }
                if (isset($_GET['designation']) && !empty($_GET['designation'])) {
                    $designation_id = mysqli_real_escape_string($conn, $_GET['designation']);
                    $conditions[] = "designation_id = '$designation_id'";
                }

                // Combine conditions with the query
                if (!empty($conditions)) {
                    $query .= " AND " . implode(" AND ", $conditions);
                }

                $query .= " ORDER BY fname ASC";
                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

                // Check if there are results
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $profile_image = $profile_image_dir . "/" . $row['image'];
                        $designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
                        $department = get_value1("hrm_department", "name", "id", $row['department_id']);
                        if (empty($designation))
                            $designation = "";
                        if (empty($department))
                            $department = "";
                        ?>
                        <?php
                        $query_edit = "SELECT * FROM hrm_employee WHERE status = 1 AND id = '{$row['id']}';";
                        $result_edit = mysqli_query($conn, $query_edit) or die(mysqli_error($conn));
                        $row_edit = mysqli_fetch_array($result_edit);
                        ?>
                        <div id="edit_employee<?php echo $row['id']; ?>" class="modal custom-modal fade" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Employee</h5>
                                        <?php if (isset($row['id'])) { ?>
                                            <?php if ($row['image'] != "") { ?>
                                                <div id="targetLayer">
                                                    <img src="upload-image/<?php echo $row['image']; ?>" height="100" width="100"
                                                        class="img-circle elevation-2" alt="User Image">
                                                </div>
                                            <?php } ?>
                                            <form name="image_form" id="image_form" method="post" enctype="multipart/form-data"
                                                action="profile-image-upload.php">
                                                <input type="text" name="emp_id_for_image" id="emp_id_for_image"
                                                    value="<?php echo $row['id']; ?>">
                                                <label for="img1" class="custom-file-upload">Select Image</label>
                                                <span id="file-selected"></span>
                                                <input type="file" name="img1" id="img1" value="Image Upload">
                                                <input name="button_image" id="button_image" type="submit" value="Upload" />
                                            </form>
                                            <script language="javascript">
                                                $(document).ready(function (e) {
                                                    $('#img1').bind('change', function () {
                                                        var fileName = $(this).val();
                                                        $('#file-selected').html(fileName);
                                                    });
                                                    $("#image_form").on('submit', function (e) {
                                                        e.preventDefault();
                                                        $.ajax({
                                                            url: "profile-image-upload.php",
                                                            type: "POST",
                                                            data: new FormData(this),
                                                            contentType: false,
                                                            cache: false,
                                                            processData: false,
                                                            success: function (data) {
                                                                $("#targetLayer").html(data);
                                                                $("#file-selected").html('');
                                                            },
                                                            error: function (data) {
                                                                console.log("error");
                                                                console.log(data);
                                                            }
                                                        });
                                                    });
                                                });
                                            </script>
                                            <div>
                                                <strong>Employee ID:</strong> <?php echo htmlspecialchars($row['emp_id']); ?><br>
                                                <strong>Job Title:</strong> <?php echo htmlspecialchars($row['job_title']); ?><br>
                                                <strong>Designation:</strong> <?php if ($row['designation_id'] != 0 && $row['designation_id'] != "") {
                                                    echo htmlspecialchars(get_value("hrm_designation", "name", $row['designation_id']));
                                                } ?><br>
                                                <strong>Department:</strong> <?php if ($row['department_id'] != 0 && $row['department_id'] != "") {
                                                    echo htmlspecialchars(get_value("hrm_department", "name", $row['department_id']));
                                                } ?><br>
                                            </div>
                                        <?php } ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <h5 class="modal-title bg-success">PERSONAL INFO</h5>
                                        <form name="f1" id="f1" method="post" action="#">
                                            <div class="profile-widget">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="input-block mb-3">
                                                            <label class="col-form-label">First Name <span
                                                                    class="text-danger">*</span></label>
                                                            <input required class="form-control"
                                                                value="<?php echo htmlspecialchars($row_edit['fname']); ?>" type="text"
                                                                name="fname<?php echo $row_edit['id']; ?>"
                                                                id="fname<?php echo $row_edit['id']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="input-block mb-3">
                                                            <label class="col-form-label">Last Name</label>
                                                            <input required class="form-control"
                                                                value="<?php echo htmlspecialchars($row_edit['lname']); ?>" type="text"
                                                                name="lname<?php echo $row_edit['id']; ?>"
                                                                id="lname<?php echo $row_edit['id']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="input-block mb-3">
                                                            <label class="col-form-label">Date Of Birth <span
                                                                    class="text-danger">*</span></label>
                                                            <div class="cal-icon1">
                                                                <input class="form-control"
                                                                    value="<?php echo date('Y-m-d', strtotime($row['dob'])); ?>"
                                                                    name="dob<?php echo $row_edit['id']; ?>"
                                                                    id="dob<?php echo $row_edit['id']; ?>" type="date"
                                                                    max="<?php echo $maxDate; ?>" date-format="YYYY-MM-DD">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="input-block mb-3">
                                                            <label class="col-form-label">Gender <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="gender<?php echo $row_edit['id']; ?>"
                                                                id="gender<?php echo $row_edit['id']; ?>" class="form-control">
                                                                <option value="1" <?php if ($row['gender'] == "1")
                                                                    echo "selected='selected'"; ?>>Male</option>
                                                                <option value="2" <?php if ($row['gender'] == "2")
                                                                    echo "selected='selected'"; ?>>Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="input-block mb-3">
                                                            <label class="col-form-label">Blood Group</label>
                                                            <select name="bgroup<?php echo $row_edit['id']; ?>"
                                                                id="bgroup<?php echo $row_edit['id']; ?>" class="form-control">
                                                                <option value="A negative" <?php if ($row['bgroup'] == "A negative")
                                                                    echo "selected='selected'"; ?>>A negative
                                                                </option>
                                                                <option value="A positive" <?php if ($row['bgroup'] == "A positive")
                                                                    echo "selected='selected'"; ?>>A positive
                                                                </option>
                                                                <option value="B negative" <?php if ($row['bgroup'] == "B negative")
                                                                    echo "selected='selected'"; ?>>B negative
                                                                </option>
                                                                <option value="B positive" <?php if ($row['bgroup'] == "B positive")
                                                                    echo "selected='selected'"; ?>>B positive
                                                                </option>
                                                                <option value="AB negative" <?php if ($row['bgroup'] == "AB negative")
                                                                    echo "selected='selected'"; ?>>AB negative
                                                                </option>
                                                                <option value="AB positive" <?php if ($row['bgroup'] == "AB positive")
                                                                    echo "selected='selected'"; ?>>AB positive
                                                                </option>
                                                                <option value="O negative" <?php if ($row['bgroup'] == "O negative")
                                                                    echo "selected='selected'"; ?>>O negative
                                                                </option>
                                                                <option value="O positive" <?php if ($row['bgroup'] == "O positive")
                                                                    echo "selected='selected'"; ?>>O positive
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="input-block mb-3">
                                                            <label class="col-form-label">Marital Status</label>
                                                            <select name="marital_status<?php echo $row_edit['id']; ?>"
                                                                id="marital_status<?php echo $row_edit['id']; ?>"
                                                                class="form-control">
                                                                <option value="1" <?php if ($row['marital_status'] == "1")
                                                                    echo "selected='selected'"; ?>>Married</option>
                                                                <option value="2" <?php if ($row['marital_status'] == "2")
                                                                    echo "selected='selected'"; ?>>Unmarried</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="text" name="id<?php echo $row_edit['id']; ?>"
                                                    id="id<?php echo $row_edit['id']; ?>" value="<?php echo $row['id']; ?>">
                                                <div class="submit-section">
                                                    <button onClick="update_personal_info(<?php echo $row_edit['id']; ?>,
                                                            document.getElementById('fname<?php echo $row_edit['id']; ?>').value,
                                                            document.getElementById('lname<?php echo $row_edit['id']; ?>').value, 
                                                            document.getElementById('dob<?php echo $row_edit['id']; ?>').value,
                                                            document.getElementById('gender<?php echo $row_edit['id']; ?>').value,
                                                            document.getElementById('bgroup<?php echo $row_edit['id']; ?>').value,
                                                            document.getElementById('marital_status<?php echo $row_edit['id']; ?>').value
                                                        );" type="button" name="b1" id="b1"
                                                        class="btn btn-primary submit-btn">Save</button>
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Contact Info -->
                                        <h5 class="modal-title bg-success">CONTACT INFO</h5>
                                        <div class="profile-widget">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Personal Email ID <span
                                                                class="text-danger">*</span></label>
                                                        <input class="form-control"
                                                            value="<?php echo htmlspecialchars($row_edit['office_email']); ?>" type="text"
                                                            name="office_email<?php echo $row_edit['id']; ?>"
                                                            id="office_email<?php echo $row_edit['id']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Official Email ID<span
                                                                class="text-danger">*</span></label>
                                                        <input class="form-control" value="<?php echo htmlspecialchars($row_edit['email']); ?>"
                                                            type="text" name="email<?php echo $row_edit['id']; ?>"
                                                            id="email<?php echo $row_edit['id']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Current Address</label>
                                                        <textarea class="form-control"
                                                            name="current_address<?php echo $row_edit['id']; ?>"
                                                            id="current_address<?php echo $row_edit['id']; ?>"><?php echo htmlspecialchars($row_edit['current_address']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Permanent Address <span
                                                                class="text-danger">*</span></label>
                                                        <textarea class="form-control"
                                                            name="permanent_address<?php echo $row_edit['id']; ?>"
                                                            id="permanent_address<?php echo $row_edit['id']; ?>"><?php echo htmlspecialchars($row_edit['permanent_address']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">House Type</label>
                                                        <select name="house_type<?php echo $row_edit['id']; ?>"
                                                            id="house_type<?php echo $row_edit['id']; ?>" class="form-control">
                                                            <option value="Rented - with Family" <?php if ($row['house_type'] == "Rented - with Family")
                                                                echo "selected='selected'"; ?>>Rented - with Family</option>
                                                            <option value="Rented - Bachelor" <?php if ($row['house_type'] == "Rented - Bachelor")
                                                                echo "selected='selected'"; ?>>Rented - Bachelor</option>
                                                            <option value="Own House" <?php if ($row['house_type'] == "Own House")
                                                                echo "selected='selected'"; ?>>Own House</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Staying at Current Residence Since <span
                                                                class="text-danger">*</span></label>
                                                        <div class="cal-icon1">
                                                            <input class="form-control"
                                                                name="staying_current_residence<?php echo $row_edit['id']; ?>"
                                                                id="staying_current_residence<?php echo $row_edit['id']; ?>"
                                                                type="date"
                                                                value="<?php echo date("Y-m-d", strtotime($row['staying_current_residence'])); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Living in Current City Since <span
                                                                class="text-danger">*</span></label>
                                                        <div class="cal-icon1">
                                                            <input class="form-control"
                                                                name="living_current_city<?php echo $row_edit['id']; ?>"
                                                                id="living_current_city<?php echo $row_edit['id']; ?>"
                                                                value="<?php echo date("Y-m-d", strtotime($row['living_current_city'])); ?>"
                                                                type="date">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Facebook</label>
                                                        <input class="form-control" type="text"
                                                            name="facebook<?php echo $row_edit['id']; ?>"
                                                            id="facebook<?php echo $row_edit['id']; ?>"
                                                            value="<?php echo htmlspecialchars(get_value1("hrm_employee_social", "facebook", "emp_id", $row_edit['id'])); ?>">
                                                    </div>
                                                </div> -->
                                                <!-- <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Twitter</label>
                                                        <input class="form-control" type="text"
                                                            value="<?php echo htmlspecialchars(get_value1("hrm_employee_social", "twitter", "emp_id", $row_edit['id'])); ?>"
                                                            name="twitter<?php echo $row_edit['id']; ?>"
                                                            id="twitter<?php echo $row_edit['id']; ?>">
                                                    </div>
                                                </div> -->
                                                <!-- <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">LinkedIn</label>
                                                        <input class="form-control"
                                                            value="<?php echo htmlspecialchars(get_value1("hrm_employee_social", "linkedin", "emp_id", $row_edit['id'])); ?>"
                                                            type="text" name="linkedin<?php echo $row_edit['id']; ?>"
                                                            id="linkedin<?php echo $row_edit['id']; ?>">
                                                    </div>
                                                </div> -->
                                            </div>
                                            <div class="submit-section">
                                                <button onClick="update_contact_info(<?php echo $row_edit['id']; ?>,
                                                        document.getElementById('office_email<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('email<?php echo $row_edit['id']; ?>').value, 
                                                        document.getElementById('current_address<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('permanent_address<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('house_type<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('staying_current_residence<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('living_current_city<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('facebook<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('twitter<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('linkedin<?php echo $row_edit['id']; ?>').value
                                                    );" class="btn btn-primary submit-btn">Save</button>
                                            </div>
                                        </div>

                                        <!-- Work Info -->
                                        <h5 class="modal-title bg-success">Work Info</h5>
                                        <div class="profile-widget">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Date of Joining <span
                                                                class="text-danger">*</span></label>
                                                        <div class="cal-icon1">
                                                            <input value="<?php echo date("Y-m-d", strtotime($row['doj'])); ?>"
                                                                class="form-control" name="doj<?php echo $row_edit['id']; ?>"
                                                                id="doj<?php echo $row_edit['id']; ?>" type="date">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Probation Period</label>
                                                        <select name="probation_period<?php echo $row_edit['id']; ?>"
                                                            id="probation_period<?php echo $row_edit['id']; ?>"
                                                            class="form-control">
                                                            <option value="1" <?php if ($row['probation_period'] == "1")
                                                                echo "selected='selected'"; ?>>1 Month</option>
                                                            <option value="2" <?php if ($row['probation_period'] == "2")
                                                                echo "selected='selected'"; ?>>2 Months</option>
                                                            <option value="3" <?php if ($row['probation_period'] == "3")
                                                                echo "selected='selected'"; ?>>3 Months</option>
                                                            <option value="4" <?php if ($row['probation_period'] == "4")
                                                                echo "selected='selected'"; ?>>4 Months</option>
                                                            <option value="5" <?php if ($row['probation_period'] == "5")
                                                                echo "selected='selected'"; ?>>5 Months</option>
                                                            <option value="6" <?php if ($row['probation_period'] == "6")
                                                                echo "selected='selected'"; ?>>6 Months</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Employee Type</label>
                                                        <select name="employee_type<?php echo $row_edit['id']; ?>"
                                                            id="employee_type<?php echo $row_edit['id']; ?>"
                                                            class="form-control">
                                                            <option value="Full Time" <?php if ($row['employee_type'] == "Full Time")
                                                                echo "selected='selected'"; ?>>Full Time</option>
                                                            <option value="Part Time" <?php if ($row['employee_type'] == "Part Time")
                                                                echo "selected='selected'"; ?>>Part Time</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Work Location <span
                                                                class="text-danger">*</span></label>
                                                        <select name="work_location<?php echo $row_edit['id']; ?>"
                                                            id="work_location<?php echo $row_edit['id']; ?>"
                                                            class="form-control">
                                                            <option value="Registered Office" <?php if ($row['work_location'] == "Registered Office")
                                                                echo "selected='selected'"; ?>>Registered Office</option>
                                                            <option value="Corporate Office" <?php if ($row['work_location'] == "Corporate Office")
                                                                echo "selected='selected'"; ?>>Corporate Office</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Work Experience</label>
                                                        <select name="experience<?php echo $row_edit['id']; ?>"
                                                            id="experience<?php echo $row_edit['id']; ?>" class="form-control">
                                                            <option value="Fresher" <?php if ($row['experience'] == "Fresher")
                                                                echo "selected='selected'"; ?>>Fresher</option>
                                                            <option value="6 Month" <?php if ($row['experience'] == "6 Month")
                                                                echo "selected='selected'"; ?>>6 Month</option>
                                                            <option value="1 Year" <?php if ($row['experience'] == "1 Year")
                                                                echo "selected='selected'"; ?>>6 Month to 1 Year</option>
                                                            <option value="2 Year" <?php if ($row['experience'] == "2 Year")
                                                                echo "selected='selected'"; ?>>1 Year to 2 Year</option>
                                                            <option value="3 Year" <?php if ($row['experience'] == "3 Year")
                                                                echo "selected='selected'"; ?>>2 Year to 3 Year</option>
                                                            <option value="4 Year" <?php if ($row['experience'] == "4 Year")
                                                                echo "selected='selected'"; ?>>3 Year to 4 Year</option>
                                                            <option value="5 Year" <?php if ($row['experience'] == "5 Year")
                                                                echo "selected='selected'"; ?>>4 Year to 5 Year</option>
                                                            <option value="6 Year" <?php if ($row['experience'] == "6 Year")
                                                                echo "selected='selected'"; ?>>5 Year to 6 Year</option>
                                                            <option value="7 Year" <?php if ($row['experience'] == "7 Year")
                                                                echo "selected='selected'"; ?>>6 Year to 7 Year</option>
                                                            <option value="8 Year" <?php if ($row['experience'] == "8 Year")
                                                                echo "selected='selected'"; ?>>7 Year to 8 Year</option>
                                                            <option value="9 Year" <?php if ($row['experience'] == "9 Year")
                                                                echo "selected='selected'"; ?>>8 Year to 9 Year</option>
                                                            <option value="10 Year" <?php if ($row['experience'] == "10 Year")
                                                                echo "selected='selected'"; ?>>9 Year to 10 Year</option>
                                                            <option value="11 Year" <?php if ($row['experience'] == "11 Year")
                                                                echo "selected='selected'"; ?>>10 Year to 11 Year</option>
                                                            <option value="12 Year" <?php if ($row['experience'] == "12 Year")
                                                                echo "selected='selected'"; ?>>11 Year to 12 Year</option>
                                                            <option value="More than 12 Year" <?php if ($row['experience'] == "More than 12 Year")
                                                                echo "selected='selected'"; ?>>More than 12 Year</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Designation <span
                                                                class="text-danger">*</span></label>
                                                        <select name="designation_id<?php echo $row_edit['id']; ?>"
                                                            id="designation_id<?php echo $row_edit['id']; ?>"
                                                            class="form-control">
                                                            <?php
                                                            $query_designation = "SELECT * FROM hrm_designation;";
                                                            $result_designation = mysqli_query($conn, $query_designation) or die(mysqli_error($conn));
                                                            while ($row_designation = mysqli_fetch_array($result_designation)) { ?>
                                                                <option value="<?php echo $row_designation['id']; ?>" <?php if ($row_designation['id'] == $row['designation_id'])
                                                                       echo "selected='selected'"; ?>>
                                                                    <?php echo htmlspecialchars($row_designation['name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Job Title</label>
                                                        <input class="form-control" type="text"
                                                            value="<?php echo htmlspecialchars($row_edit['job_title']); ?>"
                                                            name="job_title<?php echo $row_edit['id']; ?>"
                                                            id="job_title<?php echo $row_edit['id']; ?>">
                                                    </div>
                                                </div> -->
                                                <div class="col-sm-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Department <span
                                                                class="text-danger">*</span></label>
                                                        <select name="department_id<?php echo $row_edit['id']; ?>"
                                                            id="department_id<?php echo $row_edit['id']; ?>"
                                                            class="form-control">
                                                            <?php
                                                            $query_department = "SELECT * FROM hrm_department;";
                                                            $result_department = mysqli_query($conn, $query_department) or die(mysqli_error($conn));
                                                            while ($row_department = mysqli_fetch_array($result_department)) { ?>
                                                                <option value="<?php echo $row_department['id']; ?>" <?php if ($row_department['id'] == $row['department_id'])
                                                                       echo "selected='selected'"; ?>>
                                                                    <?php echo htmlspecialchars($row_department['name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="submit-section">
                                                <button class="btn btn-primary submit-btn" type="button" onclick="update_work_info(<?php echo $row_edit['id']; ?>,
                                                        document.getElementById('doj<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('probation_period<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('employee_type<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('work_location<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('experience<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('designation_id<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('job_title<?php echo $row_edit['id']; ?>').value,
                                                        document.getElementById('department_id<?php echo $row_edit['id']; ?>').value
                                                    );">Save</button>
                                            </div>
                                        </div>

                                        <!-- Education -->
                                        <div class="card analytics-card w-100">
                                            <div class="card-body">
                                                <div class="statistic-header">
                                                    <h4>Education</h4>
                                                </div>
                                                <div class="table-responsive">
                                                    <div id="display_education_detail_div<?php echo $row['id']; ?>">
                                                        <table class="table custom-table table-nowrap mb-0 table-border"
                                                            id="dynamic_field_education">
                                                            <thead>
                                                                <tr>
                                                                    <th>Qualification Type</th>
                                                                    <th>Course Name</th>
                                                                    <th>Course Type</th>
                                                                    <th>Stream</th>
                                                                    <th>Course Start Date</th>
                                                                    <th>Course End Date</th>
                                                                    <th>College Name</th>
                                                                    <th>University Name</th>
                                                                    <th>Grade</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $query_education = "SELECT * FROM hrm_employee_education WHERE emp_id='{$row_edit['id']}';";
                                                                $result_education = mysqli_query($conn, $query_education) or die(mmysqli_error($conn));
                                                                while ($row_employee_education = mysqli_fetch_array($result_education)) { ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars(get_value("hrm_qualification_type", "name", $row_employee_education['qualification_type'])); ?>
                                                                        </td>
                                                                        <td><?php echo htmlspecialchars($row_employee_education['course_name']); ?>
                                                                        </td>
                                                                        <td><?php echo htmlspecialchars(get_value("hrm_course_type", "name", $row_employee_education['course_type'])); ?>
                                                                        </td>
                                                                        <td><?php echo htmlspecialchars($row_employee_education['stream']); ?></td>
                                                                        <td><?php echo htmlspecialchars($row_employee_education['start_date']); ?>
                                                                        </td>
                                                                        <td><?php echo htmlspecialchars($row_employee_education['end_date']); ?></td>
                                                                        <td><?php echo htmlspecialchars($row_employee_education['college_name']); ?>
                                                                        </td>
                                                                        <td><?php echo htmlspecialchars($row_employee_education['university_name']); ?>
                                                                        </td>
                                                                        <td><?php echo htmlspecialchars($row_employee_education['grade']); ?></td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <br>
                                                <div align="right">
                                                    <button type="button" id="education_button<?php echo $row_edit['id']; ?>"
                                                        name="education_button<?php echo $row_edit['id']; ?>"
                                                        class="btn btn-success">Add Education</button>
                                                </div>
                                                <script>
                                                    $(document).ready(function () {
                                                        $("#education_button<?php echo $row_edit['id']; ?>").click(function () {
                                                            $("#panel<?php echo $row_edit['id']; ?>").slideToggle("slow");
                                                        });
                                                    });
                                                </script>
                                                <style>
                                                    #panel<?php echo $row_edit['id']; ?>,
                                                    #education_button<?php echo $row_edit['id']; ?> {
                                                        padding: 5px;
                                                        background-color: #f2f2f2;
                                                        color: #000000;
                                                    }

                                                    #panel<?php echo $row_edit['id']; ?> {
                                                        padding: 50px;
                                                        display: none;
                                                    }
                                                </style>
                                                <div id="panel<?php echo $row_edit['id']; ?>">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <label>Qualification Type</label><br>
                                                            <select name="qualification_type<?php echo $row_edit['id']; ?>"
                                                                id="qualification_type<?php echo $row_edit['id']; ?>"
                                                                class="form-control">
                                                                <?php
                                                                $query_display_qualification_type = "SELECT * FROM hrm_qualification_type;";
                                                                $result_display_qualification_type = mysqli_query($conn, $query_display_qualification_type) or die(mysqli_error($conn));
                                                                while ($row_display_qualification_type = mysqli_fetch_array($result_display_qualification_type)) { ?>
                                                                    <option
                                                                        value="<?php echo $row_display_qualification_type['id']; ?>">
                                                                        <?php echo htmlspecialchars($row_display_qualification_type['name']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Course Name</label><br>
                                                            <input type="text" name="course_name<?php echo $row_edit['id']; ?>"
                                                                id="course_name<?php echo $row_edit['id']; ?>"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Course Type</label><br>
                                                            <select name="course_type<?php echo $row_edit['id']; ?>"
                                                                id="course_type<?php echo $row_edit['id']; ?>"
                                                                class="form-control">
                                                                <?php
                                                                $query_display_course_type = "SELECT * FROM hrm_course_type;";
                                                                $result_display_course_type = mysqli_query($conn, $query_display_course_type) or die(mysqli_error($conn));
                                                                while ($row_display_course_type = mysqli_fetch_array($result_display_course_type)) { ?>
                                                                    <option value="<?php echo $row_display_course_type['id']; ?>">
                                                                        <?php echo htmlspecialchars($row_display_course_type['name']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Stream</label><br>
                                                            <input type="text" name="stream<?php echo $row_edit['id']; ?>"
                                                                id="stream<?php echo $row_edit['id']; ?>" class="form-control"
                                                                value="">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Course Start Date</label><br>
                                                            <input type="date" name="start_date<?php echo $row_edit['id']; ?>"
                                                                id="start_date<?php echo $row_edit['id']; ?>"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Course End Date</label><br>
                                                            <input type="date" name="end_date<?php echo $row_edit['id']; ?>"
                                                                id="end_date<?php echo $row_edit['id']; ?>" class="form-control"
                                                                value="">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>College Name</label><br>
                                                            <input type="text" name="college_name<?php echo $row_edit['id']; ?>"
                                                                id="college_name<?php echo $row_edit['id']; ?>"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>University Name</label><br>
                                                            <input type="text"
                                                                name="university_name<?php echo $row_edit['id']; ?>"
                                                                id="university_name<?php echo $row_edit['id']; ?>"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Grade</label><br>
                                                            <input type="text" name="grade<?php echo $row_edit['id']; ?>"
                                                                id="grade<?php echo $row_edit['id']; ?>" class="form-control"
                                                                value="">
                                                        </div>
                                                        <div class="submit-section">
                                                            <button class="btn btn-primary submit-btn" type="button"
                                                                onclick="update_education(<?php echo $row_edit['id']; ?>,
                                                                    document.getElementById('qualification_type<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('course_name<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('course_type<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('stream<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('start_date<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('end_date<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('college_name<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('university_name<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('grade<?php echo $row_edit['id']; ?>').value
                                                                ); display_education_detail(<?php echo $row_edit['id']; ?>, display_education_detail_div<?php echo $row['id']; ?>);">Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                        </div>

                                        <!-- Team -->
                                        <div class="card analytics-card w-100">
                                            <div class="card-body">
                                                <div class="statistic-header">
                                                    <h4>Team</h4>
                                                </div>
                                                <div class="table-responsive">
                                                    <div id="display_reporting_manager_div<?php echo $row['id']; ?>">
                                                        <table class="table custom-table table-nowrap mb-0 table-border"
                                                            id="dynamic_field_education">
                                                            <thead>
                                                                <tr>
                                                                    <th>Reporting Manager</th>
                                                                    <th>Type</th>
                                                                    <th>Department</th>
                                                                    <th>Designation</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $query_reporting_manager = "SELECT * FROM hrm_reporting_manager WHERE employee_id='{$row_edit['id']}';";
                                                                $result_reporting_manager = mysqli_query($conn, $query_reporting_manager) or die(mysqli_error($conn));
                                                                while ($row_reporting_manager = mysqli_fetch_array($result_reporting_manager)) { ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars(get_value("hrm_employee", "fname", $row_reporting_manager['reporting_manager_id'])); ?>
                                                                        </td>
                                                                        <td><?php echo htmlspecialchars($row_reporting_manager['reporting_manager_type']); ?>
                                                                        </td>
                                                                        <td><?php
                                                                        $designation_id = get_value("hrm_employee", "designation_id", $row_reporting_manager['reporting_manager_id']);
                                                                        echo htmlspecialchars(get_value("hrm_designation", "name", $designation_id));
                                                                        ?></td>
                                                                        <td><?php
                                                                        $department_id = get_value("hrm_employee", "department_id", $row_reporting_manager['reporting_manager_id']);
                                                                        echo htmlspecialchars(get_value("hrm_department", "name", $department_id));
                                                                        ?></td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <br>
                                                <div align="right">
                                                    <button type="button" id="reporting_button<?php echo $row_edit['id']; ?>"
                                                        name="reporting_button<?php echo $row_edit['id']; ?>"
                                                        class="btn btn-success">Add Reporting Manager</button>
                                                </div>
                                                <script>
                                                    $(document).ready(function () {
                                                        $("#reporting_button<?php echo $row_edit['id']; ?>").click(function () {
                                                            $("#panel_reporting<?php echo $row_edit['id']; ?>").slideToggle("slow");
                                                        });
                                                    });
                                                </script>
                                                <style>
                                                    #panel_reporting<?php echo $row_edit['id']; ?>,
                                                    #reporting_button<?php echo $row_edit['id']; ?> {
                                                        padding: 5px;
                                                        background-color: #f2f2f2;
                                                        color: #000000;
                                                    }

                                                    #panel_reporting<?php echo $row_edit['id']; ?> {
                                                        padding: 50px;
                                                        display: none;
                                                    }
                                                </style>
                                                <div id="panel_reporting<?php echo $row_edit['id']; ?>">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <label>Reporting Manager</label><br>
                                                            <select name="employee_id<?php echo $row_edit['id']; ?>"
                                                                id="employee_id<?php echo $row_edit['id']; ?>"
                                                                class="form-control">
                                                                <?php
                                                                $query_display_employee = "SELECT * FROM hrm_employee;";
                                                                $result_display_employee = mysqli_query($conn, $query_display_employee) or die(mysqli_error($conn));
                                                                while ($row_display_employee = mysqli_fetch_array($result_display_employee)) { ?>
                                                                    <option value="<?php echo $row_display_employee['id']; ?>">
                                                                        <?php echo htmlspecialchars($row_display_employee['fname'] . " " . $row_display_employee['lname']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Type</label><br>
                                                            <select name="reporting_manager_type<?php echo $row_edit['id']; ?>"
                                                                id="reporting_manager_type<?php echo $row_edit['id']; ?>"
                                                                class="form-control">
                                                                <option value="Primary">Primary</option>
                                                                <option value="Secondary">Secondary</option>
                                                            </select>
                                                        </div>
                                                        <div class="submit-section">
                                                            <button class="btn btn-primary submit-btn" type="button"
                                                                onclick="update_reporting_manager(<?php echo $row_edit['id']; ?>,
                                                                    document.getElementById('employee_id<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('reporting_manager_type<?php echo $row_edit['id']; ?>').value
                                                                ); display_reporting_manager(<?php echo $row_edit['id']; ?>, display_reporting_manager_div<?php echo $row['id']; ?>);">Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                        </div>

                                        <!-- Family -->
                                        <div class="card analytics-card w-100">
                                            <div class="card-body">
                                                <div class="statistic-header">
                                                    <h4>Family</h4>
                                                </div>
                                                <div class="table-responsive">
                                                    <div id="display_family_div<?php echo $row['id']; ?>">
                                                        <table class="table custom-table table-nowrap mb-0 table-border"
                                                            id="dynamic_field_family">
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>Relationship</th>
                                                                    <th>Phone</th>
                                                                    <th>Dependent</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $query_family = "SELECT * FROM hrm_employee_family WHERE emp_id='{$row_edit['id']}';";
                                                                $result_family = mysqli_query($conn, $query_family) or die(mysqli_error($conn));
                                                                while ($row_family = mysqli_fetch_array($result_family)) {
                                                                    $relationship_name = '';
                                                                    $relation_id = $row_family['relationship_id'];
                                                                    $rel_query = "SELECT name FROM hrm_family_relationship_member WHERE id = '$relation_id' LIMIT 1";
                                                                    $rel_result = mysqli_query($conn, $rel_query);
                                                                    if (mysqli_num_rows($rel_result) > 0) {
                                                                        $rel_row = mysqli_fetch_assoc($rel_result);
                                                                        $relationship_name = $rel_row['name'];
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($row_family['name']); ?></td>
                                                                        <td><?php echo htmlspecialchars($relationship_name); ?></td>
                                                                        <td><?php echo htmlspecialchars($row_family['phone']); ?></td>
                                                                        <td><?php echo ($row_family['dependent'] == 1) ? "Yes" : "No"; ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <br>
                                                <div align="right">
                                                    <button type="button" id="family_button<?php echo $row_edit['id']; ?>"
                                                        name="family_button<?php echo $row_edit['id']; ?>"
                                                        class="btn btn-success">Add Family Member</button>
                                                </div>
                                                <script>
                                                    $(document).ready(function () {
                                                        $("#family_button<?php echo $row_edit['id']; ?>").click(function () {
                                                            $("#panel_family<?php echo $row_edit['id']; ?>").slideToggle("slow");
                                                        });
                                                    });
                                                </script>
                                                <style>
                                                    #panel_family<?php echo $row_edit['id']; ?>,
                                                    #family_button<?php echo $row_edit['id']; ?> {
                                                        padding: 5px;
                                                        background-color: #f2f2f2;
                                                        color: #000000;
                                                    }

                                                    #panel_family<?php echo $row_edit['id']; ?> {
                                                        padding: 50px;
                                                        display: none;
                                                    }
                                                </style>
                                                <div id="panel_family<?php echo $row_edit['id']; ?>">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <label>Name</label><br>
                                                            <input type="text" name="name<?php echo $row_edit['id']; ?>"
                                                                id="name<?php echo $row_edit['id']; ?>" class="form-control"
                                                                value="">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Relationship</label><br>
                                                            <select name="relationship<?php echo $row_edit['id']; ?>"
                                                                id="relationship<?php echo $row_edit['id']; ?>"
                                                                class="form-control">
                                                                <?php
                                                                $query_display_relationship = "SELECT * FROM hrm_family_relationship_member;";
                                                                $result_display_relationship = mysqli_query($conn, $query_display_relationship) or die(mysqli_error($conn));
                                                                while ($row_display_relationship = mysqli_fetch_array($result_display_relationship)) { ?>
                                                                    <option value="<?php echo $row_display_relationship['id']; ?>">
                                                                        <?php echo htmlspecialchars($row_display_relationship['name']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Phone</label><br>
                                                            <input type="text" name="phone<?php echo $row_edit['id']; ?>"
                                                                id="phone<?php echo $row_edit['id']; ?>" class="form-control"
                                                                value="">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Dependent</label><br>
                                                            <label>
                                                                <input class="ck<?php echo $row_edit['id']; ?>"
                                                                    name="dependent<?php echo $row_edit['id']; ?>"
                                                                    id="dependent<?php echo $row_edit['id']; ?>" type="radio"
                                                                    value="1"> Yes
                                                            </label>
                                                            <label>
                                                                <input class="ck<?php echo $row_edit['id']; ?>"
                                                                    name="dependent<?php echo $row_edit['id']; ?>"
                                                                    id="dependent<?php echo $row_edit['id']; ?>" type="radio"
                                                                    value="0" checked="checked"> No
                                                            </label>
                                                        </div>
                                                        <div class="submit-section">
                                                            <button class="btn btn-primary submit-btn" type="button" onclick="update_family(<?php echo $row_edit['id']; ?>,
                                                                    document.getElementById('name<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('relationship<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('phone<?php echo $row_edit['id']; ?>').value,
                                                                    document.querySelector('input[name=dependent<?php echo $row_edit['id']; ?>]:checked').value
                                                                );">Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                        </div>

                                        <!-- Documents -->
                                        <div class="card analytics-card w-100" style="display:none;">
                                            <div class="card-body">
                                                <div class="statistic-header">
                                                    <h4>Documents</h4>
                                                </div>
                                                <div class="table-responsive">
                                                    <div id="display_document_div<?php echo $row['id']; ?>">
                                                        <table class="table custom-table table-nowrap mb-0 table-border"
                                                            id="dynamic_field_documents">
                                                            <thead>
                                                                <tr>
                                                                    <th>Type</th>
                                                                    <th>ID</th>
                                                                    <th>Uploaded By</th>
                                                                    <th>File</th>
                                                                    <th>Use it as proof for</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $query_document = "SELECT id, emp_id, document_type_id, document_number, file, proof_of, uploaded_by FROM hrm_employee_documents WHERE emp_id='{$row_edit['id']}';";
                                                                $result_document = mysqli_query($conn, $query_document) or die(mysqli_error($conn));
                                                                while ($row_document = mysqli_fetch_array($result_document)) { ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars(get_value("hrm_employee_document_type", "name", $row_document['document_type_id'])); ?>
                                                                        </td>
                                                                        <td><?php echo htmlspecialchars($row_document['document_number']); ?></td>
                                                                        <td><?php echo htmlspecialchars($row_document['uploaded_by']); ?></td>
                                                                        <td><?php echo htmlspecialchars($row_document['file']); ?></td>
                                                                        <td><?php echo htmlspecialchars($row_document['proof_of']); ?></td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <br>
                                                <div align="right">
                                                    <button type="button" id="document_button<?php echo $row_edit['id']; ?>"
                                                        name="document_button<?php echo $row_edit['id']; ?>"
                                                        class="btn btn-success">Add Documents</button>
                                                </div>
                                                <script>
                                                    $(document).ready(function () {
                                                        $("#document_button<?php echo $row_edit['id']; ?>").click(function () {
                                                            $("#panel_document<?php echo $row_edit['id']; ?>").slideToggle("slow");
                                                        });
                                                    });
                                                </script>
                                                <style>
                                                    #panel_document<?php echo $row_edit['id']; ?>,
                                                    #document_button<?php echo $row_edit['id']; ?> {
                                                        padding: 5px;
                                                        background-color: #f2f2f2;
                                                        color: #000000;
                                                    }

                                                    #panel_document<?php echo $row_edit['id']; ?> {
                                                        padding: 50px;
                                                        display: none;
                                                    }
                                                </style>
                                                <div id="panel_document<?php echo $row_edit['id']; ?>">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <label>Type</label><br>
                                                            <select name="document_type<?php echo $row_edit['id']; ?>"
                                                                id="document_type<?php echo $row_edit['id']; ?>"
                                                                class="form-control">
                                                                <?php
                                                                $query_display_employee_document_type = "SELECT * FROM hrm_employee_document_type;";
                                                                $result_employee_document_type = mysqli_query($conn, $query_display_employee_document_type) or die(mysqli_error($conn));
                                                                while ($row_employee_document_type = mysqli_fetch_array($result_employee_document_type)) { ?>
                                                                    <option
                                                                        value="<?php echo $row_employee_document_type['id']; ?>">
                                                                        <?php echo htmlspecialchars($row_employee_document_type['name']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>ID</label><br>
                                                            <input type="text"
                                                                name="document_number<?php echo $row_edit['id']; ?>"
                                                                id="document_number<?php echo $row_edit['id']; ?>"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>File</label><br>
                                                            <input type="file"
                                                                name="file_document<?php echo $row_edit['id']; ?>"
                                                                id="file_document<?php echo $row_edit['id']; ?>"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label>Use it as proof for</label><br>
                                                            <label><input class="ck1" name="proof1[]" type="checkbox"
                                                                    value="1">Photo ID </label>
                                                            <label><input class="ck1" name="proof1[]" type="checkbox"
                                                                    value="2">Date of Birth </label>
                                                            <label><input class="ck1" name="proof1[]" type="checkbox"
                                                                    value="3">Current Address</label>
                                                            <label><input class="ck1" name="proof1[]" type="checkbox"
                                                                    value="4">Permanent Address</label>
                                                        </div>
                                                        <div class="submit-section">
                                                            <button class="btn btn-primary submit-btn" type="button" onclick="add_documents(<?php echo $row_edit['id']; ?>,
                                                                    document.getElementById('document_type<?php echo $row_edit['id']; ?>').value,
                                                                    document.getElementById('document_number<?php echo $row_edit['id']; ?>').value,
                                                                    'file_document<?php echo $row_edit['id']; ?>'
                                                                );">Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <!-- /Edit Employee Modal -->

                <!-- Add Employee Modal -->
                <div id="add_employee" class="modal custom-modal fade" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Employee</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            <h5 class="modal-title bg-success">PERSONAL INFO</h5>
<form name="f1_add" id="f1_add" method="post" action="#">
    <input type="hidden" name="last_insert_id" id="last_insert_id">
    <div class="profile-widget">
        <div class="row">
            <div class="col-sm-6">
                <div class="input-block mb-3">
                    <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                    <input required class="form-control" type="text" name="fname_add" id="fname_add">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="input-block mb-3">
                    <label class="col-form-label">Last Name</label>
                    <input required class="form-control" type="text" name="lname_add" id="lname_add">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="input-block mb-3">
                    <label class="col-form-label">Date Of Birth <span class="text-danger">*</span></label>
                    <div class="cal-icon1">
                        <input class="form-control" name="dob_add" id="dob_add" type="date" max="<?php echo $maxDate; ?>" date-format="YYYY-MM-DD">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="input-block mb-3">
                    <label class="col-form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender_add" id="gender_add" class="form-control">
                        <option value="1">Male</option>
                        <option value="2">Female</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="input-block mb-3">
                    <label class="col-form-label">Blood Group</label>
                    <select name="bgroup_add" id="bgroup_add" class="form-control">
                        <option value="A negative">A negative</option>
                        <option value="A positive">A positive</option>
                        <option value="B negative">B negative</option>
                        <option value="B positive">B positive</option>
                        <option value="AB negative">AB negative</option>
                        <option value="AB positive">AB positive</option>
                        <option value="O negative">O negative</option>
                        <option value="O positive">O positive</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="input-block mb-3">
                    <label class="col-form-label">Marital Status</label>
                    <select name="marital_status_add" id="marital_status_add" class="form-control">
                        <option value="1">Married</option>
                        <option value="2">Unmarried</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="submit-section">
            <button onClick="add_personal_info(
                    document.getElementById('fname_add').value,
                    document.getElementById('lname_add').value, 
                    document.getElementById('dob_add').value,
                    document.getElementById('gender_add').value,
                    document.getElementById('bgroup_add').value,
                    document.getElementById('marital_status_add').value
                );" type="button" name="b1_add" id="b1_add" class="btn btn-primary submit-btn">Save</button>
        </div>
    </div>
</form>

<!-- Contact Info -->
<h5 class="modal-title bg-success">CONTACT INFO</h5>
<div class="profile-widget">
    <div class="row">
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Personal Email ID <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="office_email_add" id="office_email_add">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Official Email ID <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="email_add" id="email_add">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Current Address</label>
                <textarea class="form-control" name="current_address_add" id="current_address_add"></textarea>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Permanent Address <span class="text-danger">*</span></label>
                <textarea class="form-control" name="permanent_address_add" id="permanent_address_add"></textarea>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">House Type</label>
                <select name="house_type_add" id="house_type_add" class="form-control">
                    <option value="Rented - with Family">Rented - with Family</option>
                    <option value="Rented - Bachelor">Rented - Bachelor</option>
                    <option value="Own House">Own House</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Staying at Current Residence Since <span class="text-danger">*</span></label>
                <div class="cal-icon1">
                    <input class="form-control" name="staying_current_residence_add" id="staying_current_residence_add" type="date">
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Living in Current City Since <span class="text-danger">*</span></label>
                <div class="cal-icon1">
                    <input class="form-control" name="living_current_city_add" id="living_current_city_add" type="date">
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Facebook</label>
                <input class="form-control" type="text" name="facebook_add" id="facebook_add">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Twitter</label>
                <input class="form-control" type="text" name="twitter_add" id="twitter_add">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">LinkedIn</label>
                <input class="form-control" type="text" name="linkedin_add" id="linkedin_add">
            </div>
        </div>
    </div>
    <div class="submit-section">
        <button onClick="update_contact_info(
                document.getElementById('last_insert_id').value,
                document.getElementById('office_email_add').value,
                document.getElementById('email_add').value, 
                document.getElementById('current_address_add').value,
                document.getElementById('permanent_address_add').value,
                document.getElementById('house_type_add').value,
                document.getElementById('staying_current_residence_add').value,
                document.getElementById('living_current_city_add').value,
                document.getElementById('facebook_add').value,
                document.getElementById('twitter_add').value,
                document.getElementById('linkedin_add').value
            );" class="btn btn-primary submit-btn">Save</button>
    </div>
</div>

<!-- Work Info -->
<h5 class="modal-title bg-success">Work Info</h5>
<div class="profile-widget">
    <div class="row">
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Date of Joining <span class="text-danger">*</span></label>
                <div class="cal-icon1">
                    <input class="form-control" name="doj_add" id="doj_add" type="date">
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Probation Period</label>
                <select name="probation_period_add" id="probation_period_add" class="form-control">
                    <option value="1">1 Month</option>
                    <option value="2">2 Months</option>
                    <option value="3">3 Months</option>
                    <option value="4">4 Months</option>
                    <option value="5">5 Months</option>
                    <option value="6">6 Months</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Employee Type</label>
                <select name="employee_type_add" id="employee_type_add" class="form-control">
                    <option value="Full Time">Full Time</option>
                    <option value="Part Time">Part Time</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Work Location <span class="text-danger">*</span></label>
                <select name="work_location_add" id="work_location_add" class="form-control">
                    <option value="Registered Office">Registered Office</option>
                    <option value="Corporate Office">Corporate Office</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Work Experience</label>
                <select name="experience_add" id="experience_add" class="form-control">
                    <option value="Fresher">Fresher</option>
                    <option value="6 Month">6 Month</option>
                    <option value="1 Year">6 Month to 1 Year</option>
                    <option value="2 Year">1 Year to 2 Year</option>
                    <option value="3 Year">2 Year to 3 Year</option>
                    <option value="4 Year">3 Year to 4 Year</option>
                    <option value="5 Year">4 Year to 5 Year</option>
                    <option value="6 Year">5 Year to 6 Year</option>
                    <option value="7 Year">6 Year to 7 Year</option>
                    <option value="8 Year">7 Year to 8 Year</option>
                    <option value="9 Year">8 Year to 9 Year</option>
                    <option value="10 Year">9 Year to 10 Year</option>
                    <option value="11 Year">10 Year to 11 Year</option>
                    <option value="12 Year">11 Year to 12 Year</option>
                    <option value="More than 12 Year">More than 12 Year</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Designation <span class="text-danger">*</span></label>
                <select name="designation_id_add" id="designation_id_add" class="form-control">
                    <?php
                    $query_designation = "SELECT * FROM hrm_designation;";
                    $result_designation = mysqli_query($conn, $query_designation) or die(mysqli_error($conn));
                    while ($row_designation = mysqli_fetch_array($result_designation)) { ?>
                        <option value="<?php echo $row_designation['id']; ?>">
                            <?php echo htmlspecialchars($row_designation['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Job Title</label>
                <input class="form-control" type="text" name="job_title_add" id="job_title_add">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-block mb-3">
                <label class="col-form-label">Department <span class="text-danger">*</span></label>
                <select name="department_id_add" id="department_id_add" class="form-control">
                    <?php
                    $query_department = "SELECT * FROM hrm_department;";
                    $result_department = mysqli_query($conn, $query_department) or die(mysqli_error($conn));
                    while ($row_department = mysqli_fetch_array($result_department)) { ?>
                        <option value="<?php echo $row_department['id']; ?>">
                            <?php echo htmlspecialchars($row_department['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="submit-section">
        <button class="btn btn-primary submit-btn" type="button" onclick="update_work_info(
                document.getElementById('last_insert_id').value,
                document.getElementById('doj_add').value,
                document.getElementById('probation_period_add').value,
                document.getElementById('employee_type_add').value,
                document.getElementById('work_location_add').value,
                document.getElementById('experience_add').value,
                document.getElementById('designation_id_add').value,
                document.getElementById('job_title_add').value,
                document.getElementById('department_id_add').value
            );">Save</button>
    </div>
</div>

<!-- Education -->
<div class="card analytics-card w-100">
    <div class="card-body">
        <div class="statistic-header">
            <h4>Education</h4>
        </div>
        <div align="right">
            <button type="button" id="education_button_add" name="education_button_add" class="btn btn-success">Add Education</button>
        </div>
        <script>
            $(document).ready(function () {
                $("#education_button_add").click(function () {
                    $("#panel_add").slideToggle("slow");
                });
            });
        </script>
        <style>
            #panel_add, #education_button_add {
                padding: 5px;
                background-color: #f2f2f2;
                color: #000000;
            }
            #panel_add {
                padding: 50px;
                display: none;
            }
        </style>
        <div id="panel_add">
            <div class="row">
                <div class="col-lg-4">
                    <label>Qualification Type</label><br>
                    <select name="qualification_type_add" id="qualification_type_add" class="form-control">
                        <?php
                        $query_display_qualification_type = "SELECT * FROM hrm_qualification_type;";
                        $result_display_qualification_type = mysqli_query($conn, $query_display_qualification_type) or die(mysqli_error($conn));
                        while ($row_display_qualification_type = mysqli_fetch_array($result_display_qualification_type)) { ?>
                            <option value="<?php echo $row_display_qualification_type['id']; ?>">
                                <?php echo htmlspecialchars($row_display_qualification_type['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>Course Name</label><br>
                    <input type="text" name="course_name_add" id="course_name_add" class="form-control" value="">
                </div>
                <div class="col-lg-4">
                    <label>Course Type</label><br>
                    <select name="course_type_add" id="course_type_add" class="form-control">
                        <?php
                        $query_display_course_type = "SELECT * FROM hrm_course_type;";
                        $result_display_course_type = mysqli_query($conn, $query_display_course_type) or die(mysqli_error($conn));
                        while ($row_display_course_type = mysqli_fetch_array($result_display_course_type)) { ?>
                            <option value="<?php echo $row_display_course_type['id']; ?>">
                                <?php echo htmlspecialchars($row_display_course_type['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>Stream</label><br>
                    <input type="text" name="stream_add" id="stream_add" class="form-control" value="">
                </div>
                <div class="col-lg-4">
                    <label>Course Start Date</label><br>
                    <input type="date" name="start_date_add" id="start_date_add" class="form-control" value="">
                </div>
                <div class="col-lg-4">
                    <label>Course End Date</label><br>
                    <input type="date" name="end_date_add" id="end_date_add" class="form-control" value="">
                </div>
                <div class="col-lg-4">
                    <label>College Name</label><br>
                    <input type="text" name="college_name_add" id="college_name_add" class="form-control" value="">
                </div>
                <div class="col-lg-4">
                    <label>University Name</label><br>
                    <input type="text" name="university_name_add" id="university_name_add" class="form-control" value="">
                </div>
                <div class="col-lg-4">
                    <label>Grade</label><br>
                    <input type="text" name="grade_add" id="grade_add" class="form-control" value="">
                </div>
                <div class="submit-section">
                    <button class="btn btn-primary submit-btn" type="button" onclick="update_education(
                        document.getElementById('last_insert_id').value,
                        document.getElementById('qualification_type_add').value,
                        document.getElementById('course_name_add').value,
                        document.getElementById('course_type_add').value,
                        document.getElementById('stream_add').value,
                        document.getElementById('start_date_add').value,
                        document.getElementById('end_date_add').value,
                        document.getElementById('college_name_add').value,
                        document.getElementById('university_name_add').value,
                        document.getElementById('grade_add').value
                    );">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team -->
<div class="card analytics-card w-100">
    <div class="card-body">
        <div class="statistic-header">
            <h4>Team</h4>
        </div>
        <div align="right">
            <button type="button" id="reporting_button_add" name="reporting_button_add" class="btn btn-success">Add Reporting Manager</button>
        </div>
        <script>
            $(document).ready(function () {
                $("#reporting_button_add").click(function () {
                    $("#panel_reporting_add").slideToggle("slow");
                });
            });
        </script>
        <style>
            #panel_reporting_add, #reporting_button_add {
                padding: 5px;
                background-color: #f2f2f2;
                color: #000000;
            }
            #panel_reporting_add {
                padding: 50px;
                display: none;
            }
        </style>
        <div id="panel_reporting_add">
            <div class="row">
                <div class="col-lg-4">
                    <label>Reporting Manager</label><br>
                    <select name="employee_id_add" id="employee_id_add" class="form-control">
                        <?php
                        $query_display_employee = "SELECT * FROM hrm_employee;";
                        $result_display_employee = mysqli_query($conn, $query_display_employee) or die(mysqli_error($conn));
                        while ($row_display_employee = mysqli_fetch_array($result_display_employee)) { ?>
                            <option value="<?php echo $row_display_employee['id']; ?>">
                                <?php echo htmlspecialchars($row_display_employee['fname'] . " " . $row_display_employee['lname']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>Type</label><br>
                    <select name="reporting_manager_type_add" id="reporting_manager_type_add" class="form-control">
                        <option value="Primary">Primary</option>
                        <option value="Secondary">Secondary</option>
                    </select>
                </div>
                <div class="submit-section">
                    <button class="btn btn-primary submit-btn" type="button" onclick="update_reporting_manager(
                        document.getElementById('last_insert_id').value,
                        document.getElementById('employee_id_add').value,
                        document.getElementById('reporting_manager_type_add').value
                    );">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Family -->
<div class="card analytics-card w-100">
    <div class="card-body">
        <div class="statistic-header">
            <h4>Family</h4>
        </div>
        <div align="right">
            <button type="button" id="family_button_add" name="family_button_add" class="btn btn-success">Add Family Member</button>
        </div>
        <script>
            $(document).ready(function () {
                $("#family_button_add").click(function () {
                    $("#panel_family_add").slideToggle("slow");
                });
            });
        </script>
        <style>
            #panel_family_add, #family_button_add {
                padding: 5px;
                background-color: #f2f2f2;
                color: #000000;
            }
            #panel_family_add {
                padding: 50px;
                display: none;
            }
        </style>
        <div id="panel_family_add">
            <div class="row">
                <div class="col-lg-4">
                    <label>Name</label><br>
                    <input type="text" name="name_add" id="name_add" class="form-control" value="">
                </div>
                <div class="col-lg-4">
                    <label>Relationship</label><br>
                    <select name="relationship_add" id="relationship_add" class="form-control">
                        <?php
                        $query_display_relationship = "SELECT * FROM hrm_family_relationship_member;";
                        $result_display_relationship = mysqli_query($conn, $query_display_relationship) or die(mysqli_error($conn));
                        while ($row_display_relationship = mysqli_fetch_array($result_display_relationship)) { ?>
                            <option value="<?php echo $row_display_relationship['id']; ?>">
                                <?php echo htmlspecialchars($row_display_relationship['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>Phone</label><br>
                    <input type="text" name="phone_add" id="phone_add" class="form-control" value="">
                </div>
                <div class="col-lg-4">
                    <label>Dependent</label><br>
                    <label>
                        <input class="ck_add" name="dependent_add" id="dependent_add" type="radio" value="1"> Yes
                    </label>
                    <label>
                        <input class="ck_add" name="dependent_add" id="dependent_add" type="radio" value="0" checked="checked"> No
                    </label>
                </div>
                <div class="submit-section">
                    <button class="btn btn-primary submit-btn" type="button" onclick="update_family(
                        document.getElementById('last_insert_id').value,
                        document.getElementById('name_add').value,
                        document.getElementById('relationship_add').value,
                        document.getElementById('phone_add').value,
                        document.querySelector('input[name=dependent_add]:checked').value
                    );">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Documents -->
<div class="card analytics-card w-100" style="display:none;">
    <div class="card-body">
        <div class="statistic-header">
            <h4>Documents</h4>
        </div>
        <div align="right">
            <button type="button" id="document_button_add" name="document_button_add" class="btn btn-success">Add Documents</button>
        </div>
        <script>
            $(document).ready(function () {
                $("#document_button_add").click(function () {
                    $("#panel_document_add").slideToggle("slow");
                });
            });
        </script>
        <style>
            #panel_document_add, #document_button_add {
                padding: 5px;
                background-color: #f2f2f2;
                color: #000000;
            }
            #panel_document_add {
                padding: 50px;
                display: none;
            }
        </style>
        <div id="panel_document_add">
            <div class="row">
                <div class="col-lg-4">
                    <label>Type</label><br>
                    <select name="document_type_add" id="document_type_add" class="form-control">
                        <?php
                        $query_display_employee_document_type = "SELECT * FROM hrm_employee_document_type;";
                        $result_employee_document_type = mysqli_query($conn, $query_display_employee_document_type) or die(mysqli_error($conn));
                        while ($row_employee_document_type = mysqli_fetch_array($result_employee_document_type)) { ?>
                            <option value="<?php echo $row_employee_document_type['id']; ?>">
                                <?php echo htmlspecialchars($row_employee_document_type['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>ID</label><br>
                    <input type="text" name="document_number_add" id="document_number_add" class="form-control" value="">
                </div>
                <div class="col-lg-4">
                    <label>File</label><br>
                    <input type="file" name="file_document_add" id="file_document_add" class="form-control">
                </div>
                <div class="col-lg-4">
                    <label>Use it as proof for</label><br>
                    <label><input class="ck1" name="proof1_add[]" type="checkbox" value="1">Photo ID </label>
                    <label><input class="ck1" name="proof1_add[]" type="checkbox" value="2">Date of Birth </label>
                    <label><input class="ck1" name="proof1_add[]" type="checkbox" value="3">Current Address</label>
                    <label><input class="ck1" name="proof1_add[]" type="checkbox" value="4">Permanent Address</label>
                </div>
                <div class="submit-section">
                    <button class="btn btn-primary submit-btn" type="button" onclick="add_documents(
                        document.getElementById('last_insert_id').value,
                        document.getElementById('document_type_add').value,
                        document.getElementById('document_number_add').value,
                        'file_document_add'
                    );">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>
</div>
<!-- /Add Employee Modal -->

</div>
<!-- /Page Content -->

</div>
<!-- /Page Wrapper -->

</div>
<!-- /Main Wrapper -->

<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
</body>
</html>