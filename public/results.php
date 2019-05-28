<?php
include(__DIR__."/../includes/php.php");
$tpl->set("title", "Results");

if (false) {
	$denied = true;
} else {
	$denied = false;

	if (isset($_GET['categoryVotes'])) {
	
		$query = "SELECT * FROM `categories` ORDER BY `Order` ASC";
		$result = $dbh->query($query);

		$categories = array();
		
		while ($row = $result->fetch_array()) {
			$categories[$row['ID']] = array("Name" => str_replace('"', "", $row['Name']) . "<br/>" . $row['Subtitle'], "Yes" => 0, "No" => 0);
		}
		
		

		$query = "SELECT `CategoryID`, `Opinion`, COUNT(*) as `Count`
					FROM `category_feedback`
					WHERE `Opinion` != 0
					GROUP BY `CategoryID`, `Opinion`
					ORDER BY `Count` DESC";
		$result = $dbh->query($query);

		while ($row = $result->fetch_array()) {
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
			$categoryRows[$rowCount]["cols"][$colCount] = array("ID" => $key, "Name" => $data['Name'], "Data" => $json);
			
			if ($colCount == 2) {
				$colCount = -1;
			}
		}
		
		$tpl->set("categoryRows", $categoryRows);
	}
	
	if (isset($_GET['suggestionBox'])) {
		$query = "SELECT * from `suggestions` ORDER BY `ID` DESC";
		$result = $dbh->query($query);
		$suggestions = array();
		while ($row = $result->fetch_array()) {
			$suggestion = str_replace("\n", "<br />", $row['Suggestion']);		
			
			if ($row["GoodSuggestion"]) {
				$suggestion = "<strong>$suggestion</strong>";
			}
			
			if (empty($row['UserID'])) {
				$hash = "[anon]";
			} else {
				$hash = substr(md5($row['UserID']), -6);
			}
			
			$suggestion = "[{$row['ID']}] $suggestion";
			$suggestions[] = array("Text" => $suggestion, "Hash" => $hash);
		}
		$tpl->set("suggestions", $suggestions);
	}
	
	if (isset($_GET['nominations'])) {	
		$query = "SELECT `ID`, `Name`, `Subtitle`, `Suggestions`, `AutoID`, `UserID` FROM `nominee_suggestions`, `categories` WHERE `ID` = `CategoryID` ORDER BY `Order` ASC, `AutoID` DESC";
		$result = $dbh->query($query);
		
		$nominations = array();
		$categories = array();
		$category = "";
		while ($row = $result->fetch_array()) {
			if (!isset($categories[$row['ID']])) {
				$categories[$row['ID']] = array($row['Name'], $row['Subtitle']);
				$nominations[$row['ID']] = array();
			}
			
			$hash = substr(md5($row['UserID']), -6);
			
			$nominations[$row['ID']][] = array("Text" => "[{$row['AutoID']}] " . str_replace("\n", "<br />", $row['Suggestions']),
				"Hash" => $hash);
		}

		$templateCat = array();
		foreach ($categories as $cat => $catInfo) {
			$templateCat[] = array("Name" => $catInfo[0], "Subtitle" => $catInfo[1],
				"Nominations" => $nominations[$cat]);
		}
		
		$tpl->set("categories", $templateCat);
		
	}
	
	if (isset($_GET['votes'])) {
	
		$nominees = array();
	
		$query = "SELECT * FROM `nominees_all`";
		$result = $dbh->query($query);
		while ($row = $result->fetch_array()) {
			if (!isset($nominees[$row['Type']])) {
				$nominees[$row['Type']] = array();
			}
			$nominees[$row['Type']][$row['ID']] = $row['Name'];
		}
	
		$query = "SELECT * FROM `categories` WHERE `Active` = 1 ORDER BY `Order` ASC";
		$result = $dbh->query($query);

		$categories = array();
	
		while ($row = $result->fetch_array()) {
			$categories[$row['ID']] = array("Name" => str_replace('"', '\"', $row['Name']), "Subtitle" => $row['Subtitle'], "Type" => $row['Type'], "Votes" => array());
		}
		
		$sites = array("filtered" => "`Website` = 'NULL' OR `Website` = 'boards.4chan.org'",
						"4chan" => "`Website` = 'boards.4chan.org'",
						"reddit" => "`Website` = 'www.reddit.com'",
						"all" => "1 = 1",
						"null" => "`Website` = 'NULL'",
						"other" => "WHERE `Website` != 'NULL' AND `Website` != 'boards.4chan.org'
									AND `Website` != 'www.reddit.com'");
						
		if (!isset($_GET["site"]) || !isset($sites[$_GET["site"]])) {
			$site = "filtered";
		} else {
			$site = $_GET['site'];
		}
		
		$tpl->set("site", $site);

		$query = "SELECT `CategoryID`, `Nominee`, COUNT(*) AS `Count` FROM `votes`
					INNER JOIN `categories` ON `CategoryID` = `categories`.`ID`
					INNER JOIN `users` ON `UserID` = `SteamID`
					WHERE {$sites[$site]}
					GROUP BY `CategoryID`, `Nominee`";
		$result = $dbh->query($query);

		while ($row = $result->fetch_array()) {
			$cat = $row['CategoryID'];
			$nom = $row['Nominee'];
			
			$categories[$cat]["Votes"][$nom] = $row['Count'];
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
			
			$total = 0;
			$json = array();
			foreach ($data['Votes'] as $ID => $votes) {
				$name = str_replace('"', '\"', $nominees[$data['Type']][$ID]);
				$json[] = "{name: \"{$name}\", y: $votes}";
				$total += $votes;
			}
			$json = implode(",", $json);

			$categoryRows[$rowCount]["cols"][$colCount] = array("ID" => $key, "Name" => $data['Name'], "Subtitle" => $data['Subtitle'], "Total" => $total, "Data" => $json);
			
			if ($colCount == 1) {
				$colCount = -1;
			}
		}
		
		$tpl->set("categoryRows", $categoryRows);
	
	}
	
	if (isset($_GET['votesTable'])) {
	
		$nominees = array();
	
		$query = "SELECT * FROM `nominees_all`";
		$result = $dbh->query($query);
		while ($row = $result->fetch_array()) {
			if (!isset($nominees[$row['Type']])) {
				$nominees[$row['Type']] = array();
			}
			$nominees[$row['Type']][$row['ID']] = $row['Name'];
		}
	
		$query = "SELECT * FROM `categories` WHERE `Active` = 1 ORDER BY `Order` ASC";
		$result = $dbh->query($query);

		$categories = array();
	
		while ($row = $result->fetch_array()) {
			$categories[$row['ID']] = array("Name" => $row['Name'], "Subtitle" => $row['Subtitle'], "Type" => $row['Type'], "Votes" => array());
		}

		// All votes
		$query = "SELECT `CategoryID`, `Nominee`, COUNT(*) AS `Count` FROM `votes`
					INNER JOIN `categories` ON `CategoryID` = `categories`.`ID`
					GROUP BY `CategoryID`, `Nominee`
					ORDER BY `CategoryID` ASC, `Count` DESC";
		$result = $dbh->query($query);

		while ($row = $result->fetch_array()) {
			$cat = $row['CategoryID'];
			$nom = $row['Nominee'];
			
			$categories[$cat]["Votes"][$nom]["All"] = $row['Count'];
		}
		
		// 4chan and null votes
		$query = "SELECT `CategoryID`, `Nominee`, COUNT(*) AS `Count` FROM `votes`
					INNER JOIN `categories` ON `CategoryID` = `categories`.`ID`
					INNER JOIN `users` ON `UserID` = `SteamID`
					WHERE `Website` = 'NULL' OR `Website` = 'boards.4chan.org'
					GROUP BY `CategoryID`, `Nominee`
					ORDER BY `CategoryID` ASC, `Count` DESC";
		$result = $dbh->query($query);

		while ($row = $result->fetch_array()) {
			$cat = $row['CategoryID'];
			$nom = $row['Nominee'];
			
			$categories[$cat]["Votes"][$nom]["Filtered"] = $row['Count'];
		}
		
		// 4chan votes
		$query = "SELECT `CategoryID`, `Nominee`, COUNT(*) AS `Count` FROM `votes`
					INNER JOIN `categories` ON `CategoryID` = `categories`.`ID`
					INNER JOIN `users` ON `UserID` = `SteamID`
					WHERE `Website` = 'boards.4chan.org'
					GROUP BY `CategoryID`, `Nominee`
					ORDER BY `CategoryID` ASC, `Count` DESC";
		$result = $dbh->query($query);

		while ($row = $result->fetch_array()) {
			$cat = $row['CategoryID'];
			$nom = $row['Nominee'];
			
			$categories[$cat]["Votes"][$nom]["Chan"] = $row['Count'];
		}
		
		// Reddit votes
		$query = "SELECT `CategoryID`, `Nominee`, COUNT(*) AS `Count` FROM `votes`
					INNER JOIN `categories` ON `CategoryID` = `categories`.`ID`
					INNER JOIN `users` ON `UserID` = `SteamID`
					WHERE `Website` = 'www.reddit.com'
					GROUP BY `CategoryID`, `Nominee`
					ORDER BY `CategoryID` ASC, `Count` DESC";
		$result = $dbh->query($query);

		while ($row = $result->fetch_array()) {
			$cat = $row['CategoryID'];
			$nom = $row['Nominee'];
			
			$categories[$cat]["Votes"][$nom]["Reddit"] = $row['Count'];
		}
		
		// NULL votes
		$query = "SELECT `CategoryID`, `Nominee`, COUNT(*) AS `Count` FROM `votes`
					INNER JOIN `categories` ON `CategoryID` = `categories`.`ID`
					INNER JOIN `users` ON `UserID` = `SteamID`
					WHERE `Website` = 'NULL'
					GROUP BY `CategoryID`, `Nominee`
					ORDER BY `CategoryID` ASC, `Count` DESC";
		$result = $dbh->query($query);

		while ($row = $result->fetch_array()) {
			$cat = $row['CategoryID'];
			$nom = $row['Nominee'];
			
			$categories[$cat]["Votes"][$nom]["Null"] = $row['Count'];
		}
		
		// All other votes
		$query = "SELECT `CategoryID`, `Nominee`, COUNT(*) AS `Count` FROM `votes`
					INNER JOIN `categories` ON `CategoryID` = `categories`.`ID`
					INNER JOIN `users` ON `UserID` = `SteamID`
					WHERE `Website` != 'NULL' AND `Website` != 'boards.4chan.org'
					AND `Website` != 'www.reddit.com'
					GROUP BY `CategoryID`, `Nominee`
					ORDER BY `CategoryID` ASC, `Count` DESC";
		$result = $dbh->query($query);

		while ($row = $result->fetch_array()) {
			$cat = $row['CategoryID'];
			$nom = $row['Nominee'];
			
			$categories[$cat]["Votes"][$nom]["Other"] = $row['Count'];
		}
		
		$catTPL = array();
		foreach ($categories as $key => $data) {
			$tempData = array("Name" => $data['Name'], "Votes" => array());
			foreach ($data['Votes'] as $id => $count) {
				$tempData["Votes"][] = array("ID" => $id, "All" => $count["All"],
					"Filtered" => $count["Filtered"], "Chan" => $count["Chan"],
					"Reddit" => $count["Reddit"], "Null" => $count["Null"],
					"Other" => $count["Other"]);
			}
			
			$catTPL[] = $tempData;
		}
		
		$tpl->set("categories", $catTPL);
	
	}
	
	$tpl->set("categoryVotes", isset($_GET['categoryVotes']));
	$tpl->set("suggestionBox", isset($_GET['suggestionBox']));
	$tpl->set("nominations", isset($_GET['nominations']));
	$tpl->set("votes", isset($_GET['votes']));
	$tpl->set("votesTable", isset($_GET['votesTable']));
	
}

$tpl->set("denied", $denied);
$tpl->set("results", true);

fetch();

?>
