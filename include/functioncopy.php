<?php
// jai shiva
//session_start();
//include/dbconnection.php;
date_default_timezone_set('Asia/Colombo');
//date_default_timezone_set('Asia/Kolkata');
include "db.php";
function url()
{
	if($_SERVER['HTTP_HOST']=="localhost")
			{
			$url="http://localhost/hrm/";
			}
			else
			{
				$url="https://www.1solutions.biz/";
			}
		return $url;
}


function getWorkingDays($startDate,$endDate,$holidays){
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)

        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
}
function last_login_time($emp_id)
{
	$conn=connect();
	$query="select * from hrm_login_detail where emp_id='$emp_id';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	//$x="";
	$login_date_time="";
	while($row=mysqli_fetch_array($result))
	{
		$login_date_time = $row['date_time'];
	}
	return $login_date_time;
}

function total_late_in_current_month($emp_id, $current_month, $current_year)
{
	$conn=connect();
	$query="select * from hrm_attandance_machine_detail join hrm_employee on 
	hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id  
	where hrm_employee.id='$emp_id' and hrm_attandance_machine_detail.month='$current_month' 
	and hrm_attandance_machine_detail.year='$current_year';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row=mysqli_fetch_array($result);
	$count_late = 0;
	if(mysqli_num_rows($result)>0)
	{
		
		for($x=1; $x<=31; $x++)
		{
			if($row['date_in_out'.$x]!="")
			{

				${"date_in_out".$x."_in"} = substr($row['date_in_out'.$x], 0,5); 
				${"date_in_out".$x."_out"} = substr($row['date_in_out'.$x], -5); 
				

				${"date_in_out".$x."_in"}  = new DateTime(${"date_in_out".$x."_in"});
				${"date_in_out".$x."_out"} = new DateTime(${"date_in_out".$x."_out"});
				
				${"date_in_out".$x} = ${"date_in_out".$x."_in"}->diff( ${"date_in_out".$x."_out"} );

				if(${"date_in_out".$x}->format( '%H:%I:%S' )<"09:30")
				$count_late= $count_late+1;
			}
		}	
		return $count_late;
	}
	else
	{
		return $count_late;
	}
}
function total_in_time_late_in_current_month($emp_id, $current_month, $current_year)
{
	$conn=connect();
	$query="select * from hrm_attandance_machine_detail join hrm_employee on 
	hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id  
	where hrm_employee.id='$emp_id' and hrm_attandance_machine_detail.month='$current_month' 
	and hrm_attandance_machine_detail.year='$current_year';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row=mysqli_fetch_array($result);

	$count_in_late = 0;
	if(mysqli_num_rows($result)>0)
	{
	for($x=1; $x<=31; $x++)
	{
		if($row['date_in_out'.$x]!="")
		{

			${"date_in_out".$x."_in"} = substr($row['date_in_out'.$x], 0,5); 
			//${"date_in_out".$x."_in"}  = new DateTime(${"date_in_out".$x."_in"});
			

			if(${"date_in_out".$x."_in"}>"09:00")
			$count_in_late=$count_in_late+1;
		}
	}	
}
	return $count_in_late;

}

function total_out_time_late_in_current_month($emp_id, $current_month, $current_year)
{
	$conn=connect();
	$query="select * from hrm_attandance_machine_detail join hrm_employee on 
	hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id  
	where hrm_employee.id='$emp_id' and hrm_attandance_machine_detail.month='$current_month' 
	and hrm_attandance_machine_detail.year='$current_year';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row=mysqli_fetch_array($result);

	$count_out_late = 0;
	if(mysqli_num_rows($result)>0)
	{
	for($x=1; $x<=31; $x++)
	{
		if($row['date_in_out'.$x]!="")
		{
			if(substr($row['date_in_out'.$x], 5,10)!="")
			{

				${"date_in_out".$x."_in"} = substr($row['date_in_out'.$x], 5,10); 
				//${"date_in_out".$x."_in"}  = new DateTime(${"date_in_out".$x."_in"});
				

				if(${"date_in_out".$x."_in"}<"18:30")
				$count_out_late=$count_out_late+1;
			}
		}
	}	
}
	return $count_out_late;
}


