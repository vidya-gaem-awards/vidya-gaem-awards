<?php
error_reporting(E_ALL);
set_time_limit(0);

require_once("../bootstrap.php");

if (!file_exists("games.csv")) {
  die("Forgetting something?");
}

$mysqli = new Mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

$search = array(" ", "Win", "Mac", "Lin", "iOS", "Droid", "WP", "X360", "XBO", "PSVita");
$replace = array("", "PC", "PC", "PC", "Mobile", "Mobile", "Mobile", "360", "XB1", "PSV");
$delete = array("PS2", "NDS", "DC");

$allPlatforms = array("PC","PS3","PS4","PSV","PSN","360","XB1","XBLA","Wii",
  "WiiU","WiiWare","3DS","Ouya","Mobile");

$games = array();

$csv = file("games.csv");
foreach ($csv as $line) {
  $array = str_getcsv($line);
  $game = $array[0];
  if (!isset($games[$game])) {
    $games[$game] = array_fill_keys($allPlatforms, 0);
  }

  $platforms = explode(",", str_replace($search, $replace, $array[1]));
  $platforms = array_diff($platforms, $delete);
  foreach ($platforms as $platform) {
    $games[$game][$platform] = 1;
  }
}

$keys = array();
foreach ($allPlatforms as $platform) {
  $keys[] = "`$platform`";
}
$keys = implode(",", $keys);
foreach ($games as $game => $platforms) {
  $values = array();
  foreach ($platforms as $bool) {
    $values[] = $bool;
  }
  $values = implode(",", $values);
  $game = $mysqli->escape_string($game);
  $query = "INSERT INTO `2010_releases` (`Game`, $keys) VALUES (\"$game\", $values)";
  $result = $mysqli->query($query);
  echo $game . "\n";
  if ($result->error) {
    echo $result->error."<br>";
  }
}

echo "All done.";
