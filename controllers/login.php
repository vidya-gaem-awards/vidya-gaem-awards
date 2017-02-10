<?php

$result = SteamSignIn::validate();
if (strlen($result) > 0) {
  $_SESSION['login'] = $result;
  
  $info = getAPIinfo($result);
  $_SESSION['name'] = $info->personaname;
  $_SESSION['avatar'] = $info->avatar;

  $res = $mysql->real_escape_string($result);

  $mysql->query("INSERT INTO `logins` (`ID`, `UserID`, `Timestamp`) VALUES (0, \"$res\", NOW())");
  
  $name = $mysql->real_escape_string($info->personaname);
  $avatar = $mysql->real_escape_string(base64_encode(file_get_contents($info->avatar)));
  $mysqlResult = $mysql->query("SELECT `SteamID` FROM `users` WHERE `SteamID` = \"$res\"");
  if ($mysqlResult->num_rows == 0) {
    //echo "New user";
    $query = "INSERT INTO `users` (`SteamID`, `Name`, `FirstLogin`, `LastLogin`, `Avatar`) VALUES (\"$res\", \"$name\", NOW(), NOW(), \"$avatar\")";
  } else {
    //echo "Returning user";
    $query = "UPDATE `users` SET `Name` = \"$name\", `Avatar` = \"$avatar\", `LastLogin` = NOW() WHERE `SteamID` = \"$res\"";
  }
  $result = $mysql->query($query);
  if (!$result) {
    echo "Sorry, there was an error in processing your login.<br />".$mysql->error;
    die();
  }
  
  // Thanks to http://stackoverflow.com/questions/5009685/encoding-cookies-so-they-cannot-be-spoofed-or-read-etc/5009903#5009903
  $randomToken = hash('sha256',uniqid(mt_rand(), true).uniqid(mt_rand(), true));
  $randomToken .= ':'.hash_hmac('md5', $randomToken, $APIkey);
  setcookie("token", $randomToken, time()+60*60*24*30, "/", $domain);
  
  $avatar = $mysql->real_escape_string($info->avatar);
  $query =  "REPLACE INTO `login_tokens` (`UserID`, `Name`, `Avatar`, `Token`, `Generated`, `Expires`) ";
  $query .= "VALUES(\"{$_SESSION['login']}\", \"$name\", \"$avatar\", \"$randomToken\", NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))";
  $mysql->query($query);
  
}

$return = rtrim(implode("/", array_slice($SEGMENTS, 1)), "/");

header("Location: https://$domain/$return");
?>
