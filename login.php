<?php
    // start a new session
    session_start();

    if (isset($_SESSION['user_Pf_Number']) == true)
    {
        header('Location:home');
        exit();
    }
    
    // Check if the form is submitted
    // If the current HTTP request method is POST and if a form element with the name attribute set to 'signIn' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['signIn']) == true)
    {
        // require connection to the database
        require 'databaseConnection.php';
        // require connection to the database
        require 'databaseCurrentTime.php';
        
        // Escape the user input before using it in an SQL query
        $secureUserEmail = mysqli_real_escape_string($dbConnection, $_POST['userEmail']);
        $secureUserPassword = mysqli_real_escape_string($dbConnection, $_POST['userPassword']);

        // If  $secureUserEmail is empty, redirect to login.php showing "Please ensure you enter your login email" error message and exit
        if (empty($secureUserEmail) == true)
        {
            // header('Location: login.php?loginError=emptyEmail');
            header('Location: login?loginError=emptyEmail');
            exit();
        }
        // else if  $secureUserPassword is empty, redirect to login.php showing "Please ensure you enter your login password" error message and exit
        elseif (empty($secureUserPassword) == true)
        {
            // header('Location: login.php?loginError=emptyPassword');
            header('Location: login?loginError=emptyPassword');
            exit();
        }
        else
        {
            // Prepare a SQL statement with a parameter placeholder
            $stmt = $dbConnection->prepare('SELECT `email` FROM users WHERE email=?');

            // Bind parameters to the statement
            $stmt->bind_param('s', $secureUserEmail);

            // Execute the statement
            $stmt->execute();

            // Retrieve the result set
            $result = $stmt->get_result();

            // Counting the number of rows
            if ($num_rows = $result->num_rows < 1)
            {
                // header('Location: login.php?loginError=invalidUser');
                header('Location: login?loginError=invalidUser');
                exit();
            }
            else
            {
                // Prepare a SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare('SELECT * FROM users WHERE email=?');

                // Bind parameters to the statement
                $stmt->bind_param('s', $secureUserEmail);

                // Execute the statement
                $stmt->execute();

                // Retrieve the result set
                $result = $stmt->get_result();

                // Fetch data
                while ($userDetail = $result->fetch_assoc())
                {
                    // Verifies that a password matches a hash
                    if (password_verify($secureUserPassword, $userDetail['password']) == false)
                    {
                        // header('Location: login.php?loginError=invalidCredentials');
                        header('Location: login?loginError=invalidCredentials');
                        exit();
                    }
                    // elseif is not set to integer 5, redirect to login.php showing "Please contact the administrator for assistence" error message and exit
                    elseif ($userDetail['system_access'] !== 5)
                    {
                        // header('Location: login.php?loginError=loginError01');
                        header('Location: login?loginError=loginError01');
                        exit();
                    }
                    else
                    {
                        // Check if session is not started
                        if (session_status() === PHP_SESSION_NONE)
                        {
                            // start a new session if session is not started
                            session_start();
                        }

                        // $_SESSION for 'user_Pf_Number'
                        $_SESSION['user_Pf_Number'] = $userDetail['user_Pf_Number'];

                        // $_SESSION for 'title'
                        $_SESSION['title'] = $userDetail['title'];

                        // $_SESSION for 'user_role'
                        $_SESSION['user_role'] = $userDetail['user_role'];

                        // $_SESSION for 'first_name'
                        $_SESSION['first_name'] = $userDetail['first_name'];

                        // $_SESSION for 'middle_name'
                        $_SESSION['middle_name'] = $userDetail['middle_name'];

                        // $_SESSION for 'last_name'
                        $_SESSION['last_name'] = $userDetail['last_name'];

                        // $_SESSION for 'email'
                        $_SESSION['email'] = $userDetail['email'];

                        // $_SESSION for 'department_id'
                        $_SESSION['department_id'] = $userDetail['department_id'];

                        // $_SESSION for 'avatar'
                        $_SESSION['avatar'] = $userDetail['avatar'];

                        // $_SESSION for 'date_added'
                        $_SESSION['date_added'] = $userDetail['date_added'];

                        // $_SESSION for 'system_access'
                        $_SESSION['system_access'] = $userDetail['system_access'];

                        // Update the staff's last_login time and last_seen in the staff table 
                        $last_seen = 'online';

                        // this is the current time
                        $last_login = date("Y-m-d H:i:s",strtotime($currentTime));

                        // Prepare a SQL statement with a parameter placeholder
                        $updateProfileTimimgStmt = $dbConnection->prepare('UPDATE `users` SET `last_login`=?, `last_seen`=? WHERE `user_Pf_Number`=?');

                        // Bind parameters to the statement
                        $updateProfileTimimgStmt->bind_param('sss', $last_login, $last_seen, $_SESSION['user_Pf_Number']);

                        // if the statement is execute successsfully, redirect user to index.php
                        if ($updateProfileTimimgStmt->execute())
                        {
                            
                            // $_SESSION['lastPage'] is set and is not empty, redirect user to the $_SESSION['lastPage'] web page
                            if (isset($_SESSION['lastPage']) == true && empty($_SESSION['lastPage']) == false)
                            {
                                header('Location:'.$_SESSION['lastPage'].'.php');
                                exit();
                            }
                            else
                            {
                                // header('Location:home.php');
                                header('Location:home');
                                exit();
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
    <title>epes | login</title>
</head>
<body class="hold-transition login-page bg-dark">
    <!-- include 'header.php' php file. -->
    <?php include 'header.php' ?>
    <div class="login-box">
        <div class="login-logo">
            <div class="text-white font-weight-bold">
                Employee Performance Evaluation System
            </div>
        </div>
        <div class="login-box">
            <div class="login-logo">
                <a href="#" class="text-white">Logo here</a>
            </div>
            <div class="card">
                <div class="card-body login-card-body">
                    <form action="" method="post" id="login-form" onsubmit="return loginJsValidation()">
                        <div>
                            <label for="userEmail" class="control-label">Email <span class="text-danger">*</span></label>
                            <span id="emailStatus" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="email" name="userEmail" id="userEmail" placeholder="Email" class="form-control" autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="userPassword" class="control-label">Password <span class="text-danger">*</span></label>
                            <span id="userPasswordStatus" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="password" name="userPassword" id="userPassword" placeholder="Password" class="form-control">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="rememberMe">
                                    <label for="rememberMe">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="submit" name="signIn" id="signIn" class="btn btn-primary btn-block">Sign In</button>

                                <button type="button" name="loadingSignin" id="loadingSignin" class="d-none btn btn-primary btn-block">
                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                    <script>
                        function loginJsValidation()
                        {
                            // Set the value of is_input_valid to be true by default
                            var is_input_valid = true;

                            document.getElementById('signIn').className = "d-none";
                            document.getElementById('loadingSignin').className = "btn btn-primary btn-block";
                                
                            if (document.getElementById('userEmail').value === "")
                            {
                                is_input_valid = false;
                                document.getElementById('userEmail').style.border = "1px solid red";
                                document.getElementById('emailStatus').style.color = "#dc3545";
                                document.getElementById('emailStatus').innerHTML = "Please enter your email";
                                // no loader
                                document.getElementById('loadingSignin').className = "d-none";
                                document.getElementById('signIn').className = "btn btn-primary btn-block";
                            }
                            else
                            {
                                document.getElementById('userEmail').style.border = "1px solid #198754";
                                document.getElementById('emailStatus').innerHTML = "";
                                // loader executing
                                document.getElementById('signIn').className = "d-none";
                                document.getElementById('loadingSignin').className = "btn btn-primary btn-block";
                            }

                            if (document.getElementById('userPassword').value === "")
                            {
                                is_input_valid = false;
                                document.getElementById('userPassword').style.border = "1px solid red";
                                document.getElementById('userPasswordStatus').style.color = "#dc3545";
                                document.getElementById('userPasswordStatus').innerHTML = "Please enter your password";
                                // no loader
                                document.getElementById('loadingSignin').className = "d-none";
                                document.getElementById('signIn').className = "btn btn-primary btn-block";
                            }
                            else
                            {
                                document.getElementById('userPassword').style.border = "1px solid #198754";
                                document.getElementById('userPasswordStatus').innerHTML = "";
                                // loader executing
                                document.getElementById('signIn').className = "d-none";
                                document.getElementById('loadingSignin').className = "btn btn-primary btn-block";
                            }

                            return is_input_valid;
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
    <!-- include 'footer.php' php file. -->
    <?php include 'footer.php' ?>
</body>
</html>