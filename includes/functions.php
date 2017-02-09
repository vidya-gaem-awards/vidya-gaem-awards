<?php
function convertSteamID($communityID) {
    $steamID = bcsub((string)$communityID, "76561197960265728");
	$steamID = $steamID / 2;
	if ($steamID == round($steamID)) {
		$steamID = "STEAM_0:0:".$steamID;
	} else {
		$steamID = "STEAM_0:1:".floor($steamID);
	}
    return $steamID;
}

function fetch($filename = false) {
    global $tpl;
    
    if (!$filename) {
        $filename = basename($_SERVER["SCRIPT_NAME"], ".php");
	}
	
	$tpl->set('content', $tpl->fetch(__DIR__."/../templates/$filename.tpl"));
	echo $tpl->fetch(__DIR__.'/../templates/master.tpl');
}

function getAPIinfo($communityID) {
	global $APIkey;
	
	$result = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$APIkey&steamids=$communityID");
	$json = json_decode($result);
	return $json->response->players[0];
}

function canDo($privilege) {
	global $canDo;
	
	if (isset($canDo["admin"])) {
		return true;
	}
	
	if (isset($canDo[$privilege])) {
		return true;
	} else {
		return false;
	}
}

function userInput($input, $mysql = true) {
	$output = $input;
	if ($mysql) {
		$output = mysql_real_escape_string($output);
	}
	$output = str_replace(array("<", ">", '"'), array("&lt;", "&gt;", "&quot;"), $output);
	$output = trim($output);
	return $output;
}

function storeMessage($type, $string, $value = null) {
	$_SESSION['message'] = array($type, $string);
	if ($value !== NULL) {
		$_SESSION['message'][2] = $value;
	}
}

function refresh() {
	header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
}

function action($action, $firstID = false, $secondID = false) {
	global $ID;
	
	$page = explode("/", $_SERVER['SCRIPT_NAME']);
	$page = substr($page[count($page) - 1], 0, -4);
	
	if (!$firstID) {
		$firstID = 'NULL';
	} else {
		$firstID = "'$firstID'";
	}
	
	if (!$secondID) {
		$secondID = 'NULL';
	} else {
		$secondID = "'$secondID'";
	}
	
	$query = "INSERT INTO `actions` (`UserID`, `Timestamp`, `Page`, `Action`, `SpecificID1`, `SpecificID2`)";
	$query .= " VALUES ('$ID', NOW(), '$page', '$action', $firstID, $secondID)";
	mysql_query($query);
}
?>
