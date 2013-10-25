<?php
$tpl->set("confirmDeletion", false);
$tpl->set("editFormError", false);
if (!empty($_POST)) {
  
	if (isset($_POST['delete'])) {
		$category = mysql_real_escape_string($_POST['category']);
		
		if (isset($_POST['confirm'])) {
		
			$query = "DELETE FROM `categories` WHERE `ID` = '$category'";
			$result = debug_query($query);
			
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
				array_map('mysql_real_escape_string', $_POST);
				
				$category = $_POST['id'];
				$name = $_POST['name'];
				$subtitle = $_POST['subtitle'];
				$order = $_POST['order'];
				$enabled = intval(isset($_POST['enabled']));
				
				$query = "INSERT INTO `categories` (`ID`, `Name`, `Subtitle`, `Order`, `Enabled`)";
				$query .= " VALUES ('$category', '$name', '$subtitle', '$order', $enabled)";
				
				$result = mysql_query($query);
				if (!$result) {
					$tpl->set("formError", "An error occurred: " . mysql_error());
				} else {
					$serial = mysql_real_escape_string(json_encode(
						array("Name" => $name, "Subtitle" => $subtitle, "Order" => $order, "Enabled" => $enabled)));
						
					$query = "INSERT INTO `history` (`UserID`, `Table`, `EntryID`, `Values`, `Timestamp`)";
					$query .= "VALUES ('$ID', 'categories', '$category', '$serial', NOW())";
					debug_query($query);
				
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
				$secret = intval(isset($_POST['Secret']));
				$enabled = intval(isset($_POST['Enabled']));
				$nominationsEnabled = intval(isset($_POST['NominationsEnabled']));
				
				$query = "REPLACE INTO `categories` (`ID`, `Name`, `Subtitle`, `Order`, `Comments`, `Enabled`, `NominationsEnabled`, `Secret`) ";
				$query .= "VALUES ('$category', '$name', '$subtitle', $order, '$comments', $enabled, $nominationsEnabled, $secret)";
				
				$result = mysql_query($query);
				if (!$result) {
					$tpl->set("formError", "An error occurred: " . mysql_error());
				} else {
					$query = "INSERT INTO `history` (`UserID`, `Table`, `EntryID`, `Values`, `Timestamp`)";
					$query .= "VALUES ('$ID', 'categories', '$category', '$serial', NOW())";
					debug_query($query);
				
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

$query = "SELECT * FROM `categories` ORDER BY `Order` ASC";
$result = mysql_query($query);

$cats = array();
$categories = array(); 
while ($row = mysql_fetch_assoc($result)) {

	$categories[$row['ID']] = $row;

	if ($row['Enabled']) {
		$enabled = '<span class="label label-success">Enabled</span>';
		$class = "";
	} else {
		$enabled = '<span class="label label-important">Disabled</span>';
		$class = "alert-error";
	}
	$temp = array(
		"Class" => $class,
		"Enabled" => $enabled,
		"ID" => $row['ID'],
		"Name" => $row['Name'],
		"Subtitle" => $row['Subtitle'],
		"Order" => $row['Order']
	);
	
	$cats[] = $temp;
}		
	
$tpl->set("cats", $cats);
$tpl->set("editing", false);

if ($SEGMENTS[2]) {

	if (!isset($categories[$SEGMENTS[2]])) {
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
	
	}
	
}
?>