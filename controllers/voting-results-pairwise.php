<?php
$tpl->set("title", "Pairwise voting results");

$query = "SELECT * FROM `nominees` ORDER BY `NomineeID` ASC";
$result = mysql_query($query);
$nominees = array();
while ($row = mysql_fetch_assoc($result)) {
    $nominees[$row['CategoryID']][$row['NomineeID']] = $row['Name'];
}

$query = "SELECT `CategoryID`, `Name`, `Subtitle`, `Results`, `Steps` FROM `winner_cache`";
$query .= "INNER JOIN `categories` ON `CategoryID` = `ID` WHERE `Filter` = \"05combined2\" AND `Enabled` = 1 ORDER BY `Order` ASC";
$result = mysql_query($query);
$categories = array();
while ($row = mysql_fetch_assoc($result)) {
    $steps = json_decode($row['Steps'], true);
    $pairwise = $steps['Pairwise'];
    $rows = array();
  
    $headerWidth = 120 + (15 - count($pairwise)) * 20;
  
    $oneRow = "<tr class='rotate'><th style='width: {$headerWidth}px;'>&nbsp;</th>";
    foreach ($nominees[$row['CategoryID']] as $nomineeID => $name) {
        $oneRow .= "<th title=\"$name\">$name</th>";
    }
    $oneRow .= "</tr>";
    $rows[] = $oneRow;
  
    $rowKey = 0;
    foreach ($pairwise as $nominee => $comparisons) {
        $rowKey++;
        $columnKey = 0;
        $nomineeName = $nominees[$row['CategoryID']][$nominee];
        $oneRow = "<tr><th title=\"$nomineeName\">$nomineeName</th>";
        foreach ($comparisons as $nominee2 => $count) {
            $columnKey++;
            if ($rowKey == $columnKey) {
                $oneRow .= "<td>--</td>";
            }
            $oneRow .= "<td>$count</td>";
        }
        if ($rowKey == count($pairwise)) {
            $oneRow .= "<td>--</td>";
        }
        $oneRow .= "</tr>";
        $rows[] = $oneRow;
    }
    $row['Table'] = implode("\n", $rows);
    $categories[] = $row;
}

$tpl->set("categories", $categories);
