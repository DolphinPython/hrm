<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);
//$emp_id = $_GET['emp_id'];
//name, date1, no_of_days, year
$title = $_GET['title'];
$description = $_GET['description'];
$emp_id = $_GET['emp_id'];
$employees = $_GET['employees'];
$date = date("d-m-Y");
$time = date("h:i:s");
$from_email = get_value("hrm_employee", "email", $emp_id); 
send_notification_mail($employees, $title, $description, $from_email);

if($title!="" and $description!="" and $employees!="")
{

//echo $_GET['rid']."========".$_GET['rt']."from ajax and emp_id = ".$emp_id."r_auto_id=".$r_auto_id;
$query = "insert into hrm_notification(title, description, sent_by, send_to, date, time)
values ('$title', '$description', '$emp_id', '$employees', '$date', '$time');";

  //echo $query;
//echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));

echo "Notification Sent";

}
else
{
echo "Not Sent";
}
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
?>