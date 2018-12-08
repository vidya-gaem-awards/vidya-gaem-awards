<?php
namespace App\Command;

use App\Entity\Autocompleter;
use App\Entity\GameRelease;
use App\Service\ConfigService;
use App\Service\WikipediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class WikipediaCommand extends Command
{
    private $em;
    private $configService;
    private $wikipedia;

    private $year;

    public function __construct(EntityManagerInterface $em, ConfigService $configService, WikipediaService $wikipedia)
    {
        $this->em = $em;
        $this->configService = $configService;
        $this->wikipedia = $wikipedia;

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
        $games = $this->wikipedia->getGames((int)$year);

        if ($input->getOption('legacy')) {
            $autocompleter = $this->em->getRepository(Autocompleter::class)->find('video-games-' . $year);
            if (!$autocompleter) {
                $autocompleter = new Autocompleter();
                $autocompleter->setId('video-games-' . $year);
                $autocompleter->setName('Video games in ' . $year);
            }

            if (!$input->getOption('no-clear')) {
                $autocompleter->setStrings([]);
            }

            foreach ($this->wikipedia->getStringListForAutocompleter($games) as $string) {
                $autocompleter->addString($string);
            }

            $this->em->persist($autocompleter);
            $this->em->flush();
        } else {
            $this->wikipedia->addGamesToGameReleaseTable($games, !$input->getOption('no-clear'));
        }

        foreach ($this->wikipedia->getOutput() as $message) {
            $output->writeln($message);
        }

        $output->writeln('Import complete. ' . count($games) . ' games added.');
    }
}
