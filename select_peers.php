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
            header("Location: login.php?loginError=loginError01&link=select_peers");
            exit();
        }
        else
        {
            // Prepare the SQL statement
            $stmt = $dbConnection->prepare('SELECT `PF_number` FROM `peer_selection_completion` WHERE `PF_number`=?');
            // Bind parameters
            $stmt->bind_param("s", $_SESSION['user_Pf_Number']);
            // Execute the statement
            $stmt->execute();
            // Store result
            $stmt->store_result();
            if ($stmt->num_rows==1)
            {
                header("Location: peers_you_selected.php");
                exit();
            }
        }

         // Check if the form is submitted
        // If the current HTTP request method is POST and if a form element with the name attribute set to 'addAsPeerSubmit' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addAsPeerSubmit']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            // Escape the user input before using it in an SQL query
            $secure_addAsPeerInput = mysqli_real_escape_string($dbConnection,$_POST['addAsPeerInput']);

            if (empty($secure_addAsPeerInput)==false)
            {
                $time_selected = date("Y-m-d H:i:s",strtotime($currentTime));
                $approved = '0';
                $actionDate = '0';

                $addNewPeerSql = 'INSERT INTO `peer_evaluators`(`pf_being_evaluated`, `peer_pf`, `time_selected`, `approved`, `actionDate`) VALUES (?,?,?,?,?)';
                $addNewPeerStmt = $dbConnection->prepare($addNewPeerSql);
                $addNewPeerStmt->bind_param('sssss',$_SESSION['user_Pf_Number'],$secure_addAsPeerInput,$time_selected,$approved,$actionDate);
                if ($addNewPeerStmt->execute())
                {
                    header("Location: select_peers.php?response=peerAdded");
                    exit();
                }
                else
                {
                    header("Location: select_peers.php?response=peerNotAdded");
                    exit();
                }
            }
        }

        // If the current HTTP request method is POST and if a form element with the name attribute set to 'removeAsPeerSubmit' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['removeAsPeerSubmit']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            // Escape the user input before using it in an SQL query
            $secure_removeAsPeerInput = mysqli_real_escape_string($dbConnection,$_POST['removeAsPeerInput']);

            if (empty($secure_removeAsPeerInput)==false)
            {
                $removePeerSql = 'DELETE FROM `peer_evaluators` WHERE `peer_pf`=?';
                $removePeerStmt = $dbConnection->prepare($removePeerSql);
                $removePeerStmt->bind_param('s',$secure_removeAsPeerInput);
                if ($removePeerStmt->execute())
                {
                    header("Location: select_peers.php?response=peerRemoved");
                    exit();
                }
                else
                {
                    header("Location: select_peers.php?response=peerNotRemoved");
                    exit();
                }
            }
        }

        // If the current HTTP request method is POST and if a form element with the name attribute set to 'resetPeers' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['resetPeers']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            $clearPeerSql = 'DELETE FROM `peer_evaluators` WHERE `pf_being_evaluated`=?';
            $clearPeerStmt = $dbConnection->prepare($clearPeerSql);
            $clearPeerStmt->bind_param('s',$_SESSION['user_Pf_Number']);
            if ($clearPeerStmt->execute())
            {
                header("Location: select_peers.php?response=peerCleared");
                exit();
            }
            else
            {
                header("Location: select_peers.php?response=peerNotCleared");
                exit();
            }
        }

        // If the current HTTP request method is POST and if a form element with the name attribute set to 'finishSelectingPeers' is submitted as part of the POST data in an HTTP request
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['finishSelectingPeers']) == true)
        {
            // require connection to the database
            require 'databaseConnection.php';
            // require connection to the database
            require 'databaseCurrentTime.php';
            
            // Prepare the SQL statement
            $stmt = $dbConnection->prepare('SELECT `pf_being_evaluated` FROM `peer_evaluators` WHERE `pf_being_evaluated`=?');
            // Bind parameters
            $stmt->bind_param("s", $_SESSION['user_Pf_Number']);
            // Execute the statement
            $stmt->execute();
            // Store result
            $stmt->store_result();
            if ($stmt->num_rows<2)
            {
                header("Location: select_peers.php?response=minimumOf2");
                exit();
            }
            else
            {
                $date_completed = date("Y-m-d H:i:s",strtotime($currentTime));
                // Prepare the SQL statement
                $sql = 'INSERT INTO `peer_selection_completion`(`PF_number`, `date_completed`) VALUES (?,?)';
                $stmt = $dbConnection->prepare($sql);
                // Bind parameters
                $stmt->bind_param("ss", $_SESSION['user_Pf_Number'],$date_completed);
                // Execute the statement
                if ($stmt->execute())
                {
                    header("Location: peers_you_selected.php?response=success");
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
                <title>epes | Select peer</title>
                <!-- include header.php php file -->
                <?php include 'header.php' ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <?php if (isset($_GET['response'])==true && $_GET['response']=="minimumOf2"){ ?>
                    <script>
                        alert("You have to select a minimum of two peers");
                    </script>
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
                                        <h1 class="m-0">Select peer</h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                            </div>
                        </div>

                        <div class="col-lg-10 mx-auto">
                            <div class="card">
                                    <div class="card-body">
                                        <form action="" method="post">
                                            <table class="table tabe-hover table-bordered" id="peersList">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">PF</th>
                                                        <th>Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $cod = 5;
                                                        $department_id = $_SESSION['department_id'];
                                                        // Prepare a SQL statement with a parameter placeholder
                                                        $selectAllDepartmentMembersStmt = $dbConnection->prepare('SELECT `user_Pf_Number`, `first_name`,`middle_name`,`last_name` FROM `users` WHERE `is_cod`!=? AND `department_id`=? AND `user_Pf_Number`!=? ORDER BY `user_Pf_Number` ASC');
                                                        // Bind parameters to the statement
                                                        $selectAllDepartmentMembersStmt->bind_param('iss',$cod,$department_id,$_SESSION['user_Pf_Number']);
                                                        // Execute the statement
                                                        $selectAllDepartmentMembersStmt->execute();
                                                        // Retrieve the result set
                                                        $selectAllDepartmentMembersResult = $selectAllDepartmentMembersStmt->get_result();
                                                        // Fetch data
                                                        while ($selectAllDepartmentMembersRow = $selectAllDepartmentMembersResult->fetch_assoc()){
                                                    ?>
                                                        <tr>
                                                            <th class="text-center"><?php echo $selectAllDepartmentMembersRow['user_Pf_Number']; ?></th>
                                                            <td class="text-center"><b><?php echo ucwords($selectAllDepartmentMembersRow['first_name'].' '.$selectAllDepartmentMembersRow['middle_name'].' '.$selectAllDepartmentMembersRow['last_name']) ?></b></td>
                                                            <td class="text-center">
                                                                <form action="" method="post">
                                                                    <?php
                                                                        // Prepare the SQL statement
                                                                        $stmt = $dbConnection->prepare('SELECT `peer_pf` FROM `peer_evaluators` WHERE `pf_being_evaluated`=? AND `peer_pf`=?');
                                                                        // Bind parameters
                                                                        $stmt->bind_param("ss",$_SESSION['user_Pf_Number'],$selectAllDepartmentMembersRow['user_Pf_Number']);
                                                                        // Execute the statement
                                                                        $stmt->execute();
                                                                        // Store result
                                                                        $stmt->store_result();
                                                                        if ($stmt->num_rows>0){
                                                                    ?>
                                                                        <input type="text" name="removeAsPeerInput" id="removeAsPeerInput" value="<?php echo $selectAllDepartmentMembersRow['user_Pf_Number']; ?>" class="d-none" readonly>
                                                                        <button type="submit" name="removeAsPeerSubmit" id="removeAsPeerSubmit" class="btn btn-danger btn-sm">
                                                                            Remove as peer
                                                                        </button>
                                                                    <?php }else{ ?>
                                                                        <input type="text" name="addAsPeerInput" id="addAsPeerInput" value="<?php echo $selectAllDepartmentMembersRow['user_Pf_Number']; ?>" class="d-none" readonly>
                                                                        <button type="submit" name="addAsPeerSubmit" id="addAsPeerSubmit" class="btn btn-primary pr-4 pl-4 btn-sm">
                                                                            Add as peer
                                                                        </button>
                                                                    <?php } ?>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <hr>
                                            <div class="col-lg-10 text-right justify-content-center d-flex">
                                                <button class="btn btn-primary mr-2" name="finishSelectingPeers" id="finishSelectingPeers">Finish</button>
                                                <button type="submit" class="btn btn-secondary" name="resetPeers" id="resetPeers">Reset</button>
                                            </div>
                                        </form>
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