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
            header("Location: login.php?loginError=loginError01&link=newDepartment");
            exit();
        }

        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'addNewDepartMent' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addNewDepartMent']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            // Escape the user input before using it in an SQL query
            $secure_department = mysqli_real_escape_string($dbConnection, $_POST['department']);
            $secure_school = mysqli_real_escape_string($dbConnection, $_POST['school']);
            $secure_cod = mysqli_real_escape_string($dbConnection, $_POST['cod']);
            $department_status= 5;

            // If $secure_department is empty, redirect to new_department.php showing "Please enter the dapartment name" error message and exit
            if (empty($secure_department) == true)
            {
                header('Location: new_department.php?newDepartmentError=emptyDepartmentName');
                exit();
            }
            // else if  $secure_school is empty, redirect to new_department.php showing "Please select the school name" error message and exit
            elseif (empty($secure_school) == true)
            {
                header('Location: new_department.php?newDepartmentError=emptySchoolName');
                exit();
            }
            else
            {
                // if $secure_cod is not empty set the value of $secure_cod to be empty
                if (empty($secure_cod) == true)
                {
                    $secure_cod = 'notSet';
                }
                else
                {
                    // SQL select statement
                    $checkIfSelectedIsCodSql = 'SELECT `is_cod` FROM `users` WHERE `user_Pf_Number`=?';

                    // Prepare the SQL statement with a parameter placeholder
                    $checkIfSelectedIsCodStmt = $dbConnection->prepare($checkIfSelectedIsCodSql);

                    // Bind parameters to the statement
                    $checkIfSelectedIsCodStmt->bind_param('s', $secure_cod);

                    // Execute the statement
                    $checkIfSelectedIsCodStmt->execute();

                    // Retrieve the result set
                    $checkIfSelectedIsCodResult = $checkIfSelectedIsCodStmt->get_result();

                    // Fetch data
                    while ($codValue = $checkIfSelectedIsCodResult->fetch_assoc())
                    {
                        $codValueRow = $codValue['is_cod'];
                    }
                    // if is set to integer 5, redirect to new_department.php showing "Please select a user who is not currently a COD" error message and exit
                    if ($codValueRow == 5)
                    {
                        header('Location: new_department.php?newDepartmentError=existingCod');
                        exit();
                    }
                }

                // Prepare a SQL statement with a parameter placeholder
                $countDepartmentNameStmt = $dbConnection->prepare('SELECT COUNT(department_name) AS `departmentNameCount` FROM `departments` WHERE `department_name`=?');

                // Bind parameters to the statement
                $countDepartmentNameStmt->bind_param('s',$secure_department);

                // Execute the statement
                $countDepartmentNameStmt->execute();

                // Retrieve the result set
                $countDepartmentNameResult = $countDepartmentNameStmt->get_result();

                // Fetch data
                while ($departmentNameCountRow = $countDepartmentNameResult->fetch_assoc())
                {
                    $departmentNameCount = $departmentNameCountRow['departmentNameCount'];
                }

                // else if  $departmentNameCount is 1, redirect to new_department.php showing "Please enter a unique department name" error message and exit
                if ($departmentNameCount == 1)
                {
                    header('Location: new_department.php?newDepartmentError=departmentExists');
                    exit();
                }
                else
                {
                    // Prepare a SQL statement with a parameter placeholder
                    $countSchoolIdStmt = $dbConnection->prepare('SELECT COUNT(school_id) AS `schoolIdCount` FROM `schools` WHERE `school_id`=?');

                    // Bind parameters to the statement
                    $countSchoolIdStmt->bind_param('s',$secure_school);

                    // Execute the statement
                    $countSchoolIdStmt->execute();

                    // Retrieve the result set
                    $countSchoolIdResult = $countSchoolIdStmt->get_result();

                    // Fetch data
                    while ($schoolIdCountRow = $countSchoolIdResult->fetch_assoc())
                    {
                        $schoolIdCount = $schoolIdCountRow['schoolIdCount'];
                    }

                    // else if  $schoolIdCount is not inreger 1, redirect to new_department.php showing "Please select a valid school" error message and exit
                    if ($schoolIdCount !== 1)
                    {
                        header('Location: new_department.php?newDepartmentError=invalidSchool');
                        exit();
                    }
                    else
                    {
                        // select department_id from the database
                        $selectDepartmentIdIdQuery = mysqli_query($dbConnection,'SELECT `department_id` FROM `departments` ORDER BY `department_id` DESC');
                        $selectDepartmentIdIdRow = mysqli_fetch_array($selectDepartmentIdIdQuery);

                        // result is fetched as an array
                        if ($selectDepartmentIdIdRow)
                        {
                            $latestDepartmentIdId = $selectDepartmentIdIdRow['department_id'];
                        }

                        // there is no recent department id, set the start department id
                        if (empty($latestDepartmentIdId))
                        {
                            $department_id = "DPT-01";
                            $date_created = date("Y-m-d H:i:s",strtotime($currentTime));

                            // add new department
                            $addNewDepartmentSql = 'INSERT INTO `departments`(`department_id`, `department_name`, `department_status`, `school`, `cod_Pf_Number`, `date_created`, `created_by`) VALUES (?,?,?,?,?,?,?)';
                            $addNewDepartmentStmt = $dbConnection->prepare($addNewDepartmentSql);
                            $addNewDepartmentStmt->bind_param('sssssss',$department_id,$secure_department,$department_status,$secure_school,$secure_cod,$date_created,$_SESSION['user_Pf_Number']);
                            if ($addNewDepartmentStmt->execute())
                            {
                                $_SESSION['session_new_department_added'] = 'success';
                                $_SESSION['session_new_departmentName'] = $secure_department;
                                header('Location: department_list.php');
                                exit();
                            }
                            else
                            {
                                $_SESSION['session_new_department_added'] = 'failed';
                                $_SESSION['session_new_departmentName'] = '';
                                $_SESSION['session_new_departmentName_failed'] = $secure_department;
                                header('Location: new_department.php');
                                exit();
                            }
                        }
                        else
                        {
                            $department_id = str_replace("DPT-","",$latestDepartmentIdId);
                            $department_id = str_pad($department_id + 1,2,0, STR_PAD_LEFT);
                            $department_id = "DPT-" .$department_id;
                            $date_created = date("Y-m-d H:i:s",strtotime($currentTime));

                            // add new department
                            $addNewDepartmentSql = 'INSERT INTO `departments`(`department_id`, `department_name`, `department_status`, `school`, `cod_Pf_Number`, `date_created`, `created_by`) VALUES (?,?,?,?,?,?,?)';
                            $addNewDepartmentStmt = $dbConnection->prepare($addNewDepartmentSql);
                            $addNewDepartmentStmt->bind_param('sssssss',$department_id,$secure_department,$department_status,$secure_school,$secure_cod,$date_created,$_SESSION['user_Pf_Number']);
                            if ($addNewDepartmentStmt->execute())
                            {
                                $_SESSION['session_new_department_added'] = 'success';
                                $_SESSION['session_new_departmentName'] = $secure_department;
                                header('Location: department_list.php');
                                exit();
                            }
                            else
                            {
                                $_SESSION['session_new_department_added'] = 'failed';
                                $_SESSION['session_new_departmentName'] = '';
                                $_SESSION['session_new_departmentName_failed'] = $secure_department;
                                header('Location: new_department.php');
                                exit();
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
                <title>epes | Add new department</title>
                <!-- include header.php php file -->
                <?php include 'header.php' ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <div class="wrapper">
                    <!-- include topbar.php php file -->
                    <?php include 'topbar.php' ?>
                    <!-- include sidebar.php php file -->
                    <?php include 'sidebar.php' ?>

                    <!-- Content Wrapper. Contains page content -->
                    <div class="content-wrapper">
                        <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-body text-white"></div>
                        </div>
                        <div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>

                        <div class="content-header">
                            <div class="container-fluid">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <h1 class="m-0">Add new department</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4 col-lg-4 mx-auto">
                            <div class="container-fluid">
                                <div class="card">
                                    <div class="card-header font-weight-bold text-center">
                                        Add new department
                                    </div>
                                    <?php
                                        if (isset($_SESSION['session_new_department_added'])==true && empty($_SESSION['session_new_department_added'])==false && $_SESSION['session_new_department_added']=='failed'){
                                    ?>
                                        <div class="col-sm-6 col-md-4 col-lg-4 mx-auto">
                                            <div class="alert alert-info text-center" role="alert">
                                                There has been an error adding <b><?php echo $_SESSION['session_new_departmentName_failed']; ?></b> department!
                                                <br>
                                                Please try again.
                                                <?php $_SESSION['session_new_departmentName_failed']='';?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <?php if (isset($_GET['newDepartmentError']) == true && empty($_GET['newDepartmentError']) == false && $_GET['newDepartmentError'] == 'existingCod'){ ?>
                                                <span class="text-danger font-weight-bold">Please select a user who is not currently a COD</span>
                                            <?php }elseif (isset($_GET['newDepartmentError']) == true && empty($_GET['newDepartmentError']) == false && $_GET['newDepartmentError'] == 'emptyDepartmentName'){ ?>
                                                <span class="text-danger font-weight-bold">Please enter the dapartment name</span>
                                            <?php }elseif (isset($_GET['newDepartmentError']) == true && empty($_GET['newDepartmentError']) == false && $_GET['newDepartmentError'] == 'emptySchoolName'){ ?>
                                                <span class="text-danger font-weight-bold">Please select the school name</span>
                                            <?php }elseif (isset($_GET['newDepartmentError']) == true && empty($_GET['newDepartmentError']) == false && $_GET['newDepartmentError'] == 'emptyCod'){ ?>
                                                <span class="text-danger font-weight-bold">Please select COD</span>
                                            <?php }elseif (isset($_GET['newDepartmentError']) == true && empty($_GET['newDepartmentError']) == false && $_GET['newDepartmentError'] == 'departmentExists'){ ?>
                                                <span class="text-danger font-weight-bold">Please enter a unique department name</span>
                                            <?php }elseif (isset($_GET['newDepartmentError']) == true && empty($_GET['newDepartmentError']) == false && $_GET['newDepartmentError'] == 'invalidSchool'){ ?>
                                                <span class="text-danger font-weight-bold">Please select a valid school</span>
                                            <?php }elseif (isset($_GET['newDepartmentError']) == true && empty($_GET['newDepartmentError']) == false && $_GET['newDepartmentError'] == 'invalidCod'){ ?>
                                                <span class="text-danger font-weight-bold">Please select a valid COD</span>
                                            <?php } ?>
                                        </div>
                                        <form action="" method="post" onsubmit="return newDepartmentJsValidation();">
                                        <!-- <form action="" method="post"> -->
                                            <div>
                                                <div class="form-group">
                                                    <label for="department" class="control-label">Department name <span class="text-danger">*</span></label>
                                                    <span id="departmentStatus" class="d-block"></span>
                                                    <input type="text" name="department" id="department" class="form-control form-control-sm" style="text-transform: uppercase;" autocomplete="off" autofocus>
                                                </div>
                                                <div class="form-group">
                                                    <?php
                                                        $schoolStatus = 5;
                                                        $selectSchoolsSQL ='SELECT `school_id`, `school_name`, `school_email` FROM `schools` WHERE `school_status` = ? ORDER BY `school_name` ASC';
                                                        $selectSchoolsSTMT = $dbConnection->prepare($selectSchoolsSQL);
                                                        $selectSchoolsSTMT->bind_param('i', $schoolStatus);
                                                        $selectSchoolsSTMT->execute();
                                                        $selectSchoolsResult = $selectSchoolsSTMT->get_result();
                                                        if ($selectSchoolsResult->num_rows > 0)
                                                        {
                                                            $schoolId= mysqli_fetch_all($selectSchoolsResult, MYSQLI_ASSOC);
                                                        }
                                                    ?>
                                                    <label for="school" class="control-label">School name <span class="text-danger">*</span></label>
                                                    <span id="schoolStatus" class="d-block"></span>
                                                    <select name="school" id="school" class="form-control form-control-sm">
                                                        <option value="">Select school name</option>
                                                            <?php foreach ($schoolId as $schoolId) { ?>
                                                                <option value="<?php echo $schoolId['school_id']; ?>">
                                                                    <?php echo $schoolId['school_name']; ?>
                                                                </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <?php
                                                        $is_cod = 5;
                                                        $user_role = 5;
                                                        $selectEvaluatorsSQL ='SELECT `user_Pf_Number`, `user_role`, `first_name`, `middle_name`, `last_name` FROM `users` WHERE `is_cod` != ? AND `user_role` != ? ORDER BY `user_Pf_Number` ASC';
                                                        $selectEvaluatorsSTMT = $dbConnection->prepare($selectEvaluatorsSQL);
                                                        $selectEvaluatorsSTMT->bind_param('ii', $is_cod,$user_role);
                                                        $selectEvaluatorsSTMT->execute();
                                                        $selectEvaluatorsResult = $selectEvaluatorsSTMT->get_result();
                                                        if ($selectEvaluatorsResult->num_rows > 0)
                                                        {
                                                            $codPfNumber= mysqli_fetch_all($selectEvaluatorsResult, MYSQLI_ASSOC);
                                                        }
                                                    ?>
                                                    <label for="cod" class="control-label">COD</label>
                                                    <span id="codStatus" class="d-block"></span>
                                                    <select name="cod" id="cod" class="form-control form-control-sm">
                                                        <option value="">Select COD</option>
                                                            <?php foreach ($codPfNumber as $codPfNumber) { ?>
                                                                <option value="<?php echo $codPfNumber['user_Pf_Number']; ?>">
                                                                    <?php echo '(PF:'.$codPfNumber['user_Pf_Number'].') '.$codPfNumber['first_name'].' '.$codPfNumber['middle_name'].' '.$codPfNumber['last_name']; ?>
                                                                </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <button type="submit" name="addNewDepartMent" id="addNewDepartMent" class="mt-4 text-decoration-none btn btn-sm btn-primary btn-block">
                                                    Add new department
                                                </button>
                                            </div>
                                        </form>
                                        <script>
                                            function newDepartmentJsValidation()
                                            {
                                                // Set the values to be valid by default
                                                var is_it_valid = true;

                                                if (document.getElementById('department').value == "")
                                                {
                                                    is_it_valid = false;
                                                    document.getElementById('department').style.border = "1px solid red";
                                                    document.getElementById('departmentStatus').style.color = "red";
                                                    document.getElementById('departmentStatus').innerHTML = "Please enter the department name";
                                                }
                                                else
                                                {
                                                    document.getElementById('department').style.border = "1px solid green";
                                                    document.getElementById('departmentStatus').innerHTML = "";
                                                }

                                                if (document.getElementById('school').value == "")
                                                {
                                                    is_it_valid = false;
                                                    document.getElementById('school').style.border = "1px solid red";
                                                    document.getElementById('schoolStatus').style.color = "red";
                                                    document.getElementById('schoolStatus').innerHTML = "Please select the school";
                                                }
                                                else
                                                {
                                                    document.getElementById('school').style.border = "1px solid green";
                                                    document.getElementById('schoolStatus').innerHTML = "";
                                                }

                                                // if (document.getElementById('cod').value == "")
                                                // {
                                                //     is_it_valid = false;
                                                //     document.getElementById('cod').style.border = "1px solid red";
                                                //     document.getElementById('codStatus').style.color = "red";
                                                //     document.getElementById('codStatus').innerHTML = "Please select the cod";
                                                // }
                                                if (document.getElementById('cod').value !== "")
                                                {
                                                    document.getElementById('cod').style.border = "1px solid green";
                                                    document.getElementById('codStatus').innerHTML = "";
                                                }

                                                return is_it_valid;
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Main Footer -->
                    <!-- include mainfooter.php php file -->
                    <?php include 'mainfooter.php' ?>
                </div>
                <!-- include footer.php php file -->
                <?php include 'footer.php' ?>
            </body>
        </html>
<?php } ?>