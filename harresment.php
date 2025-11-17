<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';
include 'email/send_harresment_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connect();
    $emp_id = $_SESSION['id'];

    $emp_query = "SELECT fname, lname FROM hrm_employee WHERE id = '$emp_id'";
    $emp_result = mysqli_query($conn, $emp_query);
    $emp_row = mysqli_fetch_assoc($emp_result);
    $complainantName = $emp_row['fname'] . ' ' . $emp_row['lname'];

    $complainantContact = mysqli_real_escape_string($conn, $_POST['complainantContact']);
    $complainantDepartment = mysqli_real_escape_string($conn, $_POST['complainantDepartment']);
    $complainantDesignation = mysqli_real_escape_string($conn, $_POST['complainantDesignation']);
    $incidentDate = mysqli_real_escape_string($conn, $_POST['incidentDate']);
    $incidentLocation = mysqli_real_escape_string($conn, $_POST['incidentLocation']);
    $allegedHarasserId = !empty($_POST['allegedHarasserId']) ? (int)$_POST['allegedHarasserId'] : NULL;
    $harasserDetails = mysqli_real_escape_string($conn, $_POST['harasserDetails']);
    $incidentDescription = mysqli_real_escape_string($conn, $_POST['incidentDescription']);
    $witnessDetails = mysqli_real_escape_string($conn, $_POST['witnessDetails']);
    $declaration = isset($_POST['declaration']) ? 1 : 0;

    $evidencePath = '';
    $attachment = null;
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] == 0) {
        $targetDir = "Uploads/evidence/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . basename($_FILES["evidence"]["name"]);
        if (move_uploaded_file($_FILES["evidence"]["tmp_name"], $targetFile)) {
            $evidencePath = $targetFile;
            $attachment = [
                'path' => $targetFile,
                'name' => basename($_FILES["evidence"]["name"])
            ];
        } else {
            echo "<script>alert('Error uploading the evidence file.'); window.location.href = window.location.href;</script>";
            exit();
        }
    }

    $query = "INSERT INTO sexual_harassment_complaints (
                emp_id, complainant_name, complainant_contact, complainant_department, 
                complainant_designation, incident_date, incident_location, alleged_harasser_id, 
                harasser_details, incident_description, witness_details, evidence_path
              ) VALUES (
                '$emp_id', '$complainantName', '$complainantContact', '$complainantDepartment', 
                '$complainantDesignation', '$incidentDate', '$incidentLocation', 
                " . ($allegedHarasserId ? "'$allegedHarasserId'" : "NULL") . ", 
                '$harasserDetails', '$incidentDescription', '$witnessDetails', '$evidencePath'
              )";

    if (mysqli_query($conn, $query)) {
        $harasserName = 'Not specified';
        $harasserDepartment = 'Not specified';
        $harasserDesignation = 'Not specified';
        if ($allegedHarasserId) {
            $harasser_query = "SELECT e.fname, e.lname, d.name AS department_name, des.name AS designation_name
                              FROM hrm_employee e
                              LEFT JOIN hrm_department d ON e.department_id = d.id
                              LEFT JOIN hrm_designation des ON e.designation_id = des.id
                              WHERE e.id = '$allegedHarasserId'";
            $harasser_result = mysqli_query($conn, $harasser_query);
            if ($harasser_row = mysqli_fetch_assoc($harasser_result)) {
                $harasserName = $harasser_row['fname'] . ' ' . $harasser_row['lname'];
                $harasserDepartment = $harasser_row['department_name'] ?: 'Unknown';
                $harasserDesignation = $harasser_row['designation_name'] ?: 'Unknown';
            }
        }

        $recipients = ['pythondolphin@gmail.com', 'hr@1solutions.biz', 'dolphinpython@outlook.com'];
        // $recipients = ['pythondolphin@gmail.com'];
        $ccRecipients = [];
        $subject = 'New Sexual Harassment Complaint Submitted';
        $message = "
            <h3>New Sexual Harassment Complaint</h3>
            <p><strong>Complainant Name:</strong> $complainantName</p>
            <p><strong>Complainant Contact:</strong> $complainantContact</p>
            <p><strong>Complainant Department:</strong> $complainantDepartment</p>
            <p><strong>Complainant Designation:</strong> $complainantDesignation</p>
            <p><strong>Incident Date:</strong> $incidentDate</p>
            <p><strong>Incident Location:</strong> $incidentLocation</p>
            <p><strong>Alleged Harasser:</strong> $harasserName (ID: " . ($allegedHarasserId ? $allegedHarasserId : 'N/A') . ")</p>
            <p><strong>Harasser Department:</strong> $harasserDepartment</p>
            <p><strong>Harasser Designation:</strong> $harasserDesignation</p>
            <p><strong>Harasser Additional Details:</strong> $harasserDetails</p>
            <p><strong>Incident Description:</strong> $incidentDescription</p>
            <p><strong>Witness Details:</strong> $witnessDetails</p>
            <p><strong>Evidence File:</strong> " . ($evidencePath ? basename($evidencePath) : 'None') . "</p>
        ";

        if (send_harassment_email($recipients, $ccRecipients, $subject, $message, $attachment)) {
            echo "<script>alert('Complaint submitted successfully. An email notification has been sent.'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Complaint submitted successfully. Failed to send email notification.'); window.location.href = window.location.href;</script>";
        }
    } else {
        echo "<script>alert('Error submitting complaint: " . mysqli_error($conn) . "'); window.location.href = window.location.href;</script>";
    }

    mysqli_close($conn);
    exit();
}

