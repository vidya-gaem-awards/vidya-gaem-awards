<?php
$tpl->set("title", "Categories and Nominations");

mysql_query('SET NAMES utf8');

if ($SEGMENTS[1] != "edit" && $SEGMENTS[1] != "results") {

	$allowedToNominate = $loggedIn || !$ACCOUNT_REQUIRED_TO_NOMINATE;

	##### Grab the list of category votes #####

	$categoryVotes = array();
	$query = "SELECT * FROM `category_feedback` WHERE `UserID` = \"$ID\"";
	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result)) {
		$categoryVotes[$row['CategoryID']] = $row['Opinion'];
	}
	
	##### Grab the list of user nominations (if logged in)
	$userNominations = array();
	if ($allowedToNominate) {
		$query = "SELECT `CategoryID`, `Nomination` FROM `user_nominations` WHERE `UserID` = \"$ID\" ORDER BY `CategoryID` ASC, `Nomination` ASC";
		$result = mysql_query($query);
		while ($row = mysql_fetch_assoc($result)) {
			$userNominations[$row['CategoryID']][] = htmlentities($row['Nomination']);
		}
	}

	##### Grab the list of categories #####

	$query = "SELECT * FROM `categories` WHERE `Enabled` = 1 AND `Secret` = 0 ORDER BY `Order` ASC";
	$result = mysql_query($query);

	$categories = array();

	$categorySelector = "";
	$categoryJS = "";
	$autocompleters = array();

	$categoryCount = mysql_num_rows($result);

	while ($row = mysql_fetch_assoc($result)) {
		
		$categoryVote = isset($categoryVotes[$row['ID']]) ? $categoryVotes[$row['ID']] : 0;
		if ($categoryVote == 1) {
			$voteIcon = "&#x2714;";
		} else if ($categoryVote == -1) {
			$voteIcon = "&#x2718;";
		} else {
			$voteIcon = "";
		}
		$row['OpinionIcon'] = $voteIcon;

		if (!$CATEGORY_VOTING_ENABLED) {
			if (!$allowedToNominate) {
				$row['OpinionIcon'] = "";
			} else if (!isset($userNominations[$row['ID']])) {
				$row['OpinionIcon'] = "[0]";
			} else {
				$row['OpinionIcon'] = "[".count($userNominations[$row['ID']])."]";
			}
		}
	
		$categories[] = $row;
		
		if ($row['AutocompleteCategory']) {
			$autocomplete = $row['AutocompleteCategory'];
		} else {
			$autocompleters[$row['ID']] = array();
			$autocomplete = $row['ID'];
		}
		
		$nominationsEnabled = $row['NominationsEnabled'] ? "true" : "false";
		
		$desc = json_encode($row['Comments']);
		$row = array_map("htmlentities", $row);
		
		$javascriptVars = array(
			"Name" => '"'.$row['Name'].'"',
			"Subtitle" => '"'.$row['Subtitle'].'"',
			"Description" => $desc,
			"Nominations" => $nominationsEnabled,
			"Autocomplete" => '"'.$autocomplete.'"',
			"Opinion" => $categoryVote,
		);
		
		if ($allowedToNominate && isset($userNominations[$row['ID']])) {
			$javascriptVars["UserNominations"] = json_encode($userNominations[$row['ID']]);
		} else {
			$javascriptVars["UserNominations"] = "[]";
		}

		if (!$CATEGORY_VOTING_ENABLED) {
			$javascriptVars["OpinionIcon"] = "[".count(json_decode($javascriptVars["UserNominations"]))."]";
		}
		
		$categoryJS .= "\"{$row['ID']}\": {";
		foreach ($javascriptVars as $key => $value) {
			$categoryJS .= "\"$key\": $value, ";
		}
		$categoryJS .= "},\n\t";

	}

	$tpl->set("categories", $categories);

	$tpl->set("categoryJavascript", $categoryJS);

	##### Grab the list of video games for relevant autocompletions #####
	$query = "SELECT * FROM `2010_releases`";
	$result = mysql_query($query);

	$games = array();

	while ($row = mysql_fetch_assoc($result)) {

		$platforms = array();
		
		foreach ($row as $key => $value) {
			if ($key == "Game") {
				continue;
			}	
			
			if ($value > 0) {
				$platforms[] = $key;
			}
		}
		
		$platforms = implode(", ", $platforms);

		$games[] = "{value: \"{$row['Game']}\", label: \"{$row['Game']} ($platforms)\"}";
	}

	$games = '"video-game": ['."\n\t".implode(",\n\t", $games)."\n\t]";

	##### Grab a list of entered nominations for other autocompletions #####
	$query = "SELECT `CategoryID`, `Nomination`, COUNT(*) as `Count` FROM `user_nominations` WHERE `CategoryID` IN (SELECT `ID` FROM `categories` WHERE `categories`.`AutocompleteCategory` IS NULL) GROUP BY `CategoryID`, `Nomination` HAVING `Count` >= 2 ORDER BY `CategoryID` ASC, `Nomination` ASC";
	$result = mysql_query($query);

	while ($row = mysql_fetch_assoc($result)) {
		$autocompleters[$row['CategoryID']][] = '"'.$row['Nomination'].'"';
	}


	$autocompleteJS = "$games,\n";
	foreach ($autocompleters as $category => $list) {
		$autocompleteJS .= "\t\"$category\": [".implode(", ", $list)."],\n";
	}

	$tpl->set("autocompleteJavascript", $autocompleteJS);
	$tpl->set("CATEGORY_VOTING_ENABLED", $CATEGORY_VOTING_ENABLED);
	$tpl->set("allowedToNominate", $allowedToNominate);
	
