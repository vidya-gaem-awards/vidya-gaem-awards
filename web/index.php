<?php
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RequestContext;
use VGA\DependencyManager;
use AppBundle\Entity\Access;
use AppBundle\Entity\AnonymousUser;

// The false parameter to getEntityManager is very important: if removed, the timezone of all DateTime objects from
// Doctrine will be the server default instead of what's in our config.
Config::initalizeTimezone(DependencyManager::getEntityManager(false));

// Basic setup
$em = DependencyManager::getEntityManager();
$request = Request::createFromGlobals();
$session = new Session();
$session->start();

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

$context = new RequestContext();
$context->fromRequest($request);
// Due to the way that Cloudflare is set up, the user sees HTTPS but our server only sees HTTP.
// We manually update some values to pretend we have full HTTPS, else generated links will have the wrong protocol.
$_SERVER['HTTPS'] = 'on';
$context->setScheme('https');

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
