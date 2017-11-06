<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Config;
use AppBundle\Service\AuditService;
use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Action;
use AppBundle\Entity\TableHistory;

class ConfigController extends Controller
{
    public function indexAction(ConfigService $config)
    {
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $timezonesByArea = [];
        foreach ($timezones as $timezone) {
            list($area, ) = explode('/', $timezone);
            $timezonesByArea[$area][$timezone] = str_replace('_', ' ', $timezone);
        }

        return $this->render('config.twig', [
            'title' => 'Config',
            'config' => $config->getConfig(),
            'timezones' => $timezonesByArea,
        ]);
    }

    public function postAction(EntityManagerInterface $em, ConfigService $configService, Request $request, AuditService $auditService)
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
            $config->setReadOnly(true);
            $em->persist($config);
            $em->flush();

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
}
