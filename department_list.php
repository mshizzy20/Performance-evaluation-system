<?php
    // Check if session is not started
    if (session_status() === PHP_SESSION_NONE)
    {
        /* start a new session */
        session_start();
    }

    if
    // $_SESSION['user_Pf_Number'] is not set, redirect to login.php
    (isset($_SESSION['user_Pf_Number']) == false)
    {
        // start session
        session_start();
        // unset session
        session_unset();
        // destroy session
        /* ...redirect user to index.php web page... */
        header('Location:login.php');
        exit();
    }
    else
    {
        // require connection to the database
        require 'databaseConnection.php';
        // require connection to the database
        require 'databaseCurrentTime.php';

        // SQL select statement
        $selectSystemAccessSql = 'SELECT `system_access` FROM `users` WHERE `user_Pf_Number`=?';

        // Prepare the SQL statement with a parameter placeholder
        $selectSystemAccessStmt = $dbConnection->prepare($selectSystemAccessSql);

        // Bind parameters to the statement
        $selectSystemAccessStmt->bind_param('s', $_SESSION['user_Pf_Number']);

        // Execute the statement
        $selectSystemAccessStmt->execute();

        // Retrieve the result set
        $selectSystemAccessResult = $selectSystemAccessStmt->get_result();

        // Fetch data
        while ($systemAccessValue = $selectSystemAccessResult->fetch_assoc())
        {
            $systemAccessValueRow = $systemAccessValue['system_access'];
        }
        // if is not set to integer 5, redirect to login.php showing "Please contact the administrator for assistence" error message and exit
        if ($systemAccessValueRow !== 5)
        {
            // start session
            session_start();
            // unset session
            session_unset();
            // destroy session
            session_destroy();
            header("Location: login.php?loginError=loginError01&link=department_list");
            exit();
        }
        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'addDepartment' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addDepartment']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            // Escape the user input before using it in an SQL query
            $secureDepartmentName = mysqli_real_escape_string($dbConnection, $_POST['departmentName']);
            $secureSchool = mysqli_real_escape_string($dbConnection, $_POST['school']);
            $secureEvaluator = mysqli_real_escape_string($dbConnection, $_POST['evaluator']);

            // If  $secureDepartmentName is empty, redirect to add_department.php showing "Please ensure you enter department name" error message and exit
            if (empty($secureDepartmentName) == true)
            {
                header("Location: add_department.php?formError=emptyDepartmentName");
                exit();
            }
            // else if  $secureSchool is empty, redirect to add_department.php showing "Please ensure you enter school" error message and exit
            elseif (empty($secureSchool) == true)
            {
                header("Location: add_department.php?formError=emptySchool");
                exit();
            }
            // else if  $secureEvaluator is empty, redirect to add_department.php showing "Please ensure you enterevaluator" error message and exit
            elseif (empty($secureEvaluator) == true)
            {
                header("Location: add_department.php?formError=emptyEvaluator");
                exit();
            }
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | Departments</title>
                <!-- include header.php php file -->
                <?php include 'header.php' ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <div class="wrapper">
                    <!-- include topbar.php php file -->
                    <?php include 'topbar.php' ?>
                    <!-- include sidebar.php php file -->
                    <?php include 'sidebar.php' ?>

                    <!-- Content Wrapper. Contains page content -->
                    <div class="content-wrapper">
                        <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-body text-white"></div>
                        </div>
                        <div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>

                        <div class="content-header">
                            <div class="container-fluid">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <h1 class="m-0">Departments</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <div>
                            
                            <?php
                                if (isset($_SESSION['session_new_department_added'])==true && isset($_SESSION['session_new_departmentName'])==true && empty($_SESSION['session_new_department_added'])==false && empty($_SESSION['session_new_departmentName'])==false && $_SESSION['session_new_department_added']=='success'){
                            ?>
                                <div class="col-sm-6 col-md-4 col-lg-4 mx-auto">
                                    <div class="alert alert-info text-center" role="alert">
                                        <b><?php echo $_SESSION['session_new_departmentName']; ?></b> department has been added successfully!
                                        <?php $_SESSION['session_new_department_added']=''; $_SESSION['session_new_departmentName']='';?>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php
                                // Prepare a SQL statement with a parameter placeholder
                                $countStaffStmt = $dbConnection->prepare('SELECT COUNT(department_id) AS `departmentIdCount` FROM `departments`');

                                // Execute the statement
                                $countStaffStmt->execute();

                                // Retrieve the result set
                                $result = $countStaffStmt->get_result();

                                // Fetch data
                                while ($countResultRow = $result->fetch_assoc())
                                {
                                    $actualDepartmentIdCount = $countResultRow['departmentIdCount'];
                                    
                                    if ($actualDepartmentIdCount < 1)
                                    {
                            ?>
                                        <div class="col-sm-6 col-md-4 col-lg-4 mx-auto">
                                            <div class="alert alert-info text-center" role="alert">
                                                There are no departments currently!
                                                <br><br>
                                                Would you like to add one?
                                                <br><br>
                                                <a class="text-decoration-none btn btn-sm btn-primary btn-block" href="new_department.php"><i class="fa fa-plus"></i> Add new department</a>
                                            </div>
                                        </div>
                                    <?php }else{ ?>
                                        <div class="col-lg-12">
                                            <div class="card card-outline card-success">
                                                <div class="card-header">
                                                    <div class="card-tools">
                                                        <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="new_department.php"><i class="fa fa-plus"></i> Add new department</a>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-hover table-striped table-bordered table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" id="departmentListDataTables">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">#</th>
                                                                <th>Department id</th>
                                                                <th>Department name</th>
                                                                <th>school</th>
                                                                <th>COD</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                // initial number
                                                                $count01 = 1;
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $selectAllDepartmentsStmt = $dbConnection->prepare('SELECT * FROM `departments` ORDER BY `department_id` ASC');

                                                                // Execute the statement
                                                                $selectAllDepartmentsStmt->execute();

                                                                // Retrieve the result set
                                                                $selectAllDepartmentsResult = $selectAllDepartmentsStmt->get_result();

                                                                // Fetch data
                                                                while ($selectAllDepartmentsRow = $selectAllDepartmentsResult->fetch_assoc())
                                                                {
                                                            ?>
                                                                    <tr>
                                                                        <th class="text-center"><?php echo $count01++ ?></th>
                                                                        <td><b><?php echo $selectAllDepartmentsRow['department_id']; ?></b></td>
                                                                        <td><b><?php echo $selectAllDepartmentsRow['department_name']; ?></b></td>
                                                                        <td>
                                                                            <b>
                                                                                <?php
                                                                                    // SQL SELECT statement with parameters
                                                                                    $selectSchoolDetailsSQL = 'SELECT `school_id`, `school_name` FROM `schools` WHERE `school_id`=?';
                                                                                    // Prepare the resulting SQL statement query
                                                                                    $selectSchoolDetailsSTMT = $dbConnection->prepare($selectSchoolDetailsSQL);
                                                                                    // Bind all variables to the prepared SQL statement
                                                                                    $selectSchoolDetailsSTMT->bind_param('s', $selectAllDepartmentsRow['school']);
                                                                                    if
                                                                                    // ... the SQL statement is executed, ...
                                                                                    ($selectSchoolDetailsSTMT->execute())
                                                                                    {
                                                                                        // Get the mysqli result variable from the SQL statement
                                                                                        $selectSchoolDetailsSTMTResult = $selectSchoolDetailsSTMT->get_result();
                                                                                        // Fetch data
                                                                                        while ($selectSchoolDetailsSTMTRow = $selectSchoolDetailsSTMTResult->fetch_assoc())
                                                                                        {
                                                                                            echo'('.$selectSchoolDetailsSTMTRow["school_id"].') '.$selectSchoolDetailsSTMTRow["school_name"];
                                                                                        }
                                                                                    }
                                                                                ?>
                                                                            </b>
                                                                        </td>
                                                                        <td class="col-2">
                                                                            <b>
                                                                                <?php
                                                                                    // if the $selectAllDepartmentsRow['cod_Pf_Number'] is empty, show 'Not assigned'
                                                                                    if (empty($selectAllDepartmentsRow['cod_Pf_Number'] == true) || $selectAllDepartmentsRow['cod_Pf_Number'] == 'notSet'){
                                                                                ?>
                                                                                    Not assigned
                                                                                <?php }else{ ?>
                                                                                    <?php
                                                                                        // SQL SELECT statement with parameters
                                                                                        $selectCodDetailsSQL = 'SELECT `user_Pf_Number`, `first_name`, `last_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                                                        // Prepare the resulting SQL statement query
                                                                                        $selectCodDetailsSTMT = $dbConnection->prepare($selectCodDetailsSQL);
                                                                                        // Bind all variables to the prepared SQL statement
                                                                                        $selectCodDetailsSTMT->bind_param('s', $selectAllDepartmentsRow['cod_Pf_Number']);
                                                                                        if
                                                                                        // ... the SQL statement is executed, ...
                                                                                        ($selectCodDetailsSTMT->execute())
                                                                                        {
                                                                                            // Get the mysqli result variable from the SQL statement
                                                                                            $selectCodDetailsSTMTResult = $selectCodDetailsSTMT->get_result();
                                                                                            // Fetch data
                                                                                            while ($selectCodDetailsSTMTRow = $selectCodDetailsSTMTResult->fetch_assoc())
                                                                                            {
                                                                                                echo'('.$selectCodDetailsSTMTRow["user_Pf_Number"].') '.$selectCodDetailsSTMTRow["first_name"].' '.$selectCodDetailsSTMTRow["last_name"];
                                                                                            }
                                                                                        }
                                                                                    ?>
                                                                                <?php }?>
                                                                            </b>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                                                                Action
                                                                            </button>
                                                                            <div class="dropdown-menu">
                                                                                <a class="dropdown-item" href="department_details.php?dptId=<?php echo $selectAllDepartmentsRow['department_id']; ?>">
                                                                                    View
                                                                                </a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a class="dropdown-item">
                                                                                    Edit
                                                                                </a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a class="dropdown-item">
                                                                                    Delete
                                                                                </a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                        </div>
                    </div>
                    <!-- Main Footer -->
                    <!-- include mainfooter.php php file -->
                    <?php include 'mainfooter.php' ?>
                </div>
                <!-- include footer.php php file -->
                <?php include 'footer.php' ?>
                <script>
                    $(document).ready( function ()
                    {
                        $('#departmentListDataTables').DataTable();
                    } );
                </script>
            </body>
        </html>
<?php } ?>