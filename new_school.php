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
            header("Location: login.php?loginError=loginError01&link=newSchool");
            exit();
        }
        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'addNewSchool' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addNewSchool']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            // Escape the user input before using it in an SQL query
            $secure_schoolName = mysqli_real_escape_string($dbConnection, strtoupper($_POST['schoolName']));
            $secure_schoolEmail = mysqli_real_escape_string($dbConnection, strtoupper($_POST['schoolEmail']));
            $school_status= 5;

            // If $secure_schoolName is empty, redirect to new_school.php showing "Please enter the school name" error message and exit
            if (empty($secure_schoolName) == true)
            {
                header('Location: new_school.php?newSchoolError=emptySchoolName');
                exit();
            }
            else
            {
                // Prepare a SQL statement with a parameter placeholder
                $countSchoolStmt = $dbConnection->prepare('SELECT COUNT(school_name) AS `schoolNameCount` FROM `schools` WHERE `school_name`=?');

                // Bind parameters to the statement
                $countSchoolStmt->bind_param('s',$secure_schoolName);

                // Execute the statement
                $countSchoolStmt->execute();

                // Retrieve the result set
                $countSchoolNameResult = $countSchoolStmt->get_result();

                // Fetch data
                while ($countSchoolNameRow = $countSchoolNameResult->fetch_assoc())
                {
                    $schoolNameCount = $countSchoolNameRow['schoolNameCount'];
                }

                if ($schoolNameCount >= 1)
                {
                    header('Location: new_school.php?newSchoolError=schoolNameExists');
                    exit();
                }
                else
                {
                    // else if  $secure_schoolEmail is empty, set the value of $secure_schoolEmail to be empty
                    if (empty($secure_schoolEmail) == true)
                    {
                        $secure_schoolEmail = '';
                    }
                    // else if $secure_schoolEmail is invalid, redirect to new_school.php showing "Please enter a valid school email" error message and exit
                    elseif(filter_var($secure_schoolEmail, FILTER_VALIDATE_EMAIL) == false)
                    {
                        header('Location: new_school.php?newSchoolError=invalidSchoolEmail');
                        exit();
                    }
                    else
                    {
                        // Prepare a SQL statement with a parameter placeholder
                        $countSchoolEmailStmt = $dbConnection->prepare('SELECT COUNT(school_email) AS `schoolEmailCount` FROM `schools` WHERE `school_email`=?');

                        // Bind parameters to the statement
                        $countSchoolEmailStmt->bind_param('s',$secure_schoolEmail);

                        // Execute the statement
                        $countSchoolEmailStmt->execute();

                        // Retrieve the result set
                        $countSchoolEmailResult = $countSchoolEmailStmt->get_result();

                        // Fetch data
                        while ($schoolEmailCountRow = $countSchoolEmailResult->fetch_assoc())
                        {
                            $schoolEmailCount = $schoolEmailCountRow['schoolEmailCount'];
                        }
                    }

                    if ($schoolEmailCount >= 1)
                    {
                        header('Location: new_school.php?newSchoolError=schoolEmailExists');
                        exit();
                    }
                    else
                    {
                        /* select school_id from the database */
                        $selectSchoolIdQuery = mysqli_query($dbConnection,'SELECT `school_id` FROM `schools` ORDER BY `school_id` DESC');
                        $selectSchoolIdRow = mysqli_fetch_array($selectSchoolIdQuery);

                        if
                        /* ... result is fetched as an array... */
                        ($selectSchoolIdRow)
                        {
                            $latestSchoolId = $selectSchoolIdRow['school_id'];
                        }

                        if
                        /* ... there is no recent staff, set the start school id */
                        (empty($latestSchoolId))
                        {
                            $school_id = "SCH-01";
                            $date_created = date("Y-m-d H:i:s",strtotime($currentTime));

                            // add new school
                            $addNewSchoolSql = 'INSERT INTO `schools`(`school_id`, `school_name`, `school_status`, `school_email`, `date_created`, `created_by`) VALUES (?,?,?,?,?,?)';
                            $addNewSchoolStmt = $dbConnection->prepare($addNewSchoolSql);
                            $addNewSchoolStmt->bind_param('ssssss',$school_id,$secure_schoolName,$school_status,$secure_schoolEmail,$date_created,$_SESSION['user_Pf_Number']);
                            if ($addNewSchoolStmt->execute())
                            {
                                $_SESSION['session_new_school'] = 'success';
                                $_SESSION['session_schoolName'] = $secure_schoolName;
                                header('Location: school_list.php');
                                exit();
                            }
                            else
                            {
                                $_SESSION['session_new_school'] = 'failed';
                                $_SESSION['session_schoolName'] = '';
                                $_SESSION['session_schoolName_failed'] = $secure_schoolName;
                                header('Location: new_school.php');
                                exit();
                            }
                        }
                        else
                        {
                            $school_id = str_replace("SCH-","",$latestSchoolId);
                            $school_id = str_pad($school_id + 1,2,0, STR_PAD_LEFT);
                            $school_id = "SCH-" .$school_id;
                            $date_created = date("Y-m-d H:i:s",strtotime($currentTime));

                            // add new school
                            $addNewSchoolSql = 'INSERT INTO `schools`(`school_id`, `school_name`, `school_status`, `school_email`, `date_created`, `created_by`) VALUES (?,?,?,?,?,?)';
                            $addNewSchoolStmt = $dbConnection->prepare($addNewSchoolSql);
                            $addNewSchoolStmt->bind_param('ssssss',$school_id,$secure_schoolName,$school_status,$secure_schoolEmail,$date_created,$_SESSION['user_Pf_Number']);
                            if ($addNewSchoolStmt->execute())
                            {
                                $_SESSION['session_new_school'] = 'success';
                                $_SESSION['session_schoolName'] = $secure_schoolName;
                                header('Location: school_list.php');
                                exit();
                            }
                            else
                            {
                                $_SESSION['session_new_school'] = 'failed';
                                $_SESSION['session_schoolName'] = '';
                                $_SESSION['session_schoolName_failed'] = $secure_schoolName;
                                header('Location: new_school.php');
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
                <title>epes | Add new school</title>
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
                                        <h1 class="m-0">Add new school</h1>
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
                                        Add new school
                                    </div>
                                    <?php
                                        if (isset($_SESSION['session_new_school'])==true && isset($_SESSION['session_schoolName'])==true && empty($_SESSION['session_new_school'])==false && empty($_SESSION['session_schoolName'])==false && $_SESSION['session_new_school']=='failed'){
                                    ?>
                                        <div class="col-sm-6 col-md-4 col-lg-4 mx-auto">
                                            <div class="alert alert-info text-center" role="alert">
                                                There has been an error adding <b><?php echo $_SESSION['session_schoolName_failed']; ?></b>
                                                <br>
                                                Please try again.
                                                <?php $_SESSION['session_new_school']=''; $_SESSION['session_schoolName_failed']='';?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <div class="card-body">
                                    <div class="text-center">
                                        <?php
                                            if (isset($_GET['newSchoolError']) == true && empty($_GET['newSchoolError']) == false && $_GET['newSchoolError'] == 'emptySchoolName'){
                                        ?>
                                            <span class="text-danger font-weight-bold">Please enter the school name</span>
                                        <?php }elseif (isset($_GET['newSchoolError']) == true && empty($_GET['newSchoolError']) == false && $_GET['newSchoolError'] == 'emptyEmail'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter the school email</span>
                                        <?php }elseif (isset($_GET['newSchoolError']) == true && empty($_GET['newSchoolError']) == false && $_GET['newSchoolError'] == 'invalidSchoolEmail'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter a valid school email</span>
                                        <?php }elseif (isset($_GET['newSchoolError']) == true && empty($_GET['newSchoolError']) == false && $_GET['newSchoolError'] == 'schoolNameExists'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter a unique school name</span>
                                        <?php }elseif (isset($_GET['newSchoolError']) == true && empty($_GET['newSchoolError']) == false && $_GET['newSchoolError'] == 'schoolEmailExists'){ ?>
                                            <span class="text-danger font-weight-bold">Please enter a unique school email</span>
                                        <?php } ?>
                                    </div>
                                    <form action="" method="post" onsubmit="return newSchoolJsValidation();">
                                    <!-- <form action="" method="post"> -->
                                        <div>
                                            <div class="form-group">
                                                <label for="schoolName" class="control-label">School name <span class="text-danger">*</span></label>
                                                <span id="schoolNameStatus" class="d-block"></span>
                                                <input type="text" name="schoolName" id="schoolName" class="form-control form-control-sm" style="text-transform: uppercase;" autofocus autocomplete="off">
                                            </div>

                                            <div class="form-group">
                                                <label for="schoolEmail" class="control-label">School email</label>
                                                <span id="schoolEmailStatus" class="d-block"></span>
                                                <input type="text" name="schoolEmail" id="schoolEmail" class="form-control form-control-sm" style="text-transform: uppercase;" autocomplete="off">
                                            </div>
                                            <button type="submit" name="addNewSchool" id="addNewSchool" class="text-decoration-none btn btn-sm btn-primary btn-block">
                                                Add new school
                                            </button>
                                        </div>
                                    </form>
                                    <script>
                                        function newSchoolJsValidation()
                                        {
                                            // Set the values to be valid by default
                                            var is_it_valid = true;

                                            var departmentNameRegex = /^[a-zA-Z0-9\s-_]+$/;
                                            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                                            if (document.getElementById('schoolName').value == "")
                                            {
                                                is_it_valid = false;
                                                document.getElementById('schoolName').style.border = "1px solid red";
                                                document.getElementById('schoolNameStatus').style.color = "red";
                                                document.getElementById('schoolNameStatus').innerHTML = "Please enter the school name";
                                            }
                                            // else if (departmentNameRegex.test(document.getElementById('schoolName').value) == false)
                                            // {
                                            //     is_it_valid = false;
                                            //     document.getElementById('schoolName').style.border = "1px solid red";
                                            //     document.getElementById('schoolNameStatus').style.color = "red";
                                            //     document.getElementById('schoolNameStatus').innerHTML = "Please enter a valid school name";
                                            // }
                                            else
                                            {
                                                document.getElementById('schoolName').style.border = "1px solid green";
                                                document.getElementById('schoolNameStatus').innerHTML = "";
                                            }

                                            // if (document.getElementById('schoolEmail').value == "")
                                            // {
                                            //     is_it_valid = false;
                                            //     document.getElementById('schoolEmail').style.border = "1px solid red";
                                            //     document.getElementById('schoolEmailStatus').style.color = "red";
                                            //     document.getElementById('schoolEmailStatus').innerHTML = "Please enter the school email";
                                            // }
                                            // else 
                                            if (document.getElementById('schoolEmail').value !== "" && emailRegex.test(document.getElementById('schoolEmail').value) == false)
                                            {
                                                is_it_valid = false;
                                                document.getElementById('schoolEmail').style.border = "1px solid red";
                                                document.getElementById('schoolEmailStatus').style.color = "red";
                                                document.getElementById('schoolEmailStatus').innerHTML = "Please enter a valid school email";
                                            }
                                            else if (document.getElementById('schoolEmail').value !== "" && emailRegex.test(document.getElementById('schoolEmail').value) == true)
                                            {
                                                document.getElementById('schoolEmail').style.border = "1px solid green";
                                                document.getElementById('schoolEmailStatus').innerHTML = "";
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