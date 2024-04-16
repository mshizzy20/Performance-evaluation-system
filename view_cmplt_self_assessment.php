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
                    <title>epes | Self assessment</title>
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
                                            <h1 class="m-0">Self assessment</h1>
                                        </div>
                                    </div>
                                    <hr class="border-primary">
                                    <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </button>
                                </div>
                                <div class="card mt-2">
                                    <div class="card-body">
                                        <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                                            <thead>
                                                <tr>
                                                    <th class="col-2">Task</th>
                                                    <th class="col-2">Self assessment</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    // SQL select statement
                                                    $sql = 'SELECT `attribute_id`, `self_assessment_value` FROM `self_assessment` WHERE `user_Pf_Number`=? ORDER BY `attribute_id` ASC';
                                                    // Prepare the SQL statement
                                                    $stmt = $dbConnection->prepare($sql);
                                                    // Bind parameters to the statement
                                                    $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Retrieve the result set directly from the prepared statement
                                                    $stmtResult = $stmt->get_result();
                                                    // Iterate through the result set
                                                    while ($row = $stmtResult->fetch_assoc())
                                                    {
                                                        $attribute_id = $row['attribute_id'];
                                                        $self_assessment_value = $row['self_assessment_value'];
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                    // SQL select statement
                                                                    $selectAttributeSql = 'SELECT `attribute` FROM `self_assessment_form_data` WHERE `attribute_id`=?';
                                                                    // Prepare the SQL statement
                                                                    $selectAttributeStmt = $dbConnection->prepare($selectAttributeSql);
                                                                    // Bind parameters to the statement
                                                                    $selectAttributeStmt->bind_param('s', $attribute_id);
                                                                    // Execute the statement
                                                                    $selectAttributeStmt->execute();
                                                                    // Retrieve the result set directly from the prepared statement
                                                                    $selectAttributeResult = $selectAttributeStmt->get_result();
                                                                    // Fetch a single row as an associative array
                                                                    $selectAttributeRow = $selectAttributeResult->fetch_assoc();
                                                                    // Display the value of the associative array
                                                                    echo $selectAttributeRow["attribute"];
                                                                ?>
                                                            </td>
                                                            <td><?php echo $self_assessment_value; ?></td>
                                                        </tr>
                                                <?php } ?>
                                                <tr class="font-weight-bold">
                                                    <td>Total self assessment</td>
                                                    <td colspan=2>
                                                        <?php
                                                            // SQL select statement
                                                            $sql = "SELECT SUM(self_assessment_value) AS `selfAssessmentValueTotal` FROM `self_assessment` WHERE `user_Pf_Number`=?";
                                                            // Prepare the SQL statement
                                                            $stmt = $dbConnection->prepare($sql);
                                                            // Bind parameters to the statement
                                                            $stmt->bind_param('s',$_SESSION['user_Pf_Number']);
                                                            // Execute the statement
                                                            $stmt->execute();
                                                            // Retrieve the result set directly from the prepared statement
                                                            $result = $stmt->get_result();
                                                            // Fetch a single row as an associative array
                                                            $row = $result->fetch_assoc();
                                                            if (empty($row['selfAssessmentValueTotal'])==true)
                                                            {
                                                                echo "0.00";
                                                            }
                                                            else
                                                            {
                                                                echo $row['selfAssessmentValueTotal'];
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
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