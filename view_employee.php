<?php
    // require connection to the database
    require 'databaseConnection.php';

    // Prepare a SQL statement with a parameter placeholder
    $selectAllStaffStmt = $dbConnection->prepare('SELECT * FROM `users` ORDER BY `userId` ASC');

    // Execute the statement
    $selectAllStaffStmt->execute();

    // Retrieve the result set
    $selectAllStaffResult = $selectAllStaffStmt->get_result();

    // Fetch data
    while ($selectAllStaffRow = $selectAllStaffResult->fetch_assoc())
    {
?>
    <div class="container-fluid">
        <div class="card card-widget widget-user shadow">
        <div class="widget-user-header bg-dark">
            <h3 class="widget-user-username"><?php echo ucwords($name) ?></h3>
            <h5 class="widget-user-desc"><?php echo $email ?></h5>
        </div>
        <div class="widget-user-image">
            <?php if(empty($avatar) || (!empty($avatar) && !is_file('assets/uploads/'.$avatar))): ?>
            <span class="brand-image img-circle elevation-2 d-flex justify-content-center align-items-center bg-primary text-white font-weight-500" style="width: 90px;height:90px"><h4><?php echo strtoupper(substr($firstname, 0,1).substr($lastname, 0,1)) ?></h4></span>
            <?php else: ?>
            <img class="img-circle elevation-2" src="assets/uploads/<?php echo $avatar ?>" alt="User Avatar"  style="width: 90px;height:90px;object-fit: cover">
            <?php endif ?>
        </div>
        <div class="card-footer">
            <div class="container-fluid">
                <dl>
                    <dt>Department</dt>
                    <dd><?php echo $department ?></dd>
                </dl>
            <dl>
                <dt>Designation</dt>
                <dd><?php echo $designation ?></dd>
            </dl>
            <dl>
                <dt>Evaluator</dt>
                <dd><?php echo ucwords($evaluator) ?></dd>
            </dl>
            </div>
        </div>
        </div>
    </div>
<?php } ?>