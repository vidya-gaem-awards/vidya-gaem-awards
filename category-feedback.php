<?php
include("includes/php.php");
if (!$loggedIn) {
	die();
}
if (!empty($_POST)) {
	if ($_POST['opinion'] != -1 && $_POST['opinion'] != 1 && $_POST['opinion'] != 0) {
		die();
	}	
	$category = mysql_real_escape_string($_POST['ID']);
	$query = "SELECT `ID` FROM `categories` WHERE `ID` = \"$category\"";
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 0) {
		die();
	}
	
	$opinion = mysql_real_escape_string($_POST['opinion']);
	
	$query = "REPLACE INTO `category_feedback` VALUES (\"$category\", \"$ID\", $opinion)";
	mysql_query($query);
	action("opinion-given", $category, $opinion);
	echo "done";
}
?>