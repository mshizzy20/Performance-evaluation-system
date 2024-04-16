<?php
    // start a new session
    session_start();

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
        $selectSystemAccessSql = 'SELECT `is_cod`, `system_access` FROM `users` WHERE `user_Pf_Number`=?';
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
            $isCodRow = $systemAccessValue['is_cod'];
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
            header("Location: login.php?loginError=loginError01&link=evaluateDpt");
            exit();
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | Evaluate Department members</title>
                <!-- include header.php php file -->
                <?php include 'header.php' ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <div class="wrapper">
                    <!-- include topbar.php php file -->
                    <?php include 'topbar.php' ?>
                    <!-- include sidebar.php php file -->
                    <?php include 'sidebar.php' ?>

                    <div class="content-wrapper">
                        <div class="content-header">
                            <div class="container-fluid">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <h1 class="m-0">Evaluate Department members</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card card-outline card-success">
                                <div class="card-body">
                                    <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" id="departmentMembersList">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
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
                                                $department_id = $_SESSION['department_id'];
                                                // Prepare a SQL statement with a parameter placeholder
                                                $selectAllDepartmentMemnersStmt = $dbConnection->prepare('SELECT * FROM `users` WHERE `department_id`=? AND `user_Pf_Number`!=? ORDER BY `user_Pf_Number` ASC');

                                                // Bind parameters to the statement
                                                $selectAllDepartmentMemnersStmt->bind_param('ss',$department_id,$_SESSION['user_Pf_Number']);

                                                // Execute the statement
                                                $selectAllDepartmentMemnersStmt->execute();

                                                // Retrieve the result set
                                                $selectAllDepartmentMemnersResult = $selectAllDepartmentMemnersStmt->get_result();

                                                // Fetch data
                                                while ($selectAllDepartmentMemnersRow = $selectAllDepartmentMemnersResult->fetch_assoc())
                                                {
                                            ?>
                                                <tr>
                                                    <th class="text-center"><?php echo $count01++; ?></th>
                                                    <td><b><?php echo ucwords($selectAllDepartmentMemnersRow['first_name'].' '.$selectAllDepartmentMemnersRow['middle_name'].' '.$selectAllDepartmentMemnersRow['last_name']) ?></b></td>
                                                    <td>
                                                        <b>
                                                            <a href="mailto:<?php echo $selectAllDepartmentMemnersRow['email']; ?>" class="text-decoration-none text-primary">
                                                                <?php echo $selectAllDepartmentMemnersRow['email']; ?>
                                                            </a>
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <b>
                                                            <?php if ($selectAllDepartmentMemnersRow['user_role'] === 6){ ?>
                                                                Chair of Department
                                                            <?php }elseif ($selectAllDepartmentMemnersRow['user_role'] === 7){ ?>
                                                                Lecturer
                                                            <?php }?>
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <b>
                                                            <?php
                                                                if (empty($selectAllDepartmentMemnersRow['department_id'] == true)){
                                                            ?>
                                                                Department not assigned
                                                            <?php }else{ ?>
                                                                <?php
                                                                    // SQL SELECT statement with parameters
                                                                    $selectDepartmentDetailsSQL = 'SELECT `department_id`, `department_name` FROM `departments` WHERE `department_id`=?';
                                                                    // Prepare the resulting SQL statement query
                                                                    $selectDepartmentDetailsSTMT = $dbConnection->prepare($selectDepartmentDetailsSQL);
                                                                    // Bind all variables to the prepared SQL statement
                                                                    $selectDepartmentDetailsSTMT->bind_param('s', $selectAllDepartmentMemnersRow['department_id']);
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
                                                        <div class="btn-group">
                                                            <?php
                                                                // SQL select statement
                                                                $sql = 'SELECT `pf_being_evaluated` FROM `staff_competency` WHERE `pf_being_evaluated`=? AND `cod_pf`=?';
                                                                // Prepare the SQL statement with a parameter placeholder
                                                                $stmt = $dbConnection->prepare($sql);
                                                                // Bind parameters to the statement
                                                                $stmt->bind_param('ss', $selectAllDepartmentMemnersRow['user_Pf_Number'],$_SESSION['user_Pf_Number']);
                                                                // Execute the statement
                                                                $stmt->execute();
                                                                // Retrieve the result set
                                                                $stmt->store_result();
                                                                if($stmt->num_rows===13)
                                                                {
                                                            ?>
                                                            <a href="evaluateDptStf?pf=<?php echo $selectAllDepartmentMemnersRow['user_Pf_Number'] ?>" class="btn btn-success btn-sm rounded-pill" style="width:90px;" name="evaluated" id="evaluated">Evaluated</a>
                                                            <?php }else{ ?>
                                                                <a href="evaluateDptStf?pf=<?php echo $selectAllDepartmentMemnersRow['user_Pf_Number'] ?>" class="btn btn-primary btn-sm rounded-pill" style="width:90px;" name="evaluated" id="evaluated">Evaluate</a>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- include mainfooter.php php file -->
                    <?php include 'mainfooter.php' ?>
                </div>
                <!-- include footer.php php file -->
                <?php include 'footer.php' ?>
                <script>
                    $(document).ready( function ()
                    {
                        $('#departmentMembersList').DataTable();
                    } );
                </script>
            </body>
        </html>
<?php } ?>