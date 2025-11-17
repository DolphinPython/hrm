<?php
// jai shiva
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
date_default_timezone_set('Asia/Kolkata');
include 'layouts/config.php';
//$con=connect();
$date = date("Y-m-d H:i:s"); 

if(isset($_POST['b1']))
{
	$email=mysqli_real_escape_string($con, $_POST['email']);
	//$password=mysqli_real_escape_string($con, md5($_POST['password']));
	$password=mysqli_real_escape_string($con, $_POST['password']);

	//$query="SELECT * FROM users1 WHERE email ='$email' AND password ='$password' AND status = '1'";
	$query="SELECT * FROM hrm_employee WHERE email ='$email' AND password ='$password' AND (status = '1')";

	//echo $query;
	//echo $query;
	//echo $query;
	$result=mysqli_query($con,$query) or die(mysqli_error($con));
	$row=mysqli_fetch_array($result);
	if($row['id']!="")
	{
		// save in login detail table
		$query_insert="insert into hrm_login_detail(date_time, emp_id)
		 values('$date', '$row[id]')";
		$result_insert=mysqli_query($con,$query_insert) or die(mysqli_error($con));
		// save in login detail table
		//echo "aaaaaaaaaaaa";
	//$_SESSION["logintype"] = $row['who'];
	$_SESSION["id"] =  $row['id'];
	$_SESSION["email"] = $row['email'];
	$_SESSION["password"] = $row['password'];  
	
	//$token = getRandomNumber();
	$_SESSION["token"] = rand();

	$designation_id=$row['designation_id'];
$department_id=$row['department_id'];
	//echo "aaaaaaaaaaaa";              
	//  $updateemp = mysqli_query($con, "update ds_employees set emp_last_access = '$date' where emp_id = '$row[emp_id]'");
			// $loginemp = mysqli_query($con, "insert into dc_emp_login (emp_id,login_time,logout_time,token) 
			// values ('$row[emp_id]','$date','$date','$token')");
	//echo "aaaaaaaaaaaa";	
	//$department_employee = array(2,3);
	//$department_admin = array(2,3);
		if($row['department_id']==4 or $row['department_id']==6)
		{ 
			//echo $row['department_id'];
			// if department and designation is hr
			?>
			<script language="javascript">
			window.location="admin-dashboard.php";
			//alert('tutor-dashboard.php');
			</script>
			<?php 
		}
		else
		{
			?>
			<script language="javascript">
			window.location="employee-dashboard.php";
			//alert('tutor-dashboard.php');
			</script>
			<?php 
		}
		
	}
	else
	{
	?>
	<script language="javascript">
		alert("Wrong User or password. Try forgot password or contact hr");
		history.go(-1);
	</script>
	<?php
	}
}

?>