<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';

// Get employee ID from POST, not from URL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $emp_id = intval($_POST['id']);
} else {
    die("Access denied!");
}

// Get session employee ID
$session_emp_id = $_SESSION['id'] ?? null;

// DB Connection
$conn = connect();



// Get user name and other details
$query1 = "SELECT * FROM hrm_employee WHERE id='$session_emp_id';";
$result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));
$row1 = mysqli_fetch_array($result1);
$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row1['image'];





// Fetch employee details
$query = "SELECT * FROM hrm_employee WHERE id = '$emp_id'";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

if (!$row) {
    die("Employee not found!");
}

// Fetch additional details
$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);

$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

$session_designation_id = get_value1("hrm_employee", "designation_id", "id", $session_emp_id);
$session_department_id = get_value1("hrm_employee", "department_id", "id", $session_emp_id);

// Profile image
$profile_image_dir = "upload-image";
$profile_image1 = $profile_image_dir . "/" . $row['image'];

// Employee status counts
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

// Gender handling
$gender = "Other";
if ($row['gender'] == "1") {
    $gender = "Male";
} else if ($row['gender'] == "2") {
    $gender = "Female";
}

// Reporting manager
$reporting_manager = get_reporting_manager($emp_id, $profile_image_dir);
?>


<head>

    <title>Employee Profile1 - <?php echo $row['fname'];

    //echo "aaaaaaaaaaaaaaaa=".$query;
    
    ?></title>
    <style type="text/css">
        .custom-file-upload {
            border: 1px solid #ccc;
            display: inline-block;
            padding: 6px 12px;
            cursor: pointer;
        }

        .edit-icon1 {
            background-color: #ECEDEE;
            border: 1px solid #E2E4E6;
            color: #BCBEBF;
            float: right;
            font-size: 12px;
            line-height: 24px;
            min-height: 26px;
            text-align: center;
            width: 26px;
            border-radius: 24px;
        }

        .non-clickable {
            pointer-events: none;
            user-select: none;

        }
    </style>

    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script language="javascript">
        function update_profile_information(emp_id, fname, lname, dob, gender, current_address,
            permanent_address, mobile1, mobile2, email, office_email, department_id, designation_id) {
            $.ajax({
                type: "GET",
                url: "update_profile_information.php",
                data: "emp_id=" + emp_id + "&fname=" + fname + "&lname=" + lname + "&dob=" + dob +
                    "&gender=" + gender + "&current_address=" + current_address +
                    "&permanent_address=" + permanent_address + "&mobile1=" + mobile1 +
                    "&mobile2=" + mobile2 + "&email=" + email +
                    "&office_email=" + office_email + "&department_id=" + department_id +
                    "&designation_id=" + designation_id,
                success: function (data) {
                    alert(data);
                }
            });


            $('#profile_info').modal('hide');
            location.reload();


            //alert(emp_id+fname+lname+dob+gender+current_address+
            //permanent_address+mobile1+mobile2+email+office_email+department_id+designation_id); 
        }

        function update_personal_information(emp_id, nationality, religion, marital_status, bgroup) {
            $.ajax({
                type: "GET",
                url: "update_personal_information.php",
                data: "emp_id=" + emp_id + "&nationality=" + nationality + "&religion=" + religion + "&marital_status=" + marital_status + "&bgroup=" + bgroup,
                success: function (data) {
                    // alert(data);
                }
            });


            $('#personal_info_modal').modal('hide');
            location.reload();
        }

        // function update_bank_information(emp_id, bank_name, account_number, ifsc, pan) {
        //     $.ajax({
        //         type: "GET",
        //         url: "update_bank_information.php",
        //         data: "emp_id=" + emp_id + "&bank_name=" + bank_name + "&account_number=" + account_number + "&ifsc=" + ifsc + "&pan=" + pan,
        //         success: function(data) {
        //             alert(data);
        //         }
        //     });

        //     $('#bank_info_modal').modal('hide');
        //     location.reload();
        // }
    </script>

</head>