function total_days_present_in_current_month($emp_id, $current_month, $current_year)
{
	$conn=connect();
	$query="select * from hrm_attandance_machine_detail join hrm_employee on 
	hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id  
	where hrm_employee.id='$emp_id' and hrm_attandance_machine_detail.month='$current_month' 
	and hrm_attandance_machine_detail.year='$current_year';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row=mysqli_fetch_array($result);

	$total_present = 0;

	if(mysqli_num_rows($result)>0)
	{
		for($x=1; $x<=31; $x++)
		{
			if($row['date_in_out'.$x]!="")
			{

				
				$total_present=$total_present+1;
			}
		}	
		return $total_present;
	}
	else
	{
		return $total_present;
	}
}


function total_days_abscent_in_current_month($emp_id, $current_month, $current_year)
{
	$conn=connect();
	$query="select * from hrm_attandance_machine_detail join hrm_employee on 
	hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id  
	where hrm_employee.id='$emp_id' and hrm_attandance_machine_detail.month='$current_month' 
	and hrm_attandance_machine_detail.year='$current_year';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row=mysqli_fetch_array($result);

	$total_present = 0;


	if(mysqli_num_rows($result)>0)
	{
		for($x=1; $x<=31; $x++)
		{
			if($row['date_in_out'.$x]!="")
			{

				
				$total_present=$total_present+1;
			}
		}	
		$paid_leave = 1;
		$total_abscent = 22-$total_present;
		$total_abscent = $total_abscent-$paid_leave;
		return $total_abscent;
	}
	else
	{
		return $total_present;
	}

}

function total_out_time_missing_in_current_month($emp_id, $current_month, $current_year)
{
	$conn=connect();
	$query="select * from hrm_attandance_machine_detail join hrm_employee on 
	hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id  
	where hrm_employee.id='$emp_id' and hrm_attandance_machine_detail.month='$current_month' 
	and hrm_attandance_machine_detail.year='$current_year';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row=mysqli_fetch_array($result);

	$count_out_time_missing = 0;
	if(mysqli_num_rows($result)>0)
	{
	for($x=1; $x<=31; $x++)
	{
		if($row['date_in_out'.$x]!="")
		{
			if(substr($row['date_in_out'.$x], 5,10)=="")
			{				
				$count_out_time_missing=$count_out_time_missing+1;
			}
		}
	}	
}
	return $count_out_time_missing;
}

function total_minute_late_in_current_month($emp_id, $current_month, $current_year)
{
	$conn=connect();
	$query="select * from hrm_attandance_machine_detail join hrm_employee on 
	hrm_attandance_machine_detail.attandance_id = hrm_employee.attendance_id  
	where hrm_employee.id='$emp_id' and hrm_attandance_machine_detail.month='$current_month' 
	and hrm_attandance_machine_detail.year='$current_year';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row=mysqli_fetch_array($result);

	$count_late = 0;
	$total_minute = 0;
	$time_difference = 0;
	$date_in_out11_out = "";
	for($x=1; $x<=31; $x++)
	{
		if($row['date_in_out'.$x]!="")
		{

			$value =  strval($row['date_in_out'.$x]);

			//$a = array_map('strval', $a);
			$date_in_out11_in = substr($value, 0,5); 
			$date_in_out11_out = substr($value, -5); 

			//$date_in_out11_in = "$date_in_out11_in";
			//$date_in_out11_out = "$date_in_out11_out";

			
				if($date_in_out11_out!="")
				{
				$to_time = strtotime($date_in_out11_in);
				$from_time = strtotime($date_in_out11_out);
				$total_time = 570;
				//echo $total_time-round(abs($to_time - $from_time) / 60,2). " minute<br>";
				//echo round(abs($to_time - $from_time) / 60,2). " minute<br>";

				$time_difference = $time_difference + ($total_time- (round(abs($to_time - $from_time) / 60,2)));
				//echo round(abs($to_time - $from_time) / 60,2);
				echo "<br>Time Difference=".$time_difference."<br>";
				echo "<br>Perday Late = ".$date_in_out11_in.$date_in_out11_out."=".$total_time-(round(abs($to_time - $from_time) / 60,2))."<br>";
				}	
				//echo $date_in_out11_in.$date_in_out11_out;
		

				//echo var_dump($date_in_out11_out);
				

				/*$first  = new DateTime( $date_in_out11_in );
				$second = new DateTime( $date_in_out11_out );
				$diff = $first->diff( $second );
				$hour = $diff->format( '%H:%i' ); 
				$time = explode(':', $hour);
				$d1= ($time[0]*60) + ($time[1]);
				return $d1;*/


			
			
		}
	}	
	return "<br>Total Minute Late = ".$time_difference;
	
}

