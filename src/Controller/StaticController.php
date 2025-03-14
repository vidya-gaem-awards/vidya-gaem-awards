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
            ['Misc. Jingles from "Dance Dance 2" (ダンスダンス2)', 'Daiichi Shokai (大一商会)', 'F2P Award'],
            ['Wizardry Variants Daphne - Victory', 'Hitoshi Sakimoto', 'F2P Award'],
            ['Misc. Voice Clips (and unintended background noise)', 'Banzai (TV series) (a.k.a. "Banzai! Place Your Bets Now!")', 'F2P Award'],
            ['Blue Dragon - Eternity Cover', 'CJ Vidal, Felp Bagatin & Allan Lobo (Original by Nobuo Uematsu, sung by Ian Gillan)', 'F2P Award', 'https://www.youtube.com/watch?v=R8egCfOvbRE'],
            ['Donkey Kong Country 2 - Enchanted Wood', 'Cover by Tendo, Original "Forest Interlud" by David Wise', 'F2P Award', 'https://www.youtube.com/watch?v=ZHZQSSXf_0Q'],
            ['FINAL FANTASY IV MAIN THEME 1st-FIELD BGM - WESTERN Arrange- style -> WILD ARMS','Lenneth\'s Music', 'F2P Award', 'https://www.youtube.com/watch?v=uOjGLftHitQ'],
            ['Red Dead Redemption', 'Woody Jackson', 'Least Worst Award'],
            ['Hurt (Instrumental)', 'Johnny Cash', 'Most Hated Award'],
            ['Unknown Banjo Song used from 2014', ' ', 'Deja Vu Award'],
            ['Boards of Canada', ' ', 'Deja Vu Award'],
            ['Boomeraction Theme Song', ' ', 'Boomerang Award'],
            ['Krazy Cat Rag', 'Pat Prilly, Harry Breuer','The Little Game that Could Award'],
            ['A Legend Forever Dawn of Mana Soundtrack', 'Kenji Ito', 'The Little Game that Could Award'],
            ['Wildstyle Pistolero: Mirage Saloon Zone Act 1 K Mix', 'Tee Lopes', 'Hate Machine Award'],
            ['Cuckoo Clock', 'Quincas Moreira', 'Pixels Are Art XII'],
            ['The Alphabet Song', 'The Green Orbs', 'Pixels Are Art XII'],
            ['Promise','Kohmi Hirose', 'Kamige Award'],
            ['Who Let The Dogs Out', 'Baha Men','Kamige Award'],
            ['FATE BREAKER (Jazz version)', 'Michiko Naruke', 'Leading Madames Award'],
            ['I Had A Feeling','TrackTribe', 'Dude Ranch Award'],
            ['Battlefield Heroes Theme', ' ', 'Gone too Soon Award'],
            ['Asfalt Tango', 'Fanfare Ciocarlia', 'Blood Meridian Award'],
            ['"A CAPTAIN\'S CURSE" MOUTHWASHING SONG', 'XTRATUNA', 'Blood Meridian Award'],
            ['Cornia\'s Theme', 'Mitsuhiro Kaneda - Unicorn Overlord OST', 'Blood Meridian Award'],
            ['My Name is Nobody Main Theme', 'Ennio Morricone (cover)', 'Chamber Pot Award'],
            ['God Took The Wrong Son', 'Gwinn', 'Chamber Pot Award'],
            ['Samurai Soul Instrumental [Harmonica Version]', 'Kohei Tanaka', 'IP Twist Award'],
            ['Samurai Soul [Long Version] (Original Karaoke)', 'Kohei Tanaka', 'IP Twist Award'],
            ['Love Story', 'Layo & Bushwacka', 'The Least Worst of Most Hated Award'],
            ['reboot, menu themes, happy fake, boss theme', 'Radirgy 2', 'Radirgy Award'],
            ['Hydrogen','M.O.O.N.', 'Haptic Feedback Award'],
            ['Carlito', 'Russkij Pusskij', 'Carlito Award'],
            ['Dragon Age: The Veilguard - Taash Theme A', ' ', 'The Hateful Eight Award'],
            ['D0n\'t Taunt Me' , 'John Marwin', 'Untaken Meds Award'],
            ['Silent Partner', 'Tucson', 'My Son Award'],
            ['Barely Small', 'Freedom Trail Studio', 'My Son Award'],
            ['A Fistful of Dollars', 'Ennio Morricone', 'Credits'],
            ['Next Episode', 'Shiro Sagisu', 'NEXT TIME'],
            ['Godless', 'The Dandy Warhols', 'Intermission'],
            ['Eminence Front','The Who', 'Intermission'],
            ['Snakes in the Grass', 'Quantic', 'I hate Disclosures'],
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
