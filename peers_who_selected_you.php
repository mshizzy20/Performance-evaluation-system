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
            header("Location: login.php?loginError=loginError01&link=home");
            exit();
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | Peers who selected you</title>
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
                                        <h1 class="m-0">Peers who selected you</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-10 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                                        <thead>
                                            <tr>
                                                <th class="text-center">PF number</th>
                                                <th class="text-center">Name</th>
                                                <th class="text-center">Date selected</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $cod = 5;
                                                $department_id = $_SESSION['department_id'];
                                                // Prepare a SQL statement with a parameter placeholder
                                                $selectPeersISelectedStmt = $dbConnection->prepare('SELECT * FROM `peer_evaluators` WHERE `peer_pf`=? ORDER BY `time_selected` ASC');
                                                // Bind parameters to the statement
                                                $selectPeersISelectedStmt->bind_param('s',$_SESSION['user_Pf_Number']);
                                                // Execute the statement
                                                $selectPeersISelectedStmt->execute();
                                                // Retrieve the result set
                                                $selectPeersISelectedResult = $selectPeersISelectedStmt->get_result();
                                                // Fetch data
                                                while ($selectPeersISelectedRow = $selectPeersISelectedResult->fetch_assoc())
                                                {
                                            ?>
                                            <tr>
                                                <th>
                                                    <?php echo $selectPeersISelectedRow['pf_being_evaluated']; ?>
                                                </th>
                                                <td>
                                                    <?php
                                                        // SQL SELECT statement with parameters
                                                        $selectCodDetailsSQL = 'SELECT `user_Pf_Number`, `first_name`, `last_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                        // Prepare the resulting SQL statement query
                                                        $selectCodDetailsSTMT = $dbConnection->prepare($selectCodDetailsSQL);
                                                        // Bind all variables to the prepared SQL statement
                                                        $selectCodDetailsSTMT->bind_param('s', $selectPeersISelectedRow['pf_being_evaluated']);
                                                        if
                                                        // ... the SQL statement is executed, ...
                                                        ($selectCodDetailsSTMT->execute())
                                                        {
                                                            // Get the mysqli result variable from the SQL statement
                                                            $selectCodDetailsSTMTResult = $selectCodDetailsSTMT->get_result();
                                                            // Fetch data
                                                            while ($selectCodDetailsSTMTRow = $selectCodDetailsSTMTResult->fetch_assoc())
                                                            {
                                                                echo''.$selectCodDetailsSTMTRow["first_name"].' '.$selectCodDetailsSTMTRow["last_name"];
                                                            }
                                                        }
                                                    ?>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i:s', strtotime($selectPeersISelectedRow['time_selected'])); ?></td>
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
            </body>
        </html>
<?php } ?>