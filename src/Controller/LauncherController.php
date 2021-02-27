<?php
namespace App\Controller;

use App\Entity\Advertisement;
use App\Service\ConfigService;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LauncherController extends AbstractController
{
    public function countdownAction(ConfigService $configService, EntityManagerInterface $em)
    {
        $timezone = new DateTimeZone($configService->getConfig()->getTimezone());

        $streamDate = new DateTimeImmutable($configService->getConfig()->getStreamTime()->format('Y-m-d H:i:s'), $timezone);

        $timezones = [
            'Honolulu' => 'Pacific/Honolulu',
            'Anchorage' => 'America/Anchorage',
            'Seattle (PST)' => 'America/Los_Angeles',
            'Denver (MST)' => 'America/Denver',
            'Chicago (CST)' => 'America/Chicago',
            'New York (EST)' => 'America/New_York',
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
            'https://www.timeanddate.com/worldclock/fixedtime.html?msg=2020+Vidya+Gaem+Awards&iso=%s&p1=179',
            $streamDate ? $streamDate->format("Y-m-d\TH:i:s") : ''
        );

        // Fake ads
        $adverts = $em->getRepository(Advertisement::class)->findBy(['special' => 0]);

        if (empty($adverts)) {
            $ad1 = $ad2 = false;
        } else {
            $ad1 = $adverts[array_rand($adverts)];
            $ad2 = $adverts[array_rand($adverts)];
        }

        return $this->render('countdown.html.twig', [
            'streamDate' => $streamDate,
            'timezones' => $timezones,
            'otherTimezonesLink' => $otherTimezonesLink,
            'ad1' => $ad1,
            'ad2' => $ad2,
        ]);
    }

    public function streamAction(ConfigService $configService, EntityManagerInterface $em)
    {
        $timezone = new DateTimeZone($configService->getConfig()->getTimezone());

        $streamDate = DateTimeImmutable::createFromMutable($configService->getConfig()->getStreamTime());
        $streamDate = $streamDate->setTimezone($timezone);

        $showCountdown = ($streamDate > new \DateTime('now', $timezone));

        // Fake ads
        $adverts = $em->getRepository(Advertisement::class)->findBy(['special' => 0]);

        if (empty($adverts)) {
            $ad1 = $ad2 = false;
        } else {
            $ad1 = $adverts[array_rand($adverts)];
            $ad2 = $adverts[array_rand($adverts)];
        }

        return $this->render('stream.html.twig', [
            'streamDate' => $streamDate,
            'countdown' => $showCountdown,
            'ad1' => $ad1,
            'ad2' => $ad2
        ]);
    }

    public function finishedAction(EntityManagerInterface $em)
    {
        // Fake ads
        $adverts = $em->getRepository(Advertisement::class)->findBy(['special' => 0]);

        if (empty($adverts)) {
            $ad1 = $ad2 = false;
        } else {
            $ad1 = $adverts[array_rand($adverts)];
            $ad2 = $adverts[array_rand($adverts)];
        }

        return $this->render('finished.html.twig', [
            'ad1' => $ad1,
            'ad2' => $ad2
        ]);
    }
}
