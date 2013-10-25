<?php
$tpl->set("title", "Nominations");

if ($SEGMENTS[1] != "results") {

	header("Location: /categories");
	die();
	
}

if (!canDo("nominations-view")) {
	$PAGE = "401";
} else {
	$CUSTOM_TEMPLATE = "results";
	
	//$query = "SELECT `ID`, `Name`, `Subtitle`, `Nomination`, `AutoID`, `UserID` FROM `user_nominations`, `categories` WHERE `ID` = `CategoryID` ORDER BY `Order` ASC, `AutoID` DESC";
	$query = "SELECT `ID`, `Name`, `Subtitle`, `Nomination`, COUNT(*) as `Count` FROM `user_nominations`, `categories` WHERE `ID` = `CategoryID` GROUP BY `ID`, `Nomination` ORDER BY `Enabled` DESC, `Order` ASC, `Count` DESC, `Nomination` ASC";
	$result = mysql_query($query);
	
	$nominations = array();
	$categories = array();
	$category = "";
	while ($row = mysql_fetch_array($result)) {
		if (!isset($categories[$row['ID']])) {
			$categories[$row['ID']] = array($row['Name'], $row['Subtitle']);
			$nominations[$row['ID']] = array();
		}
		
		$nominations[$row['ID']][] = array("Text" => "<strong>{$row['Count']} x </strong> " . str_replace("\n", "<br />", $row['Nomination']));
	}

	$templateCat = array();
	foreach ($categories as $cat => $catInfo) {
		$templateCat[] = array("Name" => $catInfo[0], "Subtitle" => $catInfo[1],
			"Nominations" => $nominations[$cat]);
	}
	
	$tpl->set("categories", $templateCat);
}
?>