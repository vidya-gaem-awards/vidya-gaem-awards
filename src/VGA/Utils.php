<?php
namespace VGA;

use Doctrine\ORM\EntityManager;
use VGA\Model\Autocompleter;
use VGA\Model\Config;
use VGA\Model\Permission;

class Utils
{
    /**
     * @param $seed
     * @param int $max_number
     * @return int
     */
    public static function randomNumber($seed, $max_number = 100)
    {
        //hash the seed to ensure enough random(ish) characters each time
        $hash = sha1($seed);

        //use the first x characters, and convert from hex to base 10 (this is where the random number is obtain)
        $rand = base_convert(substr($hash, 0, 6), 16, 10);

        //as a decimal percentage (ensures between 0 and max number)
        return (int)round($rand / 0xFFFFFF * $max_number);
    }

    public static function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    public static function initializeDatabase(EntityManager $em)
    {
        $repo = $em->getRepository(Config::class);
        $config = $repo->findOneBy([]);

        if ($config) {
            throw new \Exception('The database already appears to be initalized.');
        }

        // Add the default config
        $config = new Config();
        $em->persist($config);

        // Add the special-case autocompleter
        $autocompleter = new Autocompleter();
        $autocompleter->setId('video-game');
        $autocompleter->setName('Video games in ' . date('Y'));
        $em->persist($autocompleter);

        // Add the standard permissions
        foreach (Permission::STANDARD_PERMISSIONS as $id => $description) {
            $permission = new Permission();
            $permission->setId($id);
            $permission->setDescription($description);
            $em->persist($permission);
        }

        $em->flush();

        // Add the default permission inheritance
        $repo = $em->getRepository(Permission::class);
        foreach (Permission::STANDARD_PERMISSION_INHERITANCE as $parent => $children) {
            /** @var Permission $parent */
            $parent = $repo->find($parent);

            foreach ($children as $child) {
                /** @var Permission $child */
                $child = $repo->find($child);
                $parent->addChild($child);
            }

            $em->persist($parent);
        }

        $em->flush();
    }
}

