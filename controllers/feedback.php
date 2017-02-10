<?php
$tpl->set("title", "Feedback");

if ($SEGMENTS[1] == "view") {
  if (canDo("feedback-view")) { 
    $CUSTOM_TEMPLATE = "view";
    require("feedback-view.php");
    return;
  } else {
    $PAGE = $loggedIn ? "403" : "401";
    return;
  }
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $values = array_map("mysql_real_escape_string", $_POST);

  $query = "INSERT INTO `feedback` (`UserID`, `UniqueID`, `Timestamp`, `GeneralRating`, `CeremonyRating`, `BestThing`, `WorstThing`, `OtherComments`, `Questions`, `Email`) ";
  $query .= "VALUES (\"$ID\", \"$uniqueID\", NOW(), \"{$values['general']}\", \"{$values['ceremony']}\", \"{$values['best']}\", \"{$values['worst']}\", \"{$values['comments']}\", \"{$values['questions']}\", \"{$values['email']}\")";
  
  $result = $mysql->query($query);
  
  if (!$result) {
		$tpl->set("error", "An error occurred. Your feedback was not saved. You can try refreshing to save it again.".$query);
  } else {
		storeMessage("success", "Your feedback has been successfully submitted.");
		action("feedback-made");
		header("Location: /feedback");
	}
}
?>
