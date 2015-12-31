<?php
use VGA\Utils;

if ($SEGMENTS[1]) {

    if (canDo("profile-edit-groups")) {
        $steamID = $SEGMENTS[1];

        if (isset($_POST['RemoveGroup'])) {
            $groupName = $_POST['RemoveGroup'];

            $query = "DELETE FROM `user_groups`
                WHERE `UserID` = ? AND `GroupName` = ?";
            $stmt = $mysql->prepare($query);
            $stmt->bind_param('ss', $steamID, $groupName);
            $result = $stmt->execute();
            if (!$result) {
                $tpl->set("formError", "An error occurred: {$stmt->error}");
            } else {
                Utils::storeMessage("formSuccess", "Group successfully removed.");
                Utils::action("profile-group-removed", $steamID, $groupName);
                Utils::refresh();
            }
        } elseif (isset($_POST['AddGroup'])) {
    
            $groupName = trim(strtolower($_POST['GroupName']));
            if ($groupName == "level6" && !canDo("super-admin")) {
                $tpl->set("formError", "That group can't be assigned through the web interface.");
            } elseif (strlen(trim($groupName)) == 0) {
                $tpl->set("formError", "Group name cannot be empty.");
            } else {
                $query = "REPLACE INTO `user_groups` VALUES (?, ?)";
                $stmt = $mysql->prepare($query);
                $stmt->bind_param('ss', $steamID, $groupName);
                $result = $stmt->execute();
                if (!$result) {
                    $tpl->set("formError", "An error occurred: {$stmt->error}");
                } else {
                    Utils::storeMessage("formSuccess", "Group successfully added.");
                    Utils::action("profile-group-added", $steamID, $groupName);
                    Utils::refresh();
                }
            }
        }
    }

    if ($user) {

        if (isset($_POST['action'])) {

            if ($_POST['action'] == "edit-details" && canDo("profile-edit-details")) {
                $query = "UPDATE `users` SET `PrimaryRole` = ?, `Email` = ?
                  WHERE `SteamID` = ?";
                $stmt = $mysql->prepare($query);
                $stmt->bind_param(
                    'sss',
                    $_POST['PrimaryRole'],
                    $_POST['Email'],
                    $user['SteamID']
                );
                $result = $stmt->execute();
                if (!$result) {
                    $tpl->set("formError", "An error occurred: {$stmt->error}");
                } else {
                    $serial = json_encode($_POST);
          
                    $query = "INSERT INTO `history` (`UserID`, `Table`, `EntryID`,
                    `Values`, `Timestamp`) VALUES (?, 'users', ?, ?, NOW())";
                    $stmt = $mysql->prepare($query);
                    $stmt->bind_param('sss', $ID, $user['SteamID'], $serial);
                    $stmt->execute();
          
                    Utils::storeMessage("formSuccess", "Details successfully updated.");
                    Utils::action("profile-details-updated", $user['SteamID']);
                    Utils::refresh();
                }
        
            } elseif ($_POST['action'] == "edit-notes" && canDo("profile-edit-notes")) {
                $query = "UPDATE `users` SET `Notes` = ? WHERE `SteamID` = ?";
                $stmt = $mysql->prepare($query);
                $stmt->bind_param('ss', $_POST['Notes'], $user['SteamID']);
                $result = $stmt->execute();
                if (!$result) {
                    $tpl->set("formError", "An error occurred: {$stmt->error}");
                } else {
                    $serial = json_encode($_POST);
          
                    $query = "INSERT INTO `history` (`UserID`, `Table`, `EntryID`,
                    `Values`, `Timestamp`) VALUES (?, 'users', ?, ?, NOW())";
                    $stmt = $mysql->prepare($query);
                    $stmt->bind_param('sss', $ID, $user['SteamID'], $serial);
                    $stmt->execute();
          
                    Utils::storeMessage("formSuccess", "Notes successfully updated.");
                    Utils::action("profile-notes-updated", $user['SteamID']);
                    Utils::refresh();
                }
      
            }
        }
    }
  
  
}
