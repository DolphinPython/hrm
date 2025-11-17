<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php include 'include/function.php';

$emp_id = $_SESSION['id'];
$conn = connect();
$query = "SELECT * FROM hrm_employee WHERE id='$emp_id'";
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Internal Complaints Committee</title>
  <?php include 'layouts/title-meta.php'; ?>
  <?php include 'layouts/head-css.php'; ?>
  <style>
    .main-wrapper {
    background: #ffffff !important;
}

    .icc-wrapper {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
      box-sizing: border-box;
      background-color: #ffffff;
    }

    .icc-content {
      max-width: 900px;
      width: 100%;
      background-color: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
      text-align: left;
    }

    .section-title {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 25px;
      color: #333;
      text-align: center;
    }

    .info-text {
      font-size: 18px;
      margin-bottom: 25px;
      color: #555;
      line-height: 1.8;
    }

    ul.icc-members {
      list-style-type: disc;
      padding-left: 25px;
    }

    ul.icc-members li {
      margin-bottom: 15px;
      font-size: 18px;
    }

    ul.icc-members a {
      color: #0d6efd;
      text-decoration: none;
    }

    ul.icc-members a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="page-wrapper">
    <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Internal Complaints Committee (ICC)</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Internal Complaints Committee (ICC)</li>
                            </ul>
                        </div>
                    </div>
                </div>
      <div class="icc-wrapper">
        <div class="icc-content">
          <h2 class="section-title">Internal Complaints Committee (ICC)</h2>
          <p class="info-text">
            The company has constituted an Internal Complaints Committee (ICC) in accordance with the Sexual Harassment of Women at Workplace Act. The current members are:
          </p>
          <ul class="icc-members">
            <li><strong>Presiding Officer:</strong> Kanu Paul (+91 96502 00202) – <a href="mailto:hr@1solutions.biz">hr@1solutions.biz</a></li>
            <li><strong>Member:</strong> Ritika Rajan (+91 88260 10745) – <a href="mailto:dolphinpython@outlook.com">dolphinpython@outlook.com</a></li>
            <li><strong>Member:</strong> Prem Rai (+91 88515 54402) – <a href="mailto:pythondolphin@gmail.com">pythondolphin@gmail.com</a></li>
            <li><strong>External Member:</strong> Dr. Nidhi Singh – Faculty, MDI</li>
          </ul>
          <p class="info-text">
            The ICC is responsible for receiving, investigating, and redressing complaints related to sexual harassment in a fair, confidential, and timely manner.
          </p>
        </div>
      </div>
      </div>
    </div>
  </div>

  <?php include 'layouts/customizer.php'; ?>
  <?php include 'layouts/vendor-scripts.php'; ?>
</body>
</html>
