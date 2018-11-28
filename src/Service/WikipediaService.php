<?php
namespace App\Service;

use App\Entity\GameRelease;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;

class WikipediaService
{
    private $em;
    private $output = [];

    /*
     * The keys of this array correspond to the names of articles on Wikipedia (converted to lowercase).
     * The values of each item is an array containing:
     *   element 0: user-readable abbreviation of platform
     *   element 1: the name of a setter in the GameRelease class (or, if set to an array, multiple setters)
     *
     * Note: if element 1 is set to null, it will be ignored when adding platforms to the GameRelease object.
     */
    private const PLATFORM_MAP = [
        '3ds' => ['3DS', 'n3ds'],
        '3dsvc' => ['3DS', 'n3ds'],
        'amazon fire' => ['Mobile', 'mobile'],
        'amazon fire tv' => ['Fire TV', 'mobile'],
        'android' => ['Mobile', 'mobile'],
        'android (operating system)' => ['Mobile', 'mobile'],
        'apple watch' => ['Mobile', 'mobile'],
        'arcade' => ['Arcade', null],
        'dc' => ['DC', null],
        'dreamcast' => ['DC', null],
        'droid' => ['Mobile', 'mobile'],
        'ds' => ['DS', null],
        'dsiware' => ['DSi', null],
        'fire os' => ['Mobile', 'mobile'],
        'gamestick' => ['GameStick', null],
        'gb' => ['GB', null],
        'gba' => ['GBA', null],
        'gbc' => ['GBC', null],
        'game boy advance' => ['GBA', null],
        'gc' => ['GCN', null],
        'gcn' => ['GCN', null],
        'htc vive' => ['Vive', ['pc', 'vr']],
        'ios' => ['Mobile', 'mobile'],
        'ios (apple)' => ['Mobile', 'mobile'],
        'iphone os' => ['Mobile', 'mobile'],
        'ipod' => ['Mobile', 'mobile'],
        'jaguar' => ['Jaguar', null],
        'java me' => ['Java ME', null],
        'lin' => ['Linux', 'pc'],
        'linux' => ['Linux', 'pc'],
        'mac' => ['macOS', 'pc'],
        'macintosh' => ['macOS', 'pc'],
        'macos' => ['macOS', 'pc'],
        'mac os' => ['macOS', 'pc'],
        'mac os x' => ['macOS', 'pc'],
        'mega drive' => ['MD', null],
        'microsoft windows' => ['PC', 'pc'],
        'mobile' => ['Mobile', 'mobile'],
        'mobile game' => ['Mobile', 'mobile'],
        'n-gage' => ['N-Gage', null],
        'n64' => ['N64', null],
        'nds' => ['DS', null],
        'new nintendo 3ds' => ['3DS', 'n3ds'],
        'neo' => ['Neo Geo', null],
        'ngp' => ['NGP', null],
        'nintendo 3ds' => ['3DS', 'n3ds'],
        'nintendo ds' => ['DS', null],
        'nintendo dsi' => ['DS', null],
        'nintendo dsiware' => ['DS', null],
        'nintendo eshop' => ['eShop', null],
        'nintendo gamecube' => ['GCN', null],
        'nintendo switch' => ['Switch', 'switch'],
        'nintendo wii' => ['Wii', 'wii'],
        'nvidia shield' => ['Mobile', null],
        'os x' => ['macOS', 'pc'],
        'osx' => ['macOS', 'pc'],
        'oculus rift' => ['Rift', ['pc', 'vr']],
        'ouya' => ['Ouya', null],
        'pc' => ['PC', 'pc'],
        'ps' => ['PSX', null],
        'ps1' => ['PSX', null],
        'ps2' => ['PS2', null],
        'ps3' => ['PS3', 'ps3'],
        'ps4' => ['PS4', 'ps4'],
        'personal computer' => ['PC', 'pc'],
        'playstation' => ['PSX', null],
        'playstation 2' => ['PS2', null],
        'playstation 3' => ['PS3', 'ps3'],
        'playstation 4' => ['PS4', 'ps4'],
        'playstation network' => ['PSN', 'psn'],
        'playstation portable' => ['PSP', null],
        'playstation vr' => ['PSVR', ['ps4', 'vr']],
        'playstation vita' => ['Vita', 'vita'],
        'psn' => ['PSN', 'psn'],
        'psp' => ['PSP', null],
        'psvita' => ['Vita', 'vita'],
        'sat' => ['Saturn', null],
        'snes' => ['SNES', null],
        'super nintendo entertainment system' => ['SNES', null],
        'virtual console' => ['VC', null],
        'wii' => ['Wii', 'wii'],
        'wiiu' => ['Wii U', 'wiiu'],
        'wiiware' => ['Wii', 'wiiware'],
        'wii u' => ['Wii U', 'wiiu'],
        'win' => ['PC', 'pc'],
        'windows' => ['PC', 'pc'],
        'windows 10' => ['PC', 'pc'],
        'windows phone' => ['Mobile', 'mobile'],
        'windows phones' => ['Mobile', 'mobile'],
        'ws' => ['WonderSwan', null],
        'x360' => ['360', 'x360'],
        'xbla' => ['360', 'xbla'],
        'xbox' => ['Xbox', null],
        'xbox (console)' => ['Xbox', null],
        'xbox 360' => ['360', 'x360'],
        'xbox live arcade' => ['360', 'xbla'],
        'xbox one' => ['XB1', 'xb1'],
    ];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function normalisePlatforms(array $gamePlatforms, array $platformMap): array
    {
        $normalisedPlatforms = [];

        foreach ($gamePlatforms as $platform) {
            if (!array_key_exists($platform, $platformMap)) {
                $this->output[] = '<fg=yellow>Unknown platform (' . $platform . ')</>';
            } elseif ($platformMap[$platform] !== null) {
                if (is_array($platformMap[$platform])) {
                    $normalisedPlatforms = array_merge($normalisedPlatforms, $platformMap[$platform]);
                } else {
                    $normalisedPlatforms[] = $platformMap[$platform];
                }
            }
        }

        $normalisedPlatforms = array_unique($normalisedPlatforms);

        sort($normalisedPlatforms);
        return $normalisedPlatforms;
    }

