<?php
// jai shiva
//include "../admin/easy_function.php";
session_start();
$id=$_SESSION['id'];
//echo "session id=".$id;
$class_id=get_value1("student", "keyword", "user_id", $id);
$subject_id=get_value1("student", "subject", "user_id", $id);
$city_id=get_value1("student", "city_id", "user_id", $id);
$locality_id=get_value1("student", "locality_id", "user_id", $id);
$tutor_type=get_value1("student", "tutor_type", "user_id", $id);
$student_tutor_type=get_value1("student", "tutor_type", "user_id", $id);
//echo get_value1($id, "student", "user_id", "keyword");
//get_value1($table, $column1, $column2, $id)
//{
$tutor_org_id=get_value1("hometutor", "id", "user_id", $_SESSION['id']);





$con=connect();
//$query="select $column1 from $table where $column2='$id'";
$url=url();
?>
<?php 
	  if($_SESSION['type']=="3")
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
<section class="content">  
      <div class="container-fluid">
       
	   
	   <div class="container-fluid">
        <h5 class="mb-2">Tutor Dashboard</h5>
        <div class="row">
		
		
		<div class="col-md-3 col-sm-6 col-12">
		  <a href="<?php echo $url.$search_url; ?>?r=slogin"> 
            <div class="info-box">
              <span class="info-box-icon bg-secondary"><i class="fas fa-search"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">FIND TUTOR JOBS / INTERESTED STUDENTS</span>
              </div>
              <!-- /.info-box-content -->
            </div>
			</a>
            <!-- /.info-box -->
          </div>
		
		
		
		
		
		 <div class="col-md-3 col-sm-6 col-12">
		  <a href="student-request.php"> 
            <div class="info-box">
              <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">STUDENT REQUEST</span>
              </div>
              <!-- /.info-box-content -->
            </div>
			</a>
            <!-- /.info-box -->
          </div>
		  
		  
		  
		  <div class="col-md-3 col-sm-6 col-12">
		  <a href="view-selected-student.php"> 
            <div class="info-box">
              <span class="info-box-icon bg-primary"><i class="fa fa-coins"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">TUTOR JOBS APPLIED</span>
              </div>
              <!-- /.info-box-content -->
            </div>
			</a>
            <!-- /.info-box -->
          </div>
		  
		  
		  
		  
		  
		  
          <!-- /.col -->
		
		
		
		
		 <div class="col-md-3 col-sm-6 col-12">
		  <a href="edit-profile-tutor.php?id=<?php echo $tutor_org_id; ?>"> 
            <div class="info-box">
              <span class="info-box-icon bg-success"><i class="far fa-edit"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">PROFILE DETAIL</span>
              </div>
              <!-- /.info-box-content -->
            </div>
			</a>
            <!-- /.info-box -->
          </div>
		  
		  
		  
		  		
		 <div class="col-md-3 col-sm-6 col-12">
		  <a href="contact-as-tutor.php"> 
            <div class="info-box">
              <span class="info-box-icon bg-warning"><i class="fa fa-hands-helping"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">CONTACT AND SUPPORT</span>
              </div>
              <!-- /.info-box-content -->
            </div>
			</a>
            <!-- /.info-box -->
          </div>
		
		
		
				  
		  
		  
		  
		  
		  
		  
		  
          

          <!-- /.col -->
        </div>
        <!-- /.row -->
	   
	 </div><!-- /.container-fluid -->
   </section>
<?php }			
if($_SESSION['type']=="8")
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
	$class_name=str_replace(",", "", $class_name);					
	$city_name=get_value("city", "name", $city_id);	
	$location_name=str_replace(" ", "-", $location_name);			
	
//	find-home-tutors/home-tutors-Class-1-12-All-Subjects-A.Babhangama-Darbhanga/1/6/1/1/l
	//echo $class_name;
	
	if($location_id=="")
	{
		$location_id=0;
	}
	
	$search_url="home-tutors/".$class_name."-".$location_name."-".$city_name."/".$keyword."/".$city_id."/".$tutor_type."/".$location_id."/l";
