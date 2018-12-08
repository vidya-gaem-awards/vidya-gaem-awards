<?php
namespace App\Command;

use App\Entity\Autocompleter;
use App\Entity\Config;
use App\Entity\Permission;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class InitialiseDatabaseCommand extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:init-db')
            ->setDescription('Initialises the database with the required data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('doctrine:schema:update');
        $arguments = ['--force' => true];
        $greetInput = new ArrayInput($arguments);
        $command->run($greetInput, $output);

        $repo = $this->em->getRepository(Config::class);
        $config = $repo->findOneBy([]);

        if ($config) {
            throw new \Exception('The database already appears to be initalized.');
        }

        // Add the default config
        $config = new Config();
        $this->em->persist($config);

        // Add the special-case autocompleter
        $autocompleter = new Autocompleter();
        $autocompleter->setId(Autocompleter::VIDEO_GAMES);
        $autocompleter->setName('Video games in ' . date('Y'));
        $this->em->persist($autocompleter);

        // Add the standard permissions
        foreach (Permission::STANDARD_PERMISSIONS as $id => $description) {
            $permission = new Permission();
            $permission->setId($id);
            $permission->setDescription($description);
            $this->em->persist($permission);
        }

        $this->em->flush();

        // Add the default permission inheritance
        $repo = $this->em->getRepository(Permission::class);
        foreach (Permission::STANDARD_PERMISSION_INHERITANCE as $parent => $children) {
            /** @var Permission $parent */
            $parent = $repo->find($parent);

            foreach ($children as $child) {
                /** @var Permission $child */
                $child = $repo->find($child);
                $parent->addChild($child);
            }

            $this->em->persist($parent);
        }

        $this->em->flush();

        $helper = $this->getHelper('question');
        $question = new Question('Enter a Steam ID or custom profile URL to give that user level 5 access: ');
        $question->setValidator(function ($answer) {
            if (empty($answer)) {
                return false;
            }

            $steam = \SteamId::create($answer);
            return $steam;
        });

        $steam = $helper->ask($input, $output, $question);

        if ($steam) {
            /** @var \SteamId $steam */
            $user = new User();
            $user
                ->setSteamId($steam->getSteamId64())
                ->setName($steam->getNickname())
                ->setAvatar($steam->getMediumAvatarUrl())
                ->setSpecial(true);

            /** @var Permission $permission */
            $permission = $this->em->getRepository(Permission::class)->find('LEVEL_5');
            $user->addPermission($permission);
            $this->em->persist($user);
            $this->em->flush();

            $output->writeln($steam->getNickname() . ' has been given level 5 access.');
        }

        $output->writeln('Setup complete.');
    }
}
