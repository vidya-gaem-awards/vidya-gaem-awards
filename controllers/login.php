<?php
use VGA\Utils;

// TODO: replace the current library with something less hacked together
// http://stackoverflow.com/questions/18674042/steam-api-authentication/18680478
$result = SteamSignIn::validate();
if (strlen($result) > 0) {
    $_SESSION['login'] = $result;
  
    $info = Utils::getAPIinfo($result);
    $_SESSION['name'] = $info->personaname;
    $_SESSION['avatar'] = $info->avatar;

  // Record the login
    $query = "INSERT INTO `logins` (`UserID`, `Timestamp`) VALUES (?, NOW())";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param('s', $result);
    $stmt->execute();
 
    $avatar = base64_encode(file_get_contents($info->avatar));

    $query = "SELECT `SteamID` FROM `users` WHERE `SteamID` = ?";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param('s', $result);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
      // New user
        $query = "INSERT INTO `users` (`SteamID`, `Name`, `FirstLogin`,
              `LastLogin`, `Avatar`) VALUES(?, ?, NOW(), NOW(), ?)";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param('sss', $result, $info->personaname, $avatar);
    } else {
      // Returning user; update their name and avatar
        $query = "UPDATE `users` SET `Name` = ?, `Avatar` = ?, `LastLogin` = NOW()
              WHERE `SteamID` = ?";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param('sss', $info->personaname, $avatar, $result);
    }
    $stmt->execute();
  
  // Thanks to http://stackoverflow.com/questions/5009685/#5009903
    $randomToken = hash('sha256', uniqid(mt_rand(), true).uniqid(mt_rand(), true));
    $randomToken .= ':'.hash_hmac('md5', $randomToken, STEAM_API_KEY);
    setcookie("token", $randomToken, time()+60*60*24*30, "/", DOMAIN);
  
    $query = "REPLACE INTO `login_tokens` (`UserID`, `Name`, `Avatar`, `Token`, 
            `Generated`, `Expires`) VALUES(?, ?, ?, ?, NOW(),
            DATE_ADD(NOW(), INTERVAL 30 DAY))";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param(
        'ssss',
        $result,
        $info->personaname,
        $info->avatar,
        $randomToken
    );
    $stmt->execute();
}

// Send them back where they came from
$return = rtrim(implode("/", array_slice($SEGMENTS, 1)), "/");
header("Location: https://" . DOMAIN . "/$return");
