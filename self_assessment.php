<?php
    // Check if session is not started
    if (session_status() === PHP_SESSION_NONE)
    {
        // start a new session
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
        session_destroy();
        // redirect user to index.php web page
        header('Location:login');
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
        // Retrieve the result set directly from the prepared statement
        $selectSystemAccessResult = $selectSystemAccessStmt->get_result();
        // Fetch rows as an associative array
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
            header('Location: login');
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
            // else if the number of rows is not identical to integer 20, redirect to expected_achievement.php web page
            if ($stmt->num_rows!==20)
            {
                header('Location: expected_achievement');
                exit();
            }
            else
            {
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
                // else if the number of rows is not identical to integer 20, redirect to achievement.php web page
                if ($stmt->num_rows!==20)
                {
                    header('Location: achievement');
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
                <title>epes | Self assessment</title>
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
                                        <h1 class="m-0">Self assessment</h1>
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
                                    $sql = 'SELECT `user_Pf_Number` FROM `self_assessment` WHERE `user_Pf_Number`=?';
                                    // Prepare the SQL statement
                                    $stmt = $dbConnection->prepare($sql);
                                    // Bind parameters to the statement
                                    $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                    // Execute the statement
                                    $stmt->execute();
                                    // Store the result set
                                    $stmt->store_result();
                                    // else if the number of rows is identical to integer 0 or less than integer 12, display the button to fill_self_assessment.php web page
                                    if ($stmt->num_rows===0 || $stmt->num_rows<12){
                                ?>
                                    <a href="fill_self_assessment.php" class="ml-1 btn btn-primary text-light text-decoration-none">Fill self assessment</a>
                                <?php } ?>
                            </div>
                            <div class="card">
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

                    </div>
                    <!-- include mainfooter.php php file -->
                    <?php include 'mainfooter.php' ?>
                </div>
                <!-- include footer.php php file -->
                <?php include 'footer.php' ?>
            </body>
        </html>
<?php } ?>