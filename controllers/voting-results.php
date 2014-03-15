<?php
$tpl->set("title", "Voting results");

$results = array();

$filterNames = array(
  "00none" => array("No filtering", "moccasin"),
  "01allofv" => array("All of /v/", "#C1FFC1"),
  "02voting+v" => array("/v/ with voting code", "honeydew"),
  "03v" => array("/v/ (no voting code)", "honeydew"),
  "05combined1" => array("All of /v/ + all of NULL", "#E0FFFF"),
  "05combined2" => array("All of /v/ + NULL with code", "lightyellow"),
  "06voting+null" => array("NULL with voting code", "aliceblue"),
  "07null" => array("NULL (no voting code)", "white"),
  "08facepunch" => array("Facepunch", "mistyrose"),
  "09neogaf" => array("NeoGAF", "mistyrose"),
  "10twitter" => array("Twitter", "mistyrose"),
  "11reddit" => array("Reddit", "mistyrose"),
);

if ($SEGMENTS[2] == "pairwise") {
  $CUSTOM_TEMPLATE = "results-pairwise";
  require_once("voting-results-pairwise.php");
  return;
}

$tpl->set("all", $SEGMENTS[2] == "all");

$query = "SELECT * FROM `categories` WHERE `Enabled` = 1 ORDER BY `Order` ASC";
$result = mysql_query($query);
$categories = array();
while ($row = mysql_fetch_assoc($result)) {
  $categories[$row['ID']] = array("Name" => $row['Name'], "Subtitle" => $row['Subtitle'], "Filters" => array(), "Nominees" => array());
}

$query = "SELECT * FROM `nominees`";
$result = mysql_query($query);
$nominees = array();
while ($row = mysql_fetch_assoc($result)) {
  if (!isset($categories[$row['CategoryID']])) {
    continue;
  }
  $categories[$row['CategoryID']]["Nominees"][$row['NomineeID']] = $row;
}

$query = "SELECT * FROM `winner_cache` ORDER BY `Filter` ASC";
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
  $rankings = array_values(json_decode($row['Results'], true));
  $steps = json_decode($row['Steps'], true);
  foreach ($rankings as $key => &$value) {
    $value = $categories[$row['CategoryID']]["Nominees"][$value]["Name"];
    if ($key == 0) {
      $value = "<span style='font-weight: bold; color: green;'>$value</span>";
    }
  }
  if ($SEGMENTS[2] != "all") {
    $rankings = array_slice($rankings, 0, 5);
  }
  $categories[$row['CategoryID']]["Filters"][] = array(
    "FilterName" => $filterNames[$row['Filter']][0],
    "FilterNameSafe" => preg_replace("/[^A-Za-z]/", '', strtolower($filterNames[$row['Filter']][0])),
    "Rankings" => $rankings,
    "VoteCount" => number_format($row['Votes']),
    "Colour" => $filterNames[$row['Filter']][1]
  );
}

$tpl->set("categories", array_values($categories));
?>
