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
                            <h3 class="page-title">Category</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Category</li>
                            </ul>
                        </div>
                        <div class="col-auto float-end ms-auto">
                            <ul class="split-head">
                                <li>
                                    <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_asset"><i class="fa-solid fa-plus"></i>  Add Category</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive table-newdatatable">
                            <table class="table table-new custom-table mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Category Name</th>
                                        <th>Created On</th>
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
                                        <td>12/12/2022 at 11.23am</td>
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
                                        <td>12/12/2022 at 11.23am</td>
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
                                        <td>12/12/2022 at 11.23am</td>
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
                                        <td>12/12/2022 at 11.23am</td>
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
                                        <td>12/12/2022 at 11.23am</td>
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
                                        <td>12/12/2022 at 11.23am</td>
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
			<div class="modal-dialog" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<h5 class="modal-title">Add Category</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<div class="row">
								<div class="col-md-12">
									<div class="input-block mb-3">
										<label class="col-form-label">Category Name</label>
										<input class="form-control" type="text">
									</div>
									<div class="input-block mb-3">
										<label class="col-form-label">Upload Category Icon</label>
										<div class="d-flex align-items-center flex-wrap">
											<div class="cat-upload me-3">
												<i class="fas fa-plus"></i>
												<input class="upload" type="file">
											</div>
											<div class="cat-upload-view me-2">
												<img src="assets/img/img1.jpg" alt="Keyboard Image">
												<a href="#" class="img-close"><i class="fas fa-times"></i></a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="submit-section mt-2">
								<button class="btn btn-primary submit-btns w-100">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- edit modal -->
		<div id="edit-asset" class="modal custom-modal fade" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<h5 class="modal-title">Edit  Category</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<div class="row">
								
								<div class="col-md-12">
									<div class="input-block mb-3">
										<label class="col-form-label">Category Name</label>
										<input class="form-control" type="text" value="laptop">
									</div>
									<div class="input-block mb-3">
										<label class="col-form-label">Upload Category Icon</label>
										<div class="d-flex align-items-center flex-wrap">
											<div class="cat-upload me-3">
												<i class="fas fa-plus"></i>
												<input class="upload" type="file">
											</div>
											<div class="cat-upload-view me-2">
												<img src="assets/img/img1.jpg" alt="Keyboard Image">
												<a href="#" class="img-close"><i class="fas fa-times"></i></a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="submit-section mt-2">
								<button class="btn btn-primary submit-btns w-100">Submit</button>
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
							<h3>Delete Category</h3>
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
		<div  class="modal custom-modal fade" id="add-assigns"  role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<h5 class="modal-title">Add Category</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<div class="row">
								<div class="col-md-12 selectnew">
									<div class="input-block mb-3">
										<label class="col-form-label">Category Name</label>
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