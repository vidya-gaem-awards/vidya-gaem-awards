<?php
include(__DIR__."/../includes/php.php");

## SITE PLACED INTO READ-ONLY MODE
storeMessage("error", "Suggestions are now closed.");

//$suggestion = $dbh->real_escape_string($_POST['suggestion']);
//$result = $dbh->query("INSERT INTO `suggestions` (`UserID`, `Suggestion`, `Timestamp`) VALUES (\"$ID\", \"$suggestion\", NOW())");
//if ($result) {
//	storeMessage("error", "An error occurred. Your suggestion was not saved. Sorry.");
//} else {
//	storeMessage("success", "Your suggestion has been submitted. If you have anything important to say, I still recommend going on the forums or visiting our Steam group chat though.");
//	action("suggestion-made");
//}
header("Location: https://2011.vidyagaemawards.com");
?>
