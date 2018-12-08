<?php
namespace App\Command;

use App\Entity\Award;
use App\Entity\ResultCache;
use App\Entity\Vote;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\VGA\ResultCalculator\Schulze;
use App\VGA\Timer;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageCheckCommand extends Command
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var KernelInterface */
    private $kernel;

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->em = $em;
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:image-check')
            ->setDescription('Checks the filesize and dimensions of all nominee images.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bold = new OutputFormatterStyle(null, null, ['bold']);
        $cyan = new OutputFormatterStyle('cyan');
        $red = new OutputFormatterStyle('red');
        $output->getFormatter()->setStyle('bold', $bold);
        $output->getFormatter()->setStyle('cyan', $cyan);
        $output->getFormatter()->setStyle('red', $red);
        $output->getFormatter()->setStyle('warning', new OutputFormatterStyle('black', 'yellow'));

        $awards = $this->em->getRepository(Award::class)
            ->findBy(['enabled' => 1], ['order' => 'ASC']);

        foreach ($awards as $award) {
            $output->writeln('<bold>' . $award->getName() . '</bold> ');
            $output->writeln(' <cyan>' . $award->getSubtitle() . '</cyan>');
            $output->writeln('');

            $longestName = 0;
            foreach ($award->getNominees() as $nominee) {
                $longestName = max($longestName, mb_strlen($nominee->getName()));
            }

            $totalSize = 0;

            foreach ($award->getNominees() as $nominee) {
                $output->write('  ' . str_pad($nominee->getName(), $longestName + 3));
                $output->write( str_repeat(' ', strlen($nominee->getName()) - mb_strlen($nominee->getName())));

                if (!$nominee->getImage()) {
                    $output->writeln('<warning>no image</warning>');
                    continue;
                }

                if ($nominee->getImage()[0] === '/') {
                    $file = $this->kernel->getProjectDir() . '/public' . $nominee->getImage();
                    if (!file_exists($file)) {
                        $output->writeln('<error>broken image</error>');
                        continue;
                    }
                } else {
                    $file = $nominee->getImage();
                }

                $image = file_get_contents($file);
                $size = strlen($image) / 1024;

                $img = imagecreatefromstring($image);

                $totalSize += $size;

                if ($size > 100) {
                    $class = 'red';
                } elseif ($size > 50) {
                    $class = 'comment';
                } else {
                    $class = 'info';
                }

                $string = sprintf('%3d kB', $size);
                $output->write("<$class>$string</$class>");
                $output->writeln(sprintf("   %4d x %4d", imagesx($img), imagesy($img)));
            }

            $output->writeln( '  ' . str_repeat('-', $longestName + 3 + 6));
            $output->write('  ' . str_pad('Total size', $longestName + 1));

            $string = sprintf('%5d kB', $totalSize);
            $output->writeln("$string");
            $output->writeln( '  ' . str_repeat('-', $longestName + 3 + 6));

            $output->writeln('');
        }
    }
}
