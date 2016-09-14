<?php
include("includes/php.php");

ini_set("display_errors", true);
error_reporting(E_ALL);

$result = SteamSignIn::validate();
if (strlen($result) > 0) {
    $_SESSION['login'] = $result;
    
    $info = getAPIinfo($result);
    $_SESSION['name'] = $info->personaname;
    $_SESSION['avatar'] = $info->avatar;

    mysql_query("INSERT INTO `logins` VALUES (0, \"$result\", NOW())");
    
    $name = mysql_real_escape_string($info->personaname);
    $mysql = mysql_query("SELECT `SteamID` FROM `users` WHERE `SteamID` = \"$result\"");
    if (mysql_num_rows($mysql) == 0) {
		//echo "New user";
		$query = "INSERT INTO `users` (`SteamID`, `Name`, `LastLogin`) VALUES (\"$result\", \"$name\", NOW())";
	} else {
		//echo "Returning user";
		$query = "UPDATE `users` SET `Name` = \"$name\", `LastLogin` = NOW() WHERE `SteamID` = \"$result\"";
	}
	$result = mysql_query($query);
	if (!$result) {
		echo "Sorry, there was an error in processing your login.<br />".mysql_error();
		die();
	}
}

header("Location: http://2011.vidyagaemawards.com/{$_GET['return']}");
?>