/*function admin_url()
{
	if($_SERVER['HTTP_HOST']=="localhost")
			{
			$url="http://localhost/tok8/deleted/";
			}
			else
			{
				$url="https://msofth.com/deleted/";
			}
		return $url;
}
function student_url()
{
	if($_SERVER['HTTP_HOST']=="localhost")
			{
			$url="http://localhost/tok8/deleted/student/";
			}
			else
			{
				$url="https://msofth.com/deleted/student/";
			}
		return $url;
}
function tutor_url()
{
	if($_SERVER['HTTP_HOST']=="localhost")
			{
			$url="http://localhost/tok8/deleted/tutor/";
			}
			else
			{
				$url="https://mvgm.in/deleted/tutor/";
			}
		return $url;
}*/

function send_notification_mail($employees, $title, $description, $from_email)
{
	//email
    $to = "pythondolphin@gmail.com, swdpankaj@gmail.com";

    $subject = 'HR-Notification';
    
    $headers  = "From: " . strip_tags($from_email) . "\r\n";
    $headers .= "Reply-To: " . strip_tags($from_email) . "\r\n";
    //$headers .= "CC: pythondolphin@gmail.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $message = '<p><strong> '.$title.'</strong><br><br> '.$description.
    '</p>';
    
    
    mail($to, $subject, $message, $headers);
    //email
}
function check_login()
{
  if(!isset($_SESSION["id"]))
  {
		header("Location: logout.php");
		//echo "not login";
  }
  /*if(strlen($_SESSION['id'])==0)
{	
		//header("Location: logout.php");
	}*/
}

function is_login()
{
  if(isset($_SESSION["id"]))
  
		return true;
 else 
 
 return false;
}
function display_all_values_in($table, $column, $ids)
{
	$conn=connect();
	$query="select $column from $table where id in ($ids);";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$x="";
	while($row=mysqli_fetch_array($result))
	{
		$x=$x.$row['name'].", ";
	}
	return rtrim($x, ", ");
}


function display_leave_by_type($leave_type_id, $emp_session_id)
{
	$conn=connect();

	$query="select * from hrm_leave_applied where emp_id = '$emp_session_id' 
	and leave_type_id = '$leave_type_id';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	
	$total = 0;
	while($row=mysqli_fetch_array($result))
	{
		$total = $total+$row['no_of_days'];
	}
	return $total;
}

function total_leave_by_id($emp_id)
{
	$conn=connect();

	$query="select * from hrm_leave_applied where emp_id = '$emp_id';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$total = 0;
	while($row=mysqli_fetch_array($result))
	{
		$total = $total+$row['no_of_days'];
	}
	return $total;
}

function leave_status_count($status)
{
	$conn=connect();

	$query="select * from hrm_leave_applied where status = '$status';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$total = 0;
	return mysqli_num_rows($result);
}




function remaining_leave($leave_type_id, $emp_session_id)
{
	$conn=connect();

	$query="select * from hrm_leave_applied where emp_id = '$emp_session_id' 
	and leave_type_id = '$leave_type_id';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$total = 0;
	while($row=mysqli_fetch_array($result))
	{
		$total = $total+$row['no_of_days'];
	}
	
	//$total_leave_taken = display_leave_by_type(1, $emp_session_id);
	$remaining_leave = 12-$total;		
	return $remaining_leave;

}



function count_where($table, $column, $value)
{
	$conn=connect();
	$query="select * from $table where $column='$value'";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	//$row=mysqli_fetch_array($result);
	return mysqli_num_rows($result);
}
function get_value($table, $column, $id)
{
	$conn=connect();
	$query="select $column from $table where id='$id'";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$x="";
	$row=mysqli_fetch_array($result);
	return $row[$column];
}
function get_value1($table, $column1, $column2, $id)
{

	$conn=connect();
	$query="select $column1 from $table where $column2='$id'";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	if(mysqli_num_rows($result)>0)
	{
	$row=mysqli_fetch_array($result);
	return $row[$column1];
	}
	else
	{
		return "";
	}
}
function get_value1_last($table, $column1, $column2, $id)
{

	$conn=connect();
	$query="select $column1 from $table where $column2='$id'";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	while($row=mysqli_fetch_array($result))
	{
		$value=$row[$column1];
	}
	return $value;
}

