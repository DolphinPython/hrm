<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <meta charset="utf-8" />
    <title>Forgot Password | HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>

</head>

<body class="account-page">
	
    <!-- Main Wrapper -->
    <div class="main-wrapper">
    
        <div class="account-content">
            <div class="container">
            
                <!-- Account Logo -->
                <div class="account-logo">
                    <a href="admin-dashboard.php"><img src="assets/img/logo2.png" alt="Dreamguy's Technologies"></a>
                </div>
                <!-- /Account Logo -->
                
                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Forgot Password?</h3>
                        <p class="account-subtitle">Enter your email to get a password reset link</p>
                        
                        <!-- Account Form -->
                        <form>
                            <div class="input-block mb-4">
                                <label class="col-form-label">Email Address</label>
                                <input class="form-control" type="text">
                            </div>
                            <div class="input-block mb-4 text-center">
                                <button class="btn btn-primary account-btn" type="submit">Reset Password</button>
                            </div>
                            <div class="account-footer">
                                <p>Remember your password? <a href="index.php">Login</a></p>
                            </div>
                        </form>
                        <!-- /Account Form -->
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->


        <!-- JAVASCRIPT -->
        <?php include 'layouts/vendor-scripts.php'; ?>

    </body>

</html>