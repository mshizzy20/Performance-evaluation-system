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
                    <title>epes | Peer &amp; peer evaluation</title>
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
                                            <h1 class="m-0">Peer &amp; peer evaluation</h1>
                                        </div>
                                    </div>
                                    <hr class="border-primary">
                                    <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </button>
                                </div>
                                <div class="container">
                                    <div class="col-lg-10 mx-auto">
                                        <div class="card">
                                            <div class="card-header font-weight-bold">
                                                Peers you selected
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">PF number</th>
                                                            <th class="text-center">Name</th>
                                                            <th class="col-2 text-right">View evaluation</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            // Prepare a SQL statement with a parameter placeholder
                                                            $selectPeersISelectedStmt = $dbConnection->prepare('SELECT * FROM `peer_evaluators` WHERE `pf_being_evaluated`=? ORDER BY `time_selected` ASC');
                                                            // Bind parameters to the statement
                                                            $selectPeersISelectedStmt->bind_param('s',$_SESSION['user_Pf_Number']);
                                                            // Execute the statement
                                                            $selectPeersISelectedStmt->execute();
                                                            // Retrieve the result set
                                                            $selectPeersISelectedResult = $selectPeersISelectedStmt->get_result();
                                                            // Fetch data
                                                            while ($selectPeersISelectedRow = $selectPeersISelectedResult->fetch_assoc())
                                                            {
                                                        ?>
                                                                <tr>
                                                                    <th class="text-center"><?php echo $selectPeersISelectedRow['peer_pf']; ?></th>
                                                                    <td class="text-center">
                                                                        <?php
                                                                            // SQL SELECT statement with parameters
                                                                            $selectPeerDetailsSQL = 'SELECT `user_Pf_Number`, `first_name`, `last_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                                            // Prepare the resulting SQL statement query
                                                                            $selectPeerDetailsSTMT = $dbConnection->prepare($selectPeerDetailsSQL);
                                                                            // Bind all variables to the prepared SQL statement
                                                                            $selectPeerDetailsSTMT->bind_param('s', $selectPeersISelectedRow['peer_pf']);
                                                                            if
                                                                            // ... the SQL statement is executed, ...
                                                                            ($selectPeerDetailsSTMT->execute())
                                                                            {
                                                                                // Get the mysqli result variable from the SQL statement
                                                                                $selectPeerDetailsSTMTResult = $selectPeerDetailsSTMT->get_result();
                                                                                // Fetch data
                                                                                $selectPeerDetailsSTMTRow = $selectPeerDetailsSTMTResult->fetch_assoc();
                                                                                echo''.$selectPeerDetailsSTMTRow["first_name"].' '.$selectPeerDetailsSTMTRow["last_name"];
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#peerEvaluation<?php echo $selectPeerDetailsSTMTRow['user_Pf_Number'];?>">
                                                                            View
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                <div class="modal fade" id="peerEvaluation<?php echo $selectPeerDetailsSTMTRow['user_Pf_Number'];?>" tabindex="-1" role="dialog" aria-labelledby="peerEvaluationLabel" aria-hidden="true" style="-webkit-user-select:none;-ms-user-select:none;user-select:none;" data-backdrop="static" data-keyboard="false">
                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="peerEvaluationLabel">Evaluation for 
                                                                                <?php echo $selectPeerDetailsSTMTRow["first_name"].' '.$selectPeerDetailsSTMTRow["last_name"];?>
                                                                            </h5>
                                                                            <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </span>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <?php
                                                                                // SQL SELECT statement with parameters
                                                                                $selectEvaluationSQL = 'SELECT `p_e_attribute_id`, `peer_evaluation_score` FROM `peer_evaluation` WHERE `pf_being_evaluated`=? AND `peer_pf`=? ORDER BY `datePosted` ASC';
                                                                                // Prepare the resulting SQL statement query
                                                                                $selectEvaluationSTMT = $dbConnection->prepare($selectEvaluationSQL);
                                                                                // Bind all variables to the prepared SQL statement
                                                                                $selectEvaluationSTMT->bind_param('ss', $selectPeerDetailsSTMTRow['user_Pf_Number'],$_SESSION['user_Pf_Number']);
                                                                                // Execute the SQL statement
                                                                                $selectEvaluationSTMT->execute();
                                                                                // Get the mysqli result variable from the SQL statement
                                                                                $selectEvaluationSTMT = $selectEvaluationSTMT->get_result();
                                                                                if($selectEvaluationSTMT->num_rows==0){
                                                                                    echo 'No data';
                                                                                }else{
                                                                                    // Fetch data
                                                                                    while($selectEvaluationSTMTRow = $selectEvaluationSTMT->fetch_assoc()){
                                                                            ?>
                                                                                    <dl>
                                                                                        <?php
                                                                                            // SQL SELECT statement with parameters
                                                                                            $p_e_attribute_id_namenSQL = 'SELECT `attribute` FROM `peer_evaluation_form_data` WHERE `p_e_attribute_id`=?';
                                                                                            // Prepare the resulting SQL statement query
                                                                                            $p_e_attribute_id_namenSTMT = $dbConnection->prepare($p_e_attribute_id_namenSQL);
                                                                                            // Bind all variables to the prepared SQL statement
                                                                                            $p_e_attribute_id_namenSTMT->bind_param('s', $selectEvaluationSTMTRow['p_e_attribute_id']);
                                                                                            // Execute the SQL statement
                                                                                            $p_e_attribute_id_namenSTMT->execute();
                                                                                            // Get the mysqli result variable from the SQL statement
                                                                                            $p_e_attribute_id_namenSTMT = $p_e_attribute_id_namenSTMT->get_result();
                                                                                            // Fetch data
                                                                                            $p_e_attribute_id_namenSTMTRow = $p_e_attribute_id_namenSTMT->fetch_assoc();
                                                                                        ?>
                                                                                        <dt><?php echo $p_e_attribute_id_namenSTMTRow['attribute']; ?></dt>
                                                                                        <dd><?php echo str_replace('\r\n','<br>',$selectEvaluationSTMTRow['peer_evaluation_score']); ?></dd>
                                                                                    </dl>
                                                                            <?php }} ?>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
                                                                        </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-header font-weight-bold">
                                                Peers who selected you
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">PF number</th>
                                                            <th class="text-center">Name</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            $cod = 5;
                                                            $department_id = $_SESSION['department_id'];
                                                            // Prepare a SQL statement with a parameter placeholder
                                                            $selectPeersISelectedStmt = $dbConnection->prepare('SELECT * FROM `peer_evaluators` WHERE `peer_pf`=? ORDER BY `time_selected` ASC');
                                                            // Bind parameters to the statement
                                                            $selectPeersISelectedStmt->bind_param('s',$_SESSION['user_Pf_Number']);
                                                            // Execute the statement
                                                            $selectPeersISelectedStmt->execute();
                                                            // Retrieve the result set
                                                            $selectPeersISelectedResult = $selectPeersISelectedStmt->get_result();
                                                            // Fetch data
                                                            while ($selectPeersISelectedRow = $selectPeersISelectedResult->fetch_assoc())
                                                            {
                                                        ?>
                                                        <tr>
                                                            <th class="text-center">
                                                                <?php echo $selectPeersISelectedRow['pf_being_evaluated']; ?>
                                                            </th>
                                                            <td class="text-center">
                                                                <?php
                                                                    // SQL SELECT statement with parameters
                                                                    $selectCodDetailsSQL = 'SELECT `user_Pf_Number`, `first_name`, `last_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                                    // Prepare the resulting SQL statement query
                                                                    $selectCodDetailsSTMT = $dbConnection->prepare($selectCodDetailsSQL);
                                                                    // Bind all variables to the prepared SQL statement
                                                                    $selectCodDetailsSTMT->bind_param('s', $selectPeersISelectedRow['pf_being_evaluated']);
                                                                    if
                                                                    // ... the SQL statement is executed, ...
                                                                    ($selectCodDetailsSTMT->execute())
                                                                    {
                                                                        // Get the mysqli result variable from the SQL statement
                                                                        $selectCodDetailsSTMTResult = $selectCodDetailsSTMT->get_result();
                                                                        // Fetch data
                                                                        while ($selectCodDetailsSTMTRow = $selectCodDetailsSTMTResult->fetch_assoc())
                                                                        {
                                                                            echo''.$selectCodDetailsSTMTRow["first_name"].' '.$selectCodDetailsSTMTRow["last_name"];
                                                                        }
                                                                    }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
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