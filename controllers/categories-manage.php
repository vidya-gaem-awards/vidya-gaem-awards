<?php
$tpl->set("confirmDeletion", false);
$tpl->set("editFormError", false);

if (canDo("categories-edit")) {
  $tpl->set("title", "Award Manager");
} else {
  $tpl->set("title", "Award Information");
}

if (!empty($_POST) && canDo("categories-edit")) {
  
  if (isset($_POST['delete'])) {
    $category = mysql_real_escape_string($_POST['category']);
    
    if (isset($_POST['confirm'])) {
    
      $query = "DELETE FROM `categories` WHERE `ID` = '$category'";
      $result = mysql_query($query);
      
      if ($result) {
        storeMessage("formSuccess", "Category \"$category\" successfully deleted.");
        action("category-delete", $_POST['category']);
        refresh();
      } else {
        $tpl->set("formError", "An error occurred: " . mysql_error());
      }
    
    } else {
      $tpl->set("confirmDeletion", $category);
    }
    
  } else {
    if ($_POST['action'] == "new") {
      if (strlen($_POST['id']) == 0 || strlen($_POST['name']) == 0 ||
        strlen($_POST['subtitle']) == 0 || strlen($_POST['order']) == 0) {
        $tpl->set("formError", "You need to fill in all of the fields.");
      } else if (!ctype_digit($_POST['order'])) {
        $tpl->set("formError", "The order must be a positive integer.");
      } else if (intval($_POST['order']) > 32767) {
        $tpl->set("formError", "Order is limited to 32767.");
      } else {      
        $category = $_POST['id'];
        $name = mysql_real_escape_string($_POST['name']);
        $subtitle = mysql_real_escape_string($_POST['subtitle']);
        $order = $_POST['order'];

        $enabled = intval(isset($_POST['enabled']));
        $nominations = intval(isset($_POST['nominations']));
        $secret = intval(isset($_POST['secret']));  
        
        $query = "INSERT INTO `categories` (`ID`, `Name`, `Subtitle`, `Order`, `Enabled`, `NominationsEnabled`, `Secret`)";
        $query .= " VALUES ('$category', '$name', '$subtitle', '$order', $enabled, $nominations, $secret)";
        
        $result = mysql_query($query);
        if (!$result) {
          $tpl->set("formError", "An error occurred: " . mysql_error());
        } else {
          $serial = mysql_real_escape_string(json_encode(
            array("Name" => $name, "Subtitle" => $subtitle, "Order" => $order, "Enabled" => $enabled)));
            
          $query = "INSERT INTO `history` (`UserID`, `Table`, `EntryID`, `Values`, `Timestamp`)";
          $query .= "VALUES ('$ID', 'categories', '$category', '$serial', NOW())";
          mysql_query($query);
        
          storeMessage("formSuccess", "Category successfully added.");
          action("category-added", $_POST['id']);
          refresh();
        }
      }
    } else if ($_POST['action'] == "edit") {
      if (strlen($_POST['Name']) == 0 || strlen($_POST['Subtitle']) == 0 || strlen($_POST['Order']) == 0) {
        $tpl->set("editFormError", "You missed a required field.");
      } else if (!ctype_digit($_POST['Order'])) {
        $tpl->set("editFormError", "The position number must be a positive integer.");
      } else if (intval($_POST['Order']) > 32767) {
        $tpl->set("editFormError", "Position number is limited to 32767.");
      } else {
      
        $serial = mysql_real_escape_string(json_encode($_POST));
      
        $_POST = array_map('mysql_real_escape_string', $_POST);
        
        $category = $_POST['ID'];
        $name = $_POST['Name'];
        $subtitle = $_POST['Subtitle'];
        $comments = $_POST['Comments'];
        $order = $_POST['Order'];
        $autocomplete = $_POST['AutocompleteCategory'];
        if ($autocomplete == $category) {
          $autocomplete = "NULL";
        } else {
          $autocomplete = "\"$autocomplete\"";
        }
        $secret = intval(isset($_POST['Secret']));
        $enabled = intval(isset($_POST['Enabled']));
        $nominationsEnabled = intval(isset($_POST['NominationsEnabled']));
        
        $query = "REPLACE INTO `categories` (`ID`, `Name`, `Subtitle`, `Order`, `Comments`, `Enabled`, `NominationsEnabled`, `Secret`, `AutocompleteCategory`) ";
        $query .= "VALUES ('$category', '$name', '$subtitle', $order, '$comments', $enabled, $nominationsEnabled, $secret, $autocomplete)";
        
        $result = mysql_query($query);
        if (!$result) {
          $tpl->set("formError", "An error occurred: " . mysql_error());
        } else {
          $query = "INSERT INTO `history` (`UserID`, `Table`, `EntryID`, `Values`, `Timestamp`)";
          $query .= "VALUES ('$ID', 'categories', '$category', '$serial', NOW())";
          mysql_query($query);
        
          storeMessage("formSuccess", "Category successfully edited.");
          action("category-edited", $_POST['ID']);
          refresh();
        }
      }           
    } else if ($_POST['action'] == "massChangeNominations") {
      if ($_POST['todo'] == "open") {
        $query = "UPDATE `categories` SET `NominationsEnabled` = 1";
        $result = mysql_query($query);
        if (!$result) {
          $tpl->set("formError", "An error ocurred: " . mysql_error());
        } else {
          storeMessage("formSuccess", "Nominations for all categories are now <strong>open</strong>.");
          action("mass-nomination-change", "open");
          refresh();
        }
      } else if ($_POST['todo'] == "close") {
        $query = "UPDATE `categories` SET `NominationsEnabled` = 0";
        $result = mysql_query($query);
        if (!$result) {
          $tpl->set("formError", "An error ocurred: " . mysql_error());
        } else {
          storeMessage("formSuccess", "Nominations for all categories are now <strong>closed</strong>.");
          action("mass-nomination-change", "close");
          refresh();
        }
      }
    } else {
      print_r($_POST);
    } 
  }
}

