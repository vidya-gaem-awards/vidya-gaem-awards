<?php
use Ehesp\SteamLogin\SteamLogin;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use VGA\Controllers;
use VGA\DependencyContainer;
use VGA\DependencyManager;
use VGA\Model\Access;
use VGA\Model\AnonymousUser;
use VGA\Model\LoginToken;
use VGA\Model\User;

require(__DIR__ . '/../bootstrap.php');

// Basic setup
$em = DependencyManager::getEntityManager();
$request = Request::createFromGlobals();
$session = new Session();
$session->start();

// TODO: delete this in production
Debug::enable();


// TODO: unsure if this is needed when using the Response class
header("Content-type: text/html; charset=utf-8");

// Check if the user is currently logged in
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

// Change this to change the default route
$routes->add('index', new Route('/', ['controller' => Controllers\IndexController::class]));

$routes->add('home', new Route('/home', ['controller' => Controllers\IndexController::class]));
$routes->add('news', new Route('/news', ['controller' => Controllers\NewsController::class]));
$routes->add('login', new Route(
    '/login/{return}',
    ['controller' => Controllers\AuthController::class, 'action' => 'login'],
    ['return' => '.*']
));
$routes->add('logout', new Route(
    '/logout',
    ['controller' => Controllers\AuthController::class, 'action' => 'logout']
));

$routes->add('people', new Route(
    '/people',
    ['controller' => Controllers\PeopleController::class, 'permission' => 'profile-view']
));

$peopleCollection = new RouteCollection();
$peopleCollection->add('permissions', new Route(
    '/permissions',
    ['action' => 'permissions', 'permission' => 'profile-view']
));
$peopleCollection->add('addPerson', new Route(
    '/new',
    ['action' => 'new', 'permission' => 'add-user']
));
$peopleCollection->add('userSearch', new Route(
    '/search',
    ['action' => 'search', 'permission' => 'add-user'],
    [],
    [],
    '',
    [],
    ['POST']
));
$peopleCollection->add('viewPerson', new Route(
    '/{steamID}',
    ['action' => 'view', 'permission' => 'profile-view'],
    ['steamID' => '\d+']
));
$peopleCollection->add('editPerson', new Route(
    '/{steamID}/edit',
    ['action' => 'edit', 'permission' => 'profile-edit-details'],
    ['steamID' => '\d+'],
    [],
    '',
    [],
    ['GET']
));
$peopleCollection->add('editPersonPost', new Route(
    '/{steamID}/edit',
    ['action' => 'post', 'permission' => 'profile-view'],
    ['steamID' => '\d+'],
    [],
    '',
    [],
    ['POST']
));
$peopleCollection->addPrefix('/people');
$peopleCollection->addDefaults([
    'controller' => Controllers\PeopleController::class
]);
$routes->addCollection($peopleCollection);
$routes->add('privacy', new Route(
    '/privacy',
    ['controller' => Controllers\StaticController::class, 'action' => 'privacy']
));
$routes->add('categories', new Route(
    '/categories',
    ['controller' => Controllers\CategoryAdminController::class]
));
$routes->add('categoryManager', new Route(
    '/categories/manage',
    [
        'controller' => Controllers\CategoryAdminController::class,
        'action' => 'managerList',
        'permission' => 'categories-feedback'
    ],
    [],
    [],
    '',
    [],
    ['GET']
));
$routes->add('categoryManagerPost', new Route(
    '/categories/manage',
    [
        'controller' => Controllers\CategoryAdminController::class,
        'action' => 'managerPost',
        'permission' => 'categories-edit'
    ],
    [],
    [],
    '',
    [],
    ['POST']
));
$routes->add('editCategory', new Route(
    '/categories/manage/{category}',
    [
        'controller' => Controllers\CategoryAdminController::class,
        'action' => 'editCategory',
        'permission' => 'categories-edit'
    ],
    [],
    [],
    '',
    [],
    ['GET']
));
$routes->add('editCategoryPost', new Route(
    '/categories/manage/{category}',
    [
        'controller' => Controllers\CategoryAdminController::class,
        'action' => 'editCategoryPost',
        'permission' => 'categories-edit'
    ],
    [],
    [],
    '',
    [],
    ['POST']
));

