<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);

$emp_id = $_GET['emp_id'];
$office_email = $_GET['office_email'];
$email = $_GET['email'];
$current_address = $_GET['current_address'];
$permanent_address = $_GET['permanent_address'];
$house_type = $_GET['house_type'];
$staying_current_residence = $_GET['staying_current_residence'];
$living_current_city = $_GET['living_current_city'];
$facebook = $_GET['facebook'];
$twitter = $_GET['twitter'];
$linkedin = $_GET['linkedin'];

$date = date("Y-m-d");

//echo $_GET['rid']."========".$_GET['rt']."from ajax and emp_id = ".$emp_id."r_auto_id=".$r_auto_id;
$query = "update hrm_employee set office_email = '$office_email', email = 
'$email', current_address = '$current_address', permanent_address = '$permanent_address', 
 house_type = '$house_type',
 staying_current_residence = '$staying_current_residence', 
 living_current_city = '$living_current_city' where id = '$emp_id';";

  //echo $query;
  echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));


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