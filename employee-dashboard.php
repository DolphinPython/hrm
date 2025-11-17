<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

// get user name and other detail
$emp_id = $_SESSION['id'];
$conn = connect();
//$id=$_GET['id'];
$query = "select * from hrm_employee where id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$x = "";
$row = mysqli_fetch_array($result);
//echo "aaaaaaaaaaaaaaaa=".$query;

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;

$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];
//count_where($table, $column, $value)
//{
//$conn=connect();
//$query="select count(*) from $table where $column='$id'";
$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");

//echo "profile_image".$profile_image;
?>


<head>

    <title>Employee Dashboard - HRMS admin template</title>

    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>


    <style>
        .event-list {
            scrollbar-width: thin;
            scrollbar-color: #007bff #f1f1f1;
        }

        .event-list::-webkit-scrollbar {
            width: 8px;
        }

        .event-list::-webkit-scrollbar-thumb {
            background-color: #007bff;
            border-radius: 4px;
        }

        .event-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* .month-of-employee{
        max-width: 50% !important;
    } */

        .calendar-container {
            /*width: 100%;*/
            /*height: 100%;*/
            /*background: white;*/
            /*border-radius: 10px;*/
            /*box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);*/
            /*padding: 15px;*/
            /*text-align: center;*/
            /*display: flex;*/
            /*flex-direction: column;*/
        }

        .calendar-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-dayscustom {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            flex-grow: 1;
            margin-top: 10px;
        }

        .daycustom,
        .day-namecustom {
            padding: 8px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .day-namecustom {
            color: #444;
            background: #f2f2f2;
        }

        .daycustom:hover {
            background: #ff6f61;
            color: white;
        }

        .current-daycustom {
            background: orange !important;
            color: white !important;
        }

        .holidaycustom {
            background: red !important;
            color: white !important;
            position: relative;
        }

        .holidaycustom::after {
            content: attr(data-holiday);
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 3px 6px;
            font-size: 12px;
            border-radius: 3px;
            display: none;
        }

        .holidaycustom:hover::after {
            display: block;
        }
        .info-card1{
            /*background:#14a5fc;*/
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>
        <!-- Page Wrapper -->
        <div class="page-wrapper">

            <!-- Page Content -->
            <div class="content container-fluid pb-0">

<!--<div class=" py-4">-->
<!--    <div class="card w-100  border-0 rounded-4 overflow-hidden">-->
        
        <!-- Card Header -->
<!--        <div class="card-header bg-success text-white text-center py-3">-->
<!--            <h5 class="mb-0 fw-bold">Punch In/Out ⬇️</h5>-->
<!--        </div>-->
        
        <!-- Card Body -->
<!--        <div class="card-body bg-white">-->
<!--            <?php-->
<!--            $query = "SELECT * FROM newuser_attendance WHERE user_id='$emp_id' AND DATE(clock_in_time)=CURDATE() ORDER BY clock_in_time DESC LIMIT 1";-->
<!--            $result = $conn->query($query);-->
<!--            $row = $result->fetch_assoc();-->
<!--            $status = isset($row['status']) ? $row['status'] : 'logout';-->

<!--            $login_time = isset($row['clock_in_time']) ? $row['clock_in_time'] : '';-->
<!--            ?>-->
            
<!--            <div class="row g-4 align-items-center">-->

                <!-- Clock-In Button and Timer -->
<!--                <div class="col-md-4 text-center">-->
<!--                    <input type="hidden" id="login_time" value="<?= isset($login_time) ? $login_time : null; ?>">-->
<!--                    <input type="hidden" id="status" value="<?= ($status === 'login') ? 'login' : 'logout'; ?>">-->
                    
<!--                    <a href="javascript:void(0);" class="btn btn-lg btn-primary px-4 py-2 d-flex justify-content-center align-items-center gap-2 rounded-3 shadow-sm" id="clock_in_btn" data-status="<?= ($status === 'login') ? 'login' : 'logout'; ?>">-->
<!--                        <img src="assets/img/icons/clock-in.svg" alt="Icon" style="height: 24px;">-->
<!--                        <?php-->
<!--                        if (isset($row['status'])) {-->
<!--                            if ($row['status'] === 'login') {-->
<!--                                echo 'Clock-Out';-->
<!--                            } else {-->
<!--                                echo 'Clock-In';-->
<!--                            }-->
<!--                        } else {-->
<!--                            echo 'Clock-In';-->
<!--                        }-->
<!--                        ?>-->
<!--                    </a>-->
                    
<!--                    <h3 class="timer mt-3 text-dark" id="timer"></h3>-->
<!--                </div>-->

                <!-- Time Info -->
<!--                <div class="col-md-5">-->
<!--                    <ul class="list-group list-group-flush">-->
<!--                        <li class="list-group-item d-flex justify-content-between">-->
<!--                            <strong>Today In Time:</strong>-->
<!--                            <span><?= isset($row['clock_in_time']) ? $row['clock_in_time'] : 'Not logged in today'; ?></span>-->
<!--                        </li>-->
<!--                        <li class="list-group-item d-flex justify-content-between">-->
<!--                            <strong>Today Out Time:</strong>-->
<!--                            <span><?= isset($row['clock_out_time']) ? $row['clock_out_time'] : 'Not logged out yet'; ?></span>-->
<!--                        </li>-->
<!--                        <li class="list-group-item d-flex justify-content-between">-->
<!--                            <strong>Break:</strong>-->
<!--                            <span>01:20 PM To 02:00 PM</span>-->
<!--                        </li>-->
<!--                    </ul>-->
<!--                </div>-->

                <!-- View Attendance Link -->
<!--                <div class="col-md-3 text-center">-->
<!--                    <a href="attendance-report-employee-hrm.php" class="btn btn-outline-success rounded-pill px-4 py-2 fw-semibold shadow-sm">-->
<!--                        View Attendance <i class="fe fe-arrow-right-circle ms-1"></i>-->
<!--                    </a>-->
<!--                </div>-->

<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<div class="py-4">
    <div class="card w-100 border-0 rounded-4 overflow-hidden">
        
        <!-- Card Header -->
        <div class="card-header bg-success text-white text-center py-3">
            <h5 class="mb-0 fw-bold">Punch In/Out ⬇️</h5>
        </div>
        
        <!-- Card Body -->
        <div class="card-body bg-white">
            <?php
            $query = "SELECT * FROM newuser_attendance WHERE user_id='$emp_id' AND DATE(clock_in_time)=CURDATE() ORDER BY clock_in_time DESC LIMIT 1";
            $result = $conn->query($query);
            $row = $result->fetch_assoc();
            $status = isset($row['status']) ? $row['status'] : 'logout';
            $login_time = isset($row['clock_in_time']) ? $row['clock_in_time'] : '';
            ?>
            
            <div class="row g-4 align-items-center">

                <!-- Clock-In Button and Timer -->
                <div class="col-md-4 text-center">
                    <input type="hidden" id="login_time" value="<?= $login_time; ?>">
                    <input type="hidden" id="status" value="<?= $status; ?>">
                    
                    
                    


<!-- Temparory Section Start -->
<div class="">
    <div class="location-status">
        <div>
            <span id="location-status-text">Checking your location...</span>
        </div>
    </div>
    
    <!-- Div A  -->
    <div id="divA" style="display:none;">
        <a href="javascript:void(0);" 
            class="btn btn-lg btn-outline-warning px-4 py-2 d-flex justify-content-center align-items-center gap-2 rounded-3 shadow-sm" 
            id="clock_in_btn" 
            data-status="<?= $status; ?>">
            
            <span class="btn-text d-flex align-items-center gap-2 ">
                <img src="assets/img/icons/clock-in.svg" alt="Icon" style="height: 24px;">
                <?= ($status === 'login') ? 'Clock-Out' : 'Clock-In'; ?>
            </span>
            
            <span class="spinner-border spinner-border-sm text-light d-none" role="status" id="loadingSpinner"></span>
        </a>
    </div>

    <!-- Div B  -->
    <div id="divB" style="display:none;">
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle me-2"></i>
            You must be within <span id="maxDistanceText"></span> of the office to clock in/out.
        </div>
    </div>

    <div class="distance-info" id="distance-info">
        <!-- <i class="fas fa-ruler-combined me-2"></i> -->
        <span id="distance-text">Calculating distance to office...</span>
    </div>
</div>

<script>
    // Office location coordinates
    const officeLat = 28.634281440118656;
    const officeLon = 77.28287964624323;
    
    // Maximum allowed distance from office (in meters)
    const maxDistance = 50;
    
    let userLat = null;
    let userLon = null;
    let distance = null;
    
    document.getElementById('maxDistanceText').textContent = maxDistance + 'm';

    // Function to calculate distance between two points (Haversine Formula)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const earthRadius = 6371000; // Radius of Earth in meters

        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);

        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
                
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return earthRadius * c;
    }

    function deg2rad(deg) {
        return deg * (Math.PI/180);
    }
    
    // Get user's location
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    userLat = position.coords.latitude;
                    userLon = position.coords.longitude;
                    
                    // Calculate distance to office
                    distance = calculateDistance(userLat, userLon, officeLat, officeLon);
                    
                    // Update UI
                    updateLocationStatus(distance <= maxDistance);
                    updateDistanceText(distance);
                    toggleDivs(distance <= maxDistance);
                },
                function(error) {
                    console.error("Error getting location:", error);
                    document.getElementById('location-status-text').innerHTML = 
                        '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Could not determine location</span>';
                    
                    // Default : agar location error hai to div B dikhado
                    toggleDivs(false);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        } else {
            alert("Geolocation is not supported by this browser.");
            toggleDivs(false);
        }
    }
    
    // Update location status UI
    function updateLocationStatus(isWithinRange) {
        const statusElement = document.getElementById('location-status-text');
        
        if (isWithinRange) {
            // statusElement.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i> You are within office range</span>';
            statusElement.innerHTML = '<span class="text-success"></span>';
        } else {
            // statusElement.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> You are not in office range</span>';
            statusElement.innerHTML = '<span class="text-danger"></span>';
        }
    }
    
    // Update distance text
    function updateDistanceText(distance) {
        const distanceElement = document.getElementById('distance-text');
        distanceElement.textContent = `You are ${Math.round(distance)} meters from the office`;
        // distanceElement.textContent = ``;
    }

    // ✅ Ye function bas divA/divB toggle karega
    function toggleDivs(isWithinRange) {
        if (isWithinRange) {
            document.getElementById('divA').style.display = 'block';
            document.getElementById('divB').style.display = 'none';
        } else {
            document.getElementById('divA').style.display = 'none';
            document.getElementById('divB').style.display = 'block';
        }
    }
    
    // Initialize the page
    window.onload = function() {
        getLocation();
    };
