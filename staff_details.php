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
    // else if $_GET['staffId'] is not set or is empty, redirect to department_members.php
    elseif (isset($_GET['staffId']) == false || empty($_GET['staffId'])==true)
    {
        header('Location: department_members.php');
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
            header("Location: login.php?loginError=loginError01&link=staff_details");
            exit();
        }
        else
        {
            // Prepare a SQL statement with a parameter placeholder
            $staffDetailsStmt = $dbConnection->prepare('SELECT `user_Pf_Number`, `title`, `first_name`, `middle_name`, `last_name`, `email`, `department_id`,`avatar`, `date_added`, `system_access`, `last_login`, `last_seen` FROM users WHERE user_Pf_Number=?');

            // Bind parameters to the statement
            $staffDetailsStmt->bind_param('s', $_GET['staffId']);

            // Execute the statement
            $staffDetailsStmt->execute();

            // Retrieve the result set
            $staffDetailsResult = $staffDetailsStmt->get_result();

            // Fetch data
            while ($staffDetail = $staffDetailsResult->fetch_assoc())
            {
                $Fetched_user_Pf_Number = $staffDetail['user_Pf_Number'];
                $Fetched_title = $staffDetail['title'];
                $Fetched_first_name = $staffDetail['first_name'];
                $Fetched_middle_name = $staffDetail['middle_name'];
                $Fetched_last_name = $staffDetail['last_name'];
                $Fetched_email = $staffDetail['email'];
                $Fetched_department_id = $staffDetail['department_id'];
                $Fetched_avatar = $staffDetail['avatar'];
                $Fetched_date_added = $staffDetail['date_added'];
                $Fetched_system_access = $staffDetail['system_access'];
                $Fetched_last_login = $staffDetail['last_login'];
                $Fetched_last_seen = $staffDetail['last_seen'];
            }
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | Details for <?php echo $Fetched_first_name; ?></title>
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
                                        <h1 class="m-0">Details for <?php echo $Fetched_first_name.' '.$Fetched_middle_name.' '.$Fetched_last_name; ?></h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4 col-lg-4 mx-auto">
                            <div class="container-fluid">
                                <div class="card card-widget widget-user shadow">
                                    <div class="widget-user-header bg-dark">
                                        <h3 class="widget-user-username"><?php echo $Fetched_first_name.' '.$Fetched_middle_name.' '.$Fetched_last_name; ?></h3>
                                        <h5 class="widget-user-desc"><?php echo $Fetched_email ?></h5>
                                    </div>
                                    <div class="widget-user-image">
                                        <?php
                                            if (empty($Fetched_avatar) == true || $Fetched_avatar == 'notSet' || empty($Fetched_avatar) == false && is_file('assets/uploads/'.$Fetched_avatar) == false)
                                            {
                                        ?>
                                            <span class="brand-image img-circle elevation-2 d-flex justify-content-center align-items-center bg-primary text-white font-weight-500" style="width: 90px;height:90px">
                                                <h4>
                                                    <?php echo strtoupper(substr($Fetched_first_name, 0,1).substr($Fetched_last_name, 0,1)); ?>
                                                </h4>
                                            </span>
                                        <?php }else{ ?>
                                            <img class="img-circle elevation-2" src="assets/uploads/<?php echo $Fetched_avatar; ?>" alt="User Avatar"  style="width: 90px;height:90px;object-fit: cover">
                                        <?php } ?>
                                    </div>
                                    <div class="card-footer">
                                        <div class="container-fluid">
                                            <?php
                                                if (empty($Fetched_department_id) == true){
                                            ?>
                                                <dl>
                                                    <dt>Department</dt>
                                                    <dd>
                                                        Department not assigned
                                                    </dd>
                                                </dl>
                                            <?php
                                                }else{
                                                    // SQL SELECT statement with parameters
                                                    $selectDepartmentDetailsSQL = 'SELECT `department_id`, `department_name` FROM `departments` WHERE `department_id`=?';
                                                    // Prepare the resulting SQL statement query
                                                    $selectDepartmentDetailsSTMT = $dbConnection->prepare($selectDepartmentDetailsSQL);
                                                    // Bind all variables to the prepared SQL statement
                                                    $selectDepartmentDetailsSTMT->bind_param('s', $Fetched_department_id);
                                                    if
                                                    // ... the SQL statement is executed, ...
                                                    ($selectDepartmentDetailsSTMT->execute())
                                                    {
                                                        // Get the mysqli result variable from the SQL statement
                                                        $selectDepartmentDetailsSTMTResult = $selectDepartmentDetailsSTMT->get_result();
                                                        // Fetch data
                                                        $selectDepartmentDetailsSTMTRow = $selectDepartmentDetailsSTMTResult->fetch_assoc();
                                                    }
                                            ?>
                                                <dl>
                                                    <dt>Department id</dt>
                                                    <dd>
                                                        <?php echo $selectDepartmentDetailsSTMTRow["department_id"]; ?>
                                                    </dd>
                                                </dl>
                                                <dl>
                                                    <dt>Department name</dt>
                                                    <dd>
                                                        <?php echo $selectDepartmentDetailsSTMTRow["department_name"]; ?>
                                                    </dd>
                                                </dl>
                                            <?php } ?>
                                            <dl>
                                                <dt>Email</dt>
                                                <dd>
                                                    <a href="mailto:<?php echo $Fetched_email; ?>">
                                                        <?php echo $Fetched_email; ?>
                                                    </a>
                                                </dd>
                                            </dl>
                                            <dl>
                                                <dt>Last login</dt>
                                                <dd>
                                                    <?php echo $Fetched_last_login; ?>
                                                </dd>
                                            </dl>
                                            <dl>
                                                <dt>Last seen</dt>
                                                <dd>
                                                    <?php echo $Fetched_last_seen; ?>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- include mainfooter.php php file -->
                    <?php include 'mainfooter.php' ?>
                </div>
                <!-- include footer.php php file -->
                <?php include 'footer.php' ?>
            </body>
        </html>
<?php } ?>