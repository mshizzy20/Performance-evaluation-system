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
            header("Location: login?loginError=loginError01&link=changePassword");
            exit();
        }

         // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'changePassword' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['changePassword']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            // Escape the user input before using it in an SQL query
            $secure_currentPassword = mysqli_real_escape_string($dbConnection,$_POST['currentPassword']);
            $secure_newPassword = mysqli_real_escape_string($dbConnection,$_POST['newPassword']);
            $secure_confirmNewPassword = mysqli_real_escape_string($dbConnection,$_POST['confirmNewPassword']);

            // If $secure_currentPassword is empty, redirect to change_password.php showing "Please enter your current password" error message and exit
            if (empty($secure_currentPassword) == true)
            {
                header('Location: change_password?ChangePasswordError=emptyCurrentPassword');
                exit();
            }
            // else if $secure_newPassword is empty, redirect to change_password.php showing "Please enter your new password" error message and exit
            elseif (empty($secure_newPassword) == true)
            {
                header('Location: change_password?ChangePasswordError=emptyNewPassword');
                exit();
            }
            // else if $secure_confirmNewPassword is empty, redirect to change_password.php showing "Please reenter your new password" error message and exit
            elseif (empty($secure_confirmNewPassword) == true)
            {
                header('Location: change_password?ChangePasswordError=emptyConfirmNewPassword');
                exit();
            }
            // else if $secure_newPassword and $secure_confirmNewPassword are not identical, redirect to change_password.php showing "Passwords don't match" error message and exit
            elseif ($secure_newPassword !== $secure_confirmNewPassword)
            {
                header('Location: change_password?ChangePasswordError=noMatch');
                exit();
            }
            else
            {
                // SQL select statement
                $selectPasswordSql = 'SELECT `password` FROM `users` WHERE `user_Pf_Number`=?';

                // Prepare the SQL statement with a parameter placeholder
                $selectPasswordStmt = $dbConnection->prepare($selectPasswordSql);

                // Bind parameters to the statement
                $selectPasswordStmt->bind_param('s', $_SESSION['user_Pf_Number']);

                // Execute the statement
                $selectPasswordStmt->execute();

                // Retrieve the result set
                $selectPasswordResult = $selectPasswordStmt->get_result();

                // Fetch data
                while ($passwordValueRow = $selectPasswordResult->fetch_assoc())
                {
                    $passwordValue = $passwordValueRow['password'];
                }

                // If $secure_currentPassword is not the same as current password, redirect to change_password.php showing "Incorrect current password" error message and exit
                if (password_verify($secure_currentPassword, $passwordValue)==false)
                {
                    header('Location: change_password?ChangePasswordError=incorrectPassword');
                    exit();
                }
                else
                {
                    /* encrypt $newPassword */
                    $password = password_hash($secure_newPassword,PASSWORD_DEFAULT);

                    /* update staff's password */
                    $updatePasswordSql = 'UPDATE `users` SET `password`=? WHERE `user_Pf_Number`=?';
                    $updatePasswordStmt = $dbConnection->prepare($updatePasswordSql);
                    $updatePasswordStmt->bind_param('ss',$password, $_SESSION['user_Pf_Number']);

                    /* ..$updatePasswordStmt is executed successfully, notify the user */
                    if ($updatePasswordStmt->execute())
                    {
                        header('Location: change_password?ChangePasswordSuccess');
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
                <title>epes | Change password</title>
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
                                        <h1 class="m-0">Change password</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <!-- Main content -->
                        <section class="content">
                            <div class="col-6 mx-auto">
                                <div class="container-fluid">
                                    <div class="card">
                                        <div class="card-header font-weight-bold text-center">
                                            Change password
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center">
                                            <?php
                                                if (isset($_GET['ChangePasswordError']) == true && empty($_GET['ChangePasswordError']) == false && $_GET['ChangePasswordError'] == 'emptyCurrentPassword'){
                                            ?>
                                                <span class="text-danger font-weight-bold">Please enter your current password</span>
                                            <?php }elseif (isset($_GET['ChangePasswordError']) == true && empty($_GET['ChangePasswordError']) == false && $_GET['ChangePasswordError'] == 'emptyNewPassword'){ ?>
                                                <span class="text-danger font-weight-bold">Please enter your new password</span>
                                            <?php }elseif (isset($_GET['ChangePasswordError']) == true && empty($_GET['ChangePasswordError']) == false && $_GET['ChangePasswordError'] == 'emptyConfirmNewPassword'){ ?>
                                                <span class="text-danger font-weight-bold">Please reenter your new password</span>
                                            <?php }elseif (isset($_GET['ChangePasswordError']) == true && empty($_GET['ChangePasswordError']) == false && $_GET['ChangePasswordError'] == 'noMatch'){ ?>
                                                <span class="text-danger font-weight-bold">Passwords don't match</span>
                                            <?php }elseif (isset($_GET['ChangePasswordError']) == true && empty($_GET['ChangePasswordError']) == false && $_GET['ChangePasswordError'] == 'incorrectPassword'){ ?>
                                                <span class="text-danger font-weight-bold">Incorrect current password</span>
                                            <?php } elseif (isset($_GET['ChangePasswordError']) == false && empty($_GET['ChangePasswordError']) == true && isset($_GET['ChangePasswordSuccess']) == true){ ?>
                                                <span class="text-success font-weight-bold">Password has been changed successfully</span>
                                            <?php } ?>
                                        </div>
                                            <form action="" method="post" onsubmit="return changePasswordJsValidation();">
                                                <!-- <form action="" method="post"> -->
                                                <div class="form-group">
                                                    <label for="currentPassword" class="control-label">Current password <span class="text-danger">*</span></label>
                                                    <span id="currentPasswordStatus" class="d-block"></span>
                                                    <input type="password" name="currentPassword" id="currentPassword" placeholder="Current password" class="form-control form-control-sm" autofocus autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label for="newPassword" class="control-label">New password <span class="text-danger">*</span></label>
                                                    <span id="newPasswordStatus" class="d-block"></span>
                                                    <div class="input-group">
                                                        <input type="password" name="newPassword" id="newPassword" placeholder="New password" class="form-control">
                                                        <div class="input-group-append">
                                                            <div class="input-group-text" onclick="viewNewPassword()">
                                                                <span id="viewNewPasswordEye" class="fas fa-eye"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="confirmNewPassword" class="control-label">Confirm new password <span class="text-danger">*</span></label>
                                                    <span id="confirmNewPasswordStatus" class="d-block"></span>
                                                    <div class="input-group">
                                                        <input type="password" name="confirmNewPassword" id="confirmNewPassword" placeholder="Confirm new password" class="form-control">
                                                        <div class="input-group-append">
                                                            <div class="input-group-text" onclick="viewConfirmNewPassword()">
                                                                <span id="viewConfirmNewPasswordEye" class="fas fa-eye"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" name="changePassword" id="changePassword" class="text-decoration-none btn btn-sm btn-primary btn-block">
                                                    Change password
                                                </button>
                                            </form>
                                            <script>
                                                function viewNewPassword()
                                                {
                                                    if (document.getElementById("newPassword").type == "password")
                                                    {
                                                        document.getElementById("newPassword").type = "text";
                                                        document.getElementById("viewNewPasswordEye").className = "fas fa-eye-slash";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById("newPassword").type = "password";
                                                        document.getElementById("viewNewPasswordEye").className = "fas fa-eye";
                                                    }
                                                }

                                                function viewConfirmNewPassword()
                                                {
                                                    if (document.getElementById("confirmNewPassword").type == "password")
                                                    {
                                                        document.getElementById("confirmNewPassword").type = "text";
                                                        document.getElementById("viewConfirmNewPasswordEye").className = "fas fa-eye-slash";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById("confirmNewPassword").type = "password";
                                                        document.getElementById("viewConfirmNewPasswordEye").className = "fas fa-eye";
                                                    }
                                                }

                                                function changePasswordJsValidation()
                                                {
                                                    // Set the values to be valid by default
                                                    var is_it_valid = true;

                                                    if (document.getElementById('currentPassword').value == "")
                                                    {
                                                        is_it_valid = false;
                                                        document.getElementById('currentPassword').style.border = "1px solid red";
                                                        document.getElementById('currentPasswordStatus').style.color = "red";
                                                        document.getElementById('currentPasswordStatus').innerHTML = "Please enter your current password";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('currentPassword').style.border = "1px solid green";
                                                        document.getElementById('currentPasswordStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('newPassword').value == "")
                                                    {
                                                        is_it_valid = false;
                                                        document.getElementById('newPassword').style.border = "1px solid red";
                                                        document.getElementById('newPasswordStatus').style.color = "red";
                                                        document.getElementById('newPasswordStatus').innerHTML = "Please enter your new password";
                                                    }
                                                    else
                                                    {
                                                        document.getElementById('newPassword').style.border = "1px solid green";
                                                        document.getElementById('newPasswordStatus').innerHTML = "";
                                                    }

                                                    if (document.getElementById('newPassword').value !== "" && document.getElementById('confirmNewPassword').value == "")
                                                    {
                                                        is_it_valid = false;
                                                        document.getElementById('confirmNewPassword').style.border = "1px solid red";
                                                        document.getElementById('confirmNewPasswordStatus').style.color = "red";
                                                        document.getElementById('confirmNewPasswordStatus').innerHTML = "Please reenter your new password";
                                                    }
                                                    else
                                                    if (document.getElementById('newPassword').value !== document.getElementById('confirmNewPassword').value)
                                                    {
                                                        is_it_valid = false;
                                                        document.getElementById('confirmNewPassword').style.border = "1px solid red";
                                                        document.getElementById('confirmNewPasswordStatus').style.color = "red";
                                                        document.getElementById('confirmNewPasswordStatus').innerHTML = "Passwords don't match";
                                                    }
                                                    else if (document.getElementById('newPassword').value !== "" && document.getElementById('newPassword').value == document.getElementById('confirmNewPassword').value)
                                                    {
                                                        document.getElementById('confirmNewPassword').style.border = "1px solid green";
                                                        document.getElementById('confirmNewPasswordStatus').innerHTML = "";
                                                    }
                                                    return is_it_valid;
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- include mainfooter.php php file -->
                    <?php include 'mainfooter.php' ?>
                </div>
                <!-- include footer.php php file -->
                <?php include 'footer.php' ?>
            </body>
        </html>
<?php } ?>