<?php
$stats = array(
  "category feedback" => 47663,
  "nominations made" => 13433, 
  "total votes" => 438545,
  "unique users" => 30221,
  "emails received" => 378,
  "steam group members" => 1430,
  "steam discussion posts" => 397,
  "page views" => 1772723,
);

$tpl->set("navbar", false);
$tpl->set("title", "2013 Vidya Gaem Awards");

$stats2 = array();

foreach ($stats as $stat => $number) {
  $stat = str_replace(" ", "&nbsp;", $stat);
  $stats2[] = "<span class='stat'>$stat: ".number_format($number)."</span>";
}

$timezonesLeft = array(
  "Honolulu" => "Pacific/Honolulu",
  "Anchorage" => "America/Anchorage",
  "Los Angeles (PDT)" => "America/Los_Angeles",
  "Denver (MDT)" => "America/Denver",
  "Chicago (CDT)" => "America/Chicago",
  "4chan Time (EDT)" => "America/New_York",
  "Rio de Janeiro" => "America/Sao_Paulo",
  "London (GMT)" => "Europe/London",
);
$timezonesRight = array(
  "Paris (CET)" => "Europe/Paris",
  "Athens (EET)" => "Europe/Athens",
  "Moscow" => "Europe/Moscow",
  "Singapore" => "Asia/Singapore",
  "Japan Time" => "Asia/Tokyo",
  "Brisbane (AEST)" => "Australia/Brisbane",
  "Sydney (AEDT)" => "Australia/Sydney",
  "Auckland" => "Pacific/Auckland",
);

$statHTML = implode(" / ", $stats2); 

// In 4chan time (UTC-4, daylight savings!)
$timeStr = "Fri, 14 Mar 2014 22:00:00 -0400";

$date = new DateTime($timeStr);

$timezonesLeftTpl = array();
$timezonesRightTpl = array();
foreach ($timezonesLeft as $name => $timezone) {
  $date->setTimeZone(new DateTimeZone($timezone));
  $offset = $date->format("P");
  $time = $date->format("l H:i");
  $time = $date->format("D M jS, H:i");
  $timezonesLeftTpl[] = array("Name" => $name, "Time" => $time, "Offset" => $offset);
}
foreach ($timezonesRight as $name => $timezone) {
  $date->setTimeZone(new DateTimeZone($timezone));
  $offset = $date->format("P");
  $time = $date->format("l H:i");
  $time = $date->format("D M jS, H:i");
  $timezonesRightTpl[] = array("Name" => $name, "Time" => $time, "Offset" => $offset);
}

// Don't forget to set the timezone back to default
$date->setTimeZone(new DateTimeZone("America/New_York"));

$tpl->set("timezonesLeft", $timezonesLeftTpl);
$tpl->set("timezonesRight", $timezonesRightTpl);

$countdown = strtotime($timeStr);

$websiteLink = "http://timeanddate.com/worldclock/fixedtime.html?msg=2013+Vidya+Gaem+Awards&iso=".$date->format("Ymd\THi")."&p1=179&sort=1";
$tpl->set("websiteLink", $websiteLink);

if ($countdown > time()) {
  $tpl->set("countdown", date("D, j M Y H:i:s \U\TCO", $countdown));
} else {
  $tpl->set("countdown", false);
}

// hi remove this when date is set
$tpl->set("dateset", true);

$tpl->set("serverDate", date("D, j M Y H:i:s \U\TCO"));
$tpl->set("timezone", date("O"));
$tpl->set("statHTML", $statHTML);
?>