/***** VIEW CATEGORY RESULTS *******/

} else if ($SEGMENTS[1] == "results") {

	if (!canDo("categories-results")) {
		$PAGE = "401";
	} else {
		$CUSTOM_TEMPLATE = "results";
	
		$query = "SELECT * FROM `categories` WHERE `Secret` = 0 ORDER BY `Enabled` DESC, `Order` ASC";
		$result = mysql_query($query);

		$categories = array();
		
		while ($row = mysql_fetch_array($result)) {
			$categories[$row['ID']] = array("Name" => str_replace('"', "", $row['Name']) . "<br/>" . $row['Subtitle'],
        "Disabled" => $row['Enabled'] ? "" : "backgroundColor: '#000000',", "Yes" => 0, "No" => 0);
		}
		
		$query = "SELECT `CategoryID`, `Opinion`, COUNT(*) as `Count`
					FROM `category_feedback`
					WHERE `Opinion` != 0
					GROUP BY `CategoryID`, `Opinion`
					ORDER BY `Count` DESC";
		$result = mysql_query($query);

		while ($row = mysql_fetch_array($result)) {
			if ($row['Opinion'] == -1) {
				$index = "No";
			} else {
				$index = "Yes";
			}
			
			$categories[$row['CategoryID']][$index] = $row['Count'];
		}

		$categoryRows = array();
		$rowCount = -1;
		$colCount = -1;
		foreach ($categories as $key => $data) {
			if ($colCount == -1) {
				$rowCount++;
				$categoryRows[] = array("cols" => array());
			}
			$colCount++;
			
			$json = '[{name: "Yes", y: '.$data['Yes'].'}, {name: "No", y: '.$data['No'].'}]';
			$categoryRows[$rowCount]["cols"][$colCount] = array("ID" => $key, "Name" => $data['Name'], "Disabled" => $data['Disabled'], "Data" => $json);
			
			if ($colCount == 2) {
				$colCount = -1;
			}
		}
		
		$tpl->set("categoryRows", $categoryRows);
	
	}

/***** EDIT CATEGORIES *******/

} else if ($SEGMENTS[1] == "edit") {

	if (!canDo("categories-edit")) {
		$PAGE = "401";
	} else {
		$CUSTOM_TEMPLATE = "edit";
		require("categories-edit.php");
	}
	
}

#### Admin tools #####
$adminTools = array();
if (canDo("categories-edit")) {
	$adminTools[] = array("Link" => "/categories/edit", "Text" => "Edit categories");
}
if (canDo("categories-results")) {
	$adminTools[] = array("Link" => "/categories/results", "Text" => "View category votes");
}
if (canDo("nominations-view")) {
	$adminTools[] = array("Link" => "/nominations/results", "Text" => "View user nominations");
}
if (canDo("nominations-edit")) {
  $adminTools[] = array("Link" => "/nominations", "Text" => "Edit official nominees");
} else if (canDo("nominees-view")) {
  $adminTools[] = arraY("Link" => "/nominations", "Text" => "View official nominees");
}
if (count($adminTools) == 0) {
	$adminTools = false;
}
$tpl->set("adminTools", $adminTools);
?>