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
        // redirect user to index.php web page
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
        // Prepare the SQL statement
        $selectSystemAccessStmt = $dbConnection->prepare($selectSystemAccessSql);
        // Bind parameters to the statement
        $selectSystemAccessStmt->bind_param('s', $_SESSION['user_Pf_Number']);
        // Execute the statement
        $selectSystemAccessStmt->execute();
        // Retrieve the result set directly from the prepared statement
        $selectSystemAccessResult = $selectSystemAccessStmt->get_result();
        // Fetch rows as an associative array
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
            header("Location: login.php");
            exit();
        }
        else
        {
            // SQL select statement
            $sql = 'SELECT `user_Pf_Number` FROM `expected_achievement` WHERE `user_Pf_Number`=?';
            // Prepare the SQL statement
            $stmt = $dbConnection->prepare($sql);
            // Bind parameters to the statement
            $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
            // Execute the statement
            $stmt->execute();
            // Store the result set
            $stmt->store_result();
            // else if the number of rows is not identical to integer 20, redirect to expected_achievement.php web page
            if ($stmt->num_rows!==20)
            {
                header("Location: expected_achievement.php");
                exit();
            }
            else
            {
                // SQL select statement
                $sql = 'SELECT `user_Pf_Number` FROM `self_achievement` WHERE `user_Pf_Number`=?';
                // Prepare the SQL statement
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                // Execute the statement
                $stmt->execute();
                // Store the result set
                $stmt->store_result();
                // else if the number of rows not identical to integer 20, redirect to achievement.php web page
                if ($stmt->num_rows!==20)
                {
                    header("Location: achievement.php");
                    exit();
                }
                else
                {
                    // SQL select statement
                    $sql = 'SELECT `user_Pf_Number` FROM `self_assessment` WHERE `user_Pf_Number`=?';
                    // Prepare the SQL statement
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                    // Execute the statement
                    $stmt->execute();
                    // Store the result set
                    $stmt->store_result();
                    // else if the number of rows not identical to integer 12, redirect to self_assessment.php web page
                    if ($stmt->num_rows!==12)
                    {
                        header("Location: self_assessment.php");
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
                <title>epes | Peer evaluation</title>
                <!-- include header.php php file -->
                <?php include 'header.php' ?>
                <script>
                    //JavaScript code to prevent the form from being resubmitted when the user refreshes the page.
                    if ( window.history.replaceState )
                    {
                        window.history.replaceState( null, null, window.location.href );
                    }
                </script>
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
                                        <h1 class="m-0">Peer evaluation</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-8 mx-auto">
                            <div class="container w-50 text-center d-none" id="peervaluationAlertDiv" style="display:none;visibility:hidden;">
                                <div role="alert" id="peervaluationAlert"></div>
                            </div>
                            <?php if (isset($_GET['error'])==true && $_GET['error']=="noUserFound"){ ?>
                                <div class="container w-50 text-center" id="errorAlertDiv">
                                    <div class="alert alert-warning text-center text-dark" role="alert">
                                        This user cannot be found
                                    </div>
                                </div>
                            <?php }elseif (isset($_GET['error'])==true && $_GET['error']=="notSelected"){ ?>
                                <div class="container w-50 text-center" id="errorAlertDiv">
                                    <div class="alert alert-warning text-center text-dark" role="alert">
                                        You are not allowed to evaluate this staff
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                                        <thead>
                                            <tr>
                                                <th class="">PF number</th>
                                                <th class="">Name</th>
                                                <th class="">Date selected</th>
                                                <?php
                                                    // SQL select statement
                                                    $sql = 'SELECT `user_Pf_Number` FROM `expected_achievement` WHERE `user_Pf_Number`=?';
                                                    // Prepare the SQL statement
                                                    $stmt = $dbConnection->prepare($sql);
                                                    // Bind parameters to the statement
                                                    $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Store the result set
                                                    $stmt->store_result();
                                                    // the number of rows
                                                    $stmt01 = $stmt->num_rows;

                                                    // SQL select statement
                                                    $sql = 'SELECT `user_Pf_Number` FROM `self_achievement` WHERE `user_Pf_Number`=?';
                                                    // Prepare the SQL statement
                                                    $stmt = $dbConnection->prepare($sql);
                                                    // Bind parameters to the statement
                                                    $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Store the result set
                                                    $stmt->store_result();
                                                    // the number of rows
                                                    $stmt02 = $stmt->num_rows;

                                                    // SQL select statement
                                                    $sql = 'SELECT `user_Pf_Number` FROM `self_assessment` WHERE `user_Pf_Number`=?';
                                                    // Prepare the SQL statement
                                                    $stmt = $dbConnection->prepare($sql);
                                                    // Bind parameters to the statement
                                                    $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Store the result set
                                                    $stmt->store_result();
                                                    // the number of rows
                                                    $stmt03 = $stmt->num_rows;

                                                    if ($stmt01===20&&$stmt02===20&&$stmt03===12){
                                                        $readyForPeerEvaluation = "Ready";
                                                ?>
                                                        <th>Action</th>
                                                <?php } ?>
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
                                                <th>
                                                    <?php echo $selectPeersISelectedRow['pf_being_evaluated']; ?>
                                                </th>
                                                <td>
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
                                                <td><?php echo date('d/m/Y H:i:s', strtotime($selectPeersISelectedRow['time_selected'])); ?></td>
                                                <?php
                                                    if ($readyForPeerEvaluation==="Ready"){
                                                ?>
                                                        <td>
                                                            <!-- <form action="fill_peer_evaluation.php" method="post" onsubmit="return peerEvaluationJsValidation()">
                                                                <input type="text" name="pfValue" id="pfValue" class="d-none" value="<?php echo $selectPeersISelectedRow['pf_being_evaluated']; ?>" style="display:none;visibility:hidden;">
                                                                <button type="submit" name="evaluatePeer" id="evaluatePeer" class="btn btn-primary btn-sm">Evaluate</button>
                                                                <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary btn-sm" style="width:70px;">
                                                                    <span class="spinner-border text-light" style="width:18px; height:18px; border-width:3px;"></span>
                                                                </button>
                                                            </form> -->
                                                            <?php
                                                                // SQL select statement
                                                                $sql = 'SELECT `pf_being_evaluated` FROM `peer_evaluation` WHERE `pf_being_evaluated`=? AND `peer_pf`=?';
                                                                // Prepare the SQL statement with a parameter placeholder
                                                                $stmt = $dbConnection->prepare($sql);
                                                                // Bind parameters to the statement
                                                                $stmt->bind_param('ss', $selectPeersISelectedRow['pf_being_evaluated'],$_SESSION['user_Pf_Number']);
                                                                // Execute the statement
                                                                $stmt->execute();
                                                                // Retrieve the result set
                                                                $stmt->store_result();
                                                                if($stmt->num_rows===15){
                                                            ?>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-success btn-sm rounded-pill" style="width:90px;" name="evaluated" id="evaluated">Evaluated</button>
                                                                    <a href="view_peer_evaluation.php?pf=<?php echo $selectPeersISelectedRow['pf_being_evaluated']; ?>" class="btn btn-success btn-sm rounded-pill ml-1" style="width:90px;" name="evaluated" id="evaluated">View</a>
                                                                </div>
                                                            <?php }else{ ?>
                                                                <a href="fill_peer_evaluation.php?pf=<?php echo $selectPeersISelectedRow['pf_being_evaluated']; ?>" name="evaluatePeer" id="evaluatePeer" class="btn btn-primary btn-sm rounded-pill">Evaluate</a>
                                                            <?php } ?>
                                                            <script>
                                                                function peerEvaluationJsValidation()
                                                                {
                                                                    // Set the value of is_input_valid to be true by default
                                                                    var is_input_valid = true;

                                                                    // document.getElementById('evaluatePeer').className = "d-none";
                                                                    // document.getElementById('loadingSpinner').className = "d-block btn btn-primary btn-sm";
                                                                    // document.getElementById('loadingSpinner').style.width = "70px";
                                                                    
                                                                    document.getElementById('errorAlertDiv').className = "d-none";
                                                                    document.getElementById('peervaluationAlertDiv').className = "d-block";
                                                                    document.getElementById('peervaluationAlert').className = "d-block alert alert-success text-center text-dark";
                                                                    document.getElementById('peervaluationAlert').style.display = "block";
                                                                    document.getElementById('peervaluationAlert').style.visibility = "visible";
                                                                    document.getElementById('peervaluationAlert').innerHTML = "Processing request<span class='ml-2 spinner-border text-light' style='width:18px; height:18px; border-width:3px;'></span>";

                                                                    if (document.getElementById('pfValue').value === "")
                                                                    {
                                                                        var is_input_valid = false;
                                                                        document.getElementById('evaluatePeer').className = "d-block btn btn-primary btn-sm";
                                                                        document.getElementById('loadingSpinner').className = "d-none";

                                                                        document.getElementById('peervaluationAlertDiv').className = "d-block";
                                                                        document.getElementById('peervaluationAlert').className = "d-block alert alert-warning text-center text-dark";
                                                                        document.getElementById('peervaluationAlert').innerHTML = "<code class='font-weight-bold'>Error</code><br>Please try again";
                                                                        document.getElementById('peervaluationAlert').style.display = "block";
                                                                        document.getElementById('peervaluationAlert').style.visibility = "visible";
                                                                    }

                                                                    return is_input_valid;
                                                                }
                                                            </script>
                                                        </td>
                                                <?php } ?>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- include mainfooter.php php file -->
                    <?php include 'mainfooter.php' ?>
                </div>
                <!-- include footer.php php file -->
                <?php include 'footer.php' ?>
            </body>
        </html>
<?php } ?>