<?php
$tpl->set("title", "Voting");

if (isset($_GET['image'])) {
  $tpl->set("image", $_GET['image']);
} else {
  $tpl->set("image", false);
}

?>