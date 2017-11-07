<?php
namespace AppBundle\Command;

use AppBundle\Entity\Autocompleter;
use AppBundle\Entity\GameRelease;
use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class WikipediaCommand extends ContainerAwareCommand
{
    private $em;
    private $configService;

    private $year;

    public function __construct(EntityManagerInterface $em, ConfigService $configService)
    {
        $this->em = $em;
        $this->configService = $configService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:wikipedia')
            ->setDescription('Imports a list of video games from Wikipedia into the autocomplete list.')
            ->addArgument('year', InputArgument::OPTIONAL, '', date('Y'))
            ->addOption('no-clear', null, InputOption::VALUE_NONE, 'Don\'t clear the list of games before importing')
            ->addOption('legacy', null, InputOption::VALUE_NONE, 'Put the list of games into a different autocompleter');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->configService->isReadOnly()) {
            throw new \RuntimeException('Database is in read-only mode. Read-only mode must be disabled to run this script.');
        }

        $year = $input->getArgument('year');

        if ($year < 1995) {
            throw new \RuntimeException('Years before 1995 are not supported.');
        }
        if ($year <= 2006) {
            throw new \RuntimeException('Years between 1996 and 2006 are not fully implemented.');
        }
        if ($year == 2011) {
            throw new \RuntimeException('2011 has different formatting that isn\'t currently supported.');
        }

        if ($input->getOption('legacy')) {
            $legacy = true;
            $autocompleter = $this->em->getRepository(Autocompleter::class)->find('video-games-' . $year);
            if (!$autocompleter) {
                $autocompleter = new Autocompleter();
                $autocompleter->setId('video-games-' . $year);
                $autocompleter->setName('Video games in ' . $year);
            }

            if (!$input->getOption('no-clear')) {
                $autocompleter->setStrings([]);
            }

            $this->em->persist($autocompleter);
            $this->em->flush();
        } else {
            $autocompleter = null;
            $legacy = false;
        }

        if (!$input->getOption('no-clear')) {
            $this->em->createQueryBuilder()->delete(GameRelease::class)->getQuery()->execute();
            $this->em->flush();
        }

        $url = "https://en.wikipedia.org/w/api.php?action=parse&page={$year}_in_video_gaming&prop=text&format=json";

        $platformList = [
            'amazon fire' => 'mobile',
            'amazon fire tv' => 'mobile',
            'android (operating system)' => 'mobile',
            'apple watch' => 'mobile',
            'dreamcast' => null,
            'fire os' => 'mobile',
            'gamestick' => null,
            'gba' => null,
            'game boy advance' => null,
            'htc vive' => ['pc', 'vr'],
            'ios' => 'mobile',
            'ios (apple)' => 'mobile',
            'ipod' => 'mobile',
            'java me' => null,
            'linux' => 'pc',
            'macintosh' => 'pc',
            'macos' => 'pc',
            'mac os' => 'pc',
            'mac os x' => 'pc',
            'microsoft windows' => 'pc',
            'mobile game' => 'mobile',
            'new nintendo 3ds' => 'n3ds',
            'nintendo 3ds' => 'n3ds',
            'nintendo ds' => null,
            'nintendo dsi' => null,
            'nintendo dsiware' => null,
            'nintendo eshop' => null,
            'nintendo gamecube' => null,
            'nintendo switch' => 'switch',
            'nvidia shield' => null,
            'os x' => 'pc',
            'oculus rift' => ['pc', 'vr'],
            'ouya' => null,
            'ps2' => null,
            'ps3' => 'ps3',
            'ps4' => 'ps4',
            'personal computer' => 'pc',
            'playstation 2' => null,
            'playstation 3' => 'ps3',
            'playstation 4' => 'ps4',
            'playstation network' => 'psn',
            'playstation portable' => null,
            'playstation vr' => ['ps4', 'vr'],
            'playstation vita' => 'vita',
            'psvita' => 'vita',
            'super nintendo entertainment system' => null,
            'virtual console' => null,
            'wii' => 'wii',
            'wiiu' => 'wiiu',
            'wiiware' => 'wiiware',
            'wii u' => 'wiiu',
            'windows phone' => 'mobile',
            'windows phones' => 'mobile',
            'xbla' => 'xbla',
            'xbox' => null,
            'xbox (console)' => null,
            'xbox 360' => 'x360',
            'xbox live arcade' => 'xbla',
            'xbox one' => 'xb1',
        ];

        $legacyPlatformList = [
            'game boy advance' => 'GBA',
            'gba' => 'GBA',
            'ios' => 'Mobile',
            'linux' => 'PC',
            'mac os x' => 'Mac',
            'macintosh' => 'Mac',
            'microsoft windows' => 'PC',
            'mobile game' => 'Mobile',
            'nintendo ds' => 'NDS',
            'nintendo gamecube' => 'GCN',
            'personal computer' => 'PC',
            'playstation 2' => 'PS2',
            'playstation 3' => 'PS3',
            'playstation network' => 'PSN',
            'playstation portable' => 'PSP',
            'wii' => 'Wii',
            'xbox' => 'Xbox',
            'xbox (console)' => 'Xbox',
            'xbox 360' => '360',
            'xbox live arcade' => 'XBLA',
        ];

        $result = json_decode(file_get_contents($url), true);

        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $result['parse']['text']['*'] . '</body></html>';

        $crawler = new Crawler($html);

        if ($year >= 2008) {
            $gamesPartial = $this->parseTable2008Onwards($crawler, $year);
        } else {
            $gamesPartial = $this->parseTable1997To2006($crawler, $year);
        }

        $list = $legacy ? $legacyPlatformList : $platformList;

        foreach ($gamesPartial as $title => $platforms) {
            foreach (array_keys($platforms) as $platform) {
                if (!array_key_exists($platform, $list)) {
                    $output->writeln('<fg=yellow>Unknown platform for game <options=bold>' . $title . ' <fg=yellow>(' . $platform . ')</>');
                } elseif ($list[$platform] !== null) {
                    foreach ((array)$list[$platform] as $platformKey) {
                        $games[$title][$platformKey] = true;
                    }
                }
            }
        }

        ksort($games);

        foreach ($games as $name => $platforms) {
            $output->writeln('<fg=cyan>' . $name . '</>', OutputInterface::VERBOSITY_VERBOSE);

            $release = new GameRelease($name);
            $platforms = array_keys(array_filter($platforms));
            if ($legacy) {
                sort($platforms);
                $game = $name . ' (' . implode(', ', $platforms) . ')';
                $autocompleter->addString($game);
            } else {
                foreach ($platforms as $platform) {
                    if (method_exists($release, 'set' . $platform)) {
                        $release->{'set'.$platform}(true);
                    } else {
                        $output->writeln('<fg=yellow>Unknown platform for game <options=bold>' . $name . ' <fg=yellow>(' . $platform . ')</>');
                    }
                }
                $this->em->persist($release);
            }
        }

        if ($legacy) {
            $this->em->persist($autocompleter);
        }

        $this->em->flush();

        $output->writeln('Import complete. ' . count($games) . ' games added.');
    }

    private function parseTable1997To2006(Crawler $crawler, $year)
    {
        if ($year <= 2000 || $year == 2002) {
            $columns = 7;
        } else {
            $columns = 6;
        }

        $tables = $crawler->filter('.wikitable.sortable');
        if ($tables->count() > 1) {
            throw new \RuntimeException('Unexpected table count: ' . $tables->count());
        }

        $games = [];

        $rows = $tables->filter('tr');
        /** @var \DOMElement $row */
        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');

            if ($cells->length === 0) {
                continue;
            }

            $gameCell = 1;
            $modifier = 0;
            if ($cells->length < $columns) {
                $modifier = -1;
            }

            $element = $cells->item($gameCell + $modifier)->firstChild;
            if ($element->hasChildNodes()) {
                $title = $element->firstChild->textContent;
            } else {
                $title = $element->textContent;
            }

            for ($i = 2 + $modifier; $i < 6 + $modifier; $i++) {
                $text = $cells->item($i)->textContent;
                if (empty($text) || $text === 'N/A') {
                    continue;
                }
                if ($i === 2) {
                    $games[$title]['pc'] = true;
                    continue;
                }

                $text = str_replace(';', ',', $text);
                $text = preg_replace('/\(.*?\)/', '', $text);
                $platforms = explode(', ', $text);

                foreach ($platforms as $platform) {
                    $platform = strtolower(str_replace('_', ' ', trim($platform)));
                    $games[$title][$platform] = true;
                }
            }
        }

        return $games;
    }

    private function parseTable2008Onwards(Crawler $crawler, $year)
    {
        // 2015 added a sources column
        if ($year <= 2014) {
            $modifier = -1;
        } else {
            $modifier = 0;
        }

        $tables = $crawler->filter('.wikitable')->reduce(function (Crawler $node, $i) use ($year) {
            $cells = $node->filter('th');
            if ($cells->count() === 0) {
                $cells = $node->filter('tr:first-child td');
            }

            // 2007 has a simpler three column layout
            if ($year == 2007) {
                $titleCell = 1;
            } else {
                $titleCell = 2;
            }

            if (count($cells) < 3 || $cells->getNode($titleCell)->textContent !== 'Title') {
                return false;
            } else {
                return true;
            }
        });

        $rows = $tables->filter('tr');
        $rowspanEffect = false;

        $games = [];

        /** @var \DOMElement $row */
        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');

            if ($cells->length < (3 + $modifier)) {
                continue;
            }

            $gameCell = $cells->length - (3 + $modifier);

            if (preg_match('/^\d\d?$/', $cells->item($gameCell)->textContent)) {
                $gameCell++;
            }

            if ($year == 2007 && !$cells->item($gameCell)->hasChildNodes()) {
                $gameCell++;
            }

            if ($rowspanEffect) {
                $title = $rowspanEffect;
                $gameCell--;
                $rowspanEffect = false;
            } else {
                $element = $cells->item($gameCell)->firstChild;
                if ($element->hasChildNodes()) {
                    $title = $element->firstChild->textContent;
                } else {
                    $title = $element->textContent;
                }

                // Workaround due a game that takes up two rows in the 2010 table
                if ($cells->item($gameCell)->hasAttribute('rowspan')) {
                    $rowspanEffect = $title;
                } else {
                    $rowspanEffect = false;
                }
            }

            $platforms = iterator_to_array($cells->item($gameCell + 1)->getElementsByTagName('a'));
            $platforms = array_map(function (\DOMElement $e) {
                return str_replace('/wiki/', '', $e->getAttribute('href'));
            }, $platforms);

            foreach ($platforms as $platform) {
                $platform = strtolower(str_replace('_', ' ', $platform));
                $games[$title][$platform] = true;
            }
        }
        return $games;
    }
}