//	header

//http://localhost/tok8/home-tutors/Class-1-12-All-Subjects-A.Babhangama-Darbhanga/1/6/1/1/l?r=slogin
//http://tuitionok.com/home-tutors/Class-1-12-All-Subjects-A.Babhangama-Darbhanga/1/6/1/1/l?r=slogin
	
	//class_id=$2&city_id=$3&tutor_type=$4&location_id=$5&t=$6 [L]
	


	
	?>  




        <div class="row">
		
		
		
			<div class="col-md-3 col-sm-6 col-12">
		<a href="tutor-request.php">
            <div class="info-box">
				<span class="info-box-icon bg-success"><i class="fa fa-hand-holding"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">Tutor Offer</span>
              </div>
              <!-- /.info-box-content -->
            </div>
		</a>
            <!-- /.info-box -->
          </div>
		  
          <!-- /.col -->
		
		
		
		
		
		
		
		
		
		
		
		
		
		<div class="col-md-3 col-sm-6 col-12">
		<a href="<?php echo $url.$search_url; ?>?r=slogin">
            <div class="info-box">
				<span class="info-box-icon bg-info"><i class="fa fa-search"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">Search and Select Tutor</span>
              </div>
              <!-- /.info-box-content -->
            </div>
		</a>
            <!-- /.info-box -->
          </div>
		  
          <!-- /.col -->
		  
		  
		  
		  <div class="col-md-3 col-sm-6 col-12" style="display:none;">
		<a href="student-search-criteria.php">
            <div class="info-box">
				<span class="info-box-icon bg-success"><i class="far fa-edit"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">Search History</span>
              </div>
              <!-- /.info-box-content -->
            </div>
		</a>
            <!-- /.info-box -->
          </div>
		  
          <!-- /.col -->
		  
		  
		
		
		 <div class="col-md-3 col-sm-6 col-12">
		  <a href="edit-student-profile.php"> 
            <div class="info-box">
              <span class="info-box-icon bg-danger"><i class="far fa-edit"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Manage Profile</span>
                <span class="info-box-number"></span>
              </div>
              <!-- /.info-box-content -->
            </div>
			</a>
            <!-- /.info-box -->
          </div>
		  
          <!-- /.col -->
		
		
		
          <div class="col-md-3 col-sm-6 col-12">
		  <a href="view-selected-tutor.php"> 
            <div class="info-box">
              <span class="info-box-icon bg-primary"><i class="fa fa-search"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">View Selected Tutor</span>
                <span class="info-box-number"></span>
              </div>
              <!-- /.info-box-content -->
            </div>
			</a>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->     
		  
		  
		  <!-- /.col -->
		
		
		
          <div class="col-md-3 col-sm-6 col-12">
		  <a href="#"> 
            <div class="info-box">
              <span class="info-box-icon bg-warning"><i class="fa fa-thumbs-up"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">SUBSCRIPTION</span>
                <span class="info-box-number"></span>
              </div>
              <!-- /.info-box-content -->
            </div>
			</a>
            <!-- /.info-box -->
          </div>
          <!-- /.col --> 
		  
		  
		  
		  <div class="col-md-3 col-sm-6 col-12">
		  <a href="contact-as-student.php"> 
            <div class="info-box">
              <span class="info-box-icon bg-secondary"><i class="fa fa-handshake"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">HELP AND SUPPORT</span>
                <span class="info-box-number"></span>
              </div>
              <!-- /.info-box-content -->
            </div>
			</a>
            <!-- /.info-box -->
          </div>
          <!-- /.col --> 
		  
		  
		  
		  
		  
		  
		  
		  
		       
          
        </div>
        <!-- /.row -->
	   
	   
	   
       


      </div><!-- /.container-fluid -->
	  
	  
	 
	  
	  
    </section>
<?php } ?>
	
	