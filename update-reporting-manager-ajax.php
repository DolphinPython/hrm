<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);
$emp_id = $_GET['emp_id'];
$reporting_manager_type = $_GET['reporting_manager_type'];

$reporting_manager_id = $_GET['reporting_manager_id'];

// if social media already inserted then update else insert

$query = "select * from hrm_reporting_manager where employee_id='$emp_id' and reporting_manager_id = '$reporting_manager_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));
if(mysqli_num_rows($result)==0)
{
  
$query = "insert into hrm_reporting_manager(employee_id, reporting_manager_id, reporting_manager_type) 
values ('$emp_id', '$reporting_manager_id', '$reporting_manager_type')";
  //echo $query;
  //echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));

}

// check the records are updated or not

//if(mysqli_affected_rows($conn)>0)
//{
    echo "Reporting Manager Added";
//}
//else
//{
//    echo "Education Already Saved";
//}
?>