</script>
<!-- // Temparory Section End -->

                    
                    
                    <!--<a href="javascript:void(0);" 
                       class="btn btn-lg btn-outline-warning px-4 py-2 d-flex justify-content-center align-items-center gap-2 rounded-3 shadow-sm" 
                       id="clock_in_btn" 
                       data-status="<?= $status; ?>">
                       
                        <span class="btn-text d-flex align-items-center gap-2 ">
                            <img src="assets/img/icons/clock-in.svg" alt="Icon" style="height: 24px;">
                            <?= ($status === 'login') ? 'Clock-Out' : 'Clock-In'; ?>
                        </span>
                        
                        <span class="spinner-border spinner-border-sm text-light d-none" role="status" id="loadingSpinner"></span>
                    </a>-->
                    
                    <h3 class="timer mt-3 text-dark" id="timer"></h3>
                </div>

                <!-- Time Info -->
                <div class="col-md-5">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Today In Time:</strong>
                            <span><?= isset($row['clock_in_time']) ? $row['clock_in_time'] : 'Not logged in today'; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Today Out Time:</strong>
                            <span><?= isset($row['clock_out_time']) ? $row['clock_out_time'] : 'Not logged out yet'; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Break:</strong>
                            <span>01:20 PM To 02:00 PM</span>
                        </li>
                    </ul>
                </div>

                <!-- View Attendance Link -->
                <div class="col-md-3 text-center">
                    <a href="attendance-report-employee-hrm.php" class="btn btn-outline-success rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        View Attendance <i class="fe fe-arrow-right-circle ms-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
            
                <!--start special day notification -->
                <div class=" pb-4">
  <div class="row g-4">

    <!-- Events Of Day -->
    <div class="col-12 col-md-4">
      <div class="card  border-0 rounded-4 h-100">
        <div class="card-header bg-primary text-white text-center rounded-top-4">
          <h5 class="mb-0">Events Of Day</h5>
        </div>
        <div class="card-body event-list bg-light" style="max-height: 220px; overflow-y: auto;">
          <?php
          $today = date('Y-m-d');
          $todayMonth = date('m');
          $todayDay = date('d');
          $eventsFound = false;

          echo "<p class='text-muted'>Today's date: <strong>" . date('F j, Y', strtotime($today)) . "</strong></p>";
          echo "<p class='text-muted'>Today is <strong>" . date('l', strtotime($today)) . "</strong>.</p>";

          $sql = "SELECT fname, lname, dob, doj 
                  FROM hrm_employee 
                  WHERE (MONTH(dob) = ? AND DAY(dob) = ?) 
                     OR (MONTH(doj) = ? AND DAY(doj) = ?)";
          if ($stmt = $conn->prepare($sql)) {
              $stmt->bind_param("iiii", $todayMonth, $todayDay, $todayMonth, $todayDay);
              $stmt->execute();
              $result = $stmt->get_result();
              if ($result->num_rows > 0) {
                  $eventsFound = true;
                  while ($row = $result->fetch_assoc()) {
                      $fname = htmlspecialchars($row['fname']);
                      $lname = htmlspecialchars($row['lname']);

                      $isBirthday = (date('m-d', strtotime($row['dob'])) === "{$todayMonth}-{$todayDay}");
                      $isWorkAnniversary = (date('m-d', strtotime($row['doj'])) === "{$todayMonth}-{$todayDay}");

                      if ($isBirthday && $isWorkAnniversary) {
                          echo "<div class='alert alert-info p-2 mb-2'>🎉 Happy Birthday & Work Anniversary, <strong>{$fname} {$lname}</strong>!</div>";
                      } elseif ($isBirthday) {
                          echo "<div class='alert alert-success p-2 mb-2'>🎂 Happy Birthday, <strong>{$fname} {$lname}</strong>!</div>";
                      } elseif ($isWorkAnniversary) {
                          echo "<div class='alert alert-primary p-2 mb-2'>🏆 Work Anniversary, <strong>{$fname} {$lname}</strong>!</div>";
                      }
                  }
              }
              $stmt->close();
          } else {
              die("Failed to prepare statement: " . $conn->error);
          }

          if (!$eventsFound) {
              echo "<div class='alert alert-secondary p-2'>Enjoy your day!</div>";
          }
          ?>
        </div>
      </div>
    </div>

    <!-- Upcoming Events -->
    <div class="col-12 col-md-4">
      <div class="card  border-0 rounded-4 h-100">
        <div class="card-header bg-success text-white text-center rounded-top-4">
          <h5 class="mb-0">Upcoming Events</h5>
        </div>
        <div class="card-body event-list bg-light" style="max-height: 220px; overflow-y: auto;">
          <?php
          $today = date('Y-m-d');
          $nextMonthDate = date('Y-m-d', strtotime('+30 days'));
          $sql = "SELECT fname, lname, dob, doj FROM hrm_employee WHERE archive_status=0";
          if ($stmt = $conn->prepare($sql)) {
              $stmt->execute();
              $result = $stmt->get_result();

              $events = [];

              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      $fname = htmlspecialchars($row['fname']);
                      $lname = htmlspecialchars($row['lname']);

                      $dob = strtotime($row['dob']);
                      $doj = strtotime($row['doj']);

                      // Birthday
                      $currentYearDob = strtotime(date('Y') . '-' . date('m-d', $dob));
                      if ($currentYearDob < strtotime($today)) {
                          $currentYearDob = strtotime((date('Y') + 1) . '-' . date('m-d', $dob));
                      }
                      $daysToDob = ($currentYearDob - strtotime($today)) / 86400;

                      if ($daysToDob > 0 && $daysToDob <= 30) {
                          $events[] = [
                              'type' => 'birthday',
                              'name' => $fname,
                              'lname' => $lname,
                              'date' => $currentYearDob,
                              'formatted_date' => date('F j', $currentYearDob),
                              'days' => $daysToDob,
                          ];
                      }

                      // Work Anniversary
                      $currentYearDoj = strtotime(date('Y') . '-' . date('m-d', $doj));
                      if ($currentYearDoj < strtotime($today)) {
                          $currentYearDoj = strtotime((date('Y') + 1) . '-' . date('m-d', $doj));
                      }
                      $daysToDoj = ($currentYearDoj - strtotime($today)) / 86400;

                      if ($daysToDoj > 0 && $daysToDoj <= 30) {
                          $events[] = [
                              'type' => 'work_anniversary',
                              'name' => $fname,
                              'lname' => $lname,
                              'date' => $currentYearDoj,
                              'formatted_date' => date('F j', $currentYearDoj),
                              'days' => $daysToDoj,
                          ];
                      }
                  }

                  usort($events, function ($a, $b) {
                      return $a['days'] - $b['days'];
                  });

                  foreach ($events as $event) {
                      if ($event['type'] === 'birthday') {
                          echo "<div class='alert alert-success p-2 mb-2'>🎂 Upcoming Birthday: <strong>{$event['name']} {$event['lname']}</strong> on {$event['formatted_date']}!</div>";
                      } elseif ($event['type'] === 'work_anniversary') {
                          echo "<div class='alert alert-primary p-2 mb-2'>🏆 Work Anniversary: <strong>{$event['name']} {$event['lname']}</strong> on {$event['formatted_date']}!</div>";
                      }
                  }
              } else {
                  echo "<div class='alert alert-secondary p-2'>No upcoming events in the next month.</div>";
              }

              $stmt->close();
          } else {
              die("Failed to prepare statement: " . $conn->error);
          }
          ?>
        </div>
      </div>
    </div>

   

    <div class="col-12 col-md-4">
      <div class="card  border-0 rounded-4 h-100">
         <div class="card-header bg-primary text-white text-center rounded-top-4">
          <h5 class="mb-0">Calendar</h5>
        </div>
            <div class="card-body"  >
                <div class="calendar-container">
                    <div class="calendar-header-custom">
                        <button class="btn btn-outline-secondary btn-sm" id="prevMonthcustom">❮</button>
                        <h5 id="monthYearcustom"></h5>
                        <button class="btn btn-outline-secondary btn-sm"id="nextMonthcustom">❯</button>
                    </div>
                        <div class="calendar-dayscustom" id="calendarcustom">
                            <!-- Days will be generated here -->
                        </div>
                </div>
            </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card  border-0 rounded-4 h-100">
         <div class="card-header bg-primary text-white text-center rounded-top-4">
          <h5 class="mb-0">Leaves</h5>
        </div>

         <!-- other -->
                                    <div class="card-body">
                                        <div class="statistic-header">
                                         
                                        </div>
                                        <div class="attendance-list">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="attendance-details">
                                                        <h4 class="text-primary">
                                                            <?php echo total_leave_by_id($emp_id); ?>
                                                        </h4>
                                                        <p>Total Leaves</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="attendance-details">
                                                        <h4 class="text-pink">
                                                            <?php
                                                            echo display_leave_by_type(1, $emp_id); ?>
                                                        </h4>
                                                        <p>Casual Leave</p>
                                                    </div>
                                                </div>
                                              
                                                <div class="col-md-4">
                                                    <div class="attendance-details">
                                                        <h4 class="text-purple">
                                                            <?php
                                                            echo display_leave_by_type(3, $emp_id); ?>
                                                        </h4>
                                                        <p>Loss Of Pay</p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="view-attendance">
                                            <a href="leaves-employee.php">
                                                Apply Leave <i class="fe fe-arrow-right-circle"></i>
                                            </a>
                                        </div>
                                    </div>

                                <!-- other -->

                                


        </div>
    </div>

      <div class="col-12 col-md-4">
      <div class="card  border-0 rounded-4 h-100">
         <div class="card-header bg-primary text-white text-center rounded-top-4">
          <h5 class="mb-0">Attendance</h5>
        </div>

        <!-- <div class="card flex-fill"> -->
                                    <div class="card-body">
                                        <div class="statistic-header">
                                            <!-- <h4>Attendance</h4> -->
                                            <!--<div class="dropdown statistic-dropdown">-->
                                            <!--    <a class="dropdown-toggle" data-bs-toggle="dropdown"-->
                                            <!--        href="javascript:void(0);">-->
                                            <!--        This Month-->
                                            <!--    </a>-->

                                            <!--</div>-->
                                        </div>
                                        <div class="attendance-list">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="attendance-details">
                                                        <h4 class="text-primary">

                                                            <?php $current_month = date("m") - 1;
                                                            $current_year = date("Y");
                                                            //echo $current_month.$current_year.$emp_id;
                                                            
                                                            $query = "select * from hrm_attandance_machine_detail join hrm_employee on 
	                                                            hrm_attandance_machine_detail.id = hrm_employee.attendance_id 
	                                                            where hrm_employee.id='$emp_id' and hrm_attandance_machine_detail.month='$current_month' 
	                                                            and hrm_attandance_machine_detail.year='$current_year';";

                                                            //echo $query;
                                                            echo total_late_in_current_month($emp_id, $current_month, $current_year);
                                                            ?>

                                                        </h4>
                                                        <p>Total Late</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="attendance-details">
                                                        <h4 class="text-pink">

                                                            <?php
                                                            echo total_in_time_late_in_current_month($emp_id, $current_month, $current_year);
                                                            ?>

                                                        </h4>
                                                        <p>Late Arrival</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="attendance-details">
                                                        <h4 class="text-success">

                                                            <?php
                                                            echo total_out_time_late_in_current_month($emp_id, $current_month, $current_year);
                                                            ?>
                                                        </h4>
                                                        <p>Before Time Departure</p>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="attendance-details">
                                                        <h4 class="text-purple">
                                                            <?php
                                                            echo total_days_present_in_current_month($emp_id, $current_month, $current_year);
                                                            ?>

                                                        </h4>
                                                        <p>Total Days Present</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="attendance-details">
                                                        <h4 class="text-primary">
                                                            <?php
                                                            echo total_days_abscent_in_current_month($emp_id, $current_month, $current_year);
                                                            ?>

                                                        </h4>
                                                        <p>Total Abscent <br>(abscent + holidays)</p>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="attendance-details">
                                                        <h4 class="text-danger">
                                                            <?php
                                                            echo total_out_time_missing_in_current_month($emp_id, $current_month, $current_year);
                                                            ?>

                                                        </h4>
                                                        <p>Total Out Time Missing</p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="view-attendance">
                                            <a href="attendance-report-employee.php">
                                                Attendance Detail <i class="fe fe-arrow-right-circle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

        
    <!-- </div> -->
    </div>

        <div class="col-12 col-md-4">
      <div class="card  border-0 rounded-4 h-100">
         <div class="card-header bg-primary text-white text-center rounded-top-4">
          <h5 class="mb-0">Important</h5>
        </div>
         <div class="card-body">
                                <div class="statistic-header">
                                    <!-- <h4>Important</h4> -->
                                    <div class="important-notification">
                                        <a href="activities.php">
                                            View All <i class="fe fe-arrow-right-circle"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="notification-tab">
                                    <ul class="nav nav-tabs">
                                        <li>
                                            <a href="#" class="active" data-bs-toggle="tab"
                                                data-bs-target="#notification_tab">
                                                <i class="la la-bell"></i> Notifications
                                            </a>
                                        </li>
                                        <!-- <li>
                                            <a href="#" data-bs-toggle="tab" data-bs-target="#schedule_tab">
                                                <i class="la la-list-alt"></i> Schedules
                                            </a>
                                        </li> -->
                                    </ul>
                                    <?php

                                    $userId = $_SESSION['id']; // Replace with actual logged-in user ID
                                    $query = "SELECT * FROM hrm_notification WHERE FIND_IN_SET(?, send_to) > 0";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("i", $userId);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    // Fetch notifications
                                    $notifications = $result->fetch_all(MYSQLI_ASSOC);
                                    ?>

                                    <div class="tab-content">
                                        <div class="tab-pane active" id="notification_tab">
                                            <div class="employee-noti-content">
                                                <ul class="employee-notification-list">
                                                    <?php foreach ($notifications as $notification): ?>
                                                        <li class="employee-notification-grid">
                                                            <div class="employee-notification-icon">
                                                                <a href="activities.php">
                                                                    <span
                                                                        class="badge-soft-<?php echo strtolower($notification['sent_by']); ?> rounded-circle">
                                                                        <?php echo strtoupper(substr($notification['sent_by'], 0, 2)); ?>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                            <div class="employee-notification-content">
                                                                <h6>
                                                                    <a href="activities.php">
                                                                        <?php echo htmlspecialchars($notification['title']); ?>
                                                                    </a>
                                                                </h6>
                                                                <ul class="nav">
                                                                    <li><?php echo htmlspecialchars($notification['time']); ?>
                                                                    </li>
                                                                    <li><?php echo htmlspecialchars(date("d M Y", strtotime($notification['date']))); ?>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

        </div>
        </div>

    

  </div>
