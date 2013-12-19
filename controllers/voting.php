<?php
$tpl->set("title", "Voting");

if ($SEGMENTS[1] == "results") {
  $CUSTOM_TEMPLATE = "results";
  require("voting-results.php");
  return;
}

date_default_timezone_set('America/New_York');
$current = time();
$start = strtotime("2013-01-01 00:00:00");
$finish = strtotime("2013-01-15 01:00:00");

$voteText = "";

$votingNotYetOpen = false;
$votingEnabled = false;
$votingConcluded = false;

if ($current < $start) {
  $seconds = $start - $current;
} else if ($current < $finish) {
  $seconds = $finish - $current;
} else {
  $seconds = 0;
}

$minutes = floor($seconds / 60);
$hours = floor($minutes / 60);
$days = floor($hours / 24);

$seconds -= $minutes * 60;
$minutes -= $hours * 60;
$hours -= $days * 24;

$dp = ((int)$days === 1 ? "" : "s");
$hp = ((int)$hours === 1 ? "" : "s");
$mp = ((int)$minutes === 1 ? "" : "s");
$sp = ((int)$seconds === 1 ? "" : "s");

if ($current > $start && $current < $finish) {
  $votingEnabled = true;
  
  if ($days >= 1) {
    $difference = number_format(round($days+($hours/24), 1), 1)." days";
  } else if ($hours >= 1) {
    $difference = round($hours+($minutes/60), 1)." hours";
  } else if ($minutes >= 1) {
    $difference = "$minutes minute".$mp;
  } else {
    $difference = "$seconds second".$sp;
  }
    
  $voteText = "Voting is now open! You have $difference left to vote.";
  
} else if ($current < $start) {
  $votingNotYetOpen = true;
  
  if ($days >= 1) {
    $difference = "$days day".$dp." and $hours hour".$hp;
  } else if ($hours >= 1) {
    $difference = "$hours hour".$hp." and $minutes minute".$mp;
  } else if ($minutes >= 1) {
    $difference = "$minutes minute".$mp;
  } else {
    $difference = "$seconds second".$sp;
  }
  
  $voteText = "Voting will open in $difference";
} else {
  $votingConcluded = true;
  $voteText = "Voting is now closed.";
}
$tpl->set("voteText", $voteText);

$lastVotes = array();

// Look up which categories have already been voted on by this user
$completedCategories = array();
$query = "SELECT * FROM `votes` WHERE `UniqueID` = \"$uniqueID\"";
$result = mysql_query($query);
while ($row = mysql_fetch_array($result)) {
  $completedCategories[$row['CategoryID']] = json_decode($row['Preferences'], true);
}

$query = "SELECT * FROM `categories` WHERE `Enabled` = 1 ORDER BY `Order` ASC";
$result = mysql_query($query);

$categories = array();
while ($row = mysql_fetch_array($result)) {
  if ($SEGMENTS[1] == $row['ID']) {
    $row['Active'] = true;
  } else {
    $row['Active'] = false;
  }
  if (isset($completedCategories[$row['ID']])) {
    $row['Completed'] = true;
  } else {
    $completedCategories[$row['ID']] = array();
    $row['Completed'] = false;
  }
  $categories[] = $row;
}

$tpl->set("categories", $categories);

$category = false;
$nominees = array();
if ($SEGMENTS[1]) {
  $cat = mysql_real_escape_string($SEGMENTS[1]);
  $query = "SELECT * FROM `categories` WHERE `ID` = \"$cat\" AND `Enabled` = 1";
  $result = mysql_query($query);
  
  if (mysql_num_rows($result) == 1) {
    
    $row = mysql_fetch_array($result);
    $category = $row;   
    
    $query = "SELECT * FROM `nominees` WHERE `CategoryID` = \"$cat\" ORDER BY `Name` ASC";
    $result = mysql_query($query);
    $nominees = array();
    $count = 0;
    while ($row = mysql_fetch_array($result)) {
      $count++;
    
      $row['Background'] = "";
    
      $prefixes = array(strtolower($cat)."-", "");
      if (empty($row['Image'])) {
        $row['Image'] = "/public/nominees/{$row['NomineeID']}.png";
      } else {
        $row['Image'] = $row['Image'];
      }

      $row['Order'] = $count;
      
      $nominees[] = $row;
    }
    
    $js = "[null";
    foreach ($completedCategories[$cat] as $nominee) {
      $js .= ", \"$nominee\"";
    }
    $js .= "]";
      
    $tpl->set("lastVotes", $js);
    
  } else {
    $_SESSION['votingCode'] = $SEGMENTS[1];
    $code = mysql_real_escape_string($SEGMENTS[1]);
    $query = "INSERT IGNORE INTO `voting_codes` (`Code`, `UserID`) VALUES (\"$code\", \"$uniqueID\")";
    mysql_query($query);
    header("Location: http://$DOMAIN/voting");
  } 
  
}

$dumbloop = array();
foreach ($nominees as $key => $null) {
    $dumbloop[] = $key+1;
}

if (!$nominees) {
  $nominees = false;
}

$tpl->set("dumbloop", $dumbloop);

$tpl->set("nominees", $nominees);
$tpl->set("category", $category);

$tpl->set("votingEnabled", $votingEnabled);
$tpl->set("votingNotYetOpen", $votingNotYetOpen);
$tpl->set("votingConcluded", $votingConcluded);

$tpl->set("special", canDo("special"));

$tpl->set("navbar", false);

?>