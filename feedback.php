<?php
include("includes/php.php");

$tpl->set("title", "Feedback");

if (isset($_POST["general"])) {
	$general = mysql_real_escape_string($_POST['general']);
	$ceremony = mysql_real_escape_string($_POST['ceremony']);
	$best = mysql_real_escape_string($_POST['best']);
	$worst = mysql_real_escape_string($_POST['worst']);
	$comments = mysql_real_escape_string($_POST['comments']);
	$questions = mysql_real_escape_string($_POST['questions']);
	
	$query = "INSERT INTO `ceremony_feedback` (`UserID`, `GeneralRating`, `CeremonyRating`, `BestThing`, `WorstThing`, `OtherComments`, `Timestamp`, `Questions`)";
	$query .= " VALUES (\"$ID\", \"$general\", \"$ceremony\", \"$best\", \"$worst\", \"$comments\", NOW(), \"$questions\")";

	$result = mysql_query($query);
	if (!$result) {
		$tpl->set("error", "An error occurred. Your feedback was not saved. You can try refreshing to save it again.");
	} else {
		storeMessage("success", "Your feedback has been successfully submitted.");
		action("feedback-made");
		header("Location: http://vidyagaemawards.com/feedback.php");
	}
}

fetch("feedback");
?>