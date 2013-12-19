<?php
if ($CATEGORY_VOTING_ENABLED) {
  $tpl->set("title", "Awards and Nominations");
} else {
  $tpl->set("title", "Award Nominations");
}

// Load award management if permissions allow
if ($SEGMENTS[1] == "manage") {
  if (!canDo("categories-feedback")) {
    $PAGE = $loggedIn ? "403" : "401";
  } else {
    $CUSTOM_TEMPLATE = "manage";
    require("categories-manage.php");
  }
} else {

  $tpl->set("CATEGORY_VOTING_ENABLED", $CATEGORY_VOTING_ENABLED);
  $allowedToNominate = $loggedIn || !$ACCOUNT_REQUIRED_TO_NOMINATE;
  $tpl->set("allowedToNominate", $allowedToNominate);
  
  // Get the list of nominations the user has already made
  $userNominations = array();
  if ($allowedToNominate) {
    $query = "SELECT `CategoryID`, `Nomination` FROM `user_nominations` 
              WHERE `UserID` = ? ORDER BY `CategoryID` ASC, `Nomination` ASC";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param('s', $ID);
    $stmt->execute();
    $stmt->bind_result($categoryID, $nomination);
    while ($stmt->fetch()) {
      $userNominations[$categoryID][] = $nomination;
    }
  }

  // This cunning query gets the list of categories and the user's votes for
  // those categories at the same time
  $query = "SELECT `ID`, `Name`, `Subtitle`, `Order`, `Comments`,
            `NominationsEnabled`, `AutocompleteCategory`, `Opinion`
            FROM `categories`
            LEFT JOIN `category_feedback` ON `ID` = `CategoryID`
            AND `UserID` = ? AND `Opinion` != 0
            WHERE `Enabled` = 1 AND `Secret` = 0
            ORDER BY `Order` ASC";
  $stmt = $mysql->prepare($query);
  $stmt->bind_param('s', $ID);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($categoryID, $name, $subtitle, $order, $comments,
    $nominationsEnabled, $autocomplete, $opinion);

  // We'll need these later
  $categories = $categoryJS = $autocompleters = $autocompleteJS = array();

  while ($stmt->fetch()) {

    // Show the appropriate icon or counter next to each category on the left
    if ($CATEGORY_VOTING_ENABLED) {
      $voteIcon = "";
      if ($opinion === 1) {
        $voteIcon = "&#x2714;";
      } else if ($opinion === -1) {
        $voteIcon = "&#x2718;";
      }
    } else {
      if (!$allowedToNominate) {
        $voteIcon = "";
      } else if (!isset($userNominations[$categoryID])) {
        $voteIcon = "[0]";
      } else {
        $voteIcon = "[".count($userNominations[$categoryID])."]";
      }
    }

    $categories[] = array(
      "ID" => $categoryID,
      "Name" => $name,
      "Subtitle" => $subtitle,
      "OpinionIcon" => $voteIcon
    );

    // If the default autocomplete is set, use the category ID for completions
    if (!$autocomplete) {
      $autocompleters[$categoryID] = array();
      $autocomplete = $categoryID;
    }

    if (!$allowedToNominate || !isset($userNominations[$categoryID])) {
      $userNominations[$categoryID] = array();
    }

    $thisCategory = array_map("htmlentities", array(
      "Name" => $name,
      "Subtitle" => $subtitle,
      "Autocomplete" => $autocomplete,
    ));
    $thisCategory["UserNominations"] = array_map("htmlentities",
      $userNominations[$categoryID]);
    
    // We add these separately so they don't get clobbered by htmlentities()
    $thisCategory = array_merge($thisCategory, array(
      "Nominations" => $nominationsEnabled,
      "Opinion" => $opinion,
      "Description" => $comments
    ));

    $categoryJS[$categoryID] = $thisCategory;

  }

  $tpl->set("categories", $categories);
  $tpl->set("categoryJavascript", json_encode($categoryJS));

  // Grab the list of video games for relevant autocompletions
  // TODO: store a list of valid platforms somewhere
  $query = "SELECT * FROM `2010_releases` ORDER BY `Game` ASC";
  $result = $mysql->query($query);

  // This loop takes each game and appends the platforms it's on as the label
  // Example: Payday 2 (PC, PS3, 360)
  while ($row = $result->fetch_assoc()) {
    $platforms = array();
    foreach ($row as $key => $value) {
      if ($key != "Game" && $key != "Notable" && $value > 0) {
        $platforms[] = $key;
      }
    }
    
    $platforms = implode(", ", $platforms);

    $autocompleteJS["video-game"][] = array(
      "value" => $row['Game'],
      "label" => $row['Game'] . " ($platforms)"
    );
  }

  // Grab special autocompleters from the database
  $query = "SELECT `ID`, `Strings` FROM `autocompleters`";
  $result = $mysql->query($query);

  while ($row = $result->fetch_assoc()) {
    $values = explode("\n", $row['Strings']);
    sort($values);
    foreach ($values as $value) {
      $autocompleters[$row['ID']][] = $value;
    }
  }

  // Grab a list of entered nominations for other autocompletions
  // TODO: this looks suspiciously long and potentially slow
  $query = "SELECT `CategoryID`, `Nomination`, COUNT(*) as `Count`
            FROM `user_nominations` WHERE `CategoryID` IN 
              (SELECT `ID` FROM `categories`
               WHERE `categories`.`AutocompleteCategory` IS NULL)
            GROUP BY `CategoryID`, `Nomination` HAVING `Count` >= 2
            ORDER BY `CategoryID` ASC, `Nomination` ASC";
  $result = $mysql->query($query);

  while ($row = $result->fetch_assoc()) {
    $autocompleters[$row['CategoryID']][] = $row['Nomination'];
  }

  // Remove duplicates and sort the list
  foreach ($autocompleters as $category => $list) {
    $cleanList = array_unique($list);
    sort($cleanList);
    $autocompleteJS[$category] = $cleanList;
  }

  $tpl->set("autocompleteJavascript", json_encode($autocompleteJS));
  
}

// Create the admin tool links
$adminTools = array();
if (canDo("categories-edit")) {
  $adminTools[] = array("Link" => "/categories/manage", "Text" => "Manage awards");
} else if (canDo("categories-feedback")) {
  $adminTools[] = array("Link" => "/categories/manage", "Text" => "View award feedback");
}
if (canDo("nominations-edit")) {
  $adminTools[] = array("Link" => "/nominations", "Text" => "Manage nominees");
} else if (canDo("nominations-view")) {
  $adminTools[] = array("Link" => "/nominations", "Text" => "View nominees");
}
if (count($adminTools) == 0) {
  $adminTools = false;
}
$tpl->set("adminTools", $adminTools);