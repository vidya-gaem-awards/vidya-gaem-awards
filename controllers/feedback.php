<?php
$tpl->set("title", "Feedback");

// Handle access permissions for viewing the feedback
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

// Handle the feedback submission
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $query = "INSERT INTO `feedback` (`UserID`, `UniqueID`, `Timestamp`, "
    . "`GeneralRating`, `CeremonyRating`, `BestThing`, `WorstThing`, "
    . "`OtherComments`, `Questions`, `Email`) "
    . "VALUES(?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $mysql->prepare($query);
  $stmt->bind_param("ssiisssss", $ID, $uniqueID, $_POST['general'],
    $_POST['ceremony'], $_POST['best'], $_POST['worst'], $_POST['comments'],
    $_POST['questions'], $_POST['email']);
  $result = $stmt->execute();

  // Check if it succeeded
  if (!$result) {
    error_log("MySQL error: ".$stmt->error);
    $tpl->set("error", "An error occurred. Your feedback was not saved. "
      . "You can try refreshing to save it again. ");
  } else {
    storeMessage("success", "Your feedback has been successfully submitted.");
    action("feedback-made");
    header("Location: /feedback");
  }
}