<?php
use VGA\Utils;

// Sanity checking
if (!canDo("add-video-game")) {
    Utils::returnJSON("error", "You don't have access to this feature.");
} elseif (trim($_POST['Game']) == "") {
    Utils::returnJSON("error", "You forgot the actual name of the game");
} elseif (count($_POST) === 1) {
    Utils::returnJSON("error", "You must select at least one platform.");
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
    Utils::returnJSON("error", "A MySQL error occurred.");
}

Utils::action("add-video-game", $game);

Utils::returnJSON("success", $game);
