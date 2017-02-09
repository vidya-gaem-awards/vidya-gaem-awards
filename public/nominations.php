<?php
include(__DIR__."/../includes/php.php");
$tpl->set("title", "Nominations");

$query = "SELECT * FROM `categories` WHERE `Active` = 1 ORDER BY `Order` ASC";
$result = $dbh->query($query);

$catCount = $result->num_rows;
$firstHalf = ceil($catCount / 2);

$categoriesOne = array();
$categoriesTwo = array();

$count = 0;
while ($row = $result->fetch_array()) {
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
	$cat = $dbh->real_escape_string($_GET['category']);
	$query = "SELECT * FROM `categories` WHERE `ID` = \"$cat\"";
	$result = $dbh->query($query);
	if ($result->num_rows == 1) {
		$row = $result->fetch_array();
		$category = array(array("ID" => $row['ID'], "Name" => $row['Name'],
			"Subtitle" => $row['Subtitle'], "ForumLink" => $row['ForumLink'],
			"Enabled" => $row['Enabled'] == 1, "Disabled" => !$row['Enabled'],
			"Type" => $row['Type'], "Description" => $row['Description']));
	}
}

if ($category) {
	
	$opinions = array();
	if ($loggedIn) {
		$query = "SELECT * FROM `nominee_feedback` WHERE `CategoryID` = \"$cat\" AND `UserID` = \"" . $dbh->real_escape_string($ID) . "\"";
		$result = $dbh->query($query);
		while ($row = $result->fetch_array()) {
			$opinions[$row['Nominee']] = $row['Opinion'];
		}
	}


	$query = "SELECT * FROM `nominees_all` WHERE `ID` IN (SELECT `Nominee` FROM `nominees` WHERE `CategoryID` = \"$cat\") AND `Type` = \"" . $dbh->real_escape_string($category[0]["Type"]) . "\"";
	$result = $dbh->query($query);
	$nominees = array();
		
	while ($row = $result->fetch_array()) {
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
	$query = "SELECT `Suggestions` FROM `nominee_suggestions` WHERE `CategoryID` = \"$cat\" AND `UserID` = \"" . $dbh->real_escape_string($ID) . "\"";
	$result = $dbh->query($query);
	$feedback = array();
	while ($row = $result->fetch_array()) {
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
