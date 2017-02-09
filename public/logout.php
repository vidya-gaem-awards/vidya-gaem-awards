<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: https://2011.vidyagaemawards.com");
?>