function get_user_detail($emp_id)
{
	$conn=connect();
	$query="select * from hrm_employee where id='$emp_id'";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row=mysqli_fetch_array($result);
	return $row;
}


/*function get_reporting_manager($emp_id)
{
	$conn=connect();
	$query="select * from hrm_reporting_manager where employee_id='$emp_id';";
	//echo $query;
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	/*$x=""; 
	$row=[];
	while($row_reporting_manager=mysqli_fetch_array($result))
	{
		$row=$row.$row_reporting_manager[0][0];
	}
	return $row;*/
/*
	$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
	//return json_encode($json );
	return $json;

}*/
function get_reporting_manager($emp_id, $profile_image_dir)
{
	$conn=connect();
	$query1="select * from hrm_reporting_manager where employee_id='$emp_id';";
	//echo $query;
	$result1=mysqli_query($conn, $query1) or die(mysqli_error($conn));
	$x=0; 
	$row=[];
	$row_reporting_manager_name = "";
	$br="";
	while($row1=mysqli_fetch_array($result1))
	{
		$x++;
		//$row_reporting_manager_ids = $row_reporting_manager_ids.$row_reporting_manager['reporting_manager_id'];
		$query2="select * from hrm_employee where id='$row1[reporting_manager_id]';";
		//echo $query;
		$result2=mysqli_query($conn, $query2) or die(mysqli_error($conn));
		$row2=mysqli_fetch_array($result2);
		if($x%1==0) $br = "<br><br>"; 
		$row_reporting_manager_name = 
		
		//$row_reporting_manager_name.$row2['fname']." ".$row2['lname'].", ";
		$row_reporting_manager_name .'<div class="text">
		<div class="avatar-box">
			<div class="avatar avatar-xs">
				<img src="'.$profile_image_dir.'/'.$row2['image'].'" alt="User Image">
			</div>
		</div>
		<a href="profile.php?id='.$row2['id'].'">'.$row2['fname'].' '.$row2['lname'].'
		</a>
	</div>'.$br;




	}
	return $row_reporting_manager_name;


}
function get_user_roll($emp_id)
{
	$conn=connect();
	$query="select * from hrm_permission where emp_id='$emp_id'";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	
	if(mysqli_num_rows($result)>0)
	{
	
	$row=mysqli_fetch_array($result);

	$query="select * from hrm_permission_name where id='$row[permission_id]'";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));

	
	$row=mysqli_fetch_array($result);	
	return $row;
	}
	else
	{
		return $row=[];
	}
}


function display_subject_in($subjects)
{
	$conn=connect();
	$query="select name from subject where id in($subjects);";
	//echo $query;
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$x="";
	while($row=mysqli_fetch_array($result))
	{
		echo $x.$row['name'].", ";
	}	
}
function get_month_name($month)
{
	if($month=="01")
	{
		return "January";
	}
	if($month=="02")
	{
		return "February";
	}
	if($month=="03")
	{
		return "March";
	}
	if($month=="04")
	{
		return "April";
	}
	if($month=="05")
	{
		return "May";
	}
	if($month=="06")
	{
		return "June";
	}
	if($month=="07")
	{
		return "July";
	}
	if($month=="08")
	{
		return "August";
	}
	if($month=="09")
	{
		return "September";
	}
	if($month=="10")
	{
		return "October";
	}
	if($month=="11")
	{
		return "November";
	}
	if($month=="12")
	{
		return "December";
	}
}
function birthday_notification()
{
	$conn=connect();

	
	$employee_birthday=get_value1("ds_employees", "emp_dob", "emp_id", $_SESSION['emp_id']);
	
	$name=get_value1("ds_employees", "emp_name", "emp_id", $_SESSION['emp_id']);
	
	$current_date=date("Y")."-".date("m")."-".date("d");

	//echo "jai shiva". $current_date;

	/*if($employee_birthday==$current_date)
	{
		echo "<h4>Today is ".$name."'s Birthday";
		echo "<h4>HAPPY BIRTHDAY".$name."</h4>"; 
		
		echo "<img src='img/birthday.jpg'>";
		
	}*/

	
	// select all employee birthday
	
	$query="select * from ds_employees where emp_status=1;";
	$result=mysqli_query($conn, $query);
	while($row=mysqli_fetch_array($result))
	{
		$employee_birthday=$row['emp_dob'];
		
		//echo $employee_birthday."==".$current_date."<br>";
		
		
		if($employee_birthday==$current_date)
		{
		    
		    //echo "<br>==================<br>";
			
			//echo $row['emp_name'];
			
			//echo "<br>===================<br>";
			
			$notification_type="Happy Birthday";
			
			$notification_msg="Today is ".$row['emp_name']." birthday";
			
		
			
			// insert into notification		
			$query="insert into ds_notification(emp_id, notification_type, notification_msg, notification_status, notification_date) 
			values ('$_SESSION[emp_id]', '$notification_type', '$notification_msg', '1', '$current_date');";
			//echo $query1;
			$result=mysqli_query($conn, $query);
			
			// insert into notification		
			$query="insert into ds_notification(emp_id, notification_type, notification_msg, notification_status, notification_date) 
			values ('125', '$notification_type', '$notification_msg', '1', '$current_date');";
			//echo $query1;
			$result=mysqli_query($conn, $query);	
			
			
			
		}

	}
}
function ddmmyyyy($dates)
{
$x=date('d-m-Y', strtotime($dates));
return $x;
}
function yyyymmdd($dates)
{
$x=date('Y-m-d', strtotime($dates));
return $x;
}
function getRandomNumber($len = "15")
{
    $better_token = $code=sprintf("%0".$len."d", mt_rand(1, str_pad("", $len,"9")));
    return $better_token;
}