</div>

    


                <!-- end special day notification -->

                <div class="row">
                    <div class="col-xxl-8 col-lg-12 col-md-12">
                        <div class="row">

                            <!-- Employee Details -->
                            <div class="col-lg-6 col-md-12">
                                <!--<div class="card employee-welcome-card flex-fill">-->
                                <!--    <div class="card-body">-->
                                <!--        <div class="welcome-info">-->
                                <!--            <div class="welcome-content">-->
                                <!--                <h4>Welcome Back, <?php echo $user_detail_array['fname']; ?></h4>-->
                                                <!-- <p>You have <span>4 meetings</span> today,</p> -->
                                <!--            </div>-->
                                <!--            <div class="welcome-img">-->
                                <!--                <img src="<?php echo $profile_image; ?>" class="img-fluid" alt="User">-->
                                <!--            </div>-->
                                <!--        </div>-->
                                <!--        <div class="welcome-btn">-->
                                <!--            <a href="profile.php?id=<?php echo $_SESSION['id']; ?>" class="btn">View-->
                                <!--                Profile</a>-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->
                               <?php
                                $holidays = [];
                                $currentYear = date("Y");
                                $sql = "SELECT name, date, no_of_days FROM hrm_holidays WHERE YEAR(STR_TO_DATE(date, '%d-%m-%Y')) = $currentYear";
                                $result = $conn->query($sql);
                                
                                while ($row = $result->fetch_assoc()) {
                                    // Convert DD-MM-YYYY to YYYY-MM-DD
                                    $formattedDate = date("Y-m-d", strtotime($row['date']));
                                    $holidays[$formattedDate] = [
                                        "name" => $row['name'], 
                                        "days" => $row['no_of_days']
                                    ];
                                }
                                
                                
                                ?>
                                

                               
                             

                                <!--<div class="card info-card flex-fill">-->
                                <!--    <div class="card-body">-->
                                <!--        <h4>Upcoming Holidays</h4>-->
                                <!--        <div class="holiday-details">-->
                                <!--            <div class="holiday-calendar">-->
                                <!--                <div class="holiday-calendar-icon">-->
                                <!--                    <img src="assets/img/icons/holiday-calendar.svg" alt="Icon">-->
                                <!--                </div>-->
                                                <!-- <div class="holiday-calendar-content">
                                <!--                    <h6>Ramzan</h6>-->
                                <!--                    <p>Mon 20 May 2024</p>-->
                                <!--                </div> -->
                                <!--            </div>-->
                                <!--            <div class="holiday-btn">-->
                                <!--                <a href="holidays.php" class="btn">View All</a>-->
                                <!--            </div>-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->
                            </div>
                            <!-- /Employee Details -->

                            <!-- Attendance & Leaves -->
                            <div class="col-lg-6 col-md-12">
                                


                               


                            </div>
                            <!-- /Attendance & Leaves -->

                        </div>
                    </div>

                    <!-- Employee Notifications -->
                    <div class="col-xxl-4 col-lg-12 col-md-12 d-flex">
                        <div class="card flex-fill">
                           
                        </div>
                    </div>


                </div>

                <!-- <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-sm-8">
                                        <div class="statistic-header">
                                            <h4>On Going Projects</h4>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 text-sm-end">
                                        <div class="owl-nav project-nav nav-control"></div>
                                    </div>
                                </div>
                                <div class="project-slider owl-carousel">

                                   
                                    <div class="project-grid">
                                        <div class="project-top">
                                            <h6>
                                                <a href="project-view.php">Deadline : 10 Feb 2024</a>
                                            </h6>
                                            <h5>
                                                <a href="project-view.php">Office Management</a>
                                            </h5>
                                            <p>Creating an online platform that enables various administrative  tasks within an organization</p>
                                        </div>
                                        <div class="project-middle">
                                            <ul class="nav">
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>20</h4>
                                                        <p>Total Tasks</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>15</h4>
                                                        <p>Total Completed</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="project-bottom">
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Project Leader :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Jeffery Lalor" data-bs-original-title="Jeffery Lalor">
                                                            <img src="assets/img/avatar/avatar-19.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Members :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Lesley Grauer" data-bs-original-title="Lesley Grauer">
                                                            <img src="assets/img/avatar/avatar-20.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Richard Miles" data-bs-original-title="Richard Miles">
                                                            <img src="assets/img/avatar/avatar-21.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Loren Gatlin" data-bs-original-title="Loren Gatlin">
                                                            <img src="assets/img/avatar/avatar-1.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Jeffery Lalor" data-bs-original-title="Jeffery Lalor">
                                                            <img src="assets/img/avatar/avatar-16.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Tarah Shropshire" data-bs-original-title="Tarah Shropshire">
                                                            <img src="assets/img/avatar/avatar-23.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="more-team-members">+16</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="project-grid">
                                        <div class="project-top">
                                            <h6>
                                                <a href="project-view.php">Deadline : 15 Feb 2024</a>
                                            </h6>
                                            <h5>
                                                <a href="project-view.php">Video Calling App</a>
                                            </h5>
                                            <p>Design and developing a software application that enables users to make video calls over the internet.</p>
                                        </div>
                                        <div class="project-middle">
                                            <ul class="nav">
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>30</h4>
                                                        <p>Total Tasks</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>12</h4>
                                                        <p>Total Completed</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="project-bottom">
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Project Leader :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Catherine Manseau" data-bs-original-title="Catherine Manseau">
                                                            <img src="assets/img/avatar/avatar-18.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Members :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Richard Miles" data-bs-original-title="Richard Miles">
                                                            <img src="assets/img/avatar/avatar-21.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Jeffery Lalor" data-bs-original-title="Jeffery Lalor">
                                                            <img src="assets/img/avatar/avatar-16.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Lesley Grauer" data-bs-original-title="Lesley Grauer">
                                                            <img src="assets/img/avatar/avatar-20.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Loren Gatlin" data-bs-original-title="Loren Gatlin">
                                                            <img src="assets/img/avatar/avatar-1.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Tarah Shropshire" data-bs-original-title="Tarah Shropshire">
                                                            <img src="assets/img/avatar/avatar-23.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="more-team-members">+10</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="project-grid">
                                        <div class="project-top">
                                            <h6>
                                                <a href="project-view.php">Deadline : 12 Apr 2024</a>
                                            </h6>
                                            <h5>
                                                <a href="project-view.php">Hospital Administration</a>
                                            </h5>
                                            <p>Creating an online platform that serves as a central hub for hospital admin, staff, patients.</p>
                                        </div>
                                        <div class="project-middle">
                                            <ul class="nav">
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>40</h4>
                                                        <p>Total Tasks</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>02</h4>
                                                        <p>Total Completed</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="project-bottom">
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Project Leader :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="John Smith" data-bs-original-title="John Smith">
                                                            <img src="assets/img/avatar/avatar-4.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Members :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Richard Miles" data-bs-original-title="Richard Miles">
                                                            <img src="assets/img/avatar/avatar-6.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Jeffery Lalor" data-bs-original-title="Jeffery Lalor">
                                                            <img src="assets/img/avatar/avatar-13.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Lesley Grauer" data-bs-original-title="Lesley Grauer">
                                                            <img src="assets/img/avatar/avatar-18.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Tarah Shropshire" data-bs-original-title="Tarah Shropshire">
                                                            <img src="assets/img/avatar/avatar-23.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Loren Gatlin" data-bs-original-title="Loren Gatlin">
                                                            <img src="assets/img/avatar/avatar-1.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="more-team-members">+12</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="project-grid">
                                        <div class="project-top">
                                            <h6>
                                                <a href="project-view.php">Deadline : 25 Apr 2024</a>
                                            </h6>
                                            <h5>
                                                <a href="project-view.php">Digital Marketpace</a>
                                            </h5>
                                            <p>Creating an online platform that connects sellers with buyers, facilitating the exchange of goods,</p>
                                        </div>
                                        <div class="project-middle">
                                            <ul class="nav">
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>50</h4>
                                                        <p>Total Tasks</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>10</h4>
                                                        <p>Total Completed</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="project-bottom">
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Project Leader :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Jeffery Lalor" data-bs-original-title="Jeffery Lalor">
                                                            <img src="assets/img/avatar/avatar-1.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Members :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Loren Gatlin" data-bs-original-title="Loren Gatlin">
                                                            <img src="assets/img/avatar/avatar-26.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Lesley Grauer" data-bs-original-title="Lesley Grauer">
                                                            <img src="assets/img/avatar/avatar-18.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Richard Miles" data-bs-original-title="Richard Miles">
                                                            <img src="assets/img/avatar/avatar-6.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Jeffery Lalor" data-bs-original-title="Jeffery Lalor">
                                                            <img src="assets/img/avatar/avatar-13.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Tarah Shropshire" data-bs-original-title="Tarah Shropshire">
                                                            <img src="assets/img/avatar/avatar-8.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="more-team-members">+13</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="project-grid">
                                        <div class="project-top">
                                            <h6>
                                                <a href="project-view.php">Deadline : 15 Feb 2024</a>
                                            </h6>
                                            <h5>
                                                <a href="project-view.php">Video Calling App</a>
                                            </h5>
                                            <p>Design and developing a software application that enables users to make video calls over the internet.</p>
                                        </div>
                                        <div class="project-middle">
                                            <ul class="nav">
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>30</h4>
                                                        <p>Total Tasks</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="project-tasks">
                                                        <h4>12</h4>
                                                        <p>Total Completed</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="project-bottom">
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Project Leader :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Catherine Manseau" data-bs-original-title="Catherine Manseau">
                                                            <img src="assets/img/avatar/avatar-18.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="project-leader">
                                                <ul class="nav">
                                                    <li>Members :</li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Richard Miles" data-bs-original-title="Richard Miles">
                                                            <img src="assets/img/avatar/avatar-21.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Jeffery Lalor" data-bs-original-title="Jeffery Lalor">
                                                            <img src="assets/img/avatar/avatar-16.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Lesley Grauer" data-bs-original-title="Lesley Grauer">
                                                            <img src="assets/img/avatar/avatar-20.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Loren Gatlin" data-bs-original-title="Loren Gatlin">
                                                            <img src="assets/img/avatar/avatar-1.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="project-view.php" data-bs-toggle="tooltip" aria-label="Tarah Shropshire" data-bs-original-title="Tarah Shropshire">
                                                            <img src="assets/img/avatar/avatar-23.jpg" alt="User">
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="more-team-members">+10</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                

                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="row">

                    <!-- Employee Month -->
                    <?php
                    $sql = "SELECT * FROM hrm_employee_of_the_month ORDER BY created_at DESC LIMIT 1";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $employee = $result->fetch_assoc();
                    } else {
                        $employee = [
                            'name' => 'No Employee Found',
                            'designation' => 'N/A',
                            'message' => 'No message available.',
                            'image_url' => 'assets/upload-image/atul.jpg',
                        ];
                    }
                    ?>

