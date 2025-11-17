<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');
date_default_timezone_set('Asia/Kolkata');
include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn = connect();
    $delete_query = "DELETE FROM hrm_notification WHERE id = '$delete_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Announcement deleted successfully!'); window.location.href='activities.php';</script>";
    } else {
        echo "<script>alert('Error deleting announcement: " . mysqli_error($conn) . "'); window.location.href='activities.php';</script>";
    }
    
    exit; // Stop further execution after handling the delete request
}

// Handle update request
if (isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $conn = connect();
    $update_query = "UPDATE hrm_notification SET title='$title', description='$description' WHERE id='$edit_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "Announcement updated successfully!";
    } else {
        echo "Error updating announcement: " . mysqli_error($conn);
    }
    exit; // Stop further execution after handling the update request
}

// Get user name and other details
$emp_id = $_SESSION['id'];
$emp_session_id = $_SESSION['id'];
$conn = connect();

$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row_employee = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = "";
$department = "";
$profile_image = "";
$active_employee = 0;
$inactive_employee = 0;

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row_employee['image'];

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Announcement</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script language="javascript">
        function save_notification(title1, description) {
            var emp_id = <?php echo $emp_id; ?>;
            var checkboxes = document.getElementsByName('emp_send_to[]');
            var vals = "";
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                if (checkboxes[i].checked) {
                    vals += "," + checkboxes[i].value;
                }
            }
            if (vals) vals = vals.substring(1);

            $.ajax({
                type: "GET",
                url: "save_notification.php",
                data: "title=" + title1 + "&description=" + description + "&employees=" + vals + "&emp_id=" + emp_id,
                success: function(data) {
                    alert(data);
                    $('#add_activity').modal('hide');
                    location.reload();
                }
            });
        }

        function delete_notification(id) {
            if (confirm("Are you sure you want to delete this announcement?")) {
                window.location.href = "?delete_id=" + id;
            }
        }

        function edit_notification(id, title, description) {
            $('#edit_title').val(title);
            $('#edit_description').val(description);
            $('#edit_id').val(id);
            $('#edit_activity').modal('show');
        }

        function update_notification() {
    var id = $('#edit_id').val();
    var title = $('#edit_title').val();
    var description = $('#edit_description').val();

    $.ajax({
        type: "POST",
        url: "update_notification.php", // Specify the correct URL
        data: { edit_id: id, title: title, description: description },
        success: function(data) {
            alert(data);
            $('#edit_activity').modal('hide');
            location.reload();
        },
        error: function(xhr, status, error) {
            alert("Error updating announcement: " + error);
        }
    });
}
    </script>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            <div class="content container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h3 class="page-title">Announcement</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Announcement</li>
                            </ul>
                        </div>
                        <?php if ($row_employee['role'] == 'admin' || $row_employee['role'] == 'super admin') { ?>
                            <div class="col-md-8 float-end ms-auto">
                                <div class="d-flex title-head">
                                    <div class="view-icons">
                                        <a href="javascript:void(0);" class="grid-view btn btn-link"><i class="las la-redo-alt"></i></a>
                                        <a href="javascript:void(0);" class="list-view btn btn-link" id="collapse-header"><i class="las la-expand-arrows-alt"></i></a>
                                        <!--<a href="javascript:void(0);" class="list-view btn btn-link" id="filter_search"><i class="las la-filter"></i></a>-->
                                    </div>
                                    <div class="form-sort">
                                        <a href="javascript:void(0);" class="list-view btn btn-link" data-bs-toggle="modal" data-bs-target="#export"><i class="las la-file-export"></i>Export</a>
                                    </div>
                                    <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_activity"><i class="la la-plus-circle"></i>Add Announcement</a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Search Filter -->
                <!--<div class="filter-filelds" id="filter_inputs">-->
                <!--    <div class="row filter-row">-->
                <!--        <div class="col-xl-2">-->
                <!--            <div class="input-block mb-3 form-focus">-->
                <!--                <input type="text" class="form-control floating">-->
                <!--                <label class="focus-label">Announcement</label>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--        <div class="col-xl-2">-->
                <!--            <div class="input-block mb-3 form-focus">-->
                <!--                <input type="email" class="form-control floating">-->
                <!--                <label class="focus-label">Owner</label>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--        <div class="col-xl-2">-->
                <!--            <div class="input-block mb-3 form-focus">-->
                <!--                <div class="cal-icon">-->
                <!--                    <input class="form-control floating datetimepicker" type="text">-->
                <!--                </div>-->
                <!--                <label class="focus-label">Due Date</label>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--        <div class="col-xl-2">-->
                <!--            <div class="input-block mb-3 form-focus">-->
                <!--                <div class="cal-icon">-->
                <!--                    <input class="form-control floating datetimepicker" type="text">-->
                <!--                </div>-->
                <!--                <label class="focus-label">Created at</label>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--        <div class="col-xl-2">-->
                <!--            <div class="input-block mb-3 form-focus select-focus">-->
                <!--                <select class="select floating">-->
                <!--                    <option>--Select--</option>-->
                <!--                    <option>Meeting</option>-->
                <!--                    <option>Calls</option>-->
                <!--                    <option>Email</option>-->
                <!--                    <option>Task</option>-->
                <!--                </select>-->
                <!--                <label class="focus-label">Announcement Type</label>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--        <div class="col-xl-2">-->
                <!--            <a href="#" class="btn btn-success w-100"> Search </a>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                <!--<hr>-->
                <!-- /Search Filter -->

                <!--<div class="filter-section activity-filter-head">-->
                <!--    <div class="all-activity-head">-->
                <!--        <h5>All Announcement</h5>-->
                <!--    </div>-->
                <!--    <ul>-->
                <!--        <li>-->
                <!--            <div class="form-sort">-->
                <!--                <i class="las la-sort-alpha-up-alt"></i>-->
                <!--                <select class="select">-->
                <!--                    <option>Sort By Alphabet</option>-->
                <!--                    <option>Ascending</option>-->
                <!--                    <option>Descending</option>-->
                <!--                    <option>Recently Viewed</option>-->
                <!--                    <option>Recently Added</option>-->
                <!--                </select>-->
                <!--            </div>-->
                <!--        </li>-->
                <!--        <li>-->
                <!--            <div class="search-set">-->
                <!--                <div class="search-input">-->
                <!--                    <a href="#" class="btn btn-searchset"><i class="las la-search"></i></a>-->
                <!--                    <div class="dataTables_filter">-->
                <!--                        <label> <input type="search" class="form-control form-control-sm" placeholder="Search"></label>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!--            </div>-->
                <!--        </li>-->
                <!--    </ul>-->
                <!--</div>-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table datatable">
                                <thead>
                                    <tr>
                                        <th>Sl. No.</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Date / Time</th>
                                        <th style="display:none;">Sent By</th>
                                        <th style="display:none;">Sent To</th>
                                        <th class="no-sort text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM hrm_notification;";
                                    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                                    $x = 0;
                                    while ($row = mysqli_fetch_array($result)) {
                                        $x++;
                                    ?>
                                        <tr>
                                            <td><?php echo $x; ?></td>
                                            <td><?php echo $row['title']; ?></td>
                                            <td><?php echo $row['description']; ?></td>
                                            <td><?php echo $row['date'] . $row['time']; ?></td>
                                            <td style="display:none;"><?php echo $row['sent_by']; ?></td>
                                            <td style="display:none;"><?php echo $row['send_to']; ?></td>
                                            <td class="text-end">
                                                <div class="dropdown dropdown-action">
                                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <?php if ($row_employee['role'] == 'admin' || $row_employee['role'] == 'super admin') { ?>
                                                            <a class="dropdown-item" href="#" onclick="edit_notification(<?php echo $row['id']; ?>, '<?php echo $row['title']; ?>', '<?php echo $row['description']; ?>')"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="#" onclick="delete_notification(<?php echo $row['id']; ?>)"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Content -->
    </div>
 
    <div class="modal custom-modal fade custom-modal-two modal-padding" id="add_activity" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header header-border justify-content-between p-0">
                    <h5 class="modal-title">Add New Notification</h5>
                    <button type="button" class="btn-close position-static" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="contact-input-set">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Title <span class="text-danger">*</span></label>
                                    <input class="form-control" required type="text" name="title" id="title">
                                </div>
                            </div>
                            <div class="col-md-6" style="display:none;">
                                <div class="input-block mb-3 activity-date-picker">
                                    <label class="col-form-label">Date <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input class="form-control floating datetimepicker" type="text" name="date1" id="date1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="display:none;">
                                <div class="activity-date-picker input-block mb-3">
                                    <label class="col-form-label">Time <span class="text-danger">*</span></label>
                                    <div class="cal-icon time-icon">
                                        <input type="text" class="form-control timepicker" name="time" id="time">
                                        <span class="cus-icon"><i class="feather-clock"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                    <textarea required class="form-control" rows="5" name="description" id="description"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="input-block mb-3">
                                    <script language="JavaScript">
                                        function selectall(source) {
                                            checkboxes = document.getElementsByName('emp_send_to[]');
                                            for (var i = 0, n = checkboxes.length; i < n; i++) {
                                                checkboxes[i].checked = source.checked;
                                            }
                                        }
                                    </script>
                                    <label class="col-form-label">Send To <span class="text-danger">*</span></label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="custom_check"><input name="emp_send_to" id="emp_send_to" type="checkbox" value="0" onClick="selectall(this)">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Check All
                                        <span class="checkmark"></span>
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <?php
                                    $query_display_employee = "SELECT * FROM hrm_employee WHERE status=1;";
                                    $result_display_employee = mysqli_query($conn, $query_display_employee) or die(mysqli_error($conn));
                                    while ($row_display_employee = mysqli_fetch_array($result_display_employee)) { ?>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <label class="custom_check"><input name="emp_send_to[]" id="emp_send_to[]" type="checkbox" value="<?php echo $row_display_employee['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_display_employee['fname'] . " " . $row_display_employee['lname']; ?>
                                            <span class="checkmark"></span>
                                        </label>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end form-wizard-button">
                                <button class="button btn-lights reset-btn" type="reset" data-bs-dismiss="modal">Reset</button>
                                <button class="btn btn-primary" type="submit" onclick="save_notification(document.getElementById('title').value, document.getElementById('description').value);">Save Announcement</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
<div class="modal custom-modal fade custom-modal-two modal-padding" id="edit_activity" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header header-border justify-content-between p-0">
                <h5 class="modal-title">Edit Notification</h5>
                <button type="button" class="btn-close position-static" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="contact-input-set">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Title <span class="text-danger">*</span></label>
                                <input class="form-control" required type="text" name="edit_title" id="edit_title">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                <textarea required class="form-control" rows="5" name="edit_description" id="edit_description"></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="col-lg-12 text-end form-wizard-button">
                            <button class="button btn-lights reset-btn" type="reset" data-bs-dismiss="modal">Reset</button>
                            <button class="btn btn-primary" type="submit" onclick="update_notification();">Update Announcement</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <?php include 'layouts/customizer.php'; ?>

    <?php include 'layouts/vendor-scripts.php'; ?>
    <script>function edit_notification(id, title, description) {
    $('#edit_title').val(title);
    $('#edit_description').val(description);
    $('#edit_id').val(id);
    $('#edit_activity').modal('show');
}</script>
<script>
    $(document).ready(function() {
   
    var table = $('.datatable').DataTable({
        retrieve: true
    });

   
    $('#someButton').click(function() {
        table.destroy();
        table = $('.datatable').DataTable();
    });
});
</script>
</body>

</html>