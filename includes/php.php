<?php
header("Content-type: text/html; charset=utf-8");

error_reporting(E_ALL ^ E_DEPRECATED);

date_default_timezone_set("America/New_York");

include("config.php");
include("openid.php");
include("bTemplate.php");
include("functions.php");
$tpl = new bTemplate();
session_start();

// Forward compatibility
$mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);

$APIkey = STEAM_API_KEY;

$domain = DOMAIN;

// Initialise some default template variables
$init = array("success", "error", "formSuccess", "formError");
foreach ($init as $item) {
	$tpl->set($item, false);
}

// Login stuff
$USER_RIGHTS = array("*");
$USER_GROUPS = array();

// Check for a valid login token
$correct = false;
if (!isset($_SESSION['login']) && isset($_COOKIE['token'])) {
  list($token, $hmac) = explode(':', $_COOKIE['token'], 2);
  $tokenValid = $hmac == hash_hmac('md5', $token, $APIkey);
  if ($tokenValid) {
    $token = $mysql->real_escape_string($_COOKIE['token']);
    $query = "SELECT * FROM `login_tokens` WHERE `Token` = \"$token\"";
    $result = $mysql->query($query);
    if ($result->num_rows === 1) {
      $row = $result->fetch_assoc();
      if (strtotime($row['Expires']) > time()) {
        $_SESSION['login'] = $row['UserID'];
        $_SESSION['name'] = $row['Name'];
        $_SESSION['avatar'] = $row['Avatar'];
        $correct = true;
      }
    }
  }
  
  if (!$correct) {
    //setcookie("token", "0", 1, "/", "vidyagaemawards.com");
  }
  
}

if (isset($_SESSION['login'])) {
	$loggedIn = true;
	$ID = $userID = $_SESSION['login'];
	$tpl->set("communityID", $userID);
	$displayName = $_SESSION['name'];
	$tpl->set("displayName", $displayName);
	$tpl->set("avatarURL", $_SESSION['avatar']);
	$tpl->set("steamID", convertSteamID($ID));
	
	// Find out which groups the user is in
	$query = "SELECT `GroupName` FROM `user_groups` WHERE `UserID` = '$userID'";
	$result = $mysql->query($query);
	while ($row = $result->fetch_assoc()) {
		$USER_GROUPS[] = $row['GroupName'];
		if (substr($row['GroupName'], 0, 5) != "level") {
      $USER_RIGHTS[] = $row['GroupName'];
    }
	}
	
	// Level 5s are assigned to levels 1-4
	// Level 4s are assigned to levels 1-3
	// etc.
	for ($i = 6; $i >= 2; $i--) {
		if (in_array("level$i", $USER_GROUPS)) {
			$USER_GROUPS[] = "level".($i-1);
		}
	}
	
	$query = "SELECT DISTINCT `CanDo` FROM `user_rights` WHERE `GroupName` IN ('";
	$query .= implode("', '", $USER_GROUPS) . "')";
	$result = $mysql->query($query);
	while ($row = $result->fetch_array()) {
		$USER_RIGHTS[] = $row['CanDo'];
	}
	
	$USER_RIGHTS[] = "logged-in";
	
} else {
	$loggedIn = false;
	
	$page = rtrim(implode("/", $SEGMENTS), "/");
	
	$tpl->set("openIDurl", SteamSignIn::genUrl("https://$domain/login/$page"));
	
	$ID = $_SERVER['REMOTE_ADDR'];
	
	$userID = "";
	
}
$tpl->set("loggedIn", $loggedIn);
if (canDo("admin")) {
	error_reporting(E_ALL);
	
	// Pretend to be the specified ID until further notice
	if (isset($_GET['pretend'])) {
		if (empty($_GET['pretend'] || !ctype_digit($_GET['pretend']))) {
			unset($_SESSION['pretend']);
		} else {
			$_SESSION['pretend'] = $_GET['pretend'];
		}
	}
	
	if (isset($_SESSION['pretend'])) {
		$ID = $_SESSION['pretend'];
		$result = $mysql->query("SELECT `Name` FROM `users` WHERE `SteamID` = \"$userID\"");
		$row = $result->fetch_array();
		$tpl->set("displayName", $row['Name']);
	}

}
$tpl->set("pretend", isset($_SESSION['pretend']));
set_time_limit(60);

