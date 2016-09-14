<?php
include("includes/php.php");
if (!empty($_POST) && isset($_GET['category']) && $loggedIn) {
	$cat = mysql_real_escape_string($_GET['category']);
	$query = "SELECT `ID` FROM `categories` WHERE `ID` = \"$cat\"";
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 0) {
		header("Location: https://vidyagaemawards.com/nominations.php");
	}
	
	$suggestions = mysql_real_escape_string($_POST['selfNomination']);
	
	$query = "INSERT INTO `nominee_suggestions` (`CategoryID`, `UserID`, `Suggestions`, `Timestamp`) VALUES (\"$cat\", \"$ID\", \"$suggestions\", NOW())";
	mysql_query($query);
	action("suggestion-made", $cat);	
	storeMessage("success", "Your nomination has been added.");
	header("Location: https://vidyagaemawards.com/nominations.php?category=$cat");
} else {
	header("Location: https://vidyagaemawards.com/nominations.php");
}
?>