    /**
     * Downloads and parses the list of games for the year from Wikipedia, and returns a list of the games with their
     * associated platforms.
     * @param int $year A year between 1995 and the present.
     * @return array Returns an array where the key is the title of the game, and the values are an array of platforms
     *               the game was released on in that year.
     *
     * @throws \Exception Throws an Exception if an unsupported year is specified, or when Wikipedia returns an error.
     */
    public function getGames(int $year): array
    {
        if ($year < 1995) {
            throw new \Exception('Years before 1995 are not supported.');
        }
        if ($year >= 1997 && $year <= 2001) {
            $this->output[] = '<bg=red>Warning:</> articles between 1997 and 2001 contain a small amount of games with platforms that were actually released in different years.';
        }
        if ($year > (int)date('Y')) {
            throw new \Exception('Future years are not supported.');
        }

        $url = "https://en.wikipedia.org/w/api.php?action=parse&page={$year}_in_video_gaming&prop=text&format=json";
        $result = json_decode(file_get_contents($url), true);

        if (isset($result['error'])) {
            throw new \Exception('Wikipedia returned an error: ' . $result['error']['info']);
        }

        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $result['parse']['text']['*'] . '</body></html>';

        $crawler = new Crawler($html);

        if ($year <= 2006 || $year === 2011) {
            $games = $this->parseSingleTableLayout($crawler, $year);
        } else {
            $games = $this->parseMultipleTableLayout($crawler, $year);
        }

        if (empty($games)) {
            throw new \Exception('Unable to parse the list of games for ' . $year . '. This may be due to formatting changes in the Wikipeda article.');
        }

        return $games;
    }

