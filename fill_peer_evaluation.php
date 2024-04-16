<?php
    // start a new session
    session_start();

    // $_SESSION['user_Pf_Number'] is not set, redirect to login.php
    if (isset($_SESSION['user_Pf_Number']) == false)
    {
        // start session
        session_start();
        // unset session
        session_unset();
        // destroy session
        session_destroy();
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
        if (isset($_GET['pf']) === false  || empty(trim($_GET['pf'])) === true)
        {
            header("Location: peer_evaluation.php");
            exit();
        }
        elseif (isset($_GET['pf']) === true  && empty(trim($_GET['pf'])) === false)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
    
            $pfValue = htmlspecialchars(mysqli_real_escape_string($dbConnection,trim($_GET['pf'])), ENT_QUOTES, 'UTF-8');

            // SQL select statement
            $sql = 'SELECT `pf_being_evaluated` FROM `peer_evaluation` WHERE `pf_being_evaluated`=? AND `peer_pf`=?';
            // Prepare the SQL statement with a parameter placeholder
            $stmt = $dbConnection->prepare($sql);
            // Bind parameters to the statement
            $stmt->bind_param('ss', $pfValue,$_SESSION['user_Pf_Number']);
            // Execute the statement
            $stmt->execute();
            // Retrieve the result set
            $stmt->store_result();
            if($stmt->num_rows===15)
            {
                header("Location: peer_evaluation.php");
                exit();
            }

            // SQL select statement
            $sql = 'SELECT `user_Pf_Number` FROM `users` WHERE `user_Pf_Number`=?';
            // Prepare the SQL statement with a parameter placeholder
            $stmt = $dbConnection->prepare($sql);
            // Bind parameters to the statement
            $stmt->bind_param('s', $pfValue);
            // Execute the statement
            $stmt->execute();
            // Retrieve the result set
            $stmt->store_result();
            if($stmt->num_rows!==1)
            {
                header("Location: peer_evaluation.php?error=noUserFound");
                exit();
            }

            // SQL select statement
            $sql = 'SELECT `pf_being_evaluated` FROM `peer_evaluators` WHERE `pf_being_evaluated`=? AND `peer_pf`=?';
            // Prepare the SQL statement with a parameter placeholder
            $stmt = $dbConnection->prepare($sql);
            // Bind parameters to the statement
            $stmt->bind_param('ss', $pfValue,$_SESSION['user_Pf_Number']);
            // Execute the statement
            $stmt->execute();
            // Retrieve the result set
            $stmt->store_result();
            if($stmt->num_rows!==1)
            {
                header("Location: peer_evaluation.php?error=notSelected");
                exit();
            }

            // Check if the form is submitted
            // If the current HTTP request method is POST and if a form element with the name attribute set to 'saveEvaluation' is submitted as part of the POST data in an HTTP request
            if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['saveEvaluation']) == true)
            {
                // require connection to the database
                require 'databaseConnection.php';
                // require connection to the database
                require 'databaseCurrentTime.php';

                $p_e_attribute_id = htmlspecialchars(mysqli_real_escape_string($dbConnection,$_POST['p_e_attribute_id']), ENT_QUOTES, 'UTF-8');
                $p_e_evaluation_value = htmlspecialchars(mysqli_real_escape_string($dbConnection,$_POST['p_e_evaluation_value']), ENT_QUOTES, 'UTF-8');
                $acceptedValues = array("0", "1", "2", "3", "4");

                if (empty($p_e_attribute_id)==true)
                {
                    $_SESSION['empty_p_e_attribute_id']='empty_p_e_attribute_id';
                    header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                    exit();
                }
                elseif (in_array($p_e_evaluation_value,$acceptedValues)==false)
                {
                    $_SESSION['incorrect_p_e_evaluation_value']='incorrect_p_e_evaluation_value';
                    header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                    exit();
                }
                elseif ($p_e_evaluation_value<"0")
                {
                    $_SESSION['negative_p_e_evaluation_value']='negative_p_e_evaluation_value';
                    header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                    exit();
                }
                elseif ($p_e_evaluation_value>"4")
                {
                    $_SESSION['invalid_p_e_evaluation_value']='invalid_p_e_evaluation_value';
                    header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                    exit();
                }
                else
                {
                    // SQL select statement
                    $sql = 'SELECT `p_e_attribute_id` FROM `peer_evaluation_form_data` WHERE `p_e_attribute_id`=?';
                    // Prepare the SQL statement
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('s', $p_e_attribute_id);
                    // Execute the statement
                    $stmt->execute();
                    // Store the result set
                    $stmt->store_result();
                    // else if the number of rows is identical to integer 1, redirect to fill_peer_evaluation.php web page
                    if ($stmt->num_rows!==1)
                    {
                        $_SESSION['not_p_e_evaluation_value']='not_p_e_evaluation_value';
                        header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                        exit();
                    }

                    // SQL select statement
                    $sql = 'SELECT `p_e_attribute_id` FROM `peer_evaluation` WHERE `p_e_attribute_id`=? AND `pf_being_evaluated`=? AND `peer_pf`=?';
                    // Prepare the SQL statement with a parameter placeholder
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('sss', $p_e_attribute_id,$pfValue,$_SESSION['user_Pf_Number']);
                    // Execute the statement
                    $stmt->execute();
                    // Store the result set
                    $stmt->store_result();
                    // if number of rows is equal to one redirect to fill_peer_evaluation.php web page
                    if ($stmt->num_rows==1)
                    {
                        $_SESSION['p_e_evaluation_value_already_set']='p_e_evaluation_value_already_set';
                        header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                        exit();
                    }

                    $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));
                    // SQL select statement
                    $sql = 'INSERT INTO `peer_evaluation`(`pf_being_evaluated`, `peer_pf`, `p_e_attribute_id`, `peer_evaluation_score`, `datePosted`) VALUES (?,?,?,?,?)';
                    // Prepare the SQL statement
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('sssss',$pfValue,$_SESSION['user_Pf_Number'],$p_e_attribute_id,$p_e_evaluation_value,$datePosted);
                    // if the statement is executed
                    if ($stmt->execute())
                    {
                        // if $p_e_attribute_id is less than string PE_15, redirect to fill_peer_evaluation.php web page
                        if ($p_e_attribute_id<"PE_15")
                        {
                            header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                            exit();
                        }
                        // elseif $p_e_attribute_id is identical to string PE_15, redirect to peer_evaluation.php web page
                        elseif ($p_e_attribute_id==="PE_15")
                        {
                            header('Location: peer_evaluation.php');
                            exit();
                        }
                    }
                }
            }

            if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['savePeerEvaluationStmt']) == true)
            {
                // require connection to the database
                require 'databaseConnection.php';
                // require connection to the database
                require 'databaseCurrentTime.php';

                $p_e_attribute_id = htmlspecialchars(mysqli_real_escape_string($dbConnection,$_POST['p_e_attribute_id']), ENT_QUOTES, 'UTF-8');
                $peerEvaluationStmt = htmlspecialchars(mysqli_real_escape_string($dbConnection,$_POST['peerEvaluationStmt']), ENT_QUOTES, 'UTF-8');

                if (empty($p_e_attribute_id)==true)
                {
                    $_SESSION['empty_p_e_attribute_id']='empty_p_e_attribute_id';
                    header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                    exit();
                }
                elseif (empty($peerEvaluationStmt)==true)
                {
                    $_SESSION['empty_p_e_attribute_id']='empty_p_e_attribute_id';
                    header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                    exit();
                }
                else
                {
                    // SQL select statement
                    $sql = 'SELECT `p_e_attribute_id` FROM `peer_evaluation_form_data` WHERE `p_e_attribute_id`=?';
                    // Prepare the SQL statement
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('s', $p_e_attribute_id);
                    // Execute the statement
                    $stmt->execute();
                    // Store the result set
                    $stmt->store_result();
                    // else if the number of rows is identical to integer 1, redirect to fill_peer_evaluation.php web page
                    if ($stmt->num_rows!==1)
                    {
                        $_SESSION['not_p_e_evaluation_value']='not_p_e_evaluation_value';
                        header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                        exit();
                    }
                    
                    // SQL select statement
                    $sql = 'SELECT `p_e_attribute_id` FROM `peer_evaluation` WHERE `p_e_attribute_id`=? AND `pf_being_evaluated`=? AND `peer_pf`=?';
                    // Prepare the SQL statement with a parameter placeholder
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('sss', $p_e_attribute_id,$pfValue,$_SESSION['user_Pf_Number']);
                    // Execute the statement
                    $stmt->execute();
                    // Store the result set
                    $stmt->store_result();
                    // if number of rows is equal to one redirect to fill_peer_evaluation.php web page
                    if ($stmt->num_rows==1)
                    {
                        $_SESSION['p_e_evaluation_value_already_set']='p_e_evaluation_value_already_set';
                        header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                        exit();
                    }

                    $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));
                    // SQL select statement
                    $sql = 'INSERT INTO `peer_evaluation`(`pf_being_evaluated`, `peer_pf`, `p_e_attribute_id`, `peer_evaluation_score`, `datePosted`) VALUES (?,?,?,?,?)';
                    // Prepare the SQL statement
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('sssss',$pfValue,$_SESSION['user_Pf_Number'],$p_e_attribute_id,$peerEvaluationStmt,$datePosted);
                    // if the statement is executed
                    if ($stmt->execute())
                    {
                        // if $p_e_attribute_id is less than string PE_15, redirect to fill_peer_evaluation.php web page
                        if ($p_e_attribute_id<"PE_15")
                        {
                            header("Location: fill_peer_evaluation.php?pf=".$_GET['pf']."");
                            exit();
                        }
                        // elseif $p_e_attribute_id is identical to string PE_15, redirect to peer_evaluation.php web page
                        elseif ($p_e_attribute_id==="PE_15")
                        {
                            header('Location: peer_evaluation.php');
                            exit();
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
                <title>epes | Fill peer evaluation</title>
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
                <?php if (isset($_SESSION['empty_p_e_attribute_id'])==true && empty($_SESSION['empty_p_e_attribute_id'])==false){ ?>
                    <script>
                            alert('Please try again');
                        </script>
                    <?php unset($_SESSION['empty_p_e_attribute_id'])?>
                <?php }elseif (isset($_SESSION['incorrect_p_e_evaluation_value'])==true && empty($_SESSION['incorrect_p_e_evaluation_value'])==false){ ?>
                    <script>
                            alert('Please select the provided selections');
                        </script>
                    <?php unset($_SESSION['incorrect_p_e_evaluation_value'])?>
                <?php }elseif (isset($_SESSION['negative_p_e_evaluation_value'])==true && empty($_SESSION['negative_p_e_evaluation_value'])==false){ ?>
                    <script>
                            alert('Selection value cannot be negative');
                        </script>
                    <?php unset($_SESSION['negative_p_e_evaluation_value'])?>
                <?php }elseif (isset($_SESSION['invalid_p_e_evaluation_value'])==true && empty($_SESSION['invalid_p_e_evaluation_value'])==false){ ?>
                    <script>
                            alert('Selection value cannot be more than 4');
                        </script>
                    <?php unset($_SESSION['invalid_p_e_evaluation_value'])?>
                <?php }elseif (isset($_SESSION['not_p_e_evaluation_value'])==true && empty($_SESSION['not_p_e_evaluation_value'])==false){ ?>
                    <script>
                            alert('This input is not allowed');
                        </script>
                    <?php unset($_SESSION['not_p_e_evaluation_value'])?>
                <?php }elseif (isset($_SESSION['p_e_evaluation_value_already_set'])==true && empty($_SESSION['p_e_evaluation_value_already_set'])==false){ ?>
                    <script>
                            alert('This evaluation has alredy been saved');
                        </script>
                    <?php unset($_SESSION['p_e_evaluation_value_already_set'])?>
                <?php } ?>
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
                                        <h1 class="m-0">Fill peer evaluation</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <a href="peer_evaluation" type="button" class="btn btn-sm btn-primary text-light">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="container w-50 text-center d-none" id="fillPeerEvaluationtAlertDiv">
                                <div class="alert alert-warning text-center text-dark" role="alert" id="fillPeerEvaluationtAlert"></div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="" method="post" onsubmit="return peerEvaluationJsValidation()" onreset="return resetPeerEvaluationForm()">
                                        <?php
                                            // SQL select statement
                                            $sql = 'SELECT `p_e_attribute_id` FROM `peer_evaluation` WHERE `pf_being_evaluated`=? AND `peer_pf`=? ORDER BY `p_e_attribute_id` ASC';
                                            // Prepare the SQL statement
                                            $stmt = $dbConnection->prepare($sql);
                                            // Bind parameters to the statement
                                            $stmt->bind_param('ss', $pfValue,$_SESSION['user_Pf_Number']);
                                            // Execute the statement
                                            $stmt->execute();
                                            // Retrieve the result set directly from the prepared statement
                                            $result = $stmt->get_result();
                                            // Fetch rows as an associative array
                                            $p_e_attribute_id = $result->fetch_assoc();
                                            if (empty($p_e_attribute_id['p_e_attribute_id'])===true){ ?>
                                                <table class="table table-responsive-sm tabel-hover table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="col-2 text-center">Attribute</th>
                                                            <th class="col-2 text-center">(0)<br>Poor</th>
                                                            <th class="col-2 text-center">(1)<br>Fair</th>
                                                            <th class="col-2 text-center">(2)<br>Good</th>
                                                            <th class="col-2 text-center">(3)<br>V. Good</th>
                                                            <th class="col-2 text-center">(4)<br>Excellent</th>
                                                            <th class="col-2 text-center">Appraisee Score</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            // SQL select statement
                                                            $sql = 'SELECT `p_e_attribute_id`, `attribute` FROM `peer_evaluation_form_data` ORDER BY `p_e_attribute_id` ASC';
                                                            // Prepare the SQL statement
                                                            $stmt = $dbConnection->prepare($sql);
                                                            // Execute the statement
                                                            $stmt->execute();
                                                            // Retrieve the result set directly from the prepared statement
                                                            $result = $stmt->get_result();
                                                            // Fetch rows as an associative array
                                                            $row = $result->fetch_assoc();
                                                        ?>
                                                        <tr>
                                                            <td class="d-none">
                                                                <input type="text" name="p_e_attribute_id" id="p_e_attribute_id" value="<?php echo $row['p_e_attribute_id']; ?>" readonly>
                                                            </td>
                                                            <td>
                                                                <?php echo $row['attribute']; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="p_e_evaluation_value" id="poor" value="0">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="p_e_evaluation_value" id="fair" value="1">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="p_e_evaluation_value" id="good" value="2">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="p_e_evaluation_value" id="vGood" value="3">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="p_e_evaluation_value" id="excellent" value="4">
                                                            </td>
                                                            <td class="text-center font-weight-bold">
                                                                <span id="theValue"></span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <hr>
                                                <div class="col-lg-10 text-right justify-content-center d-flex">
                                                    <button type="submit" class="btn btn-primary mr-2" name="saveEvaluation" id="saveEvaluation">Next</button>
                                                    <button type="reset" class="btn btn-secondary" name="resetEvaluation" id="resetEvaluation">Reset</button>

                                                    <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                        <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                    </button>
                                                </div>
                                            <?php }else{ ?>
                                                <?php
                                                    // SQL select statement
                                                    $sql = 'SELECT `p_e_attribute_id` FROM `peer_evaluation` WHERE `pf_being_evaluated`=? AND `peer_pf`=? ORDER BY `p_e_attribute_id` DESC';
                                                    // Prepare the SQL statement
                                                    $stmt = $dbConnection->prepare($sql);
                                                    // Bind parameters to the statement
                                                    $stmt->bind_param('ss', $pfValue,$_SESSION['user_Pf_Number']);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Retrieve the result set directly from the prepared statement
                                                    $result = $stmt->get_result();
                                                    // Fetch a single row as an associative array
                                                    $row = $result->fetch_assoc();
                                                    // The value of the associative array
                                                    $latestEai =  $row["p_e_attribute_id"];
                                                    // Replace all occurrences of the search string "PE_" with the replacement string ""
                                                    $nextPeerEvaluationId = str_replace("PE_","",$latestEai);
                                                    // Pad a $nextPeerEvaluationId string to a certain length with another string
                                                    $nextPeerEvaluationId = str_pad($nextPeerEvaluationId + 1,2,0, STR_PAD_LEFT);
                                                    // The new value of $nextPeerEvaluationId
                                                    $nextPeerEvaluationId = "PE_" .$nextPeerEvaluationId;
                                                ?>
                                                <?php
                                                    if ($nextPeerEvaluationId=="PE_15"){
                                                ?>
                                                    <?php
                                                        // SQL select statement
                                                        $sql = 'SELECT `p_e_attribute_id`, `attribute` FROM `peer_evaluation_form_data` WHERE `p_e_attribute_id`=?';
                                                        // Prepare the SQL statement
                                                        $stmt = $dbConnection->prepare($sql);
                                                        // Bind parameters to the statement
                                                        $stmt->bind_param('s', $nextPeerEvaluationId);
                                                        // Execute the statement
                                                        $stmt->execute();
                                                        // Retrieve the result set directly from the prepared statement
                                                        $result = $stmt->get_result();
                                                        // Fetch rows as an associative array
                                                        $row = $result->fetch_assoc();
                                                    ?>
                                                    <?php echo $row['attribute']; ?>
                                                    <br>
                                                    <input type="text" name="p_e_attribute_id" id="p_e_attribute_id" value="<?php echo $row['p_e_attribute_id']; ?>" readonly hidden>
                                                    <textarea name="peerEvaluationStmt" id="peerEvaluationStmt" class="form-control" cols="20" rows="2"></textarea>
                                                    <hr>
                                                    <div class="col-lg-10 text-right justify-content-center d-flex">
                                                        <button type="submit" class="btn btn-primary mr-2" name="savePeerEvaluationStmt" id="savePeerEvaluationStmt">
                                                            <?php if ($nextPeerEvaluationId < "PE_15") { ?>
                                                                Next
                                                            <?php }elseif ($nextPeerEvaluationId ==="PE_15") { ?>
                                                                Finish
                                                            <?php } ?>
                                                        </button>
                                                        <!-- <button type="reset" class="btn btn-secondary" name="resetEvaluation" id="resetEvaluation">Reset</button> -->

                                                        <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                            <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                        </button>
                                                    </div>
                                                <?php }else{ ?>
                                                    <table class="table table-responsive-sm tabel-hover table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th class="col-2 text-center">Attribute</th>
                                                                <th class="col-2 text-center">(0)<br>Poor</th>
                                                                <th class="col-2 text-center">(1)<br>Fair</th>
                                                                <th class="col-2 text-center">(2)<br>Good</th>
                                                                <th class="col-2 text-center">(3)<br>V. Good</th>
                                                                <th class="col-2 text-center">(4)<br>Excellent</th>
                                                                <th class="col-2 text-center">Appraisee Score</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                // SQL select statement
                                                                $sql = 'SELECT `p_e_attribute_id`, `attribute` FROM `peer_evaluation_form_data` WHERE `p_e_attribute_id`=?';
                                                                // Prepare the SQL statement
                                                                $stmt = $dbConnection->prepare($sql);
                                                                // Bind parameters to the statement
                                                                $stmt->bind_param('s', $nextPeerEvaluationId);
                                                                // Execute the statement
                                                                $stmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $result = $stmt->get_result();
                                                                // Fetch rows as an associative array
                                                                $row = $result->fetch_assoc();
                                                            ?>
                                                            <tr>
                                                                <td class="d-none">
                                                                    <input type="text" name="p_e_attribute_id" id="p_e_attribute_id" value="<?php echo $row['p_e_attribute_id']; ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <?php echo $row['attribute']; ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="p_e_evaluation_value" id="poor" value="0">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="p_e_evaluation_value" id="fair" value="1">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="p_e_evaluation_value" id="good" value="2">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="p_e_evaluation_value" id="vGood" value="3">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="p_e_evaluation_value" id="excellent" value="4">
                                                                </td>
                                                                <td class="text-center font-weight-bold">
                                                                    <span id="theValue"></span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <hr>
                                                    <div class="col-lg-10 text-right justify-content-center d-flex">
                                                        <button type="submit" class="btn btn-primary mr-2" name="saveEvaluation" id="saveEvaluation">
                                                            <?php if ($nextPeerEvaluationId < "PE_15") { ?>
                                                                Next
                                                            <?php } ?>
                                                        </button>
                                                        <button type="reset" class="btn btn-secondary" name="resetEvaluation" id="resetEvaluation">Reset</button>

                                                        <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                            <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                        </button>
                                                    </div>
                                                    <?php } ?>
                                            <?php }  ?>
                                    </form>
                                    <script>
                                        // Get all radio buttons
                                        var radioButtons = document.querySelectorAll('input[name="p_e_evaluation_value"]');

                                        // Add event listener for change to each radio button
                                        radioButtons.forEach(function(radioButton)
                                        {
                                            radioButton.addEventListener('change', function(event)
                                            {
                                                // Log the value of the checked radio button
                                                if (radioButton.checked)
                                                {
                                                    document.getElementById('theValue').innerHTML = radioButton.value;
                                                }
                                            });
                                        });

                                        function resetPeerEvaluationForm()
                                        {
                                            // Set the value of is_reset_valid to be true by default
                                            var is_reset_valid = true;
                                            // Set the value of checkedStatus to be false by default
                                            var checkedStatus = false;
                                            var radioButton = document.getElementsByName('p_e_evaluation_value');
                                            // Loop through each radio button to check if any is checked
                                            for (var i = 0; i < radioButton.length; i++)
                                            {
                                                if (radioButton[i].checked)
                                                {
                                                    checkedStatus = true;
                                                    break;
                                                }
                                            }
                                            if (checkedStatus==true)
                                            {
                                                document.getElementById('saveEvaluation').className = "d-block btn btn-primary mr-2";
                                                document.getElementById('resetEvaluation').className = "d-block btn btn-secondary";
                                                document.getElementById('loadingSpinner').className = "d-none";
                                                document.getElementById('theValue').innerHTML = "";
                                            }
                                        }

                                        function peerEvaluationJsValidation()
                                        {
                                            // Set the value of is_input_valid to be true by default
                                            var is_input_valid = true;
                                            // Set the value of checkedStatus to be false by default
                                            var checkedStatus = false;

                                            document.getElementById('saveEvaluation').className = "d-none";
                                            document.getElementById('resetEvaluation').className = "d-none";
                                            document.getElementById('loadingSpinner').className = "btn btn-primary";
                                            var radioButton = document.getElementsByName('p_e_evaluation_value');

                                            // Loop through each radio button to check if any is checked
                                            for (var i = 0; i < radioButton.length; i++)
                                            {
                                                if (radioButton[i].checked)
                                                {
                                                    checkedStatus = true;
                                                    break;
                                                }
                                            }
                                            if (checkedStatus==false)
                                            {
                                                var is_input_valid = false;
                                                document.getElementById('saveEvaluation').className = "d-block btn btn-primary mr-2";
                                                document.getElementById('resetEvaluation').className = "d-block btn btn-secondary";
                                                document.getElementById('loadingSpinner').className = "d-none";

                                                document.getElementById('fillPeerEvaluationtAlertDiv').className = "d-block";
                                                document.getElementById('fillPeerEvaluationtAlert').innerHTML = "Please select one provided selections";
                                            }
                                            else
                                            {
                                                document.getElementById('saveEvaluation').className = "d-none";
                                                document.getElementById('resetEvaluation').className = "d-none";
                                                document.getElementById('loadingSpinner').className = "btn btn-primary";
                                            }

                                            return is_input_valid;
                                        }
                                    </script>
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