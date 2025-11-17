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
// Fetch counts for cards
$open_count_query = "SELECT COUNT(*) AS open_count FROM tickets WHERE Status = 'Open'";
$in_progress_count_query = "SELECT COUNT(*) AS in_progress_count FROM tickets WHERE Status = 'In Progress'";
$resolved_count_query = "SELECT COUNT(*) AS resolved_count FROM tickets WHERE Status = 'Resolved' AND MONTH(CreatedAt) = MONTH(CURRENT_DATE())";
$closed_count_query = "SELECT COUNT(*) AS closed_count FROM tickets WHERE Status = 'Closed' AND MONTH(CreatedAt) = MONTH(CURRENT_DATE())";

$open_count_result = mysqli_query($conn, $open_count_query);
$in_progress_count_result = mysqli_query($conn, $in_progress_count_query);
$resolved_count_result = mysqli_query($conn, $resolved_count_query);
$closed_count_result = mysqli_query($conn, $closed_count_query);

$open_count = mysqli_fetch_assoc($open_count_result)['open_count'];
$in_progress_count = mysqli_fetch_assoc($in_progress_count_result)['in_progress_count'];
$resolved_count = mysqli_fetch_assoc($resolved_count_result)['resolved_count'];
$closed_count = mysqli_fetch_assoc($closed_count_result)['closed_count'];

// echo $row['department_id'];
if ($row['department_id'] != 4 and $row['department_id'] != 6) {
    header("Location:employee-dashboard.php");
}
?>

<head>

    <title>Dashboard - HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>
    <style>
        .green-bullet {
            list-style-type: none;
            /* Remove default bullet */
            position: relative;
            padding-left: 20px;
            border: none;
            /* Space for custom bullet */
        }

        .green-bullet::before {
            content: '\2022';
            /* Unicode character for bullet */
            color: green;
            /* Bullet color */
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            /* Vertically center the bullet */
        }

        .red-bullet {
            list-style-type: none;
            /* Remove default bullet */
            position: relative;
            padding-left: 20px;
            border: none;
            /* Space for custom bullet */
        }

        .red-bullet::before {
            content: '\2022';
            /* Unicode character for bullet */
            color: red;
            /* Bullet color */
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            /* Vertically center the bullet */
        }
    </style>
</head>

<?php include 'layouts/body.php'; ?>
<!-- Main Wrapper -->
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <!-- Page Wrapper -->
    <div class="page-wrapper">

        <!-- Page Content -->
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Welcome <?php echo $user_detail_array['fname']; ?>!
                            <span class="user-img">
                                <img src="<?php echo $profile_image; ?>" style="height:50px !important; width:50px !important; " alt="User Image" class="avatar">
                            </span>

                        </h3>


                        <ul class="breadcrumb">
                            <li class="breadcrumb-item active">Dashboard - User Role -
                                <?php echo $user_roll_array['permission_name']; ?>

                            </li>
                            <li>&nbsp;</li>
                            <li> | Designation -
                                <?php if ($designation != "") echo $designation;  ?></li>
                            <li>&nbsp;</li>
                            <li> | Department - <?php if ($department != "") echo $department;  ?> </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card dash-widget">
                        <div class="card-body">
                            <span class="dash-widget-icon text-purple"><i class="fa-solid  fa-check"></i></span>
                            <div class="dash-widget-info">
                                <h3><?php echo leave_status_count(0); ?></h3>
                                <span>New Leave</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card dash-widget">
                        <div class="card-body">
                            <span class="dash-widget-icon text-info"><i class="fa-solid fa-check"></i></span>
                            <div class="dash-widget-info">
                                <h3><?php echo leave_status_count(1); ?></h3>
                                <span>Pending Leave</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card dash-widget">
                        <div class="card-body">
                            <span class="dash-widget-icon text-success"><i class="fa-solid fa-check"></i></span>
                            <div class="dash-widget-info">
                                <h3><?php echo leave_status_count(2); ?></h3>
                                <span>Approved Leave</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card dash-widget">
                        <div class="card-body">
                            <span class="dash-widget-icon text-danger"><i class="fa-solid fa-check"></i></span>
                            <div class="dash-widget-info">
                                <h3><?php echo leave_status_count(3); ?></h3>
                                <span>Declined Leave</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cards Section -->
            <!-- Heading for the card section -->
            <h2 class="section-heading">Ticket Status</h2>

            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Open Tickets</p>
                            <h5 class="count-number"><?php echo $open_count; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">In Progress</p>
                            <h5 class="count-number"><?php echo $in_progress_count; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Resolved</p>
                            <h5 class="count-number"><?php echo $resolved_count; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Closed</p>
                            <h5 class="count-number"><?php echo $closed_count; ?></h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CSS to increase the size of the count numbers -->
            <style>
                .count-number {
                    font-size: 2.5rem;
                    /* Adjust the size as needed */
                    font-weight: bold;
                    margin: 0;
                }

                .section-heading {
                    text-align: center;
                    margin-bottom: 20px;
                    font-size: 2rem;
                    /* Adjust the size as needed */
                    font-weight: bold;
                }
            </style>
            <?php
            // Query 1: Employees who have clocked in
            $query1 = "
    SELECT CONCAT(fname, ' ', lname) AS name 
    FROM hrm_employee he 
    JOIN newuser_attendance nua 
    ON he.id = nua.user_id 
    WHERE DATE(nua.clock_in_time) = CURDATE();
