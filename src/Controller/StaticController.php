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
        $preshow = [
            ['VGAs 2020 Set', 'radiomuff', 'Preshow'],
            ['nostalgia junkie\'s vidya gaem awards 2020 set', 'nostalgia_junkie', 'Preshow'],
            ['VGA 2021 mix_3', 'fv.exe', 'Preshow'],
            ['nj vga 2020 beta', 'nostalgia_junkie', 'Preshow'],
            ['beat_shobon vgas2020 mix', 'beat_shobon', 'Preshow'],
            ['2020 VGA\'s Mini-mix', 'W.T. Snacks', 'Preshow'],
        ];

        $tracks = [
            ['HIKA TO KAGE', 'The Five Star Stories Movie OST', '"Hello, Fellow Posters" Award'],
            ['Metropolis, Planet Kerwan', 'David Bergeaud', 'IP Twist Award'],
            ['FBI - Headquarters - Interrogation', 'XIII OST', 'Haptic Feedback Award'],
            ['Dancer', 'Tom & Jerry', 'Blue Checkmark Award'],
            ['Don\'t Look', 'Silent Partner', 'New Challenger Award'],
            ['SimCity 4 OST', '', 'Hyperbole Award'],
            ['Luminosa', '', 'Smug Pessimist Award'],
            ['Main Theme', 'The Room', 'Guilty Pleasure Award'],
            ['Bubble Star', 'Thelonious Monkees', 'Balkanization Award'],
            ['The Mole', 'Michael McCann', 'Hate Machine Award'],
            ['Mute City', 'F-Zero', '/vr/ Award'],
            ['Ezio\'s Family', 'Jesper Kyd', 'Redemption Arc Award'],
            ['Maiden Voyage', 'Global Communication', 'Fahrenheit 2020 Award'],
            ['Apocalypse', 'Jesper Kyd', 'Pottery Award'],
            ['L no Shisou', 'Tanichui Hideki', 'PIXELS ARE 8RT Award'],
            ['Fuan', 'Taniuchi Hideki', 'Ocarina of All Time Award'],
            ['BGM001 (Title Theme)', 'Custom Maid 3D 2', 'Kaimge Award'],
            ['Ignition', '', 'Awardgate'],
            ['Angel', 'Massive Attack', 'Seal of Quality Award'],
            ['Frolic', 'Luciano Michelini', 'Seal of Quality Award'],
            ['Hazardous Environments', 'Half-Life OST', '!votemap Award'],
            ['windy bay', 'yuji ohno', 'Plot and Backstory Award'],
            ['Rock Me Amadeus (Canadian/American \'86 Mix)', 'Falco', 'Plot and Backstory Award'],
            ['Damn, I Wish I Was Your Lover', 'Sophie B. Hawkins', 'The Van Darkholme Award'],
            ['Pedestrians Crossing', 'Brenton Kossak, Blaine McGurty', 'The Little Game That Could Award'],
            ['Minueto', 'Luigi Boccherini', 'Jackie Chan Award'],
            ['Figaro (instrumental)', 'MF DOOM', 'Scrappy Doo Award'],
            ['Underwater Exploration', 'Godmode', 'Home and Hearth Award'],
            ['Tuesday (instrumental)', 'Malibu Ken', 'A E S T H E T I C S Award'],
            ['Jinmenken', 'Tobacco', 'A E S T H E T I C S Award'],
            ['Motherfuckers 64', 'Tobacco', 'A E S T H E T I C S Award'],
            ['Refbatch', 'Tobacco', 'A E S T H E T I C S Award'],
            ['Churro (instrumental)', 'Malibu Ken', 'A E S T H E T I C S Award'],
            ['Bad Fuckin Times', 'Black Moth Super Rainbow', 'A E S T H E T I C S Award'],
            ['Graphics Misers', '/v/ The Musical IV', 'A S S T H E T I C S Award'],
            ['Theme', 'Donkey Kong Country', 'DK Award Returns'],
            ['Epoitomize', 'Helltaker', 'DK Award Returns'],
            ['Due Recompense', 'FINAL FANTASY VII Remake', 'DK Award Returns'],
            ['Scenario Battle', 'Yakuza: Like a Dragon', 'DK Award Returns'],
            ['The Only Thing they Fear is You', 'Doom Eternal', 'DK Award Returns'],
            ['Out of Tartarus', 'Hades', 'DK Award Returns'],
            ['Bonus Room Blitz', 'Donkey Kong Country', 'DK Award Returns'],
            ['The Super Gore Nest', 'Doom Eternal', 'DK Award Returns'],
            ['In Deep', 'Frank Klepacki', '/v2k/ Award'],
            ['Intro Sequence', 'Alexander Brandon', '/v2k/ Award'],
            ['Red and Gold (instrumental)', 'MF DOOM', 'Humble Award Bundle'],
            ['Goodbye Horses (instrumental)', 'Q Lazzarus', 'Most Hated Award'],
            ['Menu Music', 'Grand Theft Auto V', 'Least Hated Award'],
        ];

        return $this->render('soundtrack.html.twig', [
            'preshow' => $preshow,
            'tracks' => $tracks
        ]);
    }

    public function creditsAction()
    {
        return $this->render('credits.html.twig');
    }
}
