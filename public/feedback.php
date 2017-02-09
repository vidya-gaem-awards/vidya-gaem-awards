<?php
include(__DIR__."/../includes/php.php");

$tpl->set("title", "Feedback");

## SITE PLACED INTO READ-ONLY MODE
$tpl->set("error", "Feedback is now closed.");

//if (isset($_POST["general"])) {
//	$general = $dbh->real_escape_string($_POST['general']);
//	$ceremony = $dbh->real_escape_string($_POST['ceremony']);
//	$best = $dbh->real_escape_string($_POST['best']);
//	$worst = $dbh->real_escape_string($_POST['worst']);
//	$comments = $dbh->real_escape_string($_POST['comments']);
//	$questions = $dbh->real_escape_string($_POST['questions']);
//
//	$query = "INSERT INTO `ceremony_feedback` (`UserID`, `GeneralRating`, `CeremonyRating`, `BestThing`, `WorstThing`, `OtherComments`, `Timestamp`, `Questions`)";
//	$query .= " VALUES (\"$ID\", \"$general\", \"$ceremony\", \"$best\", \"$worst\", \"$comments\", NOW(), \"$questions\")";
//
//	$result = $dbh->query($query);
//	if (!$result) {
//		$tpl->set("error", "An error occurred. Your feedback was not saved. You can try refreshing to save it again.");
//	} else {
//		storeMessage("success", "Your feedback has been successfully submitted.");
//		action("feedback-made");
//		header("Location: https://2011.vidyagaemawards.com/feedback.php");
//	}
//}

fetch("feedback");
?>
