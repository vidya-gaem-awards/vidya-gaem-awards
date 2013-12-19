<?php
$tpl->set("title", "People");

class User {

  function __construct($row) {
    $this->Name = $row['Name'];

    // Shorten the user's displayed name if needed
    $displayName = $this->Name;
    
    if (strlen($displayName) == 0) {
      $displayName = "[".$row['SteamID']."]";
    } else {
      $addEllipsis = false;
      if (strlen($displayName) >= 25) {
        $displayName = substr($displayName, 0, 23);
        $addEllipsis = true;
      }
      $displayName = htmlspecialchars($displayName, ENT_QUOTES);
      if ($addEllipsis) {
        $displayName .= "&#8230;";
      }
    }
    
    $this->DisplayName = $displayName;
    $this->SteamID = $row['SteamID'];
    
    $this->SteamAccount = strlen($this->SteamID) == 17;
    
    $this->Avatar = $row['Avatar'];
    
    // Generic question mark avatar
    if (!$this->Avatar) {
      $this->Avatar = '/9j/4AAQSkZJRgABAQAAAQABAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2'
        .'MS4wICh1c2luZyBJSkcgSlBFRyB2NjIpLCBxdWFsaXR5ID0gOTAK/9sAQwADAgIDAgIDA'
        .'wMDBAMDBAUIBQUEBAUKBwcGCAwKDAwLCgsLDQ4SEA0OEQ4LCxAWEBETFBUVFQwPFxgWFB'
        .'gSFBUU/9sAQwEDBAQFBAUJBQUJFA0LDRQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQ'
        .'UFBQUFBQUFBQUFBQUFBQUFBQUFBQU/8AAEQgAIAAgAwEiAAIRAQMRAf/EAB8AAAEFAQEB'
        .'AQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSI'
        .'TFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERU'
        .'ZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqe'
        .'oqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/E'
        .'AB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECd'
        .'wABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKS'
        .'o1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJW'
        .'Wl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz'
        .'9PX29/j5+v/aAAwDAQACEQMRAD8A/P4mW5nmllmeSR3LMzMSSc1a07R73V72KzsILi9u5'
        .'TiOC2RpJHPoFGSarQ/ef6n+de4fAn9oaL4D+DfGX9i6Uf8AhO9XSKDT9eZY3WxiDZcBGB'
        .'yTkn0JCZBxQB41qeiX+iXslnqNtdWF3H9+3uo2jkX6q2CKpgy208MsUzxyI4ZWViCDmvs'
        .'r9rrUdT1j9nb4T6h8RBbH4qXUs0zMsSxXJ04hivnKoAU5MPGBg7uM7q+NpvvJ9R/OgAh+'
        .'8/1P867T4POI/iz4Mc6U+u7NZtG/suPbuu8TKfKG4hct93njnmuKIltp5opYXjkRyrKyk'
        .'EHNWbDVbvSr63vbKaezvLeRZYbi3ZkkidTlWVhyCCMgjpQB6l+1F411nx58dPFWpa5a3m'
        .'nXaXP2ZNOvXVpLKNBhYflJUY5PB5JJ6k15LN95PqP51a1PWr7WtQnvtRuLm/vrhzJNc3T'
        .'tJLIx6lmbJJ9zVQCW5nhiiheSR3CqqqSSc0Af/9k=';
    }
    
    $this->FirstLogin = $row['FirstLogin'];
    $this->Email = $row['Email'];
    $this->LastLogin = $row['LastLogin'];
    if (strlen($row['Groups']) == 0) {
      $this->Groups = array();
    } else {
      $this->Groups = explode("|", $row['Groups']);
    }
    $this->DisplayGroups = implode(", ", $this->Groups);
    $this->PrimaryRole = htmlspecialchars($row['PrimaryRole'], ENT_QUOTES);
    $this->Notes = htmlspecialchars($row['Notes']);
  }
}

$users = array();

$query = "SELECT `users`.*, GROUP_CONCAT(`GroupName` SEPARATOR '|') as `Groups`
          FROM `users` LEFT JOIN `user_groups` ON `SteamID` = `UserID`
          WHERE `Special` = 1 GROUP BY `SteamID` ORDER BY `Name` ASC";
$result = $mysql->query($query);

while ($row = $result->fetch_assoc()) {
  $user = new User($row);
  $users[$user->SteamID] = get_object_vars($user);
}

$tpl->set("users", $users);

$tpl->set("userNotFound", false);