<body>


    <div class="main-wrapper">
        <?php


        include 'layouts/menu.php'; //echo "aaaaaaaaaaaaaaaa8=".$query; 
        ?>


        <!-- Page Wrapper -->
        <div class="page-wrapper">

            <!-- Page Content -->
            <div class="content container-fluid pb-0">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Profile <?php //echo "aaaaaaaaaaaaaaaa=".$query; 
                            ?></h3>
                            <ul class="breadcrumb">


                                <?php if ($session_department_id == 4 or $session_department_id == 6) { ?>


                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">
                                            Dashboard</a></li>

                                <?php } else { ?>
                                    <li class="breadcrumb-item"><a href="employee-dashboard.php">
                                            Dashboard</a></li>
                                <?php } ?>
                                <li class="breadcrumb-item active">Profile</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                <?php

                $query = "select * from hrm_employee where id='$emp_id';";
                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                $x = "";
                $row = mysqli_fetch_array($result);

                ?>
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="profile-view">
                                    <div class="profile-img-wrap">
                                        <div class="profile-img">
                                            <a href="#"><img src="<?php echo $profile_image1; ?>" alt="User Image"></a>
                                        </div>
                                    </div>
                                    <div class="profile-basic">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="profile-info-left">
                                                    <h3 class="user-name m-t-0 mb-0">
                                                        <?php echo $row['fname']; ?>
                                                        <?php echo $user_detail_array['lname']; ?>

                                                    </h3>
                                                    <h6 class="text-muted">

                                                        <?php echo $row['job_title']; ?>

                                                    </h6>
                                                    <small class="text-muted"><?php if ($designation != "")
                                                        echo $designation; ?>,
                                                        <?php if ($department != "")
                                                            echo $department; ?></small>
                                                    <div class="staff-id">Employee ID : <?php echo $row['emp_id']; ?>
                                                    </div>
                                                    <div class="small doj text-muted">Date of Joining :

                                                        <?php //echo $row['doj'];
                                                        
                                                        //$old_date = date('l, F d y h:i:s'); 
                                                        $old_date = date('F j, Y');
                                                        // returns Saturday, January 30 10 02:06:34
                                                        //$old_date_timestamp = strtotime($old_date);
                                                        $new_date = date('F j, Y', strtotime($row['doj']));
                                                        echo $new_date;



                                                        ?>
                                                    </div>
                                                    <div class="staff-msg"><a class="btn btn-custom"
                                                            href="employee-dashboard.php">Dashboard</a></div>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <ul class="personal-info">
                                                    <li>
                                                        <div class="title">Phone:</div>
                                                        <div class="text"><a href="#"><?php echo $row['mobile1']; ?></a>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="title">Email:</div>
                                                        <div class="text"><a href="#">
                                                                <?php echo $row['email']; ?></a></div>
                                                    </li>
                                                    <li>
                                                        <div class="title">Birthday:</div>
                                                        <div class="text">
                                                            <?php //echo $row['doj'];
                                                            
                                                            //$old_date = date('l, F d y h:i:s'); 
                                                            $old_date = date('F j, Y');
                                                            // returns Saturday, January 30 10 02:06:34
                                                            //$old_date_timestamp = strtotime($old_date);
                                                            $new_date = date('F j, Y', strtotime($row['dob']));
                                                            echo $new_date;



                                                            ?>

                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="title">Address:</div>
                                                        <div class="text">
                                                            <?php echo $row['current_address']; ?>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="title">Gender:</div>
                                                        <div class="text"><?php echo $gender; ?></div>
                                                    </li>

                                                    <li class="non-clickable">
                                                        <div class="title">Reports to:</div>





                                                        <!-- Profile Modal -->
                                                        <div id="profile_info" class="modal custom-modal fade"
                                                            role="dialog">
                                                            <div class="modal-dialog modal-dialog-centered modal-lg"
                                                                role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Profile Information</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">

                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="profile-img-wrap edit-img">


                                                                                    <!-- image -->
                                                                                    <?php
                                                                                    ?>

                                                                                    <?php if ($row['image'] != "") {
                                                                                        ?>
                                                                                        <div id="targetLayer">
                                                                                            <img src="upload-image/<?php echo $row['image']; ?>"
                                                                                                height="100" width="100"
                                                                                                class="img-circle elevation-2"
                                                                                                alt="User Image">
                                                                                        </div>
                                                                                    <?php } ?>




                                                                                    <!-- image -->

                                                                                </div>


                                                                                <!-- upload image -->
                                                                                <form name="image_form" id="image_form"
                                                                                    method="post"
                                                                                    enctype="multipart/form-data"
                                                                                    action="profile-image-upload.php">

                                                                                    <input type="hidden"
                                                                                        name="emp_id_for_image"
                                                                                        id="emp_id_for_image"
                                                                                        value="<?php echo $row['id']; ?>">



                                                                                    <label for="img1"
                                                                                        class="custom-file-upload">
                                                                                        Select Image
                                                                                    </label>
                                                                                    <span id="file-selected"></span>
                                                                                    <input type="file" name="img1"
                                                                                        id="img1" value="Image Upload">
                                                                                    <input name="button_image"
                                                                                        id="button_image" type="submit"
                                                                                        value="Upload">


                                                                                </form>
                                                                                <script language="javascript">
                                                                                    $(document).ready(function (e) {



                                                                                        // to display selected file name
                                                                                        $('#img1').bind('change', function () {
                                                                                            var fileName = '';
                                                                                            fileName = $(this).val();
                                                                                            $('#file-selected').html(fileName);
                                                                                        })
                                                                                        // to display selected file name


                                                                                        $("#image_form").on('submit', (function (e) {
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
                                                                                        }));
                                                                                    });
                                                                                </script>
                                                                                <!-- upload image -->



                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="input-block mb-3">
                                                                                            <label
                                                                                                class="col-form-label">First
                                                                                                Name</label>
                                                                                            <input type="text"
                                                                                                class="form-control"
                                                                                                value="<?php echo $row['fname']; ?>"
                                                                                                name="fname" id="fname">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="input-block mb-3">
                                                                                            <label
                                                                                                class="col-form-label">Last
                                                                                                Name</label>
                                                                                            <input type="text"
                                                                                                class="form-control"
                                                                                                value="<?php echo $row['lname']; ?>"
                                                                                                name="lname" id="lname">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="input-block mb-3">
                                                                                            <label
                                                                                                class="col-form-label">Birth
                                                                                                Date</label>

                                                                                            <input class="form-control"
                                                                                                type="date" value="<?php
                                                                                                $newDate = date("Y-m-d", strtotime($row['dob']));
                                                                                                echo $newDate; ?>"
                                                                                                name="dob" id="dob"
                                                                                                date-format="YYYY-MM-DD">

                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="input-block mb-3">
                                                                                            <label
                                                                                                class="col-form-label">Gender</label>
                                                                                            <select name="gender"
                                                                                                id="gender"
                                                                                                class="form-control">
                                                                                                <option value="1" <?php if ($row['gender'] == "1") {
                                                                                                    echo "selected='selected'";
                                                                                                } ?>>Male</option>
                                                                                                <option value="2" <?php if ($row['gender'] == "2") {
                                                                                                    echo "selected='selected'";
                                                                                                } ?>>Female</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="input-block mb-3">
                                                                                    <label
                                                                                        class="col-form-label">Current
                                                                                        Address</label>

                                                                                    <textarea class="form-control"
                                                                                        name="current_address"
                                                                                        id="current_address"><?php echo $row['current_address']; ?></textarea>



                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="input-block mb-3">
                                                                                    <label
                                                                                        class="col-form-label">Permanent
                                                                                        Address</label>

                                                                                    <textarea class="form-control"
                                                                                        name="permanent_address"
                                                                                        id="permanent_address"><?php echo $row['permanent_address']; ?></textarea>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-6">
                                                                                <div class="input-block mb-3">
                                                                                    <label
                                                                                        class="col-form-label">Mobile1</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        value="<?php echo $row['mobile1']; ?>"
                                                                                        name="mobile1" id="mobile1">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-block mb-3">
                                                                                    <label
                                                                                        class="col-form-label">Mobile2</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        value="<?php echo $row['mobile2']; ?>"
                                                                                        name="mobile2" id="mobile2">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-block mb-3">
                                                                                    <label
                                                                                        class="col-form-label">Official
                                                                                        Email</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        value="<?php echo $row['email']; ?>"
                                                                                        name="email" id="email">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-block mb-3">
                                                                                    <label
                                                                                        class="col-form-label">Personal
                                                                                        Email</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        value="<?php echo $row['office_email']; ?>"
                                                                                        name="office_email"
                                                                                        id="office_email">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-block mb-3">
                                                                                    <label
                                                                                        class="col-form-label">Department
                                                                                        <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <select class="select"
                                                                                        name="department_id"
                                                                                        id="department_id">
                                                                                        <?php
                                                                                        $query_department = "select * from hrm_department;";
                                                                                        $result_department = mysqli_query($conn, $query_department) or die(mysqli_error($conn));
                                                                                        $x = "";
                                                                                        while ($row_department = mysqli_fetch_array($result_department)) { ?>
                                                                                            <option
                                                                                                value="<?php echo $row_department['id']; ?>"
                                                                                                <?php if ($row_department['id'] == $row['department_id']) {
                                                                                                    echo "selected='selected'";
                                                                                                } ?>>
                                                                                                <?php echo $row_department['name']; ?>
                                                                                            </option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-block mb-3">
                                                                                    <label
                                                                                        class="col-form-label">Designation
                                                                                        <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <select name="designation_id"
                                                                                        id="designation_id"
                                                                                        class="select">
                                                                                        <?php
                                                                                        $query_designation = "select * from hrm_designation;";
                                                                                        $result_designation = mysqli_query($conn, $query_designation) or die(mysqli_error($conn));
                                                                                        $x = "";
                                                                                        while ($row_designation = mysqli_fetch_array($result_designation)) { ?>
                                                                                            <option
                                                                                                value="<?php echo $row_designation['id']; ?>"
                                                                                                <?php if ($row_designation['id'] == $row['designation_id']) {
                                                                                                    echo "selected='selected'";
                                                                                                } ?>>
                                                                                                <?php echo $row_designation['name']; ?>
                                                                                            </option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>


                                                                        </div>
                                                                        <div class="submit-section">

                                                                            <input type="button" onclick="update_profile_information(
      '<?php echo $row['id']; ?>', document.getElementById('fname').value,  
      document.getElementById('lname').value, 
      document.getElementById('dob').value, document.getElementById('gender').value, 
      document.getElementById('current_address').value, document.getElementById('permanent_address').value, 
      document.getElementById('mobile1').value, document.getElementById('mobile2').value,
      document.getElementById('email').value,document.getElementById('office_email').value,
      document.getElementById('department_id').value, document.getElementById('designation_id').value                
                       
                       )" name="button1" id="button1" class="btn btn-primary submit-btn" value="Save">

                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- /Profile Modal -->


                                                        <?php echo $reporting_manager;


                                                        /*
//print_r($reporting_manager);
foreach($reporting_manager as $key1 => $value1)
{
echo $key1." value is ". $value1 . "<br>";
    foreach($value1 as $key => $value)
    {
        echo "<hr><br>".$key." value is ". $value . "<br>";

        echo $value[$key1][3] . "<br>";


    }
}*/

                                                        /*
print_r($reporting_manager);
foreach($reporting_manager as $key1 => $value1)
{
//echo $key1." value is ". $value1 . "< br>";

        echo $reporting_manager[$key1][1]."<br>";

}*/
                                                        /*
foreach($reporting_manager as $row) {
    foreach($row['reporting_manager_id'] as $k) {
          echo $k['reporting_manager_id'];
         //echo $k['boards']['price'];
    }
}*/

                                                        ?>













                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro-edit"><a data-bs-target="#profile_info" data-bs-toggle="modal"
                                            class="edit-icon" href="#">
                                            <i class="fa-solid fa-pencil"></i></a></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card tab-box">
                    <div class="row user-tabs">
                        <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                            <ul class="nav nav-tabs nav-tabs-bottom">
                                <li class="nav-item"><a href="#emp_profile" data-bs-toggle="tab"
                                        class="nav-link active">Profile</a></li>


                                <li class="nav-item"><a href="#emp_assets" data-bs-toggle="tab"
                                        class="nav-link">Assets</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
                // include 'layouts/session.php';
                include 'layouts/config.php';
                $conn = $con;

                // Get employee ID from POST
                $emp_id = isset($_POST['id']) ? intval($_POST['id']) : null;

                if (!$emp_id) {
                    die("No employee ID provided in POST request.");
                }

                $total_steps = 0;
                $completed_steps = 0;
                $sql = "SELECT COUNT(*) as total, SUM(status) as completed 
        FROM employee_onboarding_steps 
        WHERE employee_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $emp_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_assoc();
                $total_steps = $data['total'];
                $completed_steps = $data['completed'] ?? 0;
                $progress_percentage = $total_steps > 0 ? round(($completed_steps / $total_steps) * 100) : 0;
                ?>

                <?php
                if ($session_emp_id == 10 || $session_emp_id == 14) {
                    echo '<div class="progress mb-3">
        <div class="progress-bar" 
             role="progressbar" 
             style="width: ' . $progress_percentage . '%;" 
             aria-valuenow="' . $progress_percentage . '" 
             aria-valuemin="0" 
             aria-valuemax="100">
            ' . $progress_percentage . '% Completed Onboarding Steps
        </div>
    </div>';
                }
                ?>


                <style>
                    .progress {
                        height: 30px;
                        background-color: #e9ecef;
                        border-radius: 15px;
                        overflow: hidden;
                        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
                        margin-top: 20px;
                    }

                    .progress-bar {
                        height: 100%;
                        background: linear-gradient(45deg, #00cc00, #28a745);
                        font-weight: bold;
                        font-size: 16px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: width 0.6s ease;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                    }
                </style>
                <div class="tab-content">

                    <!-- Profile Info Tab -->
                    <div id="emp_profile" class="pro-overview tab-pane fade show active">
                        <div class="row">
                            <div class="col-md-6 d-flex">
                                <div class="card profile-box flex-fill">
                                    <div class="card-body">
                                        <h3 class="card-title">Personal Informations


                                            <a href="#" class="edit-icon" data-bs-toggle="modal"
                                                data-bs-target="#personal_info_modal">
                                                <i class="fa-solid fa-pencil"></i></a>


                                        </h3>
                                        <ul class="personal-info">

                                            <li>
                                                <div class="title">Nationality</div>
                                                <div class="text"><?php echo $row['nationality']; ?></div>
                                            </li>
                                            <li>
                                                <div class="title">Religion</div>
                                                <div class="text">
                                                    <?php if ($row['religion'] != "")
                                                        echo $row['religion']; ?>

                                                </div>
                                            </li>
                                            <li>
                                                <div class="title">Marital status</div>
                                                <div class="text">

                                                    <?php if ($row['marital_status'] == 1)
                                                        echo "Married";
                                                    else
                                                        echo "Unmarried"; ?>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="title"> Blood Group</div>
                                                <div class="text">
                                                    <?php
                                                    if (isset($row['bgroup']) && !empty($row['bgroup'])) {
                                                        echo $row['bgroup'];
                                                    } else {
                                                        echo "Not Selected";
                                                    }
                                                    ?>



                                                </div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Info Modal -->
                            <div id="personal_info_modal" class="modal custom-modal fade" role="dialog">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Personal Information</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Nationality</label>
                                                        <select name="nationality" id="nationality" class="select">

                                                            <option value="Indian" <?php if ($row['nationality'] == "indian") {
                                                                echo "selected='selected'";
                                                            } ?>>Indian</option>


                                                            <option value="Other" <?php if ($row['nationality'] == "other") {
                                                                echo "selected='selected'";
                                                            } ?>>Other</option>

                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Religion</label>
                                                        <select name="religion" id="religion" class="select">

                                                            <option value="Hindu" <?php if ($row['religion'] == "hindu") {
                                                                echo "selected='selected'";
                                                            } ?>>Hindu</option>


                                                            <option value="Muslim" <?php if ($row['religion'] == "muslim") {
                                                                echo "selected='selected'";
                                                            } ?>>Muslim</option>

                                                            <option value="Sikh" <?php if ($row['religion'] == "sikh") {
                                                                echo "selected='selected'";
                                                            } ?>>Sikh</option>

                                                            <option value="other" <?php if ($row['religion'] == "other") {
                                                                echo "selected='selected'";
                                                            } ?>>Other</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Marital Status</label>
                                                        <select name="marital_status" id="marital_status"
                                                            class="select">

                                                            <option value="1" <?php if ($row['marital_status'] == "1") {
                                                                echo "selected='selected'";
                                                            } ?>>Married</option>


                                                            <option value="2" <?php if ($row['marital_status'] == "2") {
                                                                echo "selected='selected'";
                                                            } ?>>Unmarried</option>

                                                        </select>

                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="input-block mb-3">
                                                        <label class="col-form-label">Blood Group</label>
                                                        <select name="bgroup_add" id="bgroup" class="form-control">
                                                            <option value="Not Select"> Not Select </option>
<?php
    $bloodgroup = array("A negative", "A positive","B negative", "B positive", "AB negative","AB positive","O negative", "O positive");

    foreach($bloodgroup as $bldgroup){
        if($bldgroup == $row['bgroup']){
            echo "<option value='$bldgroup' selected> $bldgroup </option>";
        }else{
            echo "<option value='$bldgroup'> $bldgroup </option>";
        }
    }
?>

                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="submit-section">
                                                <input type="button" onclick="update_personal_information(
                                                '<?php echo $row['id']; ?>', document.getElementById('nationality').value,  
                                                document.getElementById('religion').value, 
                                                document.getElementById('marital_status').value,
                                                document.getElementById('bgroup').value 
                                                 )" name="button2" id="button2" class="btn btn-primary submit-btn"
                                                    value="Save">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Personal Info Modal -->
                            <div class="col-md-6 d-flex">
                                <div class="card profile-box flex-fill">
                                    <div class="card-body">
                                        <h3 class="card-title">Emergency Contact Details

                                            <a href="#" class="edit-icon1" data-bs-toggle="modal"
                                                data-bs-target="#family_info_modal">
                                                <i class="fa-solid fa-add"></i>
                                            </a>

                                        </h3>
                                        <div class="table-responsive">
                                            <table id="family-table" class="table table-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Relationship</th>
                                                        <!--<th>Date of Birth</th>-->
                                                        <th>Phone</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $query_family = "SELECT f.id, f.name, f.relationship_id, f.dependent, f.phone, r.name AS rname
                                         FROM hrm_employee_family f
                                         JOIN hrm_family_relationship_member r ON f.relationship_id = r.id
                                         WHERE f.emp_id='$emp_id'";
                                                    $result_family = mysqli_query($conn, $query_family);
                                                    while ($row_family = mysqli_fetch_assoc($result_family)) { ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($row_family['name']) ?></td>
                                                            <td><?= htmlspecialchars($row_family['rname']) ?></td>

                                                            <td><?= htmlspecialchars($row_family['phone']) ?></td>
                                                            <td class="text-end">
                                                                <a href="javascript:void(0);" class="edit-btn"
                                                                    data-id="<?= $row_family['id'] ?>"
                                                                    data-name="<?= $row_family['name'] ?>"
                                                                    data-relationship="<?= $row_family['relationship_id'] ?>"
                                                                    data-phone="<?= $row_family['phone'] ?>"
                                                                    data-dependent="<?= $row_family['dependent'] ?>">
                                                                    <i class="fa-solid fa-pencil m-r-5"></i>
                                                                </a>
                                                                <a href="javascript:void(0);" class="delete-btn"
                                                                    data-id="<?= $row_family['id'] ?>">
                                                                    <i class="fa-regular fa-trash-can m-r-5"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="family_info_modal" class="modal custom-modal fade" tabindex="-1">

                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Emergency Contact Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
                                        </div>
                                        <form id="familyForm">
                                            <div class="modal-body">
                                                <input type="hidden" id="family_id" name="family_id">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="col-form-label">Name <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="family_name"
                                                            name="family_name" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="col-form-label">Relationship <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" id="relationship_id"
                                                            name="relationship_id" required>
                                                            <?php
                                                            $query_rel = "SELECT * FROM hrm_family_relationship_member";
                                                            $result_rel = mysqli_query($conn, $query_rel);
                                                            while ($rel = mysqli_fetch_assoc($result_rel)) {
                                                                echo "<option value='{$rel['id']}'>{$rel['name']}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <!--<div class="col-md-6">-->
                                                    <!--    <label class="col-form-label">Date of Birth <span class="text-danger">*</span></label>-->
                                                    <!--    <input type="date" class="form-control" id="dob" name="dob" required>-->
                                                    <!--</div>-->
                                                    <div class="col-md-6">
                                                        <label class="col-form-label">Phone</label>
                                                        <input type="text" class="form-control" id="phone" name="phone">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="col-form-label">Dependent</label>
                                                        <div>
                                                            <label><input type="radio" name="dependent" value="1">
                                                                Yes</label>
                                                            <label><input type="radio" name="dependent" value="0">
                                                                No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                            <!-- /Family Info Modal -->
                            <!--  -->

                            <div class="col-md-6 d-flex">
                                <div class="card profile-box flex-fill">
                                    <div class="card-body">
                                        <h3 class="card-title">Education Informations <a href="#" class="edit-icon"
                                                data-bs-toggle="modal" data-bs-target="#education_info"><i
                                                    class="fa-solid fa-pencil"></i></a></h3>
                                        <div class="experience-box">
                                            <ul class="experience-list">


                                                <?php
                                                $query_family = "select hrm_employee_education.id, hrm_employee_education.emp_id,
                       hrm_employee_education.qualification_type, 
                       hrm_employee_education.course_name,  hrm_employee_education.course_type,
                       hrm_employee_education.course_type,  hrm_employee_education.stream,
                       
                       hrm_employee_education.start_date, hrm_employee_education.end_date,
                       hrm_employee_education.college_name, hrm_employee_education.university_name,
                       hrm_employee_education.grade, hrm_employee.fname as name1, 
                       hrm_employee.lname as name2   

                       from hrm_employee_education join hrm_employee

                         on hrm_employee_education.emp_id = hrm_employee.id
        
        where hrm_employee_education.emp_id='$emp_id';";

                                                $result_family = mysqli_query($conn, $query_family) or die(mysqli_error($conn));
                                                $x = 0;

                                                $name1 = "";
                                                $name2 = "";
                                                $qualification_type = "";
                                                $course_name = "";
                                                $stream = "";
                                                $start_date = "";
                                                $end_date = "";
                                                $college_name = "";
                                                $university_name = "";
                                                $grade = "";


                                                while ($row_family = mysqli_fetch_array($result_family)) {
                                                    //$x++;
                                                    //if($x==1) $contact_type="Primary";
                                                    //if($x%2==0) $contact_type="Secondary";
                                                    $name1 = $row_family['name1'];
                                                    $name2 = $row_family['name2'];
                                                    $qualification_type = $row_family['qualification_type'];
                                                    $course_name = $row_family['course_name'];
                                                    $stream = $row_family['stream'];
                                                    $start_date = $row_family['start_date'];
                                                    $end_date = $row_family['end_date'];
                                                    $college_name = $row_family['college_name'];
                                                    $university_name = $row_family['university_name'];
                                                    $grade = $row_family['grade'];


                                                    ?>



                                                    <li>
                                                        <div class="experience-user">
                                                            <div class="before-circle"></div>
                                                        </div>
                                                        <div class="experience-content">
                                                            <div class="timeline-content">
                                                                <a href="#/" class="name">
                                                                    <?php echo $college_name; ?></a>
                                                                <div><?php echo $stream; ?></a></div>
                                                                <span class="time">

                                                                    <?php
                                                                    $old_date = date('F j, Y');

                                                                    //$old_date_timestamp = strtotime($old_date);
                                                                    $new_date1 = date('F j, Y', strtotime($start_date));


                                                                    //$old_date_timestamp = strtotime($old_date);
                                                                    $new_date2 = date('F j, Y', strtotime($end_date));
                                                                    echo $new_date1 . " - " . $new_date2;

                                                                    ?>

                                                                </span>
                                                            </div>
                                                        </div>
                                                    </li>





                                                <?php } ?>







                                            </ul>
                                        </div>
                                        <!-- Add Education Button -->
                                        <button type="button" class="btn btn-primary" onclick="openEducationModal()">Add
                                            Education</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Education Modal -->
                            <!-- <div id="education_info" class="modal custom-modal fade" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"> Education Informations</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="form-scroll">
                                        <div class="card">
                                            <div class="card-body">
                                                <h3 class="card-title">Education Informations <a
                                                        href="javascript:void(0);" class="delete-icon"><i
                                                            class="fa-regular fa-trash-can"></i></a></h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <input type="text" value="Oxford University"
                                                                class="form-control floating">
                                                            <label class="focus-label">Institution</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <input type="text" value="Computer Science"
                                                                class="form-control floating">
                                                            <label class="focus-label">Subject</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <div class="cal-icon">
                                                                <input type="text" value="01/06/2002"
                                                                    class="form-control floating datetimepicker">
                                                            </div>
                                                            <label class="focus-label">Starting Date</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <div class="cal-icon">
                                                                <input type="text" value="31/05/2006"
                                                                    class="form-control floating datetimepicker">
                                                            </div>
                                                            <label class="focus-label">Complete Date</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <input type="text" value="BE Computer Science"
                                                                class="form-control floating">
                                                            <label class="focus-label">Degree</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <input type="text" value="Grade A"
                                                                class="form-control floating">
                                                            <label class="focus-label">Grade</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-body">
                                                <h3 class="card-title">Education Informations <a
                                                        href="javascript:void(0);" class="delete-icon"><i
                                                            class="fa-regular fa-trash-can"></i></a></h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <input type="text" value="Oxford University"
                                                                class="form-control floating">
                                                            <label class="focus-label">Institution</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <input type="text" value="Computer Science"
                                                                class="form-control floating">
                                                            <label class="focus-label">Subject</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <div class="cal-icon">
                                                                <input type="text" value="01/06/2002"
                                                                    class="form-control floating datetimepicker">
                                                            </div>
                                                            <label class="focus-label">Starting Date</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <div class="cal-icon">
                                                                <input type="text" value="31/05/2006"
                                                                    class="form-control floating datetimepicker">
                                                            </div>
                                                            <label class="focus-label">Complete Date</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <input type="text" value="BE Computer Science"
                                                                class="form-control floating">
                                                            <label class="focus-label">Degree</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus focused">
                                                            <input type="text" value="Grade A"
                                                                class="form-control floating">
                                                            <label class="focus-label">Grade</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="add-more">
                                                    <a href="javascript:void(0);"><i
                                                            class="fa-solid fa-plus-circle"></i> Add More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="submit-section">
                                        <button class="btn btn-primary submit-btn">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> -->
                            <!-- Education Modal -->
                            <div id="education_info" class="modal custom-modal fade" role="dialog">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Education Information</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="education_form">
                                                <div class="form-scroll" id="education_list">
                                                    <!-- Education details will be loaded here via AJAX -->
                                                </div>
                                                <!-- New Education Form (empty by default, filled for edit) -->
                                                <div class="form-group">
                                                    <label for="course_name">Course Name</label>
                                                    <input type="text" class="form-control" id="course_name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="college_name">College Name</label>
                                                    <input type="text" class="form-control" id="college_name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="university_name">University Name</label>
                                                    <input type="text" class="form-control" id="university_name"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="start_date">Start Date</label>
                                                    <input type="date" class="form-control" id="start_date" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="end_date">End Date</label>
                                                    <input type="date" class="form-control" id="end_date" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="stream">Stream</label>
                                                    <input type="text" class="form-control" id="stream">
                                                </div>
                                                <div class="form-group">
                                                    <label for="qualification_type">Qualification Type</label>
                                                    <select id="qualification_type" class="form-control">
                                                        <option value="1">Bachelor's</option>
                                                        <option value="2">Master's</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="course_type">Course Type</label>
                                                    <select id="course_type" class="form-control">
                                                        <option value="1">Full-time</option>
                                                        <option value="2">Part-time</option>
                                                        <option value="3">Online</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="grade">Grade</label>
                                                    <input type="text" class="form-control" id="grade">
                                                </div>

                                                <!-- Hidden ID for Edit -->
                                                <input type="hidden" id="education_id">

                                                <div class="submit-section">
                                                    <button type="button" id="saveButton" class="btn btn-success"
                                                        onclick="saveEducation()">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <!-- /Education Modal -->

                            <!-- bank model -->
                            <div class="col-md-6 d-flex">
                                <div class="card profile-box flex-fill">
                                    <div class="card-body">
                                        <h3 class="card-title">Bank Details


                                        </h3>
                                        <div class="experience-box">
                                            <ul class="experience-list">
                                                <?php
                                                $query_bank = "SELECT * FROM `hrm_bank_detail` WHERE `emp_id`='$emp_id'";
                                                $result_bank = mysqli_query($conn, $query_bank) or die(mysqli_error($conn));

                                                while ($row_bank = mysqli_fetch_array($result_bank)) {
                                                    $bnkname = $row_bank['bank_name'];
                                                    $bnknameholder = $row_bank['account_holder_name'];
                                                    $bnkactype = $row_bank['account_type'];
                                                    $bnkacno = $row_bank['account_number'];
                                                    $bnkifsc = $row_bank['ifsc'];
                                                    $bnkbranch = $row_bank['branch'];
                                                    $bnkpan = $row_bank['pan'];
                                                    ?>
                                                    <li>
                                                        <div class="experience-user">
                                                            <div class="before-circle"></div>
                                                        </div>
                                                        <div class="experience-content">
                                                            <div class="d-flex justify-content-end gap-2">
                                                                <a href="#" class="delete-icon delete-bank-btn"
                                                                    data-id="<?php echo $row_bank['id']; ?>">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </a>
                                                                <a href="#" class="edit-icon edit-bank-btn"
                                                                    data-bs-toggle="modal" data-bs-target="#bank_info"
                                                                    data-id="<?php echo $row_bank['id']; ?>">
                                                                    <i class="fa-solid fa-pencil"></i>
                                                                </a>
                                                            </div>
                                                            <div class="timeline-content">
                                                                <a href="#/"
                                                                    class="name"><?php echo $row_bank['bank_name']; ?></a>
                                                                <div>Account Type: <?php echo $row_bank['account_type']; ?>
                                                                </div>
                                                                <span class="time">A/C:
                                                                    <?php echo $row_bank['account_number']; ?></span>
                                                                <div>IFSC: <?php echo $row_bank['ifsc']; ?></div>
                                                                <div>Account holder Name:
                                                                    <?php echo $row_bank['account_holder_name']; ?>
                                                                </div>
                                                                <div>Branch: <?php echo $row_bank['branch']; ?></div>
                                                                <div>PAN: <?php echo $row_bank['pan']; ?></div>

                                                            </div>

                                                        </div>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <!-- Add Bank Details Button -->
                                        <button type="button" class="btn btn-primary" onclick="openBankModal()"><i
                                                class="fa-solid fa-add"></i></button>
                                    </div>
                                </div>
                            </div>


                            <!-- bank model -->





                            <!-- Bank Info Modal -->
                            <div id="bank_info" class="modal custom-modal fade" role="dialog">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Bank Details_SS</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close">X</button>

                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            error_reporting(1);
                                            ?>
                                            <form id="bank_details_form">
                                                <input type="hidden" id="bank_id" name="bank_id"
                                                    value="<?php echo $emp_id; ?>">
                                                <input type="hidden" id="new_id" name="new_id"
                                                    value="<?php echo $emp_id; ?>">
                                                <!-- Hidden Input for ID -->
                                                <div class="form-scroll">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h3 class="card-title">Bank Information</h3>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <input type="text" id="bank_name" name="bank_name"
                                                                        class="form-control"
                                                                        value="<?php echo $bnkname; ?>"
                                                                        placeholder="Bank Name">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" id="account_holder_name"
                                                                        name="account_holder_name" class="form-control"
                                                                        value="<?php echo $bnknameholder; ?>"
                                                                        placeholder="Account Holder name">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" id="account_type"
                                                                        name="account_type" class="form-control"
                                                                        value="<?php echo $bnkactype; ?>"
                                                                        placeholder="Account Type">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" id="account_number"
                                                                        name="account_number"
                                                                        value="<?php echo $bnkacno; ?>"
                                                                        class="form-control"
                                                                        placeholder="Account Number">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" id="ifsc" name="ifsc"
                                                                        class="form-control"
                                                                        value="<?php echo $bnkifsc; ?>"
                                                                        placeholder="IFSC Code">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" id="branch" name="branch"
                                                                        class="form-control"
                                                                        value="<?php echo $bnkbranch; ?>"
                                                                        placeholder="Branch">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" id="pan" name="pan"
                                                                        class="form-control"
                                                                        value="<?php echo $bnkpan; ?>"
                                                                        placeholder="PAN">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="submit-section">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <div class="col-md-6 d-flex" style="display:none;">
                                <div class="card profile-box flex-fill" style="display:none;">
                                    <div class="card-body">
                                        <h3 class="card-title">Experience <a href="#" class="edit-icon"
                                                data-bs-toggle="modal" data-bs-target="#experience_info"><i
                                                    class="fa-solid fa-pencil"></i></a></h3>
                                        <div class="experience-box">
                                            <ul class="experience-list">


                                                <li>
                                                    <div class="experience-user">
                                                        <div class="before-circle"></div>
                                                    </div>
                                                    <div class="experience-content">
                                                        <div class="timeline-content">
                                                            <a href="#/" class="name">Web Designer at Zen
                                                                Corporation</a>
                                                            <span class="time">Jan 2013 - Present (5 years 2
                                                                months)</span>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="experience-user">
                                                        <div class="before-circle"></div>
                                                    </div>
                                                    <div class="experience-content">
                                                        <div class="timeline-content">
                                                            <a href="#/" class="name">Web Designer at Ron-tech</a>
                                                            <span class="time">Jan 2013 - Present (5 years 2
                                                                months)</span>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="experience-user">
                                                        <div class="before-circle"></div>
                                                    </div>
                                                    <div class="experience-content">
                                                        <div class="timeline-content">
                                                            <a href="#/" class="name">Web Designer at Dalt
                                                                Technology</a>
                                                            <span class="time">Jan 2013 - Present (5 years 2
                                                                months)</span>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Profile Info Tab -->
                    <!-- Bank Statutory Tab -->
                    <div class="tab-pane fade" id="bank_statutory">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title"> Basic Salary Information</h3>
                                <form>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Salary basis <span
                                                        class="text-danger">*</span></label>
                                                <select class="select">
                                                    <option>Select salary basis type</option>
                                                    <option>Hourly</option>
                                                    <option>Daily</option>
                                                    <option>Weekly</option>
                                                    <option>Monthly</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Salary amount <small
                                                        class="text-muted">per month</small></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" class="form-control"
                                                        placeholder="Type your salary amount" value="0.00">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Payment type</label>
                                                <select class="select">
                                                    <option>Select payment type</option>
                                                    <option>Bank transfer</option>
                                                    <option>Check</option>
                                                    <option>Cash</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h3 class="card-title"> PF Information</h3>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">PF contribution</label>
                                                <select class="select">
                                                    <option>Select PF contribution</option>
                                                    <option>Yes</option>
                                                    <option>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">PF No. <span
                                                        class="text-danger">*</span></label>
                                                <select class="select">
                                                    <option>Select PF contribution</option>
                                                    <option>Yes</option>
                                                    <option>No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Employee PF rate</label>
                                                <select class="select">
                                                    <option>Select PF contribution</option>
                                                    <option>Yes</option>
                                                    <option>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Additional rate <span
                                                        class="text-danger">*</span></label>
                                                <select class="select">
                                                    <option>Select additional rate</option>
                                                    <option>0%</option>
                                                    <option>1%</option>
                                                    <option>2%</option>
                                                    <option>3%</option>
                                                    <option>4%</option>
                                                    <option>5%</option>
                                                    <option>6%</option>
                                                    <option>7%</option>
                                                    <option>8%</option>
                                                    <option>9%</option>
                                                    <option>10%</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Total rate</label>
                                                <input type="text" class="form-control" placeholder="N/A" value="11%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Employee PF rate</label>
                                                <select class="select">
                                                    <option>Select PF contribution</option>
                                                    <option>Yes</option>
                                                    <option>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Additional rate <span
                                                        class="text-danger">*</span></label>
                                                <select class="select">
                                                    <option>Select additional rate</option>
                                                    <option>0%</option>
                                                    <option>1%</option>
                                                    <option>2%</option>
                                                    <option>3%</option>
                                                    <option>4%</option>
                                                    <option>5%</option>
                                                    <option>6%</option>
                                                    <option>7%</option>
                                                    <option>8%</option>
                                                    <option>9%</option>
                                                    <option>10%</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Total rate</label>
                                                <input type="text" class="form-control" placeholder="N/A" value="11%">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <h3 class="card-title"> ESI Information</h3>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">ESI contribution</label>
                                                <select class="select">
                                                    <option>Select ESI contribution</option>
                                                    <option>Yes</option>
                                                    <option>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">ESI No. <span
                                                        class="text-danger">*</span></label>
                                                <select class="select">
                                                    <option>Select ESI contribution</option>
                                                    <option>Yes</option>
                                                    <option>No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Employee ESI rate</label>
                                                <select class="select">
                                                    <option>Select ESI contribution</option>
                                                    <option>Yes</option>
                                                    <option>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Additional rate <span
                                                        class="text-danger">*</span></label>
                                                <select class="select">
                                                    <option>Select additional rate</option>
                                                    <option>0%</option>
                                                    <option>1%</option>
                                                    <option>2%</option>
                                                    <option>3%</option>
                                                    <option>4%</option>
                                                    <option>5%</option>
                                                    <option>6%</option>
                                                    <option>7%</option>
                                                    <option>8%</option>
                                                    <option>9%</option>
                                                    <option>10%</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Total rate</label>
                                                <input type="text" class="form-control" placeholder="N/A" value="11%">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="submit-section">
                                        <button class="btn btn-primary submit-btn" type="submit">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /Bank Statutory Tab -->

                    <!-- Assets -->
                    <div class="tab-pane fade" id="emp_assets">
                        <?php

                        $sql_assigned = "SELECT aa.id AS assignment_id, a.asset_id, a.asset_name, a.image, 
                 aa.issued_date, aa.return_date
          FROM hrm_asset_assignments aa 
          JOIN hrm_assets a ON aa.asset_id = a.id 
          WHERE aa.assignee_id = '$emp_id'";
                        $result_assigned = $conn->query($sql_assigned);
                        ?>

                        <div class="container padding-top-ams">
                            <h2 class="text-center">Assigned Assets</h2>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Image</th>
                                            <th>Asset ID</th>
                                            <th>Asset Name</th>
                                            <th>Issued Date</th>
                                            <th>Return Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result_assigned->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($row['image'])): ?>
                                                        <img src="<?= $row['image'] ?>" alt="Image" width="50" height="50"
                                                            class="rounded">
                                                    <?php else: ?>
                                                        <span>No Image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $row['asset_id'] ?></td>
                                                <td><?= $row['asset_name'] ?></td>
                                                <td><?= $row['issued_date'] ?></td>
                                                <td><?= $row['return_date'] ?? 'N/A' ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <!-- /Assets -->

                </div>
            </div>
            <!-- /Page Content -->

            <!--  -->
            <!--  -->









            <!-- Bank Info Modal -->

            <?php
            $query_bank = "select * from hrm_bank_detail where emp_id='$emp_id';";
            $result_bank = mysqli_query($conn, $query_bank) or die(mysqli_error($conn));
            //$x="";
            $row_bank = mysqli_fetch_array($result_bank);
            $count_bank = mysqli_num_rows($result_bank);
            ?>
            <div id="bank_info_modal" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Bank information </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Bank Name</label><br>

                                        <input type="text" name="bank_name" id="bank_name" value="<?php if ($count_bank > 0)
                                            echo $row_bank['bank_name']; ?>">

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Account holder</label><br>

                                        <input type="text" name="account_holder_name" id="account_holder_name" value="<?php if ($count_bank > 0)
                                            echo $row_bank['account_holder_name']; ?>">

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Bank A/C Number</label><br>
                                        <input type="text" name="account_number" id="account_number" value="<?php if ($count_bank > 0)
                                            echo $row_bank['account_number']; ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">IFSC Code</label><br>
                                        <input type="text" name="ifsc" id="ifsc" value="<?php if ($count_bank > 0)
                                            echo $row_bank['ifsc']; ?>">

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Pan Number</label><br>
                                        <input type="text" name="pan" id="pan" value="<?php if ($count_bank > 0)
                                            echo $row_bank['pan']; ?>">

                                    </div>
                                </div>



                            </div>
                            <div class="submit-section">
                                <input type="button" onclick="update_bank_information(
        '<?= $emp_id; ?>', 
        document.getElementById('bank_name').value, 
        document.getElementById('account_holder_name').value,  

        document.getElementById('account_number').value, 
        document.getElementById('ifsc').value, 
        document.getElementById('pan').value 
    )" class="btn btn-primary submit-btn" value="Save">


                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Personal Info Modal -->


                <!-- Experience Modal -->
                <div id="experience_info" class="modal custom-modal fade" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Experience Informations</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="form-scroll">
                                        <div class="card">
                                            <div class="card-body">
                                                <h3 class="card-title">Experience Informations <a
                                                        href="javascript:void(0);" class="delete-icon"><i
                                                            class="fa-regular fa-trash-can"></i></a></h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <input type="text" class="form-control floating"
                                                                value="Digital Devlopment Inc">
                                                            <label class="focus-label">Company Name</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <input type="text" class="form-control floating"
                                                                value="United States">
                                                            <label class="focus-label">Location</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <input type="text" class="form-control floating"
                                                                value="Web Developer">
                                                            <label class="focus-label">Job Position</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <div class="cal-icon">
                                                                <input type="text"
                                                                    class="form-control floating datetimepicker"
                                                                    value="01/07/2007">
                                                            </div>
                                                            <label class="focus-label">Period From</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <div class="cal-icon">
                                                                <input type="text"
                                                                    class="form-control floating datetimepicker"
                                                                    value="08/06/2018">
                                                            </div>
                                                            <label class="focus-label">Period To</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-body">
                                                <h3 class="card-title">Experience Informations <a
                                                        href="javascript:void(0);" class="delete-icon"><i
                                                            class="fa-regular fa-trash-can"></i></a></h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <input type="text" class="form-control floating"
                                                                value="Digital Devlopment Inc">
                                                            <label class="focus-label">Company Name</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <input type="text" class="form-control floating"
                                                                value="United States">
                                                            <label class="focus-label">Location</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <input type="text" class="form-control floating"
                                                                value="Web Developer">
                                                            <label class="focus-label">Job Position</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <div class="cal-icon">
                                                                <input type="text"
                                                                    class="form-control floating datetimepicker"
                                                                    value="01/07/2007">
                                                            </div>
                                                            <label class="focus-label">Period From</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-block mb-3 form-focus">
                                                            <div class="cal-icon">
                                                                <input type="text"
                                                                    class="form-control floating datetimepicker"
                                                                    value="08/06/2018">
                                                            </div>
                                                            <label class="focus-label">Period To</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="add-more">
                                                    <a href="javascript:void(0);"><i
                                                            class="fa-solid fa-plus-circle"></i> Add More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="submit-section">
                                        <button class="btn btn-primary submit-btn">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Experience Modal -->

            </div>
            <!-- /Page Wrapper -->


        </div>
        <!-- end main wrapper-->

        <?php include 'layouts/customizer.php'; ?>
        <!-- JAVASCRIPT -->
        <?php include 'layouts/vendor-scripts.php'; ?>

        <script>
            function update_bank_information(emp_id, bank_name, account_number, ifsc, pan, account_holder_name) {
                console.log("Emp ID: ", emp_id); // Debugging
                console.log("Bank Name: ", bank_name); // Debugging

                $.ajax({
                    type: "GET",
                    url: "update_bank_information.php",
                    data: {
                        emp_id: emp_id,
                        bank_name: bank_name,
                        account_number: account_number,
                        account_holder_name: account_holder_name,
                        ifsc: ifsc,
                        pan: pan
                    },
                    success: function (data) {
                        console.log("Response: ", data); // Debugging
                        // alert(data);
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error: ", error); // Debugging
                    }
                });

                $('#bank_info_modal').modal('hide');
                location.reload();
            }
        </script>
        <script>
            $(document).ready(function () {
                loadEducation();
            });
            function loadEducation() {
                $.ajax({
                    url: "fetch_education.php",
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        console.log("Received Data:", data);

                        if (!Array.isArray(data)) {
                            // console.error("Error: Data is not an array", data);
                            alert("Invalid response from the server.");
                            return;
                        }

                        let html = "";
                        for (let i = 0; i < data.length; i++) {
                            let edu = data[i];
                            html += `
                <div class="card mt-2" id="education_${edu.id}">
                    <div class="card-body">
                        <h3 class="card-title">
                            ${edu.course_name} at ${edu.college_name || "N/A"}
                            <a href="javascript:void(0);" onclick="editEducation(${edu.id})" class="edit-icon">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="deleteEducation(${edu.id})" class="delete-icon">
                                <i class="fa-regular fa-trash-can"></i>
                            </a>
                        </h3>
                    </div>
                </div>`;
                        }
                        $("#education_list").html(html);
                    },
                    error: function (xhr, status, error) {
                        // console.error("AJAX Error:", status, error);
                        // console.error("Response Text:", xhr.responseText);
                        alert("Failed to fetch education details. See console for more info.");
                    }
                });
            }

            function openEducationModal() {
                // Reset the form for adding new education
                $('#education_form')[0].reset();
                $('#education_id').val('');
                $('#saveButton').text('Save');
                $('#education_info').modal('show');
            }

            function editEducation(id) {
                $.ajax({
                    url: "fetch_education_details.php",
                    type: "GET",
                    data: { id: id },
                    dataType: "json",
                    success: function (data) {
                        if (data && data.id) {
                            // Populate form with the selected education details
                            $('#course_name').val(data.course_name);
                            $('#college_name').val(data.college_name);
                            $('#university_name').val(data.university_name);
                            $('#start_date').val(data.start_date);
                            $('#end_date').val(data.end_date);
                            $('#stream').val(data.stream);
                            $('#qualification_type').val(data.qualification_type);
                            $('#course_type').val(data.course_type);
                            $('#grade').val(data.grade);
                            $('#education_id').val(data.id);

                            // Change the button text to "Update"
                            $('#saveButton').text('Update');
                            $('#education_info').modal('show');
                        }
                    },
                    error: function (xhr, status, error) {
                        // console.error("AJAX Error:", status, error);
                        // console.error("Response Text:", xhr.responseText);
                        alert("Failed to fetch education details. See console for more info.");
                    }
                });
            }

            function saveEducation() {
                const id = $('#education_id').val();
                const course_name = $('#course_name').val();
                const college_name = $('#college_name').val();
                const university_name = $('#university_name').val();
                const start_date = $('#start_date').val();
                const end_date = $('#end_date').val();
                const stream = $('#stream').val();
                const qualification_type = $('#qualification_type').val();
                const course_type = $('#course_type').val();
                const grade = $('#grade').val();

                // Prepare the data
                const data = {
                    id: id,
                    course_name: course_name,
                    college_name: college_name,
                    university_name: university_name,
                    start_date: start_date,
                    end_date: end_date,
                    stream: stream,
                    qualification_type: qualification_type,
                    course_type: course_type,
                    grade: grade
                };

                $.ajax({
                    url: "save_education.php",
                    type: "POST",
                    data: data,
                    success: function (response) {

                        if (success => 'true') {
                            alert('Education record saved successfully!');
                            $('#education_info').modal('hide');

                            loadEducation();
                            location.reload();
                        } else {
                            alert('Error saving education record!');

                        }
                    },
                    error: function (xhr, status, error) {
                        // console.error("AJAX Error:", status, error);
                        // console.error("Response Text:", xhr.responseText);
                        alert("Failed to save education details. See console for more info.");
                    }
                });
            }

            function deleteEducation(id) {
                if (confirm('Are you sure you want to delete this education record?')) {
                    $.ajax({
                        url: "delete_education.php",
                        type: "POST",
                        data: { id: id },
                        success: function (response) {
                            if (success => 'true') {
                                alert('Education record deleted!');
                                loadEducation();
                                location.reload();
                            } else {
                                alert('Education record not deleted!');
                            }
                        },
                        error: function (xhr, status, error) {
                            // console.error("AJAX Error:", status, error);
                            // console.error("Response Text:", xhr.responseText);
                            alert("Failed to delete education details. See console for more info.");
                        }
                    });
                }
            }
        </script>
        <script>
            // Open Modal for Adding a New Bank
            function openBankModal() {
                $('#bank_details_form')[0].reset();  // Reset all form fields
                $('#bank_id').val('');              // Clear hidden bank_id field
                $('#new_id').val('<?php echo $emp_id; ?>'); // Set emp_id for new entry
                $('#bank_name').val('');            // Explicitly clear all fields
                $('#account_holder_name').val('');
                $('#account_type').val('');
                $('#account_number').val('');
                $('#ifsc').val('');
                $('#branch').val('');
                $('#pan').val('');
                $('#bank_info').modal('show');      // Show the modal
            }

            // Open Modal for Editing a Bank (unchanged, for reference)
            $(document).on('click', '.edit-bank-btn', function () {
                let bankId = $(this).data('id'); // Get bank ID
                $('#bank_details_form')[0].reset(); // Reset form before populating
                $.ajax({
                    url: 'get_bank_details.php',
                    type: 'POST',
                    data: { bank_id: bankId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            // Populate modal fields
                            $('#bank_id').val(response.data.id);
                            $('#bank_name').val(response.data.bank_name);
                            $('#account_type').val(response.data.account_type);
                            $('#account_number').val(response.data.account_number);
                            $('#account_holder_name').val(response.data.account_holder_name);
                            $('#ifsc').val(response.data.ifsc);
                            $('#branch').val(response.data.branch);
                            $('#pan').val(response.data.pan);
                            $('#bank_info').modal('show'); // Show modal
                        } else {
                            alert('Failed to load bank details!');
                        }
                    }
                });
            });

            // Handle Form Submission (Add/Edit) - unchanged, for reference
            $('#bank_details_form').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "save_bank_details.php",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $("#bank_info").modal("hide");
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('AJAX Error: ' + error);
                    }
                });
            });
            // Handle Delete Button Click
            $(document).on('click', '.delete-bank-btn', function () {
                let bankId = $(this).data('id');  // Get bank ID

                // Confirm deletion
                if (confirm('Are you sure you want to delete this bank detail?')) {
                    $.ajax({
                        url: 'delete_bank_details.php',
                        type: 'POST',
                        data: { bank_id: bankId },
                        success: function (response) {
                            if (response.status === 'success') {
                                alert('Bank detail deleted successfully!');
                                location.reload();  // Reload the page to reflect the changes
                            } else {
                                alert('Failed to delete bank detail!');
                            }
                        }
                    });
                }
            });
            $(document).ready(function () {
                // Function to load family data dynamically
                // function loadFamilyData() {
                //     $.ajax({
                //         url: "fetch_family.php", // New file to fetch data
                //         type: "GET",
                //         data: { emp_id: '<?php echo $emp_id; ?>' },
                //         success: function (response) {
                //             $("tbody").html(response);
                //         },
                //         error: function () {
                //             alert("Error loading family data.");
                //         }
                //     });
                // }
                function loadFamilyData() {
                    $.ajax({
                        url: "fetch_family.php",
                        type: "GET",
                        data: { emp_id: '<?php echo $emp_id; ?>' },
                        success: function (response) {
                            $("#family-table tbody").html(response); // Target only the family table
                        },
                        error: function () {
                            alert("Error loading family data.");
                        }
                    });
                }

                // Load data on page load
                loadFamilyData();

                // Open modal for adding new family member
                $(".edit-icon1").click(function () {
                    $("#family_id").val(""); // Clear ID for new entry
                    $("#familyForm")[0].reset(); // Reset form
                    $("#family_info_modal").modal("show");
                });

                // Prefill modal for edit
                $(document).on("click", ".edit-btn", function () {
                    $("#family_id").val($(this).data("id"));
                    $("#family_name").val($(this).data("name"));
                    $("#relationship_id").val($(this).data("relationship"));
                    $("#dob").val($(this).data("dob"));
                    $("#phone").val($(this).data("phone"));
                    $("input[name='dependent'][value='" + $(this).data("dependent") + "']").prop("checked", true);
                    $("#family_info_modal").modal("show");
                });

                // Handle form submission (Create/Update)
                $("#familyForm").submit(function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: "update_family.php",
                        type: "POST",
                        data: $(this).serialize() + "&emp_id=<?php echo $emp_id; ?>",
                        success: function (response) {
                            if (response === "Success") {
                                alert("Family information saved successfully!");
                                $("#family_info_modal").modal("hide");
                                loadFamilyData(); // Refresh table
                            } else {
                                alert("Error: " + response);
                            }
                        },
                        error: function () {
                            alert("Error saving data.");
                        }
                    });
                });

                // Handle delete
                $(document).on("click", ".delete-btn", function () {
                    if (confirm("Are you sure you want to delete this record?")) {
                        $.post("delete_family.php", { id: $(this).data("id") }, function (response) {
                            if (response === "Deleted") {
                                alert("Deleted successfully!");
                                loadFamilyData(); // Refresh table
                            } else {
                                alert("Error: " + response);
                            }
                        });
                    }
                });
            });

        </script>

</body>

</html>