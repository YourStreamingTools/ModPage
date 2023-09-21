<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['access_token'])) {
  header("Location: login.php");
  exit();
}

// Database credentials
require_once "db_connect.php";

// Fetch user data from the database
$query = "SELECT * FROM users WHERE access_token = '{$_SESSION['access_token']}'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);
$username = $userData['username'];
$access_token = $userData['access_token'];

// Fetch VIPs data from the database
$vipsQuery = "SELECT * FROM vips";
$vipsResult = mysqli_query($conn, $vipsQuery);
$vipsData = mysqli_fetch_all($vipsResult, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>StreamingTools - VIPs</title>
    <link rel="icon" href="https://cdn.yourstreaming.tools/img/logo.png" sizes="32x32" />
    <link rel="icon" href="https://cdn.yourstreaming.tools/img/logo.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://cdn.yourstreaming.tools/img/logo.png" />
    <meta name="msapplication-TileImage" content="https://cdn.yourstreaming.tools/img/logo.png" />
    <link rel="stylesheet" href="include/bootstrap.css">
    <script src="include/bootstrap.min.js"></script>
    <script src="include/jquery.min.js"></script>
    <link rel="stylesheet" href="include/style.css">
    <style>
        .bot-vip {
            color: red;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="">StreamingTools</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="bans.php">Bans</a></li>
            <li><a href="mods.php">Mods</a></li>
            <li class="active"><a href="vips.php">VIPs</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <p class="navbar-text navbar-right">&copy; <?php echo date("Y"); ?> StreamingTools. All rights reserved.</p>
    </div>
</nav>
<div class="col-md-6">
<h1>Welcome <?php echo $username; ?> to the VIPs page!</h1>
<h2>The VIPs on the channel are:</h2>
<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>VIP Date & Time</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($vipsData as $vip) : ?>
            <tr>
                <td><?php echo $vip['username']; ?></td>
                <td>
                    <?php if ($vip['vip_date'] !== null) : ?>
                        <?php echo date("d F Y \a\\t g:ia", strtotime($vip['vip_date'])); ?>
                    <?php else: ?>
                        Unknown
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<br>
</body>
</html>