";
            $result1 = $conn->query($query1);
            $clockedInCount = $result1->num_rows;
            // Query 2: Employees who have NOT clocked in
            $query2 = "
    SELECT CONCAT(fname, ' ', lname) AS name 
    FROM hrm_employee he 
    LEFT JOIN newuser_attendance nua 
    ON he.id = nua.user_id 
    AND DATE(nua.clock_in_time) = CURDATE() 
    WHERE nua.user_id IS NULL;
";
            $result2 = $conn->query($query2);
            $notClockedInCount = $result2->num_rows;
            ?>


            <div class="row">
                <div class="col-md-12">








                    <div class="row">
                        <!-- Card 1: Employees who have clocked in -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    Employees Who Clocked In Today
                                </div>
                                <div class="card-body">
                                    <?php if ($result1->num_rows > 0): ?>
                                        <ul class="list-group " style="height: 270px ; overflow-y: auto;">
                                            <?php while ($row = $result1->fetch_assoc()): ?>
                                                <li class="list-group-item green-bullet"><?php echo $row['name']; ?></li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>No employees clocked in today.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Employees who have NOT clocked in -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    Employees Not Clocked In Today
                                </div>
                                <div class="card-body">
                                    <?php if ($result2->num_rows > 0): ?>
                                        <ul class="list-group" style="height: 270px ; overflow-y: auto;">
                                            <?php while ($row = $result2->fetch_assoc()): ?>
                                                <li class="list-group-item red-bullet"><?php echo $row['name']; ?></li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>All employees clocked in today.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block">Today Present</span>
                                        </div>
                                        <div>
                                            <!-- <span class="text-success">+0%</span> -->
                                        </div>
                                    </div>
                                    <h3 class="mb-3"><?= $clockedInCount; ?></h3>
                                    <div class="progress mb-2" style="height: 5px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mb-0">Overall Employees <?php echo total_employee(); ?></p>
                                </div>
                            </div>


                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block">Today Absent</span>
                                        </div>
                                        <div>
                                            <!-- <span class="text-success">+0%</span> -->
                                        </div>
                                    </div>
                                    <h3 class="mb-3"><?= $notClockedInCount; ?></h3>
                                    <div class="progress mb-2" style="height: 5px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mb-0">Overall Employees <?php echo total_employee(); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-group m-b-30">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block">Active Employee</span>
                                            </div>
                                            <div>
                                                <span class="text-success">+

                                                    <?php echo  round(($active_employee * 100) / total_employee(), 0); ?> %

                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="mb-3">
                                            <?php if ($active_employee != "") echo $active_employee; ?></h3>
                                        <div class="progress mb-2" style="height: 5px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 70%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="mb-0">Overall Employees <?php echo total_employee(); ?></p>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block">Inactive Employee</span>
                                            </div>
                                            <div>
                                                <span class="text-success">+

                                                    <?php echo  round(($inactive_employee * 100) / total_employee(), 0); ?> %


                                                    %</span>
                                            </div>
                                        </div>
                                        <h3 class="mb-3">
                                            <?php if ($inactive_employee != "") echo $inactive_employee; ?>
                                        </h3>
                                        <div class="progress mb-2" style="height: 5px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 70%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="mb-0">Overall Employees <?php echo total_employee(); ?></p>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block">Today Absent</span>
                                            </div>
                                            <div>
                                                <span class="text-success">+0%</span>
                                            </div>
                                        </div>
                                        <h3 class="mb-3">0</h3>
                                        <div class="progress mb-2" style="height: 5px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 70%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="mb-0">Overall Employees <?php echo total_employee(); ?></p>
                                    </div>
                                </div>


                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block">Today Present</span>
                                            </div>
                                            <div>
                                                <span class="text-success">+0%</span>
                                            </div>
                                        </div>
                                        <h3 class="mb-3">0</h3>
                                        <div class="progress mb-2" style="height: 5px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 70%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="mb-0">Overall Employees <?php echo total_employee(); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">Total Attendance (Current Month)</h3>
                                    <div class="table-responsive">
                                        <div id="bar-charts" style="overflow:scroll; height:448px;"></div>
                                        <div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>
                <!-- /Page Content -->

            </div>
            <!-- /Page Wrapper -->
        </div>
        <!-- /Main Wrapper -->

        <?php include 'layouts/customizer.php'; ?>

        <?php include 'layouts/vendor-scripts.php'; ?>


        <?php

        $current_month = date("m") - 1;
        $current_year = date("Y");

        //echo $current_month;

        $query1 = "select * from hrm_employee where status=1;";
        $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));
        $x = 0;
        $total_days_present = [];
        $total_days_abscent = [];
        $employee_name = [];
        $data = "";
        while ($row1 = mysqli_fetch_array($result1)) {
            $total_days_present[$x] = total_days_present_in_current_month($row1['id'], $current_month, $current_year);

            $total_days_abscent[$x] = total_days_abscent_in_current_month($row1['id'], $current_month, $current_year);

            //  $employee_name[$x] = $row1['fname'];

            $employee_name[$x] = $row1['fname'];


            $data = $data . "{y:" . "'" . $employee_name[$x] . "'" . "," . "a:" . $total_days_present[$x] . "," .
                "b:" . $total_days_abscent[$x] . "},";

            $x++;
        }
        $data = rtrim($data, ",");

        ?>




        <script language="javascript">
            $(document).ready(function() {

                // Bar Chart

                Morris.Bar({
                    element: 'bar-charts',
                    redrawOnParentResize: true,
                    data: [{
                            y: 'Ritika',
                            a: 16,
                            b: 6
                        },
                        {
                            y: 'Vishesh',
                            a: 15,
                            b: 7
                        },
                        {
                            y: 'Shivam',
                            a: 16,
                            b: 6
                        },
                        {
                            y: 'Vishal',
                            a: 17,
                            b: 5
                        },
                        {
                            y: 'Pankaj',
                            a: 18,
                            b: 4
                        },
                        {
                            y: 'Yuvraj',
                            a: 10,
                            b: 12
                        },
                        {
                            y: 'Prem',
                            a: 16,
                            b: 6
                        },
                        {
                            y: 'Nitin',
                            a: 22,
                            b: 0
                        },
                        {
                            y: 'Kanu',
                            a: 15,
                            b: 7
                        },
                        {
                            y: 'Bhavya',
                            a: 14,
                            b: 8
                        },
                        {
                            y: 'Atul',
                            a: 0,
                            b: 0
                        },
                        {
                            y: 'Sumit',
                            a: 15,
                            b: 7
                        },
                        {
                            y: 'Sonali',
                            a: 14,
                            b: 8
                        },
                        {
                            y: 'Deepak',
                            a: 16,
                            b: 6
                        },
                        {
                            y: 'Pratik',
                            a: 0,
                            b: 0
                        }




                    ],
                    xkey: 'y',
                    ykeys: ['a', 'b'],
                    labels: ['Total Present', 'Total Abscent'],
                    lineColors: ['#ff9b44', '#fc6075'],
                    lineWidth: '3px',
                    barColors: ['#ff9b44', '#fc6075'],
                    resize: true,

                    redraw: true,
                    xLabelAngle: 60

                });

                // Line Chart


            });
        </script>

        </body>

        </html>