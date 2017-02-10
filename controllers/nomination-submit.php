<?php
## SITE PLACED INTO READ-ONLY MODE
die("closed");


//$cat = $mysql->real_escape_string($_POST['Category']);
//$query = "SELECT `ID` FROM `categories` WHERE `ID` = \"$cat\"";
//$result = $mysql->query($query);
//if ($result->num_rows == 0) {
//	die("bad category");
//}
//
//$nomination = trim($_POST['Nomination']);
//if (empty($nomination)) {
//	die("blank nomination");
//}
//$nomination = $mysql->real_escape_string($nomination);
//
//$query = "SELECT `Nomination` FROM `user_nominations` WHERE `CategoryID` = \"$cat\" AND `UserID` = \"$ID\"";
//$query .= " AND LOWER(`Nomination`) = \"".strtolower($nomination)."\"";
//$result = $mysql->query($query);
//if (!$result) {
//	die($mysql->error);
//}
//if ($result->num_rows > 0) {
//	die("already exists");
//}
//
//$query = "INSERT INTO `user_nominations` (`CategoryID`, `UserID`, `Nomination`, `Timestamp`) VALUES (\"$cat\", \"$ID\", \"$nomination\", NOW())";
//$mysql->query($query);
//action("nomination-made", $cat);
//echo "success";
?>
