<?php
namespace AppBundle\Controller;

use AppBundle\Service\ConfigService;
use AppBundle\Service\NavbarService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LauncherController extends Controller
{
    public function countdownAction(ConfigService $configService, NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('countdown')) {
            throw $this->createAccessDeniedException();
        }

        $streamDate = $configService->getConfig()->getStreamTime();

        $timezones = [
            'Honolulu' => 'Pacific/Honolulu',
            'Anchorage' => 'America/Anchorage',
            'Los Angeles (PST)' => 'America/Los_Angeles',
            'Denver (MST)' => 'America/Denver',
            'Chicago (CST)' => 'America/Chicago',
            '4chan Time (EST)' => 'America/New_York',
            'Rio de Janeiro' => 'America/Sao_Paulo',
            'London (GMT)' => 'Europe/London',
            'Paris (CET)' => 'Europe/Paris',
            'Athens (EET)' => 'Europe/Athens',
            'Moscow' => 'Europe/Moscow',
            'Singapore' => 'Asia/Singapore',
            'Japan Time' => 'Asia/Tokyo',
            'Brisbane (AEST)' => 'Australia/Brisbane',
            'Sydney (AEDT)' => 'Australia/Sydney',
            'Auckland' => 'Pacific/Auckland',
        ];

        $otherTimezonesLink = sprintf(
            'https://www.timeanddate.com/worldclock/fixedtime.html?msg=2017+Vidya+Gaem+Awards&iso=%s&p1=179',
            $streamDate ? $streamDate->format("Y-m-d\TH:i:s") : ''
        );

        return $this->render('countdown.html.twig', [
            'streamDate' => $streamDate,
            'timezones' => $timezones,
            'otherTimezonesLink' => $otherTimezonesLink
        ]);
    }

    public function streamAction(ConfigService $configService, NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('stream')) {
            throw $this->createAccessDeniedException();
        }

        $streamDate = $configService->getConfig()->getStreamTime();
        $showCountdown = ($streamDate > new \DateTime());

        return $this->render('stream.html.twig', [
            'streamDate' => $streamDate,
            'countdown' => $showCountdown
        ]);
    }

    public function finishedAction(NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('finished')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('finished.html.twig');
    }
}
