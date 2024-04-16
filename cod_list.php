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
            header("Location: login.php?loginError=loginError01&link=cod_list");
            exit();
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | COD list</title>
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
                                        <h1 class="m-0">
                                            COD list
                                        </h1>
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
                                $user_role = 6;
                                // Prepare a SQL statement with a parameter placeholder
                                $countStaffStmt = $dbConnection->prepare('SELECT COUNT(user_Pf_Number) AS `userPfNumberCount` FROM `users` WHERE `user_role`=?');

                                // Bind parameters to the statement
                                $countStaffStmt->bind_param('i',$user_role);

                                // Execute the statement
                                $countStaffStmt->execute();

                                // Retrieve the result set
                                $result = $countStaffStmt->get_result();

                                // Fetch data
                                while ($countResultRow = $result->fetch_assoc())
                                {
                                    $actalUserIdCount = $countResultRow['userPfNumberCount'];
                                    
                                    if ($actalUserIdCount < 1)
                                    {
                            ?>
                                        <div class="col-sm-6 col-md-4 col-lg-4 mx-auto">
                                            <div class="alert alert-info text-center" role="alert">
                                                There is no COD currently!
                                                <br><br>
                                                Would you like to add one?
                                                <br><br>
                                                <a class="text-decoration-none btn btn-sm btn-primary btn-block" href="new_employee.php"><i class="fa fa-plus"></i> Add new COD</a>
                                            </div>
                                        </div>
                                    <?php }else{ ?>
                                        <div class="col-lg-12">
                                            <div class="card card-outline card-success">
                                                <div class="card-header">
                                                    <div class="card-tools">
                                                        <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="new_employee.php"><i class="fa fa-plus"></i> Add new COD</a>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table tabe-hover table-bordered" id="codListDataTables">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">#</th>
                                                                <th>PF number</th>
                                                                <th>Name</th>
                                                                <th>Email</th>
                                                                <th>Role</th>
                                                                <th>Department</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                // initial number
                                                                $count01 = 1;
                                                                $user_role = 6;
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $selectAllStaffStmt = $dbConnection->prepare('SELECT * FROM `users` WHERE `user_role`=? ORDER BY `user_Pf_Number` ASC');

                                                                // Bind parameters to the statement
                                                                $selectAllStaffStmt->bind_param('i',$user_role);

                                                                // Execute the statement
                                                                $selectAllStaffStmt->execute();

                                                                // Retrieve the result set
                                                                $selectAllStaffResult = $selectAllStaffStmt->get_result();

                                                                // Fetch data
                                                                while ($selectAllStaffRow = $selectAllStaffResult->fetch_assoc())
                                                                {
                                                            ?>
                                                                    <tr>
                                                                        <th class="text-center"><?php echo $count01++ ?></th>
                                                                        <th><?php echo $selectAllStaffRow['user_Pf_Number']; ?></th>
                                                                        <td><b><?php echo ucwords($selectAllStaffRow['first_name'].' '.$selectAllStaffRow['middle_name'].' '.$selectAllStaffRow['last_name']) ?></b></td>
                                                                        <td>
                                                                            <b>
                                                                                <a href="mailto:<?php echo $selectAllStaffRow['email']; ?>" class="text-decoration-none text-primary">
                                                                                    <?php echo $selectAllStaffRow['email']; ?>
                                                                                </a>
                                                                            </b>
                                                                        </td>
                                                                        <td>
                                                                            <b>
                                                                                <?php if ($selectAllStaffRow['user_role'] === 6){ ?>
                                                                                    Chair of Department
                                                                                <?php }elseif ($selectAllStaffRow['user_role'] === 7){ ?>
                                                                                    Lecturer
                                                                                <?php }?>
                                                                            </b>
                                                                        </td>
                                                                        <td>
                                                                            <b>
                                                                                <?php
                                                                                    // if the $selectAllStaffRow['department_id'] is empty, show 'Not assigned'
                                                                                    if (empty($selectAllStaffRow['department_id'] == true)){
                                                                                ?>
                                                                                    Department not assigned
                                                                                <?php }else{ ?>
                                                                                    <?php
                                                                                        // SQL SELECT statement with parameters
                                                                                        $selectDepartmentDetailsSQL = 'SELECT `department_id`, `department_name` FROM `departments` WHERE `department_id`=?';
                                                                                        // Prepare the resulting SQL statement query
                                                                                        $selectDepartmentDetailsSTMT = $dbConnection->prepare($selectDepartmentDetailsSQL);
                                                                                        // Bind all variables to the prepared SQL statement
                                                                                        $selectDepartmentDetailsSTMT->bind_param('s', $selectAllStaffRow['department_id']);
                                                                                        if
                                                                                        // ... the SQL statement is executed, ...
                                                                                        ($selectDepartmentDetailsSTMT->execute())
                                                                                        {
                                                                                            // Get the mysqli result variable from the SQL statement
                                                                                            $selectDepartmentDetailsSTMTResult = $selectDepartmentDetailsSTMT->get_result();
                                                                                            // Fetch data
                                                                                            while ($selectDepartmentDetailsSTMTRow = $selectDepartmentDetailsSTMTResult->fetch_assoc())
                                                                                            {
                                                                                                echo'('.$selectDepartmentDetailsSTMTRow["department_id"].') '.$selectDepartmentDetailsSTMTRow["department_name"];
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
                                                                                <a class="dropdown-item" href="employee_details.php?staffId=<?php echo $selectAllStaffRow['user_Pf_Number']; ?>">
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
                        $('#codListDataTables').DataTable();
                    } );
                </script>
            </body>
        </html>
<?php } ?>