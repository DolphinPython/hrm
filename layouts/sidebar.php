<?php
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);

?>
<style>
    

@media (max-width: 989px) {
    .sidebar-vertical li a {
        color: #ffffff !important;
    }
    
    .slide-nav .sidebar {
        background: #183f5b !important;
    }
}
@media (min-width: 990px) {
    .slimScrollDiv {
        margin: auto  !important; 
        border-radius: 0px !important; 
    }
    
    .sidebar-vertical li a {
        color: #ffffff !important;
    }
}
 .sidebar {
    box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.2);
    background-color: #052c65 !important;
}
.modal-body h5{
    padding:5px 10px;
    color:white !important;
}

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
        margin-left: 10px !important;
    }

</style>
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
                    <!-- <span>Main</span>-->
                    <?php //echo $_SESSION['id']; 
                    ?>
                </li>
              
                <li>
                <?php if ($row_session['role'] == 'admin' or $row_session['role'] == 'super admin') { ?>
                    <li><a class="<?php echo ($page == 'admin-dashboard.php') ? 'active' : ''; ?>"
                    href="admin-dashboard.php"><i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    
                </li>

                        <?php } else { ?>
                            <li><a class="<?php echo ($page == 'employee-dashboard.php') ? 'active' : ''; ?>"
                                    href="employee-dashboard.php"><i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                        <?php } ?>

                </li>
                <li>

                        <?php if ($row_session['role'] == 'admin' or $row_session['role'] == 'super admin') { ?>
                            <li><a class="<?php echo ($page == 'employees.php' || $page == 'employees-list.php') ? 'active' : ''; ?>"
                                    href="employees.php"><i class="fas fa-user"></i>
                                    <span>All Employees</span></a></li>

                            <li><a class="<?php echo ($page == 'holidays.php') ? 'active' : ''; ?>"
                                    href="holidays.php"><i class="fas fa-snowflake"></i>
                                    <span>Holidays</span></a></li>
                       
<?php
// Ensure database connection is available
$conn = connect(); // Assumes connect() is defined in include/function.php

// Query to get the count of New and Pending leaves
$fetch_new_pending_leaves_query = "SELECT COUNT(*) AS total_new_pending_leaves FROM hrm_leave_applied WHERE status IN (0, 1)";
$fetch_new_pending_leaves_result = mysqli_query($conn, $fetch_new_pending_leaves_query);

$total_new_pending_leaves = 0;
if ($fetch_new_pending_leaves_result) {
    $leave_count_row = mysqli_fetch_assoc($fetch_new_pending_leaves_result);
    $total_new_pending_leaves = $leave_count_row['total_new_pending_leaves'];
}
?>
<!-- Menu item for Leaves(Admin) -->
<li>
    <a class="<?php echo ($page == 'leaves.php') ? 'active' : ''; ?>" href="leaves.php">
        <i class="fas fa-clock"></i><span>Leaves(Admin)</span>
        <?php if ($total_new_pending_leaves > 0): ?>
            <span class="blinking-count"><?php echo $total_new_pending_leaves; ?></span>
        <?php endif; ?>
    </a>
</li>

                          

                            
                                    <li class="submenu">
                    <a href="#" class="noti-dot"><i class="fas fa-calendar-check"></i>
                    <span>Attendance</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">

                    <li><a class="<?php echo ($page == 'attendance-reports-admin.php') ? 'active' : ''; ?>"
                    href="attendance-reports-admin.php"><i class="fas fa-calendar-check"></i>
                    <span>Attendance(MN)</span></a></li>
                    <li><a class="<?php echo ($page == 'attendance-reports-admin.php') ? 'active' : ''; ?>"
                                    href="upload-attendance-admin.php"><i class="fas fa-calendar-check"></i>
                                    <span>Upload Attendance</span></a></li>
                                    <li><a class="<?php echo ($page == 'attandance-all-employee.php') ? 'active' : ''; ?>"
                                    href="attandance-all-employee.php"><i class="fas fa-calendar-check"></i><span>Attendance All(HRM)</span></a></li>

                    </ul>
                </li>
                <li class="submenu">
                    <a href="#" class="noti-dot"><i class="fas fa-building"></i>
                    <span>Company</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">

                    <li><a class="<?php echo ($page == 'departments.php') ? 'active' : ''; ?>"
                                    href="departments.php"><i class="fas fa-layer-group"></i>
                                    <span>Departments</span></a></li>
                            <li><a class="<?php echo ($page == 'designations.php') ? 'active' : ''; ?>"
                                    href="designations.php"><i class="fas fa-user-tie"></i>
                                   <span>Designations</span> </a></li>
                    <li><a class="<?php echo ($page == 'company_policies.php') ? 'active' : ''; ?>"
                                    href="company_policies.php"><i class="fas fa-clipboard-list"></i>
                                    <span>Company Policies</span></a></li>
                            <li><a class="<?php echo ($page == 'company_data.php') ? 'active' : ''; ?>"
                                    href="company_data.php"><i class="fas fa-file-contract"></i>
                                    <span>Company Document</span></a></li>
                                    <li><a class="<?php echo ($page == 'companies_management.php') ? 'active' : ''; ?>"
                                    href="companies_management.php"><i class="fas fa-book"></i>

                                    <span>Company Details</span></a></li>
                    </ul>
                </li>
                           
                <li class="submenu">
                    <a href="#" class="noti-dot"><i class="fas fa-bullhorn"></i>

                   <span>Announcement</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">

                    <li><a class="<?php echo ($page == 'hrm_employee_of_the_month.php') ? 'active' : ''; ?>"
                    href="hrm_employee_of_the_month.php">
                    <span>Employee of the Month</span></a></li>
                    <li><a class="<?php echo ($page == 'activities.php') ? 'active' : ''; ?>"
                                    href="activities.php">
                                    <span>Add Announcement</span></a></li>

                    </ul>
                </li>
                <!--<li class="submenu">-->
                <!--    <a href="#" class="noti-dot"><i class="fas fa-plus-circle"></i>-->


                <!--   <span>ADD</span> <span-->
                <!--            class="menu-arrow"></span></a>-->
                <!--    <ul style="display: none;">-->

                <!--    <li><a class="<?php echo ($page == 'hrm_asset_assignments.php') ? 'active' : ''; ?>"-->
                <!--    href="hrm_asset_assignments.php"><i class="fas fa-box"></i>-->
                <!--    <span>Asset Management System</span></a></li>-->
                <!--    <li><a class="<?php echo ($page == 'office-timing.php') ? 'active' : ''; ?>"-->
                <!--    href="office-timing.php"><i class="fas fa-business-time"></i>-->
                <!--    <span>Office Timing</span></a></li>-->
                <!--    <li><a class="<?php echo ($page == 'category.php') ? 'active' : ''; ?>"-->
                <!--    href="category.php"><i class="fas fa-tags"></i>-->
                <!--   <span>Ticket Category</span></a></li>-->
                <!--    </ul>-->
                <!--</li>-->
                                   
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
                                    <i class="fas fa-ticket-alt"></i><span>Ticket</span>

                                    <?php if ($total_open_in_progress_tickets > 0): ?>
                                        <span class="blinking-count"><?php echo $total_open_in_progress_tickets; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
   <?php
// include 'session.php'; 
// include 'config.php'; 
// include '../include/function.php'; 
// $conn = connect();
// Query to get the count of Pending resignations
$fetch_pending_resignations_query = "SELECT COUNT(*) AS total_pending_resignations FROM employee_resignations WHERE status = 'Pending'";
$fetch_pending_resignations_result = mysqli_query($conn, $fetch_pending_resignations_query);

$total_pending_resignations = 0;
if ($fetch_pending_resignations_result) {
    $resignation_count_row = mysqli_fetch_assoc($fetch_pending_resignations_result);
    $total_pending_resignations = $resignation_count_row['total_pending_resignations'];
}
?>
<!-- Menu item for Notice Period Management -->
<li>
    <a class="<?php echo ($page == 'noticeperiod.php') ? 'active' : ''; ?>" href="noticeperiod.php">
        <i class="fas fa-sign-out-alt"></i><span>Notice Period</span>
        <?php if ($total_pending_resignations > 0): ?>
            <span class="blinking-count"><?php echo $total_pending_resignations; ?></span>
        <?php endif; ?>
    </a>
</li>                         
                            <li class="submenu">
                    <a href="#" class="noti-dot"><i class="fa-solid fa-file-signature"></i> <span>POSH</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">


                     <li><a class="<?php echo ($page == 'guidelineposh.php') ? '' : ''; ?>" href="guidelineposh.php">
                    Guidelines</a></li>
                    <li><a class="<?php echo ($page == 'Internal-Complaints-Committee.php') ? '' : ''; ?>" href="Internal-Complaints-Committee.php">
                    Committee</a></li>
                    <li><a class="<?php echo ($page == 'harresment.php') ? '' : ''; ?>" href="harresment.php">
                    Complain</a></li>
                    <?php if ($row_session['role'] == 'admin' or $row_session['role'] == 'super admin') { ?>
                            <li><a class="<?php echo ($page == 'all_employee_complaints.php') ? 'active' : ''; ?>"
                                    href="all_employee_complaints.php"><i class="fa-solid fa-file-signature"></i><span>All Complaints</span></a></li>

                        <?php } ?>
                    
                    </ul>
                </li>
                            
                        <?php } else { ?>
                            <li><a class="<?php echo ($page == 'leaves-employee.php') ? 'active' : ''; ?>"
                                    href="leaves-employee.php"><i class="fa-solid fa-user-clock"> </i><span>My Leaves</span></a></li>


                            <li><a class="<?php echo ($page == 'attendance-report-employee.php') ? 'active' : ''; ?>"
                                    href="attendance-report-employee.php"> <i class="fa-solid fa-clipboard-list"></i><span>Attendance</span></a></li>
                            <li><a class="<?php echo ($page == 'attendance-report-employee-hrm.php') ? 'active' : ''; ?>"
                                    href="attendance-report-employee-hrm.php"><i class="fa-solid fa-clipboard-list"></i><span>Daily Attendance</span></li>
                           
                                   <?php
// Using existing connection from $conn
// Assuming $conn is already available from earlier connect() call

// Get employee ID from session
$emp_id = $_SESSION['id'];

// Check if the logged-in employee is a reporting manager
$sql = "SELECT 1 FROM hrm_reporting_manager WHERE reporting_manager_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $is_reporting_manager = $result->num_rows > 0;
    $stmt->close();
} else {
    // Handle prepare statement failure
    $is_reporting_manager = false;
    error_log("Prepare statement failed: " . $conn->error);
}

