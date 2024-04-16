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

        // Prepare the SQL statement
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
            header("Location: login.php?loginError=loginError01&link=expected_achievement");
            exit();
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | Expected achievement</title>
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
                                        <h1 class="m-0">Expected achievement</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="mb-2">
                                <?php
                                    // SQL select statement
                                    $selectStaffPfSql = 'SELECT `user_Pf_Number` FROM `expected_achievement` WHERE `user_Pf_Number`=?';
                                    // Prepare the SQL statement
                                    $selectStaffPfStmt = $dbConnection->prepare($selectStaffPfSql);
                                    // Bind parameters to the statement
                                    $selectStaffPfStmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                    // Execute the statement
                                    $selectStaffPfStmt->execute();
                                    // Store the result set
                                    $selectStaffPfStmt->store_result();
                                    if ($selectStaffPfStmt->num_rows===0 || $selectStaffPfStmt->num_rows<20){
                                ?>
                                    <a href="fill_expected_achievement.php" class="ml-1 btn btn-primary text-light text-decoration-none">Fill expected achievement</a>
                                <?php }elseif ($selectStaffPfStmt->num_rows===20){ ?>
                                <?php } ?>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                                        <thead>
                                            <tr>
                                                <th class="col-2">Task</th>
                                                <th class="col-2">Expected achievement</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // SQL select statement
                                                $getexpectedAchievementsSql = 'SELECT `expected_achievement_id`, `expected_achievement` FROM `expected_achievement` WHERE `user_Pf_Number`=? ORDER BY `expected_achievement_id` ASC';
                                                // Prepare the SQL statement
                                                $getexpectedAchievements = $dbConnection->prepare($getexpectedAchievementsSql);
                                                // Bind parameters to the statement
                                                $getexpectedAchievements->bind_param('s', $_SESSION['user_Pf_Number']);
                                                // Execute the statement
                                                $getexpectedAchievements->execute();
                                                // Retrieve the result set directly from the prepared statement
                                                $getexpectedAchievementsResult = $getexpectedAchievements->get_result();
                                                // Iterate through the result set
                                                while ($getexpectedAchievementsRow = $getexpectedAchievementsResult->fetch_assoc())
                                                {
                                                    $expected_achievement_id = $getexpectedAchievementsRow['expected_achievement_id'];
                                                    $expected_achievement = $getexpectedAchievementsRow['expected_achievement'];
                                            ?>
                                                    <tr>
                                                        <td>
                                                            <?php
                                                                // SQL select statement
                                                                $selectRefSql = 'SELECT `Ref` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                                                                // Prepare the SQL statement
                                                                $selectRefStmt = $dbConnection->prepare($selectRefSql);
                                                                // Bind parameters to the statement
                                                                $selectRefStmt->bind_param('s', $expected_achievement_id);
                                                                // Execute the statement
                                                                $selectRefStmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $selectRefResult = $selectRefStmt->get_result();
                                                                // Fetch a single row as an associative array
                                                                $selectRefRow = $selectRefResult->fetch_assoc();
                                                                // Display the value of the associative array
                                                                echo $selectRefRow["Ref"];
                                                            ?>
                                                        </td>
                                                        <td><?php echo $expected_achievement; ?></td>
                                                    </tr>
                                            <?php } ?>
                                            <tr class="font-weight-bold">
                                                <td>Total expected achievement</td>
                                                <td>
                                                    <?php
                                                        // SQL select statement
                                                        $expectedAchievementTotalSql = "SELECT SUM(expected_achievement) AS `expectedAchievementTotal` FROM `expected_achievement` WHERE `user_Pf_Number`=?";
                                                        // Prepare the SQL statement
                                                        $expectedAchievementTotalStmt = $dbConnection->prepare($expectedAchievementTotalSql);
                                                        // Bind parameter to the statement
                                                        $expectedAchievementTotalStmt->bind_param('s',$_SESSION['user_Pf_Number']);
                                                        // Execute the statement
                                                        $expectedAchievementTotalStmt->execute();
                                                        // Retrieve the result set directly from the prepared statement
                                                        $expectedAchievementTotalResult = $expectedAchievementTotalStmt->get_result();
                                                        // Fetch two rows as an associative array
                                                        $expectedAchievementTotalRow = $expectedAchievementTotalResult->fetch_assoc();
                                                        if (empty($expectedAchievementTotalRow['expectedAchievementTotal'])==true){
                                                            echo '0.00';
                                                        }
                                                        else
                                                        {
                                                            echo $expectedAchievementTotalRow['expectedAchievementTotal'];
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
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