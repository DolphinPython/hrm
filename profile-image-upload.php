<?php 
include "include/function.php";
$conn = connect();
$ids = uniqid();

$emp_id_for_image = $_POST['emp_id_for_image'];
if (is_array($_FILES)) {
    if (is_uploaded_file($_FILES['img1']['tmp_name'])) {
        $sourcePath = $_FILES['img1']['tmp_name'];
        $targetPath = "upload-image/" . $ids.str_replace(" ","-",$_FILES['img1']['name']);
        $targetPath1 = $ids.str_replace(" ","-",$_FILES['img1']['name']);
        if (move_uploaded_file($sourcePath, $targetPath)) {
            ?>
<img src="<?php echo $targetPath; ?>"
	class="img-circle elevation-2" height="100" width="100" />
<?php
        }
    }
    // update in table
    $query = "update hrm_employee set image='$targetPath1' where  
    id = '$emp_id_for_image';";
    //echo $query;
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn, "hrm_employee update error")); 
    //update social detail
    // update in table
}
?>