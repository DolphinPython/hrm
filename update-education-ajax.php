<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);
$emp_id = $_GET['emp_id'];
$qualification_type = $_GET['qualification_type'];
$course_name = $_GET['course_name'];
$course_type = $_GET['course_type'];
$stream = $_GET['stream'];
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
$college_name = $_GET['college_name'];
$university_name = $_GET['university_name'];
$grade = $_GET['grade'];
//echo $_GET['rid']."========".$_GET['rt']."from ajax and emp_id = ".$emp_id."r_auto_id=".$r_auto_id;
$query = "insert into hrm_employee_education(emp_id, qualification_type, course_name, course_type, 
stream, start_date, end_date, college_name, university_name, grade) 
values
('$emp_id', '$qualification_type', '$course_name', '$course_type', '$stream', '$start_date', '$end_date',
 '$college_name', '$university_name', '$grade')";
  //echo $query;
  //echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));

// check the records are updated or not

//if(mysqli_affected_rows($conn)>0)
//{
    echo "Education Updated";
//}
//else
//{
//    echo "Education Already Saved";
//}
?>