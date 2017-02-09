<?php
include(__DIR__."/../includes/php.php");
$tpl->set("title", "Voting");

date_default_timezone_set('America/New_York');
$current = time();
$start = strtotime("2011-12-22 00:00:00");
$finish = strtotime("2012-01-01 00:00:00");

$voteText = "";
if ($current > $start && $current < $finish) {
	$votingEnabled = true;
	$voteText = "Voting for the 2011 /v/GAs is now open!";
	
	$seconds = $finish - $current;
	$minutes = floor($seconds / 60);
	$hours = floor($minutes / 60);
	$days = floor($hours / 24);
	
	$seconds -= $minutes * 60;
	$minutes -= $hours * 60;
	$hours -= $days * 24;
	
	if ($days != 1) {
		$s = "s";
	} else {
		$s = "";
	}
	
	$difference = sprintf("%d day%s, %02d:%02d:%02d", $days, $s, $hours, $minutes, $seconds);
	
	$voteText .= "<br />Voting ends in $difference";
	
} else {
	$votingEnabled = false;
	if ($current < $start) {
		$seconds = $start - $current;
		$minutes = floor($seconds / 60);
		$hours = floor($minutes / 60);
		
		$seconds -= $minutes * 60;
		$minutes -= $hours * 60;
		
		$difference = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
		
		$voteText = "Voting for the 2011 /v/GAs will be starting soon.<br />Voting opens in $difference";
	} else {
		$voteText = "Voting for the 2011 /v/GAs has ended!";
	}
}
$tpl->set("voteText", $voteText);

if (isset($_GET['testImage'])) {
	$tpl->set("testImage", $_GET['testImage']);
} else {
	$tpl->set("testImage", false);
}

$completedCategories = array();
if ($loggedIn) {
	$query = "SELECT * FROM `votes` WHERE `UserID` = \"$ID\"";
	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result)) {
		$completedCategories[$row['CategoryID']] = $row['Nominee'];
	}
}

$query = "SELECT * FROM `categories` WHERE `Active` = 1 ORDER BY `Order` ASC";
$result = mysql_query($query);

$categories = array();
while ($row = mysql_fetch_array($result)) {
	if (isset($_GET['category']) && $_GET['category'] == $row['ID']) {
		$row['Active'] = true;
	} else {
		$row['Active'] = false;
	}
	if (isset($completedCategories[$row['ID']])) {
		$row['Completed'] = true;
	} else {
		$completedCategories[$row['ID']] = false;
		$row['Completed'] = false;
	}
	$categories[] = $row;
}

$tpl->set("categories", $categories);

$category = $nominees = false;
if (isset($_GET['category'])) {
	$cat = mysql_real_escape_string($_GET['category']);
	$query = "SELECT * FROM `categories` WHERE `ID` = \"$cat\" AND `Active` = 1";
	$result = mysql_query($query);
	
	if (mysql_num_rows($result) == 1) {
		
		$row = mysql_fetch_array($result);
		$category = $row;		
		
		$query = "SELECT * FROM `nominees`, `nominees_all` WHERE `Nominee` = `ID` AND `CategoryID` = \"$cat\" AND `Type` = \"{$category["Type"]}\" ORDER BY `Name` ASC";
		$result = mysql_query($query);
		$nominees = array();
		while ($row = mysql_fetch_array($result)) {
		
			$row['Background'] = "";
		
			$prefixes = array(strtolower($cat)."-", $row['Type']."-", "");
			if (empty($row['Image'])) {
				
				foreach ($prefixes as $prefix) {
					if (file_exists(__DIR__."/images/nominees/{$prefix}{$row['ID']}.png")) {
						$row['Background'] = "images/nominees/{$prefix}{$row['ID']}.png";
						break;
					}
				}
				
			} else {
				$row['Background'] = $row['Image'];
			}
			
			if ($completedCategories[$cat] == $row['ID']) {
				$row['Voted'] = true;
			} else {
				$row['Voted'] = false;
			}
			
			$nominees[] = $row;
		}
		
	}	
	
} else if (isset($_GET['testImage'])) {
	
	$category = array("ID" => "test", "Name" => "Image Testing", "Subtitle" => "");
	$nominees = array(array("ID" => "test", "Name" => "Test Nominee", "ExtraInfo" => "Additional information about the nominee goes here", "Background" => $_GET['testImage']));
		
}
$tpl->set("nominees", $nominees);
$tpl->set("category", $category);

$tpl->set("votingEnabled", $votingEnabled);

$tpl->set("special", canDo("special"));

$tpl->set("navbar", false);

fetch();
?>
