<?php
// Include your database connection or any required configurations
include 'db_connect.php';
// Fetch event category data from the database
$query = "SELECT category, COUNT(*) AS prominence FROM events GROUP BY category";
$result = mysqli_query($conn, $query);

$eventData = [];
while ($row = mysqli_fetch_assoc($result)) {
  $eventData[] = ['value' => intval($row['prominence']), 'name' => $row['category']];
}

// Return event category data as JSON
echo json_encode($eventData);
?>
