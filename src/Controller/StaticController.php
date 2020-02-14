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
