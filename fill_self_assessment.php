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
                    if ($stmt->num_rows===12)
                    {
                        header("Location: self_assessment.php");
                        exit();
                    }
                }
            }
        }

        // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'saveAssessment' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['saveAssessment']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';

            $attribute_id = mysqli_real_escape_string($dbConnection,$_POST['attribute_id']);
            $self_assessment_value = mysqli_real_escape_string($dbConnection,$_POST['self_assessment_value']);
            $acceptedValues = array("0", "1", "2", "3", "4");

            if (empty($attribute_id)==true)
            {
                $_SESSION['empty_attribute_id']='empty_attribute_id';
                header('Location: fill_self_assessment.php');
                exit();
            }
            elseif (in_array($self_assessment_value,$acceptedValues)==false)
            {
                $_SESSION['incorrect_self_assessment_value']='incorrect_self_assessment_value';
                header('Location: fill_self_assessment.php');
                exit();
            }
            elseif ($self_assessment_value<"0")
            {
                $_SESSION['negative_self_assessment_value']='negative_self_assessment_value';
                header('Location: fill_self_assessment.php');
                exit();
            }
            elseif ($self_assessment_value>"4")
            {
                $_SESSION['invalid_self_assessment_value']='invalid_self_assessment_value';
                header('Location: fill_self_assessment.php');
                exit();
            }
            else
            {
                // SQL select statement
                $sql = 'SELECT `attribute_id` FROM `self_assessment_form_data` WHERE `attribute_id`=?';
                // Prepare the SQL statement
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters to the statement
                $stmt->bind_param('s', $attribute_id);
                // Execute the statement
                $stmt->execute();
                // Store the result set
                $stmt->store_result();
                // else if the number of rows is identical to integer 1, redirect to fill_self_assessment.php web page
                if ($stmt->num_rows!==1)
                {
                    $_SESSION['not_self_assessment_value']='not_self_assessment_value';
                    header('Location: fill_self_assessment.php');
                    exit();
                }
                else
                {
                    // SQL select statement
                    $sql = 'SELECT `attribute_id` FROM `self_assessment` WHERE `attribute_id`=? AND `user_Pf_Number`=?';
                    // Prepare the SQL statement with a parameter placeholder
                    $stmt = $dbConnection->prepare($sql);
                    // Bind parameters to the statement
                    $stmt->bind_param('ss', $attribute_id,$_SESSION['user_Pf_Number']);
                    // Execute the statement
                    $stmt->execute();
                    // Store the result set
                    $stmt->store_result();
                    // if number of rows is equal to one redirect to fill_self_assessment.php web page
                    if ($stmt->num_rows==1)
                    {
                        $_SESSION['achievement_data_exists']=$attribute_id;
                        header('Location: fill_self_assessment.php');
                        exit();
                    }
                    else
                    {
                        $datePosted = date("Y-m-d H:i:s",strtotime($currentTime));
                        // SQL select statement
                        $sql = 'INSERT INTO `self_assessment`(`user_Pf_Number`, `attribute_id`, `self_assessment_value`, `datePosted`) VALUES (?,?,?,?)';
                        // Prepare the SQL statement
                        $stmt = $dbConnection->prepare($sql);
                        // Bind parameters to the statement
                        $stmt->bind_param('ssss',$_SESSION['user_Pf_Number'],$attribute_id,$self_assessment_value,$datePosted);
                        // if the statement is executed
                        if ($stmt->execute())
                        {
                            // if $attribute_id is less than string SA_12, redirect to fill_self_assessment.php web page
                            if ($attribute_id<"SA_12")
                            {
                                header('Location: fill_self_assessment.php');
                                exit();
                            }
                            // elseif $attribute_id is identical to string SA_12, redirect to self_assessment.php web page
                            elseif ($attribute_id==="SA_12")
                            {
                                header('Location: self_assessment.php');
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
                <title>epes | Fill self assessment</title>
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
                <?php if (isset($_SESSION['empty_attribute_id'])==true && empty($_SESSION['empty_attribute_id'])==false){ ?>
                    <script>
                            alert('Please try again');
                        </script>
                    <?php unset($_SESSION['empty_attribute_id'])?>
                <?php }elseif (isset($_SESSION['incorrect_self_assessment_value'])==true && empty($_SESSION['incorrect_self_assessment_value'])==false){ ?>
                    <script>
                            alert('Please select your assessment');
                        </script>
                    <?php unset($_SESSION['incorrect_self_assessment_value'])?>
                <?php }elseif (isset($_SESSION['nan_self_assessment_value'])==true && empty($_SESSION['nan_self_assessment_value'])==false){ ?>
                    <script>
                            alert('Self assessment value can only be a number');
                        </script>
                    <?php unset($_SESSION['nan_self_assessment_value'])?>
                <?php }elseif (isset($_SESSION['negative_self_assessment_value'])==true && empty($_SESSION['negative_self_assessment_value'])==false){ ?>
                    <script>
                            alert('Self assessment value cannot be negative');
                        </script>
                    <?php unset($_SESSION['negative_self_assessment_value'])?>
                <?php }elseif (isset($_SESSION['invalid_self_assessment_value'])==true && empty($_SESSION['invalid_self_assessment_value'])==false){ ?>
                    <script>
                            alert('Self assessment value cannot be more than 4');
                        </script>
                    <?php unset($_SESSION['invalid_self_assessment_value'])?>
                <?php }elseif (isset($_SESSION['not_self_assessment_value'])==true && empty($_SESSION['not_self_assessment_value'])==false){ ?>
                    <script>
                            alert('This input is not allowed');
                        </script>
                    <?php unset($_SESSION['not_self_assessment_value'])?>
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
                                        <h1 class="m-0">Fill self assessment</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" class="btn btn-sm btn-primary text-light" onclick="history.back()">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="container w-50 text-center d-none" id="fillSelfAssessmentAlertDiv">
                                <div class="alert alert-warning text-center text-dark" role="alert" id="fillSelfAssessmentAlert"></div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="" method="post" onsubmit="return selfAssessmentJsValidation()" onreset="return resetSelfAssessmentJsValidationForm()">
                                    <!-- <form action="" method="post"> -->
                                        <?php
                                            // SQL select statement
                                            $sql = 'SELECT `attribute_id` FROM `self_assessment` WHERE `user_Pf_Number`=? ORDER BY `attribute_id` ASC';
                                            // Prepare the SQL statement
                                            $stmt = $dbConnection->prepare($sql);
                                            // Bind parameters to the statement
                                            $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                            // Execute the statement
                                            $stmt->execute();
                                            // Retrieve the result set directly from the prepared statement
                                            $result = $stmt->get_result();
                                            // Fetch rows as an associative array
                                            $attribute_id = $result->fetch_assoc();
                                            if (empty($attribute_id['attribute_id'])==true){
                                        ?>
                                            <table class="table table-responsive-md tabe-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="col-2 text-center">Attribute</th>
                                                        <th class="col-2 text-center">Poor (0)</th>
                                                        <th class="col-2 text-center">Fair (1)</th>
                                                        <th class="col-2 text-center">Good (2)</th>
                                                        <th class="col-2 text-center">V. Good (3)</th>
                                                        <th class="col-2 text-center">Excellent (4)</th>
                                                        <th class="col-2 text-center">Appraisee Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        // SQL select statement
                                                        $sql = 'SELECT `attribute_id`, `attribute` FROM `self_assessment_form_data` ORDER BY `attribute_id` ASC';
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
                                                            <input type="radio" name="self_assessment_value" id="poor" value="0">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="self_assessment_value" id="fair" value="1">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="self_assessment_value" id="good" value="2">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="self_assessment_value" id="vGood" value="3">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="self_assessment_value" id="excellent" value="4">
                                                        </td>
                                                        <td class="text-center font-weight-bold">
                                                            <span id="theValue"></span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <hr>
                                            <div class="col-lg-10 text-right justify-content-center d-flex">
                                                <button type="submit" class="btn btn-primary mr-2" name="saveAssessment" id="saveAssessment">Next</button>
                                                <button type="reset" class="btn btn-secondary" name="resetAssessment" id="resetAssessment">Reset</button>

                                                <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                </button>
                                            </div>
                                        <?php }else{ ?>
                                                <?php
                                                    // SQL select statement
                                                    $sql = 'SELECT `attribute_id` FROM `self_assessment` WHERE `user_Pf_Number`=? ORDER BY `attribute_id` DESC';
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
                                                    $latestEai =  $row["attribute_id"];
                                                    // Replace all occurrences of the search string "SA_" with the replacement string ""
                                                    $nextSelfAssessmentId = str_replace("SA_","",$latestEai);
                                                    // Pad a $nextSelfAssessmentId string to a certain length with another string
                                                    $nextSelfAssessmentId = str_pad($nextSelfAssessmentId + 1,2,0, STR_PAD_LEFT);
                                                    // The new value of $nextSelfAssessmentId
                                                    $nextSelfAssessmentId = "SA_" .$nextSelfAssessmentId;
                                                ?>

                                            <table class="table table-responsive-md tabe-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="col-2 text-center">Attribute</th>
                                                        <th class="col-2 text-center">Poor (0)</th>
                                                        <th class="col-2 text-center">Fair (1)</th>
                                                        <th class="col-2 text-center">Good (2)</th>
                                                        <th class="col-2 text-center">V. Good (3)</th>
                                                        <th class="col-2 text-center">Excellent (4)</th>
                                                        <th class="col-2 text-center">Appraisee Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        // SQL select statement
                                                        $sql = 'SELECT `attribute_id`, `attribute` FROM `self_assessment_form_data` WHERE `attribute_id`=?';
                                                        // Prepare the SQL statement
                                                        $stmt = $dbConnection->prepare($sql);
                                                        // Bind parameters to the statement
                                                        $stmt->bind_param('s', $nextSelfAssessmentId);
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
                                                            <input type="radio" name="self_assessment_value" id="poor" value="0">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="self_assessment_value" id="fair" value="1">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="self_assessment_value" id="good" value="2">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="self_assessment_value" id="vGood" value="3">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="self_assessment_value" id="excellent" value="4">
                                                        </td>
                                                        <td class="text-center font-weight-bold">
                                                            <span id="theValue"></span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <hr>
                                            <div class="col-lg-10 text-right justify-content-center d-flex">
                                                <button type="submit" class="btn btn-primary mr-2" name="saveAssessment" id="saveAssessment">
                                                    <?php if ($nextSelfAssessmentId < "SA_12") { ?>
                                                        Next
                                                    <?php }elseif ($nextSelfAssessmentId ==="SA_12") { ?>
                                                        Finish
                                                    <?php } ?>
                                                </button>
                                                <button type="reset" class="btn btn-secondary" name="resetAssessment" id="resetAssessment">Reset</button>

                                                <button type="button" name="loadingSpinner" id="loadingSpinner" class="d-none btn btn-primary">
                                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </form>
                                    <script>
                                        // Get all radio buttons
                                        var radioButtons = document.querySelectorAll('input[name="self_assessment_value"]');

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

                                        function resetSelfAssessmentJsValidationForm()
                                        {
                                            // Set the value of is_reset_valid to be true by default
                                            var is_reset_valid = true;
                                            // Set the value of checkedStatus to be false by default
                                            var checkedStatus = false;
                                            var radioButton = document.getElementsByName('self_assessment_value');
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
                                                document.getElementById('saveAssessment').className = "d-block btn btn-primary mr-2";
                                                document.getElementById('resetAssessment').className = "d-block btn btn-secondary";
                                                document.getElementById('loadingSpinner').className = "d-none";
                                                document.getElementById('theValue').innerHTML = "";
                                            }
                                        }

                                        function selfAssessmentJsValidation()
                                        {
                                            // Set the value of is_input_valid to be true by default
                                            var is_input_valid = true;
                                            // Set the value of checkedStatus to be false by default
                                            var checkedStatus = false;

                                            document.getElementById('saveAssessment').className = "d-none";
                                            document.getElementById('resetAssessment').className = "d-none";
                                            document.getElementById('loadingSpinner').className = "btn btn-primary";
                                            var radioButton = document.getElementsByName('self_assessment_value');

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
                                                document.getElementById('saveAssessment').className = "d-block btn btn-primary mr-2";
                                                document.getElementById('resetAssessment').className = "d-block btn btn-secondary";
                                                document.getElementById('loadingSpinner').className = "d-none";

                                                document.getElementById('fillSelfAssessmentAlertDiv').className = "d-block";
                                                document.getElementById('fillSelfAssessmentAlert').innerHTML = "Please select one provided selections";
                                            }
                                            else
                                            {
                                                document.getElementById('saveAssessment').className = "d-none";
                                                document.getElementById('resetAssessment').className = "d-none";
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