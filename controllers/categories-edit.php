<?php
$tpl->set("confirmDeletion", false);
$tpl->set("editFormError", false);



if (!empty($_POST)) {
    ## SITE PLACED INTO READ-ONLY MODE
    $tpl->set("formError", "The site is now in read-only mode. Awards can no longer be changed.");
//
//	if (isset($_POST['delete'])) {
//		$category = $mysql->real_escape_string($_POST['category']);
//
//		if (isset($_POST['confirm'])) {
//
//			$query = "DELETE FROM `categories` WHERE `ID` = '$category'";
//			$result = debug_query($query);
//
//			if ($result) {
//				storeMessage("formSuccess", "Category \"$category\" successfully deleted.");
//				action("category-delete", $category);
//				refresh();
//			} else {
//				$tpl->set("formError", "An error occurred: " . $mysql->error);
//			}
//
//		} else {
//			$tpl->set("confirmDeletion", $category);
//		}
//
//	} else {
//		if ($_POST['action'] == "new") {
//			if (strlen($_POST['id']) == 0 || strlen($_POST['name']) == 0 ||
//				strlen($_POST['subtitle']) == 0 || strlen($_POST['order']) == 0) {
//				$tpl->set("formError", "You need to fill in all of the fields.");
//			} else if (!ctype_digit($_POST['order'])) {
//				$tpl->set("formError", "The order must be a positive integer.");
//			} else if (intval($_POST['order']) > 32767) {
//				$tpl->set("formError", "Order is limited to 32767.");
//			} else {
//                $values = array_map(function ($value) use ($mysql) {
//                    return $mysql->real_escape_string($value);
//                }, $_POST);
//
//				$category = $values['id'];
//				$name = $values['name'];
//				$subtitle = $values['subtitle'];
//				$order = $values['order'];
//				$enabled = intval(isset($values['enabled']));
//
//				$query = "INSERT INTO `categories` (`ID`, `Name`, `Subtitle`, `Order`, `Enabled`)";
//				$query .= " VALUES ('$category', '$name', '$subtitle', '$order', $enabled)";
//
//				$result = $mysql->query($query);
//				if (!$result) {
//					$tpl->set("formError", "An error occurred: " . $mysql->error);
//				} else {
//					$serial = $mysql->real_escape_string(json_encode(
//						array("Name" => $name, "Subtitle" => $subtitle, "Order" => $order, "Enabled" => $enabled)));
//
//					$query = "INSERT INTO `history` (`UserID`, `Table`, `EntryID`, `Values`, `Timestamp`)";
//					$query .= "VALUES ('$ID', 'categories', '$category', '$serial', NOW())";
//					debug_query($query);
//
//					storeMessage("formSuccess", "Category cessfully added.");
//					action("category-added", $values['id']);
//					refresh();
//				}
//			}
//		} else if ($_POST['action'] == "edit") {
//			if (strlen($_POST['Name']) == 0 || strlen($_POST['Subtitle']) == 0 || strlen($_POST['Order']) == 0) {
//				$tpl->set("editFormError", "You missed a required field.");
//			} else if (!ctype_digit($_POST['Order'])) {
//				$tpl->set("editFormError", "The position number must be a positive integer.");
//			} else if (intval($_POST['Order']) > 32767) {
//				$tpl->set("editFormError", "Position number is limited to 32767.");
//			} else {
//
//				$serial = $mysql->real_escape_string(json_encode($_POST));
//
//                $values = array_map(function ($value) use ($mysql) {
//                    return $mysql->real_escape_string($value);
//                }, $_POST);
//
//				$category = $values['ID'];
//				$name = $values['Name'];
//				$subtitle = $values['Subtitle'];
//				$comments = $values['Comments'];
//				$order = $values['Order'];
//				$secret = intval(isset($values['Secret']));
//				$enabled = intval(isset($values['Enabled']));
//				$nominationsEnabled = intval(isset($values['NominationsEnabled']));
//
//				$query = "REPLACE INTO `categories` (`ID`, `Name`, `Subtitle`, `Order`, `Comments`, `Enabled`, `NominationsEnabled`, `Secret`) ";
//				$query .= "VALUES ('$category', '$name', '$subtitle', $order, '$comments', $enabled, $nominationsEnabled, $secret)";
//
//				$result = $mysql->query($query);
//				if (!$result) {
//					$tpl->set("formError", "An error occurred: " . $mysql->error);
//				} else {
//					$query = "INSERT INTO `history` (`UserID`, `Table`, `EntryID`, `Values`, `Timestamp`)";
//					$query .= "VALUES ('$ID', 'categories', '$category', '$serial', NOW())";
//					debug_query($query);
//
//					storeMessage("formSuccess", "Category successfully edited.");
//					action("category-edited", $values['ID']);
//					refresh();
//				}
//			}
//		} else if ($_POST['action'] == "massChangeNominations") {
//		  if ($_POST['todo'] == "open") {
//        $query = "UPDATE `categories` SET `NominationsEnabled` = 1";
//        $result = $mysql->query($query);
//        if (!$result) {
//          $tpl->set("formError", "An error ocurred: " . $mysql->error);
//        } else {
//          storeMessage("formSuccess", "Nominations for all categories are now <strong>open</strong>.");
//          action("mass-nomination-change", "open");
//          refresh();
//        }
//      } else if ($_POST['todo'] == "close") {
//        $query = "UPDATE `categories` SET `NominationsEnabled` = 0";
//        $result = $mysql->query($query);
//        if (!$result) {
//          $tpl->set("formError", "An error ocurred: " . $mysql->error);
//        } else {
//          storeMessage("formSuccess", "Nominations for all categories are now <strong>closed</strong>.");
//          action("mass-nomination-change", "close");
//          refresh();
//        }
//      }
//		} else {
//      print_r($_POST);
//    }
//	}
}

$query = "SELECT * FROM `categories` ORDER BY `Order` ASC";
$result = $mysql->query($query);

$cats = array();
$categories = array(); 
while ($row = $result->fetch_assoc()) {

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