function get_emp_by_mail($id){
$con=connect();
$getid = mysqli_fetch_object(mysqli_query($con, "select emp_id from ds_employees where emp_loginid = '".$id."'"));
$empid = $getid->emp_id;
return $empid;
}

function get_emp_mail($id){
$con=connect();
$emp_details = mysqli_fetch_object(mysqli_query($con, "select emp_email from ds_employees where emp_id = '".$id."'"));
$empmail = $emp_details->emp_email;
return $empmail;
}

function get_emp_cmail($id){
$con=connect();
$emp_details = mysqli_fetch_object(mysqli_query($con, "select emp_email from ds_employees where emp_id = '".$id."'"));
$empmail = $emp_details->emp_email;
return $empmail;
}

function get_emp_mobile($id){
$con=connect();
$emp_details = mysqli_fetch_object(mysqli_query($con, "select emp_mobile from ds_employees where emp_id = '".$id."'"));
$empmobile = $emp_details->emp_mobile;
return $empmobile;
}

function get_emp_phone($id){
$con=connect();
$emp_details = mysqli_fetch_object(mysqli_query($con, "select emp_lnd from ds_employees where emp_id = '".$id."'"));
$empmobile = $emp_details->emp_lnd;
return $empmobile;
}

function dept_head($id){
include("include/dbconnection.php");
$dept_head = mysqli_fetch_object(mysqli_query($con, "select * from ds_department where dept_id = '".$id."'"));
$hod = $dept_head->dept_hod;
return $hod;
}

function emp_dept($id){
$con=connect();
$emp_details = mysqli_fetch_object(mysqli_query($con, "select emp_deptid from ds_employees where emp_id = '".$id."'"));
$empdept = $emp_details->emp_deptid;
return $empdept;
}

function emp_desi($id){
include("include/dbconnection.php");
$dept_head1 = mysqli_fetch_object(mysqli_query($con, "select designations_Name from ds_designations join ds_employees on ds_employees.emp_desi_id = ds_designations.designations_id where ds_employees.emp_id = '".$id."'"));
$deptname= $dept_head1->designations_Name;
return $deptname;
}

function emp_team($id){
$con=connect();
$emp_team = mysqli_fetch_object(mysqli_query($con, "select emp_team from ds_employees where emp_id = '".$id."'"));
$empteam= $emp_team->emp_team;
return $empteam;
}

function dept_name($id){
$con=connect();
$dept_head1 = mysqli_fetch_object(mysqli_query($con, "select dept_name from ds_department where dept_id = '".$id."'"));
$deptname= $dept_head1->dept_name;
return $deptname;
}

function desi_name($id){
$con=connect();
$dept_head1 = mysqli_fetch_object(mysqli_query($con, "select designations_Name from ds_designations where designations_id = '".$id."'"));
$deptname= $dept_head1->designations_Name;
return $deptname;
}

