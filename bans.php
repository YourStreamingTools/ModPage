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

$userQuery = "SELECT * FROM users WHERE access_token = '{$_SESSION['access_token']}'";
$result = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_assoc($result);
$username = $userData['username'];
// Free the result set
mysqli_free_result($result);

// Fetch banned users from the database
$query = "SELECT * FROM bans";
$result = mysqli_query($conn, $query);

// Store banned users in an array
$bannedUsers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bannedUsers[] = $row;
}

// Function to search for banned users based on username
function searchBannedUsers($users, $query) {
    $filteredUsers = [];
    foreach ($users as $user) {
        if (stripos($user['userName'], $query) !== false) {
            $filteredUsers[] = $user;
        }
    }
    return $filteredUsers;
}

// Check if a search query is submitted
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $bannedUsers = searchBannedUsers($bannedUsers, $searchQuery);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>StreamingTools - Bans</title>
    <link rel="icon" href="https://cdn.yourstreaming.tools/img/logo.png" sizes="32x32" />
    <link rel="icon" href="https://cdn.yourstreaming.tools/img/logo.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://cdn.yourstreaming.tools/img/logo.png" />
    <meta name="msapplication-TileImage" content="https://cdn.yourstreaming.tools/img/logo.png" />
    <link rel="stylesheet" href="include/style.css">
    <link rel="stylesheet" href="include/bootstrap.css">
    <script src="include/bootstrap.min.js"></script>
    <script src="include/jquery.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="">StreamingTools</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="active"><a href="bans.php">Bans</a></li>
            <li><a href="mods.php">Mods</a></li>
            <li><a href="vips.php">VIPs</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <p class="navbar-text navbar-right">&copy; <?php echo date("Y"); ?> StreamingTools. All rights reserved.</p>
    </div>
</nav>

<h1>Welcome <?php echo $username; ?> to the bans dashboard!</h1>
<div class="row">
    <form action="" method="GET">
        <div class="form-group">
            <input type="text" name="search" class="form-control" placeholder="Search by username" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Search">
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($bannedUsers) > 0): ?>
                    <?php foreach ($bannedUsers as $user): ?>
                        <tr>
                            <td class="username"><?php echo $user['userName']; ?></td>
                            <td><?php echo $user['reason']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No banned users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>