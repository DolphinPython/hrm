<?php 
include "include/function.php";
//include "include/db.php";  
//check_login();
//global $fname;
//echo "aaaaaaaa".$fname;
$conn = connect();
//echo "conn=====".print_r($conn);
$emp_id = $_GET['emp_id'];

//echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
?>
<table class="table custom-table table-nowrap mb-0 table-border" id="dynamic_field_education">
                                        <thead>
                                            <tr>
                                                <th>Qualification Type</th>
                                                <th>Course Name</th>
                                                <th>Course Type</th>
                                                <th>Stream</th>
                                                <th>Course Start Date</th>
                                                <th>Course End Date</th>
                                                <th>College Name</th>
                                                <th>Univeristy Name</th>
                                                <th>Grade</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>

                                        <?php 
        //$c=0;$c1="";
        //if(isset($_GET['id']))
        //{
          $query_education="select * from hrm_employee_education where emp_id='$emp_id';";
          //echo $query_education;
          $result_education=mysqli_query($conn, $query_education) or die(mysqli_error($conn));
          $x=""; $c=0;
          while($row_employee_education=mysqli_fetch_array($result_education))
          {
              //$c++;
              //$c1=$row_employee_education['id'];
          ?>            
            <tr>  
              <td><?php 
              echo get_value("hrm_qualification_type", "name", $row_employee_education['qualification_type']);
              //echo $row_employee_education['qualification_type']; ?></td> 
    
              <td><?php echo $row_employee_education['course_name']; ?></td> 
    
              <td><?php //echo $row_employee_education['course_type'];
              
              echo get_value("hrm_course_type", "name", $row_employee_education['course_type']);
              ?></td>

              <td><?php echo $row_employee_education['stream']; ?></td>    

              <td><?php echo $row_employee_education['start_date']; ?></td>
    
              <td><?php echo $row_employee_education['end_date']; ?></td>

              <td><?php echo $row_employee_education['college_name']; ?></td>     

              <td><?php echo $row_employee_education['university_name']; ?></td>

              <td><?php echo $row_employee_education['grade']; ?></td>

            </tr>    
          <?php } ?>
                                            
                                        </tbody>
                                    </table>
