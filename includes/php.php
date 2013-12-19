<?php
header("Content-type: text/html; charset=utf-8");

error_reporting(E_ALL ^ E_DEPRECATED);

include("config.php");

date_default_timezone_set($TIMEZONE);

include("openid.php");
include("bTemplate.php");
include("functions.php");
$tpl = new bTemplate();
session_start();

// Constants
$tpl->set("YEAR", $YEAR);

// Database connection
$mysql = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_DB);
$mysql->set_charset("utf8");

// Backwards compatibility
mysql_connect($DB_HOST, $DB_USER, $DB_PASS);
mysql_select_db($DB_DB);
mysql_query("SET NAMES utf8");

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
  $tokenValid = $hmac == hash_hmac('md5', $token, $STEAM_API_KEY);
  if ($tokenValid) {
    $query = "SELECT `UserID`, `Name`, `Avatar`, `Expires` FROM `login_tokens`
              WHERE `Token` = ?";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param('s', $_COOKIE['token']);
    $stmt->execute();
    $stmt->bind_result($userID, $name, $avatar, $expires);
    if ($stmt->fetch() && strtotime($expires) > time()) {
      $_SESSION['login'] = $userID;
      $_SESSION['name'] = $name;
      $_SESSION['avatar'] = $avatar;
      $correct = true;
    }
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
  $query = "SELECT `GroupName` FROM `user_groups` WHERE `UserID` = ?";
  $stmt = $mysql->prepare($query);
  $stmt->bind_param('s', $ID);
  $stmt->execute();
  $stmt->bind_result($groupName);
  while ($stmt->fetch()) {
    $USER_GROUPS[] = $groupName;
    if (substr($groupName, 0, 5) != "level") {
      $USER_RIGHTS[] = $groupName;
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
  while ($row = $result->fetch_assoc()) {
    $USER_RIGHTS[] = $row['CanDo'];
  }
  
  $USER_RIGHTS[] = "logged-in";
  
} else {
  $loggedIn = false;
  
  $page = rtrim(implode("/", $SEGMENTS), "/");
  
  $tpl->set("openIDurl", SteamSignIn::genUrl("http://$DOMAIN/login/$page"));
  
  $ID = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
  $userID = "";
  
} 
$tpl->set("loggedIn", $loggedIn);

set_time_limit(60);

if (!isset($_COOKIE['access']) || strlen($_COOKIE['access']) <= 10) {
  $randomToken = hash('sha256',uniqid(mt_rand(), true).uniqid(mt_rand(), true));
  $randomToken .= ':'.hash_hmac('md5', $randomToken, $STEAM_API_KEY);
  $uniqueID = $randomToken;
  setcookie("access", $randomToken, time()+60*60*24*90, "/", $DOMAIN);
} else {
  $uniqueID = $_COOKIE['access'];
}


// Message stuff
if (isset($_SESSION['message'])) {
  $tpl->set($_SESSION['message'][0], $_SESSION['message'][1]);
  if (isset($_SESSION['message'][2])) {
    $storedValue = $_SESSION['message'][2];
  }
  unset($_SESSION['message']);
}

// Navbar stuff (the items have been moved to config.php)
$navbar = "";

foreach ($NAVBAR_ITEMS as $filename => $value) {
  
  $external = strpos($filename, "http://") === 0;
  
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

// Page access stuff
$page = $PAGE;

$ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
$country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? $_SERVER['HTTP_CF_IPCOUNTRY'] : "";

$tpl->set("page", $page);
$tpl->set("logoutURL", rtrim(implode("/", $SEGMENTS), "/"));

# Don't bother recording automatic page refreshes.
if (substr($_SERVER['REQUEST_URI'], -11) != "autorefresh") {
  $query = "INSERT INTO `access` (`Timestamp`, `UniqueID`, `UserID`, `Page`, 
            `RequestString`, `RequestMethod`, `IP`, `UserAgent`, `Filename`,
            `Refer`) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $mysql->prepare($query);
  $stmt->bind_param('sssssssss', $uniqueID, $userID, $page,
    $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $ip,
    $_SERVER['HTTP_USER_AGENT'], $_SERVER['SCRIPT_FILENAME'],
    $_SERVER['HTTP_REFERER']);
  $stmt->execute();
}

$tpl->set("true", true);
$tpl->set("false", false);
$tpl->set("admin", canDo("admin"));
?>