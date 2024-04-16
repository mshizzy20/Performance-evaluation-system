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
        header('Location: login');
        exit();
    }
    else
    {
        // require connection to the database
        require 'databaseConnection.php';
        // require connection to the database
        require 'databaseCurrentTime.php';
        // SQL select statement
        $selectSystemAccessSql = 'SELECT `system_access`,`is_cod` FROM `users` WHERE `user_Pf_Number`=?';
        // Prepare the SQL statement with a parameter placeholder
        $selectSystemAccessStmt = $dbConnection->prepare($selectSystemAccessSql);
        // Bind parameters to the statement
        $selectSystemAccessStmt->bind_param('s', $_SESSION['user_Pf_Number']);
        // Execute the statement
        $selectSystemAccessStmt->execute();
        // Retrieve the result set
        $selectSystemAccessResult = $selectSystemAccessStmt->get_result();
        // Fetch data
        $systemAccessValue = $selectSystemAccessResult->fetch_assoc();
        // if is not set to integer 5, redirect to login.php showing "Please contact the administrator for assistence" error message and exit
        if ($systemAccessValue['system_access'] !== 5 || $systemAccessValue['is_cod'] !== 6)
        {
            // start session
            session_start();
            // unset session
            session_unset();
            // destroy session
            session_destroy();
            header('Location: login');
            exit();
        }
        
        $empty = "";
        // SQL select statement
        $evaluatedAchievementSql = 'SELECT `user_Pf_Number` FROM `achievement_evidence` WHERE `user_Pf_Number`=? AND `cod_score`=? AND `cod_score_date`=?';
        // Prepare the SQL statement with a parameter placeholder
        $evaluatedAchievementStmt = $dbConnection->prepare($evaluatedAchievementSql);
        // Bind parameters to the statement
        $evaluatedAchievementStmt->bind_param('sss', $_SESSION['user_Pf_Number'], $empty, $empty);
        // Execute the statement
        $evaluatedAchievementStmt->execute();
        // Retrieve the result set
        $evaluatedAchievementStmt->store_result();
        if ($evaluatedAchievementStmt->num_rows>0){
            header('Location: home');
            exit();
        }
        else
        { ?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>epes | Targets &amp; achievements</title>
                    <!-- include header.php php file -->
                    <?php include 'header.php' ?>
                </head>
                <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                    <div class="wrapper">
                        <!-- include topbar.php php file -->
                        <?php include 'topbar.php'; ?>
                        <!-- include sidebar.php php file -->
                        <?php include 'sidebar.php'; ?>
                        <!-- require connection to the functions.php page -->
                        <?php require 'functions.php';?>
                        <div class="content-wrapper">
                            <div class="content-header">
                                <div class="container-fluid">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <h1 class="m-0">Targets &amp; achievements</h1>
                                        </div>
                                    </div>
                                    <hr class="border-primary">
                                    <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </button>
                                </div>
                                <div class="col-lg-11 mx-auto mt-2">
                                    <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl card-outline card-primary">
                                        <thead>
                                            <tr>
                                                <th class="col-2">Attribute</th>
                                                <th class="col-2">Expected achievement</th>
                                                <th class="col-2">Achievement</th>
                                                <th class="col-2">Achieved weight</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // SQL select statement
                                                $selectAchievementDataSql = 'SELECT `expected_achievement_id`, `Ref` FROM `expected_achievement_form_data` ORDER BY `expected_achievement_id` ASC';
                                                // Prepare the SQL statement
                                                $selectAchievementDataStmt = $dbConnection->prepare($selectAchievementDataSql);
                                                // Execute the statement
                                                $selectAchievementDataStmt->execute();
                                                // Retrieve the result set directly from the prepared statement
                                                $selectAchievementDataResult = $selectAchievementDataStmt->get_result();
                                                // Iterate through the result set
                                                while($selectAchievementDataRow = $selectAchievementDataResult->fetch_assoc()){
                                            ?>
                                                <tr>
                                                    <td><?php echo $selectAchievementDataRow['Ref']; ?></td>
                                                    <td>
                                                        <?php
                                                            // SQL select statement
                                                            $getexpectedAchievementSql = 'SELECT `expected_achievement_id`, `expected_achievement` FROM `expected_achievement` WHERE `user_Pf_Number`=? AND `expected_achievement_id`=?';
                                                            // Prepare the SQL statement
                                                            $getexpectedAchievementStmt = $dbConnection->prepare($getexpectedAchievementSql);
                                                            // Bind parameters to the statement
                                                            $getexpectedAchievementStmt->bind_param('ss', $_SESSION['user_Pf_Number'],$selectAchievementDataRow['expected_achievement_id']);
                                                            // Execute the statement
                                                            $getexpectedAchievementStmt->execute();
                                                            // Retrieve the result set directly from the prepared statement
                                                            $getexpectedAchievementStmtResult = $getexpectedAchievementStmt->get_result();
                                                            // Fetch two rows as an associative array
                                                            $getexpectedAchievementsRow = $getexpectedAchievementStmtResult->fetch_assoc();
                                                            if(empty($getexpectedAchievementsRow['expected_achievement'])==true){
                                                                echo '<span title="The lecturer has not yet filled their expected achievement">Not set</span>';
                                                            }
                                                            else{
                                                                echo'<span title="Expected achievement filled">'.$getexpectedAchievementsRow['expected_achievement'].'</span>';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            // SQL select statement
                                                            $getAchievementSql = 'SELECT `achievement_id`, `achievement` FROM `self_achievement` WHERE `user_Pf_Number`=? AND `achievement_id`=?';
                                                            // Prepare the SQL statement
                                                            $getAchievementStmt = $dbConnection->prepare($getAchievementSql);
                                                            // Bind parameters to the statement
                                                            $getAchievementStmt->bind_param('ss', $_SESSION['user_Pf_Number'],$selectAchievementDataRow['expected_achievement_id']);
                                                            // Execute the statement
                                                            $getAchievementStmt->execute();
                                                            // Retrieve the result set directly from the prepared statement
                                                            $getAchievementStmtResult = $getAchievementStmt->get_result();
                                                            // Fetch two rows as an associative array
                                                            $getAchievementsRow = $getAchievementStmtResult->fetch_assoc();
                                                            if(empty($getAchievementsRow['achievement'])==true){
                                                                echo '<span title="The lecturer has not yet filled their achievement">Not set</span>';
                                                            }
                                                            else{
                                                                echo'<span title="Achievement filled">'.$getAchievementsRow['achievement'].'</span>';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            // SQL select statement
                                                            $getWeightSql = 'SELECT `unit`, `maximum`,`weight` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                                                            // Prepare the SQL statement
                                                            $getWeightStmt = $dbConnection->prepare($getWeightSql);
                                                            // Bind parameters to the statement
                                                            $getWeightStmt->bind_param('s', $selectAchievementDataRow['expected_achievement_id']);
                                                            // Execute the statement
                                                            $getWeightStmt->execute();
                                                            // Retrieve the result set directly from the prepared statement
                                                            $getWeightStmtResult = $getWeightStmt->get_result();
                                                            // Fetch two rows as an associative array
                                                            $getWeightRow = $getWeightStmtResult->fetch_assoc();
                                                            if(empty($getWeightRow['maximum'])==true){
                                                                echo '<span title="The maximum has not been set">Not set</span>';
                                                            }
                                                            elseif(empty($getWeightRow['weight'])==true){
                                                                echo '<span title="The weight has not been set">Not set</span>';
                                                            }
                                                            if (empty($getAchievementsRow['achievement'])==true) {
                                                                echo 'Not set';
                                                            }
                                                            else
                                                            {
                                                                if ($getWeightRow['unit'] == "No") {
                                                                    echo $achievementWeight = round(((int)$getAchievementsRow['achievement']*(int)$getWeightRow['weight'])/(int)$getWeightRow['maximum']);
                                                                }
                                                                elseif ($getWeightRow['unit'] == "%") {
                                                                    echo $achievementWeight = round(((int)$getAchievementsRow['achievement']*(int)$getWeightRow['weight'])/(int)$getWeightRow['maximum']).'&percnt;';
                                                                }
                                                                else {
                                                                    echo $achievementWeight = round(((int)$getAchievementsRow['achievement']*(int)$getWeightRow['weight'])/(int)$getWeightRow['maximum']).'<br> Unit not set';
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr class="font-weight-bold">
                                                <td>Totals</td>
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
                                                <td colspan="2">
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
                             <!-- include mainfooter.php php file -->
                            <?php include 'mainfooter.php' ?>
                        </div>
                        <!-- include footer.php php file -->
                        <?php include 'footer.php' ?>
                    </div>
                </body>
            </html>
        <?php } ?>
<?php } ?>