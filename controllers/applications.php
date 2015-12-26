<?php
$tpl->set("title", "Volunteer Applications");

$query = "SELECT * FROM `applications` ORDER BY `Timestamp` DESC";
$result = $mysql->query($query);

$applications = array();
while ($row = $result->fetch_assoc()) {
    $timestamp = strtotime($row['Timestamp']);
    $timestamp = date("M j, Y H:i:s", $timestamp);
    $row['Timestamp'] = $timestamp;
    $applications[] = $row;
}

$tpl->set("applications", $applications);
