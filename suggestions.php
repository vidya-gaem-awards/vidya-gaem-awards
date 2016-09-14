<?php
include("includes/php.php");

$suggestion = mysql_real_escape_string($_POST['suggestion']);
$result = mysql_query("INSERT INTO `suggestions` (`UserID`, `Suggestion`, `Timestamp`) VALUES (\"$ID\", \"$suggestion\", NOW())");
if ($result) {
	storeMessage("error", "An error occurred. Your suggestion was not saved. Sorry.");
} else {
	storeMessage("success", "Your suggestion has been submitted. If you have anything important to say, I still recommend going on the forums or visiting our Steam group chat though.");
	action("suggestion-made");
}
header("Location: http://vidyagaemawards.com");
?>