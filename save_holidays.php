<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);
//$emp_id = $_GET['emp_id'];
//name, date, no_of_days, year
$name = $_GET['name'];
$date = $_GET['date'];
$no_of_days = $_GET['no_of_days'];
$year = $_GET['year'];
$emp_id = $_GET['emp_id'];




//echo $_GET['rid']."========".$_GET['rt']."from ajax and emp_id = ".$emp_id."r_auto_id=".$r_auto_id;
$query = "insert into hrm_holidays(name, date, no_of_days, year, added_by)
values ('$name', '$date', '$no_of_days', '$year', '$emp_id');";

  //echo $query;
  //echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));

echo "Holidays Added";

// check the records are updated or not

//if(mysqli_affected_rows($conn)>0)
//{
  //$last_id = mysqli_insert_id($conn);
    //echo $last_id;
//}
/*else
{
    echo "Family Member Already Added";
}*/

if ($_SESSION['id'] != 10) {
    // echo "Unauthorized Access";
    exit;
}

?>