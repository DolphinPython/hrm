<?php

include 'layouts/session.php'; 
// include 'layouts/head-main.php';
include 'include/function.php';

// get user name and other detail
$emp_id = $_SESSION['id'];
$conn = connect();
//$id=$_GET['id'];

?>


<head>
    <title>Employee Dashboard - HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
    .exp1,
    .exp2 {
        display: none;
    }

    @media (max-width: 1000px) {
        .exp1 {
            display: block;
        }
    }

    @media (min-width: 1001px) {
        .exp2 {
            display: block;
        }
    }


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

    .info-card1 {
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



                                    <!--    Desktop Section Only Start    -->
                                    <div class="exp2">

                                        <a href="javascript:void(0);"
                                            class="btn btn-lg btn-outline-warning px-4 py-2 d-flex justify-content-center align-items-center gap-2 rounded-3 shadow-sm"
                                            id="clock_in_btn" data-status="<?= $status; ?>">

                                            <span class="btn-text d-flex align-items-center gap-2 ">
                                                <img src="assets/img/icons/clock-in.svg" alt="Icon"
                                                    style="height: 24px;">
                                                <?= ($status === 'login') ? 'Clock-Out' : 'Clock-In'; ?>
                                            </span>

                                            <span class="spinner-border spinner-border-sm text-light d-none"
                                                role="status" id="loadingSpinner"></span>
                                        </a>

                                    </div>
                                    <!--    Desktop Section Only End    -->
                                    <!--    Mobile Section Only Start    -->
                                    <div class="exp1">
                                    </div>
                                    <!--    Mobile Section Only End    -->


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

                                <div class="col-md-3">
                                
                                </div>

                            </div>
                        </div>
                    </div>
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
        document.addEventListener("DOMContentLoaded", function() {
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
                    "July", "August", "September", "October", "November", "December"
                ];

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

                    const formattedDate =
                        `${year}-${(month + 1).toString().padStart(2, "0")}-${day.toString().padStart(2, "0")}`;
                    if (holidays[formattedDate]) {
                        dayDiv.classList.add("holidaycustom");
                        dayDiv.setAttribute("data-holiday", holidays[formattedDate].name +
                            ` (${holidays[formattedDate].days} days)`);
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

        document.addEventListener('DOMContentLoaded', function() {
            const clockInBtn = document.getElementById('clock_in_btn');
            const btnText = clockInBtn.querySelector('.btn-text');
            const spinner = document.getElementById('loadingSpinner');

            clockInBtn.addEventListener('click', function() {
                // Disable button
                clockInBtn.disabled = true;

                // Show loading spinner and hide text
                spinner.classList.remove('d-none');
                btnText.classList.add('d-none');

                // Simulate backend update (replace with real AJAX)
                setTimeout(function() {
                    // Simulate status switch
                    const currentStatus = clockInBtn.getAttribute('data-status');
                    const newStatus = currentStatus === 'login' ? 'logout' : 'login';
                    clockInBtn.setAttribute('data-status', newStatus);

                    // Update button text
                    btnText.innerHTML =
                        `<img src="assets/img/icons/clock-in.svg" alt="Icon" style="height: 24px;"> ${newStatus === 'login' ? 'Clock-Out' : 'Clock-In'}`;

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


</body>

</html>