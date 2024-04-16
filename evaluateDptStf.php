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
        header('Location:login.php');
        exit();
    }
    // else if $_GET['pf'] is not set or is empty, redirect to evaluateDpt.php
    elseif (isset($_GET['pf']) == false || empty($_GET['pf'])==true)
    {
        // redirect user to evaluateDpt.php web page
        header('Location: evaluateDpt.php');
        exit();
    }
    else
    {
        // require connection to the database
        require 'databaseConnection.php';
        // require connection to the database
        require 'databaseCurrentTime.php';
        // SQL select statement
        $selectSystemAccessSql = 'SELECT `is_cod`, `department_id`, `system_access` FROM `users` WHERE `user_Pf_Number`=?';
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
        $systemAccessValueRow = $systemAccessValue['system_access'];
        $isCodRow = $systemAccessValue['is_cod'];
        $departmentIdRow = $systemAccessValue['department_id'];

        // if is not set to integer 5, redirect to login.php showing "Please contact the administrator for assistence" error message and exit
        if ($systemAccessValueRow !==5 || $isCodRow !==5)
        {
            // start session
            session_start();
            // unset session
            session_unset();
            // destroy session
            session_destroy();
            header("Location: login.php?loginError=loginError01&link=evaluateDpt");
            exit();
        }
        else
        {
            //SQL SELECT statement with a parameter placeholder
            $sql00 = 'SELECT `user_Pf_Number` FROM `users` WHERE `user_Pf_Number`=?';
            // Prepare the SQL statement
            $stmt00 = $dbConnection->prepare($sql00);
            // Bind parameters to the statement
            $stmt00->bind_param('s', $_GET['pf']);
            // Execute the statement
            $stmt00->execute();
            // Store result
            $stmt00->store_result();
            if($stmt00->num_rows!==1){
                // redirect user to evaluateDpt web page
                header("Location: evaluateDpt");
                exit();
            }
            else
            {
                //SQL SELECT statement with a parameter placeholder
                $sql01 = 'SELECT `department_id` FROM `departments` WHERE `department_id`=(SELECT `department_id` FROM `users` WHERE `user_Pf_Number`=? AND `department_id`=?)';
                // Prepare the SQL statement
                $stmt01 = $dbConnection->prepare($sql01);
                // Bind parameters to the statement
                $stmt01->bind_param('ss', $_GET['pf'],$departmentIdRow);
                // Execute the statement
                $stmt01->execute();
                // Store result
                $stmt01->store_result();
                if($stmt01->num_rows!==1){
                    // redirect user to evaluateDpt web page
                    header("Location: evaluateDpt");
                    exit();
                }
            }
        }
        
        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'saveEvaluation' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['saveEvaluation']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';

            $attribute_id = htmlspecialchars(mysqli_real_escape_string($dbConnection,$_POST['attribute_id']), ENT_QUOTES, 'UTF-8');
            $staff_competency_value = htmlspecialchars(mysqli_real_escape_string($dbConnection,$_POST['staff_competency_value']), ENT_QUOTES, 'UTF-8');
            $acceptedValues = array("0", "1", "2", "3", "4");

            if (empty($attribute_id)==true)
            {
                $_SESSION['empty_attribute_id']='empty_attribute_id';
                header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                exit();
            }
            elseif (in_array($staff_competency_value,$acceptedValues)==false)
            {
                $_SESSION['incorrect_staff_competency_value']='incorrect_staff_competency_value';
                header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                exit();
            }
            elseif ($staff_competency_value<"0")
            {
                $_SESSION['negative_staff_competency_value']='negative_staff_competency_value';
                header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                exit();
            }
            elseif ($staff_competency_value>"4")
            {
                $_SESSION['invalid_staff_competency_value']='invalid_staff_competency_value';
                header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                exit();
            }
            else
            {
                // SQL select statement
                $sql = 'SELECT `attribute_id` FROM `staff_competency_form_data` WHERE `attribute_id`=?';
                // Prepare the SQL statement
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('s', $attribute_id);
                // Execute the statement
                $stmt->execute();
                // Store the result set
                $stmt->store_result();
                // else if the number of rows is identical to integer 1, redirect to evaluateDptStf.php web page
                if ($stmt->num_rows!==1)
                {
                    $_SESSION['not_staff_competency_value']='not_staff_competency_value';
                    header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                    exit();
                }

                // SQL select statement
                $sql = 'SELECT `s_c_attribute_id` FROM `staff_competency` WHERE `s_c_attribute_id`=? AND `pf_being_evaluated`=? AND `cod_pf`=?';
                // Prepare the SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('sss', $attribute_id,$_GET['pf'],$_SESSION['user_Pf_Number']);
                // Execute the statement
                $stmt->execute();
                // Store the result set
                $stmt->store_result();
                // if number of rows is equal to one redirect to evaluateDptStf.php web page
                if ($stmt->num_rows==1)
                {
                    $_SESSION['staff_competency_value_already_set']='staff_competency_value_already_set';
                    header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                    exit();
                }

                $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));
                // SQL select statement
                $sql = 'INSERT INTO `staff_competency`(`pf_being_evaluated`, `cod_pf`, `s_c_attribute_id`, `staff_competency_score`, `datePosted`) VALUES (?,?,?,?,?)';
                // Prepare the SQL statement
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('sssss',$_GET['pf'],$_SESSION['user_Pf_Number'],$attribute_id,$staff_competency_value,$datePosted);
                // if the statement is executed
                if ($stmt->execute())
                {
                    // if $attribute_id is less than string SC_13, redirect to evaluateDptStf.php web page
                    if ($attribute_id<"SC_13")
                    {
                        header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                        exit();
                    }
                    // elseif $attribute_id is identical to string SC_13, redirect to evaluateDpt.php web page
                    elseif ($attribute_id==="SC_13")
                    {
                        header('Location: evaluateDpt.php');
                        exit();
                    }
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['saveStaffCompetencyStmt']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';

            $attribute_id = htmlspecialchars(mysqli_real_escape_string($dbConnection,$_POST['attribute_id']), ENT_QUOTES, 'UTF-8');
            $staffCompetencyStmt = htmlspecialchars(mysqli_real_escape_string($dbConnection,$_POST['staffCompetencyStmt']), ENT_QUOTES, 'UTF-8');

            if (empty($attribute_id)==true)
            {
                $_SESSION['empty_attribute_id']='empty_attribute_id';
                header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                exit();
            }
            elseif (empty($staffCompetencyStmt)==true)
            {
                $_SESSION['empty_attribute_id']='empty_attribute_id';
                header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                exit();
            }
            else
            {
                // SQL select statement
                $sql = 'SELECT `attribute_id` FROM `staff_competency_form_data` WHERE `attribute_id`=?';
                // Prepare the SQL statement
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('s', $attribute_id);
                // Execute the statement
                $stmt->execute();
                // Store the result set
                $stmt->store_result();
                // else if the number of rows is identical to integer 1, redirect to evaluateDptStf.php web page
                if ($stmt->num_rows!==1)
                {
                    $_SESSION['not_staff_competency_value']='not_staff_competency_value';
                    header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                    exit();
                }

                // SQL select statement
                $sql = 'SELECT `s_c_attribute_id` FROM `staff_competency` WHERE `s_c_attribute_id`=? AND `pf_being_evaluated`=? AND `cod_pf`=?';
                // Prepare the SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('sss', $attribute_id,$_GET['pf'],$_SESSION['user_Pf_Number']);
                // Execute the statement
                $stmt->execute();
                // Store the result set
                $stmt->store_result();
                // if number of rows is equal to one redirect to evaluateDptStf.php web page
                if ($stmt->num_rows==1)
                {
                    $_SESSION['staff_competency_value_already_set']='staff_competency_value_already_set';
                    header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                    exit();
                }

                $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));
                // SQL select statement
                $sql = 'INSERT INTO `staff_competency`(`pf_being_evaluated`, `cod_pf`, `s_c_attribute_id`, `staff_competency_score`, `datePosted`) VALUES (?,?,?,?,?)';
                // Prepare the SQL statement
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('sssss',$_GET['pf'],$_SESSION['user_Pf_Number'],$attribute_id,$staffCompetencyStmt,$datePosted);
                // if the statement is executed
                if ($stmt->execute())
                {
                    // if $attribute_id is less than string SC_13, redirect to evaluateDptStf.php web page
                    if ($attribute_id<"SC_13")
                    {
                        header("Location: evaluateDptStf.php?pf=".$_GET['pf']."");
                        exit();
                    }
                    // elseif $attribute_id is identical to string SC_13, redirect to evaluateDpt.php web page
                    elseif ($attribute_id==="SC_13")
                    {
                        header('Location: evaluateDpt.php');
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
                <title>epes | Evaluate Department members</title>
                <!-- include header.php php file -->
                <?php include 'header.php' ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <?php if (isset($_SESSION['empty_attribute_id'])==true && empty($_SESSION['empty_attribute_id'])==false){ ?>
                    <script>
                        alert('Please try again');
                    </script>
                    <?php unset($_SESSION['empty_attribute_id'])?>
                <?php }elseif (isset($_SESSION['incorrect_staff_competency_value'])==true && empty($_SESSION['incorrect_staff_competency_value'])==false){ ?>
                    <script>
                        alert('Please select the provided selections');
                    </script>
                    <?php unset($_SESSION['incorrect_staff_competency_value'])?>
                <?php }elseif (isset($_SESSION['negative_staff_competency_value'])==true && empty($_SESSION['negative_staff_competency_value'])==false){ ?>
                    <script>
                        alert('Selection value cannot be negative');
                    </script>
                    <?php unset($_SESSION['negative_staff_competency_value'])?>
                <?php }elseif (isset($_SESSION['invalid_staff_competency_value'])==true && empty($_SESSION['invalid_staff_competency_value'])==false){ ?>
                    <script>
                        alert('Selection value cannot be more than 4');
                    </script>
                <?php }elseif (isset($_SESSION['not_staff_competency_value'])==true && empty($_SESSION['not_staff_competency_value'])==false){ ?>
                    <script>
                            alert('This input is not allowed');
                    </script>
                    <?php unset($_SESSION['not_staff_competency_value'])?>
                <?php }elseif (isset($_SESSION['staff_competency_value_already_set'])==true && empty($_SESSION['staff_competency_value_already_set'])==false){ ?>
                    <script>
                            alert('This evaluation has alredy been saved');
                    </script>
                    <?php unset($_SESSION['staff_competency_value_already_set'])?>
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
                                        <h1 class="m-0">Evaluate
                                            <span>
                                                <?php
                                                    // SQL select statement
                                                    $getStaffSql = 'SELECT `title`, `first_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                    // Prepare the SQL statement
                                                    $getStaffSql = $dbConnection->prepare($getStaffSql);
                                                    // Bind parameters to the statement
                                                    $getStaffSql->bind_param('s', $_GET['pf']);
                                                    // Execute the statement
                                                    $getStaffSql->execute();
                                                    // Retrieve the result set directly from the prepared statement
                                                    $getStaffSqlResult = $getStaffSql->get_result();
                                                    // The value of the associative array
                                                    $getStaffSqlRow = $getStaffSqlResult->fetch_assoc();
                                                    // Display the value of the associative array
                                                    echo $getStaffSqlRow['title'].' '.$getStaffSqlRow['first_name'];
                                                ?>
                                            </span>
                                        </h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <a href="evaluateDpt" type="button" class="btn btn-sm btn-primary text-light">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-11 mx-auto">
                            <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl card-outline card-primary">
                                <tr>
                                    <td><button type="button" class="btn text-nowrap"style="background-color: #5ba1b3;" data-toggle="modal" data-target="#bioDataModal<?php echo $_GET['pf']; ?>">Bio data</button></td>
                                    <td><button type="button" class="btn text-nowrap"style="background-color: #5ba1b3;" data-toggle="modal" data-target="#peersSelectionModal<?php echo $_GET['pf']; ?>">Peers selection</button></td>
                                    <td><button type="button" class="btn text-nowrap"style="background-color: #5ba1b3;" data-toggle="modal" data-target="#peersEvaluationModal<?php echo $_GET['pf']; ?>">Peers Evaluation</button></td>
                                    <td><a type="button" class="btn text-nowrap"style="background-color: #5ba1b3;" href="expectedAchievementAndAchievement?pf=<?php echo $_GET['pf']; ?>">Expected achievement &amp; final achievement</a></td>
                                    <td><button type="button" class="btn text-nowrap"style="background-color: #5ba1b3;" data-toggle="modal" data-target="#competencyAppraisalModal<?php echo $_GET['pf']; ?>">Values &amp; staff competency appraisal</button></td>
                                </tr>

                                <!-- bioDataModal -->
                                <div class="modal fade" id="bioDataModal<?php echo $_GET['pf']; ?>" tabindex="-1" role="dialog" aria-labelledby="bioDataModalTitle" aria-hidden="true" style="-webkit-user-select:none;-ms-user-select:none;user-select:none;" data-backdrop="static" data-keyboard="false" >
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="bioDataModalTitle">
                                                    <span>
                                                        Bio data for
                                                        <span>
                                                            <?php
                                                                // SQL select statement
                                                                $getStaffSql = 'SELECT `title`, `first_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                                // Prepare the SQL statement
                                                                $getStaffSql = $dbConnection->prepare($getStaffSql);
                                                                // Bind parameters to the statement
                                                                $getStaffSql->bind_param('s', $_GET['pf']);
                                                                // Execute the statement
                                                                $getStaffSql->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $getStaffSqlResult = $getStaffSql->get_result();
                                                                // The value of the associative array
                                                                $getStaffSqlRow = $getStaffSqlResult->fetch_assoc();
                                                                // Display the value of the associative array
                                                                echo $getStaffSqlRow['title'].' '.$getStaffSqlRow['first_name'];
                                                            ?>
                                                        </span>
                                                    </span>
                                                </h5>
                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                            <div class="modal-body">
                                               <?php
                                                    // SQL statement with a parameter placeholder
                                                    $checkStaffBioDataSql = 'SELECT `PF_number` FROM `bio_data` WHERE `PF_number`=?';
                                                    // Prepare SQL statement
                                                    $checkStaffBioDataStmt = $dbConnection->prepare($checkStaffBioDataSql);
                                                    // Bind parameters to the statement
                                                    $checkStaffBioDataStmt->bind_param('s',$_GET['pf']);
                                                    // Execute the statement
                                                    $checkStaffBioDataStmt->execute();
                                                    // Retrieve the result set
                                                    $checkStaffBioDataResult = $checkStaffBioDataStmt->get_result();
                                                    // Counting the number of rows
                                                    if($checkStaffBioDataResult->num_rows!==1)
                                                    {
                                                        echo "<span>No bio data</span>";
                                                    }
                                                    else
                                                    {
                                                    // SQL statement with a parameter placeholder
                                                    $selectStaffBioDataSql = 'SELECT * FROM `bio_data` WHERE PF_number=?';
                                                    // Prepare SQL statement
                                                    $selectStaffBioDataStmt = $dbConnection->prepare($selectStaffBioDataSql);
                                                    // Bind parameters to the statement
                                                    $selectStaffBioDataStmt->bind_param('s', $_GET['pf']);
                                                    // Execute the statement
                                                    $selectStaffBioDataStmt->execute();
                                                    // Retrieve the result set
                                                    $selectStaffBiodatasResult = $selectStaffBioDataStmt->get_result();
                                                    // Fetch data
                                                    while ($selectStaffBiodataDetail = $selectStaffBiodatasResult->fetch_assoc())
                                                    {
                                               ?>
                                                    <dl class="container">
                                                        <div class="row">
                                                            <div class="col">
                                                                <dt>First name</dt>
                                                                <dd><?php echo $selectStaffBiodataDetail['First_Name']; ?></dd>
                                                            </div>

                                                            <?php if (empty($selectStaffBiodataDetail['Middle_Name'])==false){ ?>
                                                                <div class="col">
                                                                    <dt>Middle name</dt>
                                                                    <dd><?php echo $selectStaffBiodataDetail['Middle_Name']; ?></dd>
                                                                </div>
                                                            <?php } ?>

                                                            <div class="col">
                                                                <dt>Last name</dt>
                                                                <dd><?php echo $selectStaffBiodataDetail['Last_Name']; ?></dd>
                                                            </div>
                                                        </div>

                                                        <hr>
                                                        <div class="row">
                                                            <div class="col">
                                                                <dt>Department</dt>
                                                                <dd><?php echo $selectStaffBiodataDetail['Department_id']; ?></dd>
                                                            </div>

                                                            <div class="col">
                                                                <dt>Desination</dt>
                                                                <dd><?php echo $selectStaffBiodataDetail['Desination_id']; ?></dd>
                                                            </div>
                                                        </div>

                                                        <hr>
                                                        <div class="row">
                                                            <div class="col">
                                                                <dt>Nature of employment</dt>
                                                                <dd>
                                                                    <?php
                                                                        // SQL statement with a parameter placeholder
                                                                        $getEmploymentNatureSql = 'SELECT `nature_Of_Employement` FROM `natures_of_employment` WHERE `emp_nature_id`=?';
                                                                        // Prepare SQL statement
                                                                        $getEmploymentNatureStmt = $dbConnection->prepare($getEmploymentNatureSql);
                                                                        // Bind parameter to the statement
                                                                        $getEmploymentNatureStmt->bind_param('s', $selectStaffBiodataDetail['Nature_of_employment']);
                                                                        // Execute the statement
                                                                        $getEmploymentNatureStmt->execute();
                                                                        // Retrieve the result set
                                                                        $getEmploymentNatureStmtResult = $getEmploymentNatureStmt->get_result();
                                                                        // Fetch data
                                                                        $getEmploymentNatureStmtRow = $getEmploymentNatureStmtResult->fetch_assoc();
                                                                        // Display the value of the associative array
                                                                        echo $getEmploymentNatureStmtRow['nature_Of_Employement'];
                                                                    ?>
                                                                </dd>
                                                            </div>
                                                        </div>

                                                        <hr>
                                                        <div class="row">
                                                            <div class="col">
                                                                <dt>Job description</dt>
                                                                <dd><?php echo $selectStaffBiodataDetail['Job_description']; ?></dd>
                                                            </div>
                                                        </div>

                                                        <hr>
                                                        <div class="row">
                                                            <div class="col">
                                                                <dt>Qualifications</dt>
                                                                <dd><?php echo $selectStaffBiodataDetail['Qualifications']; ?></dd>
                                                            </div>
                                                        </div>

                                                        <hr>
                                                        <div class="row">
                                                            <div class="col">
                                                                <dt>Strengths</dt>
                                                                <dd><?php echo $selectStaffBiodataDetail['strengths']; ?></dd>
                                                            </div>
                                                        </div>
                                                    </dl>
                                               <?php }} ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- peersSelectionModal -->
                                <div class="modal fade" id="peersSelectionModal<?php echo $_GET['pf']; ?>" tabindex="-1" role="dialog" aria-labelledby="peersSelectionModalTitle" aria-hidden="true" style="-webkit-user-select:none;-ms-user-select:none;user-select:none;" data-backdrop="static" data-keyboard="false" >
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="peersSelectionModalTitle">
                                                    <span>
                                                        Peers selection for
                                                        <span>
                                                            <?php
                                                                // SQL select statement
                                                                $getStaffSql = 'SELECT `title`, `first_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                                // Prepare the SQL statement
                                                                $getStaffStmt = $dbConnection->prepare($getStaffSql);
                                                                // Bind parameters to the statement
                                                                $getStaffStmt->bind_param('s', $_GET['pf']);
                                                                // Execute the statement
                                                                $getStaffStmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $getStaffStmtResult = $getStaffStmt->get_result();
                                                                // The value of the associative array
                                                                $getStaffStmtRow = $getStaffStmtResult->fetch_assoc();
                                                                // Display the value of the associative array
                                                                echo $getStaffStmtRow['title'].' '.$getStaffStmtRow['first_name'];
                                                            ?>
                                                        </span>
                                                    </span>
                                                </h5>
                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                    // SQL statement with a parameter placeholder
                                                    $selectPeersSelectedSql = 'SELECT `peer_pf` FROM `peer_evaluators` WHERE `pf_being_evaluated`=?';
                                                    // Prepare SQL statement
                                                    $selectPeersSelectedStmt = $dbConnection->prepare($selectPeersSelectedSql);
                                                    // Bind parameters to the statement
                                                    $selectPeersSelectedStmt->bind_param('s',$_GET['pf']);
                                                    // Execute the statement
                                                    $selectPeersSelectedStmt->execute();
                                                    // Retrieve the result set
                                                    $result = $selectPeersSelectedStmt->get_result();
                                                    // Counting the number of rows
                                                    if($result->num_rows<1)
                                                    {
                                                        echo "<span>No peers selected</span>";
                                                    }
                                                    else
                                                    {
                                                    // SQL select statement
                                                    $selectPeersISelectedSql = 'SELECT * FROM `peer_evaluators` WHERE `pf_being_evaluated`=? ORDER BY `time_selected` ASC';
                                                    // Prepare the SQL statement
                                                    $selectPeersISelectedStmt = $dbConnection->prepare($selectPeersISelectedSql);
                                                    // Bind parameters to the statement
                                                    $selectPeersISelectedStmt->bind_param('s',$_GET['pf']);
                                                    // Execute the statement
                                                    $selectPeersISelectedStmt->execute();
                                                    // Retrieve the result set directly from the prepared statement
                                                    $selectPeersISelectedResult = $selectPeersISelectedStmt->get_result();
                                                    // Fetch values as an associative array
                                                    while ($selectPeersISelectedRow = $selectPeersISelectedResult->fetch_assoc())
                                                    {
                                                ?>
                                                    <dl class="container">
                                                        <div class="row">
                                                            <div class="col">
                                                                <dt>PF number</dt>
                                                                <dd><?php echo $selectPeersISelectedRow['peer_pf']; ?></dd>
                                                                <dt>Name</dt>
                                                                <dd>
                                                                    <?php
                                                                        // SQL select statement
                                                                        $getStaffNameSql = 'SELECT `title`, `first_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                                        // Prepare the SQL statement
                                                                        $getStaffNameStmt = $dbConnection->prepare($getStaffNameSql);
                                                                        // Bind parameters to the statement
                                                                        $getStaffNameStmt->bind_param('s', $selectPeersISelectedRow['peer_pf']);
                                                                        // Execute the statement
                                                                        $getStaffNameStmt->execute();
                                                                        // Retrieve the result set directly from the prepared statement
                                                                        $getStaffNameStmtResult = $getStaffNameStmt->get_result();
                                                                        // The value of the associative array
                                                                        $getStaffNameStmtRow = $getStaffNameStmtResult->fetch_assoc();
                                                                        // Display the value of the associative array
                                                                        echo $getStaffNameStmtRow['title'].' '.$getStaffNameStmtRow['first_name'];
                                                                    ?>
                                                                </dd>
                                                                <dt>Date selected</dt>
                                                                <dd><?php echo date('l d, F Y h:i:s', strtotime($selectPeersISelectedRow['time_selected'])); ?></dd>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    </dl>
                                                <?php }} ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- competencyAppraisalModal -->
                                <div class="modal fade" id="competencyAppraisalModal<?php echo $_GET['pf']; ?>" tabindex="-1" role="dialog" aria-labelledby="competencyAppraisalModalTitle" aria-hidden="true" style="-webkit-user-select:none;-ms-user-select:none;user-select:none;" data-backdrop="static" data-keyboard="false" >
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="competencyAppraisalModalTitle">
                                                    <span>
                                                        Values &amp; staff competency appraisal for 
                                                        <span>
                                                            <?php
                                                                // SQL select statement
                                                                $getStaffSql = 'SELECT `title`, `first_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                                // Prepare the SQL statement
                                                                $getStaffStmt = $dbConnection->prepare($getStaffSql);
                                                                // Bind parameters to the statement
                                                                $getStaffStmt->bind_param('s', $_GET['pf']);
                                                                // Execute the statement
                                                                $getStaffStmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $getStaffStmtResult = $getStaffStmt->get_result();
                                                                // The value of the associative array
                                                                $getStaffStmtRow = $getStaffStmtResult->fetch_assoc();
                                                                // Display the value of the associative array
                                                                echo $getStaffStmtRow['title'].' '.$getStaffStmtRow['first_name'];
                                                            ?>
                                                        </span>
                                                    </span>
                                                </h5>
                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                    // SQL select statement
                                                    $selectStaffCompetencyDataSql = 'SELECT `attribute_id`, `attribute` FROM `staff_competency_form_data` ORDER BY `attribute_id` ASC';
                                                    // Prepare the SQL statement
                                                    $selectStaffCompetencyDataSqlStmt = $dbConnection->prepare($selectStaffCompetencyDataSql);
                                                    // Execute the statement
                                                    $selectStaffCompetencyDataSqlStmt->execute();
                                                    // Retrieve the result set directly from the prepared statement
                                                    $selectStaffCompetencyDataResult = $selectStaffCompetencyDataSqlStmt->get_result();
                                                    // Fetch rows as an associative array
                                                    while($selectStaffCompetencyDataRow = $selectStaffCompetencyDataResult->fetch_assoc())
                                                    {
                                                ?>
                                                    <dl>
                                                        <dt><?php echo $selectStaffCompetencyDataRow['attribute']; ?></dt>
                                                        <dd>
                                                            <?php
                                                                // SQL select statement
                                                                $selectStaffCompetencyValueSql = 'SELECT `staff_competency_score` FROM `staff_competency` WHERE `pf_being_evaluated`=? AND `s_c_attribute_id`=? AND `cod_pf`=? ORDER BY `datePosted` ASC';
                                                                // Prepare the SQL statement
                                                                $selectStaffCompetencyValueSqlStmt = $dbConnection->prepare($selectStaffCompetencyValueSql);
                                                                // Bind parameters to the statement
                                                                $selectStaffCompetencyValueSqlStmt->bind_param('sss', $_GET['pf'],$selectStaffCompetencyDataRow['attribute_id'],$_SESSION['user_Pf_Number']);
                                                                // Execute the statement
                                                                $selectStaffCompetencyValueSqlStmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $selectStaffCompetencyValueResult = $selectStaffCompetencyValueSqlStmt->get_result();
                                                                // Fetch rows as an associative array
                                                                $selectStaffCompetencyValueRow = $selectStaffCompetencyValueResult->fetch_assoc();
                                                                if (empty($selectStaffCompetencyValueRow['staff_competency_score'])==true){
                                                                    echo 'Pending';
                                                                }
                                                                else{
                                                                    echo 'Competency score => '.$selectStaffCompetencyValueRow['staff_competency_score'];
                                                                }
                                                            ?>
                                                        </dd>
                                                    </dl>
                                                    <hr>
                                                <?php } ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- peersEvaluationModal -->
                                <div class="modal fade" id="peersEvaluationModal<?php echo $_GET['pf']; ?>" tabindex="-1" role="dialog" aria-labelledby="peersEvaluationModalTitle" aria-hidden="true" style="-webkit-user-select:none;-ms-user-select:none;user-select:none;" data-backdrop="static" data-keyboard="false" >
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="peersEvaluationModalTitle">
                                                    <span>
                                                        Peers evaluation for
                                                        <span>
                                                            <?php
                                                                // SQL select statement
                                                                $getStaffSql = 'SELECT `title`, `first_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                                // Prepare the SQL statement
                                                                $getStaffStmt = $dbConnection->prepare($getStaffSql);
                                                                // Bind parameters to the statement
                                                                $getStaffStmt->bind_param('s', $_GET['pf']);
                                                                // Execute the statement
                                                                $getStaffStmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $getStaffStmtResult = $getStaffStmt->get_result();
                                                                // The value of the associative array
                                                                $getStaffStmtRow = $getStaffStmtResult->fetch_assoc();
                                                                // Display the value of the associative array
                                                                echo $getStaffStmtRow['title'].' '.$getStaffStmtRow['first_name'];
                                                            ?>
                                                        </span>
                                                    </span>
                                                </h5>
                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                    // SQL select statement
                                                    $selectPeerEvaluationDataSql = 'SELECT `p_e_attribute_id`, `attribute` FROM `peer_evaluation_form_data` ORDER BY `p_e_attribute_id` ASC';
                                                    // Prepare the SQL statement
                                                    $selectPeerEvaluationDataSqlStmt = $dbConnection->prepare($selectPeerEvaluationDataSql);
                                                    // Execute the statement
                                                    $selectPeerEvaluationDataSqlStmt->execute();
                                                    // Retrieve the result set directly from the prepared statement
                                                    $selectPeerEvaluationDataResult = $selectPeerEvaluationDataSqlStmt->get_result();
                                                    // Fetch rows as an associative array
                                                    while($selectPeerEvaluationDataRow = $selectPeerEvaluationDataResult->fetch_assoc())
                                                    {
                                                ?>
                                                    <dl>
                                                        <dt><?php echo $selectPeerEvaluationDataRow['attribute']; ?></dt>
                                                        <dd>
                                                            <?php
                                                                // SQL select statement
                                                                $selectPeerEvaluationValueSql = 'SELECT `peer_pf`, `peer_evaluation_score` FROM `peer_evaluation` WHERE `pf_being_evaluated`=? AND `p_e_attribute_id`=? ORDER BY `datePosted` ASC';
                                                                // Prepare the SQL statement
                                                                $selectPeerEvaluationValueSqlStmt = $dbConnection->prepare($selectPeerEvaluationValueSql);
                                                                // Bind parameters to the statement
                                                                $selectPeerEvaluationValueSqlStmt->bind_param('ss', $_GET['pf'],$selectPeerEvaluationDataRow['p_e_attribute_id']);
                                                                // Execute the statement
                                                                $selectPeerEvaluationValueSqlStmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $selectPeerEvaluationValueResult = $selectPeerEvaluationValueSqlStmt->get_result();
                                                                // Fetch rows as an associative array
                                                                while($selectPeerEvaluationValueRow = $selectPeerEvaluationValueResult->fetch_assoc())
                                                                {
                                                            ?>
                                                                <dd>
                                                                    <?php
                                                                        // SQL select statement
                                                                        $getStaffSql = 'SELECT `user_Pf_Number`,`first_name`,`last_name` FROM `users` WHERE `user_Pf_Number`=?';
                                                                        // Prepare the SQL statement
                                                                        $getStaffStmt = $dbConnection->prepare($getStaffSql);
                                                                        // Bind parameters to the statement
                                                                        $getStaffStmt->bind_param('s', $selectPeerEvaluationValueRow['peer_pf']);
                                                                        // Execute the statement
                                                                        $getStaffStmt->execute();
                                                                        // Retrieve the result set directly from the prepared statement
                                                                        $getStaffStmtResult = $getStaffStmt->get_result();
                                                                        // The value of the associative array
                                                                        $getStaffStmtRow = $getStaffStmtResult->fetch_assoc();
                                                                        // Display the value of the associative array
                                                                        echo '('.$getStaffStmtRow['user_Pf_Number'].') '.$getStaffStmtRow['first_name'].' '.$getStaffStmtRow['last_name'].' => '.$selectPeerEvaluationValueRow['peer_evaluation_score'].'';
                                                                    ?>
                                                                </dd>
                                                            <?php } ?>
                                                        </dd>
                                                    </dl>
                                                    <hr>
                                                <?php } ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </table>
                            <?php
                                // SQL select statement
                                $sql = 'SELECT `pf_being_evaluated` FROM `staff_competency` WHERE `pf_being_evaluated`=? AND `cod_pf`=?';
                                // Prepare the SQL statement with a parameter placeholder
                                $stmt = $dbConnection->prepare($sql);
                                // Bind parameters to the statement
                                $stmt->bind_param('ss', $_GET['pf'],$_SESSION['user_Pf_Number']);
                                // Execute the statement
                                $stmt->execute();
                                // Retrieve the result set
                                $stmt->store_result();
                                if($stmt->num_rows!==13){
                            ?>
                            <div class="card card-outline card-success">
                                <div class="card-body">
                                    VALUES AND STAFF COMPETENCY APPRAISAL
                                    <form action="" method="post" onsubmit="return staffCompetencyJsValidation()" onreset="return resetStaffCompetencyForm()">
                                        <?php
                                            // SQL select statement
                                            $sql = 'SELECT `s_c_attribute_id` FROM `staff_competency` WHERE `pf_being_evaluated`=? AND `cod_pf`=? ORDER BY `s_c_attribute_id` ASC';
                                            // Prepare the SQL statement
                                            $stmt = $dbConnection->prepare($sql);
                                            // Bind parameters to the statement
                                            $stmt->bind_param('ss', $_GET['pf'],$_SESSION['user_Pf_Number']);
                                            // Execute the statement
                                            $stmt->execute();
                                            // Retrieve the result set directly from the prepared statement
                                            $result = $stmt->get_result();
                                            // Fetch rows as an associative array
                                            $s_c_attribute_id = $result->fetch_assoc();
                                            if (empty($s_c_attribute_id['s_c_attribute_id'])===true){ ?>
                                                <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
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
                                                            $sql = 'SELECT `attribute_id`, `attribute` FROM `staff_competency_form_data` ORDER BY `attribute_id` ASC';
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
                                                                <input type="text" name="attribute_id" id="attribute_id" value="<?php echo $row['attribute_id']; ?>" readonly>
                                                            </td>
                                                            <td>
                                                                <?php echo $row['attribute']; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="staff_competency_value" id="poor" value="0">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="staff_competency_value" id="fair" value="1">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="staff_competency_value" id="good" value="2">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="staff_competency_value" id="vGood" value="3">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="radio" name="staff_competency_value" id="excellent" value="4">
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
                                                    $sql = 'SELECT `s_c_attribute_id` FROM `staff_competency` WHERE `pf_being_evaluated`=? AND `cod_pf`=? ORDER BY `s_c_attribute_id` DESC';
                                                    // Prepare the SQL statement
                                                    $stmt = $dbConnection->prepare($sql);
                                                    // Bind parameters to the statement
                                                    $stmt->bind_param('ss', $_GET['pf'],$_SESSION['user_Pf_Number']);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Retrieve the result set directly from the prepared statement
                                                    $result = $stmt->get_result();
                                                    // Fetch a single row as an associative array
                                                    $row = $result->fetch_assoc();
                                                    // The value of the associative array
                                                    $latestScId =  $row["s_c_attribute_id"];
                                                    // Replace all occurrences of the search string "SC_" with the replacement string ""
                                                    $nextStaffCompetencyId = str_replace("SC_","",$latestScId);
                                                    // Pad a $nextStaffCompetencyId string to a certain length with another string
                                                    $nextStaffCompetencyId = str_pad($nextStaffCompetencyId + 1,2,0, STR_PAD_LEFT);
                                                    // The new value of $nextStaffCompetencyId
                                                    $nextStaffCompetencyId = "SC_" .$nextStaffCompetencyId;
                                                ?>
                                                <?php
                                                    if ($nextStaffCompetencyId=="SC_12" || $nextStaffCompetencyId=="SC_13"){
                                                ?>
                                                    <?php
                                                        // SQL select statement
                                                        $sql = 'SELECT `attribute_id`, `attribute` FROM `staff_competency_form_data` WHERE `attribute_id`=?';
                                                        // Prepare the SQL statement
                                                        $stmt = $dbConnection->prepare($sql);
                                                        // Bind parameters to the statement
                                                        $stmt->bind_param('s', $nextStaffCompetencyId);
                                                        // Execute the statement
                                                        $stmt->execute();
                                                        // Retrieve the result set directly from the prepared statement
                                                        $result = $stmt->get_result();
                                                        // Fetch rows as an associative array
                                                        $row = $result->fetch_assoc();
                                                    ?>
                                                    <?php echo $row['attribute']; ?>
                                                    <br>
                                                    <input type="text" name="attribute_id" id="attribute_id" value="<?php echo $row['attribute_id']; ?>" readonly hidden>
                                                    <textarea name="staffCompetencyStmt" id="staffCompetencyStmt" class="form-control" cols="20" rows="2"></textarea>
                                                    <hr>
                                                    <div class="col-lg-10 text-right justify-content-center d-flex">
                                                        <button type="submit" class="btn btn-primary mr-2" name="saveStaffCompetencyStmt" id="saveStaffCompetencyStmt">
                                                            <?php if ($nextStaffCompetencyId < "SC_13") { ?>
                                                                Next
                                                            <?php }elseif ($nextStaffCompetencyId ==="SC_13") { ?>
                                                                Finish
                                                            <?php } ?>
                                                        </button>
                                                        <!-- <button type="reset" class="btn btn-secondary" name="resetEvaluation" id="resetEvaluation">Reset</button> -->

                                                        <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                            <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                        </button>
                                                    </div>
                                                <?php }else{ ?>
                                                    <table class="table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl tabel-hover table-bordered">
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
                                                                $sql = 'SELECT `attribute_id`, `attribute` FROM `staff_competency_form_data` WHERE `attribute_id`=?';
                                                                // Prepare the SQL statement
                                                                $stmt = $dbConnection->prepare($sql);
                                                                // Bind parameters to the statement
                                                                $stmt->bind_param('s', $nextStaffCompetencyId);
                                                                // Execute the statement
                                                                $stmt->execute();
                                                                // Retrieve the result set directly from the prepared statement
                                                                $result = $stmt->get_result();
                                                                // Fetch rows as an associative array
                                                                $row = $result->fetch_assoc();
                                                            ?>
                                                            <tr>
                                                                <td class="d-none">
                                                                    <input type="text" name="attribute_id" id="attribute_id" value="<?php echo $row['attribute_id']; ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <?php echo $row['attribute']; ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="staff_competency_value" id="poor" value="0">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="staff_competency_value" id="fair" value="1">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="staff_competency_value" id="good" value="2">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="staff_competency_value" id="vGood" value="3">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="radio" name="staff_competency_value" id="excellent" value="4">
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
                                                            <?php if ($nextStaffCompetencyId < "SC_13") { ?>
                                                                Next
                                                            <?php }elseif ($nextStaffCompetencyId ==="SC_13") { ?>
                                                                Finish
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
                                        var radioButtons = document.querySelectorAll('input[name="staff_competency_value"]');

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

                                        function resetStaffCompetencyForm()
                                        {
                                            // Set the value of is_reset_valid to be true by default
                                            var is_reset_valid = true;
                                            // Set the value of checkedStatus to be false by default
                                            var checkedStatus = false;
                                            var radioButton = document.getElementsByName('staff_competency_value');
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

                                        function staffCompetencyJsValidation()
                                        {
                                            // Set the value of is_input_valid to be true by default
                                            var is_input_valid = true;
                                            // Set the value of checkedStatus to be false by default
                                            var checkedStatus = false;

                                            document.getElementById('saveEvaluation').className = "d-none";
                                            document.getElementById('resetEvaluation').className = "d-none";
                                            document.getElementById('loadingSpinner').className = "btn btn-primary";
                                            var radioButton = document.getElementsByName('staff_competency_value');

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
                            <?php } ?>
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