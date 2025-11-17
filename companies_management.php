<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

// Get user name and other details

$conn = connect();
$emp_id = $_SESSION['id'];
$user_id = $_GET['id'];

// Fetch all employees for the dropdown
$employee_query = "SELECT id, CONCAT(fname, ' ', lname) AS name FROM hrm_employee ";
$employee_result = mysqli_query($conn, $employee_query) or die(mysqli_error($conn));

$query = "SELECT * FROM hrm_employee WHERE id='$emp_id';";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$profile_image_dir = "upload-image";
$profile_image = $profile_image_dir . "/" . $row['image'];
$employee_detail = "SELECT * FROM hrm_employee WHERE id=$user_id";






$user_detail_array = get_user_detail($emp_id);
$user_roll_array = get_user_roll($emp_id);
$designation = get_value1("hrm_designation", "name", "id", $row['designation_id']);
$department = get_value1("hrm_department", "name", "id", $row['department_id']);

if ($row['role'] != 'admin' && $row['role'] != 'super admin') {
    header("Location: attendance-report-employee.php");
}

// Initialize variables
$message = '';
$data = [
    'id' => '',
    'name' => '',
    'logo' => '',
    'banner' => '',
    'logo_alt_text' => '',
    'banner_alt_text' => '',
    'email' => '',
    'mobile1' => '',
    'mobile2' => '',
    'website' => '',
    'industry' => '',
    'tax_id' => '',
    'linkedin' => '',
    'facebook' => '',
    'twitter' => '',
    'founded_year' => '',
    'employee_count' => '',
    'status' => 'active',
    'description' => '',
    'operating_hours' => '',
    'latitude' => '',
    'longitude' => '', // This will store the iframe code
    'parent_company' => '',
    'additional_contact' => '',
    'address1' => '',
    'address2' => ''
];

