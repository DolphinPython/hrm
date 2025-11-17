<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>

    <title>Error 500 - HRMS admin template</title>

    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>

</head>

  <body class="error-page">
        <!-- Main Wrapper -->
        <div class="main-wrapper">
            
            <div class="error-box">
                <h1>500</h1>
                <h3><i class="fa fa-warning"></i> Oops! Something went wrong</h3>
                <p>The page you requested was not found.</p>
                <a href="admin-dashboard.php" class="btn btn-custom">Back to Home</a>
            </div>
        
        </div>
        <!-- /Main Wrapper -->

        <!-- JAVASCRIPT -->
       <?php include 'layouts/vendor-scripts.php'; ?>

    </body>

</html>