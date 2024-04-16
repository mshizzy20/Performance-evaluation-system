<?php
    if(isset($_POST['status']) && !empty($_POST['status']))
    {
        // start the session
        session_start();
        
        if($_POST['status']=="active")
        {
            // require connection to the database
            require 'databaseConnection.php';

            // require connection to the database
            require 'databaseCurrentTime.php';

            // Set the online status to 'online'
            $online = 'online';

            // Prepare the SQL statement to update the last_seen field in the users table
            $updateLastSeenSql = "UPDATE `users` SET `last_seen`=? WHERE `user_Pf_Number`=?";

            // Prepare the statement for execution
            $updateLastSeenStmt = $dbConnection->prepare($updateLastSeenSql);

            // Bind the parameters to the statement
            $updateLastSeenStmt->bind_param('ss', $online, $_SESSION['user_Pf_Number']);

            // If the SQL statement is executed, update the value of last_seen session variable to the value of $online variable
            if($updateLastSeenStmt->execute())
            {$_SESSION['last_seen']="online";}
        }
        elseif($_POST['status']=="inactive")
        {
            // require connection to the database
            require 'databaseConnection.php';

            // require connection to the database
            require 'databaseCurrentTime.php';

            // get the current time
            $currentTime = date("Y-m-d H:i:s", strtotime($currentTime));

            // prepare the SQL statement to update the last seen time for the user
            $updateLastSeenSql = "UPDATE `users` SET `last_seen`=? WHERE `user_Pf_Number`=?";

            // create a prepared statement
            $updateLastSeenStmt = $dbConnection->prepare($updateLastSeenSql);

            // bind the parameters to the prepared statement
            $updateLastSeenStmt->bind_param('ss', $currentTime, $_SESSION['user_Pf_Number']);
            
            // If the SQL statement is executed, update the value of last_seen session variable to the value of $currentTime variable
            if($updateLastSeenStmt->execute())
            {$_SESSION['last_seen']=$currentTime;}
        }
    }
?>