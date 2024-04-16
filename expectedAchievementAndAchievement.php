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
        header('Location:login');
        exit();
    }
    // else if $_GET['pf'] is not set or is empty, redirect to evaluateDpt.php
    elseif (isset($_GET['pf']) == false || empty($_GET['pf'])==true)
    {
        // redirect user to evaluateDpt.php web page
        header('Location: evaluateDpt');
        exit();
    }
    else
    {
        // require connection to the database
        require 'databaseConnection.php';
        // require connection to the database
        require 'databaseCurrentTime.php';
        // SQL select statement
        $selectSystemAccessSql = 'SELECT `is_cod`, `department_id`, `system_access` FROM `users` WHERE `user_Pf_Number`=?';
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
        $systemAccessValueRow = $systemAccessValue['system_access'];
        $isCodRow = $systemAccessValue['is_cod'];
        $departmentIdRow = $systemAccessValue['department_id'];

        // if is not set to integer 5, redirect to login.php showing "Please contact the administrator for assistence" error message and exit
        if ($systemAccessValueRow !==5 || $isCodRow !==5)
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
        else
        {
            //SQL SELECT statement with a parameter placeholder
            $sql00 = 'SELECT `user_Pf_Number` FROM `users` WHERE `user_Pf_Number`=?';
            // Prepare the SQL statement
            $stmt00 = $dbConnection->prepare($sql00);
            // Bind parameters to the statement
            $stmt00->bind_param('s', $_GET['pf']);
            // Execute the statement
            $stmt00->execute();
            // Store result
            $stmt00->store_result();
            if($stmt00->num_rows!==1){
                // redirect user to evaluateDpt web page
                header("Location: evaluateDpt");
                exit();
            }
            else
            {
                //SQL SELECT statement with a parameter placeholder
                $sql01 = 'SELECT `department_id` FROM `departments` WHERE `department_id`=(SELECT `department_id` FROM `users` WHERE `user_Pf_Number`=? AND `department_id`=?)';
                // Prepare the SQL statement
                $stmt01 = $dbConnection->prepare($sql01);
                // Bind parameters to the statement
                $stmt01->bind_param('ss', $_GET['pf'],$departmentIdRow);
                // Execute the statement
                $stmt01->execute();
                // Store result
                $stmt01->store_result();
                if($stmt01->num_rows!==1){
                    // redirect user to evaluateDpt web page
                    header("Location: evaluateDpt");
                    exit();
                }
            }
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | Expected achievement and achievement</title>
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
                                            <h1 class="m-0">Expected achievements and achievements for
                                                <span>
                                                    <?php
                                                        // SQL select statement
                                                        $getStaffSql = 'SELECT `title`, `first_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                        // Prepare the SQL statement
                                                        $getStaffSql = $dbConnection->prepare($getStaffSql);
                                                        // Bind parameters to the statement
                                                        $getStaffSql->bind_param('s', $_GET['pf']);
                                                        // Execute the statement
                                                        $getStaffSql->execute();
                                                        // Retrieve the result set directly from the prepared statement
                                                        $getStaffSqlResult = $getStaffSql->get_result();
                                                        // The value of the associative array
                                                        $getStaffSqlRow = $getStaffSqlResult->fetch_assoc();
                                                        // Display the value of the associative array
                                                        echo $getStaffSqlRow['title'].' '.$getStaffSqlRow['first_name'];
                                                    ?>
                                                </span>
                                            </h1>
                                        </div>
                                    </div>
                                    <hr class="border-primary">
                                    <a href="evaluateDptStf?pf=<?php echo $_GET['pf'] ?>" type="button" class="btn btn-sm btn-primary text-light">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-11 mx-auto">
                                <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl card-outline card-primary">
                                    <thead>
                                        <tr>
                                            <th class="col-2">Attribute</th>
                                            <th class="col-2">Expected achievement</th>
                                            <th class="col-2">Achievement</th>
                                            <th class="col-2">Achieved weight</th>
                                            <th class="col-2">View evidence</th>
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
                                                        $getexpectedAchievementStmt->bind_param('ss', $_GET['pf'],$selectAchievementDataRow['expected_achievement_id']);
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
                                                        $getAchievementStmt->bind_param('ss', $_GET['pf'],$selectAchievementDataRow['expected_achievement_id']);
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
                                                <td>
                                                    <a href="viewAchievementEvidence?pf=<?php echo $_GET['pf']; ?>&stmt=<?php echo $selectAchievementDataRow['expected_achievement_id']; ?>" class="btn btn-sm btn-primary text-nowrap"><i class="fa-solid fa-eye mr-2"></i>View evidnce</a>
                                                    <?php
                                                        $emptyCodScore = "";
                                                        // SQL select statement
                                                        $chechStaffUploadSql = 'SELECT `cod_score` FROM `achievement_evidence` WHERE `user_Pf_Number`=? AND `attribute_id`=?  AND `cod_score`!=?';
                                                        // Prepare the SQL statement with a parameter placeholder
                                                        $chechStaffUploadStmt = $dbConnection->prepare($chechStaffUploadSql);
                                                        // Bind parameters to the statement
                                                        $chechStaffUploadStmt->bind_param('sss', $_GET['pf'], $selectAchievementDataRow['expected_achievement_id'],$emptyCodScore);
                                                        // Execute the statement
                                                        $chechStaffUploadStmt->execute();
                                                        // Retrieve the result set
                                                        $chechStaffUploadStmt->store_result();
                                                        if($chechStaffUploadStmt->num_rows!==0){
                                                    ?>
                                                        <i class="ml-1 text-success fa-solid fa-circle-check"></i>
                                                    <?php } ?>
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
                                                    $expectedAchievementTotalStmt->bind_param('s',$_GET['pf']);
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
                                                    $achievementTotalStmt->bind_param('s',$_GET['pf']);
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
            </body>
        </html>
<?php } ?>