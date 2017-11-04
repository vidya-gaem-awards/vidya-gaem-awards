<?php
use AppBundle\Controller;
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
use VGA\DependencyContainer;
use VGA\DependencyManager;
use AppBundle\Entity\Access;
use AppBundle\Entity\AnonymousUser;
use AppBundle\Entity\Config;
use AppBundle\Entity\LoginToken;
use AppBundle\Entity\User;

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
    ->setIP($request->server->get('HTTP_CF_CONNECTING_IP', $request->server->get('REMOTE_ADDR')))
    ->setRandomID($randomID)
    ->setVotingCode($votingCode);

/** @var Config $config */
$config = $em->getRepository(Config::class)->findOneBy([]);

// Define the routes
$routes = new RouteCollection();

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
//if ($user instanceof AnonymousUser) {
//    $returnLink = $generator->generate(
//        'login',
//        ['return' => $request->getPathInfo()],
//        UrlGenerator::ABSOLUTE_URL
//    );
//
//    $steam = new SteamLogin();
//    $twig->addGlobal('steamLoginLink', $steam->url($returnLink));
//}

//$navbar = [];
//foreach (NAVBAR_ITEMS as $routeName => $title) {
//    if ($route = $routes->get($routeName)) {
//        // Only show items in the menu if the user has access to them
//        $permission = $route->getDefault('permission');
//        if (!$permission || $user->canDo($permission)) {
//            $navbar[$routeName] = $title;
//        }
//    }
//}
//
//$twig->addGlobal('navbarItems', $navbar);

$matcher = new UrlMatcher($routes, $context);

$container = new DependencyContainer(
    $em,
    $request,
    $twig,
    $session,
    $user,
    $generator,
    $config
);

// Call the correct controller and method
try {
    $match = $matcher->match($request->getPathInfo());

    if (!class_exists($match['controller'])) {
        $controller = new Controller\ErrorController($container);
        $controller->internalErrorAction();
        return;
    }

    /** @var Controller\BaseController $controller */
    $controller = new $match['controller']($container);

    if (isset($match['action'])) {
        $action = $match['action'] . 'Action';
    } else {
        $action = 'indexAction';
    }

    if (isset($match['permission']) && $match['permission'] !== false && !$user->canDo($match['permission'])) {
        /** @var Controller\ErrorController $controller */
        $controller = new Controller\ErrorController($container);
        if ($user->isLoggedIn()) {
            $controller->noAccessAction();
        } else {
            $controller->needLoginAction();
        }
        return;
    }

    if (!method_exists($controller, $action)) {
        /** @var Controller\ErrorController $controller */
        $controller = new Controller\ErrorController($container);
        $controller->internalErrorAction();
        return;
    }

    // Log the page access
    $access = new Access();
    if (!($user instanceof AnonymousUser)) {
        $access->setUser($user);
    }

    if (!$config->isReadOnly()) {
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
    }

    unset($match['controller']);
    unset($match['action']);
    unset($match['permission']);
    unset($match['_route']);
    call_user_func_array([$controller, $action], $match);

} catch (ResourceNotFoundException $e) {
    $controller = new Controller\ErrorController($container);
    $controller->notFoundAction();
    return;
} catch (MethodNotAllowedException $e) {
    $controller = new Controller\ErrorController($container);
    $controller->wrongMethodAction();
    return;
}
