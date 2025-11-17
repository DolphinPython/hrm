<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);
$emp_id = $_GET['emp_id'];
$fname = $_GET['fname'];
$lname = $_GET['lname'];
$dob = $_GET['dob'];
$gender = $_GET['gender'];
$current_address = $_GET['current_address'];
$permanent_address = $_GET['permanent_address'];
$mobile1 = $_GET['mobile1'];
$mobile2 = $_GET['mobile2'];
$email = $_GET['email'];
$office_email = $_GET['office_email'];
$department_id = $_GET['department_id'];
$designation_id = $_GET['designation_id'];

//echo $_GET['rid']."========".$_GET['rt']."from ajax and emp_id = ".$emp_id."r_auto_id=".$r_auto_id;
$query = "update hrm_employee set fname = '$fname', lname = 
'$lname', dob = '$dob', gender = '$gender', current_address = '$current_address', 
permanent_address = '$permanent_address', mobile1 = '$mobile1',
mobile2 = '$mobile2', email = '$email',
office_email = '$office_email', department_id = '$department_id',
designation_id = '$designation_id' where id = '$emp_id';";

  //echo $query;
  //echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));

// check the records are updated or not

//if(mysqli_affected_rows($conn)>0)
//{
    echo "Personal Info Updated";
//}
/*else
{
    echo "Family Member Already Added";
}*/
?>