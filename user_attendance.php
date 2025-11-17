<?php
session_start();
include 'include/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }

    $userId = $_SESSION['id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    if ($latitude === false || $longitude === false) {
        echo json_encode(['success' => false, 'message' => 'Invalid coordinates']);
        exit;
    }

    // Office coordinates
    
    $officeLat =28.6344549;
    $officeLng = 77.2828088;

   

    // Calculate distance in meters
    $distance = haversineGreatCircleDistance($officeLat, $officeLng, $latitude, $longitude);
    
    // Allow within 100 meters radius
    if ($distance <= 200) {
        $conn = connect();
        
        // Check if user already has an active session
        $checkStmt = $conn->prepare("SELECT id, status FROM user_attendance WHERE user_id = ? ORDER BY login_time DESC LIMIT 1");
        $checkStmt->bind_param('i', $userId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $lastRecord = $result->fetch_assoc();
        $checkStmt->close();

        if (!$lastRecord || $lastRecord['status'] === 'logout') {
            // Clock in
            $stmt = $conn->prepare("INSERT INTO user_attendance (user_id, login_time, location_lat, location_lng, status) VALUES (?, NOW(), ?, ?, 'login')");
            $stmt->bind_param('idd', $userId, $latitude, $longitude);
            $action = 'Clock in';
        } else {
            // Clock out
            $stmt = $conn->prepare("UPDATE user_attendance SET logout_time = NOW(), status = 'logout' WHERE id = ? AND user_id = ?");
            $stmt->bind_param('ii', $lastRecord['id'], $userId);
            $action = 'Clock out';
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => $action . ' successful','distance'=>$distance]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error,'distance'=>$distance]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'You must be within 100 meters of the office to clock in/out','distance'=>$distance]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method','distance'=>$distance]);
}

function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2) {
    // Earth's radius in kilometers
    $earthRadius = 6371.0;

    // Convert latitude and longitude to radians
    $lat1 = deg2rad(floatval($lat1));
    $lon1 = deg2rad(floatval($lon1));
    $lat2 = deg2rad(floatval($lat2));
    $lon2 = deg2rad(floatval($lon2));

    // Haversine formula
    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos($lat1) * cos($lat2) *
         sin($dLon / 2) * sin($dLon / 2);
         
    $c = 2 * asin(sqrt($a));
    $distanceInKm = $earthRadius * $c;

    // Convert distance to meters and round to 2 decimal places
    $distanceInMeters = round($distanceInKm * 1000, 2);

    return $distanceInMeters;
}

?>
