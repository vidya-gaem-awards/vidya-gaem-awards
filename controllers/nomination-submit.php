<?php
$cat = mysql_real_escape_string($_POST['Category']);
$query = "SELECT `ID` FROM `categories` WHERE `ID` = \"$cat\"";
$result = mysql_query($query);
if (mysql_num_rows($result) == 0) {
  die("bad category");
}

$nomination = trim($_POST['Nomination']);
if (empty($nomination)) {
  die("blank nomination");
}
$nomination = mysql_real_escape_string($nomination);

$query = "SELECT `Nomination` FROM `user_nominations` WHERE `CategoryID` = \"$cat\" AND `UserID` = \"$ID\"";
$query .= " AND LOWER(`Nomination`) = \"".strtolower($nomination)."\"";
$result = mysql_query($query);
if (!$result) {
  die(mysql_error());
}
if (mysql_num_rows($result) > 0) {
  die("already exists");
}

$query = "INSERT INTO `user_nominations` (`CategoryID`, `UserID`, `Nomination`, `Timestamp`) VALUES (\"$cat\", \"$ID\", \"$nomination\", NOW())";
mysql_query($query);
action("nomination-made", $cat);  
echo "success";
?>