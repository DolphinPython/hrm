<?php
// jai shiva 
//$id=$_SESSION["id"];
/*if($emp_id == '10' || $emp_id == '52' || $emp_id == '217' || $emp_id == '373' )
{
  $access = 1;
}
else
{
  $access="";
}
*/  
//include "function.php";
$admin_url=admin_url();
//$student_url=student_url();
//$tutor_url=tutor_url();
//$dashboard_url=dashboard_url();

$url=url();
if(!isset($_SESSION['type']))
{
$_SESSION['type']="";
}


$user_id1=$_SESSION['id'];

$hometutor_org_id1=get_value1("hometutor", "id", "user_id", $user_id1);
?>
<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
  
  
   <?php 
   
	  if(($_SESSION['type']=="3" or $_SESSION['id']=="1") and $_SESSION['type']!="8")
	  {
	  ?>  
    <!-- Brand Logo --><?php //echo $_SESSION['type']; ?>
    <a href="<?php echo $admin_url;?>tutor-dashboard.php" class="brand-link">
      <img src="<?php echo $admin_url;?>dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
	  
      <span class="brand-text font-weight-light"><?php if($_SESSION['type']=="3" and $_SESSION['type']!="8") echo "Tutor Dashboard"; if($_SESSION['type']=="8" and $_SESSION['type']!="3") echo "Student Dashboard"; if($_SESSION['type']!="3" and $_SESSION['type']!="8") echo "Admin Dashboard"; ?> </span>
	  
    </a>
	<?php } ?>
	 <?php 
	  if($_SESSION['type']=="8" and $_SESSION['type']!="3")
	  {
	  ?>  
    <!-- Brand Logo --><?php //echo $_SESSION['type']; ?>
    <a href="<?php echo $admin_url;?>student-dashboard.php" class="brand-link">
      <img src="<?php echo $admin_url;?>dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
	  
      <span class="brand-text font-weight-light"><?php if($_SESSION['type']=="3") echo "Tutor Dashboard"; if($_SESSION['type']=="8") echo "Go To Dashboard"; else echo "Admin Dashboard"; ?> </span>
	  
    </a>
	<?php } ?>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      



      <!-- Sidebar Menu -->
	  
	  <?php 
	  
	  if($_SESSION['type']=="3" and $_SESSION['type']!="8")
	  {
	  
	  
	  
	$tutor_user_id=$_SESSION['id'];
	$query_search_tutor="select * from hometutor where user_id='$tutor_user_id';";
	//echo "query_search1========".$query_search_tutor;	
	//echo "query_search1========".$query_search1;	
	$result_search_tutor=mysqli_query($con,$query_search_tutor) or die(mysqli_error($con));
	$row_search_tutor=mysqli_fetch_array($result_search_tutor);
	$city_id=$row_search_tutor['city_id'];
	$keyword=$row_search_tutor['keyword'];	
	$location_id=$row_search_tutor['locality_id'];
	$tutor_type=$row_search_tutor['tutor_type'];
	
	$location_name=get_value("locality", "name", $location_id);
	$class_name=get_value("keywords", "name", $keyword);
	$class_name=str_replace(" ", "-", $class_name);		
	$city_name=get_value("city", "name", $city_id);	
	
//	find-home-tutors/home-tutors-Class-1-12-All-Subjects-A.Babhangama-Darbhanga/1/6/1/1/l
	
	//	header

	if($tutor_type==1) $tutor_type1="home-tutor";
			  else if($tutor_type==2) $tutor_type1="online-tutor";
				  else if($tutor_type==3) $tutor_type1="home-tutor-and-online-tutor";
				  else if($tutor_type==4) $tutor_type1="taching-at-own-place";
				  else if($tutor_type==5) $tutor_type1="home-online-and-teaching-at-own-place";	
	
	$search_url="tutor-jobs/".$tutor_type1."-in-".$city_name."/".$city_id."/".$tutor_type;
	  
	  
	  
	  
	  
	  ?>
	  
	  
	  <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo $admin_url; ?>dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="tutor-dashboard.php" class="d-block"><?php echo $_SESSION['name']; ?></a>
        </div>
      </div>
	  
	  
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
			
			
			<li class="nav-item">
                <a href="<?php echo $url.$search_url; ?>?r=slogin" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>FIND TUTOR JOBS / INTERESTED STUDENTS</p>
                </a>
              </li>
			
			
              <li class="nav-item">
                <a href="student-request.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>STUDENT REQUEST</p>
                </a>
              </li>
             
			  <li class="nav-item">
                <a href="view-selected-student.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>TUTOR JOBS APPLIED</p>
                </a>
              </li>
		             
			  
			  <li class="nav-item">
                <a href="edit-profile-tutor.php?id=<?php echo $hometutor_org_id1; ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PROFILE DETAIL</p>
                </a>
              </li>
             
             			  
			   <li class="nav-item">
                <a href="contact-as-tutor.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>CONTACT AND SUPPORT</p>
                </a>
              </li>
			  
		
						  
            </ul>
          </li>
          
        </ul>
      </nav>
	  <?php } ?>
	  <?php 
	  if($_SESSION['type']=="8" and $_SESSION['type']!="3")
	  {
	  
	  
	  //echo $_SESSION['id'];
	$student_user_id=$_SESSION['id'];
	$query_search_tutor="select * from student where user_id='$student_user_id';";
	//echo "query_search1========".$query_search_tutor;	
	//echo "query_search1========".$query_search1;	
	$result_search_tutor=mysqli_query($con,$query_search_tutor) or die(mysqli_error($con));
	$row_search_tutor=mysqli_fetch_array($result_search_tutor);
	$city_id=$row_search_tutor['city_id'];
	$keyword=$row_search_tutor['keyword'];	
	$location_id=$row_search_tutor['locality_id'];
	$tutor_type=$row_search_tutor['tutor_type'];
	
	$location_name=get_value("locality", "name", $location_id);
	$class_name=get_value("keywords", "name", $keyword);
	$class_name=str_replace(" ", "-", $class_name);		
	$city_name=get_value("city", "name", $city_id);	
	
//	find-home-tutors/home-tutors-Class-1-12-All-Subjects-A.Babhangama-Darbhanga/1/6/1/1/l
	
	  
	if($location_id=="")
	{
		$location_id=0;
	}
	
	$search_url="home-tutors/".$class_name."-".$location_name."-".$city_name."/".$keyword."/".$city_id."/".$tutor_type."/".$location_id."/l";
	  
	  
	  
	  ?>
	   <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo $admin_url;?>dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="student-dashboard.php" class="d-block"><?php echo $_SESSION['name']; ?></a>
        </div>
      </div>
	  
	  <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="student-dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
			
			
			<li class="nav-item">
                <a href="tutor-request.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>TUTOR OFFER</p>
                </a>
              </li>
			
			
              <li class="nav-item">
                <a href="<?php echo $url.$search_url; ?>?r=slogin" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SEARCH AND SELECT TUTOR</p>
                </a>
              </li>
			  
			   <li class="nav-item">
                <a href="edit-student-profile.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>MANAGE PROFILE</p>
                </a>
              </li>
             
			 
			  <li class="nav-item">
                <a href="view-selected-tutor.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>VIEW SELECTED TUTOR</p>
                </a>
              </li>
             
             
			  
			 <li class="nav-item">
                <a href="student-package-detail.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SUBSCRIPTION</p>
                </a>
             </li>
			 
			 
			  <li class="nav-item">
                <a href="contact-as-student.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>HELP AND SUPPORT</p>
                </a>
             </li>
             
             			  
			 
			  
            </ul>
          </li>
          
        </ul>
      </nav>
	   <?php } 
	  if($_SESSION['type']!="8" and $_SESSION['type']!="3")
	  {
	  ?>
	  <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo $admin_url;?>dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="student-dashboard.php" class="d-block"><?php if(isset($_SESSION['name'])){ echo $_SESSION['name']; } ?></a>
        </div>
      </div>
	  <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
			
			 <?php if($_SESSION["user_type"]=="admin")
	  { ?> 
			
              <li class="nav-item">
                <a href="add-keyword.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ADD CLASS AND SUBJECTS</p>
                </a>
              </li>
             
			 
			  <li class="nav-item">
                <a href="add-student-class.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ADD STUDENT CLASS</p>
                </a>
              </li>
             
             
			  
			  <li class="nav-item">
                <a href="add-subject.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ADD SUBJECT</p>
                </a>
              </li>
	<?php } ?>             
    <?php if($_SESSION["user_type"]=="admin_user" or $_SESSION["user_type"]=="admin")
	  { ?>       			  
			   <li class="nav-item">
                <a href="add-hometutor.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ADD HOME TUTOR</p>
                </a>
              </li>
			  
			   <li class="nav-item">
                <a href="view-hometutor.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>VIEW HOME TUTOR</p>
                </a>
              </li>
	<?php } ?>			
	<?php if($_SESSION["user_type"]=="admin")
	  { ?>			
			 <li class="nav-item">
                <a href="add-country.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ADD COUNTRY</p>
                </a>
              </li>
			  
			  
			  
			  
			  <li class="nav-item">
                <a href="add-state.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ADD STATE</p>
                </a>
              </li>
			  
			  
			  
			  
			  <li class="nav-item">
                <a href="add-city.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ADD CITY</p>
                </a>
              </li>
			  
		<?php } ?>			
			 <?php if($_SESSION["user_type"]=="admin_user" or $_SESSION["user_type"]=="admin")
	  { ?>
			
			<li class="nav-item">
                <a href="add-locality.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ADD LOCALITY</p>
                </a>
              </li>
			
	<?php } ?>
	<?php if($_SESSION["user_type"]=="admin")
	  { ?> 	
				<li class="nav-item">
                <a href="add-enquiry.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>VIEW ENQUIRY</p>
                </a>
              </li>
			  
			  
			<li class="nav-item">
                <a href="add-other-enquiry.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>VIEW OTHER ENQUIRY</p>
                </a>
              </li>
			  
			  
			<li class="nav-item">
                <a href="add-user.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ADD USER</p>
                </a>
              </li>
		<?php } ?>	
			
			
			  
            </ul>
          </li>
          
        </ul>
      </nav>
	  <?php } ?>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>