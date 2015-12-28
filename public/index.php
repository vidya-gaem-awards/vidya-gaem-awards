<?php
use Ehesp\SteamLogin\SteamLogin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use VGA\Controllers;
use VGA\DependencyManager;
use VGA\Model\AnonymousUser;
use VGA\Model\LoginToken;
use VGA\Model\User;

require(__DIR__ . '/../bootstrap.php');

// Basic setup
$em = DependencyManager::getEntityManager();
$request = Request::createFromGlobals();
$twig = DependencyManager::getTwig();
$session = new Session();
$session->start();

// Handle authentication
if ($session->get('user')) {
    $user = $em->getRepository(User::class)->find($session->get('user'));
} elseif ($cookie = $request->cookies->get('rememberMeToken')) {
    list($token, $hmac) = explode(':', $cookie, 2);
    $tokenValid = ($hmac == hash_hmac('md5', $token, STEAM_API_KEY));
    if ($tokenValid) {
        /** @var LoginToken $token */
        $token = $em->getRepository(LoginToken::class)->findBy(['token' => $token]);
        if ($token) {
            $user = $token->getUser();
        }
    }
}

if (!isset($user)) {
    $user = new AnonymousUser();
}

// Generate a random ID to keep in the cookie if one doesn't already exist.
// We use this cookie as part of the voting identification process
// (although it's similar to the remember me token, they serve different purposes)
$randomID = $request->cookies->get('access');
if ($randomID === null) {
    $factory = new \RandomLib\Factory;
    $generator = $factory->getLowStrengthGenerator();
    $randomToken = hash('sha256', $generator->generate(64));
    $randomToken .= ':' . hash_hmac('md5', $randomToken, STEAM_API_KEY);
}

// Update the user object with information that doesn't come from the database.
$user
    ->setIP($request->server->get('HTTP_CF_CONNECTING_IP', $request->server->get('REMOVE_ATTR')))
    ->setRandomID($randomID);

// Define the routes
$routes = new RouteCollection();

$routes->add('index', new Route('/', ['controller' => Controllers\IndexController::class]));
$routes->add('login', new Route(
    '/login/{return}',
    ['controller' => Controllers\AuthController::class, 'action' => 'login'],
    ['return' => '.*']
));
$routes->add('logout', new Route(
    '/logout',
    ['controller' => Controllers\AuthController::class, 'action' => 'logout'],
    ['return' => '.*']
));

$context = new RequestContext();
$context->fromRequest($request);
// Due to the way that Cloudflare is set up, the user sees HTTPS but our server only sees HTTP.
// We manually update some values to pretend we have full HTTPS, else generated links will have the wrong protocol.
$_SERVER['HTTPS'] = 'on';
$context->setScheme('https');

// Steam login link
if ($user instanceof AnonymousUser) {
    $generator = new UrlGenerator($routes, $context);
    $returnLink = $generator->generate(
        'login',
        ['return' => $request->getPathInfo()],
        UrlGenerator::ABSOLUTE_URL
    );

    $steam = new SteamLogin();
    $twig->addGlobal('steamLoginLink', $steam->url($returnLink));
}

$matcher = new UrlMatcher($routes, $context);

// Call the correct controller and method
try {
    $match = $matcher->match($request->getPathInfo());

    if (!class_exists($match['controller'])) {
        http_response_code(500);
        echo '500 &ndash; controller does not exist';
        exit;
    }

    /** @var Controllers\BaseController $controller */
    $controller = new $match['controller']();
    $controller->initialize(
        $em,
        $request,
        DependencyManager::getDatabaseHandle(),
        $twig,
        $session,
        $user
    );

    if (isset($match['action'])) {
        $action = $match['action'] . 'Action';
    } else {
        $action = 'indexAction';
    }

    if (!method_exists($controller, $action)) {
        http_response_code(500);
        echo '500 &ndash; action does not exist';
        exit;
    }

    unset($match['controller']);
    unset($match['action']);
    call_user_func_array([$controller, $action], $match);

} catch (ResourceNotFoundException $e) {
    echo '404 &ndash; page not found';
}

exit;

// Abandon hope all who go below this point
//define("EVERYONE", "*");
//define("LOGIN", "logged-in");

// Change the default page in includes/config.php
//if (strlen($PAGE) == 0) {
//    $PAGE = DEFAULT_PAGE;
//}

// Special handling for ad landing page
//if ($PAGE == "promotions") {
//    header("Location: " . AD_LANDING_PAGE);
//    exit;
//}

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

// Initialise some default template variables
$init = array("success", "error", "formSuccess", "formError");
foreach ($init as $item) {
    $tpl->set($item, false);
}

// Login stuff
$USER_RIGHTS = array("*");
$USER_GROUPS = array();

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
