<?php
include(__DIR__."/../includes/php.php");

## SITE PLACED INTO READ-ONLY MODE

//if (!$loggedIn) {
//	die();
//}
//if (!empty($_POST)) {
//	if ($_POST['opinion'] != -1 && $_POST['opinion'] != 1 && $_POST['opinion'] != 0) {
//		die();
//	}
//	$category = $dbh->real_escape_string($_POST['category']);
//	$nominee = $dbh->real_escape_string($_POST['nominee']);
//	$query = "SELECT `Nominee` FROM `nominees` WHERE `CategoryID` = \"$category\" AND `Nominee` = \"$nominee\"";
//	$result = $dbh->query($query);
//	if ($result->num_rows == 0) {
//		die();
//	}
//
//	$opinion = $dbh->real_escape_string($_POST['opinion']);
//
//	$query = "REPLACE INTO `nominee_feedback` VALUES (\"$category\", \"$nominee\", \"$ID\", $opinion)";
//	$dbh->query($query);
//	action("opinion-given", $category, $nominee);
//	echo "done";
//}
?>
