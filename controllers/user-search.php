<?php
$search = mysql_real_escape_string($_POST['ID']);
$query = "SELECT `SteamID`, `Name`, `Special`, `Avatar` FROM `users` WHERE `SteamID` = '$search'";
$result = mysql_query($query);
if ($result == false) {
    die(json_encode(array("error" => "mysql")));
}
if (mysql_num_rows($result) === 0) {
    die(json_encode(array("error" => "no matches")));
}
$row = mysql_fetch_assoc($result);
if ($row['Special'] === "1") {
    die(json_encode(array("error" => "already special", "name" => $row['Name'])));
}

if ($_POST['Add']) {
    $query = "UPDATE `users` SET `Special` = 1 WHERE `SteamID` = '$search'";
    $result = mysql_query($query);
    if (!$result) {
        die(json_encode(array("error" => "mysql")));
    }
    $query = "INSERT INTO `user_groups` VALUES ('$search', 'level1')";
    $result = mysql_query($query);
    if (!$result) {
        die(json_encode(array("error" => "mysql2")));
    }
    echo json_encode(array("success" => "true"));
} else {
    echo json_encode($row);
}
?>