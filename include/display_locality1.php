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
if(isset($_GET['hometutor_org_id']))
{
	$hometutor_org_id=mysqli_real_escape_string($con, strip_tags($_GET['hometutor_org_id']));
}	


//echo $city_id;
if(isset($_GET['id']))
{
			$id=mysqli_real_escape_string($con, strip_tags($_GET['id']));
}
//echo "aaaaaaaaaaaaaaa";
//$query="select * from locality where city_id='$city_id' order by name";
//echo $query;


?>

	
	<?php $locality_id=$_GET['locality_id'];
$locality_id1=$_GET['locality_id'];

 ?>
	
	
	
	<div class="title2small">
	<strong>Please Select All Location / Area. Where You Can Teach.</strong>
	</div>
	<br />

 <input type="text" id="mySearch2" onkeyup="myFunction2()" placeholder="Type To Search Location........" title="Type to search location." class="form-control"><br />

					

	
	<div style="overflow:scroll; height:298px;">
	<ul id="myMenu2">
	<div class="row paddings1">

	
	<?php 
		
	$locality_id = $locality_id;
	$locality_id8 = $locality_id;
	$locality_id = explode (",", $locality_id); 
	//print_r($locality_id);
	/*if (in_array(68, $locality_id))
	{
	 echo "Match found";
	}
	else
	{
	  echo "Match not found";
	}*/
	$all_location="";
	//echo "llllllll=".$locality_id1;
	
				  if($locality_id1=="") { $all_location="checked='checked'";  }

			  ?>
			  
			  
			  <label><li style="list-style:none; cursor:pointer;"><input type="checkbox" name="locality[]"  value=""
			  
			<?php echo $all_location; ?> />
			  			  
			  
			  
			  &nbsp; &nbsp;<a style="color:#666666;" class="bg-info title2small">All Location<?php //echo $row['id']; ?></a>&nbsp; &nbsp;</li></label>  
			  
			  
			  
			  
			  
			  
			  
			  
			  <?php 
			  $query="select * from locality where city_id='$city_id' order by name";
			  $result=mysqli_query($con,$query) or die(mysqli_error($con));
			  $x=0;
			  while($row=mysqli_fetch_array($result))
			  { 
			 // if($row['id']==REGEXP '(^|,)$x(,|$)' )
			  
				//echo $x;
			  
			  ?>
			  <label><li style="list-style:none; cursor:pointer;"><input type="checkbox" name="locality[]"  value="<?php echo $row['id']; ?>" <?php 
			 //if($locality_id8!=""){
			  if(in_array($row['id'], $locality_id)) { echo "checked='checked'"; }
			  //}  ?>  />
			  
			  
			  
			  &nbsp; &nbsp;<a style="color:#666666;" class="bg-info title2small"><?php echo $row['name']; ?><?php //echo $row['id']; ?></a>&nbsp; &nbsp;</li></label>
                <?php $x++;
				//if($x%2==0) echo '</div><div class="row">';
				} ?>
	</div>						
	</ul>					
	</div>
	
	
	<script>
function myFunction2() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("mySearch2");
  filter = input.value.toUpperCase();
  ul = document.getElementById("myMenu2");
  li = ul.getElementsByTagName("li");
  for (i = 0; i < li.length; i++) {
    a = li[i].getElementsByTagName("a")[0];
    if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
}
</script>