<?php

    include 'include/db.php';
    $conn=connect();


    $allemp = "SELECT * FROM hrm_employee WHERE status = 1 and archive_status =0";
    $empres = mysqli_query($conn, $allemp) or die(mysqli_error($conn));
    // mysqli_num_rows($empres);
    while ($row = mysqli_fetch_array($empres)) {
        echo "ID ";
        echo $row['id'];
        echo " | ";
        echo $row['fname']." ".$row['lname'];
        echo "<br>";
    }
    
?>
