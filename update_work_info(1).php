<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);

$emp_id = $_GET['emp_id'];
$doj = $_GET['doj'];
$probation_period = $_GET['probation_period'];
$employee_type = $_GET['employee_type'];
$work_location = $_GET['work_location'];
$experience = $_GET['experience'];
$designation_id = $_GET['designation_id'];
$job_title = $_GET['job_title'];
$department_id = $_GET['department_id'];

$date = date("Y-m-d");

//echo $_GET['rid']."========".$_GET['rt']."from ajax and emp_id = ".$emp_id."r_auto_id=".$r_auto_id;
$query = "update hrm_employee set doj = '$doj', probation_period = 
'$probation_period', employee_type = '$employee_type', work_location = '$work_location', 
 experience = '$experience',
 designation_id = '$designation_id', job_title = '$job_title', department_id = '$department_id' where id = '$emp_id';";

  //echo $query;
  //echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));

echo "Work Info Updated";

/*
// if social media already inserted then update else insert

$query = "select * from hrm_employee_social where emp_id = '$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));
if(mysqli_num_rows($result)>0)
{
  $query = "update hrm_employee_social set added_date='$date', facebook = '$facebook', twitter = '$twitter', linkedin = '$linkedin' where emp_id = '$emp_id';";
  $result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));
}
else
{
  $query = "insert into hrm_employee_social(emp_id, added_date, facebook, twitter, linkedin) values
  ('$emp_id', '$date', '$facebook', '$twitter', '$linkedin');";
  $result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));
}

// if social media already inserted then update else insert

// check the records are updated or not

//if(mysqli_affected_rows($conn)>0)
//{
    echo "Contact Info Updated";
//}
/*else
{
    echo "Family Member Already Added";
}*/

?>