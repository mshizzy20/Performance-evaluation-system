<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
        <span class="brand-link">
            <?php if ($_SESSION['user_role'] === 5){ ?>
                <h6 class="text-center p-0 m-0" title="Administrator" style="cursor:pointer;">
                    <b>ADMIN</b>
                    <span style="font-size:16px;" class="font-weight-normal d-block"><span id="time"></span></span>
                </h6>
            <?php }elseif ($_SESSION['user_role'] === 6){ ?>
                <h6 class="text-center p-0 m-0" title="Chair of department" style="cursor:pointer;">
                    <b>COD</b>
                    <span style="font-size:16px;" class="font-weight-normal d-block"><span id="time"></span></span>
                </h6>
            <?php }elseif ($_SESSION['user_role'] === 7){ ?>
                <h6 class="text-center p-0 m-0" title="Lecturer" style="cursor:pointer;">
                    <b>Lecturer</b>
                    <span style="font-size:16px;" class="font-weight-normal d-block"><span id="time"></span></span>
                </h6>
            <?php } ?>
            <script language="javascript" type="text/javascript">
                var timerID = null;
                var timerRunning = false;
                function stopclock ()
                {
                    if(timerRunning)
                    clearTimeout(timerID);
                    timerRunning = false;
                }
                function showtime ()
                {
                    var now = new Date();
                    var hours = now.getHours();
                    var minutes = now.getMinutes();
                    var seconds = now.getSeconds();

                    hours=((hours < 10) ? "0" : "") + hours;
                    minutes=((minutes < 10) ? ":0" : ":") + minutes;
                    seconds=((seconds < 10) ? ":0" : ":") + seconds;
                    amPM=((hours >= 12) ? " PM" : " AM");
                    // var currentTime = hours+minutes+seconds+amPM;
                    var currentTime = hours+minutes+seconds;

                    document.getElementById('time').innerHTML = currentTime;
                    timerID = setTimeout("showtime()",1000);
                    timerRunning = true;
                }
                function startclock()
                {
                    stopclock();
                    showtime();
                }
                window.onload=startclock;
            </script>
        </span>
    </div>
    <div class="sidebar pb-4 mb-4">
        <nav class="mt-4">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="home" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <?php if ($_SESSION['user_role'] === 5){ ?>
                    <li class="nav-item" style="cursor:pointer;">
                        <span class="nav-link">
                            <i class="nav-icon fa fa-th-list"></i>
                            <p>
                                Departments
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </span>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="new_department" class="nav-link tree-item">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Add department</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="department_list" class="nav-link tree-item">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Department List</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" style="cursor:pointer;">
                        <span class="nav-link">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>
                                Employees
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </span>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="new_employee" class="nav-link tree-item">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Add employee</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="cod_list" class="nav-link tree-item">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>COD list</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="lecturers_list" class="nav-link tree-item">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Lecturers list</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item" style="cursor:pointer;">
                        <span class="nav-link nav-edit_employee">
                            <i class="nav-icon fas fa-school"></i>
                            <p>
                                Schools
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </span>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="new_school" class="nav-link tree-item">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Add school</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="school_list" class="nav-link tree-item">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>School list</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item" style="cursor:pointer;">
                        <span class="nav-link nav-edit_employee">
                            <i class="nav-icon fas fa-gears"></i>
                            <p>
                                Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </span>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="inspect_database" class="nav-link tree-item">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Inspect database</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php }elseif ($_SESSION['user_role'] === 6){ ?>
                    <li class="nav-item">
                        <a href="department_members" class="nav-link nav-home">
                            <i class="nav-icon fa fa-users"></i>
                            <p>Members</p>
                        </a>
                    </li>
                    <li class="nav-item" style="cursor:pointer;">
                        <span class="nav-link nav-edit_employee">
                            <i class="nav-icon fa-regular fa-folder-open"></i>
                            <p>
                                Evaluation
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </span>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="evaluateDpt" class="nav-link tree-item">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Evaluate</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php }elseif ($_SESSION['user_role'] === 7){ ?>
                    <li class="nav-item" style="cursor:pointer;">
                        <span class="nav-link nav-edit_employee">
                            <i class="nav-icon fa-regular fa-folder-open"></i>
                            <p>
                                Evaluation
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </span>
                        <?php
                            // Prepare the SQL statement
                            $stmt = $dbConnection->prepare('SELECT `PF_number` FROM `bio_data` WHERE `PF_number`=?');
                            // Bind parameters
                            $stmt->bind_param("s", $_SESSION['user_Pf_Number']);
                            // Execute the statement
                            $stmt->execute();
                            // Store result
                            $stmt->store_result();
                            if ($stmt->num_rows==1){ ?>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="bio_data" class="nav-link tree-item">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>Add bio data<i class="ml-1 text-success fa-solid fa-circle-check"></i></p>
                                        </a>
                                    </li>
                                </ul>
                                <?php
                                    // Prepare the SQL statement
                                    $stmt = $dbConnection->prepare('SELECT `PF_number` FROM `peer_selection_completion` WHERE `PF_number`=?');
                                    // Bind parameters
                                    $stmt->bind_param("s", $_SESSION['user_Pf_Number']);
                                    // Execute the statement
                                    $stmt->execute();
                                    // Store result
                                    $stmt->store_result();
                                    if ($stmt->num_rows==1){ ?>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="peers_you_selected" class="nav-link tree-item">
                                                    <i class="fas fa-angle-right nav-icon"></i>
                                                    <p>Select peer<i class="ml-1 text-success fa-solid fa-circle-check"></i></p>
                                                </a>
                                            </li>
                                        </ul>
                                        <?php
                                            // SQL select statement
                                            $sql = 'SELECT `user_Pf_Number` FROM `expected_achievement` WHERE `user_Pf_Number`=?';
                                            // Prepare the SQL statement with a parameter placeholder
                                            $stmt = $dbConnection->prepare($sql);
                                            // Bind parameters to the statement
                                            $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                            // Execute the statement
                                            $stmt->execute();
                                            // Retrieve the result set
                                            $stmt->store_result();
                                            if ($stmt->num_rows>0 && $stmt->num_rows<20){?>
                                                <ul class="nav nav-treeview">
                                                    <li class="nav-item">
                                                        <a href="expected_achievement" class="nav-link tree-item">
                                                            <i class="fas fa-angle-right nav-icon"></i>
                                                            <p>Expected achievement<i class="ml-1 text-orange fa-solid fa-circle-check"></i></p>
                                                        </a>
                                                    </li>
                                                </ul>
                                            <?php }elseif ($stmt->num_rows==20){ ?>
                                                <ul class="nav nav-treeview">
                                                    <li class="nav-item">
                                                        <a href="expected_achievement" class="nav-link tree-item">
                                                            <i class="fas fa-angle-right nav-icon"></i>
                                                            <p>Expected achievement<i class="ml-1 text-success fa-solid fa-circle-check"></i></p>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <?php
                                                    // SQL select statement
                                                    $sql = 'SELECT `user_Pf_Number` FROM `self_achievement` WHERE `user_Pf_Number`=?';
                                                    // Prepare the SQL statement with a parameter placeholder
                                                    $stmt = $dbConnection->prepare($sql);
                                                    // Bind parameters to the statement
                                                    $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                                    // Execute the statement
                                                    $stmt->execute();
                                                    // Retrieve the result set
                                                    $stmt->store_result();
                                                    if ($stmt->num_rows>0 && $stmt->num_rows<20){?>
                                                        <ul class="nav nav-treeview">
                                                            <li class="nav-item">
                                                                <a href="achievement" class="nav-link tree-item">
                                                                    <i class="fas fa-angle-right nav-icon"></i>
                                                                    <p>Achievement<i class="ml-1 text-orange fa-solid fa-circle-check"></i></p>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    <?php }elseif ($stmt->num_rows==20){?>
                                                        <ul class="nav nav-treeview">
                                                            <li class="nav-item">
                                                                <a href="achievement" class="nav-link tree-item">
                                                                    <i class="fas fa-angle-right nav-icon"></i>
                                                                    <p>Achievement<i class="ml-1 text-success fa-solid fa-circle-check"></i></p>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                        <?php
                                                            // SQL select statement
                                                            $sql = 'SELECT `user_Pf_Number` FROM `self_assessment` WHERE `user_Pf_Number`=?';
                                                            // Prepare the SQL statement with a parameter placeholder
                                                            $stmt = $dbConnection->prepare($sql);
                                                            // Bind parameters to the statement
                                                            $stmt->bind_param('s', $_SESSION['user_Pf_Number']);
                                                            // Execute the statement
                                                            $stmt->execute();
                                                            // Retrieve the result set
                                                            $stmt->store_result();
                                                            if ($stmt->num_rows>0 && $stmt->num_rows<12){?>
                                                                <ul class="nav nav-treeview">
                                                                    <li class="nav-item">
                                                                        <a href="self_assessment" class="nav-link tree-item">
                                                                            <i class="fas fa-angle-right nav-icon"></i>
                                                                            <p>Self assessment<i class="ml-1 text-orange fa-solid fa-circle-check"></i></p>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            <?php }elseif ($stmt->num_rows==12){ ?>
                                                                    <ul class="nav nav-treeview">
                                                                        <li class="nav-item">
                                                                            <a href="self_assessment" class="nav-link tree-item">
                                                                                <i class="fas fa-angle-right nav-icon"></i>
                                                                                <p>Self assessment<i class="ml-1 text-success fa-solid fa-circle-check"></i></p>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                    <ul class="nav nav-treeview">
                                                                        <li class="nav-item">
                                                                            <a href="peer_evaluation" class="nav-link tree-item">
                                                                                <i class="fas fa-angle-right nav-icon"></i>
                                                                                <p>Peer evaluation</p>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                            <?php }else{ ?>
                                                                <ul class="nav nav-treeview">
                                                                    <li class="nav-item">
                                                                        <a href="self_assessment" class="nav-link tree-item">
                                                                            <i class="fas fa-angle-right nav-icon"></i>
                                                                            <p>Self assessment</p>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            <?php } ?>
                                                    <?php }else{ ?>
                                                        <ul class="nav nav-treeview">
                                                            <li class="nav-item">
                                                                <a href="achievement" class="nav-link tree-item">
                                                                    <i class="fas fa-angle-right nav-icon"></i>
                                                                    <p>Achievement</p>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    <?php } ?>
                                            <?php }else{ ?>
                                                <ul class="nav nav-treeview">
                                                    <li class="nav-item">
                                                        <a href="expected_achievement" class="nav-link tree-item">
                                                            <i class="fas fa-angle-right nav-icon"></i>
                                                            <p>Expected achievement</p>
                                                        </a>
                                                    </li>
                                                </ul>
                                            <?php } ?>
                                <?php }else{ ?>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="select_peers" class="nav-link tree-item">
                                                <i class="fas fa-angle-right nav-icon"></i>
                                                <p>Select peer</p>
                                            </a>
                                        </li>
                                    </ul>
                                <?php } ?>
                        <?php }else{ ?>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="bio_data" class="nav-link tree-item">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>Add bio data</p>
                                    </a>
                                </li>
                            </ul>
                        <?php } ?>
                    </li>
                    <?php
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
                        if ($evaluatedAchievementStmt->num_rows==0){
                    ?>
                        <li class="nav-item" style="cursor:pointer;">
                            <span class="nav-link nav-edit_employee">
                                <i class="nav-icon fa-solid fa-elevator"></i>
                                <p>
                                    View evaluation
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </span>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="view_cmplt_bio_data" class="nav-link tree-item">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>Bio data
                                            <!-- <i class="ml-2 text-success fa-solid fa-database"></i> -->
                                        </p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="view_cmplt_peers_and_peerEvaluation" class="nav-link tree-item">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>Peer &amp; peer evaluation
                                            <!-- <i class="ml-2 text-success fa-solid fa-users"></i> -->
                                        </p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="view_cmplt_targets_and_achievements" class="nav-link tree-item">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>Targets and achievements
                                            <!-- <i class="ml-1 text-orange fa-solid fa-crosshairs"></i> -->
                                        </p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="view_cmplt_self_assessment" class="nav-link tree-item">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>Self assessment
                                            <!-- <i class="ml-1 text-orange fa-solid fa-circle-check"></i> -->
                                        </p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="view_cmplt_report" class="nav-link tree-item">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>Final report</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </nav>
    </div>
</aside>