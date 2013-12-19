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
  global $STEAM_API_KEY;
  
  $result = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$STEAM_API_KEY&steamids=$communityID");
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

function storeMessage($type, $string, $value = null) {
  $_SESSION['message'] = array($type, $string);
  if ($value !== NULL) {
    $_SESSION['message'][2] = $value;
  }
}

function refresh() {
  header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
}

function action($action, $firstID = null, $secondID = null) {
  global $ID, $PAGE, $mysql;
  
  $query = "INSERT INTO `actions` (`UserID`, `Timestamp`, `Page`, `Action`,
            `SpecificID1`, `SpecificID2`) VALUES(?, NOW(), ?, ?, ?, ?)";
  $stmt = $mysql->prepare($query);
  $stmt->bind_param('sssss', $ID, $PAGE, $action, $firstID, $secondID);
  $stmt->execute();
}

function return_json($result, $info = TRUE, $extraData = array()) {
  echo json_encode(array_merge($extraData, array($result => $info)));
  exit;
}