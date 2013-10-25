<?php
$tpl->set("title", "Volunteer Applications");

$query = "SELECT * FROM `applications` ORDER BY `Timestamp` DESC";
$result = mysql_query($query);

$applications = array();
while ($row = mysql_fetch_assoc($result)) {
  $timestamp = strtotime($row['Timestamp']);
  $timestamp = date("M j, Y H:i:s", $timestamp);
  $row['Timestamp'] = $timestamp;
  $applications[] = $row;
}

$tpl->set("applications", $applications);
?>