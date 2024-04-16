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
        // require connection to the functions.php page
        require 'functions.php';

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
            // SQL select statement
            $sql = 'SELECT `user_Pf_Number` FROM `users` WHERE `user_Pf_Number`=?';
            // Prepare the SQL statement with a parameter placeholder
            $stmt = $dbConnection->prepare($sql);
            // Bind parameters to the statement
            $stmt->bind_param('s', $_GET['pf']);
            // Execute the statement
            $stmt->execute();
            // Retrieve the result set
            $stmt->store_result();
            if($stmt->num_rows!==1)
            {
                header("Location: peer_evaluation.php");
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitChanges']) == true)
            {
                $select01 = escapeCharacters(mysqli_real_escape_string($dbConnection,$_POST['select01']));
                $p_e_attribute_id_input = escapeCharacters(mysqli_real_escape_string($dbConnection,$_POST['p_e_attribute_id_input']));

                $myArray = array("Poor","Fair","Good","VGood","Excellent");

                if ($select01==NULL || empty($select01)==true || $p_e_attribute_id_input==NULL || empty($p_e_attribute_id_input)==true)
                {
                    header("Location: view_peer_evaluation?pf=".$_GET['pf']."&status=emptyValue");
                    exit();
                }
                // Check if value exists in array (case-sensitive)                
                elseif (in_array($select01, $myArray)==false)
                {
                    header("Location: view_peer_evaluation?pf=".$_GET['pf']."&status=value_does_not_exist_in_array");
                    exit();
                }
                else
                {
                    if ($select01 == "Poor")
                    {
                        $peer_evaluation_score = 0;
                    }
                    elseif ($select01 == "Fair")
                    {
                        $peer_evaluation_score = 1;
                    }
                    elseif ($select01 == "Good")
                    {
                        $peer_evaluation_score = 2;
                    }elseif ($select01 == "VGood")
                    {
                        $peer_evaluation_score = 3;
                    }elseif ($select01 == "Excellent")
                    {
                        $peer_evaluation_score = 4;
                    }

                    $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));

                    $sql = 'UPDATE `peer_evaluation` SET `peer_evaluation_score`=?, `datePosted`=? WHERE `pf_being_evaluated`=? AND `peer_pf`=? AND `p_e_attribute_id`=?';
                    $stmt = $dbConnection->prepare($sql);
                    $stmt->bind_param('sssss',$peer_evaluation_score,$datePosted,$_GET['pf'],$_SESSION['user_Pf_Number'],$p_e_attribute_id_input);
                    if ($stmt->execute())
                    {
                        header("Location: view_peer_evaluation?pf=".$_GET['pf']."&status=success");
                        exit();
                    }
                }
            }
            else if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitCommentChanges']) == true)
            {
                $peerEvaluationComment = escapeCharacters(mysqli_real_escape_string($dbConnection,$_POST['peerEvaluationComment']));
                $p_e_attribute_id_input = escapeCharacters(mysqli_real_escape_string($dbConnection,$_POST['p_e_attribute_id_input']));

                if ($peerEvaluationComment==NULL || empty($peerEvaluationComment)==true || $p_e_attribute_id_input==NULL || empty($p_e_attribute_id_input)==true)
                {
                    header("Location: view_peer_evaluation?pf=".$_GET['pf']."&status=emptyValue");
                    exit();
                }
                else
                {
                    $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));

                    $sql = 'UPDATE `peer_evaluation` SET `peer_evaluation_score`=?, `datePosted`=? WHERE `pf_being_evaluated`=? AND `peer_pf`=? AND `p_e_attribute_id`=?';
                    $stmt = $dbConnection->prepare($sql);
                    $stmt->bind_param('sssss',$peerEvaluationComment,$datePosted,$_GET['pf'],$_SESSION['user_Pf_Number'],$p_e_attribute_id_input);
                    if ($stmt->execute())
                    {
                        header("Location: view_peer_evaluation?pf=".$_GET['pf']."&status=success");
                        exit();
                    }
                }
            }
