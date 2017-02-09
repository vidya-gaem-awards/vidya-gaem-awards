<?php
header("Content-type: text/html; charset=utf-8");

ini_set("display_errors", false);
error_reporting(E_ALL);

include("config.php");
include("openid.php");
include("bTemplate.php");
include("functions.php");
$tpl = new bTemplate();
session_start();

$dbh = new mysqli(DB_HOST, DB_USER, DB_PASS);
$dbh->select_db(DB_DATABASE);

$APIkey = STEAM_API_KEY;



// Login stuff
$canDo = array();

$page = explode("/", $_SERVER['SCRIPT_NAME']);
$page = substr($page[count($page) - 1], 0, -4);
$tpl->set("page", $page);

if (isset($_SESSION['login'])) {
	$loggedIn = true;
	$ID = $_SESSION['login'];
	$tpl->set("communityID", $ID);
	$displayName = $_SESSION['name'];
	$tpl->set("displayName", $displayName);
	$tpl->set("avatarURL", $_SESSION['avatar']);
	$tpl->set("steamID", convertSteamID($ID));
	
	$result = $dbh->query("SELECT `Privilege` FROM `user_rights` WHERE `UserID` = \"" . $dbh->real_escape_string($ID) . "\"");
	while ($row = $result->fetch_array()) {
		$canDo[$row['Privilege']] = true;
	}
	
} else {
	$loggedIn = false;
	
	$tpl->set("openIDurl", SteamSignIn::genUrl("https://2011.vidyagaemawards.com/login.php?return={$page}.php"));
}
$tpl->set("loggedIn", $loggedIn);
if (canDo("admin")) {
	error_reporting(E_ALL);
	
	// Pretend to be the specified ID until further notice
	if (isset($_GET['pretend'])) {
		if (empty($_GET['pretend'])) {
			unset($_SESSION['pretend']);
		} else {
			$_SESSION['pretend'] = $_GET['pretend'];
		}
	}
	
	if (isset($_SESSION['pretend'])) {
		$ID = $_SESSION['pretend'];
		$result = $dbh->query("SELECT `Name` FROM `users` WHERE `SteamID` = \"" . $dbh->real_escape_string($ID) . "\"");
		$row = $result->fetch_array();
		$tpl->set("displayName", $row['Name']);
	}

}
$tpl->set("pretend", isset($_SESSION['pretend']));
set_time_limit(30);

if (!isset($_COOKIE['access'])) {
	setcookie("access", mt_rand(), time()+60*60*24*30, "/", "vidyagaemawards.com");
}


// Message stuff
$success = $error = false;
if (isset($_SESSION['message'])) {
	if ($_SESSION['message'][0] == "success") {
		$success = $_SESSION['message'][1];
	}
	if ($_SESSION['message'][0] == "error") {
		$error = $_SESSION['message'][1];
	}
	if (isset($_SESSION['message'][2])) {
		$storedValue = $_SESSION['message'][2];
	}
	unset($_SESSION['message']);
}
$tpl->set('success', $success);
$tpl->set('error', $error);


// Navbar stuff
$navbarItems = array("home.php" => "Home", "polls.php" => "Polls", /*"categories.php" => "Categories",*/ "nominations.php" => "Nominations");
$navbarItems["results.php"] = "Results";
$navbarItems["voting.php"] = "Voting";
$navbarItems["/forum"] = "Forum";
if (canDo("special")) {
	$navbarItems["/wiki"] = "Wiki";
}
$navbarItems["about.php"] = "About";
$navbar = "";

foreach ($navbarItems as $filename => $value) {
	
	if (basename($_SERVER['SCRIPT_NAME']) == $filename) {
		$navbar .= "<li class='active'>";
	} else {
		$navbar .= "<li>";
	}
	$navbar .= "<a href='$filename'>$value</a>";
	$navbar .= "</li>\n";

}

$tpl->set("navbar", $navbar);

// Slash removal stuff
if (get_magic_quotes_gpc()) {
    array_walk_recursive($_POST, create_function('&$val', '$val = stripslashes($val);'));
}



// Page access stuff
$page = explode("/", $_SERVER['SCRIPT_NAME']);
$page = substr($page[count($page) - 1], 0, -4);        
$reqString = userInput($_SERVER['REQUEST_URI']);
$userAgent = userInput($_SERVER['HTTP_USER_AGENT']);
$filename = userInput($_SERVER['SCRIPT_FILENAME']);
if (isset($_SERVER['HTTP_REFERER'])) {
	$refer = "'".userInput($_SERVER['HTTP_REFERER'])."'";
} else {
	$refer = "NULL";
}

## SITE PLACED INTO READ-ONLY MODE

# Don't bother recording automatic page refreshes.
//if (substr($reqString, -11) != "autorefresh") {
//	$query = "INSERT INTO `access` (`Timestamp`, `UserID`, `Page`, `RequestString`, `RequestMethod`, `IP`, `UserAgent`, `Filename`, `Refer`) ";
//	$query .= "VALUES (NOW(), '$ID', '$page', '$reqString', '{$_SERVER['REQUEST_METHOD']}', '{$_SERVER['REMOTE_ADDR']}',";
//	$query .= "'$userAgent', '$filename', $refer)";
//	$dbh->query($query);
//
//}

$tpl->set("false", false);
$tpl->set("admin", canDo("admin"));
?>
