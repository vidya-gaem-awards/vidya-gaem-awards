<?php
include(__DIR__."/../includes/php.php");
$result = $dbh->query("SELECT * FROM `ceremony_feedback` WHERE `Questions` != ''");

while ($row = $result->fetch_assoc()) {
	echo "<span ";
	if (strlen($row['Questions']) > 300) {
		echo "style='background-color: yellow;'";
	}
	echo ">";
	echo $row['ID'] . ": " . $row['Questions'] . "<br />";
	echo "</span>";
}
?>
