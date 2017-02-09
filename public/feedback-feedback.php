<?php
include(__DIR__."/../includes/php.php");

$tpl->set("title", "Viewer Feedback");
$tpl->set("denied", !canDo("special") && !canDo("feedback"));

### GENERAL RATING

$query = "SELECT COUNT(*) as `Count`, `GeneralRating` FROM `ceremony_feedback` WHERE `GeneralRating` != 0 GROUP BY `GeneralRating` ORDER BY `GeneralRating` DESC";
$result = $dbh->query($query);

$general = array();
$maxCount = 0;
$total = 0;
$average = 0;
while ($row = $result->fetch_assoc()) {
	$general[] = array(
		"rating" => $row['GeneralRating'] + 5,
		"bar" => $row['GeneralRating'],
		"count" => number_format($row['Count']),
		"count-int" => $row['Count'],
		"width" => 0,
	);
	$maxCount = max($maxCount, $row['Count']);
	$total += $row['Count'];
	$average += ($row['GeneralRating'] + 5) * $row['Count'];
}

foreach ($general as $key => $values) {
	$general[$key]["width"] = ceil(($values["count-int"] / $maxCount) * 150);
}

$average = round($average / $total, 2);

$tpl->set("general", $general);
$tpl->set("generalTotal", number_format($total));
$tpl->set("generalAverage", $average);


### CEREMONY RATING

$query = "SELECT COUNT(*) as `Count`, `CeremonyRating` FROM `ceremony_feedback` WHERE `CeremonyRating` != 0 GROUP BY `CeremonyRating` ORDER BY `CeremonyRating` DESC";
$result = $dbh->query($query);

$ceremony = array();
$maxCount = 0;
$total = 0;
$average = 0;
while ($row = $result->fetch_assoc()) {
	$ceremony[] = array(
		"rating" => $row['CeremonyRating'] + 5,
		"bar" => $row['CeremonyRating'],
		"count" => number_format($row['Count']),
		"count-int" => $row['Count'],
		"width" => 0,
	);
	$maxCount = max($maxCount, $row['Count']);
	$total += $row['Count'];
	$average += ($row['CeremonyRating'] + 5) * $row['Count'];
}

foreach ($ceremony as $key => $values) {
	$ceremony[$key]["width"] = ceil(($values["count-int"] / $maxCount) * 150);
}

$average = round($average / $total, 2);

$tpl->set("ceremony", $ceremony);
$tpl->set("ceremonyTotal", number_format($total));
$tpl->set("ceremonyAverage", $average);


function implying($str) {
	if ($str[0] == ">") {
		$str = "<span class='implying'>$str</span>";
	}
	$str = str_replace("\n", "<br />", $str);
	return $str;
}

### BEST THINGS

$query = "SELECT `BestThing` FROM `ceremony_feedback` WHERE `BestThing` != '' ORDER BY CHAR_LENGTH(`BestThing`) DESC";
$result = $dbh->query($query);

$best = array();
while ($row = $result->fetch_assoc()) {
	$best[] = implying($row['BestThing']);
}
$tpl->set("best", $best);
$tpl->set("bestCount", $result->num_rows);



### WORST THINGS

$query = "SELECT `WorstThing` FROM `ceremony_feedback` WHERE `WorstThing` != '' ORDER BY CHAR_LENGTH(`WorstThing`) DESC";
$result = $dbh->query($query);

$worst = array();
while ($row = $result->fetch_assoc()) {
	$worst[] = implying($row['WorstThing']);
}
$tpl->set("worst", $worst);
$tpl->set("worstCount", $result->num_rows);


### OTHER COMMENTS

$query = "SELECT `OtherComments` FROM `ceremony_feedback` WHERE `OtherComments` != '' ORDER BY CHAR_LENGTH(`OtherComments`) DESC";
$result = $dbh->query($query);

$other = array();
while ($row = $result->fetch_assoc()) {
	$other[] = implying($row['OtherComments']);
}
$tpl->set("other", $other);
$tpl->set("otherCount", $result->num_rows);


### QUESTIONS

$query = "SELECT `ID`, `Questions` FROM `ceremony_feedback` WHERE `Questions` != '' ORDER BY `ID` ASC";
$result = $dbh->query($query);

$questions = array();
while ($row = $result->fetch_assoc()) {
	$questions[] = $row['ID'] . ": " . implying($row['Questions']);
}
$tpl->set("questions", $questions);
$tpl->set("questionCount", $result->num_rows);

fetch();
?>
