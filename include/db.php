<?php
// jai shiva
//session_start();
// //include/dbconnection.php;
// date_default_timezone_set('Asia/Colombo');
// //date_default_timezone_set('Asia/Kolkata');
// function connect()
// {
// 	$servername = "localhost";
// 	//$username = "user8";
// 	//$password = "123!@#";
// 	//$dbname = "db8";
// 		// server 
// 	$username = "u179772606_pankaj1soluti";
// 	$password = "Hostingerpankaj123!@#";
// 	$dbname = "u179772606_hrmdb";
// // 	$username = "root";
// // 	$password = "";
// // 	$dbname = "u179772606_hrmdb";
// 	$conn = mysqli_connect($servername, $username, $password, $dbname);
// 	return $conn;
// 	//mysqli_close($conn); 
// }
 

?>


<?php
// Jai Shiva 🙏

// ✅ Set PHP timezone (for all PHP date/time functions)
date_default_timezone_set('Asia/Kolkata'); // or 'Asia/Colombo'

function connect()
{
	$servername = "localhost";
	$username   = "root";
	$password   = "";
	$dbname     = "hrm";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}

	// ✅ Set MySQL timezone for this connection (important for CURRENT_TIMESTAMP)
	mysqli_query($conn, "SET time_zone = '+05:30'");

	return $conn;
}
?>