if (!isset($_COOKIE['access']) || strlen($_COOKIE['access']) <= 10) {
  $randomToken = hash('sha256',uniqid(mt_rand(), true).uniqid(mt_rand(), true));
  $randomToken .= ':'.hash_hmac('md5', $randomToken, $APIkey);
  $uniqueID = $randomToken;
	setcookie("access", $randomToken, time()+60*60*24*90, "/", $domain);
} else {
  $uniqueID = $mysql->real_escape_string($_COOKIE['access']);
}


// Message stuff
if (isset($_SESSION['message'])) {
	$tpl->set($_SESSION['message'][0], $_SESSION['message'][1]);
	if (isset($_SESSION['message'][2])) {
		$storedValue = $_SESSION['message'][2];
	}
	unset($_SESSION['message']);
}

// Navbar stuff
$navbarItems["home"] = "Home";
//$navbarItems["video-games"] = "Vidya";
//$navbarItems["polls"] = "Polls";
//$navbarItems["categories"] = "Categories and Nominations";
//$navbarItems["results"] = "Results";
$navbarItems["people"] = "People";
//$navbarItems["launcher"] = "Stream Countdown";
//$navbarItems["videos"] = "Video Submission";
//$navbarItems["voting"] = "Voting";
//$navbarItems["config"] = "Config";
//$navbarItems["http://docs.google.com/document/d/1X0AW508HRznYLbdEHhSpOsuxlopwv9bkrKArqPHnMrY/edit"] = "FAQ";
$navbarItems["winners"] = "Winners";
$navbarItems["credits"] = "Credits";
//$navbarItems["feedback"] = "Give Feedback";
$navbarItems["http://steamcommunity.com/groups/vidyagaemawards/discussions/"] = "Forum";
$navbarItems["about"] = "About";
//$navbarItems["sitemap"] = "Sitemap";
$navbar = "";

foreach ($navbarItems as $filename => $value) {
	
	$external = strpos($filename, "http") === 0;
	
	if (!$external && !canDo($ACCESS[$filename])) {
		continue;
	}
	
	if ($PAGE == $filename) {
		$navbar .= "<li class='active'>";
	} else {
		$navbar .= "<li>";
	}
	if ($external) {
		$navbar .= "<a href='$filename'>$value</a>";
	} else {
		$navbar .= "<a href='/$filename'>$value</a>";
	}
	$navbar .= "</li>\n";

}

$tpl->set("navbar", $navbar);

// Slash removal stuff
if (get_magic_quotes_gpc()) {
    array_walk_recursive($_POST, create_function('&$val', '$val = stripslashes($val);'));
}

// Page access stuff
$page = $PAGE;
$reqString = userInput($_SERVER['REQUEST_URI']);
if (isset($_SERVER['HTTP_USER_AGENT'])) {
  $userAgent = userInput($_SERVER['HTTP_USER_AGENT']);
} else {
  $userAgent = "";
}
$filename = userInput($_SERVER['SCRIPT_FILENAME']);
if (isset($_SERVER['HTTP_REFERER'])) {
	$refer = "'".userInput($_SERVER['HTTP_REFERER'])."'";
} else {
	$refer = "NULL";
}

$ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
$country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? $_SERVER['HTTP_CF_IPCOUNTRY'] : "";

$tpl->set("page", $page);
$tpl->set("logoutURL", rtrim(implode("/", $SEGMENTS), "/"));

# Don't bother recording automatic page refreshes.
if (substr($reqString, -11) != "autorefresh") {
  $_unique = $mysql->real_escape_string($uniqueID);

	$query = "INSERT INTO `access` (`Timestamp`, `UniqueID`, `UserID`, `Page`, `RequestString`, `RequestMethod`, `IP`, `UserAgent`, `Filename`, `Refer`) ";
	$query .= "VALUES (NOW(), '$_unique', '$userID', '$page', '$reqString', '{$_SERVER['REQUEST_METHOD']}', '$ip', ";
	$query .= "'$userAgent', '$filename', $refer)";
	$mysql->query($query);
	
}

$tpl->set("true", true);
$tpl->set("false", false);
$tpl->set("admin", canDo("admin"));
?>
