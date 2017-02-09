<?php
include(__DIR__."/../includes/php.php");

## SITE PLACED INTO READ-ONLY MODE
die();

//if (!$loggedIn) {
//	die();
//}
//if (!empty($_POST)) {
//	if ($_POST['opinion'] != -1 && $_POST['opinion'] != 1 && $_POST['opinion'] != 0) {
//		die();
//	}
//	$category = $dbh->real_escape_string($_POST['ID']);
//	$query = "SELECT `ID` FROM `categories` WHERE `ID` = \"$category\"";
//	$result = $dbh->query($query);
//	if ($result->num_rows == 0) {
//		die();
//	}
//
//	$opinion = $dbh->real_escape_string($_POST['opinion']);
//
//	$query = "REPLACE INTO `category_feedback` VALUES (\"$category\", \"$ID\", $opinion)";
//	$dbh->query($query);
//	action("opinion-given", $category, $opinion);
//	echo "done";
//}
?>
