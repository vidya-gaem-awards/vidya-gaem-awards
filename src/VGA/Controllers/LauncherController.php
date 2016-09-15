<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VGA\Model\Config;

class LauncherController extends BaseController
{
    public function countdownAction()
    {
        $streamDate = $this->config->getStreamTime();

        $timezones = [
            'Honolulu' => 'Pacific/Honolulu',
            'Anchorage' => 'America/Anchorage',
            'Los Angeles (PDT)' => 'America/Los_Angeles',
            'Denver (MDT)' => 'America/Denver',
            'Chicago (CDT)' => 'America/Chicago',
            '4chan Time (EDT)' => 'America/New_York',
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
            'https://www.timeanddate.com/worldclock/fixedtime.html?msg=2015+Vidya+Gaem+Awards&iso=%s&p1=179',
            $streamDate->format("Y-m-d\TH:i:s")
        );

        $tpl = $this->twig->loadTemplate('countdown.twig');
        $response = new Response($tpl->render([
            'streamDate' => $streamDate,
            'timezones' => $timezones,
            'otherTimezonesLink' => $otherTimezonesLink
        ]));
        $response->send();
    }

    public function streamAction()
    {
        $tpl = $this->twig->loadTemplate('stream.twig');

        $streamDate = $this->config->getStreamTime();
        $showCountdown = ($streamDate > new \DateTime());
        
        $response = new Response($tpl->render([
            'streamDate' => $streamDate,
            'countdown' => $showCountdown
        ]));
        $response->send();
    }

    public function finishedAction()
    {
        $tpl = $this->twig->loadTemplate('finished.twig');
        $response = new Response($tpl->render([]));
        $response->send();
    }
}
