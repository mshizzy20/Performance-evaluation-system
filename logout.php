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
        /* ...redirect user to login.php web page... */
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
            header("Location: login.php?loginError=loginError01&link=logout");
            exit();
        }
        else
        {
            // this is the current time
            $currentTime = date("Y-m-d H:i:s",strtotime($currentTime));

            // Prepare a SQL statement with a parameter placeholder
            $updateProfileTimimgStmt = $dbConnection->prepare('UPDATE `users` SET `last_seen`=? WHERE `user_Pf_Number`=?');

            // Bind parameters to the statement
            $updateProfileTimimgStmt->bind_param('ss', $currentTime, $_SESSION['user_Pf_Number']);

            // if the statement is execute successsfully, redirect to login.php
            if ($updateProfileTimimgStmt->execute())
            {
                // start session
                session_start();
                // unset session
                session_unset();
                // destroy session
                session_destroy();
                // redirect user to login.php web page
                // header('Location: login.php');
                header('Location: login');
                // exit
                exit();
            }
        }
    }
?>