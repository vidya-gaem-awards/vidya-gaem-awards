<?php
$tpl->set("title", "Official Nominees");

$tpl->set("categoryName", false);

$tpl->set("canEdit", canDo("nominations-edit"));

if (canDo("nominations-edit")) {
  $tpl->set("title", "Nominee Manager");
} else {
  $tpl->set("title", "Nominee Viewer");
}

$cat = false;
if ($SEGMENTS[1]) {
  $cat = $mysql->real_escape_string($SEGMENTS[1]);
  $query = "SELECT * FROM `categories` WHERE `ID` = \"$cat\" AND `Enabled` = 1";
  if (!canDo("categories-secret")) {
    $query .= " AND `Secret` = 0";
  }
  $result = $mysql->query($query);
  
  if ($result->num_rows == 1) {
  
    $categoryInfo = $result->fetch_assoc();
    $tpl->set("category", $cat);
    $tpl->set("categoryName", $categoryInfo['Name']);
    $tpl->set("categorySubtitle", $categoryInfo['Subtitle']);
    $tpl->set("categorySecret", $categoryInfo['Secret']);
    $tpl->set("nominationsOpen", $categoryInfo['NominationsEnabled']);

    // Get a list of all the current nominees
    $query = "SELECT * FROM `nominees` WHERE `CategoryID` = \"$cat\" ORDER BY `Name` ASC";
    $result = $mysql->query($query);

    $official = array();
    $javascript = array();
    while ($row = $result->fetch_assoc()) {
      $javascript[$row['NomineeID']] = $row;
      if (empty($row['Image'])) {
        $row["Image"] = "/assets/nominees/{$row['NomineeID']}.png";
      }
      $official[] = $row;
    }
    $tpl->set("official", $official);
    $tpl->set("officialCount", count($official));
    $tpl->set("nomineeJavascript", json_encode($javascript));
    
    // Get a list of user nominations
    $query = "SELECT `Nomination`, COUNT(*) as `Count` FROM `user_nominations` WHERE `CategoryID` = \"$cat\" GROUP BY `Nomination` ORDER BY `Count` DESC,     `Nomination` ASC";
    $result = $mysql->query($query);
    
    $userCount = 0;
    $userNominations = array();
    $autoComplete = array();
    while ($row = $result->fetch_assoc()) {
      $nom = htmlspecialchars($row['Nomination']);
      $userNominations[] = "<li><strong>{$row['Count']} x</strong> $nom</li>";
      $userCount += $row['Count'];
      if ($row['Count'] >= 3) {
        $autoComplete[] = $row['Nomination'];
      }
    }
    sort($autoComplete);
    $userNominationsTop = implode("\n", array_splice($userNominations, 0, 15));
    $userNominations = implode("\n", $userNominations);

    $tpl->set("userNominationsTop", $userNominationsTop);
    $tpl->set("userNominations", $userNominations);
    $tpl->set("userCount", $userCount);
    
    $tpl->set("autocompleteJavascript", json_encode($autoComplete));
    
  }
 
}

// Get a list of enabled categories
$query = "SELECT * FROM `categories` WHERE `Enabled` = 1 ";
if (!canDo("categories-secret")) {
    $query .= "AND `Secret` = 0 ";
  }
$query .= "ORDER BY `Order` ASC";
$result = $mysql->query($query);

$categories = array();

$categorySelector = "";
$categoryJS = "";
$autocompleters = array();
$userNominations = array();

$categoryCount = $result->num_rows;

while ($row = $result->fetch_assoc()) {
  $row['Active'] = $row['ID'] == $cat ? "active" : "";
  $categories[] = $row;
}

$tpl->set("categories", $categories);
?>
