<?php
$tpl->set('title', "Vidya in 2013");

$query = "SELECT * FROM `2010_releases` ORDER BY `Game` ASC";
$result = $mysql->query($query);

$games = array();

while ($row = $result->fetch_assoc()) {
  
  $others = array();

  foreach ($row as $key => $value) {
    if ($key == "Game") {
      $wp = urlencode(str_replace(" ", "_", $value));
      $row[$key] = "<a href='http://en.wikipedia.org/wiki/$wp'>$value</a>";
      continue;
    }

    if ($key == "Notable") {
      $row[$key] = $value ? "notable" : "";
      continue;
    }
    
    if ($value == 1) {
      $class = "c-".strtolower($key);
      $row[$key] = "<strong class='yes $class'>✓</strong>";
      if ($key == "WiiWare" || $key == "PSN" || $key == "XBLA" || $key == "Ouya") {
        $others[] = $key;
      }
    } else {
      $row[$key] = "";
    }
  
  }
  $row["Others"] = implode(", ", $others);
  $games[] = $row;

}

$tpl->set('games', $games);
$tpl->set('adminTools', canDo("add-video-game"));