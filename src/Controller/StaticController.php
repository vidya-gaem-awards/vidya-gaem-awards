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
            ['See the Wojak', 'radiomuff', 'Preshow @ 00:00:00'],
            ['Aum Lang Sync', 'FEISaR', 'Preshow @ 00:05:23'],
            ['JABVGAS2019FINAL', 'jab50yen', 'Preshow @ 00:07:02'],
            ['Asylum for the Seething', 'radiomuff', 'Preshow @ 00:37:04'],
            ['VGAS2K20.exe', 'fv.exe', 'Preshow @ 00:44:21'],
            ['(Cum) Anywhere You Want', 'radiomuff', 'Preshow @ 01:02:03'],
            ['vgas mix 2019 i think', 'nostalgia_junkie', 'Preshow @ 01:05:30'],
            ['(Who\'s Gonna) Play These Games', 'radiomuff, Marpix', 'Preshow @ 01:31:42'],
            ['Adam\'s Weapon of Choice', 'beat_shobon', 'Preshow @ 01:34:36'],
            ['Entrapment - vidya mix', 'beat_shobon', 'Preshow @ 01:38:40'],
            ['State of the Art Technology', 'beat_shobon', 'Preshow @ 01:43:20'],
            ['shobon\'s assorted \'no idea how to title\' mix', 'beat_shobon', 'Preshow @ 01:50:29'],
            ['Ch/v/rches', 'radiomuff, donny q, db, anonymous', 'Preshow @ 01:56:19']
        ];

        $tracks = [
            ['The Battle of Solomon Sea', 'Mobile Suit Gundam 0083: Stardust Memory', 'BUY MY GAME Award'],
            ['Hika to Kage', 'The Five Star Stories', 'BUY MY GAME Award'],
            ['Midspace Action', 'Yuji Ohno, Masa Matsuda', 'IP Twist Award'],
            ['Casanova', 'Meiko Nakahara', 'IP Twist Award'],
            ['Sorrowful Stone', 'Fullmetal Alchemist', 'Smug Pessimist Award'],
            ['Apologize - Bleecker St.' , 'Big O Original Sound Score for Second Season', 'Good Deed Award'],
            ['Dangaioh vs Aizam III', 'Michiake Wantanabe', 'Hate Machine Award'],
            ['Cross Fight!', 'Dangaioh', 'Hate Machine Award'],
            ['X', 'Mobile Suit Gundam The 08th MS Team', 'Hyperbole Award'],
            ['Gymnopédie No.1', 'Erik Satie', 'Hyperbole Award'],
            ['Shades of Spring', 'Kevin MacLoed', 'Ludo Kino Award'],
            ['Rainbow Dinner', 'You & Explosion Band', 'Plot and Backstory Award'],
            ['Mermaid Girl -Akiba Koubou MIX-', 'Beatmania IIDX 19 Lincle', 'The Billy Herrington Award'],
            ['Neutral Nervous', 'Third Man', 'Tablet Mode Award'],
            ['Imaginary Sky', 'Gundam 0080: A War In The Pocket', 'Tablet Mode Award'],
            ['Midnight in a Perfect World', 'DJ Shadow', '8K ULTRA HD 2160P RETINA DISPLAY ENABLED Award'],
            ['2EM09_YAMASHITA', 'Evangelion: 2.0 You Can (Not) Advance', 'Pottery Award'],
            ['Bataille', 'nostalgia_junkie', 'Pottery Award'],
            ['Cross Fight!', 'Dangaioh', 'Tahiti Award'],
            ['Hitsu na Sento', 'Dangaioh', 'Tahiti Award'],
            ['Dangaioh vs Aizam III', 'Dangaioh', 'Tahiti Award'],
            ['Soviet Connection', 'Michael Hunter', 'Blue Checkmark Award'],
            ['Andrew Hale', 'L.A. Noire', 'Cease and Desist Award'],
            ['Zero Wing Remix', 'Bad_CRC', 'A Award Is You'],
            ['I Might Be A Cunt But At Least I\'m Not A Fucking Cunt', 'TISM', 'Intermission'],
            ['Screenwriter\'s Blues', 'Soul Coughing', 'Intermission'],
            ['Miraiha Lovers', 'Patlabor Vol 1. INTERFACE', 'Kamige Award'],
            ['Steel Arrow', 'Patlabor Vol 2. INTERCEPT', 'Kamige Award'],
            ['Then, Daybreak', 'Patlabor Vol 1. INTERFACE', 'Kamige Award'],
            ['Babel Collapses', 'Patlabor The Movie', 'Kamige Award'],
            ['Punishment', 'Shawn Lee', 'Seal of Quality Award'],
            ['Pulse', 'Macross Plus', 'Not Important Award'],
            ['eunha\'s a bitch', 'BOINK BEATS', 'Kiryu Award'],
            ['Sneaking on a Date', 'Shawn Lee', 'PIXEL5 R 74T Award'],
            ['Magmadiver', 'Neon Genesis Evangelion', 'Niche Award'],
            ['いい子ちゃんぶってるの!', 'PARAPPA THE RAPPER (TV)', 'Niche Award'],
            ['Meat Grinder', 'Katana ZERO', '(Eargasm) Patapon Award'],
            ['Fort Grays Air Base Hangar', 'Ace Combat 7', 'Patapon Award'],
            ['Lost Woods (Combat)', 'Cadence of Hyrule', 'Patapon Award'],
            ['Abyssal Time', 'Devil May Cry 5', 'Patapon Award'],
            ['Corrupted Monk', 'Sekiro', 'Patapon Award'],
            ['Men of Destiny', 'Gundam 0083: Stardust Memory', '/vr/ Award'],
            ['Assault Waves', 'Gundam 0083: Stardust Memory', '/vr/ Award'],
            ['Unreeeal Superhero 3', 'Rez and Kenet', 'Swine Flu Award'],
            ['Keyboard Cat', '', 'Swine Flu Award'],
            ['This is My Life', 'DJ Splash', 'Swine Flu Award'],
            ['The Art of Precision', 'Ed Field', '0 out of 10\'s Award'],
            ['Nyanyanyanyanyanyanya!', 'daniwell', '0 out of 10\'s Award'],
            ['Always (Synthsound 1 Instrumental)', 'Erasure', '10 out of 10\'s Award'],
            ['Diciassette Anni (Bjorn Fogelberg Remix)', 'Planet Boelex', 'Most Hated Award'],
            ['Introduction', 'Solar Fields', 'Least Worst Award'],
            ['Through the Vidya and Games', '/v/ The Musical VII', 'MT/v/ Award'],
            ['COMMUNICATION', 'Atsuko Niina', 'Consume Product Award'],
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
