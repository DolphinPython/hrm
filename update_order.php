<?php
include 'layouts/config.php';

if (isset($_POST['order']) && is_array($_POST['order'])) {
    $order = $_POST['order'];
    
    // Use prepared statements to prevent SQL injection
    $stmt = $con->prepare("UPDATE onboarding_steps SET step_order = ? WHERE step_id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $con->error);
    }

    foreach ($order as $position => $step_id) {
        $position = (int)$position; // Ensure position is an integer
        $step_id = (int)$step_id;   // Ensure step_id is an integer
        $stmt->bind_param("ii", $position, $step_id);
        if (!$stmt->execute()) {
            echo "Execute failed: " . $stmt->error;
            exit;
        }
    }
    
    $stmt->close();
    echo "success";
} else {
    echo "error: Invalid order data";
}

$con->close();
?>