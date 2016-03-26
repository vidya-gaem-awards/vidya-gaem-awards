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
use VGA\Model\Config;
use VGA\Model\LoginToken;
use VGA\Model\User;

require(__DIR__ . '/../bootstrap.php');

// Basic setup
$em = DependencyManager::getEntityManager();
$request = Request::createFromGlobals();
$session = new Session();
$session->start();

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

if ($user->canDo('view-debug-output')) {
    Debug::enable();
}

// Generate a random ID to keep in the cookie if one doesn't already exist.
// We use this cookie as part of the voting identification process
// (although it's similar to the remember me token, they serve different purposes)
$randomIDCookie = $request->cookies->get('access');
$randomIDSession = $session->get('access');

if ($randomIDCookie && $randomIDSession) {
    $randomID = $randomIDCookie;
} elseif ($randomIDSession && !$randomIDCookie) {
    // Bad practice, should be using Symfony's request class
    setcookie('access', $randomIDSession, strtotime('+90 days'), '/', $request->getHost());
    $randomID = $randomIDSession;
} elseif ($randomIDCookie && !$randomIDSession) {
    $session->set('access', $randomIDCookie);
    $randomID = $randomIDCookie;
} else {
    $factory = new \RandomLib\Factory;
    $generator = $factory->getLowStrengthGenerator();
    $randomID = hash('sha256', $generator->generate(64));
    $randomID .= ':' . hash_hmac('md5', $randomID, STEAM_API_KEY);

    // Bad practice, should be using Symfony's request class
    setcookie('access', $randomID, strtotime('+90 days'), '/', $request->getHost());
    $session->set('access', $randomID);
}

$votingCodeSession = $session->get('votingCode');
$votingCodeCookie = $request->cookies->get('votingCode');

if ($votingCodeCookie) {
    $session->set('votingCode', $votingCodeCookie);
    $votingCode = $votingCodeCookie;
} else {
    $votingCode = $votingCodeSession;
}

// Update the user object with information that doesn't come from the database.
$user
    ->setIP($request->server->get('HTTP_CF_CONNECTING_IP', $request->server->get('REMOVE_ATTR')))
    ->setRandomID($randomID)
    ->setVotingCode($votingCode);

// Define the routes
$routes = new RouteCollection();

$routes->add('home', new Route(
    '/home',
    [
        'controller' => Controllers\IndexController::class
    ]
));
$routes->add('news', new Route(
    '/news',
    [
        'controller' => Controllers\NewsController::class
    ]
));
$routes->add('login', new Route(
    '/login/{return}',
    [
        'controller' => Controllers\AuthController::class,
        'action' => 'login'
    ],
    ['return' => '.*']
));
$routes->add('logout', new Route(
    '/logout',
    [
        'controller' => Controllers\AuthController::class,
        'action' => 'logout'
    ]
));

$routes->add('people', new Route(
    '/people',
    [
        'controller' => Controllers\PeopleController::class,
        'permission' => 'profile-view'
    ]
));

