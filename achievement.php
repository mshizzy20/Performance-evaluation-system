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
            header("Location: login.php?loginError=loginError01&link=achievement");
            exit();
        }
        else
        {
            // SQL select statement
            $sql = 'SELECT `user_Pf_Number` FROM `expected_achievement` WHERE `user_Pf_Number`=?';
            // Prepare the SQL statement
            $stmt = $dbConnection->prepare($sql);
            // Bind parameters to the statement
            $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
            // Execute the statement
            $stmt->execute();
            // Store the result set
            $stmt->store_result();
            // else if the number of rows is identical to integer 20, redirect to expected_achievement.php web page
            if ($stmt->num_rows!==20)
            {
                header("Location: expected_achievement.php");
                exit();
            }
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | Achievement</title>
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
                                        <h1 class="m-0">Achievement</h1>
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
                                    $sql = 'SELECT `user_Pf_Number` FROM `self_achievement` WHERE `user_Pf_Number`=?';
                                    // Prepare the SQL statement
                                    $stmt = $dbConnection->prepare($sql);
                                    // Bind parameters to the statement
                                    $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                    // Execute the statement
                                    $stmt->execute();
                                    // Store the result set
                                    $stmt->store_result();
                                    if ($stmt->num_rows===0 || $stmt->num_rows<20){
                                ?>
                                    <a href="fill_achievement.php" class="ml-1 btn btn-primary text-light text-decoration-none">Fill achievement</a>
                                <?php } ?>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                                        <thead>
                                            <tr>
                                                <th class="col-2">Task</th>
                                                <th class="col-2">Achievement</th>
                                                <th class="col-1">Upload evidence</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // SQL select statement
                                                $getAchievementSql = 'SELECT `achievement_id`, `achievement` FROM `self_achievement` WHERE `user_Pf_Number`=? ORDER BY `achievement_id` ASC';
                                                // Prepare the SQL statement
                                                $getAchievement = $dbConnection->prepare($getAchievementSql);
                                                // Bind parameters to the statement
                                                $getAchievement->bind_param('s', $_SESSION['user_Pf_Number']);
                                                // Execute the statement
                                                $getAchievement->execute();
                                                // Retrieve the result set directly from the prepared statement
                                                $getAchievementResult = $getAchievement->get_result();
                                                // Iterate through the result set
                                                while ($getAchievementRow = $getAchievementResult->fetch_assoc())
                                                {
                                                    $achievement_id = $getAchievementRow['achievement_id'];
                                                    $achievement = $getAchievementRow['achievement'];
                                            ?>
                                                    <tr>
                                                        <td>
                                                            <?php
                                                                // SQL select statement
                                                                $selectRefSql = 'SELECT `Ref` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                                                                // Prepare the SQL statement
                                                                $selectRefStmt = $dbConnection->prepare($selectRefSql);
                                                                // Bind parameters to the statement
                                                                $selectRefStmt->bind_param('s', $achievement_id);
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
                                                        <td><?php echo $achievement; ?></td>
                                                        <td>
                                                            <a href="achievement_evidence_upload?stmt=<?php echo $achievement_id; ?>" class="btn btn-sm btn-primary text-nowrap"><i class="fa-solid fa-upload fa-bounce mr-2"></i>Upload evidnce</a>
                                                        </td>
                                                    </tr>
                                            <?php } ?>
                                            <tr class="font-weight-bold">
                                                <td>Total achievement</td>
                                                <td>
                                                    <?php
                                                        // SQL select statement
                                                        $achievementTotalSql = "SELECT SUM(achievement) AS `achievementTotal` FROM `self_achievement` WHERE `user_Pf_Number`=?";
                                                        // Prepare the SQL statement
                                                        $achievementTotalStmt = $dbConnection->prepare($achievementTotalSql);
                                                        // Bind parameter to the statement
                                                        $achievementTotalStmt->bind_param('s',$_SESSION['user_Pf_Number']);
                                                        // Execute the statement
                                                        $achievementTotalStmt->execute();
                                                        // Retrieve the result set directly from the prepared statement
                                                        $achievementTotalResult = $achievementTotalStmt->get_result();
                                                        // Fetch two rows as an associative array
                                                        $achievementTotalRow = $achievementTotalResult->fetch_assoc();
                                                        if (empty($achievementTotalRow['achievementTotal'])==true){
                                                            echo '0.00';
                                                        }
                                                        else
                                                        {
                                                            echo $achievementTotalRow['achievementTotal'];
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