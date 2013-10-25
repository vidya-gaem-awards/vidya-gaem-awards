<?php
function error($msg) {
  echo json_encode(array("result" => "error", "error" => $msg));
  exit;
}

$details = false;

$action = $_POST['Action'];
if ($action != "edit" && $action != "delete" && $action != "new") {
  error("Invalid action specified.");
}

$category = $_POST['Category'];
$cat = mysql_real_escape_string($category);

// Sanity checks on provided category and nominee
if ($action == "new") {
  $query = "SELECT * FROM `categories` WHERE `ID` = \"$cat\" AND `Enabled` = 1";
  $result = mysql_query($query);
  if (mysql_num_rows($result) === 0) {
    error("Category \"$category\" doesn't exist or isn't enabled.");
  }
}

$nominee = $_POST['NomineeID'];
$nom = mysql_real_escape_string($nominee);

$query = "SELECT * FROM `nominees` WHERE `CategoryID` = \"$cat\" AND `NomineeID` = \"$nom\"";
$result = mysql_query($query);

$nomineeExists = mysql_num_rows($result);
  
if (($action == "edit" || $action == "delete") && !$nomineeExists) {
  error("Couldn't find nominee \"$nominee\" in category \"$category\".");
}

// Time to do the real work
if ($action == "delete") {

  $query = "DELETE FROM `nominees` WHERE `CategoryID` = \"$cat\" AND `NomineeID` = \"$nom\"";
  $result = mysql_query($query);
  action("nominee-delete", $category, $nominee);
  
} else {
  if (trim($_POST['NomineeID']) == "") {
    error("ID cannot be empty.");
  } else if ($action == "new" && $nomineeExists) {
    error("A nominee with that ID already exists in this category.");
  } else if (trim($_POST['Name']) == "") {
    error("Name cannot be empty.");
  } else if (preg_match('/[^a-z0-9-]/', $_POST['NomineeID'])) {
    error("ID should consist of lowercase letters, numbers and dashes only.");
  }
  
  $values = array_map("mysql_real_escape_string", $_POST);
  
  if ($action == "edit") {
    $query = "UPDATE `nominees` SET `Name` = \"{$values["Name"]}\", `Subtitle` = \"{$values["Subtitle"]}\", ";
    $query .= "`Image` = \"{$values["Image"]}\" WHERE `CategoryID` = \"$cat\" AND `NomineeID` = \"$nom\"";
  } else {
    $query = "INSERT INTO `nominees` (`CategoryID`, `NomineeID`, `Name`, `Subtitle`, `Image`) ";
    $query .= "VALUES (\"$cat\", \"$nom\", \"{$values['Name']}\", \"{$values['Subtitle']}\", \"{$values['Image']}\")";
  }
  
  $result = mysql_query($query);
  
  if (!$result) {
    error("MySQL failure.<br>".mysql_error());
  } else {
    $serial = mysql_real_escape_string(json_encode($_POST));
    $query = "INSERT INTO `history` (`UserID`, `Table`, `EntryID`, `Values`, `Timestamp`)";
    $query .= "VALUES ('$ID', 'nominees', '$cat/$nominee', '$serial', NOW())";
    debug_query($query);
    
    action("nominee-$action", $category, $nominee);
  }
  
}

echo json_encode(array("result" => "success", "details" => $details));
?>