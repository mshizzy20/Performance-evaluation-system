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
            header("Location: login.php?loginError=loginError01&link=school_list");
            exit();
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | School list</title>
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
                                        <h1 class="m-0">Schools list</h1>
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
                                if (isset($_SESSION['session_new_school'])==true && isset($_SESSION['session_schoolName'])==true && empty($_SESSION['session_new_school'])==false && empty($_SESSION['session_schoolName'])==false && $_SESSION['session_new_school']=='success'){
                            ?>
                                <div class="col-sm-6 col-md-4 col-lg-4 mx-auto">
                                    <div class="alert alert-info text-center" role="alert">
                                        <b><?php echo $_SESSION['session_schoolName']; ?></b> school has been added successfully!
                                        <?php $_SESSION['session_new_school']=''; $_SESSION['session_schoolName']='';?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php
                                // Prepare a SQL statement with a parameter placeholder
                                $countSchoolStmt = $dbConnection->prepare('SELECT COUNT(school_id) AS `schoolIdCount` FROM `schools`');

                                // Execute the statement
                                $countSchoolStmt->execute();

                                // Retrieve the result set
                                $result = $countSchoolStmt->get_result();

                                // Fetch data
                                while ($countSchooltRow = $result->fetch_assoc())
                                {
                                    $actualDepartmentIdCount = $countSchooltRow['schoolIdCount'];
                                    
                                    if ($actualDepartmentIdCount < 1)
                                    {
                            ?>
                                        <div class="col-sm-6 col-md-4 col-lg-4 mx-auto">
                                            <div class="alert alert-info text-center" role="alert">
                                                There are no schools currently!
                                                <br><br>
                                                Would you like to add one?
                                                <br><br>
                                                <a class="text-decoration-none btn btn-sm btn-primary btn-block" href="new_school.php"><i class="fa fa-plus"></i> Add new school</a>
                                            </div>
                                        </div>
                                    <?php }else{ ?>
                                        <div class="col-lg-12">
                                            <div class="card card-outline card-success">
                                                <div class="card-header">
                                                    <div class="card-tools">
                                                        <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="new_school.php"><i class="fa fa-plus"></i> Add new school</a>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table tabe-hover table-bordered" id="schoolListDataTables">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">#</th>
                                                                <th>School id</th>
                                                                <th>School name</th>
                                                                <th>School status</th>
                                                                <th>School email</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                // initial number
                                                                $count01 = 1;
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $selectAllSchoolStmt = $dbConnection->prepare('SELECT * FROM `schools` ORDER BY `school_id` ASC');

                                                                // Execute the statement
                                                                $selectAllSchoolStmt->execute();

                                                                // Retrieve the result set
                                                                $selectAllSchoolResult = $selectAllSchoolStmt->get_result();

                                                                // Fetch data
                                                                while ($selectAllSchoolRow = $selectAllSchoolResult->fetch_assoc())
                                                                {
                                                            ?>
                                                                    <tr>
                                                                        <th class="text-center"><?php echo $count01++ ?></th>
                                                                        <td><b><?php echo $selectAllSchoolRow['school_id']; ?></b></td>
                                                                        <td><b><?php echo $selectAllSchoolRow['school_name']; ?></b></td>
                                                                        <td>
                                                                            <b>
                                                                                <?php
                                                                                    if ($selectAllSchoolRow['school_status'] === 5){
                                                                                ?>
                                                                                    Active
                                                                                <?php }else{ ?>
                                                                                    Inactive
                                                                                <?php } ?>
                                                                            </b>
                                                                        </td>
                                                                        <td>
                                                                            <b>
                                                                                <?php
                                                                                    if (empty($selectAllSchoolRow['school_email']) == true)
                                                                                    {
                                                                                ?>
                                                                                Not set
                                                                                <?php }else{ ?>
                                                                                    <a href="mailto:<?php echo $selectAllSchoolRow['school_email']; ?>" class="text-decoration-none text-primary">
                                                                                        <?php echo $selectAllSchoolRow['school_email']; ?>
                                                                                    </a>
                                                                                <?php } ?>
                                                                            </b>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                                                                Action
                                                                            </button>
                                                                            <div class="dropdown-menu">
                                                                                <a class="dropdown-item" href="school_details.php?staffId=<?php echo $selectAllSchoolRow['school_id']; ?>">
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
                        $('#schoolListDataTables').DataTable();
                    } );
                </script>
            </body>
        </html>
<?php } ?>