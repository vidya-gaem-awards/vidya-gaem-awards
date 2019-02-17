<?php
namespace App\Controller;

use App\Service\ConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\RouterInterface;

class StaticController extends AbstractController
{
    public function indexAction(RouterInterface $router, ConfigService $configService)
    {
        $defaultPage = $configService->getConfig()->getDefaultPage();
        $defaultRoute = $router->getRouteCollection()->get($defaultPage);

        return $this->forward($defaultRoute->getDefault('_controller'), $defaultRoute->getDefaults());
    }

    public function videosAction()
    {
        return $this->render('videos.html.twig');
    }

    public function soundtrackAction()
    {
        $tracks = [
            ['nelward - Artificial Intelligence Kong [from STAFFcirc Vol. II]', '/v/irgin Award'],
            ['Dream Catcher - Kevin MacLeod', 'IP Twist Award'],
            ['Laser Groove - Kevin MacLeod', '"But They\'ll Patch It!" Award'],
            ['Soul Searching - Noa', 'Guilty Pleasure Award'],
            ['Tomorrow - Bensound', 'NieR Award'],
            ['Journey Home - Day 7', 'NieR Award'],
            ['Super Power Cool Dude - Kevin MacLeod', 'The Sipp Boi Award'],
            ['Bay Breeze - FortyThr33', 'Paul Allen Award'],
            ['Paisley - TEAM MANDALA', 'CS Grad Award'],
            ['Todd and the sweet little lies - Crowbcat', 'CS Grad Award'],
            ['if only - rook1e', '2B Award'],
            ['Nostalgia - Tobu', 'Peebee Award'],
            ['Pyres - Broken Elegance', 'Who Cares Award'],
            ['Flow is Overflowing (Instrumental) - My room Records', 'Make it Stop Award'],
            ['Henry\'s Monstrocity - Nelward', 'The Little Game that Could Award'],
            ['Habanera - Kevin Macleod', 'PIXEL5 R A4T Award'],
            ['BarkerHQ - TEAL MANDALA', 'Seal of Quality Award'],
            ['Finger 11 - Paralyzer', 'Newgrounds Award'],
            ['ParagoX9 - Choaz Fantasy', 'Newgrounds Award'],
            ['Hong Kong 97 Theme', 'Kamige Award'],
            ['Nowtro - Neon Marathon (Droid Bishop remix)', '/v/3 Award'],
            ['Changing Every Day', 'Tablet Mode Award'],
            ['想死个人的兵哥哥', '4K ULTRA HD 2160P RETINA DISPLAY Award'],
            ['Maria Lisa - You Make Me Feel', 'AESTHETICS Award'],
            ['Vive - I Need Your Loving', 'AESTHETICS Award'],
            ['B.G. The Prince Of Rap - The Colour Of My Dreams (TNT Party Prince-Mix)', 'AESTHETICS Award'],
            ['Beat System - Stay With Me (Club Mix)', 'AESTHETICS Award'],
            ['Kristy - Crazy Crazy', 'AESTHETICS Award'],
            ['Piece of Cake - Run Away (Terminate Club Mix)', 'AESTHETICS Award'],
            ['Affectionate Land', 'Button Masher Award'],
            ['Shenmue II - Ohshu Soba', 'Press X to Win the Award'],
            ['Hua\'er', 'Hate Machine Award'],
            ['Dreamscape - 009 Sound System', 'Downward Spiral Award'],
            ['Why Do I have 6000 Followers - McMangos', 'Hyperbole Award'],
            ['Windows 98 Introductory Music', 'Windows 98 Award'],
            ['Katamari on the Rocks', 'Katamari Award'],
            ['Tetris Effect - Pharaoh\'s Code', 'Katamari Award'],
            ['Deltarune - Rude Buster', 'Katamari Award'],
            ['Splatoon 2 Octo Expansion - frisk (Dedf1sh)', 'Katamari Award'],
            ['Octopath Traveler - Battle I', 'Katamari Award'],
            ['Super Smash Bros Ulimate - F-ZERO Medley', 'Katamari Award'],
            ['Super Smash Bros Ulimate - Gang-Plank Galleom Remix', 'Katamari Award'],
            ['草帽丢了', 'Aniki Award'],
            ['A Sunny Day', 'Plot and Backstory Award'],
            ['Let Go - Glue70', 'Most Hated Award'],
            ['Bad Dreams - whyetc', 'Least Worst Award'],
        ];

        return $this->render('soundtrack.html.twig', [
            'tracks' => $tracks
        ]);
    }

    public function creditsAction()
    {
        return $this->render('credits.html.twig');
    }
}
