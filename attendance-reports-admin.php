<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<?php 
date_default_timezone_set('Asia/Kolkata');
include 'include/function.php';
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


$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

if($row['role'] != 'admin' and $row['role']!='super admin')
{ 
    header("Location:attendance-report-employee.php");
}
?>

<head>

    <title> Reports - HRMS admin template</title>

    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>

<script language="javascript">    
    function update_in_out_time(employee_id,hrm_attandance_machine_detail_id,
    ids,month, year, column_name, in_time, out_time,  old_in_time, old_out_time, date)
    {  
       



        //alert(employee_id+hrm_attandance_machine_detail_id+ids+month+year+column_name+in_time+out_time+old_in_time+old_out_time);     
        $.ajax({
                    type: "GET",
                    url: "update_in_out_time.php",
                    data: "employee_id=" + employee_id +
                     "&hrm_attandance_machine_detail_id=" + hrm_attandance_machine_detail_id +
                      "&ids=" + ids + "&month=" + month + "&year=" + year
                       + "&column_name=" + column_name + 
                       "&in_time=" + in_time + "&out_time=" + out_time + 
                       "&old_in_time=" + old_in_time + "&old_out_time=" + old_out_time
                       + "&date=" + date,
                    success: function(data) {
                       alert(data);
                    }
                });

                $('#edit_time').modal('hide');
                location.reload();  

         
    }
