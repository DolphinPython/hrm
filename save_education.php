<?php 
include 'layouts/session.php'; 
include 'layouts/head-main.php'; 
include 'include/function.php';

// get user name and other details
$emp_id = $_SESSION['id'];
$conn = connect();
$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);

$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);
$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");
?>

<?php
// Retrieve POST data (for both insert and update)
$emp_id = $_SESSION['id']; // Emp ID is already retrieved from the session
$id = $_POST['id'] ?? null; // If 'id' is not provided, it will be null for insert
$qualification_type = $_POST['qualification_type'];
$course_name = $_POST['course_name'];
$course_type = $_POST['course_type'];
$stream = $_POST['stream'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$college_name = $_POST['college_name'];
$university_name = $_POST['university_name'];
$grade = $_POST['grade'];

// Check if it's an update or insert
if ($id) {
    // Update existing record
    $sql = "UPDATE hrm_employee_education 
            SET qualification_type=?, course_name=?, course_type=?, stream=?, start_date=?, end_date=?, 
                college_name=?, university_name=?, grade=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssi", $qualification_type, $course_name, $course_type, $stream, $start_date, $end_date, $college_name, $university_name, $grade, $id);
} else {
    // Insert new record
    $sql = "INSERT INTO hrm_employee_education 
            (emp_id, qualification_type, course_name, course_type, stream, start_date, end_date, college_name, university_name, grade) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssss", $emp_id, $qualification_type, $course_name, $course_type, $stream, $start_date, $end_date, $college_name, $university_name, $grade);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
