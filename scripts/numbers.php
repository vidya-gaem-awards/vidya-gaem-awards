<?php
chdir(dirname(__FILE__));
function timer() {
  global $timeStart;
  
  $timeEnd = microtime(true);
  return round($timeEnd - $timeStart, 2);
}

error_reporting(E_ALL);
set_time_limit(0);

require_once("../includes/config.php");

$mysqli = new Mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_DB);

$timeStart = microtime(true);

// Step 1. Get a list of voters
$query = "SELECT DISTINCT `UniqueID` FROM `votes` LIMIT 100000";
$result = $mysqli->query($query);

$voters = array();

while ($row = $result->fetch_assoc()) {
  $voters[$row['UniqueID']] = array(
    "Codes" => array(),
    "Notes" => array(),
    "Referrers" => array());
}

$result->free();

echo "Step 1 passed: ".timer()."\n";

// Step 2. Check voting codes
$query = "SELECT * FROM `voting_codes`";
$result = $mysqli->query($query);

while ($row = $result->fetch_assoc()) {
  if (!isset($voters[$row['UserID']])) {
    continue;
  }
  $voters[$row['UserID']]['Codes'][] = $row['Code'];
}

echo "Step 2 passed: ".timer()."\n";

$result->free();

flush();
ob_flush();

// Step 3. Check referrers
function startsWith($haystack, $needle) {
  return substr($haystack, 0, strlen($needle)) == $needle;
}

$query = "SELECT `UniqueID`, `Timestamp`, `Refer` FROM `access` WHERE (`Refer` NOT LIKE \"http://%vidyagaemawards.com%\" OR `Refer` IS NULL) AND `UniqueID` != \"\"";
$query .= " ORDER BY `UniqueID` ASC, `Timestamp` ASC";
$result = $mysqli->query($query);

while ($row = $result->fetch_assoc()) {
  if (!isset($voters[$row['UniqueID']])) {
    continue;
  }
  $refer = $row['Refer'];
  if (startsWith($refer, "http:")) {
    $refer = substr($refer, 7);
  } else if (startsWith($refer, "https:")) {
    $refer = substr($refer, 8);
  }
  
  if (startsWith($refer, "www.")) {
    $refer = substr($refer, 4);
  }
  $voters[$row['UniqueID']]['Referrers'][] = $refer;
}

$result->free();

echo "Step 3 passed: ".timer()."\n";
flush();
ob_flush();

// Step 4. Begin the processing
$badSites = array("reddit.com" => -50, "t.co" => -40, "neogaf.com" => -30, "facepunch.com" => -20);

foreach ($voters as $ID => &$info) {
  $number = 0;
  
  // If user has a voting code
  if (count($info['Codes']) > 0) {
    $number += 202;
    $info['Notes'][] = "Has voting code";
  }
  
  $shitSite = false;
  $shitSiteValue = 0;
  $fromChan = false;
  $null = false;
  $otherSite = false;
  $google = false;
  
  foreach ($info['Referrers'] as $refer) {
    if (empty($refer)) {
      $null = true;
    } else if (startsWith($refer, "boards.4chan.org/v/")) {
      $fromChan = true;
    } else if (startsWith($refer, "google.")) {
      $google = true;
    } else {
      foreach ($badSites as $site => $value) {
        if (startsWith($refer, $site)) {
          $shitSiteValue = min($shitSiteValue, $value);
          $shitSite = $site;
        }
      }
      $otherSite = true;
    }
  }
  
  if (count($info['Referrers']) === 0) {
    $number += 5;
    $info['Notes'][] = "No referrers recorded";
  } else if ($fromChan && !$otherSite) {
    $number += 101;
    $info['Notes'][] = "From /v/ only";
  } else if ($null && !$otherSite) {
    $number += 25;
    $info['Notes'][] = "Null referrer only";
  } else if ($shitSite) {
    $number += $shitSiteValue;
    $info['Notes'][] = "Site blacklist: $shitSite";
  } else if (!$null && $otherSite) {
    $number += -10;
    $info['Notes'][] = "Other site only";
  } else if (!$null && $google) {
    $number += -5;
    $info['Notes'][] = "Google search only";
  }
  
  if ($number == 0) {
    $number = 1;
  }
  $info['Number'] = $number;
}

$numberTotals = array();
foreach ($voters as $info) {
  if (!isset($numberTotals[$info['Number']])) {
    $numberTotals[$info['Number']] = 0;
  }
  $numberTotals[$info['Number']]++;
}

echo "Step 4 passed: ".timer()."\n";
flush();
ob_flush();

// Step 5. Update the values in the database
$query = "UPDATE `votes` SET `Number` = ? WHERE `UniqueID` = ?";
$stmt = $mysqli->prepare($query);
if (!$stmt) {
  die($mysqli->error);
}
$stmt->bind_param('ds', $_number, $_id);

$count = 0;
foreach ($voters as $ID => $info) {
  $count++;
  $_id = $ID;
  $_number = $info['Number'];
  $stmt->execute();
  if ($count % 1000 == 0) {
    echo "Processing record $count... ".timer()."\n";
    flush();
    ob_flush();
  }
}
$stmt->close();

echo "Step 5 passed: ".timer()."\n\n";

file_put_contents("voters.json", json_encode($voters));

?>
