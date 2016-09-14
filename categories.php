<?php
require("includes/php.php");
$tpl->set("title", "Categories");

$query = "SELECT * FROM `categories` WHERE `Active` = 1 ORDER BY `order` ASC";
$result = mysql_query($query);

$opinions = array();

if ($loggedIn) {
	$query2 = "SELECT * FROM `category_feedback` WHERE `UserID` = \"$ID\"";
	$result2 = mysql_query($query2);
	while ($row2 = mysql_fetch_array($result2)) {
		$opinions[$row2['CategoryID']] = $row2['Opinion'];
	}
}

$categories = array();

while ($row = mysql_fetch_array($result)) {
	$up = $down = "";
	$catID = $row['ID'];

	if (isset($opinions[$catID])) {
		if ($opinions[$catID] == -1) {
			$down = "disabled";
		}
		if ($opinions[$catID] == 1) {
			$up = "disabled";
		}
	}

	$categories[] = array(
		"id" => $row['ID'],
		"name" => $row['Name'],
		"subtitle" => $row['Subtitle'],
		"forum" => $row['ForumLink'],
		"up" => $up,
		"down" => $down
	);
}

$tpl->set("categories", $categories);

fetch();
?>