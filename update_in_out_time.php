<?php 
session_start();
include "include/function.php";
date_default_timezone_set("Asia/Kolkata");
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);
$updated_by = $_SESSION['id'];
$employee_id = $_GET['employee_id'];
$hrm_attandance_machine_detail_id = $_GET['hrm_attandance_machine_detail_id'];
$ids = $_GET['ids'];
$month = $_GET['month'];
$year = $_GET['year'];
$column_name = $_GET['column_name'];
$in_time = $_GET['in_time'];
$out_time = $_GET['out_time'];
$old_in_time = $_GET['old_in_time'];
$old_out_time = $_GET['old_out_time'];
$date = $_GET['date'];
$update_date_time = date("d-m-Y h:i:sa");





//echo $_GET['rid']."========".$_GET['rt']."from ajax and emp_id = ".$emp_id."r_auto_id=".$r_auto_id;
$query = "update hrm_attandance_machine_detail set  $column_name = '$in_time$out_time'
 where month='$month' and year='$year' and id='$hrm_attandance_machine_detail_id';";

  echo $query;
  //echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));

// check the records are updated or not

//if(mysqli_affected_rows($conn)>0)
//{
    //echo "In/Out Time Updated";
//}
/*else
{
    echo "Family Member Already Added";
}*/
$detail = "Attendance of ".get_value("hrm_employee", "fname", $employee_id)." for the date of ".$date." has been changed";
  $query = "insert into hrm_attandance_machine_update_detail(attandance_machine_id, 
  emp_id, attendance_id, update_by, new_in_time, new_out_time, old_in_time, 
  old_out_time, detail, update_date, year, month, change_for_date) 
  values('$hrm_attandance_machine_detail_id', '$employee_id', '$ids', 
  '$updated_by', '$in_time', '$out_time', '$old_in_time', '$old_out_time', '$detail', 
  '$update_date_time', '$year', '$month', '$date');";

  //echo $query;
  //echo $query;
  

  $result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));
  echo "Attendance Time Updated";

?>