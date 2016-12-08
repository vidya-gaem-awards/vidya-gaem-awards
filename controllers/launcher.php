<?php
$stats = array(
  "award feedback" => 47663,
  "nominations made" => 13433,
  "total votes" => 438545,
  "unique users" => 30221,
  "emails received" => 378,
  "steam group members" => 1430,
  "steam discussion posts" => 397,
  "page views" => 1772723,
);
$stats2 = array();

foreach ($stats as $stat => $number) {
    $stat = str_replace(" ", "&nbsp;", $stat);
    $stats2[] = "<span class='stat'>$stat: ".number_format($number)."</span>";
}

$statHTML = implode(" / ", $stats2);
$tpl->set("statHTML", $statHTML);
