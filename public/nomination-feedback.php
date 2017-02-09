<?php
include(__DIR__."/../includes/php.php");
if (!$loggedIn) {
	die();
}
if (!empty($_POST)) {
	if ($_POST['opinion'] != -1 && $_POST['opinion'] != 1 && $_POST['opinion'] != 0) {
		die();
	}	
	$category = mysql_real_escape_string($_POST['category']);
	$nominee = mysql_real_escape_string($_POST['nominee']);
	$query = "SELECT `Nominee` FROM `nominees` WHERE `CategoryID` = \"$category\" AND `Nominee` = \"$nominee\"";
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 0) {
		die();
	}
	
	$opinion = mysql_real_escape_string($_POST['opinion']);
	
	$query = "REPLACE INTO `nominee_feedback` VALUES (\"$category\", \"$nominee\", \"$ID\", $opinion)";
	mysql_query($query);
	action("opinion-given", $category, $nominee);
	echo "done";
}
?>
