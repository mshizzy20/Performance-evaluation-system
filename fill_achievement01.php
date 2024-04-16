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
            header("Location: login.php?loginError=loginError01&link=expected_achivement");
            exit();
        }
        else
        {
            // SQL select statement
            $selectStaffPfSql = 'SELECT `user_Pf_Number` FROM `achivements` WHERE `user_Pf_Number`=?';
            // Prepare the SQL statement with a parameter placeholder
            $selectStaffPfStmt = $dbConnection->prepare($selectStaffPfSql);
            // Bind parameters to the statement
            $selectStaffPfStmt->bind_param('s', $_SESSION['user_Pf_Number']);
            // Execute the statement
            $selectStaffPfStmt->execute();
            // Retrieve the result set
            $selectStaffPfStmt->store_result();
            if ($selectStaffPfStmt->num_rows==20)
            {
                header("Location: achivement.php");
                exit();
            }
        }

        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'saveAchivement' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['saveAchivement']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';

            $achivement_id = mysqli_real_escape_string($dbConnection,$_POST['achivement_id']);
            $achivement_value = mysqli_real_escape_string($dbConnection,$_POST['achivement_value']);

            if (empty($achivement_id)==true)
            {
                header('Location: fill_achievement.php?achivementError=empty_achivement_id');
                exit();
            }
            elseif (empty($achivement_value)==true)
            {
                header('Location: fill_achievement.php?achivementError=empty_achivement_value');
                exit();
            }
            elseif ($achivement_value<"1")
            {
                $_SESSION['achivement_data_less_than_one']=$achivement_value;
                header('Location: fill_achievement.php');
                exit();
            }
            else
            {
                // Prepare a SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare('SELECT `achivement_id` FROM `achivements` WHERE `achivement_id`=? AND `user_Pf_Number`=?');
                // Bind parameters to the statement
                $stmt->bind_param('ss', $achivement_id,$_SESSION['user_Pf_Number']);
                // Execute the statement
                $stmt->execute();
                // Store result
                $stmt->store_result();
                if ($stmt->num_rows==1)
                {
                    $_SESSION['achivement_data_exists']=$achivement_id;
                    header('Location: fill_achievement.php');
                    exit();
                }
                else
                {
                    // Prepare a SQL statement with a parameter placeholder
                    $stmt = $dbConnection->prepare('SELECT `maximum` FROM `expected_achivement_data` WHERE `expected_achivement_id`=?');
                    // Bind parameters to the statement
                    $stmt->bind_param('s', $achivement_id);
                    // Execute the statement
                    $stmt->execute();
                    // Retrieve the result set
                    $result = $stmt->get_result();
                    // Fetch data
                    $row = $result->fetch_assoc();
                    if ($achivement_value>$row["maximum"])
                    {
                        $_SESSION['maxValue']=$row["maximum"];
                        header('Location: fill_achievement.php');
                        exit();
                    }
                    else
                    {
                        $add_A_V_Sql = 'INSERT INTO `achivements`(`user_Pf_Number`, `achivement_id`, `achivement`) VALUES (?,?,?)';
                        $add_A_V_Stmt = $dbConnection->prepare($add_A_V_Sql);
                        $add_A_V_Stmt->bind_param('sss',$_SESSION['user_Pf_Number'],$achivement_id,$achivement_value);
                        if ($add_A_V_Stmt->execute())
                        {
                            header('Location: fill_achievement.php');
                            exit();
                        }
                    }
                }
            }
        }

        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'finishAchivement' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['finishAchivement']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';

            $achivement_id = mysqli_real_escape_string($dbConnection,$_POST['achivement_id']);
            $achivement_value = mysqli_real_escape_string($dbConnection,$_POST['achivement_value']);

            if (empty($achivement_id)==true)
            {
                header('Location: fill_achievement.php?achivementError=empty_achivement_id');
                exit();
            }
            elseif (empty($achivement_value)==true)
            {
                header('Location: fill_achievement.php?achivementError=empty_achivement_value');
                exit();
            }
            elseif ($achivement_value<"1")
            {
                $_SESSION['achivement_data_less_than_one']=$achivement_value;
                header('Location: fill_achievement.php');
                exit();
            }
            else
            {
                // Prepare a SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare('SELECT `achivement_id` FROM `achivements` WHERE `achivement_id`=? AND `user_Pf_Number`=?');
                // Bind parameters to the statement
                $stmt->bind_param('ss', $achivement_id,$_SESSION['user_Pf_Number']);
                // Execute the statement
                $stmt->execute();
                // Store result
                $stmt->store_result();
                if ($stmt->num_rows==1)
                {
                    $_SESSION['achivement_data_exists']=$achivement_id;
                    header('Location: fill_achievement.php');
                    exit();
                }
                else
                {
                    // Prepare a SQL statement with a parameter placeholder
                    $stmt = $dbConnection->prepare('SELECT `maximum` FROM `expected_achivement_data` WHERE `expected_achivement_id`=?');
                    // Bind parameters to the statement
                    $stmt->bind_param('s', $achivement_id);
                    // Execute the statement
                    $stmt->execute();
                    // Retrieve the result set
                    $result = $stmt->get_result();
                    // Fetch data
                    $row = $result->fetch_assoc();
                    if ($achivement_value>$row["maximum"])
                    {
                        $_SESSION['maxValue']=$row["maximum"];
                        header('Location: fill_achievement.php');
                        exit();
                    }
                    else
                    {
                        $add_A_V_Sql = 'INSERT INTO `achivements`(`user_Pf_Number`, `achivement_id`, `achivement`) VALUES (?,?,?)';
                        $add_A_V_Stmt = $dbConnection->prepare($add_A_V_Sql);
                        $add_A_V_Stmt->bind_param('sss',$_SESSION['user_Pf_Number'],$achivement_id,$achivement_value);
                        if ($add_A_V_Stmt->execute())
                        {
                            header('Location: achivement.php');
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
                <title>epes | Achivement</title>
                <!-- include header.php php file -->
                <?php include 'header.php' ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <?php if (isset($_SESSION['achivement_data_less_than_one'])==true && empty($_SESSION['achivement_data_less_than_one'])==false){ ?>
                    <script>
                        alert('Achivement cannot be less than 1 (one)');
                    </script>
                    <?php $_SESSION['achivement_data_less_than_one']=""?>
                <?php }elseif (isset($_SESSION['achivement_data_exists'])==true && empty($_SESSION['achivement_data_exists'])==false){ ?>
                    <script>
                        alert('Achivement already set');
                    </script>
                    <?php $_SESSION['achivement_data_exists']=""?>
                <?php }elseif (isset($_SESSION['maxValue'])==true && empty($_SESSION['maxValue'])==false){ ?>
                    <script>
                        alert('Maximum value is <?php echo $_SESSION['maxValue']; ?>');
                    </script>
                    <?php $_SESSION['maxValue']=""?>
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
                                        <h1 class="m-0">Achivement</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <?php
                            // SQL select statement
                            $selectAchivementIdSql = 'SELECT `achivement_id` FROM `achivements` WHERE `user_Pf_Number`=? ORDER BY `achivement_id` ASC';
                    
                            // Prepare the SQL statement with a parameter placeholder
                            $selectAchivementIdStmt = $dbConnection->prepare($selectAchivementIdSql);
                    
                            // Bind parameters to the statement
                            $selectAchivementIdStmt->bind_param('s', $_SESSION['user_Pf_Number']);
                    
                            // Execute the statement
                            $selectAchivementIdStmt->execute();

                            // Retrieve the result set
                            $result = $selectAchivementIdStmt->get_result();

                            $achivement_id = $result->fetch_assoc();
                            if (empty($achivement_id['achivement_id'])){
                        ?>
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="" method="post">
                                            <table class="table table-responsive-md tabe-hover table-bordered">
                                                <?php
                                                    // Prepare a SQL statement with a parameter placeholder
                                                    $stmt = $dbConnection->prepare('SELECT `expected_achivement_id`, `Ref`, `class`, `evidence`, `unit`, `maximum` FROM `expected_achivement_data` ORDER BY `expected_achivement_id` ASC');
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Retrieve the result set
                                                    $result = $stmt->get_result();
                                                    // Fetch data
                                                    $row = $result->fetch_assoc();
                                                ?>
                                                    <thead>
                                                        <tr>
                                                            <th class="col-2"><?php echo $row['class']; ?></th>
                                                            <th class="col-2">Maximum</th>
                                                            <th class="col-2">Achived</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="d-none"><input type="text" name="achivement_id" id="achivement_id" value="<?php echo $row['expected_achivement_id']; ?>" readonly></td>
                                                            <td><?php echo $row['Ref']; ?></td>
                                                            <td><?php echo $row['maximum']; ?></td>
                                                            <td>
                                                                <div class="form-group">
                                                                <span id="achievedtStatus" class="d-block"></span>
                                                                    <input type="tel" name="achivement_value" id="achivement_value" class="form-control form-control-sm" placeholder="Enter achivement" autocomplete="off" autofocus>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                            </table>
                                            <hr>
                                            <div class="col-lg-10 text-right justify-content-center d-flex">
                                                <button type="submit" class="btn btn-primary mr-2" name="saveAchivement" id="saveAchivement">Next</button>
                                                <button type="reset" class="btn btn-secondary" name="resetAchivement" id="resetAchivement">Reset</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php }else{ ?>
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <?php
                                            // Prepare a SQL statement with a parameter placeholder
                                            $stmt = $dbConnection->prepare('SELECT `achivement_id` FROM `achivements` WHERE `user_Pf_Number`=? ORDER BY `achivement_id` DESC');
                                            // Bind parameters to the statement
                                            $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                            // Execute the statement
                                            $stmt->execute();
                                            // Retrieve the result set
                                            $result = $stmt->get_result();
                                            // Fetch data
                                            $row = $result->fetch_assoc();
                                            $latestAi =  $row["achivement_id"];
                                            
                                            $nextAid = str_replace("EA_","",$latestAi);
                                            $nextAid = str_pad($nextAid + 1,2,0, STR_PAD_LEFT);
                                            $nextAid = "EA_" .$nextAid;
                                        ?>
                                        <form action="" method="post">
                                            <table class="table table-responsive-md tabe-hover table-bordered">
                                                <?php
                                                    // Prepare a SQL statement with a parameter placeholder
                                                    $stmt = $dbConnection->prepare('SELECT `expected_achivement_id`, `Ref`, `class`, `evidence`, `unit`, `maximum` FROM `expected_achivement_data` WHERE `expected_achivement_id`=?');
                                                    // Bind parameters to the statement
                                                    $stmt->bind_param('s', $nextAid);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Retrieve the result set
                                                    $result = $stmt->get_result();
                                                    // Fetch data
                                                    $row = $result->fetch_assoc();
                                                ?>
                                                <thead>
                                                    <tr>
                                                        <th class="col-2"><?php echo $row['class']; ?></th>
                                                        <th class="col-2">Maximum</th>
                                                        <th class="col-2">Achived</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="d-none"><input type="text" name="achivement_id" id="achivement_id" value="<?php echo $row['expected_achivement_id']; ?>" readonly></td>
                                                        <td><?php echo $row['Ref']; ?></td>
                                                        <td><?php echo $row['maximum']; ?></td>
                                                        <td>
                                                            <div class="form-group">
                                                            <span id="achievedtStatus" class="d-block"></span>
                                                                <input type="tel" name="achivement_value" id="achivement_value" class="form-control form-control-sm" placeholder="Enter achivement" autocomplete="off" autofocus>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <hr>
                                            <div class="col-lg-10 text-right justify-content-center d-flex">
                                                <div id="buttonsDiv">
                                                    <?php if ($nextAid == "EA_20") { ?>
                                                        <button type="submit" class="btn btn-primary mr-2" name="finishAchivement" id="finishAchivement">Finish</button>
                                                    <?php }elseif ($nextAid <"EA_20") { ?>
                                                    <button type="submit" class="btn btn-primary mr-2" name="saveAchivement" id="saveAchivement">Next</button>
                                                    <?php } ?>
                                                    <button type="reset" class="btn btn-secondary" name="resetAchivement" id="resetAchivement">Reset</button>
                                                </div>
                                                <div id="loadingDiv" class="d-none">
                                                    <button type="button" class="btn btn-secondary" name="loadingRequest" id="loadingRequest">
                                                        <span class="spinner-border text-light" style="width:20px;height:20px; border-width:3px;"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                    <!-- include mainfooter.php php file -->
                    <?php include 'mainfooter.php' ?>
                </div>
                <!-- include footer.php php file -->
                <?php include 'footer.php' ?>
            </body>
        </html>
<?php } ?>