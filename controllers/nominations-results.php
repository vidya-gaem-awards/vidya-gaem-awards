<?php
$tpl->set("title", "Nominations");

// Handle invalid navigation
if ($SEGMENTS[1] != "results") {
	header("Location: /categories");
	exit;
}

// Handle access control
if (!canDo("nominations-view")) {
	$PAGE = "401";
	return;
}

$CUSTOM_TEMPLATE = "results";

// A somewhat inefficient query that grabs the categories and user nominees
// all at once
$query = 
  "SELECT `ID`, `Name`, `Subtitle`, `Nomination`, COUNT(*) as `Count` "
	. "FROM `user_nominations`, `categories` WHERE `ID` = `CategoryID` "
	. "GROUP BY `ID`, `Nomination` "
	. "ORDER BY `Enabled` DESC, `Order` ASC, `Count` DESC, `Nomination` ASC";
$result = $mysql->query($query);

$nominations = array();
$categories = array();

// Deal out the category info and all the nominees
while ($row = $result->fetch_assoc()) {
	$category = $row['ID'];
	if (!isset($categories[$row['ID']])) {
		$categories[$category] = array($row['Name'], $row['Subtitle']);
		$nominations[$category] = array();
	}
	
	// The meat
	$nominations[$row['ID']][] = array(
		"Text" => "<strong>{$row['Count']} x </strong> "
	  . str_replace("\n", "<br />", $row['Nomination']));
}

// Put the information into a form bTemplate can handle
$templateCat = array();
foreach ($categories as $category => $catInfo) {
	$templateCat[] = array(
		"Name" => $catInfo[0],
		"Subtitle" => $catInfo[1],
		"Nominations" => $nominations[$category]);
}

$tpl->set("categories", $templateCat);