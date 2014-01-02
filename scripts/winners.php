<?php
$timeStart = microtime(true);
function timer($msg) {
  global $timeStart;
  
  $timeEnd = microtime(true);
  echo round($timeEnd - $timeStart, 2).": $msg\n";
}

require_once("../includes/config.php");

$mysqli = new Mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_DB);

// Remove all existing data
$mysqli->query("TRUNCATE TABLE `winner_cache`");

// Start by getting a list of categories and all the nominees.
$categories = array();
$nominees = array();

$query = "SELECT * FROM `categories` WHERE `Enabled` = 1 ORDER BY `Order` ASC";
$result = $mysqli->query($query);
while ($row = $result->fetch_assoc()) {
  $categories[$row['ID']] = $row;
  $nominees[$row['ID']] = array();
}

timer("Categories loaded.");

$query = "SELECT * FROM `nominees`";
$result = $mysqli->query($query);

$nominees = array();
while ($row = $result->fetch_assoc()) {
  $nominees[$row['CategoryID']][$row['NomineeID']] = $row;
}

timer("Nominees loaded.");

$filters = array(
  "00none" => "1",
  "01allofv" => "`Number` = 303 OR `Number` = 101",  // all of /v/
  "02voting+v" => "`Number` = 303",
  "03v" => "`Number` = 101",
  "05combined1" => "`Number` >= 227 OR `Number` = 25 OR `Number` = 101",  // /v/ + NULL
  "05combined2" => "`Number` >= 227 OR `Number` = 101", // /v/ + null (with voting code)
  "06voting+null" => "`Number` = 227", // null (with voting code)
  "07null" => "`Number` = 25",  // null (no voting code)
  "08facepunch" => "`Number` = -20",
  "09neogaf" => "`Number` = -30",
  "10twitter" => "`Number` = -40",
  "11reddit" => "`Number` = -50",
);

foreach ($filters as $filterName => $condition) {
  // Now we can start grabbing votes.
  $query = "SELECT `Preferences` FROM `votes` WHERE `CategoryID` = ? AND ($condition)";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param("s", $categoryID);

  $query = "INSERT INTO `winner_cache` (`CategoryID`, `Filter`, `Results`, `Steps`, `Warnings`, `Votes`) VALUES(?, ?, ?, ?, ?, ?)";
  $insertStmt = $mysqli->prepare($query);
  $insertStmt->bind_param("sssssd", $categoryID, $filterName, $jsonResults, $jsonSteps, $jsonWarnings, $voteCount);

  foreach ($categories as $categoryID => $category) {
    $jsonResults = array();
    $jsonSteps = array();
    
    // Initial run to get first place
    $stmt->execute();
    $stmt->bind_result($preferences);
    $votes = array();
    while ($stmt->fetch()) {
      if ($preferences != "[]") {
        $votes[] = json_decode($preferences, true);
      }
    }

    $candidates = $nominees[$categoryID];
    $result = runVoteProcess($candidates, $votes);
    
    $warnings = $result['Warnings'];
    
    // INSTANT RUN-OFF START
    
    /*$jsonResults[1] = $result['Winner'];
    $jsonSteps[1] = $result['Steps'];
    
    // Now to find 2nd to 5th
    for ($i = 2; $i <= 5; $i++) {
      unset($candidates[$result['Winner']]);
      foreach ($votes as $key => &$vote) {
        $index = array_search($result['Winner'], $vote);
        // This removes the winner from preferences
        // and reindexes the array starting from 1.
        if ($index !== false) {
          unset($vote[$index]);
          $vote = array_combine(range(1, count($vote)), $vote);
        }
        if (!$vote) {
          unset($votes[$key]);
        }
      }
      //print_r($votes);
      $result = runVoteProcess($candidates, $votes);
      
      $jsonResults[$i] = $result['Winner'];
      $jsonSteps[$i] = $result['Steps'];
      $warnings = array_merge($warnings, $result['Warnings']);
    }
    $voteCount = $jsonSteps[0]['VoteCount'];*/
    
    // INSTANT RUN-OFF END
    
    
    // SCHULZE START
    $jsonResults = $result["Rankings"];
    $jsonSteps = $result["Steps"];
    // SCHULZE END
    
    // All done, send it to the database
    $jsonWarnings = json_encode($warnings);
    $jsonResults = json_encode($jsonResults);
    $jsonSteps = json_encode($jsonSteps);
    $voteCount = $result["VoteCount"];
    
    $insertStmt->execute();
    
    timer("[$filterName] Category complete: $categoryID");
  }
}

$stmt->close();
$mysqli->close();

timer("Done.");

function runVoteProcess($candidates, $votes) {
  return runSchulze($candidates, $votes);
}

