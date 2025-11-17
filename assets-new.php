<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>

    <title>Assets - HRMS admin template</title>

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
                            <h3 class="page-title">Assets</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Assets</li>
                            </ul>
                        </div>
                        <div class="col-auto float-end ms-auto">
                            <ul class="split-head">
                                <li>
                                    <div class="bookingrange btn btn-white btn-sm ">
                                        <i class="far fa-calendar-alt me-2"></i>
                                        <span>November 22, 2022 - November 28, 2022</span>
                                        <i class="fas fa-chevron-down ms-2"></i>
                                    </div>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-whiteline"><i class="fa-solid fa-download me-2" aria-hidden="true"></i>CSV</a>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-whiteline"><i class="fa-solid fa-filter me-2" aria-hidden="true"></i>Filters</a>
                                </li>
                                <li>
                                    <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_asset"><i class="fa-solid fa-plus"></i> Add New Asset</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                
                <!-- Search Filter -->
                <div class="row">
                    <div class="col-lg-2 d-flex">
                        <div class="total-widget flex-fill">
                            <h6>Total Assets</h6>
                            <h4>20</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 d-flex">
                        <div class="total-widget flex-fill">
                            <h6>Allocated</h6>
                            <h4>10</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 d-flex">
                        <div class="total-widget flex-fill">
                            <h6>Damaged</h6>
                            <h4>2</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 d-flex">
                        <div class="total-widget flex-fill">
                            <h6>Repaired</h6>
                            <h4>1</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 d-flex">
                        <div class="total-widget flex-fill">
                            <h6>Not Using</h6>
                            <h4>7</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 d-flex">
                        <div class="total-widget flex-fill">
                            <h6>Lost Items</h6>
                            <h4>2</h4>
                        </div>
                    </div>
                </div>
                <!-- /Search Filter -->
                
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
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_estimate">
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
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_estimate">
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
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_estimate">
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
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_estimate">
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
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_estimate">
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
                                                 <a class="delete-table confirm-text" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_estimate">
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
        
            
            
            <!-- Edit Asset Modal -->
            <div id="edit_asset" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Asset</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Asset Name</label>
                                            <input class="form-control" type="text" value="Dell Laptop">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Asset Id</label>
                                            <input class="form-control" type="text" value="#AST-0001" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Purchase Date</label>
                                            <input class="form-control datetimepicker" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Purchase From</label>
                                            <input class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Manufacturer</label>
                                            <input class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Model</label>
                                            <input class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Serial Number</label>
                                            <input class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Supplier</label>
                                            <input class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Condition</label>
                                            <input class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Warranty</label>
                                            <input class="form-control" type="text" placeholder="In Months">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Value</label>
                                            <input placeholder="$1800" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Asset User</label>
                                            <select class="select">
                                                <option>John Doe</option>
                                                <option>Richard Miles</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Description</label>
                                            <textarea class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Status</label>
                                            <select class="select">
                                                <option>Pending</option>
                                                <option>Approved</option>
                                                <option>Deployed</option>
                                                <option>Damaged</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Asset Modal -->
            
            <!-- Delete Asset Modal -->
            <div class="modal custom-modal fade" id="delete_asset" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Delete Asset</h3>
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

		<!-- add modal -->
		<div id="add_asset" class="modal custom-modal fade" role="dialog">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<h5 class="modal-title">Add New Asset</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Asset Name</label>
										<input class="form-control" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Asset Id</label>
										<input class="form-control" type="text" value="AST-235" disabled>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3 selectnew">
										<label class="col-form-label">Category</label>
										<select class="select">
											<option>Category 1</option>
											<option>Category 2</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="input-block mb-3">
										<label class="col-form-label">Warranty</label>
										<div class="cal-icon">
											<input class="form-control datetimepicker" type="text" placeholder="Start Date">
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="input-block mb-3">
										<label class="col-form-label">&nbsp;</label>
										<div class="cal-icon">
											<input class="form-control datetimepicker" type="text" placeholder="End Date">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Type</label>
										<input class="form-control" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Brand</label>
										<input class="form-control" type="text">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Model</label>
										<input class="form-control" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Serial Number</label>
										<input class="form-control" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Vendor</label>
										<input class="form-control" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Cost</label>
										<input class="form-control" type="text" >
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Location</label>
										<input  class="form-control" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Asset Pictures</label>
										<div class="file-upload-popup">
											<input type="text" class="form-control">
											<div class="file-upload-popupcontent">
												<input type="file">
												<span>Browse</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="submit-section">
								<button class="btn btn-primary submit-btns">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- edit modal -->
		<div id="edit-asset" class="modal custom-modal fade" role="dialog">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<h5 class="modal-title">Edit  Asset</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Asset Name</label>
										<input class="form-control" type="text" value="Laptop">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Asset Id</label>
										<input class="form-control" type="text" value="AST-235" disabled>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3 selectnew">
										<label class="col-form-label">Category</label>
										<select class="select">
											<option>Laptop</option>
											<option>Keyboard</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="input-block mb-3">
										<label class="col-form-label">Warranty</label>
										<div class="cal-icon">
											<input class="form-control datetimepicker" type="text" value="23-12-2022">
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="input-block mb-3">
										<label class="col-form-label">&nbsp;</label>
										<div class="cal-icon">
											<input class="form-control datetimepicker" type="text" value="23-12-2022">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Type</label>
										<input class="form-control" type="text" value="computer">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Brand</label>
										<input class="form-control" type="text" value="brand">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Model</label>
										<input class="form-control" type="text" value="samsung">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Serial Number</label>
										<input class="form-control" type="text" value="123456789">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Vendor</label>
										<input class="form-control" type="text" value="name">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Cost</label>
										<input class="form-control" type="text" value="250">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Location</label>
										<input  class="form-control" type="text" value="usa">
									</div>
								</div>
								<div class="col-md-6">
									<div class="input-block mb-3">
										<label class="col-form-label">Asset Pictures</label>
										<div class="file-upload-popup">
											<input type="text" class="form-control">
											<div class="file-upload-popupcontent">
												<input type="file">
												<span>Browse</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="submit-section">
								<button class="btn btn-primary submit-btns">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Delete Estimate Modal -->
		<div class="modal custom-modal fade" id="delete_estimate" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="form-header">
							<h3>Delete Asset</h3>
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
		<!-- /Delete Estimate Modal -->


		<!-- assign  Modal -->
		<div  class="modal custom-modal fade" id="add-assigns"  role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<h5 class="modal-title">Assign Asset</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<div class="row">
								<div class="col-md-12 selectnew">
									<div class="input-block mb-3">
										<label class="col-form-label">Department</label>
										<select class="select">
											<option>Department 1</option>
											<option>Department 2</option>
										</select>
									</div>
								</div>
								<div class="col-md-12 selectnew">
									<div class="input-block mb-3">
										<label class="col-form-label">Assign to</label>
										<select class="select">
											<option>Customer</option>
											<option>Client</option>
										</select>
									</div>
								</div>
								<div class="submit-section mt-3">
									<button class="btn btn-primary submit-btns w-100">Submit</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

<?php include 'layouts/customizer.php'; ?>
<!-- JAVASCRIPT -->
<?php include 'layouts/vendor-scripts.php'; ?>



</body>

</html>