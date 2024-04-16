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
        $selectSystemAccessSql = "SELECT `is_cod`,`system_access` FROM `users` WHERE `user_Pf_Number`=?";
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
            $isCodValueRow = $systemAccessValue['is_cod'];
        }
        // if is not set to integer 5, redirect to login.php showing "Please contact the administrator for assistence" error message and exit
        if ($systemAccessValueRow !== 5 || $isCodValueRow !== 6)
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
            $sql = "SELECT `user_Pf_Number` FROM `expected_achievement` WHERE `user_Pf_Number`=?";
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
                $sql = "SELECT `user_Pf_Number` FROM `self_achievement` WHERE `user_Pf_Number`=?";
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
                else
                {
                    // SQL select statement
                    $sql = "SELECT `expected_achievement_id` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?";
                    // Prepare the SQL statement
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('s', $_GET['stmt']);
                    // Execute the statement
                    $stmt->execute();
                    // Store the result set
                    $stmt->store_result();
                    // else if the number of rows is not identical to integer 20, redirect to self_assessment.php web page
                    if ($stmt->num_rows!==1)
                    {
                        header('Location: achievement');
                        exit();
                    }
                }
            }
        }

        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitEvidence' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitEvidence']) == true)
        {
            $attribute_id = trim(mysqli_real_escape_string($dbConnection, $_GET['stmt']));
            $attribute_id = htmlspecialchars(mysqli_real_escape_string($dbConnection,trim($attribute_id)), ENT_QUOTES, 'UTF-8');

            $evidence_id = trim(mysqli_real_escape_string($dbConnection, $_POST['evidenceType']));
            $evidence_id = htmlspecialchars(mysqli_real_escape_string($dbConnection,trim($evidence_id)), ENT_QUOTES, 'UTF-8');

            // SQL select statement
            $validateEvidenceIdSQL = "SELECT `evidence_id` FROM `achievement_evidence_form_data` WHERE `expected_achievement_id`=? AND `evidence_id`=?";
            // Prepare the SQL statement
            $validateEvidenceIdStmt = $dbConnection->prepare($validateEvidenceIdSQL);
            // Bind parameters to the statement
            $validateEvidenceIdStmt->bind_param('ss', $attribute_id,$evidence_id);
            // Execute the statement
            $validateEvidenceIdStmt->execute();
            // Store result
            $validateEvidenceIdStmt->store_result();

            if (empty($attribute_id)==true){
                header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=empty_attribute_id');
                exit();
            }
            elseif (empty($evidence_id)==true){
                header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=empty_evidenceType');
                exit();
            }
            elseif ($validateEvidenceIdStmt->num_rows!==1){
                header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=invalid_attribute_id');
                exit();
            }
            else{
                // SQL select statement
                $validateExpectedAchievementIdSql = "SELECT `expected_achievement_id` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?";
                // Prepare the SQL statement
                $validateExpectedAchievementIdStmt = $dbConnection->prepare($validateExpectedAchievementIdSql);
                // Bind parameters to the statement
                $validateExpectedAchievementIdStmt->bind_param('s', $attribute_id);
                // Execute the statement
                $validateExpectedAchievementIdStmt->execute();
                // Store result
                $validateExpectedAchievementIdStmt->store_result();
                // if number of rows is not identical to integer one
                if ($validateExpectedAchievementIdStmt->num_rows!==1){
                    header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=invalid_attribute_id');
                    exit();
                }
                else{
                    // SQL select statement
                    $validateEvidenceTypeSql = "SELECT `evidence_type` FROM `achievement_evidence_form_data` WHERE `expected_achievement_id`=? AND `evidence_id`=?";
                    // Prepare the SQL statement
                    $validateEvidenceTypeStmt = $dbConnection->prepare($validateEvidenceTypeSql);
                    // Bind parameters to the statement
                    $validateEvidenceTypeStmt->bind_param('ss', $attribute_id,$evidence_id);
                    // Execute the statement
                    $validateEvidenceTypeStmt->execute();
                    // Store result
                    $validateEvidenceTypeStmt->store_result();
                    // if number of rows is not identical to integer one
                    if ($validateEvidenceTypeStmt->num_rows!==1){
                        header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=invalid_evidence_id');
                        exit();
                    }
                    else{
                        $nameOfTheUpload = $_FILES['evidence']['name'];
                        $temporaryNameOfTheUpload = $_FILES['evidence']['tmp_name'];
                        $sizeOfTheUpload = $_FILES['evidence']['size'];
                        $errorOfTheUpload = $_FILES['evidence']['error'];
                        $typeOfTheUpload = $_FILES['evidence']['type'];

                        $fileExtension = explode('.',$nameOfTheUpload);
                        $fileActualExtension = strtolower(end($fileExtension));

                        if ($fileActualExtension!=="pdf"){
                            header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=File_type_is_not_allowed');
                            exit();
                        }
                        elseif ($errorOfTheUpload!==0){
                            header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=There_was_an_error_uploading_file');
                            exit();
                        }
                        // Check file size (in this example, maximum file size is 5MB)
                        elseif ($sizeOfTheUpload > 5000000){
                            header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=Your_file_is_too_big');
                            exit();
                        }
                        else{
                            // SQL select statement
                            $selectRefSql = "SELECT `Ref` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?";
                            // Prepare the SQL statement
                            $selectRefStmt = $dbConnection->prepare($selectRefSql);
                            // Bind parameters to the statement
                            $selectRefStmt->bind_param('s', $attribute_id);
                            // Execute the statement
                            $selectRefStmt->execute();
                            // Retrieve the result set directly from the prepared statement
                            $selectRefResult = $selectRefStmt->get_result();
                            // Fetch a single row as an associative array
                            $selectRefRow = $selectRefResult->fetch_assoc();
                            $ref = $selectRefRow['Ref'];

                            // SQL select statement
                            $selectEvidenceTypeSql = "SELECT `evidence_type` FROM `achievement_evidence_form_data` WHERE `expected_achievement_id`=? AND `evidence_id`=?";
                            // Prepare the SQL statement
                            $selectEvidenceTypeStmt = $dbConnection->prepare($selectEvidenceTypeSql);
                            // Bind parameters to the statement
                            $selectEvidenceTypeStmt->bind_param('ss', $attribute_id,$evidence_id);
                            // Execute the statement
                            $selectEvidenceTypeStmt->execute();
                            // Retrieve the result set directly from the prepared statement
                            $selectEvidenceTypeResult = $selectEvidenceTypeStmt->get_result();
                            // Fetch a single row as an associative array
                            $selectEvidenceTypeRow = $selectEvidenceTypeResult->fetch_assoc();
                            $evidence_type = $selectEvidenceTypeRow['evidence_type'];

                            $finalName = trim(mysqli_real_escape_string($dbConnection,'01_'.$ref.'_'.$evidence_type.'_'.$_SESSION['user_Pf_Number'].'.'.$fileActualExtension));
                            $upload_id = html_entity_decode($finalName, ENT_QUOTES, 'UTF-8');
                            $directory_upload_id = "self_assessment_evidences/".$upload_id."";

                            if (file_exists($directory_upload_id)==true){
                                // select the latest staff upload from the database
                                // SQL SELECT statement
                                $selectLatestUploadSql = "SELECT MAX(`upload_id`) AS `max_upload_id` FROM `achievement_evidence` WHERE `user_Pf_Number`=? AND `attribute_id`=? AND `evidence_id`=?";
                                // Prepare the SQL SELECT statement
                                $selectLatestUploadStmt = $dbConnection->prepare($selectLatestUploadSql);
                                // Bind parameters to the statement
                                $selectLatestUploadStmt->bind_param('sss', $_SESSION['user_Pf_Number'],$attribute_id,$evidence_id);
                                // Execute the statement
                                $selectLatestUploadStmt->execute();
                                // Retrieve the result set directly from the prepared statement
                                $selectLatestUploadResult = $selectLatestUploadStmt->get_result();
                                // Fetch a single row as an associative array
                                $selectLatestUploadRow = $selectLatestUploadResult->fetch_assoc();
                                // Latest staff upload
                                $latestStaffUpload = $selectLatestUploadRow['max_upload_id'];

                                // $latestStaffUpload is a variable containing a string that includes underscores.
                                // This line of code splits the string into an array of strings, where each element is a part of the original string that was separated by an underscore.
                                $latestStaffUploadStart = explode('_', $latestStaffUpload);
                                // This line of code retrieves the first element from the array created by the explode() function, which is the first part of the original string that appears before the first underscore.
                                $uploadActualStart = reset($latestStaffUploadStart);
                                // convert variable uploadActualStart to an integer
                                $uploadActualStartInt = (int)$uploadActualStart;
                                // add integer one to variable uploadActualStart
                                $newUploadActualStartInt = $uploadActualStartInt+1;

                                // If newUploadActualStartInt is less than integer ten add prefix zero and convert to string
                                if ($newUploadActualStartInt<10)
                                {
                                    // upload start value as a string
                                    $newUploadActualStartStr = strval('0'.$newUploadActualStartInt.'');
                                }
                                // else if newUploadActualStartInt is equal to or greater than integer ten, maintain the nunber and convert to string
                                else
                                {
                                    // upload start value as a string
                                    $newUploadActualStartStr = strval($newUploadActualStartInt.'');
                                }

                                $finalName = trim(mysqli_real_escape_string($dbConnection,$newUploadActualStartStr.'_'.$ref.'_'.$evidence_type.'_'.$_SESSION['user_Pf_Number'].'.'.$fileActualExtension));
                                $upload_id = html_entity_decode($finalName, ENT_QUOTES, 'UTF-8');

                                // SQL SELECT statement
                                $checkUploadIdSql = "SELECT `upload_id` FROM `achievement_evidence` WHERE `upload_id`=?";
                                // Prepate the SQL SELECT statement
                                $checkUploadIdStmt = $dbConnection->prepare($checkUploadIdSql);
                                // Bind parameters
                                $checkUploadIdStmt->bind_param("s", $upload_id);
                                // Execute the statement
                                $checkUploadIdStmt->execute();
                                // Store result
                                $checkUploadIdStmt->store_result();
                                // if number of rows is identical to integer one
                                if ($checkUploadIdStmt->num_rows==1){
                                    header("Location: achievement_evidence_upload?stmt=".$_GET["stmt"]."&aeuError=upload_exists_in_the_database&file=".$upload_id."");
                                    exit();
                                }
                                else{
                                    // the loation of uploading the file to
                                    $fileUploadDestination = "self_assessment_evidences/".$upload_id."";
                                    // if the file is successfully moved to its new location
                                    if(move_uploaded_file($temporaryNameOfTheUpload,$fileUploadDestination) == true){
                                        // the date of posting
                                        $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));
                                        // SQL INSERT statement
                                        $uploadEvidenceSQL = "INSERT INTO `achievement_evidence`(`user_Pf_Number`, `attribute_id`, `upload_id`, `evidence_id`, `datePosted`) VALUES (?,?,?,?,?)";
                                        // Prepare the SQL statement
                                        $uploadEvidenceStmt = $dbConnection->prepare($uploadEvidenceSQL);
                                        // Bind parameters to the statement
                                        $uploadEvidenceStmt->bind_param('sssss',$_SESSION['user_Pf_Number'],$attribute_id,$upload_id,$evidence_id,$datePosted);
                                        // if the statement is executed
                                        if ($uploadEvidenceStmt->execute())
                                        {
                                            header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuSuccess=file_uploaded');
                                            exit();
                                        }
                                        else{
                                            header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=file_not_saved');
                                            exit();
                                        }
                                    }
                                    else{
                                        header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=file_not_uploaded');
                                        exit();
                                    }
                                }
                            }
                            else{
                                // SQL SELECT statement
                                $checkUploadIdSql = "SELECT `upload_id` FROM `achievement_evidence` WHERE `upload_id`=?";
                                // Prepate the SQL SELECT statement
                                $checkUploadIdStmt = $dbConnection->prepare($checkUploadIdSql);
                                // Bind parameters
                                $checkUploadIdStmt->bind_param("s", $upload_id);
                                // Execute the statement
                                $checkUploadIdStmt->execute();
                                // Store result
                                $checkUploadIdStmt->store_result();
                                // if number of rows is identical to integer one
                                if ($checkUploadIdStmt->num_rows==1){
                                    header("Location: achievement_evidence_upload?stmt=".$_GET["stmt"]."&aeuError=upload_exists_in_the_database&file=".$upload_id."");
                                    exit();
                                }
                                else{
                                    // the loation of uploading the file to
                                    $fileUploadDestination = "self_assessment_evidences/".$upload_id."";
                                    // if the file is successfully moved to its new location
                                    if(move_uploaded_file($temporaryNameOfTheUpload,$fileUploadDestination) == true){
                                        // the date of posting
                                        $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));
                                        // SQL INSERT statement
                                        $uploadEvidenceSQL = "INSERT INTO `achievement_evidence`(`user_Pf_Number`, `attribute_id`, `upload_id`, `evidence_id`, `datePosted`) VALUES (?,?,?,?,?)";
                                        // Prepare the SQL statement
                                        $uploadEvidenceStmt = $dbConnection->prepare($uploadEvidenceSQL);
                                        // Bind parameters to the statement
                                        $uploadEvidenceStmt->bind_param('sssss',$_SESSION['user_Pf_Number'],$attribute_id,$upload_id,$evidence_id,$datePosted);
                                        // if the statement is executed
                                        if ($uploadEvidenceStmt->execute())
                                        {
                                            header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuSuccess=file_uploaded');
                                            exit();
                                        }
                                        else{
                                            header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=file_not_saved');
                                            exit();
                                        }
                                    }
                                    else{
                                        header('Location: achievement_evidence_upload?stmt='.$_GET['stmt'].'&aeuError=file_not_uploaded');
                                        exit();
                                    }
                                }
                            }
                        }
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
            <title>epes | Achievement Evidence Upload</title>
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
                                    <h1>
                                        <?php
                                            // SQL select statement
                                            $selectRefSql = 'SELECT `Ref` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                                            // Prepare the SQL statement
                                            $selectRefStmt = $dbConnection->prepare($selectRefSql);
                                            // Bind parameters to the statement
                                            $selectRefStmt->bind_param('s', $_GET['stmt']);
                                            // Execute the statement
                                            $selectRefStmt->execute();
                                            // Retrieve the result set directly from the prepared statement
                                            $selectRefResult = $selectRefStmt->get_result();
                                            // Fetch a single row as an associative array
                                            $selectRefRow = $selectRefResult->fetch_assoc();
                                            // Display the value of the associative array
                                            echo $selectRefRowValue = $selectRefRow["Ref"];
                                        ?>
                                    </h1>
                                </div>
                            </div>
                            <hr class="border-primary">
                            <a href="achievement" type="button" class="btn btn-sm btn-primary text-light">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="container col-sm-6 shadow-sm border mx-auto">
                        <div class="p-2">
                            <form action="" method="post" enctype="multipart/form-data" onsubmit="return submitEvidenceJsValidation()">
                                <div class="card-header text-center font-weight-bold">
                                    <span>Upload evidence</span>
                                    <div id="evidenceStatusDiv">
                                        <?php if (isset($_GET['aeuError'])==true && $_GET['aeuError']=="empty_attribute_id"){ ?>
                                            <span class="text-danger d-block">No attribute <br> Please try again</span>
                                        <?php }elseif (isset($_GET['aeuError'])==true && $_GET['aeuError']=="invalid_attribute_id"){ ?>
                                            <span class="text-danger d-block">Invalid attribute <br> Please try again</span>
                                        <?php }elseif (isset($_GET['aeuError'])==true && $_GET['aeuError']=="invalid_evidence_id"){ ?>
                                            <span class="text-danger d-block">Invalid evidence attribute <br> Please try again</span>
                                        <?php } elseif (isset($_GET['aeuError'])==true && $_GET['aeuError']=="empty_evidenceType"){ ?>
                                            <span class="text-danger d-block">Select the type of evidence</span>
                                        <?php } elseif (isset($_GET['aeuError'])==true && $_GET['aeuError']=="File_type_is_not_allowed"){ ?>
                                            <span class="text-danger d-block">Only <code>pdf</code> files are allowed.</span>
                                        <?php } elseif (isset($_GET['aeuError'])==true && $_GET['aeuError']=="There_was_an_error_uploading_file"){ ?>
                                            <span class="text-danger d-block">There was an error uploading file.<br>Please try again.</span>
                                        <?php } elseif (isset($_GET['aeuError'])==true && $_GET['aeuError']=="Your_file_is_too_big"){ ?>
                                            <span class="text-danger d-block">Only <code>pdf</code> files upto 5MB size are allowed.</span>
                                        <?php } elseif (isset($_GET['aeuError'])==true && $_GET['aeuError']=="upload_exists_in_the_database"){ ?>
                                            <span class="text-danger d-block">Upload exists in the database.<br>Contact admin or tech support.<br>File:<?php echo html_entity_decode($_GET['file'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php } elseif (isset($_GET['aeuError'])==true && $_GET['aeuError']=="file_not_saved"){ ?>
                                            <span class="text-danger d-block">There was an error saving file.<br>Please try again.</span>
                                        <?php } elseif (isset($_GET['aeuError'])==true && $_GET['aeuError']=="file_not_uploaded"){ ?>
                                            <span class="text-danger d-block">There was an error uploading file.<br>Please contact admin or tech support.</span>
                                        <?php } elseif (isset($_GET['aeuSuccess'])==true && $_GET['aeuSuccess']=="file_uploaded"){ ?>
                                            <span class="text-success d-block">The file has been uploaded successfully.</span>
                                            <script>$(document).ready(function () {$("#messageModal").modal('show');});</script>
                                        <?php } ?>
                                    </div>
                                </div>
                                <dl>
                                    <dd class="font-weight-bold"><label for="evidence">Evidence</label></dd>
                                    <span id="evidenceStatus" class="d-block"></span>
                                    <dt>
                                        <button type="button" class="btn btn-primary" name="evidenceButton" id="evidenceButton" onclick="$('#evidence').click()">
                                            Select file
                                        </button>
                                        <i name="checkIconForChange" id="checkIconForChange" style="display:none;"></i>
                                    </dt>
                                    <input type="file" name="evidence" id="evidence" style="display:none;" onchange="handleFileSelect()">
                                    <script>
                                        function handleFileSelect() {
                                            var fileInput = document.getElementById('evidence');
                                            var checkIcon = document.getElementById('checkIconForChange');

                                            var selectedFile = fileInput.files[0];
                                            var acceptedTypes = ['application/pdf'];

                                            // Check if a file is selected and if it's a PDF
                                            if (selectedFile && acceptedTypes.includes(selectedFile.type)) {
                                                checkIcon.style.display = 'inline';
                                                checkIcon.className = 'ml-1 text-success fa-solid fa-circle-check';
                                            } else {
                                                checkIcon.style.display = 'inline';
                                                checkIcon.className = 'ml-1 text-danger fa-solid fa-circle-xmark';
                                            }
                                        }
                                    </script>
                                </dl>
                                <dl>
                                    <dd class="font-weight-bold"><label for="evidenceType">Selest the type of evidence <span class="text-dangerml">*</span></label></dd>
                                    <?php
                                        $selectEvidenceTypeSQL ='SELECT `evidence_id`, `evidence_type` FROM `achievement_evidence_form_data` WHERE `expected_achievement_id` = ? ORDER BY `evidence_type` ASC';
                                        $selectEvidenceTypeSTMT = $dbConnection->prepare($selectEvidenceTypeSQL);
                                        $selectEvidenceTypeSTMT->bind_param('s', $_GET['stmt']);
                                        $selectEvidenceTypeSTMT->execute();
                                        $selectEvidenceTypeResult = $selectEvidenceTypeSTMT->get_result();
                                        if ($selectEvidenceTypeResult->num_rows > 0)
                                        {
                                            $EvidenceType= mysqli_fetch_all($selectEvidenceTypeResult, MYSQLI_ASSOC);
                                        }
                                    ?>
                                    <td>
                                        <span id="evidenceTypeStatus" class="d-block"></span>
                                        <select name="evidenceType" id="evidenceType" class="form-control form-control-sm">
                                            <option value="">Select type of evidence</option>
                                                <?php foreach ($EvidenceType as $EvidenceType) { ?>
                                                    <option value="<?php echo $EvidenceType['evidence_id']; ?>">
                                                        <?php echo $EvidenceType['evidence_type']; ?>
                                                    </option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </dl>
                                <button type="submit" id="submitEvidence" name="submitEvidence" class="btn btn-sm btn-block btn-primary">Submit</button>
                                <button type="button" id="loadingSubmitEvidence" name="loadingSubmitEvidence" class="btn btn-sm btn-block btn-primary d-none">
                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                </button>
                            </form>

                            <!-- Modal -->
                            <div data-backdrop="static" data-keyboard="false" class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalTitle" aria-hidden="true" style="-webkit-user-select:none;-ms-user-select:none;user-select:none;">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="messageModalTitle">
                                                <span class="text-success d-block">Success<i class="ml-1 text-success fa-solid fa-circle-check"></i></span>
                                            </h5>
                                            <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </span>
                                        </div>
                                        <div class="modal-body">
                                            The file has been uploaded successfully<br>What's next?<br>
                                            <div class="mt-4">
                                                <hr>
                                                <a href="achievement_evidence_upload?stmt=<?php echo $_GET['stmt']; ?>" type="button" class="btn btn-secondary" style="float:left !important;">Continue uploading</a>
                                                <a href="achievement" type="button" class="btn btn-primary" style="float:right !important;">Back to achievement</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                    function submitEvidenceJsValidation()
                                {
                                    // Set the value of is_input_valid to be true by default
                                    var is_input_valid = true;

                                    document.getElementById('submitEvidence').className = "d-none";
                                    document.getElementById('loadingSubmitEvidence').className = "btn btn-primary btn-block";

                                    if (document.getElementById('evidence').value === "")
                                    {
                                        is_input_valid = false;
                                        document.getElementById('evidenceStatusDiv').className = "d-none";
                                        document.getElementById('evidenceStatus').style.color = "#dc3545";
                                        document.getElementById('evidenceStatus').innerHTML = "Please select evidence";
                                        // no loader
                                        document.getElementById('loadingSubmitEvidence').className = "d-none";
                                        document.getElementById('submitEvidence').className = "btn btn-primary btn-block";
                                    }
                                    else{
                                        document.getElementById('evidenceStatus').style.color = "#fff";
                                        document.getElementById('evidenceStatus').innerHTML = "";
                                    }

                                    if (document.getElementById('evidenceType').value === "")
                                    {
                                        is_input_valid = false;
                                        document.getElementById('evidenceStatusDiv').className = "d-none";
                                        document.getElementById('evidenceTypeStatus').style.color = "#dc3545";
                                        document.getElementById('evidenceTypeStatus').innerHTML = "Please select type of evidence";
                                        // no loader
                                        document.getElementById('loadingSubmitEvidence').className = "d-none";
                                        document.getElementById('submitEvidence').className = "btn btn-primary btn-block";
                                    }
                                    else{
                                        document.getElementById('evidenceTypeStatus').style.color = "#fff";
                                        document.getElementById('evidenceTypeStatus').innerHTML = "";
                                        document.getElementById('evidenceType').style.border = "1px solid #198754";
                                    }
                                    
                                    return is_input_valid;
                                }
                            </script>
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