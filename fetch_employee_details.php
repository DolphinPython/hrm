<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php'; ?>

<?php
$conn = connect();

if (isset($_POST['id'])) {
    $emp_id = intval($_POST['id']);
    
    $query = "SELECT ae.*, hd.name AS designation_name, hdept.name AS department_name 
              FROM archived_employees ae
              LEFT JOIN hrm_designation hd ON ae.designation_id = hd.id
              LEFT JOIN hrm_department hdept ON ae.department_id = hdept.id
              WHERE ae.id = $emp_id";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo '
        <div class="row">
            <div class="col-md-4">
                <img src="' . $row['image'] . '" class="img-fluid rounded border p-2" alt="Employee Image">
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th>ID</th><td>' . $row['id'] . '</td></tr>
                    <tr><th>First Name</th><td>' . $row['fname'] . '</td></tr>
                    <tr><th>Last Name</th><td>' . $row['lname'] . '</td></tr>
                    <tr><th>Email</th><td>' . $row['email'] . '</td></tr>
                    <tr><th>Mobile 1</th><td>' . $row['mobile1'] . '</td></tr>
                    <tr><th>Mobile 2</th><td>' . $row['mobile2'] . '</td></tr>
                    <tr><th>Father\'s Name</th><td>' . $row['fathers_name'] . '</td></tr>
                    <tr><th>Date of Birth</th><td>' . $row['dob'] . '</td></tr>
                    <tr><th>Date of Joining</th><td>' . $row['doj'] . '</td></tr>
                    <tr><th>City ID</th><td>' . $row['city_id'] . '</td></tr>
                    <tr><th>Pincode</th><td>' . $row['pincode'] . '</td></tr>
                    <tr><th>Current Address</th><td>' . $row['current_address'] . '</td></tr>
                    <tr><th>Permanent Address</th><td>' . $row['permanent_address'] . '</td></tr>
                    <tr><th>Added Date</th><td>' . $row['added_date'] . '</td></tr>
                    <tr><th>Update Date</th><td>' . $row['update_date'] . '</td></tr>
                    <tr><th>Status</th><td>' . ($row['status'] == 1 ? "Active" : ($row['status'] == 2 ? "Inactive" : "Ex-Employee")) . '</td></tr>
                    <tr><th>Experience</th><td>' . $row['experience'] . ' years</td></tr>
                    <tr><th>Designation</th><td>' . $row['designation_name'] . '</td></tr>
                    <tr><th>Department</th><td>' . $row['department_name'] . '</td></tr>
                    <tr><th>Other Details</th><td>' . $row['other_detail'] . '</td></tr>
                    <tr><th>State ID</th><td>' . $row['state_id'] . '</td></tr>
                    <tr><th>Blood Group</th><td>' . $row['bgroup'] . '</td></tr>
                    <tr><th>Gender</th><td>' . $row['gender'] . '</td></tr>
                    <tr><th>Marital Status</th><td>' . $row['marital_status'] . '</td></tr>
                    <tr><th>Marriage Anniversary</th><td>' . $row['marriage_anniversary'] . '</td></tr>
                    <tr><th>Office Email</th><td>' . $row['office_email'] . '</td></tr>
                    <tr><th>House Type</th><td>' . $row['house_type'] . '</td></tr>
                    <tr><th>Staying Current Residence</th><td>' . $row['staying_current_residence'] . '</td></tr>
                    <tr><th>Living in Current City</th><td>' . $row['living_current_city'] . '</td></tr>
                    <tr><th>Probation Period</th><td>' . $row['probation_period'] . ' months</td></tr>
                    <tr><th>Employee Type</th><td>' . $row['employee_type'] . '</td></tr>
                    <tr><th>Work Location</th><td>' . $row['work_location'] . '</td></tr>
                    <tr><th>Job Title</th><td>' . $row['job_title'] . '</td></tr>
                    <tr><th>Probation Status</th><td>' . $row['probation_status'] . '</td></tr>
                    <tr><th>Religion</th><td>' . $row['religion'] . '</td></tr>
                    <tr><th>Nationality</th><td>' . $row['nationality'] . '</td></tr>
                    <tr><th>Attendance ID</th><td>' . $row['attendance_id'] . '</td></tr>
                    <tr><th>Salary</th><td>' . $row['salary'] . '</td></tr>
                </table>
            </div>
        </div>';
    } else {
        echo '<div class="alert alert-danger">Employee not found!</div>';
    }
}
?>