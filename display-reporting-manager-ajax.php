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
                                                <th>Reporting Manager</th>
                                                <th>Type</th>
                                                <th>Department</th>
                                                <th>Designation</th>
                                                
                                                

                                            </tr>
                                        </thead>
                                        <tbody>

                                        <?php 
        
        //$c=0;$c1="";
        //if(isset($_GET['id']))
        //{
          $query_reporting_manager="select * from hrm_reporting_manager where employee_id='$emp_id';";
          //echo $query_education;
          $result_reporting_manager=mysqli_query($conn, $query_reporting_manager) or die(mysqli_error($conn));
          $x=""; $c=0;
          while($row_reporting_manager=mysqli_fetch_array($result_reporting_manager))
          {
              //$c++;
              //$c1=$row_employee_education['id'];
          ?>            
            <tr>  
              <td><?php 
              echo get_value("hrm_employee", "fname", $row_reporting_manager['reporting_manager_id']);
               ?></td> 
    
               
    
              <td><?php              
             echo $row_reporting_manager['reporting_manager_type'];
              ?></td>

              
              <td><?php                             
              $designation_id = get_value("hrm_employee", "designation_id", $row_reporting_manager['reporting_manager_id']);
              echo get_value("hrm_designation", "name", $designation_id);              
               ?></td>    

              <td><?php                             
              $department_id = get_value("hrm_employee", "department_id", $row_reporting_manager['reporting_manager_id']);
              echo get_value("hrm_department", "name", $department_id);              
               ?></td>
    
              

            </tr>    
          <?php } ?>
                                            
                                        </tbody>
                                    </table>
