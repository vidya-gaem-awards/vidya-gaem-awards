<?php
include("includes/php.php");
if (!$loggedIn || empty($_POST) || !isset($_POST['category']) || !isset($_POST['nominee'])) {
	die();
}

$category = mysql_real_escape_string($_POST['category']);
$nominee = mysql_real_escape_string($_POST['nominee']);

if ($nominee == "cancel") {
	$query = "DELETE FROM `votes` WHERE `UserID` = \"$ID\" AND `CategoryID` = \"$category\"";
	mysql_query($query);
	action("vote-removed", $category);
	die("done");
}

$query = "SELECT `Nominee` FROM `nominees` WHERE `CategoryID` = \"$category\" AND `Nominee` = \"$nominee\"";
$result = mysql_query($query);
if (mysql_num_rows($result) == 0) {
	die();
}

$cookie = mysql_real_escape_string($_COOKIE['access']);

$query = "REPLACE INTO `votes` (`UserID`, `CategoryID`, `Nominee`, `Timestamp`, `IP`, `Cookie`) VALUES (\"$ID\", \"$category\", \"$nominee\", NOW(), \"{$_SERVER['REMOTE_ADDR']}\", \"$cookie\")";
$result = mysql_query($query);
if (!$result) {
	die("error");
}

action("vote-given", $category, $nominee);

echo "done";
?>