$context = new RequestContext();
$context->fromRequest($request);
// Due to the way that Cloudflare is set up, the user sees HTTPS but our server only sees HTTP.
// We manually update some values to pretend we have full HTTPS, else generated links will have the wrong protocol.
$_SERVER['HTTPS'] = 'on';
$context->setScheme('https');

$generator = new UrlGenerator($routes, $context);

// We can't instantiate Twig object any earlier as we need the UrlGenerator
$twig = DependencyManager::getTwig($generator);

// Steam login link
if ($user instanceof AnonymousUser) {
    $returnLink = $generator->generate(
        'login',
        ['return' => $request->getPathInfo()],
        UrlGenerator::ABSOLUTE_URL
    );

    $steam = new SteamLogin();
    $twig->addGlobal('steamLoginLink', $steam->url($returnLink));
}

$navbar = [];
foreach (NAVBAR_ITEMS as $routeName => $title) {
    if ($routes->get($routeName)) {
        $navbar[$routeName] = $title;
    }
}

$twig->addGlobal('navbarItems', $navbar);

$matcher = new UrlMatcher($routes, $context);

$container = new DependencyContainer(
    $em,
    $request,
    DependencyManager::getDatabaseHandle(),
    $twig,
    $session,
    $user,
    $generator
);

// Call the correct controller and method
try {
    $match = $matcher->match($request->getPathInfo());

    if (!class_exists($match['controller'])) {
        $controller = new Controllers\ErrorController($container);
        $controller->internalErrorAction();
        return;
    }

    /** @var Controllers\BaseController $controller */
    $controller = new $match['controller']($container);

    if (isset($match['action'])) {
        $action = $match['action'] . 'Action';
    } else {
        $action = 'indexAction';
    }

    if (isset($match['permission']) && !$user->canDo($match['permission'])) {
        /** @var Controllers\ErrorController $controller */
        $controller = new Controllers\ErrorController($container);
        if ($user->isLoggedIn()) {
            $controller->noAccessAction();
        } else {
            $controller->needLoginAction();
        }
        return;
    }

    if (!method_exists($controller, $action)) {
        /** @var Controllers\ErrorController $controller */
        $controller = new Controllers\ErrorController($container);
        $controller->internalErrorAction();
        return;
    }

    // Log the page access
    $access = new Access();
    if (!($user instanceof AnonymousUser)) {
        $access->setUser($user);
    }

    $access
        ->setCookieID($user->getRandomID())
        ->setPage($match['_route'])
        ->setRequestMethod($request->server->get('REQUEST_METHOD'))
        ->setRequestString($request->server->get('REQUEST_URI'))
        ->setIp($user->getIP())
        ->setUserAgent($request->server->get('HTTP_USER_AGENT'))
        ->setFilename($request->server->get('SCRIPT_FILENAME'))
        ->setReferer($request->server->get('HTTP_REFERER'));
    $em->persist($access);
    $em->flush();

    unset($match['controller']);
    unset($match['action']);
    unset($match['permission']);
    unset($match['_route']);
    call_user_func_array([$controller, $action], $match);

} catch (ResourceNotFoundException $e) {
    $controller = new Controllers\ErrorController($container);
    $controller->notFoundAction();
    return;
} catch (MethodNotAllowedException $e) {
    $controller = new Controllers\ErrorController($container);
    $controller->wrongMethodAction();
    return;
}

exit;

// Abandon hope all who go below this point

// Special handling for ad landing page
if ($PAGE == "promotions") {
    header("Location: " . AD_LANDING_PAGE);
    exit;
}

$ACCESS = array(
    // Volatile pages
    "ajax-category-feedback" => EVERYONE,
    "ajax-nominations" => "nominations-edit",
    "ajax-videogame" => "add-video-game",
    //"applications" => "applications-view",
    "categories" => EVERYONE,
    "credits" => EVERYONE,
    "launcher" => EVERYONE,
    "nominations" => "nominations-view",
    "nomination-submit" => EVERYONE,
    "referrers" => "referrers-view",
    "stream" => EVERYONE,
    //"test" => EVERYONE,
    "thanks" => EVERYONE,
    "video-games" => EVERYONE,
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
    "about" => "About",
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
    "voting-submission",
);

// Pages have the option of specifying this variable to load a different template
$CUSTOM_TEMPLATE = false;

// Initialise some default template variables
$init = array("success", "error", "formSuccess", "formError");
foreach ($init as $item) {
    $tpl->set($item, false);
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
