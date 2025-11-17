<?php 
include 'layouts/session.php';
include 'include/function.php';
use Shuchkin\SimpleXLSX;

$employee_id_session=$_SESSION['id'];

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

require_once __DIR__.'/src/SimpleXLSX.php';

echo '<h1>XLSX to HTML</h1>';
$emp_id = "";
$emp_name = "";
$day_number = "";
$time_row1 = "";
$row_end=false;
$conn=connect();
if (isset($_FILES['file'])) 
{

    if ($xlsx = SimpleXLSX::parse($_FILES['file']['tmp_name'])) {
        
        echo '<h2>Parsing Result</h2>';
        echo '<table border="1" cellpadding="3" style="border-collapse: collapse">';

        $dim = $xlsx->dimension();
        $cols = $dim[0];

		$x=1;
		$count_row=0;
		$count_row8=0;
		$id_found=0;
		$real_row_count=0;
		$index=0;
		
        foreach ($xlsx->readRows() as $k => $r) {
			$real_row_count++;
            //      if ($k == 0) continue; // skip first row
			$row_end=true;
			$time_row1 = "";
            echo '<tr>';
            for ($i = 0; $i < $cols; $i ++) 
			{
				if($i==2 and $x==65)
				{
					// get start date and end date 
					//echo '<td>i='.$i.'x='.$x.'<br>'.(isset($r[ $i ]) ? $r[ $i ] : '&nbsp;' ) . '</td>';
					$date = $r[ $i ];					
					//echo '<td>x='.$x."<br>".(isset($r[ $i ]) ? $r[ $i ] : '&nbsp;' ) . '</td>';
				}

				if($i==2 and $x==127) // get first employee id
				{
					$count_row=1;
					$count_row8=1;
					$emp_id = $r[$i].",";
					$id_found=1;
					//echo '<td>'.$emp_id.'</td>';
					//echo '<td>x='.$x."<br>i=".$i."<br>".(isset($r[ $i ]) ? $r[ $i ] : '&nbsp;' ) . '</td>';
				}
				if($i==2 and $x>127 and $count_row%2!=0) // get after first employee id 
				{
					
					$calculate = $count_row8-1;
					if($x-($calculate*31)==127)
					{
						$id_found=1;
						$emp_id = $emp_id.$r[$i].",";
						//echo '<td>x='.$x."<br>".$emp_id.(isset($r[ $i ]) ? $r[ $i ] : '&nbsp;' ) . '</td>';
						//echo "<td>yessssssss</td>";
					}
					
				}
				if($i==10 and $x==135) // get first employee name
				{
					//$count_row=1;
					//$count_row8=1;
					$emp_name = $r[$i].",";
					//$id_found=1;
					//echo '<td>'.$emp_id.'</td>';
					//echo '<td>x='.$x."<br>i=".$i."<br>".(isset($r[ $i ]) ? $r[ $i ] : '&nbsp;' ) . '</td>';
				}
				if($i==10 and $x>135 and $count_row%2!=0) // get name
				{
					
					//$calculate = $count_row8-1;
					if($x-($calculate*31)==135)
					{
						//$id_found=1;
						$emp_name = $emp_name.$r[$i].",";
						//echo '<td>x='.$x."<br>".$emp_id.(isset($r[ $i ]) ? $r[ $i ] : '&nbsp;' ) . '</td>';
						//echo "<td>yessssssss</td>";
					}
					
				}
				if($x>=94 and $x<=123) // get total date numbers
				{
					$day_number = $day_number.$r[$i].",";
				}
				if($real_row_count>=6 and $real_row_count%2==0) // get attandance time
				{
					//if($row_end)
					//{
						
					//}
					//if($row_end)
					//{
						
						$time_row1 = $time_row1.$r[$i].",";
					//}	
					
				}
				/*else
				{
					$id_found=0;
				}*/
				echo '<td>real_count_row='.$real_row_count.',count_row='.$count_row.',count_row_new='.$count_row8.',x='.$x."<br>i=".$i."<br>".(isset($r[ $i ]) ? $r[ $i ] : '&nbsp;' ) . '</td>';
				$x++;
            }
			$count_row++;
			$count_row8++;
            echo '</tr>';
			// after row end store each employee time in a string array one by one
			//$row_wise_student_time[$real_row_count-1] = 
			if($real_row_count>=6 and $real_row_count%2==0) // get attandance time
			{
				$row_wise_time_array[$index]=$time_row1;
				$index++;
			}
			// after row end store each employee time in a string array one by one
			
        }
		$row_end=false;
        echo '</table>';
    } else {
        echo SimpleXLSX::parseError();
    }

echo $date;

echo "<br>First Row = ".$time_row1."<br>";

echo "<br>Print time one by one<br>";
echo "==============";
//echo $row_wise_time_array[0][0]."<br>";
echo "==============";

//print_r($row_wise_time_array);

echo "count date time=".count($row_wise_time_array);

for($a=0; $a<count($row_wise_time_array); $a++)
{
	echo "<br>Row$a=====".$row_wise_time_array[$a]."<br>";

	$s=rtrim($row_wise_time_array[$a],",");
	$b=explode(",",$s);
	for($d=0;$d<count($b);$d++)
	{
		//echo "<br>subvalue$d=".$b[$d]."<br>";
	}
	
}
//$a=0;


// get start date and end date
$date = str_replace(' ', '', $date);
//$date = str_replace('~', '', $date);
echo "<br>".$date;
$start_date = substr($date, 0, strpos($date, "~"));
$end_date = substr($date, 11, strpos($date, "~"));
echo "<br>start_date".$start_date;
echo "<br>end_date".$end_date;
// get start date and end date
//print_r($emp_id);
echo "id found=".$id_found."emp_id==========".$emp_id."=============<br>";

echo "name=========".$emp_name."=============<br>";

$emp_id = trim($emp_id,",");


echo "<br>last id==============".$emp_id."<br>";
echo "<br>dates==============".$day_number."<br>";

$emp_id =  explode(",",$emp_id);

$emp_name =  explode(",",strtolower($emp_name));

print_r($emp_id);


$extract_month = substr($date, 5, 2);
$extract_year = substr($date, 0, 4);
//echo "aaaaaaaaaaaaa".$extract_month;
//$extract_month = ltrim($extract_month, '0');
//echo "aaaaaaaaaaaaa".$extract_month;
$year = date($extract_year);
$month = date($extract_month);
$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

//echo "Number of days in the current month: $days";

//echo "<br>extract month".$extract_month."<br>ok";
//echo "<br>extract year".$extract_year."<br>ok";


$current_date = date("Y-m-d");
// inserting value to database
$count_employee = count($emp_name);

echo "<br>count employee=".$count_employee."<br>";


// check if already inserted in this year and month
$month1=(int)$month;
$query_check = "select * from hrm_attandance_machine_detail where year='$year' and month='$month1'";
$result_check = mysqli_query($conn, $query_check);
//echo $query_check;
// check if already inserted in this year and month
for($i=0;$i<$count_employee-1;$i++)
{
	//$v="";
	
	//$v=$v."'".$row_wise_time_array[$i]."'";

	//$s=rtrim($row_wise_time_array[$i],",");

	$s=substr(trim($row_wise_time_array[$i]), 0, -1);

	$b=explode(",",$s);
	$v="";
	$dt="";
	for($d=0;$d<count($b);$d++)
	{
		$v=$v."'".$b[$d]."',";
		$dt=$dt."'".$year."-".$month."-".($d+1)."',";
		
	}
	$v=rtrim($v,",");
	$dt=rtrim($dt,",");
	//echo "v===================".$v;

	//echo "dt===================".$dt;

	
	
	if(mysqli_num_rows($result_check)==0)
	{
	
	
	$query = "insert into hrm_attandance_machine_detail
	(year, month, added_date, from_date, to_date, added_by, attandance_id, employee_name, 
	date_in_out1, date_in_out2,date_in_out3, date_in_out4,date_in_out5, date_in_out6,
	date_in_out7, date_in_out8,date_in_out9, date_in_out10,date_in_out11, date_in_out12,
	date_in_out13, date_in_out14,date_in_out15, date_in_out16,date_in_out17, date_in_out18,
	date_in_out19, date_in_out20,date_in_out21, date_in_out22,date_in_out23, date_in_out24,
	date_in_out25, date_in_out26,date_in_out27, date_in_out28,date_in_out29, date_in_out30,
	date_in_out31, date1, date2, date3, date4, date5, date6, date7, date8, date9, date10, date11,
	date12, date13, date14, date15, date16, date17, date18, date19, date20, date21, date22, date23,
	date24, date25, date26, date27, date28, date29, date30, date31)
	values('$year', '$month', '$current_date', '$start_date', '$end_date', '$employee_id_session', 
	'$emp_id[$i]', '$emp_name[$i]', $v,$dt);";
	echo "<br>".$query."<br>";
	$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));
	}
}
// inserting value to database




