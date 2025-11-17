<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';
// get user name and other detail
$emp_id = $_SESSION['id'];
$conn=connect();
//$id=$_GET['id'];
$query="select * from hrm_employee where id='$emp_id';";
$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
$x="";
$row=mysqli_fetch_array($result);
//echo "aaaaaaaaaaaaaaaa=".$query;

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation="";
$department="";
$profile_image="";
$active_employee=0;
$inactive_employee=0;



$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir."/".$row['image'];
//count_where($table, $column, $value)
//{
	//$conn=connect();
	//$query="select count(*) from $table where $column='$id'";
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

//echo "profile_image".$profile_image;
?>

<head>

    <title>Attendance - HRMS admin template</title>

    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>

</head>

<body>
    <div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>

   <!-- Page Wrapper -->
            <div class="page-wrapper">
                <div class="content container-fluid">
                
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row">
                            <div class="col-sm-12">
                                <h3 class="page-title">Attendance</h3>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Attendance</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /Page Header -->
                    
                    <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="input-block mb-3 form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="input-block mb-3 form-focus select-focus">
                                <select class="select floating"> 
                                    <option>-</option>
                                    <option>Jan</option>
                                    <option>Feb</option>
                                    <option>Mar</option>
                                    <option>Apr</option>
                                    <option>May</option>
                                    <option>Jun</option>
                                    <option>Jul</option>
                                    <option>Aug</option>
                                    <option>Sep</option>
                                    <option>Oct</option>
                                    <option>Nov</option>
                                    <option>Dec</option>
                                </select>
                                <label class="focus-label">Select Month</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="input-block mb-3 form-focus select-focus">
                                <select class="select floating"> 
                                    <option>-</option>
                                    <option>2019</option>
                                    <option>2018</option>
                                    <option>2017</option>
                                    <option>2016</option>
                                    <option>2015</option>
                                </select>
                                <label class="focus-label">Select Year</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="d-grid">
                                <a href="#" class="btn btn-success"> Search </a>  
                            </div>
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>4</th>
                                            <th>5</th>
                                            <th>6</th>
                                            <th>7</th>
                                            <th>8</th>
                                            <th>9</th>
                                            <th>10</th>
                                            <th>11</th>
                                            <th>12</th>
                                            <th>13</th>
                                            <th>14</th>
                                            <th>15</th>
                                            <th>16</th>
                                            <th>17</th>
                                            <th>18</th>
                                            <th>19</th>
                                            <th>20</th>
                                            <th>22</th>
                                            <th>23</th>
                                            <th>24</th>
                                            <th>25</th>
                                            <th>26</th>
                                            <th>27</th>
                                            <th>28</th>
                                            <th>29</th>
                                            <th>30</th>
                                            <th>31</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-09.jpg"></a>
                                                    <a href="profile.php">John Doe</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td>
                                                <div class="half-day">
                                                    <span class="first-off"><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></span> 
                                                    <span class="first-off"><i class="fa fa-close text-danger"></i></span>
                                                </div>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td>
                                                <div class="half-day">
                                                    <span class="first-off"><i class="fa fa-close text-danger"></i></span> 
                                                    <span class="first-off"><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></span>
                                                </div>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-09.jpg"></a>
                                                    <a href="profile.php">Richard Miles</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-10.jpg"></a>
                                                    <a href="profile.php">John Smith</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-05.jpg"></a>
                                                    <a href="profile.php">Mike Litorus</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-11.jpg"></a>
                                                    <a href="profile.php">Wilmer Deluna</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-12.jpg"></a>
                                                    <a href="profile.php">Jeffrey Warden</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-13.jpg"></a>
                                                    <a href="profile.php">Bernardo Galaviz</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-01.jpg"></a>
                                                    <a href="profile.php">Lesley Grauer</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-16.jpg"></a>
                                                    <a href="profile.php">Jeffery Lalor</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-xs" href="profile.php"><img alt="" src="assets/img/profiles/avatar-04.jpg"></a>
                                                    <a href="profile.php">Loren Gatlin</a>
                                                </h2>
                                            </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><i class="fa fa-close text-danger"></i> </td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                            <td><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info"><i class="fa fa-check text-success"></i></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Content -->
                
                <!-- Attendance Modal -->
                <div class="modal custom-modal fade" id="attendance_info" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Attendance Info</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card punch-status">
                                            <div class="card-body">
                                                <h5 class="card-title">Timesheet <small class="text-muted">11 Mar 2019</small></h5>
                                                <div class="punch-det">
                                                    <h6>Punch In at</h6>
                                                    <p>Wed, 11th Mar 2019 10.00 AM</p>
                                                </div>
                                                <div class="punch-info">
                                                    <div class="punch-hours">
                                                        <span>3.45 hrs</span>
                                                    </div>
                                                </div>
                                                <div class="punch-det">
                                                    <h6>Punch Out at</h6>
                                                    <p>Wed, 20th Feb 2019 9.00 PM</p>
                                                </div>
                                                <div class="statistics">
                                                    <div class="row">
                                                        <div class="col-md-6 col-6 text-center">
                                                            <div class="stats-box">
                                                                <p>Break</p>
                                                                <h6>1.21 hrs</h6>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-6 text-center">
                                                            <div class="stats-box">
                                                                <p>Overtime</p>
                                                                <h6>3 hrs</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card recent-activity">
                                            <div class="card-body">
                                                <h5 class="card-title">Activity</h5>
                                                <ul class="res-activity-list">
                                                    <li>
                                                        <p class="mb-0">Punch In at</p>
                                                        <p class="res-activity-time">
                                                            <i class="fa fa-clock-o"></i>
                                                            10.00 AM.
                                                        </p>
                                                    </li>
                                                    <li>
                                                        <p class="mb-0">Punch Out at</p>
                                                        <p class="res-activity-time">
                                                            <i class="fa fa-clock-o"></i>
                                                            11.00 AM.
                                                        </p>
                                                    </li>
                                                    <li>
                                                        <p class="mb-0">Punch In at</p>
                                                        <p class="res-activity-time">
                                                            <i class="fa fa-clock-o"></i>
                                                            11.15 AM.
                                                        </p>
                                                    </li>
                                                    <li>
                                                        <p class="mb-0">Punch Out at</p>
                                                        <p class="res-activity-time">
                                                            <i class="fa fa-clock-o"></i>
                                                            1.30 PM.
                                                        </p>
                                                    </li>
                                                    <li>
                                                        <p class="mb-0">Punch In at</p>
                                                        <p class="res-activity-time">
                                                            <i class="fa fa-clock-o"></i>
                                                            2.00 PM.
                                                        </p>
                                                    </li>
                                                    <li>
                                                        <p class="mb-0">Punch Out at</p>
                                                        <p class="res-activity-time">
                                                            <i class="fa fa-clock-o"></i>
                                                            7.30 PM.
                                                        </p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Attendance Modal -->
                
            </div>
            <!-- Page Wrapper -->


</div>
<!-- end main wrapper-->

<?php include 'layouts/customizer.php'; ?>
<!-- JAVASCRIPT -->
<?php include 'layouts/vendor-scripts.php'; ?>



</body>

</html>