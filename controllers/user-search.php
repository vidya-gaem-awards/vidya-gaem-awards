<?php
## SITE PLACED INTO READ-ONLY MODE
return_json("error", "read-only");

//
//// Get basic information about the user
//$query = "SELECT `Name`, `Special`, `Avatar` FROM `users` WHERE `SteamID` = ?";
//$stmt = $mysql->prepare($query);
//$stmt->bind_param('s', $_POST['ID']);
//$stmt->execute();
//$stmt->bind_result($name, $special, $avatar);
//$stmt->store_result();
//
//if ($stmt->num_rows === 0) {
//    return_json("error", "no matches");
//}
//$stmt->fetch();
//if ($special === 1) {
//    return_json("error", "already special", array("name" => $name));
//}
//
//if ($_POST['Add']) {
//    // Make the user special and give them level 1 access
//    $query = "UPDATE `users` SET `Special` = 1 WHERE `SteamID` = ?";
//    $stmt = $mysql->prepare($query);
//    $stmt->bind_param('s', $_POST['ID']);
//    $stmt->execute();
//
//    $query = "INSERT INTO `user_groups` VALUES (?, 'level1')";
//    $stmt = $mysql->prepare($query);
//    $stmt->bind_param('s', $_POST['ID']);
//    $stmt->execute();
//
//    return_json("success");
//} else {
//    // Return the information so you know you've got the right person
//    return_json("success", true, array(
//        "Name" => $name,
//        "Avatar" => $avatar,
//        "SteamID" => $_POST['ID'])
//    );
//}
?>
