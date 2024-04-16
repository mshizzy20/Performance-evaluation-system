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
            header("Location: login.php?loginError=loginError01&link=newEmployee");
            exit();
        }

        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'addNewEmployee' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addNewEmployee']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            // Escape the user input before using it in an SQL query
            $secure_newEmployeePfNumber = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeePfNumber']));
            $secure_newEmployeeTitle = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeeTitle']));
            $secure_newEmployeeFirstName = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeeFirstName']));
            $secure_newEmployeeMiddleName = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeeMiddleName']));
            $secure_newEmployeeLastName = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeeLastName']));
            $secure_newEmployeeDepartment = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeeDepartment']));
            $secure_newEmployeeRole = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeeRole']));
            $secure_newEmployeeEmail = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeeEmail']));
            $secure_newEmployeePassword = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeePassword']));
            $secure_newEmployeeCpassword = trim(mysqli_real_escape_string($dbConnection, $_POST['newEmployeeCpassword']));
            $system_access= 5;
            $last_login ='New user';
            $last_seen ='New user';

            // If $secure_newEmployeePfNumber is empty, redirect to new_employee.php showing "Please enter the PF number" error message and exit
            if (empty($secure_newEmployeePfNumber) == true)
            {
                header('Location: new_employee.php?newEmployeeError=emptyPFNumber');
                exit();
            }
            // else if $secure_newEmployeeTitle is empty, redirect to new_employee.php showing "Please enter the title" error message and exit
            elseif (empty($secure_newEmployeeTitle) == true)
            {
                header('Location: new_employee.php?newEmployeeError=emptyTitle');
                exit();
            }
            // else if $secure_newEmployeeFirstName is empty, redirect to new_employee.php showing "Please enter the first name" error message and exit
            elseif (empty($secure_newEmployeeFirstName) == true)
            {
                header('Location: new_employee.php?newEmployeeError=emptyFirstName');
                exit();
            }
            // else if $secure_newEmployeeLastName is empty, redirect to new_employee.php showing "Please enter the last name" error message and exit
            elseif (empty($secure_newEmployeeLastName) == true)
            {
                header('Location: new_employee.php?newEmployeeError=emptyLastName');
                exit();
            }
            // else if $secure_newEmployeeDepartment is empty, redirect to new_employee.php showing "Please select the department" error message and exit
            elseif (empty($secure_newEmployeeDepartment) == true)
            {
                header('Location: new_employee.php?newEmployeeError=emptyDepartment');
                exit();
            }
            // else if $secure_newEmployeeRole is empty, redirect to new_employee.php showing "Please select the role" error message and exit
            elseif (empty($secure_newEmployeeRole) == true)
            {
                header('Location: new_employee.php?newEmployeeError=emptyRole');
                exit();
            }
            // else if $secure_newEmployeeEmail is empty, redirect to new_employee.php showing "Please enter the email" error message and exit
            elseif (empty($secure_newEmployeeEmail) == true)
            {
                header('Location: new_employee.php?newEmployeeError=emptyEmail');
                exit();
            }
            // else if $secure_newEmployeePassword is empty, redirect to new_employee.php showing "Please enter the password" error message and exit
            elseif (empty($secure_newEmployeePassword) == true)
            {
                header('Location: new_employee.php?newEmployeeError=emptyPassword');
                exit();
            }
            // else if $secure_newEmployeeCpassword is empty, redirect to new_employee.php showing "Please reenter the password" error message and exit
            elseif (empty($secure_newEmployeeCpassword) == true)
            {
                header('Location: new_employee.php?newEmployeeError=emptyCpassword');
                exit();
            }
            // else if $secure_newEmployeePassword and $secure_newEmployeeCpassword are not identical , redirect to new_employee.php showing "Passwords don't match" error message and exit
            elseif ($secure_newEmployeePassword !== $secure_newEmployeeCpassword)
            {
                header('Location: new_employee.php?newEmployeeError=passwordError');
                exit();
            }
            // else if $secure_newEmployeeEmail is invalid, redirect to new_employee.php showing "Please enter a valid email" error message and exit
            elseif(filter_var($secure_newEmployeeEmail, FILTER_VALIDATE_EMAIL) == false)
            {
                header('Location: new_employee.php?newEmployeeError=invalidlEmail');
                exit();
            }
            else
            {
                $name = $_FILES['newEmployeeProfileImage']['name'];
                $tmp_name = $_FILES['newEmployeeProfileImage']['tmp_name'];
                $size = $_FILES['newEmployeeProfileImage']['size'];
                $error = $_FILES['newEmployeeProfileImage']['error'];
                $type = $_FILES['newEmployeeProfileImage']['type'];
                
                $fileExtension = explode('.',$name);
                $fileActualExtension = strtolower(end($fileExtension));

                $allowedExtensions = array('png','jpg','jpeg');

                if (in_array($fileActualExtension,$allowedExtensions))
                {
                    if ($error===0)
                    {
                        if ($size > 1000000000)
                        {
                            header('Location: new_employee.php?newEmployeeError=Your_file_is_too_big');
                            exit();
                        }
                    }
                    else
                    {
                        header('Location: new_employee.php?newEmployeeError=There_was_an_error_uploading_file');
                        exit();
                    }
                }
                else
                {
                    header('Location: new_employee.php?newEmployeeError=File_type_is_not_allowed');
                    exit();
                }

                if ($secure_newEmployeeRole == '5')
                {
                    $is_cod = 6;
                }
                elseif ($secure_newEmployeeRole == '6')
                {
                    $is_cod = 5;
                }
                elseif ($secure_newEmployeeRole == '7')
                {
                    $is_cod = 6;
                }

                // Hash the password
                $hashed_newEmployeePassword = password_hash($secure_newEmployeePassword, PASSWORD_DEFAULT);

                // SQL select statement
                $sql = 'SELECT `user_Pf_Number` FROM `users` WHERE `user_Pf_Number`=?';
                // Prepare the SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('s', $secure_newEmployeePfNumber);
                // Execute the statement
                $stmt->execute();
                // Retrieve the result set
                $stmt->store_result();
                // if the number of rows is identical to integer one,redirect to new_employee.php webpage
                if ($stmt->num_rows===1)
                {
                    header('Location: new_employee.php?newEmployeeError=pfExists');
                    exit();
                }

                // SQL select statement
                $sql = 'SELECT `email` FROM `users` WHERE `email`=?';
                // Prepare the SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('s', $secure_newEmployeeEmail);
                // Execute the statement
                $stmt->execute();
                // Retrieve the result set
                $stmt->store_result();
                // if the number of rows is identical to integer one,redirect to new_employee.php webpage
                if ($stmt->num_rows===1)
                {
                    header('Location: new_employee.php?newEmployeeError=emailExists');
                    exit();
                }

                if ($secure_newEmployeeRole == '6')
                {
                    $cod_Pf_Number_Not_Set = 'notSet';
                    // SQL select statement
                    $sql = 'SELECT `cod_Pf_Number` FROM `departments` WHERE `department_id`=? AND `cod_Pf_Number`=?';
                    // Prepare the SQL statement with a parameter placeholder
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('ss', $secure_newEmployeeDepartment,$cod_Pf_Number_Not_Set);
                    // Execute the statement
                    $stmt->execute();
                    // Retrieve the result set
                    $stmt->store_result();
                    // if the number of rows is identical to integer one,redirect to new_employee.php webpage
                    if ($stmt->num_rows!==1)
                    {
                        header('Location: new_employee.php?newEmployeeError=departmentAlreadyAssigned');
                        exit();
                    }
                }

                $date_added = date("Y-m-d H:i:s",strtotime($currentTime));
                $avatar = $secure_newEmployeePfNumber.'.'.$fileActualExtension;
                // add employee
                $addNewEmployeeSql = 'INSERT INTO `users`(`user_Pf_Number`, `title`, `is_cod`, `user_role`, `first_name`, `middle_name`, `last_name`, `email`, `department_id`, `password`, `avatar`, `date_added`, `system_access`, `last_login`, `last_seen`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                $addNewEmployeeStmt = $dbConnection->prepare($addNewEmployeeSql);
                $addNewEmployeeStmt->bind_param('ssiissssssssiss',$secure_newEmployeePfNumber,$secure_newEmployeeTitle,$is_cod,$secure_newEmployeeRole,$secure_newEmployeeFirstName,$secure_newEmployeeMiddleName,$secure_newEmployeeLastName,$secure_newEmployeeEmail,$secure_newEmployeeDepartment,$hashed_newEmployeePassword,$avatar,$date_added,$system_access,$last_login,$last_seen);
                if ($addNewEmployeeStmt->execute())
                {
                    $fileDestination = 'userProfilePictures/'.$avatar;
                    move_uploaded_file($tmp_name,$fileDestination);
                    if ($secure_newEmployeeRole == '6')
                    {
                        // assign the cod_Pf_Number the department set
                        $updateDepartmentCodPfNumberSql = 'UPDATE `departments` SET `cod_Pf_Number`=? WHERE `department_id`=?';
                        $updateDepartmentCodPfNumberStmt = $dbConnection->prepare($updateDepartmentCodPfNumberSql);
                        $updateDepartmentCodPfNumberStmt->bind_param('ss', $secure_newEmployeePfNumber,$secure_newEmployeeDepartment);
                        if ($updateDepartmentCodPfNumberStmt->execute())
                        {
                            $_SESSION['session_new_employee_added'] = 'success';
                            $_SESSION['session_new_employeeDetail'] = $secure_newEmployeeFirstName.'('.$secure_newEmployeePfNumber.')';
                            header('Location: employee_list.php');
                            exit();
                        }
                        else
                        {
                            $_SESSION['session_new_employee_added'] = 'failed';
                            $_SESSION['session_new_employeeDetail_failed'] = $secure_newEmployeeFirstName;
                            header('Location: new_employee.php');
                            exit();
                        }
                    }
                    else
                    {
                        $_SESSION['session_new_employee_added'] = 'success';
                        $_SESSION['session_new_employeeDetail'] = $secure_newEmployeeFirstName.'('.$secure_newEmployeePfNumber.')';
                        header('Location: employee_list.php');
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
                <title>epes | Add new employee</title>
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
                                        <h1 class="m-0">Add new employee</h1>
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
                                    <?php if (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emptyPFNumber'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter the PF number</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emptyTitle'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter the title</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emptyFirstName'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter the first name</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emptyLastName'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter the last name</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emptyDepartment'){ ?>
                                        <span class="text-danger font-weight-bold">Please select the department</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emptyRole'){ ?>
                                        <span class="text-danger font-weight-bold">Please select the role</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emptyEmail'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter the email</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emptyPassword'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter the password</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emptyCpassword'){ ?>
                                        <span class="text-danger font-weight-bold">Please reenter the password</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'passwordError'){ ?>
                                        <span class="text-danger font-weight-bold">Passwords don't match</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'invalidlEmail'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter a valid email</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'pfExists'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter a unique PF number</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'emailExists'){ ?>
                                        <span class="text-danger font-weight-bold">Please enter a unique email</span>
                                    <?php }elseif (isset($_GET['newEmployeeError']) == true && empty($_GET['newEmployeeError']) == false && $_GET['newEmployeeError'] == 'departmentAlreadyAssigned'){ ?>
                                        <span class="text-danger font-weight-bold">Department has already been assigned a COD</span>
                                    <?php } ?>
                                </div>
                                <div class="card-body">
                                    <form action="" method="post" enctype="multipart/form-data" onsubmit="return newEmployeeJsValidation();">
                                    <!-- <form action="" method="post" enctype="multipart/form-data"> -->
                                        <div class="row">
                                            <div class="col-md-6 border-right">
                                                <div class="form-group">
                                                    <label for="newEmployeePfNumber" class="control-label">PF number <span class="text-danger">*</span></label>
                                                    <span id="newEmployeePfNumberStatus" class="d-block"></span>
                                                    <input type="text" name="newEmployeePfNumber" id="newEmployeePfNumber" class="form-control form-control-sm" autocomplete="off" autofocus>
                                                </div>
                                                <div class="form-group">
                                                    <label for="newEmployeeTitle" class="control-label">Title <span class="text-danger">*</span></label>
                                                    <span id="newEmployeeTitleStatus" class="d-block"></span>
                                                    <input type="text" name="newEmployeeTitle" id="newEmployeeTitle" class="form-control form-control-sm" autocomplete="off" autofocus>
                                                </div>
                                                <div class="form-group">
                                                    <label for="newEmployeeFirstName" class="control-label">First Name <span class="text-danger">*</span></label>
                                                    <span id="newEmployeeFirstNameStatus" class="d-block"></span>
                                                    <input type="text" name="newEmployeeFirstName" id="newEmployeeFirstName" class="form-control form-control-sm" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label for="newEmployeeMiddleName" class="control-label">Middle Name (optional)</label>
                                                    <input type="text" name="newEmployeeMiddleName" id="newEmployeeMiddleName" class="form-control form-control-sm" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label for="newEmployeeLastName" class="control-label">Last Name <span class="text-danger">*</span></label>
                                                    <span id="newEmployeeLastNameStatus" class="d-block"></span>
                                                    <input type="text" name="newEmployeeLastName" id="newEmployeeLastName" class="form-control form-control-sm" autocomplete="off">
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
                                                    <label for="newEmployeeDepartment" class="control-label">Department <span class="text-danger">*</span></label>
                                                    <span id="newEmployeeDepartmentStatus" class="d-block"></span>
                                                    <select name="newEmployeeDepartment" id="newEmployeeDepartment" class="form-control form-control-sm">
                                                        <option value="">Select department</option>
                                                            <?php foreach ($department as $department) { ?>
                                                                <option value="<?php echo $department['department_id']; ?>">
                                                                    <?php echo '('.$department['department_id'].') '.$department['department_name']; ?>
                                                                </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            <div class="form-group">
                                                    <?php
                                                        $activeUserRoleStatusStatus = 5;
                                                        $selectUserRolesSQL ='SELECT `user_role_id`, `user_role_name` FROM `user_roles` WHERE `user_role_status`=? ORDER BY `user_role_name` ASC';
                                                        $selectUserRolesSTMT = $dbConnection->prepare($selectUserRolesSQL);
                                                        $selectUserRolesSTMT->bind_param('i', $activeUserRoleStatusStatus);
                                                        $selectUserRolesSTMT->execute();
                                                        $selectUserRolesResult = $selectUserRolesSTMT->get_result();
                                                        if ($selectUserRolesResult->num_rows > 0)
                                                        {
                                                            $userRole= mysqli_fetch_all($selectUserRolesResult, MYSQLI_ASSOC);
                                                        }
                                                    ?>
                                                    <label for="newEmployeeRole" class="control-label">Role <span class="text-danger">*</span></label>
                                                    <span id="newEmployeeRoleStatus" class="d-block"></span>
                                                    <select name="newEmployeeRole" id="newEmployeeRole" class="form-control form-control-sm">
                                                        <option value="">Select user role</option>
                                                            <?php foreach ($userRole as $userRole) { ?>
                                                                <option value="<?php echo $userRole['user_role_id']; ?>">
                                                                    <?php echo $userRole['user_role_id'].' '.$userRole['user_role_name']; ?>
                                                                </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="newEmployeeProfileImage" class="control-label">Avatar</label>
                                                    <img src="" name="tempProfilePicture" id="tempProfilePicture" class="img-circle bg-primary text-white font-weight-500" style="cursor: pointer;width: 90px;height:90px;object-fit: cover" onclick="$('#newEmployeeProfileImage').click()">
                                                    <input type="file" name="newEmployeeProfileImage" id="newEmployeeProfileImage" class="d-none" onchange="displayImg(this,$(this))">
                                                    <script>
                                                        function displayImg(input,_this)
                                                        {
                                                            if (input.files && input.files[0])
                                                            {
                                                                var reader = new FileReader();
                                                                reader.onload = function(e)
                                                                {
                                                                    $('#tempProfilePicture').attr('src',e.target.result);
                                                                }
                                                                reader.readAsDataURL(input.files[0]);
                                                            }
                                                        }
                                                    </script>
                                                </div>
                                                <div class="form-group">
                                                    <label for="newEmployeeEmail" class="control-label">Email <span class="text-danger">*</span></label>
                                                    <span id="newEmployeeEmailStatus" class="d-block"></span>
                                                    <input type="text" class="form-control form-control-sm" name="newEmployeeEmail" id="newEmployeeEmail" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label for="newEmployeePassword" class="control-label">Password <span class="text-danger">*</span></label>
                                                    <span id="newEmployeePasswordStatus" class="d-block"></span>
                                                    <input type="password" class="form-control form-control-sm" name="newEmployeePassword" id="newEmployeePassword" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label for="newEmployeeCpassword" class="label control-label">Confirm Password <span class="text-danger">*</span></label>
                                                    <span id="newEmployeeCpasswordStatus" class="d-block"></span>
                                                    <input type="password" class="form-control form-control-sm" name="newEmployeeCpassword" id="newEmployeeCpassword" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-lg-12 text-right justify-content-center d-flex">
                                            <button class="btn btn-primary mr-2" name="addNewEmployee" id="addNewEmployee">Save</button>
                                            <button class="btn btn-secondary" type="reset" onclick="location.reload();"name="cancelNewEmployee" id="cancelNewEmployee">Reset</button>
                                        </div>
                                    </form>
                                    <script>
                                        function newEmployeeJsValidation()
                                        {
                                            // Set the values to be valid by default
                                            var is_it_valid = true;
                                            
                                            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                                            if (document.getElementById('newEmployeePfNumber').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeePfNumber').style.border = "1px solid red";
                                                document.getElementById('newEmployeePfNumberStatus').style.color = "red";
                                                document.getElementById('newEmployeePfNumberStatus').innerHTML = "Please enter the PF number";
                                            }
                                            else
                                            {
                                                document.getElementById('newEmployeePfNumber').style.border = "1px solid green";
                                                document.getElementById('newEmployeePfNumberStatus').innerHTML = "";
                                            }
                                            
                                            if (document.getElementById('newEmployeeTitle').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeeTitle').style.border = "1px solid red";
                                                document.getElementById('newEmployeeTitleStatus').style.color = "red";
                                                document.getElementById('newEmployeeTitleStatus').innerHTML = "Please enter the title";
                                            }
                                            else
                                            {
                                                document.getElementById('newEmployeeTitle').style.border = "1px solid green";
                                                document.getElementById('newEmployeeTitleStatus').innerHTML = "";
                                            }

                                            if (document.getElementById('newEmployeeFirstName').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeeFirstName').style.border = "1px solid red";
                                                document.getElementById('newEmployeeFirstNameStatus').style.color = "red";
                                                document.getElementById('newEmployeeFirstNameStatus').innerHTML = "Please enter the first name";
                                            }
                                            else
                                            {
                                                document.getElementById('newEmployeeFirstName').style.border = "1px solid green";
                                                document.getElementById('newEmployeeFirstNameStatus').innerHTML = "";
                                            }

                                            if (document.getElementById('newEmployeeLastName').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeeLastName').style.border = "1px solid red";
                                                document.getElementById('newEmployeeLastNameStatus').style.color = "red";
                                                document.getElementById('newEmployeeLastNameStatus').innerHTML = "Please enter the last name";
                                            }
                                            else
                                            {
                                                document.getElementById('newEmployeeLastName').style.border = "1px solid green";
                                                document.getElementById('newEmployeeLastNameStatus').innerHTML = "";
                                            }

                                            if (document.getElementById('newEmployeeDepartment').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeeDepartment').style.border = "1px solid red";
                                                document.getElementById('newEmployeeDepartmentStatus').style.color = "red";
                                                document.getElementById('newEmployeeDepartmentStatus').innerHTML = "Please select the department";
                                            }
                                            else
                                            {
                                                document.getElementById('newEmployeeDepartment').style.border = "1px solid green";
                                                document.getElementById('newEmployeeDepartmentStatus').innerHTML = "";
                                            }

                                            if (document.getElementById('newEmployeeRole').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeeRole').style.border = "1px solid red";
                                                document.getElementById('newEmployeeRoleStatus').style.color = "red";
                                                document.getElementById('newEmployeeRoleStatus').innerHTML = "Please select the role";
                                            }
                                            else
                                            {
                                                document.getElementById('newEmployeeRole').style.border = "1px solid green";
                                                document.getElementById('newEmployeeRoleStatus').innerHTML = "";
                                            }

                                            if (document.getElementById('newEmployeeEmail').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeeEmail').style.border = "1px solid red";
                                                document.getElementById('newEmployeeEmailStatus').style.color = "red";
                                                document.getElementById('newEmployeeEmailStatus').innerHTML = "Please enter the email";
                                            }
                                            else if (emailRegex.test(document.getElementById('newEmployeeEmail').value) == false)
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeeEmail').style.border = "1px solid red";
                                                document.getElementById('newEmployeeEmailStatus').style.color = "red";
                                                document.getElementById('newEmployeeEmailStatus').innerHTML = "Please enter a valid email";
                                            }
                                            else
                                            {
                                                document.getElementById('newEmployeeEmail').style.border = "1px solid green";
                                                document.getElementById('newEmployeeEmailStatus').innerHTML = "";
                                            }

                                            if (document.getElementById('newEmployeePassword').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeePassword').style.border = "1px solid red";
                                                document.getElementById('newEmployeePasswordStatus').style.color = "red";
                                                document.getElementById('newEmployeePasswordStatus').innerHTML = "Please enter the password";
                                            }
                                            else
                                            {
                                                document.getElementById('newEmployeePassword').style.border = "1px solid green";
                                                document.getElementById('newEmployeePasswordStatus').innerHTML = "";
                                            }

                                            if (document.getElementById('newEmployeePassword').value == "")
                                            {
                                                is_it_valid = false;
                                                // document.getElementById('newEmployeeCpassword').style.border = "1px solid red";
                                                // document.getElementById('newEmployeeCpasswordStatus').style.color = "red";
                                                // document.getElementById('newEmployeeCpasswordStatus').innerHTML = "Please enter the password";
                                            }
                                            else if (document.getElementById('newEmployeeCpassword').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeeCpassword').style.border = "1px solid red";
                                                document.getElementById('newEmployeeCpasswordStatus').style.color = "red";
                                                document.getElementById('newEmployeeCpasswordStatus').innerHTML = "Please reenter the password";
                                            }
                                            else if (document.getElementById('newEmployeePassword').value !== document.getElementById('newEmployeeCpassword').value)
                                            {
                                                is_it_valid = false;
                                                document.getElementById('newEmployeeCpassword').style.border = "1px solid red";
                                                document.getElementById('newEmployeeCpasswordStatus').style.color = "red";
                                                document.getElementById('newEmployeeCpasswordStatus').innerHTML = "Passwords don't match";
                                            }
                                            else
                                            {
                                                document.getElementById('newEmployeeCpassword').style.border = "1px solid green";
                                                document.getElementById('newEmployeeCpasswordStatus').innerHTML = "";
                                            }
                                            
                                            return is_it_valid;
                                        }
                                    </script>
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