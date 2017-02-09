<?php
require(__DIR__."/../includes/php.php");
$tpl->set("title", "Categories");

$query = "SELECT * FROM `categories` WHERE `Active` = 1 ORDER BY `order` ASC";
$result = $dbh->query($query);

$opinions = array();

if ($loggedIn) {
	$query2 = "SELECT * FROM `category_feedback` WHERE `UserID` = \"" . $dbh->real_escape_string($ID) . "\"";
	$result2 = $dbh->query($query2);
	while ($row2 = $result2->fetch_array()) {
		$opinions[$row2['CategoryID']] = $row2['Opinion'];
	}
}

$categories = array();

while ($row = $result->fetch_array()) {
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
