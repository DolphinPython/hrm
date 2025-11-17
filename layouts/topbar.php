<?php
$emp_id_session = $_SESSION['id'];
$conn = connect();
//$id=$_GET['id'];
$query_session = "select * from hrm_employee where id='$emp_id_session';";
$result_session = mysqli_query($conn, $query_session) or die(mysqli_error($conn));
$x = "";
$row_session = mysqli_fetch_array($result_session);
if ($row_session['role'] == 'admin' or $row_session['role'] == 'super admin') {
    $d = "admin-dashboard.php";
    $admin = $row_session['fname'];
} else {
    $d = "employee-dashboard.php";
    $admin = $user_detail_array['fname'];
}
// echo $user_detail_array['fname']; ?>
<!-- Header -->
<style>
    .clock-container {
        font-size: 1rem;
        padding: 10px;
        border-radius: 10px;
        background: rgb(121 106 218);
        box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    #global-preloader {
        position: fixed;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #ddd;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<div id="global-preloader">
    <div class="spinner"></div>
</div>

<div class="header">

    <!-- Logo -->
    <!--<div class="header-left">-->
    <!--    <a href="<?php echo $d; ?>" class="logo">-->
    <!--        <img src="assets/img/logo-one-solution.png" style="height:55px;" alt="Logo">-->
    <!--    </a>-->
    <!--    <a href="<?php echo $d; ?>" class="logo collapse-logo">-->
    <!--        <img src="assets/img/logo-one-solution.png" alt="Logo">-->
    <!--    </a>-->
    <!--    <a href="<?php echo $d; ?>" class="logo2">-->
    <!--        <img src="assets/img/logo-one-solution.png" width="40" height="35" alt="Logo">-->
    <!--    </a>-->
    <!--</div>-->
    <!-- /Logo -->

    <a id="toggle_btn" href="javascript:void(0);">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>

    <!-- Header Title -->
    <div class="page-title-box">
        <h3>HRMPULSE</h3>
    </div>
    <!-- /Header Title -->

    <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa-solid fa-bars"></i></a>

    <!-- Header Menu -->
    <ul class="nav user-menu">
        <li>
            <a href="https://hrmpulse.com/chat_page.php">Chat Room</a>
        </li>
        <!--clock-->
        <li>
            <div class="clock-container">
                <span id="clock"></span>
            </div>
        </li>

        <!-- Notifications -->
        <?php
        // Fetch total notification count
        $query_count = "SELECT COUNT(*) AS total_count FROM hrm_notification";
        $result_count = mysqli_query($conn, $query_count);
        $row_count = mysqli_fetch_assoc($result_count);
        $total_notifications = $row_count['total_count'];
        ?>

        <li class="nav-item dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <i class="fa-regular fa-bell"></i>
                <span class="badge rounded-pill"><?php echo $total_notifications; ?></span>
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifications</span>
                    <?php
                    if ($row_session['department_id'] == 4 || $row_session['department_id'] == 6) { ?>
                        <a href="javascript:void(0)" class="clear-noti" href="activities.php"> Add Notification </a>
                    <?php } ?>
                </div>
                <div class="noti-content">
                    <ul class="notification-list">
                        <?php
                        $query = "SELECT * FROM hrm_notification ORDER BY date DESC, time DESC";
                        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

                        while ($row = mysqli_fetch_array($result)) { ?>
                            <li class="notification-message">
                                <a href="activities.php">
                                    <div class="chat-block d-flex">
                                        <span class="avatar flex-shrink-0">
                                            <img src="assets/img/profiles/avatar-02.jpg" alt="User Image">
                                        </span>
                                        <div class="media-body flex-grow-1">
                                            <p class="noti-details">
                                                <span class="noti-title"><?php echo $row['title']; ?></span><br>
                                                <?php echo $row['description']; ?>
                                            </p>
                                            <p class="noti-time">
                                                <span
                                                    class="notification-time"><?php echo $row['date'] . " - " . $row['time']; ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="topnav-dropdown-footer">
                    <a href="activities.php">View all Notifications</a>
                </div>
            </div>
        </li>

        <!-- /Notifications -->

        <!-- Message Notifications -->

        <!-- /Message Notifications -->

        <li class="nav-item dropdown has-arrow main-drop">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <span class="user-img"><img src="<?php echo $profile_image; ?>" alt="User Image">
                    <span class="status online"></span></span>
                <span><?php echo $admin; ?></span>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="#" onclick="submitProfileForm(<?php echo $emp_id_session; ?>)">My
                    Profile</a>
                <?php
                if ($row_session['role'] == 'admin' or $row_session['role'] == 'super admin') {
                    echo ' <a class="dropdown-item" href="admin-dashboard.php">Admin Dashboard</a>
        <a class="dropdown-item" href="employee-dashboard.php">User Dashboard</a>';

                }
                ?>
                <a class="dropdown-item" href="logout.php">Logout</a>
            </div>
        </li>
    </ul>
    <!-- /Header Menu -->

    <!-- Mobile Menu -->
    <div class="dropdown mobile-user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                class="fa-solid fa-ellipsis-vertical"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <!--<a class="dropdown-item" href="profile.php">My Profile</a>-->
            <a class="dropdown-item" href="#" onclick="submitProfileForm(<?php echo $emp_id_session; ?>)">My Profile</a>
            <?php
            if ($row_session['role'] == 'admin' or $row_session['role'] == 'super admin') {
                echo ' <a class="dropdown-item" href="admin-dashboard.php">Admin Dashboard</a>
        <a class="dropdown-item" href="employee-dashboard.php">User Dashboard</a>';

            }
            ?>
            <!--<a class="dropdown-item" href="settings.php">Settings</a>-->
            <a class="dropdown-item" href="index.php">Logout</a>
        </div>
    </div>
    <!-- /Mobile Menu -->

</div>
<!-- /Header -->
<!--clock script-->
<script>
    function updateClock() {
        let now = new Date();
        let options = { timeZone: 'Asia/Kolkata', hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' };
        let timeString = new Intl.DateTimeFormat('en-US', options).format(now);
        document.getElementById("clock").textContent = timeString;
    }

    setInterval(updateClock, 1000); // Update clock every second
    updateClock(); // Initial call to avoid 1-second delay
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Show preloader before the page loads
        let preloader = document.getElementById("global-preloader");
        preloader.style.display = "flex";

        // Hide preloader when the page is fully loaded
        window.addEventListener("load", function () {
            preloader.style.display = "none";
        });
    });

</script>
<script>
    function submitProfileForm(empId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'profile.php';
        //   form.target = '_self'; // open in new tab (remove this if same tab is needed)

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = empId;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
</script>