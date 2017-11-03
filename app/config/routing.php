<?php
use AppBundle\Controller;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();
$routes->add('privacy', new Route(
    '/privacy',
    [
        '_controller' => 'FrameworkBundle:Template:template',
        'template' => 'privacy.twig',
    ]
));
$routes->add('videos', new Route(
    '/videos',
    [
        '_controller' => 'AppBundle:Static:videos',
//        'permission' => $config->isPagePublic('videos') ? false : 'view-unfinished-pages'
    ]
));
$routes->add('soundtrack', new Route(
    '/soundtrack',
    [
        '_controller' => 'AppBundle:Static:soundtrack',
//        'permission' => $config->isPagePublic('music') ? false : 'view-unfinished-pages'
    ]
));
$routes->add('resultRedirect', new Route(
    '/voting/results',
    [
        '_controller' => 'FrameworkBundle:Redirect:redirect',
        'route' => 'detailedResults'
    ]
));
$routes->add('news', new Route(
    '/news',
    [
        '_controller' => 'AppBundle:News:index'
    ]
));
$routes->add('newsAdd', new Route(
    '/news/add',
    [
        '_controller' => 'AppBundle:News:add',
        'permission' => 'news-manage'
    ],
    [],
    [],
    '',
    [],
    ['POST']
));
$routes->add('newsDelete', new Route(
    '/news/delete/{id}',
    [
        '_controller' => 'AppBundle:News:delete',
        'permission' => 'news-manage'
    ],
    ['id' => '\d+'],
    [],
    '',
    [],
    ['POST']
));
$routes->add('login_check', new Route(
    '/login'
));
$routes->add('logout', new Route(
    '/logout'
));

return $routes;