<div class="col-xl-6 col-md-12 d-flex">
    <div class="card employee-month-card flex-fill" id="employeeOfTheMonthCard">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-9 col-md-12">
                    <div class="employee-month-details">
                        <h4>Employee of the month</h4>
                        <p>
                            <?php echo stripslashes($employee['message']); ?>
                        </p>
                    </div>
                    <div class="employee-month-content">
                        <h6>Congrats, <?php echo stripslashes($employee['name']); ?></h6>
                        <p><?php echo stripslashes($employee['designation']); ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12">
                    <div class="employee-month-img">
                        <img src="<?php echo stripslashes($employee['image_url']); ?>"
                             class="img-fluid month-of-employee"
                             alt="<?php echo stripslashes($employee['name']); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





                    <!-- /Employee Month -->

                    <!-- Company Policy -->
                    <?php

                    $query = "SELECT id, policy_name, policy_type, updated_on, file_path FROM company_policies";
                    $result = $conn->query($query);


                    $policies = [];

                    if ($result->num_rows > 0) {

                        while ($row = $result->fetch_assoc()) {
                            $policies[] = $row;
                        }
                    } else {

                        $policies[] = [
                            'policy_name' => 'No Policies Available',
                            'policy_type' => 'default',
                            'updated_on' => 'N/A',
                            'file_path' => '#'
                        ];
                    }
                    ?>

                    <div class="col-xl-6 col-md-12 d-flex">
                        <div class="card flex-fill shadow-sm border-0">
                            <div class="card-body">
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-8">
                                        <div class="statistic-header">
                                            <h4 class="text-primary fw-bold">Company Policy</h4>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 text-sm-end">
                                        <div class="owl-nav company-nav nav-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="company-slider owl-carousel">
                                    <?php foreach ($policies as $policy): ?>
                                        <div
                                            class="company-grid company-soft-<?php echo htmlspecialchars($policy['policy_type']); ?> p-3 rounded border shadow-sm">
                                            <div class="company-top d-flex align-items-center mb-3">
                                                <div class="company-icon me-3">
                                                    <span
                                                        class="company-icon-<?php echo htmlspecialchars($policy['policy_type']); ?> bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                        <?php echo strtoupper(substr($policy['policy_type'], 0, 2)); ?>
                                                    </span>
                                                </div>
                                                <div class="company-link">
                                                    <a href="companies.php?id=<?php echo $policy['id']; ?>"
                                                        class="text-decoration-none text-dark fw-bold">
                                                        <?php echo htmlspecialchars($policy['policy_type']); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="company-bottom">
                                                <ul class="list-unstyled mb-3">

                                                    <li><strong>Policy Name:</strong>
                                                        <?php echo htmlspecialchars($policy['policy_name']); ?></li>
                                                    <li><strong>Updated on:</strong>
                                                        <?php echo date('d M Y', strtotime($policy['updated_on'])); ?></li>
                                                </ul>
                                                <div class="company-bottom-links d-flex justify-content-between">
                                                    <a href="<?php echo htmlspecialchars($policy['file_path']); ?>" download
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="la la-download"></i> Download
                                                    </a>
                                                    <a href="<?php echo htmlspecialchars($policy['file_path']); ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="la la-eye"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- /Company Policy -->

                </div>
            </div>
            <!-- /Page Content -->

        </div>
        <!-- /Page Wrapper -->

    </div>
    <!-- end main wrapper-->

    <?php include 'layouts/customizer.php'; ?>

    <?php include 'layouts/vendor-scripts.php'; ?>

    <script src="assets/js/ipAddress.js"></script>
   <script>
         document.addEventListener("DOMContentLoaded", function () {
    const calendar = document.getElementById("calendarcustom");
    const monthYear = document.getElementById("monthYearcustom");
    const prevMonthBtn = document.getElementById("prevMonthcustom");
    const nextMonthBtn = document.getElementById("nextMonthcustom");

    let currentDate = new Date();
    let holidays = <?php echo json_encode($holidays); ?>;

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];

        monthYear.innerText = `${monthNames[month]} ${year}`;
        calendar.innerHTML = ""; 

        const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        daysOfWeek.forEach(day => {
            const div = document.createElement("div");
            div.classList.add("day-namecustom");
            div.innerText = day;
            calendar.appendChild(div);
        });

        const firstDay = new Date(year, month, 1).getDay();
        const totalDays = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            const emptyDiv = document.createElement("div");
            calendar.appendChild(emptyDiv);
        }

        for (let day = 1; day <= totalDays; day++) {
            const dayDiv = document.createElement("div");
            dayDiv.classList.add("daycustom");
            dayDiv.innerText = day;

            if (
                day === new Date().getDate() &&
                month === new Date().getMonth() &&
                year === new Date().getFullYear()
            ) {
                dayDiv.classList.add("current-daycustom");
            }

            const formattedDate = `${year}-${(month + 1).toString().padStart(2, "0")}-${day.toString().padStart(2, "0")}`;
            if (holidays[formattedDate]) {
                dayDiv.classList.add("holidaycustom");
                dayDiv.setAttribute("data-holiday", holidays[formattedDate].name + ` (${holidays[formattedDate].days} days)`);
            }

            calendar.appendChild(dayDiv);
        }
    }

    // ✅ Correctly update the `currentDate` without modifying the original object
    prevMonthBtn.addEventListener("click", () => {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener("click", () => {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
        renderCalendar();
    });

    renderCalendar();
});

