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
    ['_controller' => 'AppBundle:VideoGames:index', 'permission' => 'ROLE_ADD_VIDEO_GAME']
))->setMethods('GET'));

$routes->add('addVideoGame', (new Route(
    '/vidya-in-2017',
    ['_controller' => 'AppBundle:VideoGames:add']
))->setMethods('POST'));

//
// CREW
//

$routes->add('people', new Route(
    '/crew',
    ['_controller' => 'AppBundle:People:index']
));

$routes->add('permissions', new Route(
    '/crew/permissions',
    ['_controller' => 'AppBundle:People:permissions']
));

$routes->add('addPerson', new Route(
    '/crew/new',
    ['_controller' => 'AppBundle:People:new']
));

$routes->add('userSearch', (new Route(
    '/crew/search',
    ['_controller' => 'AppBundle:People:search']
))->setMethods('POST'));

$routes->add('viewPerson', new Route(
    '/crew/{steamID}',
    ['_controller' => 'AppBundle:People:view'],
    ['steamID' => '\d+']
));

$routes->add('editPerson', (new Route(
    '/crew/{steamID}/edit',
    ['_controller' => 'AppBundle:People:edit'],
    ['steamID' => '\d+']
))->setMethods('GET'));

$routes->add('editPersonPost', (new Route(
    '/crew/{steamID}/edit',
    ['_controller' => 'AppBundle:People:post'],
    ['steamID' => '\d+']
))->setMethods('POST'));

//
// AWARDS
//

$routes->add('awards', (new Route(
    '/awards',
    ['_controller' => 'AppBundle:Award:index', 'permission' => 'ROLE_AWARDS_EDIT']
))->setMethods('GET'));

$routes->add('awardFrontendPost', (new Route(
    '/awards',
    ['_controller' => 'AppBundle:Award:post', 'permission' => 'ROLE_AWARDS_EDIT']
))->setMethods('POST'));

$routes->add('awardManager', (new Route(
    '/awards/manage',
    ['_controller' => 'AppBundle:AwardAdmin:managerList']
))->setMethods('GET'));

$routes->add('awardManagerPost', (new Route(
    '/awards/manage',
    ['_controller' => 'AppBundle:AwardAdmin:managerPost']
))->setMethods('POST'));

$routes->add('awardManagerPostAjax', (new Route(
    '/awards/manage/ajax',
    ['_controller' => 'AppBundle:AwardAdmin:managerPostAjax']
))->setMethods('POST'));

$routes->add('nomineeManager', (new Route(
    '/nominees/{awardID}',
    ['_controller' => 'AppBundle:Nominee:index', 'awardID' => null]
))->setMethods('GET'));

$routes->add('nomineePost', (new Route(
    '/nominees/{awardID}',
    ['_controller' => 'AppBundle:Nominee:post']
))->setMethods('POST'));

//
// VOTING
//

$routes->add('viewVotingCode', new Route(
    '/vote/code',
    ['_controller' => 'AppBundle:Voting:codeViewer']
));

$routes->add('voting', (new Route(
    '/vote/{awardID}',
    ['_controller' => 'AppBundle:Voting:index', 'awardID' => null, 'permission' => 'ROLE_VOTING_VIEW']
))->setMethods('GET'));

$routes->add('votingSubmission', (new Route(
    '/vote/{awardID}',
    ['_controller' => 'AppBundle:Voting:post', 'permission' => 'ROLE_VOTING_VIEW']
))->setMethods('POST'));

$routes->add('voteWithCode', new Route(
    '/vote/v/{code}',
    ['_controller' => 'AppBundle:Voting:codeEntry', 'permission' => 'ROLE_VOTING_VIEW']
));

//
// RESULTS
//

$routes->add('winners', (new Route(
    '/winners',
    ['_controller' => 'AppBundle:Result:simple', 'permission' => 'ROLE_VOTING_RESULTS']
))->setMethods('GET'));

$routes->add('winnerImageUpload', (new Route(
    '/winners',
    ['_controller' => 'AppBundle:Result:winnerImageUpload']
))->setMethods('POST'));

$routes->add('results', new Route(
    '/results/{all}',
    ['_controller' => 'AppBundle:Result:detailed', 'all' => null, 'permission' => 'ROLE_VOTING_RESULTS'],
    ['all' => '(all)?']
));

$routes->add('pairwiseResults', new Route(
    '/results/pairwise',
    ['_controller' => 'AppBundle:Result:pairwise', 'permission' => 'ROLE_VOTING_RESULTS']
));

//
// REFERRERS
//

$routes->add('referrers', new Route(
    '/referrers',
    ['_controller' => 'AppBundle:Referrer:index']
));

//
// LAUNCHER
//

$routes->add('countdown', new Route(
    '/countdown',
    ['_controller' => 'AppBundle:Launcher:countdown', 'permission' => 'ROLE_VIEW_UNFINISHED_PAGES']
));

$routes->add('stream', new Route(
    '/stream',
    ['_controller' => 'AppBundle:Launcher:stream', 'permission' => 'ROLE_VIEW_UNFINISHED_PAGES']
));

$routes->add('finished', new Route(
    '/finished',
    ['_controller' => 'AppBundle:Launcher:finished', 'permission' => 'ROLE_VIEW_UNFINISHED_PAGES']
));

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

$routes->add('credits', new Route(
    '/credits',
    ['_controller' => 'AppBundle:Static:credits', 'permission' => 'ROLE_VIEW_UNFINISHED_PAGES']
));

$routes->add('resultRedirect', new Route(
    '/voting/results',
    ['_controller' => 'FrameworkBundle:Redirect:redirect', 'route' => 'results']
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
