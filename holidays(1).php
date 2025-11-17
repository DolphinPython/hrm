<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'layouts/session.php'; ?>
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

$current_month = date("m") - 1;
$current_year = date("Y");
//echo "profile_image".$profile_image;
?>

<head>

    <title>Holidays - HRMS admin template</title>

    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>
    <script language="javascript">
        function save_holidays(name, date, no_of_days, year) {
            $.ajax({
                type: "GET",
                url: "save_holidays.php",
                data: "name=" + name + "&date=" + date + "&no_of_days=" + no_of_days +
                    "&year=" + year + "&emp_id=" + <?php echo $_SESSION['id']; ?>,
                success: function(data) {
                    alert(data);
                    //$("#last_insert_id").val(data);

                }
            });

            $('#add_holiday').modal('hide');
            location.reload();

        }


        function update_holidays(name, date, no_of_days, year, ids) {
            $.ajax({
                type: "GET",
                url: "update-holidays.php",
                data: "name=" + name + "&date=" + date + "&no_of_days=" + no_of_days +
                    "&year=" + year + "&emp_id=" + <?php echo $_SESSION['id']; ?> +
                    "&ids=" + ids,
                success: function(data) {
                    alert(data);
                    //$("#last_insert_id").val(data);

                }
            });

            //$('#edit_holiday').modal('hide');
            location.reload();
            //alert(ids); 

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
                        <div class="col">
                            <h3 class="page-title">Holidays </h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Holidays</li>
                            </ul>
                        </div>
                        <div class="col-auto float-end ms-auto">
                            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_holiday"><i class="fa-solid fa-plus"></i> Add Holiday</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title </th>
                                        <th>Holiday Date</th>
                                        <th>No. Of Days</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    //$current_month = date("m")-1; 
                                    $current_year = date("Y");

                                    //$query="select * from hrm_holidays where year='$current_year';";
                                    $query = "select * from hrm_holidays;";
                                    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                                    $x = 0;
                                    while ($row = mysqli_fetch_array($result)) {
                                        $x++;
                                    ?>
                                        <tr class="holiday-completed">
                                            <td><?php echo $x; ?></td>
                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['date']; ?></td>
                                            <td><?php echo $row['no_of_days']; ?></td>
                                            <td class="text-end">
                                                <div class="dropdown dropdown-action">
                                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_holiday<?php echo $row['id']; ?>"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                        <!-- <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a> -->
                                                        <a class="dropdown-item delete-holiday-btn" href="#" data-holiday-id="<?php echo $row['id']; ?>" data-bs-toggle="modal" data-bs-target="#delete_holiday">
                                                            <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                        </a>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Edit Holiday Modal -->


                                        <?php

                                        // edit

                                        $query_edit = "select * from hrm_holidays where id='$row[id]';";
                                        $result_edit = mysqli_query($conn, $query_edit) or die(mysqli_error($conn));
                                        //$x="";
                                        $row_edit = mysqli_fetch_array($result_edit);


                                        // edit
                                        ?>
                                        <div class="modal custom-modal fade" id="edit_holiday<?php echo $row['id']; ?>" role="dialog">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Holiday</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form>
                                                            <div class="input-block mb-3">
                                                                <label class="col-form-label">Holiday Name <span class="text-danger">*</span></label>
                                                                <input class="form-control" type="text"
                                                                    name="name<?php echo $row['id']; ?>"
                                                                    id="name<?php echo $row['id']; ?>"
                                                                    value="<?php echo $row_edit['name']; ?>">
                                                            </div>
                                                            <div class="input-block mb-3">
                                                                <label class="col-form-label">Holiday Date <span class="text-danger">*</span></label>
                                                                <div class="cal-icon"><input
                                                                        class="form-control datetimepicker" type="text"
                                                                        name="date<?php echo $row['id']; ?>"
                                                                        id="date<?php echo $row['id']; ?>"
                                                                        value="<?php echo $row_edit['date']; ?>"></div>
                                                            </div>

                                                            <div class="input-block mb-3">
                                                                <label class="col-form-label">No. of Days <span class="text-danger">*</span></label>
                                                                <input class="form-control" type="text"
                                                                    name="no_of_days<?php echo $row['id']; ?>"
                                                                    id="no_of_days<?php echo $row['id']; ?>"
                                                                    value="<?php echo $row_edit['no_of_days']; ?>">
                                                            </div>
                                                            <div class="input-block mb-3">
                                                                <label class="col-form-label">Year <span class="text-danger">*</span></label>

                                                                <select name="year<?php echo $row['id']; ?>" id="year<?php echo $row['id']; ?>" class="form-control">
                                                                    <option value="2024"
                                                                        <?php if ($row['year'] == "2024") echo "selected='selected'"; ?>>2024</option>
                                                                    <option value="2025"
                                                                        <?php if ($row['year'] == "2025") echo "selected='selected'"; ?>>2025</option>
                                                                </select>
                                                            </div>
                                                            <div class="submit-section">
                                                                <button class="btn btn-primary submit-btn" onclick="update_holidays(
                              document.getElementById('name<?php echo $row['id']; ?>').value,  
                              document.getElementById('date<?php echo $row['id']; ?>').value, 
                              document.getElementById('no_of_days<?php echo $row['id']; ?>').value, 
                              document.getElementById('year<?php echo $row['id']; ?>').value,
                              '<?php echo $row['id']; ?>' 
                                    );">Submit</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Edit Holiday Modal -->

                                    <?php } ?>

                                    <tr>
                                        <th>#</th>
                                        <th>Title </th>
                                        <th>Holiday Date</th>
                                        <th>No. Of Days</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /Page Content -->

            <!-- Add Holiday Modal -->
            <div class="modal custom-modal fade" id="add_holiday" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Holiday</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <form>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Holiday Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="name" id="name">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Holiday Date <span class="text-danger">*</span></label>
                                    <div class="cal-icon"><input
                                            class="form-control datetimepicker" type="text" name="date" id="date"></div>
                                </div>

                                <div class="input-block mb-3">
                                    <label class="col-form-label">No. of Days <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="no_of_days" id="no_of_days">
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Year <span class="text-danger">*</span></label>

                                    <select name="year" id="year" class="form-control">
                                        <option value="2024"
                                            <?php if ($current_year == "2024") echo "selected='selected'"; ?>>2024</option>
                                        <option value="2025"
                                            <?php if ($current_year == "2025") echo "selected='selected'"; ?>>2025</option>
                                    </select>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn" onclick="save_holidays(
                              document.getElementById('name').value,  document.getElementById('date').value,
                                 document.getElementById('no_of_days').value, document.getElementById('year').value 
                                    );">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Add Holiday Modal -->



            <!-- Delete Holiday Modal -->
            <div class="modal custom-modal fade" id="delete_holiday" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Delete Holiday</h3>
                                <p>Are you sure you want to delete?</p>
                            </div>
                            <div class="modal-btn delete-action">
                                <div class="row">
                                    <div class="col-6">
                                        <!-- Add a data attribute to store the holiday ID -->
                                        <a href="javascript:void(0);" class="btn btn-primary continue-btn" id="confirm-delete-btn">Delete</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /Delete Holiday Modal -->

        </div>
        <!-- /Page Wrapper -->



    </div>
    <!-- end main wrapper-->

    <?php include 'layouts/customizer.php'; ?>
    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let holidayIdToDelete = null; // Store the holiday ID to delete

            // Open the delete modal and set the holiday ID
            document.querySelectorAll('.delete-holiday-btn').forEach(button => {
                button.addEventListener('click', function() {
                    holidayIdToDelete = this.dataset.holidayId; // Get holiday ID from data attribute
                    const modal = new bootstrap.Modal(document.getElementById('delete_holiday'));
                    modal.show();
                });
            });

            // Handle the "Delete" button click in the modal
            document.getElementById('confirm-delete-btn').addEventListener('click', function() {
                if (holidayIdToDelete) {
                    // Send AJAX request to delete the holiday
                    fetch('delete_holidays.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `id=${holidayIdToDelete}`
                        })
                        .then(response => response.text())
                        .then(data => {
                            alert(data); // Show success/error message
                            location.reload(); // Reload the page or update the UI dynamically
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to delete holiday.');
                        });
                }
            });
        });
    </script>



</body>

</html>