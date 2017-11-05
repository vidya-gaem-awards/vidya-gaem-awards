<?php
use Symfony\Component\Debug\Debug;
use Symfony\Component\Routing\RequestContext;
use VGA\DependencyManager;
use AppBundle\Entity\Access;

// The false parameter to getEntityManager is very important: if removed, the timezone of all DateTime objects from
// Doctrine will be the server default instead of what's in our config.
Config::initalizeTimezone(DependencyManager::getEntityManager(false));

if ($user->canDo('view-debug-output')) {
    Debug::enable();
}

$context = new RequestContext();
$context->fromRequest($request);
// Due to the way that Cloudflare is set up, the user sees HTTPS but our server only sees HTTP.
// We manually update some values to pretend we have full HTTPS, else generated links will have the wrong protocol.
$_SERVER['HTTPS'] = 'on';
$context->setScheme('https');

// Log the page access
$access = new Access();

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
