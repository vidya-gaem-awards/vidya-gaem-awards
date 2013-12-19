<?php

$tpl->set("title", "Home");

$query = "SELECT * FROM `news` WHERE `Visible` = 1 AND `Timestamp` < NOW() ORDER BY `Timestamp` DESC LIMIT 5";
$result = $mysql->query($query);

$news = array();
$newsCount = -1;
$currentDate = new DateTime('now');

while ($row = $result->fetch_assoc()) {
  $newsCount++; 
  
  $postDate = new DateTime($row['Timestamp']);
  
  $dateDiff = $postDate->diff($currentDate);
  
  // Less then 3 days old is considered new
  // Only the first 2 posts can be considered new
  $new = ($dateDiff->days < 3) && ($newsCount < 2);
  
  // Older then a week is considered old
  // The first post can never be old
  $class = ($dateDiff->days >= 7 && $newsCount > 0) ? "news-old" : "";
  
  $displayDate = $postDate->format("M j, Y");
  $news[] = array("Date" => $displayDate, "New" => $new,
    "Text" => $row['Text'], "Class" => $class);
}

$tpl->set("news", $news);

$tpl->set("APPLICATIONS_OPEN", $APPLICATIONS_OPEN);