<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\Config;
use App\Entity\TableHistory;
use App\Service\AuditService;
use App\Service\ConfigService;
use App\Service\CronJobService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class ConfigController extends Controller
{
    public function indexAction(ConfigService $configService, CronJobService $cron, RouterInterface $router)
    {
        $config = $configService->getConfig();

        $navbarConfig = [];
        foreach ($config->getNavbarItems() as $routeName => $label) {
            $navbarConfig[] = "$routeName: $label";
        }

        // Ultra alerts are very important and appear with a blinding red background to encourage you to fix the issue ASAP
        $ultraAlerts = [];
        if ($config->getStreamTime()) {
            if (new \DateTime() < $config->getStreamTime() && $config->isPagePublic('results')) {
                $ultraAlerts[] = 'The results page is public, but the stream date hasn\'t passed yet.';
            }
            if (!$cron->isCronJobEnabled() && $config->isVotingOpen()) {
                $ultraAlerts[] = 'Voting is open, but the results generator hasn\'t been activated.';
            }
            if (!$config->isPagePublic($config->getDefaultPage())) {
                $ultraAlerts[] = 'The default page doesn\'t have public access turned on.';
            }
        }

        return $this->render('config.html.twig', [
            'title' => 'Config',
            'config' => $config,
            'navigationBarConfig' => implode("\n", $navbarConfig),
            'routes' => $this->getValidNavbarRoutes($router->getRouteCollection()),
            'cronEnabled' => $cron->isCronJobEnabled(),
            'ultraAlerts' => $ultraAlerts,
        ]);
    }

    public function postAction(EntityManagerInterface $em, ConfigService $configService, Request $request, AuditService $auditService, RouterInterface $router, CronJobService $cron)
    {
        $config = $configService->getConfig();
        
        if ($config->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.'
                . ' To disable read-only mode, you will need to edit the database directly.');
            return $this->redirectToRoute('config');
        }

        $post = $request->request;

        $error = false;

        if ($post->get('readOnly')) {
            $config->setAwardSuggestions(false);
            $config->setReadOnly(true);
            $em->persist($config);
            $em->flush();

            // It's extremely likely that the cron job will already be disabled prior to the site being put into
            // read-only mode. Nonetheless, we make absolutely sure that it is disabled anyway, because once read-only
            // mode is active, the controls for the cron job become unavailable.
            $cron->disableCronJob();

            $auditService->add(
                new Action('config-readonly-enabled')
            );

            $this->addFlash('success', 'Read-only mode has been successfully enabled.');
            return $this->redirectToRoute('config');
        }

        if (!$post->get('votingStart')) {
            $config->setVotingStart(null);
        } else {
            try {
                $config->setVotingStart(new \DateTime($post->get('votingStart')));
            } catch (\Exception $e) {
                $this->addFlash('error', 'Invalid date provided for voting start.');
                $error = true;
            }
        }

        if (!$post->get('votingEnd')) {
            $config->setVotingEnd(null);
        } else {
            try {
                $config->setVotingEnd(new \DateTime($post->get('votingEnd')));
            } catch (\Exception $e) {
                $this->addFlash('error', 'Invalid date provided for voting end.');
                $error = true;
            }
        }

        if (!$post->get('streamTime')) {
            $config->setStreamTime(null);
        } else {
            try {
                $config->setStreamTime(new \DateTime($post->get('streamTime')));
            } catch (\Exception $e) {
                $this->addFlash('error', 'Invalid date provided for stream time.');
                $error = true;
            }
        }

//        try {
//            $config->setTimezone($post->get('timezone'));
//        } catch (\Exception $e) {
//            $this->addFlash('error', 'Invalid timezone provided.');
//            $error = true;
//        }

        $config->setDefaultPage($post->get('defaultPage'));
        $config->setPublicPages(array_keys($post->get('publicPages', [])));

        $navbarItems = explode("\n", $post->get('navigationMenu'));
        $navbarItems = array_map(function ($line) {
            $elements = explode(":", trim($line));
            return array_map('trim', $elements);
        }, $navbarItems);
        $navbarItems = array_column($navbarItems, 1, 0);

        $navbarError = false;
        $validRoutes = $this->getValidNavbarRoutes($router->getRouteCollection());
        foreach ($navbarItems as $routeName => $label) {
            if (substr($routeName, 0, 8) === 'dropdown') {
                continue;
            }
            if (!isset($validRoutes[$routeName])) {
                $this->addFlash('error', 'Invalid route specified in the navigation menu config (' . $routeName . ').');
                $navbarError = $error = true;
            }
            if (empty($label)) {
                $this->addFlash('error', 'No label provided for route ' . $routeName . ' in the navigation menu config.');
                $navbarError = $error = true;
            }
        }

        if (!$navbarError) {
            $config->setNavbarItems($navbarItems);
        }

        $config->setAwardSuggestions($post->getBoolean('awardSuggestions'));

        $em->persist($config);
        $em->flush();

        $auditService->add(
            new Action('config-updated', 1),
            new TableHistory(Config::class, 1, $post->all())
        );

        if (!$error) {
            $this->addFlash('success', 'Config successfully saved.');
        }

        return $this->redirectToRoute('config');
    }

    /**
     * Gets an array of routes that can be used in the top navigation bar.
     * @param RouteCollection $routeCollection
     * @return Route[] An array of routes, indexed by the route name.
     */
    private function getValidNavbarRoutes(RouteCollection $routeCollection)
    {
        $routes = array_filter($routeCollection->all(), function (Route $route, $routeName) {
            // Ignore internal routes (includes the web profiler routes and login/logout)
            if ($routeName[0] === '_' || !$route->getDefault('_controller')) {
                return false;
            }

            // Ignore POST-only routes
            if ($route->getMethods() === ['POST']) {
                return false;
            }

            // Ignore any routes with required URL parameters
            foreach ($route->getRequirements() as $parameter => $requirement) {
                if (!array_key_exists($parameter, $route->getDefaults())) {
                    return false;
                }
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH);

        ksort($routes);
        return $routes;
    }

    public function cronAction(CronJobService $cron)
    {
        return $this->render('cron.html.twig', [
            'enabled' => $cron->isCronJobEnabled()
        ]);
    }

    public function cronPostAction(Request $request, CronJobService $cron, ConfigService $configService, AuditService $auditService)
    {
        $config = $configService->getConfig();
        $post = $request->request;
        $enable = $post->getBoolean('enable');

        if ($config->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return $this->redirectToRoute('cron');
        }

        $currentlyEnabled = $cron->isCronJobEnabled();
        if (!$currentlyEnabled && $enable) {
            $cron->enableCronJob();
            $auditService->add(
                new Action('cron-results-enabled')
            );
        } elseif ($currentlyEnabled && !$enable) {
            $cron->disableCronJob();
            $auditService->add(
                new Action('cron-results-disabled')
            );
        }

        if ($currentlyEnabled === $enable) {
            $this->addFlash(
                'success',
                'The result generator ' . ($currentlyEnabled ? 'is already active.' : 'has already been deactivated.')
            );
        } else {
            $this->addFlash(
                'success',
                'The result generator has been successfully ' . ($enable ? 'activated.' : 'deactivated.')
            );
        }
        return $this->redirectToRoute('cron');
    }
}
