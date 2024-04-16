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
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>epes | Home</title>
                <!-- require header.php php file -->
                <?php require 'header.php' ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <div class="wrapper">
                    <!-- require topbar.php php file -->
                    <?php require 'topbar.php' ?>
                    <!-- require sidebar.php php file -->
                    <?php require 'sidebar.php' ?>

                    <div class="content-wrapper">
                        <div class="content-header">
                            <div class="container-fluid">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <h1 class="m-0">Home</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                            </div>
                        </div>
                        <div class="ml-4 font-italic">
                            <span id="greeting"></span>
                        </div>
                        <script language="javascript" type="text/javascript">
                            var timerID = null;
                            var timerRunning = false;
                            function stopclock ()
                            {
                                if(timerRunning)
                                clearTimeout(timerID);
                                timerRunning = false;
                            }
                            function showtime ()
                            {
                                var now = new Date();
                                var hours = now.getHours();
                                var minutes = now.getMinutes();
                                var seconds = now.getSeconds();

                                hours=((hours < 10) ? "0" : "") + hours;
                                minutes=((minutes < 10) ? ":0" : ":") + minutes;
                                seconds=((seconds < 10) ? ":0" : ":") + seconds;
                                // amPM=((hours >= 12) ? " PM" : " AM");
                                var currentTime = hours+minutes+seconds;

                                //“Good morning” is generally used from 5:00 a.m. to 11:59 p.m.
                                if (currentTime>="05:00:00" && currentTime<="11:59:59")
                                {
                                    Greetings = "Good morning"+" <?php echo $_SESSION['first_name']; ?>"+"";
                                }
                                //“Hello” time is from 12:00 p.m
                                if (currentTime>="12:00:00")
                                {
                                    Greetings = "Hello"+" <?php echo $_SESSION['first_name']; ?>"+"";
                                }
                                //“Good afternoon” time is from 12:00 p.m. to 6:00 p.m.
                                if (currentTime>="12:01:00" && currentTime<="18:00:00")
                                {
                                    Greetings = "Good afternoon"+" <?php echo $_SESSION['first_name']; ?>"+"";
                                }
                                //“Good evening” is often used after 6:01 p.m or when the sun goes down.
                                if (currentTime>="18:01:00")
                                {
                                    Greetings = "Good evening"+" <?php echo $_SESSION['first_name']; ?>"+"";
                                }
                                document.getElementById('time').innerHTML = currentTime;
                                document.getElementById('greeting').innerHTML = Greetings;
                                timerID = setTimeout("showtime()",1000);
                                timerRunning = true;
                            }
                            function startclock()
                            {
                                stopclock();
                                showtime();
                            }
                            window.onload=startclock;
                        </script>
                        <!-- Main content -->
                        <section class="content">
                            <div class="container-fluid">
                                <?php if ($_SESSION['user_role'] === 5){ ?>
                                    <div class="row">
                                        <a href="department_list" class="col-12 col-sm-6 col-md-4">
                                            <div>
                                                <div class="small-box bg-light shadow-sm border">
                                                    <div class="inner">
                                                        <h3>
                                                            <?php
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $countDepartmentsStmt = $dbConnection->prepare('SELECT COUNT(department_id) AS `departmentIdCount` FROM `departments`');

                                                                // Execute the statement
                                                                $countDepartmentsStmt->execute();

                                                                // Retrieve the result set
                                                                $countDepartmentsResult = $countDepartmentsStmt->get_result();

                                                                // Fetch data
                                                                while ($countDepartment = $countDepartmentsResult->fetch_assoc())
                                                                {
                                                                    echo $countDepartment['departmentIdCount'];
                                                                }
                                                            ?>
                                                        </h3>
                                                        <p>Total Departments</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fa fa-th-list"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <a href="employee_list" class="col-12 col-sm-6 col-md-4">
                                            <div>
                                                <div class="small-box bg-light shadow-sm border">
                                                    <div class="inner">
                                                        <h3>
                                                            <?php
                                                                $user_role = 5;
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $countPfNumberStmt = $dbConnection->prepare('SELECT COUNT(user_Pf_Number) AS `userPfNumberCount` FROM `users` WHERE `user_role`!=?');

                                                                // Bind parameters to the statement
                                                                $countPfNumberStmt->bind_param('i',$user_role);

                                                                // Execute the statement
                                                                $countPfNumberStmt->execute();

                                                                // Retrieve the result set
                                                                $countPfNumberResult = $countPfNumberStmt->get_result();

                                                                // Fetch data
                                                                while ($countPfNumber = $countPfNumberResult->fetch_assoc())
                                                                {
                                                                    echo $countPfNumber['userPfNumberCount'];
                                                                }
                                                            ?>
                                                        </h3>
                                                        <p>Total Employees</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fa fa-users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <a href="cod_list" class="col-12 col-sm-6 col-md-4">
                                            <div>
                                                <div class="small-box bg-light shadow-sm border">
                                                    <div class="inner">
                                                        <h3>
                                                            <?php
                                                                $is_cod = 5;
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $countCodPfNumberStmt = $dbConnection->prepare('SELECT COUNT(user_Pf_Number) AS `codPfNumberCount` FROM `users` WHERE `is_cod`=?');

                                                                // Bind parameters to the statement
                                                                $countCodPfNumberStmt->bind_param('i', $is_cod,);

                                                                // Execute the statement
                                                                $countCodPfNumberStmt->execute();

                                                                // Retrieve the result set
                                                                $countCodPfNumberResult = $countCodPfNumberStmt->get_result();

                                                                // Fetch data
                                                                while ($countCodPfNumber = $countCodPfNumberResult->fetch_assoc())
                                                                {
                                                                    echo $countCodPfNumber['codPfNumberCount'];
                                                                }
                                                            ?>
                                                        </h3>
                                                        <p>Total COD</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fa fa-list-alt"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <a href="lecturers_list" class="col-12 col-sm-6 col-md-4">
                                            <div>
                                                <div class="small-box bg-light shadow-sm border">
                                                    <div class="inner">
                                                        <h3>
                                                            <?php
                                                                $user_role = 7;
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $countCodPfNumberStmt = $dbConnection->prepare('SELECT COUNT(user_Pf_Number) AS `codPfNumberCount` FROM `users` WHERE `user_role`=?');

                                                                // Bind parameters to the statement
                                                                $countCodPfNumberStmt->bind_param('i', $user_role,);

                                                                // Execute the statement
                                                                $countCodPfNumberStmt->execute();

                                                                // Retrieve the result set
                                                                $countCodPfNumberResult = $countCodPfNumberStmt->get_result();

                                                                // Fetch data
                                                                while ($countCodPfNumber = $countCodPfNumberResult->fetch_assoc())
                                                                {
                                                                    echo $countCodPfNumber['codPfNumberCount'];
                                                                }
                                                            ?>
                                                        </h3>
                                                        <p>Total lecturers</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fa fa-list-alt"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php }elseif ($_SESSION['user_role'] === 6){ ?>
                                    <div class="row">
                                        <a href="department_members" class="col-12 col-sm-6 col-md-4">
                                            <div>
                                                <div class="small-box bg-light shadow-sm border">
                                                    <div class="inner">
                                                        <h3>
                                                            <?php
                                                                // Prepare a SQL statement with a parameter placeholder
                                                                $countDepartmentPfNumberStmt = $dbConnection->prepare('SELECT COUNT(user_Pf_Number) AS `departmentPfNumberCount` FROM `users` WHERE `department_id`=?');

                                                                // Bind parameters to the statement
                                                                $countDepartmentPfNumberStmt->bind_param('s',$_SESSION['department_id']);

                                                                // Execute the statement
                                                                $countDepartmentPfNumberStmt->execute();

                                                                // Retrieve the result set
                                                                $countDepartmentPfNumberResult = $countDepartmentPfNumberStmt->get_result();

                                                                // Fetch data
                                                                while ($countDepartmentPfNumber = $countDepartmentPfNumberResult->fetch_assoc())
                                                                {
                                                                    echo $countDepartmentPfNumber['departmentPfNumberCount'];
                                                                }
                                                            ?>
                                                        </h3>
                                                        <p>Department members</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fa fa-users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php }elseif ($_SESSION['user_role'] === 7){ ?>
                                    <div class="row">
                                        <a href="peers_you_selected" class="col-12 col-sm-6 col-md-4">
                                            <div class="small-box bg-light shadow-sm border">
                                                <div class="inner">
                                                    <h3>
                                                        <?php
                                                            // Prepare the SQL statement
                                                            $stmt = $dbConnection->prepare('SELECT `pf_being_evaluated` FROM `peer_evaluators` WHERE `pf_being_evaluated`=?');
                                                            // Bind parameters
                                                            $stmt->bind_param("s", $_SESSION['user_Pf_Number']);
                                                            // Execute the statement
                                                            $stmt->execute();
                                                            // Store result
                                                            $stmt->store_result();
                                                            echo $stmt->num_rows;
                                                        ?>
                                                    </h3>
                                                    <p>Total peers you selected</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-users"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="peers_who_selected_you" class="col-12 col-sm-6 col-md-4">
                                            <div class="small-box bg-light shadow-sm border">
                                                <div class="inner">
                                                    <h3>
                                                        <?php
                                                            // Prepare the SQL statement
                                                            $stmt = $dbConnection->prepare('SELECT `peer_pf` FROM `peer_evaluators` WHERE `peer_pf`=?');
                                                            // Bind parameters
                                                            $stmt->bind_param("s", $_SESSION['user_Pf_Number']);
                                                            // Execute the statement
                                                            $stmt->execute();
                                                            // Store result
                                                            $stmt->store_result();
                                                            echo $stmt->num_rows;
                                                        ?>
                                                    </h3>
                                                    <p>Total peers who selected you</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-users"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </section>
                    </div>

                    <!-- require mainfooter.php php file -->
                    <?php require 'mainfooter.php' ?>
                </div>
                <!-- require footer.php php file -->
                <?php require 'footer.php' ?>
            </body>
        </html>
<?php } ?>