<?php
include(__DIR__."/../includes/php.php");

## SITE PLACED INTO READ-ONLY MODE
die();

//if (!$loggedIn || empty($_POST) || !isset($_POST['category']) || !isset($_POST['nominee'])) {
//	die();
//}
//
//$category = mysql_real_escape_string($_POST['category']);
//$nominee = mysql_real_escape_string($_POST['nominee']);
//
//if ($nominee == "cancel") {
//	$query = "DELETE FROM `votes` WHERE `UserID` = \"$ID\" AND `CategoryID` = \"$category\"";
//    $dbh->query($query);
//	action("vote-removed", $category);
//	die("done");
//}
//
//$query = "SELECT `Nominee` FROM `nominees` WHERE `CategoryID` = \"$category\" AND `Nominee` = \"$nominee\"";
//$result = $dbh->query($query);
//if ($result->num_rows == 0) {
//	die();
//}
//
//$cookie = $dbh->real_escape_string($_COOKIE['access']);
//
//$query = "REPLACE INTO `votes` (`UserID`, `CategoryID`, `Nominee`, `Timestamp`, `IP`, `Cookie`) VALUES (\"$ID\", \"$category\", \"$nominee\", NOW(), \"{$_SERVER['REMOTE_ADDR']}\", \"$cookie\")";
//$result = $dbh->query($query);
//if (!$result) {
//	die("error");
//}
//
//action("vote-given", $category, $nominee);
//
//echo "done";
?>