</script>

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
                        <div class="col-sm-12">
                            <h3 class="page-title">Attendance Reports Admin</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Attendance Reports Admin</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                
                    <!-- Content Starts -->
                    <!-- Search Filter -->
    <form name="attendance_search_form" id="attendance_search_form" method="get" action=""> 
                <div class="row filter-row">
                    
                    <div class="col-sm-6 col-md-3">  
                        <div class="input-block mb-3 form-focus">
                        <select name="employee_id" id="employee_id" class="form-control">
                        <option value="">Select Employee</option> 
          <?php           
          $query_display_employee="select * from hrm_employee order by fname asc;";          
          $result_display_employee=mysqli_query($conn, $query_display_employee) or die(mysqli_error($conn));
          $x="";
          while($row_display_employee=mysqli_fetch_array($result_display_employee))
          { ?>
           <option value="<?php echo $row_display_employee['id']; ?>" 
           <?php if(isset($_GET['employee_id'])){ 
            if($_GET['employee_id']==$row_display_employee['id']) echo "selected = 'selected'";
            } ?>
           ><?php echo $row_display_employee['fname']." ".$row_display_employee['lname']; ?></option>
          <?php } ?>
          </select>
          
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">  
                        <div class="input-block mb-3 form-focus">
                            <div class="cal-icon">
                                
                            <select class="form-control floating select" name="month" id="month" required>
                                    <option value="">Select Month</option>
                                    <option value="1" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="1") echo "selected = 'selected'";
            } ?>>January</option>
                                    <option value="2" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="2") echo "selected = 'selected'";
            } ?>>February</option>
                                    <option value="3" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="3") echo "selected = 'selected'";
            } ?>>March</option>
                                    <option value="4" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="4") echo "selected = 'selected'";
            } ?>>April</option>
                                    <option value="5" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="5") echo "selected = 'selected'";
            } ?>>May</option>
                                    <option value="6" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="6") echo "selected = 'selected'";
            } ?>>June</option>
                                    <option value="7" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="7") echo "selected = 'selected'";
            } ?>>July</option>
                                    <option value="8" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="8") echo "selected = 'selected'";
            } ?>>August</option>
                                    <option value="9" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="9") echo "selected = 'selected'";
            } ?>>September</option>
                                    <option value="10" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="10") echo "selected = 'selected'";
            } ?>>October</option>
                                    <option value="11" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="11") echo "selected = 'selected'";
            } ?>>November</option>
                                    <option value="12" <?php if(isset($_GET['month'])){ 
            if($_GET['month']=="12") echo "selected = 'selected'";
            } ?>>December</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">  
                        <div class="input-block mb-3 form-focus">
                            <div class="cal-icon">
                                <select class="form-control floating select" name="year" id="year" required>
                                    <option value="">Select Year</option>
                                    <option value="2024" <?php if(isset($_GET['year'])){ 
            if($_GET['year']=="2024") echo "selected = 'selected'";
            } ?>>2024</option>
                                    <option value="2025" <?php if(isset($_GET['year'])){ 
            if($_GET['year']=="2025") echo "selected = 'selected'";
            } ?>>2025</option>
                                    <option value="2026" <?php if(isset($_GET['year'])){ 
            if($_GET['year']=="2026") echo "selected = 'selected'";
            } ?>>2026</option>
                                    <option value="2027" <?php if(isset($_GET['year'])){ 
            if($_GET['year']=="2027") echo "selected = 'selected'";
            } ?>>2027</option>
                                    <option value="2028" <?php if(isset($_GET['year'])){ 
            if($_GET['year']=="2028") echo "selected = 'selected'";
            } ?>>2028</option>
                                    <option value="2029" <?php if(isset($_GET['year'])){ 
            if($_GET['year']=="2029") echo "selected = 'selected'";
            } ?>>2029</option>
                                    <option value="2030" <?php if(isset($_GET['year'])){ 
            if($_GET['year']=="2030") echo "selected = 'selected'";
            } ?>>2030</option>
                                      
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">  
                        <div class="d-grid">
            <input type="submit" name="b1" id="b1" value="Search" class="btn btn-success">  
                        </div>
                    </div>     
                </div>

            </form>    
                <!-- /Search Filter -->
               
               
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Clock In</th>
                                        <th>Clock Out</th>
                                        <th>Update Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                if(isset($_GET['b1']))
                {
                    $employee_id = mysqli_real_escape_string($conn, $_GET['employee_id']); 
                    $year = mysqli_real_escape_string($conn, $_GET['year']);  
                    $month = mysqli_real_escape_string($conn, $_GET['month']);
                    $query = "select hrm_attandance_machine_detail.id as hrm_attandance_machine_detail_id,hrm_attandance_machine_detail.attandance_id,
                    hrm_attandance_machine_detail.employee_name,hrm_attandance_machine_detail.from_date,
                    hrm_attandance_machine_detail.to_date,hrm_attandance_machine_detail.date1,
                    hrm_attandance_machine_detail.date2,hrm_attandance_machine_detail.date3,
                    hrm_attandance_machine_detail.date4,hrm_attandance_machine_detail.date5,
                    hrm_attandance_machine_detail.date6,hrm_attandance_machine_detail.date7,
                    hrm_attandance_machine_detail.date8,hrm_attandance_machine_detail.date9,
                    hrm_attandance_machine_detail.date10,hrm_attandance_machine_detail.date11,
                    hrm_attandance_machine_detail.date12,hrm_attandance_machine_detail.date13,
                    hrm_attandance_machine_detail.date14,hrm_attandance_machine_detail.date15,
                    hrm_attandance_machine_detail.date16,hrm_attandance_machine_detail.date17,
                    hrm_attandance_machine_detail.date18,hrm_attandance_machine_detail.date19,
                    hrm_attandance_machine_detail.date20,hrm_attandance_machine_detail.date21,
                    hrm_attandance_machine_detail.date22,hrm_attandance_machine_detail.date23,
                    hrm_attandance_machine_detail.date24,hrm_attandance_machine_detail.date25,
                    hrm_attandance_machine_detail.date26,hrm_attandance_machine_detail.date27,
                    hrm_attandance_machine_detail.date28,hrm_attandance_machine_detail.date29,
                    hrm_attandance_machine_detail.date30,hrm_attandance_machine_detail.date31,
                    hrm_attandance_machine_detail.date_in_out1,hrm_attandance_machine_detail.date_in_out2,
                    hrm_attandance_machine_detail.date_in_out3,hrm_attandance_machine_detail.date_in_out4,
                    hrm_attandance_machine_detail.date_in_out5,hrm_attandance_machine_detail.date_in_out6,
                    hrm_attandance_machine_detail.date_in_out7,hrm_attandance_machine_detail.date_in_out8,
                    hrm_attandance_machine_detail.date_in_out9,hrm_attandance_machine_detail.date_in_out10,
                    hrm_attandance_machine_detail.date_in_out11,hrm_attandance_machine_detail.date_in_out12,
                    hrm_attandance_machine_detail.date_in_out13,hrm_attandance_machine_detail.date_in_out14,
                    hrm_attandance_machine_detail.date_in_out15,hrm_attandance_machine_detail.date_in_out16,
                    hrm_attandance_machine_detail.date_in_out17,hrm_attandance_machine_detail.date_in_out18,
                    hrm_attandance_machine_detail.date_in_out19,hrm_attandance_machine_detail.date_in_out20,
                    hrm_attandance_machine_detail.date_in_out21,hrm_attandance_machine_detail.date_in_out22,
                    hrm_attandance_machine_detail.date_in_out23,hrm_attandance_machine_detail.date_in_out24,
                    hrm_attandance_machine_detail.date_in_out25,hrm_attandance_machine_detail.date_in_out26,
                    hrm_attandance_machine_detail.date_in_out27,hrm_attandance_machine_detail.date_in_out28,
                    hrm_attandance_machine_detail.date_in_out29,hrm_attandance_machine_detail.date_in_out30,
                    hrm_attandance_machine_detail.date_in_out31,hrm_attandance_machine_detail.other_detail,
                    hrm_attandance_machine_detail.year,hrm_attandance_machine_detail.month,
                    hrm_attandance_machine_detail.added_date,hrm_attandance_machine_detail.added_by,

                    hrm_employee.id as emp_id,hrm_employee.fname,hrm_employee.lname,
                    hrm_employee.attendance_id,hrm_employee.status


                    
                    
                    from hrm_attandance_machine_detail join hrm_employee 
                    on hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id 
                    where year='$year' and month='$month' 
                    and hrm_employee.id='$employee_id'";
                    //echo $query;
                    $result=mysqli_query($conn, $query) or die(mysqli_error($conn));
                    while($row=mysqli_fetch_array($result))
                    {
                        $date30=$row['date30'];    
                 ?>
                                    <tr>
                                        <td>1</td>
                                        <td><?php echo date('j F Y', strtotime($row['date1'])); ?>
                                        
                                    </td>
                                        <td><?php echo substr($row['date_in_out1'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out1'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date1']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>2</td>
                                        <td><?php echo date('j F Y', strtotime($row['date2'])); ?></td>
                                        <td><?php echo substr($row['date_in_out2'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out2'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date2']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>3</td>
                                        <td><?php echo date('j F Y', strtotime($row['date3'])); ?></td>
                                        <td><?php echo substr($row['date_in_out3'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out3'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date3']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>4</td>
                                        <td><?php echo date('j F Y', strtotime($row['date4'])); ?></td>
                                        <td><?php echo substr($row['date_in_out4'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out4'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date4']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>5</td>
                                        <td><?php echo date('j F Y', strtotime($row['date5'])); ?></td>
                                        <td><?php echo substr($row['date_in_out5'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out5'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date5']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>6</td>
                                        <td><?php echo date('j F Y', strtotime($row['date6'])); ?></td>
                                        <td><?php echo substr($row['date_in_out6'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out6'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date6']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>7</td>
                                        <td><?php echo date('j F Y', strtotime($row['date7'])); ?></td>
                                        <td><?php echo substr($row['date_in_out7'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out7'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date7']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>8</td>
                                        <td><?php echo date('j F Y', strtotime($row['date8'])); ?></td>
                                        <td><?php echo substr($row['date_in_out8'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out8'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date8']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>9</td>
                                        <td><?php echo date('j F Y', strtotime($row['date9'])); ?></td>
                                        <td><?php echo substr($row['date_in_out9'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out9'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date9']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>10</td>
                                        <td><?php echo date('j F Y', strtotime($row['date10'])); ?></td>
                                        <td><?php echo substr($row['date_in_out10'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out10'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date10']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>11</td>
                                        <td><?php echo date('j F Y', strtotime($row['date11'])); ?></td>
                                        <td><?php echo substr($row['date_in_out11'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out11'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date11']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>12</td>
                                        <td><?php echo date('j F Y', strtotime($row['date12'])); ?></td>
                                        <td><?php echo substr($row['date_in_out12'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out12'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date12']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>13</td>
                                        <td><?php echo date('j F Y', strtotime($row['date13'])); ?></td>
                                        <td><?php echo substr($row['date_in_out13'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out13'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date13']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>14</td>
                                        <td><?php echo date('j F Y', strtotime($row['date14'])); ?></td>
                                        <td><?php echo substr($row['date_in_out14'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out14'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date14']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>15</td>
                                        <td><?php echo date('j F Y', strtotime($row['date15'])); ?></td>
                                        <td><?php echo substr($row['date_in_out15'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out15'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date15']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>16</td>
                                        <td><?php echo date('j F Y', strtotime($row['date16'])); ?></td>
                                        <td><?php echo substr($row['date_in_out16'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out16'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date16']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>17</td>
                                        <td><?php echo date('j F Y', strtotime($row['date17'])); ?></td>
                                        <td><?php echo substr($row['date_in_out17'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out17'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date17']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>18</td>
                                        <td><?php echo date('j F Y', strtotime($row['date18'])); ?></td>
                                        <td><?php echo substr($row['date_in_out18'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out18'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date18']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>19</td>
                                        <td><?php echo date('j F Y', strtotime($row['date19'])); ?></td>
                                        <td><?php echo substr($row['date_in_out19'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out19'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date19']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>20</td>
                                        <td><?php echo date('j F Y', strtotime($row['date20'])); ?></td>
                                        <td><?php echo substr($row['date_in_out20'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out20'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date20']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>21</td>
                                        <td><?php echo date('j F Y', strtotime($row['date21'])); ?></td>
                                        <td><?php echo substr($row['date_in_out21'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out21'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date21']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>22</td>
                                        <td><?php echo date('j F Y', strtotime($row['date22'])); ?></td>
                                        <td><?php echo substr($row['date_in_out22'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out22'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a class="dropdown-item" id="edit<?php echo $row['date22']; ?>" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>23</td>
                                        <td><?php echo date('j F Y', strtotime($row['date23'])); ?></td>
                                        <td><?php echo substr($row['date_in_out23'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out23'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a class="dropdown-item" id="edit<?php echo $row['date23']; ?>" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>24</td>
                                        <td><?php echo date('j F Y', strtotime($row['date24'])); ?></td>
                                        <td><?php echo substr($row['date_in_out24'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out24'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a class="dropdown-item" id="edit<?php echo $row['date24']; ?>" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>25</td>
                                        <td><?php echo date('j F Y', strtotime($row['date25'])); ?></td>
                                        <td><?php echo substr($row['date_in_out25'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out25'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a class="dropdown-item" id="edit<?php echo $row['date25']; ?>" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>26</td>
                                        <td><?php echo date('j F Y', strtotime($row['date26'])); ?></td>
                                        <td><?php echo substr($row['date_in_out26'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out26'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a class="dropdown-item" id="edit<?php echo $row['date26']; ?>" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>27</td>
                                        <td><?php echo date('j F Y', strtotime($row['date27'])); ?></td>
                                        <td><?php echo substr($row['date_in_out27'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out27'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date27']; ?>" class="dropdown-item" 
                                                     href="#" data-bs-toggle="modal" 
                                                    data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>28</td>
                                        <td><?php echo date('j F Y', strtotime($row['date28'])); ?></td>
                                        <td><?php echo substr($row['date_in_out28'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out28'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date28']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>29</td>
                                        <td><?php echo date('j F Y', strtotime($row['date29'])); ?></td>
                                        <td><?php echo substr($row['date_in_out29'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out29'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date29']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a  class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>30</td>
                                        <td><?php echo date('j F Y', strtotime($row['date30'])); ?>
                                        <br>
                                        <?php echo $row['date30']; ?>
                                    </td>
                                        <td><?php echo substr($row['date_in_out30'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out30'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" 
                                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id ="edit<?php echo $row['date30']; ?>" name ="edit<?php echo $row['date30']; ?>" 
                                                    class="dropdown-item" href="#" data-bs-toggle="modal" 
                                                    data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>31</td>
                                        <td><?php echo date('j F Y', strtotime($row['date31'])); ?></td>
                                        <td><?php echo substr($row['date_in_out31'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out31'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a id="edit<?php echo $row['date31']; ?>" class="dropdown-item" href="#" 
                                                    data-bs-toggle="modal" data-bs-target="#edit_time"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>




           <!-- dialog -->
  <!-- Edit Time Modal -->


                                  

  <div class="modal custom-modal fade" id="edit_time" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit In / Out Time <?php //echo $row['id']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                            <div class="input-block mb-3">
                                    <label class="col-form-label">Date <span class="text-danger">*</span></label>
                                    <input class="form-control" value="" readonly name="date" id="date" type="text">
                                </div>

                                <div class="input-block mb-3">
                                    <label class="col-form-label">Employee Name <span class="text-danger">*</span></label>
                                    <input class="form-control" readonly value="" name="emp_name" id="emp_name" type="text">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">In Time <span class="text-danger">*</span></label>
                                    
                    <input type="time" class="form-control" name="in_time" id="in_time" placeholder="In Time" 
                    value="<?php echo date('H:i') ?>" required />
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Out Time <span class="text-danger">*</span></label>
                                    
                    <input type="time" class="form-control" name="out_time" id="out_time" placeholder="Out Time" 
                    value="<?php echo date('H:i') ?>" required />
                                </div>
                                <div class="submit-section">
                                    <input type="button" id="save1" name="save1" 
                                    class="btn btn-primary submit-btn" value="Save" 
                                    onclick="update_in_out_time(
                    '<?php echo $_GET['employee_id']; ?>',  
                    document.getElementById('hrm_attandance_machine_detail_id').value,
                    document.getElementById('id').value, '<?php echo $_GET['month']; ?>',
                    '<?php echo $_GET['year']; ?>', document.getElementById('column_name').value,
                    document.getElementById('in_time').value,
                    document.getElementById('out_time').value,
                    document.getElementById('old_in_time').value,
                    document.getElementById('old_out_time').value,
                    document.getElementById('date').value);">


                                    <input class="form-control" readonly value="" 
                                    name="hrm_attandance_machine_detail_id" 
                                    id="hrm_attandance_machine_detail_id" type="text">

                                    <input class="form-control" readonly value="<?php echo $row['hrm_attandance_machine_detail_id']; ?>" 
                                    name="id" 
                                    id="id" type="text">

                                    <input class="form-control" readonly 
                                    value="" 
                                    name="column_name" 
                                    id="column_name" type="text">

                                    <input type="text" class="form-control" readonly 
                                    name="old_in_time" id="old_in_time" placeholder="In Time" 
                    value="" required />
                                    <input type="text" class="form-control" readonly 
                                    name="old_out_time" id="old_out_time" placeholder="Out Time" 
                    value="" required />
                                
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Time Modal --><?php //echo $row['date31']; ?>

          
<!-- dialog -->     

                                    <script type="text/javascript">
                                        

$(document).ready(function(){
  
  
    $("#edit<?php echo $row['date1']; ?>").click(function(){

    <?php $in_time = substr($row['date_in_out1'], 0,5); 
        $out_time =  substr($row['date_in_out1'], 5,10); ?>
    //alert("<?php //echo  $row['date_in_out30']; ?>");
    $("#in_time").val("<?php echo  $in_time; ?>");
    $("#out_time").val("<?php echo  $out_time; ?>");
    $("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
    $("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
    $("#date").val("<?php echo  $row['date1']; ?>");
    $("#column_name").val("date_in_out1");
    $("#old_in_time").val("<?php echo  $in_time; ?>");
    $("#old_out_time").val("<?php echo  $out_time; ?>");

    
  });

  $("#edit<?php echo $row['date2']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out2'], 0,5); 
    $out_time =  substr($row['date_in_out2'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date2']; ?>");
$("#column_name").val("date_in_out2");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});


$("#edit<?php echo $row['date3']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out3'], 0,5); 
    $out_time =  substr($row['date_in_out3'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date3']; ?>");
$("#column_name").val("date_in_out3");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date4']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out4'], 0,5); 
    $out_time =  substr($row['date_in_out4'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date4']; ?>");
$("#column_name").val("date_in_out4");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date5']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out5'], 0,5); 
    $out_time =  substr($row['date_in_out5'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date5']; ?>");
$("#column_name").val("date_in_out5");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date6']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out6'], 0,5); 
    $out_time =  substr($row['date_in_out6'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date6']; ?>");
$("#column_name").val("date_in_out6");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date7']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out7'], 0,5); 
    $out_time =  substr($row['date_in_out7'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date7']; ?>");
$("#column_name").val("date_in_out7");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date8']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out8'], 0,5); 
    $out_time =  substr($row['date_in_out8'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date8']; ?>");
$("#column_name").val("date_in_out8");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date9']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out9'], 0,5); 
    $out_time =  substr($row['date_in_out9'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date9']; ?>");
$("#column_name").val("date_in_out9");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date10']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out10'], 0,5); 
    $out_time =  substr($row['date_in_out10'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date10']; ?>");
$("#column_name").val("date_in_out10");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date11']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out11'], 0,5); 
    $out_time =  substr($row['date_in_out11'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date11']; ?>");
$("#column_name").val("date_in_out11");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});


$("#edit<?php echo $row['date12']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out12'], 0,5); 
    $out_time =  substr($row['date_in_out12'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date12']; ?>");
$("#column_name").val("date_in_out12");



$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date13']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out13'], 0,5); 
    $out_time =  substr($row['date_in_out13'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date13']; ?>");
$("#column_name").val("date_in_out13");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");

});


$("#edit<?php echo $row['date14']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out14'], 0,5); 
    $out_time =  substr($row['date_in_out14'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date14']; ?>");
$("#column_name").val("date_in_out14");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});



$("#edit<?php echo $row['date15']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out15'], 0,5); 
    $out_time =  substr($row['date_in_out15'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date15']; ?>");
$("#column_name").val("date_in_out15");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});


$("#edit<?php echo $row['date16']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out16'], 0,5); 
    $out_time =  substr($row['date_in_out16'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date16']; ?>");
$("#column_name").val("date_in_out16");


$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});


$("#edit<?php echo $row['date17']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out17'], 0,5); 
    $out_time =  substr($row['date_in_out17'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date17']; ?>");
$("#column_name").val("date_in_out17");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});



$("#edit<?php echo $row['date18']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out18'], 0,5); 
    $out_time =  substr($row['date_in_out18'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date18']; ?>");
$("#column_name").val("date_in_out18");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});


$("#edit<?php echo $row['date19']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out19'], 0,5); 
    $out_time =  substr($row['date_in_out19'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date19']; ?>");
$("#column_name").val("date_in_out19");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});


$("#edit<?php echo $row['date20']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out20'], 0,5); 
    $out_time =  substr($row['date_in_out20'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date20']; ?>");
$("#column_name").val("date_in_out20");
$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});



$("#edit<?php echo $row['date21']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out21'], 0,5); 
    $out_time =  substr($row['date_in_out21'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date21']; ?>");
$("#column_name").val("date_in_out21");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date22']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out22'], 0,5); 
    $out_time =  substr($row['date_in_out22'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date22']; ?>");
$("#column_name").val("date_in_out22");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date23']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out23'], 0,5); 
    $out_time =  substr($row['date_in_out23'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date23']; ?>");
$("#column_name").val("date_in_out23");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date24']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out24'], 0,5); 
    $out_time =  substr($row['date_in_out24'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date24']; ?>");
$("#column_name").val("date_in_out24");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});



$("#edit<?php echo $row['date25']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out25'], 0,5); 
    $out_time =  substr($row['date_in_out25'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date25']; ?>");
$("#column_name").val("date_in_out25");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date26']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out26'], 0,5); 
    $out_time =  substr($row['date_in_out26'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date26']; ?>");
$("#column_name").val("date_in_out26");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});



$("#edit<?php echo $row['date27']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out27'], 0,5); 
    $out_time =  substr($row['date_in_out27'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date27']; ?>");
$("#column_name").val("date_in_out27");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});



$("#edit<?php echo $row['date28']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out28'], 0,5); 
    $out_time =  substr($row['date_in_out28'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date28']; ?>");
$("#column_name").val("date_in_out28");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date29']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out29'], 0,5); 
    $out_time =  substr($row['date_in_out29'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date29']; ?>");
$("#column_name").val("date_in_out29");



$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date30']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out30'], 0,5); 
    $out_time =  substr($row['date_in_out30'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date30']; ?>");
$("#column_name").val("date_in_out30");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

$("#edit<?php echo $row['date31']; ?>").click(function(){

<?php $in_time = substr($row['date_in_out31'], 0,5); 
    $out_time =  substr($row['date_in_out31'], 5,10); ?>
//alert("<?php //echo  $row['date_in_out30']; ?>");
$("#in_time").val("<?php echo  $in_time; ?>");
$("#out_time").val("<?php echo  $out_time; ?>");
$("#emp_name").val("<?php echo  $row['fname']; ?> <?php echo  $row['lname']; ?>");
$("#hrm_attandance_machine_detail_id").val("<?php echo  $row['hrm_attandance_machine_detail_id']; ?>");
$("#date").val("<?php echo  $row['date31']; ?>");
$("#column_name").val("date_in_out31");

$("#old_in_time").val("<?php echo  $in_time; ?>");
$("#old_out_time").val("<?php echo  $out_time; ?>");
});

















  /*$("#edit<?php //echo $row['date27']; ?>").click(function(){
    //alert("The paragraph was clicked.");
    $("#emp_name").val(<?php //echo $row['date27']; ?>);
  });*/


});

      

        //$(".formData").val("")
    </script>

                                   
        <?php } 
        }?>
                                    
                                </tbody>
                            </table>










                            
                        </div>
                    </div>
                </div>
            
                <!-- /Content End -->
                
            </div>
            <!-- /Page Content -->
            
        </div>
        <!-- /Page Wrapper -->

</div>
<!-- end main wrapper-->

<?php //include 'layouts/customizer.php'; ?>
<!-- JAVASCRIPT -->
<?php include 'layouts/vendor-scripts.php'; ?>



</body>

</html>