// No need to close $conn here since it's being used elsewhere in your code
?>

<?php if ($is_reporting_manager): ?>
    <li>
        <a class="<?php echo ($page == 'attandance-all-employee.php') ? 'active' : ''; ?>"
           href="attandance-all-employee.php">
            <i class="fas fa-calendar-check"></i>
            <span>Employee Attendance</span>
        </a>
    </li>
     <li>
        <a class="<?php echo ($page == 'leaves.php') ? 'active' : ''; ?>"
           href="leaves.php">
            <i class="fas fa-clock"></i>
            <span>Employee Leaves</span>
        </a>
    </li>
<?php endif; ?>
                           
                            <li><a class="<?php echo ($page == 'companydetails.php') ? 'active' : ''; ?>"
                                    href="companydetails.php"><i class="fa-solid fa-briefcase"></i><span>Company Details</span></a></li>
                                    <li><a class="<?php echo ($page == 'add-expense.php') ? 'active' : ''; ?>"
                                    href="add-expense.php"><i class="fa-solid fa-briefcase"></i><span>Expenses</span></a></li>

                            <li><a class="<?php echo ($page == 'ticket.php') ? 'active' : ''; ?>" href="ticket.php"> <i class="fa-solid fa-ticket"></i><span>Ticket</span>
                                </a></li>
                            
                                   <li class="submenu">
                    <a href="#" class="noti-dot"><i class="fa-solid fa-file-signature"></i> <span>POSH</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                    <li><a class="<?php echo ($page == 'guidelineposh.php') ? '' : ''; ?>" href="guidelineposh.php">
                    Guidelines</a></li>
                    <li><a class="<?php echo ($page == 'Internal-Complaints-Committee.php') ? '' : ''; ?>" href="Internal-Complaints-Committee.php">
                    Committee</a></li>
                    <li><a class="<?php echo ($page == 'harresment.php') ? '' : ''; ?>" href="harresment.php">
                    Complain</a></li>
                            <li><a class="<?php echo ($page == 'employee_complaints.php') ? 'active' : ''; ?>"
                                    href="employee_complaints.php">Your Complaints</a></li>

                    </ul>
                </li>

                        <?php } ?>
                </li>
                
               
                <li class="submenu">
                    <a href="#" class="noti-dot"><i class="fa-solid fa-gear"></i> <span>Settings</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                    <?php 
