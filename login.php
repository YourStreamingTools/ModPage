<?php
// Set your Twitch application credentials
$clientID = ''; // CHANGE TO MAKE THIS WORK
$redirectURI = ''; // CHANGE TO MAKE THIS WORK
$clientSecret = ''; // CHANGE TO MAKE THIS WORK
$IDScope = 'openid chat:read moderation:read moderator:read:followers channel:read:vips channel:read:subscriptions moderator:read:chatters bits:read';
$info = "Please wait while we redirect you to Twitch for authorization.";

// Database credentials
require_once "db_connect.php";

// Start PHP session
session_start();

// If the user is not logged in and no authorization code is present, redirect to Twitch authorization page
if (!isset($_SESSION['access_token']) && !isset($_GET['code'])) {
    header('Location: https://id.twitch.tv/oauth2/authorize' .
        '?client_id=' . $clientID .
        '&redirect_uri=' . $redirectURI .
        '&response_type=code' .
        '&scope=' . $IDScope);
    exit;
}

// If an authorization code is present, exchange it for an access token
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Exchange the authorization code for an access token
    $tokenURL = 'https://id.twitch.tv/oauth2/token';
    $postData = array(
        'client_id' => $clientID,
        'client_secret' => $clientSecret,
        'code' => $code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $redirectURI
    );

    $curl = curl_init($tokenURL);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    // Extract the access token from the response
    $responseData = json_decode($response, true);
    $accessToken = $responseData['access_token'];

    // Store the access token in the session
    $_SESSION['access_token'] = $accessToken;

    // Fetch the user's Twitch username
    $userInfoURL = 'https://api.twitch.tv/helix/users';
    $curl = curl_init($userInfoURL);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Client-ID: ' . $clientID
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $userInfoResponse = curl_exec($curl);
    curl_close($curl);

    $userInfo = json_decode($userInfoResponse, true);

    if (isset($userInfo['data']) && count($userInfo['data']) > 0) {
        $twitchUsername = $userInfo['data'][0]['login'];
        $twitchDisplayName = $userInfo['data'][0]['display_name'];
        $profileImageUrl = $userInfo['data'][0]['profile_image_url'];
        $twitchUserId = $userInfo['data'][0]['id'];
        
        // Check if the user is authorized in the 'mods' table
        $checkQuery = "SELECT * FROM mods WHERE username = '$twitchUsername'";
        $result = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($result) > 0) {
            // User is authorized, insert/update the access token in the 'users' table
            $insertQuery = "INSERT INTO users (username, access_token, twitch_display_name, profile_image_url, twitch_user_id) 
                VALUES ('$twitchUsername', '{$_SESSION['access_token']}', '$twitchDisplayName', '$profileImageUrl', '$twitchUserId')
                ON DUPLICATE KEY UPDATE access_token = '{$_SESSION['access_token']}'";
            $insertResult = mysqli_query($conn, $insertQuery);

            if ($insertResult) {
                // Update the last login time
                $last_login = date('Y-m-d H:i:s');
                $sql = "UPDATE mods SET last_login = ? WHERE username = '$twitchUsername'";
                // Prepare and execute the update statement
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 's', $last_login);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            
                // Redirect the user to the index
                header('Location: index.php');
                exit;
            } else {
                // Handle the case where the insertion failed
                echo "Failed to save user information. Error: " . mysqli_error($conn);
                exit;
            }
        } else {
            // User is not authorized, display an error message
            echo "You are not authorized to access this page.";
            // Redirect to the unauthorized page when it's built.
            // header('Location: unauthorized.php');
            exit;
        }
        echo "Welcome " . $twitchUsername . ", we are logging you into the mod index if you are authorized.<br>";
        echo "If you haven't been redirected to the index yet: <a href='index.php'>Click Here</a>";
        exit;
    } else {
        // Failed to fetch user information from Twitch
        echo "Failed to fetch user information from Twitch.";
        exit;
    }
}

// If the user is already logged in, redirect them to the index page
if (isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>StreamingTools - Twitch Login</title>
    <link rel="icon" href="https://cdn.yourstreaming.tools/img/logo.png" sizes="32x32" />
    <link rel="icon" href="https://cdn.yourstreaming.tools/img/logo.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://cdn.yourstreaming.tools/img/logo.png" />
    <meta name="msapplication-TileImage" content="https://cdn.yourstreaming.tools/img/logo.png" />
</head>
<body>
    <?php echo "<p>$info</p>"; ?>
</body>
</html>