function random_password( $length = 8 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

function all_nc_data($emp_id, $access)
{
	$con=connect();
	if($emp_id!="" and $access==1)
	{
/*		$query= "select year(added_date) as year from dc_new_sales_details where open_pool = '1' and calling_status != '7' and task_id = '0' group by year(added_date) order by updated_date DESC;";
		$result=mysqli_query($con, $query) or die(mysqli_error($con));
		//return mysqli_num_rows($result);
		while($row=mysqli_fetch_array($result))
		{
			echo $row['year']."<br>";
		}	*/
		
			   	$query="select * from dc_new_sales_details where open_pool = '1' and calling_status != '7' and task_id = '0' order by updated_date DESC;";
		$result=mysqli_query($con, $query) or die(mysqli_error($con));
		return mysqli_num_rows($result);		   
		
		
		
	}
	else if($emp_id!="" and $access=="")
	{
	    // jai shiva
	   	$query="select * from dc_new_sales_details where open_pool = '1' and calling_emp_id = '$emp_id' and calling_status != '7' and task_id = '0' order by updated_date DESC;";
		$result=mysqli_query($con, $query) or die(mysqli_error($con));
		return mysqli_num_rows($result);		   
	}
}
function all_leeds_data($emp_id, $access)
{
	$con=connect();
	if($emp_id!="" and $access==1)
	{
		$query = "select * from dc_new_sales_details where open_pool = '1' and calling_status != '7' 
      and sod!='' order by updated_date DESC";
		$result=mysqli_query($con, $query) or die(mysqli_error($con));
		return mysqli_num_rows($result);
	}
	else if($emp_id!="" and $access=="")
	{
	   //  jai shiva
	   $query = "select * from dc_new_sales_details where open_pool = '1' and calling_emp_id = '$emp_id'  
	   and sod!='' and calling_status != '7' order by updated_date DESC";
		$result=mysqli_query($con, $query) or die(mysqli_error($con));
		return mysqli_num_rows($result);		   

	}
}

function all_open_pool()
{
	$con=connect();

	$query = "select * from dc_new_sales_details where open_pool = '0' order by added_date DESC;";                  
	$result = mysqli_query($con, $query) or die(mysqli_error($con));
	//$rcalling = mysqli_num_rows($select_calling);
	$result=mysqli_query($con, $query) or die(mysqli_error($con));
	return mysqli_num_rows($result);		   
}

function all_not_open_pool()
{
	$con=connect();
	$query="select * from dc_new_sales_details where open_pool = '2' order by added_date DESC;";
	$result=mysqli_query($con, $query) or die(mysqli_error($con));
	return mysqli_num_rows($result);		   	
		//echo $query;
}

function all_ni_pool()
{
	$con=connect();
	$query="select * from dc_new_sales_details where open_pool = '2' and calling_status= 3;";
	$result=mysqli_query($con, $query) or die(mysqli_error($con));
	return mysqli_num_rows($result);		   	
		//echo $query;
}

function user_track($empid,$title,$url,$time,$uip){
	//include("include/dbconnection.php");
	$con=connect();

	$approveleave= mysqli_query($con, "insert into ds_user_track (user_id,track_page,track_url,track_time,user_ip) values('$empid','$title','$url','$time', '$uip')");
	}
	
	function getUserIpAddr(){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			//ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			//ip pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	function nc_url($id){
	  //include("include/dbconnection.php");
	  $con=connect();
	  $client_id = mysqli_fetch_object(mysqli_query($con, "select calling_id from dc_new_sales_details where url_path = '".$id."'"));
	  $clientid = $client_id->calling_id;
	  return $clientid;
	}
	
	function nc_path($id){
	  //include("include/dbconnection.php");
	  $con=connect();
	  $client_id = mysqli_fetch_object(mysqli_query($con, "select url_path from dc_new_sales_details where calling_id = '".$id."'"));
	  $path = $client_id->url_path;
	  return $path;
	}
	// jai shiva

function emp_name($id)
{
	$con=connect();
	$emp_details = mysqli_fetch_object(mysqli_query($con, "select emp_name, emp_last_name from ds_employees where emp_id = '".$id."'"));
	if(is_object($emp_details))
	{
		$empname = $emp_details->emp_name." ".$emp_details->emp_last_name;
		return $empname;
	}	
}


function fill_unit_select_box($con)
{
$con=connect();
$output = '';
$mservices = mysqli_query($con, "SELECT * FROM ds_services where services_status = '1' ORDER BY services_order ASC");
while($result_mservices = mysqli_fetch_object($mservices))
 {
  $output .= '<option value="'.$result_mservices->services_id.'">'.$result_mservices->services_name.'</option>';
 }
 return $output;
}

function get_target($id,$m,$y) {
	$con=connect();
$target = mysqli_fetch_object(mysqli_query($con, "select tTarget from ds_emp_target where emp_id = '".$id."' and month(tAddeddate) = '".$m."' and year(tAddeddate) = '".$y."'"));
$rtarget = $target->tTarget;
return $rtarget;
}
function total_employee() {
	$con=connect();
 
$result = mysqli_query($con, "SELECT * FROM hrm_employee");
$total = mysqli_num_rows($result);
return $total;
}



function target_achieved($id,$m,$y) {
	$con=connect();
$achieved = mysqli_fetch_object(mysqli_query($con, "select sum(payment_amount) as totalachieved from ds_paid_amount where emp_id = '".$id."' and month(payment_date) = '".$m."' and year(payment_date) = '".$y."' and admin_approval = '1'"));
$rachieved = $achieved->totalachieved;
return $rachieved;
}

function target_achieved_withoutgst($id,$m,$y) {
	$con=connect();
$achieved = mysqli_fetch_object(mysqli_query($con, "select sum(tamount) as totalamount, emp_id from ds_top_performar where emp_id = '".$id."' and month(tdate) = '".$m."' and year(tdate) = '".$y."'"));
$rachieved = $achieved->totalamount;
return $rachieved;
}


function total_meeting($id,$m,$y) {
	$con=connect();
$meeting = mysqli_fetch_object(mysqli_query($con, "select count(calling_id) as totalmeeting from dc_new_sales_details_process where year(added_date) ='".$y."' and updated_by ='".$id."' and month(added_date) ='".$m."' and calling_status in(4,5,6,7)"));
$rmeeting = $meeting->totalmeeting;
return $rmeeting;
}

function deal_closed($id,$m,$y) {
	$con=connect();
$dealclosed = mysqli_fetch_object(mysqli_query($con, "select count(calling_id) as totaldeal from dc_new_sales_details_process where year(added_date) ='".$y."' and updated_by ='".$id."' and month(added_date) ='".$m."' and calling_status = 7"));
$rdealclosed = $dealclosed->totaldeal;
return $rdealclosed;
}

function qualify_incentive($id,$m,$y,$s1,$s2) {
	$con=connect();
$achieved = mysqli_fetch_object(mysqli_query($con, "select sum(payment_amount) as totalachieved from ds_paid_amount where emp_id = '".$id."' and month(payment_date) = '".$m."' and year(payment_date) = '".$y."' and (day(payment_date) >= '".$s1."' || day(payment_date) <= '".$s2."') and admin_approval = '1'"));
$rachieved = $achieved->totalachieved;
return $rachieved;
}
function total_emp_incentive($id,$month,$year){
	$con=connect();
$incentive = mysqli_fetch_object(mysqli_query($con, "select sum(cIncentive) as tincentive from ds_emp_incentive where emp_id = '".$id."' and month(cDate) = '".$month."' and year(cDate) = '".$year."'"));

$total_incentive = $incentive->tincentive;
return $total_incentive;
}

function yearbox($startYear, $endYear, $id){ 
	$con=connect();
    //start the select tag 
        echo "<select class='col-xs-10 col-sm-12' id=".$id." name=".$id." required>"; 
        echo "<option value=''>--Select Year--</option>"; 
        for ($i=$startYear;$i<=$endYear;$i++){ 
        echo "<option value=".$i.">".$i."</option>n";     
        } 

    echo "</select>";

} 

function ns_company_name($id){
	$con=connect();
$c_name = mysqli_fetch_object(mysqli_query($con, "select calling_company from dc_new_sales_details where calling_id = '".$id."'"));
$company_name = $c_name->calling_company;
return $company_name;
}

function get_account_manager($id){
	$con=connect();
$c_name = mysqli_fetch_object(mysqli_query($con, "select account_manager from ds_client where client_id = '".$id."'"));
$account_manager = $c_name->account_manager;
return $account_manager;
}

function get_service($id){
	$con=connect();
$sname = mysqli_fetch_object(mysqli_query($con, "select services_name from ds_services where services_id = '".$id."'"));
$servicesname = $sname->services_name;
return $servicesname;
}

function getdays($cdate1,$cdate2){

$start = strtotime($cdate1);
$end = strtotime($cdate2);

$count = 0;

while(date('Y-m-d', $start) < date('Y-m-d', $end)){
  $count += date('N', $start) < 6 ? 1 : 0;
  $start = strtotime("+1 day", $start);
}

return $count;
}

function nagitive_check($value){
    if (substr(strval($value), 0, 1) == "-"){
    $numbertype = 1;
    }
    return $numbertype;
}
function expenses_type($id){
	$con=connect();
$expenses_type= mysqli_fetch_object(mysqli_query($con, "select expensetype_name from ds_expensetype where expensetype_id = '".$id."'"));
$expenses_type1 = $expenses_type->expensetype_name;
return $expenses_type1;
}

function subcat_name($id){
	$con=connect();
$subcat_name = mysqli_fetch_object(mysqli_query($con, "select scategory_name from ds_scategory where scategory_id = '".$id."'"));
$scatname= $subcat_name->scategory_name;
return $scatname;
}

function cat_name($id){
	$con=connect();
$cat_name = mysqli_fetch_object(mysqli_query($con, "select category_name from ds_category where category_id = '".$id."'"));
$catname= $cat_name->category_name;
return $catname;
}

function working_time($id){
	$con=connect();
	
	 $workedtime = mysqli_fetch_object(mysqli_query($con, "select sum(working_time) as totaltime from ds_emp_report where client_id = '".$id."'"));
		 $workedtime1 = mysqli_fetch_object(mysqli_query($con, "select sum(timetaken) as totaltime from ds_taskcomment where client_id = '".$id."'"));
		  $workedtime2 = mysqli_fetch_object(mysqli_query($con, "select sum(call_time) as totaltime from ds_client_calls where client_id = '".$id."'"));
		$ttimes = 0;
		$ttimes += $workedtime->totaltime;
		$ttimes += $workedtime1->totaltime;
		$ttimes += $workedtime2->totaltime;
		$hours = floor($ttimes / 60);
		$minutes = $ttimes % 60;
		$result = $hours." Hour ".$minutes." Minute";
		return $result;
	}

	 

function cs_emp_id($id)
{
	$con=connect();
	$query = "select emp_id from ds_employees where emp_loginid = '".$id."'";
	$result=mysqli_query($con, $query ) or die(mysqli_error($con));
	$cs = mysqli_fetch_object(mysqli_query($con, $query ));
	//if(is_object($cs))
	//{
		$csempid = $cs->emp_id;
		return $csempid;
	//}		
}

function display_tutor_full_detail($org_id, $user_id, $modal_id)
{
	$conn=connect();
	$query="select * from hometutor where id='$org_id';";
	$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
	$x="";
	$row=mysqli_fetch_array($result);
	//return $row[$column];
	?>
	<!-- model dialog -->
		<div class="modal fade" id="<?php echo $modal_id; ?>">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Tutor Detail</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <table border="1">
			 <tr>
			 	<td>Name</td><td><?php echo $row['name']; ?></td>
				<td>Registration Date</td><td><?php echo $row['name']; ?></td>
			  </tr>
			  <tr>				
				<td>Last Modified Date</td><td><?php echo $row['name']; ?></td>				
				<td>Last Login Date</td><td><?php echo $row['name']; ?></td>
			  </tr>				
			  <tr>				
				<td>Email</td><td><?php echo $row['name']; ?></td>				
				<td>Mobile</td><td><?php echo $row['name']; ?></td>
			  </tr>
			  <tr>				
				<td>About Tutor</td><td><?php echo $row['name']; ?></td>				
				<td>Class and Subjects</td><td><?php echo $row['name']; ?></td>
			  </tr>	
			  <tr>				
				<td>City and Locality</td><td><?php echo $row['name']; ?></td>				
				<td>Address</td><td><?php echo $row['name']; ?></td>
			  </tr>			
			  <tr>				
				<td>Registered By</td><td><?php echo $row['name']; ?></td>				
				<td>Gender</td><td><?php echo $row['name']; ?></td>
			  </tr>				
			  
			  <tr>				
				<td>Total Request Sent By Student</td><td><?php echo $row['name']; ?></td>				
				<td>Tutor Type</td><td><?php echo $row['name']; ?></td>
			  </tr>		
			  
  			  <tr>				
				<td>Board, Fee, Experience</td><td><?php echo $row['name']; ?></td>				
				<td></td>
			  </tr>						
			  		  
			  						
				
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
		<!-- model dialog -->	
<?php 		
}

 

?>