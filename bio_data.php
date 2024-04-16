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

         // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitEmployeeDetails' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitEmployeeDetails']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_employeePfNumber = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['employeePfNumber'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_employeePfNumber = htmlspecialchars($secure_employeePfNumber, ENT_QUOTES, 'UTF-8');
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_employeeFirstName = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['employeeFirstName'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_employeeFirstName = htmlspecialchars($secure_employeeFirstName, ENT_QUOTES, 'UTF-8');
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_employeeMiddleName = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['employeeMiddleName'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_employeeMiddleName = htmlspecialchars($secure_employeeMiddleName, ENT_QUOTES, 'UTF-8');
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_employeeLastName = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['employeeLastName'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_employeeLastName = htmlspecialchars($secure_employeeLastName, ENT_QUOTES, 'UTF-8');
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_employeeDepartment = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['employeeDepartment'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_employeeDepartment = htmlspecialchars($secure_employeeDepartment, ENT_QUOTES, 'UTF-8');
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_employeeDesination = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['employeeDesination'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_employeeDesination = htmlspecialchars($secure_employeeDesination, ENT_QUOTES, 'UTF-8');
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_employementNature = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['employementNature'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_employementNature = htmlspecialchars($secure_employementNature, ENT_QUOTES, 'UTF-8');
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_employeeJobDescription = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['employeeJobDescription'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_employeeJobDescription = htmlspecialchars($secure_employeeJobDescription, ENT_QUOTES, 'UTF-8');
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_qualifications = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['qualifications'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_qualifications = htmlspecialchars($secure_qualifications, ENT_QUOTES, 'UTF-8');
            
            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_strengths = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['strengths'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_strengths = htmlspecialchars($secure_strengths, ENT_QUOTES, 'UTF-8');

            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_contributions = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['contributions'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_contributions = htmlspecialchars($secure_contributions, ENT_QUOTES, 'UTF-8');

            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_radioSelection = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['radioSelection'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_radioSelection = htmlspecialchars($secure_radioSelection, ENT_QUOTES, 'UTF-8');

            // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the input before using it in an SQL query
            $secure_difficulties = trim(stripslashes(mysqli_real_escape_string($dbConnection,$_POST['difficulties'])));
            // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
            // both double and single quotes should be converted to HTML entities.
            $secure_difficulties = htmlspecialchars($secure_difficulties, ENT_QUOTES, 'UTF-8');
            

            if (empty($secure_employeePfNumber)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyEmployeePfNumber');
                exit();
            }
            elseif (empty($secure_employeeFirstName)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyFirstName');
                exit();
            }
            elseif (empty($secure_employeeLastName)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyLastName');
                exit();
            }
            elseif (empty($secure_employeeDepartment)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyDepartment');
                exit();
            }
            elseif (empty($secure_employeeDesination)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyDesination');
                exit();
            }
            elseif (empty($secure_employementNature)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyNature');
                exit();
            }
            elseif (empty($secure_employeeJobDescription)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyJobDescription');
                exit();
            }
            elseif (empty($secure_qualifications)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyQualifications');
                exit();
            }
            elseif (empty($secure_strengths)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyStrengths');
                exit();
            }
            elseif (empty($secure_contributions)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyContributions');
                exit();
            }
            elseif (empty($secure_radioSelection)==true)
            {
                header('Location: bio_data.php?bioDataError=emptyRadioSelection');
                exit();
            }
            elseif ($secure_radioSelection==='radioYes' && empty($secure_difficulties)==true || strlen($secure_difficulties)<=0)
            {
                header('Location: bio_data.php?bioDataError=emptyDifficulties');
                exit();
            }
            elseif ($_POST['employeePfNumber']!==$_SESSION['user_Pf_Number'])
            {
                header('Location: bio_data.php?bioDataError=invalidPfNumber');
                exit();
            }
            else
            {
                // SQL SELECT statement
                $departmentExistanceSql = 'SELECT `department_id` FROM `departments` WHERE `department_id`=?';
                // Prepare the SQL statement
                $departmentExistanceStmt = $dbConnection->prepare($departmentExistanceSql);
                // Bind parameters
                $departmentExistanceStmt->bind_param("s", $secure_employeeDepartment);
                // Execute the statement
                $departmentExistanceStmt->execute();
                // Store result
                $departmentExistanceStmt->store_result();
                if ($departmentExistanceStmt->num_rows!==1)
                {
                    header('Location: bio_data.php?bioDataError=invalidDepartment');
                    exit();
                }

                // SQL SELECT statement
                $employementNatureExistanceSql = 'SELECT `emp_nature_id` FROM `natures_of_employment` WHERE `emp_nature_id`=?';
                // Prepare the SQL statement
                $employementNatureExistanceStmt = $dbConnection->prepare($employementNatureExistanceSql);
                // Bind parameters
                $employementNatureExistanceStmt->bind_param("s", $secure_employementNature);
                // Execute the statement
                $employementNatureExistanceStmt->execute();
                // Store result
                $employementNatureExistanceStmt->store_result();
                if ($employementNatureExistanceStmt->num_rows!==1)
                {
                    header('Location: bio_data.php?bioDataError=invalidEmployementNature');
                    exit();
                }

                // SQL SELECT statement
                $pfNumberExistanceSql = 'SELECT `PF_number` FROM `bio_data` WHERE `PF_number`=?';
                // Prepare the SQL statement
                $pfNumberExistanceStmt = $dbConnection->prepare($pfNumberExistanceSql);
                // Bind parameters
                $pfNumberExistanceStmt->bind_param("s", $secure_employeePfNumber);
                // Execute the statement
                $pfNumberExistanceStmt->execute();
                // Store result
                $pfNumberExistanceStmt->store_result();
                if ($pfNumberExistanceStmt->num_rows>0)
                {
                    header('Location: bio_data.php?bioDataError=pfNumberExists');
                    exit();
                }
                
                // Remove whitespace or other predefined characters from the beginning and end, remove backslashes and escape the date before using it in an SQL query
                $secure_dateAdded = trim(stripslashes(mysqli_real_escape_string($dbConnection,date("Y-m-d H:i:s",strtotime($currentTime)))));
                // convert special characters to HTML entities. This helps prevent cross-site scripting (XSS) attacks by escaping characters that have special meaning in HTML
                // both double and single quotes should be converted to HTML entities.
                $secure_dateAdded = htmlspecialchars($secure_dateAdded, ENT_QUOTES, 'UTF-8');
                if (empty($secure_dateAdded)==true)
                {
                    header('Location: bio_data.php?bioDataError=emptydateSet');
                    exit();
                }

                // SQL INSERT statement
                $insertBioDataSql = 'INSERT INTO `bio_data`(`PF_number`, `First_Name`, `Middle_Name`, `Last_Name`, `Department_id`, `Desination_id`, `Nature_of_employment`, `Job_description`, `Qualifications`, `strengths`, `contributions`, `difficulties`, `dateAdded`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
                // Prepare the SQL statement
                $insertBioDataStmt = $dbConnection->prepare($insertBioDataSql);
                // Bind parameters to the statement
                $insertBioDataStmt->bind_param('sssssssssssss',$secure_employeePfNumber,$secure_employeeFirstName,$secure_employeeMiddleName,$secure_employeeLastName,$secure_employeeDepartment,$secure_employeeDesination,$secure_employementNature,$secure_employeeJobDescription,$secure_qualifications,$secure_strengths,$secure_contributions,$secure_difficulties,$secure_dateAdded);
                // if the statement is executed
                if ($insertBioDataStmt->execute())
                {
                    header('Location: bio_data.php?bioDataSuccess');
                    exit();
                }
                else
                {
                    header('Location: bio_data.php?bioDataError=tryAgain');
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
                <title>epes | Add bio data</title>
                <!-- include header.php php file -->
                <?php include 'header.php' ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <div class="wrapper">
                    <!-- include topbar.php php file -->
                    <?php include 'topbar.php' ?>
                    <!-- include sidebar.php php file -->
                    <?php include 'sidebar.php' ?>
                    <?php
                        // require connection to the functions.php page
                        require 'functions.php';
                    ?>

                    <div class="content-wrapper">
                        <div class="content-header">
                            <div class="container-fluid">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <h1 class="m-0">Add bio data</h1>
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
                                <div class="text-center card-header">
                                    <?php if (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyEmployeePfNumber'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter your PF number</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyFirstName'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter your first name</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyLastName'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter your last name</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyDepartment'){ ?>
                                            <span class="text-danger font-weight-bold">Please select your department</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyDesination'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter your desination</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyNature'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter the nature employement</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyJobDescription'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter your job description</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyQualifications'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter your qualifications</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyStrengths'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter your strengths</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyContributions'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter your contributions</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyRadioSelection'){ ?>
                                            <span class="text-danger font-weight-bold">Please select if there are any difficulties in area of your work</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptyDifficulties'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter the difficulties in your area of work</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'invalidPfNumber'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter PF number</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'invalidDepartment'){ ?>
                                            <span class="text-danger font-weight-bold">Please select a valid department</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'invalidEmployementNature'){ ?>
                                            <span class="text-danger font-weight-bold">Please select a valid nature of employment</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'pfNumberExists'){ ?>
                                            <span class="text-danger font-weight-bold">Bio data has already uploaded</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'emptydateSet'){ ?>
                                            <span class="text-danger font-weight-bold">Empty date set <br> Please contact technical support</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == true && empty($_GET['bioDataError']) == false && $_GET['bioDataError'] == 'tryAgain'){ ?>
                                            <span class="text-danger font-weight-bold">Bio data couldn't be added<br>Please try again</span>
                                        <?php }elseif (isset($_GET['bioDataError']) == false && empty($_GET['bioDataError']) == false && isset($_GET['bioDataSuccess']) == true){ ?>
                                            <span class="text-success font-weight-bold">Bio data added successfully</span>
                                        <?php } ?>
                                </div>
                                    <div class="card-body">
                                        <?php
                                            // Prepare the SQL statement
                                            $stmt = $dbConnection->prepare('SELECT `PF_number` FROM `bio_data` WHERE `PF_number`=?');
                                            // Bind parameters
                                            $stmt->bind_param("s", $_SESSION['user_Pf_Number']);
                                            // Execute the statement
                                            $stmt->execute();
                                            // Store result
                                            $stmt->store_result();
                                            if ($stmt->num_rows==1){
                                        ?>
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
                                        <?php }else{ ?>
                                            <form action="" method="post" onsubmit="return addstaffBioDataJsValidation();">
                                                <div class="row">
                                                    <div class="col-md-6 border-right">
                                                        <div class="form-group">
                                                            <label for="employeePfNumber" class="control-label">PF number <span class="text-danger">*</span></label>
                                                            <span id="employeePfNumberStatus" class="d-block"></span>
                                                            <input type="text" name="employeePfNumber" id="employeePfNumber" class="form-control form-control-sm" autocomplete="off" autofocus>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeFirstName" class="control-label">First Name <span class="text-danger">*</span></label>
                                                            <span id="employeeFirstNameStatus" class="d-block"></span>
                                                            <input type="text" name="employeeFirstName" id="employeeFirstName" class="form-control form-control-sm" autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeMiddleName" class="control-label">Middle Name (optional)</label>
                                                            <input type="text" name="employeeMiddleName" id="employeeMiddleName" class="form-control form-control-sm" autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeLastName" class="control-label">Last Name <span class="text-danger">*</span></label>
                                                            <span id="employeeLastNameStatus" class="d-block"></span>
                                                            <input type="text" name="employeeLastName" id="employeeLastName" class="form-control form-control-sm" autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <?php
                                                                $activeDepartmentStatus = 5;
                                                                $selectDepartmentsSQL ='SELECT `department_id`, `department_name` FROM `departments` WHERE `department_status`=? ORDER BY `department_name` ASC';
                                                                $selectDepartmentsSTMT = $dbConnection->prepare($selectDepartmentsSQL);
                                                                $selectDepartmentsSTMT->bind_param('i', $activeDepartmentStatus);
                                                                $selectDepartmentsSTMT->execute();
                                                                $selectDepartmentsResult = $selectDepartmentsSTMT->get_result();
                                                                if ($selectDepartmentsResult->num_rows > 0)
                                                                {
                                                                    $department= mysqli_fetch_all($selectDepartmentsResult, MYSQLI_ASSOC);
                                                                }
                                                            ?>
                                                            <label for="employeeDepartment" class="control-label">Department <span class="text-danger">*</span></label>
                                                            <span id="employeeDepartmentStatus" class="d-block"></span>
                                                            <select name="employeeDepartment" id="employeeDepartment" class="form-control form-control-sm">
                                                                <option value="">Select department</option>
                                                                    <?php foreach ($department as $department) { ?>
                                                                        <option value="<?php echo $department['department_id']; ?>">
                                                                            <?php echo '('.$department['department_id'].') '.$department['department_name']; ?>
                                                                        </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <?php
                                                                $selectEmpNatureSQL ='SELECT `emp_nature_id`, `nature_Of_Employement` FROM `natures_of_employment` ORDER BY `nature_Of_Employement` ASC';
                                                                $selectEmpNatureSTMT = $dbConnection->prepare($selectEmpNatureSQL);
                                                                $selectEmpNatureSTMT->execute();
                                                                $selectEmpNatureResult = $selectEmpNatureSTMT->get_result();
                                                                if ($selectEmpNatureResult->num_rows > 0)
                                                                {
                                                                    $empNature= mysqli_fetch_all($selectEmpNatureResult, MYSQLI_ASSOC);
                                                                }
                                                            ?>
                                                            <label for="employementNature" class="control-label">Nature of employment <span class="text-danger">*</span></label>
                                                            <span id="employementNatureStatus" class="d-block"></span>
                                                            <select name="employementNature" id="employementNature" class="form-control form-control-sm">
                                                                <option value="">Nature of employment</option>
                                                                    <?php foreach ($empNature as $empNature) { ?>
                                                                        <option value="<?php echo $empNature['emp_nature_id']; ?>">
                                                                            <?php echo $empNature['nature_Of_Employement']; ?>
                                                                        </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeDesination" class="control-label">Desination  <span class="text-danger">*</span></label>
                                                            <span id="employeeDesinationStatus" class="d-block"></span>
                                                            <input type="text" name="employeeDesination" id="employeeDesination" class="form-control form-control-sm" autocomplete="off" autofocus>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="employeeJobDescription" class="control-label">Job description <span class="text-danger">*</span></label>
                                                            <span id="employeeJobDescriptionStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="employeeJobDescription" id="employeeJobDescription" autocomplete="off" cols="30" rows="4"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="qualifications" class="control-label">Qualifications/Technical Skills (specify) <span class="text-danger">*</span></label>
                                                            <span id="qualificationsStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="qualifications" id="qualifications" autocomplete="off" cols="30" rows="4"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="strengths" class="control-label">List your main strengths <span class="text-danger">*</span></label>
                                                            <span id="strengthsStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="strengths" id="strengths" autocomplete="off" cols="30" rows="4"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="contributions" class="control-label">List below your outstanding contributions to the Department/Division/University: <span class="text-danger">*</span></label>
                                                            <span id="contributionsStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="contributions" id="contributions" autocomplete="off" cols="30" rows="4"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="difficulties" class="control-label">Are there any areas of your work which you have difficulties and would like to have further training or support? <span class="text-danger">*</span></label>
                                                            <span id="radioSelectionStatus" class="d-block"></span>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="radioSelection" id="radioYes" value="radioYes">
                                                                <label class="form-check-label font-weight-bold" for="radioYes">Yes</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="radioSelection" id="radioNo" value="radioNo">
                                                                <label class="form-check-label font-weight-bold" for="radioNo">No</label>
                                                            </div>
                                                            <span id="difficultiesStatus" class="d-block"></span>
                                                            <textarea class="form-control form-control-sm" name="difficulties" id="difficulties" autocomplete="off" cols="30" rows="4"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="col-lg-12 text-right justify-content-center d-flex">
                                                    <button class="btn btn-primary mr-2" name="submitEmployeeDetails" id="submitEmployeeDetails">Save</button>
                                                    <span class="d-none btn btn-secondary disabled mr-2" name="loadingSpinner" id="loadingSpinner">
                                                        <div class="spinner-border" role="status"></div>
                                                    </span>
                                                    <button class="btn btn-secondary" type="reset" onclick="location.reload();"name="cancelSubmitEmployeeDetails" id="cancelSubmitEmployeeDetails">Reset</button>
                                                </div>
                                            </form>
                                            <script>
                                                function addstaffBioDataJsValidation()
                                                {
                                                // Set the value of is_input_valid to be true by default
                                                    var is_input_valid = true;

                                                    document.getElementById("submitEmployeeDetails").className = "d-none";
                                                    document.getElementById("loadingSpinner").className = "d-block";
                                                    
                                                    if (document.getElementById('employeePfNumber').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('employeePfNumber').style.border = "1px solid #dc3545";
                                                        document.getElementById('employeePfNumberStatus').style.color = "#dc3545";
                                                        document.getElementById('employeePfNumberStatus').innerHTML = "Please enter your PF number";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        if (document.getElementById('employeePfNumber').value != "<?php echo $_SESSION['user_Pf_Number']; ?>")
                                                        {
                                                            is_input_valid = false;
                                                            document.getElementById('employeePfNumber').style.border = "1px solid #dc3545";
                                                            document.getElementById('employeePfNumberStatus').style.color = "#dc3545";
                                                            document.getElementById('employeePfNumberStatus').innerHTML = "Please enter correct PF number";

                                                            document.getElementById("loadingSpinner").className = "d-none";
                                                            document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                        }else
                                                        {
                                                            document.getElementById('employeePfNumber').style.border = "1px solid #17a2b8";
                                                            document.getElementById('employeePfNumberStatus').innerHTML = "";
                                                        }
                                                    }

                                                    if (document.getElementById('employeeFirstName').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('employeeFirstName').style.border = "1px solid #dc3545";
                                                        document.getElementById('employeeFirstNameStatus').style.color = "#dc3545";
                                                        document.getElementById('employeeFirstNameStatus').innerHTML = "Please enter your first name";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('employeeFirstName').style.border = "1px solid #17a2b8";
                                                        document.getElementById('employeeFirstNameStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('employeeLastName').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('employeeLastName').style.border = "1px solid #dc3545";
                                                        document.getElementById('employeeLastNameStatus').style.color = "#dc3545";
                                                        document.getElementById('employeeLastNameStatus').innerHTML = "Please enter your last name";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('employeeLastName').style.border = "1px solid #17a2b8";
                                                        document.getElementById('employeeLastNameStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('employeeDepartment').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('employeeDepartment').style.border = "1px solid #dc3545";
                                                        document.getElementById('employeeDepartmentStatus').style.color = "#dc3545";
                                                        document.getElementById('employeeDepartmentStatus').innerHTML = "Please select your department";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('employeeDepartment').style.border = "1px solid #17a2b8";
                                                        document.getElementById('employeeDepartmentStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('employeeDesination').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('employeeDesination').style.border = "1px solid #dc3545";
                                                        document.getElementById('employeeDesinationStatus').style.color = "#dc3545";
                                                        document.getElementById('employeeDesinationStatus').innerHTML = "Please enter your desination";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('employeeDesination').style.border = "1px solid #17a2b8";
                                                        document.getElementById('employeeDesinationStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('employementNature').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('employementNature').style.border = "1px solid #dc3545";
                                                        document.getElementById('employementNatureStatus').style.color = "#dc3545";
                                                        document.getElementById('employementNatureStatus').innerHTML = "Please enter the nature employement";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('employementNature').style.border = "1px solid #17a2b8";
                                                        document.getElementById('employementNatureStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('employeeJobDescription').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('employeeJobDescription').style.border = "1px solid #dc3545";
                                                        document.getElementById('employeeJobDescriptionStatus').style.color = "#dc3545";
                                                        document.getElementById('employeeJobDescriptionStatus').innerHTML = "Please enter your job description";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('employeeJobDescription').style.border = "1px solid #17a2b8";
                                                        document.getElementById('employeeJobDescriptionStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('qualifications').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('qualifications').style.border = "1px solid #dc3545";
                                                        document.getElementById('qualificationsStatus').style.color = "#dc3545";
                                                        document.getElementById('qualificationsStatus').innerHTML = "Please enter your qualifications";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('qualifications').style.border = "1px solid #17a2b8";
                                                        document.getElementById('qualificationsStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('strengths').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('strengths').style.border = "1px solid #dc3545";
                                                        document.getElementById('strengthsStatus').style.color = "#dc3545";
                                                        document.getElementById('strengthsStatus').innerHTML = "Please enter your strengths";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('strengths').style.border = "1px solid #17a2b8";
                                                        document.getElementById('strengthsStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('contributions').value === "")
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('contributions').style.border = "1px solid #dc3545";
                                                        document.getElementById('contributionsStatus').style.color = "#dc3545";
                                                        document.getElementById('contributionsStatus').innerHTML = "Please enter your contributions";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('contributions').style.border = "1px solid #17a2b8";
                                                        document.getElementById('contributionsStatus').innerHTML = "";
                                                    }

                                                    // Set the value of checkedStatus to be false by default
                                                    var checkedStatus = false;
                                                    var radioButton = document.getElementsByName('radioSelection');

                                                    // Loop through each radio button to check if any is checked
                                                    for (var i = 0; i < radioButton.length; i++)
                                                    {
                                                        if (radioButton[i].checked)
                                                        {
                                                            var checkedValue = radioButton[i].value;
                                                            checkedStatus = true;
                                                            break;
                                                        }
                                                    }
                                                    if (checkedStatus==false)
                                                    {
                                                        is_input_valid = false;
                                                        document.getElementById('radioSelectionStatus').style.color = "#dc3545";
                                                        document.getElementById('radioSelectionStatus').innerHTML = "Please select one";

                                                        document.getElementById("loadingSpinner").className = "d-none";
                                                        document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                    }
                                                    else{
                                                        document.getElementById('radioSelectionStatus').innerHTML = "";
                                                        if (checkedValue == "radioYes" && document.getElementById('difficulties').value === "") {
                                                            is_input_valid = false;
                                                            document.getElementById('difficulties').style.border = "1px solid #dc3545";
                                                            document.getElementById('difficultiesStatus').style.color = "#dc3545";
                                                            document.getElementById('difficultiesStatus').innerHTML = "Please enter your difficulties";

                                                            document.getElementById("loadingSpinner").className = "d-none";
                                                            document.getElementById("submitEmployeeDetails").className = "d-block btn btn-primary mr-2";
                                                        }else{
                                                            document.getElementById('radioSelectionStatus').innerHTML = "";
                                                            document.getElementById('difficulties').style.border = "1px solid #17a2b8";
                                                            document.getElementById('difficultiesStatus').innerHTML = "";
                                                        }
                                                    }
                                                    return is_input_valid;
                                                }
                                            </script>
                                        <?php } ?>
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