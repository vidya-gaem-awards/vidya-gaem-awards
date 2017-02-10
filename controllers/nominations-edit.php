<?php
$tpl->set("title", "Official Nominees");
mysql_query('SET NAMES utf8');

$tpl->set("categoryName", false);

$tpl->set("canEdit", canDo("nominations-edit"));

$cat = false;
if ($SEGMENTS[1]) {
	$cat = mysql_real_escape_string($SEGMENTS[1]);
	$query = "SELECT * FROM `categories` WHERE `ID` = \"$cat\" AND `Enabled` = 1";
	if (!canDo("nominations-edit")) {
    $query .= " AND `Secret` = 0";
  }
	$result = mysql_query($query);
  
  if (mysql_num_rows($result) == 1) {
  
    $categoryInfo = mysql_fetch_assoc($result);
    $tpl->set("category", $cat);
    $tpl->set("categoryName", $categoryInfo['Name']);
    $tpl->set("categorySubtitle", $categoryInfo['Subtitle']);
    if ($categoryInfo['NominationsEnabled']) {
      $tpl->set("nominationStatus", "open");
    } else {
      $tpl->set("nominationStatus", "closed");
    }

    // Get a list of all the current nominees
    $query = "SELECT * FROM `nominees` WHERE `CategoryID` = \"$cat\" ORDER BY `Name` ASC";
    $result = mysql_query($query);

    $official = array();
    $javascript = array();
    while ($row = mysql_fetch_assoc($result)) {
      $javascript[$row['NomineeID']] = $row;
			if (empty($row['Image'])) {
        $row["Image"] = "/nominees/{$row['NomineeID']}.png";
      }
      $official[] = $row;
    }
    $tpl->set("official", $official);
    $tpl->set("officialCount", count($official));
    $tpl->set("nomineeJavascript", json_encode($javascript));
    
    // Get a list of user nominations
    $query = "SELECT `Nomination`, COUNT(*) as `Count` FROM `user_nominations` WHERE `CategoryID` = \"$cat\" GROUP BY `Nomination` ORDER BY `Count` DESC,     `Nomination` ASC";
    $result = mysql_query($query);
    
    $userCount = 0;
    $userNominations = array();
    $autoComplete = array();
    while ($row = mysql_fetch_assoc($result)) {
      $nom = htmlspecialchars($row['Nomination']);
      $userNominations[] = "<li><strong>{$row['Count']} x</strong> $nom</li>";
      $userCount += $row['Count'];
      if ($row['Count'] >= 3) {
        $autoComplete[] = $row['Nomination'];
      }
    }
    sort($autoComplete);
    $userNominations = implode("\n", $userNominations);
    
    $tpl->set("userNominations", $userNominations);
    $tpl->set("userCount", $userCount);
    
    $tpl->set("autocompleteJavascript", json_encode($autoComplete));
    
  }
 
}

// Get a list of enabled categories
$query = "SELECT * FROM `categories` WHERE `Enabled` = 1 ";
if (!canDo("nominations-edit")) {
    $query .= "AND `Secret` = 0 ";
  }
$query .= "ORDER BY `Order` ASC";
$result = mysql_query($query);

$categories = array();

$categorySelector = "";
$categoryJS = "";
$autocompleters = array();
$userNominations = array();

$categoryCount = mysql_num_rows($result);

while ($row = mysql_fetch_assoc($result)) {
  $row['Active'] = $row['ID'] == $cat ? "active" : "";
  $categories[] = $row;
}

$tpl->set("categories", $categories);
?>