$query = "SELECT * FROM `categories`";
if (!canDo("categories-secret")) {
  $query .= " WHERE `Secret` = 0";
}
$query .= " ORDER BY `Order` ASC";
$result = $mysql->query($query);

$cats = array();
$categories = array(); 
while ($row = $result->fetch_assoc()) {
  $categories[$row['ID']] = array_merge($row, array(
    "Yes" => 0, "No" => 0));
}

// Get the feedback for each category
$query = "SELECT `CategoryID`, `Opinion`, COUNT(*) as `Count`
          FROM `category_feedback`
          WHERE `Opinion` != 0
          GROUP BY `CategoryID`, `Opinion`
          ORDER BY `Count` DESC";
$stmt = $mysql->prepare($query);
$stmt->execute();
$stmt->bind_result($categoryID, $opinion, $count);
while ($stmt->fetch()) {
  if (!isset($categories[$categoryID])) continue;
  if ($opinion === 1) {
    $categories[$categoryID]['Yes'] = $count;
  } else {
    $categories[$categoryID]['No'] = $count;
  }
}

foreach ($categories as $categoryID => $row) {
  $class = "";
  if (!$row['Enabled']) {
    $status = '<span class="label label-important">Award Disabled</span>';
    $class = "alert-error";
  } else if ($row['Secret']) {
    $status = '<span class="label label-info">Secret Award!</span>';
    $class = "info";
  } else if ($row['NominationsEnabled']) {
    $status = '<span class="label label-success">Nominations Open</span>';
  } else {
    $status = '<span class="label">Nominations Closed</span>';
  }

  $totalVotes = max(1, $row['Yes'] + $row['No']);
  $yes = $row['Yes'] / $totalVotes * 100;
  $no = $row['No'] / $totalVotes * 100;
  
  $temp = array(
    "Class" => $class,
    "Status" => $status,
    "ID" => $row['ID'],
    "Name" => $row['Name'],
    "Subtitle" => $row['Subtitle'],
    "Order" => $row['Order'],
    "Yes" => $yes,
    "No" => $no,
    "Feedback" => $row['Yes'] + $row['No']
  );
  
  $cats[] = $temp;
}   
  
$tpl->set("cats", $cats);
$tpl->set("editing", false);
$tpl->set("canEdit", canDo("categories-edit"));

if ($SEGMENTS[2]) {

  if (!canDo("categories-edit")) {
    $PAGE = $loggedIn ? "401" : "403";
    $CUSTOM_TEMPLATE = false;
  } else if (!isset($categories[$SEGMENTS[2]])) {
    $PAGE = "404";
    $CUSTOM_TEMPLATE = false;
  } else {
  
    $tpl->set("editing", true);
    
    $category = $categories[$SEGMENTS[2]];
    
    $category['Enabled'] = $category['Enabled'] ? "checked='checked'" : "";
    $category['NominationsEnabled'] = $category['NominationsEnabled'] ? "checked='checked'" : "";
    $category['Secret'] = $category['Secret'] ? "checked='checked'" : "";
    
    foreach ($category as $key => $value) {
      $tpl->set($key, htmlspecialchars($value, ENT_QUOTES));
    }

    $autocompleters = array(
      array("Name" => "Default", "ID" => $category['ID']),
      array("Name" => "Video Games", "ID" => "video-game"),
    );

    $query = "SELECT `ID`, `Name` FROM `autocompleters`";
    $result = $mysql->query($query);
    while ($row = $result->fetch_assoc()) {
      $autocompleters[] = array("Name" => $row['Name'], "ID" => $row['ID']);
    }

    foreach ($autocompleters as &$autocompleter) {
      $autocompleter["Selected"] = ($autocompleter["ID"] == $category['AutocompleteCategory']) ? "selected" : "";
    }

    $tpl->set("autocompleters", $autocompleters);
  
  }
  
}
?>