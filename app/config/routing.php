<?php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();

//
// INTERNAL SYSTEM STUFF
//
$routes->add('index', new Route(
    '/',
    ['_controller' => 'AppBundle:Static:index']
));

$routes->add('login_check', new Route(
    '/login'
));

$routes->add('logout', new Route(
    '/logout'
));

//
// FRONT PAGE
//
$routes->add('home', new Route(
    '/home',
    ['_controller' => 'AppBundle:Index:index']
));

$routes->add('news', new Route(
    '/news',
    ['_controller' => 'AppBundle:News:index']
));

//
// NEWS
//
$routes->add('newsAdd', (new Route(
    '/news/add',
    ['_controller' => 'AppBundle:News:add']
))->setMethods('POST'));

$routes->add('newsDelete', (new Route(
    '/news/delete/{id}',
    ['_controller' => 'AppBundle:News:delete'],
    ['id' => '\d+']
))->setMethods('POST'));

//
// VIDEO GAMES
//
$routes->add('videoGames', (new Route(
    '/vidya-in-2017',
    ['controller' => 'AppBundle:VideoGames:index', 'permission' => 'ROLE_ADD_VIDEO_GAME']
))->setMethods('GET'));

$routes->add('addVideoGame', (new Route(
    '/vidya-in-2017',
    ['controller' => 'AppBundle:VideoGames:add']
))->setMethods('POST'));

//
// STATIC PAGES
//
$routes->add('privacy', new Route(
    '/privacy',
    ['_controller' => 'FrameworkBundle:Template:template', 'template' => 'privacy.twig']
));

$routes->add('videos', new Route(
    '/videos',
    ['_controller' => 'AppBundle:Static:videos', 'permission' => 'ROLE_VIEW_UNFINISHED_PAGES']
));

$routes->add('soundtrack', new Route(
    '/soundtrack',
    ['_controller' => 'AppBundle:Static:soundtrack', 'permission' => 'ROLE_VIEW_UNFINISHED_PAGES']
));

$routes->add('resultRedirect', new Route(
    '/voting/results',
    ['_controller' => 'FrameworkBundle:Redirect:redirect', 'route' => 'detailedResults']
));

//
// CONFIG
//
$routes->add('config', (new Route(
    '/config',
    ['_controller' => 'AppBundle:Config:index']
))->setMethods('GET'));

$routes->add('configPost', (new Route(
    '/config',
    ['_controller' => 'AppBundle:Config:post']
))->setMethods('POST'));

return $routes;
