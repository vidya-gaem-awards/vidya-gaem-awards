<?php
use VGA\Utils;

$action = $_POST['Action'];
$category = $_POST['Category'];
$nominee = $_POST['NomineeID'];

// Sanity checking
if ($action != "edit" && $action != "delete" && $action != "new") {
    Utils::returnJSON("error", "Invalid action specified.");
} elseif (trim($nominee) == "") {
    Utils::returnJSON("error", "A nominee ID is required.");
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
        Utils::returnJSON("error", "The specified category doesn't exist.");
    }
    $stmt->fetch();
    if ($enabled !== 1) {
        Utils::returnJSON("error", "The specified category is not enabled.");
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
    Utils::returnJSON("error", "Couldn't find that nominee in that category.");
} elseif ($action == "new" && $nomineeExists) {
    Utils::returnJSON(
        "error",
        "A nominee with that ID already exists in this category."
    );
}

// Deleting a nominee
if ($action == "delete") {
    $query = "DELETE FROM `nominees` WHERE `CategoryID` = ? AND `NomineeID` = ?";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param("ss", $category, $nominee);
    $stmt->execute();

    Utils::action("nominee-delete", $category, $nominee);

    Utils::returnJSON("success");
}

// Adding or editing a nominee
if (trim($_POST['Name']) == "") {
    Utils::returnJSON("error", "The nominee must have a name.");
} elseif (preg_match('/[^a-z0-9-]/', $_POST['NomineeID'])) {
    Utils::returnJSON("error", "The nominee ID should consist of lowercase letters, ".
    "numbers and dashes only.");
}

// Perform the insertion/replacement
$query = "REPLACE INTO `nominees` (`CategoryID`, `NomineeID`, `Name`, ".
  "`Subtitle`, `Image`, `FlavorText`) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $mysql->prepare($query);
$stmt->bind_param(
    "ssssss",
    $category,
    $nominee,
    $_POST['Name'],
    $_POST['Subtitle'],
    $_POST['Image'],
    $_POST['FlavorText']
);
$result = $stmt->execute();

if (!$result) {
    error_log("MySQL error: ".$stmt->error);
    Utils::returnJSON("error", "A MySQL error occurred.");
}

Utils::action("nominee-$action", $category, $nominee);

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

Utils::returnJSON("success");
