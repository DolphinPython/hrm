<?php 
date_default_timezone_set("Asia/Calcutta");
$d1 = new DateTime("2024-09-01"); /* inclusive */
$d2 = new DateTime("2024-09-30"); /* exclusive */

$interval = $d2->diff($d1);
$number_of_days = $interval->format("%d");

$number_of_weekends = $number_of_days / 7;
$remainder = $number_of_days % 7;

if ($remainder >=2 && $d1->format("D") == "Sat")
{
    $number_of_weekends++;
}
elseif ($d1->format("w") + $remainder >= 8)
{
    $number_of_weekends++;
}
//echo $number_of_weekends;




function number_of_working_days($from, $to) {
    $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
    $holidayDays = ['*-12-25', '*-01-01', '2013-12-23']; # variable and fixed holidays

    $from = new DateTime($from);
    $to = new DateTime($to);
    $to->modify('+1 day');
    $interval = new DateInterval('P1D');
    $periods = new DatePeriod($from, $interval, $to);

    $days = 0;
    foreach ($periods as $period) {
        if (!in_array($period->format('N'), $workingDays)) continue;
        if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
        if (in_array($period->format('*-m-d'), $holidayDays)) continue;
        $days++;
    }
    return $days;
}

//echo number_of_working_days('2024-08-01', '2024-08-31');






//The function returns the no. of business days between two dates and it skips the holidays
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

//Example:

//$holidays=array("2008-12-25","2008-12-26","2009-01-01");
$holidays=array("2024-10-01", "2024-10-08");

echo getWorkingDays("2024-10-01","2024-10-31",$holidays);
// => will return 7

/* 




// get date name
echo date("l", mktime(0,0,0,10,1,2024)) . "<br>";
// get date name

// total sundays between two days
date_default_timezone_set('UTC');
$sun_first = strtotime('1970-01-04');
$t1 = strtotime("2024-10-01") - $sun_first - 86400;
$t2 = strtotime("2024-10-31") - $sun_first;
$sun_count = floor($t2 / 604800) - floor($t1 / 604800); // total Sunday from 2018-10-01 to 2018-10-31
echo "<br>total sunday".$sun_count;

$sun_first = strtotime('1970-01-04');
$t1 = strtotime("2024-10-01") - $sun_first - 86400;
$t2 = strtotime("2024-10-31") - $sun_first;
$sun_count = floor($t2 / 604800) - floor($t1 / 604800); // total Sunday from 2018-10-01 to 2018-10-31
echo "<br>total sunday".$sun_count;
// total sundays between two day



// other
$months = 6;  
$years=2024;                                      
$monthName = date("F", mktime(0, 0, 0, $months));
$fromdt=date('Y-m-01 ',strtotime("First Day Of  $monthName $years")) ;
$todt=date('Y-m-d ',strtotime("Last Day of $monthName $years"));

$num_sundays='';   
$num_sundays1='';             
for ($i = 0; $i < ((strtotime($todt) - strtotime($fromdt)) / 86400); $i++)
{
    if(date('l',strtotime($fromdt) + ($i * 86400)) == 'Sunday')
    {
            $num_sundays++;
    }    
	if(date('l',strtotime($fromdt) + ($i * 86400)) == 'Saturday')
    {
            $num_sundays1++;
    }   
}
echo "<br>Total Count sunday is: ".$num_sundays;
echo "<br>Total Count saturday is: ".$num_sundays1;
// other





*/

$x=array("abc", "def");
//$y="def";
//for($j=0;$j<2)
$y="";
for($z=0; $z<2; $z++)
{
    $y="";
    for($i=0; $i<2; $i++)
    {
        $y=$y.$i.",";
    }
    $array[$z]=$y;
}

echo "<br>=============".$y;
echo "<br>=========arra0=".$array[0];
echo "<br>=========arra1=".$array[1];

//////////////////////////////////////////
// json test
//////////////////////////////////////////
$x='
{
    "a": "apple",
    "b": "banana",
    "c": "catnip"
}';
//$json_string = json_encode($x, JSON_PRETTY_PRINT);

$json = json_decode($x, true);

//echo $json['a'];

//echo  json_decode($json_string);
echo "<br>".rtrim("1,2,3,4,,,,,,,,,,,,,,,,,,,",",");

$my_string = "'name', 'name2', 'name3',,,,,,,,";
echo substr(trim($my_string), 0, -1);


echo "<br>================================<br>";

$first  = new DateTime("09:59");
	$second = new DateTime("18:33");
	
	$diff = $first->diff( $second );
	
	//if($diff->format( '%H:%I:%S' )>"09:00")
	 

	echo "date=".$diff->format( '%H:%I:%S' );

    echo "<br>==================<br>";

    for($x=1; $x<=31; $x++)
	{
		
        ${"date".$x} = $x;
        echo  "date".$x = ${"date".$x};
	}	
////////////////////////////////

$first  = "09:59";
	
	 
if($first>"09:00")
	echo "<br>date=".$first;

    echo "<br>==================<br>";



echo substr("abc", 0, 3);
   // ${"file" . $i} = file($filelist[$i]);

echo "<br>=========================<br>";

$a="09:42";
$b="18:35";
$to_time = strtotime($a);
$from_time = strtotime($b);
$total_time = 570;
//echo $total_time-round(abs($to_time - $from_time) / 60,2). " minute";

echo round(abs($to_time - $from_time) / 60,2). " minute";







echo "<br>===================<br>";

function timeDiff($time_start = null, $time_end = null, $hour_mode = true)
  {
    $to_time = strtotime($time_start);
    $from_time = strtotime($time_end);
    $hour = 0;

    if ($hour_mode) { // Outputs in hours i.e. 8.50, 10.32
      $hour = round(abs($to_time - $from_time) / 60 / 60, 1); 
    }
    else {
      $hour = round(abs($to_time - $from_time) / 60, 1); // Outputs in minutes i.e. 3360, 500
    }    

    if ($time_end < $time_start) {
        if ($hour_mode) {   
            $hour -= 24; // outputs in hours         
        }
        else {
            $hour -= 1440; // outputs in minutes
        }
    }

    return abs($hour);
  }


  echo timeDiff('18:35', '09:42');



  echo "<br>============================<br>";

$first  = new DateTime( '09:42' );
$second = new DateTime( '18:35' );
$diff = $first->diff( $second );
$hour = $diff->format( '%H:%I' ); 
$time = explode(':', $hour);
echo ($time[0]*60) + ($time[1]);
?>