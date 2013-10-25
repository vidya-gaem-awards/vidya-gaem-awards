<?php
if ($_POST['opinion'] != -1 && $_POST['opinion'] != 1 && $_POST['opinion'] != 0) {
	die("error: invalid");
}	
$category = mysql_real_escape_string($_POST['ID']);
$query = "SELECT `ID` FROM `categories` WHERE `ID` = \"$category\"";
$result = mysql_query($query);
if (mysql_num_rows($result) == 0) {
	die("error: mysql $query");
}

$opinion = mysql_real_escape_string($_POST['opinion']);

$query = "REPLACE INTO `category_feedback` VALUES (\"$category\", \"$ID\", $opinion)";
mysql_query($query);
action("opinion-given", $category, $opinion);
echo "done";
?>