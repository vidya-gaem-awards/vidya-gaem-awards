<?php
// Sanity checking
if (!canDo("add-video-game")) {
  return_json("error", "You don't have access to this feature.");
} else if (trim($_POST['Game']) == "") {
  return_json("error", "You forgot the actual name of the game");
} else if (count($_POST) === 1) {
  return_json("error", "You must select at least one platform.");
}

$allPlatforms = array("PC","PS3","PS4","PSV","PSN","360","XB1","XBLA","Wii",
  "WiiU","WiiWare","3DS","Ouya","Mobile");

$game = $_POST['Game'];
$keys = $values = array();

foreach ($_POST as $key => $value) {
  if (in_array($key, $allPlatforms)) {
    $keys[] = "`$key`";
    $values[] = 1;
  }
}

$keys = implode(", ", $keys);
$values = implode(", ", $values);

$query = "INSERT INTO `2010_releases` (`Game`, $keys) VALUES(?, $values)";
$stmt = $mysql->prepare($query);
$stmt->bind_param('s', $game);
$result = $stmt->execute();

if (!$result) {
  error_log("MySQL error: ".$stmt->error);
  return_json("error", "A MySQL error occurred.");
}

return_json("success", $game);