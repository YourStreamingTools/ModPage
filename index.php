<?php
// Start PHP session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['access_token'])) {
    header('Location: login.php');
    exit;
}

// Include the database connection file
require_once 'db_connect.php';

// Fetch additional user data from the database
$query = "SELECT * FROM users WHERE access_token = '{$_SESSION['access_token']}'";
$result = mysqli_query($conn, $query);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        // Fetch the user data
        $userData = mysqli_fetch_assoc($result);
        $username = $userData['username'];
    } else {
        echo "No user data found";
    }

    // Free the result set
    mysqli_free_result($result);
} else {
    // Query failed
    echo "Error retrieving user data: " . mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>StreamingTools - Dashboard</title>
    <link rel="icon" href="https://cdn.yourstreaming.tools/img/logo.png" sizes="32x32" />
    <link rel="icon" href="https://cdn.yourstreaming.tools/img/logo.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://cdn.yourstreaming.tools/img/logo.png" />
    <meta name="msapplication-TileImage" content="https://cdn.yourstreaming.tools/img/logo.png" />
    <link rel="stylesheet" href="include/bootstrap.css">
    <script src="include/bootstrap.min.js"></script>
    <script src="include/jquery.min.js"></script>
    <link rel="stylesheet" href="include/style.css">
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="">StreamingTools</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="index.php">Dashboard</a></li>
            <li><a href="bans.php">Bans</a></li>
            <li><a href="mods.php">Mods</a></li>
            <li><a href="vips.php">VIPs</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <p class="navbar-text navbar-right">&copy; <?php echo date("Y"); ?> StreamingTools. All rights reserved.</p>
    </div>
</nav>
<?php
$query = "SELECT modded, last_login FROM mods WHERE username = '{$username}'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);
$modded = $userData['modded'];
$last_login = $userData['last_login'];
mysqli_free_result($result);
// Close the database connection
mysqli_close($conn);

echo "<h1>Welcome $username to the mod dashboard!</h1>";
if ($modded !== null) { echo "<p><strong>Modded:</strong> " . date('F j, Y', strtotime($modded)) . "</p>"; } else { echo "<p><strong>Modded:</strong> Unknown</p>"; }
echo "<p><strong>Last Login:</strong> " . date('F j, Y', strtotime($last_login)) . " at " . date('g:i A', strtotime($last_login)) . "</p>";
?>
</body>
</html>