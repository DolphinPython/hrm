<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);
$emp_id = $_GET['emp_id'];
$bank_name = $_GET['bank_name'];
$account_number = $_GET['account_number'];
$ifsc = $_GET['ifsc'];
$pan = $_GET['pan'];

// check if bank detail added or not
$query_check = " select * from hrm_bank_detail where emp_id = '$emp_id';";

  //echo $query;
  //echo $query;
  

$result_check = mysqli_query($conn, $query_check) or die(mysqli_error($conn, "insert error"));


// check if bank detail added or not
if(mysqli_num_rows($result_check)>0)
{
//echo $_GET['rid']."========".$_GET['rt']."from ajax and emp_id = ".$emp_id."r_auto_id=".$r_auto_id;
$query = "update hrm_bank_detail set bank_name = '$bank_name', account_number = 
'$account_number', ifsc = '$ifsc', pan = '$pan' where emp_id = '$emp_id';";

  //echo $query;
  //echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));

// check the records are updated or not

//if(mysqli_affected_rows($conn)>0)
//{
    echo "Bank Information Updated";
//}
/*else
{
    echo "Family Member Already Added";
}*/
}
else
{
  $query = "insert into hrm_bank_detail(bank_name, account_number, ifsc, pan, emp_id) 
  values('$bank_name', '$account_number', '$ifsc', '$pan', '$emp_id');";

  //echo $query;
  //echo $query;
  

  $result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));
  echo "Bank Information Updated";
}
?>