<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;

class LauncherController extends BaseController
{
    public function countdownAction()
    {
        $streamDate = new \DateTime('2016-03-26T16:00:00-04:00');

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

//        foreach ($timezones as $name => $timezone) {
//            $date = clone $streamDate;
//            $date->setTimeZone(new \DateTimeZone($timezone));
//            dump($date->format('r'));
////            $offset = $date->format('P');
////            $time = $date->format('D M jS, H:i');
//            $timezones[$name] = $date;
//        }

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
}
