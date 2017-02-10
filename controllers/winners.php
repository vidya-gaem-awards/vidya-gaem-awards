<?php
$tpl->set("title", "Winners");

$results = array();

$query = "SELECT * FROM `categories` WHERE `Enabled` = 1 ORDER BY `Order` ASC";
$result = $mysql->query($query);
$categories = array();
while ($row = $result->fetch_assoc()) {
  $categories[$row['ID']] = array("ID" => $row['ID'], "Name" => $row['Name'], "Subtitle" => $row['Subtitle'], "Rankings" => array(), "Nominees" => array());
}

$query = "SELECT * FROM `nominees`";
$result = $mysql->query($query);
$nominees = array();
while ($row = $result->fetch_assoc()) {
  if (!isset($categories[$row['CategoryID']])) {
    continue;
  }
  $categories[$row['CategoryID']]["Nominees"][$row['NomineeID']] = $row['Name'];
}

$ranks = array("1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th", "11th", "12th", "13th", "14th", "15th");

$query = "SELECT * FROM `winner_cache` WHERE `Filter` = \"05combined2\"";
$result = $mysql->query($query);
while ($row = $result->fetch_assoc()) {
  $rankings = array_values(json_decode($row['Results'], true));
  foreach ($rankings as $key => &$value) {
    $value = $ranks[$key] . ". " . $categories[$row['CategoryID']]["Nominees"][$value];
  }
  $theOthers = implode(", ", array_slice($rankings, 5));
  $rankings = array_slice($rankings, 0, 5);
  $rankings[] = $theOthers;
  $categories[$row['CategoryID']]["Rankings"] = $rankings;
}

$tpl->set("categories", array_values($categories));
?>
