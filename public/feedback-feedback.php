<?php
include(__DIR__."/../includes/php.php");

$tpl->set("title", "Viewer Feedback");
$tpl->set("denied", !canDo("special") && !canDo("feedback"));

### GENERAL RATING

$query = "SELECT COUNT(*) as `Count`, `GeneralRating` FROM `ceremony_feedback` WHERE `GeneralRating` != 0 GROUP BY `GeneralRating` ORDER BY `GeneralRating` DESC";
$result = mysql_query($query);

$general = array();
$maxCount = 0;
$total = 0;
$average = 0;
while ($row = mysql_fetch_assoc($result)) {
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
$result = mysql_query($query);

$ceremony = array();
$maxCount = 0;
$total = 0;
$average = 0;
while ($row = mysql_fetch_assoc($result)) {
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
$result = mysql_query($query);

$best = array();
while ($row = mysql_fetch_assoc($result)) {
	$best[] = implying($row['BestThing']);
}
$tpl->set("best", $best);
$tpl->set("bestCount", mysql_num_rows($result));



### WORST THINGS

$query = "SELECT `WorstThing` FROM `ceremony_feedback` WHERE `WorstThing` != '' ORDER BY CHAR_LENGTH(`WorstThing`) DESC";
$result = mysql_query($query);

$worst = array();
while ($row = mysql_fetch_assoc($result)) {
	$worst[] = implying($row['WorstThing']);
}
$tpl->set("worst", $worst);
$tpl->set("worstCount", mysql_num_rows($result));


### OTHER COMMENTS

$query = "SELECT `OtherComments` FROM `ceremony_feedback` WHERE `OtherComments` != '' ORDER BY CHAR_LENGTH(`OtherComments`) DESC";
$result = mysql_query($query);

$other = array();
while ($row = mysql_fetch_assoc($result)) {
	$other[] = implying($row['OtherComments']);
}
$tpl->set("other", $other);
$tpl->set("otherCount", mysql_num_rows($result));


### QUESTIONS

$query = "SELECT `ID`, `Questions` FROM `ceremony_feedback` WHERE `Questions` != '' ORDER BY `ID` ASC";
$result = mysql_query($query);

$questions = array();
while ($row = mysql_fetch_assoc($result)) {
	$questions[] = $row['ID'] . ": " . implying($row['Questions']);
}
$tpl->set("questions", $questions);
$tpl->set("questionCount", mysql_num_rows($result));

fetch();
?>