// Handle form submission for Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $logo = $_FILES['logo']['name'];
    $banner = $_FILES['banner']['name'];
    $logo_alt_text = $_POST['logo_alt_text'];
    $banner_alt_text = $_POST['banner_alt_text'];
    $email = $_POST['email'];
    $mobile1 = $_POST['mobile1'];
    $mobile2 = $_POST['mobile2'];
    $website = $_POST['website'];
    $industry = $_POST['industry'];
    $tax_id = $_POST['tax_id'];
    $linkedin = $_POST['linkedin'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $founded_year = $_POST['founded_year'];
    $employee_count = $_POST['employee_count'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $operating_hours = $_POST['operating_hours'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude']; // Iframe code
    $parent_company = $_POST['parent_company'];
    $additional_contact = $_POST['additional_contact'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];

    // File uploads
    $target_dir = "uploads/";
    $logo_target_file = $target_dir . basename($_FILES["logo"]["name"]);
    $banner_target_file = $target_dir . basename($_FILES["banner"]["name"]);

    move_uploaded_file($_FILES["logo"]["tmp_name"], $logo_target_file);
    move_uploaded_file($_FILES["banner"]["tmp_name"], $banner_target_file);

    if (isset($_POST['add_company'])) {
        // Insert data into database
        $sql = "INSERT INTO companies (name, logo, banner, logo_alt_text, banner_alt_text, email, mobile1, mobile2, website, industry, tax_id, linkedin, facebook, twitter, founded_year, employee_count, status, description, operating_hours, latitude, longitude, parent_company, additional_contact, address1, address2)
                VALUES ('$name', '$logo', '$banner', '$logo_alt_text', '$banner_alt_text', '$email', '$mobile1', '$mobile2', '$website', '$industry', '$tax_id', '$linkedin', '$facebook', '$twitter', '$founded_year', '$employee_count', '$status', '$description', '$operating_hours', '$latitude', '$longitude', '$parent_company', '$additional_contact', '$address1', '$address2')";

        if ($conn->query($sql) === TRUE) {
            $message = "New company added successfully!";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['update_company'])) {
        $company_id = $_POST['company_id'];
        $update_sql = "UPDATE companies SET 
            name = '$name', logo = '$logo', banner = '$banner', logo_alt_text = '$logo_alt_text', banner_alt_text = '$banner_alt_text', 
            email = '$email', mobile1 = '$mobile1', mobile2 = '$mobile2', website = '$website', industry = '$industry', 
            tax_id = '$tax_id', linkedin = '$linkedin', facebook = '$facebook', twitter = '$twitter', founded_year = '$founded_year', 
            employee_count = '$employee_count', status = '$status', description = '$description', operating_hours = '$operating_hours', 
            latitude = '$latitude', longitude = '$longitude', parent_company = '$parent_company', additional_contact = '$additional_contact', 
            address1 = '$address1', address2 = '$address2' WHERE id = $company_id";

        if ($conn->query($update_sql) === TRUE) {
            $message = "Company updated successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Handle Delete action
if (isset($_GET['delete'])) {
    $company_id = $_GET['delete'];
    $delete_sql = "DELETE FROM companies WHERE id = $company_id";
    if ($conn->query($delete_sql) === TRUE) {
        $message = "Company deleted successfully!";
    } else {
        $message = "Error deleting company: " . $conn->error;
    }
}

// Handle Edit action
if (isset($_GET['edit'])) {
    $company_id = $_GET['edit'];
    $edit_sql = "SELECT * FROM companies WHERE id = $company_id";
    $result = $conn->query($edit_sql);
    $data = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reports - HRMS Admin Template</title>
        <?php include 'layouts/title-meta.php'; ?>

<?php include 'layouts/head-css.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>iframe{height:100px !important}</style>
</head>
<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="page-wrapper">
            <div class="container mt-5 padding-top-ams">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Company Details</li>
                </ul>
                <h2 class="mb-4">Edit - Company Details</h2>

                <?php if ($message) { ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php } ?>

                <?php if (isset($_GET['edit'])) { ?>
                    <h4>Edit Company</h4>
                <?php } else { ?>
                    <h4>Add New Company</h4>
                <?php } ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <?php if (isset($data['id'])) { ?>
                        <input type="hidden" name="company_id" value="<?php echo $data['id']; ?>">
                        <input type="hidden" name="existing_logo" value="<?php echo $data['logo']; ?>">
                        <input type="hidden" name="existing_banner" value="<?php echo $data['banner']; ?>">
                    <?php } ?>

                    <div class="row">
                        <!-- Company Name -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="name">Company Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $data['name']; ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo $data['email']; ?>" required>
                        </div>

                        <!-- Mobile 1 -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="mobile1">Mobile 1</label>
                            <input type="text" class="form-control" name="mobile1" value="<?php echo $data['mobile1']; ?>" required>
                        </div>

                        <!-- Mobile 2 -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="mobile2">Mobile 2</label>
                            <input type="text" class="form-control" name="mobile2" value="<?php echo $data['mobile2']; ?>">
                        </div>

                        <!-- Address Line 1 -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="address1">Address Line 1</label>
                            <input type="text" class="form-control" name="address1" value="<?php echo $data['address1']; ?>" required>
                        </div>

                        <!-- Address Line 2 -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="address2">Address Line 2</label>
                            <input type="text" class="form-control" name="address2" value="<?php echo $data['address2']; ?>" required>
                        </div>

                        <!-- Website -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="website">Website</label>
                            <input type="url" class="form-control" name="website" value="<?php echo $data['website']; ?>">
                        </div>

                        <!-- Industry -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="industry">Industry</label>
                            <input type="text" class="form-control" name="industry" value="<?php echo $data['industry']; ?>">
                        </div>

                        <!-- Tax ID -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="tax_id">Tax ID</label>
                            <input type="text" class="form-control" name="tax_id" value="<?php echo $data['tax_id']; ?>">
                        </div>

                        <!-- LinkedIn -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="linkedin">LinkedIn</label>
                            <input type="url" class="form-control" name="linkedin" value="<?php echo $data['linkedin']; ?>">
                        </div>

                        <!-- Facebook -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="facebook">Facebook</label>
                            <input type="url" class="form-control" name="facebook" value="<?php echo $data['facebook']; ?>">
                        </div>

                        <!-- Twitter -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="twitter">Twitter</label>
                            <input type="url" class="form-control" name="twitter" value="<?php echo $data['twitter']; ?>">
                        </div>

                        <!-- Founded Year -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="founded_year">Founded Year</label>
                            <input type="number" class="form-control" name="founded_year" value="<?php echo $data['founded_year']; ?>">
                        </div>

                        <!-- Employee Count -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="employee_count">Employee Count</label>
                            <input type="number" class="form-control" name="employee_count" value="<?php echo $data['employee_count']; ?>">
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status">
                                <option value="active" <?php echo ($data['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($data['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-12 form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description"><?php echo $data['description']; ?></textarea>
                        </div>

                        <!-- Operating Hours -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="operating_hours">Operating Hours</label>
                            <input type="text" class="form-control" name="operating_hours" value="<?php echo $data['operating_hours']; ?>">
                        </div>

                        <!-- Latitude -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="latitude">Latitude</label>
                            <input type="text" class="form-control" name="latitude" value="<?php echo $data['latitude']; ?>">
                        </div>

                        <!-- Longitude (Replace with Iframe Code) -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="longitude">Google Maps Embed Code</label>
                            <textarea class="form-control" name="longitude" rows="4"><?php echo $data['longitude']; ?></textarea>
                        </div>

                        <!-- Parent Company -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="parent_company">Parent Company</label>
                            <input type="text" class="form-control" name="parent_company" value="<?php echo $data['parent_company']; ?>">
                        </div>

                        <!-- Additional Contact -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="additional_contact">Additional Contact</label>
                            <input type="text" class="form-control" name="additional_contact" value="<?php echo $data['additional_contact']; ?>">
                        </div>

                        <!-- Logo -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="logo">Logo</label>
                            <input type="file" class="form-control" name="logo">
                            <?php if (isset($data['logo'])) { ?>
                                <img src="uploads/<?php echo $data['logo']; ?>" width="100" alt="Logo">
                            <?php } ?>
                        </div>

                        <!-- Banner -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="banner">Banner</label>
                            <input type="file" class="form-control" name="banner">
                            <?php if (isset($data['banner'])) { ?>
                                <img src="uploads/<?php echo $data['banner']; ?>" width="100" alt="Banner">
                            <?php } ?>
                        </div>

                        <!-- Logo Alt Text -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="logo_alt_text">Logo Alt Text</label>
                            <input type="text" class="form-control" name="logo_alt_text" value="<?php echo $data['logo_alt_text']; ?>">
                        </div>

                        <!-- Banner Alt Text -->
                        <div class="col-md-6 col-lg-4 form-group">
                            <label for="banner_alt_text">Banner Alt Text</label>
                            <input type="text" class="form-control" name="banner_alt_text" value="<?php echo $data['banner_alt_text']; ?>">
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary" name="<?php echo isset($data['id']) ? 'update_company' : 'add_company'; ?>">
                                <?php echo isset($data['id']) ? 'Update Company' : 'Add Company'; ?>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Table to display companies -->
                <h3 class="mt-5">Company List</h3>
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile 1</th>
                                <th>Mobile 2</th>
                                <th>Website</th>
                                <th>Industry</th>
                                <th>Tax ID</th>
                                <th>Logo</th>
                                <th>Banner</th>
                                <th>LinkedIn</th>
                                <th>Facebook</th>
                                <th>Twitter</th>
                                <th>Founded Year</th>
                                <th>Employee Count</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Operating Hours</th>
                                <th>Location</th> <!-- Updated from Longitude to Location -->
                                <th>Parent Company</th>
                                <th>Additional Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM companies");
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . $row['name'] . '</td>';
                                echo '<td>' . $row['email'] . '</td>';
                                echo '<td>' . $row['mobile1'] . '</td>';
                                echo '<td>' . $row['mobile2'] . '</td>';
                                echo '<td>' . $row['website'] . '</td>';
                                echo '<td>' . $row['industry'] . '</td>';
                                echo '<td>' . $row['tax_id'] . '</td>';
                                
                                // Display Logo
                                echo '<td>';
                                if ($row['logo']) {
                                    echo '<img src="uploads/' . $row['logo'] . '" width="100" alt="Logo">';
                                } else {
                                    echo 'No Logo';
                                }
                                echo '</td>';
                                
                                // Display Banner
                                echo '<td>';
                                if ($row['banner']) {
                                    echo '<img src="uploads/' . $row['banner'] . '" width="150" alt="Banner">';
                                } else {
                                    echo 'No Banner';
                                }
                                echo '</td>';
                                
                                echo '<td>' . $row['linkedin'] . '</td>';
                                echo '<td>' . $row['facebook'] . '</td>';
                                echo '<td>' . $row['twitter'] . '</td>';
                                echo '<td>' . $row['founded_year'] . '</td>';
                                echo '<td>' . $row['employee_count'] . '</td>';
                                echo '<td>' . $row['status'] . '</td>';
                                echo '<td>' . $row['description'] . '</td>';
                                echo '<td>' . $row['operating_hours'] . '</td>';
                                
                                // Display Google Maps Embed Code
                                echo '<td>';
                                if (!empty($row['longitude'])) {
                                    echo htmlspecialchars_decode($row['longitude']); // Render the iframe code
                                } else {
                                    echo 'No Map Available';
                                }
                                echo '</td>';
                                
                                echo '<td>' . $row['parent_company'] . '</td>';
                                echo '<td>' . $row['additional_contact'] . '</td>';
                                echo '<td>
                                        <a href="?edit=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="?delete=' . $row['id'] . '" class="btn btn-danger btn-sm">Delete</a>
                                      </td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Responsive Cards for Smaller Screens -->
                <div class="row d-block d-md-none">
                    <?php
                    $result = $conn->query("SELECT * FROM companies");
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="col-12 col-sm-6 col-md-4 mb-4">';
                        echo '<div class="card">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $row['name'] . '</h5>';
                        echo '<p class="card-text"><strong>Email:</strong> ' . $row['email'] . '</p>';
                        echo '<p class="card-text"><strong>Mobile 1:</strong> ' . $row['mobile1'] . '</p>';
                        echo '<p class="card-text"><strong>Mobile 2:</strong> ' . $row['mobile2'] . '</p>';
                        echo '<p class="card-text"><strong>Website:</strong> ' . $row['website'] . '</p>';
                        echo '<p class="card-text"><strong>Industry:</strong> ' . $row['industry'] . '</p>';
                        echo '<p class="card-text"><strong>Tax ID:</strong> ' . $row['tax_id'] . '</p>';

                        // Display Logo
                        if ($row['logo']) {
                            echo '<p class="card-text"><strong>Logo:</strong><br><img src="uploads/' . $row['logo'] . '" width="100" alt="Logo"></p>';
                        } else {
                            echo '<p class="card-text"><strong>Logo:</strong> No Logo</p>';
                        }
                        
                        // Display Banner
                        if ($row['banner']) {
                            echo '<p class="card-text"><strong>Banner:</strong><br><img src="uploads/' . $row['banner'] . '" width="150" alt="Banner"></p>';
                        } else {
                            echo '<p class="card-text"><strong>Banner:</strong> No Banner</p>';
                        }

                        echo '<p class="card-text"><strong>LinkedIn:</strong> ' . $row['linkedin'] . '</p>';
                        echo '<p class="card-text"><strong>Facebook:</strong> ' . $row['facebook'] . '</p>';
                        echo '<p class="card-text"><strong>Twitter:</strong> ' . $row['twitter'] . '</p>';
                        echo '<p class="card-text"><strong>Founded Year:</strong> ' . $row['founded_year'] . '</p>';
                        echo '<p class="card-text"><strong>Employee Count:</strong> ' . $row['employee_count'] . '</p>';
                        echo '<p class="card-text"><strong>Status:</strong> ' . $row['status'] . '</p>';
                        echo '<p class="card-text"><strong>Description:</strong> ' . $row['description'] . '</p>';
                        echo '<p class="card-text"><strong>Operating Hours:</strong> ' . $row['operating_hours'] . '</p>';
                        
                        // Display Google Maps Embed Code
                        echo '<p class="card-text"><strong>Location:</strong><br>';
                        if (!empty($row['longitude'])) {
                            echo htmlspecialchars_decode($row['longitude']); // Render the iframe code
                        } else {
                            echo 'No Map Available';
                        }
                        echo '</p>';

                        echo '<p class="card-text"><strong>Parent Company:</strong> ' . $row['parent_company'] . '</p>';
                        echo '<p class="card-text"><strong>Additional Contact:</strong> ' . $row['additional_contact'] . '</p>';
                        
                        echo '<div class="d-flex justify-content-between">';
                        echo '<a href="?edit=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>';
                        echo '<a href="?delete=' . $row['id'] . '" class="btn btn-danger btn-sm">Delete</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

 <?php 
include 'layouts/customizer.php';
 ?>
 <?php 
 include 'layouts/vendor-scripts.php';
 ?>
</body>
</html>