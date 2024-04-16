<?php
    // Check if session is not started
    if (session_status() === PHP_SESSION_NONE)
    {
        // start a new session
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
                // else if the number of rows is identical to integer 20, redirect to achievement.php web page
                if ($stmt->num_rows==20)
                {
                    header("Location: achievement.php");
                    exit();
                }
            }
        }

        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'saveAchievement' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['saveAchievement']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';

            $achievement_id = mysqli_real_escape_string($dbConnection,$_POST['achievement_id']);
            $achievement_value = mysqli_real_escape_string($dbConnection,$_POST['achievement_value']);

            if (empty($achievement_id)==true)
            {
                $_SESSION['empty_achievement_id']='empty_achievement_id';
                header('Location: fill_achievement.php');
                exit();
            }
            elseif (empty($achievement_value)==true)
            {
                $_SESSION['empty_achievement_value']='empty_achievement_value';
                header('Location: fill_achievement.php');
                exit();
            }
            elseif ($achievement_value<"1")
            {
                $_SESSION['achievement_data_less_than_one']=$achievement_value;
                header('Location: fill_achievement.php');
                exit();
            }
            else
            {
                // SQL select statement
                $sql = 'SELECT `achievement_id` FROM `self_achievement` WHERE `achievement_id`=? AND `user_Pf_Number`=?';
                // Prepare the SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('ss', $achievement_id,$_SESSION['user_Pf_Number']);
                // Execute the statement
                $stmt->execute();
                // Store the result set
                $stmt->store_result();
                // if number of rows is equal to one redirect to fill_achievement.php web page
                if ($stmt->num_rows==1)
                {
                    $_SESSION['achievement_data_exists']=$achievement_id;
                    header('Location: fill_achievement.php');
                    exit();
                }
                else
                {
                    // SQL select statement
                    $sql = 'SELECT `maximum` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                    // Prepare the SQL statement
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('s', $achievement_id);
                    // Execute the statement
                    $stmt->execute();
                    // Retrieve the result set directly from the prepared statement
                    $result = $stmt->get_result();
                    // Fetch a single row as an associative array
                    $row = $result->fetch_assoc();
                    // if achievement_value is more than the set maximum value redirect to fill_achievement.php web page
                    if ($achievement_value>$row["maximum"])
                    {
                        $_SESSION['maximumValue']=$row["maximum"];
                        header('Location: fill_achievement.php');
                        exit();
                    }
                    else
                    {
                        $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));
                        // SQL select statement
                        $sql = 'INSERT INTO `self_achievement`(`user_Pf_Number`, `achievement_id`, `achievement`, `datePosted`) VALUES (?,?,?,?)';
                        // Prepare the SQL statement
                        $stmt = $dbConnection->prepare($sql);
                        // Bind parameters to the statement
                        $stmt->bind_param('ssss',$_SESSION['user_Pf_Number'],$achievement_id,$achievement_value,$datePosted);
                        // if the statement is executed
                        if ($stmt->execute())
                        {
                            // if $achievement_id is less than string EA_20, redirect to fill_achievement.php web page
                            if ($achievement_id<"EA_20")
                            {
                                header('Location: fill_achievement.php');
                                exit();
                            }
                            // elseif $achievement_id is identical to string EA_20, redirect to achievement.php web page
                            elseif ($achievement_id==="EA_20")
                            {
                                header('Location: achievement.php');
                                exit();
                            }
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
                <title>epes | Achievement</title>
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

                <?php if (isset($_SESSION['empty_achievement_id'])==true && empty($_SESSION['empty_achievement_id'])==false){ ?>
                    <script>
                            alert('Please try again');
                        </script>
                    <?php unset($_SESSION['empty_achievement_id'])?>
                <?php }elseif (isset($_SESSION['empty_achievement_value'])==true && empty($_SESSION['empty_achievement_value'])==false){ ?>
                    <script>
                            alert('Please enter achievement');
                        </script>
                    <?php unset($_SESSION['empty_achievement_value'])?>
                <?php }elseif (isset($_SESSION['achievement_data_less_than_one'])==true && empty($_SESSION['achievement_data_less_than_one'])==false){ ?>
                    <script>
                            alert('Achievement cannot be less than 1 (one)');
                        </script>
                    <?php unset($_SESSION['achievement_data_less_than_one'])?>
                <?php }elseif (isset($_SESSION['achievement_data_exists'])==true && empty($_SESSION['achievement_data_exists'])==false){ ?>
                    <script>
                            alert('Achievement has already been set');
                        </script>
                    <?php unset($_SESSION['achievement_data_exists'])?>
                <?php }elseif (isset($_SESSION['maximumValue'])==true && empty($_SESSION['maximumValue'])==false){ ?>
                    <script>
                            alert('Maximum expected achievement is <?php echo $_SESSION['maximumValue']; ?>');
                        </script>
                    <?php unset($_SESSION['maximumValue'])?>
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
                                        <h1 class="m-0">Achievement</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="" method="post" onsubmit="return achievementJsValidation()">
                                    <!-- <form action="" method="post"> -->
                                        <?php
                                            // SQL select statement
                                            $selectAchievementIdSql = 'SELECT `achievement_id` FROM `self_achievement` WHERE `user_Pf_Number`=? ORDER BY `achievement_id` ASC';
                                            // Prepare the SQL statement
                                            $selectAchievementIdStmt = $dbConnection->prepare($selectAchievementIdSql);
                                            // Bind parameters to the statement
                                            $selectAchievementIdStmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                            // Execute the statement
                                            $selectAchievementIdStmt->execute();
                                            // Retrieve the result set directly from the prepared statement
                                            $result = $selectAchievementIdStmt->get_result();
                                            // Fetch rows as an associative array
                                            $achievement_id = $result->fetch_assoc();
                                            if (empty($achievement_id['achievement_id'])==true){
                                        ?>
                                            <table class="table table-responsive-md tabe-hover table-bordered">
                                                <?php
                                                    // SQL select statement
                                                    $sql = 'SELECT `expected_achievement_id`, `Ref`, `class`, `evidence`, `unit`, `maximum`, `weight` FROM `expected_achievement_form_data` ORDER BY `expected_achievement_id` ASC';
                                                    // Prepare the SQL statement
                                                    $stmt = $dbConnection->prepare($sql);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Retrieve the result set directly from the prepared statement
                                                    $result = $stmt->get_result();
                                                    // Fetch rows as an associative array
                                                    $row = $result->fetch_assoc();
                                                ?>
                                                    <thead>
                                                        <tr>
                                                            <th class="col-2"><?php echo $row['class']; ?></th>
                                                            <th class="col-2">Maximum</th>
                                                            <th class="col-2">Achievement</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="d-none"><input type="text" name="achievement_id" id="achievement_id" value="<?php echo $row['expected_achievement_id']; ?>" readonly></td>
                                                            <td><?php echo $row['Ref']; ?></td>
                                                            <td>
                                                                <?php echo $row['maximum']; ?>
                                                                <input type="tel" name="maximumExpectedValue" id="maximumExpectedValue" class="d-none" readonly value="<?php echo $row['maximum']; ?>" style="display:none">
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                <span id="achievementStatus" class="d-block"></span>
                                                                    <input type="tel" name="achievement_value" id="achievement_value" class="form-control form-control-sm" placeholder="Enter achievement" autocomplete="off" autofocus>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                            </table>
                                            <hr>
                                            <div class="col-lg-10 text-right justify-content-center d-flex">
                                                <button type="submit" class="btn btn-primary mr-2" name="saveAchievement" id="saveAchievement">Next</button>
                                                <button type="reset" class="btn btn-secondary" name="resetAchievement" id="resetAchievement">Reset</button>

                                                <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                </button>
                                            </div>
                                        <?php }else{ ?>
                                                <?php
                                                    // SQL select statement
                                                    $sql = 'SELECT `achievement_id` FROM `self_achievement` WHERE `user_Pf_Number`=? ORDER BY `achievement_id` DESC';
                                                    // Prepare the SQL statement
                                                    $stmt = $dbConnection->prepare($sql);
                                                    // Bind parameters to the statement
                                                    $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Retrieve the result set directly from the prepared statement
                                                    $result = $stmt->get_result();
                                                    // Fetch a single row as an associative array
                                                    $row = $result->fetch_assoc();
                                                    // The value of the associative array
                                                    $latestEai =  $row["achievement_id"];
                                                    // Replace all occurrences of the search string "EA_" with the replacement string ""
                                                    $nextAchievementId = str_replace("EA_","",$latestEai);
                                                    // Pad a $nextAchievementId string to a certain length with another string
                                                    $nextAchievementId = str_pad($nextAchievementId + 1,2,0, STR_PAD_LEFT);
                                                    // The new value of $nextAchievementId
                                                    $nextAchievementId = "EA_" .$nextAchievementId;
                                                ?>
                                                <table class="table table-responsive-md tabe-hover table-bordered">
                                                    <?php
                                                        // SQL select statement
                                                        $sql = 'SELECT `expected_achievement_id`, `Ref`, `class`, `evidence`, `unit`, `maximum`, `weight` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                                                        // Prepare the SQL statement
                                                        $stmt = $dbConnection->prepare($sql);
                                                        // Bind parameters to the statement
                                                        $stmt->bind_param('s', $nextAchievementId);
                                                        // Execute the statement
                                                        $stmt->execute();
                                                        // Retrieve the result set directly from the prepared statement
                                                        $result = $stmt->get_result();
                                                        // Fetch a single row as an associative array
                                                        $row = $result->fetch_assoc();
                                                    ?>
                                                        <thead>
                                                            <tr>
                                                                <th class="col-2"><?php echo $row['class']; ?></th>
                                                                <th class="col-2">Maximum</th>
                                                                <th class="col-2">Achievement</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="d-none"><input type="text" name="achievement_id" id="achievement_id" value="<?php echo $row['expected_achievement_id']; ?>" readonly></td>
                                                                <td><?php echo $row['Ref']; ?></td>
                                                                <td>
                                                                    <?php echo $row['maximum']; ?>
                                                                    <input type="tel" name="maximumExpectedValue" id="maximumExpectedValue" class="d-none" readonly value="<?php echo $row['maximum']; ?>" style="display:none">
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                    <span id="achievementStatus" class="d-block"></span>
                                                                        <input type="tel" name="achievement_value" id="achievement_value" class="form-control form-control-sm" placeholder="Enter achievement" autocomplete="off" autofocus>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                </table>
                                            <hr>
                                            <div class="col-lg-10 text-right justify-content-center d-flex">
                                                <button type="submit" class="btn btn-primary mr-2" name="saveAchievement" id="saveAchievement">
                                                    <?php if ($nextAchievementId < "EA_20") { ?>
                                                        Next
                                                    <?php }elseif ($nextAchievementId ==="EA_20") { ?>
                                                        Finish
                                                    <?php } ?>
                                                </button>
                                                <button type="reset" class="btn btn-secondary" name="resetAchievement" id="resetAchievement">Reset</button>

                                                <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </form>
                                    <script>
                                        function achievementJsValidation()
                                        {
                                            // Set the value of is_input_valid to be true by default
                                            var is_input_valid = true;

                                            document.getElementById('saveAchievement').className = "d-none";
                                            document.getElementById('resetAchievement').className = "d-none";
                                            document.getElementById('loadingSpinner').className = "btn btn-primary";

                                            if (document.getElementById('achievement_value').value === "")
                                            {
                                                is_input_valid = false;
                                                document.getElementById('achievement_value').style.border = "1px solid red";
                                                document.getElementById('achievementStatus').style.color = "#dc3545";
                                                document.getElementById('achievementStatus').innerHTML = "Please enter your achievement";
                                                // no loader
                                                document.getElementById('loadingSpinner').className = "d-none";
                                                document.getElementById('saveAchievement').className = "btn btn-primary mr-2";
                                                document.getElementById('resetAchievement').className = "btn btn-secondary";
                                            }
                                            else if (document.getElementById('achievement_value').value === "0")
                                            {
                                                is_input_valid = false;
                                                document.getElementById('achievement_value').style.border = "1px solid red";
                                                document.getElementById('achievementStatus').style.color = "#dc3545";
                                                document.getElementById('achievementStatus').innerHTML = "Achievement cannot be zero";
                                                // no loader
                                                document.getElementById('loadingSpinner').className = "d-none";
                                                document.getElementById('saveAchievement').className = "btn btn-primary mr-2";
                                                document.getElementById('resetAchievement').className = "btn btn-secondary";
                                            }
                                            else if (document.getElementById('achievement_value').value < "1")
                                            {
                                                is_input_valid = false;
                                                document.getElementById('achievement_value').style.border = "1px solid red";
                                                document.getElementById('achievementStatus').style.color = "#dc3545";
                                                document.getElementById('achievementStatus').innerHTML = "Achievement cannot be less than one";
                                                // no loader
                                                document.getElementById('loadingSpinner').className = "d-none";
                                                document.getElementById('saveAchievement').className = "btn btn-primary mr-2";
                                                document.getElementById('resetAchievement').className = "btn btn-secondary";
                                            }
                                            // else if (document.getElementById('achievement_value').value > document.getElementById('maximumExpectedValue').value)
                                            // {
                                            //     is_input_valid = false;
                                            //     document.getElementById('achievement_value').style.border = "1px solid red";
                                            //     document.getElementById('achievementStatus').style.color = "#dc3545";
                                            //     document.getElementById('achievementStatus').innerHTML = "Maximum expected value is <strong>"+document.getElementById('maximumExpectedValue').value+"</strong>";
                                            //     // no loader
                                            //     document.getElementById('loadingSpinner').className = "d-none";
                                            //     document.getElementById('saveAchievement').className = "btn btn-primary mr-2";
                                            //     document.getElementById('resetAchievement').className = "btn btn-secondary";
                                            // }
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