?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>epes | View peer evaluation</title>
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
                                                <h1 class="m-0">View peer evaluation</h1>
                                            </div>
                                        </div>
                                        <hr class="border-primary">
                                        <a href="peer_evaluation" type="button" class="btn btn-sm btn-primary text-light">
                                            <i class="fa fa-arrow-left"></i> Back
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <?php
                                                if (isset($_GET['pf'])===true && empty($_GET['pf'])===false && isset($_GET['status'])===true && empty($_GET['status'])===false)
                                                {
                                                    if ($_GET['status']=="emptyValue" || $_GET['status']=="value_does_not_exist_in_array")
                                                    {
                                                        $alerColor="alert-warning";
                                                        $alerStrong="Error!";
                                                        $alerText="Please select the provided selections";
                                                    }elseif ($_GET['status']=="emptyValue" || $_GET['status']=="success")
                                                    {
                                                        $alerColor="alert-success";
                                                        $alerStrong="Success!";
                                                        $alerText="Changes have been made";
                                                    }
                                            ?>
                                                    <div class="alert <?php echo escapeCharacters($alerColor); ?> alert-dismissible fade show" role="alert">
                                                        <strong><?php echo escapeCharacters($alerStrong); ?></strong> <?php echo escapeCharacters($alerText); ?>
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                            <?php } ?>
                                            <table class="table table-responsive-md table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="col-2">Attribute</th>
                                                        <th class="col-2">Score</th>
                                                        <th class="col-1">Edit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $overall = 'PE_14';
                                                        // SQL select statement
                                                        $sql = 'SELECT `p_e_attribute_id`,`peer_evaluation_score` FROM `peer_evaluation` WHERE `pf_being_evaluated`=? AND `peer_pf`=? AND `p_e_attribute_id`!=?';
                                                        // Prepare the SQL statement
                                                        $stmt = $dbConnection->prepare($sql);
                                                        // Bind parameters to the statement
                                                        $stmt->bind_param('sss', $_GET['pf'],$_SESSION['user_Pf_Number'],$overall);
                                                        // Execute the statement
                                                        $stmt->execute();
                                                        // Retrieve the result set directly from the prepared statement
                                                        $result = $stmt->get_result();
                                                        // Iterate through the result set
                                                        while ($row = $result->fetch_assoc())
                                                        {
                                                            $p_e_attribute_id = $row['p_e_attribute_id'];
                                                            $peer_evaluation_score = $row['peer_evaluation_score'];
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <?php
                                                                        // SQL select statement
                                                                        $selectAttributeSql = 'SELECT `attribute` FROM `peer_evaluation_form_data` WHERE `p_e_attribute_id`=?';
                                                                        // Prepare the SQL statement
                                                                        $selectAttributeStmt = $dbConnection->prepare($selectAttributeSql);
                                                                        // Bind parameters to the statement
                                                                        $selectAttributeStmt->bind_param('s', $p_e_attribute_id);
                                                                        // Execute the statement
                                                                        $selectAttributeStmt->execute();
                                                                        // Retrieve the result set directly from the prepared statement
                                                                        $selectAttributeResult = $selectAttributeStmt->get_result();
                                                                        // Fetch a single row as an associative array
                                                                        $selectAttributeRow = $selectAttributeResult->fetch_assoc();
                                                                        // Display the value of the associative array
                                                                        echo escapeCharacters($selectAttributeRow["attribute"]);
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                        if ($peer_evaluation_score==="0")
                                                                        {
                                                                            echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') Poor');
                                                                        }
                                                                        elseif ($peer_evaluation_score==="1")
                                                                        {
                                                                            echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') Fair');
                                                                        }
                                                                        elseif ($peer_evaluation_score==="2")
                                                                        {
                                                                            echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') Good');
                                                                        }
                                                                        elseif ($peer_evaluation_score==="3")
                                                                        {
                                                                            echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') V. Good');
                                                                        }
                                                                        elseif ($peer_evaluation_score==="4")
                                                                        {
                                                                            echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') Excellent');
                                                                        }
                                                                        else
                                                                        {
                                                                            echo escapeCharacters($peer_evaluation_score);
                                                                        }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <!-- Button trigger modal -->
                                                                    <button type="button" class="btn btn-primary btn-sm text-nowrap" data-toggle="modal" data-target="#editPeerEvaluationCenterModal<?php echo escapeCharacters($p_e_attribute_id); ?>">
                                                                        <i class="fa fa-pencil mr-2"></i>Edit
                                                                    </button>

                                                                    <!-- Modal -->
                                                                    <div class="modal fade" id="editPeerEvaluationCenterModal<?php echo escapeCharacters($p_e_attribute_id); ?>" tabindex="-1" role="dialog" aria-labelledby="editPeerEvaluationCenterModalTitle" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                                                                        Edit <strong><?php echo escapeCharacters($selectAttributeRow["attribute"]); ?></strong>
                                                                                    </h5>
                                                                                    <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </span>
                                                                                </div>
                                                                                <form action="" method="post" onsubmit="return editEvaluationJsValidation()">
                                                                                    <div class="modal-body">
                                                                                            <div class="form-group">
                                                                                                <?php
                                                                                                    if ($p_e_attribute_id<"PE_15"){
                                                                                                ?>
                                                                                                    <label for="peerEvaluation01">Current score</label>
                                                                                                    <input type="text" class="form-control" id="peerEvaluation01" name="peerEvaluation01" placeholder="Current score"
                                                                                                    value="<?php
                                                                                                                if ($peer_evaluation_score==="0")
                                                                                                                {echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') Poor');}
                                                                                                                elseif ($peer_evaluation_score==="1"){echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') Fair');}
                                                                                                                elseif ($peer_evaluation_score==="2"){echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') Good');}
                                                                                                                elseif ($peer_evaluation_score==="3"){echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') V. Good');}
                                                                                                                elseif ($peer_evaluation_score==="4"){echo escapeCharacters($valuatedPeerEvaluationScore='('.$peer_evaluation_score.') Excellent');}
                                                                                                                else{echo escapeCharacters($peer_evaluation_score);}
                                                                                                            ?>" readonly>
                                                                                                <?php }elseif ($p_e_attribute_id=="PE_15"){ ?>
                                                                                                    <textarea name="peerEvaluationComment" id="peerEvaluationComment" class="form-control" cols="20" rows="2" readonly><?php echo escapeCharacters($peer_evaluation_score); ?></textarea>
                                                                                                <?php } ?>
                                                                                                <input type="text" class="form-control" id="p_e_attribute_id_input" hidden name="p_e_attribute_id_input" placeholder="<?php echo escapeCharacters($p_e_attribute_id); ?>" value="<?php echo escapeCharacters($p_e_attribute_id); ?>" readonly>
                                                                                            </div>
                                                                                            <?php
                                                                                                if ($p_e_attribute_id<"PE_15"){
                                                                                            ?>
                                                                                                <div class="form-group">
                                                                                                    <label for="select01">Peer evaluation</label>
                                                                                                    <select class="form-control" id="select01" name="select01">
                                                                                                        <option value="Select">Select</option>
                                                                                                        <option value="Poor">(0) Poor</option>
                                                                                                        <option value="Fair">(1) Fair</option>
                                                                                                        <option value="Good">(2) Good</option>
                                                                                                        <option value="VGood">(3) V. Good</option>
                                                                                                        <option value="Excellent">(4) Excellent</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            <?php }elseif ($p_e_attribute_id=="PE_15"){ ?>
                                                                                                <textarea name="peerEvaluationComment" id="peerEvaluationComment" class="form-control" cols="20" rows="2" placeholder="Any other comment(s)"></textarea>
                                                                                            <?php } ?>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <?php
                                                                                            if ($p_e_attribute_id<"PE_15"){
                                                                                        ?>
                                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                            <button type="submit" id="submitChanges" name="submitChanges" class="btn btn-primary">Save changes</button>
                                                                                            <button type="button" id="loadingSubmit" name="loadingSubmit" class="btn btn-primary d-none" style="width:116px">
                                                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                                            </button>
                                                                                        <?php }elseif ($p_e_attribute_id=="PE_15"){ ?>
                                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                            <button type="submit" id="submitCommentChanges" name="submitCommentChanges" class="btn btn-primary">Save changes</button>
                                                                                            <button type="button" id="loadingSubmit" name="loadingSubmit" class="btn btn-primary d-none" style="width:116px">
                                                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                                            </button>
                                                                                        <?php } ?>
                                                                                    </div>
                                                                                </form>
                                                                                <script>
                                                                                    function editEvaluationJsValidation()
                                                                                    {
                                                                                        // Set the value of is_input_valid to be true by default
                                                                                        var is_input_valid = true;
                                                                                        
                                                                                        document.getElementById('submitChanges').className = "d-none";
                                                                                        document.getElementById('submitCommentChanges').className = "d-none";
                                                                                        document.getElementById('loadingSubmit').className = "btn btn-primary d-block";
                                                                                        document.getElementById('loadingSubmit').style.width = "116px";

                                                                                        return is_input_valid;
                                                                                    }
                                                                                </script>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                    <?php } ?>
                                                    <tr class="font-weight-bold">
                                                        <td>Total score</td>
                                                        <td>
                                                            <?php
                                                                // SQL select statement
                                                                $sql = "SELECT SUM(peer_evaluation_score) AS `peer_evaluation_scoreTotal` FROM `peer_evaluation` WHERE `pf_being_evaluated`=? AND `peer_pf`=? AND `p_e_attribute_id`!=?";
                                                                // Prepare the SQL statement
                                                                $stmt = $dbConnection->prepare($sql);
                                                                // Bind parameters to the statement
                                                                $stmt->bind_param('sss', $_GET['pf'],$_SESSION['user_Pf_Number'],$overall);
                                                                // Execute the statement
                                                                $stmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $result = $stmt->get_result();
                                                                // Fetch rows as an associative array
                                                                while ($row = $result->fetch_assoc())
                                                                {
                                                                    echo escapeCharacters($row['peer_evaluation_scoreTotal']);
                                                                }
                                                            ?>
                                                        </td>
                                                        <td colspan="1"></td>
                                                    </tr>
                                                    <tr class="font-weight-bold">
                                                        <td>Overall</td>
                                                        <td>
                                                            <?php
                                                            $overall = 'PE_14';
                                                                // SQL select statement
                                                                $sql = "SELECT `peer_evaluation_score` FROM `peer_evaluation` WHERE `pf_being_evaluated`=? AND `peer_pf`=?";
                                                                // Prepare the SQL statement
                                                                $stmt = $dbConnection->prepare($sql);
                                                                // Bind parameters to the statement
                                                                $stmt->bind_param('ss', $_GET['pf'],$_SESSION['user_Pf_Number']);
                                                                // Execute the statement
                                                                $stmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $result = $stmt->get_result();
                                                                // Fetch rows as an associative array
                                                                $row = $result->fetch_assoc();
                                                                echo escapeCharacters($row['peer_evaluation_score']);
                                                            ?>
                                                        </td>
                                                        <td colspan="1"></td>
                                                    </tr>
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
<?php } ?>