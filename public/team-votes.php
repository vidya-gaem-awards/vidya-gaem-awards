<?php
include(__DIR__."/../includes/php.php");

error_reporting(E_ALL ^ E_NOTICE);

$tpl->set("title", "Team Votes");

$query = "SELECT `users`.`Name` as `User`, `categories`.`Name` as `Category`, `categories`.`ID` as `CategoryID`, `nominees_all`.`Name` as `Nominee`, `votes`.`Nominee` as `NomineeID` FROM `votes`";
$query .= " INNER JOIN `categories` ON `CategoryID` = `categories`.`ID`";
$query .= " INNER JOIN `users` ON `votes`.`UserID` = `SteamID`";
$query .= " INNER JOIN `user_rights` ON `user_rights`.`UserID` = `SteamID`";
$query .= " INNER JOIN `nominees_all` ON `nominees_all`.`ID` = `Nominee`";
$query .= " WHERE `Privilege` = 'secretclub'";
$query .= " AND `categories`.`Type` = `nominees_all`.`Type`";
$query .= " ORDER BY `users`.`Name` ASC, `Order` ASC";

$result = $dbh->query($query);

$votes = array();
$categories = array();

while ($row = $result->fetch_assoc()) {
	$votes[$row['CategoryID']][$row['User']] = $row['Nominee'];
}

$query = "SELECT * FROM `categories` WHERE `Active` = 1 ORDER BY `Order` ASC";
$result = $dbh->query($query);

while ($row = $result->fetch_assoc()) {
	$categories[$row['ID']] = $row['Name'];
}

$users = array("Clamburger", "Cluey3", "Ice", "Lord Mandalore", "Nighthood", "puyo", "Ryan", "Thorkell The Tall");
shuffle($users);
$fakeUsers = array("A", "B", "C", "D", "E", "F", "G", "H");
$tpl->set("users", $fakeUsers);

$htmlCats = array();
foreach ($categories as $ID => $category) {
	$temp = array("Name" => $category, "HTML" => "");
	foreach ($users as $user) {
		$temp["HTML"] .= "<td>{$votes[$ID][$user]}</td>";
	}
	$htmlCats[] = $temp;
}
$tpl->set("categories", $htmlCats);

fetch();
?>
