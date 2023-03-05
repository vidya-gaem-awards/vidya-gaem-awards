<?php
namespace App\Controller;

use App\Service\ConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class StaticController extends AbstractController
{
    public function indexAction(RouterInterface $router, ConfigService $configService): Response
    {
        $defaultPage = $configService->getConfig()->getDefaultPage();
        $defaultRoute = $router->getRouteCollection()->get($defaultPage);

        return $this->forward($defaultRoute->getDefault('_controller'), $defaultRoute->getDefaults());
    }

    public function videosAction(): Response
    {
        return $this->render('videos.html.twig');
    }

    public function soundtrackAction(): Response
    {
        $preshow = [
//            ['vgas2021 mix', 'beat_shobon', 'Preshow'],
//            ['MAIN MIX FOR FINAL EXPORT_2', 'fv.exe', 'Preshow'],
//            ['vga 2021', 'nostalgia_junkie', 'Preshow'],
//            ['vga2021', 'W.T. Snacks', 'Preshow'],
        ];

        $tracks = [
            ['All The Things She Said (Instrumental)', 't.A.T.u', 'Most Hated Award'],
            ['The Concept of Love (Concept of Passion)', 'Ollie King OST', 'Least Worst Award'],
            ['Little Samba', 'Quincas Moreira', 'Press X to Win the Award'],
            ['DROP OUT', 'DDR Extreme', 'Scrappy Doo Award'],
            ['A New Morning', 'Erik Suhrk', 'Diamond in the Rough Award'],
            ['Road Tripzzz', 'Ofshane', 'Hate Machine Award'],
            ['Slipping Away', 'Dyalla', 'Hate Machine Award'],
            ['Loser (Instrumental)', 'Beck', 'Seal of Quality Award'],
            ['Le Pain Perdu', 'Cibo Matto', 'P10XELS ARE AR10 Award'],
            ['Your Vibe (J99 Remix)', 'R4 - The 20th Anniv. Sounds', 'Kamige Award'],
            ['Can Can Bunny Superior OST', 'Cocktail Soft', 'Plot and Backstory Award'],
            ['Calvin Harris', 'Josh Pan', 'Van Darkholme Award'],
            ['Hannon', 'Jeremy Black', 'Van Darkholme Award'],
            ['Tag Walls, Punch Fascists', '2 Mello', 'Redemption Arc Award'],
            ['Bad Apple!!', 'Masayoshi Minoshima', 'Cirno\'s Perfect Math Class Award'],
            ['Objection', 'Ace Attorney Orchestra', 'PowerPoint Award'],
            ['Little Fish', 'YouTube Audio Library', 'PowerPoint Award'],
            ['Something Memorable', 'Kn1ght', 'Borne of Metal Souls Award'],
            ['Let It Go', 'Ollie King', 'IP Twist Award'],
            ['Party Without Me', 'kmlkmljkl', 'Morbius Sweep Award'],
            ['ramen beat', 'layo', 'Paul Allen\'s Award'],
            ['Honey', 'Erykah Badu', 'LIGHTNING ROUND'],
            ['Seashanty2', 'Old School Runescape', 'Sweep Point Tutorial'],
        ];

        return $this->render('soundtrack.html.twig', [
            'preshow' => $preshow,
            'tracks' => $tracks
        ]);
    }

    public function creditsAction(): Response
    {
        return $this->render('credits.html.twig');
    }

    public function trailersAction(): Response
    {
        return $this->render('trailers.html.twig');
    }
}
