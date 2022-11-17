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
            ['vgas2021 mix', 'beat_shobon', 'Preshow'],
            ['MAIN MIX FOR FINAL EXPORT_2', 'fv.exe', 'Preshow'],
            ['vga 2021', 'nostalgia_junkie', 'Preshow'],
            ['vga2021', 'W.T. Snacks', 'Preshow'],
        ];

        $tracks = [
            ['Lofi & Chill Melody', 'AzM Music', 'Intro'],
            ['Moon Garage (Dj CUTMAN Remix)', 'Ridge Racer Arrange 2013', '/v/irgin Award'],
            ['Scooby Doo Opening Theme (MIDI version)', 'Ted Nichols', 'Scrappy Doo Award'],
            ['Scooby Doo Underscore', 'Ted Nichols', 'Scrappy Doo Award'],
            ['Rage Your Dreams (MIDI version)', 'Initial D', 'Initial /v/ Award'],
            ['Call Me Yoshi ("Call Me Al" Parody)', 'Anon', 'Initial /v/ Award'],
            ['You Made It (from GRAN TURISMO 2 EXTENDED SCORE ~GROOVE~ )', 'Isamu Ohira', 'Press X to win the Award'],
            ['Cotton Eye Joe (MIDI version)', 'Rednex', 'IP Twist Award'],
            ['Ironic (MIDI version)', 'Alanis Morisette', 'Guilty Pleasure Award'],
            ['Dragonborn (MIDI version)', 'The Elder Scrolls V: Skyrim Soundtrack', 'Horse Armor Award'],
            ['Upstream (SNES)', 'Umihara Kawase', '/ck/ Award'],
            ['Horse With No Name (MIDI version)', 'America', 'Absolute State of Play'],
            ['Puma Twins Stripdance', 'Dominion Tank Police', 'Kamige Award'],
            ['Black Magic', 'Bible Black', '/vr/ Award'],
            ['Active Heart (La Noche de Walpurgis)', 'Bible Black', '/vr/ Award'],
            ['Mirada a Springfield (Eye on Springfield Cover)', 'Mariano Veiga', '/vr/ Award'],
            ['FM MECHA メカ YM2612 (FMDrive VST + SPSG) ...CSM mode in the end :)', 'AlyJaMesLaB', '/vr/ Award'],
            ['Napolitan Blues (Team Italy/Fatal Fury Team)', 'The King of Fighters \'94', '/vr/ Award'],
            ['FC ゴエモン2の曲を数曲アレンジしてみた♪ (Cover)', 'Chugoku and Hokkaido', '/vr/ Award'],
            ['Star Light', 'Sonic The Hedgehog', '/vr/ Award'],
            ['Geese Howard\'s Theme (Geese Ni Kissu)', 'Fatal Fury: King of Fighters', '/vr/ Award'],
            ['Overworld (Genesis/MD Soundfont cover)', 'Super Mario World', '/vr/ Award'],
            ['Theme of Simon (Shimoine\'s Castlevania VRC6 Collection Cover)', 'Super Castlevania IV', '/vr/ Award'],
            ['Battle 1', 'Final Fantasy IV (2 USA)', '/vr/ Award'],
            ['Battle 1 (FF4 Arrange brr890 cover)', 'Final Fantasy II', '/vr/ Award'],
            ['Animal Parade ', 'Metal Max (1991 Famicom Version)', '/vr/ Award'],
            ['Level Up (aka Party Join/In)', 'Metal Max (1991 Famicom Version)', '/vr/ Award'],
            ['Caterpillar Tracks', 'Metal Max (1991 Famicom Version)', '/vr/ Award'],
            ['Ending', 'Super Mario World ', '/vr/ Award'],
            ['U.S.S.R', 'Super Dodge Ball (NES)', '/vr/ Award'],
            ['Katyusya', 'Girls Und Panzer', '/vr/ Award'],
            ['(SFX)', 'Street Fighter II', '/vr/ Award'],
            ['(SFX)', 'Home Alone (film)', '/vr/ Award'],
            ['(SFX)', 'Legends of the Hidden Temple (TV game show)', '/vr/ Award'],
            ['Title Screen', 'Vib Ribbon', 'Vib-Ribbon Award'],
            ['Pandora Palace', 'Deltarune', 'Vib-Ribbon Award'],
            ['The Disaster of Passion ', 'Guilty Gear Strive', 'Vib-Ribbon Award'],
            ['Kill the Itch', 'NEO: The World Ends With You', 'Vib-Ribbon Award'],
            ['Snow In Summer', 'NieR Replicant ver.1.22474487139…', 'Vib-Ribbon Award'],
            ['Demon King Battle', 'Shin Megami Tensei V', 'Vib-Ribbon Award'],
            ['Song of the Ancients - Devola', 'NieR Replicant ver.1.22474487139…', 'Vib-Ribbon Award'],
            ['Little Bit', 'Dickey Moe', 'Intermission'],
            ['Cursed by Fate', 'Last Bastion', 'Intermission'],
            ['Broke', 'The Orange King', 'Intermission'],
            ['Jaybird', 'Cavalier', 'Intermission'],
            ['Live at the O2 in London', 'Foo Fighters ft. Rick Astley', 'Intermission'],
            ['ED - Sorya nai ze! ? Fureijā', 'ED', 'Intermission'],
            ['Prelude and Fugue in C Major', 'Johann Sebastian Bach', '9IXELS ARE 9RT Award'],
            ['Positive Force', 'SoulEye', 'Name a More Iconic Duo Award'],
            ['Song 2 (MIDI version)', 'Blur', 'A E S T H E T I C S Award'],
            ['Quiet Curves', 'R4: Ridge Racer Type 4 Soundtrack', 'Plot and Backstory Award'],
            ['It\'s Raining Men (MIDI version)', 'The Weather Girls', 'The Billy Herrington Award'],
            ['Gamecube Presentation / Sizzle Reel', 'Nintendo 2000 Spaceworld Music', 'The Most Interesting Thing In 2001'],
            ['(SFX)', 'The Lord of the Rings: The Fellowship of the Ring ', 'The Most Interesting Thing In 2001'],
            ['Extended Play', 'Sc-ry-ed OST', 'The Most Interesting Thing In 2001'],
            ['Final Destination', 'Super Smash Bros. Melee', 'The Most Interesting Thing In 2001'],
            ['Melee Practice ', '(ASMR) Gamecube Controller Sounds', 'The Most Interesting Thing In 2001'],
            ['Let\'s Rock! (Title)', 'Devil May Cry', 'The Most Interesting Thing In 2001'],
            ['Shop Theme', 'Wario Land 4', 'The Most Interesting Thing In 2001'],
            ['Hurry Up', 'Wario Land 4', 'The Most Interesting Thing In 2001'],
            ['Stage Clear / Results Screen', 'Guru Logi Champ', 'The Most Interesting Thing In 2001'],
            ['Imminent', 'Metal Gear 2 (MSX)', 'The Most Interesting Thing In 2001'],
            ['Fission Mailed', 'Metal Gear Solid 2: Sons of Liberty', 'The Most Interesting Thing In 2001'],
            ['Fame (MIDI version)', 'Irene Cara', 'Hate Machine Award'],
            ['Whats Up? (MIDI version)', '4 Non Blondes', 'LAN Party Award'],
            ['The Mountain Village', 'Calum Bowen', 'Diamond in the Rough Award'],
            ['Tor', 'Iji', 'Dance in the Pale Moonlight Award'],
            ['White Wedding (midi version)', 'Billy Idol', 'Least Worst Award'],
            ['Hung Up (Instrumental)', 'Madonna', 'Most Hated Award'],
            ['Prenotion ', 'Phantasy Star Online', 'Outro'],
            ['Can Still See the Light (Piano Version) ', 'Phantasy Star Online', 'Outro']
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
