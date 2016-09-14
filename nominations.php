<?php
include("includes/php.php");
$tpl->set("title", "Nominations");

$query = "SELECT * FROM `categories` WHERE `Active` = 1 ORDER BY `Order` ASC";
$result = mysql_query($query);

$catCount = mysql_num_rows($result);
$firstHalf = ceil($catCount / 2);

$categoriesOne = array();
$categoriesTwo = array();

$count = 0;
while ($row = mysql_fetch_array($result)) {
	$count++;
	if (isset($_GET['category']) && $_GET['category'] == $row['ID']) {
		$active = true;
	} else {
		$active = false;
	}
	$temp = array("ID" => $row['ID'], "Name" => $row['Name'],
		"Subtitle" => $row['Subtitle'], "Active" => $active,
		"Disabled" => !$row['Enabled']);
	
	if ($count <= $firstHalf) {
		$categoriesOne[] = $temp;
	} else {
		$categoriesTwo[] = $temp;
	}
}

$tpl->set("categoriesOne", $categoriesOne);
$tpl->set("categoriesTwo", $categoriesTwo);
	
$category = false;
if (isset($_GET['category'])) {
	$cat = mysql_real_escape_string($_GET['category']);
	$query = "SELECT * FROM `categories` WHERE `ID` = \"$cat\"";
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 1) {
		$row = mysql_fetch_array($result);
		$category = array(array("ID" => $row['ID'], "Name" => $row['Name'],
			"Subtitle" => $row['Subtitle'], "ForumLink" => $row['ForumLink'],
			"Enabled" => $row['Enabled'] == 1, "Disabled" => !$row['Enabled'],
			"Type" => $row['Type'], "Description" => $row['Description']));
	}
}

if ($category) {
	
	$opinions = array();
	if ($loggedIn) {
		$query = "SELECT * FROM `nominee_feedback` WHERE `CategoryID` = \"$cat\" AND `UserID` = \"$ID\"";
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result)) {
			$opinions[$row['Nominee']] = $row['Opinion'];
		}
	}


	$query = "SELECT * FROM `nominees_all` WHERE `ID` IN (SELECT `Nominee` FROM `nominees` WHERE `CategoryID` = \"$cat\") AND `Type` = \"{$category[0]["Type"]}\"";
	$result = mysql_query($query);
	$nominees = array();
		
	while ($row = mysql_fetch_array($result)) {
		$good = $bad = "";
		$catID = $row['ID'];
		
		if (isset($opinions[$catID])) {
			if ($opinions[$catID] == -1) {
				$bad = "ohyes";
			}
			if ($opinions[$catID] == 1) {
				$good = "ohyes";
			}
		}
			
		
		$nominees[] = array("ID" => $row['ID'], "Name" => $row['Name'], "Good" => $good, "Bad" => $bad);
	}
	
	$tpl->set("empty", empty($nominees));
	$tpl->set("nominees", $nominees);
}

if ($loggedIn && $category) {
	$query = "SELECT `Suggestions` FROM `nominee_suggestions` WHERE `CategoryID` = \"$cat\" AND `UserID` = \"$ID\"";
	$result = mysql_query($query);
	$feedback = array();
	while ($row = mysql_fetch_array($result)) {
		$feedback[] = $row["Suggestions"];
	}
	
	if (empty($feedback)) {
		$feedback[] = "<em>You have not made any nominations for this category.</em>";
	}	
	$tpl->set("selfNominations", $feedback);
}


$tpl->set("category", $category);

fetch();
?>