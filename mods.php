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

// Fetch mods data from the database
$modsQuery = "SELECT * FROM mods";
$modsResult = mysqli_query($conn, $modsQuery);
$modsData = mysqli_fetch_all($modsResult, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>gfaUnDead - Mods</title>
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
          <a class="navbar-brand" href="">gfaUnDead</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="bans.php">Bans</a></li>
            <li class="active"><a href="mods.php">Mods</a></li>
            <li><a href="vips.php">VIPs</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <p class="navbar-text navbar-right">&copy; <?php echo date("Y"); ?> gfaUnDead. All rights reserved.</p>
    </div>
</nav>
<h1>Welcome <?php echo $username; ?> to the mods page!</h1>
<h2>The mods on the channel are:</h2>
<h3>
<ul>
  <?php foreach ($modsData as $mod) : ?>
    <li<?php if ($mod['bot'] == 1) : ?> class="bot-mod"<?php endif; ?>>
      <?php echo $mod['username']; ?><?php if ($mod['bot'] == 1) : ?> (Bot) <?php endif; ?>
      <?php if ($mod['bot'] == 0) : ?>
        <?php if ($mod['modded'] !== null) : ?>
          (Modded Date: <?php echo date("d F Y", strtotime($mod['modded'])); ?>)
        <?php else: ?>
          (Modded Date: Unknown)
        <?php endif; ?>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>
</h3>
</body>
</html>