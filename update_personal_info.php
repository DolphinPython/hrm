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
$bgroup = $_GET['bgroup'];
$marital_status = $_GET['marital_status'];



//echo $_GET['rid']."========".$_GET['rt']."from ajax and emp_id = ".$emp_id."r_auto_id=".$r_auto_id;
$query = "update hrm_employee set fname = '$fname', lname = 
'$lname', dob = '$dob', gender = '$gender', bgroup = '$bgroup', marital_status = '$marital_status' 
where id = '$emp_id';";

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