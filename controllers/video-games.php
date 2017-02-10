<?php
$tpl->set('title', "2012 in vidya");

$query = "SELECT * FROM `2010_releases`";
$result = $mysql->query($query);

$games = array();

while ($row = $result->fetch_assoc()) {
	
	foreach ($row as $key => $value) {
		if ($key == "Game") {
			$wp = urlencode(str_replace(" ", "_", $value));
			$row[$key] = "<a href='https://en.wikipedia.org/wiki/$wp'>$value</a>";
			continue;
		}
		
		if ($value == 1) {
			$row[$key] = "<strong>✓</strong>";
		} else {
			$row[$key] = "";
		}
	}

	$games[] = $row;

}

$tpl->set('games', $games);
	
	
?>
