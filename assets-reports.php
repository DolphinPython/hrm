<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>

    <title>Employees - HRMS admin template</title>

    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>

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
                            <h3 class="page-title">Reports</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Reports</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                
                <div class="row filter-row mb-4">
                    <div class="col-sm-6 col-md-3 col-xxl-3">  
                        <div class="input-block mb-3">
                            <div class="cals-icon">
                                <input class="form-control" type="text" placeholder="Search by asset name">
                                <span class="icon-text"><i class="fas fa-laptop"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-xxl-3"> 
                        <div class="input-block mb-3">
                            <div class="cals-icon">
                                <input class="form-control" type="text" placeholder="Search by allocated name">
                                <span class="icon-text"><i class="fa-solid fa-user"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-xxl-2">  
                        <div class="input-block mb-3 ">
                            <div class="cals-icon">
                                <input class="form-control" type="text" placeholder="Asset ID">
                                <span class="icon-text"><i class="fa-regular fa-folder-open"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-xxl-2">  
                        <div class="input-block mb-3">
                            <div class="cal-icon">
                                <input class="form-control" type="text" placeholder="Category">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-xxl-2">  
                        <a href="#" class="btn btn-primary w-100 text-capitalize"><i class="file-text"></i> Generate Report</a>  
                    </div>     
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive table-newdatatable">
                            <table class="table table-new custom-table mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Asset ID</th>
                                        <th>Category</th>
                                        <th>Department</th>
                                        <th>Allocated to</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <a href="assets-details.php" class="table-imgname">
                                                <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                                                <span>Laptop</span>
                                            </a>
                                        </td>
                                        <td>AST - 001</td>
                                        <td>Laptop</td>
                                        <td>UI</td>
                                        <td class="table-namesplit">
                                            <a href="javascript:void(0);" class="table-profileimage">
                                                <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="table-name">
                                                <span>John Paul Raj</span>
                                                <p>john@dreamguystech.com</p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="dropdown action-label">
                                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle text-primary" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Assigned 
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#"> Pending</a>
                                                    <a class="dropdown-item" href="#">Approved</a>
                                                    <a class="dropdown-item" href="#">Returned</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" data-bs-toggle="modal" data-bs-target="#edit-asset" href="javascript:void(0);">
                                                   <img src="assets/img/icons/edit.svg" alt="Edit Icon">
                                                </a>
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_asset">
                                                    <img src="assets/img/icons/delete.svg" alt="Delete Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                                            <a href="assets-details.php" class="table-imgname">
                                                <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                                                <span>Laptop</span>
                                            </a>
                                        </td>
                                        <td>AST - 002</td>
                                        <td>Computer</td>
                                        <td>PHP</td>
                                        <td class="table-namesplit">
                                            <a href="javascript:void(0);" class="table-profileimage" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                <img src="assets/img/profiles/avatar-05.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="table-name">
                                                <span>Vinod Selvaraj</span>
                                                <p>vinod.s@dreamguystech.com</p>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#add-assigns" class="btn btn-assign" ><i class="fa-solid fa-plus"></i> Assign  </a>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                   <img src="assets/img/icons/edit.svg" alt="Edit Icon">
                                                </a>
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_asset">
                                                    <img src="assets/img/icons/delete.svg" alt="Delete Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
                                            <a href="assets-details.php" class="table-imgname">
                                                <img src="assets/img/keyboard.png" class="me-2" alt="Keyboard Image">
                                                <span>Dell Keyboard</span>
                                            </a>
                                        </td>
                                        <td>AST - 003</td>
                                        <td>Keyboard</td>
                                        <td>SEO</td>
                                        <td class="table-namesplit">
                                            <a href="javascript:void(0);" class="table-profileimage" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                <img src="assets/img/profiles/avatar-03.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="table-name">
                                                <span>Harika </span>
                                                <p>harika.v@dreamguystech.com</p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="dropdown action-label">
                                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle text-primary" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Assigned 
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#"> Pending</a>
                                                    <a class="dropdown-item" href="#">Approved</a>
                                                    <a class="dropdown-item" href="#">Returned</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                   <img src="assets/img/icons/edit.svg" alt="Edit Icon">
                                                </a>
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_asset">
                                                    <img src="assets/img/icons/delete.svg" alt="Delete Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>
                                            <a href="#" class="table-imgname">
                                                <img src="assets/img/mouse.png" class="me-2" alt="Mouse Image">
                                                <span>Logitech Mouse</span>
                                            </a>
                                        </td>
                                        <td>AST - 0024</td>
                                        <td>Mouse</td>
                                        <td>Android</td>
                                        <td class="table-namesplit">
                                            <a href="assets-details.php" class="table-profileimage" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                <img src="assets/img/profiles/avatar-03.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="assets-details.php" class="table-name">
                                                <span>Mythili  </span>
                                                <p>mythili@dreamguystech.com</p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="dropdown action-label">
                                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle text-primary" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Assigned 
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#"> Pending</a>
                                                    <a class="dropdown-item" href="#">Approved</a>
                                                    <a class="dropdown-item" href="#">Returned</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                   <img src="assets/img/icons/edit.svg" alt="Edit Icon">
                                                </a>
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_asset">
                                                    <img src="assets/img/icons/delete.svg" alt="Delete Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>
                                            <a href="#" class="table-imgname">
                                                <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                                                <span>Laptop</span>
                                            </a>
                                        </td>
                                        <td>AST - 005</td>
                                        <td>Laptop</td>
                                        <td>UI</td>
                                        <td class="table-namesplit">
                                            <a href="assets-details.php" class="table-profileimage" >
                                                <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="assets-details.php" class="table-name">
                                                <span>John Paul Raj</span>
                                                <p>john@dreamguystech.com</p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="dropdown action-label">
                                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle text-primary" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Assigned 
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#"> Pending</a>
                                                    <a class="dropdown-item" href="#">Approved</a>
                                                    <a class="dropdown-item" href="#">Returned</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                   <img src="assets/img/icons/edit.svg" alt="Edit Icon">
                                                </a>
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_asset">
                                                    <img src="assets/img/icons/delete.svg" alt="Delete Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>
                                            <a href="#" class="table-imgname">
                                                <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                                                <span>Laptop</span>
                                            </a>
                                        </td>
                                        <td>AST - 006</td>
                                        <td>Computer</td>
                                        <td>PHP</td>
                                        <td class="table-namesplit">
                                            <a href="javascript:void(0);" class="table-profileimage" >
                                                <img src="assets/img/profiles/avatar-05.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="table-name">
                                                <span>Vinod Selvaraj</span>
                                                <p>vinod.s@dreamguystech.com</p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="dropdown action-label">
                                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle text-primary" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Assigned 
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#"> Pending</a>
                                                    <a class="dropdown-item" href="#">Approved</a>
                                                    <a class="dropdown-item" href="#">Returned</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                   <img src="assets/img/icons/edit.svg" alt="Edit Icon">
                                                </a>
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_asset">
                                                    <img src="assets/img/icons/delete.svg" alt="Delete Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Content -->
            
            <!-- Delete Asset Modal -->
            <div class="modal custom-modal fade" id="delete_asset" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Delete Report</h3>
                                <p>Are you sure want to delete?</p>
                            </div>
                            <div class="modal-btn delete-action">
                                <div class="row">
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-primary continue-btn">Delete</a>
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
            <!-- /Delete Asset Modal -->
            
        </div>
        <!-- /Page Wrapper -->

    </div>
<!-- end main wrapper-->
<?php include 'layouts/customizer.php'; ?>
<!-- JAVASCRIPT -->
<?php include 'layouts/vendor-scripts.php'; ?>



</body>

</html>