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
	
	
	
				<strong>Select Locality</strong><br />
			
			<select name="locality_id_student" class="chosen-select" style="border:solid; border-color:#999999; height:48px; width:95%;" data-placeholder="Locality........" id="locality_id_student">

			  
                <option value="">Locality</option>
                <?php $query="select * from locality where city_id='$city_id' order by name";
			  $result=mysqli_query($con, $query) or die(mysqli_error($con));
			  while($row=mysqli_fetch_array($result))
			  { ?>
                <option <?php
				if(isset($_POST['id']))
				{
				 if($row_v['city_id']==$row['id']) echo "selected='selected'";
				 }
				 ?> value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                <?php } ?>
                <option value="0">Others</option>
              </select>
			
			
					

	
	
	