// punch-in/punch-out buffering

document.addEventListener('DOMContentLoaded', function () {
    const clockInBtn = document.getElementById('clock_in_btn');
    const btnText = clockInBtn.querySelector('.btn-text');
    const spinner = document.getElementById('loadingSpinner');

    clockInBtn.addEventListener('click', function () {
        // Disable button
        clockInBtn.disabled = true;

        // Show loading spinner and hide text
        spinner.classList.remove('d-none');
        btnText.classList.add('d-none');

        // Simulate backend update (replace with real AJAX)
        setTimeout(function () {
            // Simulate status switch
            const currentStatus = clockInBtn.getAttribute('data-status');
            const newStatus = currentStatus === 'login' ? 'logout' : 'login';
            clockInBtn.setAttribute('data-status', newStatus);

            // Update button text
            btnText.innerHTML = `<img src="assets/img/icons/clock-in.svg" alt="Icon" style="height: 24px;"> ${newStatus === 'login' ? 'Clock-Out' : 'Clock-In'}`;

            // Restore button
            spinner.classList.add('d-none');
            btnText.classList.remove('d-none');
            clockInBtn.disabled = false;

            // Optionally reload the page or update time info
            location.reload();
        }, 2000);
    });
});


    
</script>

<script>
    // Select all cards that should have the 3D tilt effect
    // const cards = document.querySelectorAll('.card');

    // cards.forEach(card => {
    //     card.addEventListener("mousemove", function(event) {
    //         const cardRect = card.getBoundingClientRect();
    //         const cardCenterX = cardRect.left + cardRect.width / 2;
    //         const cardCenterY = cardRect.top + cardRect.height / 2;
    //         const mouseX = event.clientX;
    //         const mouseY = event.clientY;

    //         const deltaX = mouseX - cardCenterX;
    //         const deltaY = mouseY - cardCenterY;

    //         const angleX = (deltaY / cardRect.height) * 20; // Controls tilt in X direction
    //         const angleY = (deltaX / cardRect.width) * -20; // Controls tilt in Y direction

    //         // Apply the tilt effect to the card
    //         card.style.transform = `rotateX(${angleX}deg) rotateY(${angleY}deg)`;

    //         // Calculate the card movement away from the cursor
    //         const cardOffsetX = (mouseX - cardCenterX) * 0.05; // Adjust distance here
    //         const cardOffsetY = (mouseY - cardCenterY) * 0.05;

    //         // Move the card away from the cursor
    //         card.style.transform = `rotateX(${angleX}deg) rotateY(${angleY}deg) translateX(${cardOffsetX}px) translateY(${cardOffsetY}px)`;
    //     });

    //     card.addEventListener("mouseleave", function() {
    //         // Reset the tilt effect when the mouse leaves
    //         card.style.transform = `rotateX(0deg) rotateY(0deg) translateX(0px) translateY(0px)`;
    //     });
    // });
</script>

</body>

</html>