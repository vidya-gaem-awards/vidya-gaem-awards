<?php
$tpl->set("title", "News");

$query = "SELECT * FROM `news` ORDER BY `Timestamp` DESC";
$result = $mysql->query($query);

$news = array();
$newsCount = -1;
$currentDate = new DateTime('now');

while ($row = $result->fetch_assoc()) {
  $newsCount++; 
  
  $postDate = new DateTime($row['Timestamp']);
  
  $displayDate = $postDate->format("M j, Y \a\\t g:ia");
  $news[] = array("Date" => $displayDate,
    "Text" => $row['Text']);
}

$tpl->set("news", $news);

$timezone = date("T: P \U\\T\C");
$tpl->set("timezone", $timezone);

$currentTime = date("g:i A \o\\n F jS");
$tpl->set("currentTime", $currentTime);
?>