// upload file

                $ids = uniqid();
                $name = $_FILES["file"]["name"];
				$target_dir = "attandance-file/";
				$target_file = $target_dir . $ids.basename(str_replace(" ","-", $_FILES["file"]["name"]));
				$target_file1 = $ids.basename(str_replace(" ","-", $_FILES["file"]["name"]));
				$uploadOk = 1;
				$ext = end((explode(".", $name)));
//echo $ext;
				// Check if file already exists
				if (file_exists($target_file)) {
				// echo "Sorry, file already exists.";
				$uploadOk = 1;
				}

				// Check file size
				if ($_FILES["file"]["size"] > 1000000) {
				// echo "Sorry, your file is too large.";
				$uploadOk = 0;
				}

				// Allow certain file formats
				if($ext != "xlsx") {
				echo "Please convert excel file to xlsx.";
				$uploadOk = 0;
				}


				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
				 echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
				} 
				else
				{
					//$target_file = $ids.str_replace(" ","-", $target_file);
					if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
					{
					echo "The file ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded.";
					} 
					else
					{
					echo "Sorry, there was an error uploading your file.";
					}

				}    
                
                // upload file

                // save file detail to database
                $year = "";//$_POST['year'];
                $month = "";//$_POST['month'];
                $datetime = "";//$_POST['date_time'];
                $name = "";//$_POST['name'];
                $upload_for_month = "";//$_POST['upload_for_month'];
                $query = "insert into hrm_attandance_file
                (year, month, date_time, upload_for_month, file1, name)
values('$year', '$month', '$datetime', '$upload_for_month', '$target_file1', '$name');";

  //echo $query;
  

$result = mysqli_query($conn, $query) or die(mysqli_error($conn, "insert error"));
                // save file detail to database

                   

}

echo '<h2>Upload form</h2>
<form method="post" enctype="multipart/form-data">
*.XLSX <input type="file" name="file"  />&nbsp;&nbsp;<input type="submit" value="Parse" />
</form>';