$emp_id = $_SESSION['id'];
$conn = connect();

$employee_query = "SELECT e.fname, e.lname, e.email, d.name AS department_name, des.name AS designation_name
                  FROM hrm_employee e
                  LEFT JOIN hrm_department d ON e.department_id = d.id
                  LEFT JOIN hrm_designation des ON e.designation_id = des.id
                  WHERE e.id = '$emp_id'";
$employee_result = mysqli_query($conn, $employee_query);
$employee_row = mysqli_fetch_assoc($employee_result);

$complainantName = $employee_row['fname'] . ' ' . $employee_row['lname'];
$complainantContact = $employee_row['email'] ?: 'Not available';
$complainantDepartment = $employee_row['department_name'] ?: 'Not available';
$complainantDesignation = $employee_row['designation_name'] ?: 'Not available';

$harasser_query = "SELECT id, fname, lname FROM hrm_employee WHERE status = '1'";
$harasser_result = mysqli_query($conn, $harasser_query);

if (mysqli_num_rows($harasser_result) == 0) {
    echo "<script>alert('No employees available. Please contact HR.'); window.location.href = window.location.href;</script>";
    exit();
}
mysqli_data_seek($harasser_result, 0);

// $user_detail_array = get_user_detail($emp_id);
// $user_roll_array = get_user_roll($emp_id);
// $profile_image = "";
// $active_employee = count_where("hrm_employee", "status", "1");
// $inactive_employee = count_where("hrm_employee", "status", "0");

