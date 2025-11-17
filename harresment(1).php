<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';
include 'email/send_harresment_email.php'; // Include the email functions file

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connect();

    // Get the logged-in employee's ID
    $emp_id = $_SESSION['id'];

    // Sanitize and validate input data
    $complainantContact = mysqli_real_escape_string($conn, $_POST['complainantContact']);
    $complainantDepartment = mysqli_real_escape_string($conn, $_POST['complainantDepartment']);
    $incidentDate = mysqli_real_escape_string($conn, $_POST['incidentDate']);
    $incidentLocation = mysqli_real_escape_string($conn, $_POST['incidentLocation']);
    $harasserDetails = mysqli_real_escape_string($conn, $_POST['harasserDetails']);
    $incidentDescription = mysqli_real_escape_string($conn, $_POST['incidentDescription']);
    $witnessDetails = mysqli_real_escape_string($conn, $_POST['witnessDetails']);
    $declaration = isset($_POST['declaration']) ? 1 : 0;

    // Handle file upload
    $evidencePath = '';
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] == 0) {
        $targetDir = "uploads/evidence/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create the directory if it doesn't exist
        }
        $targetFile = $targetDir . basename($_FILES["evidence"]["name"]);

        // Check if the file was successfully uploaded
        if (move_uploaded_file($_FILES["evidence"]["tmp_name"], $targetFile)) {
            $evidencePath = $targetFile;
        } else {
            echo "<script>alert('Error uploading the evidence file.'); window.location.href = window.location.href;</script>";
            exit();
        }
    }

    // Insert data into the database
    $query = "INSERT INTO sexual_harassment_complaints (
                emp_id, 
                complainant_contact, 
                complainant_department, 
                incident_date, 
                incident_location, 
                harasser_details, 
                incident_description, 
                witness_details, 
                evidence_path
              ) VALUES (
                '$emp_id', 
                '$complainantContact', 
                '$complainantDepartment', 
                '$incidentDate', 
                '$incidentLocation', 
                '$harasserDetails', 
                '$incidentDescription', 
                '$witnessDetails', 
                '$evidencePath'
              )";

    if (mysqli_query($conn, $query)) {
        // Send email notification
        $recipients = ['pythondolphin@gmail.com', 'hr@1solutions.biz', 'dolphinpython@outlook.com']; // Primary recipient (To field)
        // $ccRecipients = ['ns792999@gmail.com']; // CC recipients
        $subject = 'New Sexual Harassment Complaint Submitted';
        $message = "
            <h3>New Sexual Harassment Complaint</h3>
            <p><strong>Complainant Contact:</strong> $complainantContact</p>
            <p><strong>Department:</strong> $complainantDepartment</p>
            <p><strong>Incident Date:</strong> $incidentDate</p>
            <p><strong>Incident Location:</strong> $incidentLocation</p>
            <p><strong>Harasser Details:</strong> $harasserDetails</p>
            <p><strong>Description:</strong> $incidentDescription</p>
            <p><strong>Witness Details:</strong> $witnessDetails</p>
        ";

        if (send_harassment_email($recipients, $ccRecipients, $subject, $message)) {
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

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

// Count active and inactive employees
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sexual Harassment</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            <div class="content container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12 col-lg-8 col-md-8">
                            <h3 class="page-title">Sexual Harassment Complaint Form (POSH Act 2013)</h3>
                        </div>
                        <div class="col-sm-12 col-lg-4 col-md-4">
                            <!-- Download Button for POSH PDF -->
                            <div class="mb-3">
                                <a href="uploads/posh/posh.pdf" download="posh.pdf" class="btn btn-success">
                                    <i class="fas fa-download"></i> Download POSH Guidelines
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Form Container -->
                <div class="form-container">
                    <form id="complaintForm" action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <!-- Complainant Details -->
                        <div class="mb-3">
                            <label for="complainantContact" class="form-label">Contact Information</label>
                            <input type="text" class="form-control" id="complainantContact" name="complainantContact" placeholder="Enter your phone number or email" required>
                        </div>
                        <div class="mb-3">
                            <label for="complainantDepartment" class="form-label">Department</label>
                            <input type="text" class="form-control" id="complainantDepartment" name="complainantDepartment" placeholder="Enter your department" required>
                        </div>

                        <!-- Incident Details -->
                        <div class="mb-3">
                            <label for="incidentDate" class="form-label">Date of Incident</label>
                            <input type="date" class="form-control" id="incidentDate" name="incidentDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="incidentLocation" class="form-label">Location of Incident</label>
                            <input type="text" class="form-control" id="incidentLocation" name="incidentLocation" placeholder="Enter the location where the incident occurred" required>
                        </div>
                        <div class="mb-3">
                            <label for="harasserDetails" class="form-label">Details of the Alleged Harasser</label>
                            <input type="text" class="form-control" id="harasserDetails" name="harasserDetails" placeholder="Enter the name and designation of the alleged harasser" required>
                        </div>
                        <div class="mb-3">
                            <label for="incidentDescription" class="form-label">Description of the Incident</label>
                            <textarea class="form-control" id="incidentDescription" name="incidentDescription" rows="5" placeholder="Provide a detailed description of the incident" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="witnessDetails" class="form-label">Witness Details (if any)</label>
                            <textarea class="form-control" id="witnessDetails" name="witnessDetails" rows="3" placeholder="Provide names and contact details of witnesses (if applicable)"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="evidence" class="form-label">Upload Evidence (if any)</label>
                            <input type="file" class="form-control" id="evidence" name="evidence">
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
                        <div class="disclaimer">
                            <p>This complaint will be handled in strict confidentiality as per the guidelines of the POSH Act 2013.</p>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Page Content -->
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- end main wrapper-->

    <?php include 'layouts/customizer.php'; ?>
    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>

    <!-- Custom JS for Validation -->
    <script>
        function validateForm() {
            const complainantContact = document.getElementById('complainantContact').value;
            const complainantDepartment = document.getElementById('complainantDepartment').value;
            const incidentDate = document.getElementById('incidentDate').value;
            const incidentLocation = document.getElementById('incidentLocation').value;
            const harasserDetails = document.getElementById('harasserDetails').value;
            const incidentDescription = document.getElementById('incidentDescription').value;
            const declaration = document.getElementById('declaration').checked;

            if (!complainantContact || !complainantDepartment || !incidentDate || !incidentLocation || !harasserDetails || !incidentDescription || !declaration) {
                alert("Please fill all the required fields and agree to the declaration.");
                return false;
            }

            return true;
        }
    </script>
</body>

</html>