<?php 

include 'layouts/session.php';
include 'layouts/head-main.php';
include 'include/function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $conn = connect();

   
    $emp_id = intval($_SESSION['id']);
    
   
    $old_password     = trim($_POST['old_password']);
    $new_password     = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

   
    if ($new_password !== $confirm_password) {
        $_SESSION['password_msg'] = "New passwords do not match!";
        $_SESSION['msg_type']   = "danger";
        header("Location: change-password.php");
        exit();
    }

   
    $stmt = $conn->prepare("SELECT password FROM hrm_employee WHERE id = ?");
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();

    if (!$user) {
        $_SESSION['password_msg'] = "User not found!";
        $_SESSION['msg_type']   = "danger";
        header("Location: change-password.php");
        exit();
    }

  
    if ($old_password !== $user['password']) {
        $_SESSION['password_msg'] = "Old password is incorrect!";
        $_SESSION['msg_type']   = "danger";
        header("Location: change-password.php");
        exit();
    }

  
    $update_stmt = $conn->prepare("UPDATE hrm_employee SET password = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_password, $emp_id);

   
    if ($update_stmt->execute()) {
        $_SESSION['password_msg'] = "Password updated successfully!";
        $_SESSION['msg_type']   = "success";
        header("Location: index.php"); // Redirect to index.php
        exit();
    } else {
        $_SESSION['password_msg'] = "Error updating password!";
        $_SESSION['msg_type']   = "danger";
        header("Location: change-password.php");
        exit();
    }
    // Redirect to avoid form re-submission
    // header("Location: change-password.php");
    // exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password - HRMS admin template</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>

<?php include 'layouts/body.php'; ?>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="account-content">
            <!-- Account Logo -->
            <div class="account-logo">
                <a href="admin-dashboard.php">
                    <img src="assets/img/logo2.png" alt="Dreamguy's Technologies">
                </a>
            </div>
            <div class="account-box">
                <div class="account-wrapper">
                    <h3 class="account-title">Change Password</h3>
                    
                    <!-- Display session message if exists -->
                    <?php if (isset($_SESSION['password_msg'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?>">
                            <?php 
                                echo $_SESSION['password_msg'];
                                unset($_SESSION['password_msg']);
                                unset($_SESSION['msg_type']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Password Change Form -->
                    <form action="change-password.php" method="post">
    <div class="form-group">
        <label>Old password</label>
        <div class="input-group">
            <input type="password" class="form-control" name="old_password" id="old_password" required>
            <div class="input-group-append">
                <span class="input-group-text" onclick="togglePassword('old_password', 'eye_old')">
                    <i id="eye_old" class="fa fa-eye"></i>
                </span>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label>New password</label>
        <div class="input-group">
            <input type="password" class="form-control" name="new_password" id="new_password" required
                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                   title="Must contain at least 8 characters, including uppercase, lowercase and number">
            <div class="input-group-append">
                <span class="input-group-text" onclick="togglePassword('new_password', 'eye_new')">
                    <i id="eye_new" class="fa fa-eye"></i>
                </span>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label>Confirm password</label>
        <div class="input-group">
            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
            <div class="input-group-append">
                <span class="input-group-text" onclick="togglePassword('confirm_password', 'eye_confirm')">
                    <i id="eye_confirm" class="fa fa-eye"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="submit-section mb-4">
        <button type="submit" class="btn btn-primary submit-btn">Update Password</button>
    </div>
</form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->

    <?php include 'layouts/vendor-scripts.php'; ?>
    <script>
    function togglePassword(inputId, eyeId) {
        let input = document.getElementById(inputId);
        let eyeIcon = document.getElementById(eyeId);

        if (input.type === "password") {
            input.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }
</script>
</body>
</html>
