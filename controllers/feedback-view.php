<?php
$tpl->set("title", "Viewer Feedback");

### GENERAL RATING

$query = "SELECT COUNT(*) as `Count`, `GeneralRating` FROM `feedback` WHERE `GeneralRating` != 0 GROUP BY `GeneralRating` ORDER BY `GeneralRating` DESC";
$result = mysql_query($query);

$general = array();
$maxCount = 0;
$total = 0;
$average = 0;
while ($row = mysql_fetch_assoc($result)) {
  $general[] = array(
    "rating" => $row['GeneralRating'] + 5,
    "bar" => $row['GeneralRating'] + 5,
    "count" => number_format($row['Count']),
    "count-int" => $row['Count'],
    "width" => 0,
  );
  $maxCount = max($maxCount, $row['Count']);
  $total += $row['Count'];
  $average += ($row['GeneralRating'] + 5) * $row['Count'];
}

foreach ($general as $key => $values) {
  $general[$key]["width"] = ceil(($values["count-int"] / $maxCount) * 300);
}

$average = round($average / $total, 2);

$tpl->set("general", $general);
$tpl->set("generalTotal", number_format($total));
$tpl->set("generalAverage", $average);


### CEREMONY RATING

$query = "SELECT COUNT(*) as `Count`, `CeremonyRating` FROM `feedback` WHERE `CeremonyRating` != 0 GROUP BY `CeremonyRating` ORDER BY `CeremonyRating` DESC";
$result = mysql_query($query);

$ceremony = array();
$maxCount = 0;
$total = 0;
$average = 0;
while ($row = mysql_fetch_assoc($result)) {
  $ceremony[] = array(
    "rating" => $row['CeremonyRating'] + 5,
    "bar" => $row['CeremonyRating'] + 5,
    "count" => number_format($row['Count']),
    "count-int" => $row['Count'],
    "width" => 0,
  );
  $maxCount = max($maxCount, $row['Count']);
  $total += $row['Count'];
  $average += ($row['CeremonyRating'] + 5) * $row['Count'];
}

foreach ($ceremony as $key => $values) {
  $ceremony[$key]["width"] = ceil(($values["count-int"] / $maxCount) * 300);
}

$average = round($average / $total, 2);

$tpl->set("ceremony", $ceremony);
$tpl->set("ceremonyTotal", number_format($total));
$tpl->set("ceremonyAverage", $average);


function implying($str) {
  if ($str[0] == ">") {
    $str = "<span class='implying'>$str</span>";
  }
  $str = str_replace("\n", "<br />", $str);
  return $str;
}

$tpl->set("header", false);

$items = array();

if (!isset($_GET['sort'])) {
  $_GET['sort'] = false;
}

if ($_GET['sort'] == "length") {
  $sort = "CHAR_LENGTH(`Text`) DESC";
} else {
  $sort = "`ID` DESC";
}

$categories = array(
  "best" => array("BestThing", "The Best Parts"),
  "worst" => array("WorstThing", "The Worst Parts"),
  "comments" => array("OtherComments", "General Comments"),
  "questions" => array("Questions", "Questions for the /v/GA team")
);

if (in_array($SEGMENTS[2], array_keys($categories))) {
  $query = "SELECT `ID`, `Timestamp`, `{$categories[$SEGMENTS[2]][0]}` AS `Text` FROM `feedback` WHERE `{$categories[$SEGMENTS[2]][0]}` != '' ORDER BY $sort";
  $result = mysql_query($query);

  while ($row = mysql_fetch_assoc($result)) {
    $items[] = array("ID" => $row['ID'], "Text" => implying($row['Text']));
  }
  
  $header = "{$categories[$SEGMENTS[2]][1]} <small>".count($items)." responses ";
  if ($_GET['sort'] == "length") {
    $header .= "<a href='/feedback/view/{$SEGMENTS[2]}'>sort by time submitted</a>";
  } else {
    $header .= "<a href='/feedback/view/{$SEGMENTS[2]}?sort=length'>sort by comment length</a>";
  }
  $header .= "</small>";
  $tpl->set("header", $header);
  
  $output = "";
  foreach ($items as $item) {
    $output .= "<li><a href='/feedback/view/{$item['ID']}'>{$item['ID']}</a>: {$item['Text']}</li>\n";
  }
  $tpl->set("output", $output);
}

$tpl->set("unique", false);

if ($SEGMENTS[2]) {
  $feedbackID = $SEGMENTS[2];
  if (!ctype_digit($feedbackID)) {
    return;
  }
  
  $query = "SELECT * FROM `feedback` WHERE `ID` = $feedbackID";
  $result = mysql_query($query);
  $row = mysql_fetch_assoc($result);
  
  $tpl->set("unique", true);
  $tpl->set("feedbackID", $row['ID']);
  $tpl->set("submissionDate", date("F jS Y, H:i:s", strtotime($row['Timestamp'])));
  $tpl->set("general", $row['GeneralRating'] == 0 ? "--" : $row['GeneralRating'] + 5);
  $tpl->set("ceremony", $row['CeremonyRating'] == 0 ? "--" : $row['CeremonyRating'] + 5);
  $tpl->set("email", $row['Email'] ? $row['Email'] : "<em>not provided</em>");
  
  $columns = array("BestThing", "WorstThing", "OtherComments", "Questions");
  foreach ($columns as $columnName) {
    $text = trim(str_replace("\n", "<br>", $row[$columnName]));
    $tpl->set($columnName, $text ? $text : "<em>left blank</em>");
  }
}
?>