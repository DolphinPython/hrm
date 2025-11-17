<?php 


$emp_id_session = $_SESSION['id'];
$conn=connect();
//$id=$_GET['id'];
$query_session="select * from hrm_employee where id='$emp_id_session';";
$result_session=mysqli_query($conn, $query_session) or die(mysqli_error($conn));
$x="";
$row_session=mysqli_fetch_array($result_session);
if($row_session['department_id'] == 4 or $row_session['department_id']==6)
{
    $d="admin-dashboard.php";
    $admin = "Admin";
}
else
{
    $d="employee-dashboard.php";
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
            color:#ffffff;
        }
</style>
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
            
                <!-- Search -->
                <!--<li class="nav-item">-->
                <!--    <div class="top-nav-search">-->
                <!--        <a href="javascript:void(0);" class="responsive-search">-->
                <!--            <i class="fa-solid fa-magnifying-glass"></i>-->
                <!--       </a>-->
                <!--        <form action="search.php">-->
                <!--            <input class="form-control" type="text" placeholder="Search here">-->
                <!--            <button class="btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>-->
                <!--        </form>-->
                <!--    </div>-->
                <!--</li>-->
                <!-- /Search -->
            
                <!-- Flag -->
                <!--<li class="nav-item dropdown has-arrow flag-nav">-->
                <!--    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">-->
                <!--        <img src="assets/img/flags/us.png" alt="Flag" height="20"> <span>English</span>-->
                <!--    </a>-->
                <!--    <div class="dropdown-menu dropdown-menu-right">-->
                <!--        <a href="javascript:void(0);" class="dropdown-item">-->
                <!--            <img src="assets/img/flags/us.png" alt="Flag" height="16"> English-->
                <!--        </a>-->
                <!--        <a href="javascript:void(0);" class="dropdown-item">-->
                <!--            <img src="assets/img/flags/fr.png" alt="Flag" height="16"> French-->
                <!--        </a>-->
                <!--        <a href="javascript:void(0);" class="dropdown-item">-->
                <!--            <img src="assets/img/flags/es.png" alt="Flag" height="16"> Spanish-->
                <!--        </a>-->
                <!--        <a href="javascript:void(0);" class="dropdown-item">-->
                <!--            <img src="assets/img/flags/de.png" alt="Flag" height="16"> German-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</li>-->
                <!-- /Flag -->
                <!--clock-->
                <li>
                    <div class="clock-container">
        <span id="clock"></span>
    </div>
                </li>
            
                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <i class="fa-regular fa-bell"></i> <span class="badge rounded-pill">3</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Notifications</span>
    <?php 
    if($row_session['department_id'] == 4 or $row_session['department_id']==6)
    {
        ?>                  
     <a href="javascript:void(0)" class="clear-noti" href="activities.php"> Add Notification </a>
    <?php } ?>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">


                            <?php 
//$current_month = date("m")-1; 
$current_year = date("Y"); 

//$query="select * from hrm_holidays where year='$current_year';";
$query="select * from hrm_notification;";
$result=mysqli_query($conn, $query) or die(mysqli_error($conn));
$x=0;
while($row=mysqli_fetch_array($result))
{
    $x++;
 
?>                
                                <li class="notification-message">
                                    <a href="activities.php">
                                        <div class="chat-block d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img src="assets/img/profiles/avatar-02.jpg" alt="User Image">
                                            </span>
                                            <div class="media-body flex-grow-1">
                                                <p class="noti-details"><span class="noti-title"><?php echo $row['title']; ?></span><br><?php echo $row['description']; ?></p>
                                                <p class="noti-time"><span class="notification-time"><?php echo $row['date']."-".$row['time']; ?></span></p>
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
                <li class="nav-item dropdown" style="display:none;">
                    <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <i class="fa-regular fa-comment"></i><span class="badge rounded-pill">8</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Messages</span>
                            <a href="javascript:void(0)" class="clear-noti"> Clear All </a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                <li class="notification-message">
                                    <a href="chat.php">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img src="assets/img/profiles/avatar-09.jpg" alt="User Image">
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author">Richard Miles </span>
                                                <span class="message-time">12:28 AM</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="chat.php">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img src="assets/img/profiles/avatar-02.jpg" alt="User Image">
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author">John Doe</span>
                                                <span class="message-time">6 Mar</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="chat.php">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img src="assets/img/profiles/avatar-03.jpg" alt="User Image">
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author"> Tarah Shropshire </span>
                                                <span class="message-time">5 Mar</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="chat.php">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img src="assets/img/profiles/avatar-05.jpg" alt="User Image">
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author">Mike Litorus</span>
                                                <span class="message-time">3 Mar</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="chat.php">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img src="assets/img/profiles/avatar-08.jpg" alt="User Image">
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author"> Catherine Manseau </span>
                                                <span class="message-time">27 Feb</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="chat.php">View all Messages</a>
                        </div>
                    </div>
                </li>
                <!-- /Message Notifications -->

                <li class="nav-item dropdown has-arrow main-drop">
                    <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <span class="user-img"><img src="<?php echo $profile_image; ?>" alt="User Image">
                        <span class="status online"></span></span>
                        <span><?php echo $admin; ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php?id=<?php echo $emp_id_session; ?>">My Profile</a>
                        
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
            <!-- /Header Menu -->
            
            <!-- Mobile Menu -->
            <div class="dropdown mobile-user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="settings.php">Settings</a>
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