    /**
     * This function supports the table style used from 1997 to 2006 (plus 2011).
     * @param Crawler $crawler
     * @param int $year
     * @return array
     */
    private function parseSingleTableLayout(Crawler $crawler, int $year): array
    {
        if ($year <= 1996) {
            // 1995 and 1996 have tables with more detail, but all the information we need is in the first three columns
            $columns = 3;
        } elseif ($year <= 2000 || $year == 2002) {
            // These years add an 'Arcade' column at the end
            $columns = 7;
        } elseif ($year === 2011) {
            // For some reason 2011 has a different column set to every other year (Date / Title / PC / Consoles)
            $columns = 4;
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

            for ($i = 2 + $modifier; $i < $columns + $modifier; $i++) {
                $text = trim($cells->item($i)->textContent);

                if (empty($text) || $text === 'N/A') {
                    continue;
                }
                if ($i === 2) {
                    $games[$title][] = 'pc';
                    continue;
                } elseif ($i === 6) {
                    $games[$title][] = 'arcade';
                    continue;
                }

                $text = str_replace(';', ',', $text);
                // TODO: detect whether there's a year in parentheses that doesn't match the current year
                $text = preg_replace('/\(.*?\)/', '', $text);
                // Removes citation markers
                $text = preg_replace('/\[\d+\]/', '', $text);
                $platforms = explode(', ', $text);

                foreach ($platforms as $platform) {
                    $platform = strtolower(str_replace('_', ' ', trim($platform)));
                    $games[$title][] = $platform;
                }
            }
        }

        return $games;
    }

    /**
     * This function supports the table style used from 2007 onwards (with the exception of 2011).
     * @param Crawler $crawler
     * @param int $year
     * @return array
     */
    private function parseMultipleTableLayout(Crawler $crawler, int $year): array
    {
        // 2015 added a sources column
        if ($year <= 2014) {
            $modifier = -1;
        } elseif ($year <= 2016) {
            $modifier = 0;
        } else {
            $modifier = 1;
        }

        $tables = $crawler->filter('.wikitable')->reduce(function (Crawler $node, $i) use ($year) {
            $cells = $node->filter('th');
            if ($cells->count() === 0) {
                $cells = $node->filter('tr:first-child td');
            }

            // Thses years have a simpler three column layout
            if ($year === 2007 || $year === 2009 || $year === 2010) {
                $titleCell = 1;
            } else {
                $titleCell = 2;
            }

            if (count($cells) < 3 || trim($cells->getNode($titleCell)->textContent) !== 'Title') {
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
                $games[$title][] = $platform;
            }
        }
        return $games;
    }

    public function addGamesToGameReleaseTable(array $games, bool $deleteExisting = true): void
    {
        if ($deleteExisting) {
            foreach ($this->em->getRepository(GameRelease::class)->findAll() as $gameRelease) {
                $this->em->remove($gameRelease);
            }
            $this->em->flush();
        }

        array_walk($games, function (&$value, $key) {
            $value = $this->normalisePlatforms($value, $this->getGameReleasePlatformMap());
        });
        ksort($games);

        foreach ($games as $title => $platforms) {
            $gameRelease = new GameRelease($title);
            foreach ($platforms as $platform) {
                if (method_exists($gameRelease, 'set' . $platform)) {
                    $gameRelease->{'set'.$platform}(true);
                } else {
                    $this->output[] = '<fg=yellow>Unknown platform for game <options=bold>' . $title . ' <fg=yellow>(' . $platform . ')</>';
                }
            }
            $this->em->persist($gameRelease);
        }

        $this->em->flush();
    }

    /**
     * Gets an array of strings that's suitable for using in a call to $autocompleter->setStrings.
     * @param array $games
     * @return string[]
     */
    public function getStringListForAutocompleter(array $games): array
    {
        array_walk($games, function (&$value, $key) {
            $value = $this->normalisePlatforms($value, $this->getUserReadablePlatformMap());
        });
        $games = $this->combineDuplicateGames($games);

        ksort($games);

        $strings = [];

        foreach ($games as $title => $platforms) {
            $strings[] = $title . ' (' . implode(', ', $platforms) . ')';
        }

        return $strings;
    }

    public function getOutput(): array
    {
        return $this->output;
    }

    private function getUserReadablePlatformMap(): array
    {
        $map = [];
        foreach (self::PLATFORM_MAP as $platform => $values) {
            $map[$platform] = $values[0];
        }
        return $map;
    }

    private function getGameReleasePlatformMap(): array
    {
        $map = [];
        foreach (self::PLATFORM_MAP as $platform => $values) {
            $map[$platform] = $values[1];
        }
        return $map;
    }
}