$peopleCollection = new RouteCollection();
$peopleCollection->add('permissions', new Route(
    '/permissions',
    [
        'action' => 'permissions',
        'permission' => 'profile-view'
    ]
));
$peopleCollection->add('addPerson', new Route(
    '/new',
    [
        'action' => 'new',
        'permission' => 'add-user'
    ]
));
$peopleCollection->add('userSearch', new Route(
    '/search',
    [
        'action' => 'search',
        'permission' => 'add-user'
    ],
    [],
    [],
    '',
    [],
    ['POST']
));
$peopleCollection->add('viewPerson', new Route(
    '/{steamID}',
    [
        'action' => 'view',
        'permission' => 'profile-view'
    ],
    ['steamID' => '\d+']
));
$peopleCollection->add('editPerson', new Route(
    '/{steamID}/edit',
    [
        'action' => 'edit',
        'permission' => 'profile-edit-details'
    ],
    ['steamID' => '\d+'],
    [],
    '',
    [],
    ['GET']
));
$peopleCollection->add('editPersonPost', new Route(
    '/{steamID}/edit',
    [
        'action' => 'post',
        'permission' => 'profile-view'
    ],
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
    [
        'controller' => Controllers\StaticController::class,
        'action' => 'privacy'
    ]
));
$routes->add('categories', new Route(
    '/awards',
    ['controller' => Controllers\CategoryController::class],
    [],
    [],
    '',
    [],
    ['GET']
));
$routes->add('categoryManager', new Route(
    '/awards/manage',
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
    '/awards/manage',
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
    '/awards/manage/{category}',
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
    '/awards/manage/{category}',
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
$routes->add('videoGames', new Route(
    '/vidya-in-2015',
    [
        'controller' => Controllers\VideoGamesController::class
    ],
    [],
    [],
    '',
    [],
    ['GET']
));
$routes->add('addVideoGame', new Route(
    '/vidya-in-2015',
    [
        'controller' => Controllers\VideoGamesController::class,
        'action' => 'add',
        'permission' => 'add-video-game'
    ],
    [],
    [],
    '',
    [],
    ['POST']
));
$routes->add('awardFrontendPost', new Route(
    '/awards',
    [
        'controller' => Controllers\CategoryController::class,
        'action' => 'post'
    ],
    [],
    [],
    '',
    [],
    ['POST']
));
$routes->add('referrers', new Route(
    '/referrers',
    [
        'controller' => Controllers\ReferrerController::class,
        'permission' => 'referrers-view'
    ]
));
$routes->add('nomineeManager', new Route(
    '/nominees/{category}',
    [
        'controller' => Controllers\NomineeController::class,
        'permission' => 'nominations-view',
        'category' => null
    ],
    [],
    [],
    '',
    [],
    ['GET']
));
$routes->add('nomineePost', new Route(
    '/nominees/{category}',
    [
        'controller' => Controllers\NomineeController::class,
        'action' => 'post',
        'permission' => 'nominations-edit'
    ],
    [],
    [],
    '',
    [],
    ['POST']
));
$routes->add('config', new Route(
    '/config',
    [
        'controller' => Controllers\ConfigController::class,
        'permission' => 'edit-config'
    ],
    [],
    [],
    '',
    [],
    ['GET']
));
$routes->add('configPost', new Route(
    '/config',
    [
        'controller' => Controllers\ConfigController::class,
        'action' => 'post',
        'permission' => 'edit-config'
    ],
    [],
    [],
    '',
    [],
    ['POST']
));
$routes->add('viewVotingCode', new Route(
    '/vote/code',
    [
        'controller' => Controllers\VotingController::class,
        'action' => 'codeViewer',
        'permission' => 'voting-view'
    ]
));
$routes->add('voting', new Route(
    '/vote/{category}',
    [
        'controller' => Controllers\VotingController::class,
        'category' => null
//        'permission' => 'voting-view',
    ],
    [],
    [],
    '',
    [],
    ['GET']
));
$routes->add('votingSubmission', new Route(
    '/vote/{category}',
    [
        'controller' => Controllers\VotingController::class,
        'action' => 'post',
//        'permission' => 'voting-view'
    ],
    [],
    [],
    '',
    [],
    ['POST']
));
$routes->add('voteWithCode', new Route(
    '/vote/v/{code}',
    [
        'controller' => Controllers\VotingController::class,
        'action' => 'codeEntry'
    ]
));
$routes->add('simpleResults', new Route(
    '/winners',
    [
        'controller' => Controllers\ResultController::class,
        'action' => 'simple',
        'permission' => 'voting-results'
    ]
));
$routes->add('detailedResults', new Route(
    '/results/{all}',
    [
        'controller' => Controllers\ResultController::class,
        'action' => 'detailed',
        'all' => null,
        'permission' => 'voting-results',
    ],
    [
        'all' => '(all)?'
    ]
));
$routes->add('pairwiseResults', new Route(
    '/results/pairwise',
    [
        'controller' => Controllers\ResultController::class,
        'action' => 'pairwise',
        'permission' => 'voting-results',
    ]
));
$routes->add('countdown', new Route(
    '/countdown',
    [
        'controller' => Controllers\LauncherController::class,
        'action' => 'countdown'
    ]
));
$routes->add('stream', new Route(
    '/stream',
    [
        'controller' => Controllers\LauncherController::class,
        'action' => 'stream',
        'permission' => 'voting-results'
    ]
));

/** @var Config $config */
$config = $em->getRepository(Config::class)->findOneBy([]);
$defaultRoute = $routes->get($config->getDefaultPage());

if ($defaultRoute) {
    $index = clone $defaultRoute;
    $index->setPath('/');
    $routes->add('index', $index);
}

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
    if ($route = $routes->get($routeName)) {
        // Only show items in the menu if the user has access to them
        $permission = $route->getDefault('permission');
        if (!$permission || $user->canDo($permission)) {
            $navbar[$routeName] = $title;
        }
    }
}

$twig->addGlobal('navbarItems', $navbar);

$matcher = new UrlMatcher($routes, $context);

$container = new DependencyContainer(
    $em,
    $request,
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
        ->setUserAgent($request->server->get('HTTP_USER_AGENT', ''))
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

//$ACCESS = array(
//    // Volatile pages
//    "applications" => "applications-view",
//    "credits" => EVERYONE,
//    "test" => EVERYONE,
//    "thanks" => EVERYONE,
//    "volunteer-submission" => LOGIN,
//    "videos" => EVERYONE,
//);
//
//// Pages that won't use the master template
//$noMaster = array(
//    "thanks",
//    "voting"
//);
//
//// Pages so basic they don't need a PHP file.
//$noPHP = array(
//    "about" => "About",
//    "videos" => "Video Submission",
//);
//
//$noContainer = array(
//    "videos"
//);