if ($SEGMENTS[1] == "permissions") {

    $CUSTOM_TEMPLATE = "permissions";
    
    for ($i = 6; $i >= 0; $i--) {
        if (in_array("level$i", $USER_GROUPS)) {
            break;
        }
    }
    
    $level = $i;
    $tpl->set("level", $level);
    
    $query = "SELECT * FROM `user_rights`
              ORDER BY `GroupName` DESC, `Description` ASC";
    $result = $mysql->query($query);
    $permissions = array();
    while ($row = $result->fetch_assoc()) {
        if (canDo($row['CanDo'])) {
            if (isset($row['Description'])) {
                $desc = "<abbr title='{$row['CanDo']}'>{$row['Description']}</abbr>";
            } else {
                $desc = "<tt>{$row['CanDo']}</tt>";
            }
            $levelName = "level ".$row['GroupName'][5];
            $permissions[] = "$desc <small class='muted'>($levelName)</small>";
        }
    }
    $tpl->set("permissions", $permissions);

} else if ($SEGMENTS[1] == "add") {
    
    if (!canDo("add-user")) {
        $PAGE = $loggedIn ? "403" : "401";
    } else {
        $CUSTOM_TEMPLATE = "add";
        require("people-add.php");
    }

} else if ($SEGMENTS[1]) {

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
        storeMessage("formSuccess", "Group successfully removed.");
        action("profile-group-removed", $steamID, $groupName);
        refresh();
      }
    } else if (isset($_POST['AddGroup'])) {
    
      $groupName = trim(strtolower($_POST['GroupName']));
      if ($groupName == "level6" && !canDo("super-admin")) {
        $tpl->set("formError", "That group can't be assigned through the web interface.");
      } else if (strlen(trim($groupName)) == 0) {
        $tpl->set("formError", "Group name cannot be empty.");
      } else {
        $query = "REPLACE INTO `user_groups` VALUES (?, ?)";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param('ss', $steamID, $groupName);
        $result = $stmt->execute();
        if (!$result) {
          $tpl->set("formError", "An error occurred: {$stmt->error}");
        } else {
          storeMessage("formSuccess", "Group successfully added.");
          action("profile-group-added", $steamID, $groupName);
          refresh();
        }
      }
    }
  }

  if (isset($users[$SEGMENTS[1]])) {
    $user = $users[$SEGMENTS[1]];
    $inList = true;
  } else {
    $steamID = mysql_real_escape_string($SEGMENTS[1]);
    
    $query = "SELECT * FROM `users` WHERE `SteamID` = '$steamID'";
    $result = $mysql->query($query);
    if ($result->num_rows === 0) {
      $user = false;
      $tpl->set("userNotFound", true);
    } else {
      $user = new User($result->fetch_assoc());
      $user = get_object_vars($user);
      $inList = false;
    }
  }
  
  $tpl->set("CanAddUser", canDo("add-user"));
  
  if ($user) {

    if (isset($_POST['action'])) {

      if ($_POST['action'] == "edit-details" && canDo("profile-edit-details")) {        
        $query = "UPDATE `users` SET `PrimaryRole` = ?, `Email` = ?
                  WHERE `SteamID` = ?";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param('sss', $_POST['PrimaryRole'], $_POST['Email'],
          $user['SteamID']);
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
          
          storeMessage("formSuccess", "Details successfully updated.");
          action("profile-details-updated", $user['SteamID']);
          refresh();
        }
        
      } else if ($_POST['action'] == "edit-notes" && canDo("profile-edit-notes")) {
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
          
          storeMessage("formSuccess", "Notes successfully updated.");
          action("profile-notes-updated", $user['SteamID']);
          refresh();
        }
      
      }
    }
  
    if ($user['LastLogin']) {
      $user['LastLogin'] = date("F jS, Y", strtotime($user['LastLogin']));
    }
    $CUSTOM_TEMPLATE = "individual";
    foreach ($user as $key => $value) {
      $tpl->set($key, $value);
    }
    
    $tpl->set("CanEdit", canDo("profile-edit-details"));
    $tpl->set("CanEditGroups", canDo("profile-edit-groups") && $user['SteamAccount']);
    $tpl->set("CanEditNotes", canDo("profile-edit-notes"));
    
    if ($SEGMENTS[2] == "edit") {
    
      if (!canDo("profile-edit-details")) {
        $PAGE = $loggedIn ? "403" : "401";
        $CUSTOM_TEMPLATE = false;
      }
      
      $tpl->set("editing", true);
    
    } else {
      $tpl->set("editing", false);
    }
    
  }
  
  
}
?>