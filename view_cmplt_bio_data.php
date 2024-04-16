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
        else{
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | View bio data</title>
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
                                        <h1 class="m-0">View bio data</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <?php
                                        // Prepare a SQL statement with a parameter placeholder
                                        $selectStaffBiodatasStmt = $dbConnection->prepare('SELECT * FROM `bio_data` WHERE PF_number=?');
                                        // Bind parameters to the statement
                                        $selectStaffBiodatasStmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                        // Execute the statement
                                        $selectStaffBiodatasStmt->execute();
                                        // Retrieve the result set
                                        $selectStaffBiodatasResult = $selectStaffBiodatasStmt->get_result();
                                        // Fetch data
                                        while ($selectStaffBiodataDetail = $selectStaffBiodatasResult->fetch_assoc())
                                        {
                                    ?>
                                            <form action="" method="post">
                                                <div class="row">
                                                    <div class="col-md-6 border-right">
                                                        <div class="form-group">
                                                            <label for="employeePfNumber" class="control-label">PF number <span class="text-danger">*</span></label>
                                                            <span id="employeePfNumberStatus" class="d-block"></span>
                                                            <input type="text" name="employeePfNumber" id="employeePfNumber" class="form-control form-control-sm" autocomplete="off" value="<?php echo $selectStaffBiodataDetail['PF_number']; ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeFirstName" class="control-label">First Name <span class="text-danger">*</span></label>
                                                            <span id="employeeFirstNameStatus" class="d-block"></span>
                                                            <input type="text" name="employeeFirstName" id="employeeFirstName" class="form-control form-control-sm" autocomplete="off" value="<?php echo $selectStaffBiodataDetail['First_Name']; ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeMiddleName" class="control-label">Middle Name (optional)</label>
                                                            <input type="text" name="employeeMiddleName" id="employeeMiddleName" class="form-control form-control-sm" autocomplete="off" value="<?php echo $selectStaffBiodataDetail['Middle_Name']; ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeLastName" class="control-label">Last Name <span class="text-danger">*</span></label>
                                                            <span id="employeeLastNameStatus" class="d-block"></span>
                                                            <input type="text" name="employeeLastName" id="employeeLastName" class="form-control form-control-sm" autocomplete="off" value="<?php echo $selectStaffBiodataDetail['Last_Name']; ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeDepartment" class="control-label">Department <span class="text-danger">*</span></label>
                                                            <span id="employeeDepartmentStatus" class="d-block"></span>
                                                            <?php
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $getDepartmentNameStmt = $dbConnection->prepare('SELECT `department_name` FROM `departments` WHERE department_id=?');
                                                                // Bind parameters to the statement
                                                                $getDepartmentNameStmt->bind_param('s', $selectStaffBiodataDetail['Department_id']);
                                                                // Execute the statement
                                                                $getDepartmentNameStmt->execute();
                                                                // Retrieve the result set
                                                                $getDepartmentNameResult = $getDepartmentNameStmt->get_result();
                                                                // Fetch data
                                                                $getDepartmentNameResultDetail = $getDepartmentNameResult->fetch_assoc();
                                                            ?>
                                                            <input type="text" name="employeeDepartment" id="employeeDepartment" class="form-control form-control-sm" autocomplete="off" value="<?php echo $getDepartmentNameResultDetail['department_name']; ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employementNature" class="control-label">Nature of employment  <span class="text-danger">*</span></label>
                                                            <span id="employementNatureStatus" class="d-block"></span>
                                                            <?php
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $getEmployementNatureStmt = $dbConnection->prepare('SELECT `nature_Of_Employement` FROM `natures_of_employment` WHERE emp_nature_id=?');
                                                                // Bind parameters to the statement
                                                                $getEmployementNatureStmt->bind_param('s', $selectStaffBiodataDetail['Nature_of_employment']);
                                                                // Execute the statement
                                                                $getEmployementNatureStmt->execute();
                                                                // Retrieve the result set
                                                                $getEmployementNatureResult = $getEmployementNatureStmt->get_result();
                                                                // Fetch data
                                                                $getEmployementNatureResultDetail = $getEmployementNatureResult->fetch_assoc();
                                                            ?>
                                                            <input type="text" name="employementNature" id="employementNature" class="form-control form-control-sm" autocomplete="off" value="<?php echo $getEmployementNatureResultDetail['nature_Of_Employement']; ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeDesination" class="control-label">Designation  <span class="text-danger">*</span></label>
                                                            <span id="employeeDesinationStatus" class="d-block"></span>
                                                            <input type="text" name="employeeDesination" id="employeeDesination" class="form-control form-control-sm" autocomplete="off" value="<?php echo $selectStaffBiodataDetail['Desination_id']; ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeJobDescription" class="control-label">Job description <span class="text-danger">*</span></label>
                                                            <span id="employeeJobDescriptionStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="employeeJobDescription" id="employeeJobDescription" autocomplete="off" cols="30" rows="4" readonly>
                                                                <?php
                                                                    $content= (string)$selectStaffBiodataDetail['Job_description'];
                                                                    echo nl2br($content);
                                                                ?>
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="qualifications" class="control-label">Qualifications/Technical Skills (specify) <span class="text-danger">*</span></label>
                                                            <span id="qualificationsStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="qualifications" id="qualifications" autocomplete="off" cols="30" rows="4" readonly>
                                                                <?php
                                                                    $content= (string)$selectStaffBiodataDetail['Qualifications'];
                                                                    echo nl2br($content);
                                                                ?>
                                                            </textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="strengths" class="control-label">List your main strengths <span class="text-danger">*</span></label>
                                                            <span id="strengthsStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="strengths" id="strengths" autocomplete="off" cols="30" rows="4" readonly>
                                                                <?php
                                                                    $content= (string)$selectStaffBiodataDetail['strengths'];
                                                                    echo nl2br($content);
                                                                ?>
                                                            </textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="contributions" class="control-label">List below your outstanding contributions to the Department/Division/University: <span class="text-danger">*</span></label>
                                                            <span id="contributionsStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="contributions" id="contributions" autocomplete="off" cols="30" rows="4" readonly>
                                                                <?php
                                                                    $content= (string)$selectStaffBiodataDetail['contributions'];
                                                                    echo nl2br($content);
                                                                ?>
                                                            </textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="difficulties" class="control-label">Are there any areas of your work which you have difficulties and would like to have further training or support? <span class="text-danger">*</span></label>
                                                            <span id="radioSelectionStatus" class="d-block"></span>
                                                            <?php
                                                                if (empty($selectStaffBiodataDetail['difficulties'])==true){
                                                            ?>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="radioSelection" id="radioYes" value="radioYes" disabled>
                                                                    <label class="form-check-label font-weight-bold" for="radioYes">Yes</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="radioSelection" id="radioNo" value="radioNo" checked disabled>
                                                                    <label class="form-check-label font-weight-bold" for="radioNo">No</label>
                                                                </div>
                                                            <?php }elseif (empty($selectStaffBiodataDetail['difficulties'])==false){ ?>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="radioSelection" id="radioYes" value="radioYes" checked disabled>
                                                                    <label class="form-check-label font-weight-bold" for="radioYes">Yes</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="radioSelection" id="radioNo" value="radioNo" disabled>
                                                                    <label class="form-check-label font-weight-bold" for="radioNo">No</label>
                                                                </div>
                                                            <?php } ?>
                                                            <span id="difficultiesStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="difficulties" id="difficulties" autocomplete="off" cols="30" rows="4" readonly>
                                                                <?php
                                                                    $content= (string)$selectStaffBiodataDetail['difficulties'];
                                                                    echo nl2br($content);
                                                                ?>
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                    <?php } ?>
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