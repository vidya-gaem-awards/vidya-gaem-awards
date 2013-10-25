<?php
$tpl->set("title", "Nominations");
if ($SEGMENTS[1] == "results") {
  if (!canDo("nominations-view")) {
    $PAGE = "403";
  } else {
    $CUSTOM_TEMPLATE = "results";
    require_once("nominations-results.php");
  }
} else if (!canDo("nominees-view")) {
  $PAGE = "403";
} else {
  $CUSTOM_TEMPLATE = "edit";
  require_once("nominations-edit.php");
}
?>