<?php
    // start a new session
    session_start();

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
        header('Location: login');
        exit();
    }
    else
    {
        // require connection to the database
        require 'databaseConnection.php';
        // require connection to the database
        require 'databaseCurrentTime.php';
        // SQL select statement
        $selectSystemAccessSql = 'SELECT `system_access`,`is_cod` FROM `users` WHERE `user_Pf_Number`=?';
        // Prepare the SQL statement with a parameter placeholder
        $selectSystemAccessStmt = $dbConnection->prepare($selectSystemAccessSql);
        // Bind parameters to the statement
        $selectSystemAccessStmt->bind_param('s', $_SESSION['user_Pf_Number']);
        // Execute the statement
        $selectSystemAccessStmt->execute();
        // Retrieve the result set
        $selectSystemAccessResult = $selectSystemAccessStmt->get_result();
        // Fetch data
        $systemAccessValue = $selectSystemAccessResult->fetch_assoc();
        // if is not set to integer 5, redirect to login.php showing "Please contact the administrator for assistence" error message and exit
        if ($systemAccessValue['system_access'] !== 5 || $systemAccessValue['is_cod'] !== 6)
        {
            // start session
            session_start();
            // unset session
            session_unset();
            // destroy session
            session_destroy();
            header('Location: login');
            exit();
        }
        
        $empty = "";
        // SQL select statement
        $evaluatedAchievementSql = 'SELECT `user_Pf_Number` FROM `achievement_evidence` WHERE `user_Pf_Number`=? AND `cod_score`=? AND `cod_score_date`=?';
        // Prepare the SQL statement with a parameter placeholder
        $evaluatedAchievementStmt = $dbConnection->prepare($evaluatedAchievementSql);
        // Bind parameters to the statement
        $evaluatedAchievementStmt->bind_param('sss', $_SESSION['user_Pf_Number'], $empty, $empty);
        // Execute the statement
        $evaluatedAchievementStmt->execute();
        // Retrieve the result set
        $evaluatedAchievementStmt->store_result();
        if ($evaluatedAchievementStmt->num_rows>0){
            header('Location: home');
            exit();
        }
        else
        { ?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>epes | Achievement report </title>
                    <!-- include header.php php file -->
                    <?php include 'header.php' ?>
                </head>
                <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                    <div class="wrapper">
                        <!-- include topbar.php php file -->
                        <?php include 'topbar.php'; ?>
                        <!-- include sidebar.php php file -->
                        <?php include 'sidebar.php'; ?>
                        <!-- require connection to the functions.php page -->
                        <?php require 'functions.php';?>
                        <div class="content-wrapper">
                            <div class="content-header">
                                <div class="container-fluid">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <h1 class="m-0">Achievement report </h1>
                                        </div>
                                    </div>
                                    <hr class="border-primary">
                                    <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </button>
                                </div>
                                <div class="container">
                                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                    <script type="text/javascript">
                                    google.charts.load('current', {'packages':['corechart']});
                                    google.charts.setOnLoadCallback(drawChart);

                                    function drawChart() {

                                        var data = google.visualization.arrayToDataTable([
                                        ['user_Pf_Number', 'cod_score'],
                                        <?php
                                            $sql = "SELECT `user_Pf_Number`,`attribute_id`, AVG(DISTINCT `cod_score`) AS `VAL` FROM achievement_evidence GROUP BY `attribute_id`";
                                            $fire = mysqli_query($dbConnection,$sql);
                                            while ($result = mysqli_fetch_assoc($fire)) {
                                                echo"['".$result['user_Pf_Number']."',".$result['VAL']."],";
                                            }
                                        ?>
                                        ]);

                                        var options = {
                                        title: 'Overall Performance'
                                        };

                                        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                                        chart.draw(data, options);
                                    }
                                    </script>
                                    <div id="piechart" style="width: 900px; height: 500px;"></div>
                                </div>
                            </div>
                             <!-- include mainfooter.php php file -->
                            <?php include 'mainfooter.php' ?>
                        </div>
                        <!-- include footer.php php file -->
                        <?php include 'footer.php' ?>
                    </div>
                </body>
            </html>
        <?php } ?>
<?php } ?>