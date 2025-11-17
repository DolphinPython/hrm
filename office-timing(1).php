<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';

$emp_id = $_SESSION['id'];
$conn = connect();
$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");
?>

<head>
    <title>Reports - HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
<?php
$relaxation_time = '';
$normal_fine = '';
$extra_fine_time = '';
$extra_fine = '';
$half_day_time = '';
$evening_half_time = ''; // New field
$login_time = '';
$logout_time = '';
$saturday_option = 'all-on';

$checkQuery = "SELECT * FROM office_timing WHERE id = 1";
$result = mysqli_query($conn, $checkQuery);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $relaxation_time = $row['relaxation_time'];
    $normal_fine = $row['normal_fine'];
    $extra_fine_time = $row['extra_fine_time'];
    $extra_fine = $row['extra_fine'];
    $half_day_time = $row['half_day_time'];
    $evening_half_time = $row['evening_half_time'] ?? ''; // New field
    $login_time = $row['login_time'];
    $logout_time = $row['logout_time'];
    $saturday_option = $row['saturday_option'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $relaxation_time = mysqli_real_escape_string($conn, $_POST['relaxation_time']);
    $normal_fine = intval($_POST['normal_fine']);
    $extra_fine_time = mysqli_real_escape_string($conn, $_POST['extra_fine_time']);
    $extra_fine = intval($_POST['extra_fine']);
    $half_day_time = mysqli_real_escape_string($conn, $_POST['half_day_time']);
    $evening_half_time = mysqli_real_escape_string($conn, $_POST['evening_half_time']); // New field
    $login_time = mysqli_real_escape_string($conn, $_POST['login_time']);
    $logout_time = mysqli_real_escape_string($conn, $_POST['logout_time']);
    $saturday_option = mysqli_real_escape_string($conn, $_POST['saturday_option']);

    if ($result && mysqli_num_rows($result) > 0) {
        $updateQuery = "
            UPDATE office_timing 
            SET 
                relaxation_time = '$relaxation_time',
                normal_fine = $normal_fine,
                extra_fine_time = '$extra_fine_time',
                extra_fine = $extra_fine,
                half_day_time = '$half_day_time',
                evening_half_time = '$evening_half_time',
                login_time = '$login_time',
                logout_time = '$logout_time',
                saturday_option = '$saturday_option'
            WHERE id = 1";

        if (mysqli_query($conn, $updateQuery)) {
            $message = "Record updated successfully!";
        } else {
            $message = "Error updating record: " . mysqli_error($conn);
        }
    } else {
        $insertQuery = "
            INSERT INTO office_timing (
                relaxation_time, normal_fine, extra_fine_time, extra_fine, half_day_time, evening_half_time, login_time, logout_time, saturday_option
            ) VALUES (
                '$relaxation_time', $normal_fine, '$extra_fine_time', $extra_fine, '$half_day_time', '$evening_half_time', '$login_time', '$logout_time', '$saturday_option'
            )";

        if (mysqli_query($conn, $insertQuery)) {
            $message = "Record inserted successfully!";
        } else {
            $message = "Error inserting record: " . mysqli_error($conn);
        }
    }
}
?>

<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <?php if (!empty($message)) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form name="form1" id="mainForm" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="relaxation_time">Relaxation Time</label>
                                        <input type="time" class="form-control" id="relaxation_time" name="relaxation_time" value="<?php echo htmlspecialchars($relaxation_time); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="normal_fine">Normal Fine</label>
                                        <input type="number" class="form-control" id="normal_fine" name="normal_fine" value="<?php echo htmlspecialchars($normal_fine); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="extra_fine_time">Extra Fine Time</label>
                                        <input type="time" class="form-control" id="extra_fine_time" name="extra_fine_time" value="<?php echo htmlspecialchars($extra_fine_time); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="extra_fine">Extra Fine</label>
                                        <input type="number" class="form-control" id="extra_fine" name="extra_fine" value="<?php echo htmlspecialchars($extra_fine); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="half_day_time">Half Day Time</label>
                                        <input type="time" class="form-control" id="half_day_time" name="half_day_time" value="<?php echo htmlspecialchars($half_day_time); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="evening_half_time">Evening Half Time</label>
                                        <input type="time" class="form-control" id="evening_half_time" name="evening_half_time" value="<?php echo htmlspecialchars($evening_half_time); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="login_time">Login Time</label>
                                        <input type="time" class="form-control" id="login_time" name="login_time" value="<?php echo htmlspecialchars($login_time); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="logout_time">Logout Time</label>
                                        <input type="time" class="form-control" id="logout_time" name="logout_time" value="<?php echo htmlspecialchars($logout_time); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="saturday_option">Saturday Option</label>
                                        <select class="form-control" id="saturday_option" name="saturday_option" required>
                                            <option value="all-on" <?php echo ($saturday_option === 'all-on') ? 'selected' : ''; ?>>All Saturday On</option>
                                            <option value="1st-3rd-on" <?php echo ($saturday_option === '1st-3rd-on') ? 'selected' : ''; ?>>1st & 3rd Saturday On</option>
                                            <option value="all-off" <?php echo ($saturday_option === 'all-off') ? 'selected' : ''; ?>>All Saturday Off</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
</body>
</html>
