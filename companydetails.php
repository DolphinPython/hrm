<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

// Get user name and other details
$emp_id = $_SESSION['id'];
$conn = connect();
$query = "select * from hrm_employee where id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];

$active_employee = count_where("hrm_employee", "status", "1");
$inactive_employee = count_where("hrm_employee", "status", "0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Employee Dashboard - HRMS Admin Template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        /* Custom Styles */
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .page-wrapper {
            background-color: #f8f9fa;
        }

        .company-details-card {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #e0e0e0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .company-details-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            border-color: #007bff;
        }

        .company-details-card h3 {
            color: #007bff;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .company-details-card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
            transition: border-color 0.3s ease;
        }

        .company-details-card img:hover {
            border-color: #007bff;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group strong {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .form-group p {
            margin: 0;
            color: #555;
            font-size: 1rem;
        }

        .social-icons i {
            font-size: 30px;
            margin-right: 15px;
            color: #007bff;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .social-icons a:hover i {
            color: #0056b3;
            transform: scale(1.2);
        }

        .map-container {
            height: 400px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            border: 2px solid #e0e0e0;
            transition: border-color 0.3s ease;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 15px;
        }

        .map-container:hover {
            border-color: #007bff;
        }

        .breadcrumb {
            background-color: #ffffff;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #007bff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container-top-margin {
            margin-top: 30px;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .company-details-card,
        .breadcrumb,
        .page-header {
            animation: fadeIn 0.8s ease-out;
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <div class="main-wrapper">
            <?php include 'layouts/menu.php'; ?>

            <?php
            $query = "SELECT * FROM companies";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
            } else {
                $data = null;
            }
            ?>

            <!-- Page Wrapper -->
            <div class="container container-top-margin">
                <div class="page-header">
                    <h1>Company Details</h1>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="employee-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Company details</li>
                </ul>
                <div class="row">
                    <?php if ($data) { ?>
                        <!-- Company Logo and Banner -->
                        <div class="col-md-6">
    <div class="company-details-card">
        <h3>Company Logo</h3>
        <?php if (!empty($data['logo'])) { ?>
            <div class="logo-container">
                <img src="uploads/<?php echo htmlspecialchars($data['logo']); ?>" alt="<?php echo htmlspecialchars($data['logo_alt_text']); ?>">
            </div>
        <?php } else { ?>
            <p class="card-text"><strong>Logo:</strong> No Logo</p>
        <?php } ?>
    </div>
</div>

<div class="col-md-6">
    <div class="company-details-card">
        <h3>Company Banner</h3>
        <?php if (!empty($data['banner'])) { ?>
            <div class="banner-container">
                <img src="uploads/<?php echo htmlspecialchars($data['banner']); ?>" alt="<?php echo htmlspecialchars($data['banner_alt_text']); ?>">
            </div>
        <?php } else { ?>
            <p class="card-text"><strong>Banner:</strong> No Banner</p>
        <?php } ?>
    </div>
</div>

                        <!-- Company Information -->
                        <div class="col-md-12">
                            <div class="company-details-card">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Company Name:</strong>
                                            <p><?php echo htmlspecialchars($data['name']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Email:</strong>
                                            <p><?php echo htmlspecialchars($data['email']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Mobile 1:</strong>
                                            <p><?php echo htmlspecialchars($data['mobile1']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Mobile 2:</strong>
                                            <p><?php echo htmlspecialchars($data['mobile2']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Address:</strong>
                                            <p><?php echo htmlspecialchars($data['address1']) . ', ' . htmlspecialchars($data['address2']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Website:</strong>
                                            <p><a href="<?php echo htmlspecialchars($data['website']); ?>" target="_blank"><?php echo htmlspecialchars($data['website']); ?></a></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Industry:</strong>
                                            <p><?php echo htmlspecialchars($data['industry']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Status:</strong>
                                            <p><?php echo ucfirst(htmlspecialchars($data['status'])); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Founded Year:</strong>
                                            <p><?php echo htmlspecialchars($data['founded_year']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>No. of Employees:</strong>
                                            <p><?php echo htmlspecialchars($data['employee_count']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <strong>Description:</strong>
                                            <p><?php echo htmlspecialchars($data['description']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Operating Hours -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <strong>Operating Hours:</strong>
                                            <p><?php echo htmlspecialchars($data['operating_hours']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Parent Company and Additional Contacts -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Parent Company:</strong>
                                            <p><?php echo htmlspecialchars($data['parent_company']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Additional Contact:</strong>
                                            <p><?php echo htmlspecialchars($data['additional_contact']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Social Media Links -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="social-icons">
                                            <?php if (!empty($data['linkedin'])) { ?>
                                                <a href="<?php echo htmlspecialchars($data['linkedin']); ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                                            <?php } ?>
                                            <?php if (!empty($data['facebook'])) { ?>
                                                <a href="<?php echo htmlspecialchars($data['facebook']); ?>" target="_blank"><i class="fab fa-facebook"></i></a>
                                            <?php } ?>
                                            <?php if (!empty($data['twitter'])) { ?>
                                                <a href="<?php echo htmlspecialchars($data['twitter']); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Section (Map) -->
                        <div class="col-md-12">
                            <div class="company-details-card">
                                <h3>Location</h3>
                                <?php if (!empty($data['longitude'])) { ?>
                                    <div class="map-container">
                                        <?php echo htmlspecialchars_decode($data['longitude']); ?>
                                    </div>
                                <?php } else { ?>
                                    <p>No location data available.</p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="col-md-12">
                            <p>Company data is not available.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- /Page Wrapper -->
        </div>
    </div>
    <!-- end main wrapper-->

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
</body>

</html>