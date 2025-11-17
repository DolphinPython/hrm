<?php //include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

    <head>
        
        <title>Login - HRMS admin template</title>
        <?php include 'layouts/title-meta.php'; ?>

        <?php include 'layouts/head-css.php'; ?>
<style>
    .modal-content {
        border-radius: 10px;
    }
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: none;
    }
    .modal-body {
        text-align: left;
        padding: 20px;
    }
    .modal-footer {
        border-top: none;
        justify-content: center;
    }
    .modal-body p, .modal-body ul {
        margin-bottom: 15px;
    }
    .modal-body strong {
        font-weight: 600;
    }
</style>
    </head>

    <body class="account-page">
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
			<div class="account-content">
				<!--<a href="job-list.php" class="btn btn-primary apply-btn">Apply Job</a>-->
				<div class="container">
				
					<!-- Account Logo -->
					<div class="account-logo">
						<a href="admin-dashboard.php"><img src="assets/img/logo2.png" alt="Dreamguy's Technologies"></a>
					</div>
					<!-- /Account Logo -->
					
					<div class="account-box">
						<div class="account-wrapper">
							<h3 class="account-title">Login</h3>
							<p class="account-subtitle">Access to our dashboard</p>
							
							<!-- Account Form -->
							<form name="f1" id="f1" action="loginck.php" method="post">
								<div class="input-block mb-4">
									<label class="col-form-label">Email Address</label>
									<input class="form-control" type="text" value="" name="email" id="email">
								</div>
								<div class="input-block mb-4">
									<div class="row align-items-center">
										<div class="col">
											<label class="col-form-label">Password</label>
										</div>
										<div class="col-auto">
    <a class="text-muted" href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
        Forgot password?
    </a>
</div>
									</div>
									<div class="position-relative">
										<input class="form-control" type="password" value="" name="password" id="password">
										<span class="fa-solid fa-eye-slash" id="toggle-password"></span>
									</div>
								</div>
								<div class="input-block mb-4 text-center">
									<button class="btn btn-primary account-btn" type="submit" name="b1" id="b1">Login</button>
								</div>
								<!--<div class="account-footer">-->
								<!--	<p>Don't have an account yet? <a href="register.php">Register</a></p>-->
								<!--</div>-->
							</form>
							<!-- /Account Form -->
							 <?php ?>
									
						</div>
					</div>
				</div>
			</div>
        </div>
        <!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Password Recovery Instructions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>If you need to recover your password, please contact the HR department at <a href="mailto:hr@1solutions.biz">hr@1solutions.biz</a>.</p>
                <p>Once you receive your new password from HR, kindly log in to your dashboard immediately and reset your password for security purposes.</p>
                <p>To change your password, follow this path:<br>
                   Dashboard → Settings → Change Password</p>
                <p><strong>Note:</strong> It is strongly recommended to reset your password as soon as you log in using the temporary one.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /Forgot Password Modal -->
		<!-- /Main Wrapper -->
        <?php include 'layouts/vendor-scripts.php'; ?>

    </body>

</html>