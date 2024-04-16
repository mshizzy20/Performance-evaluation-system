<?php
// Start the session
session_start();

// Check if last activity time is set
if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 180)) {
    // If user has been inactive for more than 3 minutes, destroy the session and log them out
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automatic Logout</title>
    <script>
        //3 * 60 * 1000; // 3 minutes in milliseconds
        // Function to redirect to logout page after 3 minutes of inactivity
        function checkInactive() {
            setTimeout(function() {window.location.href = "logout.php";}, 180000); // 3 minutes in milliseconds
        }
        
        // Event listener for mouse movement
        document.addEventListener("mousemove", resetTimer);
        // Event listener for key press
        document.addEventListener("keypress", resetTimer);

        // Function to reset the timer when there's activity
        function resetTimer() {
            clearTimeout(checkInactive);
            checkInactive();
        }

        // Initialize the timer
        checkInactive();
    </script>
</head>
<body>
    <h1>Automatic Logout</h1>
    <!-- Your HTML content here -->
</body>
</html>
