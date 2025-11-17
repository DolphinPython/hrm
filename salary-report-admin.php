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
                            <h3 class="page-title">Salary Report Admin</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Salary Report Admin</li>
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
    <div class="col-lg-3">
        <label>Total Days Present - 

        <?php 
      if(isset($_GET['employee_id']))  
      {
        $emp_id=$_GET['employee_id'];
      }
        $current_month = date("m")-1; $current_year = date("Y");
//echo $current_month.$current_year;



echo total_days_present_in_current_month($emp_id, $current_month, $current_year);
?>
        </label>
    </div>
    <div class="col-lg-3">
        <label>Total Days Abscent - 

        <?php 
echo total_days_abscent_in_current_month($emp_id, $current_month, $current_year);
$total_days_abscent_in_current_month = total_days_abscent_in_current_month($emp_id, $current_month, $current_year);
?>
        </label>
    </div>
    <div class="col-lg-3">
        <label>
Total Days Late
<?php 
echo total_late_in_current_month($emp_id, $current_month, $current_year);

?>

<div style="display:none;">
 <br>
  Total Minutes Late
 <?php 

echo total_minute_late_in_current_month($emp_id, $current_month, $current_year);
$query="select * from hrm_attandance_machine_detail join hrm_employee on 
	hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id  
	where hrm_employee.id='$emp_id' and hrm_attandance_machine_detail.month='$current_month' 
	and hrm_attandance_machine_detail.year='$current_year';";
    //echo $query;
?>


    </div>
        
        </label>
    </div>
    <div class="col-lg-3">
        <label>Monthly Salary - <?php echo get_value("hrm_employee", "salary", $emp_id); 
        $salary =  get_value("hrm_employee", "salary", $emp_id);
        $per_day_salary = get_value("hrm_employee", "salary", $emp_id)/30; 
        $per_day_salary = round($per_day_salary, 2);
        //echo $per_day_salary;
        ?></label>


        


        <label>Salary Deducted For <input type="text" name="no_deduct_days" id="no_deduct_days" size="2" maxlength="2"  
        value="<?php echo $total_days_abscent_in_current_month; ?>"> Days  
        <button type="button" class="btn btn-primary btn-sm" 
        onclick="display_calculated_salary(
        document.getElementById('no_deduct_days').value, '<?php echo $per_day_salary; ?>', '<?php echo $salary; ?>' );">Show</button>


       
       
        <label>Deduct Abscent Salary - 
            
        <span id="div_deducted_salary"><?php  $abscent_salary = $per_day_salary * $total_days_abscent_in_current_month;
        //echo $abscent_salary;
        ?></span>
        
        </label>
        


          <label>Salary Payable - 
            
          <span id="div_salary_payable"><?php $salary_payable = $salary - $abscent_salary;
        //echo $salary_payable;
        ?></span>

        </label>
        




        
    </div>
</div>


                
               
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
                    $query = "select * from hrm_attandance_machine_detail join hrm_employee 
                    on hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id 
                    where hrm_attandance_machine_detail.year='$year' and hrm_attandance_machine_detail.month='$month' 
                    and hrm_employee.id='$employee_id'";
                    //echo $query;
                    $result=mysqli_query($conn, $query) or die(mysqli_error($conn));
                    while($row=mysqli_fetch_array($result))
                    {    
                 ?>
                                    <tr>
                                        <td>1</td>
                                        <td><?php echo date('j F Y', strtotime($row['date1'])); ?></td>
                                        <td><?php echo substr($row['date_in_out1'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out1'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>30</td>
                                        <td><?php echo date('j F Y', strtotime($row['date30'])); ?></td>
                                        <td><?php echo substr($row['date_in_out30'], 0,5); ?></td>
                                        <td><?php echo substr($row['date_in_out30'], 5,10); ?></td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right" style="">
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
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
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" style="display:none;" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
        <?php } 
        }?>
                                    
                                </tbody>
                            </table>

                            <!-- Edit Holiday Modal -->
            <div class="modal custom-modal fade" id="edit_holiday" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit In / Out Time</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Employee Name <span class="text-danger">*</span></label>
                                    <input class="form-control" value="New Year" type="text">
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
                                    <button class="btn btn-primary submit-btn">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Edit Holiday Modal -->
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

<script language="javascript">
    function display_calculated_salary(abscent_days, per_day_salary, salary)
    {

        //display_calculated_salary(this.value, <?php echo $total_days_abscent_in_current_month; ?>, <?php echo $salary; ?> )
        //document.getElementById('d1_div_calculaed_salary').style.display='block';



        //alert(salary);
        var deducted_salary = per_day_salary * abscent_days;

        document.getElementById('div_deducted_salary').innerHTML=Math.trunc(deducted_salary);

        var salary_payable = salary - deducted_salary;
        document.getElementById('div_salary_payable').innerHTML=Math.trunc(salary_payable);

    }
</script>

<?php include 'layouts/customizer.php'; ?>
<!-- JAVASCRIPT -->
<?php include 'layouts/vendor-scripts.php'; ?>



</body>

</html>