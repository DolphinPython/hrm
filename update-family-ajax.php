<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);
$emp_id = $_GET['emp_id'];
$name = $_GET['name'];
$relationship = $_GET['relationship'];
$phone = $_GET['phone'];
$dependent = $_GET['dependent'];

// if social media already inserted then update else insert

/*$query = "select * from hrm_employee_family where employee_id='$emp_id' and reporting_manager_id = '$reporting_manager_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));
if(mysqli_num_rows($result)==0)
{
  
*/
$query = "insert into hrm_employee_family(emp_id, name, relationship_id, phone, dependent) 
values ('$emp_id', '$name', '$relationship', '$phone', '$dependent')";
  //echo $query;
  //echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));

//}

// check the records are updated or not

//if(mysqli_affected_rows($conn)>0)
//{
    echo "Family Member Added";
//}
//else
//{
//    echo "Education Already Saved";
//}
?>