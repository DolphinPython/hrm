<?php
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);

?>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">




            <?php //include 'include/function.php';
            

            $emp_id_session = $_SESSION['id'];
            $conn = connect();
            //$id=$_GET['id'];
            $query_session = "select * from hrm_employee where id='$emp_id_session';";
            $result_session = mysqli_query($conn, $query_session) or die(mysqli_error($conn));
            $x = "";
            $row_session = mysqli_fetch_array($result_session);

            ?>
            <ul class="sidebar-vertical">
                <li class="menu-title">
                    <!--<span>Main</span>-->
                    <?php //echo $_SESSION['id']; 
                    ?>
                </li>
                <li class="submenu">
                    <a href="#"><i class="la la-dashboard"></i> <span> Dashboard</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">


                        <?php if ($row_session['department_id'] == 4 or $row_session['department_id'] == 6) { ?>
                            <li><a class="<?php echo ($page == 'admin-dashboard.php') ? 'active' : ''; ?>"
                                    href="admin-dashboard.php">Admin Dashboard</a></li>
                                    <li><a class="<?php echo ($page == 'employee-dashboard.php') ? 'active' : ''; ?>"
                                    href="employee-dashboard.php">Personal Dashboard</a></li>

                        <?php } else { ?>
                            <li><a class="<?php echo ($page == 'employee-dashboard.php') ? 'active' : ''; ?>"
                                    href="employee-dashboard.php">Employee Dashboard</a></li>
                        <?php } ?>

                    </ul>
                </li>

                <li class="menu-title">
                    <span>Employees</span>
                </li>
                <li class="submenu">
                    <a href="#" class="noti-dot"><i class="la la-user"></i> <span> Employees</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">

                        <?php if ($row_session['department_id'] == 4 or $row_session['department_id'] == 6) { ?>
                            <li><a class="<?php echo ($page == 'employees.php' || $page == 'employees-list.php') ? 'active' : ''; ?>"
                                    href="employees.php">All Employees</a></li>

                            <li><a class="<?php echo ($page == 'holidays.php') ? 'active' : ''; ?>"
                                    href="holidays.php">Holidays</a></li>
                            <li><a class="<?php echo ($page == 'leaves.php') ? 'active' : ''; ?>" href="leaves.php">Leaves
                                    (Admin)
                                    <span class="badge rounded-pill bg-primary float-end">1</span></a></li>
                            <li><a class="<?php echo ($page == 'attendance-reports-admin.php') ? 'active' : ''; ?>"
                                    href="attendance-reports-admin.php">Attendance (Admin)</a></li>

                            <li><a class="<?php echo ($page == 'attendance-reports-admin.php') ? 'active' : ''; ?>"
                                    href="upload-attendance-admin.php">Upload Attendance (Admin)</a></li>

                            <li><a class="<?php echo ($page == 'departments.php') ? 'active' : ''; ?>"
                                    href="departments.php">Departments</a></li>
                            <li><a class="<?php echo ($page == 'designations.php') ? 'active' : ''; ?>"
                                    href="designations.php">Designations</a></li>
                            <li><a class="<?php echo ($page == 'attendance-reports-admin.php') ? 'active' : ''; ?>"
                                    href="salary-report-admin.php">Salary Admin</a></li>
                            <li><a class="<?php echo ($page == 'hrm_employee_of_the_month.php') ? 'active' : ''; ?>"
                                    href="hrm_employee_of_the_month.php">Employee of the Month</a></li>
                            <li><a class="<?php echo ($page == 'company_policies.php') ? 'active' : ''; ?>"
                                    href="company_policies.php">Company Policies</a></li>
                            <li><a class="<?php echo ($page == 'company_data.php') ? 'active' : ''; ?>"
                                    href="company_data.php">Company Document</a></li>
                            <li><a class="<?php echo ($page == 'activities.php') ? 'active' : ''; ?>"
                                    href="activities.php">Add Announcement</a></li>
                            <li><a class="<?php echo ($page == 'hrm_asset_assignments.php') ? 'active' : ''; ?>"
                                    href="hrm_asset_assignments.php">Asset Management System</a></li>
                            <li><a class="<?php echo ($page == 'companies_management.php') ? 'active' : ''; ?>"
                                    href="companies_management.php">Company Details</a></li>

                            <li><a class="<?php echo ($page == 'office-timing.php') ? 'active' : ''; ?>"
                                    href="office-timing.php">Office Timing</a></li>
                            <li><a class="<?php echo ($page == 'attandance-all-employee.php') ? 'active' : ''; ?>"
                                    href="attandance-all-employee.php">Attandance All Employee</a></li>
                            <li><a class="<?php echo ($page == 'archived_employees.php') ? 'active' : ''; ?>"
                        href="archived_employees.php">Past Employees Details</a></li>
                            <style>
                                /* Blinking animation */
                                @keyframes blink {
                                    0% {
                                        opacity: 1;
                                    }

                                    50% {
                                        opacity: 0;
                                    }

                                    100% {
                                        opacity: 1;
                                    }
                                }

                                .blinking-count {
                                    animation: blink 1s infinite;
                                    background-color: orange;
                                    color: white;
                                    padding: 2px 8px;
                                    border-radius: 50%;
                                    font-size: 12px;
                                    margin-left: 115px !important;
                                }
                            </style>
                            <li><a class="<?php echo ($page == 'category.php') ? 'active' : ''; ?>"
                                    href="category.php">Ticket Category</a></li>

                            <?php
                            // Query to get the combined count of Open and In Progress tickets
                            $fetch_open_in_progress_query = "SELECT COUNT(*) AS total_open_in_progress_tickets FROM tickets WHERE Status IN ('Open', 'In Progress')";
                            $fetch_open_in_progress_result = mysqli_query($conn, $fetch_open_in_progress_query);

                            if ($fetch_open_in_progress_result) {
                                $ticket_count_row = mysqli_fetch_assoc($fetch_open_in_progress_result);
                                $total_open_in_progress_tickets = $ticket_count_row['total_open_in_progress_tickets'];
                                // echo "Total Open and In Progress Tickets: " . $total_open_in_progress_tickets;
                            }
                            ?>
                            <li>
                                <a class="<?php echo ($page == 'view-ticket.php') ? 'active' : ''; ?>"
                                    href="view-ticket.php">
                                    Ticket
                                    <?php if ($total_open_in_progress_tickets > 0): ?>
                                        <span class="blinking-count"><?php echo $total_open_in_progress_tickets; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li><a class="<?php echo ($page == 'harresment.php') ? 'active' : ''; ?>" href="harresment.php">
                                    Add Complaints</a></li>
                        <?php } else { ?>
                            <li><a class="<?php echo ($page == 'leaves-employee.php') ? 'active' : ''; ?>"
                                    href="leaves-employee.php">Leaves (Employee)</a></li>


                            <li><a class="<?php echo ($page == 'attendance-report-employee.php') ? 'active' : ''; ?>"
                                    href="attendance-report-employee.php">Attendance (Employee)</a></li>
                            <li><a class="<?php echo ($page == 'attendance-report-employee-hrm.php') ? 'active' : ''; ?>"
                                    href="attendance-report-employee-hrm.php">Daily Attendance (Employee)</a></li>
                            <li><a class="<?php echo ($page == 'companydetails.php') ? 'active' : ''; ?>"
                                    href="companydetails.php">Company Details</a></li>

                            <li><a class="<?php echo ($page == 'ticket.php') ? 'active' : ''; ?>" href="ticket.php">Ticket
                                </a></li>
                            <li><a class="<?php echo ($page == 'harresment.php') ? 'active' : ''; ?>" href="harresment.php">
                                    Add Complaints</a></li>
                            <li><a class="<?php echo ($page == 'employee_complaints.php') ? 'active' : ''; ?>"
                                    href="employee_complaints.php">Your Complaints</a></li>
                           

                        <?php } ?>

                        <?php if ($emp_id_session == '1' || $emp_id_session == '10' || $emp_id_session == '8') { ?>
                            <li><a class="<?php echo ($page == 'all_employee_complaints.php') ? 'active' : ''; ?>"
                                    href="all_employee_complaints.php">All Complaints</a></li>

                        <?php } ?>

                        

                    </ul>
                </li>

                <li>
                    <a href="change-password.php"><i class="la la-lock"></i><span>Change Password</span></a>
                </li>

            </ul>

        </div>
    </div>
</div>

<!-- /Sidebar -->