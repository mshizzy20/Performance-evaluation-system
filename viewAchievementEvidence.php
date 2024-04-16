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
    elseif (isset($_GET['pf']) == false || isset($_GET['stmt']) == false || empty($_GET['pf'])==true || empty($_GET['stmt'])==true)
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
        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitEvidenceScore' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitEvidenceScore']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';

            $attribute_id = trim(mysqli_real_escape_string($dbConnection, $_POST['attribute_id']));
            $attribute_id = htmlspecialchars(mysqli_real_escape_string($dbConnection,trim($attribute_id)), ENT_QUOTES, 'UTF-8');

            $evidenceScore = trim(mysqli_real_escape_string($dbConnection, $_POST['evidenceScore']));
            $evidenceScore = htmlspecialchars(mysqli_real_escape_string($dbConnection,trim($evidenceScore)), ENT_QUOTES, 'UTF-8');

            $lec_Pf_Number = trim(mysqli_real_escape_string($dbConnection, $_GET['pf']));
            $lec_Pf_Number = htmlspecialchars(mysqli_real_escape_string($dbConnection,trim($lec_Pf_Number)), ENT_QUOTES, 'UTF-8');

            if (empty($attribute_id)==true){
                header('Location: viewAchievementEvidence?pf='.$_GET['pf'].'&stmt='.$_GET['stmt'].'&error=empty_attribute_id');
                exit();
            }
            elseif (empty($evidenceScore)==true){
                header('Location: viewAchievementEvidence?pf='.$_GET['pf'].'&stmt='.$_GET['stmt'].'&error=empty_evidenceScore');
                exit();
            }
            elseif (empty($lec_Pf_Number)==true){
                header('Location: viewAchievementEvidence?pf='.$_GET['pf'].'&stmt='.$_GET['stmt'].'&error=empty_pf');
                exit();
            }
            else{
                // SQL select statement
                $getMaximumScoreSql = 'SELECT `maximum` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                // Prepare the SQL statement
                $getMaximumScoreStmt = $dbConnection->prepare($getMaximumScoreSql);
                // Bind parameters to the statement
                $getMaximumScoreStmt->bind_param('s', $attribute_id);
                // Execute the statement
                $getMaximumScoreStmt->execute();
                // Retrieve the result set directly from the prepared statement
                $getMaximumScoreResult = $getMaximumScoreStmt->get_result();
                // The value of the associative array
                $getMaximumScoreRow = $getMaximumScoreResult->fetch_assoc();
                
                if ($evidenceScore > $getMaximumScoreRow['maximum']){
                    header('Location: viewAchievementEvidence?pf='.$_GET['pf'].'&stmt='.$_GET['stmt'].'&error=invalid&max='.$getMaximumScoreRow['maximum'].'');
                    exit();
                }
                else{
                    $cod_score_date = date("Y-m-d H:i:s",strtotime($currentTime));
                    // SQL select statement
                    $saveCodScoreSql = 'UPDATE `achievement_evidence` SET `cod_score`=?, `cod_score_date`=? WHERE `user_Pf_Number`=? AND `attribute_id`=?';
                    // Prepare the SQL statement
                    $saveCodScoreStmt = $dbConnection->prepare($saveCodScoreSql);
                    // Bind parameters to the statement
                    $saveCodScoreStmt->bind_param('ssss', $evidenceScore,$cod_score_date,$lec_Pf_Number,$attribute_id);
                    // Execute the statement
                    if ($saveCodScoreStmt->execute()){
                        header('Location: viewAchievementEvidence?pf='.$_GET['pf'].'&stmt='.$_GET['stmt'].'&success=saved');
                        exit();
                    }
                    else{
                        header('Location: viewAchievementEvidence?pf='.$_GET['pf'].'&stmt='.$_GET['stmt'].'&error=notSaved');
                        exit();
                    }
                }
            }
        }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | Achievement evidences</title>
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
                                        <h1 class="m-0">Achievement evidences for
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
                                <a href="expectedAchievementAndAchievement?pf=<?php echo $_GET['pf']; ?>" type="button" class="btn btn-sm btn-primary text-light">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="container">
                            <?php
                                // SQL select statement
                                $chechStaffUploadSql = 'SELECT `user_Pf_Number` FROM `achievement_evidence` WHERE `user_Pf_Number`=? AND `attribute_id`=?';
                                // Prepare the SQL statement with a parameter placeholder
                                $chechStaffUploadStmt = $dbConnection->prepare($chechStaffUploadSql);
                                // Bind parameters to the statement
                                $chechStaffUploadStmt->bind_param('ss', $_GET['pf'],$_GET['stmt']);
                                // Execute the statement
                                $chechStaffUploadStmt->execute();
                                // Retrieve the result set
                                $chechStaffUploadStmt->store_result();
                                if($chechStaffUploadStmt->num_rows<1){
                            ?>
                                <div class="text-center"><h3>No evidence has been uploaded</h3></div>
                            <?php }else{ ?>
                                <?php
                                    // SQL select statement
                                    $getCodScoreSql = 'SELECT `attribute_id`, `cod_score` FROM `achievement_evidence` WHERE `user_Pf_Number`=? AND `attribute_id`=?';
                                    // Prepare the SQL statement with a parameter placeholder
                                    $getCodScoreStmt = $dbConnection->prepare($getCodScoreSql);
                                    // Bind parameters to the statement
                                    $getCodScoreStmt->bind_param('ss', $_GET['pf'],$_GET['stmt']);
                                    // Execute the statement
                                    $getCodScoreStmt->execute();
                                    // Retrieve the result set directly from the prepared statement
                                    $getCodScoreResult = $getCodScoreStmt->get_result();
                                    $getCodScoreResultRow = $getCodScoreResult->fetch_assoc();
                                    if (empty($getCodScoreResultRow['cod_score'])==true) {
                                ?>
                                    <button type="button" class="mb-3 btn btn-primary btn-sm text-nowrap" data-toggle="modal" data-target="#evaluateEvidenceModal<?php echo $getCodScoreResultRow['attribute_id']; ?>">
                                        Evaluate
                                    </button>

                                    <div data-backdrop="static" data-keyboard="false" class="modal fade" id="evaluateEvidenceModal<?php echo $getCodScoreResultRow['attribute_id'];?>" tabindex="-1" role="dialog" aria-labelledby="evaluateEvidenceModalTitle" aria-hidden="true" style="-webkit-user-select:none;-ms-user-select:none;user-select:none;">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="evaluateEvidenceModalTitle">
                                                        Evaluate evidence
                                                    </h5>
                                                    <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </span>
                                                </div>
                                                <form action="" method="post" onsubmit="return evidenceScoreJsValidation()">
                                                    <div class="modal-body">
                                                        <input type="text" name="attribute_id" id="attribute_id" class="form-control" value="<?php echo $getCodScoreResultRow['attribute_id']; ?>" style="display: none;" hidden autocomplete="off">
                                                        <?php
                                                            // SQL select statement
                                                            $getMaximumScoreSql = 'SELECT `maximum` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                                                            // Prepare the SQL statement
                                                            $getMaximumScoreStmt = $dbConnection->prepare($getMaximumScoreSql);
                                                            // Bind parameters to the statement
                                                            $getMaximumScoreStmt->bind_param('s', $_GET['stmt']);
                                                            // Execute the statement
                                                            $getMaximumScoreStmt->execute();
                                                            // Retrieve the result set directly from the prepared statement
                                                            $getMaximumScoreResult = $getMaximumScoreStmt->get_result();
                                                            // The value of the associative array
                                                            $getMaximumScoreRow = $getMaximumScoreResult->fetch_assoc();
                                                        ?>
                                                        <span class="d-block text-muted">
                                                            Maximum score : <?php echo $getMaximumScoreRow['maximum']; ?>
                                                        </span>
                                                        <label for="evidenceScore" class="font-weight-bold">Evidence score</label>
                                                        <input type="tel" name="evidenceScore" id="evidenceScore" class="form-control" placeholder="Evidence score" autocomplete="off">
                                                        <button type="submit" name="submitEvidenceScore" id="submitEvidenceScore" class=" mt-3 btn btn-sm btn-block btn-primary">Submit</button>
                                                        <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none mt-3 btn btn-sm btn-block btn-primary">
                                                            <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-footer">
                                                    </div>
                                                </form>
                                                <script>
                                                    function evidenceScoreJsValidation()
                                                    {
                                                        // Set the value of is_input_valid to be true by default
                                                        var is_input_valid = true;

                                                        document.getElementById('submitEvidenceScore').className = "d-none";
                                                        document.getElementById('loadingSpinner').className = "d-block mt-3 btn btn-sm btn-block btn-primary";

                                                        return is_input_valid;
                                                    }
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                
                                <div>
                                    <?php if (isset($_GET['error'])==true && empty($_GET['error'])==false && $_GET['error']=="empty_attribute_id"){ ?>
                                        <div class="container w-50 text-center" id="errorAlertDiv">
                                            <div class="alert alert-warning text-center text-dark font-weight-bold" role="alert">
                                                Please try again
                                            </div>
                                        </div>
                                    <?php } elseif (isset($_GET['error'])==true && empty($_GET['error'])==false && $_GET['error']=="empty_evidenceScore"){ ?>
                                        <div class="container w-50 text-center" id="errorAlertDiv">
                                            <div class="alert alert-warning text-center text-dark font-weight-bold" role="alert">
                                                Please fill the evidence score
                                            </div>
                                        </div>
                                    <?php } elseif (isset($_GET['error'])==true && empty($_GET['error'])==false && $_GET['error']=="empty_pf"){ ?>
                                        <div class="container w-50 text-center" id="errorAlertDiv">
                                            <div class="alert alert-warning text-center text-dark font-weight-bold" role="alert">
                                                Please fill the evidence score
                                            </div>
                                        </div>
                                    <?php } elseif (isset($_GET['error'])==true && isset($_GET['max'])==true && empty($_GET['max'])==false){ ?>
                                        <div class="container w-50 text-center" id="errorAlertDiv">
                                            <div class="alert alert-warning text-center text-dark font-weight-bold" role="alert">
                                                The maximum evidence score is <?php echo $_GET['max']; ?>
                                            </div>
                                        </div>
                                    <?php } elseif (isset($_GET['error'])==true && empty($_GET['error'])==false && $_GET['error']=="notSaved"){ ?>
                                        <div class="container w-50 text-center" id="errorAlertDiv">
                                            <div class="alert alert-warning text-center text-dark font-weight-bold" role="alert">
                                                Please try again
                                            </div>
                                        </div>
                                    <?php } elseif (isset($_GET['success'])==true && empty($_GET['success'])==false && $_GET['success']=="saved"){ ?>
                                        <div class="container w-50 text-center" id="errorAlertDiv">
                                            <div class="alert alert-success text-center text-dark font-weight-bold" role="alert">
                                                The evidence score has been saved successfully
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="row">
                                    <?php
                                        // SQL select statement
                                        $getCategorizedStaffUploadsSql = 'SELECT `attribute_id`, `cod_score`, `evidence_id` FROM `achievement_evidence` WHERE `user_Pf_Number`=? AND `attribute_id`=? GROUP BY `evidence_id` ASC';
                                        // Prepare the SQL statement with a parameter placeholder
                                        $getCategorizedStaffUploadsStmt = $dbConnection->prepare($getCategorizedStaffUploadsSql);
                                        // Bind parameters to the statement
                                        $getCategorizedStaffUploadsStmt->bind_param('ss', $_GET['pf'],$_GET['stmt']);
                                        // Execute the statement
                                        $getCategorizedStaffUploadsStmt->execute();
                                        // Retrieve the result set directly from the prepared statement
                                        $getCategorizedStaffUploadsResult = $getCategorizedStaffUploadsStmt->get_result();
                                        // The value of the associative array
                                        while($getCategorizedStaffUploadsRow = $getCategorizedStaffUploadsResult->fetch_assoc()){
                                    ?>
                                        <div class="col-12 col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-header" style="background-color: rgba(0, 0, 0, .03);">
                                                    <?php
                                                        // SQL select statement
                                                        $getEvidenceNameSql = 'SELECT `evidence_type` FROM `achievement_evidence_form_data` WHERE `evidence_id`=?';
                                                        // Prepare the SQL statement
                                                        $getEvidenceNameSql = $dbConnection->prepare($getEvidenceNameSql);
                                                        // Bind parameters to the statement
                                                        $getEvidenceNameSql->bind_param('s', $getCategorizedStaffUploadsRow['evidence_id']);
                                                        // Execute the statement
                                                        $getEvidenceNameSql->execute();
                                                        // Retrieve the result set directly from the prepared statement
                                                        $getEvidenceNameSqlResult = $getEvidenceNameSql->get_result();
                                                        // The value of the associative array
                                                        $getEvidenceNameSqlRow = $getEvidenceNameSqlResult->fetch_assoc();
                                                        // Display the value of the associative array
                                                        echo $getEvidenceNameSqlRow['evidence_type'];
                                                    ?>
                                                </div>
                                                <div class="card-body">
                                                    <?php
                                                        // initial number
                                                        $count01 = 1;
                                                        // SQL select statement
                                                        $getEvidenceSql = 'SELECT `cod_score`, `upload_id` FROM `achievement_evidence` WHERE `user_Pf_Number`=? AND `attribute_id`=? AND `evidence_id`=? ORDER BY `datePosted` ASC';
                                                        // Prepare the SQL statement with a parameter placeholder
                                                        $getEvidenceStmt = $dbConnection->prepare($getEvidenceSql);
                                                        // Bind parameters to the statement
                                                        $getEvidenceStmt->bind_param('sss', $_GET['pf'],$_GET['stmt'],$getCategorizedStaffUploadsRow['evidence_id']);
                                                        // Execute the statement
                                                        $getEvidenceStmt->execute();
                                                        // Retrieve the result set directly from the prepared statement
                                                        $getEvidenceResult = $getEvidenceStmt->get_result();
                                                    ?>
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Evidence</th>
                                                                <th>Score</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                // The value of the associative array
                                                                while($getEvidenceRow = $getEvidenceResult->fetch_assoc()){
                                                            ?>
                                                                <tr>
                                                                    <td>
                                                                        <a href="<?php echo 'self_assessment_evidences/'.$getEvidenceRow['upload_id'].''?>" target="_blank" class="text-decoration-none text-dark">
                                                                            <?php
                                                                                $newCount = $count01++;
                                                                                if ($newCount<10) {
                                                                                    echo '0'.$newCount.'. '.$getEvidenceNameSqlRow['evidence_type'].' <i class="fa-solid fa-file-pdf fa-lg" style="color: #bf3636;"></i></i';
                                                                                }else{
                                                                                    echo $newCount.'. '.$getEvidenceNameSqlRow['evidence_type'].' <i class="fa-solid fa-file-pdf fa-lg" style="color: #bf3636;"></i></i';
                                                                                }
                                                                            ?>
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                            if (empty($getEvidenceRow['cod_score'])==true){
                                                                                echo '---';
                                                                            }
                                                                            else{
                                                                                echo $getEvidenceRow['cod_score'];
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="card-footer text-muted">
                                                    Achievement evidence upload
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
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