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
  <title>POSH Policy - Expetize Pvt Ltd</title>
  <?php include 'layouts/title-meta.php'; ?>
  <?php include 'layouts/head-css.php'; ?>
  <style>
 
 .main-wrapper {
    background: #ffffff !important;
}
  .container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 20px 20px;
    /* max-width: 960px; */
    margin: 0 auto;
    background-color: #fff !important;
  }
  .policy-title {
    font-size: 2.2rem;
    font-weight: bold;
    color: #343a40;
    text-align: center;
    margin-bottom: 40px;
  }
  .policy-section {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
    margin-bottom: 30px;
  }
  .policy-section h5 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #0056b3;
  }
  ul {
    padding-left: 20px;
  }
  ul li {
    margin-bottom: 8px;
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
                            <h3 class="page-title">POSH POLICY</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">POSH POLICY</li>
                            </ul>
                        </div>
                    </div>
                </div>

      <div class="container">
        <div class="policy-title">POSH POLICY - Expetize Private Limited (1Solutions.biz)</div>

        <div class="policy-section">
          <h5>1. Policy Statement</h5>
          <p>At Expetize Private Limited, operating under the brand 1Solutions.biz, we are committed to providing a safe, secure, and respectful work environment free from sexual harassment. We have zero tolerance for any form of sexual harassment and affirm our obligation to ensure compliance with the Sexual Harassment of Women at Workplace (Prevention, Prohibition and Redressal) Act, 2013.</p>
        </div>

        <div class="policy-section">
          <h5>2. Objective</h5>
          <ul>
            <li>Prevent sexual harassment at the workplace.</li>
            <li>Provide a mechanism for the redressal of complaints.</li>
            <li>Promote a safe and inclusive working environment.</li>
          </ul>
        </div>

        <div class="policy-section">
          <h5>3. Scope</h5>
          <p>This policy is applicable to all employees of Expetize Private Limited, including but not limited to:</p>
          <ul>
            <li>Full-time, part-time, temporary, and contractual staff</li>
            <li>Interns and trainees</li>
            <li>Consultants and vendors</li>
            <li>Remote or off-site employees</li>
          </ul>
          <p>It covers any location considered a workplace, including company offices, client locations, work-related travel, virtual meetings, and company-sponsored events.</p>
        </div>

        <div class="policy-section">
          <h5>4. Definition of Sexual Harassment</h5>
          <p>As per the Act, sexual harassment includes any unwelcome act or behavior (whether directly or by implication), such as:</p>
          <ul>
            <li>Physical contact and advances</li>
            <li>A demand or request for sexual favors</li>
            <li>Making sexually colored remarks</li>
            <li>Showing pornography</li>
            <li>Any other unwelcome physical, verbal, or non-verbal conduct of a sexual nature</li>
          </ul>
        </div>

        <div class="policy-section">
          <h5>5. Internal Complaints Committee (ICC)</h5>
          <p>The company has constituted an Internal Complaints Committee (ICC) comprising:</p>
          <ul>
            <li>Presiding Officer: A senior woman employee</li>
            <li>Members: Two employees committed to the cause of women</li>
            <li>External Member: A third-party NGO or expert familiar with issues related to sexual harassment</li>
          </ul>
          <p>The names and contact details of ICC members will be displayed on internal communication channels.</p>
        </div>

        <div class="policy-section">
          <h5>6. Complaint Procedure</h5>
          <ul>
            <li>A complaint should be made in writing within 3 months of the incident.</li>
            <li>The complaint can be submitted to the ICC via email or in person.</li>
            <li>Confidentiality of the complainant and the proceedings will be maintained at all stages.</li>
          </ul>
        </div>

        <div class="policy-section">
          <h5>7. Redressal Process</h5>
          <p>The ICC will conduct a fair and unbiased inquiry within 90 days. Both parties will be given a chance to be heard. If sexual harassment is proven, appropriate disciplinary action will be taken, which may include:</p>
          <ul>
            <li>Written apology</li>
            <li>Warning</li>
            <li>Suspension</li>
            <li>Termination of employment</li>
          </ul>
        </div>

        <div class="policy-section">
          <h5>8. False Complaints</h5>
          <p>While the company encourages employees to speak up against misconduct, malicious or false complaints will be taken seriously and may attract disciplinary action.</p>
        </div>

        <div class="policy-section">
          <h5>9. Awareness & Training</h5>
          <ul>
            <li>Conduct regular training and awareness programs for employees.</li>
            <li>Display this policy at conspicuous places in the office.</li>
            <li>Sensitize new hires during onboarding.</li>
          </ul>
        </div>

        <div class="policy-section">
          <h5>10. Confidentiality</h5>
          <p>All complaints, identities of parties involved, and proceedings will be kept confidential and disclosed only to the extent necessary for investigation and resolution.</p>
        </div>

        <div class="policy-section">
          <h5>11. Policy Review</h5>
          <p>This policy will be reviewed annually or as required to ensure compliance with applicable laws and evolving best practices.</p>
        </div>

        <div class="policy-section">
          <h5>Contact Information</h5>
          <p>For any complaints or further information, employees are encouraged to contact the ICC at:</p>
          <p><strong>Email:</strong> hr@1solutions.biz</p>
        </div>

      </div>
    </div>
  </div>
</div>
<?php include 'layouts/customizer.php'; ?>
<?php include 'layouts/vendor-scripts.php'; ?>
</body>
</html>
