<?php
$current = time();
$start = strtotime($VOTING_START);
$finish = strtotime($VOTING_END);

if ($current < $start) {
  return_json("error", "Voting hasn't opened yet.");
} else if ($current > $finish) {
  return_json("error", "Voting has closed.");
}

$preferences = array_values(array_filter($_POST['Preferences']));
array_unshift($preferences, "");
unset($preferences[0]);

// Check for duplicate nominees
if (count($preferences) != count(array_unique($preferences))) {
  return_json("error", "Duplicate nominees are not allowed.");
}

// Verify that all the nominees actually exist in that category
// This has the side effect of checking that the category exists as well
$query = "SELECT `NomineeID` FROM `nominees` WHERE `CategoryID` = ?";
$stmt = $mysql->prepare($query);
$stmt->bind_param('s', $_POST['Category']);
$stmt->execute();
$stmt->bind_result($nominee);
$missing = array_values($preferences);

while ($stmt->fetch()) {
  $key = array_search($nominee, $missing);
  unset($missing[$key]);
}

if (count($missing) > 0) {
  return_json("error", "The following nominees don't exist in that category: " . implode(", ", $missing));
}

$preferences = json_encode($preferences);

// Will be the steam ID if logged in, IP address if not. Not a great system
$_userID = $loggedIn ? $ID : NULL;
$_code = isset($_SESSION['votingCode']) ? $_SESSION['votingCode'] : "";
$query = "REPLACE INTO `votes` (`UniqueID`, `CategoryID`, `Preferences`,
          `Timestamp`, `UserID`, `IP`, `VotingCode`)
          VALUES(?, ?, ?, NOW(), ?, ?, ?)";
$stmt = $mysql->prepare($query);
$stmt->bind_param('ssssss', $uniqueID, $_POST['Category'], $preferences,
  $_userID, $IP, $_code);
$result = $stmt->execute();
if ($result) {
  action("voted", $_POST['Category']);
} else {
  return_json("error", "MySQL error: ".$stmt->error);
}

return_json("success");