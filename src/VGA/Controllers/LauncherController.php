<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;

class LauncherController extends BaseController
{
    public function countdownAction()
    {
        $streamDate = $this->config->getStreamTime();

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
            'https://www.timeanddate.com/worldclock/fixedtime.html?msg=2016+Vidya+Gaem+Awards&iso=%s&p1=179',
            $streamDate ? $streamDate->format("Y-m-d\TH:i:s") : ''
        );

        $tpl = $this->twig->load('countdown.twig');
        $response = new Response($tpl->render([
            'streamDate' => $streamDate,
            'timezones' => $timezones,
            'otherTimezonesLink' => $otherTimezonesLink
        ]));
        $response->send();
    }

    public function streamAction()
    {
        $tpl = $this->twig->load('stream.twig');

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
        $tpl = $this->twig->load('finished.twig');
        $response = new Response($tpl->render([]));
        $response->send();
    }
}
