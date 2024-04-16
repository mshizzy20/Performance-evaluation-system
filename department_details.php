<?php
    // start a new session
    session_start();

    if
    // $_SESSION['user_Pf_Number'] is not set, redirect to login
    (isset($_SESSION['user_Pf_Number']) == false)
    {
        // start session
        session_start();
        // unset session
        session_unset();
        // destroy session
        session_destroy();
        // redirect user to login web page
        header('Location:login');
        exit();
    }
    // else if $_GET['dptId'] is not set or is empty, redirect to department_list
    elseif (isset($_GET['dptId']) == false || empty($_GET['dptId'])==true)
    {
        // redirect user to department_list web page
        header('Location: department_list');
        exit();
    }
    else
    {
        // require connection to the database
        require 'databaseConnection.php';
        // require connection to the database
        require 'databaseCurrentTime.php';
        // require connection to the functions.php page
        require 'functions.php';
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
        if ($systemAccessValueRow !== 5){
            // start session
            session_start();
            // unset session
            session_unset();
            // destroy session
            session_destroy();
            // redirect user to login web page
            header("Location: login.php?loginError=loginError01&link=department_list");
            exit();
        }
        else
        {
            // Prepare a SQL statement with a parameter placeholder
            $stmt = $dbConnection->prepare('SELECT `department_id` FROM `departments` WHERE department_id=?');
            // Bind parameters to the statement
            $stmt->bind_param('s', $_GET['dptId']);
            // Execute the statement
            $stmt->execute();
            // Store result
            $stmt->store_result();
            if($stmt->num_rows!==1){
                // redirect user to department_list web page
                header("Location: department_list");
                exit();
            }
            else{
                // Prepare a SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare('SELECT `department_id`, `department_name`, `department_status`, `school`, `cod_Pf_Number`, `date_created`, `created_by` FROM `departments` WHERE department_id=?');
                // Bind parameters to the statement
                $stmt->bind_param('s', $_GET['dptId']);
                // Execute the statement
                $stmt->execute();
                // Retrieve the result set
                $result = $stmt->get_result();
                while ($dptRow = $result->fetch_assoc()){
?>
                    <!DOCTYPE html>
                    <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>epes | <?php echo escapeCharacters($dptRow['department_name']); ?></title>
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
                                                    <h1 class="m-0"><?php echo escapeCharacters($dptRow['department_name']); ?> department</h1>
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
                                                    <h3 class="mt-4"><?php echo escapeCharacters($dptRow['department_name']); ?></h3>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="container-fluid">
                                                        <dl>
                                                            <dt>Department id</dt>
                                                            <dd>
                                                                <?php echo escapeCharacters($dptRow["department_id"]); ?>
                                                            </dd>
                                                        </dl>
                                                        <dl>
                                                            <dt>Department name</dt>
                                                            <dd>
                                                                <?php echo escapeCharacters($dptRow["department_name"]); ?>
                                                            </dd>
                                                        </dl>
                                                        <dl>
                                                            <dt>Department status</dt>
                                                            <dd>
                                                                <?php if ($dptRow["department_status"]===5){?>
                                                                    <span class="font-weight-bold text-success">Active</span>
                                                                <?php }else{?>
                                                                    <span class="font-weight-bold text-danger">Inctive</span>
                                                                <?php } ?>
                                                            </dd>
                                                        </dl>
                                                        <dl>
                                                            <dt>Department school</dt>
                                                            <dd>
                                                                <?php
                                                                    // SQL SELECT statement with parameters
                                                                    $sql = 'SELECT `school_name` FROM `schools` WHERE `school_id`=?';
                                                                    // Prepare the resulting SQL statement query
                                                                    $stmt = $dbConnection->prepare($sql);
                                                                    // Bind all variables to the prepared SQL statement
                                                                    $stmt->bind_param('s',$dptRow["school"]);
                                                                    // execute the SQL statement
                                                                    $stmt->execute();
                                                                    // Get the mysqli result variable from the SQL statement
                                                                    $result = $stmt->get_result();
                                                                    $schRow = $result->fetch_assoc();
                                                                    echo escapeCharacters($schRow["school_name"]);
                                                                ?>
                                                            </dd>
                                                        </dl>
                                                        <dl>
                                                            <dt>Chair Of Department</dt>
                                                            <?php
                                                                // SQL SELECT statement with parameters
                                                                $sql = 'SELECT `title`, `first_name`, `middle_name`, `last_name`, `email` FROM `users` WHERE `user_Pf_Number`=?';
                                                                // Prepare the resulting SQL statement query
                                                                $stmt = $dbConnection->prepare($sql);
                                                                // Bind all variables to the prepared SQL statement
                                                                $stmt->bind_param('s',$dptRow["cod_Pf_Number"]);
                                                                // execute the SQL statement
                                                                $stmt->execute();
                                                                // Get the mysqli result variable from the SQL statement
                                                                $result = $stmt->get_result();
                                                                while($codRow = $result->fetch_assoc()){
                                                            ?>
                                                            <dd>
                                                                <?php echo escapeCharacters($codRow["first_name"]." ".$codRow["middle_name"]." ".$codRow["last_name"]); ?>
                                                            </dd>
                                                            <dd>
                                                                <a href="mailto:<?php echo escapeCharacters($codRow["email"]); ?>"><?php echo escapeCharacters($codRow["email"]); ?></a>
                                                            </dd>
                                                            <?php } ?>
                                                        </dl>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Main Footer -->
                                <!-- include mainfooter.php php file -->
                                <?php include 'mainfooter.php' ?>
                            </div>
                            <!-- include footer.php php file -->
                            <?php include 'footer.php' ?>
                        </body>
                    </html>
                <?php } ?>
            <?php } ?>
        <?php } ?>
<?php } ?>