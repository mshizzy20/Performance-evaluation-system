<?php
    // Check if session is not started
    if (session_status() === PHP_SESSION_NONE)
    {
        // start session if session is not started
        session_start();
    }

    // if $_SESSION['user_Pf_Number'] is not set, redirect to login.php
    if (isset($_SESSION['user_Pf_Number']) == false)
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

        $selectSystemAccessSql = 'SELECT `system_access` FROM `users` WHERE `user_Pf_Number`=?';
        $selectSystemAccessStmt = $dbConnection->prepare($selectSystemAccessSql);
        $selectSystemAccessStmt->bind_param('s', $_SESSION['user_Pf_Number']);
        $selectSystemAccessStmt->execute();
        $selectSystemAccessResult = $selectSystemAccessStmt->get_result();
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
            // redirect user to login.php web page
            header("Location: login.php");
            exit();
        }
        else
        {
            $selectStaffPfSql = 'SELECT `user_Pf_Number` FROM `expected_achievement` WHERE `user_Pf_Number`=?';
            $selectStaffPfStmt = $dbConnection->prepare($selectStaffPfSql);
            $selectStaffPfStmt->bind_param('s', $_SESSION['user_Pf_Number']);
            $selectStaffPfStmt->execute();
            $selectStaffPfStmt->store_result();
            if ($selectStaffPfStmt->num_rows==20)
            {
                header("Location: expected_achievement.php");
                exit();
            }
        }

        // If the current HTTP request method is POST and if a form element with the name attribute set to 'saveExpectedAchievement' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['saveExpectedAchievement']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';

            $expected_achievement_id = mysqli_real_escape_string($dbConnection,$_POST['expected_achievement_id']);
            $expected_achievement_value = mysqli_real_escape_string($dbConnection,$_POST['expected_achievement_value']);

            if (empty($expected_achievement_id)==true)
            {
                $_SESSION['empty_expected_achievement_id']='empty_expected_achievement_id';
                header('Location: fill_expected_achievement.php');
                exit();
            }
            elseif (empty($expected_achievement_value)==true)
            {
                $_SESSION['empty_expected_achievement_value']='empty_expected_achievement_value';
                header('Location: fill_expected_achievement.php');
                exit();
            }
            elseif ($expected_achievement_value<"1")
            {
                $_SESSION['expected_achievement_data_less_than_one']=$expected_achievement_value;
                header('Location: fill_expected_achievement.php');
                exit();
            }
            else
            {
                // SQL select statement
                $sql = 'SELECT `expected_achievement_id` FROM `expected_achievement` WHERE `expected_achievement_id`=? AND `user_Pf_Number`=?';
                // Prepare the SQL statement with a parameter placeholder
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('ss', $expected_achievement_id,$_SESSION['user_Pf_Number']);
                // Execute the statement
                $stmt->execute();
                // Store the result set
                $stmt->store_result();
                // if number of rows is equal to one redirect to fill_expected_achievement.php web page
                if ($stmt->num_rows==1)
                {
                    $_SESSION['expected_achievement_data_exists']=$expected_achievement_id;
                    header('Location: fill_expected_achievement.php');
                    exit();
                }
                else
                {
                    // SQL select statement
                    $sql = 'SELECT `maximum` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                    // Prepare the SQL statement
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('s', $expected_achievement_id);
                    // Execute the statement
                    $stmt->execute();
                    // Retrieve the result set directly from the prepared statement
                    $result = $stmt->get_result();
                    // Fetch a single row as an associative array
                    $row = $result->fetch_assoc();
                    // if expected_achievement_value is more than the set maximum value redirect to fill_expected_achievement.php web page
                    if ($expected_achievement_value>$row["maximum"])
                    {
                        $_SESSION['maximumValue']=$row["maximum"];
                        header('Location: fill_expected_achievement.php');
                        exit();
                    }
                    else
                    {
                        $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));
                        // SQL select statement
                        $sql = 'INSERT INTO `expected_achievement`(`user_Pf_Number`, `expected_achievement_id`, `expected_achievement`,  `datePosted`) VALUES (?,?,?,?)';
                        // Prepare the SQL statement
                        $stmt = $dbConnection->prepare($sql);
                        // Bind parameters to the statement
                        $stmt->bind_param('ssss',$_SESSION['user_Pf_Number'],$expected_achievement_id,$expected_achievement_value,$datePosted);
                        // if the statement is executed
                        if ($stmt->execute())
                        {
                            // if $expected_achievement_id is less than string EA_20, redirect to fill_expected_achievement.php web page
                            if ($expected_achievement_id<"EA_20")
                            {
                                header('Location: fill_expected_achievement.php');
                                exit();
                            }
                            // elseif $expected_achievement_id is identical to string EA_20, redirect to expected_achievement.php web page
                            elseif ($expected_achievement_id==="EA_20")
                            {
                                header('Location: expected_achievement.php');
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
                <title>epes | Expected achievement</title>
                <!-- include header.php php file -->
                <?php include 'header.php' ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

                <?php if (isset($_SESSION['empty_expected_achievement_id'])==true && empty($_SESSION['empty_expected_achievement_id'])==false){ ?>
                    <script>
                        alert('Please try again');
                    </script>
                    <?php unset($_SESSION['empty_expected_achievement_id'])?>
                <?php }elseif (isset($_SESSION['empty_expected_achievement_value'])==true && empty($_SESSION['empty_expected_achievement_value'])==false){ ?>
                    <script>
                        alert('Please enter your expected achievement');
                    </script>
                    <?php unset($_SESSION['empty_expected_achievement_value'])?>
                <?php }elseif (isset($_SESSION['expected_achievement_data_less_than_one'])==true && empty($_SESSION['expected_achievement_data_less_than_one'])==false){ ?>
                    <script>
                        alert('Expected achievement cannot be less than 1 (one)');
                    </script>
                    <?php unset($_SESSION['expected_achievement_data_less_than_one'])?>
                <?php }elseif (isset($_SESSION['expected_achievement_data_exists'])==true && empty($_SESSION['expected_achievement_data_exists'])==false){ ?>
                    <script>
                        alert('Expected achievement has already been set');
                    </script>
                    <?php unset($_SESSION['expected_achievement_data_exists'])?>
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
                                        <h1 class="m-0">Expected achievement</h1>
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
                                        <form action="" method="post" onsubmit="return expectedAchievementJsValidation()">
                                        <!-- <form action="" method="post"> -->
                                            <?php
                                                $selectAchievementIdSql = 'SELECT `expected_achievement_id` FROM `expected_achievement` WHERE `user_Pf_Number`=? ORDER BY `expected_achievement_id` ASC';
                                                $selectAchievementIdStmt = $dbConnection->prepare($selectAchievementIdSql);
                                                $selectAchievementIdStmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                                $selectAchievementIdStmt->execute();
                                                $result = $selectAchievementIdStmt->get_result();
                                                $expected_achievement_id = $result->fetch_assoc();
                                                if (empty($expected_achievement_id['expected_achievement_id'])==true){
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
                                                                <th class="col-2">Expected achievement</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="d-none"><input type="text" name="expected_achievement_id" id="expected_achievement_id" value="<?php echo $row['expected_achievement_id']; ?>" readonly></td>
                                                                <td><?php echo $row['Ref']; ?></td>
                                                                <td>
                                                                    <?php echo $row['maximum']; ?>
                                                                    <input type="tel" name="maximumExpectedValue" id="maximumExpectedValue" class="d-none" readonly value="<?php echo $row['maximum']; ?>" style="display:none">
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                    <span id="expectedAchievementStatus" class="d-block"></span>
                                                                        <input type="tel" name="expected_achievement_value" id="expected_achievement_value" class="form-control form-control-sm" placeholder="Enter expected achievement" autocomplete="off" autofocus>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                </table>
                                                <hr>
                                                <div class="col-lg-10 text-right justify-content-center d-flex">
                                                    <button type="submit" class="btn btn-primary mr-2" name="saveExpectedAchievement" id="saveExpectedAchievement">Next</button>
                                                    <button type="reset" class="btn btn-secondary" name="resetExpectedAchievement" id="resetExpectedAchievement">Reset</button>

                                                    <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                        <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                    </button>
                                                </div>
                                            <?php }else{ ?>
                                                <?php
                                                    // SQL select statement
                                                    $sql = 'SELECT `expected_achievement_id` FROM `expected_achievement` WHERE `user_Pf_Number`=? ORDER BY `expected_achievement_id` DESC';
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
                                                    $latestEai =  $row["expected_achievement_id"];
                                                    // Replace all occurrences of the search string "EA_" with the replacement string ""
                                                    $nextEai = str_replace("EA_","",$latestEai);
                                                    // Pad a $nextEai string to a certain length with another string
                                                    $nextEai = str_pad($nextEai + 1,2,0, STR_PAD_LEFT);
                                                    // The new value of $nextEai
                                                    $nextEai = "EA_" .$nextEai;
                                                ?>
                                                <table class="table table-responsive-md tabe-hover table-bordered">
                                                    <?php
                                                        // SQL select statement
                                                        $sql = 'SELECT `expected_achievement_id`, `Ref`, `class`, `evidence`, `unit`, `maximum`, `weight` FROM `expected_achievement_form_data` WHERE `expected_achievement_id`=?';
                                                        // Prepare the SQL statement
                                                        $stmt = $dbConnection->prepare($sql);
                                                        // Bind parameters to the statement
                                                        $stmt->bind_param('s', $nextEai);
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
                                                            <th class="col-2">Expected</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="d-none"><input type="text" name="expected_achievement_id" id="expected_achievement_id" value="<?php echo $row['expected_achievement_id']; ?>" readonly></td>
                                                            <td><?php echo $row['Ref']; ?></td>
                                                            <td>
                                                                <?php echo $row['maximum']; ?>
                                                                <input type="tel" name="maximumExpectedValue" id="maximumExpectedValue" class="d-none" readonly value="<?php echo $row['maximum']; ?>" style="display:none">
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                <span id="expectedAchievementStatus" class="d-block"></span>
                                                                    <input type="tel" name="expected_achievement_value" id="expected_achievement_value" class="form-control form-control-sm" placeholder="Enter expected achievement" autocomplete="off" autofocus>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <hr>
                                                <div class="col-lg-10 text-right justify-content-center d-flex">
                                                    <button type="submit" class="btn btn-primary mr-2" name="saveExpectedAchievement" id="saveExpectedAchievement">
                                                        <?php if ($nextEai < "EA_20") { ?>
                                                            Next
                                                        <?php }elseif ($nextEai ==="EA_20") { ?>
                                                            Finish
                                                        <?php } ?>
                                                    </button>
                                                    <button type="reset" class="btn btn-secondary" name="resetExpectedAchievement" id="resetExpectedAchievement">Reset</button>
                                                    <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                        <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        </form>
                                        <script>
                                            function expectedAchievementJsValidation()
                                            {
                                                // Set the value of is_input_valid to be true by default
                                                var is_input_valid = true;

                                                document.getElementById('saveExpectedAchievement').className = "d-none";
                                                document.getElementById('resetExpectedAchievement').className = "d-none";
                                                document.getElementById('loadingSpinner').className = "btn btn-primary";
                                                    
                                                if (document.getElementById('expected_achievement_value').value === "")
                                                {
                                                    is_input_valid = false;
                                                    document.getElementById('expected_achievement_value').style.border = "1px solid red";
                                                    document.getElementById('expectedAchievementStatus').style.color = "#dc3545";
                                                    document.getElementById('expectedAchievementStatus').innerHTML = "Please enter your expected achievement";
                                                    // no loader
                                                    document.getElementById('loadingSpinner').className = "d-none";
                                                    document.getElementById('saveExpectedAchievement').className = "btn btn-primary mr-2";
                                                    document.getElementById('resetExpectedAchievement').className = "btn btn-secondary";
                                                }
                                                else if (document.getElementById('expected_achievement_value').value === "0")
                                                {
                                                    is_input_valid = false;
                                                    document.getElementById('expected_achievement_value').style.border = "1px solid red";
                                                    document.getElementById('expectedAchievementStatus').style.color = "#dc3545";
                                                    document.getElementById('expectedAchievementStatus').innerHTML = "Expected achievement cannot be zero";
                                                    // no loader
                                                    document.getElementById('loadingSpinner').className = "d-none";
                                                    document.getElementById('saveExpectedAchievement').className = "btn btn-primary mr-2";
                                                    document.getElementById('resetExpectedAchievement').className = "btn btn-secondary";
                                                }
                                                else if (document.getElementById('expected_achievement_value').value < "1")
                                                {
                                                    is_input_valid = false;
                                                    document.getElementById('expected_achievement_value').style.border = "1px solid red";
                                                    document.getElementById('expectedAchievementStatus').style.color = "#dc3545";
                                                    document.getElementById('expectedAchievementStatus').innerHTML = "Expected achievement cannot be less than one";
                                                    // no loader
                                                    document.getElementById('loadingSpinner').className = "d-none";
                                                    document.getElementById('saveExpectedAchievement').className = "btn btn-primary mr-2";
                                                    document.getElementById('resetExpectedAchievement').className = "btn btn-secondary";
                                                }
                                                // else if (document.getElementById('expected_achievement_value').value > document.getElementById('maximumExpectedValue').value)
                                                // {
                                                //     is_input_valid = false;
                                                //     document.getElementById('expected_achievement_value').style.border = "1px solid red";
                                                //     document.getElementById('expectedAchievementStatus').style.color = "#dc3545";
                                                //     document.getElementById('expectedAchievementStatus').innerHTML = "Maximum expected value is <strong>"+document.getElementById('maximumExpectedValue').value+"</strong>";
                                                //     // no loader
                                                //     document.getElementById('loadingSpinner').className = "d-none";
                                                //     document.getElementById('saveExpectedAchievement').className = "btn btn-primary mr-2";
                                                //     document.getElementById('resetExpectedAchievement').className = "btn btn-secondary";
                                                // }
                                                else
                                                {
                                                    document.getElementById('expected_achievement_value').style.border = "1px solid #198754";
                                                    document.getElementById('expectedAchievementStatus').innerHTML = "";
                                                    // loader executing
                                                    document.getElementById('saveExpectedAchievement').className = "d-none";
                                                    document.getElementById('resetExpectedAchievement').className = "d-none";
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