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

function getAPIinfo($communityID) {
	global $APIkey;
	
	$result = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$APIkey&steamids=$communityID");
	$json = json_decode($result);
	return $json->response->players[0];
}

function canDo($privilege) {
	global $USER_RIGHTS, $USER_GROUPS;
	
	if (in_array("admin", $USER_GROUPS)) {
		return true;
	}
	
	return in_array($privilege, $USER_RIGHTS);
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
	global $ID, $PAGE;
		
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
	$query .= " VALUES ('$ID', NOW(), '$PAGE', '$action', $firstID, $secondID)";
	mysql_query($query);
}

function debug_query($query) {

	$result = mysql_query($query);
	if (!$result) {
		report_error($query);
	}
	return $result;
}

function report_error($query) {
        
	$error = mysql_error();
	echo "<pre><strong>A MySQL error has occurred.</strong>\n";
		
	echo "<strong>Query:</strong> $query\n";
	echo "<strong>Error:</strong> $error\n";
	echo "</pre>";
	
	#die();
}

function generateUUID() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
?>
