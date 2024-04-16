<?php
    /* Database configuration */

    // Database host
    $servername = "localhost";
    
    // Database username
    $username = "root";
    
    // Database password
    $password = "";
    
    // Database name
    $database = "epes_v2_db";
    

    // Create database connection
    $dbConnection = mysqli_connect($servername, $username, $password, $database);

    // Check database connection
    if ($dbConnection->connect_error)
    {
        die("Connection failed: " . $dbConnection->connect_error);
    }
?>