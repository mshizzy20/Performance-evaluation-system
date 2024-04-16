<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Logout</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<script type="text/javascript">
    var inactivityTime = 3 * 60 * 1000; // 3 minutes in milliseconds

    var logoutTimer;

    function resetTimer() {
        clearTimeout(logoutTimer);
        logoutTimer = setTimeout(logout, inactivityTime);
    }

    function logout() {
        // Redirect to logout script or perform logout actions
        window.location.href = 'logout.php';
    }

    $(document).ready(function () {
        resetTimer();

        $(document).mousemove(function () {
            resetTimer();
        });

        $(document).keypress(function () {
            resetTimer();
        });
    });
</script>

</body>
</html>
