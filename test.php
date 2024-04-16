<?php
  session_start();
  include 'databaseConnection.php';
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
   $evaluatedAchievementStmt->get_result();
   echo $evaluatedAchievementStmt->num_rows;
  //  if ($evaluatedAchievementStmt->num_rows==0){}
?>