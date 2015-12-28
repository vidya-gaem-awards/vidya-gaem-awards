<?php
//ob_start('ob_gzhandler');
require(__DIR__ . '/../bootstrap.php');

define("EVERYONE", "*");
define("LOGIN", "logged-in");

// URL rewriter
// Courtesy of https://stackoverflow.com/questions/893218/rewrite-for-all-urls
$_SERVER['REQUEST_URI_PATH'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$SEGMENTS = explode('/', trim($_SERVER['REQUEST_URI_PATH'], '/'));
$SEGMENTS = array_map("strtolower", $SEGMENTS);

for ($i = 0; $i <= 9; $i++) {
    if (!isset($SEGMENTS[$i])) {
        $SEGMENTS[$i] = null;
    }
}


$PAGE = $SEGMENTS[0];

// Change the default page in includes/config.php
if (strlen($PAGE) == 0) {
    $PAGE = DEFAULT_PAGE;
}

// Special handling for logout page
if ($PAGE == "logout") {
    session_start();
    $_SESSION = array();
    session_destroy();

    $return = rtrim(implode("/", array_slice($SEGMENTS, 1)), "/");
    setcookie("token", "0", 1, "/", DOMAIN);
    header("Location: /$return");
    exit;
}

// Special handling for ad landing page
if ($PAGE == "promotions") {
    header("Location: " . AD_LANDING_PAGE);
    exit;
}

$ACCESS = array(
    // These ones should never have to change
    "404" => EVERYONE,
    //"about" => EVERYONE,
    "home" => EVERYONE,
    "login" => EVERYONE,
    "privacy" => EVERYONE,
    "promotions" => EVERYONE,
    //"sitemap" => EVERYONE,

    // Volatile pages
    "ajax-category-feedback" => EVERYONE,
    "ajax-nominations" => "nominations-edit",
    "ajax-videogame" => "add-video-game",
    //"applications" => "applications-view",
    "categories" => EVERYONE,
    "credits" => EVERYONE,
    "launcher" => EVERYONE,
    "news" => EVERYONE,
    "nominations" => "nominations-view",
    "nomination-submit" => EVERYONE,
    "people" => "profile-view",
    "referrers" => "referrers-view",
    "stream" => EVERYONE,
    //"test" => EVERYONE,
    "thanks" => EVERYONE,
    "user-search" => "add-user",
    "video-games" => EVERYONE,
    //"vg-redirect" => EVERYONE,
    //"volunteer-submission" => LOGIN,
    //"videos" => EVERYONE,
    "voting" => EVERYONE,
    "voting-code" => "voting-view",
    "voting-submission" => EVERYONE,
    "winners" => EVERYONE // Change to EVERYONE
);

if (isset($ACCESS["nomination-submit"]) && ACCOUNT_REQUIRED_TO_NOMINATE) {
    $ACCESS["nomination-submit"] = LOGIN;
}

// Pages that won't use the master template
$noMaster = array(
    "login",
    "launcher",
    "stream",
    "thanks",
    "voting"
);

// Pages so basic they don't need a PHP file.
$noPHP = array(
    "405" => "405 Method Not Allowed",
    "404" => "404 Not Found",
    "403" => "403 Forbidden",
    "401" => "401 Unauthorized",
    "about" => "About",
    "privacy" => "Privacy Policy",
    "sitemap" => "Sitemap",
    "stream" => "",
    "videos" => "Video Submission",
);

$noContainer = array("videos");

// Pages that should only be accessed via POST requests
$postOnly = array(
    "ajax-category-feedback",
    "ajax-nominations",
    "ajax-videogame",
    "nomination-submit",
    "volunteer-submission",
    "user-search",
    "voting-submission",
);

// Pages have the option of specifying this variable to load a different template
$CUSTOM_TEMPLATE = false;

header("Content-type: text/html; charset=utf-8");

require(__DIR__ . '/../bootstrap.php');

use VGA\Utils;

error_reporting(E_ALL);
date_default_timezone_set(TIMEZONE);

function canDo($privilege)
{
    global $USER_RIGHTS, $USER_GROUPS;

    if (in_array("admin", $USER_GROUPS)) {
        return true;
    }

    return in_array($privilege, $USER_RIGHTS);
}

$tpl = new BTemplate();
session_start();

// Constants
$tpl->set("YEAR", YEAR);

// Database connection
$mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
$mysql->set_charset("utf8");

// Backwards compatibility
mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_DATABASE);
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
    $tokenValid = $hmac == hash_hmac('md5', $token, STEAM_API_KEY);
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
    $tpl->set("steamID", Utils::convertSteamID($ID));

    // Find out which groups the user is in
    $query = "SELECT `GroupName` FROM `user_groups` WHERE `UserID` = ?";
    $stmt = $mysql->prepare($query);
    if (!$stmt) {
        error_log("MySQL error: ".$mysql->error);
    } else {
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
    }

    $USER_RIGHTS[] = "logged-in";

} else {
    $loggedIn = false;

    $page = rtrim(implode("/", $SEGMENTS), "/");

    $tpl->set("openIDurl", SteamSignIn::genUrl("https://" . DOMAIN . "/login/$page"));

    $ID = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
    $userID = "";

}
$tpl->set("loggedIn", $loggedIn);

