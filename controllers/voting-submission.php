<?php
function error($msg) {
  echo json_encode(array("result" => "error", "error" => $msg));
  exit;
}

error("Voting has closed.");

$cat = mysql_real_escape_string($_POST['Category']);
$preferences = $_POST['Preferences'];
unset($preferences[0]);

// Check for duplicate nominees
if (count($preferences) != count(array_unique($preferences))) {
  error("Duplicate nominees are not allowed.");
}

// Verify that all the nominees actually exist in that category
// This has the side effect of checking that the category exists as well
$query = "SELECT `NomineeID` FROM `nominees` WHERE `CategoryID` = \"$cat\"";
$result = mysql_query($query);

$missing = array_values($preferences);

while ($row = mysql_fetch_assoc($result)) {
  $key = array_search($row['NomineeID'], $missing);
  unset($missing[$key]);
}

if (count($missing) > 0) {
  error("The following nominees don't exist in that category: " . implode(", ", $missing));
}

$preferences = mysql_real_escape_string(json_encode($preferences));

// Will be the steam ID if logged in, IP address if not. Not a great system
$_userID = $loggedIn ? '"'.$ID.'"' : "NULL";
$_code = isset($_SESSION['votingCode']) ? mysql_real_escape_string($_SESSION['votingCode']) : "";
$query =  "REPLACE INTO `votes` (`UniqueID`, `CategoryID`, `Preferences`, `Timestamp`, `UserID`, `IP`, `VotingCode`) ";
$query .= "VALUES (\"$uniqueID\", \"$cat\", \"$preferences\", NOW(), $_userID, \"{$_SERVER['REMOTE_ADDR']}\", \"$_code\")";
$result = mysql_query($query);

if ($result) {
  action("voted", $cat);
} else {
  error("MySQL error: " . mysql_error());
}

echo json_encode(array("result" => "success"));
?>