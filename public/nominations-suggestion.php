<?php
include(__DIR__."/../includes/php.php");

## SITE PLACED INTO READ-ONLY MODE
header("Location: https://2011.vidyagaemawards.com/nominations.php");

//if (!empty($_POST) && isset($_GET['category']) && $loggedIn) {
//	$cat = $dbh->real_escape_string($_GET['category']);
//	$query = "SELECT `ID` FROM `categories` WHERE `ID` = \"$cat\"";
//	$result = $dbh->query($query);
//	if ($result->num_rows == 0) {
//		header("Location: https://2011.vidyagaemawards.com/nominations.php");
//	}
//
//	$suggestions = $dbh->real_escape_string($_POST['selfNomination']);
//
//	$query = "INSERT INTO `nominee_suggestions` (`CategoryID`, `UserID`, `Suggestions`, `Timestamp`) VALUES (\"$cat\", \"$ID\", \"$suggestions\", NOW())";
//    $dbh->query($query);
//	action("suggestion-made", $cat);
//	storeMessage("success", "Your nomination has been added.");
//	header("Location: https://2011.vidyagaemawards.com/nominations.php?category=$cat");
//} else {
//	header("Location: https://2011.vidyagaemawards.com/nominations.php");
//}
?>