set_time_limit(60);

if (!isset($_COOKIE['access']) || strlen($_COOKIE['access']) <= 10) {
    $randomToken = hash('sha256', uniqid(mt_rand(), true).uniqid(mt_rand(), true));
    $randomToken .= ':'.hash_hmac('md5', $randomToken, STEAM_API_KEY);
    $uniqueID = $randomToken;
    setcookie("access", $randomToken, time()+60*60*24*90, "/", DOMAIN);
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

foreach (NAVBAR_ITEMS as $filename => $value) {

    $external = strpos($filename, "://") === 0;

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

$IP = isset($_SERVER['HTTP_CF_CONNECTING_IP'])
    ? $_SERVER['HTTP_CF_CONNECTING_IP']
    : $_SERVER['REMOTE_ADDR'];

$country = isset($_SERVER['HTTP_CF_IPCOUNTRY'])
    ? $_SERVER['HTTP_CF_IPCOUNTRY']
    : "";

$tpl->set("page", $page);
$tpl->set("logoutURL", rtrim(implode("/", $SEGMENTS), "/"));

# Don't bother recording automatic page refreshes.
if (substr($_SERVER['REQUEST_URI'], -11) != "autorefresh") {
    $query = "INSERT INTO `access` (`Timestamp`, `UniqueID`, `UserID`, `Page`,
            `RequestString`, `RequestMethod`, `IP`, `UserAgent`, `Filename`,
            `Refer`) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param(
        'sssssssss',
        $uniqueID,
        $userID,
        $page,
        $_SERVER['REQUEST_URI'],
        $_SERVER['REQUEST_METHOD'],
        $IP,
        $_SERVER['HTTP_USER_AGENT'],
        $_SERVER['SCRIPT_FILENAME'],
        $_SERVER['HTTP_REFERER']
    );
    $stmt->execute();
}

$tpl->set("true", true);
$tpl->set("false", false);
$tpl->set("admin", canDo("admin"));

// Enforce access control
if (!isset($ACCESS[$PAGE])) {
    header('HTTP/1.0 404 Not Found');
    $PAGE = "404";
} else {
    if ($ACCESS[$PAGE] != EVERYONE && !$loggedIn) {
        header('HTTP/1.0 401 Unauthorized');
        $PAGE = "401";
    } else {
        if (!canDo($ACCESS[$PAGE])) {
            header('HTTP/1.0 403 Forbidden');
            $PAGE = "403";
        }
    }
}

// Enforce post-only pages
if (in_array($PAGE, $postOnly) && $_SERVER['REQUEST_METHOD'] != "POST") {
    header('HTTP/1.0 405 Method Not Allowed');
    $PAGE = "405";
}

// Run the page-specific code
if (!isset($noPHP[$PAGE])) {
    require("controllers/$PAGE.php");
} else {
    $tpl->set('title', $noPHP[$PAGE]);
}

// Post-only pages have no view at all
if (!in_array($PAGE, $postOnly)) {

    // Special variable if we don't need the container
    $tpl->set('noContainer', in_array($PAGE, $noContainer));

    // Render the required templates
    $template = $CUSTOM_TEMPLATE ? $PAGE . "-" . $CUSTOM_TEMPLATE : $PAGE;
    if (!in_array($PAGE, $noMaster)) {
        $tpl->set('content', $tpl->fetch("views/$template.tpl"));
        echo $tpl->fetch("views/master.tpl");
    } else {
        echo $tpl->fetch("views/$template.tpl");
    }
}