if ($row_session['role'] == 'admin' || $row_session['role'] == 'super admin') { ?>
    <li><a class="<?= ($page == 'hrm_asset_assignments.php') ? 'active' : ''; ?>"
        href="hrm_asset_assignments.php">
        Asset Management System</a></li>
    <li><a class="<?= ($page == 'office-timing.php') ? 'active' : ''; ?>"
        href="office-timing.php">
        Office Timing</a></li>
    <li><a class="<?= ($page == 'category.php') ? 'active' : ''; ?>"
        href="category.php">
        Ticket Category</a></li>
         <li><a class="<?php echo ($page == 'archived_employees.php') ? 'active' : ''; ?>"
                                    href="archived_employees.php">Former Employee</a></li>
                                    <li><a class="<?php echo ($page == 'passwordgenerate.php') ? 'active' : ''; ?>"
                                    href="passwordgenerate.php">Password Management
</a></li>
<li><a class="<?php echo ($page == 'salary-management.php') ? 'active' : ''; ?>"
                                    href="salary-management.php">Salary Management</a></li>

                            <li><a class="<?php echo ($page == 'attendance-reports-admin.php') ? 'active' : ''; ?>"
                                    href="salary-report-admin.php">Salary Admin</a></li>
                                      <li><a class="<?php echo ($page == 'onboarding.php') ? 'active' : ''; ?>"
                                    href="onboarding.php">Onboarding Management</a></li>
                                    <li><a class="<?php echo ($page == 'manage_expenses.php') ? 'active' : ''; ?>"
                                    href="manage_expenses.php">Expenses Management</a></li>
                                    
                                    <li><a class="<?php echo ($page == 'admin-logs.php') ? 'active' : ''; ?>"
                                    href="admin-logs.php">Admin Logs</a></li>
                                   
<?php } ?>
                    <li>
                    <a href="apply_resignation.php">Resignation Form</a>
                    </li>
                   
                    <li>
                    <a href="change-password.php">Change Password</a>
                    </li>

                    </ul>
                </li>
                <?php 
if ($row_session['role'] == 'super admin') { ?>
                 <li class="submenu">
                    <a href="#" class=""><i class="fa-solid fa-users"></i>  <span>Users</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                    
    <li><a class="<?= ($page == 'assignrole.php') ? 'active' : ''; ?>"
        href="assignrole.php">
        Users role</a></li>                                  

                                   </ul>
                </li>
                <?php } ?>
            </ul>

        </div>
    </div>
</div>

<!-- /Sidebar -->