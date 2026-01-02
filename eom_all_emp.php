<?php

include 'include/db.php';
$conn=connect();


$current_batch = "EOM_Dec_2025";
$total_points  = 30;

/* Eligible Employees (Nominees) */
$eligible_sql = "
SELECT id, emp_id, full_name, emp_point 
FROM eom 
WHERE eom_batch = '$current_batch'
";
$eligible_res = mysqli_query($conn, $eligible_sql);
$total_eligible = mysqli_num_rows($eligible_res);

if ($total_eligible == 0) {
    die("❌ No eligible employees");
}

$points_per_vote = $total_points / $total_eligible;



echo "<h3>Employee of the Month Voting</h3>";
echo "<form method='post' action='submit_vote.php'>";

/* Eligible Employees (radio options) */
$eligible_sql = "
SELECT id, full_name 
FROM eom 
WHERE eom_batch = '$current_batch'
";
$eligible_res = mysqli_query($conn, $eligible_sql);

while ($row = mysqli_fetch_assoc($eligible_res)) {
    echo "
        <div>
            <input type='radio' name='eom_id' value='{$row['id']}' required>
            {$row['full_name']}
        </div>
    ";
}

echo "<br><button type='submit'>Submit Vote</button>";
echo "</form>";

?>



