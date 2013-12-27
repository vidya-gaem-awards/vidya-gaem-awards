<?php
$action = $_POST['Action'];
$category = $_POST['Category'];
$nominee = $_POST['NomineeID'];

// Sanity checking
if ($action != "edit" && $action != "delete" && $action != "new") {
  return_json("error", "Invalid action specified.");
} else if (trim($nominee) == "") {
  return_json("error", "A nominee ID is required.");
}

// Check if the category exists and is enabled
if ($action == "new") {
  $query = "SELECT `Enabled` FROM `categories` WHERE `ID` = ?";
  $stmt = $mysql->prepare($query);
  $stmt->bind_param("s", $category);
  $stmt->execute();
  $stmt->bind_result($enabled);
  $stmt->store_result();
  if ($stmt->num_rows === 0) {
    return_json("error", "The specified category doesn't exist.");
  }
  $stmt->fetch();
  if ($enabled !== 1) {
    return_json("error", "The specified category is not enabled.");
  }
}

// Check if the nominee exists
$query = "SELECT `NomineeID` FROM `nominees` " .
  "WHERE `CategoryID` = ? AND `NomineeID` = ?";
$stmt = $mysql->prepare($query);
$stmt->bind_param("ss", $category, $nominee);
$stmt->execute();
$stmt->store_result();

$nomineeExists = $stmt->num_rows === 1;
  
if ($action != "new" && !$nomineeExists) {
  return_json("error", "Couldn't find that nominee in that category.");
} else if ($action == "new" && $nomineeExists) {
  return_json("error",
    "A nominee with that ID already exists in this category.");
}

// Deleting a nominee
if ($action == "delete") {
  $query = "DELETE FROM `nominees` WHERE `CategoryID` = ? AND `NomineeID` = ?";
  $stmt = $mysql->prepare($query);
  $stmt->bind_param("ss", $category, $nominee);
  $stmt->execute();

  action("nominee-delete", $category, $nominee);

  return_json("success");
}

// Adding or editing a nominee
if (trim($_POST['Name']) == "") {
  return_json("error", "The nominee must have a name.");
} else if (preg_match('/[^a-z0-9-]/', $_POST['NomineeID'])) {
  return_json("error", "The nominee ID should consist of lowercase letters, ".
    "numbers and dashes only.");
}

// Perform the insertion/replacement
$query = "REPLACE INTO `nominees` (`CategoryID`, `NomineeID`, `Name`, ".
  "`Subtitle`, `Image`, `FlavorText`) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $mysql->prepare($query);
$stmt->bind_param("ssssss", $category, $nominee, $_POST['Name'],
  $_POST['Subtitle'], $_POST['Image'], $_POST['FlavorText']);
$result = $stmt->execute();

if (!$result) {
  error_log("MySQL error: ".$stmt->error);
  return_json("error", "A MySQL error occurred.");
}

action("nominee-$action", $category, $nominee);

// Perform a history update
$query = "INSERT INTO `history` "
  . "(`UserID`, `Table`, `EntryID`, `Values`, `Timestamp`) "
  . "VALUES(?, ?, ?, ?, NOW())";
$stmt = $mysql->prepare($query);
$stmt->bind_param("ssss", $ID, $table, $entryID, $values);

$table = "nominees";
$entryID = "$category/$nominee";
$values = json_encode($_POST);

$result = $stmt->execute();

if (!$result) {
  error_log("MySQL error: ".$stmt->error);
}

return_json("success");