<?php
// Sanity checking
if (!$CATEGORY_VOTING_ENABLED) {
  return_json("error", "Voting on categories is currently disabled.");
} else if ($_POST['opinion'] != -1 && $_POST['opinion'] != 1 && $_POST['opinion'] != 0) {
  return_json("error", "You provided an invalid opinion.");
} 

$category = $_POST['ID'];

// Check if the category exists
$query = "SELECT `ID` FROM `categories` WHERE `ID` = ?";
$stmt = $mysql->prepare($query);
$stmt->bind_param("s", $category);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
  return_json("error", "The specified category doesn't exist.");
}
$stmt->close();

// Insert the vote, overwriting the previous one if it exists
$query = "REPLACE INTO `category_feedback` VALUES (?, ?, ?)";
$stmt = $mysql->prepare($query);
$stmt->bind_param("ssi", $category, $ID, $_POST['opinion']);
$stmt->execute();
$stmt->close();

action("opinion-given", $category, $opinion);

return_json("success");