// $profile_image_dir = "upload-image";
// $profile_image = $profile_image_dir . "/" . $employee_row['image'];
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
    <title>Sexual Harassment</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .main-wrapper {
            background: #ffffff !important;
        }
        .section-divider {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .section-divider h4 {
            margin-bottom: 20px;
            color: #333;
        }
        .resizable-field {
            resize: both;
            overflow: auto;
            min-width: 150px;
            max-width: 100%;
            min-height: 38px;
            max-height: 200px;
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .resizable-field:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
            outline: none;
        }
        .resizable-textarea {
            resize: both;
            overflow: auto;
            min-width: 200px;
            max-width: 100%;
            min-height: 100px;
            max-height: 400px;
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .resizable-textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
            outline: none;
        }
        .resizable-file-container {
            resize: both;
            overflow: auto;
            min-width: 150px;
            max-width: 100%;
            min-height: 38px;
            max-height: 100px;
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            display: inline-block;
        }
        .custom-file-input {
            width: 100%;
            cursor: pointer;
        }
        .custom-file-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12 col-lg-8 col-md-8">
                            <h3 class="page-title">Sexual Harassment Complaint Form (POSH Act 2013)</h3>
                        </div>
                        <div class="col-sm-12 col-lg-4 col-md-4">
                            <div class="mb-3">
                                <a href="Uploads/posh/posh.pdf" download="posh.pdf" class="btn btn-success">
                                    <i class="fas fa-download"></i> Download POSH Guidelines
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-container">
                    <form id="complaintForm" action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <!-- Section 1: Your Information -->
                        <div class="section-divider">
                            <h4>Your Information</h4>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="complainantContact" class="form-label">Contact Information</label>
                                    <input type="text" class="form-control" id="complainantContact" name="complainantContact" value="<?php echo htmlspecialchars($complainantContact); ?>" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="complainantDepartment" class="form-label">Department</label>
                                    <input type="text" class="form-control" id="complainantDepartment" name="complainantDepartment" value="<?php echo htmlspecialchars($complainantDepartment); ?>" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="complainantDesignation" class="form-label">Designation</label>
                                    <input type="text" class="form-control" id="complainantDesignation" name="complainantDesignation" value="<?php echo htmlspecialchars($complainantDesignation); ?>" readonly>
                                </div>
                                <div class="col-md-3 mb-3"></div>
                            </div>
                        </div>

                        <!-- Section 2: Complaint Against -->
                        <div class="section-divider">
                            <h4>Complaint Against</h4>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="allegedHarasserId" class="form-label">Select Alleged Harasser</label>
                                    <select class="form-control" id="allegedHarasserId" name="allegedHarasserId" onchange="fetchEmployeeDetails()">
                                        <option value="">Select an employee</option>
                                        <?php while ($employee = mysqli_fetch_array($harasser_result)) { ?>
                                            <option value="<?php echo $employee['id']; ?>">
                                                <?php echo htmlspecialchars($employee['fname'] . ' ' . $employee['lname']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="harasserDepartment" class="form-label">Harasser Department</label>
                                    <input type="text" class="form-control" id="harasserDepartment" name="harasserDepartment" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="harasserDesignation" class="form-label">Harasser Designation</label>
                                    <input type="text" class="form-control" id="harasserDesignation" name="harasserDesignation" readonly>
                                </div>
                                <div class="col-md-3 mb-3"></div>
                            </div>
                        </div>

                        <!-- Section 3: Harassment Details -->
                        <div class="section-divider">
                            <h4>Harassment Details</h4>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="incidentDate" class="form-label">Date of Incident</label>
                                    <input type="date" class="form-control" id="incidentDate" name="incidentDate" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="incidentLocation" class="form-label">Location of Incident</label>
                                    <textarea class="resizable-field" id="incidentLocation" name="incidentLocation" placeholder="Enter the location where the incident occurred" required rows="1"></textarea>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="harasserDetails" class="form-label">Additional Harasser Details (Optional)</label>
                                    <textarea class="resizable-field" id="harasserDetails" name="harasserDetails" placeholder="Enter additional details about the alleged harasser" rows="1"></textarea>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="evidence" class="form-label">Upload Evidence (if any)</label>
                                    <div class="">
                                        <input type="file" class="custom-file-input" id="evidence" name="evidence">
                                        <!-- <label class="custom-file-label" for="evidence">No file chosen</label> -->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="incidentDescription" class="form-label">Description of the Incident</label>
                                    <textarea class="resizable-textarea" id="incidentDescription" name="incidentDescription" placeholder="Provide a detailed description of the incident" required></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="witnessDetails" class="form-label">Witness Details (if any)</label>
                                    <textarea class="resizable-textarea" id="witnessDetails" name="witnessDetails" placeholder="Provide names and contact details of witnesses (if applicable)"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Declaration -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="declaration" name="declaration" required>
                            <label class="form-check-label" for="declaration">
                                I declare that the information provided above is true and accurate to the best of my knowledge. I understand that false complaints may lead to disciplinary action.
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-submit">Submit Complaint</button>

                        <!-- Disclaimer -->
                        <div class="disclaimer mt-3">
                            <p>This complaint will be handled in strict confidentiality as per the guidelines of the POSH Act 2013.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>

    <script>
        function validateForm() {
            const incidentDate = document.getElementById('incidentDate').value;
            const incidentLocation = document.getElementById('incidentLocation').value;
            const incidentDescription = document.getElementById('incidentDescription').value;
            const declaration = document.getElementById('declaration').checked;

            if (!incidentDate || !incidentLocation || !incidentDescription || !declaration) {
                alert("Please fill all the required fields and agree to the declaration.");
                return false;
            }
            return true;
        }

        function fetchEmployeeDetails() {
            const employeeId = document.getElementById('allegedHarasserId').value;
            const harasserDepartmentField = document.getElementById('harasserDepartment');
            const harasserDesignationField = document.getElementById('harasserDesignation');

            if (!employeeId) {
                harasserDepartmentField.value = '';
                harasserDesignationField.value = '';
                console.log('No employee selected, clearing fields');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'fetch_employeeposh_details.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log('AJAX Response:', response);
                            harasserDepartmentField.value = response.department || 'Not available';
                            harasserDesignationField.value = response.designation || 'Not available';
                        } catch (e) {
                            console.error('Error parsing JSON:', e, xhr.responseText);
                            alert('Error fetching employee details. Please try again.');
                        }
                    } else {
                        console.error('AJAX Error:', xhr.status, xhr.statusText);
                        alert('Failed to fetch employee details. Server error.');
                    }
                }
            };
            xhr.onerror = function () {
                console.error('AJAX Request failed');
                alert('Network error while fetching employee details.');
            };
            xhr.send('employee_id=' + encodeURIComponent(employeeId));
        }

        // Update file input label
        document.getElementById('evidence').addEventListener('change', function () {
            const fileName = this.files.length > 0 ? this.files[0].name : 'No file chosen';
            document.querySelector('.custom-file-label').textContent = fileName;
        });
    </script>
</body>

</html>