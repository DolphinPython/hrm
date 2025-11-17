<?php
// jai shiva
//session_start(); 	
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//include "connect.php";
include "function.php";
date_default_timezone_set('Asia/Colombo');
//$city_id=$_GET['city_id'];
//$user_id=$_SESSION['ids'];
$con=connect();
//echo $_GET['city_id'];
$city_id=mysqli_real_escape_string($con, strip_tags($_GET['city_id']));
if(isset($_GET['locality_id']))
{
	$locality_id=mysqli_real_escape_string($con, strip_tags($_GET['locality_id']));
}		
//echo $city_id;
if(isset($_GET['id']))
{
			$id=mysqli_real_escape_string($con, strip_tags($_GET['id']));
}
$url=url();
//echo "aaaaaaaaaaaaaaa";
//$query="select * from locality where city_id='$city_id' order by name";
//echo $query;
?>

	

	
	
			
			<select name="locality_id"   style="border:solid; border-color:#999999; height:35px; width:100%;" id="locality_id">

			  
                <option value="">Locality</option>
                <?php $query="select * from locality where city_id='$city_id' order by name";
			  $result=mysqli_query($con, $query) or die(mysqli_error($con));
			  while($row=mysqli_fetch_array($result))
			  { ?>
                <option <?php
				if(isset($_GET['locality_id']))
				{
				 if($locality_id==$row['id']) echo "selected='selected'";
				 }
				 ?> value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                <?php } ?>
                <option value="0">Others</option>
              </select>
			
			
					