// $candidates should be an array of nominee rows
// $votes should be an array of decoded preferences
function runIRV($candidates, $votes) {
  $firstPref = array_combine(array_keys($candidates), array_fill(0, count($candidates), 0));
  
  $roundsRequired = count($candidates) - 1;
  $currentRound = 0;
  
  foreach ($votes as $key => $vote) {
    $firstPref[$vote[1]]++;
  }
  
  $html = "";
  $steps = array();
  $warnings = array();
  
  while ($currentRound < $roundsRequired - 1) {
      
    $currentRound++;
    
    $voteCount = count($votes);
    arsort($firstPref);
    
    // In the event of a tie, not a single fuck is given. Take a pseudo-random one.
    $lowest = array_keys($firstPref, min($firstPref));
    if (count($lowest) > 1) {
      $warnings[] = "Warning: tie in round $currentRound";
    }
    $lowest = $lowest[0];
    
    $thisRound = array("VoteCount" => $voteCount, "Ranking" => array());
    foreach ($firstPref as $candidate => $muhVotes) {
      $thisRound["Ranking"][] = "{$candidates[$candidate]["Name"]}: $muhVotes (".round($muhVotes/$voteCount*100,2)."%)";
    }
    $thisRound["Eliminated"] = $candidates[$lowest]["Name"];
    
    $steps[] = $thisRound;
    
    unset($firstPref[$lowest]);
    
    foreach ($votes as $key => $vote) {
      $preferencesLeft = array_keys($vote);
      sort($preferencesLeft);
      $lowestKey = $preferencesLeft[0];
      if ($vote[$lowestKey] == $lowest) {
      
        // Find the second lowest preference still available
        if (isset($preferencesLeft[1])) {
            $secondLowestKey = $preferencesLeft[1];
            $firstPref[$vote[$secondLowestKey]]++;
        }
      }
      $wastedVote = array_search($lowest, $vote);
      unset($votes[$key][$wastedVote]);
      if (count($votes[$key]) === 0) {
        unset($votes[$key]);
      }
    }
  }
  
  // Final round
  
  $currentRound++;
  
  $voteCount = count($votes);
  arsort($firstPref);
  
  $winner = array_keys($firstPref, max($firstPref));
  $winner = $winner[0];
  if (count($winner) > 1) {
    $warnings[] = "Warning: tie in round $currentRound";
  }
  
  $thisRound = array("VoteCount" => $voteCount, "Ranking" => array());
  foreach ($firstPref as $candidate => $votes) {
    $thisRound["Ranking"][] = "{$candidates[$candidate]["Name"]}: $votes (".round($votes/$voteCount*100,2)."%)";
  }
  $steps[] = $thisRound;
  
  $result = array("Steps" => $steps, "Warnings" => $warnings, "Winner" => $winner);
  return $result;
}

function runSchulze($candidates, $votes) {

  $warnings = array();
  
  // create a matrix of pairwise preferences
  $pairwise = array();
  // for every nominee
  
  $candidates2 = $candidates;
  $candidates3 = $candidates2;
  
  foreach ($candidates as $candidateX => $xInfo) {
    // compare it to every other nominee
    foreach ($candidates2 as $candidateY => $yInfo) {
      //check you aren't comparing it to itself
      if ($candidateX != $candidateY) {
        // set initial matrix value - not sure if this is required
        $pairwise[$candidateX][$candidateY] = 0;
        // now iterate through each voter
        foreach ($votes as $key => $vote) {
          // check each candidate was voted for and store it in 20 otherwise
          if (array_search($candidateX, $vote) === false) {
            $vote[20] = $candidateX;
          }
          if (array_search($candidateY, $vote) === false) {
            $vote[20] = $candidateY;
          }
          // compare the ranks - don't know the data structure well enough to guess this
          if ( array_search($candidateX, $vote) < array_search($candidateY, $vote)) {
            // increase the matrix value of candidateX preferred over candidateY
            $pairwise[$candidateX][$candidateY]++;
          }
        }
      }
    }
  }
  
  // hopefully we should get a pairwise matrix that we can now compare strengths of strongest paths
  $strengths = array();
  foreach ($candidates as $i => $value) {
     foreach ($candidates2 as $j => $value) {
      if ($i != $j) {
        if ($pairwise[$i][$j] > $pairwise[$j][$i]) {
          $strengths[$i][$j] = $pairwise[$i][$j];
        } else {
          $strengths[$i][$j] = 0;
        }
      }
    }
  }
  foreach ($candidates as $i => $value) {
    foreach ($candidates2 as $j => $value) {
      if ($i != $j) {
        foreach ($candidates3 as $k => $value) {
          if (($i != $k) && ($j != $k)) {
            $strengths[$j][$k] = max($strengths[$j][$k], min($strengths[$j][$i], $strengths[$i][$k]));
          }
        }
      }
    }
  }
  
  $result = $strengths;
  
  $rankings = array_fill(1, count($candidates), array());
  
  foreach ($result as $nominee => $row) {
    $counts = array_count_values($row);
    $position = (int)($counts[0] + 1);
    $rankings[$position][] = $nominee;
  }
  
  $finalRankings = array();
  foreach ($rankings as $position => $nominees) {
    if (count($nominees) > 1) {
      $warnings[] = "Tie at position $position: ".implode(", ", $nominees);
    } else if (count($nominees) == 0) {
      $warnings[] = "Gap at position $position";
    }
    foreach ($nominees as $nominee) {
      $finalRankings[] = $nominee;
    }
  }
  
  $finalRankings = array_combine(range(1, count($candidates)), $finalRankings);
  
  return array("Steps" => array("Pairwise" => $pairwise, "Strengths" => $strengths), "Rankings" => $finalRankings, "VoteCount" => count($votes), "Warnings" => $warnings);
}
