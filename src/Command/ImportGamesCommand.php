<?php
namespace App\Command;

use App\Entity\GameRelease;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportGamesCommand extends Command
{
    private $em;
    private $configService;

    public function __construct(EntityManagerInterface $em, ConfigService $configService)
    {
        $this->em = $em;
        $this->configService = $configService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:import-games')
            ->setDescription('Imports a list of video games from a CSV into the autocomplete list.')
            ->setHelp('This script populates the `game_releases` table using data from Wikipedia that has been pre-formatted'
                . ' and placed into an CSV. The CSV should consist of two columns: the first column should contain the name'
                . ' of the game, and the second column should contain a comma separated list of platform abbreviations.')
            ->addArgument('file', InputArgument::REQUIRED, 'Filename of the CSV to import')
            ->addOption('no-clear', null, InputOption::VALUE_NONE, 'Don\'t clear the list of games before importing');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('file');

        if ($this->configService->isReadOnly()) {
            throw new \RuntimeException('Database is in read-only mode. Read-only mode must be disabled to run this script.');
        }

        if (!file_exists($filename)) {
            throw new \RuntimeException('File \'' . $filename . '\' does not exist.');
        }

        if (!$input->getOption('no-clear')) {
            $this->em->createQueryBuilder()->delete(GameRelease::class)->getQuery()->execute();
            $this->em->flush();
        }

        // The second and third characters are non-breaking spaces
        $search = [' ', "\xc2\xa0", "\xa0", 'Win', 'Mac', 'Lin', 'iOS', 'Droid', 'Android', 'WP', 'X360', 'XBO', 'PSVita', '3DS', 'HTCVive', 'OculusRift', 'PlayStationVR'];
        $replace = ['', '', '', 'pc', 'pc', 'pc', 'mobile', 'mobile', 'mobile', 'mobile', 'x360', 'xb1', 'vita', 'n3ds', 'pc,vr', 'pc,vr', 'ps4,vr'];
        $delete = ['PS2', 'NDS', 'DC', 'PSP', 'Fire', 'Ouya'];

        $allPlatforms = [
            'pc', 'vr', 'ps3', 'ps4', 'vita', 'psn', 'x360', 'xb1', 'xbla', 'wii', 'wiiu', 'wiiware', 'n3ds', 'mobile'
        ];

        $games = array();

        $csv = file($filename);
        foreach ($csv as $line) {
            $array = str_getcsv($line);
            $game = trim($array[0]);
            if (!isset($games[$game])) {
                $games[$game] = array_fill_keys($allPlatforms, false);
            }

            $platforms = explode(",", trim(str_replace($search, $replace, $array[1])));
            $platforms = array_diff($platforms, $delete);
            foreach ($platforms as $platform) {
                $platform = ucfirst(strtolower($platform));
                $games[$game][$platform] = true;
            }
        }

        foreach ($games as $name => $platforms) {
            $output->writeln('<fg=cyan>' . $name . '</>', OutputInterface::VERBOSITY_VERBOSE);

            $release = new GameRelease($name);
            $platforms = array_keys(array_filter($platforms));
            foreach ($platforms as $platform) {
                if (method_exists($release, 'set' . $platform)) {
                    $release->{'set'.$platform}(true);
                } else {
                    $output->writeln('<fg=yellow>Unknown platform for game <options=bold>' . $name . ' <fg=yellow>(' . $platform . ')</>');
                }
            }
            $this->em->persist($release);
        }

        $this->em->flush();

        $output->writeln('Import complete. ' . count($games) . ' games added.');
    }
}
